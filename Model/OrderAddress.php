<?php
/**
 * Created by PhpStorm.
 */
//Create	a	model	that	interacts	with	the	database	table
namespace Magenest\OrderManager\Model;

/**
 * Class OrderManage
 * @package Magenest\OrderManager\Model
 */
class OrderAddress extends \Magento\Framework\Model\AbstractModel
{

    public function _construct()
    {
        $this->_init('Magenest\OrderManager\Model\ResourceModel\OrderAddress');
    }
}
