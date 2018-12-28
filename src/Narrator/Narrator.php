<?php
namespace Narrator;


use Narrator\Exceptions\CallbackExpectedException;


class Narrator implements INarrator
{
	/** @var IParams */
	private $params = null;
	
	/** @var IExceptions */
	private $exceptions = null;
	
	/** @var IReturnValue */
	private $returnValue = null;

	/** @var callable|null */
	private $callback = null;
	
	/** @var callable|null */
	private $before = null;
	
	/** @var callable|null */
	private $after = null;
	
	/** @var callable|null */
	private $always = null;
	
	
	private function getCallback(?callable $callback = null): callable 
	{
		if ($callback)
			return $callback;
		
		if ($this->callback)
			return $this->callback;
		
		throw new CallbackExpectedException();
	}
	
	private function invokeFunction(?callable $callback): void
	{
		if ($callback)
		{
			$callback();
		}
	}
	
	
	public function __construct(callable $callback = null)
	{
		$this->params = new Params();
		$this->exceptions = new Exceptions();
		$this->returnValue = new ReturnValue();
		$this->callback = $callback;
	}
	
	public function __clone()
	{
		$this->params = clone $this->params;
		$this->exceptions = clone $this->exceptions;
		$this->returnValue = clone $this->returnValue;
	}
	
	public function __invoke()
	{
		return $this->invoke();
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
		$this->before = $callback;
		return $this;
	}
	
	/**
	 * Called after the target callback executed and only if all exceptions had been handled.
	 * @param callable $callback
	 * @return INarrator
	 */
	public function after(callable $callback): INarrator
	{
		$this->after = $callback;
		return $this;
	}
	
	/**
	 * Always called after target callback, even if an exception occurred.
	 * @param callable $callback
	 * @return INarrator
	 */
	public function always(callable $callback): INarrator
	{
		$this->always = $callback;
		return $this;
	}

	/**
	 * @param callable|null $callback
	 * @param callable|null $invoker
	 * @return mixed
	 */
	public function invoke(?callable $callback = null, ?callable $invoker = null)
	{
		$callback = $this->getCallback($callback);
		
		if (is_array($callback))
		{
			$reflection = new \ReflectionMethod(get_class($callback[0]), $callback[1]);
		}
		else
		{
			$reflection = new \ReflectionFunction($callback);
		}
		
		try
		{
			$this->invokeFunction($this->before);
			
			$params = $this->params->get($reflection->getParameters());
			$returnedValue = call_user_func_array($invoker ?: $callback, $params);
			$result = $this->returnValue->get($returnedValue);
			
			$this->invokeFunction($this->after);
			
			return $result;
		}
		catch (\Throwable $e)
		{
			return $this->exceptions->handle($e);
		}
		finally
		{
			$this->invokeFunction($this->always);
		}
	}
	
	/**
	 * @param object $object
	 * @param string $method
	 * @return mixed
	 */
	public function invokeMethodIfExists($object, string $method)
	{
		if (!method_exists($object, $method) || !is_callable([$object, $method]))
			/** @noinspection PhpInconsistentReturnPointsInspection */
			return;
		
		return $this->invoke([$object, $method]);
	}

	public function setCallback(callable $callback): INarrator
	{
		$this->callback = $callback;
		return $this;
	}
}