<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Defunt;
use AcMarche\Sepulture\Entity\Sepulture;
use AcMarche\Sepulture\Form\DefuntType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Defunt controller.
 */
#[Route(path: '/defunt')]
class DefuntController extends AbstractController
{
    public function __construct(
        private ManagerRegistry $managerRegistry
    ) {
    }

    /**
     * Displays a form to create a new Defunt entity.
     */
    #[IsGranted('ROLE_SEPULTURE_EDITEUR')]
    #[Route(path: '/new/{id}', name: 'defunt_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Sepulture $sepulture): Response
    {
        $entity = new Defunt();
        $entity->setSepulture($sepulture);
        $form = $this->createForm(DefuntType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();

            $user = $this->getUser();
            $entity->setUserAdd($user);
            $sepulture = $entity->getSepulture();

            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', 'Le défunt a bien été ajouté');

            return $this->redirectToRoute('sepulture_show', [
                'slug' => $sepulture->getSlug(),
            ]);
        }

        return $this->render(
            '@Sepulture/defunt/new.html.twig',
            [
                'entity' => $entity,
                'sepulture' => $sepulture,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Defunt entity.
     */
    #[Route(path: '/{id}', name: 'defunt_show', methods: ['GET'])]
    public function show(Defunt $defunt): Response
    {
        $deleteForm = $this->createDeleteForm($defunt->getId());

        return $this->render(
            '@Sepulture/defunt/show.html.twig',
            [
                'entity' => $defunt,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Defunt entity.
     */
    #[IsGranted('ROLE_SEPULTURE_EDITEUR')]
    #[Route(path: '/{id}/edit', name: 'defunt_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Defunt $defunt): Response
    {
        $em = $this->managerRegistry->getManager();
        $editForm = $this->createEditForm($defunt);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            $sepulture = $defunt->getSepulture();

            $this->addFlash('success', 'Le défunt a bien été modifié');

            return $this->redirectToRoute('sepulture_show', [
                'slug' => $sepulture->getSlug(),
            ]);
        }

        return $this->render(
            '@Sepulture/defunt/edit.html.twig',
            [
                'entity' => $defunt,
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to edit a Defunt entity.
     *
     * @param Defunt $entity The entity
     *
     * @return FormInterface The form
     */
    private function createEditForm(Defunt $entity): FormInterface
    {
        return $this->createForm(
            DefuntType::class,
            $entity,
            [
            ]
        );
    }

    /**
     * Deletes a Defunt entity.
     */
    #[IsGranted('ROLE_SEPULTURE_EDITEUR')]
    #[Route(path: '/{id}/delete', name: 'defunt_delete', methods: ['POST'])]
    public function delete(Request $request, $id): RedirectResponse
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $entity = $em->getRepository(Defunt::class)->find($id);

            if (null === $entity) {
                throw $this->createNotFoundException('Unable to find Defunt entity.');
            }

            $sepulture = $entity->getSepulture();

            $em->remove($entity);
            $em->flush();

            $this->addFlash('success', 'Le défunt a bien été supprimé');

            return $this->redirectToRoute('sepulture_show', [
                'slug' => $sepulture->getSlug(),
            ]);
        }

        return $this->redirectToRoute('cimetiere');
    }

    /**
     * Creates a form to delete a Defunt entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return FormInterface The form
     */
    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('defunt_delete', [
                'id' => $id,
            ]))
            ->getForm();
    }
}
