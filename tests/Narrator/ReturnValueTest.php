<?php
namespace Narrator;


use PHPUnit\Framework\TestCase;


class ReturnValueTest extends TestCase
{
	public function test_ReturnSelf()
	{
		$subject = new ReturnValue();
		
		self::assertEquals($subject, $subject->bySubType('a', 1));
		self::assertEquals($subject, $subject->bySubTypes([]));
		self::assertEquals($subject, $subject->byType('a', 1));
		self::assertEquals($subject, $subject->byTypes([]));
		self::assertEquals($subject, $subject->defaultValue(1));
		self::assertEquals($subject, $subject->byValue(1, 2));
		self::assertEquals($subject, $subject->int(1));
		self::assertEquals($subject, $subject->bool(1));
		self::assertEquals($subject, $subject->string(1));
		self::assertEquals($subject, $subject->float(1));
		self::assertEquals($subject, $subject->null(1));
	}

	/**
	 * @expectedException \Narrator\Exceptions\NotAScalarException
	 */
	public function test_get_ValueObjectByValue_ExceptionThrown()
	{
		$subject = new ReturnValue();
		$subject->byValue(new \stdClass(), 1);
	}
	
	public function test_get_ValueScalarByValue_ValueReturned()
	{
		$subject = new ReturnValue();
		$subject->byValue(2, 1);
		
		self::assertEquals(1, $subject->get(2));
	}
	
	public function test_get_ValueScalarByValue_CallbackValueReturned()
	{
		$subject = new ReturnValue();
		$subject->byValue(2, function() {return 1;});
		
		self::assertEquals(1, $subject->get(2));
	}
	
	public function test_get_ValueScalarByType_ValueReturned()
	{
		$subject = new ReturnValue();
		$subject->int(1);
		
		self::assertEquals(1, $subject->get(2));
	}
	
	public function test_get_ValueIntByType_ValueReturned()
	{
		$subject = new ReturnValue();
		$subject->byType('int', 1);
		
		self::assertEquals(1, $subject->get(2));
	}
	
	public function test_get_ValuesByTypes_ValueReturned()
	{
		$subject = new ReturnValue();
		$subject->byTypes([
			'int' 		=> 1,
			'string' 	=> 'test'
		]);
		
		self::assertEquals(1, $subject->get(2));
	}
	
	public function test_get_ValueScalarByType_CallbackValueReturned()
	{
		$subject = new ReturnValue();
		$subject->int(function() {return 1;});
		
		self::assertEquals(1, $subject->get(2));
	}
	
	public function test_get_ValueObjectByType_ValueReturned()
	{
		$subject = new ReturnValue();
		$subject->byType(ReturnValueTestHelper_B::class, 1);
		
		self::assertEquals(1, $subject->get(new ReturnValueTestHelper_B()));
	}
	
	public function test_get_ValueObjectByType_CallbackValueReturned()
	{
		$subject = new ReturnValue();
		$subject->byType(ReturnValueTestHelper_B::class, function() {return 1;});
		
		self::assertEquals(1, $subject->get(new ReturnValueTestHelper_B()));
	}
	
	public function test_get_ValueObjectBySubtype_ValueReturned()
	{
		$subject = new ReturnValue();
		$subject->bySubType(ReturnValueTestHelper_A::class, 1);
		
		self::assertEquals(1, $subject->get(new ReturnValueTestHelper_B()));
	}
	
	public function test_get_ValueObjectBySubtype_CallbackValueReturned()
	{
		$subject = new ReturnValue();
		$subject->bySubType(ReturnValueTestHelper_A::class, function() {return 1;});
		
		self::assertEquals(1, $subject->get(new ReturnValueTestHelper_B()));
	}
	
	public function test_get_ValueNull_ValueReturned()
	{
		$subject = new ReturnValue();
		$subject->null(1);
		
		self::assertEquals(1, $subject->get(null));
	}
	
	public function test_get_ValueNull_CallbackValueReturned()
	{
		$subject = new ReturnValue();
		$subject->null(function() {return 1;});
		
		self::assertEquals(1, $subject->get(null));
	}
	
	public function test_get_ValueNull_DefaultReturned()
	{
		$subject = new ReturnValue();
		$subject->defaultValue(1);
		
		self::assertEquals(1, $subject->get(null));
	}
	
	public function test_get_ValueNull_DefaultCallbackReturned()
	{
		$subject = new ReturnValue();
		$subject->defaultValue(function() {return 1;});
		
		self::assertEquals(1, $subject->get(null));
	}
	
	public function test_get_ValueScalar_DefaultReturned()
	{
		$subject = new ReturnValue();
		$subject->defaultValue(1);
		
		self::assertEquals(1, $subject->get(2));
	}
	
	public function test_get_ValueScalar_DefaultCallbackReturned()
	{
		$subject = new ReturnValue();
		$subject->defaultValue(function() {return 1;});
		
		self::assertEquals(1, $subject->get(2));
	}
	
	public function test_get_ValueObject_DefaultReturned()
	{
		$subject = new ReturnValue();
		$subject->defaultValue(1);
		
		self::assertEquals(1, $subject->get(new ReturnValueTestHelper_B()));
	}
	
	public function test_get_ValueObject_DefaultCallbackReturned()
	{
		$subject = new ReturnValue();
		$subject->defaultValue(function() {return 1;});
		
		self::assertEquals(1, $subject->get(new ReturnValueTestHelper_B()));
	}
	
	public function test_get_NoReturnValueSet_ReturnOriginalValue()
	{
		$subject = new ReturnValue();
		
		self::assertEquals(2, $subject->get(2));
	}
}


class ReturnValueTestHelper_A {}

class ReturnValueTestHelper_B extends ReturnValueTestHelper_A {}