<?php
namespace Narrator\Setup;


interface IClassSetup
{
	/**
	 * @param array|string|mixed $item
	 */
	public function add($item): void;
	public function count(): int;
}