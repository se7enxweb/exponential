<?php
/**
 * The Smarty security policy of .stpl templates (expSmartySecurity, smarty.ini [SmartySettings] Security=enabled with
 * the lists the installation ships): what it refuses when a template is compiled ({eval}, {fetch file=...}, {math},
 * {debug}, {config_load}, $smarty.server, .get, .const, .template_object, PHP constants, static classes, -> on
 * objects, the modifier debug_print_var, files outside the trusted directories), what it lets through (Smarty's
 * control tags, the template system's operators, functions and blocks, fetch() as an operator), that it is off by
 * default, and the pre-filter of SecurityObjectAccess on its own. expSmartyOperatorParityTest runs every parity
 * case under the policy as well.
 *
 * Smarty 5 as in expSmartyTemplateRenderTest (Composer, or SMARTY_LIBRARY_PATH); without it the cases are skipped.
 * No database and no siteaccess.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group smarty
 */

class expSmartySecurityTest extends PHPUnit\Framework\TestCase
{
    /** @var string directory of this class's template files */
    private static $dir;

    /** @var array smarty.ini settings saved by setUp() */
    private $saved = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-smarty-security-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir, 0777, true );
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

    protected function setUp(): void
    {
        if ( !expSmartyRenderer::loadLibrary() )
            $this->markTestSkipped( 'Smarty 5 is not installed (composer require smarty/smarty, or SMARTY_LIBRARY_PATH)' );
        $ini = eZINI::instance( 'smarty.ini' );
        foreach ( array( 'Security', 'SecurityTrustedDirList', 'SecurityStaticClassList', 'SecurityObjectAccess' ) as $name )
            $this->saved[$name] = $ini->hasVariable( 'SmartySettings', $name ) ? $ini->variable( 'SmartySettings', $name ) : null;
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'smarty.ini' );
        foreach ( $this->saved as $name => $value )
            $ini->setVariable( 'SmartySettings', $name, $value );
        expSmartyRenderer::resetSettings();
    }

    private function enable( array $settings = array() )
    {
        $ini = eZINI::instance( 'smarty.ini' );
        $ini->setVariable( 'SmartySettings', 'Security', 'enabled' );
        $ini->setVariable( 'SmartySettings', 'SecurityTrustedDirList', array( self::$dir ) );
        foreach ( $settings as $name => $value )
            $ini->setVariable( 'SmartySettings', $name, $value );
        expSmartyRenderer::resetSettings();
    }

    private function file( $source, $suffix = '.stpl' )
    {
        $file = self::$dir . '/' . md5( $source ) . $suffix;
        if ( !file_exists( $file ) )
            file_put_contents( $file, $source );
        return $file;
    }

    /** Compiles and runs $source with the Smarty instance of the renderer; the exception, if any. */
    private function compileError( $source )
    {
        $smarty = expSmartyRenderer::smarty();
        try
        {
            $smarty->createTemplate( 'file:' . getcwd() . '/' . $this->file( $source ) )->fetch();
        }
        catch ( \Smarty\Exception $e )
        {
            return $e->getMessage();
        }
        return null;
    }

    private function render( $source, array $variables = array() )
    {
        $tpl = eZTemplate::factory();
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        $output = $tpl->fetch( $this->file( $source ) );
        foreach ( array_keys( $variables ) as $name )
            $tpl->unsetVariable( $name );
        return $output;
    }

    public function testOnByDefault()
    {
        $this->assertSame( 'enabled', eZINI::instance( 'smarty.ini' )->variable( 'SmartySettings', 'Security' ) );
        expSmartyRenderer::resetSettings();
        $this->assertNotNull( expSmartyRenderer::smarty()->security_policy );
        $this->assertNotNull( $this->compileError( '{$smarty.server.REQUEST_TIME_FLOAT|default:1}' ) );
    }

    public static function refusedProvider()
    {
        return array(
            'eval'                    => array( "{eval var='{\$x}'}" ),
            'fetch file'              => array( "{fetch file='/etc/hostname'}" ),
            'fetch file in a capture' => array( "{capture assign=c}{fetch file='settings/site.ini'}{/capture}{\$c}" ),
            'math'                    => array( "{math equation='1 + 1'}" ),
            'debug'                   => array( '{debug}' ),
            'config_load'             => array( "{config_load file='x.conf'}" ),
            'html_image'              => array( "{html_image file='x.png'}" ),
            '$smarty.server'          => array( '{$smarty.server.PATH}' ),
            '$smarty.server in an if' => array( '{if $smarty.server.HTTPS}x{/if}' ),
            '$smarty.get'             => array( '{$smarty.get.id}' ),
            '$smarty.post'            => array( '{$smarty.post.id}' ),
            '$smarty.cookies'         => array( '{$smarty.cookies.id}' ),
            '$smarty.session'         => array( '{$smarty.session.id}' ),
            '$smarty.env'             => array( '{$smarty.env.PATH}' ),
            '$smarty.request'         => array( '{$smarty.request.id}' ),
            '$smarty.const'           => array( '{$smarty.const.PHP_VERSION}' ),
            '$smarty.template_object' => array( '{$smarty.template_object->getSmarty()}' ),
            'PHP constant'            => array( '{if PHP_VERSION}x{/if}' ),
            'static class'            => array( '{eZSys::rootDir()}' ),
            'static property'         => array( '{eZSys::$instance}' ),
            'method of an object'     => array( '{$node->expSmartyObject()}' ),
            'property of an object'   => array( '{if $node->Name}x{/if}' ),
            'object in a string'      => array( '{"name: `$node->Name`"}' ),
            'debug_print_var'         => array( '{$x|debug_print_var}' ),
            'include outside'         => array( "{include file='/etc/hostname'}" ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('refusedProvider')]
    public function testRefused( $source )
    {
        $this->enable();
        $error = $this->compileError( $source );
        $this->assertNotNull( $error, "refused: $source" );
        $this->assertMatchesRegularExpression( '/security|not allowed|not trusted|directory/i', $error );
        // through the template system: nothing of it reaches the page
        $output = $this->render( $source, array( 'x' => 'X' ) );
        $this->assertStringNotContainsString( '/', strip_tags( preg_replace( '/<!--.*?-->/s', '', $output ) ) );
    }

    public static function allowedProvider()
    {
        return array(
            'control'          => array( '{if $a}{foreach [1, 2] as $i}{$i}{if $i@last}.{/if}{/foreach}{elseif $b}b{else}c{/if}', '12.' ),
            'foreachelse'      => array( '{foreach [] as $i}{$i}{foreachelse}none{/foreach}', 'none' ),
            'while and break'  => array( '{$i = 0}{while true}{$i = $i + 1}{if $i > 2}{break}{/if}{/while}{$i}', '3' ),
            'capture'          => array( '{capture assign=c}captured{/capture}{$c}', 'captured' ),
            'delimiters'       => array( '{ldelim}x{rdelim}', '{x}' ),
            'isset'            => array( '{if isset($u)}set{else}unset{/if}', 'unset' ),
            'default'          => array( "{\$u|default:'d'}", 'd' ),
            'Smarty modifier'  => array( "{'abc'|upper}{[1, 2]|count}", 'ABC2' ),
            'operators'        => array( "{concat('a', 'b')}{\$t|wash}{'x y'|upcase}", 'ab&lt;b&gt;X Y' ),
            'operator mode'    => array( "{cond(true, 'yes', 'no')}{sum(1, 2)}", 'yes3' ),
            'fetch operator'   => array( "{if false}{fetch('content', 'node', ['node_id' => 2])}{/if}ok", 'ok' ),
            'helper modifiers' => array( "{foreach exprange(1, 3) as \$i}{\$i}{/foreach}", '123' ),
            'exp tags'         => array( "{expset name=a value='set' scope=root}{\$a}{expsetblock variable=b scope=root}block{/expsetblock}{\$b}", 'setblock' ),
            'function'         => array( '{function name=hello}hi {$who}{/function}{call name=hello who=you}', 'hi you' ),
            'text with braces' => array( "<script>var o = { a: 1 }; if (x) { y(); }</script>", "<script>var o = { a: 1 }; if (x) { y(); }</script>" ),
            'literal'          => array( '{literal}a->b {$x->y}{/literal}', 'a->b {$x->y}' ),
            'arrow in a single-quoted string' => array( "{'a->b'}", 'a->b' ),
            'array arrow'      => array( "{\$h = ['k' => 'v']}{\$h.k}", 'v' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('allowedProvider')]
    public function testAllowed( $source, $expected )
    {
        $this->enable();
        $this->assertNull( $this->compileError( $source ), "allowed: $source" );
        $this->assertSame( $expected, $this->render( $source, array( 'a' => true, 't' => '<b>' ) ) );
    }

    public function testIncludeFromATrustedDirectory()
    {
        $this->enable();
        $child = $this->file( 'child {$v}' );
        $this->assertSame( 'child 1', $this->render( "{include file='" . getcwd() . '/' . $child . "' v=1}" ) );
    }

    public function testStaticClassList()
    {
        $this->enable( array( 'SecurityStaticClassList' => array( 'expSmartyTestStatics' ) ) );
        $this->assertSame( 'static', $this->render( '{expSmartyTestStatics::name()}' ) );
        $this->assertNotNull( $this->compileError( '{eZSys::rootDir()}' ) );
    }

    public function testObjectAccessSetting()
    {
        $this->enable( array( 'SecurityObjectAccess' => 'enabled' ) );
        $this->assertNull( $this->compileError( '{if false}{$node->expSmartyObject()}{/if}' ) );
    }

    public function testCompiledApart()
    {
        eZINI::instance( 'smarty.ini' )->setVariable( 'SmartySettings', 'Security', 'disabled' );
        expSmartyRenderer::resetSettings();
        $plain = expSmartyRenderer::smarty()->getCompileDir();
        $this->enable();
        $secure = expSmartyRenderer::smarty()->getCompileDir();
        $this->assertNotSame( $plain, $secure );
        $this->assertStringEndsWith( '/smarty/compile-secure/', $secure );
        // a template compiled without the policy is compiled again with it
        $source = '{$smarty.server.PATH|default:1}';
        expSmartyRenderer::resetSettings();
        eZINI::instance( 'smarty.ini' )->setVariable( 'SmartySettings', 'Security', 'disabled' );
        $this->assertNull( $this->compileError( $source ) );
        $this->enable();
        $this->assertNotNull( $this->compileError( $source ) );
    }

    public static function filterProvider()
    {
        return array(
            'plain text'          => array( 'a -> b', false ),
            'auto literal'        => array( '{ a->b }', false ),
            'comment'             => array( '{* $x->y *}', false ),
            'literal'             => array( '{literal}{$x->y}{/literal}', false ),
            'single quotes'       => array( "{'a->b'}", false ),
            'escaped quote'       => array( "{'it\\'s a->b'}", false ),
            'array arrow'         => array( "{['a' => 1]}", false ),
            'method'              => array( '{$x->y()}', true ),
            'property'            => array( '{$x->y}', true ),
            'double quotes'       => array( '{"`$x->y`"}', true ),
            'nested'              => array( '{if {$x->y}}z{/if}', true ),
            'after a literal'     => array( '{literal}{{/literal}{$x->y}', true ),
            'after a text brace'  => array( 'function () { return 1; } {$x->y}', true ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('filterProvider')]
    public function testObjectAccessFilter( $source, $refused )
    {
        try
        {
            $this->assertSame( $source, expSmartySecurity::objectAccessFilter( $source ) );
            $this->assertFalse( $refused, "refused: $source" );
        }
        catch ( \Smarty\CompilerException $e )
        {
            $this->assertTrue( $refused, "allowed: $source" );
        }
    }
}

/** A class a test allows with SecurityStaticClassList */
class expSmartyTestStatics
{
    public static function name()
    {
        return 'static';
    }
}
