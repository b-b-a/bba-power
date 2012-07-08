<?php
/**
 * Abstract.php
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
 * @package    BBA
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Abstract Model.
 *
 * @category   BBA
 * @package    BBA
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
abstract class BBA_Model_DbTable_Abstract extends ZendSF_Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_rowPrefix = '';

    /**
     * @var array
     */
    protected $_nullAllowed = array();

    public function init() {
        parent::init();
        
        $this->_rowPrefix = strstr($this->_primary, '_', true);
    }

    protected function _checkConstraints($data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->_nullAllowed) && ('0' == $value || '' == $value)) {
                $data[$key] = null;
            }
        }

        return $data;
    }

    public function insert(array $data)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data[$this->_rowPrefix . '_dateCreate'] = new Zend_Db_Expr('CURDATE()');
        $data[$this->_rowPrefix . '_userCreate'] = $auth->getId();

        $data = $this->_checkConstraints($data);

        $this->_log->info(Zend_Debug::dump($data, "\nINSERT: " . __CLASS__ . "\n", false));

        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data[$this->_rowPrefix . '_dateModify'] = new Zend_Db_Expr('CURDATE()');
        $data[$this->_rowPrefix . '_userModify'] = $auth->getId();

        $data = $this->_checkConstraints($data);

        $this->_log->info(Zend_Debug::dump($data, "\nUPDATE: " . __CLASS__ . "\n", false));

        return parent::update($data, $where);
    }

    public function delete($where)
    {
        $this->_log->info(Zend_Debug::dump($where, "DELETE: " . __CLASS__, false));

        return parent::delete($where);
    }

    protected function _stripSpacesAndHyphens($subject)
    {
        $filter = new Zend_Filter_PregReplace(array(
                'match' => '/\s+|-+/',
                'replace' => ''
            )
        );

        return $filter->filter($subject);
    }
}