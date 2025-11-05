<?php

namespace App\Entity;

use App\Repository\TrialRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TrialRequestRepository::class)]
class TrialRequest
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank, Assert\Length(max:80)]
    private ?string $firstName = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank, Assert\Length(max:80)]
    private ?string $lastName = null;

    #[ORM\Column(length: 160)]
    #[Assert\NotBlank, Assert\Email, Assert\Length(max:160)]
    private ?string $email = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank, Assert\Length(max:30)]
    #[Assert\Regex(pattern: '/^[0-9+ ().-]{6,20}$/', message: 'Téléphone invalide.')]
    private ?string $phone = null;

    #[ORM\Column(length: 60, nullable: true)]
    #[Assert\Length(max:60)]
    private ?string $goal = null;

    #[ORM\Column(length: 80, nullable: true)]
    #[Assert\Length(max:80)]
    private ?string $class = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $message = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $trialDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $trialSlot = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // getters/setters…
    public function getId(): ?int { return $this->id; }
    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(?string $v): self { $this->firstName = $v; return $this; }
    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(?string $v): self { $this->lastName = $v; return $this; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $v): self { $this->email = $v; return $this; }
    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $v): self { $this->phone = $v; return $this; }
    public function getGoal(): ?string { return $this->goal; }
    public function setGoal(?string $v): self { $this->goal = $v; return $this; }
    public function getClass(): ?string { return $this->class; }
    public function setClass(?string $v): self { $this->class = $v; return $this; }
    public function getMessage(): ?string { return $this->message; }
    public function setMessage(?string $v): self { $this->message = $v; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getTrialDate(): ?\DateTimeInterface { return $this->trialDate; }
    public function setTrialDate(?\DateTimeInterface $d): self { $this->trialDate = $d; return $this; }
    public function getTrialSlot(): ?string { return $this->trialSlot; }
    public function setTrialSlot(?string $s): self { $this->trialSlot = $s; return $this; }
}
