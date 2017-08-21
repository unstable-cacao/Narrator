<?php
namespace Narrator;


interface IReturnValue
{
	/**
	 * @param string $type
	 * @param callable|mixed $value
	 * @return IReturnValue
	 */
	public function byType(string $type, $value): IReturnValue;
	
	/**
	 * @param array $valueByType
	 * @return IReturnValue
	 */
	public function byTypes(array $valueByType): IReturnValue;
	
	/**
	 * @param string $subType
	 * @param callable|mixed $value
	 * @return IReturnValue
	 */
	public function bySubType(string $subType, $value): IReturnValue;
	
	/**
	 * @param array $valueBySubType
	 * @return IReturnValue
	 */
	public function bySubTypes(array $valueBySubType): IReturnValue;
	
	/**
	 * @param callable|mixed $value
	 * @return IReturnValue
	 */
	public function defaultValue($value): IReturnValue;
	
	/**
	 * @param callable|mixed $value
	 * @return IReturnValue
	 */
	public function onNull($value): IReturnValue;
}