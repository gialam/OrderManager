<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @category  Magenest
 */
namespace Magenest\OrderManager\Block\Address\Shipping;

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
        parent::__construct($context, $data);
    }
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Shipping Information '));
    }
    public function getOrderShippingAddress()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $collectionItem = $this->itemCollection->create()->load($orderId);
//        $collection = $collectionItem->getGrandTotal();
        return $collectionItem;
    }

    public function getCountryShippingHtmlSelect()
    {
        $country = $this->_collectionDataShipping->getCountryHtmlSelect();
        return $country;
    }
//    public function getRegionShipping()
//    {
//        $region = $this->_editData->getRegion();
//        return $region;
//    }
    public function getBackUrl()
    {
        return $this->getUrl('*/*/history');
    }
    public function getBaseUrl()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        return $this->getUrl('ordermanager/address/saveShipping',['order_id'=>$orderId]);
    }

}