<?php

namespace App\Entity\Bill;

use App\Entity\DataEntity;
use App\Repository\Bill\BiContractRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=BiContractRepository::class)
 */
class BiContract extends DataEntity
{
    const PREFIX = "CO";

    const CONTRACT_READ = ["contract:read"];

    const PERIOD_MENS = 1;
    const PERIOD_TRIM = 2;
    const PERIOD_SEME = 3;
    const PERIOD_ANNU = 4;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"contract:read", "relation:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"contract:read", "relation:read"})
     */
    private $numero = "Z-Brouillon";

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"invoice:read"})
     */
    private $uid;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"contract:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"contract:read"})
     */
    private $period = self::PERIOD_MENS;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"contract:read"})
     */
    private $theme = BiInvoice::THEME_1;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"contract:read"})
     */
    private $isActive = true;

    /**
     * @ORM\OneToMany(targetEntity=BiContractCustomer::class, mappedBy="contract")
     */
    private $contractCustomers;

    /**
     * @ORM\ManyToOne(targetEntity=BiSociety::class, fetch="EAGER", inversedBy="biContracts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $society;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="date")
     */
    private $dateAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"contract:read"})
     */
    private $duration;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"contract:read"})
     */
    private $dueType = BiInvoice::DUE_TYPE_MANUAL;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dueAt;

    /**
     * @ORM\Column(type="float")
     * @Groups({"contract:read"})
     */
    private $totalHt;

    /**
     * @ORM\Column(type="float")
     * @Groups({"contract:read"})
     */
    private $totalRemise;

    /**
     * @ORM\Column(type="float")
     * @Groups({"contract:read"})
     */
    private $totalTva;

    /**
     * @ORM\Column(type="float")
     * @Groups({"contract:read"})
     */
    private $totalTtc;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"contract:read"})
     */
    private $footer;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"contract:read"})
     */
    private $note;

    public function __construct()
    {
        $this->createdAt = $this->initNewDate();
        $this->contractCustomers = new ArrayCollection();
        $this->uid = Uuid::v4();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * @return string
     * @Groups({"contract:read"})
     */
    public function getIdentifiant(): string
    {
        return self::PREFIX . "-" . $this->uid;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

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

    public function getPeriod(): ?int
    {
        return $this->period;
    }

    public function setPeriod(int $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function getTheme(): ?int
    {
        return $this->theme;
    }

    public function setTheme(int $theme): self
    {
        $this->theme = $theme;

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

    /**
     * @return Collection<int, BiContractCustomer>
     */
    public function getContractCustomers(): Collection
    {
        return $this->contractCustomers;
    }

    public function addContractCustomer(BiContractCustomer $contractCustomer): self
    {
        if (!$this->contractCustomers->contains($contractCustomer)) {
            $this->contractCustomers[] = $contractCustomer;
            $contractCustomer->setContract($this);
        }

        return $this;
    }

    public function removeContractCustomer(BiContractCustomer $contractCustomer): self
    {
        if ($this->contractCustomers->removeElement($contractCustomer)) {
            // set the owning side to null (unless already changed)
            if ($contractCustomer->getContract() === $this) {
                $contractCustomer->setContract(null);
            }
        }

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

    /**
     * @return string
     * @Groups({"contract:read"})
     */
    public function getPeriodString(): string
    {
        $values = ["", "Mensuelle", "Trimestrielle", "Semestrielle", "Annuelle"];

        return $values[$this->period];
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

    public function getDateAt(): ?\DateTimeInterface
    {
        return $this->dateAt;
    }

    public function setDateAt(\DateTimeInterface $dateAt): self
    {
        $this->dateAt = $dateAt;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDueType(): ?int
    {
        return $this->dueType;
    }

    public function setDueType(int $dueType): self
    {
        $this->dueType = $dueType;

        return $this;
    }

    public function getDueAt(): ?\DateTimeInterface
    {
        return $this->dueAt;
    }

    public function setDueAt(?\DateTimeInterface $dueAt): self
    {
        $this->dueAt = $dueAt;

        return $this;
    }

    /**
     * @return string|null
     * @Groups({"contract:read"})
     */
    public function getDateAtJavascript(): ?string
    {
        return $this->setDateJavascript($this->dateAt);
    }

    /**
     * @return string|null
     * @Groups({"contract:read"})
     */
    public function getDueAtJavascript(): ?string
    {
        return $this->setDateJavascript($this->dueAt);
    }

    public function getTotalHt(): ?float
    {
        return $this->totalHt;
    }

    public function setTotalHt(float $totalHt): self
    {
        $this->totalHt = $totalHt;

        return $this;
    }

    public function getTotalRemise(): ?float
    {
        return $this->totalRemise;
    }

    public function setTotalRemise(float $totalRemise): self
    {
        $this->totalRemise = $totalRemise;

        return $this;
    }

    public function getTotalTva(): ?float
    {
        return $this->totalTva;
    }

    public function setTotalTva(float $totalTva): self
    {
        $this->totalTva = $totalTva;

        return $this;
    }

    public function getTotalTtc(): ?float
    {
        return $this->totalTtc;
    }

    public function setTotalTtc(float $totalTtc): self
    {
        $this->totalTtc = $totalTtc;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getFooter(): ?string
    {
        return $this->footer;
    }

    public function setFooter(?string $footer): self
    {
        $this->footer = $footer;

        return $this;
    }
}
