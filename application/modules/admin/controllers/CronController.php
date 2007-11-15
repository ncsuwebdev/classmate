<?php
/**
 * Aerial
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
 * @package    Aerial (Admin)
 * @subpackage Admin_CronController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: SemesterController.php 160 2007-07-20 14:15:52Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Controller to show the status of all cron jobs running in the system
 *
 * @package    Aerial (Admin)
 * @subpackage Admin_CronController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
class Admin_CronController extends Internal_Controller_Action 
{

    /**
     * Runs when the class is initialized.  Sets up the view instance and the
     * various models used in the class.
     *
     */
    public function init()
    {
        $zcf = Zend_Controller_Front::getInstance();

        $this->_front = $zcf;

        $this->_acl      = $zcf->getParam('acl');
        $this->_role     = Itdcs_Authz::getInstance()->getRole();
        $this->_resource = $zcf->getRequest()->module . '_' . $zcf->getRequest()->controller;

        $this->_logger = Zend_Registry::get('logger');
    }


    /**
     * shows all the semesters
     *
     */
    public function indexAction()
    {
        $this->view->acl = array(
            'add'    => false,
            'edit'   => false,
            'toggle' => $this->_acl->isAllowed($this->_role, $this->_resource, 'toggle'),
            );

        $cs = new CronStatus();

        $jobs = $cs->getAvailableCronJobs();
        if ($jobs === false) {
            $this->printError('Error getting cron jobs', $cs->getMessages());
        }

        if (count($jobs) != 0) {
            $this->view->javascript = 'sortable.js';
        }

        $this->view->cronjobs       = $jobs;
        $this->view->title          = "Cron Job Status";
    }

    public function toggleAction()
    {
        $cs = new CronStatus();

        $filter = Zend_Registry::get('inputFilter');

        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $post = Zend_Registry::get('post');

            $path = $filter->filter($post['path']);
            $status = ($filter->filter($post['status']) == 'enable') ? 'enabled' : 'disabled';

            $result = $cs->setCronStatus($path, $status);

            if ($result === false) {
                $this->printError('error setting status', $cs->getMessages());
            }

            $this->_redirect('/admin/cron/');
        } else {
            $get = Zend_Registry::get('get');
            $path = $filter->filter($get['path']);

            if (isset($get['status'])) {
                $status = $filter->filter($get['status']);
            } else {
                $cj = $cs->find($path);

                if ($cj === false) {
                    $this->printError('Error getting path', $cs->getMessages());
                }

                if ($cj->count() != 1) {
                    $cj = array(
                        'status' => 'disabled',
                        'path'   => $path
                        );

                    $status = 'disabled';
                } else {

                    $cj = $cj->current()->toArray();

                    $status = $cj['status'];
                }
            }

            if ($path == 'all') {
                $this->view->displayPath = 'all cron jobs';
            } else {
                $this->view->displayPath = $path;
            }

            $this->view->path = $path;
            $this->view->status = ($status == 'enabled') ? 'disable' : 'enable';

            $this->view->title          = "Toggle Cron Job Status";
        }
    }
}