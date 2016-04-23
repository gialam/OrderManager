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
        array $data = []
    ) {

        $this->_request         = $request;
        $this->_logger          = $loggerInterface;
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
        $orderId = $this->getRequest()->getParam('order_id');
        $orderCollection = $this->_orderFactory->create()->load($orderId);
        $status = $orderCollection->getStatus();
        $firstName  = $orderCollection->getCustomerFirstname();
        $lastName   = $orderCollection->getCustomerLastname();
        $email = $orderCollection->getCustomerEmail();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $id = $data['order_id'];
//            $this->_logger->addDebug(print_r($id,true));

            $model = $this->_objectManager->create('Magenest\OrderManager\Model\OrderManage');
            $model->load($id,'order_id');
            $dataInfo = [
                'order_id' => $id,
                'status' => $status,
                'customer_name' =>  $firstName.' '.$lastName,
                'customer_email' => $email
            ];
            $model->addData($dataInfo);
            $modelLast = $this->_objectManager->create('Magenest\OrderManager\Model\OrderItem');

            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($model->getData());
           $totals = 0;
            try {
                foreach( $orderCollection->getAllItems() as $item){
                    $collections = $modelLast->getCollection();
                    $dataItem = [
                        'order_id' => $orderId,
                        'product_id' =>$item->getProductId(),
                        'name' =>$item->getName() ,
                        'sku' =>$item->getSku() ,
                        'price' =>$item->getPrice() ,
                        'discount'=>$item->getDiscountAmount() ,
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
                $this->messageManager->addSuccess(__('Please wait ulti informations has through !. Email will send to you'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
//                    $orderId = $this->getRequest()->getParam('order_id');
                    return $resultRedirect->setPath('sales/order/view',['order_id'=>$id]);
                }
                return $resultRedirect->setPath('sales/order/view',['order_id'=>$id]);
            } catch (\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());

            } catch (\Exception $e) {
                $this->messageManager->addError($e, __('Something went wrong while update order'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                return $resultRedirect->setPath('sales/order/view',['order_id'=>$id]);
            }
        }
        return $resultRedirect->setPath('sales/order/view',['order_id'=>$orderId]);

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