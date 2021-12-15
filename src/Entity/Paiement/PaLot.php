<?php

namespace App\Entity\Paiement;

use App\Entity\DataEntity;
use App\Repository\Paiement\PaLotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaLotRepository::class)
 */
class PaLot extends DataEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $msgId;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbOfTxs;

    /**
     * @ORM\Column(type="integer")
     */
    private $total;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datePaiement;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titulaire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $iban;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $bic;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=PaOrder::class, mappedBy="lot")
     */
    private $orders;

    public function __construct()
    {
        $this->createdAt = $this->initNewDate();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMsgId(): ?int
    {
        return $this->msgId;
    }

    public function setMsgId(int $msgId): self
    {
        $this->msgId = $msgId;

        return $this;
    }

    public function getNbOfTxs(): ?int
    {
        return $this->nbOfTxs;
    }

    public function setNbOfTxs(int $nbOfTxs): self
    {
        $this->nbOfTxs = $nbOfTxs;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(\DateTimeInterface $datePaiement): self
    {
        $this->datePaiement = $datePaiement;

        return $this;
    }

    public function getTitulaire(): ?string
    {
        return $this->titulaire;
    }

    public function setTitulaire(string $titulaire): self
    {
        $this->titulaire = $titulaire;

        return $this;
    }

    /**
     * @return string
     * @Groups({"admin:read"})
     */
    public function getIbanHidden(): string
    {
        return $this->toFormatIbanHidden($this->iban);
    }

    public function getIban(): ?string
    {
        return $this->cryptBank('decrypt', $this->iban);
    }

    public function setIban(string $iban): self
    {
        $this->iban = $this->cryptBank('encrypt', $iban);

        return $this;
    }

    public function getBic(): ?string
    {
        return $this->cryptBank('decrypt', $this->bic);
    }

    public function setBic(string $bic): self
    {
        $this->bic = $this->cryptBank('encrypt', $bic);

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

    /**
     * @return Collection|PaOrder[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(PaOrder $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setLot($this);
        }

        return $this;
    }

    public function removeOrder(PaOrder $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getLot() === $this) {
                $order->setLot(null);
            }
        }

        return $this;
    }
}
