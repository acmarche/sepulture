<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 19/09/16
 * Time: 15:09.
 */

namespace AcMarche\Sepulture\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileHelper
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function uploadFile($directory, UploadedFile $file, $fileName)
    {
        return $file->move($directory, $fileName);
    }

    public function deleteOneDoc($directory, $filename)
    {
        $file = $directory.DIRECTORY_SEPARATOR.$filename;

        $fs = new Filesystem();
        $fs->remove($file);
    }

    public function deleteAllDocs($directory)
    {
        $fs = new Filesystem();
        $fs->remove($directory);
    }

    public function getImages($idsepulture, $max = 60)
    {
        $separator = DIRECTORY_SEPARATOR;
        $finder = new Finder();
        $files = [];
        $directory = $this->parameterBag->get('acmarche_sepulture_upload_sepulture_directory').$separator.$idsepulture.$separator;

        if (is_dir($directory)) {
            $finder->files()->in($directory);
            $root = $this->parameterBag->get('acmarche_sepulture_download_sepulture_directory').$separator.$idsepulture.$separator;
            $i = 1;

            foreach ($finder as $file) {
                $f = [];

                $name = $file->getFilename();
                $url = $root.$name;
                $size = $file->getSize();

                $f['size'] = $size;
                $f['name'] = $name;
                $f['url'] = $url;
                $f['i'] = $i; //pour id zoom
                ++$i;

                $files[] = $f;
                if ($i > $max) {
                    break;
                }
            }
        }

        return $files;
    }
}
