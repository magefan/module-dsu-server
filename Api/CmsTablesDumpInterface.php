<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Api;

/**
 * Interface CmsTablesDumpInterface
 * @package Magefan\DSUServer\Api
 */
interface CmsTablesDumpInterface extends TablesDumpInterface
{
    /**
     * Return dump of CMS page and block tables from server.
     *
     * @api
     * @return string[] The sql dump of CMS page and block tables .
     */
    public function get();
}
