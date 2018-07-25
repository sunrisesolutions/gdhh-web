<?php

namespace App\Service\HoSo;


use App\Entity\HocBa\BangDiem;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use App\Entity\HoSo\TruongPhuTrachDoi;
use App\Service\Data\SpreadsheetWriter;
use App\Service\BaseService;

class ThanhVienService extends BaseService {
	public function preUpdate(ThanhVien $object){
		if( ! empty($phanBoNamNay = $object->getPhanBoNamNay())) {
			$phanBoNamNay->setVaiTro();
			if(empty($object->getChiDoan())) {
				$phanBoNamNay->setChiDoan(null);
			}
		};
		
		$namHocHienTai = $this->container->get(NamHocService::class)->getNamHocHienTai();
		$object->initiatePhanBo($namHocHienTai);		
	}
	
	public function writeBangDiemDoiNhomGiaoLyHeading(SpreadsheetWriter $sWriter, $hocKy, PhanBo $phanBo) {
		$cacTruongPT = $phanBo->getCacTruongPhuTrachDoi();
		
		$cacDoiNhomGiaoLyStr = '';
		$thanhVien           = $phanBo->getThanhVien();
		
		/** @var TruongPhuTrachDoi $truongPT */
		foreach($cacTruongPT as $truongPT) {
			$cacDoiNhomGiaoLyStr .= $truongPT->getDoiNhomGiaoLy()->getNumber() . ', ';
		}
		
		$cacDoiNhomGiaoLyStr = substr($cacDoiNhomGiaoLyStr, 0, - 2);
		$cacDoiNhomGiaoLyStr .= sprintf(' (%s)', $thanhVien->getTitle() . ' ' . $thanhVien->getFirstname());
		
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
		$sWriter->writeCell(sprintf('NĂM HỌC %d - %d', $phanBo->getNamHoc()->getId(), $phanBo->getNamHoc()->getId() + 1));
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
		$sWriter->goDown();
		
		$sWriter->writeCell(sprintf('CHI ĐOÀN: %d %s', $phanBo->getChiDoan()->getNumber(), 'Đội: ' . $cacDoiNhomGiaoLyStr));
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
		$sWriter->goDown();
		$sWriter->goDown();
		
	}
	
