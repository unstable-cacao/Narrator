<?php
namespace Narrator;


interface INarrator
{
	public function __clone();
	public function __invoke();
	
	/**
	 * @param callable|null $callback
	 * @param callable|null $invoker
	 * @return mixed
	 */
	public function invoke(?callable $callback = null, ?callable $invoker = null);
	
	/**
	 * @param object $object
	 * @param string $method
	 * @return mixed
	 */
	public function invokeMethodIfExists($object, string $method);
	
	public function params(): IParams;
	public function exceptions(): IExceptions;
	public function returnValue(): IReturnValue;
	
	public function setCallback(callable $callback): INarrator;

	/**
	 * Called before the target callback is invoked.
	 * @param callable $callback
	 * @return INarrator
	 */
	public function before(callable $callback): INarrator;

	/**
	 * Called after the target callback executed and only if all exceptions had been handled.
	 * @param callable $callback
	 * @return INarrator
	 */
	public function after(callable $callback): INarrator;

	/**
	 * Always called after target callback, even if an exception occurred.
	 * @param callable $callback
	 * @return INarrator
	 */
	public function always(callable $callback): INarrator;
}