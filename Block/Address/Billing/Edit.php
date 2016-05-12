<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @category  Magenest
 */
namespace Magenest\OrderManager\Block\Address\Billing;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Directory\Block\Data;
use Magento\Customer\Block\Address\Edit as editData;

class Edit extends Template
{
    /**
     * @var CustomerSession\
     */
    protected $_customerSession;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $itemCollection;
    protected $_collectionDataShipping;
    protected $_editData;
    protected $_addressFactory;
    /**
     * Edit constructor.
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Sales\Model\OrderFactory $itemCollectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CustomerSession $customerSession,
        ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\OrderFactory $itemCollectionFactory,
        \Magenest\OrderManager\Model\OrderAddressFactory $addressFactory,
        Data  $collectionDataShipping,
        editData $editData,
        array $data =[]
    )
    {
        $this->_customerSession = $customerSession;
        $this->_scopeConfig = $scopeConfig;
        $this->itemCollection        = $itemCollectionFactory;
        $this->_collectionDataShipping = $collectionDataShipping;
        $this->_editData = $editData;
        $this->_addressFactory  = $addressFactory;
        parent::__construct($context, $data);
    }
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Billing Information '));
    }
    public function getOrderBillingAddress()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $collectionItem = $this->itemCollection->create()->load($orderId);
        return $collectionItem;
    }
    public function getShippingAddress()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $data = $this->_addressFactory->create()->getCollection()->addFieldToFilter('order_id',$orderId)
            ->addFieldToFilter('address_type','shipping')->getData();
        return $data;
    }
    public function getCountryBillingHtmlSelect()
    {
        $country = $this->_collectionDataShipping->getCountryHtmlSelect();
        return $country;
    }
    public function getBaseUrl()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        return $this->getUrl('ordermanager/address/saveBilling',['order_id'=>$orderId]);
    }
}