	public function writeBangDiemDoiNhomGiaoLyHK1Data(SpreadsheetWriter $sWriter, PhanBo $phanBo) {
		$style1 = array(
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
		$sWriter->mergeCellsRightDown(3, 1);
		$sWriter->writeCell('CHUYÊN CẦN');
		
		$sWriter->goDown(2);
		
		$sWriter->setCurrentCellColor('FF9900');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->writeCellAndGoRight(' T9 ');
		$sWriter->setCurrentCellColor('FF9900');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->setCurrentCellColor('FF9900');
		$sWriter->writeCellAndGoRight(' T10 ');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->setCurrentCellColor('FF9900');
		$sWriter->writeCellAndGoRight(' T11 ');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->setCurrentCellColor('FF9900');
		$sWriter->writeCellAndGoRight(' T12 ');
		
		$sWriter->goUp(2);
		
		$sWriter->setCurrentCellColor('0000FF');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->mergeCellsDown(2);
		$sWriter->getCurrentColumnDimension()->setAutoSize(false);
		$sWriter->getCurrentColumnDimension()->setWidth(20);
		$sWriter->writeCellAndGoRight(' TB.CHUYÊN CẦN ');
		
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
		$sWriter->writeCellAndGoRight(' ĐIỂM THI HKI ');
		
		$sWriter->setCurrentCellColor('FF0000');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->mergeCellsDown(2);
		$sWriter->getCurrentColumnDimension()->setAutoSize(false);
		$sWriter->getCurrentColumnDimension()->setWidth(20);
		$sWriter->writeCellAndGoRight(' TB HKI ');
		
		$sWriter->setCurrentCellColor('FF9900');
		$sWriter->getCurrentCellStyle()->applyFromArray($style1);
		$sWriter->mergeCellsDown(2);
		$sWriter->getCurrentColumnDimension()->setAutoSize(false);
		$sWriter->getCurrentColumnDimension()->setWidth(20);
		$sWriter->writeCellAndGoRight(' PHIẾU LỄ CN ');
		
		
		$sWriter->goDown(2);
		//////////////// Write Names and Code
		/** @var TruongPhuTrachDoi $truongPT */
		foreach($phanBo->getCacTruongPhuTrachDoi() as $truongPT) {
			$phanBoHangNam = $truongPT->getDoiNhomGiaoLy()->getPhanBoThieuNhi();
			/** @var PhanBo $phanBo */
			foreach($phanBoHangNam as $phanBo) {
				$chiDoan = $phanBo->getChiDoan();
				
				$sWriter->goDown();
				$sWriter->goFirstColumn();
				$thanhVien = $phanBo->getThanhVien();
				if(empty($bangDiem = $phanBo->getBangDiem())) {
					$phanBo->setBangDiem($bangDiem = new BangDiem());
				}
				
				$sWriter->writeCellAndGoRight('  ' . $thanhVien->getCode() . '  ');
				$sWriter->writeCellAndGoRight('  ' . $thanhVien->getChristianname() . '  ');
				$sWriter->writeCellAndGoRight('  ' . $thanhVien->getLastname() . ' ' . $thanhVien->getMiddlename() . '  ');
				$sWriter->writeCellAndGoRight('  ' . $thanhVien->getFirstname() . '  ');
				$sWriter->writeCellAndGoRight($bangDiem->getCc9());
				$sWriter->writeCellAndGoRight($bangDiem->getCc10());
				$sWriter->writeCellAndGoRight($bangDiem->getCc11());
				$sWriter->writeCellAndGoRight($bangDiem->getCc12());
				$sWriter->alignCurrentCellCenter();
				
				$cusorRow           = $sWriter->getCursorRow();
				$cacCotDiemBiLoaiBo = $chiDoan->getCotDiemBiLoaiBo();
				
				$cotDiem    = [
					'E' => 'cc9',
					'F' => 'cc10',
					'G' => 'cc11',
					'H' => 'cc12',
				];
				$sumCCStr   = 'SUM(';
				$sumCCCount = 0;
				foreach($cotDiem as $key => $value) {
					if(!empty($cacCotDiemBiLoaiBo) && in_array($value, $cacCotDiemBiLoaiBo)) {
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
				$sWriter->writeCellAndGoRight($bangDiem->getQuizTerm1());
				$sWriter->alignCurrentCellCenter();
				$sWriter->writeCellAndGoRight($bangDiem->getMidTerm1());
				$sWriter->alignCurrentCellCenter();
				$sWriter->writeCellAndGoRight($bangDiem->getFinalTerm1());
				$sWriter->alignCurrentCellCenter();
				
				
				$cotDiem       = [
					'I' => 'tbCCTerm1',
					'J' => 'quizTerm1',
					'K' => 'midTerm1',
					'L' => 'finalTerm1',
				];
				$sumTerm1Str   = 'SUM(';
				$sumTerm1Count = 0;
				foreach($cotDiem as $key => $value) {
					if(!empty($cacCotDiemBiLoaiBo) && in_array($value, $cacCotDiemBiLoaiBo)) {
						continue;
					}
					$sumTerm1Str .= $key . $cusorRow . ',';
					if($key === 'L') {
						$sumTerm1Str .= $key . $cusorRow . ',';
						$sumTerm1Count ++;
					}
					$sumTerm1Count ++;
				}
				$sumTerm1Str = substr($sumTerm1Str, 0, - 1);
				$sumTerm1Str .= ')';
				
				if($sumCCCount === 0) {
					$sWriter->writeCellAndGoRight(0);
				} else {
					$sWriter->writeCellAndGoRight(sprintf('=ROUND(%s/%d,2)', $sumTerm1Str, $sumTerm1Count));
				}
				
				
				$sWriter->alignCurrentCellCenter();
				$sWriter->writeCellAndGoRight($bangDiem->getSundayTickets());
			}
		}
		
		
		$sWriter->getCellsStyle('A5', 'N' . $sWriter->getLastRow())->applyFromArray(
			array(
				'borders' => array(
					'allborders' => array(
						'style' => \PHPExcel_Style_Border::BORDER_THIN
					)
				)
			));
		
		
	}
	
}