<?php
/**
 * Created by PhpStorm.
 */
namespace Magenest\OrderManager\Controller\Adminhtml\Address;

use Magento\Backend\App\Action\Context;
use Psr\Log\LoggerInterface;
/**
 * Class Save
 * @package Magenest\PDFInvoice\Controller\Adminhtml\Invoice
 */
class SaveShipping extends  \Magento\Backend\App\Action
{

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
        $order = $this->getRequest()->getParam('order_id');

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $model = $this->_objectManager->create('Magenest\OrderManager\Model\OrderAddress');


        if ($data) {
            $shippingId = $data['shippingid'];
            $model->load($shippingId, 'address_id');
            $dataInfo = [
                'address_id'     =>$shippingId,
                'firstname'      => $data['firstname'],
                'lastname'       => $data['lastname'],
                'company'        => $data['company'],
                'telephone'      => $data['telephone'],
                'fax'            => $data['fax'],
                'street'         => $data['street'],
                'city'           => $data['city'],
                'postcode'       => $data['postcode'],
                'country_id'     => $data['country_id'],
            ];

            $model->addData($dataInfo);
            try {
                $model->save();
                $this->messageManager->addSuccess(__('Shipping address has been changed '));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                if ($this->getRequest()->getParam('back'))
                {
                    return $resultRedirect->setPath('ordermanager/order/edit',
                        ['id' => $this->getRequest()->getParam('id')]);
                }

                return $resultRedirect->setPath('ordermanager/order/edit',
                    ['id' => $this->getRequest()->getParam('id')]);
            } catch (\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());

            } catch (\Exception $e) {
                $this->messageManager->addError($e, __('Something went wrong while saving the shipping address.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        return $resultRedirect->setPath('ordermanager/order/edit',['id' => $this->getRequest()->getParam('id')]);
    }
    protected function _isAllowed()
    {
        return true;
    }
}
