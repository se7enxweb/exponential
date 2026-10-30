<?php
/**
 * The Twig sandbox (twig.ini Security, on by default, expTwigSecurity): every bridged operator, template function and
 * exp* tag is allowed, Twig's own tags, filters and functions only as listed, no method of an object and no property
 * unless allowed, values of the template system read through their attributes; templates compiled with the sandbox
 * are kept apart from those compiled without it.
 *
 * Twig 3 from Composer, or TWIG_LIBRARY_PATH; without it the cases are skipped. No database and no siteaccess.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group twig
 */

require_once __DIR__ . '/expTwigTemplateRenderTest.php';

class expTwigSecurityTest extends PHPUnit\Framework\TestCase
{
    private static $dir;

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-twig-security-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
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

    private function render( $source, array $variables = array() )
    {
        $file = self::$dir . '/' . md5( $source ) . '.twig';
        file_put_contents( $file, $source );
        $tpl = eZTemplate::factory();
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        $output = $tpl->fetch( $file );
        foreach ( array_keys( $variables ) as $name )
            $tpl->unsetVariable( $name );
        return $output;
    }

    public function testTheSandboxIsOnByDefault()
    {
        $this->assertSame( 'enabled', eZINI::instance( 'twig.ini' )->variable( 'TwigSettings', 'Security' ) );
        $this->assertTrue( expTwigRenderer::twig()->hasExtension( \Twig\Extension\SandboxExtension::class ) );
        $this->assertStringEndsWith( '/twig/compile-secure', expTemplateEngineInfo::engines()['twig']['compiled'] );
    }

    public static function refusedProvider()
    {
        return array(
            'a tag not listed'      => array( '{% do 1 %}x' ),
            'a filter not listed'   => array( '{{ [1]|filter(v => v) }}x' ),
            'a function not listed' => array( "{{ constant('PHP_VERSION') }}x" ),
            'a method'              => array( '{{ obj.secret() }}x' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('refusedProvider')]
    public function testRefused( $source )
    {
        $output = $this->render( $source, array( 'obj' => new expTwigTestObject( array( 'name' => 'n' ) ) ) );
        $this->assertStringNotContainsString( 'x', strip_tags( preg_replace( '/<!--.*?-->/s', '', $output ) ), 'the template does not run' );
    }

    public function testAllowed()
    {
        $object = new expTwigTestObject( array( 'name' => 'n' ) );
        $this->assertSame( 'N|n|ab|2', $this->render( "{{ obj.name|upcase }}|{{ obj.name }}|{{ concat('a', 'b') }}|{{ [1,2]|length }}", array( 'obj' => $object ) ) );
        $this->assertSame( 'ok', $this->render( "{% set a = 1 %}{% if a %}{% for i in [1] %}ok{% endfor %}{% endif %}" ) );
    }

    public function testWithoutTheSandboxEverythingRunsCompiledApart()
    {
        $ini = eZINI::instance( 'twig.ini' );
        $ini->setVariable( 'TwigSettings', 'Security', 'disabled' );
        expTwigRenderer::resetSettings();
        try
        {
            $this->assertSame( 'x', $this->render( '{% do 1 %}x' ) );
            $this->assertFalse( expTwigRenderer::twig()->hasExtension( \Twig\Extension\SandboxExtension::class ) );
            $this->assertStringEndsWith( '/twig/compile', expTemplateEngineInfo::engines()['twig']['compiled'] );
        }
        finally
        {
            $ini->setVariable( 'TwigSettings', 'Security', 'enabled' );
            expTwigRenderer::resetSettings();
        }
    }

    public function testThePolicyLists()
    {
        $policy = expTwigSecurity::fromSettings( eZINI::instance( 'twig.ini' ) );
        $policy->checkSecurity( array( 'if', 'expinclude', 'expcacheblock' ), array( 'wash', 'exp_implode', 'length' ), array( 'fetch', 'attribute_view_gui', 'expvar' ) );
        $this->expectException( \Twig\Sandbox\SecurityNotAllowedFilterError::class );
        $policy->checkSecurity( array(), array( 'map' ), array() );
    }
}
