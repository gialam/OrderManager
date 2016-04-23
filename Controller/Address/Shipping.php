<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @category  Magenest
 */
namespace Magenest\OrderManager\Controller\Address;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\PageFactory;
//use Magenest\OrderManager\Block\Address\Shipping\Edit as editShipping;

class Shipping extends Action
{
    protected $_resultPageFactory;

    protected $_coreRegistry;

    protected $editshipping;

    public function __construct(
        Context $context,
        PageFactory $pageFactory
//        editShipping $editShipping

    ) {
        $this->_resultPageFactory = $pageFactory;
//        $this->editshipping = $editShipping;

        parent::__construct($context);
    }

    public function execute()
    {
        $orderId = $this->getRequest()->getParams();
//        $this->editshipping->getOrderShippingAddress($orderId);

        $this->_view->loadLayout();
        if ($block = $this->_view->getLayout()->getBlock('order.shipping.edit')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->renderLayout();
    }
    protected function _isAllowed()
    {
        return true;
    }
}
