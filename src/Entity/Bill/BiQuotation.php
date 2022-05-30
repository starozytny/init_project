<?php

namespace App\Entity\Bill;

use App\Entity\DataEntity;
use App\Repository\Bill\BiQuotationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=BiQuotationRepository::class)
 */
class BiQuotation extends DataEntity
{
    const PREFIX = "DE";

    const QUOTATION_READ = ['quotation:read'];

    const THEME_1 = 1;

    const STATUS_DRAFT = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_REFUSED = 3;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"quotation:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"quotation:read"})
     */
    private $numero = "Z-Brouillon";

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"invoice:read"})
     */
    private $uid;

    /**
     * @ORM\Column(type="date")
     * @Groups({"quotation:read"})
     */
    private $dateAt;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"quotation:read"})
     */
    private $valideTo;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"quotation:read"})
     */
    private $status = BiInvoice::STATUS_DRAFT;

    /**
     * @ORM\Column(type="float")
     * @Groups({"quotation:read"})
     */
    private $totalHt;

    /**
     * @ORM\Column(type="float")
     * @Groups({"quotation:read"})
     */
    private $totalRemise;

    /**
     * @ORM\Column(type="float")
     * @Groups({"quotation:read"})
     */
    private $totalTva;

    /**
     * @ORM\Column(type="float")
     * @Groups({"quotation:read"})
     */
    private $totalTtc;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"quotation:read"})
     */
    private $fromName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"quotation:read"})
     */
    private $fromAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $fromAddress2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $fromComplement;

    /**
     * @ORM\Column(type="string", length=40)
     * @Groups({"quotation:read"})
     */
    private $fromZipcode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"quotation:read"})
     */
    private $fromCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $fromCountry;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $fromPhone1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"quotation:read"})
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
     * @Groups({"quotation:read"})
     */
    private $toName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"quotation:read"})
     */
    private $toAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $toAddress2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $toComplement;

    /**
     * @ORM\Column(type="string", length=40)
     * @Groups({"quotation:read"})
     */
    private $toZipcode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"quotation:read"})
     */
    private $toCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $toCountry;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $toEmail;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $toPhone1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $siEmail;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $siPhone1;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"quotation:read"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"quotation:read"})
     */
    private $footer;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"quotation:read"})
     */
    private $note;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $logo = "data:image/png;base64,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";

    /**
     * @ORM\Column(type="integer")
     */
    private $theme = self::THEME_1;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"quotation:read"})
     */
    private $isSent = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"quotation:read"})
     */
    private $isSeen = false;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"quotation:read"})
     */
    private $isArchived = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"quotation:read"})
     */
    private $customerId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"quotation:read"})
     */
    private $siteId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $refCustomer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $refSite;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $siName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $siAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $siAddress2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $siComplement;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $siZipcode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $siCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"quotation:read"})
     */
    private $siCountry;

    /**
     * @ORM\ManyToOne(targetEntity=BiSociety::class, fetch="EAGER", inversedBy="biQuotations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $society;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"quotation:read"})
     */
    private $invoiceId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"quotation:read"})
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

    public function getValideTo(): ?\DateTimeInterface
    {
        return $this->valideTo;
    }

    public function setValideTo(?\DateTimeInterface $valideTo): self
    {
        $this->valideTo = $valideTo;

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
     * @Groups({"quotation:read"})
     */
    public function getDateAtString(): ?string
    {
        return $this->getFullDateString($this->dateAt, "ll", false);
    }

    /**
     * @return string|null
     * @Groups({"quotation:read"})
     */
    public function getValideToString(): ?string
    {
        return $this->getFullDateString($this->valideTo, "ll", false);
    }

    /**
     * @return string|null
     * @Groups({"quotation:read"})
     */
    public function getDateAtJavascript(): ?string
    {
        return $this->setDateJavascript($this->dateAt);
    }

    /**
     * @return string|null
     * @Groups({"quotation:read"})
     */
    public function getValideToJavascript(): ?string
    {
        return $this->setDateJavascript($this->valideTo);
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

    /**
     * @return string
     * @Groups({"quotation:read"})
     */
    public function getStatusString(): string
    {
        $values = ["Brouillon", "En cours", "Accepté", "Refusé"];

        return $values[$this->status];
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
     * @Groups({"quotation:read"})
     */
    public function getIdentifiant(): string
    {
        return self::PREFIX . "-" . $this->uid;
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
