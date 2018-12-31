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
	
	public function test_invoke_InvokerMethodProvided_InvokerCalled()
	{
		$isInvoked = false;
		$subject = new Narrator();
		
		$subject->invoke(
			function () {},
			function () use (&$isInvoked) { $isInvoked = true; }
		);
		
		self::assertTrue($isInvoked);
	}
	
	public function test_invoke_InvokerMethodProvided_ParamsPassedToInvokeMethod()
	{
		$subject = new Narrator();
		$subject->params()->byName('a', 1);
		$subject->params()->byName('b', 2);
		
		$subject->invoke(
			function ($a) {},
			function ($b) use (&$param) { $param = $b; }
		);
		
		self::assertEquals(1, $param);
	}
	
	public function test_invoke_InvokerReflectionMethodProvided_ParamsPassedToInvokeMethod()
	{
		$subject = new Narrator();
		$subject->params()->byName('a', 1);
		$subject->params()->byName('b', 2);
		
		$subject->invoke(
			function ($a, $b) {},
			new \ReflectionFunction(function (...$arg) use (&$param) { $param = $arg; })
		);
		
		self::assertEquals([1, 2], $param);
	}
	
	public function test_invoke_ReflectionPassed_MethodInvoked()
	{
		$subject = new Narrator();
		$isCalled = false;
		$f = function () use (&$isCalled) { $isCalled = true; };
		
		$subject->invoke(new \ReflectionFunction($f));
		
		self::assertTrue($isCalled);
	}
	
	public function test_invoke_CallsInvoke()
	{
		$subject = new Narrator();
		$isCalled = false;
		$subject->setCallback(function() use (&$isCalled) { $isCalled = true; });
		$subject();
		
		self::assertTrue($isCalled);
	}
	
	
	public function test_invokeMethodIfExists_MethodFound_MethodInvoked()
	{
		$subject = new Narrator();
		$subject->params()->byName('b', 2);
		
		$c = new class 
		{
			public function d($b) { return $b; }
		};
		
		$res = $subject->invokeMethodIfExists($c, 'd');
		
		
		self::assertEquals($res, 2);
	}
	
	public function test_invokeMethodIfExists_MethodNotFound_NoExceptionThrown()
	{
		$subject = new Narrator();
		$c = new class {};
		
		$res = $subject->invokeMethodIfExists($c, 'notFound');
		
		
		self::assertNull($res);
	}
	
	public function test_invokeMethodIfExists_NonPublicMethodFound_NoExceptionThrown()
	{
		$subject = new Narrator();
		
		$c1 = new class 
		{
			public $called = false;
			protected function call() { $this->called = true; }
		};
		
		$c2 = new class 
		{
			public $called = false;
			private function call() { $this->called = true; }
		};
		
		$subject->invokeMethodIfExists($c1, 'call');
		$subject->invokeMethodIfExists($c2, 'call');
		
		self::assertFalse($c1->called);
		self::assertFalse($c2->called);
	}
	
	public function test_invokeMethodIfExists_NonPublicMethodWithInvoker_InvokerCalled()
	{
		$subject = new Narrator();
		$c = new class { private function prv() {} };
		$called = false;
		
		$subject->invokeMethodIfExists($c, 'prv', function () use (&$called) { $called = true; });
		
		self::assertTrue($called);
	}
	
	public function test_invokeMethodIfExists_NonPublicMethodWithInvoker_InvokerCalledWithArgs()
	{
		$subject = new Narrator();
		$subject->params()->byName('a', 1);
		$c = new class { private function prv($a) {} };
		$param = null;
		
		$subject->invokeMethodIfExists($c, 'prv', function ($a) use (&$param) { $param = $a; });
		
		self::assertEquals(1, $param);
	}
	
	public function test_invokeMethodIfExists_MethodNotExistWithInvoker_InvokerNotCalled()
	{
		$subject = new Narrator();
		$subject->params()->byName('a', 1);
		$c = new class {};
		$called = false;
		
		$subject->invokeMethodIfExists($c, 'notFound', function () use (&$called) { $called = true; });
		
		self::assertFalse($called);
	}
	
	
	public function test_invokeIfExists_CallableExists_Invoke()
	{
		$subject = new Narrator();
		$subject->params()->byName('a', 1);
		$called = false;
		
		$subject->invokeIfExists(function() use (&$called) { $called = true; });
		
		self::assertTrue($called);
	}
	
	public function test_invokeIfExists_CallableNotExists_CallableNotInvoked()
	{
		$subject = new Narrator();
		
		$subject->invokeIfExists('asdas' . rand(0, 1000));
	}
	
	public function test_invokeIfExists_CallableExistsAndInvokerPassed_InvokerCalled()
	{
		$subject = new Narrator();
		$called = false;
		
		$subject->invokeIfExists(function() {}, function() use (&$called) { $called = true; });
		
		self::assertTrue($called);
	}
	
	public function test_invokeIfExists_CallableNotExistsAndInvokerPassed_InvokerCalled()
	{
		$subject = new Narrator();
		$called = false;
		
		$subject->invokeIfExists('asdas' . rand(0, 1000), function() use (&$called) { $called = true; });
		
		self::assertFalse($called);
	}
	
	public function test_invokeIfExists_CallableExistsAndInvokerPassed_InvokerCalledWithArgs()
	{
		$subject = new Narrator();
		$subject->params()->byName('a', 1);
		$param = false;
		
		$subject->invokeIfExists(function($a) {}, function($p) use (&$param) { $param = $p; });
		
		self::assertEquals(1, $param);
	}
	
	
	public function test_invokeCreateInstance_NoConstructor_NewInstanceReturned()
	{
		$subject = new Narrator();
		$class = new class {};
		
		$inst = $subject->invokeCreateInstance(get_class($class));
		
		self::assertInstanceOf(get_class($class), $inst);
	}
	
	public function test_invokeCreateInstance_ReflectionClassPassed_NewInstanceReturned()
	{
		$subject = new Narrator();
		$class = new class {};
		
		$inst = $subject->invokeCreateInstance(new \ReflectionClass($class));
		
		self::assertInstanceOf(get_class($class), $inst);
	}
	
	public function test_invokeCreateInstance_ClassHasConstructor_ConstructorInvoked()
	{
		$subject = new Narrator();
		$class = new class {
			public $a = 0;
			public function __construct()
			{
				$this->a = 1;
			}
		};
		
		$inst = $subject->invokeCreateInstance(new \ReflectionClass($class));
		
		self::assertEquals(1, $inst->a);
	}
	
	public function test_invokeCreateInstance_ConstructorHasArgs_ArgsPassed()
	{
		$subject = new Narrator();
		$subject->params()->byName('a', 1);
		$subject->params()->byName('b', 2);
		
		$class = new class {
			public $prms;
			public function __construct($a = 0, $b = 2)
			{
				$this->prms = [$a, $b];
			}
		};
		
		$inst = $subject->invokeCreateInstance(new \ReflectionClass($class));
		
		self::assertEquals([1, 2], $inst->prms);
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