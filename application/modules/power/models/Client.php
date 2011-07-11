<?php
/**
 * Client.php
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
 * DAO to represent a single Client.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Client extends BBA_Model_Abstract
{
    protected $_name;
    protected $_desc;
    protected $_docLoa;
    protected $_dateExpiryLoa;

    protected $_prefix = 'cl_';

    public function getName()
    {
        return $this->_name;
    }

    public function setName($text)
    {
        $this->_name = (string) $text;
        return $this;
    }

    public function getDesc()
    {
        return $this->_desc;
    }

    public function setDesc($text)
    {
        $this->_desc = (string) $text;
        return $this;
    }

    public function getDocLoa()
    {
        return $this->_docLoa;
    }

    public function setDocLoa($text)
    {
        $this->_docLoa = (string) $text;
        return $this;
    }

    public function getDateExpiryLoa()
    {
        return $this->_dateExpiryLoa;
    }

    public function setDateExpiryLoa($date)
    {
        $this->_dateExpiryLoa =  new Zend_Date($date);
        return $this;
    }
}
