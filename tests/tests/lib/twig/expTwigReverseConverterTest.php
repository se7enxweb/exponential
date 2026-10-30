<?php
/**
 * The Twig -> tpl converter (expTwigReverseConverter):
 *
 *  - every .tpl case of the template engine's own tests (eZTemplateEngineRenderTest::renderProvider() and
 *    eZTemplateOperatorMatrixTest::caseTable()) converted tpl -> twig -> tpl renders, in the template system, the
 *    output the case expects; converting the result to .twig again gives the first .twig (fixpoint), and back again
 *    the same .tpl (idempotent);
 *  - hand-written Twig converts to a .tpl that prints what the case expects, and what has no exact .tpl form is a
 *    note and a comment.
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

class expTwigReverseConverterTest extends PHPUnit\Framework\TestCase
{
    /** @var string directory of this class's files */
    private static $dir;

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-twig-reverse-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
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

    /** Renders a .tpl text in a template of its own, as eZTemplateEngineRenderTest does. */
    private function renderTpl( $source, array $variables )
    {
        $file = self::$dir . '/' . md5( $source ) . '.tpl';
        if ( !file_exists( $file ) )
            file_put_contents( $file, $source );
        $tpl = new eZTemplate();
        $tpl->autoload();
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        return $tpl->fetch( $file );
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

    #[\PHPUnit\Framework\Attributes\DataProvider('caseProvider')]
    public function testRoundTripRendersTheSameOutput( $tplSource, array $variables, $expected )
    {
        $tpl = eZTemplate::factory();
        $twig = ( new expTwigConverter( $tpl ) )->convert( $tplSource, 'case.tpl' );
        $reverse = new expTwigReverseConverter( $tpl );
        $back = $reverse->convert( $twig, 'case.twig' );
        $this->assertSame( array(), $reverse->warnings(), "twig: $twig" );
        $this->assertSame( $expected, $this->renderTpl( $back, $variables ), "tpl: $tplSource\ntwig: $twig\ntpl again: $back" );

        // one round later nothing changes any more: the .tpl and the .twig are stable (the first round may differ in
        // form only: Twig reads {{ 'q' }} as the text q)
        $twig2 = ( new expTwigConverter( $tpl ) )->convert( $back, 'case.tpl' );
        $back2 = ( new expTwigReverseConverter( $tpl ) )->convert( $twig2, 'case.twig' );
        $this->assertSame( $expected, $this->renderTpl( $back2, $variables ), "tpl again: $back\ntwig again: $twig2\ntpl the third time: $back2" );
        $twig3 = ( new expTwigConverter( $tpl ) )->convert( $back2, 'case.tpl' );
        $this->assertSame( $twig2, $twig3, "the .twig is stable: tpl again: $back\ntpl the third time: $back2" );
        $this->assertSame( $back2, ( new expTwigReverseConverter( $tpl ) )->convert( $twig3, 'case.twig' ), 'the .tpl is stable' );
    }

    public static function twigProvider()
    {
        return array(
            'operators'        => array( "{% set x = 2 %}{% if x == 2 and a > 0 %}A{% elseif not a %}B{% else %}C{% endif %}", 'A' ),
            'arithmetic'       => array( "{{ a + 2 * 3 }}|{{ a > 0 ? 'y' : 'n' }}|{{ -a }}", '7|y|-1' ),
            'concat'           => array( "{{ 'a' ~ a ~ 'b' }}", 'a1b' ),
            'for, loop, else'  => array( "{% for k, v in list %}{{ k }}={{ v|upper }}{% if loop.last %}.{% else %},{% endif %}{% else %}none{% endfor %}", '0=X,1=Y.' ),
            'for else empty'   => array( "{% for v in [] %}{{ v }}{% else %}none{% endfor %}", 'none' ),
            'loop.index'       => array( "{% for v in list %}{{ loop.index }}{{ v }}{% endfor %}", '1x2y' ),
            'is defined'       => array( "{% if nothing is defined %}y{% else %}n{% endif %}{% if a is defined %}Y{% endif %}", 'nY' ),
            'null coalesce'    => array( "{{ nothing ?? 'd' }}", 'd' ),
            'in'               => array( "{% if 'x' in list %}in{% endif %}", 'in' ),
            'capture'          => array( "{% set c %}[{{ a }}]{% endset %}{{ c }}", '[1]' ),
            'hash and array'   => array( "{% set h = {'k': 'v', 'n': [1, 2]} %}{{ h.k }}{{ h.n|length }}", 'v2' ),
            'length and join'  => array( "{{ list|length }}{{ list|join('-') }}", '2x-y' ),
        );
    }

    /**
     * The .twig is read as written: a constant i18n (which the running environment translates when it compiles the
     * template) stays an i18n call in the .tpl, so the .tpl translates in every language.
     */
    public function testAConstantI18nStaysAnI18nCall()
    {
        $reverse = new expTwigReverseConverter( eZTemplate::factory() );
        $tplSource = $reverse->convert( "{{ 'More'|i18n('design/standard/content') }}", 'i18n.twig' );
        $this->assertStringContainsString( "'More'|i18n('design/standard/content')", $tplSource );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('twigProvider')]
    public function testHandWrittenTwig( $twig, $expected )
    {
        $reverse = new expTwigReverseConverter( eZTemplate::factory() );
        $tplSource = $reverse->convert( $twig, 'hand.twig' );
        $this->assertSame( array(), $reverse->warnings(), "tpl: $tplSource" );
        $this->assertSame( $expected, $this->renderTpl( $tplSource, array( 'a' => 1, 'list' => array( 'x', 'y' ) ) ), "twig: $twig\ntpl: $tplSource" );
    }

    public function testWhatHasNoTemplateFormIsANoteAndAComment()
    {
        $reverse = new expTwigReverseConverter( eZTemplate::factory() );
        $tplSource = $reverse->convert( "A{{ a|default('x') }}B{{ template_from_string('x') }}C", 'hand.twig' );
        $this->assertCount( 2, $reverse->warnings() );
        $this->assertStringContainsString( '{* TWIG (not converted:', $tplSource );
        $this->assertSame( 'ABC', $this->renderTpl( $tplSource, array( 'a' => 1 ) ) );
    }

    public function testHtmlTwigSuffixAndMarker()
    {
        $converter = new expTemplateDesignConverter( 'twig', 'tpl' );
        list( $text, $notes ) = $converter->convertText( "{{ a }}{# exp:template:convert tpl->twig md5:" . md5( '{{ a }}' ) . " from a.tpl -- generated; a file changed by hand is not replaced without --force #}", 'a.html.twig' );
        $this->assertSame( '{$a}', $text );
        $this->assertSame( array(), $notes );
    }
}
