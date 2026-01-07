<?php

namespace App\Processor\Area;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use App\Entity\Area;
use App\Entity\TimeSlot;

#[AutoconfigureTag('api_platform.state_processor')]
class AreaCreateProcessor implements ProcessorInterface
{
  private const DAYS_OF_WEEK = [
    'MONDAY',
    'TUESDAY',
    'WEDNESDAY',
    'THURSDAY',
    'FRIDAY',
    'SATURDAY',
    'SUNDAY'
  ];

  private const TIME_SLOTS = [
    '8:00-10:00',
    '10:00-12:00',
    '12:00-14:00',
    '14:00-16:00',
    '16:00-18:00',
    '18:00-20:00'
  ];

  public function __construct(
    private readonly EntityManagerInterface $entityManager
  ) {}

  public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Area
  {
    /** @var Area $area */
    $area = $data;

    // Persist the area first
    $this->entityManager->persist($area);
    $this->entityManager->flush();

    // Generate time slots for all days and time ranges
    foreach (self::DAYS_OF_WEEK as $day) {
      foreach (self::TIME_SLOTS as $slot) {
        $timeSlot = new TimeSlot();
        $timeSlot->area = $area;
        $timeSlot->dayOfWeek = $day;
        $timeSlot->slot = $slot;
        $timeSlot->isActive = true;

        $this->entityManager->persist($timeSlot);
        $area->addTimeSlot($timeSlot);
      }
    }

    $this->entityManager->flush();

    return $area;
  }
}
