<?php

namespace Divvy\Newsletter\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Check extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('newsletter_subscriber','subscriber_id'); 
    }
}
