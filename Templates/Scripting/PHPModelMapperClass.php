<?php
<#assign licenseFirst = "/** ">
<#assign licensePrefix = " * ">
<#assign licenseLast = " */">
<#assign subpackage = "Model_Mapper">
<#include "../Licenses/license-${project.license}.txt">

/**
 * Mapper Class for ${name}.
 *
 * @category   ${project.name}
 * @package
 * @subpackage ${subpackage}
 * @copyright  Copyright (c) 2011 ${copyright}
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     ${author}
 */
class _Model_Mapper_${name} extends ZendSF_Model_Mapper_Acl_Abstract
{
    /**
     * @var _Model_DbTable_${name}
     */
    protected $_dbTableClass;

    /**
     * @var _Model_${name}
     */
    protected $_modelClass;

    /**
     * Injector for the acl, the acl can be injected directly
     * via this method.
     *
     * We add all the access rules for this resource here, so we first call
     * parent method to add $this as the resource then we
     * define it rules here.
     *
     * @param Zend_Acl_Resource_Interface $acl
     * @return ZendSF_Model_Mapper_Abstract
     */
    public function setAcl (Zend_Acl $acl)
    {
        parent::setAcl($acl);

        // implement rules here.

        return $this;
    }
}
