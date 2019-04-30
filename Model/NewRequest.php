<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Model;

use Magefan\DSUServer\Api\RequestRepositoryInterface;
use Magefan\DSUServer\Ui\Component\Source\Status;
use Magento\Framework\Exception\LocalizedException;
use Magefan\DSUServer\Model\Authorise;

/**
 * Class NewRequest
 * @package Magefan\DSUServer\Model
 */
class NewRequest implements \Magefan\DSUServer\Api\NewRequestInterface
{
    /**
     * @var \Magefan\DSUServer\Model\RequestFactory
     */
    private $requestFactory;
    /**
     * @var RequestRepositoryInterface
     */
    private $requestRepository;
    /**
     * @var \Magefan\DSUServer\Model\Config
     */
    private $config;

    private $authorise;

    /**
     * NewRequest constructor.
     * @param RequestFactory $requestFactory
     * @param RequestRepositoryInterface $requestRepository
     * @param Config $config
     * @param \Magefan\DSUServer\Model\Authorise $authorise
     */
    public function __construct(
        RequestFactory $requestFactory,
        RequestRepositoryInterface $requestRepository,
        \Magefan\DSUServer\Model\Config $config,
        Authorise $authorise
    ) {
        $this->requestFactory = $requestFactory;
        $this->requestRepository = $requestRepository;
        $this->config = $config;
        $this->authorise = $authorise;
    }

    /**
     * @param string $name
     * @param string $email
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool|mixed
     */
    public function create($name, $email)
    {
        $this->authorise->checkAuthorization($email);

        $model = $this->requestFactory->create();
        try {
            $model
                ->setName($name)
                ->setEmail($email)
                ->setStatus(Status::STATUS_NEW);
            $this->requestRepository->save($model);
        } catch (\Exception $e) {
            throw new LocalizedException(__('Something went wrong while saving the request.'));
        }

        return true;
    }
}
