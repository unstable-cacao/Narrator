<?php
namespace Narrator;


interface IReturnValue
{
	/**
	 * @param int|float|string|bool $value
	 * @param callable|mixed $returnValue
	 * @return IReturnValue
	 */
	public function byValue($value, $returnValue): IReturnValue;
	
	/**
	 * @param callable|mixed $value
	 * @return IReturnValue
	 */
	public function int($value): IReturnValue;
	
	/**
	 * @param callable|mixed $value
	 * @return IReturnValue
	 */
	public function float($value): IReturnValue;
	
	/**
	 * @param callable|mixed $value
	 * @return IReturnValue
	 */
	public function string($value): IReturnValue;
	
	/**
	 * @param callable|mixed $value
	 * @return IReturnValue
	 */
	public function bool($value): IReturnValue;
	
	/**
	 * @param callable|mixed $value
	 * @return IReturnValue
	 */
	public function null($value): IReturnValue;
	
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
	 * @param string|mixed|array $item
	 * @return IExceptions
	 */
	public function addSetup($item): IExceptions;
}