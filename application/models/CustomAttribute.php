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
 * @subpackage Custom Attribute
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Model allows simple integration of custom attributes that are tied to parent
 * nodes.
 *
 * @package    RSPM
 * @subpackage CustomAttribute
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class CustomAttribute
{
    
	/**
	 * Types of attributes available
	 *
	 * @var array
	 */
	protected $_types = array(
	   'text',
	   'textarea',
	   'radio',
	   'checkbox',
	   'select',
	   'ranking'
	);
	
	/**
	 * The array of options returned for a custom attribute of type "ranking"
	 *
	 * @var array
	 */
	protected $_rankingOptions = array(
	   'N/A' => 'N/A',
       '1' => '1',
       '2' => '2', 
       '3' => '3', 
       '4' => '4', 
       '5' => '5'
    );
	
	/**
	 * Gets the attributes that have been assigned to a node, then renders 
	 * them if need be
	 *
	 * @param mixed $nodeId
	 * @param string $render
	 * @return array of attributes
	 */
	public function getAttributesForNode($nodeId, $render = 'none')
	{
		$na = new NodeAttribute();
		
		$where = null;
		if (!is_null($nodeId)) {
		    $where = $na->getAdapter()->quoteInto('nodeId = ?', $nodeId);
		}
		
		$attributes = $na->fetchAll($where, 'order')->toArray();
		
        foreach ($attributes as &$a) {
            if ($a['type'] == 'ranking') {
                
                $a['options'] = $this->convertOptionsToString($this->_rankingOptions);
            }
        }
		
		if ($render != 'none') {
			foreach ($attributes as &$a) {
			    
				if ($render == 'display') {
					$a['render'] = $this->renderDisplay($a);
				} elseif ($render == 'form') {
					$a['render'] = $this->renderFormElement($a);
				} else {
					$a['render'] = '';
				}			
			}
		}
		
		return $attributes;
	}
	
	/**
	 * Converts options for selects and radios from a string to an array
	 *
	 * @param string $options
	 * @return array
	 */
	public function convertOptionsToArray($options)
	{
		$options = unserialize($options);
		
		return (is_array($options)) ? $options : array();
	}
	
	/**
	 * Converts options for selects and radios from an array to a string 
	 * (used for storage in DB)
	 *
	 * @param array $options
	 * @return string
	 */
	public function convertOptionsToString($options)
	{
		return serialize((is_array($options)) ? $options: array());
	}
	
	/**
	 * Gets all available types of custom attributes
	 *
	 * @return array
	 */
	public function getTypes()
	{
		return array_combine($this->_types, $this->_types);
	}
	
	/**
	 * Renders an attribute using the form template
	 *
	 * @param array $attribute
	 * @param mixed $value
	 * @return resulting HTML
	 */
	public function renderFormElement($attribute, $value = null)
	{
		$opts = array();
				
		if ($attribute['required']) {
			$opts['class'] = 'required';
		}
		
		$name = 'custom[' . $attribute['attributeId'] . ']';
		
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		switch ($attribute['type']) {
			
			case 'text':
				$opts['size'] = '20';
				$attribute['formField'] = $view->formText($name, $value, $opts);
				break;
			case 'textarea':
				$opts['rows'] = "3";
				$opts['cols'] = "50";
				$attribute['formField'] = $view->formTextarea($name, $value, $opts);
				break;
			case 'radio':
			    $listsep = "<br />\n";
			    if ($attribute['direction'] == "horizontal") {
			        $listsep = "&nbsp;";
			    }
				$attribute['formField'] = $view->formRadio($name, $value, $opts, $attribute['options'], $listsep);
				break;
			case 'checkbox':
				$attribute['formField'] = $view->formCheckbox($name, $value, $opts);
				break;
			case 'select':
				$opts['size'] = '1';
				$attribute['formField'] = $view->formSelect($name, $value, $opts, $attribute['options']);
				break;
		    case 'ranking':
		        $tmpOptions = $this->_rankingOptions;
		                     
		        $listsep = "<br />\n";
                if ($attribute['direction'] == "horizontal") {
                    $listsep = "&nbsp;";
                }
		               
                $attribute['formField'] = $view->formRadio($name, $value, $opts, $tmpOptions, $listsep);
                break;
			default:
				return '';
		}
		
		$view->tempAttribute = $attribute;
		return $view->render('customAttribute.tpl');
	}
	
	/**
	 * Renders the display view for an attribute
	 *
	 * @param array $attribute
	 * @param mixed $value
	 * @return HTML for display
	 */
    public function renderDisplay($attribute, $value = null)
    {       
    	$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
    	
    	$attribute['formField'] = (is_array($value)) ? implode('<br />', $value) : $value;
        $view->tempAttribute = $attribute;
        
        return $view->render('customAttribute.tpl');
    }
	
    /**
     * Saves data from custom attributes that are tied to a parent node and ID
     *
     * @param mixed $nodeId
     * @param mixed $parentId
     * @param array $data
     */
	public function saveData($nodeId, $parentId, array $data) 
	{
        $nv = new NodeValue();
        $dba = $nv->getAdapter();
        
        $inTransaction = false;
        
	    try {
            $dba->beginTransaction();
        } catch (Exception $e) {
            $inTransaction = true;
        }
        
        foreach ($data as $key => $value) {
            $d = array(
                'nodeId' => $nodeId,
                'parentId'   => $parentId,
                'attributeId' => $key,
                'value'       => $value,
            );

            $where = $dba->quoteInto('nodeId = ?', $nodeId) . ' AND ' . 
                $dba->quoteInto('parentId = ?', $parentId) . ' AND ' . 
                $dba->quoteInto('attributeId = ?', $key);
                
            $result = $nv->fetchAll($where);
            
            if ($result->count() != 0) {
	            try {
	                 $nv->update($d, null);
	            } catch (Exception $e) {
    	            if (!$inTransaction) {
                        $dba->rollBack();
                    }
	                throw $e;
	            }
            } else {
                try {
                     $nv->insert($d);
                } catch (Exception $e) {
                    if (!$inTransaction) {
                        $dba->rollBack();
                    }
                    throw $e;
                }            	
            }
        }
        
	    if (!$inTransaction) {
            $dba->commit();
        }
	}
	
	/**
	 * given a node and parent ID, removes all custom attributes associated with it
	 *
	 * @param mixed $nodeId
	 * @param mixed $parentId
	 */
	public function deleteData($nodeId, $parentId)
	{
		$nv = new NodeValue;
		$dba = $nv->getAdapter();
		
		$where = $dba->quoteInto('nodeId = ?', $nodeId) . 
		  ' AND ' . 
		  $dba->quoteInto('parentId = ?', $parentId);
		  
		$nv->delete($where);
		
	}
	
	/**
	 * Gets all submitted data based on a node and parent ID, can also
	 * render that data in a display or form template
	 *
	 * @param mixed $nodeId
	 * @param mixed $parentId
	 * @param string $render
	 * @return array
	 */
	public function getData($nodeId, $parentId, $render='none')
	{
		$attributes = $this->getAttributesForNode($nodeId);
		$nv = new NodeValue();
		
		$ret = array();

		foreach ($attributes as $a) {
			
			$dba = $nv->getAdapter();
			$where = $dba->quoteInto('nodeId = ?', $nodeId) . ' AND ' . 
			 $dba->quoteInto('parentId = ?', $parentId) . ' AND ' . 
			 $dba->quoteInto('attributeId = ?', $a['attributeId']);
			 
			$sv = $nv->fetchall($where);

			$value = '';
			
			if ($sv->count() == 1) {
				$value = $sv->current()->value;
			}
			
			$a['options'] = $this->convertOptionsToArray($a['options']);
			
			$temp = array(
			     'attribute' => $a,
			     'value'     => ($a['type'] == 'select' || $a['type'] == 'radio') ? ((isset($a['options'][$value])) ? $a['options'][$value] : '') : $value,
			     'render'    => '',
			);
			
			if ($render == 'display') {
				$temp['render'] = $this->renderDisplay($a, $value);
			} elseif ($render == 'form') {
				$temp['render'] = $this->renderFormElement($a, $value);
			}
			
			$ret[] = $temp;
		}
		
		return $ret;
	}
	
    /**
     * Updates the display order of the URLs from a group.
     *
     * @param int $groupId
     * @param array $order
     */
    public function updateAttributeOrder($nodeId, $order)
    {
    	$na = new NodeAttribute();
    	
        $dba = $na->getAdapter();
        
        $inTransaction = false;
        
        try { 
            $dba->beginTransaction();
        } catch (Exception $e) {
            $inTransaction = true;
        }

        $i = 1;
        foreach ($order as $o) {

            if (!is_integer($o)) {                
                if (!$inTransaction) {
                    $dba->rollBack();
                }
                throw new Internal_Exception_Input("New position was not an integer.");
            }

            $data = array("order" => $i);

            $where = $dba->quoteInto('attributeId = ?', $o) .
                     " AND " .
                     $dba->quoteInto('nodeId = ?', $nodeId);

            try {
                $na->update($data, $where);
            } catch(Exception $e) {
                if (!$inTransaction) {
                    $dba->rollBack();
                }
                throw $e;
            }
            $i++;
        }
        if (!$inTransaction) {
            $dba->commit();
        }
    }    	
}