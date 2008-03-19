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
    
    public function deleteDocument($documentId)
    {
        $docMap = new DocumentMap();
        
        $where =  $docMap->getAdapter()->quoteInto('documentId = ?', $documentId);
        
        $mapping = $docMap->fetchRow($where);
                
        $where = $docMap->getAdapter()->quoteInto('documentId = ?', $documentId);
        
        $docMap->delete($where);
        
        $this->delete($where);
        
        if ($mapping->attributeName == "workshopId") {
            $this->rebuildZipFile($mapping->attributeId);    
        }
    }
    
    public function rebuildZipFile($workshopId)
    {                    
        $dm = new DocumentMap();
        
        $where =  $dm->getAdapter()->quoteInto('attributeId = ?', $workshopId);
        $where .= " AND ";
        $where .= $dm->getAdapter()->quoteInto('attributeName = ?', 'workshopId');
        
        $mappings = $dm->fetchAll($where);
            
        $docs = array();
        
        foreach ($mappings as $m) {
            $docs[] = $this->find($m->documentId);
        }
        
        $uc = Zend_Registry::get('userConfig');
           
        if (!is_readable($uc['fileUploadPathWorkshop']['value'])) {
            throw new Internal_Exception_Data('Target directory ' . $uc['fileUploadPathWorkshop']['value'] . ' is not readable');
        }
        
        $zip = new Zip($uc['fileUploadPathWorkshop']['value'] . '/' . $workshopId . '/all_handouts.zip');
               
        foreach ($docs as $d) {
            
            $target = $uc['fileUploadPathWorkshop']['value'] . '/' . $workshopId . '/' . $d->name;

            if (is_file($target)) {
                $zip->addFiles($target);
                 
            } else {
                throw new Internal_Exception_Data('File not found: ' . $target);        
            }
        }
        
        $zip->createZipFile();
    }
}