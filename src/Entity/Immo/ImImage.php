<?php

namespace App\Entity\Immo;

use App\Repository\Immo\ImImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ImImageRepository::class)
 */
class ImImage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("list:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("list:read")
     */
    private $file;

    /**
     * @ORM\Column(type="integer")
     * @Groups("list:read")
     */
    private $rank;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPortrait;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $thumb;

    /**
     * @ORM\ManyToOne(targetEntity=ImBien::class, inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bien;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(int $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    public function getIsPortrait(): ?bool
    {
        return $this->isPortrait;
    }

    public function setIsPortrait(bool $isPortrait): self
    {
        $this->isPortrait = $isPortrait;

        return $this;
    }

    public function getThumb(): ?string
    {
        return $this->thumb;
    }

    public function setThumb(string $thumb): self
    {
        $this->thumb = $thumb;

        return $this;
    }

    public function getBien(): ?ImBien
    {
        return $this->bien;
    }

    public function setBien(?ImBien $bien): self
    {
        $this->bien = $bien;

        return $this;
    }

}
