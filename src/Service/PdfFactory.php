<?php
/**
 * This file is part of sepulture application
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 6/11/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace AcMarche\Sepulture\Service;

use AcMarche\Sepulture\Entity\Cimetiere;
use AcMarche\Sepulture\Entity\Sepulture;
use AcMarche\Sepulture\Form\Rw\Rw1945Type;
use AcMarche\Sepulture\Form\Rw\SihRwType;
use AcMarche\Sepulture\Form\Rw\SihStatutType;
use AcMarche\Sepulture\Repository\SepultureRepository;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;

class PdfFactory
{
    private FileHelper $fileHelper;
    private Environment $environment;
    private Pdf $pdf;
    private SessionInterface $session;
    private SepultureRepository $sepultureRepository;
    private CimetiereUtil $cimetiereUtil;
    private ParameterBagInterface $parameterBag;
    private FormFactoryInterface $formFactory;
    private FinderJf $finderJf;

    public function __construct(
        FileHelper $fileHelper,
        Environment $environment,
        Pdf $pdf,
        SessionInterface $session,
        SepultureRepository $sepultureRepository,
        CimetiereUtil $cimetiereUtil,
        ParameterBagInterface $parameterBag,
        FormFactoryInterface $formFactory,
        FinderJf $finderJf
    ) {
        $this->fileHelper = $fileHelper;
        $this->environment = $environment;
        $this->pdf = $pdf;
        $this->session = $session;
        $this->sepultureRepository = $sepultureRepository;
        $this->cimetiereUtil = $cimetiereUtil;
        $this->parameterBag = $parameterBag;
        $this->formFactory = $formFactory;
        $this->finderJf = $finderJf;
    }

    public function sepulture(Sepulture $sepulture): Response
    {
        $images = $this->fileHelper->getImages($sepulture->getId());

        $html = $this->environment->render(
            '@Sepulture/export/sepulture.html.twig',
            [
                'sepulture' => $sepulture,
                'images' => $images,
                'cimetiere' => false,
            ]
        );

        $name = 'sepulture-'.$sepulture->getSlug();

        return new Response(
            $this->pdf->getOutputFromHtml($html), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$name.'.pdf"',
            ]
        );
    }


    public function search(): PdfResponse
    {
        $data = unserialize($this->session->get('sepulture_search'));

        $sepultures = $this->sepultureRepository->search($data);

        $html = $this->environment->render(
            '@Sepulture/export/head.html.twig',
            [
                'entity' => 'Recherche',
                'sepultures' => $sepultures,
                'cimetiere' => true,
                'rw' => false,
            ]
        );

        foreach ($sepultures as $sepulture) {
            $images = $this->fileHelper->getImages($sepulture->getId());

            $html .= $this->environment->render(
                '@Sepulture/export/content.html.twig',
                [
                    'entity' => $sepulture,
                    'images' => $images,
                ]
            );
        }

        $html .= $this->environment->render('@Sepulture/export/foot.html.twig', []);

        $name = 'Export-'.date('d-m-Y');

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html),
            $name.'.pdf'
        );
    }


    public function cimetiere(Cimetiere $cimetiere): PdfResponse
    {
        $sepultures = $this->sepultureRepository->search(['cimetiere' => $cimetiere]);

        $html = $this->environment->render(
            '@Sepulture/export/head.html.twig',
            [
                'entity' => $cimetiere,
                'sepultures' => $sepultures,
                'cimetiere' => true,
                'rw' => false,
            ]
        );

        foreach ($sepultures as $sepulture) {
            $images = $this->fileHelper->getImages($sepulture->getId());

            $html .= $this->environment->render(
                '@Sepulture/export/content.html.twig',
                [
                    'entity' => $sepulture,
                    'images' => $images,
                ]
            );
        }

        $html .= $this->environment->render('@Sepulture/export/foot.html.twig', []);

        $name = $cimetiere->getSlug();

        //  return new Response($html);
        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html),
            $name.'.pdf'
        );
    }

    public function sihl(Cimetiere $cimetiere, bool $rw = false): ?PdfResponse
    {
        $ville = $this->parameterBag->get('acmarche_sepulture_nom_ville');

        $sepultures = $this->sepultureRepository->getImportanceHistorique($cimetiere);

        $head = $this->environment->render(
            '@Sepulture/export/head.html.twig',
            [
                'entity' => $cimetiere,
                'cimetiere' => null,
                'sepulutres' => null,
                'rw' => $rw,
            ]
        );

        $shil = $this->environment->render(
            '@Sepulture/export/sihl.html.twig',
            [
                'cimetiere' => $cimetiere,
                'ville' => $ville,
            ]
        );

        $foot = $this->environment->render('@Sepulture/export/foot.html.twig', []);

        $contactRw = $this->cimetiereUtil->getContactRw();
        if ($rw) {
            $this->finderJf->cleanFolder($cimetiere);
        }

        foreach ($sepultures as $sepulture) {
            $images = $this->fileHelper->getImages($sepulture->getId());
            $form = $this->formFactory->create(SihStatutType::class, $sepulture);
            $formRw = $this->formFactory->create(SihRwType::class);
            $fileName = $sepulture->getSlug().'_ihl.pdf';

            if ($rw) {
                $encare = $this->environment->render(
                    '@Sepulture/export/_encare_sihl.html.twig',
                    [
                        'form' => $form->createView(),
                        'formRw' => $formRw->createView(),
                        'contactRw' => $contactRw,
                    ]
                );
            }

            $content = $this->environment->render(
                '@Sepulture/export/content.html.twig',
                [
                    'entity' => $sepulture,
                    'images' => $images,
                ]
            );

            if ($rw) {
                $html = $head.$encare.$content.$foot;
                $this->pdf->generateFromHtml($html, $this->finderJf->getOuputPath($cimetiere).$fileName);
            }
        }

        if (!$rw) {
            $html = $head.$shil.$content.$foot;
            $name = 'sihl';

            //  return new Response($html);
            return new PdfResponse(
                $this->pdf->getOutputFromHtml($html),
                $name.'.pdf'
            );
        }

        return null;
    }


    public function a1945(Cimetiere $cimetiere, bool $rw = false): ?PdfResponse
    {
        $ville = $this->parameterBag->get('acmarche_sepulture_nom_ville');

        $sepultures = $this->sepultureRepository->getAvant1945($cimetiere);

        $head = $this->environment->render(
            '@Sepulture/export/head.html.twig',
            [
                'entity' => $cimetiere,
                'cimetiere' => null,
                'sepulutres' => null,
                'rw' => $rw,
            ]
        );

        $entete = $this->environment->render(
            '@Sepulture/export/a1945.html.twig',
            [
                'cimetiere' => $cimetiere,
                'ville' => $ville,
            ]
        );

        $foot = $this->environment->render('@Sepulture/export/foot.html.twig', []);

        $contactRw = $this->cimetiereUtil->getContactRw();

        if ($rw) {
            $this->finderJf->cleanFolder($cimetiere);
        }

        foreach ($sepultures as $sepulture) {
            $images = $this->fileHelper->getImages($sepulture->getId());
            $formRw = $this->formFactory->create(Rw1945Type::class);
            $fileName = $sepulture->getSlug().'_a1945.pdf';

            if ($rw) {
                $encare = $this->environment->render(
                    '@Sepulture/export/_encare_1945.html.twig',
                    [
                        'formRw' => $formRw->createView(),
                        'contactRw' => $contactRw,
                    ]
                );
            }

            $content = $this->environment->render(
                '@Sepulture/export/content.html.twig',
                [
                    'entity' => $sepulture,
                    'images' => $images,
                ]
            );

            if ($rw) {
                $html = $head.$encare.$content.$foot;
                $this->pdf->generateFromHtml($html, $this->finderJf->getOuputPath($cimetiere).$fileName);
            }

        }

        if (!$rw) {
            $html = $head.$entete.$content.$foot;
            $name = 'a1945';

            //  return new Response($html);
            return new PdfResponse(
                $this->pdf->getOutputFromHtml($html),
                $name.'.pdf'
            );
        }

        return null;
    }
}
