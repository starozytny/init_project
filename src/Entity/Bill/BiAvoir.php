<?php

namespace App\Entity\Bill;

use App\Entity\DataEntity;
use App\Repository\Bill\BiAvoirRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=BiAvoirRepository::class)
 */
class BiAvoir extends DataEntity
{
    const PREFIX = "AV";

    const AVOIR_READ = ['avoir:read'];

    const THEME_1 = 1;

    const STATUS_DRAFT = 0;
    const STATUS_ACTIF = 1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"avoir:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"avoir:read"})
     */
    private $numero = "Z-Brouillon";

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"avoir:read"})
     */
    private $uid;

    /**
     * @ORM\Column(type="date")
     * @Groups({"avoir:read"})
     */
    private $dateAt;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"avoir:read", "invoice-contract:read"})
     */
    private $status = self::STATUS_DRAFT;

    /**
     * @ORM\Column(type="float")
     * @Groups({"avoir:read"})
     */
    private $totalHt;

    /**
     * @ORM\Column(type="float")
     * @Groups({"avoir:read"})
     */
    private $totalRemise;

    /**
     * @ORM\Column(type="float")
     * @Groups({"avoir:read"})
     */
    private $totalTva;

    /**
     * @ORM\Column(type="float")
     * @Groups({"avoir:read", "invoice-contract:read"})
     */
    private $totalTtc;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"avoir:read"})
     */
    private $fromName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"avoir:read"})
     */
    private $fromAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $fromAddress2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $fromComplement;

    /**
     * @ORM\Column(type="string", length=40)
     * @Groups({"avoir:read"})
     */
    private $fromZipcode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"avoir:read"})
     */
    private $fromCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $fromCountry;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $fromPhone1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $fromEmail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fromSiren;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fromTva;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"avoir:read"})
     */
    private $toName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"avoir:read"})
     */
    private $toAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $toAddress2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $toComplement;

    /**
     * @ORM\Column(type="string", length=40)
     * @Groups({"avoir:read"})
     */
    private $toZipcode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"avoir:read"})
     */
    private $toCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $toCountry;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $toEmail;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $toPhone1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $siEmail;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $siPhone1;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"avoir:read"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=BiSociety::class, fetch="EAGER", inversedBy="biInvoices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $society;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"avoir:read"})
     */
    private $footer;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"avoir:read"})
     */
    private $note;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"avoir:read"})
     */
    private $noteProduct;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $logo;

    /**
     * @ORM\Column(type="integer")
     */
    private $theme = self::THEME_1;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"avoir:read"})
     */
    private $isSent = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"avoir:read"})
     */
    private $isSeen = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"avoir:read"})
     */
    private $isArchived = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"avoir:read"})
     */
    private $customerId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"avoir:read"})
     */
    private $siteId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $refCustomer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $refSite;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $siName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $siAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $siAddress2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $siComplement;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $siZipcode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $siCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $siCountry;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"avoir:read"})
     */
    private $invoiceId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"avoir:read"})
     */
    private $refInvoice;

    public function __construct()
    {
        $this->createdAt = $this->initNewDate();
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

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

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

    public function getFromName(): ?string
    {
        return $this->fromName;
    }

    public function setFromName(string $fromName): self
    {
        $this->fromName = $fromName;

        return $this;
    }

    public function getFromAddress(): ?string
    {
        return $this->fromAddress;
    }

    public function setFromAddress(string $fromAddress): self
    {
        $this->fromAddress = $fromAddress;

        return $this;
    }

    public function getFromComplement(): ?string
    {
        return $this->fromComplement;
    }

    public function setFromComplement(?string $fromComplement): self
    {
        $this->fromComplement = $fromComplement;

        return $this;
    }

    public function getFromZipcode(): ?string
    {
        return $this->fromZipcode;
    }

    public function setFromZipcode(string $fromZipcode): self
    {
        $this->fromZipcode = $fromZipcode;

        return $this;
    }

    public function getFromCity(): ?string
    {
        return $this->fromCity;
    }

    public function setFromCity(string $fromCity): self
    {
        $this->fromCity = $fromCity;

        return $this;
    }

    public function getFromPhone1(): ?string
    {
        return $this->fromPhone1;
    }

    public function setFromPhone1(?string $fromPhone1): self
    {
        $this->fromPhone1 = $fromPhone1;

        return $this;
    }

    public function getFromEmail(): ?string
    {
        return $this->fromEmail;
    }

    public function setFromEmail(?string $fromEmail): self
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    public function getToName(): ?string
    {
        return $this->toName;
    }

    public function setToName(string $toName): self
    {
        $this->toName = $toName;

        return $this;
    }

    public function getToAddress(): ?string
    {
        return $this->toAddress;
    }

    public function setToAddress(string $toAddress): self
    {
        $this->toAddress = $toAddress;

        return $this;
    }

    public function getToComplement(): ?string
    {
        return $this->toComplement;
    }

    public function setToComplement(?string $toComplement): self
    {
        $this->toComplement = $toComplement;

        return $this;
    }

    public function getToZipcode(): ?string
    {
        return $this->toZipcode;
    }

    public function setToZipcode(string $toZipcode): self
    {
        $this->toZipcode = $toZipcode;

        return $this;
    }

    public function getToCity(): ?string
    {
        return $this->toCity;
    }

    public function setToCity(string $toCity): self
    {
        $this->toCity = $toCity;

        return $this;
    }

    public function getToEmail(): ?string
    {
        return $this->toEmail;
    }

    public function setToEmail(?string $toEmail): self
    {
        $this->toEmail = $toEmail;

        return $this;
    }

    public function getToPhone1(): ?string
    {
        return $this->toPhone1;
    }

    public function setToPhone1(?string $toPhone1): self
    {
        $this->toPhone1 = $toPhone1;

        return $this;
    }

    public function getSiEmail(): ?string
    {
        return $this->siEmail;
    }

    public function setSiEmail(?string $siEmail): self
    {
        $this->siEmail = $siEmail;

        return $this;
    }

    public function getSiPhone1(): ?string
    {
        return $this->siPhone1;
    }

    public function setSiPhone1(?string $siPhone1): self
    {
        $this->siPhone1 = $siPhone1;

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

    /**
     * @return string|null
     * @Groups({"avoir:read"})
     */
    public function getDateAtString(): ?string
    {
        return $this->getFullDateString($this->dateAt, "ll", false);
    }

    /**
     * @return string|null
     * @Groups({"avoir:read", "invoice-contract:read"})
     */
    public function getDateAtJavascript(): ?string
    {
        return $this->setDateJavascript($this->dateAt);
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

    public function getTotalHt(): ?float
    {
        return $this->totalHt;
    }

    public function setTotalHt(float $totalHt): self
    {
        $this->totalHt = $totalHt;

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

    public function getNoteProduct(): ?string
    {
        return $this->noteProduct;
    }

    public function setNoteProduct(?string $noteProduct): self
    {
        $this->noteProduct = $noteProduct;

        return $this;
    }

    /**
     * @return string
     * @Groups({"avoir:read"})
     */
    public function getStatusString(): string
    {
        $values = ["Brouillon", "Actif"];

        return $values[$this->status];
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

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getFromSiren(): ?string
    {
        return $this->fromSiren;
    }

    public function setFromSiren(?string $fromSiren): self
    {
        $this->fromSiren = $fromSiren;

        return $this;
    }

    public function getFromTva(): ?string
    {
        return $this->fromTva;
    }

    public function setFromTva(?string $fromTva): self
    {
        $this->fromTva = $fromTva;

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

    public function getTheme(): ?int
    {
        return $this->theme;
    }

    public function setTheme(int $theme): self
    {
        $this->theme = $theme;

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

    /**
     * @return string
     * @Groups({"avoir:read"})
     */
    public function getIdentifiant(): string
    {
        return self::PREFIX . "-" . $this->uid;
    }

    public function getIsSent(): ?bool
    {
        return $this->isSent;
    }

    public function setIsSent(bool $isSent): self
    {
        $this->isSent = $isSent;

        return $this;
    }

    public function getIsSeen(): ?bool
    {
        return $this->isSeen;
    }

    public function setIsSeen(bool $isSeen): self
    {
        $this->isSeen = $isSeen;

        return $this;
    }

    public function getIsArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): self
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function getFromCountry(): ?string
    {
        return $this->fromCountry;
    }

    public function setFromCountry(?string $fromCountry): self
    {
        $this->fromCountry = $fromCountry;

        return $this;
    }

    public function getToCountry(): ?string
    {
        return $this->toCountry;
    }

    public function setToCountry(?string $toCountry): self
    {
        $this->toCountry = $toCountry;

        return $this;
    }

    public function getToAddress2(): ?string
    {
        return $this->toAddress2;
    }

    public function setToAddress2(?string $toAddress2): self
    {
        $this->toAddress2 = $toAddress2;

        return $this;
    }

    public function getFromAddress2(): ?string
    {
        return $this->fromAddress2;
    }

    public function setFromAddress2(?string $fromAddress2): self
    {
        $this->fromAddress2 = $fromAddress2;

        return $this;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(?int $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getSiteId(): ?int
    {
        return $this->siteId;
    }

    public function setSiteId(?int $siteId): self
    {
        $this->siteId = $siteId;

        return $this;
    }

    public function getSiName(): ?string
    {
        return $this->siName;
    }

    public function setSiName(?string $siName): self
    {
        $this->siName = $siName;

        return $this;
    }

    public function getSiAddress(): ?string
    {
        return $this->siAddress;
    }

    public function setSiAddress(?string $siAddress): self
    {
        $this->siAddress = $siAddress;

        return $this;
    }

    public function getSiAddress2(): ?string
    {
        return $this->siAddress2;
    }

    public function setSiAddress2(?string $siAddress2): self
    {
        $this->siAddress2 = $siAddress2;

        return $this;
    }

    public function getSiComplement(): ?string
    {
        return $this->siComplement;
    }

    public function setSiComplement(?string $siComplement): self
    {
        $this->siComplement = $siComplement;

        return $this;
    }

    public function getSiZipcode(): ?string
    {
        return $this->siZipcode;
    }

    public function setSiZipcode(?string $siZipcode): self
    {
        $this->siZipcode = $siZipcode;

        return $this;
    }

    public function getSiCity(): ?string
    {
        return $this->siCity;
    }

    public function setSiCity(?string $siCity): self
    {
        $this->siCity = $siCity;

        return $this;
    }

    public function getSiCountry(): ?string
    {
        return $this->siCountry;
    }

    public function setSiCountry(?string $siCountry): self
    {
        $this->siCountry = $siCountry;

        return $this;
    }

    public function getRefCustomer(): ?string
    {
        return $this->refCustomer;
    }

    public function setRefCustomer(?string $refCustomer): self
    {
        $this->refCustomer = $refCustomer;

        return $this;
    }

    public function getRefSite(): ?string
    {
        return $this->refSite;
    }

    public function setRefSite(?string $refSite): self
    {
        $this->refSite = $refSite;

        return $this;
    }

    public function getInvoiceId(): ?int
    {
        return $this->invoiceId;
    }

    public function setInvoiceId(?int $invoiceId): self
    {
        $this->invoiceId = $invoiceId;

        return $this;
    }

    public function getRefInvoice(): ?string
    {
        return $this->refInvoice;
    }

    public function setRefInvoice(?string $refInvoice): self
    {
        $this->refInvoice = $refInvoice;

        return $this;
    }
}
