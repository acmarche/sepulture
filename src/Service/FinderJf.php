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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class FinderJf
{
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    function find_all_files(string $path, string $slugname): array
    {
        $files = [];
        $finder = new Finder();
        $finder->files()->in($path);
        $finder->sortByName();
        $i = 0;

        $url = DIRECTORY_SEPARATOR.'export'.DIRECTORY_SEPARATOR.$slugname;

        foreach ($finder as $file) {
            $fileName = $file->getRelativePathname();
            $files[$i]['name'] = $fileName;
            $files[$i]['pathname'] = $file->getPathname();
            $files[$i]['url'] = $url.DIRECTORY_SEPARATOR.$fileName;
            $files[$i]['size'] = $file->getSize() / 1000;
            $i++;
        }

        return $files;
    }

    public function findDirectories(string $path): Finder
    {
        $finder = new Finder();

        return $directories = $finder->directories()->in($path);
    }

    public function cleanFolder(Cimetiere $cimetiere): void
    {
        $filesystem = new Filesystem();
        $path = $this->getOuputPath($cimetiere);
        $finder = new Finder();
        if ($filesystem->exists($path)) {
            $finder->files()->in($path);
            foreach ($finder as $file) {
                $filesystem->remove($file);
            }
        }
    }

    public function getExportDirectory(): string
    {
        return $this->parameterBag->get(
                'kernel.project_dir'
            ).DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'export';
    }

    public function getOuputPath(Cimetiere $cimetiere): string
    {
        return $this->getExportDirectory().DIRECTORY_SEPARATOR.$cimetiere->getSlug().DIRECTORY_SEPARATOR;
    }

    public function getCacheDirectory(): string {
         return $this->parameterBag->get(
                'kernel.project_dir'
            ).DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR;
    }

}