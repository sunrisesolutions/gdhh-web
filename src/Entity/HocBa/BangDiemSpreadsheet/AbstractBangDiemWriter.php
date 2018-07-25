<?php

namespace App\Entity\HocBa\BangDiemSpreadsheet;

use App\Entity\HocBa\BangDiem;
use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien\HuynhTruong;
use App\Service\Data\SpreadsheetWriter;
use Doctrine\Common\Collections\Collection;
use Liuggio\ExcelBundle\Factory;

abstract class AbstractBangDiemWriter {
	/** @var SpreadsheetWriter */
	protected $sWriter;
	
	/** @var HuynhTruong */
	protected $huynhTruong;
	
	/** @var Factory */
	protected $spreadsheetFactory;
	
	/** @var \PHPExcel */
	protected $excelObj;
	
	public function __construct(HuynhTruong $truong) {
		$this->huynhTruong = $truong;
	}
	
	/**
	 * @return mixed
	 * @throws \PHPExcel_Exception
	 */
	protected function createSpreadsheetWriter() {
		if(empty($this->spreadsheetFactory)) {
			$this->spreadsheetFactory = new Factory();
		}
		
		if( ! empty($this->sWriter)) {
			return $this->sWriter;
		}
		
		$f              = $this->spreadsheetFactory;
		$this->excelObj = $phpExcelObject = $f->createPHPExcelObject();
		$name           = $this->huynhTruong->getThanhVien()->getName();
		$phpExcelObject->getProperties()->setCreator($name)
		               ->setLastModifiedBy($name)
		               ->setTitle("Download - Bang Diem")
		               ->setSubject("Bang Diem")
		               ->setDescription("Raw Data")
		               ->setKeywords("office 2005 openxml php")
		               ->setCategory("Raw Data Download");
		
		$phpExcelObject->setActiveSheetIndex(0);
		$activeSheet = $phpExcelObject->getActiveSheet();
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$phpExcelObject->setActiveSheetIndex(0);
		$sWriter       = new SpreadsheetWriter($activeSheet);
		$this->sWriter = $sWriter;
		
		return $sWriter;
	}
	
	protected abstract function writeHeading($hocKy, $namHocId, ChiDoan $chiDoan = null, HuynhTruong $truong = null);
	
	protected function writeBaseHeading($hocKy, $namHocId, ChiDoan $chiDoan = null, HuynhTruong $truong = null) {
		$sWriter = $this->sWriter;
		
		$sWriter->writeCell("BẢNG ĐIỂM HỌC KỲ " . $hocKy);
		$sWriter->mergeCellsRight(13);
		$sWriter->getCurrentCellStyle()->applyFromArray(array(
			'font'      => array(
				'bold' => true,
//				'color' => array( 'rgb' => 'FF0000' ),
				'size' => 18,
				'name' => 'Times New Roman'
			)
		,
			'alignment' => array(
				'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
			)
		));
		
		$sWriter->getCurrentRowDimension()->setRowHeight(30);
		$sWriter->goDown();
		$sWriter->writeCell(sprintf('NĂM HỌC %d - %d', $namHocId, $namHocId + 1));
		$sWriter->mergeCellsRight(13);
		$sWriter->getCurrentCellStyle()->applyFromArray(array(
			'font'      => array(
				'bold' => true,
//				'color' => array( 'rgb' => 'FF0000' ),
				'size' => 15,
				'name' => 'Times New Roman'
			)
		,
			'alignment' => array(
				'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
			)
		));
		$sWriter->getCurrentRowDimension()->setRowHeight(25);
	}
	
