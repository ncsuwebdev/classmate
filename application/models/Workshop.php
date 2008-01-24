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
 * @subpackage Workshop
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the workshops
 *
 * @package    Classmate
 * @subpackage Workshop
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Workshop extends Ot_Db_Table
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
            $index = Zend_Search_Lucene::open($config->search->workshopIndexPath);
        } catch (Exception $e) {
        	$index = Zend_Search_Lucene::create($config->search->workshopIndexPath);
        }
        
        if ($limit != 'none') {
            Zend_Search_Lucene::setResultSetLimit($limit);
        }
        
        return $index->find($query);
    }
    
    public function index($workshopId)
    {
    	$config = Zend_Registry::get('config');
    	
    	$thisWorkshop = $this->find($workshopId);
    	if (is_null($thisWorkshop)) {
    		return;
    	}
    	
    	$tag = new Tag();
    	$tags = $tag->getTagsForAttribute('workshopId', $workshopId);
    	
    	$tagNames = array();
    	foreach ($tags as $t) {
    		$tagNames[] = $t['name'];
    	}
    	
        try {
            $index = Zend_Search_Lucene::open($config->search->workshopIndexPath);
        } catch (Exception $e) {
            $index = Zend_Search_Lucene::create($config->search->workshopIndexPath);
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
    	
    	$doc->addField(Zend_Search_Lucene_Field::UnIndexed('description', $thisWorkshop->description));
    	
    	$index->addDocument($doc);
    	
    }
}