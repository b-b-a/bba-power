<?php
/**
 * Doc.php
 *
 * Copyright (c) 2011 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of BBA.
 *
 * BBA is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * BBA is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BBA.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Client
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Doc.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Client
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Client_Doc extends ZendSF_Form_Abstract
{
    protected $_fileDecorators = array(
        'File',
        'Errors',
        'Description',
        array(
            array('data' => 'HtmlTag'),
            array(
                'tag' => 'p',
                'class' => 'element'
            )
        ),
        array(
            'Label',
            array('tag' => 'p')
        ),
        array(
            array('row' => 'HtmlTag'),
            array(
                'tag' => 'div',
                'class' => 'form_row'
            )
        )
    );

    public function init()
    {
        $this->setName('clientDocs');

        $this->addElement('file','client_docLoa', array(
            'label'         => 'New LoA Document (pdf):',
            'filters'       => array('StripTags', 'StringTrim'),
            'validators'    => array(
                array('Count', false, array(1)),
                array('Extension', false, array('pdf')),
            ),
            'destination'   => realpath(APPLICATION_PATH . '/../bba-power-docs/client_docLoa'),
            'decorators'    => $this->_fileDecorators,
            'required'      => false,
            'attribs'       => array(
                'data-dojo-type'    => 'dojox.form.Uploader',
                'data-dojo-id'      => 'client_docLoa',
                'data-dojo-props'   => "label: 'Choose LoA File'",
                'force'             => "iframe"
            )
        ));
    }

}
