<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Model\Dump;

use Magefan\DSUServer\Api\MediaDumpInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magefan\DSUServer\Model\Authorise;
use Magento\Framework\Filesystem;

/**
 * Class Media
 * @package Magefan\DSUServer\Model\Dump
 */
class Media implements MediaDumpInterface
{
    /**
     * @var Authorise
     */
    private $authorise;
    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * Media constructor.
     * @param Authorise $authorise
     * @param Filesystem $filesystem
     */
    public function __construct(
        Authorise $authorise,
        Filesystem $filesystem
    ) {
        $this->authorise = $authorise;
        $this->filesystem = $filesystem;
    }
    /**
     * Return structure of media directory.
     *
     * @api
     * @return string[] Structure of media directory.
     */
    public function get()
    {
        if ($this->authorise->isValid()) {
            $mediaPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
            $media = json_encode($this->listFolderFiles($mediaPath));

            return $media;
        } else {
            throw new LocalizedException(__('Invalid email or secret.'));
        }
    }

    /**
     * @param $dir
     * @return array|void
     */
    protected function listFolderFiles($dir)
    {

        $files = [];
        $ffs = scandir($dir);

        unset($ffs[array_search('.', $ffs, true)]);
        unset($ffs[array_search('..', $ffs, true)]);
        unset($ffs[array_search('cache', $ffs, true)]);
        unset($ffs[array_search('downloadable', $ffs, true)]);

        // prevent empty ordered elements
        if (count($ffs) < 1) {
            return;
        }

        foreach ($ffs as $ff) {
            if (substr($ff, 0, 1) !== ".") {
                if (is_dir($dir . '/' . $ff)) {
                    $files[$ff] = $this->listFolderFiles($dir . '/' . $ff);
                } else {
                    $files[] = $ff;
                }
            }
        }
        return $files;
    }
}
