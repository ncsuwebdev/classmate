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
 * @subpackage Document
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the Documents
 *
 * @package    Classmate
 * @subpackage Document
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Document extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_document';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = 'documentId';
    
    public function getDocumentsForAttribute($attributeName, $attributeId)
    {
    	$docMap = new DocumentMap();
    	
    	$where = $docMap->getAdapter()->quoteInto('attributeName = ?', $attributeName) . 
    	   ' AND ' . 
    	   $docMap->getAdapter()->quoteInto('attributeId = ?', $attributeId);
    	   
    	$docs = $docMap->fetchAll($where);
    	
    	$docIds = array();
    	foreach ($docs as $d) {
    		$docIds[] = $d->documentId;
    	}
    	
    	if (count($docIds) == 0) {
    		return array();
    	}
    	
    	$where = $this->getAdapter()->quoteInto('documentId IN (?)', $docIds);
    	
    	return $this->fetchAll($where, 'name')->toArray();
    }
}