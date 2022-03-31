<?php

namespace App\Entity\Bill;

use App\Entity\DataEntity;
use App\Entity\Society;
use App\Repository\Bill\BiItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BiItemRepository::class)
 */
class BiItem extends DataEntity
{
    const FOLDER_IMAGES = "bill/items";

    const ITEM_READ = ['item:read'];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"item:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Groups({"item:read"})
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"item:read"})
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"item:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"item:read"})
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=60)
     * @Groups({"item:read"})
     */
    private $unity;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"item:read"})
     */
    private $price;

    /**
     * @ORM\Column(type="float")
     * @Groups({"item:read"})
     */
    private $rateTva;

    /**
     * @ORM\ManyToOne(targetEntity=Society::class, inversedBy="biItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $society;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"item:read"})
     */
    private $image;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getUnity(): ?string
    {
        return $this->unity;
    }

    public function setUnity(string $unity): self
    {
        $this->unity = $unity;

        return $this;
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

    public function getRateTva(): ?float
    {
        return $this->rateTva;
    }

    public function setRateTva(float $rateTva): self
    {
        $this->rateTva = $rateTva;

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

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string
     * @Groups({"item:read"})
     */
    public function getImageFile(): string
    {
        return $this->getFileOrDefault($this->image, self::FOLDER_IMAGES, "https://robohash.org/" . $this->image . "?size=64x64");
    }
}
