<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class UserEndpointTest extends ApiTestCase
{

  public function testLogin(): void
  {
    $payload = [
      "email" => "user1@gmail.com",
      "password" => "User@123"
    ];

    $client = static::createClient();

    // 1. Attempt to login with the payload
    // Since we haven't created the user yet, this should fail with 401
    $response = $client->request('POST', '/api/login', [
      'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
      'json' => $payload,
    ]);

    $this->assertResponseStatusCodeSame(401);
    $this->assertJsonContains(['message' => 'Invalid credentials.']);
  }

  public function testRegister(): void
  {
    $client = static::createClient();

    // For registration, we need more fields based on the User entity groups
    $payload = [
      "email" => "newuser@gmail.com",
      "password" => "User@123",
      "fullName" => "Test User",
      "phoneNumber" => "+441234567890"
    ];

    $response = $client->request('POST', '/api/users/register', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => $payload,
    ]);

    // If the registration is successful, it should return 201 Created
    // Note: You'll need a test database configured for this to actually run and persist
    $this->assertResponseStatusCodeSame(201);
    $this->assertJsonContains([
      'email' => 'newuser@gmail.com',
      'fullName' => 'Test User'
    ]);
  }

  public function testUserVerify(): void
  {
    $client = static::createClient();
    $email = "verify-test@gmail.com";

    // 1. Register the user
    $client->request('POST', '/api/users/register', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => [
        "email" => $email,
        "password" => "User@123",
        "fullName" => "Verify User",
        "phoneNumber" => "+441234567890"
      ],
    ]);

    $this->assertResponseStatusCodeSame(201);

    // 2. GET THE CODE FROM THE DATABASE
    // Since the API doesn't return the code (for security), we fetch it from the DB directly
    $userRepo = static::getContainer()->get(\App\Repository\UserRepository::class);
    $user = $userRepo->findOneBy(['email' => $email]);
    $code = $user->verificationCode;

    // 3. Verify the user
    $response = $client->request('POST', '/api/verify', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => [
        "email" => $email,
        "code" => $code
      ],
    ]);

    // We expect a successful JSON response
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains(['success' => true]);
  }
}
