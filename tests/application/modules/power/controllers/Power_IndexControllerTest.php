<?php

require_once dirname(__FILE__) . '/../../../../../application/modules/power/controllers/IndexController.php';

/**
 * Test class for Power_IndexController.
 * Generated by PHPUnit on 2011-10-29 at 16:14:29.
 */
class Power_IndexControllerTest extends ControllerTestCase
{
    /**
     * Only valid users can access this controller.
     */
    public function testInitAction()
    {
        $this->dispatch('/');
        $this->assertQuery('#auth');
    }

    /**
     * Default controller is Meter.
     */
    public function testIndexAction()
    {
        $this->doAuth();
        $this->dispatch('/');
        $this->assertRedirectTo('/meter');
    }
}

?>
