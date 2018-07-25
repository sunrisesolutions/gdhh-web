<?php

namespace App\Service\Data;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

class SpreadsheetWriter {
	// PHPExcel_Worksheet
	private $workSheet;
	private $cursorRow;
	private $cursorColumn;
	private $lastColumn;
	private $lastRow;
	private $questionColumnArray = array();
	
	function __construct(\PHPExcel_Worksheet $workSheet) {
		$this->workSheet    = $workSheet;
		$this->cursorRow    = '1';
		$this->lastRow      = '1';
		$this->cursorColumn = 'A';
		$this->lastColumn   = 'A';
	}
	
	public function getCurrentRowDimension() {
		return $this->workSheet->getRowDimension($this->cursorRow);
	}
	
	public function getCurrentColumnDimension() {
		return $this->workSheet->getColumnDimension($this->cursorColumn);
	}
	
	public function mergeCellsRightDown($numberRight, $numberDown) {
		$startCell = $this->cursorColumn . $this->cursorRow;
		
		$targetColumn = $this->cursorColumn;
		for($i = 0; $i < $numberRight; $i ++) {
			$targetColumn ++;
		}
		
		$targetRow = $this->cursorRow;
		for($i = 0; $i < $numberDown; $i ++) {
			$targetRow ++;
		}
		
		$endCell = $targetColumn . $targetRow;
		$this->workSheet->mergeCells($startCell . ':' . $endCell);
	}
	
	public function mergeCellsRight($number) {
		$startCell    = $this->cursorColumn . $this->cursorRow;
		$targetColumn = $this->cursorColumn;
		for($i = 0; $i < $number; $i ++) {
			$targetColumn ++;
		}
		$endCell = $targetColumn . $this->cursorRow;
		$this->workSheet->mergeCells($startCell . ':' . $endCell);
	}
	
	public function mergeCellsDown($number) {
		$startCell = $this->cursorColumn . $this->cursorRow;
		$targetRow = $this->cursorRow;
		for($i = 0; $i < $number; $i ++) {
			$targetRow ++;
		}
		$endCell = $this->cursorColumn . $targetRow;
		$this->workSheet->mergeCells($startCell . ':' . $endCell);
	}
	
	public function setCurrentCellColor($color) {
		$this->getCurrentCellStyle()->getFill()->applyFromArray(array(
			'type'       => \PHPExcel_Style_Fill::FILL_SOLID,
			'startcolor' => array(
				'rgb' => $color
			)
		));
	}
	
	public function getCellsStyle($startCell, $endCell) {
		return $this->workSheet->getStyle($startCell . ':' . $endCell);
	}
	
	public function getCurrentCellStyle() {
		return $this->workSheet->getStyle($this->cursorColumn . $this->cursorRow);
	}
	
	public function alignCurrentCellCenter() {
		$this->getCurrentCellStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$this->getCurrentCellStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}
	
	public function writeCellAndGoRight($value = '') {
		$this->writeCell($value);
		$this->goRight();
	}
	
	public function writeCell($value) {
		$ws         = $this->workSheet;
		$coordinate = $this->cursorColumn . $this->cursorRow;
		$ws->setCellValue($coordinate, $value);
	}
	
	public function goRight() {
		$this->cursorColumn ++;
		if($this->cursorColumn > $this->lastColumn) {
			$this->lastColumn ++;
		}
	}
	
	public function goLeft() {
		$this->cursorColumn --;
	}
	
	public function goDown($number = 1) {
		for($i = 0; $i < $number; $i ++) {
			$this->cursorRow ++;
			if($this->cursorRow > $this->lastRow) {
				$this->lastRow ++;
			}
		}
	}
	
	public function goUp($number = 1) {
		for($i = 0; $i < $number; $i ++) {
			$this->cursorRow --;
		}
	}
	
	public function getCursor() {
		return array( $this->cursorColumn, $this->cursorRow );
	}
	
	public function setCursor($cursor) {
		$this->cursorColumn = $cursor[0];
		$this->cursorRow    = $cursor[1];
	}
	
	public function goFirstColumn() {
		$this->setCursor(array( 'A', $this->getCursorRow() ));
	}
	
	public function goFirstRow() {
		$this->setCursor(array( $this->getCursorColumn(), '1' ));
	}
	
	public function goLastColumn() {
		$this->setCursor(array( $this->getLastColumn(), $this->getCursorRow() ));
	}
	
	public function goLastRow() {
		$this->setCursor(array( $this->getCursorColumn(), $this->getLastRow() ));
	}
	
	/**
	 * @param string $cursorColumn
	 */
	public function setCursorColumn($cursorColumn) {
		$this->cursorColumn = $cursorColumn;
	}
	
	/**
	 * @return string
	 */
	public function getCursorColumn() {
		return $this->cursorColumn;
	}
	
	/**
	 * @param string $cursorRow
	 */
	public function setCursorRow($cursorRow) {
		$this->cursorRow = $cursorRow;
	}
	
	/**
	 * @return string
	 */
	public function getCursorRow() {
		return $this->cursorRow;
	}
	
	/**
	 * @param string $lastColumn
	 */
	public function setLastColumn($lastColumn) {
		$this->lastColumn = $lastColumn;
	}
	
	/**
	 * @return string
	 */
	public function getLastColumn() {
		return $this->lastColumn;
	}
	
	/**
	 * @param string $lastRow
	 */
	public function setLastRow($lastRow) {
		$this->lastRow = $lastRow;
	}
	
	/**
	 * @return string
	 */
	public function getLastRow() {
		return $this->lastRow;
	}
	
	/**
	 * @param \PHPExcel_Worksheet $workSheet
	 */
	public function setWorkSheet($workSheet) {
		$this->workSheet = $workSheet;
	}
	
	/**
	 * @return \PHPExcel_Worksheet
	 */
	public function getWorkSheet() {
		return $this->workSheet;
	}
	
	public function writeHeading() {
//        $categoryList = $this->categoryList;
		$this->writeCell("Employee's Id");
		$this->goRight();
		$this->writeCell("Employee's Name");
//        foreach ($categoryList as $index_category => $category) {
//            $questionList = $category->getQuestionList();
//            foreach ($questionList as $index_question => $question) {
//                $this->goRight();
//                $this->writeCell('#' . ($index_category + 1) . ':' . $question->getName());
//                $this->questionColumnArray[$question->getId()] = $this->getCursorColumn();
//            }
//        }
		$this->goRight();
		$this->writeCell("Comments");
	}
	
	
	public function getSheet() {
		return $this->workSheet;
	}
	
} 