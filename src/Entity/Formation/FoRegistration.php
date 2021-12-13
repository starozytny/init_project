<?php

namespace App\Entity\Formation;

use App\Entity\DataEntity;
use App\Entity\User;
use App\Repository\Formation\FoRegistrationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FoRegistrationRepository::class)
 */
class FoRegistration extends DataEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="foRegistrations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=FoWorker::class, inversedBy="registrations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $worker;

    /**
     * @ORM\ManyToOne(targetEntity=FoFormation::class, inversedBy="registrations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $formation;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->createdAt = $this->initNewDate();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getWorker(): ?FoWorker
    {
        return $this->worker;
    }

    public function setWorker(?FoWorker $worker): self
    {
        $this->worker = $worker;

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
}
