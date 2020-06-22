<?php

namespace AcMarche\Sepulture\DataFixtures\ORM;

use AcMarche\Sepulture\Entity\Legal;
use AcMarche\Sepulture\Entity\Materiaux;
use AcMarche\Sepulture\Entity\Page;
use AcMarche\Sepulture\Entity\Sihl;
use AcMarche\Sepulture\Entity\TypeSepulture;
use AcMarche\Sepulture\Entity\User;
use AcMarche\Sepulture\Security\SecurityData;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LoadData extends Fixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setNom('admin');
        $admin->setPrenom('admin');
        $admin->setPlainPassword('admin');
        $admin->setEmail('jf@marche.be');
        $admin->setRoles(SecurityData::getRoleAdmin());
        $manager->persist($admin);

        $editeur = new User();
        $editeur->setNom('editeur');
        $editeur->setPrenom('editeur');
        $editeur->setPlainPassword('editeur');
        $editeur->setEmail('editeur@marche.be');
        $editeur->setRoles(SecurityData::getRoleEditeur());
        $manager->persist($editeur);

        $manager->flush();

        $choice_visuel = [
            'Toujours utilisé' => 'Toujours utilisé',
            'Bon' => 'Bon',
            'Moyen' => 'Moyen',
            'Defaut' => 'Défaut d\'entretient',
        ];
        foreach ($choice_visuel as $visuel) {
            $t = new \AcMarche\Sepulture\Entity\Visuel();
            $t->setNom($visuel);
            $manager->persist($t);
        }

        $choice_legal = [
            'Concession en cours',
            'Concession échue, à renouveler',
            'Défaut d\'entretien ou abandon',
        ];

        foreach ($choice_legal as $legal) {
            $t = new Legal();
            $t->setNom($legal);
            $manager->persist($t);
        }

        $materiel = new Materiaux();
        $materiel->setNom('Yvoire');
        $manager->persist($materiel);

        $type = new TypeSepulture();
        $type->setNom('Boule');
        $manager->persist($type);

        $sihl = new Sihl();
        $sihl->setNom('Paysage');
        $manager->persist($sihl);

        $page = new Page();
        $page->setTitre('Les sépultures des cimetières de Marche-en-Famenne');
        $page->setSlug('home');
        $page->setContenu('voucou');
        $manager->persist($page);

        $manager->flush();
    }
}
