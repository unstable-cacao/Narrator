<?php
namespace Narrator;


use Narrator\Exceptions\CouldNotResolveParameterException;


class Params implements IParams
{
	/** @var array */
	private $paramsByPosition = [];

	/** @var array */
	private $paramsByType = [];

	/** @var array */
	private $paramsBySubType = [];

	/** @var array */
	private $paramsByName = [];
	
	/** @var callable|mixed|null */
	private $last = null;
	
	
	private function getSingleParameter(int $i, \ReflectionParameter $parameter)
	{
		$value = null;
		$type = (string)$parameter->getType();
		$class = $parameter->getClass();
		
		if (key_exists($i, $this->paramsByPosition))
		{
			$value = $this->paramsByPosition[$i];
		}
		else if ($type && key_exists($type, $this->paramsByType))
		{
			$value = $this->paramsByType[$type];
		}
		else if ($class)
		{
			$class = $class->getName();
			
			foreach ($this->paramsBySubType as $subType => $val)
			{
				if (is_subclass_of($class, $subType))
				{
					$value = $val;
					break;
				}
			}
		}
		else if (key_exists($parameter->getName(), $this->paramsByName))
		{
			$value = $this->paramsByName[$parameter->getName()];
		}
		else
		{
			throw new CouldNotResolveParameterException($i, $parameter);
		}
		
		return $this->getValue($value, $parameter);
	}
	
	private function getValue($value, \ReflectionParameter $parameter)
	{
		return is_callable($value) ? $value($parameter) : $value;
	}
	

	/**
	 * @param string $name
	 * @param callable|mixed $value
	 * @return IParams
	 */
	public function byName(string $name, $value): IParams
	{
		$this->paramsByName[$name] = $value;
		return $this;
	}

	/**
	 * @param array $valueByName
	 * @return IParams
	 */
	public function byNames(array $valueByName): IParams
	{
		$this->paramsByName = array_merge($this->paramsByName, $valueByName);
		return $this;
	}

	/**
	 * @param string $type
	 * @param callable|mixed $value
	 * @return IParams
	 */
	public function byType(string $type, $value): IParams
	{
		$this->paramsByType[$type] = $value;
		return $this;
	}

	/**
	 * @param array $valueByType
	 * @return IParams
	 */
	public function byTypes(array $valueByType): IParams
	{
		$this->paramsByType = array_merge($this->paramsByType, $valueByType);
		return $this;
	}

	/**
	 * @param string $subType
	 * @param callable|mixed $value
	 * @return IParams
	 */
	public function bySubType(string $subType, $value): IParams
	{
		$this->paramsBySubType[$subType] = $value;
		return $this;
	}

	/**
	 * @param array $valueBySubType
	 * @return IParams
	 */
	public function bySubTypes(array $valueBySubType): IParams
	{
		$this->paramsBySubType = array_merge($this->paramsBySubType, $valueBySubType);
		return $this;
	}

	/**
	 * @param int $index
	 * @param callable|mixed $value
	 * @return IParams
	 */
	public function atPosition(int $index, $value): IParams
	{
		$this->paramsByPosition[$index] = $value;
		return $this;
	}

	/**
	 * @param callable|mixed $value
	 * @return IParams
	 */
	public function first($value): IParams
	{
		array_unshift($this->paramsByPosition, $value);
		return $this;
	}

	/**
	 * @param callable|mixed $value
	 * @return IParams
	 */
	public function last($value): IParams
	{
		$this->last = $value;
		return $this;
	}
	
	public function get(array $parameters): array 
	{
		$result = [];
		$length = count($parameters);
		
		for ($i = 0; $i < $length; $i++)
		{
			if ($i == $length - 1 && !is_null($this->last))
			{
				$result[] = $this->getValue($this->last, $parameters[$i]);
			}
			else
			{
				$result[] = $this->getSingleParameter($i, $parameters[$i]);
			}
		}
		
		return $result;
	}
}