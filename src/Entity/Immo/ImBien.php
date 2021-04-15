<?php

namespace App\Entity\Immo;

use App\Repository\Immo\ImBienRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=ImBienRepository::class)
 */
class ImBien
{
    const ADMIN_LIST_READ = ['admin-list:read'];
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("admin-list:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("admin-list:read")
     */
    private $ref;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("admin-list:read")
     */
    private $realRef;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dispo;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("admin-list:read")
     */
    private $typeAd;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("admin-list:read")
     */
    private $typeBien;

    /**
     * @ORM\Column(type="integer")
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
     * @Groups("admin-list:read")
     */
    private $label;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups("admin-list:read")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=ImAgency::class, inversedBy="biens")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("admin-list:read")
     */
    private $agency;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSync;

    /**
     * @ORM\OneToOne(targetEntity=ImAddress::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $address;

    /**
     * @ORM\OneToOne(targetEntity=ImFinancial::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $financial;

    /**
     * @ORM\OneToOne(targetEntity=ImFeature::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $feature;

    /**
     * @ORM\OneToOne(targetEntity=ImFeatureExt::class, cascade={"persist", "remove"})
     */
    private $featureExt;

    /**
     * @ORM\OneToOne(targetEntity=ImDiagnostic::class, cascade={"persist", "remove"})
     */
    private $diagnostic;

    /**
     * @ORM\OneToOne(targetEntity=ImCopro::class, cascade={"persist", "remove"})
     */
    private $copro;

    /**
     * @ORM\OneToOne(targetEntity=ImResponsable::class, cascade={"persist", "remove"})
     */
    private $responsable;

    /**
     * @ORM\OneToMany(targetEntity=ImImage::class, mappedBy="bien", orphanRemoval=true, fetch="EAGER")
     */
    private $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
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
     * @Groups("admin-list:read")
     * @SerializedName("thumb")
     */
    public function getFirstThumb()
    {
        return $this->getImages() && $this->getImages()[0] ? $this->getImages()[0]->getThumb() : null;
    }
}
