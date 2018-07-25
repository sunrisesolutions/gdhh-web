<?php

namespace App\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class AttributeArray extends Type {
	const ATTRIBUTE_ARRAY = 'attribute_array';
	
	public function convertToDatabaseValue($value, AbstractPlatform $platform) {
		if( ! is_array($value)) {
			return $value;
		}
		
		foreach($value as $itemKey => $itemValue) {
			$value[ $itemKey ] = '{' . $itemKey . '}' . '=>' . '[' . $itemValue . ']';
		}
		
		$attribute = implode(',', $value);
		
		return $attribute;
	}
	
	public function convertToPHPValue($value, AbstractPlatform $platform) {
		if(empty($value)) {
			return array();
		}
		$returnedArray = array();
		$attributes    = explode(',', $value);
		foreach($attributes as $attrKey => $attrValue) {
			$array                                      = explode('=>', $attrValue);
			$arrayKey                                   = $array[0];
			$arrayValue                                 = $array[1];
			$returnedArray[ substr($arrayKey, 1, - 1) ] = substr($arrayValue, 1, - 1);
		}
		
		return $returnedArray;
	}
	
	/**
	 * Gets the SQL declaration snippet for a field of this type.
	 *
	 * @param array                                     $fieldDeclaration The field declaration.
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
	 *
	 * @return string
	 */
	public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
		return 'text';
	}
	
	/**
	 * Gets the name of this type.
	 *
	 * @return string
	 *
	 * @todo Needed?
	 */
	public function getName() {
		return self::ATTRIBUTE_ARRAY;
	}
}