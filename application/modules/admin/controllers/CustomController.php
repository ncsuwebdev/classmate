<?php
/**
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file _LICENSE.txt.
 *
 * This license is also available via the world-wide-web at
 * http://itdapps.ncsu.edu/bsd.txt
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to itappdev@ncsu.edu so we can send you a copy immediately.
 *
 * @package    RSPM
 * @subpackage Admin_CustomController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Allows the management of custom attributes to certain parent nodes within
 * the application.
 *
 * @package    RSPM
 * @subpackage Admin_CustomController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 */
class Admin_CustomController extends Internal_Controller_Action 
{
	
    /**
     * Shows all available nodes to add attributes to
     */
    public function indexAction()
    {
        $this->view->title = "Custom Attribute Admin";
            
        $node = new Node();
        $nodes = $node->fetchAll()->toArray();
        
        $this->view->nodes = $nodes;

    }

    /**
     * Shows all attributes associated with the selected node
     *
     */
    public function detailsAction()
    {
        $this->view->acl = array(
            'add'    => $this->_acl->isAllowed($this->_role, $this->_resource, 'add'),
            'edit'   => $this->_acl->isAllowed($this->_role, $this->_resource, 'edit'),
            'delete' => $this->_acl->isAllowed($this->_role, $this->_resource, 'delete'),
            'attributeDetails' => $this->_acl->isAllowed($this->_role, $this->_resource, 'attributeDetails'),
            );   

        $get = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');
        
        if (!isset($get['nodeId'])) {
        	throw new Internal_Exception_Input('Node ID not found');
        }
        
        $nodeId = $filter->filter($get['nodeId']);
        
        $node = new Node();
        $thisNode = $node->find($nodeId);
        
        if (is_null($thisNode)) {
        	throw new Internal_Exception_Data('Node not found');
        }
        
        $ca = new CustomAttribute();
        $attributes = $ca->getAttributesForNode($nodeId);
        
        $this->view->title = 'Attributes for ' . $nodeId;
        $this->view->node = $thisNode->toArray();
        $this->view->attributes = $attributes;       
    }
    
    /**
     * Updates the display order of the attributes from the AJAX request
     *
     */
    public function orderAttributesAction()
    {
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {

            $filter = Zend_Registry::get('inputFilter');
            $post = Zend_Registry::get('post');

            $nodeId = $filter->filter($post['nodeId']);
            $order = $filter->filter($post['order']);
            $order = explode(',', $order);

            for ($x = 0; $x < count($order); $x++) {
                $order[$x] = (int)$order[$x];
            }

            $ca = new CustomAttribute();

            try {
                $ca->updateAttributeOrder($nodeId, $order);
                echo "New order saved successfully."; 
            } catch (Exception $e) {
            	echo "Saving new order failed - " . $e->getMessage();
            }
            
            $this->_logger->setEventItem('attributeName', 'nodeId');
            $this->_logger->setEventItem('attributeId', $nodeId);
            $this->_logger->info($nodeId . ' had attributes reordered');
            
            $this->_helper->viewRenderer->setNeverRender();
        }
    }   
    
    /**
     * Shows the details of a selected attribute
     *
     */
    public function attributeDetailsAction()
    {
        $this->view->acl = array(
            'add'    => $this->_acl->isAllowed($this->_role, $this->_resource, 'add'),
            'edit'   => $this->_acl->isAllowed($this->_role, $this->_resource, 'edit'),
            'delete' => $this->_acl->isAllowed($this->_role, $this->_resource, 'delete'),
            );  
                	
    	$get = Zend_Registry::get('get');
    	$filter = Zend_Registry::get('inputFilter');
    	
    	$node = new Node();
    	$ca   = new CustomAttribute();
    	$na   = new NodeAttribute();
    	
    	if (!isset($get['attributeId'])) {
    		throw new Internal_Exception_Input('Attribute ID not set in query string');
    	}
    	
    	$attributeId = $filter->filter($get['attributeId']);
    	$attribute = $na->find($attributeId);

    	if (is_null($attribute)) {
    		throw new Internal_Exception_Data('Attribute not found');
    	}
    	
    	$attribute = $attribute->toArray();
    	
    	
    	$attribute['options'] = $ca->convertOptionsToArray($attribute['options']);
    	$attribute['render'] = $ca->renderFormElement($attribute);
    	
    	$thisNode = $node->find($attribute['nodeId']);
    	
    	if (is_null($thisNode)) {
    		throw new Internal_Exception_Data('Node not found');    		
    	}
    	
    	$this->view->attribute = $attribute;
    	$this->view->node = $thisNode->toArray();
    	$this->view->title = 'Attribute Details';
    	
    }
    
