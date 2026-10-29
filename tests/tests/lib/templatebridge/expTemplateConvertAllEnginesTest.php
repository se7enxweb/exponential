<?php
/**
 * Every conversion direction between the three template engines with the 453 cases of the template engine's own tests
 * (eZTemplateEngineRenderTest, eZTemplateOperatorMatrixTest): each .tpl case goes tpl -> stpl -> twig (Smarty to Twig
 * through .tpl, in one run) and tpl -> twig -> stpl, each result is rendered by its own engine, and back to .tpl again;
 * every rendering gives the output the case expects.
 *
 * Smarty 5 and Twig 3 from Composer, or SMARTY_LIBRARY_PATH and TWIG_LIBRARY_PATH; without them the cases are skipped.
 * No database and no siteaccess.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group smarty
 * @group twig
 */

require_once __DIR__ . '/../eztemplate/eZTemplateEngineRenderTest.php';
require_once __DIR__ . '/../eztemplate/eZTemplateOperatorMatrixTest.php';
require_once __DIR__ . '/../smarty/expSmartyOperatorParityTest.php';

class expTemplateConvertAllEnginesTest extends PHPUnit\Framework\TestCase
{
    private static $dir;

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-all-engines-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir, 0777, true );
        expSmartyOperatorParityTest::trustTestDirectory( self::$dir );
        if ( !class_exists( '\Smarty\Smarty' ) && getenv( 'SMARTY_LIBRARY_PATH' ) )
            eZINI::instance( 'smarty.ini' )->setVariable( 'SmartySettings', 'LibraryPath', getenv( 'SMARTY_LIBRARY_PATH' ) );
        if ( !class_exists( '\Twig\Environment' ) && getenv( 'TWIG_LIBRARY_PATH' ) )
            eZINI::instance( 'twig.ini' )->setVariable( 'TwigSettings', 'LibraryPath', getenv( 'TWIG_LIBRARY_PATH' ) );
    }

    public static function tearDownAfterClass(): void
    {
        foreach ( glob( self::$dir . '/*' ) as $file )
            unlink( $file );
        rmdir( self::$dir );
    }

    protected function setUp(): void
    {
        if ( !expSmartyRenderer::loadLibrary() || !expTwigRenderer::loadLibrary() )
            $this->markTestSkipped( 'Smarty 5 and Twig 3 are needed (SMARTY_LIBRARY_PATH, TWIG_LIBRARY_PATH)' );
    }

    public static function caseProvider()
    {
        $rows = array();
        foreach ( eZTemplateEngineRenderTest::renderProvider() as $name => $case )
            $rows["engine: $name"] = array( $case[0], $case[1], $case[2] );
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

    private function convert( $from, $to, $text )
    {
        $converter = new expTemplateDesignConverter( $from, $to );
        list( $converted ) = $converter->convertText( $text, "case.$from" );
        return $converted;
    }

    private function render( $text, $suffix, array $variables )
    {
        $file = self::$dir . '/' . md5( $text ) . $suffix;
        if ( !file_exists( $file ) )
            file_put_contents( $file, $text );
        $tpl = eZTemplate::factory();
        // each rendering starts from the same variables: what one writes back (append-block ...) is not seen by the next
        $saved = $tpl->Variables;
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        $output = $tpl->fetch( $file );
        $tpl->Variables = $saved;
        return $output;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('caseProvider')]
    public function testEveryPathRendersTheSame( $tplSource, array $variables, $expected )
    {
        // tpl -> stpl -> twig -> tpl
        $stpl = $this->convert( 'tpl', 'stpl', $tplSource );
        $twigFromSmarty = $this->convert( 'stpl', 'twig', $stpl );
        $this->assertSame( $expected, $this->render( $twigFromSmarty, '.twig', $variables ), "stpl -> twig\nstpl: $stpl\ntwig: $twigFromSmarty" );
        $this->assertSame( $expected, $this->render( $this->convert( 'twig', 'tpl', $twigFromSmarty ), '.tpl', $variables ), 'tpl -> stpl -> twig -> tpl' );

        // tpl -> twig -> stpl -> tpl
        $twig = $this->convert( 'tpl', 'twig', $tplSource );
        $stplFromTwig = $this->convert( 'twig', 'stpl', $twig );
        $this->assertSame( $expected, $this->render( $stplFromTwig, '.stpl', $variables ), "twig -> stpl\ntwig: $twig\nstpl: $stplFromTwig" );
        $this->assertSame( $expected, $this->render( $this->convert( 'stpl', 'tpl', $stplFromTwig ), '.tpl', $variables ), 'tpl -> twig -> stpl -> tpl' );
    }
}
