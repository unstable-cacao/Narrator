<?php
namespace Narrator;


use Narrator\Exceptions\CouldNotResolveParameterException;
use Skeleton\Base\ISkeletonSource;
use Skeleton\Exceptions\ImplementerNotDefinedException;


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
	
	/** @var callable[] */
	private $callbacks = [];
	
	/** @var ISkeletonSource|null */
	private $skeleton = null;
	
	
	private function tryByPosition(\ReflectionParameter $parameter, &$value): bool 
    {
        if (key_exists($parameter->getPosition(), $this->paramsByPosition))
        {
            $value = $this->paramsByPosition[$parameter->getPosition()];
            $value = $this->getValue($value, $parameter);
            
            return true;
        }
        
        return false;
    }
    
    private function tryByType(\ReflectionParameter $parameter, &$value): bool
    {
        $type = (string)$parameter->getType();
        
        if ($type && key_exists($type, $this->paramsByType))
        {
            $value = $this->paramsByType[$type];
            $value = $this->getValue($value, $parameter);
            
            return true;
        }
        
        return false;
    }
    
    private function tryBySubType(\ReflectionParameter $parameter, &$value): bool
    {
        $class = $parameter->getClass();
        
        if ($class)
        {
            $class = $class->getName();
        
            foreach ($this->paramsBySubType as $subType => $val)
            {
                if (is_subclass_of($class, $subType))
                {
                    $value = $this->getValue($val, $parameter);
                    return true;
                }
            }
        }
        
        return false;
    }
    
    private function tryByName(\ReflectionParameter $parameter, &$value): bool
    {
        if (key_exists($parameter->getName(), $this->paramsByName))
        {
            $value = $this->paramsByName[$parameter->getName()];
            $value = $this->getValue($value, $parameter);
            
            return true;
        }
        
        return false;
    }
    
    private function tryCallback(\ReflectionParameter $parameter, &$value): bool
    {
        foreach ($this->callbacks as $callback)
        {
            $isFound = false;
            $result = $callback($parameter, $isFound);
        
            if ($isFound)
            {
                $value = $result;
                return true;
            }
        }
        
        return false;
    }
    
    private function tryFromSkeleton(\ReflectionParameter $parameter, &$value): bool
    {
        $class = $parameter->getClass();
        
        if ($class && $this->skeleton)
        {
            try
            {
                $value = $this->skeleton->get($class->getName());
            }
            catch (ImplementerNotDefinedException $e)
            {
                return false;
            }
    
            $value = $this->getValue($value, $parameter);
            
            return true;
        }
        
        return false;
    }
    
    private function tryDefaultValue(\ReflectionParameter $parameter, &$value): bool
    {
        if ($parameter->isOptional()) 
        {
            $value = $parameter->getDefaultValue();
            $value = $this->getValue($value, $parameter);
            
            return true;
        }
        
        return false;
    }
	
	private function getSingleParameter(\ReflectionParameter $parameter)
	{
		$result = $this->tryByPosition($parameter, $value) ||
            $this->tryByType($parameter, $value) ||
            $this->tryBySubType($parameter, $value) ||
            $this->tryByName($parameter, $value) ||
            $this->tryCallback($parameter, $value) ||
            $this->tryFromSkeleton($parameter, $value) ||
            $this->tryDefaultValue($parameter, $value);
		
		if (!$result)
            throw new CouldNotResolveParameterException($parameter);
		
		return $value;
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
    
    /**
     * @param ISkeletonSource $o
     * @return IParams
     */
	public function fromSkeleton(ISkeletonSource $o): IParams
    {
        $this->skeleton = $o;
        return $this;
    }
	
	/**
	 * @param callable $callback
	 * @return IParams
	 */
	public function addCallback(callable $callback): IParams
	{
		$this->callbacks[] = $callback;
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
				$result[] = $this->getSingleParameter($parameters[$i]);
			}
		}
		
		return $result;
	}
}