<?php

namespace App\Entity\Agenda;

use App\Entity\DataEntity;
use App\Entity\User;
use App\Repository\Agenda\AgEventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AgEventRepository::class)
 */
class AgEvent extends DataEntity
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_CANCEL = 2;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endAt;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:read"})
     */
    private $allDay = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read"})
     */
    private $location;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"user:read"})
     */
    private $comment;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"user:read"})
     */
    private $persons;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, fetch="EAGER", inversedBy="agEvents")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"user:read"})
     */
    private $creator;

    public function __construct()
    {
        $this->createdAt = $this->initNewDate();
    }

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

    /**
     * @return string|null
     * @Groups({"user:read"})
     */
    public function getStartAtAgenda(): ?string
    {
        return $this->setDateAgenda($this->startAt);
    }

    /**
     * @return string|null
     * @Groups({"user:read"})
     */
    public function getStartAtJavascript(): ?string
    {
        return $this->setDateJavascript($this->startAt);
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(?\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * @return string|null
     * @Groups({"user:read"})
     */
    public function getEndAtAgenda(): ?string
    {
        return $this->setDateAgenda($this->endAt);
    }

    /**
     * @return string|null
     * @Groups({"user:read"})
     */
    public function getEndAtJavascript(): ?string
    {
        return $this->setDateJavascript($this->endAt);
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getAllDay(): ?bool
    {
        return $this->allDay;
    }

    public function setAllDay(bool $allDay): self
    {
        $this->allDay = $allDay;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $updatedAt->setTimezone(new \DateTimeZone("Europe/Paris"));
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPersons(): ?string
    {
        return $this->persons;
    }

    public function setPersons(?string $persons): self
    {
        $this->persons = $persons;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * @return string
     * @Groups({"user:read"})
     */
    public function getStatusString(): string
    {
        $status = ["Inactif", "Actif", "Annulé"];

        return $status[$this->status];
    }
}
