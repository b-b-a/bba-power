<?php
/**
 * Abstract.php
 *
 * Copyright (c) 2011 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of ZendSF.
 *
 * ZendSF is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ZendSF is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ZendSF.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   ZendSF
 * @package    ZendSF
 * @subpackage Form
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Base class for forms.
 *
 * @category   ZendSF
 * @package    ZendSF
 * @subpackage Form
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
abstract class ZendSF_Form_Abstract extends Zend_Form
{
    /**
     * @var ZendSF_Model_Abstract
     */
    protected $_model;

    /**
     * Model setter
     *
     * @param ZendSF_Model_Abstract
     * @return none
     */
    public function setModel(ZendSF_Model_Abstract $model)
    {
        $this->_model = $model;
    }

    /**
     * Model Getter
     *
     * @return ZendSF_Model_Abstract
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * Excludes the email from validating against the database.
     *
     * @param string $email
     * @return ZendSF_Form_Abstract
     * @access public
     */
    public function excludeEmailFromValidation($element, $exclude)
    {
        $val = $this->getElement($element)
                ->getValidator('Db_NoRecordExists')
                ->setExclude($exclude);

        return $this;
    }
}
