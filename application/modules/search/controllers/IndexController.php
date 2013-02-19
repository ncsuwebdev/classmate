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
 * @package    Search_IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @version    SVN: $Id: LogController.php 210 2007-08-01 18:23:50Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * Controller to manage search indexes.
 *
 * @package    Search_IndexController
 * @category   Controller
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
class Search_IndexController extends Zend_Controller_Action 
{

    public function tagAction()
    {
        $this->_helper->viewRenderer->setNeverRender();
        $this->view->layout()->disableLayout();
                
        $get = Zend_Registry::get('getFilter');

        $search = $get->q;
        
        $tag = new Tag();
        
        $tags = $tag->fetchAll();
        
        //echo $search;
        if ($search != '') {
            foreach ($tags as $t) {
                if (strpos(strtolower($t->name), $search) !== false) {
                    echo $t->name . "|" . $t->tagId . "\n";
                }
            }
        }
    }
 
    /**
     * reindexes the selected search index
     *
     */
    public function reindexAction()
    {
        $form = Ot_Form_Template::delete('reindex', 'search-index-reindex:reindex');
        
        if ($this->_request->isPost() && $form->isValid($_POST)) {
            set_time_limit(0);
            
            $workshop = new Workshop();
                
            $workshops = $workshop->fetchAll();
                
            foreach ($workshops as $w) {
                $workshop->index($w);
            }
            
            $this->_helper->flashMessenger->addMessage('msg-info-reindexed');
            
            $this->_redirect('/');
            
        }
        
        $this->view->form = $form;
        $this->_helper->pageTitle('search-index-reindex:title');
    }
}