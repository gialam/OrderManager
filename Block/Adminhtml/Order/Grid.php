<?php
/**
 * Created by PhpStorm.
 * User: gialam
 * Date: 25/01/2016
 * Time: 20:24
 */
namespace Magenest\OrderManager\Block\Adminhtml\Order;
use	Magento\Backend\Block\Widget\Grid	as	WidgetGrid;
class	Grid	extends	\Magento\Backend\Block\Widget\Grid\Extended
{

    protected	$_orderCollection;
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
        \Magenest\OrderManager\Model\ResourceModel\OrderManage\Collection $orderCollection,
        array $data = []
    ){
        $this->_orderCollection	=	$orderCollection;
        parent::__construct($context,	$backendHelper,	$data);
        $this->setEmptyText(__('No	Order change'));
    }

    /**
     * @return $this ket noi database de lay du lieu cho grid
     */
    protected	function	_prepareCollection()
    {
        $this->setCollection($this->_orderCollection);
        return	parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' =>__('ID #'),
                'index'  => 'id',
            ]
        );
        $this->addColumn(
            'order_id',
            [
                'header' =>__('Order Id'),
                'index'  => 'order_id',
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' =>__('Status'),
                'index' => 'status',
            ]
        );
        $this->addColumn(
            'status_check',
            [
                'header' =>__('Sensorship'),
                'index' => 'status_check',
            ]
        );
        $this->addColumn(
            'customer_name',
            [
                'header' =>__('Customer Name'),
                'index' => 'customer_name',
            ]
        );
        $this->addColumn(
            'customer_email',
            [
                'header'  => __('Customer Email'),
                'index'   =>'customer_email',
            ]
        );
        $this->addColumn(
            'edit',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit'
                        ],
                        'field' => 'id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
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
            'delete_order',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => __('Are you sure to delete ?')
            ]
        );

        return $this;
    }
}