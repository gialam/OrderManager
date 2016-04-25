<?php
/**
 * Created by PhpStorm.
 * User: gialam
 * Date: 18/02/2016
 * Time: 09:05
 */
namespace Magenest\OrderManager\Controller\Adminhtml\Order;

use \Magento\Backend\App\Action\Context;

class MassDeleteOrder extends \Magento\Backend\App\Action
{

    protected $_logger;
    /**
     * Delete constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context,
        \Psr\Log\LoggerInterface $loggerInterface
    )
    {
        $this->_logger = $loggerInterface;
        parent::__construct($context);

    }
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getParam('order');
        $manageOrder  = $this->_objectManager->create('Magenest\OrderManager\Model\OrderManage');
        $manageAddress  = $this->_objectManager->create('Magenest\OrderManager\Model\OrderAddress');
        $manageItem     = $this->_objectManager->create('Magenest\OrderManager\Model\OrderItem');

//        $this->_logger->addDebug(print_r($data,true));
        if (!is_array($data) || empty($data)) {
            $this->messageManager->addError(__('Please select order(s).'));
        } else {
            try {
                foreach ($data as $dataInfo) {
                    $id           = $dataInfo;
                    $order        = $manageOrder->load($id);
                    $order->delete();
                }
//                foreach($data as $dataAddress){
//                    $id = $dataAddress;
//                    $orderId      = $manageOrder->load($id)->getOrderId();
//                    $modelAddress = $this->_objectManager->create('Magenest\OrderManager\Model\OrderAddress')
//                        ->getCollection()
//                        ->addFieldToFilter('order_id',$orderId);
////                    $address->delete();
//                    $this->_logger->addDebug(print_r($modelAddress,true));
//                }

//                foreach($data as $dataItem){
//                    $id             = $dataItem;
//                    $orderId        = $dataItem->getOrderId();
//                    $item           = $manageItem->load($orderId);
//                    $item->delete();
//                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($data))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $this->resultRedirectFactory->create()->setPath('ordermanager/order/');
    }
}
