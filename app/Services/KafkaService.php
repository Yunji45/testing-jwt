<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KafkaService
{
    private $broker;
    private $user;
    private $password;

    public function __construct()
    {
        $this->broker = env('KAFKA_BROKER');
        $this->user = env('KAFKA_USERNAME');
        $this->password = env('KAFKA_PASSWORD');
    }

    public function produce(string $topic, string $key, array $data, array $headers = [])
    {
        $url = $this->getProduceUrl($topic);

        $defaultHeaders = $this->getHeaders();
        $headers = array_merge($defaultHeaders, $headers);

        $payload = [
            'records' => [
                [
                    'key' => $key,
                    'value' => $data,
                ]
            ]
        ];

        $response = Http::withHeaders($headers)->post($url, $payload);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'error' => true,
            'status' => $response->status(),
            'body' => $response->body(),
        ];
    }

    private function getProduceUrl(string $topic): string
    {
        return sprintf('http://%s/topics/%s', $this->broker, $topic);
    }

    private function getHeaders(): array
    {
        $auth = base64_encode("{$this->user}:{$this->password}");

        return [
            'Authorization' => 'Basic ' . $auth,
            'Content-Type'  => 'application/vnd.kafka.json.v2+json',
            'Accept'        => 'application/vnd.kafka.v2+json',
        ];
    }
}
