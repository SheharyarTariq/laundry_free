<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class AreaEndpointTest extends ApiTestCase
{
  private static ?string $adminToken = null;
  private static ?string $createdAreaId = null;

  protected function setUp(): void
  {
    parent::setUp();

    if (self::$adminToken === null) {
      self::$adminToken = $this->getAdminToken();
    }
  }

  private function getAdminToken(): string
  {
    $client = static::createClient();
    $email = 'admin@test.com';

    // Get UserRepository to check if admin exists
    $userRepo = static::getContainer()->get(\App\Repository\UserRepository::class);
    $entityManager = static::getContainer()->get('doctrine')->getManager();

    $admin = $userRepo->findOneBy(['email' => $email]);

    // Create admin user if doesn't exist
    if (!$admin) {
      $admin = new \App\Entity\User();
      $admin->email = $email;
      $admin->password = password_hash('admin123', PASSWORD_DEFAULT);
      $admin->fullName = 'Admin User';
      $admin->phoneNumber = '+441234567890';
      $admin->roles = ['ROLE_ADMIN'];
      $admin->isActive = true;
      $admin->emailVerifiedAt = new \DateTimeImmutable();

      $entityManager->persist($admin);
      $entityManager->flush();
    }

    // Login as admin to get token
    $response = $client->request('POST', '/api/login', [
      'json' => [
        'email' => $email,
        'password' => 'admin123'
      ]
    ]);

    $this->assertResponseIsSuccessful();
    $data = $response->toArray();
    $this->assertArrayHasKey('token', $data);

    return $data['token'];
  }

  public function testCreateAreaWithAutomaticTimeSlots(): void
  {
    $client = static::createClient();

    $areaName = 'Test Area ' . time();

    $response = $client->request('POST', '/api/areas', [
      'headers' => [
        'Authorization' => 'Bearer ' . self::$adminToken,
        'Content-Type' => 'application/ld+json',
        'Accept' => 'application/ld+json'
      ],
      'json' => [
        'area' => $areaName
      ]
    ]);

    $this->assertResponseStatusCodeSame(201);
    $data = $response->toArray();

    // Store area ID for later tests
    self::$createdAreaId = $data['id'];

    // Assert basic area data
    $this->assertArrayHasKey('id', $data);
    $this->assertEquals($areaName, $data['area']);
    $this->assertArrayHasKey('createdAt', $data);
  }

  /**
   * @depends testCreateAreaWithAutomaticTimeSlots
   */
  public function testVerifyTimeSlotsCreated(): void
  {
    $this->assertNotNull(self::$createdAreaId, 'Area ID should be set from previous test');

    $client = static::createClient();

    // Get the area details with time slots
    $response = $client->request('GET', '/api/areas/' . self::$createdAreaId, [
      'headers' => [
        'Authorization' => 'Bearer ' . self::$adminToken
      ]
    ]);

    $this->assertResponseIsSuccessful();
    $data = $response->toArray();

    // Assert time slots are included
    $this->assertArrayHasKey('timeSlots', $data);
    $timeSlots = $data['timeSlots'];

    // Assert 42 time slots were created (7 days × 6 slots per day)
    $this->assertCount(42, $timeSlots, 'Should have exactly 42 time slots');
  }

  /**
   * @depends testVerifyTimeSlotsCreated
   */
  public function testVerifyAllTimeSlotsAreActive(): void
  {
    $this->assertNotNull(self::$createdAreaId, 'Area ID should be set from previous test');

    $client = static::createClient();

    $response = $client->request('GET', '/api/areas/' . self::$createdAreaId, [
      'headers' => [
        'Authorization' => 'Bearer ' . self::$adminToken
      ]
    ]);

    $data = $response->toArray();
    $timeSlots = $data['timeSlots'];

    // Verify all slots are active
    foreach ($timeSlots as $slot) {
      $this->assertTrue($slot['isActive'], 'All time slots should be active by default');
    }
  }

  /**
   * @depends testVerifyTimeSlotsCreated
   */
  public function testVerifyDaysAndTimeRanges(): void
  {
    $this->assertNotNull(self::$createdAreaId, 'Area ID should be set from previous test');

    $client = static::createClient();

    $response = $client->request('GET', '/api/areas/' . self::$createdAreaId, [
      'headers' => [
        'Authorization' => 'Bearer ' . self::$adminToken
      ]
    ]);

    $data = $response->toArray();
    $timeSlots = $data['timeSlots'];

    $expectedDays = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'];
    $expectedSlots = ['8:00-10:00', '10:00-12:00', '12:00-14:00', '14:00-16:00', '16:00-18:00', '18:00-20:00'];

    // Group slots by day
    $slotsByDay = [];
    foreach ($timeSlots as $slot) {
      $day = $slot['dayOfWeek'];
      if (!isset($slotsByDay[$day])) {
        $slotsByDay[$day] = [];
      }
      $slotsByDay[$day][] = $slot['slot'];
    }

    // Verify all 7 days are present
    $this->assertCount(7, $slotsByDay, 'Should have time slots for all 7 days');

    foreach ($expectedDays as $day) {
      $this->assertArrayHasKey($day, $slotsByDay, "Should have time slots for $day");

      // Verify 6 time slots per day
      $this->assertCount(6, $slotsByDay[$day], "Should have 6 time slots for $day");

      // Verify correct time ranges
      sort($slotsByDay[$day]); // Sort to ensure consistent order
      sort($expectedSlots);
      $this->assertEquals($expectedSlots, $slotsByDay[$day], "Time slots for $day should match expected values");
    }
  }

  protected function tearDown(): void
  {
    parent::tearDown();

    // Clean up: delete the created area (cascade will delete time slots)
    if (self::$createdAreaId !== null) {
      $client = static::createClient();

      try {
        $client->request('DELETE', '/api/areas/' . self::$createdAreaId, [
          'headers' => [
            'Authorization' => 'Bearer ' . self::$adminToken
          ]
        ]);
      } catch (\Exception $e) {
        // Ignore cleanup errors
      }
    }
  }
}
