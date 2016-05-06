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

    protected $_logger;
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */


    protected $_template = 'order/product/subtotal/total.phtml';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productFactory;

    public function __construct(
        Template\Context $context,
        \Magenest\OrderManager\Helper\Total $totalInfo,
        LoggerInterface $loggerInterface,
        array $data =[]
    )
    {
        $this->_totalInfo   = $totalInfo;
        $this->_logger = $loggerInterface;
        parent::__construct($context, $data);
    }
    public function getTotalInfo()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $data = $this->_totalInfo->getTotalData($orderId);
        return $data;
    }
}