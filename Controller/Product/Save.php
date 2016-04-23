<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @category  Magenest
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
class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    protected $_logger;
    protected $_productFactory;
    /**
     * Save constructor.
     * @param Context $context
     * @param RequestInterface $request
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        LoggerInterface $loggerInterface,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
        array $data = []
    ) {

        $this->_request         = $request;
        $this->_logger          = $loggerInterface;
        $this->_productFactory   = $productFactory;
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
        $orderId = $this->getRequest()->getParam('order_id');
        $items = json_decode($data['pTableData'], true);
//        $this->_logger->addDebug(print_r($items,true));
        $resultRedirect = $this->resultRedirectFactory->create();
        $model = $this->_objectManager->create('Magenest\OrderManager\Model\OrderItem');

        if(isset($items)) {
//            $dataProduct = $this->_productFactory->create()->addAttributeToSelect('*')->load(2);
//            foreach ($dataProduct as $dataProducts) {
//                $price = $dataProducts->getPrice();
//                $rowTotal = $dataProducts->getRowTotal();
//                $discount = $dataProducts->getDiscount();
//            }
//            $this->_logger->addDebug(print_r($discount,true));
//            $this->_logger->addDebug(print_r($productId,true));
        $collections = $model->getCollection();
            $totals = 0;
            try {
                foreach ($items as $item)
                {
                    $collections = $model->getCollection();
                    $productId = $item['productId'];
                    $name = $item['name'];
                    $sku =  $item['sku'];
                    $price =  $item['price'];
                    $discount =  $item['discount'];
                    $quantity = $item['quantity'];
                    $dataInfo = [
                        'order_id' => trim($orderId),
                        'product_id' => trim($productId),
                        'name' => trim($name),
                        'sku' => trim($sku),
                        'price' => trim($price),
                        'discount'=> trim($discount),
                        'quantity' => trim($quantity),
                    ];

                    $model = $collections->addFieldToFilter('order_id', trim($orderId))
                                ->addFieldToFilter('product_id', trim($productId))->getFirstItem();
                    $model->addData($dataInfo);
                    $model->save();
                    $totals++;
                }

                $this->messageManager->addSuccess(__('Items has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('ordermanager/product/view',
                        ['order_id' => $orderId]);
                }
                return $resultRedirect->setPath('ordermanager/product/view',
                    ['order_id' => $orderId]);
            } catch (\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());

            } catch (\Exception $e) {
                $this->messageManager->addError($e, __('Something went wrong while saving the shipping address.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }


        }
        return $resultRedirect->setPath('ordermanager/product/view',['order_id' => $orderId]);
    }

    /**
     * @param $value
     * @param $data
     * @return string
     */

    protected function _isAllowed()
    {
        return true;
    }
}