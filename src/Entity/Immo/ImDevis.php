<?php

namespace App\Entity\Immo;

use App\Repository\Immo\ImDevisRepository;
use Carbon\Carbon;
use Carbon\Factory;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ImDevisRepository::class)
 */
class ImDevis
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
     * @Assert\NotBlank()
     * @Groups({"admin:read"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"admin:read"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Groups({"admin:read"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank()
     * @Groups({"admin:read"})
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"admin:read"})
     */
    private $typeAd;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"admin:read"})
     */
    private $typeBien;

    /**
     * @ORM\Column(type="string", length=5)
     * @Assert\NotBlank()
     * @Groups({"admin:read"})
     */
    private $zipcode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"admin:read"})
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"admin:read"})
     */
    private $etat;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank()
     * @Groups({"admin:read"})
     */
    private $area;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"admin:read"})
     */
    private $areaLand;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Groups({"admin:read"})
     */
    private $nbPiece;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"admin:read"})
     */
    private $nbRoom;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"admin:read"})
     */
    private $infos;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * @Groups({"admin:read"})
     */
    private $createdAt;

    public function __construct()
    {
        $createdAt = new \DateTime();
        $createdAt->setTimezone(new \DateTimeZone("Europe/Paris"));
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

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

    public function getTypeAd(): ?string
    {
        return $this->typeAd;
    }

    public function setTypeAd(string $typeAd): self
    {
        $this->typeAd = $typeAd;

        return $this;
    }

    public function getTypeBien(): ?string
    {
        return $this->typeBien;
    }

    public function setTypeBien(string $typeBien): self
    {
        $this->typeBien = $typeBien;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getArea(): ?float
    {
        return $this->area;
    }

    public function setArea(float $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getAreaLand(): ?float
    {
        return $this->areaLand;
    }

    public function setAreaLand(?float $areaLand): self
    {
        $this->areaLand = $areaLand;

        return $this;
    }

    public function getNbPiece(): ?int
    {
        return $this->nbPiece;
    }

    public function setNbPiece(int $nbPiece): self
    {
        $this->nbPiece = $nbPiece;

        return $this;
    }

    public function getNbRoom(): ?int
    {
        return $this->nbRoom;
    }

    public function setNbRoom(?int $nbRoom): self
    {
        $this->nbRoom = $nbRoom;

        return $this;
    }

    public function getInfos(): ?string
    {
        return $this->infos;
    }

    public function setInfos(?string $infos): self
    {
        $this->infos = $infos;

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
     * Return created at time in string format d/m/Y
     * @Groups({"admin:read"})
     */
    public function getCreatedAtString(): ?string
    {
        if($this->createdAt == null){
            return null;
        }
        return date_format($this->createdAt, 'd/m/Y');
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
}
