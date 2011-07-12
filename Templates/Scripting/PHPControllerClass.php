<?php
<#assign licenseFirst = "/** ">
<#assign licensePrefix = " * ">
<#assign licenseLast = " */">
<#assign subpackage = "Controller">
<#include "../Licenses/license-${project.license}.txt">

/**
 * Controller Class ${name}.
 *
 * @category   ${project.name}
 * @package
 * @subpackage ${subpackage}
 * @copyright  Copyright (c) 2011 ${copyright}
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     ${author}
 */
class _${name} extends ZendSF_Controller_Action_Abstract
{
    /**
     * Initialization code.
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        // action body
    }
}
