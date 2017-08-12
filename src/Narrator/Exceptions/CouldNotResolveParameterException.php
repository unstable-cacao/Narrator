<?php
namespace Narrator\Exceptions;


class CouldNotResolveParameterException extends NarratorException
{
	public function __construct(int $index, \ReflectionParameter $parameter)
	{
		parent::__construct("Could not resolve parameter {$parameter->getName()} at position $index, " . 
			"method {$parameter->getDeclaringFunction()->getName()}");
	}
}