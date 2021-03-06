<?php

namespace AcMarche\Sepulture\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sepulture.
 *
 * @ORM\Table(name="defunts")
 * @ORM\Entity(repositoryClass="AcMarche\Sepulture\Repository\DefuntRepository")
 */
class Defunt implements SluggableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SluggableTrait;

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
     * @ORM\Column(name="nom", type="string", length=150, nullable=false, options={"comment" = "patronyme"})
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="prenom", type="string", length=150, nullable=true, options={"comment" = "prenom du mort"})
     */
    private $prenom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fonction", type="string", length=255, nullable=true, options={"comment" = "fonction social"})
     */
    private $fonction;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=120, nullable=true, options={"comment" = "date anniversaire"})
     */
    protected $birthday;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=120, nullable=true, options={"comment" = "date de mort"})
     */
    protected $date_deces;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lieu_naissance", type="string", length=255, nullable=true, options={"comment" = "ne ou"})
     */
    private $lieu_naissance;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lieu_deces", type="string", length=255, nullable=true, options={"comment" = "mort ou"})
     */
    private $lieu_deces;

    /**
     * @var Sepulture|null
     * @ORM\ManyToOne(targetEntity="Sepulture", inversedBy="defunts")
     * @ORM\JoinColumn(name="sepulture_id", referencedColumnName="id")
     * */
    private $sepulture;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="AcMarche\Sepulture\Entity\User")
     * @ORM\JoinColumn(name="user_add", nullable=false)
     */
    protected $user_add;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $user;

    public function __toString()
    {
        return $this->getNom() . ' ' . $this->getPrenom();
    }

    public function getSluggableFields(): array
    {
        return ['nom', 'prenom'];
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(?string $fonction): self
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function getBirthday(): ?string
    {
        return $this->birthday;
    }

    public function setBirthday(?string $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getDateDeces(): ?string
    {
        return $this->date_deces;
    }

    public function setDateDeces(?string $date_deces): self
    {
        $this->date_deces = $date_deces;

        return $this;
    }

    public function getLieuNaissance(): ?string
    {
        return $this->lieu_naissance;
    }

    public function setLieuNaissance(?string $lieu_naissance): self
    {
        $this->lieu_naissance = $lieu_naissance;

        return $this;
    }

    public function getLieuDeces(): ?string
    {
        return $this->lieu_deces;
    }

    public function setLieuDeces(?string $lieu_deces): self
    {
        $this->lieu_deces = $lieu_deces;

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(?string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSepulture(): ?Sepulture
    {
        return $this->sepulture;
    }

    public function setSepulture(?Sepulture $sepulture): self
    {
        $this->sepulture = $sepulture;

        return $this;
    }

    public function getUserAdd(): ?User
    {
        return $this->user_add;
    }

    public function setUserAdd(?User $user_add): self
    {
        $this->user_add = $user_add;

        return $this;
    }
}
