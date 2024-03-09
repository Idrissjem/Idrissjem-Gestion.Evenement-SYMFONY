<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9]*$/',
        message: 'Le nom doit contenir uniquement des lettres et des chiffres.',
    )]
    private ?string $nom = null;
    

    #[ORM\Column]
    #[Assert\GreaterThan(
        value: 0,
        message: 'Le prix doit être supérieur à 0.',
    )]
    private ?float $prix = null;
    

    // #[Assert\NotBlank(message: 'nom ne peut pas être vide.')]
    // #[Assert\Length(max: 8, maxMessage: 'nom ne peut pas dépasser {{ limit }} caractères.')]
    // #[ORM\Column(length: 255)]
    // private ?string $marque = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    // public function getMarque(): ?string
    // {
    //     return $this->marque;
    // }

    // public function setMarque(string $marque): static
    // {
    //     $this->marque = $marque;

    //     return $this;
    // }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
    #[ORM\Column]
    private ?int $likes = 0;
   

    #[ORM\Column]
    private ?int $dis = 0;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    private ?Category $category = null;
    
    public function incrementDislikes(): void
    {
        $this->dis++;
    }

    public function getLikes(): int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): void
    {
        $this->likes = $likes;
    }

    public function getDislikes(): int
    {
        return $this->dis;
    }

    public function setDislikes(int $dislikes): void
    {
        $this->dis = $dislikes;
    }

    public function incrementLikes(): void
    {
        $this->likes++;
    }

    
    public function checkAndDeleteIfRequired(): bool
    {
        return $this->dis - $this->likes >= 2;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
