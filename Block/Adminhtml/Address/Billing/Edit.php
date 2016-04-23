<?php
/**
 * Created by PhpStorm.
 */
namespace Magenest\OrderManager\Block\Adminhtml\Address\Billing;

use Psr\Log\LoggerInterface;
use Magento\Directory\Block\Data;

class Edit extends \Magento\Backend\Block\Template
{

    protected $_logger;
    protected $_addressFactory;
    protected $_dataBilling;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        LoggerInterface $loggerInterface,
        \Magenest\OrderManager\Model\OrderAddressFactory $addressFactory,
        Data  $collectionDataShipping,
        array $data =[] )
    {
        $this->_logger         = $loggerInterface;
        $this->_addressFactory = $addressFactory;
        $this->_dataBilling = $collectionDataShipping;

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
    public function getCountryBillingHtmlSelect()
    {
        $country = $this->_dataBilling->getCountryHtmlSelect();
        return $country;
    }
    public function getUpdateBillingUrl()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        return $this->getUrl('ordermanager/address/updatebilling',['order_id'=>$orderId]);
    }
}