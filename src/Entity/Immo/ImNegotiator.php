<?php

namespace App\Entity\Immo;

use App\Repository\Immo\ImNegotiatorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ImNegotiatorRepository::class)
 */
class ImNegotiator
{
    const TRANSPORT_UNKNOWN = 0;
    const TRANSPORT_PIED = 1;
    const TRANSPORT_COMMUN = 2;
    const TRANSPORT_VOITURE_PRO = 3;
    const TRANSPORT_VOITURE_PERSO = 4;
    const TRANSPORT_DEUX_ROUES_PRO = 5;
    const TRANSPORT_DEUX_ROUES_PERSO = 6;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"admin:read", "user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups({"admin:read", "user:read"})
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"admin:read"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"admin:read"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Groups({"admin:read", "user:read"})
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Groups({"admin:read", "user:read"})
     */
    private $phone2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read", "user:read"})
     */
    private $email;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"admin:read"})
     */
    private $transport = 0;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"admin:read"})
     */
    private $immatriculation;

    /**
     * @ORM\ManyToOne(targetEntity=ImAgency::class, fetch="EAGER", inversedBy="negotiators")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"admin:read"})
     */
    private $agency;

    /**
     * @ORM\OneToMany(targetEntity=ImBien::class, mappedBy="negotiator")
     */
    private $biens;

    /**
     * @ORM\OneToMany(targetEntity=ImOwner::class, mappedBy="negotiator")
     */
    private $owners;

    public function __construct()
    {
        $this->biens = new ArrayCollection();
        $this->owners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhone2(): ?string
    {
        return $this->phone2;
    }

    public function setPhone2(?string $phone2): self
    {
        $this->phone2 = $phone2;

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

    /**
     * @return string
     * @Groups({"admin:read"})
     */
    public function getFullTransportString(): ?string
    {
        if($this->transport == null){
            return null;
        }
        return $this->getTransportString() . (($this->transport != 0 && $this->transport != 1) ? " - " . $this->immatriculation : "");
    }

    /**
     * @return string
     * @Groups({"admin:read"})
     */
    public function getTransportString(): string
    {
        $transports = ["", "Pied", "Transport en commun", "Voiture professionnelle", "Voiture personnelle", "Deux roues professionnel", "Deux roues personnel"];

        return $transports[$this->transport];
    }

    public function getTransport(): ?int
    {
        return $this->transport;
    }

    public function setTransport(?int $transport): self
    {
        $this->transport = $transport;

        return $this;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(?string $immatriculation): self
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getAgency(): ?ImAgency
    {
        return $this->agency;
    }

    public function setAgency(?ImAgency $agency): self
    {
        $this->agency = $agency;

        return $this;
    }

    /**
     * @return string
     * @Groups({"admin:read", "user:read"})
     */
    public function getFullname(): string
    {
        return trim($this->lastname . ' ' . $this->firstname);
    }

    /**
     * @return Collection|ImBien[]
     */
    public function getBiens(): Collection
    {
        return $this->biens;
    }

    public function addBien(ImBien $bien): self
    {
        if (!$this->biens->contains($bien)) {
            $this->biens[] = $bien;
            $bien->setNegotiator($this);
        }

        return $this;
    }

    public function removeBien(ImBien $bien): self
    {
        if ($this->biens->removeElement($bien)) {
            // set the owning side to null (unless already changed)
            if ($bien->getNegotiator() === $this) {
                $bien->setNegotiator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ImOwner[]
     */
    public function getOwners(): Collection
    {
        return $this->owners;
    }

    public function addOwner(ImOwner $owner): self
    {
        if (!$this->owners->contains($owner)) {
            $this->owners[] = $owner;
            $owner->setNegotiator($this);
        }

        return $this;
    }

    public function removeOwner(ImOwner $owner): self
    {
        if ($this->owners->removeElement($owner)) {
            // set the owning side to null (unless already changed)
            if ($owner->getNegotiator() === $this) {
                $owner->setNegotiator(null);
            }
        }

        return $this;
    }
}