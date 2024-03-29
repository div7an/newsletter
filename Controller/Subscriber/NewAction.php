<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Divvy\Newsletter\Controller\Subscriber;

use Magento\Customer\Api\AccountManagementInterface as CustomerAccountManagement;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Validator\EmailAddress as EmailValidator;
use Magento\Newsletter\Controller\Subscriber as SubscriberController;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Newsletter\Model\SubscriberFactory;
// (My addition)
use Magento\Framework\Controller\ResultFactory;


/**
 * New newsletter subscription action
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class NewAction extends SubscriberController implements HttpPostActionInterface
{
    // //Necessary variables to be used in constructor for cache (My Addition)
    // protected $_cacheTypeList;
    // protected $_cacheFrontendPool;

    /**
     * @var CustomerAccountManagement
     */
    protected $customerAccountManagement;

    /**
     * @var EmailValidator
     */
    private $emailValidator;

    /**
     * Initialize dependencies.
     *
     * @param Context $context
     * @param SubscriberFactory $subscriberFactory
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param CustomerUrl $customerUrl
     * @param CustomerAccountManagement $customerAccountManagement
     * @param EmailValidator $emailValidator
     */
    public function __construct(
        Context $context,
        SubscriberFactory $subscriberFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        CustomerUrl $customerUrl,
        CustomerAccountManagement $customerAccountManagement,

        //Cache classes and objects (My Addition)
        //\Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        //\Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,

        EmailValidator $emailValidator = null
    ) {
        // Assigning objects to variables creted above(My Addition)
        // $this->_cacheTypeList = $cacheTypeList;
        // $this->_cacheFrontendPool = $cacheFrontendPool;

        $this->customerAccountManagement = $customerAccountManagement;
        $this->emailValidator = $emailValidator ?: ObjectManager::getInstance()->get(EmailValidator::class);
        parent::__construct(
            $context,
            $subscriberFactory,
            $customerSession,
            $storeManager,
            $customerUrl
        );
    }

    /**
     * Validates that the email address isn't being used by a different account.
     *
     * @param string $email
     * @throws LocalizedException
     * @return void
     */

    public $data = array(); //My addition

    protected function validateEmailAvailable($email)
    {
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        if ($this->_customerSession->isLoggedIn()
            && ($this->_customerSession->getCustomerDataObject()->getEmail() !== $email
            && !$this->customerAccountManagement->isEmailAvailable($email, $websiteId))
        ) {
            //$data = array('This email address is already assigned to another user.');
            throw new LocalizedException(
                __('This email address is already assigned to another user.')
            );
        }
    }

    /**
     * Validates that if the current user is a guest, that they can subscribe to a newsletter.
     *
     * @throws LocalizedException
     * @return void
     */
    protected function validateGuestSubscription()
    {
        if ($this->_objectManager->get(ScopeConfigInterface::class)
                ->getValue(
                    Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG,
                    ScopeInterface::SCOPE_STORE
                ) != 1
            && !$this->_customerSession->isLoggedIn()
        ) {
           // $data = array('Sorry, but the administrator denied subscription for guests. Please <a href="%1">register</a>.');
            throw new LocalizedException(
                __(
                    'Sorry, but the administrator denied subscription for guests. Please <a href="%1">register</a>.',
                    $this->_customerUrl->getRegisterUrl()
                )
            );
        }
    }

    /**
     * Validates the format of the email address
     *
     * @param string $email
     * @throws LocalizedException
     * @return void
     */
    protected function validateEmailFormat($email)
    {
        if (!$this->emailValidator->isValid($email)) {
            //$data = array('Please enter a valid email address.');
            throw new LocalizedException(__('Please enter a valid email address.'));
        }
    }

    /**
     * New subscription action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $email = (string)$this->getRequest()->getPost('email');

            try {
                $this->validateEmailFormat($email);
                $this->validateGuestSubscription();
                $this->validateEmailAvailable($email);

                $subscriber = $this->_subscriberFactory->create()->loadByEmail($email);
                if ($subscriber->getId()
                    && (int) $subscriber->getSubscriberStatus() === Subscriber::STATUS_SUBSCRIBED
                ) {
                    $data = array('This email address is already subscribed.');
                    throw new LocalizedException(
                        
                        __('This email address is already subscribed.')
                    );
                }

                $status = (int) $this->_subscriberFactory->create()->subscribe($email);
                $this->messageManager->addSuccessMessage($this->getSuccessMessage($status));
                if($this->getSuccessMessage($status) == "Thank you for your subscription.") 
                    $data= array("Thank you for your subscription.");
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                //$data = array('Something went wrong with the subscription.');
                $this->messageManager->addExceptionMessage($e, __('Something went wrong with the subscription.'));
            }
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $redirect */
        // $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        // $redirectUrl = $this->_redirect->getRedirectUrl();

        //Cleaning Cache  (My Addition)
        // $types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
        // foreach ($types as $type) {
        //     $this->_cacheTypeList->cleanType($type);
        // }
        // foreach ($this->_cacheFrontendPool as $cacheFrontend) {
        //     $cacheFrontend->getBackend()->clean();
        //}

        
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($data);
        return $resultJson;
        //return $redirect->setUrl($redirectUrl);
    }



    /**
     * Get success message
     *
     * @param int $status
     * @return Phrase
     */
    private function getSuccessMessage(int $status): Phrase
    {

        if ($status === Subscriber::STATUS_NOT_ACTIVE) {
            //$data = array('The confirmation request has been sent.');
            return __('The confirmation request has been sent.');
        }
        //$data = array('Thank you for your subscription.');
        return __('Thank you for your subscription.');
    }
}
