<?php
/**
 * Created by PhpStorm.
 * User: gialam
 * Date: 18/02/2016
 * Time: 09:50
 */
namespace Magenest\OrderManager\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Psr\Log\LoggerInterface;
use Magenest\OrderManager\Model\OrderManageFactory;

class Update extends \Magento\Backend\App\Action
{
    protected $_logger;
    protected $_manageFactory;
    /**
     * Delete constructor.
     * @param Action\Context $context
     */
    public function __construct(
        Action\Context $context,
        LoggerInterface $loggerInterface,
        OrderManageFactory $manageFactory
    )
    {
        $this->_manageFactory = $manageFactory;
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
//        $model = $this->_manageFactory->create()->getCollection()->addFieldToFilter('order_id',$data['order_id']);
//        foreach ($model as $models){
//            $id = $models->getId();
//        }


        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $model = $this->_objectManager->create('Magenest\OrderManager\Model\OrderItem');
//        $billingId = $data['billingid'];
        $totals = 0;
        if ($data) {
            $id = $this->_manageFactory->create()->load($data['order_id'],'order_id')->getId();
           $collection =  $model->getCollection()->addFieldToFilter('order_id',$data['order_id']);
            foreach ($collection as $collections)
            {
                $dataInfo = [
                    'quantity'     => $data['quantity-'.$collections->getProductId()],
                ];
//                $this->_logger->addDebug(print_r($dataInfo,true));
                $modelSave = $model->getCollection()->addFieldToFilter('order_id',$data['order_id'])
                    ->addFieldToFilter('product_id',$collections->getProductId())->getFirstItem();
                $modelSave->addData($dataInfo);
                $modelSave->save();
                $totals++;
            }

//
            try {
//                $model->save();
                $this->messageManager->addSuccess(__('The Item(s) has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('ordermanager/order/edit', ['id'=>$id,'_current' => true]);
                }
                return $resultRedirect->setPath('ordermanager/order/edit',['id'=>$id]);
            } catch (\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data['order_id']);
                return $resultRedirect->setPath('ordermanager/order/edit',['id'=>$id]);
            }

        }
//        return $resultRedirect->setPath('ordermanager/order/edit',['id'=>$id]);
    }
    protected function _isAllowed()
    {
        return true;
    }
}
