<?php
/**
 * A {cache-block} whose body includes the same template again (a tree or a menu that caches each node with keys of
 * its own) stores the outer block after the inner one ends, compiled as interpreted.
 *
 *   - CB-01 the compiled code keeps the block's cluster file handler in a PHP variable named after the block's
 *           placement. A template that includes itself runs the same code in the same scope: the inner block set the
 *           variable to its own handler and unset it at its end, so the outer block stored its text with no handler
 *           (a PHP warning and an Error) and was never cached. The second rendering, from the cache, gives the
 *           same text.
 *
 * Each case is rendered interpreted and compiled, without template errors and without PHP notices. Cache files go to
 * the installation's template-block cache under a key unique to the run and are removed in tearDown().
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group eztemplate
 */

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('eztemplate')]
class eZTemplateCacheBlockRecursionTest extends PHPUnit\Framework\TestCase
{
    /** @var string directory of this class's template files and compiled templates */
    private static $dir;

    private $savedGlobals = array();

    private $handler;

    private $token;

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-eztemplate-cacheblock-recursion-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir . '/compiled', 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        self::removeTree( self::$dir );
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics', 'eZCurrentAccess' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        if ( !isset( $GLOBALS['eZCurrentAccess']['name'] ) )
            $GLOBALS['eZCurrentAccess'] = array( 'name' => 'phpunit' );
        $this->handler = self::exceptionHandler();
        $this->token = 'cbrec' . getmypid() . substr( md5( uniqid( '', true ) ), 0, 8 );
    }

    protected function tearDown(): void
    {
        // the cluster handler and the expiry check register eZExecution's handlers on their first call
        while ( self::exceptionHandler() !== $this->handler && self::exceptionHandler() !== null )
            restore_exception_handler();
        foreach ( $this->savedGlobals as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
        $dir = eZTemplateCacheBlock::templateBlockCacheDir();
        if ( is_dir( $dir ) )
        {
            $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $files as $file )
            {
                if ( $file->isFile() && strpos( (string)file_get_contents( $file->getPathname() ), $this->token ) !== false )
                    unlink( $file->getPathname() );
            }
        }
    }

    private static function exceptionHandler()
    {
        $handler = set_exception_handler( null );
        restore_exception_handler();
        return $handler;
    }

    private static function removeTree( $path )
    {
        if ( !$path || !file_exists( $path ) )
            return;
        if ( is_dir( $path ) && !is_link( $path ) )
        {
            foreach ( scandir( $path ) as $entry )
            {
                if ( $entry !== '.' && $entry !== '..' )
                    self::removeTree( $path . '/' . $entry );
            }
            rmdir( $path );
        }
        else
            unlink( $path );
    }

    /**
     * Renders $source as the template $name ("%self%" names the template itself) compiled or interpreted. Returns
     * array( output without white space, errors, PHP notices, exception message or null ).
     */
    private function render( $name, $source, array $variables, $compiled )
    {
        if ( $compiled )
        {
            unset( $GLOBALS['eZSiteBasics'] );
            $GLOBALS['eZTemplateCompilerSettings']['compile'] = true;
            $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = self::$dir . '/compiled';
        }
        else
        {
            $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
            $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
        }

        $file = self::$dir . '/' . $name . '.tpl';
        file_put_contents( $file, str_replace( '%self%', 'file:' . $file, $source ) );

        $notices = array();
        $exception = null;
        $output = '';
        $tpl = new eZTemplate();
        set_error_handler( function ( $no, $str, $f, $l ) use ( &$notices )
        {
            $notices[] = $str . ' (' . basename( $f ) . ':' . $l . ')';
            return true;
        } );
        try
        {
            $tpl->autoload();
            foreach ( $variables as $variable => $value )
                $tpl->setVariable( $variable, $value );
            $output = $tpl->fetch( $file );
        }
        catch ( Throwable $e )
        {
            $exception = get_class( $e ) . ': ' . $e->getMessage();
        }
        finally
        {
            restore_error_handler();
        }
        return array( preg_replace( '/\s+/', '', (string)$output ), $tpl->errorLog(), $notices, $exception );
    }

    /** CB-01 */
    public function testCacheBlockIsStoredAfterARecursiveInclude()
    {
        $tree = array(
            array( 'id' => 1, 'children' => array( array( 'id' => 11, 'children' => array() ) ) ),
            array( 'id' => 2, 'children' => array() ),
        );
        $source = '{cache-block keys=array( $token, $nodes[0].id )}[{$token}]{foreach $nodes as $n}({$n.id}{if $n.children}{include uri="%self%" nodes=$n.children token=$token}{/if}){/foreach}{/cache-block}';
        foreach ( array( 'interpreted' => false, 'compiled' => true ) as $mode => $compiled )
        {
            // a token per mode: each mode renders, then reads its own blocks back from the cache
            $this->token .= $mode[0];
            foreach ( array( 'rendered', 'from the cache' ) as $pass )
            {
                list( $output, $errors, $notices, $exception ) = $this->render( 'cacheblock_tree_' . $mode,
                    $source, array( 'nodes' => $tree, 'token' => $this->token ), $compiled );
                $this->assertNull( $exception, "$mode, $pass" );
                // the token in the text lets tearDown() find the cache files
                $this->assertSame( '(1(11))(2)', str_replace( '[' . $this->token . ']', '', $output ), "$mode, $pass" );
                $this->assertSame( array(), $errors, "$mode, $pass: no template errors" );
                $this->assertSame( array(), $notices, "$mode, $pass: no PHP notices" );
            }
        }
    }
}
