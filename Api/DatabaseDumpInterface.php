<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Api;

/**
 * Interface DatabaseDumpInterface
 * @package Magefan\DSUServer\Api
 */
interface DatabaseDumpInterface extends TablesDumpInterface
{
    /**
     * Return dump of all tables from server.
     *
     * @api
     * @return string[] The sql dump of all tables.
     */
    public function get();
}
