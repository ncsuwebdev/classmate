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
 * @subpackage Tag
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with requests for tags
 *
 * @package    Classmate
 * @subpackage Tag
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Tag extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_tag';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = 'tagId';
    
    public function getTagsForAttribute($attributeName, $attributeId)
    {
        $tagMap = new TagMap();
        
        $where = $tagMap->getAdapter()->quoteInto('attributeName = ?', $attributeName) . 
           ' AND ';
        
        if (is_array($attributeId)) {
        	if (count($attributeId) == 0) {
        		return array();
        	}
        	$where .= $tagMap->getAdapter()->quoteInto('attributeId IN (?)', $attributeId);
        } else {
        	$where .= $tagMap->getAdapter()->quoteInto('attributeId = ?', $attributeId);
        }           
                   
        $tags = $tagMap->fetchAll($where);
        
        $tagIds = array();
        foreach ($tags as $t) {
            $tagIds[] = $t->tagId;
        }
        
        if (count($tagIds) == 0) {
            return array();
        }
        
        $where = $this->getAdapter()->quoteInto('tagId IN (?)', $tagIds);
        
        return $this->fetchAll($where, 'name')->toArray();    	
    }
    
    public function getAttributeIdsWithTag($attributeName, $tagId)
    {
    	$tagMap = new TagMap();
    	$ids = array();
    	
    	$dba = $this->getAdapter();

    	$where = $dba->quoteInto('attributeName = ?', $attributeName) . ' AND '; 
    	 
        if (is_array($tagId) && count($tagId) != 0) {
            $where .= $dba->quoteInto('tagId IN (?)', $tagId);
        } elseif ($tagId != '') {
            $where .= $dba->quoteInto('tagId = ?', $tagId);
        } else {
        	return array();   	
        }
        
    	$result = $tagMap->fetchAll($where, 'attributeId DESC');
    	
    	foreach ($result as $r) {
    		$ids[] = $r->attributeId;
    	}
    	
    	return $ids;
    }
    
    /**
     * Given an array of tags, sets them as tags for a given attribute
     *
     * @param string $attributeName
     * @param mixed $attributeId
     * @param array $tags
     */
    public function setTagsForAttribute($attributeName, $attributeId, $tags)
    {
        $tagMap = new TagMap();
        $dba = $tagMap->getAdapter();
        
        $dba->beginTransaction();
        
        $where = $tagMap->getAdapter()->quoteInto('attributeName = ?', $attributeName) . 
           ' AND ' . 
           $tagMap->getAdapter()->quoteInto('attributeId = ?', $attributeId);

        try {
            $tagMap->delete($where);
        } catch (Exception $e) {
        	$dba->rollback();
        	throw $e;
        }
        
        $realTags = array();
        foreach ($tags as $t) {
        	if (trim($t) != '') {
        		$realTags[] = $t;
        	}
        }
        
        $tags = $realTags;
        
        if (count($tags) != 0) {
        	$ids = array();
        	$ignore = array();
        	
        	/**
        	 * @todo this is a fix that had to be added because quoteInto does a 
        	 * foreach by reference on the array values, causing them to change
        	 * the values in the base array.  When that bug gets fixed, this
        	 * can be removed and $tags can be passed to quoteInto
        	 */
        	$tagList = array();
        	foreach ($tags as $t) {
        		$tagList[] = trim($t);
        	}
        	
        	$where = $dba->quoteInto('name IN (?)', $tagList);
        	
        	$existing = $this->fetchAll($where);

        	foreach ($existing as $e) {
        		$ids[] = $e->tagId;
        		$ignore[] = $e->name;
        	}
        	
        	$new = array_diff($tags, $ignore);
        	
        	
        	foreach ($new as $n) {
        		try {
        		    $this->insert(array('name' => $n));
        		} catch (Exception $e) {
        			$dba->rollBack();
        			throw $e;
        		}

        		$ids[] = $dba->lastInsertId($this->_name);
        	}
        	
        	foreach ($ids as $i) {
        		$data = array(
        		   'attributeName' => $attributeName,
        		   'attributeId'   => $attributeId,
        		   'tagId'         => $i,
        		);
        		
        		try {
        			$tagMap->insert($data);
        		} catch (Exception $e) {
        			$dba->rollback();
        			throw $e;
        		}
        	}
        }
        
        $dba->commit();        
    }
    
    public function insert(array $data)
    {
    	$tagId = parent::insert($data);
        
    	$this->index($data['tagId'], $data['tagName']);
    	
        return $tagId;
    }
    
    public function index($tagId, $tagName)
    {
        $config = Zend_Registry::get('config');
        
        try {
            $index = Zend_Search_Lucene::open($config->search->tagIndexPath);
        } catch (Exception $e) {
            $index = Zend_Search_Lucene::create($config->search->tagIndexPath);
        }
        
        $term  = new Zend_Search_Lucene_Index_Term($tagId, 'tagId');
        $query = new Zend_Search_Lucene_Search_Query_Term($term);
        
        $hits = $index->find($query);
        
        foreach ($hits as $hit) {
            $index->delete($hit->id);
        }
        
        $doc = new Zend_Search_Lucene_Document();
        
        $doc->addField(Zend_Search_Lucene_Field::Keyword('tagId', $tagId));
        
        $doc->addField(Zend_Search_Lucene_Field::Text('name', $tagName));
        
        $index->addDocument($doc);    	
    }
}
