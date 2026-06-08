<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Repayment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @OA\Info(
 *     title="Transaction Service API",
 *     version="1.0.0",
 *     description="IAE Tugas 2 - Mini Service untuk manajemen transaksi dan cicilan pinjaman",
 *     @OA\Contact(email="student@telkomuniversity.ac.id")
 * )
 * @OA\SecurityScheme(
 *     securityScheme="ApiKeyAuth",
 *     type="apiKey",
 *     in="header",
 *     name="X-IAE-KEY"
 * )
 * @OA\Server(url="/", description="Local Server")
 */
class TransactionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/transactions",
     *     summary="Ambil daftar semua transaksi",
     *     tags={"Transactions"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar transaksi berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="account_id", type="string"),
     *                 @OA\Property(property="type", type="string", enum={"credit","debit"}),
     *                 @OA\Property(property="amount", type="number"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="reference_number", type="string"),
     *                 @OA\Property(property="transaction_date", type="string")
     *             )),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="service_name", type="string", example="Transaction-Service"),
     *                 @OA\Property(property="api_version", type="string", example="v1"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized - X-IAE-KEY tidak valid")
     * )
     */
    public function index()
    {
        $transactions = Transaction::orderBy('transaction_date', 'desc')->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data retrieved successfully',
            'data'    => $transactions,
            'meta'    => [
                'service_name' => 'Transaction-Service',
                'api_version'  => 'v1',
                'total'        => $transactions->count(),
            ],
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/transactions/account/{account_id}",
     *     summary="Ambil riwayat transaksi milik nasabah tertentu",
     *     tags={"Transactions"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="path",
     *         required=true,
     *         description="ID rekening nasabah",
     *         @OA\Schema(type="string", example="ACC-001")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Riwayat transaksi nasabah berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Transaksi tidak ditemukan untuk akun ini"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function getByAccount(string $account_id)
    {
        $transactions = Transaction::where('account_id', $account_id)
            ->orderBy('transaction_date', 'desc')
            ->get();

        if ($transactions->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => "No transactions found for account: {$account_id}",
                'errors'  => null,
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Data retrieved successfully',
            'data'    => $transactions,
            'meta'    => [
                'service_name' => 'Transaction-Service',
                'api_version'  => 'v1',
                'account_id'   => $account_id,
                'total'        => $transactions->count(),
            ],
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/transactions/repayment/{account_id}",
     *     summary="Cek riwayat pembayaran cicilan nasabah",
     *     tags={"Repayments"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="path",
     *         required=true,
     *         description="ID rekening nasabah",
     *         @OA\Schema(type="string", example="ACC-001")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Riwayat cicilan berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Data cicilan tidak ditemukan"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function getRepaymentHistory(string $account_id)
    {
        $repayments = Repayment::where('account_id', $account_id)
            ->orderBy('installment_number')
            ->get();

        if ($repayments->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => "No repayment records found for account: {$account_id}",
                'errors'  => null,
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Data retrieved successfully',
            'data'    => $repayments,
            'meta'    => [
                'service_name' => 'Transaction-Service',
                'api_version'  => 'v1',
                'account_id'   => $account_id,
                'total'        => $repayments->count(),
            ],
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/transactions/repayment",
     *     summary="Eksekusi pembayaran cicilan pinjaman",
     *     tags={"Repayments"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"account_id","repayment_amount","installment_number"},
     *             @OA\Property(property="account_id", type="string", example="ACC-001"),
     *             @OA\Property(property="repayment_amount", type="number", example=1500000),
     *             @OA\Property(property="installment_number", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pembayaran cicilan berhasil dieksekusi",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Repayment processed successfully"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Data cicilan tidak ditemukan atau sudah terbayar"),
     *     @OA\Response(response=422, description="Validasi gagal"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function processRepayment(Request $request)
    {
        $validated = $request->validate([
            'account_id'         => 'required|string',
            'repayment_amount'   => 'required|numeric|min:1',
            'installment_number' => 'required|integer|min:1',
        ]);

        // Cari cicilan yang pending
        $repayment = Repayment::where('account_id', $validated['account_id'])
            ->where('installment_number', $validated['installment_number'])
            ->where('status', 'pending')
            ->first();

        if (!$repayment) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Repayment record not found or already paid.',
                'errors'  => null,
            ], 404);
        }

        // Update status cicilan
        $repayment->update([
            'repayment_amount' => $validated['repayment_amount'],
            'status'           => 'paid',
            'paid_at'          => now(),
        ]);

        // Catat sebagai transaksi debit
        $transaction = Transaction::create([
            'account_id'       => $validated['account_id'],
            'type'             => 'debit',
            'amount'           => $validated['repayment_amount'],
            'description'      => "Pembayaran cicilan ke-{$validated['installment_number']}",
            'reference_number' => 'REP-' . strtoupper(Str::random(10)),
            'transaction_date' => now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Repayment processed successfully',
            'data'    => [
                'repayment'   => $repayment->fresh(),
                'transaction' => $transaction,
            ],
            'meta'    => [
                'service_name' => 'Transaction-Service',
                'api_version'  => 'v1',
            ],
        ], 201);
    }
}
