<?php

namespace AcMarche\Sepulture\Twig\Extension;

use AcMarche\Sepulture\Entity\Sepulture;
use AcMarche\Sepulture\Service\FileHelper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FileDownload extends AbstractExtension
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var FileHelper
     */
    private $fileHelper;

    public function __construct(FileHelper $fileHelper, ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
        $this->fileHelper = $fileHelper;
    }

    /**
     * @Override
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            new TwigFilter('acmarche_sepulture_download_sepulture', [$this, 'downloadSepulture']),
            new TwigFilter('acmarche_sepulture_getimage', [$this, 'getimage']),
            new TwigFilter('acmarche_sepulture_download_cimetiere', [$this, 'downloadCimetiere']),
        ];
    }

    public function downloadSepulture(Sepulture $sepulture, $fileName)
    {
        $directory = $this->parameterBag->get(
                'acmarche_sepulture_download_sepulture_directory'
            ).DIRECTORY_SEPARATOR.$sepulture->getId();
        $file = $directory.DIRECTORY_SEPARATOR.$fileName;

        return $file;
    }

    public function getImage(string $idsepulture)
    {
        $file = $this->fileHelper->getImages($idsepulture, 1);
        if (count($file) > 0) {
            return $file[0];
        }

        return false;
    }

    public function downloadCimetiere($fileName)
    {
        $directory = $this->parameterBag->get('acmarche_sepulture_download_cimetiere_directory');
        $file = $directory.DIRECTORY_SEPARATOR.$fileName;

        return $file;
    }
}
