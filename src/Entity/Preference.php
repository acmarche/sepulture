<?php

namespace AcMarche\Sepulture\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Page.
 *
 * @ORM\Table(name="preference")
 * @ORM\Entity(repositoryClass="AcMarche\Sepulture\Repository\PreferenceRepository")
 */
class Preference
{
    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $clef;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     */
    private $valeur;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     */
    private $username;

    public function __toString()
    {
        return $this->nom;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getClef(): ?string
    {
        return $this->clef;
    }

    public function setClef(string $clef): self
    {
        $this->clef = $clef;

        return $this;
    }

    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    public function setValeur(string $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
}
