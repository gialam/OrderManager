<?php
/**
 * Created by PhpStorm.
 */
namespace Magenest\OrderManager\Block\Adminhtml\Address\Shipping;

use Psr\Log\LoggerInterface;
use Magento\Directory\Block\Data;

class Edit extends \Magento\Backend\Block\Template
{

    protected $_logger;
    protected $_addressFactory;
    protected $_dataBilling;
    protected $_orderFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        LoggerInterface $loggerInterface,
        \Magenest\OrderManager\Model\OrderAddressFactory $addressFactory,
        \Magenest\OrderManager\Model\OrderManageFactory $orderFactory,
        Data  $collectionDataShipping,
        array $data =[] )
    {
        $this->_logger         = $loggerInterface;
        $this->_addressFactory = $addressFactory;
        $this->_orderFactory   = $orderFactory;
        $this->_dataBilling    = $collectionDataShipping;

        parent::__construct($context, $data);
    }
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Shipping Information '));
    }
    public function getDataShipping(){
        $orderId = $this->getRequest()->getParam('order_id');
        $data = $this->_addressFactory->create()->getCollection()
            ->addFieldToFilter('order_id',$orderId)
            ->addFieldToFilter('address_type','shipping');
        return $data;
    }
    public function getCountryBillingHtmlSelect()
    {
        $country = $this->_dataBilling->getCountryHtmlSelect();
        return $country;
    }
    public function getBackUrl()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $data = $this->_orderFactory->create()->load($orderId,'order_id');
        $id = $data->getId();
        return $this->getUrl('ordermanager/order/edit',['id'=>$id]);
//        return $id;
    }
    public function getBaseUrl()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        return $this->getUrl('ordermanager/address/saveShipping',['order_id'=>$orderId]);
    }
}