<?php
/**
 * Created by PhpStorm.
 */
namespace Magenest\OrderManager\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Psr\Log\LoggerInterface;
use Magenest\OrderManager\Model\OrderItemFactory;
use Magenest\OrderManager\Model\OrderAddressFactory;
use Magento\Directory\Model\RegionFactory;


/**
 * Class Save
 * @package Magenest\PDFInvoice\Controller\Adminhtml\Invoice
 */
class Accept extends  \Magento\Backend\App\Action
{

    protected $_request;
    protected $_logger;
    protected $_itemFactory;
    protected $_addressFactory;
    protected $_regionFactory;
    /**
     * Save constructor.
     * @param Context $context
     */
    public function __construct(
        Context                $context,
        LoggerInterface        $loggerInterface,
        OrderItemFactory       $itemFactory,
        OrderAddressFactory    $addressFactory,
        RegionFactory          $regionFactory
    ) {
        $this->_itemFactory    = $itemFactory;
        $this->_addressFactory = $addressFactory;
        $this->_logger         = $loggerInterface;
        $this->_regionFactory  = $regionFactory;
        parent::__construct($context);

    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
//        $this->_logger->addDebug(print_r($orderId,true));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $modelItem      = $this->_objectManager->create('Magento\Sales\Model\Order\Item');
        $modelAddress   = $this->_objectManager->create('Magento\Sales\Model\Order\Address');
        $items          = $this->_itemFactory->create()->getCollection()
                            ->addFieldToFilter('order_id',$orderId);
        $address        = $this->_addressFactory->create()->getCollection()
                            ->addFieldToFilter('order_id',$orderId);

        $i = 0;
            try {
                if(!empty($items)) {
                    foreach ($items as $item) {
                        $collections   = $modelItem->getCollection();
                        $productId     = $item['productId'];
                        $dataItem = [
                            'name'     => $item['name'],
                            'sku'      => $item['sku'],
                            'price'    => $item['price'],
                            'discount' => $item['discount'],
                            'quantity' => $item['quantity'],
                        ];
                        $this->_logger->addDebug(print_r($dataItem,true));
                        $modelItem = $collections->addFieldToFilter('order_id', $orderId)
                            ->addFieldToFilter('product_id', $productId)->getFirstItem();
//                        $modelItem->addData($dataItem);
//                        $modelItem->save();
                        $i++;
                    }
                }

                if(!empty($address)) {
                    foreach ($address as $infoAddress) {
                        $addressId            = $infoAddress['address_id'];
                        $dataAddress = [
                            'entity_id'       =>$infoAddress->getAddressId(),
                            'region_id'       => $infoAddress->getRegionId(),
                            'country_id'      => $infoAddress->getCountryId(),
                            'postcode'        => $infoAddress->getPostcode(),
                            'fax'             => $infoAddress->getFax(),
                            'lastname'        => $infoAddress->getLastname(),
                            'firstname'       => $infoAddress->getFirstname(),
                            'street'          => $infoAddress->getStreet(),
                            'city'            => $infoAddress->getCity(),
                            'telephone'       => $infoAddress->getTelephone(),
                            'company'         => $infoAddress->getCompany(),
                            'region'          => $this->_regionFactory->create()->load($infoAddress->getRegionId(),'region_id')->getName(),
                        ];
                        $this->_logger->addDebug(print_r($dataAddress,true));
                        $modelAddress = $modelAddress->getCollection()->addFieldToFilter('entity_id',$addressId)->getFirstItem();
                        $modelAddress->addData($dataAddress);
                        $modelAddress->save();
                        $i++;

                    }
                }

                $this->messageManager->addSuccess(__('Information has been accepted .'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
//                    $orderId = $this->getRequest()->getParam('order_id');
                    return $resultRedirect->setPath('ordermanager/order/');
                }
                return $resultRedirect->setPath('ordermanager/order/');
            } catch (\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());

            } catch (\Exception $e) {
                $this->messageManager->addError($e, __('Something went wrong while accept data'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
//                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                return $resultRedirect->setPath('ordermanager/order/edit');
            }

        return $resultRedirect->setPath('ordermanager/order/');
    }
    protected function _isAllowed()
    {
        return true;
    }
}
