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
 * @package    Power
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Bootstrap.
 *
 * @category   BBA
 * @package    Power
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
        if ('production' == $this->getEnvironment()) {
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
    }

    /**
     * Sets the logging for the application.
     */
    protected function _initLogging()
    {
        $this->bootstrap('frontController');
        $logger = new Zend_Log();

        $writer = 'production' == $this->getEnvironment() ?
            new Zend_Log_Writer_Stream(APPLICATION_PATH.'/../data/logs/app.log') :
            new Zend_Log_Writer_Firebug();
        $logger->addWriter($writer);

        if ('production' == $this->getEnvironment()) {
            $filter = new Zend_Log_Filter_Priority(Zend_Log::CRIT);
            $logger->addFilter($filter);
        }

        $this->_logger = $logger;
        Zend_Registry::set('log', $logger);
    }

    /**
     * Sets the Database profiler for the application.
     */
    protected function _initDbProfiler()
    {
        $this->_logger->info(__METHOD__);

        if ('production' !== $this->getEnvironment()) {
            $this->bootstrap('db');
            $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
            $profiler->setEnabled(true);
            $this->getPluginResource('db')
                 ->getDbAdapter()
                 ->setProfiler($profiler);
        }
    }

    /**
     * Caches database table information.
     */
    protected function _initDbCaches()
    {
        $this->_logger->info(__METHOD__);

        if ('production' == $this->getEnvironment()) {
            $frontendOptions = array(
                // set cache life time for 30 days
                'lifetime'                => 60*60*24*30,
                'automatic_serialization' => true
            );
            $backendOptions = array(
                'cache_dir' => APPLICATION_PATH . '/../data/cache'
            );

            $cache = Zend_Cache::factory(
                'Core',
                'File',
                $frontendOptions,
                $backendOptions
            );
            Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        }
    }

    /**
     * Setup default module, autoloader and resource loader.
     */
    protected function _initDefaultModuleAutoloader()
    {
        $this->_logger->info(__METHOD__);

        $this->_resourceLoader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Power',
            'basePath'  => APPLICATION_PATH . '/modules/power',
        ));
    }

    /**
     * Start session
     */
    public function _initCoreSession()
    {
        $this->_logger->info(__METHOD__);

        $this->bootstrap('db');
        $this->bootstrap('session');

        Zend_Session::start();
    }

    /**
     * Add Global Action Helpers
     */
    protected function _initActionHelpers()
    {
        $this->_logger->info(__METHOD__);

        Zend_Controller_Action_HelperBroker::addHelper(new ZendSF_Controller_Helper_Acl());
        Zend_Controller_Action_HelperBroker::addHelper(new ZendSF_Controller_Helper_Service());
    }

    /**
     * Sets up the helper paths for the application.
     */
    protected function _initGlobalViewHelperPath()
    {
        $this->_logger->info(__METHOD__);

        $this->bootstrap('view');

        $this->_view = $this->getResource('view');

        $this->_view->addHelperPath(
                APPLICATION_PATH . '/../library/ZendSF/View/Helper',
                'ZendSF_View_Helper'
        );
    }

    protected function _initConfig()
    {
        $this->_logger->info(__METHOD__);

        $options = new Zend_Config_Ini(APPLICATION_PATH . '/configs/options.ini');
        Zend_Registry::set('config', $options);
    }

    protected function _initViewSettings()
    {
        $this->_logger->info(__METHOD__);

        Zend_Dojo::enableView($this->_view);

        $this->_view->dojo()
            //->setCdnVersion('1.6.1')
            ->setLocalPath('/js/release/dojo/dojo.js')
            //->addStyleSheetModule('dijit.themes.claro')
            //->addLayer('/js/bba/bba.layer.js')
            ->disable();
        $this->_view->headLink()
            ->prependStylesheet('/js/release/dojo/dijit/themes/claro/claro.css')
            ->prependStylesheet('/js/release/dojo/dojox/grid/resources/Grid.css')
            ->prependStylesheet('/js/release/dojo/dojox/grid/resources/claroGrid.css');

        $this->_view->headTitle('BBA Power')->setSeparator(' - ');
    }

    protected function _initMenu()
    {
        $this->_logger->info(__METHOD__);

        $menu = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
        $this->_view->navigation(new Zend_Navigation($menu));
    }

}
