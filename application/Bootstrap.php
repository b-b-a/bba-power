<?php
/**
 * Bootstrap.php
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
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Bootstrap.
 *
 * @category   BBA
 * @package    BBA
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * @var Zend_Log
     */
    protected $_logger;

    /**
     * @var Zend_Application_Module_Autoloader
     */
    protected $_resourceLoader;

    /**
     * @var Zend_View
     */
    protected $_view;

    /**
     * @var Zend_Controller_Front
     */
    public $frontController;

    /**
     * Adds a cache to production environment for plugin loader.
     */
    protected function _initPluginLoaderCache()
    {
        $classFileIncCache =
            APPLICATION_PATH .
            '/../data/cache/pluginLoaderCache.php';

        if (file_exists($classFileIncCache)) {
            include_once $classFileIncCache;
        }

        Zend_Loader_PluginLoader::setIncludeFileCache(
            $classFileIncCache
        );
    }

    /**
     * Set up Default DataBase Adapter.
     */
    protected function _initDb()
    {
        $dbOptions =  new Zend_Config_Ini(APPLICATION_PATH . '/configs/db.ini');

        $db = Zend_Db::factory(
            $dbOptions->resources->db
        );
        Zend_Db_Table::setDefaultAdapter($db);

        Zend_Registry::set('db', $db);
    }

    /**
     * Sets the logging for the application.
     */
    protected function _initLogging()
    {
        $log = new ZendSF_Log($this);
        $this->_logger = Zend_Registry::get('log');
        return $log;
    }

    /**
     * Caches database table information.
     */
    protected function _initDbCaches()
    {
        if ('production' == $this->getEnvironment()) {
            $cache = Zend_Cache::factory('Core', 'File',
                array(
                    'lifetime'                => 60*60*24*1,
                    'automatic_serialization' => true
                ),
                array('cache_dir' => APPLICATION_PATH . '/../data/cache')
            );
            Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        }
    }

    /**
     * Add Global Action Helpers
     */
    protected function _initActionHelpers()
    {
        Zend_Controller_Action_HelperBroker::addHelper(new ZendSF_Controller_Helper_Acl());
        Zend_Controller_Action_HelperBroker::addHelper(new ZendSF_Controller_Helper_Service());
    }

    /**
     * Sets up the helper paths for the application.
     */
    protected function _initGlobalViewHelperPath()
    {
        $this->bootstrap('view');

        $this->_view = $this->getResource('view');

        $this->_view->addHelperPath('ZendSF/View/Helper', 'ZendSF_View_Helper');
    }

    /**
     * Sets up and adds options config file to the registry.
     */
    protected function _initConfig()
    {
        $options = new Zend_Config_Ini(APPLICATION_PATH . '/configs/options.ini');
        Zend_Registry::set('config', $options);
    }

    /**
     * sets up the View with dojo enabled.
     */
    protected function _initViewSettings()
    {
        Zend_Dojo::enableView($this->_view);
        Zend_Dojo_View_Helper_Dojo::setUseDeclarative();

        $this->_view->dojo()
            ->setDjConfig(array(
                'parseOnLoad'   => true,
                'async'         => true
            ))
            ->addStyleSheetModule('dijit.themes.claro')
            ->setRenderModules(false);

        if ('development' !== $this->getEnvironment()) {
            $this->_view->dojo()
                ->setLocalPath('js/release/bba/dojo/dojo.js')
                ->setDjConfigOption('packages', array(
                    array(
                        'location'  => "../../../bba",
                        'name'      => "bba"
                    )
                ));
            $this->_view->headLink()
                ->appendStylesheet('js/release/bba/dojox/grid/resources/Grid.css')
                ->appendStylesheet('js/release/bba/dojox/grid/resources/claroGrid.css')
                //->appendStylesheet('js/release/bba/dojox/form/resources/UploaderFileList.css')
                ->appendStylesheet('js/release/bba/dojox/widget/Wizard/Wizard.css');
        } else {
            $this->_view->dojo()
                ->setLocalPath('/library/dojo/dojo/dojo.js')
                ->setDjConfigOption('packages', array(
                    array(
                        'location'  => "/bba-power/public/js/bba",
                        'name'      => "bba"
                    )
                ));
            $this->_view->headLink()
                ->appendStylesheet('/library/dojo/dojox/grid/resources/Grid.css')
                ->appendStylesheet('/library/dojo/dojox/grid/resources/claroGrid.css')
                //->appendStylesheet('/library/dojo/dojox/form/resources/UploaderFileList.css')
                ->appendStylesheet('/library/dojo/dojox/widget/Wizard/Wizard.css');
        }

        $this->_view->headTitle('BBA Power')->setSeparator(' - ');
    }

    /**
     * Sets up the main menu and adds it to the view navigation container.
     */
    protected function _initMenu()
    {
        $menu = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
        $this->_view->navigation(new Zend_Navigation($menu));
    }

}
