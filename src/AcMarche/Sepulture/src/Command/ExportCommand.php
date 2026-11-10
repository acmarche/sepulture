<?php

namespace AcMarche\Sepulture\Command;

use AcMarche\Sepulture\Repository\DefuntRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'sepulture:export',
    description: 'Add a short description for your command',
)]
class ExportCommand extends Command
{
    public function __construct(
        private DefuntRepository $defuntRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    //Nom, Prénoms, Lieu et Date d'inhumation, Lieu et Date de Naissance, Lieu et Date de décès
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $spreadsheet = new Spreadsheet();
        $active = $this->worksheet = $spreadsheet->getActiveSheet();

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

        $column = 'A';
        $line = 2;
        foreach ($this->defuntRepository->findAll() as $defunt) {
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

        return Command::SUCCESS;
    }
}
