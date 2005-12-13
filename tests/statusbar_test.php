<?php
/**
 * ezcConsoleToolsOutputTest 
 * 
 * @package ConsoleTools
 * @subpackage Tests
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Test suite for ezcConsoleStatusbar class.
 * 
 * @package ConsoleTools
 * @subpackage Tests
 */
class ezcConsoleToolsStatusbarTest extends ezcTestCase
{

    private $stati = array( 
        true,
        false,
        true,
        true,
        false,
        true,
        true,
        true,
        false,
        false,
        true,
        true,
        false,
        true,
        true,
        false,
        true,
        false,
        true,
        true,
        false,
        false,
        false,
        true,
        false,
    );

	public static function suite()
	{
		return new ezcTestSuite( "ezcConsoleToolsStatusbarTest" );
	}

    /**
     * setUp 
     * 
     * @access public
     */
    public function setUp()
    {
    }

    /**
     * tearDown 
     * 
     * @access public
     */
    public function tearDown()
    {
    }

    public function testStatusbar1()
    {
        $out = new ezcConsoleOutput();
        $status = new ezcConsoleStatusbar( $out );
        ob_start();
        foreach ( $this->stati as $statusVal )
        {
            $status->add($statusVal);
        }
        $res = ob_get_contents();
        ob_end_clean();
        $this->assertEquals(
            file_get_contents( dirname( __FILE__ ) . '/dat/testStatusbar1.dat' ),
            $res,
            "Formated statusbar not generated correctly."
        );
        // To prepare test files use this:
        // file_put_contents( dirname( __FILE__ ) . '/dat/testStatusbar1.dat', $res );
    }
    
    public function testStatusbar2()
    {
        $out = new ezcConsoleOutput();
        $out->options->useFormats = false;
        $status = new ezcConsoleStatusbar( $out );
        ob_start();
        foreach ( $this->stati as $statusVal )
        {
            $status->add($statusVal);
        }
        $res = ob_get_contents();
        ob_end_clean();
        $this->assertEquals(
            file_get_contents( dirname( __FILE__ ) . '/dat/testStatusbar2.dat' ),
            $res,
            "Unformated statusbar not generated correctly."
        );
        // To prepare test files use this:
        // file_put_contents( dirname( __FILE__ ) . '/dat/testStatusbar2.dat', $res );
    }
}
?>
