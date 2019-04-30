<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magefan\DSUServer\Model\ResourceModel\Request as ResourceModel;
use Magento\Framework\Url;
use Magefan\DSUServer\Ui\Component\Source\Status;

/**
 * Class Request
 * @package Magefan\DSUServer\Model
 */
class Request extends AbstractModel implements IdentityInterface
{
    /**
     * @var \Magento\Framework\Url
     */
    protected $url;
    /**
     * DSUServer cache tag
     */
    protected $cacheTag = 'dsu_request';
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $eventPrefix = 'dsu_request';

    /**
     * Request constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param Url $url
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        Url $url,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->url = $url;
    }
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Retrieve Approved Url
     *
     * @return string
     */
    public function getApproveUrl()
    {
        return $this->url->getUrl('dsuserver/request/manage', [
            'id' => $this->getId(),
            'status' => Status::STATUS_APPROVED,
            'admin_secret' => $this->getAdminSecret(),
            '_nosid' => true
        ]);
    }
    /**
     * Retrieve Declined Url
     *
     * @return string
     */
    public function getDeclineUrl()
    {
        return $this->url->getUrl('dsuserver/request/manage', [
            'id' => $this->getId(),
            'status' => Status::STATUS_DECLINED,
            'admin_secret' => $this->getAdminSecret(),
            '_nosid' => true
        ]);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return ['dsu_request' . '_' . $this->getId(), 'dsu_request'];
    }
    /**
     * Retrieve id
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData('id');
    }
    /**
     * Retrieve name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData('name');
    }
    /**
     * Retrieve email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getData('email');
    }
    /**
     * Retrieve secret
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->getData('secret');
    }
    /**
     * Retrieve Admin secret
     *
     * @return string
     */
    public function getAdminSecret()
    {
        return $this->getData('admin_secret');
    }
    /**
     * Retrieve created ad
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }
    /**
     * Retrieve updated ad
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData('updated_at');
    }
    /**
     * Retrieve status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return mixed
     */
    public function setId($id)
    {
        return $this->setData('id', $id);
    }

    /**
     * Set Name
     *
     * @param string $name
     * @return mixed
     */
    public function setName($name)
    {
        return $this->setData('name', $name);
    }
    /**
     * Set Email
     *
     * @param string $email
     * @return mixed
     */
    public function setEmail($email)
    {
        return $this->setData('email', $email);
    }
    /**
     * Set Secret
     *
     * @param string $secret
     * @return mixed
     */
    public function setSecret($secret)
    {
        return $this->setData('secret', $secret);
    }
    /**
     * Set Admin Secret
     *
     * @param string $adminSecret
     * @return mixed
     */
    public function setAdminSecret($adminSecret)
    {
        return $this->setData('admin_secret', $adminSecret);
    }
    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData('created_at', $createdAt);
    }
    /**
     * Set update at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData('updated_at', $updatedAt);
    }
    /**
     * Set status
     *
     * @param int $status
     * @return mixed
     */
    public function setStatus($status)
    {
        return $this->setData('status', $status);
    }
}
