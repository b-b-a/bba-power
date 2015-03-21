<?php
/**
 * Power.php
 *
 * Copyright (c) 2011 Shaun Freeman <shaun@shaunfreeman.co.uk>.
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
 * @package    Power
 * @subpackage Model_Acl
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Description of BBA
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_Acl
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Acl_Power extends Zend_Acl
{
	/**
	 * An array of user roles.
	 *
	 * @var array
	 */
	public static $bbaRoles = array(
        'decline'       => array(
            'label'         => 'Decline',
            'parent'        => null,
            'privileges'    => 'none'
        ),
	    // client roles and privileges
	    'clientRead'    => array(
		    'label'         => 'Client Read',
		    'parent'        => 'decline',
		    'privileges'    => array(
		        'view'      => array(
		            'Client', 'ClientAd', 'ClientPers', 
		            'Contract', 'Doc', 'Index', 'Meter',
		            'MeterUsage', 'Site', 'Supplier', 
		            'SupplierContract', 'Tender'
		        ),
		        'add'       => 'none',
		        'edit'      => 'none',
		        'resources' => array(
        	        'Auth', 'MenuClient', 'MenuContract', 
        	        'MenuMeter', 'MenuSite', 'MenuSupplier'
                ),
		    ),
		),
		'client'		=> array(
		    'label'         => 'Client',
		    'parent'        => 'clientRead',
		    'privileges'    => array(
		        'view'      => 'inherit',
		        'add'       => array(
		            'ClientAd', 'ClientPers', 'Meter', 
		           	'MeterUsage', 'Site'
		        ),
		        'edit'      => array(
		            'Client', 'ClientAd', 'ClientPers',
		            'Meter', 'MeterUsage', 'Site',
		        ),
		        'resources' => 'inherit',
		    ),
		),
        'agent'         => array(
            'label'         => 'Agent',
            'parent'        => null,
            'privileges'    => 'none'
        ),
		'read'          => array(
		    'label'         => 'Read',
		    'parent'        => null,
		    'privileges'    => array(
		        'view'      => array(
		            'Client', 'ClientAd', 'ClientPers','Contract', 'Doc',
		            'Index', 'Meter', 'MeterUsage', 'Site', 'Supplier',
		            'SupplierContract', 'SupplierPers', 'Tender'
		        ),
		        'add'       => 'none',
		        'edit'      => 'none',
		        'resources' => array(
        	        'Auth', 'BBAView', 'MenuClient', 'MenuContract',
        	        'MenuMeter', 'MenuSite', 'MenuSupplier'
                )
		    )
		),
		'meterUsage'    => array(
		    'label'         => 'meterUsage',
		    'parent'        => 'read',
		    'privileges'    => array(
		        'view'      => 'inherit',
		        'add'       => 'inherit',
		        'edit'      => 'inherit',
		        'resources' => array('MeterUsage')
		    )
		),
		'user'          => array(
		    'label'         => 'User',
		    'parent'        => 'meterUsage',
		    'privileges'    => array(
		        'view'      => 'inherit',
		        'add'       => 'inherit',
		        'edit'      => 'inherit',
		        'resources' => array(
		        	'Client', 'ClientAd', 'ClientPers', 'Contract',
        	        'Meter', 'MeterContract', 'Site', 'Supplier', 'SupplierPers', 'Tender'
		        )
		    )
		),
		'admin'         => array(
		    'label'         => 'Admin',
		    'parent'        => 'user',
		    'privileges'    => 'all'
		),
	);
	
	/**
	 * An array of resources.
	 *
	 * @var array
	 */
	protected $_bbaResources = array(
	    // resources for menu.
	    'MenuClient', 'MenuContract', 'MenuInvoice', 'MenuMeter', 
	    'MenuSite', 'MenuSupplier', 'MenuUser',
        // resources based on controllers and DataBase tables.
        'Auth', 'Client', 'ClientAd', 'ClientPers',
        'Contract', 'Doc', 'Index', 'Invoice',
        'InvoiceLine', 'InvoiceUsage', 'Meter', 'MeterContract',
        'MeterUsage', 'Site', 'Supplier', 'SupplierContract',
        'SupplierPers', 'Tender', 'User',
        // special view for BBA staff only
        'BBAView'
	);
	
    /**
     * Set up role and resouces for power module.
     */
    public function __construct()
    {   
        // block all by default.
        $this->deny();
        
        // add resources.
        foreach ($this->_bbaResources as $value) {
            $this->addResource(new Zend_Acl_Resource($value));
        }
        
        // add roles and privileges
        $this->addRole(new Zend_Acl_Role('guest'));
        
        foreach (self::$bbaRoles as $role => $values) {
            $this->addRole(new Zend_Acl_Role($role), $values['parent']);
            
            if (is_string($values['privileges'])) {
    	        if ($values['privileges'] === 'all') {
    	        	$this->allow($role);
    	        }
    	    }
    	    
    	    if (is_array($values['privileges'])) {
    	        foreach ($values['privileges'] as $key => $value) {
        	        if (is_array($value)) {  
        	            if ($key === 'resources') {
        	                $this->allow($role, $value);
        	            } else {
        	                $this->allow($role, $value, $key);
        	            }
        	        }
        	    }
    	    }
        }
    }
}
