<?php
/**
 * Created by PhpStorm.
 */
namespace Magenest\OrderManager\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory  as PostCollectionFactory;

/**
 * Reviews admin controller
 */
abstract class Order extends Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var PostCollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;


    /**
     * Invoice constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param PostCollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        PostCollectionFactory $productCollectionFactory
    ) {
        $this->_context = $context;
        $this->coreRegistry = $coreRegistry;
//        $this->_postFactory = $postFactory;
        $this->_collectionFactory = $productCollectionFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_OrderManager::order')
            ->addBreadcrumb(__('Manage Order Manager'), __('Manage Order Manager'));
        $resultPage->getConfig()->getTitle()->set(__('Manage Order Manager'));

        return $resultPage;
    }
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_OrderManager::order');

    }
}
