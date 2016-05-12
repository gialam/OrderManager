<?php
/**
 * Created by PhpStorm.
 * User: gialam
 * Date: 18/02/2016
 * Time: 09:05
 */
namespace Magenest\OrderManager\Controller\Adminhtml\Order;

use \Magento\Backend\App\Action\Context;

class MassDelete extends \Magento\Backend\App\Action
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

        if (!is_array($data) || empty($data)) {
            $this->messageManager->addError(__('Please select order(s).'));
        } else {
            $i=0;
            try {

                foreach ($data as $dataInfo) {
                    $id            = $dataInfo;
                    $order        = $manageOrder->load($id);
                    $dataManage   =[
                        'status_check'=>'no accept',
                    ];
                    $i++;
                    $orderId = $order->getOrderId();
                    if ($orderId) {
                        $collectionItem = $manageItem->getCollection()->addFieldToFilter('order_id', $orderId);
                        $collectionAddress = $manageAddress->getCollection()->addFieldToFilter('order_id', $orderId);

                        foreach ($collectionItem as $collectionItems){
                            $infoItem = $collectionItems->getData();
                            $collectionItems->setData($infoItem);
                            $collectionItems->delete();
                            $i++;
                        }

                        foreach ($collectionAddress as $collectionAddresss){
                            $infoAddress = $collectionAddresss->getData();
//                            $this->_logger->addDebug(print_r($infoAddress, true));
                            $collectionAddresss->setData($infoAddress);
                            $collectionAddresss->delete();
                            $i++;
                        }

                    }
                    $order->addData($dataManage);
                    $order->save();
                }

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