    /**
     * Adds a new attribute to a node
     *
     */
    public function addAction()
    {
    	$ca = new CustomAttribute();
    	$filter = Zend_Registry::get('inputFilter');
    	
        if ($this->_request->isPost()) {
            
            $post   = Zend_Registry::get('post');

            $options = array();
            if (isset($post['option'])) {
	            foreach ($post['option'] as $o) {
	                if ($o != '') {
                        $options[] = $filter->filter($o); 
                    }
	            }
            }
            
            $data = array(
                       'nodeId'    => $filter->filter($post['nodeId']),
                       'label'     => $filter->filter($post['label']),
                       'type'      => $filter->filter($post['type']),
                       'options'   => $ca->convertOptionsToString($options),
                       'required'  => $filter->filter($post['required']),
                       'direction' => $filter->filter($post['direction']),
                       'order'     => 0,
                    );
            
            $na = new NodeAttribute();
            
            $id = $na->insert($data);
            
            $this->_logger->setEventItem('attributeName', 'nodeId');
            $this->_logger->setEventItem('attributeId', $data['nodeId']);
            $this->_logger->info('Attribute ' . $data['label'] . ' added'); 

            $this->_logger->setEventItem('attributeName', 'nodeAttributeId');
            $this->_logger->setEventItem('attributeId', $id);
            $this->_logger->info('Attribute ' . $data['label'] . ' added');             
            
            $this->_redirect('/admin/custom/details/?nodeId=' . $data['nodeId']);
            
        } else {
        	$get = Zend_Registry::get('get');
        	
	        if (!isset($get['nodeId'])) {
	            throw new Internal_Exception_Input('Node ID not found');
	        }
	        
	        $nodeId = $filter->filter($get['nodeId']);
	        
	        $node = new Node();
	        $thisNode = $node->find($nodeId);
	        
	        if (is_null($thisNode)) {
	            throw new Internal_Exception_Data('Node not found');
	        }

	        $this->view->title = "Add Custom Attribute to " . $nodeId;
	        $this->view->node = $thisNode->toArray();
	        $this->view->types = $ca->getTypes();
        }
    }

