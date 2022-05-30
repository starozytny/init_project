<?php

namespace App\Entity\Bill;

use App\Entity\DataEntity;
use App\Repository\Bill\BiSiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=BiSiteRepository::class)
 */
class BiSite extends DataEntity
{
    const PREFIX = "ST";

    const SITE_READ = ["site:read"];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"site:read", "relation:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"site:read"})
     */
    private $numero;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @Groups({"site:read"})
     */
    private $useNumero = true;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"site:read"})
     */
    private $payType = BiInvoice::PAY_TYPE_VIREMENT;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"site:read"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Groups({"site:read"})
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"site:read"})
     */
    private $uid;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"site:read", "relation:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"site:read"})
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"site:read"})
     */
    private $address2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"site:read"})
     */
    private $complement;

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups({"site:read"})
     */
    private $zipcode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"site:read"})
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"site:read"})
     */
    private $country;

    /**
     * @ORM\ManyToOne(targetEntity=BiCustomer::class, fetch="EAGER", inversedBy="sites")
     * @Groups({"site:read"})
     */
    private $customer;

    /**
     * @ORM\OneToMany(targetEntity=BiContractCustomer::class, mappedBy="site")
     */
    private $contractCustomers;

    /**
     * @ORM\ManyToOne(targetEntity=BiSociety::class, fetch="EAGER", inversedBy="biSites")
     * @ORM\JoinColumn(nullable=false)
     */
    private $society;

    public function __construct()
    {
        $this->uid = Uuid::v4();
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

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): self
    {
        $this->uid = $uid;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCustomer(): ?BiCustomer
    {
        return $this->customer;
    }

    public function setCustomer(?BiCustomer $customer): self
    {
        $this->customer = $customer;

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

    /**
     * @return string
     * @Groups({"site:read"})
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
            $contractCustomer->setSite($this);
        }

        return $this;
    }

    public function removeContractCustomer(BiContractCustomer $contractCustomer): self
    {
        if ($this->contractCustomers->removeElement($contractCustomer)) {
            // set the owning side to null (unless already changed)
            if ($contractCustomer->getSite() === $this) {
                $contractCustomer->setSite(null);
            }
        }

        return $this;
    }
}
