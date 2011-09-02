<?php
/**
 * ClientContact.php
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
 * Mapper Class for ClientContact.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Mapper_ClientContact extends ZendSF_Model_Mapper_Acl_Abstract
{
    /**
     * @var string the DbTable class name
     */
    protected $_dbTableClass = 'Power_Model_DbTable_ClientContact';

    /**
     * @var sting the model class name
     */
    protected $_modelClass = 'Power_Model_ClientContact';

    public function getContactByClientId($id)
    {
        $select = $this->getDbTable()
            ->select(false)
            ->setIntegrityCheck(false)
            ->from('client_contact')
            ->join(
                'client_address',
                'clientCo_idAddress = clientAd_idAddress',
                array('postcode' => 'clientAd_postcode')
            )
            ->where('clientCo_idClient = ?', $id);

        return $this->fetchAll($select);
    }

    public function save()
    {
        if (!$this->checkAcl('save')) {
            throw new ZendSF_Acl_Exception('saving clients contacts is not allowed.');
        }

        $form = $this->getForm('clientContactSave')->getValues();

        // remove client contact id if not set.
        if (!$form['clientCo_idClientContact']) unset($form['clientCo_idClientContact']);

        $model = new Power_Model_ClientContact($form);

        // set modified and create dates.
        if ($form['returnAction'] == 'add') {
            $model->setDateCreate();
            $model->userCreate = $form['userId'];
        }

        // add modified date and by if updating record.
        if ($model->getId()) {
            $model->userModify = $form['userId'];
            $model->setDateModify();
        }

        return parent::save($model);
    }

    public function delete($id)
    {
        if (!$this->checkAcl('delete')) {
            throw new ZendSF_Acl_Exception('Deleting client contacts is not allowed.');
        }

        $where = $this->getDbTable()
            ->getAdapter()
            ->quoteInto('clientCo_idClientContact = ?', $id);

        return parent::delete($where);
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
        $this->_acl->allow('admin', $this)
            ->deny('admin', $this, array('delete'));

        return $this;
    }

}
