<?php

namespace Divvy\Newsletter\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;



class Index extends \Magento\Framework\App\Action\Action
{
    protected $_subscriber;
    protected $_coreRegistry;

    public function __construct(
    \Magento\Newsletter\Model\Subscriber $subscriber,
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\Registry $coreRegistry
    ){
    parent::__construct($context);
    $this->_subscriber= $subscriber;
    $this->_coreRegistry = $coreRegistry;
    }

    public function execute()
    {
        // ----------------------------------------------
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        if($customerSession->isLoggedIn()) {
            
            //$customerData = Mage::getSingleton('customer/session')->getCustomer();
            $string = $customerSession->getCustomer()->getId();
            
            //\Magento\Newsletter\Model\Subscriber $subscriber;
            //$this->_subscriber= $subscriber;
            $checkSubscriber = $this->_subscriber->loadByCustomerId($string);

            //$result = $this->resultFactory->create(ResultFactory::TYPE_RAW);

            
            if ($checkSubscriber->isSubscribed()) {
                //$result->setContents('Subscribed');
                // Customer is subscribed
                $this->_coreRegistry->register('status', 1);
            } else {
                //$result->setContents('Not subscribed');
                // Customer is not subscribed
                $this->_coreRegistry->register('status', 0);
            }
            
            
            //return $result;
            
        }
        else
        {
        // $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        // $result->setContents('not logged in');
        // return $result;
            $this->_coreRegistry->register('status', 0);
        }

        // ----------------------------------------------------
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}

