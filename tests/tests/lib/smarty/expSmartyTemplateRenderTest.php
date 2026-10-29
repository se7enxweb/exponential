<?php
/**
 * End-to-end tests of Smarty templates (lib/smarty): a .stpl file in, output out, through the template system as the
 * site uses it (eZTemplate::factory(), eZTemplateFileResource, the exp_smarty_render stub, expSmartyRenderer).
 *
 * Covers the input modes of bridged operators (an operator reads its input as its operator value, or, for cond,
 * first_set and the input-as-parameter true operators such as ezini and sum, its parameters alone), {foreach} over a
 * computed source, variables written back to the calling template, the stub and its cache key, the block function
 * check, and the converter (tpl -> stpl) on the constructs the media design port needed.
 *
 * Smarty 5 comes from the installation's Composer autoload (smarty/smarty in composer.json). Without it, the
 * environment variable SMARTY_LIBRARY_PATH may name a Smarty checkout ([SmartySettings] LibraryPath); otherwise the
 * rendering tests are skipped.
 *
 * No database and no siteaccess.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group smarty
 */

require_once __DIR__ . '/expSmartyOperatorParityTest.php';

class expSmartyTemplateRenderTest extends PHPUnit\Framework\TestCase
{
    /** @var string directory of this class's template files */
    private static $dir;

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-smarty-render-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir, 0777, true );
        expSmartyOperatorParityTest::trustTestDirectory( self::$dir );
        $libraryPath = getenv( 'SMARTY_LIBRARY_PATH' );
        if ( !class_exists( '\Smarty\Smarty' ) && $libraryPath )
            eZINI::instance( 'smarty.ini' )->setVariable( 'SmartySettings', 'LibraryPath', $libraryPath );
    }

    public static function tearDownAfterClass(): void
    {
        foreach ( glob( self::$dir . '/*' ) as $file )
            unlink( $file );
        rmdir( self::$dir );
    }

    private function requireSmarty()
    {
        if ( !expSmartyRenderer::loadLibrary() )
            $this->markTestSkipped( 'Smarty 5 is not installed (composer require smarty/smarty, or SMARTY_LIBRARY_PATH)' );
    }

    /**
     * Writes $source to a file of its own (Smarty and the template system key compiled files on the file and its
     * modification time) and returns the path.
     */
    private function file( $source, $suffix )
    {
        $file = self::$dir . '/' . md5( $source ) . $suffix;
        if ( !file_exists( $file ) )
            file_put_contents( $file, $source );
        return $file;
    }

    private function renderSmarty( $source, array $variables = array() )
    {
        $tpl = eZTemplate::factory();
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        $output = $tpl->fetch( $this->file( $source, '.stpl' ) );
        foreach ( array_keys( $variables ) as $name )
            $tpl->unsetVariable( $name );
        return $output;
    }

    public static function renderProvider()
    {
        return array(
            // plain Smarty
            'variable'                       => array( '{$a}', array( 'a' => 'x' ), 'x' ),
            'array element'                  => array( '{$h.k}', array( 'h' => array( 'k' => 'v' ) ), 'v' ),
            'smarty if'                      => array( '{if $a == 2}two{else}other{/if}', array( 'a' => 2 ), 'two' ),

            // operators that read their input as the operator value (input-as-parameter 'always' or no hint)
            'contains on a variable'         => array( "{\$o = ['a.css']}{\$o|contains:'a.css'|json_encode}", array(), 'true' ),
            'contains on a literal'          => array( "{['a','b']|contains:'b'|json_encode}", array(), 'true' ),
            'implode (ez_ where Smarty has the name)' => array( "{['a','b']|exp_implode:','}", array(), 'a,b' ),
            'append'                         => array( "{\$o = ['a']}{\$o = \$o|append:'b'}{\$o|json_encode}", array(), '["a","b"]' ),
            'merge as function'              => array( "{merge(['a'], ['b'])|json_encode}", array(), '["a","b"]' ),
            'upcase'                         => array( "{'abc'|upcase}", array(), 'ABC' ),
            'i18n without a translation'     => array( "{'Close'|i18n:'design/standard'}", array(), 'Close' ),
            'eq as modifier'                 => array( "{1|eq:2|json_encode}", array(), 'false' ),

            // operators that read their parameters alone
            'concat as function'             => array( "{concat('a', 'b', 'c')}", array(), 'abc' ),
            'concat as modifier'             => array( "{'a'|concat:'b'}", array(), 'ab' ),
            'sum as function'                => array( '{sum(1, 2)}', array(), '3' ),
            'sum as modifier'                => array( '{1|sum:2}', array(), '3' ),
            'cond false'                     => array( '{cond(false, 1, 2)}', array(), '2' ),
            'cond true'                      => array( '{cond(true, 1, 2)}', array(), '1' ),
            'first_set'                      => array( "{first_set(\$nope, 'x')}", array(), 'x' ),
            'and'                            => array( '{and(true, false)|json_encode}', array(), 'false' ),
            'ezini'                          => array( "{ezini('SmartySettings', 'CompileCheck', 'smarty.ini')}", array(), 'enabled' ),
            'ezini_hasvariable'              => array( "{ezini_hasvariable('SmartySettings', 'NoSuchSetting', 'smarty.ini')|json_encode}", array(), 'false' ),

            // {foreach} over a computed source, as the converter writes it
            'foreach over a loop variable'   => array( "{\$__loop1 = ['x', 'y']}{foreach \$__loop1 as \$i}[{\$i}]{/foreach}", array(), '[x][y]' ),
            'foreach over an operator'       => array( "{\$__loop1 = ['a']|append:'b'}{foreach \$__loop1 as \$i}{\$i}{/foreach}", array(), 'ab' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('renderProvider')]
    public function testRender( $source, array $variables, $expected )
    {
        $this->requireSmarty();
        $this->assertSame( $expected, trim( $this->renderSmarty( $source, $variables ) ) );
    }

    public function testAssignedVariableIsWrittenBackToTheCallingTemplate()
    {
        $this->requireSmarty();
        $stpl = $this->file( "{\$smarty_written_back = 'from smarty'}", '.stpl' );
        $tpl = eZTemplate::factory();
        $output = $tpl->fetch( $this->file( "{exp_smarty_render( '$stpl' )}[{\$smarty_written_back}]", '.tpl' ) );
        $this->assertSame( 'from smarty', $tpl->variable( 'smarty_written_back' ), 'fetch() resets the variables; captureVariables() keeps them' );
        $tpl->unsetVariable( 'smarty_written_back' );
        $this->assertSame( '[from smarty]', trim( $output ) );
    }

    public function testStubAndCacheKey()
    {
        $this->assertTrue( expSmartyRenderer::isSmartyTemplate( 'design/x/templates/a.stpl' ) );
        $this->assertFalse( expSmartyRenderer::isSmartyTemplate( 'design/x/templates/a.tpl' ) );
        $this->assertSame( "{exp_smarty_render( 'design/x/templates/a.stpl' )}", expSmartyRenderer::stubFor( 'design/x/templates/a.stpl' ) );
        $this->assertStringStartsWith( '<!--', expSmartyRenderer::stubFor( "a'b.stpl" ) );

        $resource = new eZTemplateFileResource();
        $extra = array();
        $this->assertSame( md5( 'file:a.tpl' ), $resource->cacheKey( 'file:a.tpl', 'file', 'a.tpl', $extra ) );
        $stplKey = $resource->cacheKey( 'file:a.stpl', 'file', 'a.stpl', $extra );
        $this->assertNotSame( md5( 'file:a.stpl' ), $stplKey, 'a stub never shares a compiled file with a parse of the raw text' );
        $this->assertSame( expSmartyRenderer::cacheKey( 'file:a.stpl', 'a.stpl' ), $stplKey );
    }

    public function testTheSiteTemplateSystemRegistersTheStubOperator()
    {
        $this->assertTrue( expSmartyRenderer::hasOperator( 'exp_smarty_render' ), 'settings/site.ini AutoloadPathList[] lists lib/smarty/classes/' );
    }

    public function testBlockFunctionsAreTold()
    {
        $this->assertTrue( expSmartyRenderer::isBlockFunction( 'section' ), 'section has a body' );
        $this->assertFalse( expSmartyRenderer::isBlockFunction( 'include' ), 'include has none' );
        $this->assertFalse( expSmartyRenderer::isBlockFunction( 'no_such_function' ) );
    }

    public static function converterProvider()
    {
        return array(
            'variable'              => array( '{$node.name|wash}', '{$node.name|wash}' ),
            // a newline follows a tag that prints nothing: Smarty removes the one after such a tag, as the .tpl output has it
            'foreach over variable' => array( '{foreach $list as $i}{$i}{/foreach}', "{foreach \$list as \$i}\n{\$i}{/foreach}" ),
            'foreach over operator' => array( "{foreach \$a|append('x') as \$i}{\$i}{/foreach}", "{\$__loop1 = \$a|append:'x'}\n{foreach \$__loop1 as \$i}\n{\$i}{/foreach}" ),
            'first_set as modifier' => array( '{$a|first_set($b)}', '{first_set($b)}' ),
            'cond'                  => array( "{cond(\$a, 'x', 'y')}", "{cond(\$a, 'x', 'y')}" ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('converterProvider')]
    public function testConverter( $tplSource, $expected )
    {
        $converter = new expSmartyConverter( eZTemplate::factory() );
        $this->assertSame( $expected, trim( $converter->convert( $tplSource, 'test.tpl' ) ) );
    }

    public function testEveryConvertedConstructCompiles()
    {
        $this->requireSmarty();
        $converter = new expSmartyConverter( eZTemplate::factory() );
        $source = "{def \$x = array( 'a', 'b' )}{foreach \$x|append( 'c' ) as \$i}{\$i}{delimiter},{/delimiter}{/foreach}" .
                  "{if \$x|contains( 'a' )}A{/if}{\$x|first_set( 'z' )}{undef \$x}";
        $stpl = $this->file( $converter->convert( $source, 'test.tpl' ), '.stpl' );
        expSmartyRenderer::smarty()->createTemplate( 'file:' . realpath( $stpl ) )->compileTemplateSource();
        $this->assertSame( 'a,b,cAz', trim( eZTemplate::factory()->fetch( $stpl ) ) );
    }
}
