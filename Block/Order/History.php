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
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class History
 *
 * @package Magenest\Ticket\Block\Order
 */
class History extends Template
{

    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'order/history.phtml';


    protected $_ordermanageFactory;

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
        CustomerSession $customerSession,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->_ordermanageFactory = $ordermanageFactory;
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
        $this->pageConfig->getTitle()->set(__('My Order'));
    }

    /**
     * Get Ticket Collection
     *
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrderInfo()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        else{
            $data = $this->_ordermanageFactory->create()->getCollection()
            ->addFieldToFilter('customer_id',$customerId);
        }

        return $data;
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
