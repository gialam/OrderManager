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
use Psr\Log\LoggerInterface;

class View extends Action
{
    protected $_resultPageFactory;

    protected $_coreRegistry;
    protected $_logger;


    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        LoggerInterface $loggerInterface

    ) {
        $this->_logger = $loggerInterface;
        $this->_resultPageFactory = $pageFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $orderId = $this->getRequest()->getParams();

//        $this->_logger->addDebug(print_r($orderId,true));
        $this->_view->loadLayout();
        if ($block = $this->_view->getLayout()->getBlock('ordermanager.product.edit')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->renderLayout();
    }
    protected function _isAllowed()
    {
        return true;
    }
}