    /**
     * Modifies an existing attribute
     *
     */
    public function editAction()
    {
        $ca = new CustomAttribute();
        $na = new NodeAttribute();
        $node = new Node();
        
        $filter = Zend_Registry::get('inputFilter');
        
        if ($this->_request->isPost()) {
            
            $post   = Zend_Registry::get('post');

            $options = array();
            if (isset($post['option'])) {
                foreach ($post['option'] as $o) {
                	if ($o != '') {
                        $options[] = $filter->filter($o); 
                	}
                }
            }
            
            if (!isset($post['attributeId'])) {
                throw new Internal_Exception_Input('Attribute ID not set in query string');
            }
            
            $attributeId = $filter->filter($post['attributeId']);
            $attribute = $na->find($attributeId);
    
            if (is_null($attribute)) {
                throw new Internal_Exception_Data('Attribute not found');
            }
            
            $attribute = $attribute->toArray();         
            
            $attribute['options'] = $ca->convertOptionsToArray($attribute['options']);
            
            foreach ($post['opt_delete'] as $opt) {
            	$key = array_search($filter->filter($opt), $attribute['options']);
            	unset($attribute['options'][$key]);
            }
            
            $attribute['options'] = array_merge($attribute['options'], $options);
            
            
            $data = array(
                       'attributeId' => $attributeId,
                       'label'       => $filter->filter($post['label']),
                       'type'        => $filter->filter($post['type']),
                       'required'    => $filter->filter($post['required']),
                       'direction'   => $filter->filter($post['direction']),
                    );
                    
            if (($data['type'] == 'select' || $data['type'] == 'radio') && is_array($attribute['options'])) {
            	$data['options'] = $ca->convertOptionsToString($attribute['options']);
            } else {
            	$data['options'] = '';
            }

            $na = new NodeAttribute();
            
            $na->update($data, null);
            
            $this->_logger->setEventItem('attributeName', 'nodeAttributeId');
            $this->_logger->setEventItem('attributeId', $data['attributeId']);
            $this->_logger->info('Attribute ' . $data['label'] . ' modified');              
            
            $this->_redirect('/admin/custom/details/?nodeId=' . $attribute['nodeId']);
            
        } else {
            $get = Zend_Registry::get('get');
        
	        if (!isset($get['attributeId'])) {
	            throw new Internal_Exception_Input('Attribute ID not set in query string');
	        }
	        
	        $attributeId = $filter->filter($get['attributeId']);
	        $attribute = $na->find($attributeId);
	
	        if (is_null($attribute)) {
	            throw new Internal_Exception_Data('Attribute not found');
	        }
	        
	        $attribute = $attribute->toArray();	        
	        
	        $attribute['options'] = $ca->convertOptionsToArray($attribute['options']);
	        
	        $thisNode = $node->find($attribute['nodeId']);
	        
	        if (is_null($thisNode)) {
	            throw new Internal_Exception_Data('Node not found');            
	        }

            $this->view->title = "Edit Custom Attribute for " . $attribute['nodeId'];
            $this->view->node = $thisNode->toArray();
            $this->view->attribute = $attribute;
            $this->view->types = $ca->getTypes();
        }
    }

    /**
     * Deletes an attribute
     *
     */
    public function deleteAction()
    {        
        $filter = Zend_Registry::get('inputFilter');
        
        $na = new NodeAttribute();
        $node = new Node();
        
        $filter = Zend_Registry::get('inputFilter');
        
        if ($this->_request->isPost()) {  
            $post = Zend_Registry::get('post');
            
            if (!isset($post['attributeId'])) {
                throw new Internal_Exception_Input('Attribute ID not set in query string');
            }
            
            $attributeId = $filter->filter($post['attributeId']);
            $attribute = $na->find($attributeId);
    
            if (is_null($attribute)) {
                throw new Internal_Exception_Data('Attribute not found');
            }
            
            $attribute = $attribute->toArray();      

            $where = $na->getAdapter()->quoteInto('attributeId = ?', $attributeId);
            $na->delete($where);
            
            $nv = new NodeValue();
            $nv->delete($where);
            
            $this->_logger->setEventItem('attributeName', 'nodeAttributeId');
            $this->_logger->setEventItem('attributeId', $attributeId);
            $this->_logger->info('Attribute ' . $data['label'] . ' and all values were deleted');             
                        
            
            $this->_redirect('/admin/custom/details/?nodeId=' . $attribute['nodeId']);
        	
        } else {
        	
	        $get = Zend_Registry::get('get');
	        
	        if (!isset($get['attributeId'])) {
	            throw new Internal_Exception_Input('Attribute ID not set in query string');
	        }
	        
	        $attributeId = $filter->filter($get['attributeId']);
	        $attribute = $na->find($attributeId);
	
	        if (is_null($attribute)) {
	            throw new Internal_Exception_Data('Attribute not found');
	        }
	        
	        $attribute = $attribute->toArray();
	        
	        $thisNode = $node->find($attribute['nodeId']);
	        
	        if (is_null($thisNode)) {
	            throw new Internal_Exception_Data('Node not found');            
	        }
	        
	        $this->view->attribute = $attribute;
	        $this->view->node = $thisNode->toArray();
	        $this->view->title = 'Delete Attribute from ' . $attribute['nodeId'];
        }
    }
	
}