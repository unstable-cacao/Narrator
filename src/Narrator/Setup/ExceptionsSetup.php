<?php
namespace Narrator\Setup;


class ExceptionsSetup implements IClassSetup
{
	public function getCallbackFor(\Throwable $t): \ReflectionMethod
	{
		
	}
	
	public function tryGetCallbackFor(\Throwable $t, ?\ReflectionMethod $handler): bool 
	{
		
	}
}