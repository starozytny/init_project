<?php

namespace App\Entity\Bill;

use App\Entity\DataEntity;
use App\Repository\Bill\BiSocietyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BiSocietyRepository::class)
 */
class BiSociety extends DataEntity
{
    const FOLDER_LOGOS = "societies/logos";

    const BILL_READ = ["bill-society:read"];

    const FORME_EURL = 0;
    const FORME_SARL = 1;
    const FORME_SA = 2;
    const FORME_SNC = 3;
    const FORME_SAS = 4;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"admin:read", "bill-society:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"admin:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="integer", unique=true)
     * @Groups({"admin:read"})
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read"})
     */
    private $logo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read"})
     */
    private $siren;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read"})
     */
    private $siret;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read"})
     */
    private $rcs;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read"})
     */
    private $numeroTva;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $forme = self::FORME_SARL;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Groups({"admin:read"})
     */
    private $phone1;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"admin:read"})
     */
    private $address = "A définir";

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups({"admin:read"})
     */
    private $zipcode = 13860;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"admin:read"})
     */
    private $city = "Marseille";

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read"})
     */
    private $complement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read"})
     */
    private $country = "France";

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read"})
     */
    private $address2;

    /**
     * @ORM\OneToMany(targetEntity=BiInvoice::class, mappedBy="society")
     */
    private $biInvoices;

    /**
     * @ORM\OneToMany(targetEntity=BiItem::class, mappedBy="society")
     */
    private $biItems;

    /**
     * @ORM\OneToMany(targetEntity=BiCustomer::class, mappedBy="society")
     */
    private $biCustomers;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read"})
     */
    private $bankName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read"})
     */
    private $bankNumero;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read"})
     */
    private $bankTitulaire;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read"})
     */
    private $bankBic;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Groups({"admin:read"})
     */
    private $bankCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read"})
     */
    private $bankIban;

    /**
     * @ORM\OneToMany(targetEntity=BiSite::class, mappedBy="society")
     */
    private $biSites;

    /**
     * @ORM\OneToMany(targetEntity=BiContract::class, mappedBy="society")
     */
    private $biContracts;

    /**
     * @ORM\OneToMany(targetEntity=BiQuotation::class, mappedBy="society")
     */
    private $biQuotations;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"admin:read"})
     */
    private $footerInvoice;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"admin:read"})
     */
    private $noteInvoice;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"admin:read"})
     */
    private $footerQuotation;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"admin:read"})
     */
    private $noteQuotation;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"admin:read"})
     */
    private $footerAvoir;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"admin:read"})
     */
    private $noteAvoir;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $counterInvoice = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $counterQuotation = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $counterContract = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $counterCustomer = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $counterAvoir = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $counterSite = 0;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $yearInvoice = 2022;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $yearQuotation = 2022;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $yearContract = 2022;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $yearCustomer = 2022;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $yearAvoir = 2022;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $yearSite = 2022;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateInvoice;

    /**
     * @ORM\Column(type="date")
     */
    private $dateContract;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCompta;

    public function __construct()
    {
        $today = $this->initNewDate();
        $this->dateContract = $today->modify("-1 month");

        $this->biInvoices = new ArrayCollection();
        $this->biItems = new ArrayCollection();
        $this->biCustomers = new ArrayCollection();
        $this->biSites = new ArrayCollection();
        $this->biContracts = new ArrayCollection();
        $this->biQuotations = new ArrayCollection();
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
     * @return string
     * @Groups({"admin:read"})
     */
    public function getCodeString(): string
    {
        $code = $this->code;
        if($code < 10){
            return "000" . $code;
        }elseif($code < 100){
            return "00" . $code;
        }elseif($code < 1000){
            return "0" . $code;
        }

        return $code;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

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

    /**
     * @return string
     * @Groups({"admin:read"})
     */
    public function getFullname(): string
    {
        return "#" . $this->getCodeString() . " - " . $this->name;
    }

    /**
     * @return string
     * @Groups({"admin:read"})
     */
    public function getLogoFile(): string
    {
        return $this->getFileOrDefault($this->logo, self::FOLDER_LOGOS, null);
    }

    public function getSiren(): ?string
    {
        return $this->siren;
    }

    public function setSiren(?string $siren): self
    {
        $this->siren = $siren;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getRcs(): ?string
    {
        return $this->rcs;
    }

    public function setRcs(?string $rcs): self
    {
        $this->rcs = $rcs;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getForme(): ?int
    {
        return $this->forme;
    }

    public function setForme(int $forme): self
    {
        $this->forme = $forme;

        return $this;
    }

    public function getNumeroTva(): ?string
    {
        return $this->numeroTva;
    }

    public function setNumeroTva(?string $numeroTva): self
    {
        $this->numeroTva = $numeroTva;

        return $this;
    }

    /**
     * @return string
     * @Groups({"admin:read"})
     */
    public function getFormeString(): string
    {
        $values = ["EURL", "SARL", "SA", "SNC", "SAS"];

        return $values[$this->forme];
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function setComplement(?string $complement): self
    {
        $this->complement = $complement;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPhone1(): ?string
    {
        return $this->phone1;
    }

    public function setPhone1(?string $phone1): self
    {
        $this->phone1 = $phone1;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * @return Collection<int, BiInvoice>
     */
    public function getBiInvoices(): Collection
    {
        return $this->biInvoices;
    }

    public function addBiInvoice(BiInvoice $biInvoice): self
    {
        if (!$this->biInvoices->contains($biInvoice)) {
            $this->biInvoices[] = $biInvoice;
            $biInvoice->setSociety($this);
        }

        return $this;
    }

    public function removeBiInvoice(BiInvoice $biInvoice): self
    {
        if ($this->biInvoices->removeElement($biInvoice)) {
            // set the owning side to null (unless already changed)
            if ($biInvoice->getSociety() === $this) {
                $biInvoice->setSociety(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BiItem>
     */
    public function getBiItems(): Collection
    {
        return $this->biItems;
    }

    public function addBiItem(BiItem $biItem): self
    {
        if (!$this->biItems->contains($biItem)) {
            $this->biItems[] = $biItem;
            $biItem->setSociety($this);
        }

        return $this;
    }

    public function removeBiItem(BiItem $biItem): self
    {
        if ($this->biItems->removeElement($biItem)) {
            // set the owning side to null (unless already changed)
            if ($biItem->getSociety() === $this) {
                $biItem->setSociety(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BiCustomer>
     */
    public function getBiCustomers(): Collection
    {
        return $this->biCustomers;
    }

    public function addBiCustomer(BiCustomer $biCustomer): self
    {
        if (!$this->biCustomers->contains($biCustomer)) {
            $this->biCustomers[] = $biCustomer;
            $biCustomer->setSociety($this);
        }

        return $this;
    }

    public function removeBiCustomer(BiCustomer $biCustomer): self
    {
        if ($this->biCustomers->removeElement($biCustomer)) {
            // set the owning side to null (unless already changed)
            if ($biCustomer->getSociety() === $this) {
                $biCustomer->setSociety(null);
            }
        }

        return $this;
    }

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function setBankName(?string $bankName): self
    {
        $this->bankName = $bankName;

        return $this;
    }

    public function getBankNumero(): ?string
    {
        return $this->bankNumero;
    }

    public function setBankNumero(?string $bankNumero): self
    {
        $this->bankNumero = $bankNumero;

        return $this;
    }

    public function getBankTitulaire(): ?string
    {
        return $this->bankTitulaire;
    }

    public function setBankTitulaire(?string $bankTitulaire): self
    {
        $this->bankTitulaire = $bankTitulaire;

        return $this;
    }

    public function getBankBic(): ?string
    {
        return $this->bankBic;
    }

    public function setBankBic(?string $bankBic): self
    {
        $this->bankBic = $bankBic;

        return $this;
    }

    public function getBankCode(): ?string
    {
        return $this->bankCode;
    }

    public function setBankCode(?string $bankCode): self
    {
        $this->bankCode = $bankCode;

        return $this;
    }

    public function getBankIban(): ?string
    {
        return $this->cryptBank('decrypt', $this->bankIban);
    }

    public function setBankIban(?string $bankIban): self
    {
        $this->bankIban = $this->cryptBank('encrypt', $bankIban);

        return $this;
    }

    /**
     * @return Collection<int, BiSite>
     */
    public function getBiSites(): Collection
    {
        return $this->biSites;
    }

    public function addBiSite(BiSite $biSite): self
    {
        if (!$this->biSites->contains($biSite)) {
            $this->biSites[] = $biSite;
            $biSite->setSociety($this);
        }

        return $this;
    }

    public function removeBiSite(BiSite $biSite): self
    {
        if ($this->biSites->removeElement($biSite)) {
            // set the owning side to null (unless already changed)
            if ($biSite->getSociety() === $this) {
                $biSite->setSociety(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BiContract>
     */
    public function getBiContracts(): Collection
    {
        return $this->biContracts;
    }

    public function addBiContract(BiContract $biContract): self
    {
        if (!$this->biContracts->contains($biContract)) {
            $this->biContracts[] = $biContract;
            $biContract->setSociety($this);
        }

        return $this;
    }

    public function removeBiContract(BiContract $biContract): self
    {
        if ($this->biContracts->removeElement($biContract)) {
            // set the owning side to null (unless already changed)
            if ($biContract->getSociety() === $this) {
                $biContract->setSociety(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BiQuotation>
     */
    public function getBiQuotations(): Collection
    {
        return $this->biQuotations;
    }

    public function addBiQuotation(BiQuotation $biQuotation): self
    {
        if (!$this->biQuotations->contains($biQuotation)) {
            $this->biQuotations[] = $biQuotation;
            $biQuotation->setSociety($this);
        }

        return $this;
    }

    public function removeBiQuotation(BiQuotation $biQuotation): self
    {
        if ($this->biQuotations->removeElement($biQuotation)) {
            // set the owning side to null (unless already changed)
            if ($biQuotation->getSociety() === $this) {
                $biQuotation->setSociety(null);
            }
        }

        return $this;
    }

    public function getCounterInvoice(): ?int
    {
        return $this->counterInvoice;
    }

    public function setCounterInvoice(int $counterInvoice): self
    {
        $this->counterInvoice = $counterInvoice;

        return $this;
    }

    public function getYearInvoice(): ?int
    {
        return $this->yearInvoice;
    }

    public function setYearInvoice(int $yearInvoice): self
    {
        $this->yearInvoice = $yearInvoice;

        return $this;
    }

    public function getCounterQuotation(): ?int
    {
        return $this->counterQuotation;
    }

    public function setCounterQuotation(int $counterQuotation): self
    {
        $this->counterQuotation = $counterQuotation;

        return $this;
    }

    public function getYearQuotation(): ?int
    {
        return $this->yearQuotation;
    }

    public function setYearQuotation(int $yearQuotation): self
    {
        $this->yearQuotation = $yearQuotation;

        return $this;
    }

    public function getCounterCustomer(): ?int
    {
        return $this->counterCustomer;
    }

    public function setCounterCustomer(int $counterCustomer): self
    {
        $this->counterCustomer = $counterCustomer;

        return $this;
    }

    public function getYearCustomer(): ?int
    {
        return $this->yearCustomer;
    }

    public function setYearCustomer(int $yearCustomer): self
    {
        $this->yearCustomer = $yearCustomer;

        return $this;
    }

    /**
     * @return string|null
     * @Groups({"bill-society:read"})
     */
    public function getDateInvoiceStringSlash(): ?string
    {
        return $this->getFullDateString($this->dateInvoice, "L");
    }

    /**
     * @return string|null
     * @Groups({"admin:read", "bill-society:read"})
     */
    public function getDateInvoiceJavascript(): ?string
    {
        return $this->setDateJavascript($this->dateInvoice);
    }

    public function getDateInvoice(): ?\DateTimeInterface
    {
        return $this->dateInvoice;
    }

    public function setDateInvoice(?\DateTimeInterface $dateInvoice): self
    {
        $this->dateInvoice = $dateInvoice;

        return $this;
    }

    /**
     * @return string|null
     * @Groups({"admin:read", "bill-society:read"})
     */
    public function getDateComptaJavascript(): ?string
    {
        return $this->setDateJavascript($this->dateCompta);
    }

    public function getDateComptaString(): ?string
    {
        return $this->getFullDateString($this->dateCompta, 'llll', 2);
    }

    public function getDateCompta(): ?\DateTimeInterface
    {
        return $this->dateCompta;
    }

    public function setDateCompta(?\DateTimeInterface $dateCompta): self
    {
        $this->dateCompta = $dateCompta;

        return $this;
    }

    /**
     * @return string|null
     * @Groups({"bill-society:read"})
     */
    public function getDateContractStringSlash(): ?string
    {
        return $this->getFullDateString($this->dateContract, "MM/Y");
    }

    /**
     * @return string|null
     * @Groups({"admin:read", "bill-society:read"})
     */
    public function getDateContractJavascript(): ?string
    {
        return $this->setDateJavascript($this->dateContract);
    }

    public function getDateContract(): ?\DateTimeInterface
    {
        return $this->dateContract;
    }

    public function setDateContract(\DateTimeInterface $dateContract): self
    {
        $this->dateContract = $dateContract;

        return $this;
    }

    public function getYearContract(): ?int
    {
        return $this->yearContract;
    }

    public function setYearContract(int $yearContract): self
    {
        $this->yearContract = $yearContract;

        return $this;
    }

    public function getCounterContract(): ?int
    {
        return $this->counterContract;
    }

    public function setCounterContract(int $counterContract): self
    {
        $this->counterContract = $counterContract;

        return $this;
    }

    public function getYearAvoir(): ?int
    {
        return $this->yearAvoir;
    }

    public function setYearAvoir(int $yearAvoir): self
    {
        $this->yearAvoir = $yearAvoir;

        return $this;
    }

    public function getCounterAvoir(): ?int
    {
        return $this->counterAvoir;
    }

    public function setCounterAvoir(int $counterAvoir): self
    {
        $this->counterAvoir = $counterAvoir;

        return $this;
    }

    public function getYearSite(): ?int
    {
        return $this->yearSite;
    }

    public function setYearSite(int $yearSite): self
    {
        $this->yearSite = $yearSite;

        return $this;
    }

    public function getCounterSite(): ?int
    {
        return $this->counterSite;
    }

    public function setCounterSite(int $counterSite): self
    {
        $this->counterSite = $counterSite;

        return $this;
    }

    public function getFooterInvoice(): ?string
    {
        return $this->footerInvoice;
    }

    public function setFooterInvoice(?string $footerInvoice): self
    {
        $this->footerInvoice = $footerInvoice;

        return $this;
    }

    public function getNoteInvoice(): ?string
    {
        return $this->noteInvoice;
    }

    public function setNoteInvoice(?string $noteInvoice): self
    {
        $this->noteInvoice = $noteInvoice;

        return $this;
    }

    public function getFooterQuotation(): ?string
    {
        return $this->footerQuotation;
    }

    public function setFooterQuotation(?string $footerQuotation): self
    {
        $this->footerQuotation = $footerQuotation;

        return $this;
    }

    public function getNoteQuotation(): ?string
    {
        return $this->noteQuotation;
    }

    public function setNoteQuotation(?string $noteQuotation): self
    {
        $this->noteQuotation = $noteQuotation;

        return $this;
    }

    public function getFooterAvoir(): ?string
    {
        return $this->footerAvoir;
    }

    public function setFooterAvoir(?string $footerAvoir): self
    {
        $this->footerAvoir = $footerAvoir;

        return $this;
    }

    public function getNoteAvoir(): ?string
    {
        return $this->noteAvoir;
    }

    public function setNoteAvoir(?string $noteAvoir): self
    {
        $this->noteAvoir = $noteAvoir;

        return $this;
    }
}
