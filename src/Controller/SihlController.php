<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Sihl;
use AcMarche\Sepulture\Form\SihlType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Sihl controller.
 *
 * @Route("/sihl")
 */
class SihlController extends AbstractController
{
    /**
     * Lists all Sihl entities.
     *
     * @Route("/", name="sihl", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(Sihl::class)->findAll();

        return $this->render(
            '@Sepulture/sihl/index.html.twig', [
            'entities' => $entities,
        ]);
    }

    /**
     * Creates a form to create a Sihl entity.
     *
     * @param Sihl $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Sihl $entity)
    {
        $form = $this->createForm(
            SihlType::class,
            $entity,
            [
                'action' => $this->generateUrl('sihl_new'),
                'method' => 'POST',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Create']);

        return $form;
    }

    /**
     * Displays a form to create a new Sihl entity.
     *
     * @Route("/new", name="sihl_new", methods={"GET","POST"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function newAction(Request $request)
    {
        $entity = new Sihl();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'Le sihl a bien été ajouté');

            return $this->redirectToRoute('sihl');
        }

        return $this->render(
            '@Sepulture/sihl/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Sihl entity.
     *
     * @Route("/{id}", name="sihl_show", methods={"GET"})
     */
    public function showAction(Sihl $sihl)
    {
        $deleteForm = $this->createDeleteForm($sihl->getId());

        return $this->render(
            '@Sepulture/sihl/show.html.twig', [
            'entity' => $sihl,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Sihl entity.
     *
     * @Route("/{id}/edit", name="sihl_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function editAction(Request $request, Sihl $sihl)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createEditForm($sihl);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Le sihl a bien été modifié');

            return $this->redirectToRoute('sihl');
        }

        return $this->render(
            '@Sepulture/sihl/edit.html.twig', [
            'entity' => $sihl,
            'edit_form' => $editForm->createView(),
        ]);
    }

    /**
     * Creates a form to edit a Sihl entity.
     *
     * @param Sihl $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Sihl $entity)
    {
        $form = $this->createForm(
            SihlType::class,
            $entity,
            [
                'action' => $this->generateUrl('sihl_edit', ['id' => $entity->getId()]),
                'method' => 'POST',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Update']);

        return $form;
    }

    /**
     * Deletes a Sihl entity.
     *
     * @Route("/{id}", name="sihl_delete", methods={"DELETE"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(Sihl::class)->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Sihl entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->addFlash('success', 'Le sihl a bien été supprimé');
        }

        return $this->redirectToRoute('sihl');
    }

    /**
     * Creates a form to delete a Sihl entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sihl_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }
}
