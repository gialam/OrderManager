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
        $modelManageOrder = $this->_objectManager->create('Magenest\OrderManager\Model\OrderManage');
        $modelOrder       = $this->_objectManager->create('Magento\Sales\Model\Order');
        $modelItem        = $this->_objectManager->create('Magento\Sales\Model\Order\Item');
        $modelAddress     = $this->_objectManager->create('Magento\Sales\Model\Order\Address');

        $items            = $this->_itemFactory->create()->getCollection()
                            ->addFieldToFilter('order_id',$orderId);
        $address          = $this->_addressFactory->create()->getCollection()
                            ->addFieldToFilter('order_id',$orderId);

        $i = 0;
            try {
                $modelManageOrder->load($orderId,'order_id');
                $modelManageOrder->setData('status_check','accept');
                $modelManageOrder->save();

                if(!empty($items->getData())) {
                    foreach ($items as $item) {
                        $productId     = $item['productId'];
                        $dataItem = [
                            'order_id' => $orderId,
//                            'product_id'=>$item['product_id'],
                            'name'     => $item['name'],
                            'sku'      => $item['sku'],
                            'price'    => $item['price'],
                            'discount_percent'       => $item['discount'],
                            'discount_amount'        => $item['discount'] * $item['quantity'] * $item['price'] / 100,
                            'base_discount_amount'   => $item['discount'] * $item['quantity'] * $item['price'] / 100,
                            'discount_invoiced'      => $item['discount'] * $item['quantity'] * $item['price'] / 100,
                            'base_discount_invoiced' => $item['discount'] * $item['quantity'] * $item['price'] / 100,
                            'qty_ordered'            => $item['quantity'],
                            'row_total'              => $item['quantity'] * $item['price'],
                            'base_row_total'         => $item['quantity'] * $item['price'],
                            'row_invoiced'           => $item['quantity'] * $item['price'],
                            'base_row_invoiced'      => $item['quantity'] * $item['price'],
                            'tax_percent'            => $item['tax'],
                            'tax_amount'             => $item['tax'] * $item['quantity'] * $item['price'] / 100,
                            'base_tax_amount'        => $item['percent'] * $item['quantity'] * $item['price'] / 100,
                        ];
//                        $this->_logger->addDebug(print_r($dataItem,true));
                        $modelItems = $modelItem->getCollection()->addFieldToFilter('order_id', $orderId)
                            ->addFieldToFilter('product_id', $productId)->getFirstItem();
                        $modelItems->addData($dataItem);
                        $modelItems->save();
                        $i++;
                    }
                }

                if(!empty($address->getData())) {
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
//                        $this->_logger->addDebug(print_r($dataAddress,true));
                        $modelAddress = $modelAddress->getCollection()->addFieldToFilter('entity_id',$addressId)->getFirstItem();
                        $modelAddress->addData($dataAddress);
                        $modelAddress->save();
                        $i++;

                    }
                }
                if(!empty($items->getData())) {
                    $modelOrder->load($orderId,'entity_id');

                    $costShip = $modelOrder->load($orderId,'entity_id')->getShippingAmount();
                    $subtotal      = 0;
                    $totalDiscount = 0;
                    $totalTax      = 0;
                    $totalQuantity = 0;

                    foreach ($items as $item) {
                        $price= $item->getPrice();
                        $quantity = $item->getQuantity();
                        $rowTotal = $quantity * $price;
                        $discount = $item->getDiscount() * $rowTotal /100;
                        $tax      = $item->getTax() * $rowTotal /100;
                        $i++;
                        $totalQuantity += $quantity;
                        $totalDiscount += $discount;
                        $totalTax += $tax;
                        $subtotal += $rowTotal;
                        $grandtotal = $subtotal + $totalTax - $totalDiscount + $costShip;
                    }


                    $dataOrder = [
                        'base_grand_total'      => $grandtotal,
                        'grand_total'           => $grandtotal,
                        'base_total_invoiced'   => $grandtotal,
                        'base_total_paid'       => $grandtotal,
                        'total_invoiced'        => $grandtotal,
                        'total_paid'            => $grandtotal,
                        'subtotal'              => $subtotal,
                        'base_subtotal'         => $subtotal,
                        'subtotal_invoiced'     => $subtotal,
                        'base_subtotal_invoice' => $subtotal,
                        'tax_amount'            => $totalTax,
                        'tax_invoiced'          => $totalTax,
                        'base_tax_amount'       => $totalTax,
                        'base_tax_invoiced'     => $totalTax,
                        'discount_amount'       => '-'.$totalDiscount,
                        'base_discount_amount'  => '-'.$totalDiscount,
                        'discount_invoiced'     => '-'.$totalDiscount,
                        'base_discount_invoiced'=> '-'.$totalDiscount,
                        'total_qty_ordered'     => $totalQuantity ,

                    ];
                    $modelOrder->addData($dataOrder);
                    $modelOrder->save();
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
                return $resultRedirect->setPath('ordermanager/order/edit');
            }

        return $resultRedirect->setPath('ordermanager/order/');
    }
    protected function _isAllowed()
    {
        return true;
    }
}
