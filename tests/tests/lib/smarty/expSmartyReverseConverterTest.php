<?php
/**
 * The Smarty -> tpl converter (expSmartyReverseConverter):
 *
 *  - every .tpl case of the template engine's own tests (eZTemplateEngineRenderTest::renderProvider() and
 *    eZTemplateOperatorMatrixTest::caseTable()) converted tpl -> stpl -> tpl renders, in the template system, the
 *    output the case expects; converting the result to .stpl again gives the first .stpl (fixpoint), and back again
 *    the same .tpl (idempotent);
 *  - hand-written Smarty converts to a .tpl that prints what the case expects, and what has no exact .tpl form is a
 *    note and a comment.
 *
 * The conversion of whole designs and the console commands are tested in
 * tests/tests/kernel/private/classes/commands/expTemplateConvertCommandTest.php.
 *
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
require_once __DIR__ . '/expSmartyOperatorParityTest.php';

class expSmartyReverseConverterTest extends PHPUnit\Framework\TestCase
{
    /** @var string directory of this class's files */
    private static $dir;

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-smarty-reverse-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir, 0777, true );
        expSmartyOperatorParityTest::trustTestDirectory( self::$dir );
        $libraryPath = getenv( 'SMARTY_LIBRARY_PATH' );
        if ( !class_exists( '\Smarty\Smarty' ) && $libraryPath )
            eZINI::instance( 'smarty.ini' )->setVariable( 'SmartySettings', 'LibraryPath', $libraryPath );
    }

    public static function tearDownAfterClass(): void
    {
        self::removeTree( self::$dir );
    }

    private static function removeTree( $path )
    {
        if ( !$path || !file_exists( $path ) )
            return;
        if ( is_dir( $path ) && !is_link( $path ) )
        {
            foreach ( scandir( $path ) as $entry )
            {
                if ( $entry !== '.' && $entry !== '..' )
                    self::removeTree( $path . '/' . $entry );
            }
            rmdir( $path );
        }
        else
            unlink( $path );
    }

    /**
     * Renders a .tpl text in a template of its own, as eZTemplateEngineRenderTest does.
     */
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
        $stpl = ( new expSmartyConverter( $tpl ) )->convert( $tplSource, 'case.tpl' );
        $reverse = new expSmartyReverseConverter( $tpl );
        $back = $reverse->convert( $stpl, 'case.stpl' );
        $this->assertSame( array(), $reverse->warnings(), "stpl: $stpl" );
        $this->assertSame( $expected, $this->renderTpl( $back, $variables ), "tpl: $tplSource\nstpl: $stpl\ntpl again: $back" );

        // converting again changes nothing
        $stpl2 = ( new expSmartyConverter( $tpl ) )->convert( $back, 'case.tpl' );
        $this->assertSame( $stpl, $stpl2, "the .stpl of the .tpl again: tpl again: $back" );
        $this->assertSame( $back, ( new expSmartyReverseConverter( $tpl ) )->convert( $stpl2, 'case.stpl' ) );
    }

    public static function smartyProvider()
    {
        return array(
            'operators'          => array( "{\$x = 2}{if \$x == 2 && \$a > 0}A{elseif !\$a}B{else}C{/if}", 'A' ),
            'arithmetic'         => array( "{\$a + 2 * 3}|{(\$a > 0) ? 'y' : 'n'}|{-\$a}", '7|y|-1' ),
            'string interpolation' => array( "{\$s = \"a \$a b\"}{\$s}", 'a 1 b' ),
            'foreach key, @last, foreachelse' => array( "{foreach \$list as \$k => \$v}{\$k}={\$v|upper}{if \$v@last}.{else},{/if}{foreachelse}none{/foreach}", '0=X,1=Y.' ),
            'foreachelse'        => array( "{foreach [] as \$v}{\$v}{foreachelse}none{/foreach}", 'none' ),
            '@index @iteration @first' => array( "{foreach \$list as \$v}{\$v@index}{\$v@iteration}{if \$v@first}F{/if}{/foreach}", '01F12' ),
            'delimiter'          => array( "{foreach \$list as \$v}{if !\$v@first}, {/if}{\$v}{/foreach}", 'x, y' ),
            'for'                => array( "{for \$i = 1 to 3}{\$i}{/for}{for \$i = 3 to 1}{\$i}{/for}", '123' ),
            'while'              => array( "{\$i = 0}{while \$i < 3}{\$i}{\$i = \$i + 1}{/while}", '012' ),
            'capture'            => array( "{capture assign=c}X{\$a}{/capture}[{\$c}]", '[X1]' ),
            'append'             => array( "{\$l = []}{\$l[] = 1}{\$l[] = 2}{\$l|exp_implode:','}", '1,2' ),
            'def then set'       => array( "{\$a = 1}{\$b = 2}{\$a = 3}{\$b = 4}{\$a}{\$b}{\$a = null}{\$b = null}", '34' ),
            'lookups'            => array( "{\$h.k}{\$h['k']}{\$list[1]}{\$list.0}", 'vvyx' ),
            'arrays'             => array( "{['a' => 1, 'b' => 2]|json_encode_is_not_used_here|escape}", null ),
            'comment, braces, literal' => array( "{* comment *}\nT{ldelim}x{rdelim}{literal}{a}{/literal}{ \$a}", 'T{x}{a}{ $a}' ),
            'newlines after tags' => array( "{if true}\nA\n{/if}\nB\n{\$x = 1}\n\nC{\$x}\n", "A\nB\n\nC1\n" ),
            'leading newline'    => array( "\n\nX", "\nX" ),
            'exp_ prefix'        => array( "{\$list|exp_count}{'a'|exp_upcase}", '2A' ),
            'default'            => array( "{\$a|default:'x'}{\$nope|default:'d'}{''|default:'e'}{\$h.k|default:0}", '1dev' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('smartyProvider')]
    public function testHandWrittenSmarty( $stpl, $expected )
    {
        $reverse = new expSmartyReverseConverter( eZTemplate::factory() );
        $tpl = $reverse->convert( $stpl, 'hand.stpl' );
        $variables = array( 'a' => 1, 'list' => array( 'x', 'y' ), 'h' => array( 'k' => 'v' ) );
        if ( $expected === null )
        {
            // no exact .tpl form: a note, and the Smarty source kept as a comment
            $this->assertNotEmpty( $reverse->warnings() );
            $this->assertStringContainsString( '{* SMARTY (not converted:', $tpl );
            $this->assertSame( '', $this->renderTpl( $tpl, $variables ) );
            return;
        }
        $this->assertSame( array(), $reverse->warnings(), $tpl );
        $this->assertSame( $expected, $this->renderTpl( $tpl, $variables ), "stpl: $stpl\ntpl: $tpl" );
        $this->assertSame( $tpl, ( new expSmartyReverseConverter( eZTemplate::factory() ) )->convert( $stpl, 'hand.stpl' ), 'the same result twice' );
    }

    public static function notConvertedProvider()
    {
        return array(
            'escape'          => array( "{\$a|escape}" ),
            '$smarty'         => array( "{\$smarty.now}" ),
            'strict compare'  => array( "{if \$a === 1}A{/if}" ),
            'method call'     => array( "{\$a->foo()}" ),
            'smarty include'  => array( "{include file='x.tpl'}" ),
            'array element assignment' => array( "{\$a.b = 1}" ),
            'unknown modifier' => array( "{\$a|no_such_modifier_anywhere}" ),
            'smarty section'  => array( "{section name=s loop=\$list}{\$list[s]}{/section}" ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('notConvertedProvider')]
    public function testWhatHasNoTplFormIsANoteAndAComment( $stpl )
    {
        $reverse = new expSmartyReverseConverter( eZTemplate::factory() );
        $tpl = $reverse->convert( "<p>{$stpl}</p>", 'hand.stpl' );
        $this->assertCount( 1, $reverse->warnings(), $tpl );
        $this->assertStringContainsString( 'kept as a comment', $reverse->warnings()[0] );
        $this->assertStringStartsWith( '<p>{* SMARTY (not converted: ', $tpl );
        $this->assertSame( '<p></p>', $this->renderTpl( $tpl, array( 'a' => 1, 'list' => array( 'x' ) ) ) );
    }
}
