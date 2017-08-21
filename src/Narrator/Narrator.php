<?php
namespace Narrator;


class Narrator implements INarrator
{
	/** @var IParams */
	private $params;
	
	/** @var IExceptions */
	private $exceptions;
	
	/** @var IReturnValue */
	private $returnValue;
	
	
	public function __construct()
	{
		$this->params = new Params();
		$this->exceptions = new Exceptions();
		$this->returnValue = new ReturnValue();
	}
	
	public function __clone()
	{
		// TODO: Implement __clone() method.
	}
	
	public function __invoke(callable $target, ...$params)
	{
		// TODO: Implement __invoke() method.
	}
	
	public function params(): IParams
	{
		return $this->params;
	}
	
	public function exceptions(): IExceptions
	{
		return $this->exceptions;
	}
	
	public function returnValue(): IReturnValue
	{
		return $this->returnValue;
	}
	
	/**
	 * Called before the target callback is invoked.
	 * @param callable $callback
	 * @return INarrator
	 */
	public function before(callable $callback): INarrator
	{
		// TODO: Implement before() method.
	}
	
	/**
	 * Called after the target callback executed and only if all exceptions had been handled.
	 * @param callable $callback
	 * @return INarrator
	 */
	public function after(callable $callback): INarrator
	{
		// TODO: Implement after() method.
	}
	
	/**
	 * Always called after target callback, even if an exception occurred.
	 * @param callable $callback
	 * @return INarrator
	 */
	public function always(callable $callback): INarrator
	{
		// TODO: Implement always() method.
	}
}