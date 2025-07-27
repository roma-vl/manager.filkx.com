<?php

namespace App\Service;

use phpcent\Client;

class PhpCentFactory
{
    public static function create(): Client
    {
        $client = new Client($_ENV['CENTRIFUGO_API_URL'] ?? 'http://localhost:8000/api');
        $client->setApiKey($_ENV['CENTRIFUGO_API_KEY']);
        return $client;
    }
}
