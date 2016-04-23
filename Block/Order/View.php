<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @category  Magenest
 */
namespace Magenest\OrderManager\Block\Order;

class View extends \Magento\Sales\Block\Order\View
{
    protected $_orderFactory ;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Payment\Helper\Data $paymentHelper,
        array $data)
    {
        parent::__construct($context, $registry, $httpContext, $paymentHelper, $data);
    }
    public function getViewEditProductUrl($order)
    {
        return $this->getUrl('ordermanager/product/view', ['order_id' => $order->getId()]);
    }
}