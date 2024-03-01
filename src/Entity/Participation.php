<?php

namespace App\Entity;

use App\Repository\ParticipationRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'nom ne peut pas être vide.')]
    #[Assert\Length(max: 8, maxMessage: 'nom ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'prenom ne peut pas être vide.')]
    #[Assert\Length(max: 8, maxMessage: 'prenom ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $prenom = null;

    #[Assert\NotBlank(message: 'Email ne peut pas être vide.')]
    #[Assert\Email(message: 'L\'adresse email "{{ value }}" n\'est pas valide.')]
    private ?string $email = null;
    
    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le numéro de téléphone ne peut pas être vide.')]
    #[Assert\Type(type: 'numeric', message: 'Le numéro de téléphone doit être un nombre.')]
    #[Assert\Length(exactMessage:"Le numéro de téléphone doit contenir exactement {{ limit }} caractères.",min:8,max:8)]
    private ?int $tel = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[Assert\NotBlank(message: 'User ne peut pas être vide.')]
    #[ORM\ManyToOne(inversedBy: 'participation')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    private ?Event $event = null;

 

    
    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getTel(): ?int
    {
        return $this->tel;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }
    public function setTel(string $tel): static
    {
        $this->tel = $tel;

        return $this;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }
    public function __toString(): string
{
    return $this->nom ?? ''; 
}
   


}
