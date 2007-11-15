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
 * @package
 * @subpackage Internal_Plugin_View
 * @category   Front Controller Plugin
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: Auth.php 189 2007-07-31 19:27:49Z jfaustin@EOS.NCSU.EDU $
 */

class Internal_Plugin_View extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopShutdown()
    {
        $vr = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
               
        if (empty($vr->view) || $vr->getNeverRender()) {
            return;
        }

        $view     = $vr->view;
        $response = $this->getResponse();
        
        $view->actionTemplate = $response->getBody();
        
        $response->setBody($view->render('site.tpl'));
        
    }
}