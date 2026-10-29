<?php
/**
 * Twig templates (.twig, .html.twig) in the template system: operators as filters (with an input) and functions
 * (without), the operators that ignore their input (first_set, cond) as in a .tpl, values of the template system read
 * through their attributes, write-back of what a .twig defines, changes and removes, {% expinclude %} of .twig and .tpl
 * in the shared namespace and in a named one, the exp* tags, the stub and the compiled-template key of both endings,
 * the suffix registry, and a changed template under a persistent worker.
 *
 * Twig 3 from Composer, or TWIG_LIBRARY_PATH; without it the cases are skipped. No database and no siteaccess.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group twig
 */

/** A value of the template system for the tests: attributes, as content objects and nodes have them. */
class expTwigTestObject
{
    private $attributes;

    public function __construct( array $attributes )
    {
        $this->attributes = $attributes;
    }

    public function attributes()
    {
        return array_keys( $this->attributes );
    }

    public function hasAttribute( $name )
    {
        return array_key_exists( $name, $this->attributes );
    }

    public function attribute( $name )
    {
        return $this->hasAttribute( $name ) ? $this->attributes[$name] : null;
    }

    public function secret()
    {
        return 'never';
    }
}

class expTwigTemplateRenderTest extends PHPUnit\Framework\TestCase
{
    /** @var string directory of this class's template files */
    private static $dir;

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-twig-render-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir, 0777, true );
        $libraryPath = getenv( 'TWIG_LIBRARY_PATH' );
        if ( !class_exists( '\Twig\Environment' ) && $libraryPath )
            eZINI::instance( 'twig.ini' )->setVariable( 'TwigSettings', 'LibraryPath', $libraryPath );
    }

    public static function tearDownAfterClass(): void
    {
        foreach ( glob( self::$dir . '/*' ) as $file )
            unlink( $file );
        rmdir( self::$dir );
    }

    protected function setUp(): void
    {
        if ( !expTwigRenderer::loadLibrary() )
            $this->markTestSkipped( 'Twig 3 is not installed (composer require twig/twig, or TWIG_LIBRARY_PATH)' );
    }

    private function file( $source, $suffix, $name = null )
    {
        $file = self::$dir . '/' . ( $name !== null ? $name : md5( $source ) ) . $suffix;
        if ( !file_exists( $file ) || file_get_contents( $file ) !== $source )
            file_put_contents( $file, $source );
        return $file;
    }

    private function render( $source, array $variables = array(), $suffix = '.twig' )
    {
        $tpl = eZTemplate::factory();
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        $output = $tpl->fetch( $this->file( $source, $suffix ) );
        foreach ( array_keys( $variables ) as $name )
            $tpl->unsetVariable( $name );
        return $output;
    }

    public static function renderProvider()
    {
        $node = new expTwigTestObject( array( 'name' => 'Node <b>', 'data_map' => array( 'title' => new expTwigTestObject( array( 'content' => 'T' ) ) ),
                                              'empty' => null, 'children' => array( new expTwigTestObject( array( 'name' => 'c1' ) ), new expTwigTestObject( array( 'name' => 'c2' ) ) ) ) );
        return array(
            'variable'                    => array( '{{ a }}', array( 'a' => 'x' ), 'x' ),
            'undefined prints nothing'    => array( '[{{ nothing }}][{{ nothing.deeper }}]', array(), '[][]' ),
            'operator as filter'          => array( "{{ 'abc'|upcase }}", array(), 'ABC' ),
            'operator as function'        => array( "{{ concat('a', 'b') }}", array(), 'ab' ),
            'twig name with exp_'         => array( "{{ ['a','b']|exp_implode(',') }}|{{ exp_count(['a']) }}", array(), 'a,b|1' ),
            'input-as-parameter operator' => array( '{{ 1|sum(2) }}|{{ 2|eq(2) ? "y" : "n" }}', array(), '3|y' ),
            'first_set with a set input'  => array( "{{ 'in'|first_set('param') }}", array(), 'param' ),
            'first_set as function'       => array( "{{ first_set(nope, 'x') }}", array(), 'x' ),
            'cond'                        => array( "{{ cond(false, 1, 2) }}", array(), '2' ),
            'contains'                    => array( "{{ ['a','b']|contains('b') ? 'yes' : 'no' }}", array(), 'yes' ),
            'object attribute'            => array( '{{ node.name|wash }}', array( 'node' => $node ), 'Node &lt;b&gt;' ),
            'object chain'                => array( '{{ node.data_map.title.content }}', array( 'node' => $node ), 'T' ),
            'object null attribute'       => array( '[{{ node.empty }}][{{ node.no_such }}]', array( 'node' => $node ), '[][]' ),
            'object loop'                 => array( '{% for c in node.children %}{{ c.name }}{% endfor %}', array( 'node' => $node ), 'c1c2' ),
            'object to operator'          => array( '{{ node.children|count }}', array( 'node' => $node ), '2' ),
            'expattr after an operator'   => array( "{{ expattr(fetch_value, 'k') }}{{ ({'k': 'v'}).k }}", array( 'fetch_value' => array( 'k' => 'w' ) ), 'wv' ),
            'expvar global'               => array( "{{ expvar('g', 'GlobalNs') }}", array(), '' ),
            'expwhile'                    => array( "{% set n = 0 %}{% expwhile n < 3 %}{{ n }}{% set n = n + 1 %}{% endexpwhile %}", array(), '012' ),
            'exprunonce'                  => array( "{% for i in [1,2] %}{% exprunonce id=1 %}once{% endexprunonce %}{% endfor %}", array(), 'once' ),
            'unknown exp_ operator passes input' => array( "{{ 'x'|exp_no_such_operator }}", array(), 'x' ),
            // translated when the template is compiled, as the template compiler does for a .tpl
            'i18n constant'               => array( "{{ 'Close'|i18n('design/standard/content') }}", array(), 'Close' ),
            'i18n with arguments'         => array( "{{ 'a %1 b %name'|i18n('test/context', '', ['X', {'%name': 'N'}|first]) }}|{{ 'a %1'|i18n('test/context', '', [v]) }}", array( 'v' => 'V' ), 'a X b %name|a V' ),
            'i18n of a variable'          => array( "{{ t|i18n('design/standard/content') }}", array( 't' => 'Close' ), 'Close' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('renderProvider')]
    public function testRender( $source, array $variables, $expected )
    {
        $this->assertSame( $expected, $this->render( $source, $variables ) );
    }

    public function testHtmlTwigEndingRendersToo()
    {
        $this->assertSame( 'html x', $this->render( 'html {{ a }}', array( 'a' => 'x' ), '.html.twig' ) );
    }

    public function testWriteBackToTheCallingTemplate()
    {
        $twig = $this->file( "{% set twig_written_back = 'from twig' %}{% set twig_removed = null %}{% set __helper = 1 %}", '.twig' );
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'twig_removed', 'there' );
        $output = $tpl->fetch( $this->file( "{exp_twig_render( '$twig' )}[{\$twig_written_back}]", '.tpl' ) );
        $this->assertSame( '[from twig]', trim( $output ) );
        $this->assertSame( 'from twig', $tpl->variable( 'twig_written_back' ) );
        $this->assertFalse( $tpl->hasVariable( 'twig_removed' ), 'set to null: removed' );
        $this->assertFalse( $tpl->hasVariable( '__helper' ), 'helper variables stay in Twig' );
        $tpl->unsetVariable( 'twig_written_back' );
        // a capture is the text, not Twig's Markup object
        $twig = $this->file( "{% set captured %}<b>{{ 1 }}</b>{% endset %}", '.twig' );
        $tpl->fetch( $this->file( "{exp_twig_render( '$twig' )}", '.tpl' ) );
        $this->assertSame( '<b>1</b>', $tpl->variable( 'captured' ) );
        $tpl->unsetVariable( 'captured' );
    }

    public function testIncludesInEveryDirection()
    {
        $leafTpl = $this->file( '{def $from_tpl = concat( "tpl:", $v )}[tpl {$v}]', '.tpl', 'leaf-tpl' );
        $leafTwig = $this->file( "{% set from_twig = 'twig:' ~ v %}[twig {{ v }}]", '.twig', 'leaf-twig' );
        $named = $this->file( '[named {$v}]', '.tpl', 'leaf-named' );
        $parent = $this->file( "{% expinclude uri='file:$leafTwig' v=1 %}{{ from_twig }}|{% expinclude uri='file:$leafTpl' v=2 %}{{ from_tpl }}|" .
                               "{% expinclude uri='file:$named' name='Ns' v=3 %}", '.twig', 'parent' );
        // a .twig included in the shared namespace writes back what it sets (as a Smarty subtemplate does); a {def} of
        // an included .tpl is local to that file and gone when it ends, as in the template system
        $this->assertSame( '[twig 1]twig:1|[tpl 2]|[named 3]', $this->render( file_get_contents( $parent ) ) );
    }

    public function testScopedSetAndSetBlock()
    {
        $tpl = eZTemplate::factory();
        $tpl->fetch( $this->file( "{% expset name='scoped_global' value='g' scope='global' %}{% expsetblock variable='block_global' scope='global' %}B{% endexpsetblock %}" .
                                  "{% expappendblock variable='appended' scope='global' %}1{% endexpappendblock %}{% expappendblock variable='appended' scope='global' %}2{% endexpappendblock %}", '.twig' ) );
        $this->assertSame( 'g', $tpl->variable( 'scoped_global' ) );
        $this->assertSame( 'B', $tpl->variable( 'block_global' ) );
        $this->assertSame( array( '1', '2' ), $tpl->variable( 'appended' ) );
        foreach ( array( 'scoped_global', 'block_global', 'appended' ) as $name )
            $tpl->unsetVariable( $name );
    }

    public function testWhileLoopLimit()
    {
        $ini = eZINI::instance( 'twig.ini' );
        $saved = $ini->variable( 'TwigSettings', 'WhileLoopLimit' );
        $ini->setVariable( 'TwigSettings', 'WhileLoopLimit', '5' );
        try
        {
            $this->assertSame( '12345', $this->render( "{% set n = 0 %}{% expwhile true %}{% set n = n + 1 %}{{ n }}{% endexpwhile %}" ) );
        }
        finally
        {
            $ini->setVariable( 'TwigSettings', 'WhileLoopLimit', $saved );
        }
    }

    public function testStubCacheKeyAndRegistry()
    {
        foreach ( array( 'design/x/templates/a.twig', 'design/x/templates/a.html.twig' ) as $file )
        {
            $this->assertTrue( expTwigRenderer::isTwigTemplate( $file ) );
            $this->assertSame( 'twig', expTemplateEngines::engineFor( $file ) );
            $this->assertSame( "{exp_twig_render( '$file' )}", expTemplateEngines::stubFor( $file ) );
        }
        $this->assertSame( '.html.twig', expTemplateEngines::suffixOf( 'a.html.twig' ), 'the longest ending first' );
        $this->assertSame( '.twig', expTemplateEngines::suffixOf( 'a.twig' ) );
        $this->assertSame( 'stpl', expTemplateEngines::engineFor( 'a.stpl' ) );
        $this->assertNull( expTemplateEngines::engineFor( 'a.tpl' ) );
        $this->assertNull( expTemplateEngines::engineFor( '.twig' ), 'an ending alone is no template' );
        $this->assertStringStartsWith( '<!--', expTwigRenderer::stubFor( "a'b.twig" ) );

        $resource = new eZTemplateFileResource();
        $extra = array();
        foreach ( array( 'a.twig', 'a.html.twig' ) as $name )
        {
            $key = $resource->cacheKey( "file:$name", 'file', $name, $extra );
            $this->assertNotSame( md5( "file:$name" ), $key, 'a stub never shares a compiled file with a parse of the raw text' );
            $this->assertSame( expTwigRenderer::cacheKey( "file:$name", $name ), $key );
        }
        $this->assertNotSame( $resource->cacheKey( 'file:a.stpl', 'file', 'a.stpl', $extra ), $resource->cacheKey( 'file:a.twig', 'file', 'a.twig', $extra ) );
        $this->assertTrue( expTemplateBridge::hasOperator( 'exp_twig_render' ), 'settings/site.ini AutoloadPathList[] lists lib/twig/classes/' );
    }

    public function testAChangedTemplateGetsANewClassUnderAPersistentWorker()
    {
        $file = $this->file( 'one', '.twig', 'changing' );
        $this->assertSame( 'one', $this->render( 'one', array(), '.twig' ) );
        $twig = expTwigRenderer::twig();
        $name = expTwigRenderer::templateName( $twig, realpath( $file ) );
        $before = $twig->getTemplateClass( $name );
        touch( $file, time() + 5 );
        clearstatcache();
        $this->assertSame( $before, $twig->getTemplateClass( $name ), 'within a page each file is looked at once' );
        $twig->pageStart();
        $this->assertNotSame( $before, $twig->getTemplateClass( $name ), 'with AutoReload the modification time is part of the class name' );
    }

    public function testConstantTranslationsAreCompiledIn()
    {
        $twig = expTwigRenderer::compileCheck( "{{ 'Close'|i18n('design/standard/content') }}", 'c.twig' );
        $this->assertNull( $twig );
        $code = expTwigRenderer::checkEnvironment()->compileSource( new \Twig\Source( "{{ 'Close'|i18n('design/standard/content') }}", 'c.twig' ) );
        $this->assertStringNotContainsString( "'i18n'", $code, 'no operator call left' );
        $this->assertStringContainsString( '"Close"', $code );
        $this->assertSame( 'expi18nargs', $this->extensionFunctionName() );
    }

    /**
     * Bridged operators, template functions, the native operators and the helpers are compiled as direct static calls:
     * the compiled template never looks a filter or function up in the environment, which would make Twig set up all
     * of its extensions on every request.
     */
    public function testBridgedCallsAreCompiledAsStaticCalls()
    {
        $source = "{{ name|ezurl }}{{ fetch('content', 'node', {node_id: 2}) }}{{ attribute_view_gui({attribute: a}) }}"
                . "{{ list|implode(',') }}{{ cond(a, 'x', 'y') }}{{ expvar('x') }}{% for k, v in expsectionloop(3) %}{% endfor %}";
        $this->assertNull( expTwigRenderer::compileCheck( $source, 'calls.twig' ) );
        $this->assertNull( expTwigRenderer::compileCheck( $source, 'calls.twig' ) );
        $code = expTwigRenderer::checkEnvironment()->compileSource( new \Twig\Source( $source, 'calls.twig' ) );
        $this->assertStringNotContainsString( 'getFilter(', $code );
        $this->assertStringNotContainsString( 'getFunction(', $code );
        $this->assertStringContainsString( "expTwigBridgeCalls::filter(\"ezurl\"", $code );
        $this->assertStringContainsString( "expTwigBridgeCalls::call(\"fetch\"", $code );
        $this->assertStringContainsString( "expTwigBridgeCalls::templateFunction(\"attribute_view_gui\"", $code );
        $this->assertStringContainsString( 'expTwigFastOperators::filter_implode(', $code );
    }

    /**
     * Compiled templates are written with the permissions of the installation's other cache files, whatever the
     * umask of the process that compiles them (Velocity's workers run as root with umask 022).
     */
    public function testCompiledTemplatesAreWrittenWithTheInstallationsPermissions()
    {
        $dir = eZSys::rootDir() . '/var/tmp/twig-cache-permissions-' . getmypid();
        $old = umask( 022 );
        try
        {
            $cache = new expTwigFilesystemCache( $dir );
            $key = $cache->generateKey( 'x.twig', 'expTwigPermissionsProbe' );
            $cache->write( $key, '<?php ' );
            $expected = 0777 & ~eZFile::creationUmask();
            $this->assertSame( decoct( $expected ), decoct( fileperms( dirname( $key ) ) & 0777 ) );
        }
        finally
        {
            umask( $old );
            foreach ( (array)glob( $dir . '/*/*' ) as $file )
                @unlink( $file );
            foreach ( (array)glob( $dir . '/*' ) as $sub )
                @rmdir( $sub );
            @rmdir( $dir );
        }
    }

    private function extensionFunctionName()
    {
        return expTwigRenderer::checkEnvironment()->getFunction( 'expi18nargs' )->getName();
    }

    public function testBlockFunctionsBecomeTags()
    {
        $this->assertFalse( expTwigExtension::tag( 'section' ), 'section is written by the converter itself' );
        $this->assertFalse( expTwigExtension::tag( 'no_such_function' ) );
        $this->assertSame( 'wash', expTwigExtension::filter( 'wash' )->getName() );
        $this->assertFalse( expTwigExtension::filter( 'no_such_operator_at_all' ) );
        $this->assertTrue( expTwigExtension::isBridgedFunction( 'attribute_view_gui' ) );
        $this->assertSame( 'wash', expTwigExtension::operatorFor( 'exp_wash' ) );
    }

    public static function converterProvider()
    {
        return array(
            'variable'          => array( '{$node.name|wash}', '{{ node.name|wash }}' ),
            'foreach'           => array( '{foreach $list as $i}{$i}{/foreach}', "{% for i in list %}\n{{ i }}{% endfor %}" ),
            'twig word operator' => array( '{and($a, $b)}', '{{ exp_and(a, b) }}' ),
            'twig filter name'  => array( '{$a|merge($b)}', '{{ a|exp_merge(b) }}' ),
            'function call'     => array( '{attribute_view_gui attribute=$a}', "{{ attribute_view_gui({'attribute': a}) }}" ),
            'include'           => array( "{include uri='design:x.tpl' a=1}", "{% expinclude uri='design:x.tpl' a=1 %}" ),
            'braces in text'    => array( '{literal}a{{b}}{/literal}', "a{{ '{' }}{b}}" ),
            'global variable'   => array( '{$#g}', "{{ expvar('g', '') }}" ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('converterProvider')]
    public function testConverter( $tplSource, $expected )
    {
        $converter = new expTwigConverter( eZTemplate::factory() );
        $this->assertSame( $expected, trim( $converter->convert( $tplSource, 'test.tpl' ) ) );
    }

    public function testEveryConvertedConstructCompiles()
    {
        $converter = new expTwigConverter( eZTemplate::factory() );
        $source = "{def \$x = array( 'a', 'b' )}{foreach \$x|append( 'c' ) as \$i}{\$i}{delimiter},{/delimiter}{/foreach}" .
                  "{if \$x|contains( 'a' )}A{/if}{\$x|first_set( 'z' )}{undef \$x}";
        $twig = $converter->convert( $source, 'test.tpl' );
        $this->assertNull( expTwigRenderer::compileCheck( $twig, 'test.twig' ) );
        $this->assertSame( 'a,b,cAz', trim( $this->render( $twig ) ) );
    }
}
