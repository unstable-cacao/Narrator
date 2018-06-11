<?php
namespace Narrator;


use PHPUnit\Framework\TestCase;
use Skeleton\Skeleton;


class ParamsTest extends TestCase
{
	public function test_ReturnSelf()
	{
		$subject = new Params();
		
		self::assertEquals($subject, $subject->bySubType('a', 1));
		self::assertEquals($subject, $subject->bySubTypes([]));
		self::assertEquals($subject, $subject->byType('a', 1));
		self::assertEquals($subject, $subject->byTypes([]));
		self::assertEquals($subject, $subject->byName('a', 1));
		self::assertEquals($subject, $subject->byNames([]));
		self::assertEquals($subject, $subject->atPosition(0, 1));
		self::assertEquals($subject, $subject->first('a'));
		self::assertEquals($subject, $subject->last('a'));
		self::assertEquals($subject, $subject->addCallback(function() {}));
	}
	
	public function test_get_EmptyArrayPassed_EmptyArrayReturned()
	{
		$subject = new Params();
		
		self::assertEmpty($subject->get([]));
	}

	/**
	 * @expectedException \Narrator\Exceptions\CouldNotResolveParameterException
	 */
	public function test_get_ParameterNotFound_ExceptionThrown()
	{
		$subject = new Params();
		$n = new class
		{
			public function a(int $i) {}
		};
		
		$subject->get([new \ReflectionParameter([$n, 'a'], 'i')]);
	}
	
