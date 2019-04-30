<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Controller\Adminhtml\DsuServer;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magefan\DSUServer\Api\RequestRepositoryInterface;

/**
 * Class Delete
 * @package Magefan\DSUServer\Controller\Adminhtml\DsuServer
 */
class Delete extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magefan_DSUServer::development_store_update';

    /**
     * @var RequestRepositoryInterface
     */
    private $repository;

    /**
     * Delete constructor.
     * @param Context $context
     * @param RequestRepositoryInterface $repository
     */
    public function __construct(
        Context $context,
        RequestRepositoryInterface $repository
    ) {
        parent::__construct($context);
        $this->repository = $repository;
    }
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $this->repository->deleteById($id);

                $this->messageManager->addSuccessMessage(__('You deleted the item.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find an item to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
