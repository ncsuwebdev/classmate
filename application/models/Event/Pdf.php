<?php

Class Event_Pdf {
	
	protected $_pdf;
	
	protected $_style;
	
	protected $_offsets;
	
	/**
	 * Basic constructor
	 * 
	 * @param String $filename the filen
	 */
	public function __construct() {
		
		$this->_pdf = new Zend_Pdf();
		
		$this->_style = new Zend_Pdf_Style();
		$this->_style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
		$this->_style->setLineColor(new Zend_Pdf_Color_GrayScale(0.2));

		$this->_offsets = array();
		$this->_offsets['top'] = 753;
		$this->_offsets['left'] = 45;
		$this->_offsets['right'] = 555;
		$this->_offsets['bottom'] = 50;
	}
	
	/**
	 * Generates the signup sheet for the given event
	 * 
	 * @param $event
	 */
	public function generateSignupSheet($event) {
		
		/**
		 * Calculate how many pages are needed
		 */
		$entriesPerPage = 37;
		$pageCount = ceil(count($event['attendeeList'])/$entriesPerPage);
		
		/*
		 * Set up the offsets for the three columns (first, last, signature)
		 */
		$this->_offsets['columnLeft'] = 45;
		$this->_offsets['columnMiddle'] = 150;
		$this->_offsets['columnRight'] = 250;
		
		/**
		 * Fill each page with user information
		 */
		for($i = 0; $i < $pageCount; $i++) {
			
			$page = $this->_pdf->newPage(Zend_Pdf_Page::SIZE_LETTER);
			
			$this->_pdf->pages[] = $page;
			$lineCounter = 0;
			
			$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 9);
			$page->drawText('Page ' . ($i + 1) . ' of ' . $pageCount, $this->_offsets['right'] - 40, $this->_offsets['top']);
	        
			$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 12);
			$page->drawText($event['workshopTitle'], $this->center($event['workshopTitle']), $this->getLineOffset($lineCounter++));
			
			$page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
			$page->drawText($event['location'], $this->center($event['location']), $this->getLineOffset($lineCounter++));
			
			$date = new DateTime($event['date']);
	        $startTime = new DateTime($event['startTime']);
	        $endTime = new DateTime($event['endTime']);
	        
	        $dateString = $date->format('l, M d, Y') . '(' . $startTime->format('g:i A') . ' - ' . $endTime->format('g:i A') . ')';
	        
	        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
	        $page->drawText($dateString, $this->center($dateString), $this->getLineOffset($lineCounter++));
	        
	        $instructorString = 'Instructor'. (count($event['instructors']) > 1 ? 's' : '') .': ' . implode(', ', $event['instructors']);
	        
	        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
	        $page->drawText($instructorString, $this->center($instructorString), $this->getLineOffset($lineCounter++));
	        
	        $lineCounter += 2;
	        
	        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 12);
	        
	        $page->drawText('First Name', $this->_offsets['columnLeft'], $this->getLineOffset($lineCounter));
	        $page->drawText('Last Name', $this->_offsets['columnMiddle'], $this->getLineOffset($lineCounter));
	        $page->drawText('Signature', $this->_offsets['columnRight'], $this->getLineOffset($lineCounter));
	        
	        $lineCounter++;
	        
	        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
	        
	        $attendeeList = array_slice($event['attendeeList'], $i * $entriesPerPage, $entriesPerPage);
	        
	        foreach($attendeeList as $a) {
	        	$page->drawText($a['firstName'], $this->_offsets['columnLeft'], $this->getLineOffset($lineCounter));
	        	$page->drawText($a['lastName'], $this->_offsets['columnMiddle'], $this->getLineOffset($lineCounter));
	        	$page->drawLine($this->_offsets['columnRight'], $this->getLineOffset($lineCounter) - 2, $this->_offsets['right'], $this->getLineOffset($lineCounter) - 2);
	        	$lineCounter++;
	        }
		}
        
		return $this->_pdf->render();
	}
	
	/**
	 * Returns the left position of the centered String
	 * 
	 * @param String $str The String to center
	 * @param $fontSize The 
	 */
	private function center($str) {
		$textWidth = $this->getTextWidth($str);
		$pageWidth = $this->_offsets['right'] - $this->_offsets['left'];
		return $this->_offsets['left'] + $pageWidth/2 - $textWidth/2;
	}
	
	/**
	 * Returns the top offset of a new line of text
	 * @param unknown_type $lineOffset
	 */
	private function getLineOffset($lineOffset) {
		return $this->_offsets['top'] - $lineOffset * 16;
	}
	
	/**
	 * Return the length of a generated string in points
	 * 
	 * @param String $text
	 */
	private function getTextWidth($text) 
	{
		$font = $this->_style->getFont();
		$fontSize = $this->_style->getFontSize();
		
		$drawing_text = iconv('', 'UTF-16BE', $text);
		$characters    = array();
		for ($i = 0; $i < strlen($drawing_text); $i++) {
			$characters[] = (ord($drawing_text[$i++]) << 8) | ord ($drawing_text[$i]);
		}
		$glyphs        = $font->glyphNumbersForCharacters($characters);
		$widths        = $font->widthsForGlyphs($glyphs);
		$text_width    = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
		return $text_width;	
	}
}