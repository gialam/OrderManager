<?php
/**
 * Created by PhpStorm.
 */
namespace Magenest\OrderManager\Block\Adminhtml\Address\Billing;

use Psr\Log\LoggerInterface;
use Magento\Directory\Block\Data;
use Magento\Customer\Block\Address\Edit as editData;

class Edit extends \Magento\Backend\Block\Template
{

    protected $_logger;
    protected $_addressFactory;
    protected $_dataBilling;
    protected $_regionFactory;
    protected $_editData;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        LoggerInterface $loggerInterface,
        \Magenest\OrderManager\Model\OrderAddressFactory $addressFactory,
        \Magenest\OrderManager\Model\OrderManageFactory $manageFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        Data  $collectionDataShipping,
        editData $editData,
        array $data =[] )
    {
        $this->_logger         = $loggerInterface;
        $this->_addressFactory = $addressFactory;
        $this->_manageFactory  = $manageFactory;
        $this->_dataBilling = $collectionDataShipping;
        $this->_regionFactory = $regionFactory;
        $this->_editData = $editData;
        parent::__construct($context, $data);
    }
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Billing Information '));
    }
    public function getDataBilling(){
        $orderId = $this->getRequest()->getParam('order_id');
     $data = $this->_addressFactory->create()->getCollection()
         ->addFieldToFilter('order_id',$orderId)
         ->addFieldToFilter('address_type','billing');
     return $data;
    }
    public function getRegionId()
    {
        $collection = $this->getDataBilling();
        foreach($collection as $collections)
        {
            $region = $collections->getRegionId();

        }
        return $region;
    }
    public function getRegionName()
    {
        $id = $this->getRegionId();
        $collection = $this->_regionFactory->create()->load($id,'region_id')->getName();
        return $collection;
    }
    public function getCountryBillingHtmlSelect()
    {
        $country = $this->_dataBilling->getCountryHtmlSelect();
        return $country;
    }
    public function getUpdateBillingUrl()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        return $this->getUrl('ordermanager/address/updateBilling',['order_id'=>$orderId]);
    }
//    public function getRegionBilling()
//    {
//        $region = $this->_editData->getRegion();
//        return $region;
//    }
    public function getBackUrl()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $id = $this->_manageFactory->create()->load($orderId,'order_id')->getId();
        return $this->getUrl('ordermanager/order/edit',['id'=>$id]);
    }
}