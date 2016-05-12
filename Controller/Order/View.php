<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @category  Magenest
 */
namespace Magenest\OrderManager\Controller\Order;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Psr\Log\LoggerInterface;

class View extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $_logger;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        LoggerInterface        $loggerInterface
    ){
        $this->_logger         = $loggerInterface;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * View my ticket
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $orderId = $this->getRequest()->getParams();
//        $this->_logger->addDebug(print_r($orderId,true));
        $resultPage = $this->resultPageFactory->create();

        $block = $resultPage->getLayout()->getBlock('ordermanager.manage.view');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        return $resultPage;
    }
}
