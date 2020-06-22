<?php

namespace AcMarche\Sepulture\Command;

use AcMarche\Sepulture\Entity\Legal;
use AcMarche\Sepulture\Entity\Materiaux;
use AcMarche\Sepulture\Entity\Page;
use AcMarche\Sepulture\Entity\Sihl;
use AcMarche\Sepulture\Entity\TypeSepulture;
use AcMarche\Sepulture\Entity\User;
use AcMarche\Sepulture\Entity\Visuel;
use AcMarche\Sepulture\Security\SecurityData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoadDataCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    protected function configure()
    {
        $this
            ->setName('sepulture:loaddata')
            ->setDescription('Initialise les données et utilisateurs par défaut dans la base de données');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->loadData();
        $this->loadUsers();
        $output->writeln('Les données ont bien été enregistrées');

        return 0;
    }

    public function loadData()
    {
        $visuels = ['Toujours utilisé', 'Bon', 'Moyen', 'Défaut d\'entretien', 'Ruine'];
        foreach ($visuels as $nom) {
            $visuel = new Visuel();
            $visuel->setNom($nom);
            $this->entityManager->persist($visuel);
        }

        $legals = [
            'Concession en cours',
            'Concession échue, à renouveler',
            'Défaut d\'entretien ou abandon',
            'Contrefacon',
        ];

        foreach ($legals as $nom) {
            $legal = new Legal();
            $legal->setNom($nom);
            $this->entityManager->persist($legal);
        }

        $shils = [
            'Historique' => 'historique',
            'Artistique' => 'artistique',
            'Social' => 'social',
            'Technique' => 'technique',
            'Paysage' => 'paysage',
        ];

        foreach ($shils as $nom => $slug) {
            $shil = new Sihl();
            $shil->setNom($nom);
            $shil->setSlug($slug);
            $this->entityManager->persist($shil);
        }

        $types = ['Pleine terre', 'Chapelle', 'Caveau', 'Concession'];
        foreach ($types as $nom) {
            $type = new TypeSepulture();
            $type->setNom($nom);
            $this->entityManager->persist($type);
        }

        $materiaux = ['Petit-granit (Pierre bleu)', 'Bois', 'Granit', 'Fonte'];
        foreach ($materiaux as $nom) {
            $materiel = new Materiaux();
            $materiel->setNom($nom);
            $this->entityManager->persist($materiel);
        }

        $page = new Page();
        $page->setTitre('Les sépultures des cimetières de Marche-en-Famenne');
        $page->setSlug('home');
        $page->setContenu('Contenu');
        $this->entityManager->persist($page);

        $page = new Page();
        $page->setTitre('Contactez nous');
        $page->setSlug('contact');
        $page->setContenu('Contenu');
        $this->entityManager->persist($page);

        $this->entityManager->flush();
    }

    public function loadUsers()
    {
        $admin = new User();
        //$admin->setUsername('admin');
        $admin->setNom('admin');
        $admin->setPrenom('admin');
        $admin->setEmail('admin@domain.be');
        $admin->setPassword($this->userPasswordEncoder->encodePassword($admin, 'admin'));
        $admin->setRoles(SecurityData::getRoles());
        $this->entityManager->persist($admin);

        $editeur = new User();
        //$editeur->setUsername('editeur');
        $editeur->setNom('editeur');
        $editeur->setPrenom('editeur');
        $editeur->setPassword($this->userPasswordEncoder->encodePassword($admin, 'editeur'));
        $editeur->setEmail('editeur@domain.be');
        $editeur->setRoles([SecurityData::getRoleEditeur()]);
        $this->entityManager->persist($editeur);

        $this->entityManager->flush();
    }
}
