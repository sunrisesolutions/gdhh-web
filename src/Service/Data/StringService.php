<?php

namespace App\Service\Data;


class StringService {
	
	/**
	 * @param $content
	 * @param $start
	 * @param $end
	 *
	 * @return string
	$code = '[include file="header.html"]';
	 * $innerCode = GetBetween($code, '[', ']');
	 * $innerCodeParts = explode(' ', $innerCode);
	 *
	 * $command = $innerCodeParts[0];
	 *
	 * $attributeAndValue = $innerCodeParts[1];
	 * $attributeParts = explode('=', $attributeParts);
	 * $attribute = $attributeParts[0];
	 * $attributeValue = str_replace('\"', '', $attributeParts[1]);
	 *
	 * echo $command . ' ' . $attribute . '=' . $attributeValue;
	 * //this will result in include file=header.html
	 * $command will be "include"
	 *
	 * $attribute will be "file"
	 *
	 * $attributeValue will be "header.html"
	 */
	public function getBetween($content, $start, $end) {
		$r = explode($start, $content);
		if(isset($r[1])) {
			$r = explode($end, $r[1]);
			
			return trim($r[0]);
		}
		
		return '';
	}
	
}