<?php
namespace Narrator;


interface IExceptions
{
	public function defaultHandler(callable $handler): IExceptions;
	public function byType(string $typeName, callable $handler): IExceptions;
	public function byTypes(array $handlersByTypes): IExceptions;
	public function bySubType(string $subTypeName, callable $handler): IExceptions;
	public function bySubTypes(array $handlersBySubTypes): IExceptions;

	/**
	 * @param string|mixed|array $item
	 * @return IExceptions
	 */
	public function addSetup($item): IExceptions;
}