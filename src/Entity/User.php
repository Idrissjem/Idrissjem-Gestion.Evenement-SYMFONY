<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

  

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "L'email ne peut pas être vide.")]
    #[Assert\Email(message: "Le format de l'email est invalide.")]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le mot de passe ne peut pas être vide.")]
    #[Assert\Length(
        min: 8,
        max: 4096,
        minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères."
    )]
    private ?string $password = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide.")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom ne peut pas être vide.")]
    private ?string $prenom = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le numéro de téléphone ne peut pas être vide.")]
    #[Assert\Regex(
        pattern: '/^[0-9]{8}$/',
        message: "Le numéro de téléphone doit contenir 8 chiffres."
    )]
    private ?int $tel = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    // #[ORM\Column(type:'string', length: 255)]
    // private ?string $resetToken = null;

    #[ORM\Column]
    private ?bool $isBanned = false; 


    public function getIsBanned(): ?bool
    {
        return $this->isBanned;
    }

    // Setter for isBanned
    public function setIsBanned(?bool $isBanned): self
    {
        $this->isBanned = $isBanned;
        return $this;
    }


    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Produit::class)]
    private Collection $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
{
    return $this->name;
}

public function setName(string $name): static
{
    $this->name = $name;

    return $this;
}



public function getPrenom(): ?string
{
    return $this->prenom;
}

public function setPrenom(string $prenom): static
{
    $this->prenom = $prenom;

    return $this;
}

public function getTel(): ?int
    {
        return $this->tel;
    }

    public function setTel(int $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

public function getImage(): ?string
{
    return $this->image;
}

public function setImage(string $image): static
{
    $this->image = $image;

    return $this;
}

public function getUsernom(): ?string
{
    return $this->username;
}

public function setUsername(string $username): static
{
    $this->username = $username;

    return $this;
}










// public function getResetToken() : ?sttring
// {
//     return $this->resetToken ;
// }
// public function setResetToken(?string $resetToken):self 
// {
//  $this->resetToken= $resetToken;
//  return $this ; 
// }
public function __toString(): string
{
    return $this->image ?? ''; 
}

/**
 * @return Collection<int, Produit>
 */
public function getProduits(): Collection
{
    return $this->produits;
}

public function addProduit(Produit $produit): static
{
    if (!$this->produits->contains($produit)) {
        $this->produits->add($produit);
        $produit->setUser($this);
    }

    return $this;
}

public function removeProduit(Produit $produit): static
{
    if ($this->produits->removeElement($produit)) {
        // set the owning side to null (unless already changed)
        if ($produit->getUser() === $this) {
            $produit->setUser(null);
        }
    }

    return $this;
}
}
