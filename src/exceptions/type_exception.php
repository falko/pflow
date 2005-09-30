<?php
/**
 * File containing the ezcTypeException class.
 *
 * @package Base
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */
/**
 * ezcTypeException is thrown whenever the type of the given instance is not 
 * as expected.
 *
 * @package Base
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 * @version //autogen//
 */
class ezcBaseTypeException extends Exception
{
    /**
     * Constructs a new ezcTypeException on the 
     */
    function __construct( $expectedType, $gotType )
    {
        parent::__construct( "Expected type '$expectedType' but got type '$gotType'", 0 );
    }
}
?>
