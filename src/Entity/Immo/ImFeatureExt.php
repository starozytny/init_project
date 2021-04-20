<?php

namespace App\Entity\Immo;

use App\Repository\Immo\ImFeatureExtRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ImFeatureExtRepository::class)
 */
class ImFeatureExt
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $nbParking;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $nbBox;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $hasElevator;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $hasCellar;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $hasIntercom;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $hasConcierge;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $hasTerrace;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $hasClim;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $hasPiscine;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbParking(): ?int
    {
        return $this->nbParking;
    }

    public function setNbParking(?int $nbParking): self
    {
        $this->nbParking = $nbParking;

        return $this;
    }

    public function getNbBox(): ?int
    {
        return $this->nbBox;
    }

    public function setNbBox(?int $nbBox): self
    {
        $this->nbBox = $nbBox;

        return $this;
    }

    public function getHasElevator(): ?int
    {
        return $this->hasElevator;
    }

    public function setHasElevator(?int $hasElevator): self
    {
        $this->hasElevator = $hasElevator;

        return $this;
    }

    public function getHasCellar(): ?int
    {
        return $this->hasCellar;
    }

    public function setHasCellar(?int $hasCellar): self
    {
        $this->hasCellar = $hasCellar;

        return $this;
    }

    public function getHasIntercom(): ?int
    {
        return $this->hasIntercom;
    }

    public function setHasIntercom(?int $hasIntercom): self
    {
        $this->hasIntercom = $hasIntercom;

        return $this;
    }

    public function getHasConcierge(): ?int
    {
        return $this->hasConcierge;
    }

    public function setHasConcierge(?int $hasConcierge): self
    {
        $this->hasConcierge = $hasConcierge;

        return $this;
    }

    public function getHasTerrace(): ?int
    {
        return $this->hasTerrace;
    }

    public function setHasTerrace(?int $hasTerrace): self
    {
        $this->hasTerrace = $hasTerrace;

        return $this;
    }

    public function getHasClim(): ?int
    {
        return $this->hasClim;
    }

    public function setHasClim(?int $hasClim): self
    {
        $this->hasClim = $hasClim;

        return $this;
    }

    public function getHasPiscine(): ?int
    {
        return $this->hasPiscine;
    }

    public function setHasPiscine(?int $hasPiscine): self
    {
        $this->hasPiscine = $hasPiscine;

        return $this;
    }
}
