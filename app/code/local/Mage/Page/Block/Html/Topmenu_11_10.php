<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 * @package     Mage_Page
 * @copyright   Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Top menu block
 *
 * @category    Mage
 * @package     Mage_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Page_Block_Html_Topmenu extends Mage_Core_Block_Template
{
    /**
     * Top menu data tree
     *
     * @var Varien_Data_Tree_Node
     */
    protected $_menu;

    /**
     * Init top menu tree structure
     */
    public function _construct()
    {
        $this->_menu = new Varien_Data_Tree_Node(array(), 'root', new Varien_Data_Tree());
    }

    /**
     * Get top menu html
     *
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @return string
     */
    public function getHtml($outermostClass = '', $childrenWrapClass = '')
    {
        Mage::dispatchEvent('page_block_html_topmenu_gethtml_before', array(
            'menu' => $this->_menu
        ));

        $this->_menu->setOutermostClass($outermostClass);
        $this->_menu->setChildrenWrapClass($childrenWrapClass);

        $html = $this->_getHtml($this->_menu, $childrenWrapClass);

        Mage::dispatchEvent('page_block_html_topmenu_gethtml_after', array(
            'menu' => $this->_menu,
            'html' => $html
        ));

        return $html;
    }

    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param Varien_Data_Tree_Node $menuTree
     * @param string $childrenWrapClass
     * @return string
     */
    protected function _getHtml(Varien_Data_Tree_Node $menuTree, $childrenWrapClass)
    {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = is_null($parentLevel) ? 0 : $parentLevel + 1;

        $counter = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        foreach ($children as $child) {

            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }
			/*$html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';*/
			/* KD Custom Code */
			if($childLevel>0 && $childrenCount>10)
			{
				$html .= '<li ' . $this->_getRenderedMenuItemAttributes($child);
				$html .= ' style="float:left;width:170px;"';
				/*if($childrenCount%10==0)
					$html .= ' style="width:'.(($childrenCount/10)*200).'px;float:left;"';
				else
					$html .= ' style="width:'.((($childrenCount/10)+1)*200).'px;float:left;"';*/
				$html .= '>';
			}
			else
			{
				$html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
			}
            /* KD Custom Code */			
            $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>'. $this->escapeHtml($child->getName()). '</span></a>';

            if ($child->hasChildren()) {
                if (!empty($childrenWrapClass)) {
                    $html .= '<div class="' . $childrenWrapClass . '"> ';
                }
				/*$html .= '<ul class="level' . $childLevel . '">';
                $html .= $this->_getHtml($child, $childrenWrapClass);
                $html .= '</ul>';*/
				
				/* KD Custom Code */
                $html .= '<ul class="level' . $childLevel .'"';
				$childChildren = $child->getChildren();
				$childChildrenCount = $childChildren->count();
				
				if($childLevel>0)
				{
					$html .= ' style="position:absolute;';
				}
				//Calculate Width 
				
				if($childLevel>0 && $childChildrenCount>10)
				{				
					$html .= 'width:'.(ceil($childChildrenCount/10)*170).'px;';
				}
				
				//Calculate Top 
				if($childLevel>0 && $childrenCount>10)
				{
					$menu_row = ceil($childrenCount/10);
					if($menu_row<=0)$menu_row=1;

					if($counter%$menu_row==0)
					{
						$top = floor($counter/$menu_row);
					}
					else
					{
						$top = floor($counter/$menu_row)+1;
					}
					if($top>1)
						$html .= ' top:-'.((($top-1)*28)+6).'px !important ;"';
					else
						$html .= ' top:-6px !important ;"';
				}
				elseif($childLevel>0)
				{
					$top = $counter;
					//$html .= ' top:-'.((($top-1)*28)-6).'px !important ;"';
					if($top>1)
						$html .= ' top:-'.((($top-1)*28)+6).'px !important ;"';
					else
						$html .= ' top:-6px !important ;"';
				}
				$html .= '>';


                $html .= $this->_getHtml($child, $childrenWrapClass);
                $html .= '</ul>';
				/* KD Custom Code */
				
                if (!empty($childrenWrapClass)) {
                    $html .= '</div>';
                }
            }
            $html .= '</li>';
            $counter++;
        }

        return $html;
    }

    /**
     * Generates string with all attributes that should be present in menu item element
     *
     * @param Varien_Data_Tree_Node $item
     * @return string
     */
    protected function _getRenderedMenuItemAttributes(Varien_Data_Tree_Node $item)
    {
        $html = '';
        $attributes = $this->_getMenuItemAttributes($item);

        foreach ($attributes as $attributeName => $attributeValue) {
            $html .= ' ' . $attributeName . '="' . str_replace('"', '\"', $attributeValue) . '"';
        }

        return $html;
    }

    /**
     * Returns array of menu item's attributes
     *
     * @param Varien_Data_Tree_Node $item
     * @return array
     */
    protected function _getMenuItemAttributes(Varien_Data_Tree_Node $item)
    {
        $menuItemClasses = $this->_getMenuItemClasses($item);
        $attributes = array(
            'class' => implode(' ', $menuItemClasses)
        );

        return $attributes;
    }

    /**
     * Returns array of menu item's classes
     *
     * @param Varien_Data_Tree_Node $item
     * @return array
     */
    protected function _getMenuItemClasses(Varien_Data_Tree_Node $item)
    {
        $classes = array();

        $classes[] = 'level' . $item->getLevel();
        $classes[] = $item->getPositionClass();

        if ($item->getIsFirst()) {
            $classes[] = 'first';
        }

        if ($item->getIsActive()) {
            $classes[] = 'active';
        }

        if ($item->getIsLast()) {
            $classes[] = 'last';
        }

        if ($item->getClass()) {
            $classes[] = $item->getClass();
        }

        if ($item->hasChildren()) {
            $classes[] = 'parent';
        }

        return $classes;
    }
}
