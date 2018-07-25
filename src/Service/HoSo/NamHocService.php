<?php

namespace App\Service\HoSo;

use App\Entity\HoSo\NamHoc;
use App\Service\BaseService;

class NamHocService extends BaseService {
	
	public function getNamHocHienTai() {
		$repo   = $this->getDoctrine()->getRepository(NamHoc::class);
		/** @var NamHoc $namHoc */
		$namHoc = $repo->findOneBy(
			array( 'enabled' => true, 'started' => true )
		);
		
		return $namHoc;
	}
}