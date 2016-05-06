<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @category  Magenest
 */
namespace Magenest\OrderManager\Block\Order;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magenest\OrderManager\Model\OrderManageFactory ;
use Magenest\OrderManager\Model\OrderItemFactory ;
use Magenest\OrderManager\Helper\Address ;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\OrderFactory;

/**
 * Class History
 *
 * @package Magenest\Ticket\Block\Order
 */
class ViewManage extends Template
{

    protected $_ordermanageFactory;
    protected $_itemFactory;
    protected $_addressInfo;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var string
     */
    protected $tickets;

    /**
     * @param Context $context
     * @param TicketCollectionFactory $ticketCollectionFactory
     * @param CustomerSession $customerSession
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        OrderManageFactory $ordermanageFactory,
        OrderItemFactory   $itemFactory,
        Address            $addressInfo,
        CustomerSession $customerSession,
        OrderFactory $ordercoreFactory,
        ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        array $data = []
    ) {
        $this->_regionFactory    = $regionFactory;
        $this->_ordermanageFactory = $ordermanageFactory;
        $this->_itemFactory        = $itemFactory;
        $this->_addressInfo     = $addressInfo;
        $this->_ordercoreFactory = $ordercoreFactory;
        $this->_customerSession = $customerSession;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Information'));
    }

    /**
     * Get Ticket Collection
     *
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrderId()
    {
       $orderId = $this->getRequest()->getParam('order_id');

        return $orderId;
    }
    /**
     *  infomation product of order
     */
    public function getItemsInfo()
    {
        $orderId = $this->getOrderId();
        $items   = $this->_itemFactory->create()->getCollection()->addFieldToFilter('order_id',$orderId);
        return $items;
    }
    /**
     * currency
     * @return string
     */
    public function getSymbolItem()
    {
        $orderId = $this->getOrderId();
        $data = $this->_ordercoreFactory->create()->load($orderId);
        $symbol = $data->getOrderCurrency()->getCurrencySymbol();
        return $symbol;
    }
    public function getBillingAddress()
    {
        $orderId = $this->getOrderId();
        $billing = $this->_addressInfo->getAddress($orderId,'billing');
        return $billing;
    }

    public function getShippingAddress()
    {
        $orderId = $this->getOrderId();
        $shipping = $this->_addressInfo->getAddress($orderId,'shipping');
        return $shipping;
    }
    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param object $ticket
     * @return string
     */
    public function getViewUrl($orderId)
    {
        return $this->getUrl('ordermanager/order/view', ['order_id' => $orderId]);
    }

}
