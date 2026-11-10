<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 6/11/18
 * Time: 14:00.
 */

namespace AcMarche\Sepulture\Service;

use AcMarche\Sepulture\Entity\Cimetiere;
use AcMarche\Sepulture\Repository\CimetiereRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CimetiereFileService
{
    private SessionInterface $session;

    public function __construct(
        private ParameterBagInterface $parameterBag,
        private FileHelper $fileHelper,
        private CimetiereRepository $cimetiereRepository,
        RequestStack $requestStack
    ) {
        $this->session = $requestStack->getSession();
    }

    public function traitfiles(FormInterface $form, Cimetiere $cimetiere): void
    {
        $image = $form->get('imageFile')->getData();
        $plan = $form->get('planFile')->getData();

        $directory = $this->parameterBag->get('acmarche_sepulture_upload_cimetiere_directory');

        $fileName = false;

        if ($image instanceof UploadedFile) {
            $fileName = md5(uniqid()).'.'.$image->guessClientExtension();

            try {
                $this->fileHelper->uploadFile($directory, $image, $fileName);
                $cimetiere->setImageName($fileName);
            } catch (FileException $error) {
                $this->session->getFlashBag()->add('danger', $error->getMessage());
            }
        }

        if ($plan instanceof UploadedFile) {
            $fileName = md5(uniqid()).'.'.$plan->guessClientExtension();

            try {
                $this->fileHelper->uploadFile($directory, $plan, $fileName);
                $cimetiere->setPlanName($fileName);
            } catch (FileException $error) {
                $this->session->getFlashBag()->add('danger', $error->getMessage());
            }
        }

        if ($fileName) {
            $this->cimetiereRepository->insert($cimetiere);
        }
    }
}
