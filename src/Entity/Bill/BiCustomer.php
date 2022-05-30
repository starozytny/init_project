<?php

namespace App\Entity\Bill;

use App\Entity\DataEntity;
use App\Repository\Bill\BiCustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BiCustomerRepository::class)
 */
class BiCustomer extends DataEntity
{
    const PREFIX = "CL";

    const CUSTOMER_READ = ["customer:read"];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"customer:read", "site:read", "relation:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customer:read"})
     */
    private $numero;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @Groups({"customer:read"})
     */
    private $useNumero = true;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"customer:read"})
     */
    private $payType = BiInvoice::PAY_TYPE_VIREMENT;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customer:read", "site:read", "relation:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"customer:read"})
     */
    private $numeroTva;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"customer:read"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Groups({"customer:read"})
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"customer:read"})
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"customer:read"})
     */
    private $address2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"customer:read"})
     */
    private $complement;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"customer:read"})
     */
    private $zipcode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"customer:read"})
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"customer:read"})
     */
    private $country;

    /**
     * @ORM\ManyToOne(targetEntity=BiSociety::class, fetch="EAGER", inversedBy="biCustomers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $society;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"customer:read"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=BiSite::class, mappedBy="customer")
     */
    private $sites;

    /**
     * @ORM\OneToMany(targetEntity=BiContractCustomer::class, mappedBy="customer")
     */
    private $contractCustomers;

    public function __construct()
    {
        $this->createdAt = $this->initNewDate();
        $this->sites = new ArrayCollection();
        $this->contractCustomers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
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

    public function getNumeroTva(): ?string
    {
        return $this->numeroTva;
    }

    public function setNumeroTva(?string $numeroTva): self
    {
        $this->numeroTva = $numeroTva;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function setComplement(?string $complement): self
    {
        $this->complement = $complement;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getSociety(): ?BiSociety
    {
        return $this->society;
    }

    public function setSociety(?BiSociety $society): self
    {
        $this->society = $society;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * @return Collection<int, BiSite>
     */
    public function getSites(): Collection
    {
        return $this->sites;
    }

    public function addSite(BiSite $site): self
    {
        if (!$this->sites->contains($site)) {
            $this->sites[] = $site;
            $site->setCustomer($this);
        }

        return $this;
    }

    public function removeSite(BiSite $site): self
    {
        if ($this->sites->removeElement($site)) {
            // set the owning side to null (unless already changed)
            if ($site->getCustomer() === $this) {
                $site->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BiContractCustomer>
     */
    public function getContractCustomers(): Collection
    {
        return $this->contractCustomers;
    }

    public function addContractCustomer(BiContractCustomer $contractCustomer): self
    {
        if (!$this->contractCustomers->contains($contractCustomer)) {
            $this->contractCustomers[] = $contractCustomer;
            $contractCustomer->setCustomer($this);
        }

        return $this;
    }

    public function removeContractCustomer(BiContractCustomer $contractCustomer): self
    {
        if ($this->contractCustomers->removeElement($contractCustomer)) {
            // set the owning side to null (unless already changed)
            if ($contractCustomer->getCustomer() === $this) {
                $contractCustomer->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     * @Groups({"invoice:read"})
     */
    public function getPayTypeString(): string
    {
        return $this->getPayTypeFullString($this->payType);
    }

    public function getPayType(): ?int
    {
        return $this->payType;
    }

    public function setPayType(int $payType): self
    {
        $this->payType = $payType;

        return $this;
    }

    public function getUseNumero(): ?bool
    {
        return $this->useNumero;
    }

    public function setUseNumero(bool $useNumero): self
    {
        $this->useNumero = $useNumero;

        return $this;
    }
}
