<?php
/**
* Copyright © 2015 Magenest. All rights reserved.
* See COPYING.txt for license details.
*
* @category  Magenest
*/
namespace Magenest\OrderManager\Block\Adminhtml\Order\View;
class Items extends \Magento\Sales\Block\Adminhtml\Order\View\Items
{/**
 * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
 */
    protected $productFactory;
    protected $__orderCollection;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
        array $data = []
    )
    {
        $this->productFactory = $productFactory;
        $this->_orderCollection        = $orderFactory;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $data);
    }

    public function getOrderInfo()
    {
         $orderId = $this->getRequest()->getParam('order_id');
         $collectionItem = $this->_orderCollection->create()->load($orderId);
         //        $collection = $collectionItem->getGrandTotal();
         return $collectionItem;
    }
    public function getDataProduct(){
        $collection = $this->productFactory->create()
            ->addAttributeToSelect('*');
        $collection->setPageSize(5);
        return $collection;
    }
    public function getRemoveProduct($id_product)
    {
        return $this->getUrl('ordermanager/order/remove',['item_id'=>$id_product]);
    }
}