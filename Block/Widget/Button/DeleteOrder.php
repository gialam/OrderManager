<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @category  Magenest
 */
namespace Magenest\OrderManager\Block\Widget\Button;

use Magento\Backend\Block\Widget\Button\Toolbar as ToolbarContext;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Backend\Block\Widget\Button\ButtonList;

class DeleteOrder
{


    /**
     * @param ToolbarContext $toolbar
     * @param AbstractBlock $context
     * @param ButtonList $buttonList
     * @return array
     */
    public function beforePushButtons(
        ToolbarContext $toolbar,

        \Magento\Framework\View\Element\AbstractBlock $context,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
    ) {
        if (!$context instanceof \Magento\Sales\Block\Adminhtml\Order\Invoice\View) {
            return [$context, $buttonList];
        }

        $buttonList->add('delete_order',
            [
                'label' => __('Delete'),
                'onclick' => 'setLocation(\'' . $context->getUrl('ordermanager/order/delete') . '\')',
                'class' => 'delete_order'
            ]
        );

        return [$context, $buttonList];
    }
}