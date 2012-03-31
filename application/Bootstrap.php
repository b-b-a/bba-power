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

    private $_errorType = array (
		E_ERROR				=> 'ERROR',
		E_WARNING			=> 'WARNING',
		E_PARSE				=> 'PARSING ERROR',
		E_NOTICE			=> 'NOTICE',
		E_CORE_ERROR		=> 'CORE ERROR',
		E_CORE_WARNING		=> 'CORE WARNING',
		E_COMPILE_ERROR		=> 'COMPILE ERROR',
		E_COMPILE_WARNING	=> 'COMPILE WARNING',
		E_USER_ERROR		=> 'USER ERROR',
		E_USER_WARNING		=> 'USER WARNING',
		E_USER_NOTICE		=> 'USER NOTICE',
		E_STRICT			=> 'STRICT NOTICE',
		E_RECOVERABLE_ERROR	=> 'RECOVERABLE ERROR',
		E_DEPRECATED		=> 'DEPRECATED',
		E_USER_DEPRECATED	=> 'USER DEPRECATED'
	);

    private $_errorHandlerMap = array (
		E_NOTICE            => Zend_Log::NOTICE,
        E_USER_NOTICE       => Zend_Log::NOTICE,
        E_WARNING           => Zend_Log::WARN,
        E_CORE_WARNING      => Zend_Log::WARN,
        E_USER_WARNING      => Zend_Log::WARN,
        E_ERROR             => Zend_Log::ERR,
        E_USER_ERROR        => Zend_Log::ERR,
        E_CORE_ERROR        => Zend_Log::ERR,
        E_RECOVERABLE_ERROR => Zend_Log::ERR,
        E_STRICT            => Zend_Log::DEBUG,
        E_DEPRECATED        => Zend_Log::DEBUG,
        E_USER_DEPRECATED   => Zend_Log::DEBUG
	);

    /**
     * EOL character
     */
    const EOL = "\n";

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
        $bbaErrorHandler = set_error_handler(array($this,'errorHandler'));
        $logger = new Zend_Log();
        $dbLog = new Zend_Log();

        if ('production' == $this->getEnvironment()) {
            $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH.'/../data/logs/bba-power.log');
            $dbWriter = new Zend_Log_Writer_Stream(APPLICATION_PATH.'/../data/logs/bba-power-db.log');
            $filter = new Zend_Log_Filter_Priority(Zend_Log::INFO);
            $logger->addFilter($filter);
            //$dbLog->addFilter($filter);
        } else {
            $writer = new Zend_Log_Writer_Firebug();
            $dbWriter = new Zend_Log_Writer_Firebug();
            $writer->setPriorityStyle(8, 'TABLE');
            $logger->addPriority('TABLE', 8);
        }

        $logger->addWriter($writer);
        $dbLog->addWriter($dbWriter);

        $this->_logger = $logger;
        Zend_Registry::set('log', $logger);
        Zend_Registry::set('dblog', $dbLog);
    }

    public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        if ('production' == $this->getEnvironment()) {
            if (isset($this->_errorHandlerMap[$errno])) {
                $priority = $this->_errorHandlerMap[$errno];
            } else {
                $priority = Zend_Log::INFO;
            }
            $errorMessage = self::EOL . 'Error ' . $this->_errorType[$errno] . self::EOL;
            $errorMessage .= 'ERROR NO : ' . $errno . self::EOL;
            $errorMessage .= 'TEXT : ' . $errstr . self::EOL;
            $errorMessage .= 'LOCATION : ' . $errfile . ' ' . $errline . self::EOL;
            $errorMessage .= 'DATE : ' . date('F j, Y, g:i a') . self::EOL;
            $errorMessage .= '------------------------------------' . self::EOL;
            $this->_logger->log($errorMessage, $priority);
        } else {
            $errorMessage = array('Error : ' . $this->_errorType[$errno], array(
                array('', ''),
                array('Error No', $errno),
                array('Message', $errstr),
                array('File Name', $errfile),
                array('Line No', $errline),
                //array('Context', $errcontext)
            ));

            $this->_logger->table($errorMessage);
        }

        return true;
    }

    /**
     * Sets the Database profiler for the application.
     */
    protected function _initDbProfiler()
    {
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
     * Setup default module, autoloader and resource loader.
     */
    protected function _initDefaultModuleAutoloader()
    {
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
        $this->bootstrap('db');
        $this->bootstrap('session');

        Zend_Session::start();
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

        $this->_view->addHelperPath(
                APPLICATION_PATH . '/../library/ZendSF/View/Helper',
                'ZendSF_View_Helper'
        );
    }

    protected function _initConfig()
    {
        $options = new Zend_Config_Ini(APPLICATION_PATH . '/configs/options.ini');
        Zend_Registry::set('config', $options);
    }

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
                ->appendStylesheet('js/release/bba/dojox/widget/Wizard/Wizard.css');
        } else {
            $this->_view->dojo()
                ->setLocalPath('js/dojo/dojo.js');
            $this->_view->headLink()
                ->appendStylesheet('js/dojox/grid/resources/Grid.css')
                ->appendStylesheet('js/dojox/grid/resources/claroGrid.css')
                ->appendStylesheet('js/dojox/widget/Wizard/Wizard.css');
        }

        $this->_view->headTitle('BBA Power')->setSeparator(' - ');
    }

    protected function _initMenu()
    {
        $menu = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
        $this->_view->navigation(new Zend_Navigation($menu));
    }

}
