<?php

namespace AcMarche\Sepulture\Controller;

use AcMarche\Sepulture\Entity\Cimetiere;
use AcMarche\Sepulture\Entity\Sepulture;
use AcMarche\Sepulture\Service\FinderJf;
use AcMarche\Sepulture\Service\PdfFactory;
use AcMarche\Sepulture\Service\SpreadsheetDownloaderTrait;
use AcMarche\Sepulture\Service\XlsFactory;
use AcMarche\Sepulture\Service\ZipFactory;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_SEPULTURE_EDITEUR')]
#[Route(path: '/export')]
class ExportController extends AbstractController
{
    use SpreadsheetDownloaderTrait;

    public function __construct(
        private PdfFactory $pdfFactory,
        private XlsFactory $xlsFactory,
        private FinderJf $finderJf,
        private ZipFactory $zipFactory
    ) {
    }

    #[Route(path: '/sepulture/{slug}', name: 'export_sepulture_pdf', methods: ['GET'])]
    public function sepulture(Sepulture $sepulture): Response
    {
        return $this->pdfFactory->sepulture($sepulture);
    }

    #[Route(path: '/search', name: 'export_sepulture_search_pdf', methods: ['GET'])]
    public function search(Request $request): Response
    {
        if (!$request->getSession()->has('sepulture_search')) {
            return $this->redirectToRoute('sepulture');
        }

        return $this->pdfFactory->search();
    }

    #[Route(path: '/cimetiere/{slug}', name: 'export_cimetiere_pdf', methods: ['GET'])]
    public function cimetiere(Cimetiere $cimetiere): PdfResponse
    {
        return $this->pdfFactory->cimetiere($cimetiere);
    }

    #[Route(path: '/sihl/{id}/rw/{rw}', name: 'export_sihl_pdf', methods: ['GET'])]
    public function sihl(Cimetiere $cimetiere, bool $rw = false)
    {
        $response = $this->pdfFactory->sihl($cimetiere, $rw);
        if ($rw) {
            return $this->redirectToRoute('export_rw');
        }

        return $response;
    }

    #[Route(path: '/a1945/{id}/rw/{rw}', name: 'export_avant1945_pdf', methods: ['GET'])]
    public function a1945(Cimetiere $cimetiere, bool $rw = false)
    {
        $response = $this->pdfFactory->a1945($cimetiere, $rw);
        if ($rw) {
            return $this->redirectToRoute('export_rw');
        }

        return $response;
    }

    #[Route(path: '/indigent/', name: 'export_indigent_xls', methods: ['GET'])]
    public function indigent(): Response
    {
        $xls = $this->xlsFactory->create();

        return $this->downloadXls($xls, 'indigeants.xls');
    }

    #[Route(path: '/defunts/', name: 'export_defunts_xls', methods: ['GET'])]
    public function defunts(): Response
    {
        $xls = $this->xlsFactory->createDefunts();

        return $this->downloadXls($xls, 'defunts.xls');
    }

    #[Route(path: '/finish/{slug}', name: 'export_rw_cimetiere', methods: ['GET'])]
    #[Route(path: '/finish', name: 'export_rw', methods: ['GET'])]
    public function rw(Cimetiere $cimetiere = null): Response
    {
        $path = $this->finderJf->getExportDirectory();
        $directories = $this->finderJf->findDirectories($path);
        $files = [];
        if (null !== $cimetiere) {
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

    #[Route(path: '/zip/{id}', name: 'export_rw_cimetiere_zip', methods: ['GET'])]
    public function downloadZip(Cimetiere $cimetiere): Response
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
