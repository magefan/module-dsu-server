<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Cron;

use Magefan\DSUServer\Ui\Component\Source\Status;

/**
 * Class ClearRequest
 * @package Magefan\DSUServer\Cron
 */
class ClearRequest
{
    /**
     * @var \Magefan\DSUServer\Model\ResourceModel\Request
     */
    protected $resource;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $date;
    /**
     * @var \Magefan\DSUServer\Model\Config
     */
    protected $config;

    /**
     * ClearRequest constructor.
     * @param \Magefan\DSUServer\Model\ResourceModel\Request $resource
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     * @param \Magefan\DSUServer\Model\Config $config
     */
    public function __construct(
        \Magefan\DSUServer\Model\ResourceModel\Request $resource,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magefan\DSUServer\Model\Config $config
    ) {
        $this->resource = $resource;
        $this->date = $date;
        $this->config = $config;
    }

    /**
     * Execute Cron ClearRequest
     */
    public function execute()
    {
        $table = $this->resource->getTable('magefan_dsu_request');
        $this->resource->getConnection()->delete(
            $table,
            [
                    'created_at < ?' => $this->date->date()
                        ->modify("- {$this->config->getNewRequestLifetime()} hour")->format('Y-m-d H:i:s'),
                    'status = ?' => Status::STATUS_NEW
            ]
        );
        $this->resource->getConnection()->delete(
            $table,
            [
                'created_at < ?' => $this->date->date()
                    ->modify("- {$this->config->getApprovedRequestLifetime()} hour")->format('Y-m-d H:i:s'),
                'status = ?' => Status::STATUS_APPROVED
            ]
        );
        $this->resource->getConnection()->delete(
            $table,
            [
            'created_at < ?' => $this->date->date()
                ->modify("- {$this->config->getDeclinedRequestLifetime()} hour")->format('Y-m-d H:i:s'),
            'status = ?' => Status::STATUS_DECLINED
            ]
        );
    }
}
