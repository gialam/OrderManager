<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @category  Magenest
 */
namespace Magenest\OrderManager\Block\Product\Subtotal;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class Total extends Template
{
    /**
     * @var CustomerSession\
     */
    protected $_customerSession;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;
    protected $_logger;
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_itemCollection;

    protected $_template = 'order/product/subtotal/total.phtml';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productFactory;
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
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
        \Magento\Sales\Model\OrderFactory $itemCollectionFactory,
        \Magenest\OrderManager\Model\OrderItemFactory $orderItemFactory,
        LoggerInterface $loggerInterface,
        array $data =[]
    )
    {
        $this->_customerSession = $customerSession;
        $this->_scopeConfig = $scopeConfig;
        $this->_itemCollection        = $itemCollectionFactory;
        $this->productFactory = $productFactory;
        $this->_orderItemFactory = $orderItemFactory;
        $this->_logger = $loggerInterface;
        parent::__construct($context, $data);
    }
    public function getDataCollection()
    {
        $orderId = $this->getRequest()->getParam('order_id');
//        $this->_logger->addDebug(print_r('1111111',true));
        $collection = $this->_orderItemFactory->create()->getCollection()->addFieldToFilter('order_id',$orderId);

        return $collection;
    }
    public function getSubtotal()
    {
        $sum = 0;
        $i = 0;
        foreach($this->getDataCollection() as $collections)
        {
            $price = $collections->getPrice();
            $quantity = $collections->getQuantity();
            $subtotal = $price * $quantity;
            $i++;
            $sum += $subtotal;

        }

        return  $sum;

    }
    public function getItemCollection()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $data = $this->_itemCollection->create()->load($orderId);
        return $data;
    }
    public function getShippingHandling()
    {

        $priceShipping = $this->getItemCollection()->getBaseShippingAmount();
        return $priceShipping;
    }
    public function getDiscount()
    {
        $discounts = 0;
        $i = 0;
        foreach($this->getDataCollection() as $collections)
        {
            $discount = $collections->getDiscount();
            $i++;
            $discounts += $discount;

        }
        return $discounts;
    }
    public function getTaxAmount()
    {

        $tax = $this->getItemCollection()->getBaseTaxAmount();
        return $tax;
    }
    public function getGrandTotal()
    {

        $grandTotal = $this->getSubtotal() + $this->getShippingHandling() +
            $this->getTaxAmount() + $this->getDiscount();
        return $grandTotal;
    }
    public function getSymbolCurrency()
    {
        $symbol = $this->getItemCollection()->getOrderCurrency()->getCurrencySymbol();
        return $symbol;
    }

}