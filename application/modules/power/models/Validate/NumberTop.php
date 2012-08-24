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

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::METER_WATER_ERROR => 'This meter cannot be added as a Top No. has been entered. (Top No. is not allowed for Water meters.)',
    	self::METER_GAS_ERROR => 'This meter cannot be added as a Top No. has been entered. (Top No. is not allowed for Gas meters.)'
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
    	
    	if ('water' === $context['meter_type'] && isset($context['meter_numberTop'])) {
    		$this->_error(self::METER_WATER_ERROR);
    		return false;
    	}
    	
    	if ('gas' === $context['meter_type'] && isset($context['meter_numberTop'])) {
    		$this->_error(self::METER_GAS_ERROR);
    		return false;
    	}
    }
        
}
