<?php
namespace Narrator;


interface IReturnValue
{
	/**
	 * @param string $type
	 * @param callable|mixed $value
	 * @return IParams
	 */
	public function byType(string $type, $value): IParams;
	
	/**
	 * @param array $valueByType
	 * @return IParams
	 */
	public function byTypes(array $valueByType): IParams;
	
	/**
	 * @param string $subType
	 * @param callable|mixed $value
	 * @return IParams
	 */
	public function bySubType(string $subType, $value): IParams;
	
	/**
	 * @param array $valueBySubType
	 * @return IParams
	 */
	public function bySubTypes(array $valueBySubType): IParams;
	
	/**
	 * @param callable|mixed $value
	 * @return IParams
	 */
	public function defaultValue($value): IParams;
	
	/**
	 * @param callable|mixed $value
	 * @return IParams
	 */
	public function onNull($value): IParams;
}