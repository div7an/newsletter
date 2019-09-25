<?php

namespace Divvy\Newsletter\Model;

use Magento\Framework\Model\AbstractModel;

class Check extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Divvy\Newsletter\Model\ResourceModel\Check::class);
    }
}