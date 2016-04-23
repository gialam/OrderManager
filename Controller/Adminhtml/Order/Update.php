<?php
/**
 * Created by PhpStorm.
 */
namespace Magenest\OrderManager\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Psr\Log\LoggerInterface;


/**
 * Class Save
 * @package Magenest\PDFInvoice\Controller\Adminhtml\Invoice
 */
class Update extends  \Magento\Backend\App\Action
{

    protected $_request;

    protected $_logger;
    /**
     * Save constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context,
        LoggerInterface $loggerInterface
    ) {
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
        $this->_logger->addDebug(print_r($data,true));
//        $id =   $data['id'];
//        $itemId = $data['item_id'];
//
//        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
//        $resultRedirect = $this->resultRedirectFactory->create();
//        if ($itemId) {
//            $model = $this->_objectManager->create('Magenest\OrderManager\Model\OrderItem');
//            $model->load($itemId);
//
//            $model->setData($itemId);
//            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($model->getData());
//            try {
//                $model->delete();
//                $this->messageManager->addSuccess(__('Product have deleted .'));
//                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
//                if ($this->getRequest()->getParam('back')) {
////                    $orderId = $this->getRequest()->getParam('order_id');
//                    return $resultRedirect->setPath('ordermanager/order/edit',['id'=>$id]);
//                }
//                return $resultRedirect->setPath('ordermanager/order/edit',['id'=>$id]);
//            } catch (\LocalizedException $e) {
//                $this->messageManager->addError($e->getMessage());
//
//            } catch (\Exception $e) {
//                $this->messageManager->addError($e, __('Something went wrong while remove product'));
//                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
//                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
//                return $resultRedirect->setPath('ordermanager/order/edit',['id'=>$id]);
//            }
//        }
//        return $resultRedirect->setPath('ordermanager/order/edit',['id'=>$id]);
    }
    protected function _isAllowed()
    {
        return true;
    }
}
