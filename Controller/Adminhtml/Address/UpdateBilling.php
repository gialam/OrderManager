<?php
/**
 * Created by PhpStorm.
 * User: gialam
 * Date: 18/02/2016
 * Time: 09:50
 */
namespace Magenest\OrderManager\Controller\Adminhtml\Address;

use \Magento\Backend\App\Action\Context;
use Psr\Log\LoggerInterface;

class UpdateBilling extends \Magento\Backend\App\Action
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
        $a = '111111';
        $this->_logger->addDebug(print_r($a,true));
//        $orderId = $data['order_id'];
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

//        if ($orderId) {
//            $modelManage = $this->_objectManager->create('Magenest\OrderManager\Model\OrderManage');
//            $modelManage->load($orderId,'order_id');
//            $modelManage->setData($orderId,'order_id');
//
//            $modelItem = $this->_objectManager->create('Magenest\OrderManager\Model\OrderItem')
//                ->getCollection()
//                ->addFieldToFilter('order_id',$orderId);
//
//            $modelAddress = $this->_objectManager->create('Magenest\OrderManager\Model\OrderAddress')
//                ->getCollection()
//                ->addFieldToFilter('order_id',$orderId);
//            $totals = 0;
//            $i = 0;
//            try {
//
//                $this->messageManager->addSuccess(__('The Order has been deleted.'));
//                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
//                if ($this->getRequest()->getParam('back')) {
//                    return $resultRedirect->setPath('ordermanager/order/', ['_current' => true]);
//                }
//                return $resultRedirect->setPath('*/*/');
//            } catch (\LocalizedException $e) {
//                $this->messageManager->addError($e->getMessage());
//            } catch (\Exception $e) {
//                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
//                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($orderId);
//                return $resultRedirect->setPath('ordermanager/order/');
//            }
//
//        }
//        return $resultRedirect->setPath('*/*/');
    }
    protected function _isAllowed()
    {
        return true;
    }
}
