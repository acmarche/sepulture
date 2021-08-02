<?php

namespace AcMarche\Sepulture\Controller;

use Symfony\Component\HttpFoundation\Response;
use AcMarche\Sepulture\Entity\Defunt;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Defunt controller.
 *
 * @Route("/patronyme")
 */
class PatronymeController extends AbstractController
{
    /**
     * @Route("/", name="patronymes")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $defunts = $em->getRepository(Defunt::class)->findAllGroupByName();

        return $this->render(
            '@Sepulture/patronyme/index.html.twig',
            ['defunts' => $defunts]
        );
    }

    /**
     * @Route("/show/{id}", name="patronyme_show")
     */
    public function show(Defunt $defunt): Response
    {
        $nom = $defunt->getNom();

        $em = $this->getDoctrine()->getManager();
        $defunts = $em->getRepository(Defunt::class)->findBy(['nom' => $nom]);

        return $this->render(
            '@Sepulture/patronyme/show.html.twig',
            ['defunt' => $defunt, 'defunts' => $defunts]
        );
    }
}
