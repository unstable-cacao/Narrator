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
}