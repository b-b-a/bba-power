<?php
/**
 * Abstract.php
 *
 * Copyright (c) 2012 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of BBA Power.
 *
 * BBA Power is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * BBA Power is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BBA Power.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   BBA_Power
 * @package
 * @subpackage Form
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Abstract.
 *
 * @category   BBA_Power
 * @package
 * @subpackage Form
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
abstract class Power_Form_Doc_Abstract extends Zend_Form
{
    public function createFormElement($name, $label)
    {
        $fileTypeString = '.' . implode(', .', array_keys(Power_Model_Doc::$mimeMap));

        $this->addElement('file', $name, array(
            'label'         => 'New ' . $label . ' Doc:',
            'filters'       => array('StripTags', 'StringTrim'),
            'validators'    => array(
                array('Count', false, array(1)),
                array('Extension', false, array(
                    implode(',', array_keys(Power_Model_Doc::$mimeMap)),
                    'messages' => array(
                        Zend_Validate_File_Extension::FALSE_EXTENSION => "File '%value%' is not allowed. Only " . $fileTypeString . " types can be stored."
                    )
                ))
            ),
            'destination'   => realpath(APPLICATION_PATH . '/../bba-power-docs/' . $name),
            'required'      => false,
        	'Description'   => $label . ' File: ',
            'attribs'       => array(
                'data-dojo-type'    => 'dojox.form.Uploader',
                'data-dojo-id'      => $name,
                'data-dojo-props'   => "label: 'Choose'",
                'force'             => "iframe"
            ),
            'decorators'    => array(
            	'Description',
            	'File',
                'Errors',
                array(
                    array('filebutton' => 'HtmlTag'),
                    array(
                        'tag'   => 'p',
                        'class' => 'file-element'
                    ),
                ),
                array(
                    array('filename' => 'HtmlTag'),
                    array(
                        'tag'   => 'p',
                        'id'    => $name . '_file',
                        'class' => 'file-element'
                    )
                ),
            	array(
            		array('data' => 'HtmlTag'),
            		array(
            			'tag'   => 'td',
            		)
            	),
                array(
                    'Label',
                    array(
                    	'tag' 	=> 'th',
                    	'class' => 'label'
                    )
                ),
                array(
                    array('row' => 'HtmlTag'),
                    array(
                        'tag'   => 'tr',
                        'class' => 'file-form_row'
                    )
                )
            )
        ));
    }
}
