<?php
/**
 * Twig templates give the output of the .tpl they were converted from, for every case of the template engine's own
 * tests: each .tpl case of eZTemplateEngineRenderTest (the parser and every template function) and of
 * eZTemplateOperatorMatrixTest (the operators of every kernel operator class), with its input as a literal and as
 * template variables, is converted with expTwigConverter, rendered as a .twig through the template system
 * (eZTemplate::factory(), the exp_twig_render stub, expTwigRenderer, Twig) and compared with the output the .tpl
 * test expects; once with the sandbox (twig.ini Security=enabled, the default) and once without.
 *
 * Twig 3 from Composer, or TWIG_LIBRARY_PATH; without it the cases are skipped. No database and no siteaccess.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group twig
 */

require_once __DIR__ . '/../eztemplate/eZTemplateEngineRenderTest.php';
require_once __DIR__ . '/../eztemplate/eZTemplateOperatorMatrixTest.php';

class expTwigOperatorParityTest extends PHPUnit\Framework\TestCase
{
    /** @var string directory of this class's template files */
    private static $dir;

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-twig-parity-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
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

    public static function caseProvider()
    {
        $rows = array();
        foreach ( eZTemplateEngineRenderTest::renderProvider() as $name => $case )
        {
            list( $source, $variables, $expected ) = $case;
            $rows["engine: $name"] = array( $source, $variables, $expected );
        }
        foreach ( eZTemplateOperatorMatrixTest::caseTable() as $name => $case )
        {
            list( $literal, $withVariables, $variables, $expected ) = $case;
            if ( $expected === null )
                continue;
            $rows["operator: $name, literal"] = array( $literal, array(), $expected );
            $rows["operator: $name, variables"] = array( $withVariables, $variables, $expected );
        }
        return $rows;
    }

    private function render( $tplSource, array $variables )
    {
        $tpl = eZTemplate::factory();
        $converter = new expTwigConverter( $tpl );
        $twigSource = $converter->convert( $tplSource, 'parity.tpl' );
        $file = self::$dir . '/' . md5( $twigSource ) . '.twig';
        if ( !file_exists( $file ) )
            file_put_contents( $file, $twigSource );
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        $output = $tpl->fetch( $file );
        foreach ( array_keys( $variables ) as $name )
            $tpl->unsetVariable( $name );
        return array( $output, $twigSource );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('caseProvider')]
    public function testConvertedTemplateGivesTheSameOutput( $tplSource, array $variables, $expected )
    {
        if ( !expTwigRenderer::loadLibrary() )
            $this->markTestSkipped( 'Twig 3 is not installed (composer require twig/twig, or TWIG_LIBRARY_PATH)' );
        list( $output, $twigSource ) = $this->render( $tplSource, $variables );
        $this->assertSame( $expected, $output, "tpl: $tplSource\ntwig: $twigSource" );
    }

    /**
     * The same without the sandbox (Security=disabled), compiled apart.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('caseProvider')]
    public function testConvertedTemplateGivesTheSameOutputWithoutTheSandbox( $tplSource, array $variables, $expected )
    {
        if ( !expTwigRenderer::loadLibrary() )
            $this->markTestSkipped( 'Twig 3 is not installed (composer require twig/twig, or TWIG_LIBRARY_PATH)' );
        $ini = eZINI::instance( 'twig.ini' );
        $saved = $ini->variable( 'TwigSettings', 'Security' );
        $ini->setVariable( 'TwigSettings', 'Security', 'disabled' );
        expTwigRenderer::resetSettings();
        try
        {
            list( $output, $twigSource ) = $this->render( $tplSource, $variables );
            $this->assertSame( $expected, $output, "tpl: $tplSource\ntwig: $twigSource" );
        }
        finally
        {
            $ini->setVariable( 'TwigSettings', 'Security', $saved );
            expTwigRenderer::resetSettings();
        }
    }
}
