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
 * @package    Classmate
 * @subpackage Profile_IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Allows the user to set a customized profile linked to their user ID.
 *
 * @package    Classmate
 * @subpackage Profile_IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Profile_IndexController extends Internal_Controller_Action 
{
	/**
	 * Shows all users in the system
	 *
	 */
	public function indexAction()
	{
		$get    = Zend_Registry::get('get');
		$filter = Zend_Registry::get('inputFilter');
		$config = Zend_Registry::get('config');
		$uc     = Zend_Registry::get('userConfig');
		
		$editable = true;
		if (isset($get['userId'])) {
		    if ($this->_acl->isAllowed($this->_role, $this->_resource, 'editAllProfiles')) {
                $userId = $filter->filter($get['userId']);		
            } else {
            	$userId = Zend_Auth::getInstance()->getIdentity();
            }
		} else {
			$userId = Zend_Auth::getInstance()->getIdentity();
		}


		if ($userId == Zend_Auth::getInstance()->getIdentity()) {
			$role = Ot_Authz::getInstance()->getRole();
			
			if ($role == 'activation_pending') {
				$this->_redirect('/profile/index/edit/');
			}
		}
		
		$this->view->acl = array(
		    'edit' => $editable
	    );
	    
        $displayUserId = preg_replace('/@.*$/', '', $userId);
        $realm         = preg_replace('/^[^@]*@/', '', $userId);	    
                
        $adapter = $config->authentication->$realm->toArray();

        $a = new $adapter['class'];
            
        $this->view->adapter = array(
           'realm'       => $realm,
           'name'        => $adapter['name'],
           'description' => $adapter['description'],
        );
        
		$profile = new Profile();		
		$up = $profile->find($userId);
		
		if (is_null($up)) {
			throw new Internal_Exception_Input('No profile exists for this user');
		}
        
        $this->view->profile       = $up->toArray();
        $this->view->displayUserId = $displayUserId;
        $this->view->title         = "Appointments for " . ((isset($up->firstName)) ? $up->firstName . ' ' . $up->lastName : $displayUserId);
        $this->view->types         = $config->profileTypes->toArray();
        		
        $stayOpen = new Zend_Date();
        $stayOpen->subHour($uc['numHoursEvaluationAvailability']['value']);
        
		$ca = new CustomAttribute();
		$this->view->custom = $ca->getData('User_Profile', $userId, 'display');

		// Get all current reservations for the user
        $attendees = new Attendees();
        $reservations = $attendees->getEventsForAttendee($userId);
            
        $workshopIds = array();
        $location = new Location();
        $locationCache = array();        
        
	    $docMap = new DocumentMap();
	    $instructor = new Instructor();
        $profile = new Profile();
               
        $activeReservations = array();
        $completedReservations = array();
        
        $eu = new EvaluationUser();
        
        foreach ($reservations as $r) {
        	
        	$active = false;
        	$e = $r;
        	
        	// we determine if the class is open
            $startDt = new Zend_Date(strtotime($e['date'] . ' ' . $e['startTime']));
            $endDt   = new Zend_Date(strtotime($e['date'] . ' ' . $e['endTime']));
            $endDt->addHour($uc['numHoursEvaluationAvailability']['value']);
            
            $e['evaluatable'] = false;
            
            // checks to see if its possible that the class is open for evaluation
            if ((($startDt->getTimestamp() < time() && $endDt->getTimestamp() > time()) && !$eu->hasCompleted($userId, $r['eventId']))) {
            	$e['evaluatable'] = true;
            	$active = true;
            } elseif ($startDt->getTimestamp() > time()) {
            	$e['evaluatable'] = false;
            	$active = true;
            }
            
            $workshopIds[] = $e['workshopId'];
            
            $where =  $docMap->getAdapter()->quoteInto('attributeId = ?', $e['workshopId']) . 
                " AND " . 
                $docMap->getAdapter()->quoteInto('attributeName = ?', "workshopId");
                
            $maps = $docMap->fetchAll($where);
            
            if ($maps->count() > 0) {
                $e['hasHandouts'] = true;
            }
            
            $where = $instructor->getAdapter()->quoteInto('eventId = ?', $e['eventId']);
            $e['instructors'] = $instructor->fetchAll($where)->toArray();
            
            foreach ($e['instructors'] as &$i) {
                $i['profile'] = $profile->find($i['userId'])->toArray();
            }
            
            if (isset($locationCache[$e['locationId']])) {
                $e['location'] = $locationCache[$e['locationId']];
            } else {
                $thisLocation = $location->find($e['locationId']);        
                if (is_null($thisLocation)) {
                    throw new Internal_Exception_Data('Location not found');
                }
                $e['location'] = $thisLocation->toArray();      
                $locationCache[$e['locationId']] = $e['location'];
            }                  
            
            $e['cancelable'] = false;
            
            if ($active) {
            	
                $startDt->subHour($uc['numHoursEventCancel']['value']);
                $e['cancelable']  = ($startDt->getTimestamp() > time()); 
                           
                if ($e['status'] == 'waitlist') {
            	
	                $waiting = $attendees->getAttendeesForEvent($e['eventId'], 'waitlist');
	                
	                $position = 1;
	                
	                foreach ($waiting as $w) {
	                	if ($userId == $w['userId']) {
	                		break;
	                	}
	                	$position++;
	                }
	
	                $e['waitlistPosition'] = $position;

                }
                
                $activeReservations[] = $e;
            } else {
            	
            	$completedReservations[] = $e;
            }
        }
            
        $this->view->activeReservations = $activeReservations;        
        $this->view->completedReservations = array_reverse($completedReservations);        

        $activeTeaching = array();
        $completedTeaching = array();
        
        // Get presently taught classes
        $teaching = $instructor->getEventsForInstructor($userId);
        foreach ($teaching as $e) {           
            if (isset($locationCache[$e['locationId']])) {
                $e['location'] = $locationCache[$e['locationId']];
            } else {
                $thisLocation = $location->find($e['locationId']);        
                if (is_null($thisLocation)) {
                    throw new Internal_Exception_Data('Location not found');
                }
                $e['location'] = $thisLocation->toArray();      
                $locationCache[$e['locationId']] = $e['location'];
            } 

            $startDt = new Zend_Date(strtotime($e['date'] . ' ' . $e['startTime']));
            
            if ($startDt->getTimestamp() > time()) {
            	$activeTeaching[] = $e;
            } else {
            	$completedTeaching[] = $e;
            }
        }
        
        $this->view->activeTeaching = $activeTeaching;
        $this->view->completedTeaching = array_reverse($completedTeaching);
        
        // Get all related workshops for the user
        $workshop = new Workshop();

        $newRelated = array();
        $related = $workshop->getRelatedWorkshops($workshopIds, 4);
           
        foreach ($related as $r) {
            if (!in_array($r->workshopId, $workshopIds)) {
               $newRelated[] = array(
                 'title' => $r->title,
                 'workshopId' => $r->workshopId,
                 'description' => $r->description,
                 'tags' => explode(',', $r->tags),
                 'workshopCategoryId' => $r->workshopCategoryId,
               );
            }
        }
        
        $this->view->relatedWorkshops = $newRelated;
        
        $wc = new WorkshopCategory();
        $result = $wc->fetchAll(null, 'name')->toArray();
        
        $categories = array();
        foreach ($result as $c) {
            $categories[$c['workshopCategoryId']] = $c;
        }
        
        $this->view->categories = $categories;
        
        $this->view->hideTitle = true;
                    
        $this->view->javascript = array(
            'mootabs1.2.js',
        );
        
        $fm = $this->getHelper('FlashMessenger');
        $fm->setNamespace('login');

        $this->view->messages = $fm->getMessages();
	}
	
	/**
	 * allows a user to edit their profile
	 *
	 */
    public function editAction()
    {
        $filter = Zend_Registry::get('inputFilter');
        $config = Zend_Registry::get('config');
        
        $ca = new CustomAttribute();
        $profile = new Profile();

        if ($this->_request->isPost()) {
            $post = Zend_Registry::get('post');

            if (isset($post['userId'])) {
                if ($this->_acl->isAllowed($this->_role, $this->_resource, 'editAllProfiles')) {
                    $userId = $filter->filter($post['userId']); 
                } else {
                    $userId = Zend_Auth::getInstance()->getIdentity();
                }
            } else {
                $userId = Zend_Auth::getInstance()->getIdentity();
            }            
            
            $data = array(
                'userId'       => $userId,
                'firstName'    => $filter->filter($post['firstName']),
                'lastName'     => $filter->filter($post['lastName']),
                'emailAddress' => $filter->filter($post['emailAddress']),
                'type'         => $filter->filter($post['type']),
            );
            
            $thisProfile = $profile->find($data['userId']);
            if (is_null($thisProfile)) {
            	throw new Internal_Exception_Data('Profile not found');
            }
            
            $thisProfile = $thisProfile->toArray();
            
            if ($_FILES['pic']['name'] != '') {

                 $image = new Image;

                 $image->resizeImage($filter->filter($_FILES['pic']['tmp_name']), 32, 32);

                 $iData = array(
                    'source' => file_get_contents($filter->filter($_FILES['pic']['tmp_name'])),
                    'alt'    => $data['userId'],
                    'contentType' => $filter->filter($_FILES['pic']['type']),
                    'name'        => $filter->filter($_FILES['pic']['name']),
                    );


                 if (isset($thisProfile['picImageId']) && $thisProfile['picImageId'] != 0) {
                     $image->deleteImage($thisProfile['picImageId']);
                 }

                 $image->insert($iData);

                 $data['picImageId'] = $image->getAdapter()->lastInsertId();
            }         

            $realm = preg_replace('/^[^@]*@/', '', $userId);        
                    
            $adapter = $config->authentication->$realm->toArray();
    
            $a = new $adapter['class']; 
            
            if (!$a->autoLogin()) {
                $au = $a->editAccount($userId, '', $data['emailAddress']);
            }
            
            $profile->update($data, null);
            
            $custom = $post['custom'];
            
            foreach ($custom as &$c) {
                $c = $filter->filter($c);
            }
            
            $ca->saveData('User_Profile', $userId, $custom);
            
            if ($userId == Zend_Auth::getInstance()->getIdentity() && Ot_Authz::getInstance()->getRole() == 'activation_pending') {
                $authz = new $config->authorization($userId);
                $authz->editUser($userId, 'authUser');
            }
            
            $this->_logger->setEventItem('attributeName', 'userId');
            $this->_logger->setEventItem('attributeId', $data['userId']);
            $this->_logger->info('User changed profile');             
            
            $this->_redirect('/profile/?userId=' . $userId);
            
                
        } else {
        
	        $get = Zend_Registry::get('get');
	        
	        if (isset($get['userId'])) {
	        	if ($this->_acl->isAllowed($this->_role, $this->_resource, 'editAllProfiles')) {
                    $userId = $filter->filter($get['userId']); 
	        	} else {
	        		$userId = Zend_Auth::getInstance()->getIdentity();
	        	}
	        } else {
	            $userId = Zend_Auth::getInstance()->getIdentity();
	        }
	        
	        
            if ($userId == Zend_Auth::getInstance()->getIdentity()) {
	            $role = Ot_Authz::getInstance()->getRole();
	            
	            if ($role == 'activation_pending') {
	                $this->view->notice = true;
	            }
	        }	  
	              
	        $this->view->acl = array('editable' => true);
	        
	        $displayUserId = preg_replace('/@.*$/', '', $userId);
	        $realm         = preg_replace('/^[^@]*@/', '', $userId);        
	                
	        $adapter = $config->authentication->$realm->toArray();
	
	        $a = new $adapter['class'];
	            
	        $this->view->adapter = array(
	           'realm'       => $realm,
	           'name'        => $adapter['name'],
	           'description' => $adapter['description'],
	        );

	        $up = $profile->find($userId);
	        
	        if (is_null($up)) {
	            throw new Internal_Exception_Input('No profile exists for this user');
	        }
	        
	        $up = $up->toArray();
	        
            if (!$a->autoLogin() && $up['emailAddress'] == '') {
                $au = $a->getUser($userId);
                
                $up['emailAddress'] = $au[0]['email'];
            }	        
        
	        $this->view->types         = $config->profileTypes->toArray();
	        $this->view->profile       = $up;
	        $this->view->displayUserId = $displayUserId;
	        $this->view->title         = "Details for " . $displayUserId;
	                
	        $ca = new CustomAttribute();
	        $this->view->custom = $ca->getData('User_Profile', $userId, 'form');
        }
    }	
    
    /**
     * allows users to edit anyones profiles
     *
     */
    public function editAllProfilesAction()
    {
    }
	
}
?>