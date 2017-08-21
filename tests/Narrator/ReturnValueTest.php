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
		self::assertEquals($subject, $subject->onNull(1));
		self::assertEquals($subject, $subject->defaultValue(1));
		
	}
	
	public function test_get_ValueNull_ValueReturned()
	{
		$subject = new ReturnValue();
		$subject->onNull(1);
		
		self::assertEquals(1, $subject->get(null));
	}
	
	public function test_get_ValueNull_CallbackValueReturned()
	{
		$subject = new ReturnValue();
		$subject->onNull(function() {return 1;});
		
		self::assertEquals(1, $subject->get(null));
	}
	
	public function test_get_ValuePrimitive_ValueReturned()
	{
		$subject = new ReturnValue();
		$subject->byType('integer', 1);
		
		self::assertEquals(1, $subject->get(2));
	}
	
	public function test_get_ValuePrimitive_CallbackValueReturned()
	{
		$subject = new ReturnValue();
		$subject->byType('integer', function() {return 1;});
		
		self::assertEquals(1, $subject->get(2));
	}
	
	public function test_get_ValueObjectWithType_ValueReturned()
	{
		$subject = new ReturnValue();
		$subject->byType(ReturnValueTestHelper_B::class, 1);
		
		self::assertEquals(1, $subject->get(new ReturnValueTestHelper_B()));
	}
	
	public function test_get_ValueObjectWithType_CallbackValueReturned()
	{
		$subject = new ReturnValue();
		$subject->byType(ReturnValueTestHelper_B::class, function() {return 1;});
		
		self::assertEquals(1, $subject->get(new ReturnValueTestHelper_B()));
	}
	
	public function test_get_ValueObjectWithSubtype_ValueReturned()
	{
		$subject = new ReturnValue();
		$subject->bySubType(ReturnValueTestHelper_A::class, 1);
		
		self::assertEquals(1, $subject->get(new ReturnValueTestHelper_B()));
	}
	
	public function test_get_ValueObjectWithSubtype_CallbackValueReturned()
	{
		$subject = new ReturnValue();
		$subject->bySubType(ReturnValueTestHelper_A::class, function() {return 1;});
		
		self::assertEquals(1, $subject->get(new ReturnValueTestHelper_B()));
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
	
	public function test_get_ValuePrimitive_DefaultReturned()
	{
		$subject = new ReturnValue();
		$subject->defaultValue(1);
		
		self::assertEquals(1, $subject->get(2));
	}
	
	public function test_get_ValuePrimitive_DefaultCallbackReturned()
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
		
		self::assertNull($subject->get(null));
	}
}


class ReturnValueTestHelper_A {}

class ReturnValueTestHelper_B extends ReturnValueTestHelper_A {}