<?php
/**
 * NumberMain.php
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
 * NumberMain Validation Model.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Validate
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Validate_NumberMain extends Zend_Validate_Abstract
{
	/**
	 * @var string
	 */
    const NUMBER_MAIN_EXISTS = 'numberMainExists';
    
    const MPAN_INVALID = 'mpanInvalid';
    
    const METER_TYPE_ERROR = 'meterTypeError';
    
    const GAS_NUMBER_LENGTH = 'gasNumberError';
    
    const ZERO_NOT_ALLOWED = 'zeroNotAllowed';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NUMBER_MAIN_EXISTS => 'This meter cannot be added as the meter Main No "%value%" already exists.',
    	self::MPAN_INVALID => 'This meter number "%value%" is invalid.',
    	self::METER_TYPE_ERROR => 'This meter cannot be added as a Main No. has been entered. (Main No. is not allowed for Water meters.)',
    	self::GAS_NUMBER_LENGTH => 'Gas numbers must be between 6 and 10 digits long.',
    	self::ZERO_NOT_ALLOWED => 'Main No. cannot be all zeros.'
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
    
    public function checkMPAN($mpan)
    {
    	$primes = array(3, 5, 7, 13, 17, 19, 23, 29, 31, 37, 41, 43);
    	$sum = 0;
    	$mpan = str_split($mpan);
    	
    	if ((count($mpan) - 1) == count($primes)) {
    		for ($i = 0; $i < count($primes); $i++) {
    			$sum += $mpan[$i] * $primes[$i];
    		}
    		return (($sum % 11 % 10) == end($mpan)) ? true : false;
    	
    	} else {
    		return false;
    	}
    }

    /**
     * (non-PHPdoc)
     * @see Zend_Validate_Interface::isValid()
     */
    public function isValid($value, $context = null)
    {
        $this->_setValue($value);
        
        if ('water' === $context['meter_type'] && isset($context['meter_numberMain'])) {
        	$this->_error(self::METER_TYPE_ERROR);
        	return false;
        }

        $currentMeter = isset($context['meter_idMeter']) ?
            $this->_model->getMeterById($context['meter_idMeter']) : null;
        
        $meter = $this->_model->getMeterByNumberMain($value, $currentMeter);

        if (null === $meter) {
        	
        	if ('electric' === $context['meter_type'] && !$this->checkMPAN($value)) {
        		$this->_error(self::MPAN_INVALID);
        		return false;
        	}
        	
        	if ('gas' === $context['meter_type']) {
        		$numDigits = count(str_split($value));
        		
        		if (!preg_match('/^\d{6,10}$/', $value)) {
        			$this->_error(self::GAS_NUMBER_LENGTH);
        			return false;
        		}
        		
        		if (preg_match('/^0{6,10}$/', $value)) {
        			$this->_error(self::ZERO_NOT_ALLOWED);
        			return false;
        		}
        	}
        	
            return true;
        }

        $this->_error(self::NUMBER_MAIN_EXISTS);
        return false;
    }
}
