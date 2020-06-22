<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Sepulture;
use AcMarche\Sepulture\Service\FileHelper;
use AcMarche\Sepulture\Service\Mailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Image controller.
 *
 * @Route("/image")
 */
class ImageController extends AbstractController
{
    /**
     * @var FileHelper
     */
    private $fileHelper;
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(FileHelper $fileHelper, Mailer $mailer)
    {
        $this->fileHelper = $fileHelper;
        $this->mailer = $mailer;
    }

    /**
     * Displays a form to create a new Image entity.
     *
     * @Route("/new/{id}", name="image_edit", methods={"GET"})
     * @IsGranted("ROLE_SEPULTURE_EDITEUR")
     */
    public function editAction(Sepulture $sepulture)
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('image_upload', ['id' => $sepulture->getId()]))
            ->setMethod('POST')
            ->getForm();

        $images = $this->fileHelper->getImages($sepulture->getId());
        $deleteForm = $this->createDeleteForm($sepulture->getId());

        return $this->render(
            '@Sepulture/image/edit.html.twig',
            [
                'images' => $images,
                'form_delete' => $deleteForm->createView(),
                'sepulture' => $sepulture,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/upload/{id}", name="image_upload", methods={"POST"})
     * @IsGranted("ROLE_SEPULTURE_EDITEUR")
     */
    public function uploadAction(Request $request, Sepulture $sepulture)
    {
        if ($request->isXmlHttpRequest()) {
            $file = $request->files->get('file');

            if ($file instanceof UploadedFile) {
                if (!preg_match('#image#', $file->getMimeType())) {
                    $this->mailer->sendError('Sepulture, pas image', $sepulture.' : '.$file->getMimeType());

                    return new Response('ko');
                }
                $fileName = md5(uniqid()).'.'.$file->guessClientExtension();
                $directory = $this->getParameter(
                        'acmarche_sepulture_upload_sepulture_directory'
                    ).DIRECTORY_SEPARATOR.$sepulture->getId();

                try {
                    $this->fileHelper->uploadFile($directory, $file, $fileName);
                } catch (FileException $error) {
                    $this->addFlash('danger', $error->getMessage());
                }
            }

            return new Response('okid');
        }

        return new Response('ko');
    }

    /**
     * Deletes a Image entity.
     *
     * @Route("/delete/{sepultureId}", name="image_delete", methods={"DELETE"})
     * @IsGranted("ROLE_SEPULTURE_EDITEUR")
     */
    public function deleteAction(Request $request, $sepultureId)
    {
        $em = $this->getDoctrine()->getManager();
        $sepulture = $em->getRepository(Sepulture::class)->find($sepultureId);

        if (!$sepulture) {
            throw $this->createNotFoundException('Unable to find Sepulture entity.');
        }

        $form = $this->createDeleteForm($sepultureId);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $files = $request->get('img', false);

            if (!$files) {
                $this->addFlash('danger', "Vous n'avez sélectionnez aucune photo");

                return $this->redirectToRoute('image_edit', ['sepulture' => $sepulture->getSlug()]);
            }

            $directory = $this->getParameter(
                    'acmarche_sepulture_upload_sepulture_directory'
                ).DIRECTORY_SEPARATOR.$sepulture->getId().DIRECTORY_SEPARATOR;
            foreach ($files as $filename) {
                try {
                    $this->fileHelper->deleteOneDoc($directory, $filename);
                    $this->addFlash('success', "L'image $filename a bien été supprimée");
                } catch (FileException $e) {
                    $this->addFlash('danger', "L'image  $filename n'a pas pu être supprimée. ");
                }
            }
        }

        return $this->redirectToRoute('image_edit', ['id' => $sepulture->getId()]);
    }

    /**
     * Creates a form to delete a Image entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('image_delete', ['sepultureId' => $id]))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                ['label' => 'Supprimer les images sélectionnées', 'attr' => ['class' => 'btn-danger btn-xs']]
            )
            ->getForm();
    }
}
