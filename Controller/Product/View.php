<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @category  Magenest
 */
namespace Magenest\OrderManager\Controller\Product;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\PageFactory;

class View extends Action
{
    protected $_resultPageFactory;

    protected $_coreRegistry;



    public function __construct(
        Context $context,
        PageFactory $pageFactory

    ) {
        $this->_resultPageFactory = $pageFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $orderId = $this->getRequest()->getParams();

        $this->_view->loadLayout();
        if ($block = $this->_view->getLayout()->getBlock('order.product.edit')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->renderLayout();
    }
    protected function _isAllowed()
    {
        return true;
    }
}
