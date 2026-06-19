<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RabbitMQService
{
    private string $baseUrl = 'https://iae-sso.virtualfri.id';

    public function publish(string $event, array $data, string $token): bool
    {
        $payload = [
            'event_name'   => $event,
            'service_name' => 'Transaction-Service',
            'api_version'  => 'v1',
            'occurred_at'  => now()->toIso8601String(),
            'message'      => $data
        ];

        Log::info('RabbitMQ PUBLISH REQUEST', [
            'url'     => "{$this->baseUrl}/api/v1/messages/publish",
            'payload' => $payload,
            'token'   => substr($token, 0, 20) . '...',
        ]);

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Content-Type'  => 'application/json',
        ])->post("{$this->baseUrl}/api/v1/messages/publish", $payload);

        Log::info('RabbitMQ PUBLISH RESPONSE', [
            'status' => $response->status(),
            'body'   => $response->json(),
        ]);

        return $response->successful();
    }
}