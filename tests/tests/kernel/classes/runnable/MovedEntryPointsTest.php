<?php
/**
 * The moved entry points (#207): CLI scripts (bin/), cronjob parts (cronjobs/) and module views are one call
 * to a class. Contract of the stubs and their classes, checked structurally; nothing of the moved code is
 * included or executed.
 *
 *  MV-01 — Sample: bin/php/ezcache.php calls an existing Command class, mapped by autoload/ezp_kernel.php
 *  MV-02 — Sample: cronjobs/workflow.php returns the call of an existing CronjobPart class
 *  MV-03 — Every stub (any file under bin/, cronjobs/, kernel/, extension/*\/ carrying the marker) names a class that
 *          is mapped by autoload/ezp_kernel.php or var/autoload/ezp_extension.php
 *  MV-04 — The mapped class file exists and `php -l` passes
 *  MV-05 — The class file declares the class and it extends the matching Exponential\Runnable base
 *          (Command for bin/, CronjobPart for cronjobs/, ModuleView for module views)
 *  MV-06 — The stub itself parses, calls main( __FILE__ ... ) and passes get_defined_vars() for parts and views
 *  MV-07 — The walk finds stubs at all (the marker is not lost)
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group runnable
 */

class MovedEntryPointsTest extends PHPUnit\Framework\TestCase
{
    const MARKER = '(#207); this file is the entry point.';

    private static $root;
    private static $map;
    private static $stubs;

    private static function root()
    {
        if ( self::$root === null )
            self::$root = dirname( __DIR__, 5 );
        return self::$root;
    }

    /** class => file, from the kernel and the extension autoload arrays (data files, plain arrays) */
    private static function map()
    {
        if ( self::$map === null )
        {
            self::$map = array();
            foreach ( array( 'autoload/ezp_kernel.php', 'var/autoload/ezp_extension.php' ) as $f )
            {
                $a = is_file( self::root() . '/' . $f ) ? include self::root() . '/' . $f : array();
                if ( is_array( $a ) )
                    self::$map += $a;
            }
        }
        return self::$map;
    }

