<?php

namespace App\Entity\Bill;

use App\Repository\Bill\BiProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BiProductRepository::class)
 */
class BiProduct
{
    const PRODUCT_READ = ["product:read"];

    const TYPE_INVOICE = 0;
    const TYPE_QUOTATION = 1;
    const TYPE_CONTRACT = 2;
    const TYPE_AVOIR = 3;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"product:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"product:read"})
     */
    private $identifiant;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"product:read"})
     */
    private $uid;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Groups({"product:read"})
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"product:read"})
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"product:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"product:read"})
     */
    private $content;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"product:read"})
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Groups({"product:read"})
     */
    private $unity;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"product:read"})
     */
    private $price;

    /**
     * @ORM\Column(type="float")
     * @Groups({"product:read"})
     */
    private $rateTva = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"product:read"})
     */
    private $codeTva = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"product:read"})
     */
    private $type = self::TYPE_INVOICE;

    /**
     * @ORM\ManyToOne(targetEntity=BiSociety::class, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $society;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): self
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(?float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnity(): ?string
    {
        return $this->unity;
    }

    public function setUnity(?string $unity): self
    {
        $this->unity = $unity;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getRateTva(): ?float
    {
        return $this->rateTva;
    }

    public function setRateTva(float $rateTva): self
    {
        $this->rateTva = $rateTva;

        return $this;
    }

    public function getIdentifiant(): ?string
    {
        return $this->identifiant;
    }

    public function setIdentifiant(string $identifiant): self
    {
        $this->identifiant = $identifiant;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

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

    public function getCodeTva(): ?int
    {
        return $this->codeTva;
    }

    public function setCodeTva(int $codeTva): self
    {
        $this->codeTva = $codeTva;

        return $this;
    }
}
