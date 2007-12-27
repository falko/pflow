<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ezcReflectionClassTest extends ezcTestCase
{
    /**
     * @var ezcReflectionClass
     */
    protected $class;

    public function setUp() {
        $this->class = new ezcReflectionClass('ezcReflectionClass');
    }

    public function tearDown() {
        unset($this->class);
    }

    public function testGetName() {
        self::assertEquals($this->class->getName(), 'ezcReflectionClass');
    }
	
	public function testCall() {
		$myRefClass = new ezcReflectionClass(new MyReflectionClass('MyReflectionClass'));
		self::assertTrue($myRefClass->doSomeMetaProgramming());
	}

    public function testGetMethod() {
        $method = $this->class->getMethod('getMethod');
        self::assertType('ezcReflectionMethod', $method);
        self::assertEquals($method->getName(), 'getMethod');
    }


    public function testGetConstructor() {
        $method = $this->class->getConstructor();
        self::assertType('ezcReflectionMethod', $method);
        self::assertEquals($method->getName(), '__construct');
    }


    public function testGetMethods() {
        $class = new ezcReflectionClass('TestWebservice');
        $methods = $class->getMethods();
        self::assertEquals(0, count($methods));

        $class = new ezcReflectionClass('TestMethods');
        $methods = $class->getMethods();

        $expectedMethods = array('__construct', 'm1', 'm2', 'm3', 'm4');
        foreach ($methods as $method) {
            self::assertType('ezcReflectionMethod', $method);
            self::assertContains($method->getName(), $expectedMethods);

            ReflectionTestHelper::deleteFromArray($method->getName(), $expectedMethods);
        }
        self::assertEquals(0, count($expectedMethods));
    }

    public function testGetParentClass() {
        $parent = $this->class->getParentClass();

        self::assertType('ReflectionClass', $parent);
        self::assertEquals($parent->getName(), 'ReflectionClass');

        $parentParent = $parent->getParentClass();
        self::assertNull($parentParent);
    }

    public function testGetProperty() {
        $prop = $this->class->getProperty('docParser');

        self::assertType('ezcReflectionProperty', $prop);
        self::assertEquals('docParser', $prop->getName());

        try {
            $prop = $this->class->getProperty('none-existing-property');
        }
        catch (ReflectionException $expected) {
            return;
        }
        $this->fail('ReflectionException has not been raised on none existing property.');
    }

    public function testGetProperties() {
        $class = new ezcReflectionClass('TestWebservice');
        $properties = $class->getProperties();

        $expected = array('prop1', 'prop2', 'prop3');

        foreach ($properties as $prop) {
            self::assertType('ezcReflectionProperty', $prop);
            self::assertContains($prop->getName(), $expected);

            ReflectionTestHelper::deleteFromArray($prop->getName(), $expected);
        }
        self::assertEquals(0, count($expected));
    }

    public function testIsWebService() {
        self::assertFalse($this->class->isWebService());

        $class = new ezcReflectionClass('TestWebservice');
        self::assertTrue($class->isWebService());
    }

    public function testGetShortDescription() {
        $class = new ezcReflectionClass('TestWebservice');
        $desc = $class->getShortDescription();

        self::assertEquals('This is the short description', $desc);
    }

    public function testGetLongDescription() {
        $class = new ezcReflectionClass('TestWebservice');
        $desc = $class->getLongDescription();

        $expected = "This is the long description with may be additional infos and much more lines\nof text.\n\nEmpty lines are valide to.\n\nfoo bar";
        self::assertEquals($expected, $desc);
    }

    public function testIsTagged() {
        self::assertFalse($this->class->isTagged('foobar'));

        $class = new ezcReflectionClass('TestWebservice');
        self::assertTrue($class->isTagged('foobar'));
    }



    public function testGetTags() {
        $tags = $this->class->getTags();

        $expectedTags = array('package', 'version', 'author', 'author');
        ReflectionTestHelper::expectedTags($expectedTags, $tags, $this);

        $expectedTags = array('webservice', 'foobar');
        $class = new ezcReflectionClass('TestWebservice');
        $tags = $class->getTags();
        ReflectionTestHelper::expectedTags($expectedTags, $tags, $this);
    }

    public function testGetExtension() {
        $class = new ezcReflectionClass('ReflectionClass');
        $ext = $class->getExtension();
        self::assertType('ezcReflectionExtension', $ext);
        self::assertEquals($ext->getName(), 'Reflection');

        $class = new ezcReflectionClass('TestWebservice');
        $ext = $class->getExtension();
        self::assertNull($ext);
    }

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionClassTest" );
    }
}
?>
