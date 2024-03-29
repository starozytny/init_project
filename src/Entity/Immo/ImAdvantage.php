<?php

namespace App\Entity\Immo;

use App\Repository\Immo\ImAdvantageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ImAdvantageRepository::class)
 */
class ImAdvantage
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
    private $hasGarden = ImBien::ANSWER_UNKNOWN;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read", "suivi:read"})
     */
    private $hasTerrace = ImBien::ANSWER_UNKNOWN;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $hasPool = ImBien::ANSWER_UNKNOWN;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $hasCave = ImBien::ANSWER_UNKNOWN;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $hasDigicode = ImBien::ANSWER_UNKNOWN;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $hasInterphone = ImBien::ANSWER_UNKNOWN;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $hasGuardian = ImBien::ANSWER_UNKNOWN;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $hasAlarme = ImBien::ANSWER_UNKNOWN;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read", "suivi:read"})
     */
    private $hasLift = ImBien::ANSWER_UNKNOWN;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $hasClim = ImBien::ANSWER_UNKNOWN;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $hasCalme = ImBien::ANSWER_UNKNOWN;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $hasInternet = ImBien::ANSWER_UNKNOWN;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $hasHandi = ImBien::ANSWER_UNKNOWN;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $hasFibre = ImBien::ANSWER_UNKNOWN;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read"})
     */
    private $situation;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"user:read"})
     */
    private $sousType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"user:read"})
     */
    private $sol;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHasGarden(): ?int
    {
        return $this->hasGarden;
    }

    public function setHasGarden(int $hasGarden): self
    {
        $this->hasGarden = $hasGarden;

        return $this;
    }

    public function getHasTerrace(): ?int
    {
        return $this->hasTerrace;
    }

    public function setHasTerrace(int $hasTerrace): self
    {
        $this->hasTerrace = $hasTerrace;

        return $this;
    }

    public function getHasPool(): ?int
    {
        return $this->hasPool;
    }

    public function setHasPool(int $hasPool): self
    {
        $this->hasPool = $hasPool;

        return $this;
    }

    public function getHasCave(): ?int
    {
        return $this->hasCave;
    }

    public function setHasCave(int $hasCave): self
    {
        $this->hasCave = $hasCave;

        return $this;
    }

    public function getHasDigicode(): ?int
    {
        return $this->hasDigicode;
    }

    public function setHasDigicode(int $hasDigicode): self
    {
        $this->hasDigicode = $hasDigicode;

        return $this;
    }

    public function getHasInterphone(): ?int
    {
        return $this->hasInterphone;
    }

    public function setHasInterphone(int $hasInterphone): self
    {
        $this->hasInterphone = $hasInterphone;

        return $this;
    }

    public function getHasGuardian(): ?int
    {
        return $this->hasGuardian;
    }

    public function setHasGuardian(int $hasGuardian): self
    {
        $this->hasGuardian = $hasGuardian;

        return $this;
    }

    public function getHasAlarme(): ?int
    {
        return $this->hasAlarme;
    }

    public function setHasAlarme(int $hasAlarme): self
    {
        $this->hasAlarme = $hasAlarme;

        return $this;
    }

    public function getHasLift(): ?int
    {
        return $this->hasLift;
    }

    public function setHasLift(int $hasLift): self
    {
        $this->hasLift = $hasLift;

        return $this;
    }

    public function getHasClim(): ?int
    {
        return $this->hasClim;
    }

    public function setHasClim(int $hasClim): self
    {
        $this->hasClim = $hasClim;

        return $this;
    }

    public function getHasCalme(): ?int
    {
        return $this->hasCalme;
    }

    public function setHasCalme(int $hasCalme): self
    {
        $this->hasCalme = $hasCalme;

        return $this;
    }

    public function getHasInternet(): ?int
    {
        return $this->hasInternet;
    }

    public function setHasInternet(int $hasInternet): self
    {
        $this->hasInternet = $hasInternet;

        return $this;
    }

    public function getHasHandi(): ?int
    {
        return $this->hasHandi;
    }

    public function setHasHandi(int $hasHandi): self
    {
        $this->hasHandi = $hasHandi;

        return $this;
    }

    public function getHasFibre(): ?int
    {
        return $this->hasFibre;
    }

    public function setHasFibre(int $hasFibre): self
    {
        $this->hasFibre = $hasFibre;

        return $this;
    }

    public function getSituation(): ?string
    {
        return $this->situation;
    }

    public function setSituation(?string $situation): self
    {
        $this->situation = $situation;

        return $this;
    }

    public function getSousType(): ?int
    {
        return $this->sousType;
    }

    public function setSousType(?int $sousType): self
    {
        $this->sousType = $sousType;

        return $this;
    }

    public function getSol(): ?int
    {
        return $this->sol;
    }

    public function setSol(?int $sol): self
    {
        $this->sol = $sol;

        return $this;
    }
}
