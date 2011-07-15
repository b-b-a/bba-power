<?php
/**
 * Tender.php
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
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * DAO to represent a single Tender.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Tender extends BBA_Model_Abstract
{
    /**
     * @var int
     */
    protected $_idTender;

    /**
     * @var int
     */
    protected $_idContract;

    /**
     * @var int
     */
    protected $_idSupplier;

    /**
     * @var int
     */
    protected $_idSupplierContact;

    /**
     *
     * @var int
     */
    protected $_periodContract;

    /**
     * @var Zend_Date
     */
    protected $_dateExpiresQuote;

    /**
     * @var string
     */
    protected $_txtResponse;

    /**
     * @var float
     */
    protected $_chargeStanding;

    /**
     * @var float
     */
    protected $_priceUnitDay;

    /**
     * @var float
     */
    protected $_priceUnitNight;

    /**
     * @var float
     */
    protected $_priceUnitOther;

    /**
     * @var float
     */
    protected $_chargeCapacity;

}