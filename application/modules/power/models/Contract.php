<?php
/**
 * Contract.php
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
 * DAO to represent a single Contract.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Contract extends BBA_Model_Abstract
{
    /**
     * @var int
     */
    protected $_idContract;

    /**
     * @var int
     */
    protected $_idTenderSelected;

    /**
     * @var int
     */
    protected $_idSupplierContractSelected;

    /**
     * @var string
     */
    protected $_type;

    /**
     * @var string
     */
    protected $_status;

    /**
     * @var string
     */
    protected $_desc;

    /**
     * @var Zend_Date
     */
    protected $_dateStart;

    /**
     * @var Zend_Date
     */
    protected $_dateEnd;

    /**
     * @var string
     */
    protected $_txtTenderRequest;

    /**
     * @var string
     */
    protected $_docAnalysis;

    /**
     * @var string
     */
    protected $_docTermination;

    /**
     * @var int
     */
    protected $_periodBill;

    /**
     * @var int
     */
    protected $_periodCommission;

    /**
     * @var int
     */
    protected $_idUserAgent;

    /**
     * @var string
     */
    protected $_prefix = 'contract_';
}