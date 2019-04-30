<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Controller\Request;

use Magefan\DSUServer\Api\RequestRepositoryInterface;
use Magefan\DSUServer\Ui\Component\Source\Status;

/**
 * Class Manage
 * @package Magefan\DSUServer\Controller\Request
 */
class Manage extends \Magento\Framework\App\Action\Action
{
    /**
     * @var RequestRepositoryInterface
     */
    private $requestRepository;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param RequestRepositoryInterface $requestRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        RequestRepositoryInterface $requestRepository
    ) {
        $this->requestRepository = $requestRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $adminSecret = $this->getRequest()->getParam('admin_secret');
        $id = (int)$this->getRequest()->getParam('id');
        $status = (int)$this->getRequest()->getParam('status');

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($adminSecret && $id && $status) {
            $model = $this->requestRepository->getById($id);
            if ($model->getAdminSecret() == $adminSecret) {
                if (Status::STATUS_NEW == $model->getStatus()) {
                    if (Status::STATUS_APPROVED == $status) {
                        $model->setStatus(Status::STATUS_APPROVED);
                        $this->requestRepository->save($model);
                        $this->messageManager->addSuccessMessage(__('DSU Request has been approved.'));
                    } elseif (Status::STATUS_DECLINED == $status) {
                        $model->setStatus(Status::STATUS_DECLINED);
                        $this->requestRepository->save($model);
                        $this->messageManager->addSuccessMessage(__('DSU Request has been declined.'));
                    }
                } else {
                    $this->messageManager->addErrorMessage(__('This request has been already processed.'));
                }
            } else {
                $this->messageManager->addErrorMessage(__('You are not allowed to manage this request.'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('Some required parameters are missing.'));
        }

        return $resultRedirect->setPath('/');
    }
}
