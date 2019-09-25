<?php

namespace Divvy\Newsletter\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Update extends \Magento\Framework\App\Action\Action
{
    //Necessary variables to be used in constructor
    protected $_postFactory;
    protected $_httpRequest;
    // protected $_cacheTypeList;
    // protected $_cacheFrontendPool;
    protected $_postResource;
    protected $_subscriber;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Newsletter\Model\Subscriber $subscriber,
        // \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        // \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\App\Request\Http $httpRequest,
        \Divvy\Newsletter\Model\CheckFactory $postFactory,
        \Divvy\Newsletter\Model\ResourceModel\Check $postResource
        )
    {
        $this->_postFactory = $postFactory;
        $this->_subscriber= $subscriber;
        $this->_postResource = $postResource;
        $this->_httpRequest = $httpRequest;
        // $this->_cacheTypeList = $cacheTypeList;
        // $this->_cacheFrontendPool = $cacheFrontendPool;
        return parent::__construct($context);
    }

    public function execute()
    {
        // Getting customer ID
        $customerid;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        if($customerSession->isLoggedIn()) {
            $customerid = $customerSession->getCustomer()->getId();
        }
       

        // Getting subscriber ID (\Magento\Newsletter\Model\Subscriber has been used for this)
        $checkSubscriber = $this->_subscriber->loadByCustomerId($customerid);
        $sid = $checkSubscriber->getID();
        

        // --------------------------------------JUNK-----------------------------------------------------------
        // $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        // $connection = $resource->getConnection();
        // $tableName = $resource->getTableName('newsletter_subscriber'); //gives table name with prefix
        // $sql = "select subscriber_id FROM " . $tableName . " where customer_id=".$customerid;
        // $resul = $connection->fetchAll($sql); // gives associated array, table fields as key in array.
        // //$res = $resul['subscriber_id'];
        // $res;
        // foreach($resul as $key=>$value)
        // {

        //     $res = $value;
        // }
        // --------------------------------------JUNK-----------------------------------------------------------
        
        //Updating subscriber status
        // Used the following classes for it
        // \Magento\Framework\App\Request\Http 
        // \Divvy\Newsletter\Model\CheckFactory 
        // \Divvy\Newsletter\Model\ResourceModel\Check 
        $post = $this->_postFactory->create();
        $postUpdate = $post->load($sid);
        $postUpdate->setSubscriberStatus(3);
        $postUpdate->save();

        // For adding success message
        $this->messageManager->addSuccess(__('You are successfully unsubscribed.'));

        // Declaring variable for redirection of page
        //$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        // For clearing cache
        // $types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
        // foreach ($types as $type) {
        //     $this->_cacheTypeList->cleanType($type);
        // }
        // foreach ($this->_cacheFrontendPool as $cacheFrontend) {
        //     $cacheFrontend->getBackend()->clean();
        // }

        // For implementing redirection of page
        // $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        // return $resultRedirect;

        $data=array("abc");
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($data);
        return $resultJson;


        // --------------------------------------JUNK-----------------------------------------------------------
        // $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        // return $result;
        // --------------------------------------JUNK-----------------------------------------------------------
    }
}

