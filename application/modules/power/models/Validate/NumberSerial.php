<?php
/**
 * NumberSerial.php
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
 * @category   BBA
 * @package    Power
 * @subpackage Validate
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * NumberSerial Validation Model.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Validate
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Validate_NumberSerial extends Zend_Validate_Abstract
{
	/**
	 * @var string
	 */
    const NUMBER_SERIAL_EXISTS = 'numberSerialExists';
    
    const ZERO_NOT_ALLOWED = 'zeroNotAllowed';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NUMBER_SERIAL_EXISTS => 'This meter cannot be added as the meter Serial No "%value%" already exists.',
    	self::ZERO_NOT_ALLOWED => 'Serial No. cannot be all zeros.'
    );

    /**
     * constructor method
     * 
     * @param Power_Model_Meter $model
     */
    public function __construct(Power_Model_Meter $model)
    {
        $this->_model = $model;
    }

    /**
     * (non-PHPdoc)
     * @see Zend_Validate_Interface::isValid()
     */
    public function isValid($value, $context = null)
    {
    	$this->_setValue($value);
    	
    	$currentMeter = isset($context['meter_idMeter']) ?
            $this->_model->getMeterById($context['meter_idMeter']) : null;
    	
    	$meter = $this->_model->getMeterByNumberSerial($value, $currentMeter);
    	
    	if (null === $meter) {
    		
    		if (preg_match('/^0{1,}$/', $value)) {
    			$this->_error(self::ZERO_NOT_ALLOWED);
    			return false;
    		}
    		
    		return true;
    	}
    	
    	$this->_error(self::NUMBER_SERIAL_EXISTS);
    	return false;
    }
        
}
