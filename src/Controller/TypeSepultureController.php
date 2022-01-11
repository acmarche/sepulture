<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\TypeSepulture;
use AcMarche\Sepulture\Form\TypeSepultureType;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * TypeSepulture controller.
 */
#[Route(path: '/typesepulture')]
class TypeSepultureController extends AbstractController
{
    public function __construct(
        private ManagerRegistry $managerRegistry
    ) {
    }

    /**
     * Lists all TypeSepulture entities.
     */
    #[Route(path: '/', name: 'typesepulture', methods: ['GET'])]
    public function index(): Response
    {
        $em = $this->managerRegistry->getManager();
        $entities = $em->getRepository(TypeSepulture::class)->findAll();

        return $this->render(
            '@Sepulture/type_sepulture/index.html.twig',
            [
                'entities' => $entities,
            ]
        );
    }

    /**
     * Creates a form to create a TypeSepulture entity.
     *
     * @param TypeSepulture $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(TypeSepulture $entity): FormInterface
    {
        return      $this->createForm(
            TypeSepultureType::class,
            $entity,
            [
                'action' => $this->generateUrl('typesepulture_new'),
                'method' => 'POST',
            ]
        );
    }

    /**
     * Displays a form to create a new TypeSepulture entity.
     */
    #[IsGranted(data: 'ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/new', name: 'typesepulture_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $entity = new TypeSepulture();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'Le type a bien été ajouté');

            return $this->redirectToRoute('typesepulture');
        }

        return $this->render(
            '@Sepulture/type_sepulture/new.html.twig',
            [
                'entity' => $entity,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a TypeSepulture entity.
     */
    #[Route(path: '/{id}', name: 'typesepulture_show', methods: ['GET'])]
    public function show(TypeSepulture $type): Response
    {
        $deleteForm = $this->createDeleteForm($type->getId());

        return $this->render(
            '@Sepulture/type_sepulture/show.html.twig',
            [
                'entity' => $type,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing TypeSepulture entity.
     */
    #[IsGranted(data: 'ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/{id}/edit', name: 'typesepulture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeSepulture $type): Response
    {
        $em = $this->managerRegistry->getManager();
        $editForm = $this->createEditForm($type);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Le type a bien été modifié');

            return $this->redirectToRoute('typesepulture');
        }

        return $this->render(
            '@Sepulture/type_sepulture/edit.html.twig',
            [
                'entity' => $type,
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to edit a TypeSepulture entity.
     *
     * @param TypeSepulture $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(TypeSepulture $entity): FormInterface
    {
        return $this->createForm(
            TypeSepultureType::class,
            $entity,
            [
                'action' => $this->generateUrl('typesepulture_edit', [
                    'id' => $entity->getId(),
                ]),
            ]
        );
    }

    /**
     * Deletes a TypeSepulture entity.
     */
    #[IsGranted(data: 'ROLE_SEPULTURE_ADMIN')]
    #[Route(path: '/{id}/delete', name: 'typesepulture_delete', methods: ['POST'])]
    public function delete(Request $request, $id): RedirectResponse
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $entity = $em->getRepository(TypeSepulture::class)->find($id);

            if (null === $entity) {
                throw $this->createNotFoundException('Unable to find TypeSepulture entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->addFlash('success', 'Le type a bien été supprimé');
        }

        return $this->redirectToRoute('typesepulture');
    }

    /**
     * Creates a form to delete a TypeSepulture entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return FormInterface The form
     */
    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('typesepulture_delete', [
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
