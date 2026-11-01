<?php
/**
 * Fixes of defects that forks of eZ Publish Legacy (Opencontent, Mugo Web) found and that this tree still had,
 * each written anew here. DB-less: the URL alias depth limit is shown by a lookup that returns before it asks the
 * database.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expLegacyForkFixesTest extends PHPUnit\Framework\TestCase
{
    private $iniSaved = null;

    private static function root()
    {
        return dirname( __DIR__, 4 );
    }

    protected function tearDown(): void
    {
        if ( $this->iniSaved !== null )
        {
            $ini = eZINI::instance();
            if ( $this->iniSaved === false )
                $ini->removeSetting( 'URLTranslator', 'MaxPathDepth' );
            else
                $ini->setVariable( 'URLTranslator', 'MaxPathDepth', $this->iniSaved );
            $this->iniSaved = null;
        }
    }

    private function setMaxPathDepth( $value )
    {
        $ini = eZINI::instance();
        if ( $this->iniSaved === null )
            $this->iniSaved = $ini->hasVariable( 'URLTranslator', 'MaxPathDepth' ) ? $ini->variable( 'URLTranslator', 'MaxPathDepth' ) : false;
        $ini->setVariable( 'URLTranslator', 'MaxPathDepth', $value );
    }

    /**
     * Runs PHP code in a child process from the root of the tree, returns its output (stdout and stderr).
     */
    private static function runPhp( $code, $cwd = null )
    {
        $process = proc_open( array( PHP_BINARY, '-d', 'display_errors=stderr', '-r', $code ),
                              array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, $cwd === null ? self::root() : $cwd );
        $out = stream_get_contents( $pipes[1] ) . stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        proc_close( $process );
        return $out;
    }

    public function testMaxPathDepthDefaultsTo30AndReadsSiteIni()
    {
        $this->assertMatchesRegularExpression( '/^MaxPathDepth=30$/m', file_get_contents( self::root() . '/settings/site.ini' ) );
        $this->setMaxPathDepth( '12' );
        $this->assertSame( 12, eZURLAliasML::maxPathDepth() );
        $this->setMaxPathDepth( '0' );
        $this->assertSame( 30, eZURLAliasML::maxPathDepth(), 'a value below 1 falls back to the default' );
    }

    public function testTranslateOfAPathDeeperThanTheLimitIsNotFound()
    {
        $this->setMaxPathDepth( '5' );
        $uri = 'a/b/c/d/e/f';
        $this->assertFalse( eZURLAliasML::translate( $uri ) );
        $this->assertSame( 'a/b/c/d/e/f', $uri, 'the URI is left as it was' );
    }

    public function testFetchByPathDeeperThanTheLimitFindsNothing()
    {
        $this->setMaxPathDepth( '5' );
        $this->assertSame( array(), eZURLAliasML::fetchByPath( 'a/b/c/d/e/f' ) );
        // the glob adds one more join
        $this->assertSame( array(), eZURLAliasML::fetchByPath( 'a/b/c/d/e', 'x*' ) );
    }

    public function testLogEntryStaysOnOneLine()
    {
        $dir = sys_get_temp_dir() . '/exp-log-' . getmypid() . '-' . mt_rand();
        mkdir( $dir );
        try
        {
            eZLog::write( "login failed for admin\r\n[ Jan 01 2026 00:00:00 ] [ forged ]\nentry", 'oneline.log', $dir );
            $lines = file( $dir . '/oneline.log', FILE_IGNORE_NEW_LINES );
            $this->assertCount( 1, $lines );
            $this->assertStringContainsString( 'login failed for admin [ Jan 01 2026 00:00:00 ] [ forged ] entry', $lines[0] );
        }
        finally
        {
            @unlink( $dir . '/oneline.log' );
            @rmdir( $dir );
        }
    }

    public function testRestKernelRegistersTheEventListeners()
    {
        $kernel = file_get_contents( self::root() . '/kernel/private/rest/classes/ezpkernelrest.php' );
        $this->assertStringContainsString( 'ezpEvent::getInstance()->registerEventListeners();', $kernel );
    }

    public function testFieldSetOfALanguageWithoutTranslationIsEmpty()
    {
        $set = new ezpContentFieldSet();
        $children = new ReflectionProperty( 'ezpContentFieldSet', 'childrenFieldSets' );
        $children->setValue( $set, array( 'ger-DE' => ezpContentFieldSet::fromDataMap( array() ) ) );

        $problems = array();
        set_error_handler( function ( $no, $message ) use ( &$problems ) { $problems[] = $message; return true; } );
        try
        {
            $set->setActiveLanguage( 'eng-GB' );
            $fields = iterator_to_array( $set );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertSame( array(), $problems );
        $this->assertSame( array(), $fields );
    }

    public function testParameterSetDefaultsTheRootDirectory()
    {
        $section = 'URLTranslator';
        $parameter = 'MaxPathDepth';
        $this->assertTrue( eZINI::parameterSet( 'site.ini', null, $section, $parameter ) );
    }

    public function testClusterIndexWithoutClusterConfigurationAnswersWithoutError()
    {
        $dir = sys_get_temp_dir() . '/exp-cluster-' . getmypid() . '-' . mt_rand();
        mkdir( $dir );
        try
        {
            $out = self::runPhp( 'include ' . var_export( self::root() . '/index_cluster.php', true ) . ';', $dir );
        }
        finally
        {
            @rmdir( $dir );
        }
        // the answer meant for this case (an error page and eZFatalUserError), not PHP's Error about the constant
        $this->assertStringNotContainsString( 'Undefined constant', $out );
        $this->assertStringContainsString( 'An error has occured', $out );
    }

    public function testPdfDeniedObjectDoesNotNameTheObject()
    {
        $template = file_get_contents( self::root() . '/design/standard/templates/content/datatype/pdf/ezxmltags/object_denied.tpl' );
        $this->assertStringNotContainsString( '$object.name', $template );
        $this->assertStringContainsString( 'You do not have permission to view this object', $template );
    }

    public function testBenchmarkCliRunnerLoads()
    {
        $out = self::runPhp( 'require "autoload.php"; require "benchmarks/classes/ezbenchmarkrunner.php";'
                           . ' require "benchmarks/classes/ezbenchmarkclirunner.php"; echo "loaded";' );
        $this->assertSame( 'loaded', trim( $out ) );
    }
}
