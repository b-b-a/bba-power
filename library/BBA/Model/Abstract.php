<?php
/**
 * Abstract.php
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
 * @package    BBA
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * DAO to represent a single Abstract.
 *
 * @category   BBA
 * @package    BBA
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class BBA_Model_Abstract extends ZendSF_Model_Abstract
{
    /**
     *
     * @var int
     */
    protected $_id;

    /**
     * @var int
     */
    protected $_userCreate;

    /**
     * @var Zend_Date
     */
    protected $_dateCreate;

    /**
     * @var int
     */
    protected $_userModify;

    /**
     * @var Zend_Date
     */
    protected $_dateModify;

    /**
     * @var string
     */
    protected $_dateFormat = 'yyyy-MM-dd';

    /**
     * Gets the model id
     *
     * @return type
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Sets the model id
     *
     * @param type $id
     * @return _Model_Abstract
     */
    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }

    /**
     * Gets the user id of the user who created this record.
     *
     * @return int
     */
    public function getUserCreate()
    {
        return $this->_userCreate;
    }

    /**
     * Sets the user id of the user who created this record.
     *
     * @param int $id
     * @return Power_Model_Abstract
     */
    public function setUserCreate($id)
    {
        $this->_userCreate = (int) $id;
        return $this;
    }

    /**
     * Gets the create date of this record.
     *
     * @return Zend_Date
     */
    public function getDateCreate()
    {
        return $this->_dateCreate;
    }

    /**
     * Sets the create date for this record.
     *
     * @param string $date
     * @return Power_Model_Abstract
     */
    public function setDateCreate($date)
    {
        $this->_dateCreate = new Zend_Date($date);
        return $this;
    }

    /**
     * Gets the user id of who modified this record.
     *
     * @return int
     */
    public function getUserModify()
    {
        return $this->_userModify;
    }

    /**
     * Sets the user id of who modified this record.
     *
     * @param int $id
     * @return Power_Model_Abstract
     */
    public function setUserModify($id)
    {
        $this->_userModify = (int) $id;
        return $this;
    }

    /**
     * Gets the modified date
     *
     * @return Zend_Date
     */
    public function getDateModify()
    {
        return $this->_dateModify;
    }

    /**
     * Sets the modified date
     *
     * @param string $date
     * @return Power_Model_Abstract
     */
    public function setDateModify($date)
    {
        $this->_dateModify = new Zend_Date($date);
        return $this;
    }

}