<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Cimetiere;
use AcMarche\Sepulture\Entity\Sepulture;
use AcMarche\Sepulture\Service\CimetiereUtil;
use AcMarche\Sepulture\Service\FinderJf;
use AcMarche\Sepulture\Service\PdfFactory;
use AcMarche\Sepulture\Service\XlsFactory;
use AcMarche\Sepulture\Service\ZipFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ExportController.
 *
 * @Route("/export")
 */
class ExportController extends AbstractController
{
    /**
     * @var PdfFactory
     */
    private $pdfFactory;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var XlsFactory
     */
    private $xlsFactory;
    /**
     * @var FinderJf
     */
    private $finderJf;
    /**
     * @var CimetiereUtil
     */
    private $cimetiereUtil;
    /**
     * @var ZipFactory
     */
    private $zipFactory;

    public function __construct(
        SessionInterface $session,
        PdfFactory $pdfFactory,
        XlsFactory $xlsFactory,
        FinderJf $finderJf,
        CimetiereUtil $cimetiereUtil,
        ZipFactory $zipFactory
    ) {
        $this->pdfFactory = $pdfFactory;
        $this->session = $session;
        $this->xlsFactory = $xlsFactory;
        $this->finderJf = $finderJf;
        $this->cimetiereUtil = $cimetiereUtil;
        $this->zipFactory = $zipFactory;
    }

    /**
     * @Route("/sepulture/{slug}", name="export_sepulture_pdf", methods={"GET"})
     */
    public function sepulture(Sepulture $sepulture)
    {
        return $this->pdfFactory->sepulture($sepulture);
    }

    /**
     * @Route("/search", name="export_sepulture_search_pdf", methods={"GET"})
     */
    public function search()
    {
        if (!$this->session->has('sepulture_search')) {
            return $this->redirectToRoute('sepulture');
        }

        return $this->pdfFactory->search();
    }

    /**
     * @Route("/cimetiere/{slug}", name="export_cimetiere_pdf", methods={"GET"})
     */
    public function cimetiere(Cimetiere $cimetiere)
    {
        return $this->pdfFactory->cimetiere($cimetiere);
    }

    /**
     * @Route("/sihl/{id}/rw/{rw}", name="export_sihl_pdf", methods={"GET"})
     */
    public function sihl(Cimetiere $cimetiere, bool $rw = false)
    {
        $response = $this->pdfFactory->sihl($cimetiere, $rw);

        if ($rw) {
            return $this->redirectToRoute('export_rw');
        }

        return $response;
    }

    /**
     * @Route("/a1945/{id}/rw/{rw}", name="export_avant1945_pdf", methods={"GET"})
     */
    public function a1945(Cimetiere $cimetiere, bool $rw = false)
    {
        $response = $this->pdfFactory->a1945($cimetiere, $rw);
        if ($rw) {
            return $this->redirectToRoute('export_rw');
        }

        return $response;
    }

    /**
     * @Route("/indigent/", name="export_indigent_xls", methods={"GET"})
     */
    public function indigent()
    {
        return $this->xlsFactory->create();
    }

    /**
     * @Route("/finish/{slug}", name="export_rw_cimetiere", methods={"GET"})
     * @Route("/finish", name="export_rw", methods={"GET"})
     */
    public function rw(Cimetiere $cimetiere = null)
    {
        $path = $this->finderJf->getExportDirectory();
        $directories = $this->finderJf->findDirectories($path);

        $files = [];

        if ($cimetiere) {
            $fullpath = $this->finderJf->getOuputPath($cimetiere);
            $files = $this->finderJf->find_all_files($fullpath, $cimetiere->getSlug());
        }

        return $this->render(
            '@Sepulture/export/rw.html.twig',
            [
                'directories' => $directories,
                'files' => $files,
                'cimetiere' => $cimetiere,
            ]
        );
    }

    /**
     * @Route("/zip/{id}", name="export_rw_cimetiere_zip", methods={"GET"})
     */
    public function downloadZip(Cimetiere $cimetiere)
    {
        $zip = $this->zipFactory->create($cimetiere);
        $fileName = $zip->filename;
        $zip->close();

        $response = new Response(file_get_contents($fileName));
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            basename($fileName)
        );

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

}
