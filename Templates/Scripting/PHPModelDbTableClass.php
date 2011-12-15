<?php
<#assign licenseFirst = "/** ">
<#assign licensePrefix = " * ">
<#assign licenseLast = " */">
<#assign subpackage = "Model_DbTable">
<#include "../Licenses/license-${project.license}.txt">

/**
 * Database adapter class for the ${name} table.
 *
 * @category   ${project.name}
 * @package
 * @subpackage ${subpackage}
 * @copyright  Copyright (c) 2011 ${copyright}
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     ${author}
 */
class _Model_DbTable_${name} extends ZendSF_Model_DbTable_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = '';

    /**
     * @var string primary key
     */
    protected $_primary = '';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array();
}
