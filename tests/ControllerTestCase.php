<?php

require_once 'Zend/Application.php';
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

class ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
    /**
     *
     * @var Zend_Application
     */
    public $application;

    /**
     *
     * @var Zend_Db_Adapter_Pdo_Sqlite
     */
    protected $_db;

    /**
     * Sets up application ready for testing.
     */
    protected function setUp()
    {
        $this->application = new Zend_Application(
            'testing',
            APPLICATION_PATH . '/configs/application.ini'
        );

        if (file_exists(APPLICATION_PATH . '/../tests/tmp/db/test.sqlite')) {
            unlink(APPLICATION_PATH . '/../tests/tmp/db/test.sqlite');
        }

        $this->setupDb();

        $_SERVER['SERVER_NAME'] = 'bba';
        $this->bootstrap = array($this, 'appBootstrap');

        parent::setUp();
    }

    /**
     * Reset all parameters.
     */
    protected function tearDown()
    {
        $this->resetRequest()->resetResponse();
        $this->request->setPost(array());
        $this->request->setQuery(array());
        Zend_Auth::getInstance()->clearIdentity();
    }

    /**
     * Bootstrap application.
     */
    protected function appBootstrap()
    {
        $this->application->bootstrap();
    }

    /**
     * Setup database.
     */
    protected function setupDb()
    {
        $this->_db = new Zend_Db_Adapter_Pdo_Sqlite(array(
            'dbname'   => APPLICATION_PATH . '/../tests/tmp/db/test.sqlite'
        ));

        $schemaSql = file_get_contents(APPLICATION_PATH . '/../scripts/db/sqlite/schema.sqlite.sql');
        $dataSql = file_get_contents(APPLICATION_PATH . '/../scripts/db/sqlite/data.sqlite.sql');

        $this->_db->exec($schemaSql);

        $this->_db->exec($dataSql);
    }

    /**
     * Gets to CRSF hash to validate login form.
     *
     * @return string $csrf
     */
    protected function getLoginCSRF()
    {
        $this->dispatch('/auth/login');
        $html = $this->getResponse()->getBody();

        // parse page content, find the hash value prefilled to the hidden element
        $dom = new Zend_Dom_Query($html);
        $csrf = $dom->query('#csrf')->current()->getAttribute('value');
        $this->tearDown();

        return $csrf;
    }

    /**
     * logs in a valid user.
     *
     * @param type $name
     * @param type $passwd
     */
    protected function login($name, $passwd)
    {
        $csrf = $this->getLoginCSRF();
        $this->request->setMethod('POST')
            ->setPost(array(
                'user_name'     => $name,
                'user_password' => $passwd,
                'csrf'          => $csrf
            ));

       $this->dispatch('/auth/authenticate');

       $this->assertRedirectTo('/meter');
       $this->resetRequest()->resetResponse();
    }

    /**
     * Gets a fake Identity for logged in tests.
     *
     * @param Zend_Auth
     */
    protected function doAuth( $identity = null )
    {
        if ( $identity === null ) {
            $identity = $this->generateFakeIdentity();
        }

        Zend_Auth::getInstance()->getStorage()->write( $identity );
    }

    /**
     * Creates a fake Identity.
     *
     * @return Zend_Auth
     */
    protected function generateFakeIdentity()
    {
        $identity = new stdClass();

        $user = new Power_Model_User(array(
            'user_idUser'       => '1',
            'user_name'         => 'shaun',
            'user_password'     => md5('shaunBBAPower'),
            'user_role'         => 'admin',
            'user_fullName'     => 'Shaun',
            'user_accessClient' => ''
        ));

        foreach( $user->toArray() as $key => $val ) {
                $identity->$key = $val;
        }

        return $identity;
    }
}

?>