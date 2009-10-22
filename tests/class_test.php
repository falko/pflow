<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogen//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ezcReflectionClassTest extends ezcTestCase
{
    /**#@+
     * @var ezcReflectionClass
     * @deprecated
     */
    protected $class;
    protected $classTestWebservice;
    protected $classReflectionFunction;
    /**#@-*/

	public function setUp() {
        // comparison objects for expected values
        $this->expected = array(
            'SomeClass'
                => new ReflectionClass( 'SomeClass' ),
            'TestWebservice'
                => new ReflectionClass( 'TestWebservice' ),
            'ReflectionFunction'
                => new ReflectionClass( 'ReflectionFunction' ),
        );
        $this->setUpFixtures();
        $this->actual = array(
            'SomeClass'
                => $this->class,
            'TestWebservice'
                => $this->classTestWebservice,
            'ReflectionFunction'
                => $this->classReflectionFunction,
        );
    }

    public function setUpFixtures() {
        $this->class                   = new ezcReflectionClass( 'SomeClass' );
        $this->classTestWebservice     = new ezcReflectionClass( 'TestWebservice' );
        $this->classReflectionFunction = new ezcReflectionClass( 'ReflectionFunction' );
    }

    public function tearDown() {
        unset($this->class);
    }

    public function testGetName() {
        self::assertEquals('SomeClass', $this->class->getName());
    }

    public function testGetMethod() {
        $method = $this->class->getMethod('helloWorld');
        self::assertType('ezcReflectionMethod', $method);
        self::assertEquals('helloWorld', $method->getName());
    }

    public function testGetConstructor() {
        $method = $this->class->getConstructor();
        self::assertType('ezcReflectionMethod', $method);
        self::assertEquals('__construct', $method->getName());
    }

	public function testGetInterfaces() {
        $ifaces = $this->class->getInterfaces();

        self::assertType('ezcReflectionClass', $ifaces[0]);
        self::assertEquals('IInterface', $ifaces[0]->getName());
        self::assertEquals(1, count($ifaces));
    }

    public function testGetMethods() {
        $methods = $this->classTestWebservice->getMethods();
        self::assertEquals(0, count($methods));

        $methods = $this->class->getMethods();

        $expectedMethods = array('__construct', 'helloWorld', 'doSomeMetaProgramming');
        self::assertEquals(count($expectedMethods), count($methods));
        foreach ($methods as $method) {
            self::assertType('ezcReflectionMethod', $method);
            self::assertContains($method->getName(), $expectedMethods);

            ReflectionTestHelper::deleteFromArray($method->getName(), $expectedMethods);
        }
        self::assertEquals(0, count($expectedMethods));
    }

    public function testGetParentClass() {
        $parent = $this->class->getParentClass();

        self::assertType('ezcReflectionClass', $parent);
        self::assertEquals('BaseClass', $parent->getName());
    }

    public function testGetParentClassFalse() {
        self::assertFalse( $this->classTestWebservice->getParentClass() );
    }

    public function testGetProperty() {
        $prop = $this->class->getProperty('fields');

        self::assertType('ezcReflectionProperty', $prop);
        self::assertEquals('fields', $prop->getName());

        try {
            $prop = $this->class->getProperty('none-existing-property');
        }
        catch (ReflectionException $expected) {
            return;
        }
        $this->fail('ReflectionException has not been raised on none existing property.');
    }

    public function testGetProperties() {
        $properties = $this->classTestWebservice->getProperties();

        $expected = array('prop1', 'prop2', 'prop3');

        foreach ($properties as $prop) {
            self::assertType('ezcReflectionProperty', $prop);
            self::assertContains($prop->getName(), $expected);

            ReflectionTestHelper::deleteFromArray($prop->getName(), $expected);
        }
        self::assertEquals(0, count($expected));
    }

    public function testGetShortDescription() {
        $desc = $this->classTestWebservice->getShortDescription();

        self::assertEquals('This is the short description', $desc);
    }

    public function testGetLongDescription() {
        $desc = $this->classTestWebservice->getLongDescription();

        $expected = "This is the long description with may be additional infos and much more lines\nof text.\n\nEmpty lines are valide to.\n\nfoo bar";
        self::assertEquals($expected, $desc);
    }

    public function testIsTagged() {
        self::assertFalse($this->class->isTagged('foobar'));

        self::assertTrue($this->classTestWebservice->isTagged('foobar'));
    }

    public function testGetTags() {
        $tags = $this->class->getTags();

        $expectedTags = array('licence', 'donotdocument', 'testclass', 'ignore');
        ReflectionTestHelper::expectedTags($expectedTags, $tags, $this);

        $expectedTags = array('webservice', 'foobar');
        $tags = $this->classTestWebservice->getTags();
        ReflectionTestHelper::expectedTags($expectedTags, $tags, $this);
    }

    public function testGetExtension() {
        $ext = $this->classReflectionFunction->getExtension();
        self::assertType('ezcReflectionExtension', $ext);
        self::assertEquals('Reflection', $ext->getName());

        $ext = $this->class->getExtension();
        self::assertNull($ext);
    }

    public function testGetExtensionName() {
        self::assertEquals( 'Reflection', $this->classReflectionFunction->getExtensionName() );
        self::assertEquals( '', $this->class->getExtensionName() );
    }

    public function testExport() {
        self::assertEquals( ReflectionClass::export('TestWebservice', true), ezcReflectionClass::export('TestWebservice', true) );
    }

    public function getWrapperMethods() {
        $wrapperMethods = array(
            array( '__toString', array() ),
            array( 'getName', array() ),
            array( 'isInternal', array() ),
            array( 'isUserDefined', array() ),
            array( 'getFileName', array() ),
            array( 'getStartLine', array() ),
            array( 'getEndLine', array() ),
            array( 'getDocComment', array() ),
            array( 'getExtension', array() ),
            array( 'getExtensionName', array() ),
            array( 'getConstants', array() ),
            array( 'getDefaultProperties', array() ),
            array( 'getInterfaceNames', array() ),
            array( 'getModifiers', array() ),
            array( 'getStaticProperties', array() ),
            array( 'isAbstract', array() ),
            array( 'isFinal', array() ),
            array( 'isInstantiable', array() ),
            array( 'isIterateable', array() ),
            array( 'isInterface', array() ),
            // FIXME: array( 'isDeprecated', array() ),
        );
        if ( version_compare( PHP_VERSION, '5.3.0' ) === 1 ) {
            $wrapperMethods530 = array(
                array( 'getNamespaceName', array() ),
                array( 'inNamespace', array() ),
                array( 'getShortName', array() ),
            );
        } else {
            $wrapperMethods530 = array();
        }
        return array_merge( $wrapperMethods, $wrapperMethods530 );
    }

    /**
     * @dataProvider getWrapperMethods
     */
    public function testWrapperMethods( $method, $arguments ) {
        foreach ( array_keys( $this->expected ) as $fixtureName ) {
            try {
                $actual = call_user_func_array(
                    array( $this->actual[ $fixtureName ], $method ), $arguments
                );
                $expected = call_user_func_array(
                    array( $this->expected[ $fixtureName ], $method ), $arguments
                );
                if ( $expected instanceOf Reflector ) {
                    self::assertEquals( (string) $expected, (string) $actual );
                } else {
                    self::assertEquals( $expected, $actual );
                }
            } catch ( ReflectionException $e ) {
                if ( !(
                    $this->$php_fixtureName instanceOf ReflectionMethod
                    and
                    $e->getMessage() == 'Method ' . $this->$php_fixtureName->getDeclaringClass()->getName() . '::' . $this->$php_fixtureName->getName() . ' does not have a prototype'
                ) ) {
                    self::fail( 'Unexpected ReflectionException: ' . $e->getMessage() );
                }
            }
        }
    }

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionClassTest" );
    }
}
?>