    /** @return array relative path => array( kind, class ) */
    private static function stubs()
    {
        if ( self::$stubs !== null )
            return self::$stubs;
        $root = self::root();
        $dirs = array( 'bin', 'cronjobs', 'kernel' );
        foreach ( glob( $root . '/extension/*', GLOB_ONLYDIR ) as $e )
            $dirs[] = 'extension/' . basename( $e );
        $found = array();
        foreach ( $dirs as $d )
        {
            if ( !is_dir( "$root/$d" ) )
                continue;
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( "$root/$d", FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS ) );
            foreach ( $it as $file )
            {
                $path = $file->getPathname();
                $relp = substr( $path, strlen( $root ) );
                if ( substr( $path, -4 ) !== '.php' || strpos( $relp, '/.git/' ) !== false || strpos( $relp, '/var/' ) !== false
                     || strpos( $relp, '/node_modules/' ) !== false || strpos( $relp, '/vendor/' ) !== false )
                    continue;
                if ( $file->getSize() > 20000 )
                    continue; // a stub is a few lines plus its header comment
                $text = (string) file_get_contents( $path );
                if ( strpos( $text, self::MARKER ) === false )
                    continue;
                $rel = substr( $path, strlen( $root ) + 1 );
                $class = '';
                $kind = '';
                if ( preg_match( '/\\\\(Exponential\\\\(Command|Cronjob|View)\\\\[A-Za-z0-9_\\\\]+)::main\s*\(/', $text, $m ) )
                {
                    $class = $m[1];
                    $kind = $m[2];
                }
                $found[$rel] = array( $kind, $class );
            }
        }
        ksort( $found );
        return self::$stubs = $found;
    }

    private static function expectedBase( $kind )
    {
        return array( 'Command' => 'Command', 'Cronjob' => 'CronjobPart', 'View' => 'ModuleView' )[$kind];
    }

    public static function stubProvider()
    {
        $rows = array();
        foreach ( self::stubs() as $rel => $info )
            $rows[$rel] = array( $rel, $info[0], $info[1] );
        return $rows;
    }

    /** MV-07 */
    public function testTheWalkFindsStubs()
    {
        $this->assertGreaterThan( 0, count( self::stubs() ) );
        $kinds = array_unique( array_column( self::stubs(), 0 ) );
        $this->assertContains( 'Command', $kinds );
        $this->assertContains( 'Cronjob', $kinds );
    }

    /** MV-01 */
    public function testSampleCommandEzcache()
    {
        $text = file_get_contents( self::root() . '/bin/php/ezcache.php' );
        $this->assertStringContainsString( self::MARKER, $text );
        $this->assertMatchesRegularExpression( '/\\\\Exponential\\\\Command\\\\Kernel\\\\Ezcache::main\( __FILE__ \);\s*$/', $text );
        $map = self::map();
        $this->assertArrayHasKey( 'Exponential\\Command\\Kernel\\Ezcache', $map );
        $this->assertFileExists( self::root() . '/' . $map['Exponential\\Command\\Kernel\\Ezcache'] );
    }

    /** MV-02 */
    public function testSampleCronjobWorkflow()
    {
        $text = file_get_contents( self::root() . '/cronjobs/workflow.php' );
        $this->assertStringContainsString( self::MARKER, $text );
        $this->assertMatchesRegularExpression( '/return \\\\Exponential\\\\Cronjob\\\\Kernel\\\\Workflow::main\( __FILE__, get_defined_vars\(\) \);\s*$/', $text );
        $map = self::map();
        $this->assertArrayHasKey( 'Exponential\\Cronjob\\Kernel\\Workflow', $map );
        $this->assertSame( 'kernel/private/classes/cronjobs/workflow.php', $map['Exponential\\Cronjob\\Kernel\\Workflow'] );
    }

    /**
     * MV-03 .. MV-06
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'stubProvider' )]
    /**
     */
    public function testStubAndItsClass( $rel, $kind, $class )
    {
        $root = self::root();
        $this->assertNotSame( '', $class, "$rel: no call of an Exponential\\{Command,Cronjob,View}\\... class's main() found" );

        // MV-06 the stub
        $stub = (string) file_get_contents( "$root/$rel" );
        $this->assertStringContainsString( '::main( __FILE__', $stub, "$rel passes __FILE__" );
        if ( $kind !== 'Command' )
            $this->assertStringContainsString( 'get_defined_vars()', $stub, "$rel hands over its variables" );
        $this->assertSame( 0, self::lint( "$root/$rel" ), "$rel parses" );

        // MV-03
        $map = self::map();
        $this->assertArrayHasKey( $class, $map, "$rel: $class is not in autoload/ezp_kernel.php or var/autoload/ezp_extension.php" );

        // MV-04
        $file = $root . '/' . $map[$class];
        $this->assertFileExists( $file, "$rel: the file of $class" );
        $this->assertSame( 0, self::lint( $file ), "$file parses" );

        // MV-05
        $code = (string) file_get_contents( $file );
        $parts = explode( '\\', $class );
        $short = array_pop( $parts );
        $ns = implode( '\\', $parts );
        $this->assertMatchesRegularExpression( '/namespace\s+' . preg_quote( $ns, '/' ) . '\s*[;{]/', $code, "$file declares namespace $ns" );
        $base = self::expectedBase( $kind );
        $this->assertMatchesRegularExpression(
            '/class\s+' . preg_quote( $short, '/' ) . '\s+extends\s+\\\\Exponential\\\\Runnable\\\\' . $base . '\b/',
            $code, "$class extends \\Exponential\\Runnable\\$base" );
    }

    private static function lint( $file )
    {
        $out = array();
        $code = 1;
        exec( escapeshellarg( PHP_BINARY ) . ' -l ' . escapeshellarg( $file ) . ' 2>&1', $out, $code );
        return $code;
    }
}
