<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;
use Magefan\DSUServer\Model\ResourceModel\Request;
use Magefan\DSUServer\Model\Request as Model;

/**
 * Class DeleteButton
 * @package Magefan\DSUServer\Block\Adminhtml\Edit
 */
class DeleteButton implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Request
     */
    protected $resource;
    /**
     * @var Model
     */
    protected $model;

    /**
     * DeleteButton constructor.
     * @param Context $context
     * @param Request $resource
     * @param Model $model
     */
    public function __construct(
        Context $context,
        Request $resource,
        Model $model
    ) {
        $this->context = $context;
        $this->resource = $resource;
        $this->model = $model;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($id = $this->getItemId()) {
            $data = [
                'label'      => __('Delete'),
                'class'      => 'delete',
                'on_click'   => 'deleteConfirm(\'' . __(
                    'Are you sure you want to delete this item?'
                ) . '\', \'' . $this->getDeleteUrl($id) . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * Get delete button url
     *
     * @param int $id
     * @return string
     */
    public function getDeleteUrl($id)
    {
        return $this->context->getUrlBuilder()->getUrl('*/*/delete', ['id' => $id]);
    }

    /**
     * Get item ID
     *
     * @return int|null
     */
    protected function getItemId()
    {
        try {
            $this->resource->load($this->model, $this->context->getRequest()->getParam('id'));
            return $this->model->getId();
        } catch (\Exception $e) {
            return null;
        }
    }
}
