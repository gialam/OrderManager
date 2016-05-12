<?php
/**
 * Created by PhpStorm.
 * User: gialam
 * Date: 17/02/2016
 * Time: 14:12
 */
namespace Magenest\OrderManager\Controller\Adminhtml\Item;

use Magento\Backend\App\Action;
use Psr\Log\LoggerInterface;
class Grid extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    protected $_logger;
    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        LoggerInterface $loggerInterface
    ) {
        $this->resultPageFactory    = $resultPageFactory;
        $this->_coreRegistry        = $registry;
        $this->_logger              = $loggerInterface;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */


    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_OrderManager::order')
            ->addBreadcrumb(__('Manage Order'), __('Manage Order'));
        return $resultPage;
    }

    /**
     * @return $this|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        // 1. Get ID and return NewPage
        $orderId = $this->getRequest()->getParam('order_id');
        $this->_view->loadLayout();
        if ($block = $this->_view->getLayout()->getBlock('order.item.collection.grid')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->renderLayout();

    }
    protected function _isAllowed()
    {
        return true;
    }
}
