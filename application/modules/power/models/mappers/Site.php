<?php
/**
 * Sites.php
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
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Mapper Class for Sites.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Mapper_Site extends ZendSF_Model_Mapper_Acl_Abstract
{
    /**
     * @var Power_Model_DbTable_Sites
     */
    protected $_dbTableClass;

    /**
     * @var Power_Model_Sites
     */
    protected $_modelClass;

    public function getClientAndAddress()
    {
        $select = $this->getDbTable()
            ->select(false)
            ->setIntegrityCheck(false)
            ->from('site')
            ->join(
                'client_address',
                'clientAd_idAddress = site_idAddress',
                array('clientAd_addressName', 'clientAd_address1')
            )
            ->join(
                'client',
                'client_idClient = site_idClient ',
                array('client_name')
            )
            ;

        $resultSet = $this->fetchAll($select, true);

        foreach ($resultSet as $row) {
            /* @var $newRow Power_Model_Meter */
            $newRow = new $this->_modelClass($row);

            $newRow->setSiteAddress($row['clientAd_addressName']);
            $newRow->setClient($row['client_name']);

            $rows[] = $newRow;
        }

        return $rows;
    }

    /**
     * Injector for the acl, the acl can be injected directly
     * via this method.
     *
     * We add all the access rules for this resource here, so we first call
     * parent method to add $this as the resource then we
     * define it rules here.
     *
     * @param Zend_Acl_Resource_Interface $acl
     * @return ZendSF_Model_Mapper_Abstract
     */
    public function setAcl(Zend_Acl $acl)
    {
        parent::setAcl($acl);

        // implement rules here.

        return $this;
    }

}
