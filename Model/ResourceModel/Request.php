<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Math\Random;

/**
 * Class Request
 * @package Magefan\DSUServer\Model\ResourceModel
 */
class Request extends AbstractDb
{
    /**
     * @var Random
     */
    protected $random;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magefan_dsu_request', 'id');
    }

    /**
     * Request constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param Random $random
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        Random $random
    ) {
        parent::__construct($context);
        $this->random = $random;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $request
     * @return $this|AbstractDb
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $request)
    {
        if (!$request->getData('secret')) {
            $request->setData('secret', $this->random->getRandomString(255));
        }

        if (!$request->getAdminSecret()) {
            $request->setAdminSecret($this->random->getRandomString(32));
        }

        return $this;
    }
}
