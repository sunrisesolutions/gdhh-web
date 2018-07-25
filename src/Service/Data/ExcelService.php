<?php

namespace App\Service\Data;
use App\Service\BaseService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExcelService extends BaseService {
	
	public function phpToExcelDatetime(\DateTime $dt) {
		return \PHPExcel_Shared_Date::FormattedPHPToExcel($dt->format('Y'), $dt->format('m'), $dt->format('d'),
			$dt->format('H'), $dt->format('i'), $dt->format('s')
		);
	}
	
}