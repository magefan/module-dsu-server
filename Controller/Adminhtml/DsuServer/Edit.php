<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Controller\Adminhtml\DsuServer;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magefan\DSUServer\Api\RequestRepositoryInterface;

/**
 * Class Edit
 * @package Magefan\DSUServer\Controller\Adminhtml\DsuServer
 */
class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magefan_DSUServer::development_store_update';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var RequestRepositoryInterface
     */
    private $repository;

    /**
     * Edit constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param RequestRepositoryInterface $repository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        RequestRepositoryInterface $repository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->repository = $repository;
        parent::__construct($context);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magefan_DSUServer::development_store_update');

        if ($id) {
            try {
                $resultPage->getConfig()->getTitle()->prepend($this->repository->getById($id)->getName());
            } catch (\Exception $e) {
                $resultRedirect = $this->resultRedirectFactory->create();
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/');
            }
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Item'));
        }

        return $resultPage;
    }
}
