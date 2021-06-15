<?php

namespace App\Entity\Immo;

use App\Repository\Immo\ImDemandeRepository;
use Carbon\Carbon;
use Carbon\Factory;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ImDemandeRepository::class)
 */
class ImDemande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"admin:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"admin:read"})
     */
    private $phone;

    /**
     * @ORM\Column(type="text")
     * @Groups({"admin:read"})
     */
    private $message;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"admin:read"})
     */
    private $isSeen;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $bienIdentifiant;

    /**
     * @ORM\ManyToOne(targetEntity=ImBien::class, inversedBy="demandes")
     * @Groups({"admin:read"})
     */
    private $bien;

    public function __construct()
    {
        $createdAt = new DateTime();
        $createdAt->setTimezone(new \DateTimeZone('Europe/Paris'));
        $this->createdAt = $createdAt;
        $this->isSeen = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Return created at time in string format ago
     * @Groups({"admin:read"})
     */
    public function getCreatedAtAgo(): ?string
    {
        $frenchFactory = new Factory([
            'locale' => 'fr_FR',
            'timezone' => 'Europe/Paris'
        ]);
        $createdAt = Carbon::instance($this->getCreatedAt());

        return $frenchFactory->make($createdAt)->diffForHumans();
    }

    public function getIsSeen(): ?bool
    {
        return $this->isSeen;
    }

    public function setIsSeen(bool $isSeen): self
    {
        $this->isSeen = $isSeen;

        return $this;
    }

    public function getBienIdentifiant(): ?string
    {
        return $this->bienIdentifiant;
    }

    public function setBienIdentifiant(string $bienIdentifiant): self
    {
        $this->bienIdentifiant = $bienIdentifiant;

        return $this;
    }

    public function getBien(): ?ImBien
    {
        return $this->bien;
    }

    public function setBien(?ImBien $bien): self
    {
        $this->bien = $bien;

        return $this;
    }
}
