<?php

require_once dirname(__FILE__) . '/../../../../../application/modules/power/controllers/UserController.php';

/**
 * Test class for Power_UserController.
 * Generated by PHPUnit on 2011-11-01 at 09:34:38.
 */
class Power_UserControllerTest extends ControllerTestCase
{
    /**
     * Init tests.
     * only valid users can access this controller.
     */
    public function testInit()
    {
        $this->dispatch('/');
        $this->assertQuery('#auth');
    }

    /**
     * @todo Implement testIndexAction().
     */
    public function testIndexAction() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testUserStoreAction().
     */
    public function testUserStoreAction() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testListAction().
     */
    public function testListAction() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testAddAction().
     */
    public function testAddAction() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testEditAction().
     */
    public function testEditAction() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testSaveAction().
     */
    public function testSaveAction() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}

?>
