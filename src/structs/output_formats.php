<?php
/**
 * File containing the ezcConsoleOutputFormats class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class to store the collection for formating classes.
 *
 * This class stores objects of {@link ezcConsoleOutputFormat}, which
 * represents a format option set for {@link ezcConsoleOutput}.
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleOutputFormats
{
    /**
     * Array of ezcConsoleOutputFormat.
     * 
     * @var array(ezcConsoleOutputFormat)
     */
    protected $formats = array();

    /**
     * Create a new ezcConsoleOutputFormats object.
     *
     * Creates a new, empty object of this class. It also adds a default
     * format.
     */
    public function __construct()
    {
        $this->formats['default'] = new ezcConsoleOutputFormat();
    }
    
    /**
     * Read access to the formats.
     *
     * Formats are accessed directly like properties of this object. If a
     * format does not exist, it is created on the fly (using default values),
     * 
     * @param string $formatName
     * @return ezcConsoleOutputFormat The format.
     */
    public function __get( $formatName )
    {
        if ( !isset( $this->formats[$formatName] ) )
        {
            $this->formats[$formatName] = new ezcConsoleOutputFormat();
        }
        return $this->formats[$formatName];
    }

    /**
     * Write access to the formats.
     *
     * Formats are accessed directly like properties of this object. If a
     * format does not exist, it is created on the fly (using default values),
     * 
     * @param string $formatName
     * @param ezcConsoleOutputFormat $val The format defintion.
     */
    public function __set( $formatName, ezcConsoleOutputFormat $val )
    {
        $this->formats[$formatName] = $val;
    }
 
    /**
     * Property isset access.
     * 
     * @param string $formatName Name of the property.
     * @return bool True is the property is set, otherwise false.
     */
    public function __isset( $formatName )
    {
        return isset( $this->formats[$formatName] );
    }

}

?>
