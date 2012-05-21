<?php
/**
 * Save.php
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
 * Form Class Save.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Client
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Client_Save extends ZendSF_Dojo_Form_Abstract
{
    protected $_simpleTextareaDecorators = array(
        'DijitElement',
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
                'class' => 'form_row simple-textarea'
            )
        )
    );


    public function init()
    {
        $this->setName('client');

        $this->setAttrib('enctype', 'multipart/form-data');

        $this->addHiddenElement('client_idClient', '');

        $this->addElement('ValidationTextBox', 'client_name', array(
            'label'         => 'Client Name:',
            'required'      => true,
            'filters'       => array('StripTags', 'StringTrim'),
            'validators'    => array(
                //array('Alnum', true, array('allowWhiteSpace' => true)),
                array('StringLength', true, array('max' => 64))
            ),
            'dijitParams'   => array(
                'promptMessage' => 'Enter the clients name.'
            )

        ));

        $request = Zend_Controller_Front::getInstance()->getRequest();

        if ($request->getPost('type') === 'edit') {

            $list = $this->getModel()->getClientAddressesByClientId(
                $request->getPost('client_idClient')
            );

            // reset options
            $multiOptions = array(0 => ($list->count() > 0) ? 'Please Select An Address' : 'No Addresses Available');

            foreach($list as $row) {
                $multiOptions[$row->clientAd_idAddress] = $row->getAddress1AndPostcode();
            }

            $this->addElement('FilteringSelect', 'client_idAddress', array(
                'label'         => 'Main Address:',
                'filters'       => array('StripTags', 'StringTrim'),
                'atuocomplete'  => false,
                'multiOptions'  => $multiOptions,
                'required'      => true,
                'validators'    => array(
                    array('GreaterThan', true, array(
                        'min'       => '0',
                        'message'   => 'Please select an address.'
                    ))
                ),
                'ErrorMessages' => array('Please select an address.'),
                'dijitParams'   => array(
                    'promptMessage' => 'Choose a client address.'
                )
            ));

            $list = $this->getModel()->getClientContactsByClientId(
                $request->getPost('client_idClient')
            );

            // reset options
            $multiOptions = array(0 => ($list->count() > 0) ? 'Please Select Someone' : 'No Client Personnel Available');
            foreach($list as $row) {
                $multiOptions[$row->clientCo_idClientContact] = $row->clientCo_name;
            }

            $this->addElement('FilteringSelect', 'client_idClientContact', array(
                'label'         => 'Main Liaison:',
                'filters'       => array('StripTags', 'StringTrim'),
                'atuocomplete'  => false,
                'multiOptions'  => $multiOptions,
                'required'      => false,
                'dijitParams'   => array(
                    'promptMessage' => 'Choose a client contact.'
                )
            ));
        }

        $this->addElement('file','client_docLoa', array(
            'label'         => 'Upload LoA:',
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
                'data-dojo-props'   => "isDebug:true, label: 'Choose LoA File', showInput: 'before'",
                'force'             => "iframe"
            )
        ));

        $this->addElement('ValidationTextBox', 'client_dateExpiryLoa', array(
            'label'         => 'LoA Expiry Date:',
            'formatLength'  => 'short',
            'filters'       => array('StripTags', 'StringTrim'),
            'validators'    => array(
                array('Date', true, array(
                    'format' => 'dd/MM/yyyy'
                ))
            ),
            'required'      => false,
            'dijitParams'   => array(
                'promptMessage' => 'Enter the date that the letter of authority expires.'
            ),
            'attribs'       => array('style' => 'width: 80px;')
        ));

        $this->addElement('ZendSFDojoSimpleTextarea', 'client_desc', array(
            'label'         => 'Description:',
            'required'      => false,
            'filters'       => array('StripTags', 'StringTrim'),
            'decorators'    => $this->_simpleTextareaDecorators
        ));
    }
}
