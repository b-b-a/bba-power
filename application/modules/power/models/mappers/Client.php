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
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Mapper Class for Client.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Mapper_Client extends BBA_Model_Mapper_Abstract
{
    /**
     * @var string the DbTable class name
     */
    protected $_dbTableClass = 'Power_Model_DbTable_Client';

    /**
     * @var sting the model class name
     */
    protected $_modelClass = 'Power_Model_Client';

    public function saveNewClient($form)
    {
        if (!$this->checkAcl('save')) {
            throw new ZendSF_Acl_Exception('saving clients is not allowed.');
        }

        $log = Zend_Registry::get('log');

        $this->getDbTable()->getAdapter()->beginTransaction();

        try {
            $form = $this->getForm($form)->getValues();
            /**
             * TODO: must add checking on each stage.
             */
            // save client first.
            $clientSave = $this->save($form);

            // then save client address.
            $clientAd = new Power_Model_Mapper_ClientAddress();
            $form['clientAd_addressName'] = 'Main';
            $form['clientAd_idClient'] = $clientSave;
            $clientAdSave = $clientAd->save($form);

            // now save client contact
            $clientCo = new Power_Model_Mapper_ClientContact();
            $form['clientCo_idClient'] = $clientSave;
            $form['clientCo_idAddress'] = $clientAdSave;
            $clientCoSave = $clientCo->save($form);

            // now update client with address and contact ids.
            $form['client_idClient'] = $clientSave;
            $form['client_idAddress'] = $clientAdSave;
            $form['client_idClientContact'] = $clientCoSave;
            $clientSave = $this->save($form);

            $this->getDbTable()->getAdapter()->commit();

            $save = 1;
        } catch (Exception $e) {
            $log->info($e);
            $this->getDbTable()->getAdapter()->rollBack();
            return 0;
        }

        return $save;
    }

    public function save($form)
    {
        if (!$this->checkAcl('save')) {
            throw new ZendSF_Acl_Exception('saving clients is not allowed.');
        }

        return parent::save($form);
    }

    /**
     * Deletes a single row in the database.
     * First we check wheather we are allowed then act according.
     *
     * @param int $id
     * @return int number of rows deleted
     */
    public function delete($id)
    {
        if (!$this->checkAcl('delete')) {
            throw new ZendSF_Acl_Exception('Deleting clients is not allowed.');
        }

        $where = $this->getDbTable()
            ->getAdapter()
            ->quoteInto('client_idClient = ?', $id);

        return parent::delete($where);
    }
}
