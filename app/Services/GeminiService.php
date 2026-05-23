<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = 'AIzaSyAjUVyLOgNtlKK3Y4WG0lrM7eW67Snxpow';
    }

    public function generateAnalytic($prompt)
    {
        // PERBAIKAN FINAL: Menggunakan gemini-2.0-flash yang merupakan model default paling stabil saat ini
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $this->apiKey;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            return $response->json('candidates.0.content.parts.0.text');
        }

        throw new \Exception("Gemini API Error: " . $response->body());
    }
}
