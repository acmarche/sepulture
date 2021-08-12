<?php

namespace AcMarche\Sepulture\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\FormInterface;
use AcMarche\Sepulture\Entity\Cimetiere;
use AcMarche\Sepulture\Entity\Sepulture;
use AcMarche\Sepulture\Form\CimetiereType;
use AcMarche\Sepulture\Repository\SepultureRepository;
use AcMarche\Sepulture\Service\CimetiereFileService;
use AcMarche\Sepulture\Service\CimetiereUtil;
use AcMarche\Sepulture\Service\FileHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Cimetiere controller.
 *
 * @Route("/cimetiere")
 */
class CimetiereController extends AbstractController
{
    private CimetiereUtil $cimetiereUtil;
    private CimetiereFileService $cimetiereFileService;
    private FileHelper $fileHelper;
    private SepultureRepository $sepultureRepository;

    public function __construct(
        CimetiereUtil $cimetiereUtil,
        CimetiereFileService $cimetiereFileService,
        FileHelper $fileHelper,
        SepultureRepository $sepultureRepository
    ) {
        $this->cimetiereUtil = $cimetiereUtil;
        $this->cimetiereFileService = $cimetiereFileService;
        $this->fileHelper = $fileHelper;
        $this->sepultureRepository = $sepultureRepository;
    }

