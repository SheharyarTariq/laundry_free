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

  /**
   * Test requesting a password reset code
   */
  public function testResetPasswordRequest(): void
  {
    $client = static::createClient();
    $email = "reset-test@gmail.com";

    // 1. Register a user first
    $client->request('POST', '/api/users/register', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => [
        "email" => $email,
        "password" => "User@123",
        "fullName" => "Reset Test User",
        "phoneNumber" => "+441234567890"
      ],
    ]);

    $this->assertResponseStatusCodeSame(201);

    // 2. Request a password reset
    $response = $client->request('POST', '/api/reset-password/request', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => [
        "email" => $email
      ],
    ]);

    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains([
      'success' => true,
      'message' => 'Password reset code has been sent to your email'
    ]);

    // 3. Verify that the reset code was set in the database
    $userRepo = static::getContainer()->get(\App\Repository\UserRepository::class);
    $user = $userRepo->findOneBy(['email' => $email]);

    $this->assertNotEmpty($user->verificationCode);
    $this->assertGreaterThan(time(), $user->verificationCodeExpiry);
  }

  /**
   * Test requesting a password reset for non-existent email
   * Should return success message for security (to prevent email enumeration)
   */
  public function testResetPasswordRequestNonExistentEmail(): void
  {
    $client = static::createClient();

    $response = $client->request('POST', '/api/reset-password/request', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => [
        "email" => "nonexistent@gmail.com"
      ],
    ]);

    // Should still return success for security
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains([
      'success' => true,
      'message' => 'If the email exists, a password reset code has been sent'
    ]);
  }

  /**
   * Test confirming password reset with valid code
   */
  public function testResetPasswordConfirmSuccess(): void
  {
    $client = static::createClient();
    $email = "reset-confirm-test@gmail.com";
    $newPassword = "NewPassword@123";

    // 1. Register a user
    $client->request('POST', '/api/users/register', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => [
        "email" => $email,
        "password" => "OldPassword@123",
        "fullName" => "Reset Confirm User",
        "phoneNumber" => "+441234567890"
      ],
    ]);

    $this->assertResponseStatusCodeSame(201);

    // 2. Request password reset
    $client->request('POST', '/api/reset-password/request', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => ["email" => $email],
    ]);

    // 3. Get the reset code from database
    $userRepo = static::getContainer()->get(\App\Repository\UserRepository::class);
    $user = $userRepo->findOneBy(['email' => $email]);
    $resetCode = $user->verificationCode;

    // 4. Confirm password reset with the code
    $response = $client->request('POST', '/api/reset-password/confirm', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => [
        "email" => $email,
        "token" => $resetCode,
        "newPassword" => $newPassword
      ],
    ]);

    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains([
      'success' => true,
      'message' => 'Password has been reset successfully. You can now login with your new password.'
    ]);

    // 5. Verify the verification code was cleared
    $userRepo->findOneBy(['email' => $email]); // Refresh from DB
    $updatedUser = $userRepo->findOneBy(['email' => $email]);
    $this->assertEmpty($updatedUser->verificationCode);

    // 6. Test login with new password
    $loginResponse = $client->request('POST', '/api/login', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => [
        "email" => $email,
        "password" => $newPassword
      ],
    ]);

    $this->assertResponseStatusCodeSame(200);
  }

  /**
   * Test confirming password reset with invalid code
   */
  public function testResetPasswordConfirmInvalidCode(): void
  {
    $client = static::createClient();
    $email = "reset-invalid-code@gmail.com";

    // 1. Register a user
    $client->request('POST', '/api/users/register', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => [
        "email" => $email,
        "password" => "User@123",
        "fullName" => "Invalid Code User",
        "phoneNumber" => "+441234567890"
      ],
    ]);

    // 2. Request password reset
    $client->request('POST', '/api/reset-password/request', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => ["email" => $email],
    ]);

    // 3. Try to confirm with wrong code
    $response = $client->request('POST', '/api/reset-password/confirm', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => [
        "email" => $email,
        "token" => "9999", // Invalid code
        "newPassword" => "NewPassword@123"
      ],
    ]);

    // Should return error
    $this->assertResponseStatusCodeSame(500);
  }

  /**
   * Test changing password for authenticated user
   */
  public function testChangePasswordSuccess(): void
  {
    $client = static::createClient();
    $email = "change-password-test@gmail.com";
    $oldPassword = "OldPassword@123";
    $newPassword = "NewPassword@456";

    // 1. Register a user
    $client->request('POST', '/api/users/register', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => [
        "email" => $email,
        "password" => $oldPassword,
        "fullName" => "Change Password User",
        "phoneNumber" => "+441234567890"
      ],
    ]);

    // 2. Login to get JWT token
    $loginResponse = $client->request('POST', '/api/login', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => [
        "email" => $email,
        "password" => $oldPassword
      ],
    ]);

    $loginData = $loginResponse->toArray();
    $token = $loginData['token'];

    // 3. Change password
    $response = $client->request('POST', '/api/change-password', [
      'headers' => [
        'Accept' => 'application/ld+json',
        'Content-Type' => 'application/ld+json',
        'Authorization' => 'Bearer ' . $token
      ],
      'json' => [
        "currentPassword" => $oldPassword,
        "newPassword" => $newPassword
      ],
    ]);

    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains([
      'success' => true,
      'message' => 'Password has been changed successfully.'
    ]);

    // 4. Verify can login with new password
    $newLoginResponse = $client->request('POST', '/api/login', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => [
        "email" => $email,
        "password" => $newPassword
      ],
    ]);

    $this->assertResponseStatusCodeSame(200);
  }

  /**
   * Test changing password with incorrect current password
   */
  public function testChangePasswordInvalidCurrentPassword(): void
  {
    $client = static::createClient();
    $email = "change-password-invalid@gmail.com";
    $password = "CurrentPassword@123";

    // 1. Register a user
    $client->request('POST', '/api/users/register', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => [
        "email" => $email,
        "password" => $password,
        "fullName" => "Invalid Change User",
        "phoneNumber" => "+441234567890"
      ],
    ]);

    // 2. Login to get JWT token
    $loginResponse = $client->request('POST', '/api/login', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => [
        "email" => $email,
        "password" => $password
      ],
    ]);

    $loginData = $loginResponse->toArray();
    $token = $loginData['token'];

    // 3. Try to change password with wrong current password
    $response = $client->request('POST', '/api/change-password', [
      'headers' => [
        'Accept' => 'application/ld+json',
        'Content-Type' => 'application/ld+json',
        'Authorization' => 'Bearer ' . $token
      ],
      'json' => [
        "currentPassword" => "WrongPassword@123",
        "newPassword" => "NewPassword@456"
      ],
    ]);

    $this->assertResponseStatusCodeSame(400);
    $this->assertJsonContains([
      'success' => false,
      'message' => 'Invalid current password.'
    ]);
  }

  /**
   * Test changing password without authentication
   */
  public function testChangePasswordUnauthenticated(): void
  {
    $client = static::createClient();

    // Try to change password without authentication
    $response = $client->request('POST', '/api/change-password', [
      'headers' => ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'],
      'json' => [
        "currentPassword" => "Current@123",
        "newPassword" => "New@123"
      ],
    ]);

    // Should return 401 Unauthorized
    $this->assertResponseStatusCodeSame(401);
  }
}
