<?php
/**
 * Created by PhpStorm.
 * User: gialam
 * Date: 25/01/2016
 * Time: 20:24
 */
namespace Magenest\OrderManager\Block\Adminhtml\Item;
use	Magento\Backend\Block\Widget\Grid	as	WidgetGrid;
class	Grid	extends	\Magento\Backend\Block\Widget\Grid\Extended
{

    protected	$_productCollection;
    protected $_status;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Packt\HelloWorld\Model\ResourceModel\Subscription\Collection $subscriptionCollection
     * @param array $data
     */
    public	function	__construct(
        \Magento\Backend\Block\Template\Context	$context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\Config $salesConfig,
        array $data = []
    ){
        $this->_catalogConfig = $catalogConfig;
        $this->_sessionQuote = $sessionQuote;
        $this->_salesConfig = $salesConfig;
        $this->_productCollection	=	$productFactory;
        parent::__construct($context,	$backendHelper,	$data);
        $this->setEmptyText(__('No	Product(s)	Found'));
    }

    /**
     * @return $this ket noi database de lay du lieu cho grid
     */
    /**
     * Retrieve quote store object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_sessionQuote->getStore();
    }

    /**
     * Retrieve quote object
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->_sessionQuote->getQuote();
    }

    protected	function	_prepareCollection()
    {
        $attributes = $this->_catalogConfig->getProductAttributes();
        /* @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollection->create()->getCollection();
        $collection->setStore(
            $this->getStore()
        )->addAttributeToSelect(
            $attributes
        )->addAttributeToSelect(
            'sku'
        )->addStoreFilter()->addAttributeToFilter(
            'type_id',
            $this->_salesConfig->getAvailableProductTypes()
        )->addAttributeToSelect(
            'gift_message_available'
        );
        $this->setCollection($collection );
        return	parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'index' => 'entity_id'
            ]
        );
        $this->addColumn(
        'name',
        [
            'header' => __('Product'),
            'index' => 'name'
        ]
    );
        $this->addColumn(
            'sku',
            [
                'header' =>__('SKU'),
                'index' => 'sku',
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'column_css_class' => 'price',
                'type' => 'currency',
                'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
                'rate' => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
                'index' => 'price',
                'renderer' => 'Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Price'
            ]
        );
        $this->addColumn(
            'qty',
            [
                'filter' => false,
                'sortable' => false,
                'header' => __('Quantity'),
                'renderer' => 'Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Qty',
                'name' => 'qty',
                'inline_css' => 'qty',
                'type' => 'input',
                'validate_class' => 'validate-number',
                'index' => 'qty'
            ]
        );

        return $this;
    }

    /**
     * @return $this
     * add action in box action
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setTemplate('Magento_Backend::widget/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('order');

        $this->getMassactionBlock()->addItem(
            'add_item',
            [
                'label' => __('Add Item(s)'),
                'url' => $this->getUrl('*/*/massAdd'),
                'confirm' => __('Are you sure to add product(s) ?')
            ]
        );

        return $this;
    }
}