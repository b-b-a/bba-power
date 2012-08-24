<?php
/**
 * NumberTop.php
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
 * NumberTop Validation Model.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Validate
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Validate_NumberTop extends Zend_Validate_Abstract
{
	/**
	 * @var string
	 */
    const METER_WATER_ERROR = 'waterNotAllowed';
    
    const METER_GAS_ERROR = 'gasNotAllowed';
    
    const NUMBER_TOP_INVALID = 'numberTopInvalid';
    
    const LINE_LOSS_ERROR = 'lineLossInvalid';
    
    const ZERO_NOT_ALLOWED = 'zeroNotAllowed';
    
    protected $_validLineLoss = array(
    	'00', '01', '02', '03', '04', '05', '06', '07', '08'
    );

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::METER_WATER_ERROR => 'This meter cannot be added as a Top No. has been entered. (Top No. is not allowed for Water meters.)',
    	self::METER_GAS_ERROR => 'This meter cannot be added as a Top No. has been entered. (Top No. is not allowed for Gas meters.)',
    	self::NUMBER_TOP_INVALID => 'The Top No. must have eight numbers.',
    	self::LINE_LOSS_ERROR => 'The first two digits of the Top No. are invalid. The Top No. must be one of these ("00", "01", "02", "03", "04", "05", "06", "07", "08").',
    	self::ZERO_NOT_ALLOWED => 'Top No. cannot be all zeros.'
    );

    /**
     * (non-PHPdoc)
     * @see Zend_Validate_Interface::isValid()
     */
    public function isValid($value, $context = null)
    {
    	$this->_setValue($value);
    	
    	if ('water' === $context['meter_type'] && isset($context['meter_numberTop'])) {
    		$this->_error(self::METER_WATER_ERROR);
    		return false;
    	}
    	
    	if ('gas' === $context['meter_type'] && isset($context['meter_numberTop'])) {
    		$this->_error(self::METER_GAS_ERROR);
    		return false;
    	}
    	
    	if (!preg_match('/^\d{8}$/', $value)) {
    		$this->_error(self::NUMBER_TOP_INVALID);
    		return false;
    	}
    	
    	if (preg_match('/^0{8}$/', $value)) {
    		$this->_error(self::ZERO_NOT_ALLOWED);
    		return false;
    	}
    	
    	if (!in_array(substr($value, 0, 2), $this->_validLineLoss)) {
    		$this->_error(self::LINE_LOSS_ERROR);
    		return false;
    	}
    	
    	return true;
    }
        
}
