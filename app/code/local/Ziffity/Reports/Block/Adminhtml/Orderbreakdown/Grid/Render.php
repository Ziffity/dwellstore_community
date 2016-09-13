<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento End User License Agreement
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magento.com/license/enterprise-edition
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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license http://www.magento.com/license/enterprise-edition
 */

/**
 * Adminhtml grid item renderer date
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Ziffity_Reports_Block_Adminhtml_Orderbreakdown_Grid_Render
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Date
{
    /**
     * Retrieve date format
     *
     * @return string
     */
    protected function _getFormat()
    {
        $format = $this->getColumn()->getFormat();
        if (!$format) {
            if (is_null(self::$_format)) {
                try {
                    $localeCode = Mage::app()->getLocale()->getLocaleCode();
                    $localeData = new Zend_Locale_Data;
                    switch ($this->getColumn()->getPeriodType()) {
                        case 'month' :
                            self::$_format = $localeData->getContent($localeCode, 'dateitem', 'yM');
                            break;

                        case 'year' :
                            self::$_format = $localeData->getContent($localeCode, 'dateitem', 'y');
                            break;

                        default:
                            self::$_format = Mage::app()->getLocale()->getDateFormat(
                                Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
                            );
                            break;
                    }
                }
                catch (Exception $e) {

                }
            }
            $format = self::$_format;
        }
        return $format;
    }
	
	
	function date_convert($dt, $tz1, $df1, $tz2, $df2) {
  $res = '';
  if(!in_array($tz1, timezone_identifiers_list())) { // check source timezone
    trigger_error(__FUNCTION__ . ': Invalid source timezone ' . $tz1, E_USER_ERROR);
  } elseif(!in_array($tz2, timezone_identifiers_list())) { // check destination timezone
    trigger_error(__FUNCTION__ . ': Invalid destination timezone ' . $tz2, E_USER_ERROR);
  } else {
    // create DateTime object
    $d = DateTime::createFromFormat($df1, $dt, new DateTimeZone($tz1));
    // check source datetime
    if($d && DateTime::getLastErrors()["warning_count"] == 0 && DateTime::getLastErrors()["error_count"] == 0) {
      // convert timezone
      $d->setTimeZone(new DateTimeZone($tz2));
      // convert dateformat
      $res = $d->format($df2);
    } else {
      trigger_error(__FUNCTION__ . ': Invalid source datetime ' . $dt . ', ' . $df1, E_USER_ERROR);
    }
  }
  return $res;
}
	
	
	

    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
		
        if ($data = $row->getData($this->getColumn()->getIndex())) {
			
			$data=$this->date_convert($data, 'UTC', 'Y-m-d H:i:s', 'America/Los_Angeles', 'Y-m-d H:i:s');
			
            switch ($this->getColumn()->getPeriodType()) {
                case 'month' :
                    $dateFormat = 'yyyy-MM';
                    break;
                case 'year' :
                    $dateFormat = 'yyyy';
                    break;
                default:
                    $dateFormat = Varien_Date::DATE_INTERNAL_FORMAT;
                    break;
            }

            $format = $this->_getFormat();
            try {
                $data = ($this->getColumn()->getGmtoffset())
                    ? Mage::app()->getLocale()->date($data, $dateFormat)->toString($format)
                    : Mage::getSingleton('core/locale')->date($data, Zend_Date::ISO_8601, null, false)->toString($format);
            }
            catch (Exception $e) {
                $data = ($this->getColumn()->getTimezone())
                    ? Mage::app()->getLocale()->date($data, $dateFormat)->toString($format)
                    : Mage::getSingleton('core/locale')->date($data, $dateFormat, null, false)->toString($format);
            }
            return $data;
        }
        return $this->getColumn()->getDefault();
    }
}
