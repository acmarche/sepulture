<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\ContactRw;
use AcMarche\Sepulture\Form\ContactRwType;
use AcMarche\Sepulture\Repository\ContactRwRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/contact/rw")
 */
class ContactRwController extends AbstractController
{
    /**
     * @Route("/", name="contact_rw_index", methods={"GET"})
     */
    public function index(ContactRwRepository $contactRwRepository): Response
    {
        return $this->render(
            '@Sepulture/contact_rw/index.html.twig',
            [
                'contacts' => $contactRwRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="contact_rw_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $contactRw = new ContactRw();
        $contactRw->setDateExpiration(new \DateTime());
        $contactRw->setDateRapport(new \DateTime());

        $form = $this->createForm(ContactRwType::class, $contactRw);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contactRw);
            $entityManager->flush();
            $this->addFlash('success', 'Le contact a bien été ajouté');

            return $this->redirectToRoute('contact_rw_show', ['id' => $contactRw->getId()]);
        }

        return $this->render(
            '@Sepulture/contact_rw/new.html.twig',
            [
                'contact' => $contactRw,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="contact_rw_show", methods={"GET"})
     */
    public function show(ContactRw $contactRw): Response
    {
        return $this->render(
            '@Sepulture/contact_rw/show.html.twig',
            [
                'contact' => $contactRw,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="contact_rw_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ContactRw $contactRw): Response
    {
        $form = $this->createForm(ContactRwType::class, $contactRw);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Le contact a bien été modifié');

            return $this->redirectToRoute('contact_rw_show', ['id' => $contactRw->getId()]);
        }

        return $this->render(
            '@Sepulture/contact_rw/edit.html.twig',
            [
                'contact' => $contactRw,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="contact_rw_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ContactRw $contactRw): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contactRw->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($contactRw);
            $entityManager->flush();
            $this->addFlash('success', 'Le contact a bien été supprimé');
        }

        return $this->redirectToRoute('contact_rw_index');
    }
}
