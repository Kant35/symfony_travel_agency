<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\DestinationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=DestinationRepository::class)
 * @UniqueEntity(
 *      fields={"titre"},
 *      message="Cette destination existe déjà"
 * )
 */
class Destination
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Merci d'indiquer un titre à la destination")
     */
    private $titre;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Merci de remplir la description de la destination")
     */
    private $Description;

    /**
     * @ORM\OneToMany(targetEntity=Conseiller::class, mappedBy="specialite")
     */
    private $conseillers;

    /**
     * @ORM\ManyToMany(targetEntity=Produit::class, mappedBy="destinations")
     */
    private $produits;

    public function __construct()
    {
        $this->conseillers = new ArrayCollection();
        $this->produits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    /**
     * @return Collection<int, Conseiller>
     */
    public function getConseillers(): Collection
    {
        return $this->conseillers;
    }

    public function addConseiller(Conseiller $conseiller): self
    {
        if (!$this->conseillers->contains($conseiller)) {
            $this->conseillers[] = $conseiller;
            $conseiller->setSpecialite($this);
        }

        return $this;
    }

    public function removeConseiller(Conseiller $conseiller): self
    {
        if ($this->conseillers->removeElement($conseiller)) {
            // set the owning side to null (unless already changed)
            if ($conseiller->getSpecialite() === $this) {
                $conseiller->setSpecialite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->addDestination($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produits->removeElement($produit)) {
            $produit->removeDestination($this);
        }

        return $this;
    }
}
