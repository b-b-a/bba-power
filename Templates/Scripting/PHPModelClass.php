<?php
<#assign licenseFirst = "/** ">
<#assign licensePrefix = " * ">
<#assign licenseLast = " */">
<#assign subpackage = "Model">
<#include "../Licenses/license-${project.license}.txt">

/**
 * DAO to represent a single ${name}.
 *
 * @category   ${project.name}
 * @package
 * @subpackage ${subpackage}
 * @copyright  Copyright (c) 2011 ${copyright}
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     ${author}
 */
class _Model_${name} extends BBA_Model_Abstract
{
    /**
     * @var string
     */
    protected $_prefix = '';
}