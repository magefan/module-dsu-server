<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Ui\Component\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package Magefan\DSUServer\Ui\Component\Source
 */
class Status implements OptionSourceInterface
{
    /**
     * Available visibility values
     */
    const STATUS_NEW   = 1;
    const STATUS_APPROVED = 2;
    const STATUS_DECLINED   = 3;

    /**
     * Get available Visibility options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('New'),
                'value' => self::STATUS_NEW,
            ],
            [
                'label' => __('Approved'),
                'value' => self::STATUS_APPROVED,
            ],
            [
                'label' => __('Declined'),
                'value' => self::STATUS_DECLINED,
            ],
        ];

        return $options;
    }
}
