<?php

namespace App\Entity\HocBa\BangDiemSpreadsheet;

use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien\HuynhTruong;
use App\Entity\HoSo\TruongPhuTrachDoi;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class BangDiemChiDoanWriter extends BangDiemNhomWriter {
	
	public function writeHeading($hocKy, $namHocId, ChiDoan $chiDoan = null, HuynhTruong $truong = null) {
		if(empty($truong)) {
			$truong = $this->huynhTruong;
		}
		$chiDoan = $truong->getPhanBo()->getChiDoan();
		
		$this->writeBaseHeading($hocKy, $namHocId, $chiDoan, $truong);
		
		$sWriter = $this->sWriter;
		$sWriter->goDown();
		
		$truongPhuTrachStr = $truong->getThanhVien()->getName();
		$sWriter->writeCell(sprintf('CHI ĐOÀN: %d', $chiDoan->getNumber()));
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
		
		$sWriter->writeCell(sprintf('Phụ-trách: %s', $truongPhuTrachStr));
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

    public function writeHeadingDauNam($namHocId, ChiDoan $chiDoan = null, HuynhTruong $truong = null) {
        if(empty($truong)) {
            $truong = $this->huynhTruong;
        }
        $chiDoan = $truong->getPhanBo()->getChiDoan();

        $this->writeBaseHeading('dau-nam', $namHocId, $chiDoan, $truong);

        $sWriter = $this->sWriter;
        $sWriter->goDown();

        $truongPhuTrachStr = $truong->getThanhVien()->getName();
        $sWriter->writeCell(sprintf('CHI ĐOÀN: %d', $chiDoan->getNumber()));
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

        $sWriter->writeCell(sprintf('Phụ-trách: %s', $truongPhuTrachStr));
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
}