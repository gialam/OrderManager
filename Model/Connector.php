<?php
/**
 * Created by PhpStorm.
 * User: gialam
 * Date: 01/03/2016
 * Time: 09:59
 */
namespace Magenest\OrderManager\Model;

/**
 * Class Connector
 * @package Magenest\OrderManager\Model
 */
class Connector
{
    const CONFIG_DEADLINE = 'ordermanager_labels/ordermanager_editor/ordermanager_deadline';
    const CONFIG_DELETE_ORDER= 'ordermanager_labels/ordermanager_editor/ordermanager_delete_core';
    const CONFIG_REMOVE_ITEM = 'ordermanager_labels/ordermanager_editor/ordermanager_remove';
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Connector constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(

        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array   $data = []
    ) {
    
        $this->_scopeConfig = $scopeConfig;

    }


    public function getDeadline()
    {
        $orderDeadline =  $this->_scopeConfig->getValue(self::CONFIG_DEADLINE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $orderDeadline;
    }
    public function getDeleteOrder()
    {
        $orderDelete =  $this->_scopeConfig->getValue(self::CONFIG_DELETE_ORDER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $orderDelete;
    }
    public function getRemoveItem()
    {
        $orderRemove =  $this->_scopeConfig->getValue(self::CONFIG_REMOVE_ITEM, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $orderRemove;
    }
}
