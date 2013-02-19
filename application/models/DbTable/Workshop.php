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
 * @package    Workshop
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the workshops
 *
 * @package    Workshop
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class App_Model_DbTable_Workshop extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_workshop';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = 'workshopId';
    
    public function getRelatedWorkshops($workshopId, $limit = 'none')
    {
        $tag = new Tag();
        $tags = $tag->getTagsForAttribute('workshopId', $workshopId);
        
        $tagNames = array();
        foreach ($tags as $t) {
            $tagNames[] = $t['name'];
        }
        
        return $this->search(implode(' ', $tagNames), $limit);
    }
    
    public function search($query, $limit = 'none')
    {
        $config = Zend_Registry::get('config');
       
        try {
            $index = Zend_Search_Lucene::open($config->app->search->workshopIndexPath);
        } catch (Exception $e) {
            $index = Zend_Search_Lucene::create($config->app->search->workshopIndexPath);
        }
                
        if ($limit != 'none') {
            Zend_Search_Lucene::setResultSetLimit($limit);
        }
        
        return $index->find($query);
    }
    
    /**
     * Deletes a workshop from the search index
     *
     * @param Zend_Db_Table_Row or int $workshopId
     */
    public function deleteFromIndex($workshopId)
    {
        $config = Zend_Registry::get('config');
        
        if ($workshopId instanceof Zend_Db_Table_Row) {
            $thisWorkshop = $workshopId;
            $workshopId = $thisWorkshop->workshopId;
            
        } else {
            $thisWorkshop = $this->find($workshopId);
            if (is_null($thisWorkshop)) {
                return;
            }
        }
        
        try {
            $index = Zend_Search_Lucene::open($config->app->search->workshopIndexPath);
        } catch (Exception $e) {
            $index = Zend_Search_Lucene::create($config->app->search->workshopIndexPath);
        }
        
        $term  = new Zend_Search_Lucene_Index_Term($workshopId, 'workshopId');
        $query = new Zend_Search_Lucene_Search_Query_Term($term);
        
        $hits = $index->find($query);
        
        foreach ($hits as $hit) {
            $index->delete($hit->id);
        }
    }
    
    /**
     * Adds a workshop to the search index
     *
     * @param Zend_Db_Table_Row or int $workshopId
     */
    public function index($workshopId)
    {
        
        $config = Zend_Registry::get('config');
        
        if ($workshopId instanceof Zend_Db_Table_Row) {
            $thisWorkshop = $workshopId;
            $workshopId = $thisWorkshop->workshopId;
            
        } else {
            $thisWorkshop = $this->find($workshopId);
            if (is_null($thisWorkshop)) {
                return;
            }
        }
        
        $tag = new Tag();
        $tags = $tag->getTagsForAttribute('workshopId', $workshopId);
        
        $tagNames = array();
        foreach ($tags as $t) {
            $tagNames[] = $t['name'];
        }
        
        try {
            $index = Zend_Search_Lucene::open($config->app->search->workshopIndexPath);
        } catch (Exception $e) {
            $index = Zend_Search_Lucene::create($config->app->search->workshopIndexPath);
        }
        
        $term  = new Zend_Search_Lucene_Index_Term($workshopId, 'workshopId');
        $query = new Zend_Search_Lucene_Search_Query_Term($term);
        
        $hits = $index->find($query);
        
        foreach ($hits as $hit) {
            $index->delete($hit->id);
        }
        
        $doc = new Zend_Search_Lucene_Document();
        
        $doc->addField(Zend_Search_Lucene_Field::Keyword('workshopId', $workshopId));
        
        $doc->addField(Zend_Search_Lucene_Field::Text('title', $thisWorkshop->title));
        
        $doc->addField(Zend_Search_Lucene_Field::Text('tags', implode(',', $tagNames)));
        
        $doc->addField(Zend_Search_Lucene_Field::Keyword('categoryId', $thisWorkshop->categoryId));
        
        $doc->addField(Zend_Search_Lucene_Field::UnIndexed('description', $thisWorkshop->description));
        
        $index->addDocument($doc);
    }
    
   /**
     * Gets the form for adding and editing a location
     *
     * @param array $values
     * @return Zend_Form
     */
    public function form($values = array())
    {
        require_once(APPLICATION_PATH . '/models/Workshop/Category.php');
        
        $form = new Zend_Form();
        $form->setAttrib('id', 'workshopForm')
             ->setDecorators(array(
                     'FormElements',
                     array('HtmlTag', array('tag' => 'div', 'class' => 'zend_form')),
                     'Form',
             ));
             
        $title = $form->createElement('text', 'title', array('label' => 'Workshop Title:'));
        $title->setRequired(true)
              ->addFilter('StringTrim')
              ->addFilter('StripTags')
              ->setAttrib('maxlength', '255')
              ->setValue((isset($values['title']) ? $values['title'] : ''));
              
        $group = $form->createElement('select', 'group', array('label' => 'Offered By:'));
        $group->addMultiOption('groupId1', 'Test Group1');
        $group->addMultiOption('groupId2', 'Test Group2');
        $group->setValue((isset($values['group']) ? $values['group'] : null));
        //TODO: change this to add all the enabled groups in the database
              
        $tags = $form->createElement('text', 'tags', array('label' => 'Tags:'));
        $tags->setRequired(false)
             ->addFilter('StringTrim')
             ->addFilter('StripTags')
             ->setValue((isset($values['tags'])) ? implode(', ', $values['tags']) : '');
              
        $status = $form->createElement('select', 'status', array('label' => 'Status:'));
        $status->addMultiOption('enabled', 'Enabled');
        $status->addMultiOption('disabled', 'Disabled');
        $status->setValue((isset($values['status']) ? $values['status'] : 'enabled'));
        
        $category = new Category();
        $categoryList = $category->fetchAll(null, 'name');

        $categories = $form->createElement('select', 'categoryId', array('label' => 'Worshop Category: '));
        $categories->setRequired(true);
        
        foreach ($categoryList as $category) {
            $categories->addMultiOption($category->categoryId, $category->name);
        }
        
        $categories->setValue((isset($values['categoryId']) ? $values['categoryId'] : ''));
        
        
        $prerequisites = $form->createElement('textarea', 'prerequisites', array('label' => 'workshop-index-add:preRequisites'));
        $prerequisites->setRequired(false)
                 ->addFilter('StringTrim')
                 ->setAttrib('style', 'width: 95%; height: 200px;')
                 ->setValue((isset($values['prerequisites']) ? $values['prerequisites'] : ''));

        $featured = $form->createElement('checkbox', 'features', array('label' => 'Featured?'));
        $featured->setValue((isset($values['featured']) ? $values['featured'] : null));                 
              
        $description = $form->createElement('textarea', 'description', array('label' => 'Description:')); 
        $description->setRequired(false)
                    ->addFilter('StringTrim')
                    ->setAttrib('style', 'width: 95%; height: 200px;')
                    ->setValue((isset($values['description']) ? $values['description'] : ''));
                            
        $editors = $form->createElement('multiselect', 'editors', array('label' => 'Workshop Editors:'));
        $editors->setRequired(false)
                ->setAttrib('size', '10');
                
        $account = new Ot_Account();
        $accounts = $account->fetchAll(null, array('lastName', 'firstName'));
        
        foreach ($accounts as $a) {
            $editors->addMultiOption($a->accountId, $a->firstName . ' ' . $a->lastName . ' (' . $a->username . ')');
        }
        
        $editors->setValue((isset($values['editors']) ? $values['editors'] : ''));
                    
        $submit = $form->createElement('submit', 'submitButton', array('label' => 'Submit'));
        $submit->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formSubmit'))
                 ));

        $cancel = $form->createElement('button', 'cancel', array('label' => 'Cancel'));
        $cancel->setAttrib('id', 'cancel');
        $cancel->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formButton'))
                ));
        
        $form->addElements(array($status, $title, $categories, $group, $tags, $description, $prerequisites, $editors));

        $form->setElementDecorators(array(
                  'ViewHelper',
                  'Errors',
                  array('HtmlTag', array('tag' => 'div', 'class' => 'elm')),
                  array('Label', array('tag' => 'span')),
              ))
             ->addElements(array($submit, $cancel));

        if (isset($values['workshopId'])) {

            $workshopId = $form->createElement('hidden', 'workshopId');
            $workshopId->setValue($values['workshopId']);
            $workshopId->setDecorators(array(
                array('ViewHelper', array('helper' => 'formHidden'))
            ));

            $form->addElement($workshopId);
        }
        return $form;
    }
}