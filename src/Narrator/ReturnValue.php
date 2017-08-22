<?php
namespace Narrator;


class ReturnValue implements IReturnValue
{
	private const OBJECT 	= 'object';
	private const INT 		= 'integer';
	private const BOOL 		= 'boolean';
	private const FLOAT 	= 'double';
	private const NULL 		= 'NULL';
	private const STRING	= 'string';
	
	private const TYPE_MAP = [
		'int' 	=> self::INT,
		'bool' 	=> self::BOOL,
		'float' => self::FLOAT,
		'null'	=> self::NULL
	];
	
	
	/** @var array */
	private $returnByType = [];
	
	/** @var array */
	private $returnBySubType = [];
	
	/** @var array */
	private $returnByValue = [];
	
	/** @var callable|mixed|null */
	private $default = null;
	
	
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
		if (key_exists($type, self::TYPE_MAP))
		{
			$this->returnByType[self::TYPE_MAP[$type]] = $value;
		}
		else
		{
			$this->returnByType[$type] = $value;
		}
		
		return $this;
	}
	
	/**
	 * @param array $valueByType
	 * @return IReturnValue
	 */
	public function byTypes(array $valueByType): IReturnValue
	{
		foreach ($valueByType as $type => $value) 
		{
			$this->byType($type, $value);
		}
		
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
	 * @param int|float|string|bool|null $value
	 * @param callable|mixed $returnValue
	 * @return IReturnValue
	 */
	public function byValue($value, $returnValue): IReturnValue
	{
		if (is_null($value))
		{
			$this->returnByValue[self::NULL] = $returnValue;
		}
		else
		{
			if (!in_array(gettype($value), self::TYPE_MAP))
				throw new \Exception("Only scalar values or null are allowed.");
			
			$this->returnByValue[$value] = $returnValue;
		}
		
		return $this;
	}
	
	/**
	 * @param callable|mixed $value
	 * @return IReturnValue
	 */
	public function int($value): IReturnValue
	{
		return $this->byType(self::INT, $value);
	}
	
	/**
	 * @param callable|mixed $value
	 * @return IReturnValue
	 */
	public function float($value): IReturnValue
	{
		return $this->byType(self::FLOAT, $value);
	}
	
	/**
	 * @param callable|mixed $value
	 * @return IReturnValue
	 */
	public function string($value): IReturnValue
	{
		return $this->byType(self::STRING, $value);
	}
	
	/**
	 * @param callable|mixed $value
	 * @return IReturnValue
	 */
	public function bool($value): IReturnValue
	{
		return $this->byType(self::BOOL, $value);
	}
	
	/**
	 * @param callable|mixed $value
	 * @return IReturnValue
	 */
	public function null($value): IReturnValue
	{
		return $this->byValue(self::NULL, $value);
	}
	
	/**
	 * @param mixed $value
	 * @return mixed
	 */
	public function get($value)
	{
		$type = gettype($value);
		
		if ((is_null($value) || in_array($type, self::TYPE_MAP)) && key_exists($value, $this->returnByValue))
		{
			return $this->getValue($this->returnByValue[$value], $value);
		}
		else
		{
			$type = gettype($value);
			
			if ($type == self::OBJECT)
			{
				$class = get_class($value);
				
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