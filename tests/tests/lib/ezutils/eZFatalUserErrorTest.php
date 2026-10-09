<?php
/**
 * Tests of eZFatalUserError::raise(), which replaces trigger_error( $message, E_USER_ERROR ): PHP 8.4 deprecates
 * passing E_USER_ERROR to trigger_error().
 *
 *  FU-01 - A handler that takes the error gets E_USER_ERROR, the message and the place of the call; the script goes on
 *  FU-02 - A handler that returns false, or no handler, stops the script with an uncaught eZFatalUserError
 *  FU-03 - The handler is not active while it runs, and is back afterwards
 *  FU-04 - eZDebug::writeError() while errors go to PHP (HANDLE_TO_PHP) raises no deprecation
 *  FU-05 - eZExpiryHandler::restore() that cannot read the expiry file raises no deprecation
 *  FU-06 - Without a handler, both stop the script in a child process, exit code 255, with no deprecation
 *  FU-07 - No code outside the tests passes E_USER_ERROR to trigger_error()
 *
 * No kernel bootstrap, no database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezdebug
 */

class eZFatalUserErrorTest extends PHPUnit\Framework\TestCase
{
    private $errors = array();

    private $savedGlobals = array();

    const GLOBAL_KEYS = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugAlwaysLog' );

