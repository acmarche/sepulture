<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Visuel;
use AcMarche\Sepulture\Form\VisuelType;
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
 * Visuel controller.
 */
#[Route(path: '/visuel')]
class VisuelController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry
    ) {
    }

    /**
     * Lists all Visuel entities.
     */
    #[Route(path: '/', name: 'visuel', methods: ['GET'])]
    public function index(): Response
    {
        $em = $this->managerRegistry->getManager();
        $entities = $em->getRepository(Visuel::class)->findAll();

        return $this->render(
            '@Sepulture/visuel/index.html.twig',
            [
                'entities' => $entities,
            ]
        );
    }

    /**
     * Creates a form to create a Visuel entity.
     *
     * @param Visuel $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Visuel $entity): FormInterface
    {
        return     $this->createForm(
            VisuelType::class,
            $entity,
            [
                'action' => $this->generateUrl('visuel_new'),
                'method' => 'POST',
            ]
        );
    }

    /**
     * Displays a form to create a new Visuel entity.
     */
    #[IsGranted('ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/new', name: 'visuel_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $entity = new Visuel();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'Le visuel a bien été ajouté');

            return $this->redirectToRoute('visuel');
        }

        return $this->render(
            '@Sepulture/visuel/new.html.twig',
            [
                'entity' => $entity,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Visuel entity.
     */
    #[Route(path: '/{id}', name: 'visuel_show', methods: ['GET'])]
    public function show(Visuel $visuel): Response
    {
        $deleteForm = $this->createDeleteForm($visuel->getId());

        return $this->render(
            '@Sepulture/visuel/show.html.twig',
            [
                'entity' => $visuel,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Visuel entity.
     */
    #[IsGranted('ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/{id}/edit', name: 'visuel_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Visuel $visuel): Response
    {
        $em = $this->managerRegistry->getManager();
        $editForm = $this->createEditForm($visuel);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Le visuel a bien été modifié');

            return $this->redirectToRoute('visuel');
        }

        return $this->render(
            '@Sepulture/visuel/edit.html.twig',
            [
                'entity' => $visuel,
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to edit a Visuel entity.
     *
     * @param Visuel $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(Visuel $entity): FormInterface
    {
        return $this->createForm(
            VisuelType::class,
            $entity,
            [
                'action' => $this->generateUrl('visuel_edit', [
                    'id' => $entity->getId(),
                ]),
            ]
        );
    }

    /**
     * Deletes a Visuel entity.
     */
    #[IsGranted('ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/{id}/delete', name: 'visuel_delete', methods: ['POST'])]
    public function delete(Request $request, $id): RedirectResponse
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $entity = $em->getRepository(Visuel::class)->find($id);

            if (null === $entity) {
                throw $this->createNotFoundException('Unable to find Visuel entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->addFlash('success', 'Le visuel a bien été supprimé');
        }

        return $this->redirectToRoute('visuel');
    }

    /**
     * Creates a form to delete a Visuel entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return FormInterface The form
     */
    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('visuel_delete', [
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
