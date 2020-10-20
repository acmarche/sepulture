<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\TypeSepulture;
use AcMarche\Sepulture\Form\TypeSepultureType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * TypeSepulture controller.
 *
 * @Route("/typesepulture")
 */
class TypeSepultureController extends AbstractController
{
    /**
     * Lists all TypeSepulture entities.
     *
     * @Route("/", name="typesepulture", methods={"GET"})
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

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
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TypeSepulture $entity)
    {
        $form = $this->createForm(
            TypeSepultureType::class,
            $entity,
            [
                'action' => $this->generateUrl('typesepulture_new'),
                'method' => 'POST',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Create']);

        return $form;
    }

    /**
     * Displays a form to create a new TypeSepulture entity.
     *
     * @Route("/new", name="typesepulture_new", methods={"GET","POST"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function new(Request $request)
    {
        $entity = new TypeSepulture();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
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
     *
     * @Route("/{id}", name="typesepulture_show", methods={"GET"})
     */
    public function show(TypeSepulture $type)
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
     *
     * @Route("/{id}/edit", name="typesepulture_edit", methods={"GET","PUT"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function edit(Request $request, TypeSepulture $type)
    {
        $em = $this->getDoctrine()->getManager();

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
                'edit_form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to edit a TypeSepulture entity.
     *
     * @param TypeSepulture $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TypeSepulture $entity)
    {
        $form = $this->createForm(
            TypeSepultureType::class,
            $entity,
            [
                'action' => $this->generateUrl('typesepulture_edit', ['id' => $entity->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Update']);

        return $form;
    }

    /**
     * Deletes a TypeSepulture entity.
     *
     * @Route("/{id}", name="typesepulture_delete", methods={"DELETE"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function delete(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(TypeSepulture::class)->find($id);

            if (!$entity) {
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
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('typesepulture_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }
}