    protected function setUp(): void
    {
        $this->errors = array();
        foreach ( self::GLOBAL_KEYS as $key )
            $this->savedGlobals[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
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
    }

    /** Installs a handler that records each error and answers $answer */
    private function recordErrors( $answer )
    {
        $errors = &$this->errors;
        set_error_handler( function ( $errno, $errstr, $errfile = '', $errline = 0 ) use ( &$errors, $answer )
        {
            $errors[] = array( $errno, $errstr, $errfile, $errline );
            return $answer;
        } );
    }

    private static function currentHandler()
    {
        $handler = set_error_handler( 'var_dump' );
        restore_error_handler();
        return $handler;
    }

    /** An eZDebug of its own that writes nothing and hands errors to PHP when $toPhp */
    private function quietDebug( $toPhp )
    {
        $debug = new eZDebug();
        foreach ( $debug->AlwaysLog as $level => $always )
            $debug->AlwaysLog[$level] = false;
        $debug->HandleType = $toPhp ? eZDebug::HANDLE_TO_PHP : eZDebug::HANDLE_NONE;
        unset( $GLOBALS['eZDebugAlwaysLog'] );
        $GLOBALS['eZDebugGlobalInstance'] = $debug;
        $GLOBALS['eZDebugEnabled'] = $toPhp;
        return $debug;
    }

    /** FU-01 */
    #[\PHPUnit\Framework\Attributes\DataProvider( 'handledAnswers' )]
    public function testAHandlerThatTakesTheErrorLetsTheScriptGoOn( $answer )
    {
        $this->recordErrors( $answer );
        try
        {
            $line = __LINE__ + 1;
            $result = eZFatalUserError::raise( 'x1 handled' );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertNull( $result );
        $this->assertSame( array( array( E_USER_ERROR, 'x1 handled', __FILE__, $line ) ), $this->errors );
    }

    public static function handledAnswers()
    {
        // PHP goes on for every answer but false; eZDebug's own handler answers null
        return array( 'true' => array( true ), 'null' => array( null ), 'zero' => array( 0 ), 'string' => array( '' ) );
    }

    /** FU-02 */
    public function testAHandlerThatReturnsFalseStopsTheScript()
    {
        $this->recordErrors( false );
        try
        {
            $line = __LINE__ + 1;
            eZFatalUserError::raise( 'x1 not handled' );
            $this->fail( 'eZFatalUserError::raise() went on although the handler returned false' );
        }
        catch ( eZFatalUserError $e )
        {
            $this->assertSame( 'x1 not handled', $e->getMessage() );
            $this->assertSame( __FILE__, $e->getFile() );
            $this->assertSame( $line, $e->getLine() );
            // an Error, so that code catching Exception does not take what used to be fatal
            $this->assertNotInstanceOf( 'Exception', $e );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertCount( 1, $this->errors );
        $this->assertSame( E_USER_ERROR, $this->errors[0][0] );
    }

    /** FU-02 */
    public function testWithoutAHandlerTheScriptStops()
    {
        set_error_handler( null );
        try
        {
            eZFatalUserError::raise( 'x1 no handler' );
            $this->fail( 'eZFatalUserError::raise() went on without a handler' );
        }
        catch ( eZFatalUserError $e )
        {
            $this->assertSame( 'x1 no handler', $e->getMessage() );
        }
        finally
        {
            restore_error_handler();
        }
    }

    /** FU-03 */
    public function testTheHandlerIsNotActiveWhileItRuns()
    {
        $inside = 'not called';
        $handler = function () use ( &$inside )
        {
            $inside = eZFatalUserErrorTest::currentHandlerForTest();
            return true;
        };
        set_error_handler( $handler );
        try
        {
            eZFatalUserError::raise( 'x1 nested' );
            $this->assertNull( $inside );
            $this->assertSame( $handler, self::currentHandler() );
        }
        finally
        {
            restore_error_handler();
        }
    }

    public static function currentHandlerForTest()
    {
        return self::currentHandler();
    }

    /** FU-04 */
    public function testDebugErrorsHandedToPhpRaiseNoDeprecation()
    {
        $this->quietDebug( true );
        $this->recordErrors( true );
        try
        {
            eZDebug::writeError( 'x1 debug error', 'x1label' );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertCount( 1, $this->errors, var_export( $this->errors, true ) );
        $this->assertSame( E_USER_ERROR, $this->errors[0][0] );
        $this->assertSame( 'x1label: x1 debug error', $this->errors[0][1] );
        $this->assertStringEndsWith( 'lib/ezutils/classes/ezdebug.php', $this->errors[0][2] );
    }

    /** FU-05 */
    public function testAnUnreadableExpiryFileRaisesNoDeprecation()
    {
        $this->quietDebug( false );
        $handler = ( new ReflectionClass( 'eZExpiryHandler' ) )->newInstanceWithoutConstructor();
        $handler->CacheFile = new class
        {
            public function processFile( $callback )
            {
                return false;
            }
        };
        $this->recordErrors( true );
        try
        {
            $handler->restore();
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertCount( 1, $this->errors, var_export( $this->errors, true ) );
        $this->assertSame( E_USER_ERROR, $this->errors[0][0] );
        $this->assertSame( 'Fatal error - could not restore expiry.php file.', $this->errors[0][1] );
        $this->assertStringEndsWith( 'lib/ezutils/classes/ezexpiryhandler.php', $this->errors[0][2] );
    }

    /** FU-06 */
    #[\PHPUnit\Framework\Attributes\DataProvider( 'childCases' )]
    public function testWithoutAHandlerTheScriptStopsInAChildProcess( $case, $message )
    {
        $command = array( PHP_BINARY, '-d', 'error_reporting=-1', '-d', 'display_errors=stderr', '-d', 'log_errors=0',
                          __DIR__ . '/fixtures/fatal_user_error_child.php', $case );
        $process = proc_open( $command, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
        $this->assertIsResource( $process );
        $stdout = stream_get_contents( $pipes[1] );
        $stderr = stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $status = proc_close( $process );

        $this->assertSame( 255, $status, "stdout: $stdout\nstderr: $stderr" );
        $this->assertStringNotContainsString( 'AFTER', $stdout );
        $this->assertStringContainsString( 'Fatal error', $stderr );
        $this->assertStringContainsString( $message, $stderr );
        $this->assertStringNotContainsString( 'Deprecated', $stdout . $stderr );
    }

    public static function childCases()
    {
        return array(
            'eZDebug::writeError() handed to PHP' => array( 'debug', 'x1label: x1 the fatal message' ),
            'eZExpiryHandler::restore()' => array( 'expiry', 'Fatal error - could not restore expiry.php file.' ),
        );
    }

    /** FU-07 */
    public function testNoCodeOutsideTheTestsPassesEUserErrorToTriggerError()
    {
        $root = dirname( __DIR__, 4 );
        $files = glob( $root . '/*.php' );
        foreach ( array( 'kernel', 'lib', 'bin', 'cronjobs', 'update' ) as $dir )
        {
            if ( !is_dir( "$root/$dir" ) )
                continue;
            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( "$root/$dir", FilesystemIterator::SKIP_DOTS ) );
            foreach ( $iterator as $file )
            {
                if ( $file->isFile() && substr( $file->getFilename(), -4 ) === '.php' )
                    $files[] = $file->getPathname();
            }
        }
        $found = array();
        foreach ( $files as $file )
        {
            $source = file_get_contents( $file );
            if ( strpos( $source, 'E_USER_ERROR' ) === false )
                continue;
            if ( preg_match_all( '/trigger_error\s*\([^;]*?,\s*E_USER_ERROR\s*\)/s', $source, $matches, PREG_OFFSET_CAPTURE ) )
            {
                foreach ( $matches[0] as $match )
                    $found[] = substr( $file, strlen( $root ) + 1 ) . ':' . ( substr_count( substr( $source, 0, $match[1] ), "\n" ) + 1 );
            }
        }
        $this->assertSame( array(), $found, 'Use eZFatalUserError::raise() instead' );
    }
}
