<?php
/**
 * ContractSite.php
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
 * DAO to represent a single ContractSite.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_ContractSite extends ZendSF_Model_Abstract
{
    /**
     * @var int
     */
    protected $_idContract;

    /**
     *
     * @var int
     */
    protected $_idSite;

    /**
     * @var string
     */
    protected $_prefix = 'contractSite_';

    /**
     * Gets the contract id.
     *
     * @return int
     */
    public function getIdContract()
    {
        return $this->_idContract;
    }

    /**
     * Sets the contract id.
     *
     * @param int $id
     * @return Power_Model_ContractSite
     */
    public function setIdContract($id)
    {
        $this->_idContract = (int) $id;
        return $this;
    }

    /**
     * Gets the site id.
     *
     * @return type
     */
    public function getIdSite()
    {
        return $this->_idSite;
    }

    /**
     * Sets the site id.
     *
     * @param int $id
     * @return Power_Model_ContractSite
     */
    public function setIdSite($id)
    {
        $this->_idSite = (int) $id;
        return $this;
    }
}
