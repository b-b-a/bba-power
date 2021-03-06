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
 * @subpackage Form_Site
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Save.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Site
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Site_Add extends BBA_Dojo_Form_Abstract
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
        $this->setName('site');

        $this->addElement('FilteringSelect', 'site_idClient', array(
            'label' => 'Client:',
            'filters' => array('StripTags', 'StringTrim'),
            'autoComplete' => false,
            'hasDownArrow' => true,
            'storeId' => 'clientStore',
            //'storeType' => 'dojo.data.ItemFileReadStore',
            //'storeParams' => array('url' => "site/data-store/type/clients"),
            'dijitParams' => array(
                'searchAttr' => 'client_name',
                'promptMessage' => 'Select a Client'
            ),
            'attribs' => array(
                //'onChange' => 'bba.Site.changeAddress(this.value);'
            ),
            'required' => true,
            'value' => '0',
            'validators' => array(
                array('GreaterThan', true, array(
                    'min' => '0',
                    'message' => 'Please select a client.'
                ))
            ),
            'ErrorMessages' => array('Please select a client.'),
        ));

        $this->addElement('FilteringSelect', 'site_idAddress', array(
            'label' => 'Address:',
            'filters' => array('StripTags', 'StringTrim'),
            'autoComplete' => false,
            'hasDownArrow' => true,
            'storeId' => 'addressStore',
            'dijitParams' => array('searchAttr' => 'address1AndPostcode'),
            'attribs' => array(
                'disabled' => true,
                'onChange' => 'bba.Site.changeBillAddress(this);'
            ),
            'required' => true,
            'value' => '0',
            'validators' => array(
                array('GreaterThan', true, array(
                    'min' => '0',
                    'message' => 'Please select an address.'
                ))
            ),
            'ErrorMessages' => array('Please select an address.'),
        ));

        $this->addElement('FilteringSelect', 'site_idAddressBill', array(
            'label' => 'Billing Address:',
            'filters' => array('StripTags', 'StringTrim'),
            'autoComplete' => false,
            'hasDownArrow' => true,
            'storeId' => 'addressStore',
            'dijitParams' => array('searchAttr' => 'address1AndPostcode'),
            'attribs' => array('disabled' => true),
            'required' => false,
            'value' => '0'
        ));

        $this->addElement('FilteringSelect', 'site_idClientPersonnel', array(
            'label' => 'Client Liaison:',
            'filters' => array('StripTags', 'StringTrim'),
            'autoComplete' => false,
            'hasDownArrow' => true,
            'storeId' => 'personnelStore',
            'dijitParams' => array('searchAttr' => 'clientPers_name'),
            'attribs' => array(
                'disabled' => true,
                'onChange' => 'bba.Site.addPersonnel(this);'
            ),
            'required' => false,
            'value' => '0'
        ));

        $this->addElement('SimpleTextarea', 'site_desc', array(
            'label' => 'Description:',
            'required' => false,
            'filters' => array('StripTags', 'StringTrim'),
            'decorators' => $this->_simpleTextareaDecorators
        ));

        $this->addHiddenElement('site_idSite', '');
    }
}