	public function writeData($hocKy, Collection $phanBoThieuNhi) {
		$style1  = array(
			'font'      => array(
				'bold'  => true,
				'color' => array( 'rgb' => 'FFFFFF' ),
				'size'  => 12,
				'name'  => 'Times New Roman'
			)
		,
			'alignment' => array(
				'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
			)
		);
		$sWriter = $this->sWriter;
		
		if($hocKy === 1) {
			$cotDiemCC = [
				'E' => 'cc9',
				'F' => 'cc10',
				'G' => 'cc11',
				'H' => 'cc12',
			];
		} else {
			$cotDiemCC = [
				'E' => 'cc1',
				'F' => 'cc2',
				'G' => 'cc3',
				'H' => 'cc4',
				'I' => 'cc5',
			];
		}
		
		$sWriter->setCurrentCellColor('FF9900');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->getCurrentRowDimension()->setRowHeight(20);
		$sWriter->mergeCellsDown(2);
		$sWriter->writeCellAndGoRight('MÃ SỐ');
		
		$sWriter->setCurrentCellColor('FF9900');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->setCurrentCellColor('FF9900');
		$sWriter->mergeCellsDown(2);
		$sWriter->writeCellAndGoRight('TÊN THÁNH');
		
		
		$sWriter->setCurrentCellColor('FF9900');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->setCurrentCellColor('FF9900');
		$sWriter->mergeCellsDown(2);
		$sWriter->writeCellAndGoRight('HỌ');
		
		
		$sWriter->setCurrentCellColor('FF9900');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->setCurrentCellColor('FF9900');
		$sWriter->mergeCellsDown(2);
		$sWriter->writeCellAndGoRight('TÊN');
		
		
		$sWriter->setCurrentCellColor('FF9900');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->setCurrentCellColor('FF9900');
		$sWriter->mergeCellsRightDown(count($cotDiemCC) - 1, 1);
		$sWriter->writeCell('CHUYÊN CẦN');
		
		$sWriter->goDown(2);
		
		foreach($cotDiemCC as $cd) {
			$sWriter->setCurrentCellColor('FF9900');
			$sWriter->getCurrentCellStyle()->applyFromArray($style1);
			$sWriter->writeCellAndGoRight(sprintf(' %s ', str_replace('cc', 'T', $cd)));
		}
		
		$sWriter->goUp(2);
		
		$sWriter->setCurrentCellColor('0000FF');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->mergeCellsDown(2);
		$sWriter->getCurrentColumnDimension()->setAutoSize(false);
		$sWriter->getCurrentColumnDimension()->setWidth(20);
		$sWriter->writeCellAndGoRight(' TB.CC ');
		
		$sWriter->setCurrentCellColor('0000FF');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->mergeCellsDown(2);
		$sWriter->getCurrentColumnDimension()->setAutoSize(false);
		$sWriter->getCurrentColumnDimension()->setWidth(20);
		$sWriter->writeCellAndGoRight(' TB.MIỆNG ');
		
		$sWriter->setCurrentCellColor('0000FF');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->mergeCellsDown(2);
		$sWriter->getCurrentColumnDimension()->setAutoSize(false);
		$sWriter->getCurrentColumnDimension()->setWidth(20);
		$sWriter->writeCellAndGoRight(' ĐIỂM 1 TIẾT ');
		
		$sWriter->setCurrentCellColor('0000FF');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->mergeCellsDown(2);
		$sWriter->getCurrentColumnDimension()->setAutoSize(false);
		$sWriter->getCurrentColumnDimension()->setWidth(20);
		$sWriter->writeCellAndGoRight(' ĐIỂM THI HK' . $hocKy . ' ');
		
		$sWriter->setCurrentCellColor('0000FF');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->mergeCellsDown(2);
		$sWriter->getCurrentColumnDimension()->setAutoSize(false);
		$sWriter->getCurrentColumnDimension()->setWidth(20);
		$sWriter->writeCellAndGoRight(' TB.GL. HK' . $hocKy . ' ');
		
		$sWriter->setCurrentCellColor('FF0000');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->mergeCellsDown(2);
		$sWriter->getCurrentColumnDimension()->setAutoSize(false);
		$sWriter->getCurrentColumnDimension()->setWidth(20);
		$sWriter->writeCellAndGoRight(' TB. HK' . $hocKy . ' ');
		
		if($hocKy === 2) {
			$sWriter->setCurrentCellColor('0000FF');
			$sWriter->getCurrentCellStyle()->applyFromArray($style1);
			$sWriter->mergeCellsDown(2);
			$sWriter->getCurrentColumnDimension()->setAutoSize(false);
			$sWriter->getCurrentColumnDimension()->setWidth(20);
			$sWriter->writeCellAndGoRight(' TB. HK1 ');
			
			$sWriter->setCurrentCellColor('FF0000');
			$sWriter->getCurrentCellStyle()->applyFromArray($style1);
			$sWriter->mergeCellsDown(2);
			$sWriter->getCurrentColumnDimension()->setAutoSize(false);
			$sWriter->getCurrentColumnDimension()->setWidth(20);
			$sWriter->writeCellAndGoRight(' TB. Cả-năm ');
			
			$sWriter->setCurrentCellColor('0000FF');
			$sWriter->getCurrentCellStyle()->applyFromArray($style1);
			$sWriter->mergeCellsDown(2);
			$sWriter->getCurrentColumnDimension()->setAutoSize(false);
			$sWriter->getCurrentColumnDimension()->setWidth(20);
			$sWriter->writeCellAndGoRight(' TB.GL. HK1 ');
			
			$sWriter->setCurrentCellColor('FF0000');
			$sWriter->getCurrentCellStyle()->applyFromArray($style1);
			$sWriter->mergeCellsDown(2);
			$sWriter->getCurrentColumnDimension()->setAutoSize(false);
			$sWriter->getCurrentColumnDimension()->setWidth(20);
			$sWriter->writeCellAndGoRight(' TB.GL. Cả-năm ');
			
		}
		
		$sWriter->setCurrentCellColor('FF9900');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->mergeCellsDown(2);
		$sWriter->getCurrentColumnDimension()->setAutoSize(false);
		$sWriter->getCurrentColumnDimension()->setWidth(20);
		$sWriter->writeCellAndGoRight(' PHIẾU CN HK' . $hocKy);
		
		if($hocKy === 2) {
			$sWriter->setCurrentCellColor('FF9900');
			$sWriter->getCurrentCellStyle()->applyFromArray($style1);
			$sWriter->mergeCellsDown(2);
			$sWriter->getCurrentColumnDimension()->setAutoSize(false);
			$sWriter->getCurrentColumnDimension()->setWidth(20);
			$sWriter->writeCellAndGoRight(' PHIẾU CN Cả-năm ');
			
			$sWriter->setCurrentCellColor('FF0000');
			$sWriter->getCurrentCellStyle()->applyFromArray($style1);
			$sWriter->mergeCellsDown(2);
			$sWriter->getCurrentColumnDimension()->setAutoSize(false);
			$sWriter->getCurrentColumnDimension()->setWidth(20);
			$sWriter->writeCellAndGoRight(' XẾP-LOẠI ');
			
			$sWriter->setCurrentCellColor('FF9900');
			$sWriter->getCurrentCellStyle()->applyFromArray($style1);
			$sWriter->mergeCellsDown(2);
			$sWriter->getCurrentColumnDimension()->setAutoSize(false);
			$sWriter->getCurrentColumnDimension()->setWidth(20);
			$sWriter->writeCellAndGoRight(' KHEN-THƯỞNG ');
			
			$sWriter->setCurrentCellColor('FF0000');
			$sWriter->getCurrentCellStyle()->applyFromArray($style1);
			$sWriter->mergeCellsDown(2);
			$sWriter->getCurrentColumnDimension()->setAutoSize(false);
			$sWriter->getCurrentColumnDimension()->setWidth(20);
			$sWriter->writeCellAndGoRight(' PHÂN-ĐOÀN ');
			
			$sWriter->setCurrentCellColor('FF0000');
			$sWriter->getCurrentCellStyle()->applyFromArray($style1);
			$sWriter->mergeCellsDown(2);
			$sWriter->getCurrentColumnDimension()->setAutoSize(false);
			$sWriter->getCurrentColumnDimension()->setWidth(20);
			$sWriter->writeCellAndGoRight(' CHI-ĐOÀN ');
			
			$sWriter->setCurrentCellColor('FF0000');
			$sWriter->getCurrentCellStyle()->applyFromArray($style1);
			$sWriter->mergeCellsDown(2);
			$sWriter->getCurrentColumnDimension()->setAutoSize(false);
			$sWriter->getCurrentColumnDimension()->setWidth(20);
			$sWriter->writeCell(' TRƯỞNG PHỤ-TRÁCH ');
		}
		
		$sWriter->goDown(2);
		//////////////// Write Names and Code
		/** @var PhanBo $phanBo */
		foreach($phanBoThieuNhi as $phanBo) {
			$chiDoan = $phanBo->getChiDoan();
			
			$sWriter->goDown();
			$sWriter->goFirstColumn();
			$thanhVien = $phanBo->getThanhVien();
			if(empty($bangDiem = $phanBo->getBangDiem())) {
				$phanBo->setBangDiem($bangDiem = new BangDiem());
			}
			
			$sWriter->writeCellAndGoRight('  ' . $thanhVien->getId() . '  ');
			$sWriter->writeCellAndGoRight('  ' . $thanhVien->getChristianname() . '  ');
			$sWriter->writeCellAndGoRight('  ' . $thanhVien->getLastname() . ' ' . $thanhVien->getMiddlename() . '  ');
			$sWriter->writeCellAndGoRight('  ' . $thanhVien->getFirstname() . '  ');
			
			foreach($cotDiemCC as $key => $value) {
				//			$sWriter->writeCellAndGoRight($bangDiem->getCc9());
				$getter = 'get' . ucfirst($value);
				$sWriter->writeCellAndGoRight($bangDiem->$getter());
			}
			
			$sWriter->alignCurrentCellCenter();
			$cusorRow           = $sWriter->getCursorRow();
			$sumCCStr           = 'SUM(';
			$sumCCCount         = 0;
			$cacCotDiemBiLoaiBo = $chiDoan->getCotDiemBiLoaiBo();
			foreach($cotDiemCC as $key => $value) {
				if( ! empty($cacCotDiemBiLoaiBo) && in_array($value, $cacCotDiemBiLoaiBo)) {
					continue;
				}
				$sumCCStr .= $key . $cusorRow . ',';
				$sumCCCount ++;
			}
			
			$sumCCStr = substr($sumCCStr, 0, - 1);
			$sumCCStr .= ')';
			
			if($sumCCCount === 0) {
				$sWriter->writeCellAndGoRight(0);
			} else {
				$sWriter->writeCellAndGoRight(sprintf('=ROUND(%s/%d,2)', $sumCCStr, $sumCCCount));
			}
			
			$sWriter->alignCurrentCellCenter();
			$getter = 'getQuizTerm' . $hocKy;
			$sWriter->writeCellAndGoRight($bangDiem->$getter());
			$sWriter->alignCurrentCellCenter();
			$getter = 'getMidTerm' . $hocKy;
			$sWriter->writeCellAndGoRight($bangDiem->$getter());
			$sWriter->alignCurrentCellCenter();
			$getter = 'getFinalTerm' . $hocKy;
			$sWriter->writeCellAndGoRight($bangDiem->$getter());
			
			$iCode       = ord('E');
			$nextCode    = $iCode + count($cotDiemCC);
			$quizTermCol = $nextCode + 1;
			
			/// <<< Tinh Diem TB GIAO LY
			$cotDiemGL    = [
				chr($quizTermCol)    => 'quixTerm1',
				chr(++ $quizTermCol) => 'midTerm1',
				chr(++ $quizTermCol) => 'finalTerm1',
			];
			$finalTermCol = $quizTermCol;
			$sWriter->alignCurrentCellCenter();
			$sumGLStr   = 'SUM(';
			$sumGLCount = 0;
			foreach($cotDiemGL as $key => $value) {
				if( ! empty($cacCotDiemBiLoaiBo) && in_array($value, $cacCotDiemBiLoaiBo)) {
					continue;
				}
				$sumGLStr .= $key . $cusorRow . ',';
				if($key === chr($finalTermCol)) {
					$sumGLStr .= $key . $cusorRow . ',';
					$sumGLCount ++;
				}
				$sumGLCount ++;
			}
			$sumGLStr = substr($sumGLStr, 0, - 1);
			$sumGLStr .= ')';
			
			$tbGLTermCol = $sWriter->getCursorColumn();
			if($sumGLCount === 0) {
				$sWriter->writeCellAndGoRight(0);
			} else {
				$sWriter->writeCellAndGoRight(sprintf('=ROUND(%s/%d,2)', $sumGLStr, $sumGLCount));
			}
			/// >>> Tinh Diem TB GIAO LY
			
			$cotDiem = [
				chr($nextCode)    => 'tbCCTerm1',
				chr(++ $nextCode) => 'quizTerm1',
				chr(++ $nextCode) => 'midTerm1',
				chr(++ $nextCode) => 'finalTerm1',
			];
			
			$sWriter->alignCurrentCellCenter();
			$sumTerm1Str   = 'SUM(';
			$sumTerm1Count = 0;
			foreach($cotDiem as $key => $value) {
				if( ! empty($cacCotDiemBiLoaiBo) && in_array($value, $cacCotDiemBiLoaiBo)) {
					continue;
				}
				$sumTerm1Str .= $key . $cusorRow . ',';
				if($key === chr($nextCode)) {
					$sumTerm1Str .= $key . $cusorRow . ',';
					$sumTerm1Count ++;
				}
				$sumTerm1Count ++;
			}
			$sumTerm1Str = substr($sumTerm1Str, 0, - 1);
			$sumTerm1Str .= ')';
			$tbTermCol   = $sWriter->getCursorColumn();
			if($sumTerm1Count === 0) {
				$sWriter->writeCellAndGoRight(0);
			} else {
				$sWriter->writeCellAndGoRight(sprintf('=ROUND(%s/%d,2)', $sumTerm1Str, $sumTerm1Count));
			}
			
			if($hocKy === 2) {
				$tbTerm1Col = $sWriter->getCursorColumn();
				$sWriter->alignCurrentCellCenter();
				$sWriter->writeCellAndGoRight($bangDiem->getTbTerm1());
				$sWriter->alignCurrentCellCenter();
				$sWriter->writeCellAndGoRight(sprintf('=ROUND(SUM(%s,%s)/2,2)', $tbTermCol . $cusorRow, $tbTerm1Col . $cusorRow)); // tbYear
				
				$sWriter->alignCurrentCellCenter();
				$tbGLTerm1Col = $sWriter->getCursorColumn();
				$sWriter->writeCellAndGoRight($bangDiem->getTbGLTerm1());
				$sWriter->alignCurrentCellCenter();
				$sWriter->writeCellAndGoRight(sprintf('=ROUND(SUM(%s,%s)/2,2)', $tbGLTermCol . $cusorRow, $tbGLTerm1Col . $cusorRow)); // tbGLYear
			}
			
			
			$sWriter->alignCurrentCellCenter();
			$getter = 'getSundayTicketTerm' . $hocKy;
			$sWriter->writeCell($bangDiem->$getter());
			
			if($hocKy === 2) {
				$sWriter->goRight();
				$sWriter->alignCurrentCellCenter();
				$sWriter->writeCellAndGoRight($bangDiem->getSundayTickets());
				
				$sWriter->alignCurrentCellCenter();
				$sWriter->writeCellAndGoRight($bangDiem->getCategoryTrans());
				
				$sWriter->alignCurrentCellCenter();
				$sWriter->writeCellAndGoRight($bangDiem->isAwarded() ? 'Có' : 'Không');
				
				$sWriter->alignCurrentCellCenter();
				$sWriter->writeCellAndGoRight($bangDiem->getPhanBo()->getPhanDoan());
				
				$sWriter->alignCurrentCellCenter();
				$sWriter->writeCellAndGoRight($bangDiem->getPhanBo()->getChiDoan()->getName());
				
				$sWriter->alignCurrentCellCenter();
				$dngl = $bangDiem->getPhanBo()->getDoiNhomGiaoLy();
				if(empty($dngl)) {
					$sWriter->writeCell('KHÔNG CÓ ĐỘI');
				} else {
					$sWriter->writeCell($dngl->getTenCacTruongPhuTrach());
				}
			}
		}
		
		
		$sWriter->getCellsStyle('A5', $sWriter->getLastColumn() . $sWriter->getLastRow())->applyFromArray(
			array(
				'borders' => array(
					'allborders' => array(
						'style' => \PHPExcel_Style_Border::BORDER_THIN
					)
				)
			));
		
		
	}
	
	public function writeBangDiemHocKy($hocKy) {
		$this->createSpreadsheetWriter();
		$this->writeHeading($hocKy, $this->huynhTruong->getPhanBo()->getNamHoc()->getId(), null, null);
		$this->writeData($hocKy, $this->getPhanBoThieuNhi());
	}
	
	protected abstract function getPhanBoThieuNhi(): Collection;
	
	/**
	 * @return Factory
	 */
	public function getSpreadsheetFactory(): Factory {
		return $this->spreadsheetFactory;
	}
	
	/**
	 * @return \PHPExcel
	 */
	public function getExcelObj(): \PHPExcel {
		return $this->excelObj;
	}
	
}