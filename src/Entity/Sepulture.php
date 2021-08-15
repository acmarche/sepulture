<?php

namespace AcMarche\Sepulture\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sepulture.
 *
 * @ORM\Table(name="sepultures")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"parcelle"}, message="Cette valeur est déjà utilisée")
 * @ORM\Entity(repositoryClass="AcMarche\Sepulture\Repository\SepultureRepository")
 */
class Sepulture implements SluggableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SluggableTrait;

    /**
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     *
     * @ORM\Column(name="parcelle", type="string", length=100, unique=true)
     * @Assert\NotBlank()
     */
    private ?string $parcelle = null;

    /**
     * @ORM\ManyToOne(targetEntity="Cimetiere", inversedBy="sepultures")
     * @ORM\JoinColumn(name="cimetiere_id", referencedColumnName="id")
     * */
    private ?Cimetiere $cimetiere = null;

    /**
     * @var TypeSepulture[]|iterable
     * @ORM\ManyToMany(targetEntity="TypeSepulture", inversedBy="sepultures", cascade={"persist"})
     * @ORM\JoinTable(name="sepultures_types")
     */
    private Collection $types;

    /**
     * @var Sihl[]|iterable
     * @ORM\ManyToMany(targetEntity="AcMarche\Sepulture\Entity\Sihl", inversedBy="sepultures", cascade={"persist"})
     * @ORM\JoinTable(name="sepultures_sihls")
     */
    private Collection $sihls;

    /**
     * @ORM\Column(name="statut", type="string", length=255, nullable=true, options={"comment" = "finis, a relire, erreurs.."})
     */
    private ?string $statut = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $statutSih = null;

    /**
     * @ORM\Column(name="type_autre", type="string", length=255, nullable=true, options={"comment" = "autre type"})
     */
    private ?string $type_autre = null;

    /**
     * @var Materiaux[]|iterable
     * @ORM\ManyToMany(targetEntity="Materiaux", inversedBy="sepultures")
     */
    private Collection $materiaux;

    /**
     * @ORM\ManyToOne(targetEntity="Visuel", inversedBy="sepultures")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?Visuel $visuel = null;

    /**
     * @ORM\ManyToOne(targetEntity="Legal", inversedBy="sepultures")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?Legal $legal = null;

    /**
     * @ORM\Column(name="materiaux_autre", type="string", length=255, nullable=true, options={"comment" = "autre materiaux"})
     */
    private ?string $materiaux_autre = null;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private ?string $description = null;

    /**
     * @ORM\Column(name="description_autre", type="text", nullable=true)
     */
    private ?string $description_autre = null;

    /**
     * @ORM\Column(name="aspect_visuel", type="string", length=200, nullable=true, options={"comment" = "bon,moyen,ruine..."})
     */
    private ?string $aspect_visuel = null;

    /**
     * @ORM\Column(name="aspect_legal", type="string", length=200, nullable=true, options={"comment" = "bon,moyen,ruine..."})
     */
    private ?string $aspect_legal = null;

    /**
     * @ORM\Column(name="symbole", type="text", nullable=true, options={"comment" = "epis,fleur,franc-macon..."})
     */
    private ?string $symbole = null;

    /**
     * @ORM\Column(name="epitaphe", type="text", nullable=true, options={"comment" = "et devises"})
     */
    private ?string $epitaphe = null;

    /**
     * @var Defunt[]|iterable
     * @ORM\OneToMany(targetEntity="Defunt", mappedBy="sepulture", cascade={"remove"})
     * @ORM\OrderBy({"nom" = "ASC"})
     * */
    private Collection $defunts;

    /**
     * @ORM\Column(name="architectural", type="text", nullable=true, options={"comment" = "interet historique"})
     */
    private ?string $architectural = null;

    /**
     * @ORM\Column(name="sociale", type="text", nullable=true, options={"comment" = "fonction sociale"})
     */
    private ?string $sociale = null;

    /**
     * @ORM\Column(name="sociale_check", type="boolean", nullable=true, options={"comment" = "inscription mise ou pas"})
     */
    private ?bool $sociale_check;

    /**
     * @ORM\Column(name="combattant14", type="boolean", nullable=true, options={"comment" = "ancien combattant 14-18"})
     */
    private ?bool $combattant14;

    /**
     * @ORM\Column(name="combattant40", type="boolean", nullable=true, options={"comment" = "ancien combattant 40-45"})
     */
    private ?bool $combattant40;

    /**
     * @ORM\Column(name="contact", type="text", nullable=true)
     */
    private ?string $contact = null;

    /**
     * @ORM\Column(name="annee_releve", type="integer", length=4, nullable=true)
     */
    private ?int $annee_releve = null;

    /**
     * @ORM\Column(name="guerre", type="boolean", nullable=true, options={"comment" = "1er immu avant 45"})
     */
    private ?bool $guerre;

    /**
     * PARTIE REGION WALLONNE.
     */
    /**
     * @ORM\Column(name="rw_statut", type="text", nullable=true)
     */
    private ?string $rw_statut = null;

    /**
     * @ORM\Column(name="rw_commentaire", type="text", nullable=true)
     */
    private ?string $rw_commentaire = null;

    /**
     * @ORM\Column(name="url", type="string", nullable=true, options={"comment" = "genealogie"})
     */
    private ?string $url = null;

    /**
     * @var Commentaire[]|iterable
     * @ORM\OneToMany(targetEntity="Commentaire", mappedBy="sepulture", cascade={"persist", "remove"})
     *
     * */
    private Collection $commentaires;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Sepulture\Entity\User")
     * @ORM\JoinColumn(name="user_add", nullable=false)
     */
    protected ?User $user_add = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $user = null;

    public array $images;

    public function __toString()
    {
        return $this->parcelle;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->types = new ArrayCollection();
        $this->sihls = new ArrayCollection();
        $this->materiaux = new ArrayCollection();
        $this->defunts = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->statutSih = 0;
    }

    public function getSluggableFields(): array
    {
        return ['id', 'parcelle'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParcelle(): ?string
    {
        return $this->parcelle;
    }

    public function setParcelle(string $parcelle): self
    {
        $this->parcelle = $parcelle;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getTypeAutre(): ?string
    {
        return $this->type_autre;
    }

    public function setTypeAutre(?string $type_autre): self
    {
        $this->type_autre = $type_autre;

        return $this;
    }

    public function getMateriauxAutre(): ?string
    {
        return $this->materiaux_autre;
    }

    public function setMateriauxAutre(?string $materiaux_autre): self
    {
        $this->materiaux_autre = $materiaux_autre;

        return $this;
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

    public function getDescriptionAutre(): ?string
    {
        return $this->description_autre;
    }

    public function setDescriptionAutre(?string $description_autre): self
    {
        $this->description_autre = $description_autre;

        return $this;
    }

    public function getAspectVisuel(): ?string
    {
        return $this->aspect_visuel;
    }

    public function setAspectVisuel(?string $aspect_visuel): self
    {
        $this->aspect_visuel = $aspect_visuel;

        return $this;
    }

    public function getAspectLegal(): ?string
    {
        return $this->aspect_legal;
    }

    public function setAspectLegal(?string $aspect_legal): self
    {
        $this->aspect_legal = $aspect_legal;

        return $this;
    }

    public function getSymbole(): ?string
    {
        return $this->symbole;
    }

    public function setSymbole(?string $symbole): self
    {
        $this->symbole = $symbole;

        return $this;
    }

    public function getEpitaphe(): ?string
    {
        return $this->epitaphe;
    }

    public function setEpitaphe(?string $epitaphe): self
    {
        $this->epitaphe = $epitaphe;

        return $this;
    }

    public function getArchitectural(): ?string
    {
        return $this->architectural;
    }

    public function setArchitectural(?string $architectural): self
    {
        $this->architectural = $architectural;

        return $this;
    }

    public function getSociale(): ?string
    {
        return $this->sociale;
    }

    public function setSociale(?string $sociale): self
    {
        $this->sociale = $sociale;

        return $this;
    }

    public function getSocialeCheck(): ?bool
    {
        return $this->sociale_check;
    }

    public function setSocialeCheck(?bool $sociale_check): self
    {
        $this->sociale_check = $sociale_check;

        return $this;
    }

    public function getCombattant14(): ?bool
    {
        return $this->combattant14;
    }

    public function setCombattant14(?bool $combattant14): self
    {
        $this->combattant14 = $combattant14;

        return $this;
    }

    public function getCombattant40(): ?bool
    {
        return $this->combattant40;
    }

    public function setCombattant40(?bool $combattant40): self
    {
        $this->combattant40 = $combattant40;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getAnneeReleve(): ?int
    {
        return $this->annee_releve;
    }

    public function setAnneeReleve(?int $annee_releve): self
    {
        $this->annee_releve = $annee_releve;

        return $this;
    }

    public function getGuerre(): ?bool
    {
        return $this->guerre;
    }

    public function setGuerre(?bool $guerre): self
    {
        $this->guerre = $guerre;

        return $this;
    }

    public function getRwStatut(): ?string
    {
        return $this->rw_statut;
    }

    public function setRwStatut(?string $rw_statut): self
    {
        $this->rw_statut = $rw_statut;

        return $this;
    }

    public function getRwCommentaire(): ?string
    {
        return $this->rw_commentaire;
    }

    public function setRwCommentaire(?string $rw_commentaire): self
    {
        $this->rw_commentaire = $rw_commentaire;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

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

    public function getCimetiere(): ?Cimetiere
    {
        return $this->cimetiere;
    }

    public function setCimetiere(?Cimetiere $cimetiere): self
    {
        $this->cimetiere = $cimetiere;

        return $this;
    }

    /**
     * @return Collection|TypeSepulture[]
     */
    public function getTypes(): iterable
    {
        return $this->types;
    }

    public function addType(TypeSepulture $type): self
    {
        if (!$this->types->contains($type)) {
            $this->types[] = $type;
        }

        return $this;
    }

    public function removeType(TypeSepulture $type): self
    {
        if ($this->types->contains($type)) {
            $this->types->removeElement($type);
        }

        return $this;
    }

    /**
     * @return Collection|Sihl[]
     */
    public function getSihls(): iterable
    {
        return $this->sihls;
    }

    public function addSihl(Sihl $sihl): self
    {
        if (!$this->sihls->contains($sihl)) {
            $this->sihls[] = $sihl;
        }

        return $this;
    }

    public function removeSihl(Sihl $sihl): self
    {
        if ($this->sihls->contains($sihl)) {
            $this->sihls->removeElement($sihl);
        }

        return $this;
    }

    /**
     * @return Collection|Materiaux[]
     */
    public function getMateriaux(): iterable
    {
        return $this->materiaux;
    }

    public function addMateriaux(Materiaux $materiaux): self
    {
        if (!$this->materiaux->contains($materiaux)) {
            $this->materiaux[] = $materiaux;
        }

        return $this;
    }

    public function removeMateriaux(Materiaux $materiaux): self
    {
        if ($this->materiaux->contains($materiaux)) {
            $this->materiaux->removeElement($materiaux);
        }

        return $this;
    }

    public function getVisuel(): ?Visuel
    {
        return $this->visuel;
    }

    public function setVisuel(?Visuel $visuel): self
    {
        $this->visuel = $visuel;

        return $this;
    }

    public function getLegal(): ?Legal
    {
        return $this->legal;
    }

    public function setLegal(?Legal $legal): self
    {
        $this->legal = $legal;

        return $this;
    }

    /**
     * @return Collection|Defunt[]
     */
    public function getDefunts(): iterable
    {
        return $this->defunts;
    }

    public function addDefunt(Defunt $defunt): self
    {
        if (!$this->defunts->contains($defunt)) {
            $this->defunts[] = $defunt;
            $defunt->setSepulture($this);
        }

        return $this;
    }

    public function removeDefunt(Defunt $defunt): self
    {
        if ($this->defunts->contains($defunt)) {
            $this->defunts->removeElement($defunt);
            // set the owning side to null (unless already changed)
            if ($defunt->getSepulture() === $this) {
                $defunt->setSepulture(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getCommentaires(): iterable
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setSepulture($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->contains($commentaire)) {
            $this->commentaires->removeElement($commentaire);
            // set the owning side to null (unless already changed)
            if ($commentaire->getSepulture() === $this) {
                $commentaire->setSepulture(null);
            }
        }

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

    public function getStatutSih(): int
    {
        return $this->statutSih;
    }

    public function setStatutSih(?int $statutSih): self
    {
        $this->statutSih = $statutSih;

        return $this;
    }
}
