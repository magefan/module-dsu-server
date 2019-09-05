<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Model;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

/**
 * Class Config
 * @package Magefan\DSUServer\Model
 */
class Config
{
    /**
     * System config constants
     */
    const NEW_REQUEST_LIFETIME            = 'dsuserver/update_requests/new_request_lifetime';
    const APPROVED_REQUEST_LIFETIME       = 'dsuserver/update_requests/approved_request_lifetime';
    const DECLINED_REQUEST_LIFETIME       = 'dsuserver/update_requests/declined_request_lifetime';
    const NEW_SEND_TO                     = 'dsuserver/request_email/new_send_to';
    const SENDER                          = 'dsuserver/request_email/sender';
    const ENABLED                         = 'dsuserver/general/enabled';
    const ALLOWED_IPS                     = 'dsuserver/general/allowed_ips';
    const ALLOWED_EMAIL                   = 'dsuserver/general/allowed_email';
    const ENABLED_TD                      = 'dsuserver/transform_data/enabledtd';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * Config constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param RemoteAddress $remoteAddress
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        RemoteAddress $remoteAddress
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getNewRequestLifetime()
    {
        return (int)$this->scopeConfig->getValue(self::NEW_REQUEST_LIFETIME, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getApprovedRequestLifetime()
    {
        return (int)$this->scopeConfig->getValue(self::APPROVED_REQUEST_LIFETIME, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getDeclinedRequestLifetime()
    {
        return (int)$this->scopeConfig->getValue(self::DECLINED_REQUEST_LIFETIME, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getNewSendTo()
    {
        return $this->scopeConfig->getValue(self::NEW_SEND_TO, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getSenderIdentity()
    {
        return $this->scopeConfig->getValue(self::SENDER, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getSenderName()
    {
        return $this->scopeConfig->getValue('trans_email/ident_' . $this->getSenderIdentity() . '/name', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getSenderEmail()
    {
        return $this->scopeConfig->getValue('trans_email/ident_' . $this->getSenderIdentity() . '/email', ScopeInterface::SCOPE_STORE);
    }
    /**
     * @return mixed
     */
    public function getExtensionEnable()
    {
        return $this->scopeConfig->getValue(self::ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isAllowedIPs()
    {
        $allowedIps = $this->scopeConfig->getValue(
            self::ALLOWED_IPS,
            ScopeInterface::SCOPE_STORE
        );
        $allowedIps = explode(',', $allowedIps);

        foreach ($allowedIps as $k => $v) {
            $v = trim($v);
            if (!$v) {
                unset($allowedIps[$k]);
            } else {
                $allowedIps[$k] = $v;
            }
        }

        $ip = $this->remoteAddress->getRemoteAddress();

        return $ip && in_array($ip, $allowedIps);
    }

    /**
     * @return string
     */
    public function getRemoteAddress()
    {
        return $this->remoteAddress->getRemoteAddress();
    }

    /**
     * @param string $email
     * @return bool
     */
    public function isAllowedEmail($email)
    {
        $allowedEmail = $this->scopeConfig->getValue(
            self::ALLOWED_EMAIL,
            ScopeInterface::SCOPE_STORE
        );
        $allowedEmail = explode(',', $allowedEmail);
        foreach ($allowedEmail as $k => $v) {
            $v = trim($v);
            if (!$v) {
                unset($allowedEmail[$k]);
            } else {
                $allowedEmail[$k] = $v;
            }
        }

        return $email && in_array($email, $allowedEmail);
    }
    /**
     * @return mixed
     */
    public function isTransformEnable()
    {
        return $this->scopeConfig->getValue(self::ENABLED_TD, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $templateType
     * @return bool
     */
    public function isRequestEmailEnable($templateType)
    {
        return $this->scopeConfig->getValue('dsuserver/request_email/' . $templateType . '_enabled');
    }
}
