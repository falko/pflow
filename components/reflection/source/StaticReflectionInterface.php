<?php
/**
 * I provide completely working code with this framework, which will not be
 * developed any further, because there are already existing packages, which try
 * to provide similar functionallities.
 */

namespace de\buzz2ee\reflection;

/**
 * Static interface implementation.
 *
 * @author  Manuel Pichler <mapi@pdepend.org>
 * @license Copyright by Manuel Pichler
 * @version $Revision$
 */
class StaticReflectionInterface extends \ReflectionClass
{
    const TYPE = __CLASS__;

    /**
     * @var string
     */
    private $_name = null;

    /**
     * @var string
     */
    private $_docComment = false;

    /**
     * The file pathname where this interface is defined.
     *
     * @var string
     */
    private $_fileName = null;

    /**
     * The start line number where the interface declaration starts.
     *
     * @var integer
     */
    private $_startLine = -1;

    /**
     * @var array(\ReflectionMethod)
     */
    private $_methods = null;

    /**
     * @var array(\ReflectionClass)
     */
    private $_interfaces = null;

    /**
     * @param string $name
     * @param string $docComment
     */
    public function __construct( $name, $docComment = '' )
    {
        $this->setName( $name );
        $this->_docComment = $docComment;
    }

    /**
     * Returns the file pathname where this interface was defined.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->_fileName;
    }

    /**
     * Sets the file pathname where this interface was defined. Note that this
     * method is only for internal use.
     *
     * @param string $fileName File pathname where the interface was defined.
     *
     * @return void
     * @access private
     */
    public function initFileName( $fileName )
    {
        if ( $this->_fileName === null )
        {
            $this->_fileName = $fileName;
        }
        else
        {
            throw new \LogicException( 'Property fileName already set.' );
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Sets the qualified name of the reflected interface.
     *
     * @param string $name The full qualified interface name.
     *
     * @return void
     */
    protected function setName( $name )
    {
        $this->_name = ltrim( $name, '\\' );
    }

    /**
     * Returns the short name of the class, the part without the namespace.
     *
     * @return string
     */
    public function getShortName()
    {
        if ( ( $pos = strrpos( $this->_name, '\\' ) ) === false )
        {
            return $this->_name;
        }
        return substr( $this->_name, $pos + 1 );
    }

    /**
     * Returns the namespace name of the reflected interface.
     *
     * @return string
     */
    public function getNamespaceName()
    {
        if ( ( $pos = strrpos( $this->_name, '\\' ) ) === false )
        {
            return '';
        }
        return substr( $this->_name, 0, $pos );
    }

    /**
     * Checks if this class is defined in a namespace. 
     *
     * @return boolean
     */
    public function inNamespace()
    {
        return ( $this->getNamespaceName() !== '' );
    }

    /**
     * @return string
     */
    public function getDocComment()
    {
        return $this->_docComment;
    }

    /**
     * Returns the class/interface modifiers
     *
     * @return integer
     */
    public function getModifiers()
    {
        return 0;
    }

    /**
     * Returns <b>true</b> when the class is declared abstract or is an interface.
     *
     * @return boolean
     */
    public function isAbstract()
    {
        return true;
    }

    /**
     * Returns <b>true</b> when the class is declared as final.
     *
     * @return boolean
     */
    public function isFinal()
    {
        return false;
    }

    /**
     * @return boolean
     */
    public function isInterface()
    {
        return true;
    }

    /**
     * Checks whether the class is internal, as opposed to user-defined.
     *
     * @return boolean
     */
    public function isInternal()
    {
        return false;
    }

    /**
     * Checks whether the class is user-defined, as opposed to internal. 
     *
     * @return boolean
     */
    public function isUserDefined()
    {
        return true;
    }

    /**
     * Checks if a class is an instance of an object.
     *
     * @param object $object The object to check.
     *
     * @return boolean
     */
    public function isInstance( $object )
    {
        return ( $object instanceof $this->_name );
    }

    /**
     * Checks if the class is instantiable.
     *
     * @return boolean
     */
    public function isInstantiable()
    {
        return false;
    }

    public function isIterateable()
    {
        
    }

    public function isSubclassOf( $class )
    {
        
    }

    public function hasConstant( $name )
    {
        
    }

    public function getConstant( $name )
    {
        
    }

    public function getConstants()
    {
        
    }

    public function implementsInterface( $interface )
    {
        
    }

    /**
     * @return array(\ReflectionClass)
     */
    public function getInterfaces()
    {
        return (array) $this->_interfaces;
    }

    public function getInterfaceNames()
    {
        $names = array();
        foreach ( $this->getInterfaces() as $interface )
        {
            $names[] = $interface->getName();
        }
        return $names;
    }

    /**
     * @param array(\ReflectionClass) $interfaces
     *
     * @return void
     * @access private
     */
    public function initInterfaces( array $interfaces )
    {
        if ( $this->_interfaces === null )
        {
            $this->_interfaces = $interfaces;
        }
        else
        {
            throw new \LogicException( 'Interfaces already set' );
        }
    }

    /**
     * @return \ReflectionClass
     */
    public function getParentClass()
    {
        return null;
    }

    public function getConstructor()
    {
        
    }

    /**
     * Checks whether a specific method is defined in a class.
     *
     * @param string $name Name of the method being checked for.
     *
     * @return boolean
     */
    public function hasMethod( $name )
    {
        return isset( $this->_methods[strtolower( $name )] );
    }

    /**
     * @param string $name
     *
     * \ReflectionMethod
     */
    public function getMethod( $name )
    {
        if ( isset( $this->_methods[strtolower( $name )] ) )
        {
            return $this->_methods[strtolower( $name )];
        }
        throw new \RuntimeException( 'No method ' . $this->_name . '::' . $name . '() found.' );
    }

    /**
     * @return array(\ReflectionMethod)
     */
    public function getMethods( $filter = -1 )
    {
        return (array) $this->_methods;
    }

    /**
     * @param array(\ReflectionMethod) $methods
     *
     * @return void
     * @access private
     */
    public function initMethods( array $methods )
    {
        if ( $this->_methods === null )
        {
            $this->_methods = array();
            foreach ( $methods as $method )
            {
                $this->_methods[strtolower( $method->getName() )] = $method;
            }
        }
        else
        {
            throw new \LogicException( 'Methods already set' );
        }
    }

    /**
     * Checks whether the specified property is defined.
     *
     * @param string $name Name of the property being checked for.
     * 
     * @return boolean
     */
    public function hasProperty( $name )
    {
        return false;
    }

    /**
     * @param string $name
     *
     * @return \ReflectionProperty
     */
    public function getProperty( $name )
    {
        throw new \RuntimeException( sprintf( 'Property %s does not exist', $name ) );
    }

    /**
     * @return array(\ReflectionProperty)
     */
    public function getProperties( $filter = -1 )
    {
        return array();
    }

    /**
     * Gets default properties from a class.
     *
     * @return array(mixed)
     */
    public function getDefaultProperties()
    {
        return array();
    }

    /**
     * Gets the static property values.
     *
     * @param string $name    The property name.
     * @param mixed  $default Optional default value.
     *
     * @return mixed
     */
    public function getStaticPropertyValue( $name, $default = null )
    {

    }

    /**
     * Get the static properties.
     *
     * @return array(string=>mixed)
     */
    public function getStaticProperties()
    {
        return array();
    }

    /**
     * Sets static property value.
     *
     * @param string $name  The property name.
     * @param mixed  $value The new property value.
     *
     * @return void
     */
    public function setStaticPropertyValue( $name, $value )
    {
        throw new \ReflectionException( 'Method setStaticPropertyValue() is not supported' );
    }

    /**
     * Get the starting line number. 
     *
     * @return integer
     */
    public function getStartLine()
    {
        return $this->_startLine;
    }

    /**
     * Initializes the start line number. Note that this method is only used
     * internally by this component.
     *
     * @param integer $startLine Line number where the interface declaration starts.
     *
     * @return void
     * @access private
     */
    public function initStartLine( $startLine )
    {
        if ( $this->_startLine === -1 )
        {
            $this->_startLine = $startLine;
        }
        else
        {
            throw new \LogicException( 'Property startLine already set.' );
        }
    }

    public function getEndLine()
    {
        
    }

    /**
     * Gets an extensions <b>\ReflectionExtension</b> object or <b>null</b>.
     *
     * @return \ReflectionExtension
     */
    public function getExtension()
    {
        return null;
    }

    /**
     * Returns the name of the owning extension or <b>false</b>.
     *
     * @return string|boolean
     */
    public function getExtensionName()
    {
        return false;
    }

    /**
     * Creates an instance of the context class.
     *
     * @param mixed $args Accepts a variable number of arguments which are
     *                    passed to the function much like call_user_func().
     *
     * @return object
     */
    public function newInstance( $args )
    {
        throw new \ReflectionException( 'Method newInstance() is not supported' );
    }

    /**
     * Creates an instance of the context class.
     *
     * @param array(mixed) $args Arguments which are passed to the constructor
     *                           much like call_user_func_array().
     *
     * @return object
     */
    public function newInstanceArgs( array $args = array() )
    {
        throw new \ReflectionException( 'Method newInstanceArgs() is not supported' );
    }

    public function __toString()
    {
        return '';
    }
}