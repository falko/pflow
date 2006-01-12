<?php
/**
 * File containing the ezcConsoleOptionDependencyViolationException.
 * 
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * A dependency rule for a parameter was violated.
 * This exception can be caught using {@link ezcConsoleOptionException}.
 *
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleOptionDependencyViolationException extends ezcConsoleOptionException
{
    function __construct( ezcConsoleOption $dependingOption, ezcConsoleOption $dependantOption, $valueRange = null )
    {
        $message  = "The option with long name <{$dependingOption->long}> ";
        $message .= "depends on the usage of the option with long name <{$dependantOption->long}> ";
        if ( $valueRange !== null )
        {
            $message .= "being the value range <{$valueRange}> ";
        }
        $message .= "but this one was not submitted.";
        parent::__construct( $message );
    }
}
?>
