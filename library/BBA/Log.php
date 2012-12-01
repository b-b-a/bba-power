<?php
/**
 * Log.php
 *
 * Copyright (c) 2012 Shaun Freeman <shaun@shaunfreeman.co.uk>.
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
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Log & Error handling class.
 *
 * @category   BBA
 * @package    BBA
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class BBA_Log
{
    /**
     * @var Zend_Application_Bootstrap_Bootstrap
     */
    public $bootstrap;

    /**
     * @var Zend_Log
     */
    protected $_logger;

    /**
     * Converts php error types into strings.
     *
     * @var array
     */
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

    /**
     * Contruct a default map of phpErrors to Zend_Log priorities.
     * Some of the errors are uncatchable, but are included for completeness.
     *
     * @var array
     */
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

    public static $bootstrapDB = false;

    /**
     * Construct method.
     *
     * @param Zend_Application_Bootstrap_Bootstrap $bootstrap
     * @param bool $bootstrapDB
     * @return none
     */
    public function __construct($bootstrap, $bootstrapDB=null)
    {
        if ($bootstrapDB) self::$bootstrapDB = $bootstrapDB;
        $this->bootstrap = $bootstrap;

        set_error_handler(array($this,'errorHandler'));

        $this->_initLogging()
            ->_initDbProfiler();
    }

    /**
     * Sets up Zend_Log and Firerbug writer.
     *
     * @return ZendSF_Log
     */
    protected function _initLogging()
    {
        $logger = new Zend_Log();
        $dbLog = new Zend_Log();

        if ('production' == $this->bootstrap->getEnvironment()) {
            $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH.'/../data/logs/app.log');
            $dbWriter = new Zend_Log_Writer_Stream(APPLICATION_PATH.'/../data/logs/app-db.log');
            $filter = new Zend_Log_Filter_Priority(Zend_Log::INFO);
            $logger->addFilter($filter);
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

        return $this;
    }

    /**
     * Sets the Database profiler for the application.
     *
     * @return none
     */
    protected function _initDbProfiler()
    {
        if ('production' !== $this->bootstrap->getEnvironment()) {
            if (self::$bootstrapDB) $this->bootstrap->bootstrap('db');
            $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
            $profiler->setEnabled(true);
            $db = (self::$bootstrapDB) ?
                $this->bootstrap->getPluginResource('db')->getDbAdapter() : Zend_Registry::get('db');
            $db->setProfiler($profiler);
        }
    }

    /**
     * Sets up custom error handler
     *
     * @link http://www.php.net/manual/en/function.set-error-handler.php Custom error handler
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @param array $errcontext
     * @return boolean
     */
    public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        if ('production' == $this->bootstrap->getEnvironment()) {
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
                array('Context', $errcontext)
            ));

            $this->_logger->table($errorMessage);
        }

        return true;
    }
}
