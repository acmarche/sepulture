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

use AcMarche\Sepulture\Entity\Defunt;
use AcMarche\Sepulture\Repository\SepultureRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class XlsFactory
{
    private SepultureRepository $sepultureRepository;

    public function __construct(SepultureRepository $sepultureRepository)
    {
        $this->sepultureRepository = $sepultureRepository;
    }

    public function create(): BinaryFileResponse
    {
        $phpExcelObject = new Spreadsheet();

        $phpExcelObject->getProperties()->setCreator('intranet')
            ->setTitle('Liste des candidatures')
            ->setSubject('Office 2005 XLSX Test Document')
            ->setDescription('Test document for Office 2005 XLSX, generated using PHP classes.')
            ->setKeywords('office 2005 openxml php')
            ->setCategory('Test result file');

        $defunts = $this->createXlsObject($phpExcelObject);

        $this->defuntsXlsObject($phpExcelObject, $defunts);

        $writer = new Xlsx($phpExcelObject);
        $temp_file = tempnam(sys_get_temp_dir(), 'indigeants.xls');

        // Create the excel file in the tmp directory of the system
        try {
            $writer->save($temp_file);
        } catch (Exception $e) {
        }

        $fileName = 'indigeants.xls';
        $response = new BinaryFileResponse($temp_file);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            null === $fileName ? $response->getFile()->getFilename() : $fileName
        );

        return $response;
    }

    /**
     * @param Spreadsheet $phpExcelObject
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function createXlsObject(Spreadsheet $phpExcelObject): array
    {
        $sepultures = $this->sepultureRepository->getIndigents();

        $active = $phpExcelObject->setActiveSheetIndex(0);

        $defunts = [];

        /**
         * title.
         */
        $c = 1;
        $lettre = 'A';
        $active
            ->setCellValue($lettre++.$c, 'Id')
            ->setCellValue($lettre++.$c, 'Parcelle')
            ->setCellValue($lettre++.$c, 'Slugname')
            ->setCellValue($lettre++.$c, 'Cimetière')
            ->setCellValue($lettre++.$c, 'Cimetière Id')
            ->setCellValue($lettre++.$c, 'Aspect visuel')
            ->setCellValue($lettre++.$c, 'Architectural')
            ->setCellValue($lettre++.$c, 'Guerre')
            ->setCellValue($lettre++.$c, 'Combattant14')
            ->setCellValue($lettre++.$c, 'Combattant40')
            ->setCellValue($lettre++.$c, 'Social check')
            ->setCellValue($lettre++.$c, 'Social')
            ->setCellValue($lettre++.$c, 'Description')
            ->setCellValue($lettre++.$c, 'Created');

        $l = 2;

        foreach ($sepultures as $sepulture) {
            $lettre = 'A';

            //$birthday = $sepulture->getNeLe() != null ? $sepulture->getNeLe()->format($format) : '';

            //$sepulture = new Sepulture();

            $active
                ->setCellValue($lettre++.$l, $sepulture->getId())
                ->setCellValue($lettre++.$l, $sepulture->getParcelle())
                ->setCellValue($lettre++.$l, $sepulture->getSlug())
                ->setCellValue($lettre++.$l, $sepulture->getCimetiere()->getNom())
                ->setCellValue($lettre++.$l, $sepulture->getCimetiere()->getId())
                ->setCellValue($lettre++.$l, $sepulture->getAspectVisuel())
                ->setCellValue($lettre++.$l, $sepulture->getArchitectural())
                ->setCellValue($lettre++.$l, $sepulture->getGuerre())
                ->setCellValue($lettre++.$l, $sepulture->getCombattant14())
                ->setCellValue($lettre++.$l, $sepulture->getCombattant40())
                ->setCellValue($lettre++.$l, $sepulture->getSocialeCheck())
                ->setCellValue($lettre++.$l, $sepulture->getSociale())
                ->setCellValue($lettre++.$l, $sepulture->getDescription())
                ->setCellValue($lettre++.$l, $sepulture->getCreatedAt()->format('Y-m-d'));
            ++$l;

            $morts = $sepulture->getDefunts();

            foreach ($morts as $mort) {
                $defunts[] = $mort;
            }
        }

        $phpExcelObject->getActiveSheet()->setTitle('Sepultures');
        $phpExcelObject->setActiveSheetIndex(0);

        return $defunts;
    }

    /**
     * @param Spreadsheet $phpExcelObject
     * @param Defunt[] $defunts
     *
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function defuntsXlsObject(Spreadsheet $phpExcelObject, $defunts): Spreadsheet
    {
        $phpExcelObject->createSheet();
        $active = $phpExcelObject->setActiveSheetIndex(1);

        /**
         * title.
         */
        $c = 1;
        $lettre = 'A';
        $active
            ->setCellValue($lettre++.$c, 'Id')
            ->setCellValue($lettre++.$c, 'Id sepulture')
            ->setCellValue($lettre++.$c, 'Nom')
            ->setCellValue($lettre++.$c, 'Prenom')
            ->setCellValue($lettre++.$c, 'Ne le')
            ->setCellValue($lettre++.$c, 'Ne a')
            ->setCellValue($lettre++.$c, 'Mort le')
            ->setCellValue($lettre++.$c, 'Mort a')
            ->setCellValue($lettre++.$c, 'Fonction')
            ->setCellValue($lettre++.$c, 'Created');

        $l = 2;

        foreach ($defunts as $defunt) {
            $lettre = 'A';

            //$birthday = $sepulture->getNeLe() != null ? $sepulture->getNeLe()->format($format) : '';

            //  $defunt = new Defunt();

            $active
                ->setCellValue($lettre++.$l, $defunt->getId())
                ->setCellValue($lettre++.$l, $defunt->getSepulture()->getId())
                ->setCellValue($lettre++.$l, $defunt->getNom())
                ->setCellValue($lettre++.$l, $defunt->getPrenom())
                ->setCellValue($lettre++.$l, $defunt->getBirthday())
                ->setCellValue($lettre++.$l, $defunt->getLieuNaissance())
                ->setCellValue($lettre++.$l, $defunt->getDateDeces())
                ->setCellValue($lettre++.$l, $defunt->getLieuDeces())
                ->setCellValue($lettre++.$l, $defunt->getFonction())
                ->setCellValue($lettre++.$l, $defunt->getCreatedAt()->format('Y-m-d'));
            ++$l;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Defunts');
        $phpExcelObject->setActiveSheetIndex(1);

        return $phpExcelObject;
    }
}