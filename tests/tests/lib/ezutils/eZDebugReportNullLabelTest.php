<?php
/**
 * The HTML debug report of a message whose label or text is null, the case LoveStack (mugoweb/ezpublish-legacy)
 * commit 4065ed16b3 guarded with a (string) cast before htmlspecialchars() in eZDebug::printReportInternal():
 * eZDebug::writeNotice( null ) and eZDebug::writeWarning( 'text', null ) store null, and the report must show
 * them without a PHP 8.1 null deprecation.
 *
 * The debug instance is one of its own, with its log files in a private directory under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 * @group php8
 */

class eZDebugReportNullLabelTest extends PHPUnit\Framework\TestCase
{
    const GLOBAL_KEYS = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly', 'eZDebugAlwaysLog', 'eZDebugLogFileEnabled' );

    private $savedGlobals = array();
    private $dir;
    private $debug;
    private $messages = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        foreach ( self::GLOBAL_KEYS as $key )
            $this->savedGlobals[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $this->dir = 'var/tmp/phpunit-debug-null-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 ) . '/';
        mkdir( $this->dir, 0777, true );
        unset( $GLOBALS['eZDebugAlwaysLog'], $GLOBALS['eZDebugLogFileEnabled'] );
        $this->debug = new eZDebug();
        foreach ( $this->debug->LogFiles as $level => $file )
            $this->debug->LogFiles[$level] = array( $this->dir, $file[1] );
        $GLOBALS['eZDebugGlobalInstance'] = $this->debug;
        $GLOBALS['eZDebugEnabled'] = true;
        $GLOBALS['eZDebugLogOnly'] = false;
    }

    protected function tearDown(): void
    {
        foreach ( $this->savedGlobals as $key => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $saved[0];
        }
        foreach ( glob( $this->dir . '*' ) as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    private function report()
    {
        $this->messages = array();
        set_error_handler( function ( $errno, $errstr, $errfile = null, $errline = null ) {
            $this->messages[] = $errstr . ( $errfile !== null ? " ($errfile:$errline)" : "" );
            return true;
        } );
        ob_start();
        try
        {
            $report = $this->debug->printReportInternal( true, true );
        }
        finally
        {
            $printed = ob_get_clean();
            restore_error_handler();
        }
        return (string)$report . $printed;
    }

    public function testNullLabelAndNullTextAreReportedWithoutDeprecation()
    {
        eZDebug::writeNotice( null, 'label-of-a-null-notice' );
        eZDebug::writeWarning( 'a <warning> without a label', null );
        $this->assertSame( null, $this->debug->DebugStrings[0]['String'] );
        $this->assertSame( null, $this->debug->DebugStrings[1]['Label'] );

        $report = $this->report();
        $this->assertSame( array(), $this->messages );
        $this->assertStringContainsString( 'label-of-a-null-notice', $report );
        $this->assertStringContainsString( 'a &lt;warning&gt; without a label', $report );
    }

    public function testTheReportDoesNotCastOnlyInTheDebugBar()
    {
        // The classic report in eZDebug::printReportInternal() is used when the debug bar file is not there;
        // its two htmlspecialchars() calls take a string as well.
        $source = file_get_contents( 'lib/ezutils/classes/ezdebug.php' );
        $this->assertStringContainsString( "htmlspecialchars( (string)\$label,", $source );
        $this->assertStringContainsString( "htmlspecialchars( (string)\$debug['String'],", $source );
        $this->assertStringNotContainsString( "htmlspecialchars( \$label,", $source );
        $this->assertStringNotContainsString( "htmlspecialchars( \$debug['String'],", $source );
    }
}
