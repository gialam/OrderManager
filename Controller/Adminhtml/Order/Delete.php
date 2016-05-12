<?php
/**
 * Created by PhpStorm.
 * User: gialam
 * Date: 18/02/2016
 * Time: 09:50
 */
namespace Magenest\OrderManager\Controller\Adminhtml\Order;

use \Magento\Backend\App\Action\Context;
use Psr\Log\LoggerInterface;

class Delete extends \Magento\Backend\App\Action
{
    protected $_logger;
    /**
     * Delete constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context,
        LoggerInterface $loggerInterface
    )
    {
        $this->_logger = $loggerInterface;
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
        $orderId = $data['order_id'];
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($orderId) {

            /**
             * delete order
             */
            $modelManage = $this->_objectManager->create('Magenest\OrderManager\Model\OrderManage');
            $modelManage->load($orderId,'order_id');
            $dataManage = [
              'status_check'=>'no accept',
            ];
            $modelManage->addData($dataManage);

            /**
             * delete item(s)
             */
            $modelItem = $this->_objectManager->create('Magenest\OrderManager\Model\OrderItem')
                ->getCollection()
                ->addFieldToFilter('order_id',$orderId);

            /**
             * delete address
             */
            $modelAddress = $this->_objectManager->create('Magenest\OrderManager\Model\OrderAddress')
                ->getCollection()
                ->addFieldToFilter('order_id',$orderId);
            $totals = 0;
            $i = 0;
            try {
                $modelManage->save();

                foreach ($modelItem as $items) {
                    $items->setData($orderId,'order_id');
                    $items->delete();
                    $totals++;
                }

                foreach ($modelAddress as $addresss) {
                    $addresss->setData($orderId,'order_id');
                    $addresss->delete();
                    $i++;
                }
                $this->messageManager->addSuccess(__('The Order has been deleted.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('ordermanager/order/', ['_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($orderId);
                return $resultRedirect->setPath('ordermanager/order/');
            }

        }
        return $resultRedirect->setPath('*/*/');
    }
    protected function _isAllowed()
    {
        return true;
    }
}
