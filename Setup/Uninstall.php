<?php

namespace Divvy\Newsletter\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    protected $_resource;
    public function __construct(
    \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
    }

    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $tablename = $connection->getTableName('setup_module');
        $query = "delete from ".$tablename."where module='Divvy_Newsletter'";
        $connection->query($query);
        $setup->endSetup();
    }
}
