<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    security: "is_granted('ROLE_ADMIN')",
    operations: [
        new Post(
            normalizationContext: [
                'groups' => ['Postcode:V$Create']
            ],
            denormalizationContext: [
                'groups' => ['Postcode:W$Create']
            ],
        ),

        new Get(
            normalizationContext: [
                'groups' => ['Postcode:V$Detail']
            ],
        ),

        new GetCollection(
            normalizationContext: [
                'groups' => ['Postcode:V$List']
            ],
        ),

        new Delete(),

        new Put(
            normalizationContext: [
                'groups' => ['Postcode:V$Update']
            ],
            denormalizationContext: [
                'groups' => ['Postcode:W$Update']
            ],
        )
    ]
)]

#[UniqueEntity(fields: ['postcode'], message: 'This postcode already exists.')]
#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'postcode__postcode', fields: ['postcode'])]
#[ORM\HasLifecycleCallbacks]
class Postcode
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups([
        'Postcode:V$Create',
        'Postcode:V$Detail',
        'Postcode:V$List',
        'Postcode:V$Update'
        ])]
    public Uuid $id;

    #[ORM\Column(length: 255)]
    #[Groups([
        'Postcode:V$Create', 
        'Postcode:V$Detail', 
        'Postcode:V$List', 
        'Postcode:V$Update', 
        'Postcode:W$Create', 
        'Postcode:W$Update'
        ])]
    #[Assert\NotBlank(message: 'Postcode name is required')]
    #[Assert\Regex(
        pattern: '/^([A-Z]{1,2}\d[A-Z\d]?|ASCN|STHL|TDCU|BBND|[BFS]IQQ|PCRN|TKCA) ?\d[A-Z]{2}$/i',
        message: 'Please enter a valid UK postcode (e.g., SW1A 1AA, EC1A 1BB)'
    )]
    public string $postcode;

    #[ORM\ManyToOne(targetEntity: Area::class, inversedBy: 'postcodes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups([
        'Postcode:V$Create', 
        'Postcode:V$Detail', 
        'Postcode:V$List', 
        'Postcode:V$Update', 
        'Postcode:W$Create', 
        'Postcode:W$Update'
        ])]
    #[Assert\NotNull(message: 'Area is required')]
    private ?Area $area = null;


    #[ORM\Column(type: 'datetime')]
    #[Groups([
        'Postcode:V$Create', 
        'Postcode:V$Detail', 
        'Postcode:V$List'
        ])]
    public \DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    #[Groups([
        'Postcode:V$Create', 
        'Postcode:V$Detail', 
        'Postcode:V$List'
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

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): static
    {
        $this->area = $area;

        return $this;
    }
}
