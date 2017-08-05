<?php
namespace Narrator; 


use PHPUnit\Framework\TestCase;


class ExceptionsTest extends TestCase
{
	public function test_ReturnSelf()
	{
		$subject = new Exceptions();
		
		self::assertEquals($subject, $subject->bySubType('a', function() {}));
		self::assertEquals($subject, $subject->bySubTypes([]));
		self::assertEquals($subject, $subject->byType('a', function() {}));
		self::assertEquals($subject, $subject->byTypes([]));
		self::assertEquals($subject, $subject->defaultHandler(function() {}));
	}

	/**
	 * @expectedException \Exception
	 */
	public function test_handle_NoHandlerFound_ExceptionRethrown()
	{
		$subject = new Exceptions();
		$subject->handle(new \Exception());
	}
	
	public function test_handle_TypeHandlerSet_TypeHandlerInvoked()
	{
		$isCalled = false;
		
		$subject = new Exceptions();
		$subject->byType(\Exception::class, function() use (&$isCalled) {$isCalled = true;});
		$subject->handle(new \Exception());
		
		self::assertTrue($isCalled);
	}
	
	public function test_handle_SubTypeHandlerSet_SubTypeHandlerInvoked()
	{
		$isCalled = false;
		
		$subject = new Exceptions();
		$subject->bySubType(\Exception::class, function() use (&$isCalled) {$isCalled = true;});
		$subject->handle(new \Exception());
		
		self::assertTrue($isCalled);
	}
	
	public function test_handle_DefaultHandlerSet_DefaultHandlerInvoked()
	{
		$isCalled = false;
		
		$subject = new Exceptions();
		$subject->defaultHandler(function() use (&$isCalled) {$isCalled = true;});
		$subject->handle(new \Exception());
		
		self::assertTrue($isCalled);
	}
}