<?php
/**
 * File containing the ezcConsoleProgressbarOptions class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Struct class to store the options of the ezcConsoleOutput class.
 *
 * This class stores the options for the {@link ezcConsoleOutput} class.
 *
 * The ezcConsoleProgressbarOptions class has the following properties:
 * - <b>barChar</b> <i>string</i>, contains the character to fill the progress bar with when it grows.
 * - <b>emptyChar</b> <i>string</i>, contains the character to fill the empty space in the progress bar with.
 * - <b>progressChar</b> <i>string</i>, contains the character for the right most position of the progress bar's progress.
 * - <b>formatString</b> <i>string</i>, contains the format for the complete progress bar.
 * - <b>width</b> <i>string</i>, contains the width of the progress bar in characters.
 * - <b>fractionFormat<b> <i>string</i>, contains an sprintf() format for the fraction display.
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleProgressbarOptions
{
    /**
     * Property array defining all this class' properties
     *
     * @var array
     */
    protected $properties = array( 
        'barChar' => '+',
        'emptyChar' => '-',
        'progressChar' => '>',
        'formatString' => '%act% / %max% [%bar%] %fraction%%',
        'width' => 78,
        'fractionFormat' => '%01.2f',
    );

    /**
     * Create a new ezcConsoleProgressbarOptions struct. 
     *
     * Create a new ezcConsoleProgressbarOptions struct for use with {@link
     * ezcConsoleOutput}. 
     * 
     * @param int $verbosityLevel Verbosity of the output to show.
     * @param int $autobreak      Auto wrap lines after num chars (0 = unlimited)
     * @param bool $useFormats    Whether to enable formated output
     */
    public function __construct( 
        $barChar = '+',
        $emptyChar = '-', 
        $progressChar = '>',
        $formatString = '%act% / %max% [%bar%] %fraction%%',
        $width = 100,
        $fractionFormat = '%01.2f'
    )
    {
        $this->__set( 'barChar', $barChar );
        $this->__set( 'emptyChar', $emptyChar );
        $this->__set( 'progressChar', $progressChar );
        $this->__set( 'formatString', $formatString );
        $this->__set( 'width', $width );
        $this->__set( 'fractionFormat', $fractionFormat );
    }

    /**
     * Property read access.
     * 
     * @throws ezcBasePropertyNotFoundException if the the desired property is
     *         not found.
     *
     * @param string $propertyName Name of the property.
     * @return mixed Value of the property or null.
     */
    public function __get( $propertyName )
    {
        if ( isset( $this->$propertyName ) )
        {
            return $this->properties[$propertyName];
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Property write access.
     * 
     * @throws ezcBasePropertyNotFoundException
     *         If a desired property could not be found.
     * @throws ezcBaseConfigException
     *         If a desired property value is out of range
     *         {@link ezcBaseConfigException::VALUE_OUT_OF_RANGE}.
     *
     * @param string $propertyName Name of the property.
     * @param mixed $val  The value for the property.
     */
    public function __set( $propertyName, $val )
    {
        switch ( $propertyName )
        {
            case 'barChar':
            case 'emptyChar':
            case 'progressChar':
            case 'formatString':
            case 'fractionFormat':
                if ( strlen( $val ) < 1 )
                {
                    throw new ezcBaseConfigException( $propertyName, ezcBaseConfigException::VALUE_OUT_OF_RANGE, $val );
                }
                break;
            case 'width':
                if ( !is_int( $val ) || $val < 5 )
                {
                    throw new ezcBaseConfigException( $propertyName, ezcBaseConfigException::VALUE_OUT_OF_RANGE, $val );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $val;
    }
 
    /**
     * Property isset access.
     * 
     * @param string $propertyName Name of the property.
     * @return bool True if the property is set, false otherwise.
     */
    public function __isset( $propertyName )
    {
        return isset( $this->properties[$propertyName] );
    }

}

?>
