<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Api;

use Magefan\DSUServer\Model\Request;

/**
 * Interface RequestRepositoryInterface
 * @package Magefan\DSUServer\Api
 */
interface RequestRepositoryInterface
{
    /**
     * save Request model.
     * @param Request $request
     * @return mixed
     */
    public function save(Request $request);

    /**
     * Retrieve Request model.
     * @param $requestId
     * @return mixed
     */
    public function getById($requestId);

    /**
     * Retrieve Request matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface
    $searchCriteria
     * @return \Magento\Framework\Api\SearchResults
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Request model.
     * @param Request $request
     * @return mixed
     */
    public function delete(Request $request);

    /**
     * Delete Request by ID.
     *
     * @param int $requestId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($requestId);
}
