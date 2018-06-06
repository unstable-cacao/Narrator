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
	
	public function test_invoke_CallbackFromParamCalled()
	{
		$subject = new Narrator();
		$isCalled = false;
		
		$subject->invoke(function() use (&$isCalled) { $isCalled = true; });
		
		self::assertTrue($isCalled);
	}
	
	public function test_invoke_CallbackFromMemberCalled()
	{
		$subject = new Narrator();
		$isCalled = false;
		$subject->setCallback(function() use (&$isCalled) { $isCalled = true; });
		
		$subject->invoke();
		
		self::assertTrue($isCalled);
	}
	
	public function test_invoke_ExceptionHandlerSet_ExceptionHandled()
	{
		$subject = new Narrator();
		$isCalled = false;
		$subject->exceptions()->defaultHandler(function() use (&$isCalled) { $isCalled = true; });
		$subject->invoke(function() {throw new \Exception();});
		
		self::assertTrue($isCalled);
	}
	
	/**
	 * @expectedException \Exception
	 */
	public function test_invoke_ExceptionHandlerNotSet_ExceptionThrown()
	{
		$subject = new Narrator();
		
		$subject->invoke(function() {throw new \Exception();});
	}
	
	/**
	 * @expectedException \Narrator\Exceptions\CouldNotResolveParameterException
	 */
	public function test_invoke_NoParamHandler_ExceptionThrown()
	{
		$subject = new Narrator();
		
		$subject->invoke(function(int $i) {});
	}
	
	public function test_invoke_ParamHandlerSet_ParamsPassedToCallback()
	{
		$subject = new Narrator();
		$gotParams = false;
		$subject->params()->byType('int', 1);
		
		$subject->invoke(function(int $i) use (&$gotParams)
		{
			if ($i == 1) $gotParams = true;
		});
		
		self::assertTrue($gotParams);
	}
	
	public function test_invoke_ReturnParameterReturned()
	{
		$subject = new Narrator();
		
		self::assertEquals(1, $subject->invoke(function() { return 1; }));
	}
	
	public function test_invoke_ReturnValuePassedToReturnHandler()
	{
		$subject = new Narrator();
		$subject->returnValue()->defaultValue(2);
		
		self::assertEquals(2, $subject->invoke(function() { return 1; }));
	}
	
	public function test_invoke_ObjectMethodPassed_MethodInvoked()
	{
		$subject = new Narrator();
		
		$class = new class 
		{
			public $isCalled = false;
			public function callMe()
			{
				$this->isCalled = true;
			}
		};
		
		$subject->invoke([$class, 'callMe']);
		
		self::assertTrue($class->isCalled);
	}
	
	public function test__invoke_CallsInvoke()
	{
		$subject = new Narrator();
		$isCalled = false;
		$subject->setCallback(function() use (&$isCalled) { $isCalled = true; });
		$subject();
		
		self::assertTrue($isCalled);
	}
	
	public function test__clone_ClonesMembers()
	{
		$subject = new Narrator();
		$subject->returnValue()->defaultValue(2);
		
		$cloned = clone $subject;
		$cloned->returnValue()->defaultValue(3);
		
		self::assertEquals(2, $subject->invoke(function() { return 1; }));
	}
}