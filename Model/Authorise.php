<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Model;

use \Magento\Framework\App\RequestInterface;
use Magefan\DSUServer\Ui\Component\Source\Status;
use Magefan\DSUServer\Api\RequestRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magefan\DSUServer\Model\Config;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class Authorise
 * @package Magefan\DSUServer\Model
 */
class Authorise
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
    /**
     * @var RequestRepositoryInterface
     */
    private $requestRepository;
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var TimezoneInterface
     */
    protected $date;
    /**
     * @var Json
     */
    protected $json;

    /**
     * Authorise constructor.
     * @param RequestInterface $request
     * @param RequestRepositoryInterface $requestRepository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magefan\DSUServer\Model\Config $config
     * @param TimezoneInterface $date
     * @param Json $json
     */
    public function __construct(
        RequestInterface $request,
        RequestRepositoryInterface $requestRepository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Config $config,
        TimezoneInterface $date,
        Json $json
    ) {
        $this->request = $request;
        $this->requestRepository = $requestRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->config = $config;
        $this->date = $date;
        $this->json = $json;
    }

    /**
     * Return valid data
     * @return bool
     * @throws LocalizedException
     */
    public function isValid()
    {
        $data = $this->json->unserialize($this->request->getContent());

        foreach (['secret', 'email'] as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }

        $this->checkAuthorization($data['email']);

        $dateValue = $this->date->date()->modify("- {$this->config->getApprovedRequestLifetime()} hour")
            ->format('Y-m-d H:i:s');

        $filter1 = $this->filterBuilder
            ->setField('secret')
            ->setValue($data['secret'])
            ->create();
        $filter2 = $this->filterBuilder
            ->setField('email')
            ->setValue($data['email'])
            ->create();
        $filter3 = $this->filterBuilder
            ->setField('status')
            ->setValue(Status::STATUS_APPROVED)
            ->create();
        $filter4 = $this->filterBuilder
            ->setField('created_at')
            ->setValue($dateValue)
            ->setConditionType('gteq')
            ->create();
        $searchCriteria = $this->searchCriteriaBuilder->addFilters([$filter1, $filter2, $filter3, $filter4])->create();
        $items = $this->requestRepository->getList($searchCriteria);

        return count($items->getItems()) >=1;
    }

    /**
     * @param $email
     * @throws LocalizedException
     * @return bool
     */
    public function checkAuthorization($email)
    {
        if (!$this->config->getExtensionEnable()) {
            throw new LocalizedException(__('The DSU Server is disabled.'));
        }
        if (!$this->config->isAllowedIPs()) {
            throw new LocalizedException(__('Your IP ' . $this->config->getRemoteAddress() . ' is not whitelisted on the DSU Server.'));
        }
        if (!$this->config->isAllowedEmail($email)) {
            throw new LocalizedException(__('Your Email is not whitelisted on the DSU Server.'));
        }
    }
}