    /**
     * Lists all Cimetiere entities.
     *
     * @Route("/", name="cimetiere", methods={"GET"})
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(Cimetiere::class)->search([]);
        foreach ($entities as $cimetiere) {
            $ihs = $this->sepultureRepository->getImportanceHistorique($cimetiere);
            $cimetiere->setIhsCount(count($ihs));
            $a1945 = $this->sepultureRepository->getAvant1945($cimetiere);
            $cimetiere->setA1945Count(count($a1945));
        }

        return $this->render(
            '@Sepulture/cimetiere/index.html.twig',
            [
                'entities' => $entities,
            ]
        );
    }

    /**
     * Displays a form to create a new Cimetiere entity.
     *
     * @Route("/new", name="cimetiere_new", methods={"GET","POST"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $entity = new Cimetiere();

        $form = $this->createForm(CimetiereType::class, $entity);

        $form;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $cimetiere = $em->getRepository(Cimetiere::class)->findoneBy(['nom' => $entity->getNom()]);

            if ($cimetiere !== null) {
                $this->addFlash('error', 'Il ne peut y avoir deux cimetière avec le même nom');

                return $this->redirectToRoute('cimetiere_new');
            }

            $em->persist($entity);
            $em->flush();

            $this->cimetiereFileService->traitfiles($form, $entity);

            $this->addFlash('success', 'Le cimetière a bien été ajouté');

            return $this->redirectToRoute('cimetiere_show', ['slug' => $entity->getSlug()]);
        }

        return $this->render(
            '@Sepulture/cimetiere/new.html.twig',
            [
                'entity' => $entity,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Cimetiere entity.
     *
     * @Route("/{slug}", name="cimetiere_show", methods={"GET"})
     */
    public function show(Cimetiere $cimetiere): Response
    {
        $em = $this->getDoctrine()->getManager();
        $sepultures = $em->getRepository(Sepulture::class)->search(['cimetiere' => $cimetiere]);

        $deleteForm = $this->createDeleteForm($cimetiere->getId());
        $deleteFileForm = $this->createFileDeleteForm($cimetiere->getId());

        return $this->render(
            '@Sepulture/cimetiere/show.html.twig',
            [
                'sepultures' => $sepultures,
                'cimetiere' => $cimetiere,
                'delete_form' => $deleteForm->createView(),
                'delete_file_form' => $deleteFileForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Cimetiere entity.
     *
     * @Route("/{slug}/edit", name="cimetiere_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function edit(Request $request, Cimetiere $cimetiere): Response
    {
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createEditForm($cimetiere);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->cimetiereFileService->traitfiles($editForm, $cimetiere);

            $this->addFlash('success', 'Le cimetière a bien été modifié');

            return $this->redirectToRoute('cimetiere_show', ['slug' => $cimetiere->getSlug()]);
        }

        return $this->render(
            '@Sepulture/cimetiere/edit.html.twig',
            [
                'entity' => $cimetiere,
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to edit a Cimetiere entity.
     *
     * @param Cimetiere $entity The entity
     *
     * @return FormInterface The form
     */
    private function createEditForm(Cimetiere $entity): FormInterface
    {
        $form = $this->createForm(
            CimetiereType::class,
            $entity,
            [
                'action' => $this->generateUrl('cimetiere_edit', ['slug' => $entity->getSlug()]),
                
            ]
        );

        

        return $form;
    }

    /**
     * Deletes a Cimetiere entity.
     *
     * @Route("/{id}", name="cimetiere_delete", methods={"DELETE"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function delete(Request $request, $id): Response
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(Cimetiere::class)->find($id);

            if ($entity === null) {
                throw $this->createNotFoundException('Unable to find Cimetiere entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->addFlash('success', 'Le cimetière a bien été supprimé');
        }

        return $this->redirectToRoute('cimetiere');
    }

    /**
     * Creates a form to delete a Cimetiere entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return FormInterface The form
     */
    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cimetiere_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }

    /**
     * Deletes a Image entity.
     *
     * @Route("/delete/file/{id}", name="cimetiere_file_delete", methods={"DELETE"})
     * @IsGranted("ROLE_SEPULTURE_ADMIN")
     */
    public function deleteFile(Request $request, Cimetiere $cimetiere): Response
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFileDeleteForm($cimetiere->getId());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $request->get('imageName', false);
            $plan = $request->get('planName', false);

            if (!$image && !$plan) {
                $this->addFlash('danger', "Vous n'avez sélectionnez aucun fichier");

                return $this->redirectToRoute('cimetiere_show', ['slug' => $cimetiere->getSlug()]);
            }

            $directory = $this->getParameter('acmarche_sepulture_upload_cimetiere_directory');

            if ($image) {
                try {
                    $this->fileHelper->deleteOneDoc($directory, $image);
                    $this->addFlash('success', "Le fichier $image a bien été supprimé");
                    $cimetiere->setImageName(null);
                } catch (FileException $e) {
                    $this->addFlash('danger', "Le fichier $image n'a pas pu être supprimé.");
                }
            }

            if ($plan) {
                try {
                    $this->fileHelper->deleteOneDoc($directory, $plan);
                    $this->addFlash('success', "Le fichier $plan a bien été supprimé");
                    $cimetiere->setPlanName(null);
                } catch (FileException $e) {
                    $this->addFlash('danger', "Le fichier $plan n'a pas pu être supprimé.");
                }
            }

            $em->persist($cimetiere);
            $em->flush();
        }

        return $this->redirectToRoute('cimetiere_show', ['slug' => $cimetiere->getSlug()]);
    }

    private function createFileDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cimetiere_file_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                ['label' => 'Supprimer les fichiers sélectionnés', 'attr' => ['class' => 'btn-danger btn-xs']]
            )
            ->getForm();
    }

    /**
     * @Route("/setdefault/{id}", name="cimetiere_set_default", methods={"GET"})
     * @IsGranted("ROLE_SEPULTURE_EDITEUR")
     * */
    public function setDefaultCimetiere(Request $request, Cimetiere $cimetiere): Response
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $cimetiereId = $request->get('cimetiere');
        $cimetiere = $em->getRepository(Cimetiere::class)->find($cimetiereId);

        if ($cimetiere === null) {
            $this->addFlash('error', 'Cimetière non trouvé');

            return $this->redirectToRoute('cimetiere');
        }

        $this->cimetiereUtil->setCimetiereByDefault($user->getUsername(), $cimetiere);

        $this->addFlash('success', 'Cimetière mis par défaut');

        return $this->redirectToRoute('cimetiere_show', ['slug' => $cimetiere->getSlug()]);
    }
}
