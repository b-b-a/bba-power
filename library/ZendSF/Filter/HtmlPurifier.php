<?php
/**
 * HtmlPurifier.php
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
 * @subpackage Filter
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Uses HtmlPurifier to make text inputs safe.
 *
 * @category   ZendSF
 * @package    ZendSF
 * @subpackage Filter
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class ZendSF_Filter_HtmlPurifier implements Zend_Filter_Interface
{
    /**
     * @var HTMLPurifier The HTMLPurifier instance
     */
    protected $_instance;

    /**
     * Constructor
     *
     * @param mixed $config
     */
    public function __construct($config = null)
    {
        $config['Cache.SerializerPath'] = APPLICATION_PATH . '/../data/cache';

        require_once APPLICATION_PATH . '/../library/htmlpurifier/HTMLPurifier.includes.php';
		require_once APPLICATION_PATH . '/../library/htmlpurifier/HTMLPurifier.auto.php';

        $this->_instance = new HTMLPurifier($config);
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the string $value, purified by HTMLPurifier
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        return $this->_instance->purify($value);
    }
}
