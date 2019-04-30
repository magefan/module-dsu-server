<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Api;

/**
 * Interface MediaDumpInterface
 * @package Magefan\DSUServer\Api
 */
interface MediaDumpInterface extends TablesDumpInterface
{
    /**
     * Return structure of media directory.
     *
     * @api
     * @return string[] The structure of media directory.
     */
    public function get();
}
