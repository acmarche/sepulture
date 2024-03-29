<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Legal;
use AcMarche\Sepulture\Form\LegalType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
/**
 * Legal controller.
 */
#[Route(path: '/legal')]
class LegalController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry
    ) {
    }

    /**
     * Lists all Legal entities.
     */
    #[Route(path: '/', name: 'legal', methods: ['GET'])]
    public function index(): Response
    {
        $em = $this->managerRegistry->getManager();
        $entities = $em->getRepository(Legal::class)->findAll();

        return $this->render(
            '@Sepulture/legal/index.html.twig',
            [
                'entities' => $entities,
            ]
        );
    }

    /**
     * Creates a form to create a Legal entity.
     *
     * @param Legal $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Legal $entity): FormInterface
    {
        return    $this->createForm(
            LegalType::class,
            $entity,
            [
                'action' => $this->generateUrl('legal_new'),
                'method' => 'POST',
            ]
        );
    }

    /**
     * Displays a form to create a new Legal entity.
     */
    #[IsGranted('ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/new', name: 'legal_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $entity = new Legal();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', "L'aspect légal a bien été ajouté");

            return $this->redirectToRoute('legal');
        }

        return $this->render(
            '@Sepulture/legal/new.html.twig',
            [
                'entity' => $entity,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Legal entity.
     */
    #[Route(path: '/{id}', name: 'legal_show', methods: ['GET'])]
    public function show(Legal $legal): Response
    {
        $deleteForm = $this->createDeleteForm($legal->getId());

        return $this->render(
            '@Sepulture/legal/show.html.twig',
            [
                'entity' => $legal,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Legal entity.
     */
    #[IsGranted('ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/{id}/edit', name: 'legal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Legal $legal): Response
    {
        $em = $this->managerRegistry->getManager();
        $editForm = $this->createEditForm($legal);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', "L'aspect légal a bien été modifié");

            return $this->redirectToRoute('legal');
        }

        return $this->render(
            '@Sepulture/legal/edit.html.twig',
            [
                'entity' => $legal,
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to edit a Legal entity.
     *
     * @param Legal $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(Legal $entity): FormInterface
    {
        return $this->createForm(
            LegalType::class,
            $entity,
            [
                'action' => $this->generateUrl('legal_edit', [
                    'id' => $entity->getId(),
                ]),
            ]
        );
    }

    /**
     * Deletes a Legal entity.
     */
    #[IsGranted('ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/{id}/delete', name: 'legal_delete', methods: ['POST'])]
    public function delete(Request $request, $id): RedirectResponse
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $entity = $em->getRepository(Legal::class)->find($id);

            if (null === $entity) {
                throw $this->createNotFoundException('Unable to find Legal entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->addFlash('success', "L'aspect légal a bien été supprimé");
        }

        return $this->redirectToRoute('legal');
    }

    /**
     * Creates a form to delete a Legal entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return FormInterface The form
     */
    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('legal_delete', [
                'id' => $id,
            ]))

            ->add('submit', SubmitType::class, [
                'label' => 'Delete',
                'attr' => [
                    'class' => 'btn-danger',
                    
                ], ])
            ->getForm();
    }
}
