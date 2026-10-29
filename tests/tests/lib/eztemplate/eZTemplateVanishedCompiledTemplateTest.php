<?php
/**
 * A compiled template whose file vanishes between loading and running it (a template cache clear while a page
 * renders: "Unknown compiled template", then "Trying to access array offset on null in eztemplate.php" because the
 * caller processed a template that had no tree):
 *   - fetch() of such a template loads it once more, compiles it again and renders it, with no PHP warning
 *   - the same for an {include}d template (processURI())
 *   - process() with no tree renders nothing, writes no PHP warning and reports the template once
 *
 * No kernel bootstrap, no database: a bare eZTemplate, compiled templates in a private directory under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group eztemplate
 */

/** A template object that removes the compiled file of the given template right before it runs, once */
class eZTemplateVanishingCompiledFile extends eZTemplate
{
    public $vanish = array();
    public $vanished = array();

    function executeCompiledTemplate( &$resourceData, &$textElements, $rootNamespace, $currentNamespace, &$extraParameters )
    {
        $name = basename( (string)$resourceData['template-filename'] );
        if ( in_array( $name, $this->vanish, true ) && !isset( $this->vanished[$name] ) )
        {
            $this->vanished[$name] = true;
            $dir = eZTemplateCompiler::compilationDirectory();
            foreach ( (array)glob( $dir . '/' . pathinfo( $name, PATHINFO_FILENAME ) . '-*.php' ) as $file )
                unlink( $file );
        }
        return parent::executeCompiledTemplate( $resourceData, $textElements, $rootNamespace, $currentNamespace, $extraParameters );
    }
}

class eZTemplateVanishedCompiledTemplateTest extends PHPUnit\Framework\TestCase
{
    private static $dir;
    private $savedGlobals = array();
    private $warnings = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-eztemplate-vanish-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir . '/compiled', 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        foreach ( (array)glob( self::$dir . '/compiled/*' ) as $f )
            unlink( $f );
        rmdir( self::$dir . '/compiled' );
        foreach ( (array)glob( self::$dir . '/*' ) as $f )
            unlink( $f );
        rmdir( self::$dir );
    }

    protected function setUp(): void
    {
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        unset( $GLOBALS['eZSiteBasics'] );
        $GLOBALS['eZTemplateCompilerSettings']['compile'] = true;
        $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = self::$dir . '/compiled';
        $this->warnings = array();
        set_error_handler( function ( $no, $message, $file, $line ) {
            $this->warnings[] = $message . ' in ' . basename( $file ) . ':' . $line;
            return true;
        }, E_WARNING | E_NOTICE );
    }

    protected function tearDown(): void
    {
        restore_error_handler();
        foreach ( $this->savedGlobals as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
    }

    private function file( $name, $source )
    {
        $file = self::$dir . '/' . $name;
        file_put_contents( $file, $source );
        return $file;
    }

    private function tpl()
    {
        $tpl = new eZTemplateVanishingCompiledFile();
        $tpl->autoload();
        return $tpl;
    }

    public function testAFetchedTemplateWhoseCompiledFileVanishedIsCompiledAgain()
    {
        $file = $this->file( 'outer_vanish.tpl', 'hello {$who}' );
        $warm = $this->tpl();
        $warm->setVariable( 'who', 'first' );
        $this->assertSame( 'hello first', $warm->fetch( $file ), 'the first fetch compiles it' );

        $tpl = $this->tpl();
        $tpl->vanish = array( 'outer_vanish.tpl' );
        $tpl->setVariable( 'who', 'world' );
        $this->assertSame( 'hello world', $tpl->fetch( $file ) );
        $this->assertSame( array( 'outer_vanish.tpl' => true ), $tpl->vanished, 'the compiled file was removed before it ran' );
        $this->assertSame( array(), $this->warnings );
    }

    public function testAnIncludedTemplateWhoseCompiledFileVanishedIsCompiledAgain()
    {
        // processURI() is what {include} runs for a template it does not inline
        $inner = $this->file( 'inner_vanish.tpl', '[{$v}]' );
        $warm = $this->tpl();
        $warm->setVariable( 'v', 1 );
        $this->assertSame( '[1]', $warm->fetch( $inner ) );

        $tpl = $this->tpl();
        $tpl->vanish = array( 'inner_vanish.tpl' );
        $tpl->setVariable( 'v', 2 );
        $elements = array();
        $extra = array();
        $tpl->processURI( $inner, true, $extra, $elements, '', '' );
        $this->assertSame( '[2]', implode( '', $elements ) );
        $this->assertArrayHasKey( 'inner_vanish.tpl', $tpl->vanished );
        $this->assertSame( array(), $this->warnings );
    }

    public function testProcessWithoutATreeRendersNothingAndReportsTheTemplateOnce()
    {
        $tpl = $this->tpl();
        $text = 'unchanged';
        $tpl->process( null, $text, '', '', 'design:content/dashboard.tpl' );
        $this->assertSame( '', $text );
        $tpl->process( null, $text, '', '', 'design:content/dashboard.tpl' );
        $this->assertSame( array( 'design:content/dashboard.tpl' => true ), $tpl->MissingRootReported );
        $elements = array();
        $tpl->processNode( null, $elements, '', '' );
        $this->assertSame( array(), $elements );
        $this->assertSame( array(), $this->warnings, 'no PHP warning' );
    }
}
