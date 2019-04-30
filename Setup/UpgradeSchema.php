<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class UpgradeSchema
 * @package Magefan\DSUServer\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->createDsuRequest($setup);
        }
    }

    /**
     * @param $setup
     */
    private function createDsuRequest($setup)
    {
        /**
         * Create table 'custom_index'
         */
        $tableName = 'magefan_dsu_request';

        $table = $setup->getConnection()->newTable(
            $setup->getTable($tableName)
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Entity ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
        )->addColumn(
            'email',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Email'
        )->addColumn(
            'secret',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Secret'
        )->addColumn(
            'admin_secret',
            Table::TYPE_TEXT,
            64,
            ['nullable' => false],
            'Admin Secret'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created at'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Updated at'
        )->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Status'
        )->addIndex(
            $setup->getIdxName(
                $setup->getTable($tableName),
                ['secret'],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            ['secret'],
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )->setComment(
            'Index Secret'
        );

        $setup->getConnection()->createTable($table);
    }
}
