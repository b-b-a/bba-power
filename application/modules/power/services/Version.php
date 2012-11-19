<?php
/**
 * Version.php
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
 * @subpackage Service
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Store and retreives version number
 *
 * @category   BBA
 * @package    Power
 * @subpackage Service
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
final class Power_Service_Version
{
	/**
	 * Get current BBA_Power version
	 *
     * @param none
	 * @return string
	 */
	public static function getVersion()
	{
		return '4.0.1' . self::getEnvironment();
	}

    /**
     * Gets the current environment.
     *
     * $_SERVER['BBA_POWER_ENVIRONMENT'] is set in the apache2.conf
     * /etc/apache2/apache2.conf.
     * Also can be set in the .htaccess file.
     *
     * <code>
     * <IfModule env_module>
     *      SetEnv BBA_POWER_ENVIRONMENT "EP-Virtual-Machine-192"
     * </IfModule>
     * </code>
     *
     * @param none
     * @return string
     */
    public static function getEnvironment()
    {
        $string = '';

        if (isset($_SERVER['BBA_POWER_ENVIRONMENT'])) {
            $string = " (".$_SERVER['BBA_POWER_ENVIRONMENT'].")";
        //    $string .= "-(".ucwords($_ENV['APPLICATION_ENV']).")";
        //  This will confuse folk - if we start using the option it can be reinstated.
        }

        return $string;
    }
}
