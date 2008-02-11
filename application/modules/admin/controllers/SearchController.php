<?php
/**
 * 
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
 * @package     (Admin)
 * @subpackage Admin_SearchController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: LogController.php 210 2007-08-01 18:23:50Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Controller to manage search indexes.
 *
 * @package     (Admin)
 * @subpackage Admin_SearchController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
class Admin_SearchController extends Internal_Controller_Action 
{
    /**
     * displays logs based on search criteria
     */
    public function indexAction()
    {
        $this->view->title = "Search Manager";
    }

    /**
     * shows the details of the log message
     *
     */
    public function reindexAction()
    {
    	set_time_limit(0);
    	
        $get    = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');
        
        $target = $filter->filter($get['target']);
        
        if ($target == 'workshops') {
        	$workshop = new Workshop();
        	
        	$workshops = $workshop->fetchAll();
        	
        	foreach ($workshops as $w) {
        		$workshop->index($w);
        	}
        	
        } elseif ($target == 'tags') {
        	$tag = new Tag();
        	
        	$tags = $tag->fetchAll();
        	
        	foreach ($tags as $t) {
        		$tag->index($t->tagId, $t->name);
        	}
        }
        
        $this->_redirect('/admin/search/');
    }
}