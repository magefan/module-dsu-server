<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUServer\Model\Dump;

use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magefan\DSUServer\Api\DatabaseDumpInterface;
use Magefan\DSUServer\Model\Config;
use Magefan\DSUServer\Model\Authorise;
use Magento\Framework\Exception\LocalizedException;
use Magefan\DSUServer\Model\ResourceModel\Request;
use Magento\Framework\Event\ManagerInterface;
use Magefan\DSUServer\Model\Dump\Database\Mysqldump;

/**
 * Class Database
 * @package Magefan\DSUServer\Model\Dump
 */
class Database implements DatabaseDumpInterface
{
    /**
     * Available customer values
     */
    const FIRST_NAME  = 'Jon';
    const LAST_NAME   = 'Dou';
    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    protected $deploymentConfig;

    /**
     * @var array
     */
    public $tables = [];

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var Authorise
     */
    protected $authorise;
    /**
     * @var Request
     */
    protected $resource;
    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * Database constructor.
     * @param \Magento\Framework\App\DeploymentConfig $deploymentConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param Config $config
     * @param Authorise $authorise
     * @param Request $resource
     * @param ManagerInterface $eventManager
     * @param string|null $tables
     */
    public function __construct(
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Magento\Framework\Filesystem $filesystem,
        Config $config,
        Authorise $authorise,
        Request $resource,
        ManagerInterface $eventManager,
        string $tables = null
    ) {
        $this->deploymentConfig = $deploymentConfig;
        $this->filesystem = $filesystem;
        $this->config = $config;
        $this->authorise = $authorise;
        $this->resource = $resource;
        $this->eventManager = $eventManager;
        if ($tables) {
            $this->tables = explode(',', $tables);
        }
    }

    /**
     * Return dump of Magefan Blog tables from server.
     *
     * @api
     * @return string[] The sql dump of Magefan Blog tables.
     */
    public function get()
    {
        if ($this->authorise->isValid()) {
            $varDirectoryPath = $this->getVarDirectoryPath();
            $dumpFile = $varDirectoryPath . 'backup/dump.sql.gz';

            $this->createDatabaseDump($dumpFile, $this->tables);
            readfile($dumpFile);
            unlink($dumpFile);
        } else {
            throw new LocalizedException(__('Invalid email or secret.'));
        }
    }

    /**
     * @return array
     */
    protected function getDatabaseConnectionSettings()
    {
        $settings = [];
        $settings['database'] = $this->deploymentConfig->get(
            ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT
            . '/' . ConfigOptionsListConstants::KEY_NAME
        );
        $settings['user'] = $this->deploymentConfig->get(
            ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT
            . '/' . ConfigOptionsListConstants::KEY_USER
        );
        $settings['password'] = $this->deploymentConfig->get(
            ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT
            . '/' . ConfigOptionsListConstants::KEY_PASSWORD
        );
        $settings['host'] = $this->deploymentConfig->get(
            ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT
            . '/' . ConfigOptionsListConstants::KEY_HOST
        );
        return $settings;
    }

    /**
     * @return array
     */
    protected function getTalesWithPrefix($tables)
    {
        foreach ($tables as $key => $table) {
            $tables[$key] = $this->resource->getTable($table);
        }
        return $tables;
    }

    /**
     * @return string
     */
    protected function getVarDirectoryPath()
    {
        return $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath();
    }

