<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * Image
 *
 * @ORM\Table(name="image", indexes={@ORM\Index(name="id_type_image", columns={"id_type_image"})})
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 *
 */
class Image
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups("post:read")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     *
     *
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="chemin", type="string", length=255, nullable=true)
     * @Groups("post:read")
     */
    private $chemin;

    /**
 * @var \TypeImage
 *
 * @ORM\ManyToOne(targetEntity="TypeImage")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="id_type_image", referencedColumnName="id")
 * })
 */
    private $idTypeImage;

    /**
     * @var \Produit
     *
     * @ORM\ManyToOne(targetEntity="Produit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_produit", referencedColumnName="id")
     * })
     */
    private $idProduit;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getChemin(): ?string
    {
        return $this->chemin;
    }

    public function setChemin(?string $chemin): self
    {
        $this->chemin = $chemin;

        return $this;
    }

    public function getIdTypeImage(): ?TypeImage
    {
        return $this->idTypeImage;
    }

    public function setIdTypeImage(?TypeImage $idTypeImage): self
    {
        $this->idTypeImage = $idTypeImage;

        return $this;
    }
    public function getIdProduit(): ?Produit
    {
        return $this->idProduit;
    }

    public function setIdProduit(?Produit $idProduit): self
    {
        $this->idProduit = $idProduit;

        return $this;
    }

    public function __toString()
    {
        return $this->chemin;
    }


}
