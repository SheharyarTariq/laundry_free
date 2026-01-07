<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
  security: "is_granted('ROLE_ADMIN')",
  operations: [
    new Patch(
      normalizationContext: [
        'groups' => ['TimeSlot:V$Update']
      ],
      denormalizationContext: [
        'groups' => ['TimeSlot:W$Update']
      ],
    ),
  ]
)]
#[ORM\Entity]
#[ORM\Table(name: '`time_slot`')]
#[ORM\HasLifecycleCallbacks]
class TimeSlot
{
  #[ORM\Id]
  #[ORM\Column(type: UuidType::NAME, unique: true)]
  #[ORM\GeneratedValue(strategy: 'CUSTOM')]
  #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
  #[Groups([
    'TimeSlot:V$List',
    'Area:V$Detail'
  ])]
  public Uuid $id;

  #[ORM\ManyToOne(targetEntity: Area::class, inversedBy: 'timeSlots')]
  #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
  #[Groups([
    'TimeSlot:V$List'
  ])]
  public Area $area;

  #[ORM\Column(length: 20)]
  #[Groups([
    'TimeSlot:V$List',
    'TimeSlot:V$Update',
    'Area:V$Detail'
  ])]
  #[Assert\NotBlank(message: 'Day of week is required')]
  #[Assert\Choice(
    choices: ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'],
    message: 'Invalid day of week'
  )]
  public string $dayOfWeek;

  #[ORM\Column(length: 20)]
  #[Groups([
    'TimeSlot:V$List',
    'TimeSlot:V$Update',
    'Area:V$Detail'
  ])]
  #[Assert\NotBlank(message: 'Slot is required')]
  public string $slot;

  #[ORM\Column(type: 'boolean', options: ['default' => true])]
  #[Groups([
    'TimeSlot:V$List',
    'TimeSlot:V$Update',
    'TimeSlot:W$Update',
    'Area:V$Detail'
  ])]
  public bool $isActive = true;

  #[ORM\Column(type: 'datetime')]
  #[Groups([
    'TimeSlot:V$List'
  ])]
  public \DateTime $createdAt;

  #[ORM\Column(type: 'datetime')]
  #[Groups([
    'TimeSlot:V$List'
  ])]
  public \DateTime $updatedAt;

  public function __construct()
  {
    $this->createdAt = new \DateTime();
    $this->updatedAt = new \DateTime();
  }

  #[ORM\PreUpdate]
  public function setUpdatedAtValue(): void
  {
    $this->updatedAt = new \DateTime();
  }
}
