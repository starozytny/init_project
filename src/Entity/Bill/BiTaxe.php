<?php

namespace App\Entity\Bill;

use App\Entity\Society;
use App\Repository\Bill\BiTaxeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BiTaxeRepository::class)
 */
class BiTaxe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $code;

    /**
     * @ORM\Column(type="float")
     * @Groups({"user:read"})
     */
    private $rate;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:read"})
     */
    private $isNatif = false;

    /**
     * @ORM\ManyToOne(targetEntity=Society::class)
     */
    private $society;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getIsNatif(): ?bool
    {
        return $this->isNatif;
    }

    public function setIsNatif(bool $isNatif): self
    {
        $this->isNatif = $isNatif;

        return $this;
    }

    public function getSociety(): ?Society
    {
        return $this->society;
    }

    public function setSociety(?Society $society): self
    {
        $this->society = $society;

        return $this;
    }
}
