<?php
/**
 * The message rows of the Exp Debug bar (expDebugBarReport::messageRows()) with a message whose text is not a
 * string: null and a number are shown as their string value and raise nothing, also on the xdebug branch (which
 * compares the text with eZDebug::XDEBUG_SIGNATURE; the test passes "xdebug loaded" instead of needing it), and
 * a message carrying the xdebug signature is shown as xdebug's own markup.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 4065ed16b3), Copyright (C) Mugo Web, GPL v2
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 */

class expDebugBarReportMessageTextTest extends PHPUnit\Framework\TestCase
{
    private function rows( array $strings, $xdebug )
    {
        $debug = new eZDebug();
        foreach ( $strings as $i => $string )
            $debug->DebugStrings[] = array( 'Level' => eZDebug::LEVEL_NOTICE, 'IP' => '', 'Time' => 0, 'Label' => 'l' . $i,
                                            'String' => $string, 'BackgroundClass' => '' );
        $method = new ReflectionMethod( 'expDebugBarReport', 'messageRows' );
        $raised = array();
        set_error_handler( function ( $errno, $errstr, $errfile, $errline ) use ( &$raised )
        {
            $raised[] = "$errstr in $errfile on line $errline";
            return true;
        } );
        try
        {
            $result = $method->invoke( null, $debug, array( eZDebug::LEVEL_NOTICE ), $xdebug );
        }
        finally
        {
            restore_error_handler();
        }
        return array( $result, $raised );
    }

    public static function xdebugStates()
    {
        return array( 'xdebug loaded' => array( true ), 'without xdebug' => array( false ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('xdebugStates')]
    public function testNullAndNumbersRaiseNothing( $xdebug )
    {
        list( $result, $raised ) = $this->rows( array( null, 42, 'plain <b>' ), $xdebug );
        $this->assertSame( array(), $raised );
        $this->assertCount( 3, $result['rows'] );
        $this->assertStringContainsString( '<pre></pre>', $result['rows'][0] );
        $this->assertStringContainsString( '<pre>42</pre>', $result['rows'][1] );
        $this->assertStringContainsString( '<pre>plain &lt;b&gt;</pre>', $result['rows'][2] );
        $this->assertSame( 3, $result['counts']['notice'] );
    }

    public function testTheXdebugSignatureKeepsXdebugsMarkup()
    {
        list( $result, $raised ) = $this->rows( array( eZDebug::XDEBUG_SIGNATURE . '<table class="xdebug">x</table>' ), true );
        $this->assertSame( array(), $raised );
        $this->assertStringContainsString( '<pre><table class="xdebug">x</table></pre>', $result['rows'][0] );

        list( $result ) = $this->rows( array( eZDebug::XDEBUG_SIGNATURE . '<b>' ), false );
        $this->assertStringContainsString( '&lt;b&gt;', $result['rows'][0], 'escaped without xdebug' );
    }
}
