<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @category  Magenest
 */

namespace Magenest\OrderManager\Controller\Address;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\OrderFactory;

/**
 * Class Save
 * @package Magenest\PDFInvoice\Controller\Adminhtml\Invoice
 */
class SaveBilling extends \Magento\Framework\App\Action\Action
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    protected $_logger;
    protected $_addressFactory;
    protected $_orderFactory;
    /**
     * Save constructor.
     * @param Context $context
     * @param RequestInterface $request
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        LoggerInterface $loggerInterface,
        \Magenest\OrderManager\Model\OrderAddressFactory $addressFactory,
        OrderFactory $orderFactory

    ) {
        $this->_request = $request;
        $this->_logger = $loggerInterface;
        $this->_addressFactory = $addressFactory;
        $this->_orderFactory   = $orderFactory;
        parent::__construct($context);

    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */

        $order = $this->getRequest()->getParam('order_id');
        /**
         * get Data order on sales
         */
        $orderCollection = $this->_orderFactory->create()->load($order);
        $status = $orderCollection->getStatus();
        $firstName  = $orderCollection->getCustomerFirstname();
        $lastName   = $orderCollection->getCustomerLastname();
        $email = $orderCollection->getCustomerEmail();
        $resultRedirect = $this->resultRedirectFactory->create();
        $modelOrder = $this->_objectManager->create('Magenest\OrderManager\Model\OrderManage');
        $model = $this->_objectManager->create('Magenest\OrderManager\Model\OrderAddress');

        if ($data) {

            $modelOrder->load($order, 'order_id');
            $dataOrder = [
                'order_id' => $order,
                'status' => $status,
                'customer_name' => $firstName . ' ' . $lastName,
                'customer_email' => $email
            ];
            $modelOrder->addData($dataOrder);

            $billingId = $data['billingid'];
            $model->load($billingId, 'address_id');

//            $this->_logger->addDebug(print_r($dataInfo,true));

                try {
                    if (isset($data['default_billing'])) {
                        $collection = $this->_addressFactory->create()->getCollection()
                            ->addFieldToFilter('order_id', $order)
                            ->addFieldToFilter('address_type', 'shipping');
                        foreach ($collection as $collections) {
                            $dataInfomation = [
                                'address_id' => $billingId,
                                'order_id' => $order,
                                'firstname' => $collections->getFirstname(),
                                'lastname' => $collections->getLastname(),
                                'company' => $collections->getCompany(),
                                'telephone' => $collections->getTelephone(),
                                'fax' => $collections->getFax(),
                                'street' => $collections->getStreet(),
                                'city' => $collections->getCity(),
                                'postcode' => $collections->getPostcode(),
                                'region_id' => $collections->getRegionId(),
                                'country_id' => $collections->getCountryId(),
                                'address_type' => 'billing'
                            ];
                            $model->addData($dataInfomation);
                            $model->save();
                        }

                    } else {
                        $dataInfo = [
                            'address_id' => $billingId,
                            'order_id' => $order,
                            'firstname' => $data['firstname'],
                            'lastname' => $data['lastname'],
                            'company' => $data['company'],
                            'telephone' => $data['telephone'],
                            'fax' => $data['fax'],
                            'street' => $data['street'],
                            'city' => $data['city'],
                            'postcode' => $data['postcode'],
                            'region_id' => $data['region_id'],
                            'country_id' => $data['country_id'],
                            'address_type' => 'billing'
                        ];
                        $model->addData($dataInfo);
                        $model->save();
                    }
                    $modelOrder->save();

                    $this->messageManager->addSuccess(__('Billing Address has been sent to checker.'));
                    $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                    if ($this->getRequest()->getParam('back')) {
                        return $resultRedirect->setPath('sales/order/view',
                            ['order_id' => $this->getRequest()->getParam('order_id')]);
                    }
                    return $resultRedirect->setPath('sales/order/view',
                        ['order_id' => $this->getRequest()->getParam('order_id')]);
                } catch (\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());

                } catch (\Exception $e) {
                    $this->messageManager->addError($e, __('Something went wrong while saving the billing address.'));
                    $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                    $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                    return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                }
            }
        return $resultRedirect->setPath('sales/order/view',['order_id' =>$this->getRequest()->getParam('order_id')]);
    }

    /**
     * @param $value
     * @param $data
     * @return string
     */

    protected function _isAllowed()
    {
        return true;
    }
}