<?php
/**
 * SupplierContact.php
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
 * DAO to represent a single SupplierContact.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_SupplierContact extends BBA_Model_Abstract
{
    /**
     * @var int
     */
    protected $_idSupplierContract;

    /**
     * @var int
     */
    protected $_idSupplier;

    /**
     * @var string
     */
    protected $_name;

    /**
     * @var string
     */
    protected $_phone;

    /**
     * @var string
     */
    protected $_email;

    /**
     * @var string
     */
    protected $_address1;

    /**
     * @var string
     */
    protected $_address2;

    /**
     * @var string
     */
    protected $_address3;

    /**
     * @var string
     */
    protected $_postcode;

    /**
     * @var string
     */
    protected $_prefix = 'supplierCo_';
}