<?php
/**
 * DojoCheckBox.php
 *
 * Copyright (c) 2011 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of ZendSF.
 *
 * ZendSF is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ZendSF is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ZendSF.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   ZendSF
 * @package    ZendSF_Dojo
 * @subpackage View
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Dojo CheckBox dijit
 *
 * @category   ZendSF
 * @package    ZendSF_Dojo
 * @subpackage View
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class ZendSF_Dojo_View_Helper_ZendSFDojoCheckBox extends Zend_Dojo_View_Helper_CheckBox
{
    public function zendSFDojoCheckBox($id, $value = null, array $params = array(), array $attribs = array(), array $checkedOptions = null)
    {
        return $this->checkBox($id, $value, $params, $attribs, $checkedOptions);
    }
    
    /**
     * Converts an associative array to a string of tag attributes.
     *
     * @access public
     *
     * @param array $attribs From this array, each key-value pair is
     * converted to an attribute name and value.
     *
     * @return string The XHTML for the attributes.
     */
    protected function _htmlAttribs($attribs)
    {
    	if (isset($attribs['required']) && $attribs['required'] == 'false') {
    		unset($attribs['required']);
    	}
    	return parent::_htmlAttribs($attribs);
    }
}
