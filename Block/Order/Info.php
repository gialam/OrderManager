<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @category  Magenest
 */

namespace Magenest\OrderManager\Block\Order;

use Magento\Sales\Model\Order\Address;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;

class Info extends \Magento\Sales\Block\Order\Info
{
    public function __construct(
        TemplateContext $context,
        Registry $registry,
        PaymentHelper $paymentHelper,
        AddressRenderer $addressRenderer, array $data)
    {
        parent::__construct($context, $registry, $paymentHelper, $addressRenderer, $data);
    }
    public function getViewEditShippingUrl($order)
    {
        return $this->getUrl('ordermanager/address/shipping', ['order_id' => $order->getId()]);
    }
    public function getViewEditBillingUrl($order)
    {
        return $this->getUrl('ordermanager/address/billing', ['order_id' => $order->getId()]);
    }
}