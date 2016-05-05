<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @category  Magenest
 */
namespace Magenest\OrderManager\Controller\Product;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Customer\Model\Session as CustomerSession;


/**
 * Class Save
 * @package Magenest\PDFInvoice\Controller\Adminhtml\Invoice
 */
class Update extends \Magento\Framework\App\Action\Action
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    protected $_logger;
    protected $_orderFactory;
    protected $_customerSession;
    /**
     * Save constructor.
     * @param Context $context
     * @param RequestInterface $request
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        LoggerInterface $loggerInterface,
        OrderFactory $orderFactory,
        \Magenest\OrderManager\Model\OrderItemFactory $orderItemFactory,
        \Magenest\OrderManager\Model\OrderAddressFactory $addressFactory,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_orderItemFactory = $orderItemFactory;
        $this->_request         = $request;
        $this->_logger          = $loggerInterface;
        $this->_orderFactory   = $orderFactory;
        $this->_addressFactory  = $addressFactory;
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
        $orderId = $this->getRequest()->getParam('order_id');

        $customerId = $this->_customerSession->getCustomerId();
        $orderCollection = $this->_orderFactory->create()->load($orderId);
        $status = $orderCollection->getStatus();
        $firstName  = $orderCollection->getCustomerFirstname();
        $lastName   = $orderCollection->getCustomerLastname();
        $email = $orderCollection->getCustomerEmail();

        /**
         * get data for total
         */
//        $priceShipping = $orderCollection->getBaseShippingAmount();
//        $tax = $orderCollection->getBaseTaxAmount();
//        $collection = $this->_orderItemFactory->create()->getCollection()->addFieldToFilter('order_id',$orderId);
//        $sum = 0;
//        $i = 0;
//        $discounts = 0;
//        foreach($collection as $collections )
//        {
//            $price = $collections->getPrice();
//            $quantity = $collections->getQuantity();
//            $subtotal = $price * $quantity;
//            $discount = $collections->getDiscount();
//            $i++;
//            $sum += $subtotal;
//            $discounts += $discount;
//
//        }
//        $grandTotal = $sum + $priceShipping + $tax + $discounts;

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {

            /**
             * save order info
             */
            $id = $data['order_id'];
            $model = $this->_objectManager->create('Magenest\OrderManager\Model\OrderManage');
            $model->load($id,'order_id');
            $dataInfo = [
                'order_id' => $id,
                'customer_id' =>$customerId,
                'status' => $status,
                'status_check'=>'checking',
                'customer_name' =>  $firstName.' '.$lastName,
                'customer_email' => $email
            ];
            $model->addData($dataInfo);
            /**
             * save order total
             */
//            $modelGrid = $this->_objectManager->create('Magenest\OrderManager\Model\OrderGrid');
//            $modelGrid->load($orderId,'increment_id');
//            $dataGrid = [
//                'increment_id' => $orderId,
//                'grand_total' => $grandTotal,
//                'subtotal' =>  $sum,
//                'shipping_and_handling' => $priceShipping,
//            ];
//            $modelGrid->addData($dataGrid);

            /**
             * save item(s) order
             */
            $modelLast = $this->_objectManager->create('Magenest\OrderManager\Model\OrderItem');

            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($model->getData());
           $totals = 0;
            try {
                /**
                 * save item(s) order
                 */
                foreach( $orderCollection->getAllItems() as $item){
                    $collections = $modelLast->getCollection();
                    $dataItem = [
                        'order_id' => $orderId,
                        'product_id' =>$item->getProductId(),
                        'name' =>$item->getName() ,
                        'sku' =>$item->getSku() ,
                        'price' =>$item->getPrice() ,
                        'discount'=>'0',
                        'tax'    =>$item->getTaxPercent(),
                        'quantity' =>$data['quantity-'.$item->getProductId()] ,
                    ];
//                    $this->_logger->addDebug(print_r($dataItem,true));
                    $modelLast = $collections->addFieldToFilter('order_id',$orderId)
                        ->addFieldToFilter('product_id',$item->getProductId())->getFirstItem();
                    $modelLast->addData($dataItem);
                    $modelLast->save();
                    $totals++;
                }


                $model->save();
//                $modelGrid->save();
                $this->messageManager->addSuccess(__('Please wait ulti informations has through !. Email will send to you'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
//                    $orderId = $this->getRequest()->getParam('order_id');
                    return $resultRedirect->setPath('ordermanager/product/view',['order_id'=>$id]);
                }
                return $resultRedirect->setPath('ordermanager/product/view',['order_id'=>$id]);
            } catch (\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());

            } catch (\Exception $e) {
                $this->messageManager->addError($e, __('Something went wrong while update order'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                return $resultRedirect->setPath('ordermanager/product/view',['order_id'=>$id]);
            }
        }
        return $resultRedirect->setPath('ordermanager/product/view',['order_id'=>$orderId]);

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