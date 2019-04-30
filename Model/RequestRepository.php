<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Model;

use Magefan\DSUServer\Model\RequestFactory;
use Magefan\DSUServer\Api\RequestRepositoryInterface;
use Magefan\DSUServer\Model\ResourceModel\Request as RequestResourceModel;
use Magefan\DSUServer\Model\ResourceModel\Request\CollectionFactory;
use Magento\Framework\Api\SearchResultsFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Adapter\ConnectionException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

/**
 * Class RequestRepository
 * @package Magefan\DSUServer\Model
 */
class RequestRepository implements RequestRepositoryInterface
{
    /**
     * @var \Magefan\DSUServer\Model\RequestFactory
     */
    private $requestFactory;
    /**
     * @var RequestResourceModel
     */
    private $requestResourceModel;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var SearchResultsFactory
     */
    private $searchResultsFactory;

    /**
     * RequestRepository constructor.
     * @param RequestFactory $requestFactory
     * @param RequestResourceModel $requestResourceModel
     * @param CollectionFactory $collectionFactory
     * @param SearchResultsFactory $searchResultsFactory
     */
    public function __construct(
        RequestFactory $requestFactory,
        RequestResourceModel $requestResourceModel,
        CollectionFactory $collectionFactory,
        SearchResultsFactory $searchResultsFactory
    ) {
        $this->requestFactory = $requestFactory;
        $this->requestResourceModel = $requestResourceModel;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @param Request $request
     * @return bool|mixed
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\TemporaryState\CouldNotSaveException
     */
    public function save(Request $request)
    {
        if ($request) {
            try {
                $this->requestResourceModel->save($request);
            } catch (ConnectionException $exception) {
                throw new \Magento\Framework\Exception\TemporaryState\CouldNotSaveException(
                    __('Database connection error'),
                    $exception,
                    $exception->getCode()
                );
            } catch (CouldNotSaveException $e) {
                throw new CouldNotSaveException(__('Unable to save item'), $e);
            } catch (ValidatorException $e) {
                throw new CouldNotSaveException(__($e->getMessage()));
            }
            return $this->getById($request->getId());
        }
        return false;
    }

    /**
     * @param $requestId
     * @param bool $editMode
     * @param null $storeId
     * @param bool $forceReload
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($requestId, $editMode = false, $storeId = null, $forceReload = false)
    {
        $request = $this->requestFactory->create();
        $this->requestResourceModel->load($request, $requestId);
        if (!$request->getId()) {
            throw new NoSuchEntityException(__('Requested item doesn\'t exist'));
        }
        return $request;
    }

    /**
     * @param Request $request
     * @return bool
     * @throws CouldNotDeleteException
     * @throws StateException
     */
    public function delete(Request $request)
    {
        try {
            $this->requestResourceModel->delete($request);
        } catch (ValidatorException $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove item')
            );
        }
        return true;
    }

    /**
     * @param int $requestId
     * @return mixed
     * @throws CouldNotDeleteException
     * @throws StateException
     * @throws NoSuchEntityException
     */
    public function deleteById($requestId)
    {
        return $this->delete($this->getById($requestId));
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magefan\DSUServer\Model\ResourceModel\Request\Collection $collection */
        $collection = $this->collectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        /** @var \Magento\Framework\Api\searchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setItems($collection->getData());

        return $searchResult;
    }
}
