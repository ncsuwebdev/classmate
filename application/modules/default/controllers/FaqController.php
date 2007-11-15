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
 * @subpackage FaqController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: FaqController.php 157 2007-07-20 13:32:41Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Shows frequently asked questions
 *
 * @package    
 * @subpackage FaqController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
class FaqController extends Internal_Controller_Action 
{
    /**
     * Runs when the class is initialized.  Sets up the view instance and the
     * various models used in the class.
     *
     */
    public function init()
    {
        $this->_logger = Zend_Registry::get('logger');
    }


    /**
     * shows all FAQ's
     *
     */
    public function indexAction()
    {
        $this->view->title = 'Frequently Asked Questions';
    }
}
