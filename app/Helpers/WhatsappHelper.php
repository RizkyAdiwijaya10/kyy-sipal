<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppHelper
{
    protected static $apiKey;
    protected static $baseUrl = 'https://api.fonnte.com';

    /**
     * Inisialisasi API key
     */
    protected static function init()
    {
        
        self::$apiKey = env('FONNTE_TOKEN');
    }

    /**
     * Format nomor HP menjadi 62xxxxxxxx
     */
    protected static function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Kirim pesan WhatsApp
     */
    public static function sendMessage($target, $message)
    {
        self::init();

        try {

            $target = self::formatPhone($target);

            Log::info('Mengirim WhatsApp...', [
                'target' => $target,
                'message' => $message
            ]);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => self::$apiKey,
                ])
                ->post(self::$baseUrl . '/send', [
                    'target' => $target,
                    'message' => $message,
                ]);

            $data = $response->json();

            Log::info('Response Fonnte', [
                'response' => $data
            ]);

            if (
                $response->successful() &&
                isset($data['status']) &&
                $data['status'] == true
            ) {

                Log::info('WhatsApp berhasil dikirim', [
                    'target' => $target
                ]);

                return [
                    'success' => true,
                    'response' => $data
                ];
            }

            Log::error('WhatsApp gagal dikirim', [
                'target' => $target,
                'response' => $data
            ]);

            return [
                'success' => false,
                'error' => $data
            ];

        } catch (\Exception $e) {

            Log::error('WhatsApp Exception', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    // /**
    //  * Kirim pesan ke banyak nomor
    //  */
    // public static function sendBroadcast($targets, $message)
    // {
    //     self::init();

    //     try {

    //         $formattedTargets = array_map(function ($phone) {
    //             return self::formatPhone($phone);
    //         }, $targets);

    //         $response = Http::timeout(30)
    //             ->withHeaders([
    //                 'Authorization' => self::$apiKey,
    //             ])
    //             ->post(self::$baseUrl . '/send', [
    //                 'target' => implode(',', $formattedTargets),
    //                 'message' => $message,
    //             ]);

    //         $data = $response->json();

    //         return [
    //             'success' => $response->successful(),
    //             'response' => $data
    //         ];

    //     } catch (\Exception $e) {

    //         Log::error('WhatsApp Broadcast Error', [
    //             'message' => $e->getMessage()
    //         ]);

    //         return [
    //             'success' => false,
    //             'error' => $e->getMessage()
    //         ];
    //     }
    // }
}