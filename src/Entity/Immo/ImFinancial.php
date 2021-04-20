<?php

namespace App\Entity\Immo;

use App\Repository\Immo\ImFinancialRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ImFinancialRepository::class)
 */
class ImFinancial
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("list:read")
     */
    private $price;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("list:read")
     */
    private $commission;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("list:read")
     */
    private $charges;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("list:read")
     */
    private $foncier;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("list:read")
     */
    private $deposit;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("list:read")
     */
    private $partHonoEdl;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("list:read")
     */
    private $complementLoyer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("list:read")
     */
    private $honoChargesDe;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("list:read")
     */
    private $horsHonoAcquereur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("list:read")
     */
    private $modalitesChargesLocataire;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("list:read")
     */
    private $bouquet;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("list:read")
     */
    private $rente;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCommission(): ?float
    {
        return $this->commission;
    }

    public function setCommission(?float $commission): self
    {
        $this->commission = $commission;

        return $this;
    }

    public function getCharges(): ?float
    {
        return $this->charges;
    }

    public function setCharges(?float $charges): self
    {
        $this->charges = $charges;

        return $this;
    }

    public function getFoncier(): ?float
    {
        return $this->foncier;
    }

    public function setFoncier(?float $foncier): self
    {
        $this->foncier = $foncier;

        return $this;
    }

    public function getDeposit(): ?float
    {
        return $this->deposit;
    }

    public function setDeposit(?float $deposit): self
    {
        $this->deposit = $deposit;

        return $this;
    }

    public function getPartHonoEdl(): ?float
    {
        return $this->partHonoEdl;
    }

    public function setPartHonoEdl(?float $partHonoEdl): self
    {
        $this->partHonoEdl = $partHonoEdl;

        return $this;
    }

    public function getComplementLoyer(): ?float
    {
        return $this->complementLoyer;
    }

    public function setComplementLoyer(?float $complementLoyer): self
    {
        $this->complementLoyer = $complementLoyer;

        return $this;
    }

    public function getHonoChargesDe(): ?string
    {
        return $this->honoChargesDe;
    }

    public function setHonoChargesDe(?string $honoChargesDe): self
    {
        $this->honoChargesDe = $honoChargesDe;

        return $this;
    }

    public function getHorsHonoAcquereur(): ?float
    {
        return $this->horsHonoAcquereur;
    }

    public function setHorsHonoAcquereur(?float $horsHonoAcquereur): self
    {
        $this->horsHonoAcquereur = $horsHonoAcquereur;

        return $this;
    }

    public function getModalitesChargesLocataire(): ?string
    {
        return $this->modalitesChargesLocataire;
    }

    public function setModalitesChargesLocataire(?string $modalitesChargesLocataire): self
    {
        $this->modalitesChargesLocataire = $modalitesChargesLocataire;

        return $this;
    }

    public function getBouquet(): ?float
    {
        return $this->bouquet;
    }

    public function setBouquet(?float $bouquet): self
    {
        $this->bouquet = $bouquet;

        return $this;
    }

    public function getRente(): ?float
    {
        return $this->rente;
    }

    public function setRente(?float $rente): self
    {
        $this->rente = $rente;

        return $this;
    }
}
