<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $page = $em->getRepository(Page::class)->findOneBy(['slug' => 'home']);
        if (!$page) {
            $page = $this->createHomePage();
        }

        return $this->render(
            '@Sepulture/default/index.html.twig',
            ['page' => $page]
        );
    }

    protected function createHomePage()
    {
        $em = $this->getDoctrine()->getManager();
        $page = new Page();
        $page->setSlug('home');
        $page->setTitre('Bienvenue sur le site des cimetières de la commune');
        $page->setContenu('Editer la page pour modifier le contenu');
        $em->persist($page);

        $em->flush();

        return $page;
    }
}
