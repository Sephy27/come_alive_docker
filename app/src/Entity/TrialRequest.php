<?php

namespace App\Entity;

use App\Repository\TrialRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TrialRequestRepository::class)]
class TrialRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 80)]
    private ?string $firstName = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 80)]
    private ?string $lastName = null;

    #[ORM\Column(length: 160)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 160)]
    private ?string $email = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 30)]
    #[Assert\Regex(
        pattern: '/^[0-9+ ().-]{6,20}$/',
        message: 'Téléphone invalide.'
    )]
    private ?string $phone = null;

    #[ORM\Column(length: 60, nullable: true)]
    #[Assert\Length(max: 60)]
    private ?string $goal = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "Merci d'écrire un message.")]
    private ?string $message = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: "Merci de choisir une date.")]
    private ?\DateTimeInterface $trialDate = null;

    /**
     * Contient le JSON du créneau :
     * {
     *   "day": "fri",
     *   "start": "11:45",
     *   "end": "12:30",
     *   "title": "GYM SENIOR"
     * }
     */
    #[ORM\Column(length: 300)]
    #[Assert\NotBlank(message: "Merci de choisir un créneau disponible.")]
    private ?string $trialSlot = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // ─────────── Getters / Setters ───────────

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getGoal(): ?string
    {
        return $this->goal;
    }

    public function setGoal(?string $goal): self
    {
        $this->goal = $goal;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getTrialDate(): ?\DateTimeInterface
    {
        return $this->trialDate;
    }

    public function setTrialDate(?\DateTimeInterface $trialDate): self
    {
        $this->trialDate = $trialDate;
        return $this;
    }

    public function getTrialSlot(): ?string
    {
        return $this->trialSlot;
    }

    public function setTrialSlot(?string $trialSlot): self
    {
        $this->trialSlot = $trialSlot;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}

