<?php

namespace Divvy\Newsletter\Block;

use Magento\Framework\View\Element\Template;
use Divvy\Newsletter\Model\ResourceModel\Check\Collection;
use Divvy\Newsletter\Model\ResourceModel\Check\CollectionFactory;

class CheckBlock extends Template
{
    private $collectionFactory;
    protected $_coreRegistry;

    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context,$data);
    }

    public function getItems()
    {
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $customerSession = $objectManager->get('Magento\Framework\App\Http\Context');
        // $isLoggedIn = $customerSession->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);

        // if($isLoggedIn)
        // {
        //     $string = $customerSession->getValue()->getId();
        //     return $string;
        //     // Logged In
        // }
        // else
        // {
        //     return 'not logged in';
        //     // Not Logged In
        // }
        $status = $this->_coreRegistry->registry('status');
        return $status;
            
        
 
           


        // $i = 0;
        // $items = $this->collectionFactory->create()->getItems(); 
        // foreach($items as $item)
        // {
        //     $list[$i] = $item->getsubscriber_status();
        //     $i++;
        // }
        // return $list;
    }

}