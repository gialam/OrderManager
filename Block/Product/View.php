<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @category  Magenest
 */
namespace Magenest\OrderManager\Block\Product;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class View extends Template
{
    protected $_storeManager;
    /**
     * @var CustomerSession\
     */
    protected $_customerSession;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $itemCollection;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productFactory;
    /**
     * Edit constructor.
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Sales\Model\OrderFactory $itemCollectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CustomerSession $customerSession,
        ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
        \Magento\Sales\Model\OrderFactory $itemCollectionFactory,
        \Magenest\OrderManager\Model\OrderItemFactory $orderItemFactory,
        StoreManagerInterface $storemanager,
        array $data =[]
    )
    {
        $this->_storeManager = $storemanager;
        $this->_customerSession = $customerSession;
        $this->_scopeConfig = $scopeConfig;
        $this->itemCollection        = $itemCollectionFactory;
        $this->productFactory = $productFactory;
        $this->_orderItemFactory = $orderItemFactory;
        parent::__construct($context, $data);
    }
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__(' Edit Product(s)'));
    }

    public function getItemsOrder()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $collectionItem = $this->itemCollection->create()->load($orderId);
        return $collectionItem;
    }

    public function getDataProduct(){
        $collection = $this->productFactory->create()
            ->addAttributeToSelect('*');
        $collection->setPageSize(100);
        return $collection;
    }

    public function getNewProduct(){
        $orderId = $this->getRequest()->getParam('order_id');
        $data = $this->_orderItemFactory->create()->getCollection()->addFieldToFilter('order_id',$orderId);
        return $data;
    }

    public function getRemoveProduct($id_product)
    {
        $orderId = $this->getRequest()->getParam('order_id');
     return $this->getUrl('ordermanager/product/remove',['item_id'=>$id_product,'order_id'=>$orderId]);
    }

    public function getAddProductUrl()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        return $this->getUrl('ordermanager/product/addProduct', ['order_id' => $orderId]);
    }

        public function getNewAddProductUrl()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        return $this->getUrl('ordermanager/product/save',['order_id' => $orderId]);
    }

    public function getUpdateProductUrl()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        return $this->getUrl('ordermanager/product/update',['order_id'=>$orderId]);
    }

        public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    public function getSubtotalProduct()
    {
        $subtotal = $this->getLayout()->createBlock('Magenest\OrderManager\Block\Product\Subtotal\Total');
        return $subtotal->toHtml();
    }
    public function getImageRender()
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
//        $imageUrl = $mediaDirectory.'catalog/product/'.$this->_getValue($row);
//        return '<img src="'.$imageUrl.'" width="50"/>';
        return $mediaDirectory;
    }
    public function getCancelUrl()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        return $this->getUrl('ordermanager/product/cancel',['order_id'=>$orderId]);
    }
    public function getBackUrl()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        return $this->getUrl('sales/order/view',['order_id'=>$orderId]);
    }
    public function getProductNew()
    {
        $newProduct = $this->getLayout()->createBlock('Magenest\OrderManager\Block\Product\NewProduct');
        return $newProduct->toHtml();
    }

}