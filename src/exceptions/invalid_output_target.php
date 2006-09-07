<?php
/**
 * File containing the ezcConsoleInvalidOutputTargetException.
 *
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Thrown if a given target {@see ezcConsoleOutputFormat} could not be opened.
 *
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleInvalidOutputTargetException extends ezcConsoleException
{
    
    public function __construct( $target )
    {
        parent::__construct( "The target <{$target}> could not be opened for writing." );
    }

}
?>

