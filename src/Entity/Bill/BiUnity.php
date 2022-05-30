<?php

namespace App\Entity\Bill;

use App\Repository\Bill\BiUnityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BiUnityRepository::class)
 */
class BiUnity
{
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
     * @ORM\Column(type="boolean")
     * @Groups({"user:read"})
     */
    private $isNatif = false;

    /**
     * @ORM\ManyToOne(targetEntity=BiSociety::class, fetch="EAGER")
     */
    private $society;

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

    public function getIsNatif(): ?bool
    {
        return $this->isNatif;
    }

    public function setIsNatif(bool $isNatif): self
    {
        $this->isNatif = $isNatif;

        return $this;
    }

    public function getSociety(): ?BiSociety
    {
        return $this->society;
    }

    public function setSociety(?BiSociety $society): self
    {
        $this->society = $society;

        return $this;
    }
}
