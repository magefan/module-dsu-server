<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Model\ResourceModel\Request;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magefan\DSUServer\Model\Request;
use Magefan\DSUServer\Model\ResourceModel\Request as ResourceModel;

/**
 * Class Collection
 * @package Magefan\DSUServer\Model\ResourceModel\Request
 */
class Collection extends AbstractCollection
{
    /**
     *
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Dsu server prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'dsu_request_collection';

    /**
     * Dsu server object
     *
     * @var string
     */
    protected $_eventObject = 'dsu_request_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Request::class, ResourceModel::class);
    }
}
