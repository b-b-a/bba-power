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
class Power_Form_Client_Save extends ZendSF_Form_Abstract
{
    public function init()
    {
        //$this->setName('client');

        $this->setAttrib('enctype', 'multipart/form-data');

        $this->addElement('ValidationTextBox', 'client_name', array(
            'label'     => 'Client Name:',
            'required'  => true,
            'filters'   => array('StripTags', 'StringTrim')
        ));

/*        $this->addElement('file','client_docLoa', array(
            'label' => 'Upload Letter of Authority:',
            'destination' => realpath(APPLICATION_PATH . '/../data/loa'),
            'validators' => array(
                array('Count', false, array(1)),
                array('Size', false, array(1048576*5)),
                array('Extension', false, array('pdf')),
            ),
            'decorators' => $this->_fileDecorators
        ));
*/


        $this->addElement('TextBox', 'client_dateExpiryLoa', array(
            'label'     => 'LoA Expiry Date:',
            'formatLength'   => 'short',
            'filters'   => array('StripTags', 'StringTrim'),
            'validators'    => array(
                array('Date', true, array(
                    'format' => 'dd/MM/yyyy'
                ))
            )
        ));

        $request = Zend_Controller_Front::getInstance()->getRequest();

        if ($request->getPost('type') === 'edit') {

            $list = $this->getModel()->getClientAddressesByClientId(
                $request->getPost('idClient')
            );

            // reset options
            $multiOptions = array(0 => '');
            foreach($list as $row) {
                $multiOptions[$row->clientAd_idAddress] = $row->getAddress1AndPostcode();
            }

            $this->addElement('FilteringSelect', 'client_idAddress', array(
                'label'     => 'Main Address:',
                'filters'   => array('StripTags', 'StringTrim'),
                'atuocomplete' => false,
                'multiOptions'  => $multiOptions,
                'required'  => true
            ));

            $list = $this->getModel()->getClientContactsByClientId(
                $request->getPost('idClient')
            );

            // reset options
            $multiOptions = array(0 => '');
            foreach($list as $row) {
                $multiOptions[$row->clientCo_idClientContact] = $row->clientCo_name;
            }

            $this->addElement('FilteringSelect', 'client_idClientContact', array(
                'label'     => 'Main Contact:',
                'filters'   => array('StripTags', 'StringTrim'),
                'atuocomplete' => false,
                'multiOptions'  => $multiOptions,
                'required'  => true
            ));
        }

        $this->addHiddenElement('client_idClient', '');
    }
}
