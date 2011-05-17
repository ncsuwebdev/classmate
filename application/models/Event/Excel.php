<?php

Class Event_Excel {
	
    protected $_excelDoc = null;

    protected $_date = null;

    protected $_headerStyleArray = null;
    
    protected $_tableHeaderStyleArray = null;
    
    protected $_contentStyleArray = null;

    const A = 65;

    public function __construct()
    {
    	
    	$this->_headerStyleArray = array(
    		'font' => array('bold' => true)
    	
    	);
    	
        $this->_tableHeaderStyleArray = array(
    		'font' => array(
    		    'bold' => true,
        		'size' => 14
    		),
        );
        
        $this->_contentStyleArray = array(
			'font' => array(
        		'size'	=> 12
        	)
        );
        
    }

    public function generateSignupSheet($event, $includeEndingTerms = false, $includeNotEndingTerms = false)
    {
        $this->_excelDoc = new PHPExcel();

        // Set properties
        $this->_excelDoc->getProperties()->setCreator("Classmate Application");
        $this->_excelDoc->getProperties()->setLastModifiedBy("No user");
        $this->_excelDoc->getProperties()->setTitle('');
        $this->_excelDoc->getProperties()->setSubject('');
        $this->_excelDoc->getProperties()->setDescription('');

        $this->_excelDoc->addSheet($this->_signupSheet($event, $includeEndingTerms, $includeNotEndingTerms));

        // remove the first sheet that was added by default that we didn't use
        $this->_excelDoc->removeSheetByIndex(0);

        return $this->_excelDoc;
    }

    protected function _signupSheet($event, $includeEndingTerms, $includeNotEndingTerms)
    {
        $sheet = new PHPExcel_Worksheet($this->_excelDoc, 'Signup Sheet for Workshop ' . $event['workshopTitle']);
        
        // Set up the margins so the header doesn't bleed into the page
        $sheet->getPageMargins()->setTop(1.5);
        
        // Make a three column page layout
        $sheet->getColumnDimension('A')->setWidth(16);
        $sheet->getColumnDimension('B')->setWidth(16);
        $sheet->getColumnDimension('C')->setWidth(45);
        
        $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/config.xml', 'production');
        
        $date = new DateTime($event['date']);
        $startTime = new DateTime($event['startTime']);
        $endTime = new DateTime($event['endTime']);
        
        // Set the header on odd pages.
       	// The code formatting is off because the header doesn't ignore spaces.
       	/*
       	 * Format:
       	 *		Title
       	 *		Room name
       	 *		date('D, M d, Y') (startTime('g:i A') - endTime('g:i A'))
       	 *		Instructors  
       	 * 
       	 */
        $sheet->getHeaderFooter()->setOddHeader('&C&B&14' . $event['workshopTitle'] . '&14&B&12 ' . chr(10)
 		.$event['location'] . chr(10)   
 		.$date->format('l, M d, Y') . '(' . $startTime->format('g:i A') . ' - ' . $endTime->format('g:i A') . ')' . chr(10)
 		.'Instructor: ' . implode(',', $event['instructors']) . '&12&C');

        // Write Column Headers for the table
        $sheet->setCellValue('A1', 'First Name');
        $sheet->setCellValue('B1', 'Last Name');
        $sheet->setCellValue('C1', 'Signature');

        // reformat it a little bit in a simpler way for us to use it in our
        // spreadsheet printin' loop
        $rows = array();

        foreach ($event['attendeeList'] as $a) {

			$rows[] = array(
				$a['firstName'],
				$a['lastName']
			);
        }
        
        $signin = new PHPExcel_Style();
        $signin->applyFromArray(array(
        	'borders' => array(
        		'bottom' => array(
        		'style' => PHPExcel_Style_Border::BORDER_THIN)
        	)
        ));

        $rowCounter = 3;
        foreach ($rows as $row) {

            $row = array_values($row);

            // put the totals in the row
            $char = self::A;
            foreach ($row as $cell) {
                $sheet->setCellValue(chr($char) . $rowCounter, $cell);
                
                $char++;
            }

            $rowCounter++;
        }

        $tableHeaderStyle = new PHPExcel_Style();
        $tableHeaderStyle->applyFromArray($this->_tableHeaderStyleArray);
        
        $tableBodyStyle = new PHPExcel_Style();
        $tableBodyStyle->applyFromArray($this->_contentStyleArray);
        
        $sheet->setSharedStyle($tableHeaderStyle, 'A1:C1');
        $sheet->setSharedStyle($tableBodyStyle, 'A3:B' . ($rowCounter - 1));
        $sheet->setSharedStyle($signin, 'C3:C' . ($rowCounter - 1));

        return $sheet;
    }
}