<?php

namespace App\Entity\Immo;

use App\Repository\Immo\ImStatRepository;
use Carbon\Carbon;
use Carbon\Factory;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ImStatRepository::class)
 */
class ImStat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $totBiens;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $totLocations;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $totVentes;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $nbMaisons;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $nbAppartements;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $nbParkings;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $nbBureaux;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $nbLocaux;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $nbImmeubles;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $nbTerrains;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $nbCommerces;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"admin:read"})
     */
    private $nbAutres;

    /**
     * @ORM\ManyToOne(targetEntity=ImAgency::class, inversedBy="stats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $agency;

    public function __construct()
    {
        $createdAt = new \DateTime();
        $createdAt->setTimezone(new \DateTimeZone("Europe/Paris"));
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return string|null
     * @Groups({"admin:read"})
     */
    public function getCreatedAtString(): ?string
    {
        if($this->createdAt){
            $frenchFactory = new Factory([
                'locale' => 'fr_FR',
                'timezone' => 'Europe/Paris'
            ]);
            $time = Carbon::instance($this->createdAt);

            return $frenchFactory->make($time)->isoFormat('ll');
        }

        return null;
    }

    public function getTotBiens(): ?int
    {
        return $this->totBiens;
    }

    public function setTotBiens(int $totBiens): self
    {
        $this->totBiens = $totBiens;

        return $this;
    }

    public function getTotLocations(): ?int
    {
        return $this->totLocations;
    }

    public function setTotLocations(int $totLocations): self
    {
        $this->totLocations = $totLocations;

        return $this;
    }

    public function getTotVentes(): ?int
    {
        return $this->totVentes;
    }

    public function setTotVentes(int $totVentes): self
    {
        $this->totVentes = $totVentes;

        return $this;
    }

    public function getNbMaisons(): ?int
    {
        return $this->nbMaisons;
    }

    public function setNbMaisons(int $nbMaisons): self
    {
        $this->nbMaisons = $nbMaisons;

        return $this;
    }

    public function getNbAppartements(): ?int
    {
        return $this->nbAppartements;
    }

    public function setNbAppartements(int $nbAppartements): self
    {
        $this->nbAppartements = $nbAppartements;

        return $this;
    }

    public function getNbParkings(): ?int
    {
        return $this->nbParkings;
    }

    public function setNbParkings(int $nbParkings): self
    {
        $this->nbParkings = $nbParkings;

        return $this;
    }

    public function getNbBureaux(): ?int
    {
        return $this->nbBureaux;
    }

    public function setNbBureaux(int $nbBureaux): self
    {
        $this->nbBureaux = $nbBureaux;

        return $this;
    }

    public function getNbLocaux(): ?int
    {
        return $this->nbLocaux;
    }

    public function setNbLocaux(int $nbLocaux): self
    {
        $this->nbLocaux = $nbLocaux;

        return $this;
    }

    public function getNbImmeubles(): ?int
    {
        return $this->nbImmeubles;
    }

    public function setNbImmeubles(int $nbImmeubles): self
    {
        $this->nbImmeubles = $nbImmeubles;

        return $this;
    }

    public function getNbTerrains(): ?int
    {
        return $this->nbTerrains;
    }

    public function setNbTerrains(int $nbTerrains): self
    {
        $this->nbTerrains = $nbTerrains;

        return $this;
    }

    public function getNbCommerces(): ?int
    {
        return $this->nbCommerces;
    }

    public function setNbCommerces(int $nbCommerces): self
    {
        $this->nbCommerces = $nbCommerces;

        return $this;
    }

    public function getNbAutres(): ?int
    {
        return $this->nbAutres;
    }

    public function setNbAutres(int $nbAutres): self
    {
        $this->nbAutres = $nbAutres;

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
}
