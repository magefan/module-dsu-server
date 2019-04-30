<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Api;

/**
 * Interface NewRequestInterface
 * @package Magefan\DSUServer\Api
 */
interface NewRequestInterface
{
    /**
     * Return.
     *
     * @param string $name
     * @param string $email
     * @return mixed
     */
    public function create($name, $email);
}
