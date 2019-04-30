<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Controller\Adminhtml\DsuServer;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magefan\DSUServer\Model\RequestFactory;
use Magefan\DSUServer\Api\RequestRepositoryInterface;

/**
 * Class Save
 * @package Magefan\DSUServer\Controller\Adminhtml\DsuServer
 */
class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magefan_DSUServer::development_store_update';

    /**
     * @var RequestFactory
     */
    private $requestFactory;
    /**
     * @var RequestRepositoryInterface
     */
    private $repository;

    /**
     * Save constructor.
     * @param Context $context
     * @param RequestFactory $requestFactory
     * @param RequestRepositoryInterface $repository
     */
    public function __construct(
        Context $context,
        RequestFactory $requestFactory,
        RequestRepositoryInterface $repository
    ) {
        $this->requestFactory = $requestFactory;
        $this->repository = $repository;
        parent::__construct($context);
    }
    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $request        = $this->getRequest();
        $data           = $request->getPostValue();

        if ($data) {
            $id = $request->getParam('id');

            if (empty($data['id'])) {
                $data['id'] = null;
            }

            if ($id) {
                try {
                    $item = $this->repository->getById($id);
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    return $resultRedirect->setPath('*/*/', ['id' => $id]);
                }
            } else {
                $item = $this->requestFactory->create();
            }

            $item->setData($data);

            try {
                $this->repository->save($item);
                $this->messageManager->addSuccessMessage(__('Item was saved successfully.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving test item.'));
            }

            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
