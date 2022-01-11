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

use AcMarche\Sepulture\Entity\Cimetiere;
use ZipArchive;

class ZipFactory
{
    public function __construct(
        private FinderJf $finderJf
    ) {
    }

    public function create(Cimetiere $cimetiere): ZipArchive
    {
        $fullpath = $this->finderJf->getOuputPath($cimetiere);
        $files = $this->finderJf->find_all_files($fullpath, $cimetiere->getSlug());

        $zip = new ZipArchive();
        $zipName = $this->finderJf->getCacheDirectory().$cimetiere->getSlug().'.zip';

        $zip->open($zipName, ZipArchive::CREATE);

        foreach ($files as $file) {
            $zip->addFile($file['pathname']);
        }

        return $zip;
    }
}
