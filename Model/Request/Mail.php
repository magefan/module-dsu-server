<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Model\Request;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magefan\DSUServer\Ui\Component\Source\Status;
use Magento\Framework\App\Area;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Mail
 * @package Magefan\DSUServer\Model\Request
 */
class Mail
{
    /**
     * Available visibility values
     */
    const STATUS_NEW       = 'new';
    const STATUS_APPROVED  = 'approved';
    const STATUS_DECLINED  = 'declined';
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magefan\DSUServer\Model\Config
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $store;

    /**
     * Mail constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magefan\DSUServer\Model\Config $config
     * @param StoreManagerInterface $store
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magefan\DSUServer\Model\Config $config,
        StoreManagerInterface $store
    ) {
        $this->logger = $logger;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->config = $config;
        $this->store = $store;
    }

    /**
     * @param $request
     * @param $templateType
     * @return bool
     * @throws \Exception
     */
    public function send($request, $templateType)
    {
        if (!in_array($templateType, [self::STATUS_NEW, self::STATUS_APPROVED, self::STATUS_DECLINED])) {
            throw new \Exception(__('Invalid DSU email template type.'));
        }

        if (!$this->config->isRequestEmailEnable($templateType)) {
            return false;
        }

        $this->inlineTranslation->suspend();
        try {
            $copyTo = [];
            $addTo = null;

            if ($request->getStatus() == Status::STATUS_NEW) {
                $newSendTo = $this->config->getNewSendTo();
                $newSendTo = explode(',', $newSendTo);

                $first = true;
                foreach ($newSendTo as $to) {
                    $to = trim($to);
                    if ($to) {
                        if ($first) {
                            $first = false;
                            $addTo = $to;
                        } else {
                            $copyTo[] = $to;
                        }
                    }
                }
            } else {
                $addTo = trim($request->getEmail());
            }

            if (!$addTo) {
                return false;
            }

            $store = $this->store->getStore(Store::DEFAULT_STORE_ID);
            $this->transportBuilder->setTemplateIdentifier('dsuserver_request_email_' . $templateType . '_template')
            ->setTemplateOptions([
                'area' => Area::AREA_FRONTEND,
                'store' => Store::DEFAULT_STORE_ID,
            ])
            ->setTemplateVars(
                [
                    'store' => $store,
                    'request' => $request
                ]
            )
            ->setFrom(
                [
                    'name' => $this->config->getSenderName(),
                    'email' => $this->config->getSenderEmail()
                ]
            )
            ->addTo($addTo);

            if (!empty($copyTo)) {
                foreach ($copyTo as $to) {
                    $this->transportBuilder->addCc($to);
                }
            }

            $this->transportBuilder->getTransport()->sendMessage();

            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->logger->debug('Sending to ' . $request->getEmail() . ' discarded. ' . $e->getMessage());
            return false;
        }

        return true;
    }
}
