<?php
namespace Narrator;


class ReturnValue implements IReturnValue
{
	/** @var array */
	private $returnByType = [];
	
	/** @var array */
	private $returnBySubType = [];
	
	/** @var callable|mixed|null */
	private $default = null;
	
	/** @var callable|mixed|null */
	private $onNull = null;
	
	
	/**
	 * @param callable|mixed $returnValue
	 * @param mixed $originalValue
	 * @return mixed
	 */
	private function getValue($returnValue, $originalValue)
	{
		return is_callable($returnValue) ? $returnValue($originalValue) : $returnValue;
	}
	
	
	/**
	 * @param string $type
	 * @param callable|mixed $value
	 * @return IReturnValue
	 */
	public function byType(string $type, $value): IReturnValue
	{
		$this->returnByType[$type] = $value;
		return $this;
	}
	
	/**
	 * @param array $valueByType
	 * @return IReturnValue
	 */
	public function byTypes(array $valueByType): IReturnValue
	{
		$this->returnByType = array_merge($this->returnByType, $valueByType);
		return $this;
	}
	
	/**
	 * @param string $subType
	 * @param callable|mixed $value
	 * @return IReturnValue
	 */
	public function bySubType(string $subType, $value): IReturnValue
	{
		$this->returnBySubType[$subType] = $value;
		return $this;
	}
	
	/**
	 * @param array $valueBySubType
	 * @return IReturnValue
	 */
	public function bySubTypes(array $valueBySubType): IReturnValue
	{
		$this->returnBySubType = array_merge($this->returnBySubType, $valueBySubType);
		return $this;
	}
	
	/**
	 * @param callable|mixed $value
	 * @return IReturnValue
	 */
	public function defaultValue($value): IReturnValue
	{
		$this->default = $value;
		return $this;
	}
	
	/**
	 * @param callable|mixed $value
	 * @return IReturnValue
	 */
	public function onNull($value): IReturnValue
	{
		$this->onNull = $value;
		return $this;
	}
	
	/**
	 * @param mixed $value
	 * @return mixed
	 */
	public function get($value)
	{
		if (is_null($value))
		{
			if ($this->onNull)
				return $this->getValue($this->onNull, $value);
		}
		else
		{
			$type = gettype($value);
			$class = get_class($value);
			
			if ($class)
			{
				if (key_exists($class, $this->returnByType))
					return $this->getValue($this->returnByType[$class], $value);
				
				foreach ($this->returnBySubType as $subType => $returnValue)
				{
					if ($value instanceof $subType)
						return $this->getValue($this->returnBySubType[$subType], $value);
				}
			}
			else
			{
				if (key_exists($type, $this->returnByType))
					return $this->getValue($this->returnByType[$type], $value);
			}
		}
		
		if ($this->default)
			return $this->getValue($this->default, $value);
		
		return $value;
	}
}