<?php

namespace App\Entity\Immo;

use App\Repository\Immo\ImDiagnosticRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ImDiagnosticRepository::class)
 */
class ImDiagnostic
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
    private $dpeVal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("list:read")
     */
    private $dpeLettre;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("list:read")
     */
    private $gesVal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("list:read")
     */
    private $gesLettre;

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
}
