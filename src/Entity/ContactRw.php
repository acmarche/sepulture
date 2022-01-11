<?php

namespace AcMarche\Sepulture\Entity;

use AcMarche\Sepulture\Repository\ContactRwRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactRwRepository::class)]
class ContactRw
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $gestionnaire = null;
    #[ORM\Column(type: 'string', length: 200)]
    private ?string $adresse = null;
    #[ORM\Column(type: 'string', length: 50)]
    private ?string $codeIns = null;
    #[ORM\Column(type: 'string', length: 150)]
    private ?string $nom = null;
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $email = null;
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $telephone = null;
    #[ORM\Column(type: 'date')]
    private $dateRapport;
    #[ORM\Column(type: 'date')]
    private $dateExpiration;
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getGestionnaire(): ?string
    {
        return $this->gestionnaire;
    }
    public function setGestionnaire(string $gestionnaire): self
    {
        $this->gestionnaire = $gestionnaire;

        return $this;
    }
    public function getAdresse(): ?string
    {
        return $this->adresse;
    }
    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }
    public function getCodeIns(): ?string
    {
        return $this->codeIns;
    }
    public function setCodeIns(string $codeIns): self
    {
        $this->codeIns = $codeIns;

        return $this;
    }
    public function getNom(): ?string
    {
        return $this->nom;
    }
    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }
    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }
    public function getDateRapport(): ?DateTimeInterface
    {
        return $this->dateRapport;
    }
    public function setDateRapport(DateTimeInterface $dateRapport): self
    {
        $this->dateRapport = $dateRapport;

        return $this;
    }
    public function getDateExpiration(): ?DateTimeInterface
    {
        return $this->dateExpiration;
    }
    public function setDateExpiration(DateTimeInterface $dateExpiration): self
    {
        $this->dateExpiration = $dateExpiration;

        return $this;
    }
}
