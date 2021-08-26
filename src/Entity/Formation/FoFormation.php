<?php

namespace App\Entity\Formation;

use App\Entity\DataEntity;
use App\Repository\Formation\FoFormationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=FoFormationRepository::class)
 */
class FoFormation extends DataEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"admin:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"admin:read"})
     */
    private $slug;

    /**
     * @ORM\Column(type="date")
     * @Groups({"admin:read"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"admin:read"})
     */
    private $isPublished;

    /**
     * @ORM\Column(type="text")
     * @Groups({"admin:read"})
     */
    private $content;

    /**
     * @ORM\Column(type="float")
     * @Groups({"admin:read"})
     */
    private $price;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"admin:read"})
     */
    private $rating;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * How long ago a user was added.
     *
     * @return string
     * @Groups({"admin:read"})
     */
    public function getCreatedAtAgo(): string
    {
        return $this->getHowLongAgo($this->createdAt);
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
}
