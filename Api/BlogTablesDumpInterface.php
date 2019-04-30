<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Api;

/**
 * Interface BlogTablesDumpInterface
 * @package Magefan\DSUServer\Api
 */
interface BlogTablesDumpInterface extends TablesDumpInterface
{
    /**
     * Return dump of Magefan Blog tables from server.
     *
     * @api
     * @return string[] The sql dump of Magefan Blog tables.
     */
    public function get();
}
