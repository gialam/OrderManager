<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @category  Magenest
 */

namespace	Magenest\OrderManager\Block\Adminhtml;
class Order extends	\Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Magenest_OrderManager';
        $this->_controller = 'adminhtml_order';
//        $this->removeButton('add');
        parent::_construct();
        $this->removeButton('add');
    }
}