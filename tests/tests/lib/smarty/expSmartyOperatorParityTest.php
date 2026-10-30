<?php
/**
 * Smarty templates give the output of the .tpl they were converted from, for every case of the template engine's
 * own tests: each .tpl case of eZTemplateEngineRenderTest (the parser and every template function: def/set/undef,
 * if, foreach with its parameters, for, while, do, switch, section, sequence, delimit, literal, set-block,
 * append-block) and of eZTemplateOperatorMatrixTest (the operators of eZTemplateLogicOperator, ...Arithmetic...,
 * ...Array..., ...Type..., ...String..., ...Text..., ...Unit..., ...Digest..., ...Control...), with its input as a
 * literal and as template variables, is converted with expSmartyConverter, rendered as a .stpl through the template
 * system (eZTemplate::factory(), the exp_smarty_render stub, expSmartyRenderer, Smarty) and compared with the output
 * the .tpl test expects.
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

require_once __DIR__ . '/../eztemplate/eZTemplateEngineRenderTest.php';
require_once __DIR__ . '/../eztemplate/eZTemplateOperatorMatrixTest.php';

class expSmartyOperatorParityTest extends PHPUnit\Framework\TestCase
{
    /** @var string directory of this class's template files */
    private static $dir;

    /**
     * The Smarty security policy (on by default) trusts the design template directories only: a test that renders
     * templates from a directory of its own adds it to [SmartySettings] SecurityTrustedDirList[].
     *
     * @param string $dir
     */
    public static function trustTestDirectory( $dir )
    {
        $ini = eZINI::instance( 'smarty.ini' );
        $trusted = $ini->hasVariable( 'SmartySettings', 'SecurityTrustedDirList' ) ? (array)$ini->variable( 'SmartySettings', 'SecurityTrustedDirList' ) : array();
        if ( !in_array( $dir, $trusted, true ) )
            $ini->setVariable( 'SmartySettings', 'SecurityTrustedDirList', array_merge( $trusted, array( $dir ) ) );
        if ( class_exists( 'expSmartyRenderer' ) )
            expSmartyRenderer::resetSettings();
    }

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-smarty-parity-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir, 0777, true );
        self::trustTestDirectory( self::$dir );
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

    #[\PHPUnit\Framework\Attributes\DataProvider('caseProvider')]
    public function testConvertedTemplateGivesTheSameOutput( $tplSource, array $variables, $expected )
    {
        if ( !expSmartyRenderer::loadLibrary() )
            $this->markTestSkipped( 'Smarty 5 is not installed (composer require smarty/smarty, or SMARTY_LIBRARY_PATH)' );

        $tpl = eZTemplate::factory();
        $converter = new expSmartyConverter( $tpl );
        $stplSource = $converter->convert( $tplSource, 'parity.tpl' );
        // one file per source: Smarty keys its compiled files on the file and its modification time
        $file = self::$dir . '/' . md5( $stplSource ) . '.stpl';
        if ( !file_exists( $file ) )
            file_put_contents( $file, $stplSource );

        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        $output = $tpl->fetch( $file );
        foreach ( array_keys( $variables ) as $name )
            $tpl->unsetVariable( $name );

        $this->assertSame( $expected, $output, "tpl: $tplSource\nstpl: $stplSource" );
    }

    /**
     * The same, compiled under the Smarty security policy (smarty.ini Security=enabled with the lists the
     * installation ships, this class's directory trusted): the converted templates use nothing it refuses.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('caseProvider')]
    public function testConvertedTemplateGivesTheSameOutputUnderTheSecurityPolicy( $tplSource, array $variables, $expected )
    {
        if ( !expSmartyRenderer::loadLibrary() )
            $this->markTestSkipped( 'Smarty 5 is not installed (composer require smarty/smarty, or SMARTY_LIBRARY_PATH)' );
        $ini = eZINI::instance( 'smarty.ini' );
        $saved = array( $ini->variable( 'SmartySettings', 'Security' ),
                        $ini->hasVariable( 'SmartySettings', 'SecurityTrustedDirList' ) ? $ini->variable( 'SmartySettings', 'SecurityTrustedDirList' ) : array() );
        $ini->setVariable( 'SmartySettings', 'Security', 'enabled' );
        $ini->setVariable( 'SmartySettings', 'SecurityTrustedDirList', array( self::$dir ) );
        expSmartyRenderer::resetSettings();
        try
        {
            $this->assertNotNull( expSmartyRenderer::smarty()->security_policy );
            $this->testConvertedTemplateGivesTheSameOutput( $tplSource, $variables, $expected );
        }
        finally
        {
            $ini->setVariable( 'SmartySettings', 'Security', $saved[0] );
            $ini->setVariable( 'SmartySettings', 'SecurityTrustedDirList', $saved[1] );
            expSmartyRenderer::resetSettings();
        }
    }
}
