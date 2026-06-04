<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppHelper
{
    protected static function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }

    public static function sendMessage($target, $message)
    {
        $apiKey  = config('fonnte.token');
        $baseUrl = config('fonnte.base_url');
        $timeout = config('fonnte.timeout', 30);

        // Guard: jangan kirim jika token kosong
        if (empty($apiKey)) {
            Log::error('WhatsApp gagal: FONNTE_TOKEN tidak ditemukan di config.');
            return ['success' => false, 'error' => 'Token tidak tersedia'];
        }

        $target = self::formatPhone($target);

        // Guard: jangan kirim jika nomor tidak valid
        if (strlen($target) < 10) {
            Log::warning('WhatsApp gagal: nomor tidak valid', ['target' => $target]);
            return ['success' => false, 'error' => 'Nomor tidak valid'];
        }

        try {
            Log::info('Mengirim WhatsApp via Fonnte...', [
                'target'  => $target,
                'message' => $message,
            ]);

            $response = Http::timeout($timeout)
                ->withHeaders([
                    'Authorization' => $apiKey,
                ])
                ->post($baseUrl . '/send', [
                    'target'  => $target,
                    'message' => $message,
                ]);

            $data = $response->json();

            Log::info('Response Fonnte', ['response' => $data]);

            if ($response->successful() && !empty($data['status'])) {
                Log::info('WhatsApp berhasil dikirim', ['target' => $target]);
                return ['success' => true, 'response' => $data];
            }

            Log::error('WhatsApp gagal dikirim', [
                'target'   => $target,
                'response' => $data,
            ]);

            return ['success' => false, 'error' => $data];
        } catch (\Exception $e) {
            Log::error('WhatsApp Exception', [
                'target'  => $target,
                'message' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
