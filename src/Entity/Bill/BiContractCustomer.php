<?php

namespace App\Entity\Bill;

use App\Entity\DataEntity;
use App\Repository\Bill\BiContractCustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BiContractCustomerRepository::class)
 */
class BiContractCustomer extends DataEntity
{
    const RELATION_READ = ["relation:read"];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"relation:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"relation:read"})
     */
    private $numero;

    /**
     * @ORM\ManyToOne(targetEntity=BiContract::class, fetch="EAGER", inversedBy="contractCustomers")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"relation:read"})
     */
    private $contract;

    /**
     * @ORM\ManyToOne(targetEntity=BiCustomer::class, fetch="EAGER", inversedBy="contractCustomers")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"relation:read"})
     */
    private $customer;

    /**
     * @ORM\ManyToOne(targetEntity=BiSite::class, fetch="EAGER", inversedBy="contractCustomers")
     * @Groups({"relation:read"})
     */
    private $site;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"relation:read"})
     */
    private $lastMonth;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"relation:read"})
     */
    private $lastYear;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"relation:read"})
     */
    private $isActive = true;

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
        $today = $this->initNewDate();
        $this->createdAt = $today;

        $year = (int) $today->format('Y');
        $month = (int) $today->format('n');

        $month = $month == 1 ? 12 : $month - 1;
        $year = $month == 1 ? $year - 1 : $year;

        $this->lastYear = $year;
        $this->lastMonth = $month;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContract(): ?BiContract
    {
        return $this->contract;
    }

    public function setContract(?BiContract $contract): self
    {
        $this->contract = $contract;

        return $this;
    }

    public function getCustomer(): ?BiCustomer
    {
        return $this->customer;
    }

    public function setCustomer(?BiCustomer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getSite(): ?BiSite
    {
        return $this->site;
    }

    public function setSite(?BiSite $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getLastMonth(): ?int
    {
        return $this->lastMonth;
    }

    public function setLastMonth(int $lastMonth): self
    {
        $this->lastMonth = $lastMonth;

        return $this;
    }

    public function getLastYear(): ?int
    {
        return $this->lastYear;
    }

    public function setLastYear(int $lastYear): self
    {
        $this->lastYear = $lastYear;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

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
        if($updatedAt){
            $updatedAt->setTimezone(new \DateTimeZone("Europe/Paris"));
        }
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
