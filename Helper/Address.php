<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManager\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class Pdforder
 *
 * @package Magenest\PDFInvoice\Helper
 */
class Address extends AbstractHelper
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_addressFactory;
    protected $_countryFactory;
    protected $_regionFactory;



    public function __construct(
        Context $context,
        \Magenest\OrderManager\Model\OrderAddressFactory $addressFactory,
        \Magento\Directory\Model\CountryFactory           $countryFactory,
        \Magento\Directory\Model\RegionFactory            $regionFactory,
        \Magento\Sales\Model\OrderFactory                $orderFactory,
        array $data = []
    ) {
        $this->_orderCollection        = $orderFactory;
        $this->_addressFactory         = $addressFactory;
        $this->_countryFactory   = $countryFactory;
        $this->_regionFactory    = $regionFactory;
        parent::__construct($context);
    }


    /**
     * @param $creditmemoId
     * @return bool|string
     * @throws \Zend_Pdf_Exception
     */
    public function getAddress($orderId,$type = 'billing')
    {
        if ($type == 'order') {
            $collection = $this->_addressFactory->create()->getCollection()
                ->addFieldToFilter('order_id',$orderId)->addFieldToFilter('address_type','billing');
        }
        else{
            $collection = $this->_addressFactory->create()->getCollection()
                ->addFieldToFilter('order_id',$orderId)->addFieldToFilter('address_type','shipping');
        }

        foreach($collection as $collections)
        {
            $region = $collections->getRegionId();
            $country = $collections->getCountryId();
            $name = $collections->getFirstname().' '.$collections->getLastname();
            $company = $collections->getCompany();
            $street  = $collections->getStreet();
            $city = $collections->getCity();
            $postcode = $collections->getPostcode();
            $telephone = $collections->getTelephone();


        }
        $regionName = $this->_regionFactory->create()->load($region,'region_id')->getName();
        $countryName = $this->_countryFactory->create()->loadByCode($country)->getName();
        if (!empty($region)){
            $regionInfo = $city.','.$regionName.','.$postcode;
        }
        else{
            $regionInfo = $city.','.$postcode;
        }

        $dataAddress=[
            'name'=> $name,
            'company'=>$company,
            'street'=>$street,
            'region_info'=>$regionInfo,
            'country'=>$countryName,
            'telephone'=>$telephone,
        ];
        return $dataAddress;
    }

}
