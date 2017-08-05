<?php
namespace Narrator;


class Exceptions implements IExceptions
{
	/** @var callable */
	private $defaultHandler;
	
	/** @var array */
	private $typeHandlers = [];
	
	/** @var array */
	private $subTypeHandlers = [];
	
	
	public function defaultHandler(callable $handler): IExceptions
	{
		$this->defaultHandler = $handler;
		return $this;
	}

	public function byType(string $typeName, callable $handler): IExceptions
	{
		$this->typeHandlers[$typeName] = $handler;
		return $this;
	}

	public function byTypes(array $handlersByTypes): IExceptions
	{
		$this->typeHandlers = array_merge($this->typeHandlers, $handlersByTypes);
		return $this;
	}

	public function bySubType(string $subTypeName, callable $handler): IExceptions
	{
		$this->subTypeHandlers[$subTypeName] = $handler;
		return $this;
	}

	public function bySubTypes(array $handlersBySubTypes): IExceptions
	{
		$this->subTypeHandlers = array_merge($this->subTypeHandlers, $handlersBySubTypes);
		return $this;
	}
	
	public function handle(\Throwable $exception)
	{
		$type = get_class($exception);
		
		if (key_exists($type, $this->typeHandlers)) 
		{
			$callback = $this->typeHandlers[$type];
			return $callback($exception);
		}

		foreach ($this->subTypeHandlers as $subType => $handler)
		{
			if ($exception instanceof $subType)
			{
				return $handler($exception);
			}
		}
		
		if ($this->defaultHandler)
		{
			$callback = $this->defaultHandler;
			return $callback($exception);
		}
		
		throw $exception;
	}
}