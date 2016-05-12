<?php
/**
 * Created by PhpStorm.
 */
namespace Magenest\OrderManager\Controller\Product;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;


/**
 * Class Save
 * @package Magenest\PDFInvoice\Controller\Adminhtml\Invoice
 */
class Cancel extends \Magento\Framework\App\Action\Action
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    protected $_logger;
    /**
     * Save constructor.
     * @param Context $context
     * @param RequestInterface $request
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        LoggerInterface $loggerInterface
    ) {

        $this->_request = $request;
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
//        $this->_logger->addDebug(print_r($data,true));
        $orderId = $data['order_id'];
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($orderId) {
            $modelManage = $this->_objectManager->create('Magenest\OrderManager\Model\OrderManage');
            $modelManage->load($orderId,'order_id');
            $modelManage->setData($orderId,'order_id');

            $modelItem = $this->_objectManager->create('Magenest\OrderManager\Model\OrderItem')
                ->getCollection()
                ->addFieldToFilter('order_id',$orderId);

            $modelAddress = $this->_objectManager->create('Magenest\OrderManager\Model\OrderAddress')
                ->getCollection()
                ->addFieldToFilter('order_id',$orderId);
            $totals = 0;
            $i = 0;
            try {
                $modelManage->delete();
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
                    return $resultRedirect->setPath('sales/order/view', ['order_id'=>$orderId,'_current' => true]);
                }
                return $resultRedirect->setPath('sales/order/view', ['order_id'=>$orderId]);
            } catch (\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($orderId);
                return $resultRedirect->setPath('sales/order/view', ['order_id'=>$orderId]);
            }

        }
        return $resultRedirect->setPath('sales/order/view', ['order_id'=>$orderId]);
    }
    protected function _isAllowed()
    {
        return true;
    }
}
