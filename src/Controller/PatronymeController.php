<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Defunt;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
/**
 * Defunt controller.
 */
#[Route(path: '/patronyme')]
class PatronymeController extends AbstractController
{
    public function __construct(
        private ManagerRegistry $managerRegistry
    ) {
    }

    #[Route(path: '/', name: 'patronymes')]
    public function index(): Response
    {
        $em = $this->managerRegistry->getManager();
        $defunts = $em->getRepository(Defunt::class)->findAllGroupByName();

        return $this->render(
            '@Sepulture/patronyme/index.html.twig',
            [
                'defunts' => $defunts,
            ]
        );
    }

    #[Route(path: '/show/{id}', name: 'patronyme_show')]
    public function show(Defunt $defunt): Response
    {
        $nom = $defunt->getNom();
        $em = $this->managerRegistry->getManager();
        $defunts = $em->getRepository(Defunt::class)->findBy([
            'nom' => $nom,
        ]);

        return $this->render(
            '@Sepulture/patronyme/show.html.twig',
            [
                'defunt' => $defunt,
                'defunts' => $defunts,
            ]
        );
    }
}
