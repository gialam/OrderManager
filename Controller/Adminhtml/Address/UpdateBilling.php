<?php
/**
 * Created by PhpStorm.
 * User: gialam
 * Date: 18/02/2016
 * Time: 09:50
 */
namespace Magenest\OrderManager\Controller\Adminhtml\Address;

use Magento\Backend\App\Action;
use Psr\Log\LoggerInterface;
use Magenest\OrderManager\Model\OrderManageFactory;

class UpdateBilling extends \Magento\Backend\App\Action
{
    protected $_logger;
    protected $_manageFactory;
    /**
     * Delete constructor.
     * @param Context $context
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
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $model = $this->_objectManager->create('Magenest\OrderManager\Model\OrderAddress');


        if ($data) {
            $billingId = $data['billingid'];
            $id = $this->_manageFactory->create()->load($data['order_id'],'order_id')->getId();
//            $this->_logger->addDebug(print_r($id,true));
            $model->load($billingId, 'address_id');
            try {
                if (isset($data['default_billing'])) {
                    $collection = $model->getCollection()
                        ->addFieldToFilter('order_id', $data['order_id'])
                        ->addFieldToFilter('address_type', 'shipping');
                    foreach ($collection as $collections) {
                        $dataInfomation = [
                            'address_id' => $billingId,
                            'order_id' => $data['order_id'],
                            'firstname' => $collections->getFirstname(),
                            'lastname' => $collections->getLastname(),
                            'company' => $collections->getCompany(),
                            'telephone' => $collections->getTelephone(),
                            'fax' => $collections->getFax(),
                            'street' => $collections->getStreet(),
                            'city' => $collections->getCity(),
                            'postcode' => $collections->getPostcode(),
                            'region_id' => $collections->getRegionId(),
                            'country_id' => $collections->getCountryId(),
                            'address_type' => 'billing'
                        ];
                        $model->addData($dataInfomation);
                        $model->save();
                    }

                } else {
                    $dataInfo = [
                        'address_id' => $billingId,
                        'order_id' => $data['order_id'],
                        'firstname' => $data['firstname'],
                        'lastname' => $data['lastname'],
                        'company' => $data['company'],
                        'telephone' => $data['telephone'],
                        'fax' => $data['fax'],
                        'street' => $data['street'],
                        'city' => $data['city'],
                        'postcode' => $data['postcode'],
//                        'region_id' => $data['region_id'],
                        'country_id' => $data['country_id'],
                        'address_type' => 'billing'
                    ];
                    $model->addData($dataInfo);
                    $model->save();
                }
                $this->messageManager->addSuccess(__('The Billing address has been saved.'));
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
        return $resultRedirect->setPath('ordermanager/address/editbilling',['order_id'=>$data['order_id']]);
    }
    protected function _isAllowed()
    {
        return true;
    }
}
