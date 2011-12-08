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
 * @subpackage Form_Tender
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Save.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Tender
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Tender_Save extends ZendSF_Form_Abstract
{
    public function init()
    {
        $this->addElement('FilteringSelect', 'tender_idSupplier', array(
            'label'         => 'Supplier:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autoComplete'  => false,
            'hasDownArrow'  => true,
            'storeId'       => 'supplierStore',
            'storeType'     => 'dojo.data.ItemFileReadStore',
            'storeParams'   => array('url' => "/supplier/autocomplete/param/supplier"),
            'dijitParams'   => array('searchAttr' => 'supplier_name'),
            //'attribs'       => array('readonly' => true),
            'required'      => true
        ));

        /*
        $this->addElement('FilteringSelect', 'tender_idSupplierContact', array(
            'label'         => 'Supplier Contact:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autoComplete'  => false,
            'hasDownArrow'  => true,
            'storeId'       => 'supplierStore',
            'storeType'     => 'dojo.data.ItemFileReadStore',
            'storeParams'   => array('url' => "/supplier/autocomplete/param/supplier"),
            'dijitParams'   => array('searchAttr' => 'supplier_name'),
            //'attribs'       => array('readonly' => true),
            'required'      => true
        ));*/

        $this->addElement('NumberSpinner', 'tender_periodContract', array(
            'label'     => 'Tender Period:',
            'min'       => 0,
            'required'  => true,
            //'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('TextBox', 'tender_dateExpiresQuote', array(
            'label'         => 'Expiry Date:',
            'formatLength'  => 'short',
            'filters'       => array('StripTags', 'StringTrim'),
            'validators'    => array(
                array('Date', true, array(
                    'format' => 'dd/MM/yyyy'
                ))
            ),
            'required'      => true
        ));

        $this->addElement('NumberSpinner', 'tender_chargeStanding', array(
            'label'         => 'Standing Charge:',
            'smallDelta'    => 0.01,
            'constraints'   => array(
                'places'    => 2,
                'min'       => 0
            ),
            'required'  => true,
            //'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('NumberSpinner', 'tender_priceUnitDay', array(
            'label'         => 'Unit Price Day:',
            'smallDelta'    => 0.01,
            'constraints'   => array(
                'places'    => 2,
                'min'       => 0
            ),
            'required'  => true,
            //'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('NumberSpinner', 'tender_priceUnitNight', array(
            'label'         => 'Unit Price Night:',
            'smallDelta'    => 0.01,
            'constraints'   => array(
                'places'    => 2,
                'min'       => 0
            ),
            'required'  => true,
            //'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('NumberSpinner', 'tender_priceUnitOther', array(
            'label'         => 'Unit Price Other:',
            'smallDelta'    => 0.01,
            'constraints'   => array(
                'places'    => 2,
                'min'       => 0
            ),
            'required'  => true,
            //'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('NumberSpinner', 'tender_chargeCapacity', array(
            'label'         => 'Charge Capacity:',
            'smallDelta'    => 0.01,
            'constraints'   => array(
                'places'    => 2,
                'min'       => 0
            ),
            'required'  => true,
            //'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('NumberSpinner', 'tender_chargeKva', array(
            'label'         => 'Charge KVA:',
            'smallDelta'    => 0.01,
            'constraints'   => array(
                'places'    => 2,
                'min'       => 0
            ),
            'required'  => true,
            //'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('NumberSpinner', 'tender_commission', array(
            'label'         => 'Commission:',
            'smallDelta'    => 0.01,
            'constraints'   => array(
                'places'    => 2,
                'min'       => 0
            ),
            'required'  => true,
            //'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addHiddenElement('tender_idTender', '');
        $this->addHiddenElement('tender_idContract', '');
        
    }

}
