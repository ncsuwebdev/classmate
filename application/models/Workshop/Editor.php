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
 * @package    Workshop_Editor
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the workshop editor
 *
 * @package    Workshop_Editor
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Workshop_Editor extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_workshop_editor';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = array('workshopId', 'userId');

    public function isEditor($workshopId, $accountId) {
    	$dba = $this->getAdapter();
    	
    	$where = $dba->quoteInto('workshopId = ?', $workshopId);
    	   
    	$result = $this->fetchAll($where);
    	
    	$editors = array();
    	foreach ($result as $r) {
    		$editors[] = $r->accountId;
    	}
    	
    	if (in_array($accountId, $editors)) {
    		return true;
    	}
    	
    	if (in_array('instructors', $editors)) {
            $select = $dba->select();
            $select->from(array('i' => 'tbl_instructor'));
            $select->join(array('e' => 'tbl_event'), 'i.eventId = e.eventId');
            $select->where('i.accountId = ?', $accountId);
            $where = 
                '(' . 
                    '(' . 
                        $dba->quoteInto('e.date > ?', date('Y-m-d')) .
                    ')' .                       
                    ' OR ' . 
                    '(' . 
                        $dba->quoteInto('e.date = ?', date('Y-m-d')) . 
                        ' AND ' .
                        $dba->quoteInto('e.endTime >= ?', date('H:i:s')) . 
                    ')' .    
                ')';
            $select->where($where);
            
            $stmt = $dba->query($select);
            $result = $stmt->fetchAll();
            
            if (count($result) != 0) {
            	return true;
            }
    	}
    	
    	return false;
    }
    
   /**
     * Given an array of tags, sets them as tags for a given attribute
     *
     * @param string $attributeName
     * @param mixed $attributeId
     * @param array $tags
     */
    public function setEditorsForWorkshop($workshopId, $editors)
    {
    	$dba = $this->getAdapter();
    	
        $dba->beginTransaction();
        
        $where = $dba->quoteInto('workshopId = ?', $workshopId);

        try {
            $this->delete($where);
        } catch (Exception $e) {
        	$dba->rollback();
        	throw $e;
        }
        
        foreach ($editors as $e) {
        	$data = array(
        		'workshopId' => $workshopId,
        		'accountId'  => $e,
        	);
        	
            try {
	            $this->insert($data);
	        } catch (Exception $e) {
	        	$dba->rollback();
	        	throw $e;
	        }        	
        	
        }
        $dba->commit();        
    }    
}
