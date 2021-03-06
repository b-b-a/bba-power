<?php

require_once dirname(__FILE__) . '/../../../../../application/modules/power/controllers/AuthController.php';

/**
 * Test class for Power_AuthController.
 * Generated by PHPUnit on 2011-10-31 at 01:13:00.
 */
class Power_AuthControllerTest extends ControllerTestCase
{
    /**
     * Init tests.
     */
    public function testInit()
    {
        $this->dispatch('/');
        $this->assertQuery('#auth');
    }

    /**
     * Login tests.
     */
    public function testLoginAction()
    {
        $this->login('shaun', 'shaun');
        $this->assertTrue(Zend_Auth::getInstance()->hasIdentity(), 'Login assertion failed');
    }

    /**
     * Logout tests.
     */
    public function testLogoutAction()
    {
        // only a logged in user can logout.
        $this->dispatch('/auth/logout');
        $this->assertQuery('#auth');
        $this->tearDown();

        // logged in user can logout.
        $this->login('shaun', 'shaun');
        $this->dispatch('/auth/logout');
        $this->assertFalse(Zend_Auth::getInstance()->hasIdentity(), 'Login assertion failed');
    }

    /**
     * Authentication tests.
     */
    public function testAuthenticateAction()
    {
        // only allow POST requests.
        $this->request->setQuery(array(
            'user_name'     => 'shaun',
            'user_password' => 'shaun'
        ));
        $this->dispatch('/auth/authenticate');
        $this->assertQuery('#auth');
        $this->tearDown();

        // only valid form values allowed.
        $csrf = $this->getLoginCSRF();
        $this->request->setMethod('POST')
            ->setPost(array(
                'user_name'     => '',
                'user_password' => '',
                'csrf'          => $csrf
            ));
        $this->dispatch('/auth/authenticate');
        $this->assertQuery('.errors');
        $this->tearDown();

        // only a valid user can login.
        $csrf = $this->getLoginCSRF();
        $this->request->setMethod('POST')
            ->setPost(array(
                'user_name'     => 'blah',
                'user_password' => '123456',
                'csrf'          => $csrf
            ));
        $this->dispatch('/auth/authenticate');
        $this->assertQueryContentContains('.hint', 'Login failed, Please try again');
        $this->tearDown();

        // only Guest can authenticate.
        $this->login('shaun', 'shaun');
        $this->dispatch('/auth/authenticate');
        $this->assertRedirectTo('/meter');
    }
}

?>