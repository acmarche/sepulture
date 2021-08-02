<?php

namespace AcMarche\Sepulture\Twig\Extension;

use AcMarche\Sepulture\Entity\Sepulture;
use AcMarche\Sepulture\Service\FileHelper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FileDownload extends AbstractExtension
{
    private ParameterBagInterface $parameterBag;
    private FileHelper $fileHelper;

    public function __construct(FileHelper $fileHelper, ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
        $this->fileHelper = $fileHelper;
    }

    /**
     * @Override
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('acmarche_sepulture_download_sepulture', fn(Sepulture $sepulture, $fileName) => $this->downloadSepulture($sepulture, $fileName)),
            new TwigFilter('acmarche_sepulture_getimage', fn(string $idsepulture) => $this->getImage($idsepulture)),
            new TwigFilter('acmarche_sepulture_download_cimetiere', fn($fileName) => $this->downloadCimetiere($fileName)),
        ];
    }

    public function downloadSepulture(Sepulture $sepulture, $fileName): string
    {
        $directory = $this->parameterBag->get(
                'acmarche_sepulture_download_sepulture_directory'
            ).DIRECTORY_SEPARATOR.$sepulture->getId();

        return $directory.DIRECTORY_SEPARATOR.$fileName;
    }

    public function getImage(string $idsepulture)
    {
        $file = $this->fileHelper->getImages($idsepulture, 1);
        if (count($file) > 0) {
            return $file[0];
        }

        return false;
    }

    public function downloadCimetiere($fileName): string
    {
        $directory = $this->parameterBag->get('acmarche_sepulture_download_cimetiere_directory');

        return $directory.DIRECTORY_SEPARATOR.$fileName;
    }
}
