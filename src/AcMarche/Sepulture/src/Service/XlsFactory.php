<?php
/**
 * This file is part of sepulture application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 6/11/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AcMarche\Sepulture\Service;

use AcMarche\Sepulture\Entity\Defunt;
use AcMarche\Sepulture\Repository\DefuntRepository;
use AcMarche\Sepulture\Repository\SepultureRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class XlsFactory
{
    public function __construct(
        private SepultureRepository $sepultureRepository,
        private DefuntRepository $defuntRepository
    ) {
    }

    public function create(): Spreadsheet
    {
        $phpExcelObject = new Spreadsheet();

        $phpExcelObject->getProperties()->setCreator('intranet')
            ->setTitle('Liste des candidatures')
            ->setSubject('Office 2005 XLSX Test Document')
            ->setDescription('Test document for Office 2005 XLSX, generated using PHP classes.')
            ->setKeywords('office 2005 openxml php')
            ->setCategory('Test result file');

        $defunts = $this->createXlsObject($phpExcelObject);

        return $this->defuntsXlsObject($phpExcelObject, $defunts);

    }

    /**
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
     * @param Defunt[] $defunts
     *
     * @return mixed
     *
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

    public function createDefunts(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $active = $spreadsheet->getActiveSheet();

        $column = 'A';
        $line = 1;
        $active
            ->setCellValue($column++.$line, 'Id')
            ->setCellValue($column++.$line, 'Nom')
            ->setCellValue($column++.$line, 'Prenom')
            ->setCellValue($column++.$line, 'Lieu naissance')
            ->setCellValue($column++.$line, 'Date naissance')
            ->setCellValue($column++.$line, 'Lieu Deces')
            ->setCellValue($column++.$line, 'Date Deces')
            ->setCellValue($column++.$line, 'Lieu inhumation')
            ->setCellValue($column++.$line, 'Date inhumation');


        $line = 2;
        foreach ($this->defuntRepository->findAll() as $defunt) {
            $column = 'A';
            $sepulture = $defunt->getSepulture();
            $active
                ->setCellValue($column++.$line, $defunt->getId())
                ->setCellValue($column++.$line, $defunt->getNom())
                ->setCellValue($column++.$line, $defunt->getPrenom())
                ->setCellValue($column++.$line, $defunt->getLieuNaissance())
                ->setCellValue($column++.$line, $defunt->getBirthday())
                ->setCellValue($column++.$line, $defunt->getLieuDeces())
                ->setCellValue($column++.$line, $defunt->getDateDeces())
                ->setCellValue($column++.$line, $sepulture?->getCimetiere()?->getNom())
                ->setCellValue($column++.$line, 'no');
            $line++;
        }

        return $spreadsheet;
    }
}
