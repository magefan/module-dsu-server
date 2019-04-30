<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Api;

/**
 * Interface ConfigurationTablesDumpInterface
 * @package Magefan\DSUServer\Api
 */
interface ConfigurationTablesDumpInterface extends TablesDumpInterface
{
    /**
     * Return dump of core_config_data table from server.
     *
     * @api
     * @return string[] The sql dump of core_config_data table.
     */
    public function get();
}
