<?php

/**
 * Cyclone
 *
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
 * @package    Cyclone (Default)
 * @subpackage IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: IndexController.php 197 2007-08-01 15:17:51Z gplocke@EOS.NCSU.EDU $
 */

/**
 * Main index controller for Cyclone
 *
 * @package    Cyclone (Default)
 * @subpackage IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
class IndexController extends Internal_Controller_Action 
{
    /**
     * shows the homepage
     *
     */
    public function indexAction()
    {
    	if (Zend_Auth::getInstance()->hasIdentity()) {
    		$attendees = new Attendees();
    		$this->view->attendeeEvents = $attendees->getEventsForAttendee(Zend_Auth::getInstance()->getIdentity(), time());
    		
    		$this->_helper->viewRenderer->setScriptAction('index-loggedin');
    	}
        $this->view->title = 'Welcome to Classmate';
        $this->view->showNews = true;
    }
    
    public function autoSuggestAction()
    {
        $this->_helper->viewRenderer->setNeverRender();
        
        $tag = new Tag();
        
        $post = Zend_Registry::get('post');

        $filter = Zend_Registry::get('inputFilter');
        $search = $filter->filter($post['search']);
        
        while (preg_match('/,/', $search)) {
            $search = trim(preg_replace('/^[^,]*,/', '', $search));
        }
        
        $ret = array();
        if ($search != '') {
	        $where = $tag->getAdapter()->quoteInto('name LIKE ?', $search . '%');
	        
	        $tags = $tag->fetchAll($where, 'name');
	                
	        foreach ($tags as $t) {
	        	$ret[] = $t->name;
	        }
        }
                
        echo Zend_Json_Encoder::encode($ret);
    }

    public function searchAction()
    {
    	$get = Zend_Registry::get('get');
    	$filter = Zend_Registry::get('inputFilter');
    	
    	if (!isset($get['search'])) {
    		throw new Internal_Exception_Input('No search term was set');
    	}
    	
    	$search = $filter->filter($get['search']);
    	if ($search == '') {
    		throw new Internal_Exception_Input('No search term was set');
    	}
    	
    	$tag = new Tag();
    	
    	$ids = $tag->getAttributeIdsWithTag('workshopId', $search);
    	
    	if (count($ids) != 0) {
	    	$workshop = new Workshop;
	    	$where = $workshop->getAdapter()->quoteInto('workshopId IN (?)', $ids);
	    	
	    	$workshops = $workshop->fetchAll($where, 'workshopId DESC')->toArray();
    	} else {
    		$workshops = array();
    	}
    	
    	$this->view->title = "Your search for &quot;" . $search . "&quot; returned " . count($workshops) . " workshop" . ((count($workshops) != 1) ? "s" : "") . ":";
    	$this->view->workshops = $workshops;
    }
    
    public function imageAction()
    {
        $this->_helper->viewRenderer->setNeverRender();
        
        $get = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');

        if (!isset($get['imageId'])) {
            $imageId = 0;
        } else{
            $imageId = $filter->filter($get['imageId']);
        }

        $image = new Image;

        $result = $image->find($imageId);     

        if (!is_null($result)) {
        	$result = $result->toArray();
        	
	        header("Content-type: " . $result['contentType']);
	        echo $result['source'];
        }
    }   
}
