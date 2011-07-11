<?php
/**
 * Add.php
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
 * @subpackage Form_Meter
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Add.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Meter
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Meter_Add extends ZendSF_Form_Abstract
{
    public function init()
    {
        $this->setName('meter');

        $model = new Power_Model_Mapper_Site();
        $sites = $model->getClientAndAddress();

        $multiOptions = array(
            0 => 'Select Site'
        );

        /* @var $site Power_Model_Site */
        foreach ($sites as $site) {

            $multiOptions[$site->getClient()][] = $site->getSiteAddress();
        }

        $log = Zend_Registry::get('log');
        $log->info($multiOptions);

        $this->addElement('select', 'me_site_id', array(
            'validators'    => array(
                array('GreaterThan', true, array('min' => 0))
            ),
            'errorMessages'  => array('Please select a site for this meter.'),
            'label'         => 'Meter Site:',
            'MultiOptions'  => $multiOptions,
            'required'      => true
        ));

        $this->addElement('text', 'me_type', array(
            'label'     => 'Meter Type:',
            'required'  => true
        ));

        $this->addElement('text', 'me_no', array(
            'label'     => 'Meter No:',
            'required'  => true
        ));

        $this->addElement('text', 'me_date_install', array(
            'label'     => 'Date Installed:',
            'required'  => true
        ));

        $this->addElement('text', 'me_date_removed', array(
            'label'     => 'Date Removed:',
            'required'  => true
        ));

        $this->addSubmit('Add', 'submit');
        $this->addSubmit('Cancel', 'cancel');
    }

}
