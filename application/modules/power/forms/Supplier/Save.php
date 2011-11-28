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
 * @subpackage Form_Supplier
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Save.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Supplier
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Supplier_Save extends ZendSF_Form_Abstract
{
    public function init()
    {
        $this->setName('supplier');

        $this->addElement('ValidationTextBox', 'supplier_name', array(
            'label'     => 'Supplier Name:',
            'required'  => true,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('ValidationTextBox', 'supplier_address1', array(
            'label'     => 'Address 1:',
            'required'  => true,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('ValidationTextBox', 'supplier_address2', array(
            'label'     => 'Address 2:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('ValidationTextBox', 'supplier_address3', array(
            'label'     => 'Address 3:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('ValidationTextBox', 'supplier_postcode', array(
            'label'         => 'Postcode:',
            'required'      => true,
            'filters'       => array('StripTags', 'StringTrim', 'StringToUpper'),
            'validators'    => array(
                array('PostCode', true, array(
                    'locale' => 'en_GB'
                ))
            )
        ));

        $view = $this->getView();
        $table = new Power_Model_Mapper_Supplier();

        if (isset($view->request['idSupplier'])) {

            $list = $table->getContactsBySupplierId(array(
                'supplier_idSupplier' => $view->request['idSupplier']
            ));

            // reset options
            $multiOptions = array(0 => '');
            foreach($list as $row) {
                $multiOptions[$row->idSupplierContact] = $row->getAddress1AndPostcode();
            }

            $this->addElement('FilteringSelect', 'supplier_idSupplierContact', array(
                'label'     => 'Address:',
                'filters'   => array('StripTags', 'StringTrim'),
                'atuocomplete' => false,
                'multiOptions'  => $multiOptions,
                'required'  => true
            ));
        }

        // supplier contact to do.

        $auth = Zend_Auth::getInstance()->getIdentity();

        $this->addHiddenElement('userId', $auth->getId());
        $this->addHiddenElement('supplier_idSupplier', '');
    }
}
