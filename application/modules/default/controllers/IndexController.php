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
        $this->view->title = 'Welcome to Classmate';
        $this->view->showNews = true;
    }
    
    public function searchAction()
    {
        $this->_helper->viewRenderer->setNeverRender();
        
        echo Zend_Json_Encoder::encode(array("excel", "photoshop", "php", "word"));
    }

    public function imageAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        
        $get = Zend_Registry::get('get');
        $filter = Zend_Registry::get('inputFilter');

        if (!isset($get['imageId'])) {
            $imageId = 0;
        } else{
            $imageId = $filter->filter($get['imageId']);
        }

        $image = new Image;

        $result = $image->find((int)$imageId);

        header("Content-type: " . $result['contentType']);
        echo $result['source'];
    }   
}
