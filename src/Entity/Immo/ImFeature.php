<?php

namespace App\Entity\Immo;

use App\Repository\Immo\ImFeatureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ImFeatureRepository::class)
 */
class ImFeature
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
    private $area;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("list:read")
     */
    private $areaLand;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("list:read")
     */
    private $areaLiving;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $nbSdb;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $nbSle;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $nbWc;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $isWcSeparate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $floor;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $nbFloor;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $nbPiece;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $nbRoom;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $nbBalcony;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $isMeuble;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $constructionYear;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $isRefaitNeuf;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("list:read")
     */
    private $heating;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("list:read")
     */
    private $kitchen;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $isSouth;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $isEast;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $isWest;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $isNorth;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArea(): ?float
    {
        return $this->area;
    }

    public function setArea(?float $area): self
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

    public function getAreaLiving(): ?float
    {
        return $this->areaLiving;
    }

    public function setAreaLiving(?float $areaLiving): self
    {
        $this->areaLiving = $areaLiving;

        return $this;
    }

    public function getNbSdb(): ?int
    {
        return $this->nbSdb;
    }

    public function setNbSdb(?int $nbSdb): self
    {
        $this->nbSdb = $nbSdb;

        return $this;
    }

    public function getNbSle(): ?int
    {
        return $this->nbSle;
    }

    public function setNbSle(?int $nbSle): self
    {
        $this->nbSle = $nbSle;

        return $this;
    }

    public function getNbWc(): ?int
    {
        return $this->nbWc;
    }

    public function setNbWc(?int $nbWc): self
    {
        $this->nbWc = $nbWc;

        return $this;
    }

    public function getIsWcSeparate(): ?int
    {
        return $this->isWcSeparate;
    }

    public function setIsWcSeparate(?int $isWcSeparate): self
    {
        $this->isWcSeparate = $isWcSeparate;

        return $this;
    }

    public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function setFloor(?int $floor): self
    {
        $this->floor = $floor;

        return $this;
    }

    public function getNbFloor(): ?int
    {
        return $this->nbFloor;
    }

    public function setNbFloor(?int $nbFloor): self
    {
        $this->nbFloor = $nbFloor;

        return $this;
    }

    public function getNbPiece(): ?int
    {
        return $this->nbPiece;
    }

    public function setNbPiece(?int $nbPiece): self
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

    public function getNbBalcony(): ?int
    {
        return $this->nbBalcony;
    }

    public function setNbBalcony(?int $nbBalcony): self
    {
        $this->nbBalcony = $nbBalcony;

        return $this;
    }

    public function getIsMeuble(): ?int
    {
        return $this->isMeuble;
    }

    public function setIsMeuble(?int $isMeuble): self
    {
        $this->isMeuble = $isMeuble;

        return $this;
    }

    public function getConstructionYear(): ?int
    {
        return $this->constructionYear;
    }

    public function setConstructionYear(?int $constructionYear): self
    {
        $this->constructionYear = $constructionYear;

        return $this;
    }

    public function getIsRefaitNeuf(): ?int
    {
        return $this->isRefaitNeuf;
    }

    public function setIsRefaitNeuf(?int $isRefaitNeuf): self
    {
        $this->isRefaitNeuf = $isRefaitNeuf;

        return $this;
    }

    public function getHeating(): ?string
    {
        return $this->heating;
    }

    public function setHeating(?string $heating): self
    {
        $this->heating = $heating;

        return $this;
    }

    public function getKitchen(): ?string
    {
        return $this->kitchen;
    }

    public function setKitchen(?string $kitchen): self
    {
        $this->kitchen = $kitchen;

        return $this;
    }

    public function getIsSouth(): ?int
    {
        return $this->isSouth;
    }

    public function setIsSouth(?int $isSouth): self
    {
        $this->isSouth = $isSouth;

        return $this;
    }

    public function getIsEast(): ?int
    {
        return $this->isEast;
    }

    public function setIsEast(?int $isEast): self
    {
        $this->isEast = $isEast;

        return $this;
    }

    public function getIsWest(): ?int
    {
        return $this->isWest;
    }

    public function setIsWest(?int $isWest): self
    {
        $this->isWest = $isWest;

        return $this;
    }

    public function getIsNorth(): ?int
    {
        return $this->isNorth;
    }

    public function setIsNorth(?int $isNorth): self
    {
        $this->isNorth = $isNorth;

        return $this;
    }
}
