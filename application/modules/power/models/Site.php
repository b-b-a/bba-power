<?php
/**
 * Site.php
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
 * DAO to represent a single Site.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Site extends BBA_Model_Abstract
{
    /**
     * @var int
     */
    protected $_idClient;

    /**
     * @var int
     */
    protected $_idAddress;

    /**
     * @var int
     */
    protected $_idAddressBill;

    /**
     * @var int
     */
    protected $_idClientContact;

    /**
     * @var string
     */
    protected $_prefix = 'site_';

    /**
     * Gets the client Id
     *
     * @return int clientId
     */
    public function getIdClient()
    {
        return $this->_idClient;
    }

    /**
     * Sets the client id
     *
     * @param int $id
     * @return Power_Model_Site
     */
    public function setIdClient($id)
    {
        $this->_idClient = (int) $id;
        return $this;
    }

    /**
     * Gets the client address for this site.
     *
     * @return int clientAddressId
     */
    public function getIdAddress()
    {
        return $this->_IdAddress;
    }

    /**
     * Sets client address for this site.
     *
     * @param int $id
     * @return Power_Model_Site
     */
    public function setIdAddress($id)
    {
        $this->_idAddress = (int) $id;
        return $this;
    }

    /**
     * Gets the client address id for billing
     *
     * @return int siteClientAddressIdBill
     */
    public function getIdAddressBill()
    {
        return $this->_idAddressBill;
    }

    /**
     * Sets the client address id for billing
     * @param int $id
     * @return Power_Model_Site
     */
    public function setIdAddressBill($id)
    {
        $this->_idAddressBill = (int) $id;
        return $this;
    }

    /**
     * Gets the client contact id.
     *
     * @return int clientContactId
     */
    public function getIdClientContact()
    {
        return $this->_idClientContact;
    }

    /**
     * Sets the client contact id.
     *
     * @param int $id
     * @return Power_Model_Site
     */
    public function setIdClientContact($id)
    {
        $this->_idClientContact = (int) $id;
        return $this;
    }
}
