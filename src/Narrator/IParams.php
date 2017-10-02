<?php
namespace Narrator;


interface IParams
{
	/**
	 * @param string $name
	 * @param callable|mixed $value
	 * @return IParams
	 */
	public function byName(string $name, $value): IParams;

	/**
	 * @param array $valueByName
	 * @return IParams
	 */
	public function byNames(array $valueByName): IParams;

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
	 * @param int $index
	 * @param callable|mixed $value
	 * @return IParams
	 */
	public function atPosition(int $index, $value): IParams;
	
	/**
	 * @param callable|mixed $value
	 * @return IParams
	 */
	public function first($value): IParams;
	
	/**
	 * @param callable|mixed $value
	 * @return IParams
	 */
	public function last($value): IParams;

	/**
	 * @param string|mixed|array $item
	 * @return IExceptions
	 */
	public function addSetup($item): IExceptions;
}