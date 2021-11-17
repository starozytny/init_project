<?php

namespace App\Entity\Immo\Ad;

use App\Entity\DataEntity;
use App\Repository\Immo\Ad\ImDiagnosticRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ImDiagnosticRepository::class)
 */
class ImDiagnostic extends DataEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dpeVal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dpeLettre;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $gesVal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gesLettre;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateRelease;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $versionDpe;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $dpeMinConso;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $dpeMaxConso;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dpeRefConso;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDpeVal(): ?int
    {
        return $this->dpeVal;
    }

    public function setDpeVal(?int $dpeVal): self
    {
        $this->dpeVal = $dpeVal;

        return $this;
    }

    public function getDpeLettre(): ?string
    {
        return $this->dpeLettre;
    }

    public function setDpeLettre(?string $dpeLettre): self
    {
        $this->dpeLettre = $dpeLettre;

        return $this;
    }

    public function getGesVal(): ?int
    {
        return $this->gesVal;
    }

    public function setGesVal(?int $gesVal): self
    {
        $this->gesVal = $gesVal;

        return $this;
    }

    public function getGesLettre(): ?string
    {
        return $this->gesLettre;
    }

    public function setGesLettre(?string $gesLettre): self
    {
        $this->gesLettre = $gesLettre;

        return $this;
    }

    public function getDateReleaseString(): ?string
    {
        return $this->getFullDateString($this->dateRelease);
    }

    public function getDateRelease(): ?\DateTimeInterface
    {
        return $this->dateRelease;
    }

    public function setDateRelease(?\DateTimeInterface $dateRelease): self
    {
        $this->dateRelease = $dateRelease;

        return $this;
    }

    public function getVersionDpe(): ?string
    {
        return $this->versionDpe;
    }

    public function setVersionDpe(?string $versionDpe): self
    {
        $this->versionDpe = $versionDpe;

        return $this;
    }

    public function getDpeMinConso(): ?float
    {
        return $this->dpeMinConso;
    }

    public function setDpeMinConso(?float $dpeMinConso): self
    {
        $this->dpeMinConso = $dpeMinConso;

        return $this;
    }

    public function getDpeMaxConso(): ?float
    {
        return $this->dpeMaxConso;
    }

    public function setDpeMaxConso(?float $dpeMaxConso): self
    {
        $this->dpeMaxConso = $dpeMaxConso;

        return $this;
    }

    public function getDpeRefConso(): ?int
    {
        return $this->dpeRefConso;
    }

    public function setDpeRefConso(?int $dpeRefConso): self
    {
        $this->dpeRefConso = $dpeRefConso;

        return $this;
    }
}
