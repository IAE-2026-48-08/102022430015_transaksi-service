<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SsoService
{
    private string $baseUrl = 'https://iae-sso.virtualfri.id';
    private string $apiKey = 'KEY-MHS-99';
    private string $nim = '102022430015';

    public function getM2MToken(): string
    {
        Cache::forget('sso_m2m_token');

        return Cache::remember('sso_m2m_token', 3500, function () {
            $response = Http::post("{$this->baseUrl}/api/v1/auth/token", [
                'api_key' => $this->apiKey,
                'nim'     => $this->nim,
            ]);

            Log::info('SSO M2M TOKEN RESPONSE', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);

            return $response->json('token');
        });
    }

    public function loginUser(string $email, string $password): array
    {
        $response = Http::post("{$this->baseUrl}/api/v1/auth/token", [
            'email'    => $email,
            'password' => $password,
        ]);
        return $response->json();
    }
}