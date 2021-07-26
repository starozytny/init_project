<?php

namespace App\Entity\Immo;

use App\Repository\Immo\ImBienRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=ImBienRepository::class)
 */
class ImBien
{
    const LIST_READ = ['list:read'];
    const SHOW_READ = ['show:read'];

    const NATURE_LOCATION = 0;
    const NATURE_VENTE = 1;
    const NATURE_VIAGER = 2;
    const NATURE_INVEST = 3;
    const NATURE_BAIL = 4;
    const NATURE_VACANCE = 5;
    const NATURE_PRESTIGE = 6;
    const NATURE_COMMERCE = 7;

    const TYPE_MAISON = 0;
    const TYPE_APPARTEMENT = 1;
    const TYPE_PARKING = 2;
    const TYPE_BUREAUX = 3;
    const TYPE_LOCAL = 4;
    const TYPE_IMMEUBLE = 5;
    const TYPE_TERRAIN = 6;
    const TYPE_FOND_COMMERCE = 7;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"list:read", "show:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list:read", "show:read"})
     */
    private $ref;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"list:read", "show:read", "admin:read"})
     */
    private $realRef;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dispo;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list:read", "show:read"})
     */
    private $typeAd;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list:read", "show:read"})
     */
    private $typeBien;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"list:read", "show:read"})
     */
    private $codeTypeAd;

    /**
     * @ORM\Column(type="integer")
     */
    private $codeTypeBien;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $typeT;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list:read", "show:read", "admin:read"})
     */
    private $label;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"list:read", "show:read"})
     */
    private $content;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSync;

    /**
     * @ORM\ManyToOne(targetEntity=ImAgency::class, inversedBy="biens", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"list:read", "show:read"})
     */
    private $agency;

    /**
     * @ORM\OneToOne(targetEntity=ImAddress::class, cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"list:read", "show:read", "admin:read"})
     */
    private $address;

    /**
     * @ORM\OneToOne(targetEntity=ImFinancial::class, cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"list:read", "show:read"})
     */
    private $financial;

    /**
     * @ORM\OneToOne(targetEntity=ImFeature::class, cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"list:read", "show:read"})
     */
    private $feature;

    /**
     * @ORM\OneToOne(targetEntity=ImFeatureExt::class, cascade={"persist", "remove"}, fetch="EAGER")
     * @Groups({"list:read", "show:read"})
     */
    private $featureExt;

    /**
     * @ORM\OneToOne(targetEntity=ImDiagnostic::class, cascade={"persist", "remove"}, fetch="EAGER")
     * @Groups({"list:read", "show:read"})
     */
    private $diagnostic;

    /**
     * @ORM\OneToOne(targetEntity=ImCopro::class, cascade={"persist", "remove"})
     * @Groups({"list:read", "show:read"})
     */
    private $copro;

    /**
     * @ORM\OneToOne(targetEntity=ImResponsable::class, cascade={"persist", "remove"}, fetch="EAGER")
     * @Groups({"list:read"})
     */
    private $responsable;

    /**
     * @ORM\OneToMany(targetEntity=ImImage::class, mappedBy="bien", orphanRemoval=true, fetch="EAGER")
     * @Groups({"list:read", "show:read"})
     */
    private $images;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list:read", "show:read"})
     */
    private $identifiant;

    /**
     * @ORM\OneToMany(targetEntity=ImDemande::class, mappedBy="bien")
     */
    private $demandes;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\RelativeSlugHandler", options={
     *          @Gedmo\SlugHandlerOption(name="relationField", value="address"),
     *          @Gedmo\SlugHandlerOption(name="relationSlugField", value="slug"),
     *          @Gedmo\SlugHandlerOption(name="separator", value="-"),
     *          @Gedmo\SlugHandlerOption(name="urilize", value="true"),
     *      })
     * }, fields={"typeAd", "label", "identifiant"})
     * @Groups({"list:read", "show:read", "admin:read"})
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"list:read", "show:read"})
     */
    private $virtuel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"list:read", "show:read"})
     */
    private $panoramique;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"list:read", "show:read"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"list:read", "show:read"})
     */
    private $identifiantTech;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->demandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function getRealRef(): ?string
    {
        return $this->realRef;
    }

    public function setRealRef(?string $realRef): self
    {
        $this->realRef = $realRef;

        return $this;
    }

    public function getDispo(): ?\DateTimeInterface
    {
        return $this->dispo;
    }

    public function setDispo(?\DateTimeInterface $dispo): self
    {
        $this->dispo = $dispo;

        return $this;
    }

    /**
     * @return false|string|null
     * @Groups({"list:read", "show:read"})
     */
    public function getDispoString()
    {
        if($this->getDispo()){
            return date_format($this->dispo, "d/m/Y");
        }

        return null;
    }

    public function getTypeAd(): ?string
    {
        return $this->typeAd;
    }

    public function setTypeAd(string $typeAd): self
    {
        $this->typeAd = $typeAd;

        return $this;
    }

    public function getTypeBien(): ?string
    {
        return $this->typeBien;
    }

    public function setTypeBien(string $typeBien): self
    {
        $this->typeBien = $typeBien;

        return $this;
    }

    public function getCodeTypeAd(): ?int
    {
        return $this->codeTypeAd;
    }

    public function setCodeTypeAd(int $codeTypeAd): self
    {
        $this->codeTypeAd = $codeTypeAd;

        return $this;
    }

    public function getCodeTypeBien(): ?int
    {
        return $this->codeTypeBien;
    }

    public function setCodeTypeBien(int $codeTypeBien): self
    {
        $this->codeTypeBien = $codeTypeBien;

        return $this;
    }

    public function getTypeT(): ?string
    {
        return $this->typeT;
    }

    public function setTypeT(?string $typeT): self
    {
        $this->typeT = $typeT;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

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

    public function getAgency(): ?ImAgency
    {
        return $this->agency;
    }

    public function setAgency(?ImAgency $agency): self
    {
        $this->agency = $agency;

        return $this;
    }

    public function getIsSync(): ?bool
    {
        return $this->isSync;
    }

    public function setIsSync(bool $isSync): self
    {
        $this->isSync = $isSync;

        return $this;
    }

    public function getAddress(): ?ImAddress
    {
        return $this->address;
    }

    public function setAddress(ImAddress $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getFinancial(): ?ImFinancial
    {
        return $this->financial;
    }

    public function setFinancial(ImFinancial $financial): self
    {
        $this->financial = $financial;

        return $this;
    }

    public function getFeature(): ?ImFeature
    {
        return $this->feature;
    }

    public function setFeature(ImFeature $feature): self
    {
        $this->feature = $feature;

        return $this;
    }

    public function getFeatureExt(): ?ImFeatureExt
    {
        return $this->featureExt;
    }

    public function setFeatureExt(?ImFeatureExt $featureExt): self
    {
        $this->featureExt = $featureExt;

        return $this;
    }

    public function getDiagnostic(): ?ImDiagnostic
    {
        return $this->diagnostic;
    }

    public function setDiagnostic(?ImDiagnostic $diagnostic): self
    {
        $this->diagnostic = $diagnostic;

        return $this;
    }

    public function getCopro(): ?ImCopro
    {
        return $this->copro;
    }

    public function setCopro(?ImCopro $copro): self
    {
        $this->copro = $copro;

        return $this;
    }

    public function getResponsable(): ?ImResponsable
    {
        return $this->responsable;
    }

    public function setResponsable(?ImResponsable $responsable): self
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * @return Collection|ImImage[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(ImImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setBien($this);
        }

        return $this;
    }

    public function removeImage(ImImage $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getBien() === $this) {
                $image->setBien(null);
            }
        }

        return $this;
    }

    /**
     * @Groups({"list:read"})
     * @SerializedName("thumb")
     */
    public function getFirstThumb()
    {
        return $this->getImages() && $this->getImages()[0] ? $this->getImages()[0]->getThumb() : null;
    }

    public function getFirstImage()
    {
        return $this->getImages() && $this->getImages()[0] ? $this->getImages()[0]->getFile() : null;
    }

    public function getIdentifiant(): ?string
    {
        return $this->identifiant;
    }

    public function setIdentifiant(string $identifiant): self
    {
        $this->identifiant = $identifiant;

        return $this;
    }

    /**
     * @return Collection|ImDemande[]
     */
    public function getDemandes(): Collection
    {
        return $this->demandes;
    }

    public function addDemande(ImDemande $demande): self
    {
        if (!$this->demandes->contains($demande)) {
            $this->demandes[] = $demande;
            $demande->setBien($this);
        }

        return $this;
    }

    public function removeDemande(ImDemande $demande): self
    {
        if ($this->demandes->removeElement($demande)) {
            // set the owning side to null (unless already changed)
            if ($demande->getBien() === $this) {
                $demande->setBien(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @return string|null
     * @Groups({"show:read"})
     */
    public function getContactName(): ?string
    {
        $name = $this->getAgency()->getName();

        if($this->getResponsable()){
            $name = $this->getResponsable()->getName();
        }

        return $name;
    }

    /**
     * @return string|null
     * @Groups({"show:read"})
     */
    public function getContactEmail(): ?string
    {
        $agency = $this->getAgency();

        if($this->codeTypeAd === ImBien::NATURE_LOCATION){
            $email = $agency->getEmailLocation() ?? $agency->getEmail();
        }else{
            $email = $agency->getEmailVente() ?? $agency->getEmail();
        }

        if($this->getResponsable()){
            $email = $this->getResponsable()->getEmail();
        }

        return $email;
    }

    /**
     * @return string|null
     * @Groups({"show:read"})
     */
    public function getContactPhone(): ?string
    {
        $agency = $this->getAgency();

        if($this->codeTypeAd === ImBien::NATURE_LOCATION){
            $phone = $agency->getPhoneLocation() ?? $agency->getPhone();
        }else{
            $phone = $agency->getPhoneVente() ?? $agency->getPhone();
        }

        if($this->getResponsable()){
            $phone = $this->getResponsable()->getPhone();
        }

        return $phone;
    }

    public function getVirtuel(): ?string
    {
        return $this->virtuel;
    }

    public function setVirtuel(?string $virtuel): self
    {
        $this->virtuel = $virtuel;

        return $this;
    }

    public function getPanoramique(): ?string
    {
        return $this->panoramique;
    }

    public function setPanoramique(?string $panoramique): self
    {
        $this->panoramique = $panoramique;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getIdentifiantTech(): ?string
    {
        return $this->identifiantTech;
    }

    public function setIdentifiantTech(?string $identifiantTech): self
    {
        $this->identifiantTech = $identifiantTech;

        return $this;
    }
}
