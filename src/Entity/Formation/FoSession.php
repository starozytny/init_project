<?php

namespace App\Entity\Formation;

use App\Repository\Formation\FoSessionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FoSessionRepository::class)
 */
class FoSession
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $start;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $end;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $time2;

    /**
     * @ORM\Column(type="float")
     */
    private $priceHT;

    /**
     * @ORM\Column(type="float")
     */
    private $priceTTC;

    /**
     * @ORM\Column(type="float")
     */
    private $tva;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $duration;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $duration2;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $durationTotal;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $durationByDay;

    /**
     * @ORM\Column(type="integer")
     */
    private $max;

    /**
     * @ORM\Column(type="integer")
     */
    private $min;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $animator;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $zipcode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $modTrav;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $modEval;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $modPeda;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $modAssi;

    /**
     * @ORM\ManyToOne(targetEntity=FoFormation::class, inversedBy="sessions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $formation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(?\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getTime(): ?string
    {
        return $this->time;
    }

    public function setTime(string $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getTime2(): ?string
    {
        return $this->time2;
    }

    public function setTime2(?string $time2): self
    {
        $this->time2 = $time2;

        return $this;
    }

    public function getPriceHT(): ?float
    {
        return $this->priceHT;
    }

    public function setPriceHT(float $priceHT): self
    {
        $this->priceHT = $priceHT;

        return $this;
    }

    public function getPriceTTC(): ?float
    {
        return $this->priceTTC;
    }

    public function setPriceTTC(float $priceTTC): self
    {
        $this->priceTTC = $priceTTC;

        return $this;
    }

    public function getTva(): ?float
    {
        return $this->tva;
    }

    public function setTva(float $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDuration2(): ?string
    {
        return $this->duration2;
    }

    public function setDuration2(?string $duration2): self
    {
        $this->duration2 = $duration2;

        return $this;
    }

    public function getDurationTotal(): ?string
    {
        return $this->durationTotal;
    }

    public function setDurationTotal(string $durationTotal): self
    {
        $this->durationTotal = $durationTotal;

        return $this;
    }

    public function getDurationByDay(): ?string
    {
        return $this->durationByDay;
    }

    public function setDurationByDay(string $durationByDay): self
    {
        $this->durationByDay = $durationByDay;

        return $this;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(int $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function getMin(): ?int
    {
        return $this->min;
    }

    public function setMin(int $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function getAnimator(): ?string
    {
        return $this->animator;
    }

    public function setAnimator(string $animator): self
    {
        $this->animator = $animator;

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

    public function getZipcode(): ?int
    {
        return $this->zipcode;
    }

    public function setZipcode(?int $zipcode): self
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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getModTrav(): ?string
    {
        return $this->modTrav;
    }

    public function setModTrav(?string $modTrav): self
    {
        $this->modTrav = $modTrav;

        return $this;
    }

    public function getModEval(): ?string
    {
        return $this->modEval;
    }

    public function setModEval(?string $modEval): self
    {
        $this->modEval = $modEval;

        return $this;
    }

    public function getModPeda(): ?string
    {
        return $this->modPeda;
    }

    public function setModPeda(?string $modPeda): self
    {
        $this->modPeda = $modPeda;

        return $this;
    }

    public function getModAssi(): ?string
    {
        return $this->modAssi;
    }

    public function setModAssi(?string $modAssi): self
    {
        $this->modAssi = $modAssi;

        return $this;
    }

    public function getFormation(): ?FoFormation
    {
        return $this->formation;
    }

    public function setFormation(?FoFormation $formation): self
    {
        $this->formation = $formation;

        return $this;
    }
}
