<?php
/**
 * Mercent
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category   Mercent
 * @package    Mercent_MercentAnalytics
 */


/**
 * Mercent Analytics module observer
 *
 * @category   Mercent
 * @package    Mercent_MercentAnalytics
 */
class Mercent_MercentAnalytics_Model_Observer
{
    public function order_success_page_view($observer)
    {
        $quoteId = Mage::getSingleton('checkout/session')->getLastQuoteId();
        $mercentanalyticsBlock = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('mercent_analytics');
        if ($quoteId && ($mercentanalyticsBlock instanceof Mage_Core_Block_Abstract)) {
            $quote = Mage::getModel('sales/quote')->load($quoteId);
            $mercentanalyticsBlock->setQuote($quote);
        }
    }
}