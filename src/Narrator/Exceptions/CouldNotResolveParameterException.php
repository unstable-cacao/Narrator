<?php
namespace Narrator\Exceptions;


class CouldNotResolveParameterException extends NarratorException
{
	public function __construct(\ReflectionParameter $parameter)
	{
		$index = $parameter->getPosition();
		
		$method = $parameter->getDeclaringFunction();
		$methodName = $method->getName();
		
		if ($method instanceof \ReflectionMethod)
		{
			$methodName = $method->getDeclaringClass()->getName() . '::' . $methodName;
		}
		
		parent::__construct("Could not resolve parameter {$parameter->getName()} at position $index, " .
			"method $methodName");
	}
}