<?php
/**
 * Service.php
 *
 * Copyright (c) 2010 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of ZendSF.
 *
 * ZendSF is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Uthando-CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ZendSF.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   ZendSF
 * @package    ZendSF
 * @subpackage Controller_Helper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman
 */

/**
 * Description of Uthando_Controller_Helper_Service
 *
 * @category   ZendSF
 * @package    ZendSF
 * @subpackage Controller_Helper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class ZendSF_Controller_Helper_Service extends Zend_Controller_Action_Helper_Abstract
{
    protected $_services = array();

    public function getService($service, $module)
    {
        if (!isset($this->_services[$module][$service])) {
            $class = implode('_', array(
                    ucfirst($module),
                    'Service',
                    ucfirst($service)
            ));

            $front = Zend_Controller_Front::getInstance();
            $classPath = $front->getModuleDirectory($module) .
                    '/services/' .
                    ucfirst($service) .
                    '.php';

            if (!file_exists($classPath)) {
                return false;
            }
            if (!class_exists($class)) {
                throw new ZendSF_Exception("Class $class not found in " . basename($classPath));
            }
            $this->_services[$module][$service] = new $class();
        }
	    return $this->_services[$module][$service];
    }

    public function direct($service, $module)
    {
        return $this->getService($service, $module);
    }
}
