<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Plugin;

use Magefan\DSUServer\Ui\Component\Source\Status;

/**
 * Class RequestPlugin
 * @package Magefan\DSUServer\Plugin
 */
class RequestPlugin
{
    /**
     * @var \Magefan\DSUServer\Model\Request\Mail
     */
    protected $mail;

    /**
     * RequestPlugin constructor.
     * @param \Magefan\DSUServer\Model\Request\Mail $mail
     */
    public function __construct(
        \Magefan\DSUServer\Model\Request\Mail $mail
    ) {
        $this->mail = $mail;
    }

    /**
     * @param \Magefan\DSUServer\Model\ResourceModel\Request $subject
     * @param $result
     * @param $mail
     * @return mixed
     * @throws \Exception
     */
    public function afterSave(\Magefan\DSUServer\Model\ResourceModel\Request $subject, $result, $mail)
    {
        if ($mail->getStatus() == Status::STATUS_NEW) {
            $templateType = 'new';
        } elseif ($mail->getStatus() == Status::STATUS_APPROVED) {
            $templateType = 'approved';
        } elseif ($mail->getStatus() == Status::STATUS_DECLINED) {
            $templateType = 'declined';
        }

        $this->mail->send($mail, $templateType);
        return $result;
    }
}
