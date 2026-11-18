<?php
/**
 * The functions every compiled template calls (compiledFetchAttribute() and the others of common.php) are defined
 * before a compiled template runs, even when a template cache clear removed common.php after the template was
 * compiled ("Call to undefined function compiledFetchAttribute()" ended the request before).
 *
 * Each test runs in a process of its own: once a compiled template has run, the functions stay defined for the
 * rest of the process.
 *
 * No kernel bootstrap, no database: a bare eZTemplate, compiled templates in a private directory under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group eztemplate
 */

class eZTemplateCompilerCommonCodeTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/phpunit-eztemplate-common-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir . '/compiled', 0777, true );
        unset( $GLOBALS['eZSiteBasics'] );
        $GLOBALS['eZTemplateCompilerSettings']['compile'] = true;
        $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = $this->dir . '/compiled';
    }

    protected function tearDown(): void
    {
        foreach ( (array)glob( $this->dir . '/compiled/*' ) as $f )
            unlink( $f );
        rmdir( $this->dir . '/compiled' );
        foreach ( (array)glob( $this->dir . '/*' ) as $f )
            unlink( $f );
        rmdir( $this->dir );
    }

    private function tpl()
    {
        $tpl = new eZTemplate();
        $tpl->autoload();
        return $tpl;
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testACompiledTemplateRunsWhenCommonPhpWasRemovedAfterItWasCompiled()
    {
        $file = $this->dir . '/attribute.tpl';
        file_put_contents( $file, '[{$item.name}]' );

        // Compiled, not executed: the template's file and common.php are written, nothing is defined yet
        $GLOBALS['eZTemplateCompilerSettings']['execution'] = false;
        $first = $this->tpl();
        $first->setVariable( 'item', array( 'name' => 'first' ) );
        $this->assertSame( '[first]', $first->fetch( $file ) );
        $this->assertFileExists( $this->dir . '/compiled/common.php' );
        $this->assertNotEmpty( glob( $this->dir . '/compiled/attribute-*.php' ), 'the template was compiled' );
        $this->assertFalse( defined( 'EZ_TEMPLATE_COMPILER_COMMON_CODE' ) );

        // A template cache clear removes common.php only
        unlink( $this->dir . '/compiled/common.php' );

        $GLOBALS['eZTemplateCompilerSettings']['execution'] = true;
        $tpl = $this->tpl();
        $tpl->setVariable( 'item', array( 'name' => 'second' ) );
        $this->assertSame( '[second]', $tpl->fetch( $file ) );
        $this->assertTrue( function_exists( 'compiledFetchAttribute' ) );
        $this->assertFileExists( $this->dir . '/compiled/common.php', 'common.php is written again' );
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testLoadCommonCodeDefinesTheFunctionsOnce()
    {
        $this->assertFalse( function_exists( 'compiledFetchAttribute' ) );
        eZTemplateCompiler::loadCommonCode();
        $this->assertTrue( defined( 'EZ_TEMPLATE_COMPILER_COMMON_CODE' ) );
        $this->assertTrue( function_exists( 'compiledFetchAttribute' ) );
        $this->assertTrue( function_exists( 'compiledFetchVariable' ) );
        eZTemplateCompiler::loadCommonCode();
        $this->assertFileExists( $this->dir . '/compiled/common.php' );
    }
}
