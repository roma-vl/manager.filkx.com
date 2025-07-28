<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Auth;

use App\Tests\Functional\DbWebTestCase;

class SignUpTest extends DbWebTestCase
{
    private const URI = '/api/auth/signup';

    public function testGetMethodNotAllowed(): void
    {
        $this->client->request('GET', self::URI);

        static::assertSame(405, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccessfulSignUp(): void
    {
        $payload = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test-john@app.test',
            'password' => 'password',
        ];

        $this->client->request(
            'POST',
            self::URI,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $this->client->getResponse();

        static::assertSame(201, $response->getStatusCode());
        static::assertJson($content = $response->getContent());

        $data = json_decode($content, true);

        static::assertIsArray($data);
        static::assertEmpty($data);
    }

    public function testValidationErrors(): void
    {
        $payload = [
            'first_name' => '',
            'last_name' => '',
            'email' => 'not-email',
            'password' => 'short',
        ];

        $this->client->request(
            'POST',
            self::URI,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $this->client->getResponse();

        static::assertSame(400, $response->getStatusCode());
        static::assertJson($content = $response->getContent());

        $data = json_decode($content, true);

        static::assertArrayHasKey('violations', $data);
        $violations = array_column($data['violations'], 'title', 'propertyPath');

        static::assertSame('This value should not be blank.', $violations['first_name'] ?? null);
        static::assertSame('This value should not be blank.', $violations['last_name'] ?? null);
        static::assertSame('This value is not a valid email address.', $violations['email'] ?? null);
        static::assertSame('This value is too short. It should have 6 characters or more.', $violations['password'] ?? null);
    }

    public function testUserAlreadyExists(): void
    {
        $payload = [
            'first_name' => 'Tom',
            'last_name' => 'Bent',
            'email' => 'exesting-user@app.test',
            'password' => 'password',
        ];

        $this->client->request(
            'POST',
            self::URI,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $this->client->getResponse();

        static::assertSame(400, $response->getStatusCode());
        static::assertJson($content = $response->getContent());

        $data = json_decode($content, true);

        static::assertArrayHasKey('error', $data);
        static::assertSame('User already exists.', $data['error']['message'] ?? '');
    }
}
