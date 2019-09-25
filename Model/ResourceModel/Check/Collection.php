<?php

namespace Divvy\Newsletter\Model\ResourceModel\Check;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Divvy\Newsletter\Model\Check;
use Divvy\Newsletter\Model\ResourceModel\Check as CheckResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'subscriber_id';

    protected function _construct()
    {
        $this->_init(Check::class, CheckResource::class);
    }
}