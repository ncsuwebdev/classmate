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
 * @package    ClassMate (Admin)
 * @subpackage Admin_StatsController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: LogController.php 210 2007-08-01 18:23:50Z jfaustin@EOS.NCSU.EDU $
 */
class Admin_StatsController extends Internal_Controller_Action 
{
    public function indexAction()
    {
        $stats = new Stats();
        
        $this->view->loginCount = $stats->getLoginCount();
        
        $this->view->upcomingEventCounts = $stats->getUpcomingEventsCount();
        $this->view->pastEventCounts = $stats->getPastEventsCount();
        
        $this->view->javascript = array('excanvas.js', 'plootr.js', 'tabletochart.js');
    }
}