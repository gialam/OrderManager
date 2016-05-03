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

/**
 * Class Remove
 * @package Magenest\OrderManager\Controller\Adminhtml\Order
 */
class Remove extends \Magento\Backend\App\Action
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * Remove constructor.
     * @param Context $context
     * @param LoggerInterface $loggerInterface
     */
    public function __construct(
        Context              $context,
        LoggerInterface      $loggerInterface
    )
    {
        $this->_logger       = $loggerInterface;
        parent::__construct($context);
    }

    /**
     * remove action
     * @return $this
     */
    public function execute()
    {
        $data      = $this->getRequest()->getParams();
        $id        = $data['id'];
        $itemId    = $data['item_id'];

//        $this->_logger->addDebug(print_r($itemId,true));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model     = $this->_objectManager->create('Magenest\OrderManager\Model\OrderItem');
            $modelData = $model->load($itemId);

            try {
                $modelData->setData($itemId);
                $modelData->delete();
                $this->messageManager->addSuccess(__('Product have deleted .'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                return $resultRedirect->setPath('ordermanager/order/edit',['id'=>$id]);
            } catch (\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());

            } catch (\Exception $e) {
                $this->messageManager->addError($e, __('Something went wrong while remove product'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                return $resultRedirect->setPath('ordermanager/order/edit',['id'=>$id]);
            }
        }
        return $resultRedirect->setPath('ordermanager/order/edit',['id'=>$id]);
    }
    protected function _isAllowed()
    {
        return true;
    }
}
