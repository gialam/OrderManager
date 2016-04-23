<?php
/**
 * Created by PhpStorm.
 */
namespace Magenest\OrderManager\Block\Adminhtml\Order;

use Psr\Log\LoggerInterface;
use Magento\Sales\Model\OrderFactory;

class Edit extends \Magento\Backend\Block\Template
{
    protected $_itemFactory;
    protected $_logger;
    protected $_orderFactory;
    protected $_addressFactory;
    protected $_ordercoreFactory;
    public function __construct(
      \Magento\Backend\Block\Template\Context $context,
      \Magenest\OrderManager\Model\OrderItemFactory $itemFactory,
      \Magenest\OrderManager\Model\OrderManageFactory $orderFactory,
      \Magenest\OrderManager\Model\OrderAddressFactory $addressFactory,
      OrderFactory $ordercoreFactory,
      LoggerInterface $loggerInterface,
      array $data =[] )
    {
      $this->_logger      = $loggerInterface;
      $this->_itemFactory = $itemFactory;
      $this->_orderFactory = $orderFactory;
      $this->_addressFactory = $addressFactory;
      $this->_ordercoreFactory = $ordercoreFactory;
      parent::__construct($context, $data);
    }
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Information Order '));
    }
    public function getOrderId()
    {
        $id = $this->getRequest()->getParam('id');
        $order = $this->_orderFactory->create()->getCollection()->addFieldToFilter('id',$id);
        foreach($order as $orders){
            $orderId = $orders->getOrderId();
        }
        return $orderId;
    }

    public function getItemsInfo()
    {
        $orderId = $this->getOrderId();
        $items = $this->_itemFactory->create()->getCollection()->addFieldToFilter('order_id',$orderId);
        return $items;
    }

    public function getUpdateProductUrl()
    {
        $orderId = $this->getOrderId();
        return $this->getUrl('ordermanager/order/update',['order_id'=>$orderId]);
    }

    public function getRemoveProduct($id_product)
    {
        $id = $this->getRequest()->getParam('id');
        return $this->getUrl('ordermanager/order/remove',['id'=>$id,'item_id'=>$id_product]);
    }

    public function getBillingAddress()
    {
        $orderId = $this->getOrderId();
        $billing = $this->_addressFactory->create()->getCollection()
            ->addFieldToFilter('order_id',$orderId)
            ->addFieldToFilter('address_type','billing');
        return $billing;
    }

    public function getShippingAddress()
    {
        $orderId = $this->getOrderId();
        $billing = $this->_addressFactory->create()->getCollection()
            ->addFieldToFilter('order_id',$orderId)
            ->addFieldToFilter('address_type','shipping');
        return $billing;
    }

    public function getDeleteDataUrl()
    {
        $orderId = $this->getOrderId();
        return $this->getUrl('ordermanager/order/delete',['order_id'=>$orderId]);
    }
    public function getAcceptUrl()
    {
        $orderId = $this->getOrderId();
        return $this->getUrl('ordermanager/order/accept',['order_id'=>$orderId]);
    }
    public function getSymbolItem()
    {
        $orderId = $this->getOrderId();
        $data = $this->_ordercoreFactory->create()->load($orderId);
        $symbol = $data->getOrderCurrency()->getCurrencySymbol();
        return $symbol;
    }
    public function getBillingUrl($order_id)
    {
     return $this->getUrl('ordermanager/address/editbilling',['order_id'=>$order_id]);
    }

    public function getShippingUrl($order_id)
    {
        return $this->getUrl('ordermanager/address/editshipping',['order_id'=>$order_id]);
    }

    public function getBackUrl()
    {
        return $this->getUrl('ordermanager/order/');
    }

    protected function _isAllowed()
    {
        return true;
    }
}