	public function test_get_ParameterByPositionAtFirstIndex_ValueReturned()
	{
		$subject = new Params();
		$subject->first(1);
		$n = new class
		{
			public function a(int $i) {}
		};
		
		self::assertEquals(1, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
	}
	
	public function test_get_ParameterByPositionAtFirstIndex_CallbacksValueReturned()
	{
		$subject = new Params();
		$subject->first(function() {return 1;});
		$n = new class
		{
			public function a(int $i) {}
		};
		
		self::assertEquals(1, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
	}
	
	public function test_get_ParameterByPositionNotAtFirstIndex_ValueReturned()
	{
		$subject = new Params();
		$subject->first(1);
		$subject->atPosition(1, 2);
		$n = new class
		{
			public function a(int $i, int $j) {}
		};
		
		self::assertEquals(2, $subject->get((new \ReflectionMethod($n, 'a'))->getParameters())[1]);
	}
	
	public function test_get_ParameterByPositionNotAtFirstIndex_CallbacksValueReturned()
	{
		$subject = new Params();
		$subject->first(1);
		$subject->atPosition(1, function() {return 2;});
		$n = new class
		{
			public function a(int $i, int $j) {}
		};
		
		self::assertEquals(2, $subject->get((new \ReflectionMethod($n, 'a'))->getParameters())[1]);
	}
	
	public function test_get_ParameterByPositionAtLastIndex_ValueReturned()
	{
		$subject = new Params();
		$subject->first(1);
		$subject->atPosition(1, 2);
		$subject->last(3);
		$n = new class
		{
			public function a(int $i, int $j, int $k) {}
		};
		
		self::assertEquals(3, $subject->get((new \ReflectionMethod($n, 'a'))->getParameters())[2]);
	}
	
	public function test_get_ParameterByPositionAtLastIndex_CallbacksValueReturned()
	{
		$subject = new Params();
		$subject->first(1);
		$subject->atPosition(1, 2);
		$subject->last(function() {return 3;});
		$n = new class
		{
			public function a(int $i, int $j, int $k) {}
		};
		
		self::assertEquals(3, $subject->get((new \ReflectionMethod($n, 'a'))->getParameters())[2]);
	}
	
	public function test_get_ParameterByType_ValueReturned()
	{
		$subject = new Params();
		$subject->byType('int', 1);
		$n = new class
		{
			public function a(int $i) {}
		};
		
		self::assertEquals(1, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
	}
	
	public function test_get_ParameterByType_CallbacksValueReturned()
	{
		$subject = new Params();
		$subject->byType('int', function() {return 1;});
		$n = new class
		{
			public function a(int $i) {}
		};
		
		self::assertEquals(1, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
	}
	
	public function test_get_ParameterBySubType_ValueReturned()
	{
		$subject = new Params();
		$subject->bySubType(ParamsTestHelper_I2::class, 1);
		$n = new class
		{
			public function a(ParamsTestHelper_A $i) {}
		};
		
		self::assertEquals(1, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
	}
	
	public function test_get_ParameterBySubType_CallbacksValueReturned()
	{
		$subject = new Params();
		$subject->bySubType(ParamsTestHelper_I2::class, function() {return 1;});
		$n = new class
		{
			public function a(ParamsTestHelper_A $i) {}
		};
		
		self::assertEquals(1, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
	}
	
	public function test_get_ParameterBySubTypeDeepInheritance_ValueReturned()
	{
		$subject = new Params();
		$subject->bySubType(ParamsTestHelper_I1::class, 1);
		$n = new class
		{
			public function a(ParamsTestHelper_A $i) {}
		};
		
		self::assertEquals(1, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
	}
	
	public function test_get_ParameterBySubTypeDeepInheritance_CallbacksValueReturned()
	{
		$subject = new Params();
		$subject->bySubType(ParamsTestHelper_I1::class, function() {return 1;});
		$n = new class
		{
			public function a(ParamsTestHelper_A $i) {}
		};
		
		self::assertEquals(1, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
	}
	
	public function test_get_ParameterBySubTypeInterfaceImplementation_ValueReturned()
	{
		$subject = new Params();
		$subject->bySubType(ParamsTestHelper_I1::class, 1);
		$n = new class
		{
			public function a(ParamsTestHelper_I2 $i) {}
		};
		
		self::assertEquals(1, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
	}
	
	public function test_get_ParameterBySubTypeInterfaceImplementation_CallbacksValueReturned()
	{
		$subject = new Params();
		$subject->bySubType(ParamsTestHelper_I1::class, function() {return 1;});
		$n = new class
		{
			public function a(ParamsTestHelper_I2 $i) {}
		};
		
		self::assertEquals(1, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
	}
	
	public function test_get_ParameterBySubTypeInheritance_ValueReturned()
	{
		$subject = new Params();
		$subject->bySubType(ParamsTestHelper_I1::class, 1);
		$n = new class
		{
			public function a(ParamsTestHelper_B $i) {}
		};
		
		self::assertEquals(1, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
	}
	
	public function test_get_ParameterBySubTypeInheritance_CallbacksValueReturned()
	{
		$subject = new Params();
		$subject->bySubType(ParamsTestHelper_I1::class, function() {return 1;});
		$n = new class
		{
			public function a(ParamsTestHelper_B $i) {}
		};
		
		self::assertEquals(1, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
	}
	
	public function test_get_ParameterByName_ValueReturned()
	{
		$subject = new Params();
		$subject->byName('i', 1);
		$n = new class
		{
			public function a(int $i) {}
		};
		
		self::assertEquals(1, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
	}
	
	public function test_get_ParameterByName_CallbacksValueReturned()
	{
		$subject = new Params();
		$subject->byName('i', function() {return 1;});
		$n = new class
		{
			public function a(int $i) {}
		};
		
		self::assertEquals(1, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
	}
	
	public function test_get_ParameterOrderLast_ValueReturned()
	{
		$subject = new Params();
		
		$subject->last(1);
		$subject->atPosition(0, 2);
		$subject->byType(ParamsTestHelper_A::class, 3);
		$subject->bySubType(ParamsTestHelper_I1::class, 4);
		$subject->byName('i', 5);
		
		$n = new class
		{
			public function a(ParamsTestHelper_A $i) {}
		};
		
		self::assertEquals(1, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
	}
	
	public function test_get_ParameterOrderPosition_ValueReturned()
	{
		$subject = new Params();
		
		$subject->atPosition(0, 2);
		$subject->byType(ParamsTestHelper_A::class, 3);
		$subject->bySubType(ParamsTestHelper_I1::class, 4);
		$subject->byName('i', 5);
		
		$n = new class
		{
			public function a(ParamsTestHelper_A $i) {}
		};
		
		self::assertEquals(2, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
	}
	
	public function test_get_ParameterOrderType_ValueReturned()
	{
		$subject = new Params();
		
		$subject->byType(ParamsTestHelper_A::class, 3);
		$subject->bySubType(ParamsTestHelper_I1::class, 4);
		$subject->byName('i', 5);
		
		$n = new class
		{
			public function a(ParamsTestHelper_A $i) {}
		};
		
		self::assertEquals(3, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
	}
	
	public function test_get_ParameterOrderSubType_ValueReturned()
	{
		$subject = new Params();
		
		$subject->bySubType(ParamsTestHelper_I1::class, 4);
		$subject->byName('i', 5);
		
		$n = new class
		{
			public function a(ParamsTestHelper_A $i) {}
		};
		
		self::assertEquals(4, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
	}
    
    public function test_get_ParameterNotResolved_DefaultValueReturned()
    {
        $subject = new Params();
        
        $n = new class
        {
            public function a(int $i = 5) {}
        };
        
        self::assertEquals(5, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
    }
    
    public function test_get_SkeletonExists_ReturnFromSkeleton()
    {
        $subject = new Params();
        $obj = new ParamsTestHelper_A();
        $skeleton = new Skeleton();
        $skeleton->set(ParamsTestHelper_I1::class, $obj);
        $subject->fromSkeleton($skeleton);
        
        $n = new class
        {
            public function a(ParamsTestHelper_I1 $i = null) {}
        };
        
        self::assertEquals($obj, $subject->get([new \ReflectionParameter([$n, 'a'], 'i')])[0]);
    }
    
    /**
     * @expectedException \Narrator\Exceptions\CouldNotResolveParameterException
     */
    public function test_get_SkeletonExistsButParamNotFound_ExceptionThrown()
    {
        $subject = new Params();
        $skeleton = new Skeleton();
        $subject->fromSkeleton($skeleton);
        
        $n = new class
        {
            public function a(ParamsTestHelper_I1 $i) {}
        };
    
        $subject->get([new \ReflectionParameter([$n, 'a'], 'i')]);
    }
	
	
	public function test_get_CallbackSetup_CallbackInvoked()
	{
		$isCalled = false;
		
		$subject = new Params();
		$n = new class
		{
			public function a($i) {}
		};
		
		$subject->addCallback(function ($parameter, &$isFound) 
			use (&$isCalled)
			{
                $isFound = true;
				$isCalled = true;
			});
		
		$subject->get([new \ReflectionParameter([$n, 'a'], 'i')]);
		
		self::assertTrue($isCalled);
	}
}

interface ParamsTestHelper_I1 {}

interface ParamsTestHelper_I2 extends ParamsTestHelper_I1 {}

class ParamsTestHelper_A implements ParamsTestHelper_I2 {}

class ParamsTestHelper_B extends ParamsTestHelper_A {}