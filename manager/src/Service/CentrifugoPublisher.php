<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CentrifugoPublisher
{
    private HttpClientInterface $client;
    private string $apiKey;

    public function __construct(HttpClientInterface $client, string $centrifugoApiKey)
    {
        $this->client = $client;
        $this->apiKey = $centrifugoApiKey;
    }

    public function publish(string $channel, array $data): void
    {
        $response = $this->client->request('POST', 'http://centrifugo:8000/api/publish', [
            'headers' => [
                'Authorization' => 'apikey ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'channel' => $channel,
                'data' => $data
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Failed to publish to Centrifugo');
        }
    }


}