    /**
     * @param $dumpFile
     * @param array $tables
     */
    public function createDatabaseDump($dumpFile, $tables = [])
    {
        $tables = $this->getTalesWithPrefix($tables);

        $settings = $this->getDatabaseConnectionSettings();
        $database = $settings['database'];
        $user = $settings['user'];
        $password = $settings['password'];
        $host = $settings['host'];

        $updateDatabaseConfiguration = $this->config->getValue('dsuserver/exclude_data');

        $allTables = [
            'customers' => ['customer_entity', 'customer_address_entity', 'customer_address_entity_datetime', 'customer_address_entity_decimal', 'customer_address_entity_int', 'customer_address_entity_text', 'customer_address_entity_varchar', 'ustomer_entity', 'customer_entity_datetime', 'customer_entity_decimal', 'customer_entity_int', 'customer_entity_text', 'customer_entity_varchar', 'customer_grid_flat', 'customer_log'],
            'subscribers' => ['newsletter_subscriber'],
            'sales' => ['quote', 'quote_address', 'quote_address_item', 'quote_id_mask', 'quote_item', 'quote_item_option', 'quote_payment', 'quote_shipping_rate', 'sales_bestsellers_aggregated_daily', 'sales_bestsellers_aggregated_monthly', 'sales_bestsellers_aggregated_yearly', 'sales_creditmemo', 'sales_creditmemo_comment', 'sales_creditmemo_grid', 'sales_creditmemo_item', 'sales_invoice', 'sales_invoice_comment', 'sales_invoiced_aggregated', 'sales_invoiced_aggregated_order', 'sales_invoice_grid', 'sales_invoice_item', 'sales_order', 'sales_order_address', 'sales_order_aggregated_created', 'sales_order_aggregated_updated', 'sales_order_grid', 'sales_order_item', 'sales_order_payment', 'sales_order_status', 'sales_order_status_history', 'ales_order_status_label', 'ales_order_status_state', 'sales_order_tax', 'sales_order_tax_item', 'sales_payment_transaction', 'sales_refunded_aggregated', 'sales_refunded_aggregated_order'],
            'logs' => ['report_event', 'report_viewed_product_index', 'report_compared_product_index', 'customer_visitor'],
        ];

        $confTables = [];
        foreach ($allTables as $type => $tbs) {
            if (!empty($updateDatabaseConfiguration[$type])) {
                $confTables = array_merge($confTables, $tbs);
            }
        }

        $customTables = [];
        if ($updateDatabaseConfiguration['custom']) {
            $customTables = explode(',', $updateDatabaseConfiguration['custom']);
            foreach ($customTables as $k => $v) {
                $v = trim($v);
                if (!$v) {
                    unset($customTables[$k]);
                } else {
                    $customTables[$k] = $v;
                }
            }
        }
        $confTables = array_merge($confTables, $customTables);

        $confTables = $this->getTalesWithPrefix($confTables);

        if (!file_exists(dirname($dumpFile))) {
            mkdir(dirname($dumpFile), 0775, true);
        }

        try {
            if ($confTables) {
                $dump = new Mysqldump($s = 'mysql:host=' . $host . ';dbname=' . $database, $user, $password, $dumpSettings = ['include-tables' => $tables, 'no-data' => $confTables, 'add-drop-table' => true, 'compress' => Mysqldump::GZIP]);
            } else {
                $dump = new Mysqldump($s = 'mysql:host=' . $host . ';dbname=' . $database, $user, $password, $dumpSettings = ['include-tables' => $tables, 'add-drop-table' => true, 'compress' => Mysqldump::GZIP]);
            }
            if ($this->config->isTransformEnable()) {
                $this->eventManager->dispatch('dsu_tables_dump_before_transform', ['dump' => $dump]);

                $dump->setTransformColumnValueHook(function ($tableName, $colName, $colValue, $row) {

                    if (false !== strpos($colValue, '@')) {
                        if (false === strpos($tableName, 'core_config_data')) {
                            if (filter_var($colValue, FILTER_VALIDATE_EMAIL)) { //is email address
                                $colValue = substr(hash('sha256', $colValue), 0, 100) . '@example.com';
                                return $colValue;
                            }
                        }
                    }

                    if (in_array($colName, ['firstname', 'customer_firstname'])) {
                        return self::FIRST_NAME;
                    }

                    if (in_array($colName, ['lastname', 'customer_lastname'])) {
                        return self::LAST_NAME;
                    }

                    return $colValue;
                });

                $this->eventManager->dispatch('dsu_tables_dump_after_transform', ['dump' => $dump]);
            }

            $dump->start($dumpFile);
        } catch (\Exception $e) {
            throw new LocalizedException(__('mysqldump-php error: %1', $e->getMessage()));
        }
    }
}
