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
 * @package    
 * @subpackage Admin_SemesterController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: SemesterController.php 210 2007-08-01 18:23:50Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Controller to show semesters that are in the system
 *
 * @package    
 * @subpackage Admin_SemesterController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
class Admin_SemesterController extends Internal_Controller_Action
{
    /**
     * shows all the semesters
     *
     */
    public function indexAction()
    {
        $this->view->acl = array(
            'edit'   => $this->_acl->isAllowed($this->_role, $this->_resource, 'edit'),
            );

        $semester = new Semester();

        $semesters = $semester->fetchAll(null, 'startDate')->toArray();

        $temp = array();

        foreach ($semesters as $s) {
            $activeDt = new Zend_Date($s['startDate']);
            $activeDt->subDay($s['preSemesterActivateDays']);

            $gcOpenDt = new Zend_Date($s['startDate']);
            $gcOpenDt->subDay($s['gcPreSemesterAvailableDays']);

            $ctExpireDt = new Zend_Date($s['startDate']);
            $ctExpireDt->addDay($s['gcPostSemesterComtechAvailableDays']);

            $s['activeDt'] = $activeDt->getTimestamp();
            $s['gcOpenDt'] = $gcOpenDt->getTimestamp();
            $s['ctExpireDt'] = $ctExpireDt->getTimestamp();

            $hide = new Zend_Date();
            $hide->subMonth(6);

            if ($s['gcOpenDt'] > $hide->getTimestamp()) {
                $temp[] = $s;
            }
        }


        $current = $semester->getCurrentSemester();

        if (count($temp) != 0) {
            $this->view->javascript = 'sortable.js';
        }

        $this->view->current        = $current->semesterId;
        $this->view->semesters      = $temp;
        $this->view->title          = "Semester Manager";
    }

    public function editAction()
    {
        $filter = Zend_Registry::get('inputFilter');

        $semester = new Semester();

        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $post = Zend_Registry::get('post');

            if (!isset($post['semesterId'])) {
                throw new Internal_Exception_Input('semester ID not set in query string');
            }

            $data = array(
                'semesterId'                         => (int)$filter->filter($post['semesterId']),
                'gcPreSemesterAvailableDays'         => $filter->filter($post['gcPreSemesterAvailableDays']),
                'preSemesterActivateDays'            => $filter->filter($post['preSemesterActivateDays']),
                'gcPostSemesterComtechAvailableDays' => $filter->filter($post['gcPostSemesterComtechAvailableDays']),
                );

            $semester->update($data, null);

            $this->_redirect('/admin/semester');

        } else {
            $get = Zend_Registry::get('get');

            if (!isset($get['semesterId'])) {
                throw new Internal_Exception_Input('semester ID not set in query string');
            }

            $semesterId = $filter->filter($get['semesterId']);

            $sem = $semester->find((int)$semesterId);

            if (is_null($sem)) {
                throw new Internal_Exception_Input('semester not found');
            }

            $this->view->semester       = $sem->toArray();
            $this->view->title          = "Semester Manager";
        }
    }
}