<?php

namespace AcMarche\Sepulture\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
use AcMarche\Sepulture\Entity\Defunt;
use AcMarche\Sepulture\Entity\Sepulture;
use AcMarche\Sepulture\Form\DefuntType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Defunt controller.
 *
 * @Route("/defunt")
 */
class DefuntController extends AbstractController
{
    /**
     * Displays a form to create a new Defunt entity.
     *
     * @Route("/new/{id}", name="defunt_new", methods={"GET","POST"})
     * @IsGranted("ROLE_SEPULTURE_EDITEUR")
     */
    public function new(Request $request, Sepulture $sepulture): Response
    {
        $entity = new Defunt();
        $entity->setSepulture($sepulture);

        $form = $this->createForm(DefuntType::class, $entity)            ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            $entity->setUserAdd($user);
            $sepulture = $entity->getSepulture();

            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', 'Le défunt a bien été ajouté');

            return $this->redirectToRoute('sepulture_show', ['slug' => $sepulture->getSlug()]);
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
     *
     * @Route("/{id}", name="defunt_show", methods={"GET"})
     */
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
     *
     * @Route("/{id}/edit", name="defunt_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_SEPULTURE_EDITEUR")
     */
    public function edit(Request $request, Defunt $defunt): Response
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createEditForm($defunt);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            $sepulture = $defunt->getSepulture();

            $this->addFlash('success', 'Le défunt a bien été modifié');

            return $this->redirectToRoute('sepulture_show', ['slug' => $sepulture->getSlug()]);
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
        $form = $this->createForm(
            DefuntType::class,
            $entity,
            [
                
            ]
        );

        return $form;
    }

    /**
     * Deletes a Defunt entity.
     *
     * @Route("/{id}", name="defunt_delete", methods={"DELETE"})
     * @IsGranted("ROLE_SEPULTURE_EDITEUR")
     */
    public function delete(Request $request, $id): Response
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(Defunt::class)->find($id);

            if ($entity === null) {
                throw $this->createNotFoundException('Unable to find Defunt entity.');
            }

            $sepulture = $entity->getSepulture();

            $em->remove($entity);
            $em->flush();

            $this->addFlash('success', 'Le défunt a bien été supprimé');

            return $this->redirectToRoute('sepulture_show', ['slug' => $sepulture->getSlug()]);
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
            ->setAction($this->generateUrl('defunt_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete'])
            ->getForm();
    }
}
