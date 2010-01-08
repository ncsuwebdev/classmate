<?php
/**
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * This license is also available via the world-wide-web at
 * http://itdapps.ncsu.edu/bsd.txt
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to itappdev@ncsu.edu so we can send you a copy immediately.
 *
 * @package    (Default)
 * @subpackage IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @version    SVN: $Id: $
 */

/**
 * Main index controller
 *
 * @package    IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
class MigrationController extends Zend_Controller_Action 
{         
    
	protected $_sourceDb = null;

	protected $_destDb = null;
	
	protected $_userAccountMap = array();
	
	protected $_sourceTables = array();
	
	protected $_destTables = array();
	
	public function init()
	{
	    die('You can not run this unless you know what you are doing.');
		parent::init();
		
		$km = new KeyManager();
		
		$key = $km->getKey('classmate', 'prod');
        
		$sourceDb = array(
		    'adapter'  => 'PDO_MYSQL',
		    'username' => $key->username,
		    'password' => $key->password,
		    'host'     => $key->host,
		    'port'     => $key->port,
		    'dbname'   => $key->dbname
		    );  
		    
		$this->_sourceDb = Zend_Db::factory($sourceDb['adapter'], $sourceDb);	    
		    
		$key = $km->getKey('classmate', 'dev');

	    $destDb = array(
		    'adapter'  => 'PDO_MYSQL',
		    'username' => $key->username,
		    'password' => $key->password,
		    'host'     => $key->host,
		    'port'     => $key->port,
		    'dbname'   => $key->dbname
		    );
		
		$this->_destDb = Zend_Db::factory($destDb['adapter'], $destDb);	    
		
        $destTables = $this->_destDb->listTables();
       	foreach ($destTables as $d) {
       		$this->_destTables[$d] = '*** NOT IMPORTED TO ***';
       	}
       	
       	$sourceTables = $this->_sourceDb->listTables();
           foreach ($sourceTables as $s) {
       		$this->_sourceTables[$s] = '*** NOT MOVED ***';
       	}		
		
	}
    /**
     * Redirects to the changelog
     *
     */
    public function indexAction()
    {
        $dba = $this->_destDb;
        
        $dba->beginTransaction();
        
        echo "<pre>";
        
        try {
        	     	
        	// Straight-up copy
        	$this->_copy('tbl_cron_status', 'oit_tbl_ot_cron_status', array('path' => 'name', 'schedule' => null));
        	$this->_copy('tbl_node_attribute', 'oit_tbl_ot_custom_attribute', array('nodeId' => 'objectId'));
        	$this->_copy('tbl_node_value', 'oit_tbl_ot_custom_attribute_value', array('nodeId' => 'objectId'));
        	$this->_copy('tbl_email_queue', 'oit_tbl_ot_email_queue', array('callId' => null));
        	
        	$this->_copy('tbl_evaluation', 'oit_tbl_evaluation');
        	$this->_copy('tbl_event', 'oit_tbl_event');
        	$this->_copy('tbl_location', 'oit_tbl_location');        	
        	$this->_copy('tbl_search_term', 'oit_tbl_search_term');  
        	$this->_copy('tbl_tag', 'oit_tbl_tag');
			$this->_copy('tbl_tag_map', 'oit_tbl_tag_map'); 
        	$this->_copy('tbl_workshop', 'oit_tbl_workshop', array('workshopCategoryId' => null, 'softwareDependency' => null)); 
        	$this->_copy('tbl_workshop_link', 'oit_tbl_workshop_link');
        	
        	$this->_transferAccounts();
        	
        	$this->_copyAccountData('tbl_attendees', 'oit_tbl_event_attendee', array('userId' => 'accountId'));
        	$this->_copyAccountData('tbl_evaluation_user', 'oit_tbl_evaluation_user', array('userId' => 'accountId'));
			$this->_copyAccountData('tbl_instructor', 'oit_tbl_event_instructor', array('userId' => 'accountId'));
			$this->_copyAccountData('tbl_workshop_editor', 'oit_tbl_workshop_editor', array('userId' => 'accountId'));
			
			$this->_copyDocuments();
			
			//$this->_copyTriggers();
        	
        } catch (Exception $e) {
        	$dba->rollback();
        	throw $e;
        }
        
        $dba->commit();
        
        asort($this->_sourceTables);
        asort($this->_destTables);
                
        echo "---------------------------\nSource Tables\n---------------------------\n";
        foreach ($this->_sourceTables as $table => $change) {
        	echo "$change - $table\n";
        }
        
        echo "\n\n---------------------------\nDestination Tables\n---------------------------\n";
        foreach ($this->_destTables as $table => $change) {
        	echo "$change - $table\n";
        }
                
        die();
    }
    
    protected function _transferAccounts()
    {
    	$this->_destDb->delete('oit_tbl_ot_account', '1=1');
    	
    	$sourceResults = $this->_sourceDb->fetchAll('SELECT * FROM tbl_profile');
    	
    	$newRoles = $this->_destDb->fetchAll('SELECT * FROM oit_tbl_ot_role');
    	
        $newRoleMap = array();
    	foreach ($newRoles as $r) {
    		$newRoleMap[$r['name']] = $r['roleId'];
    	}
    	    	
    	$oldRoles = $this->_sourceDb->fetchAll('SELECT * FROM tbl_authz');
    	
    	$roleMap = array();
    	foreach ($oldRoles as $r) {
    		$roleMap[$r['userId']] = (isset($newRoleMap[$r['role']])) ? $newRoleMap[$r['role']] : 0;
    	}
    	
    	$passwords = $this->_sourceDb->fetchAll('SELECT * FROM tbl_auth_user');
    	$passwordMap = array();
    	foreach ($passwords as $p) {
    	    $passwordMap[$p['userId']] = $p['password'];
    	}
    	
    	foreach ($sourceResults as $s) {
    		$data = array(
    			'username'     => preg_replace('/@.*/', '', $s['userId']),
    			'realm'        => preg_replace('/^[^@]*@/', '', $s['userId']),
    			'password'     => (isset($passwordMap[$s['userId']])) ? $passwordMap[$s['userId']] : md5(microtime()),
    			'apiCode'      => '',
    			'role'         => (isset($roleMap[$s['userId']])) ? $roleMap[$s['userId']] : 0,
    			'emailAddress' => $s['emailAddress'],
    			'firstName'    => $s['firstName'],
    			'lastName'     => $s['lastName'],
    			'timezone'     => 'America/New_York',
    			'lastLogin'    => 0,
    		);
    		
    		$this->_destDb->insert('oit_tbl_ot_account', $data);
    		
    		$this->_userAccountMap[$s['userId']] = $this->_destDb->lastInsertId();
    	}
    	
    	$this->_sourceTables['tbl_profile']     = 'Copied to Account Table';
    	$this->_sourceTables['tbl_authz']    = 'Copied to Account Table';
    	$this->_sourceTables['tbl_auth_user'] = 'Copied to Account Table';
    	$this->_destTables['oit_tbl_ot_account']   = 'Imported';
    }
    
    protected function _copy($sourceTbl, $destTbl, $changes = null)
    {
    	$this->_sourceTables[$sourceTbl] = 'Copied';
    	$this->_destTables[$destTbl]     = 'Copied';
    	
    	$sourceResults = $this->_sourceDb->fetchAll('SELECT * FROM ' . $sourceTbl);
    	
    	if (count($sourceResults) == 0) {
    		return;
    	}
    	
    	$mapping = array_combine(array_keys($sourceResults[0]), array_keys($sourceResults[0]));
    	
    	if (!is_null($changes)) {
    		foreach ($changes as $key => $c) {
    			if (is_null($c)) {
    				unset($mapping[$key]);
    			} else {
    				$mapping[$key] = $c;
    			}
    		}
    	}

    	$counter = 0;
    	
    	$this->_destDb->delete($destTbl, '1=1');
    	foreach ($sourceResults as $s) {
    		$data = array();
    		
    		foreach ($mapping as $source => $dest) {
    			$data[$dest] = $s[$source];
    		}
    		
    		$this->_destDb->insert($destTbl, $data);
    		
    		$counter++;
    	}
    }
    
    protected function _copyAccountData($sourceTbl, $destTbl, $changes)
    {
    	$this->_sourceTables[$sourceTbl] = 'Copied with Accounts';
    	$this->_destTables[$destTbl]     = 'Copied with Accounts';    	
    	
    	$sourceResults = $this->_sourceDb->fetchAll('SELECT * FROM ' . $sourceTbl);
    	
    	if (count($sourceResults) == 0) {
    		return;
    	}
    	
    	$counter = 0;
    	
    	$this->_destDb->delete($destTbl, '1=1');
    	foreach ($sourceResults as $s) {
    		$data = $s;
    		foreach ($changes as $source => $dest) {
    			$data[$dest] = (isset($this->_userAccountMap[$data[$source]])) ? $this->_userAccountMap[$data[$source]] : 0;
    			unset($data[$source]);
    		}
    		
    		$this->_destDb->insert($destTbl, $data);
    		
    		$counter++;
    	}
    }
    
    protected function _copyDocuments()
    {
        $documents = $this->_sourceDb->fetchAll('SELECT * FROM tbl_document, tbl_document_map WHERE tbl_document.documentId = tbl_document_map.documentId');
        
        foreach ($documents as $d) {
            $data = array(
                'workshopId'  => $d['attributeId'],
                'name'        => $d['name'],
                'description' => $d['description'],
                'type'        => $d['type'],
                'uploadDt'    => $d['uploadDt'],
                'filesize'    => $d['filesize'],
            );
            
            $this->_destDb->insert('oit_tbl_workshop_document', $data);
        }
        
        $this->_sourceTables['tbl_document'] = 'Copied';
        $this->_sourceTables['tbl_document_map'] = 'Copied';
        $this->_destTables['oit_tbl_workshop_document']   = 'Imported';

    }
    
    public function triggerAction()
    {
        $triggers = $this->_sourceDb->fetchAll('SELECT * FROM tbl_email_trigger');
        
        foreach ($triggers as $t) {
            echo '<trigger name="' . $t['triggerId'] . '" description="' . $t['description'] . '">' . "\n";
            
            $vars = $this->_sourceDb->fetchAll('SELECT * FROM tbl_email_trigger_variable where triggerId = \'' . $t['triggerId'] . '\'');
            foreach ($vars as $v) {
                echo '    <var name="' . $v['variable'] . '" description="' . $v['description'] . '"/>' . "\n";
                
            }
            echo '</trigger>' . "\n";
        }
        die();
    }
    
    protected function _copyTriggers()
    {
        $map = array(
            'Event_Instructor_Waitlist_To_Attending' => 'Instructor_Promote_User_Waitlist_To_Attending',
            'Event_Instructor_Signup' => 'Instructor_Registered_User',
            'Event_Instructor_Signup_Waitlist' => 'Instructor_Registered_User_For_Waitlist',
            'Event_Instructor_Cancel_Reservation' => 'Instructor_Cancels_Users_Reservation',
        );
        
        $templates = $this->_sourceDb->fetchAll('SELECT * FROM tbl_email_template');

        foreach ($templates as $t) {
            if (isset($map[$t['triggerId']])) {
                $t['triggerId'] = $map[$t['triggerId']];
            }
            
            $action = array(
                'triggerId' => $t['triggerId'],
                'name'      => $t['name'],
                'helper'    => 'Ot_Trigger_Plugin_EmailQueue'
            );
            
            $this->_destDb->insert('oit_tbl_ot_trigger_action', $action);
            
            $actionId = $this->_destDb->lastInsertId();
            
            $eq = array(
                'triggerActionId' => $actionId,
                'to'              => $t['to'],
                'from'            => 'classreg@ncsu.edu',
                'subject'         => $t['subject'],
                'body'            => $t['body']
            );
            
            $this->_destDb->insert('oit_tbl_ot_trigger_helper_emailqueue', $eq);
        }
        
        $this->_sourceTables['tbl_email_template'] = 'Copied';
        $this->_destTables['oit_tbl_ot_trigger_action'] = 'Imported';
        $this->_destTables['oit_tbl_ot_trigger_helper_emailqueue']   = 'Imported';
                
    }
    
}
