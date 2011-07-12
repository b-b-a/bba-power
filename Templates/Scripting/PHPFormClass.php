<?php
<#assign licenseFirst = "/** ">
<#assign licensePrefix = " * ">
<#assign licenseLast = " */">
<#assign subpackage = "Form">
<#include "../Licenses/license-${project.license}.txt">

/**
 * Form Class ${name}.
 *
 * @category   ${project.name}
 * @package
 * @subpackage ${subpackage}
 * @copyright  Copyright (c) 2011 ${copyright}
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     ${author}
 */
class _Form_${name} extends ZendSF_Form_Abstract
{
    public function init()
    {

    }
}
