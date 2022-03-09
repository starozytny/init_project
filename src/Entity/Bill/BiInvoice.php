<?php

namespace App\Entity\Bill;

use App\Entity\DataEntity;
use App\Repository\Bill\BiInvoiceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BiInvoiceRepository::class)
 */
class BiInvoice extends DataEntity
{
    const INVOICE_READ = ['invoice:read'];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"invoice:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"invoice:read"})
     */
    private $numero;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"invoice:read"})
     */
    private $dateAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"invoice:read"})
     */
    private $dueAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"invoice:read"})
     */
    private $fromName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"invoice:read"})
     */
    private $fromAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"invoice:read"})
     */
    private $fromComplement;

    /**
     * @ORM\Column(type="string", length=40)
     * @Groups({"invoice:read"})
     */
    private $fromZipcode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"invoice:read"})
     */
    private $fromCity;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Groups({"invoice:read"})
     */
    private $fromPhone1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"invoice:read"})
     */
    private $fromEmail;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"invoice:read"})
     */
    private $toName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"invoice:read"})
     */
    private $toAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"invoice:read"})
     */
    private $toComplement;

    /**
     * @ORM\Column(type="string", length=40)
     * @Groups({"invoice:read"})
     */
    private $toZipcode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"invoice:read"})
     */
    private $toCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"invoice:read"})
     */
    private $toEmail;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Groups({"invoice:read"})
     */
    private $toPhone1;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"invoice:read"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->createdAt = $this->initNewDate();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getDateAt(): ?\DateTimeInterface
    {
        return $this->dateAt;
    }

    public function setDateAt(\DateTimeInterface $dateAt): self
    {
        $this->dateAt = $dateAt;

        return $this;
    }

    public function getDueAt(): ?\DateTimeInterface
    {
        return $this->dueAt;
    }

    public function setDueAt(?\DateTimeInterface $dueAt): self
    {
        $this->dueAt = $dueAt;

        return $this;
    }

    public function getFromName(): ?string
    {
        return $this->fromName;
    }

    public function setFromName(string $fromName): self
    {
        $this->fromName = $fromName;

        return $this;
    }

    public function getFromAddress(): ?string
    {
        return $this->fromAddress;
    }

    public function setFromAddress(string $fromAddress): self
    {
        $this->fromAddress = $fromAddress;

        return $this;
    }

    public function getFromComplement(): ?string
    {
        return $this->fromComplement;
    }

    public function setFromComplement(?string $fromComplement): self
    {
        $this->fromComplement = $fromComplement;

        return $this;
    }

    public function getFromZipcode(): ?string
    {
        return $this->fromZipcode;
    }

    public function setFromZipcode(string $fromZipcode): self
    {
        $this->fromZipcode = $fromZipcode;

        return $this;
    }

    public function getFromCity(): ?string
    {
        return $this->fromCity;
    }

    public function setFromCity(string $fromCity): self
    {
        $this->fromCity = $fromCity;

        return $this;
    }

    public function getFromPhone1(): ?string
    {
        return $this->fromPhone1;
    }

    public function setFromPhone1(?string $fromPhone1): self
    {
        $this->fromPhone1 = $fromPhone1;

        return $this;
    }

    public function getFromEmail(): ?string
    {
        return $this->fromEmail;
    }

    public function setFromEmail(?string $fromEmail): self
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    public function getToName(): ?string
    {
        return $this->toName;
    }

    public function setToName(string $toName): self
    {
        $this->toName = $toName;

        return $this;
    }

    public function getToAddress(): ?string
    {
        return $this->toAddress;
    }

    public function setToAddress(string $toAddress): self
    {
        $this->toAddress = $toAddress;

        return $this;
    }

    public function getToComplement(): ?string
    {
        return $this->toComplement;
    }

    public function setToComplement(?string $toComplement): self
    {
        $this->toComplement = $toComplement;

        return $this;
    }

    public function getToZipcode(): ?string
    {
        return $this->toZipcode;
    }

    public function setToZipcode(string $toZipcode): self
    {
        $this->toZipcode = $toZipcode;

        return $this;
    }

    public function getToCity(): ?string
    {
        return $this->toCity;
    }

    public function setToCity(string $toCity): self
    {
        $this->toCity = $toCity;

        return $this;
    }

    public function getToEmail(): ?string
    {
        return $this->toEmail;
    }

    public function setToEmail(?string $toEmail): self
    {
        $this->toEmail = $toEmail;

        return $this;
    }

    public function getToPhone1(): ?string
    {
        return $this->toPhone1;
    }

    public function setToPhone1(?string $toPhone1): self
    {
        $this->toPhone1 = $toPhone1;

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
        $updatedAt->setTimezone(new \DateTimeZone("Europe/Paris"));
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return string|null
     * @Groups({"invoice:read"})
     */
    public function getDateAtString(): ?string
    {
        return $this->getFullDateString($this->dateAt);
    }

    /**
     * @return string|null
     * @Groups({"invoice:read"})
     */
    public function getDueAtString(): ?string
    {
        return $this->getFullDateString($this->dueAt);
    }

    /**
     * @return string|null
     * @Groups({"invoice:read"})
     */
    public function getDateAtJavascript(): ?string
    {
        return $this->setDateJavascript($this->dateAt);
    }

    /**
     * @return string|null
     * @Groups({"invoice:read"})
     */
    public function getDueAtJavascript(): ?string
    {
        return $this->setDateJavascript($this->dueAt);
    }
}
