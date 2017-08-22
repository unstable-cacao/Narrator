<?php
namespace Narrator;


use PHPUnit\Framework\TestCase;


class NarratorTest extends TestCase
{
	public function test_ReturnSelf()
	{
		$subject = new Narrator();
		
		self::assertEquals($subject, $subject->after(function () {}));
		self::assertEquals($subject, $subject->before(function () {}));
		self::assertEquals($subject, $subject->always(function () {}));
		self::assertEquals($subject, $subject->after(function () {}));
		self::assertEquals($subject, $subject->setCallback(function () {}));
	}
	
	public function test_params_ReturnIParams()
	{
		$subject = new Narrator();
		
		self::assertInstanceOf(IParams::class, $subject->params());
	}
	
	public function test_returnValue_ReturnIReturnValue()
	{
		$subject = new Narrator();
		
		self::assertInstanceOf(IReturnValue::class, $subject->returnValue());
	}
	
	public function test_exceptions_ReturnIExceptions()
	{
		$subject = new Narrator();
		
		self::assertInstanceOf(IExceptions::class, $subject->exceptions());
	}

	/**
	 * @expectedException \Narrator\Exceptions\CallbackExpectedException
	 */
	public function test_invoke_NoCallback_ExceptionThrown()
	{
		$subject = new Narrator();
		$subject->invoke();
	}
	
	public function test_invoke_BeforeCalled()
	{
		$subject = new Narrator();
		$isCalled = false;
		
		$subject->before(function() use (&$isCalled) { $isCalled = true; });
		$subject->invoke(function() {});
		
		self::assertTrue($isCalled);
	}
	
	public function test_invoke_AfterCalled()
	{
		$subject = new Narrator();
		$isCalled = false;
		
		$subject->after(function() use (&$isCalled) { $isCalled = true; });
		$subject->invoke(function() {});
		
		self::assertTrue($isCalled);
	}
	
	public function test_invoke_AlwaysCalled()
	{
		$subject = new Narrator();
		$isCalled = false;
		
		$subject->always(function() use (&$isCalled) { $isCalled = true; });
		$subject->invoke(function() {});
		
		self::assertTrue($isCalled);
	}
	
	public function test_invoke_AlwaysCalledWithException()
	{
		$subject = new Narrator();
		$isCalled = false;
		
		$subject->exceptions()->defaultHandler(function() {});
		$subject->before(function() { throw new \Exception(); });
		$subject->always(function() use (&$isCalled) { $isCalled = true; });
		$subject->invoke(function() {});
		
		self::assertTrue($isCalled);
	}
	
	public function test_invoke_CallbackCalled()
	{
		$subject = new Narrator();
		$isCalled = false;
		
		$subject->invoke(function() use (&$isCalled) { $isCalled = true; });
		
		self::assertTrue($isCalled);
	}
	
	public function test_invoke_ReturnParameterReturned()
	{
		$subject = new Narrator();
		
		self::assertEquals(1, $subject->invoke(function() { return 1; }));
	}
}