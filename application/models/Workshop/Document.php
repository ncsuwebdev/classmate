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
 * @package    Document
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @version    SVN: $Id: $
 */

/**
 * Model to interact with the Documents
 *
 * @package    Workshop_Document
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
class Workshop_Document extends Ot_Db_Table
{
    /**
     * Name of the table in the database
     *
     * @var string
     */
    protected $_name = 'tbl_workshop_document';

    /**
     * Primary key of the table
     *
     * @var string
     */
    protected $_primary = 'workshopDocumentId';
    
    public function getDocumentsForWorkshop($workshopId)
    {
    	$where = $this->getAdapter()->quoteInto('workshopId = ?', $workshopId);
   	
    	return $this->fetchAll($where, 'name')->toArray();
    }
    
    public function rebuildZipFile($workshopId)
    {                    
        $documents = $this->getDocumentsForWorkshop($workshopId);
        
        $config = Zend_Registry::get('config');
           
        if (!is_readable($config->user->fileUploadPathWorkshop->val)) {
            throw new Ot_Exception_Data('Target directory is not readable');
        }
        
        $zip = new Zip($config->user->fileUploadPathWorkshop->val . '/' . $workshopId . '/all_handouts.zip');
               
        foreach ($documents as $d) {
            $target = $config->user->fileUploadPathWorkshop->val . '/' . $workshopId . '/' . $d['name'];

            if (is_file($target)) {
                $zip->addFiles($target);
            } else {
                throw new Ot_Exception_Data('File not found: ' . $target);        
            }
        }
        
        $zip->createZipFile();
    }
    
   /**
     * Gets the form for adding and editing a document
     *
     * @param array $values
     * @return Zend_Form
     */
    public function form($values = array())
    {
    	$config = Zend_Registry::get('config');
    	
        $form = new Zend_Form();
        $form->setAttrib('id', 'documentForm')
             ->setAttrib('enctype', 'multipart/form-data')
             ->setDecorators(array(
                     'FormElements',
                     array('HtmlTag', array('tag' => 'div', 'class' => 'zend_form')),
                     'Form',
             ));
        
        $file = $form->createElement('file', 'document', array('label' => 'Upload File:')); 
        $file->setRequired(true)
             ->addValidator('Count', false, 1)
             ->addValidator('Size', false, 10240000)
             ->addValidator('Extension', false, $config->user->fileUploadAllowableExtensions->val ? $config->user->fileUploadAllowableExtensions->val : "")
             ;
             
        if (!isset($values['workshopDocumentId'])) {
        	$form->addElement($file);
        }

        $title = $form->createElement('text', 'description', array('label' => 'File Description:'));
        $title->setRequired(true)
              ->addFilter('StringTrim')
              ->addFilter('StripTags')
              ->setAttrib('maxlength', '255')
              ->setValue((isset($values['description']) ? $values['description'] : ''));
              
        $form->addElement($title);
                
        $submit = $form->createElement('submit', 'submitButton', array('label' => 'Submit'));
        $submit->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formSubmit'))
                 ));

        $cancel = $form->createElement('button', 'cancel', array('label' => 'Cancel'));
        $cancel->setAttrib('id', 'cancel');
        $cancel->setDecorators(array(
                   array('ViewHelper', array('helper' => 'formButton'))
                ));
        
        $form->setElementDecorators(array(
                  'ViewHelper',
                  'Errors',
                  array('HtmlTag', array('tag' => 'div', 'class' => 'elm')),
                  array('Label', array('tag' => 'span')),
              ))
             ->addElements(array($submit, $cancel));
             
        $file->setDecorators(array(
              'File',
              'Errors',
              array('HtmlTag', array('tag' => 'div', 'class' => 'elm')),
              array('Label', array('tag' => 'span')),
          ));

        
        if (isset($values['workshopDocumentId'])) {

            $workshopDocumentId = $form->createElement('hidden', 'workshopDocumentId');
            $workshopDocumentId->setValue($values['workshopDocumentId']);
            $workshopDocumentId->setDecorators(array(
                array('ViewHelper', array('helper' => 'formHidden'))
            ));

            $form->addElement($workshopDocumentId);
        }
        return $form;
    }    
}