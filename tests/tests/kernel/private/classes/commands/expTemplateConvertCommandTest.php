<?php
/**
 * The template conversion command space: exp:template:convert and the six named directions
 * exp:template:convert:<from>-to-<to> (tpl, stpl, twig; kernel/private/classes/commands/exptemplateconvert*.php),
 * exp:template:convert:diff, and the design converter behind them (expTemplateDesignConverter, lib/templatebridge/classes/):
 *
 *  - every named command maps to its engines, and the console runs it by its name;
 *  - the engine-specific conversion command names of Smarty are unknown commands;
 *  - the engines and their other names, the converter registry (site.ini [TemplateBridgeSettings] ConverterList);
 *  - usage errors exit 2, a direction without a converter exits 3;
 *  - every direction over a design, the Smarty <-> Twig ones through .tpl in one run, .html.twig read and written;
 *  - the marker: in the target engine's comment syntax, at the very end, invalid once the content changes;
 *  - both directions over a design written to var/tmp: a dry run writes nothing, a second run changes nothing, a
 *    file changed by hand is skipped unless forced, --file converts one template;
 *  - with a design named in EXP_TEMPLATE_TEST_DESIGN (EXP_SMARTY_TEST_DESIGN as before, or
 *    extension/sevenx_themes_media/design/media when it exists), the round trip of the whole design through Smarty and
 *    through Twig: no errors, and every template whose source parses is a fixpoint.
 *
 * Smarty 5 and Twig 3 are needed only for the compile check of what the forward directions write (Composer, or
 * SMARTY_LIBRARY_PATH / TWIG_LIBRARY_PATH); without them that check is left out. No database and no siteaccess.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group smarty
 * @group twig
 */

require_once dirname( __DIR__, 4 ) . '/lib/smarty/expSmartyOperatorParityTest.php';

class expTemplateConvertCommandTest extends PHPUnit\Framework\TestCase
{
    /** @var string directory of this class's files */
    private static $dir;

    /** @var string the installation root */
    private static $root;

    /** The named commands: alias => array( bin script, command class, from engine, to engine ). */
    private static $named = array(
        'exp:template:convert:tpl-to-stpl' => array( 'exptemplateconverttpltostpl', 'Exptemplateconverttpltostpl', 'tpl', 'stpl' ),
        'exp:template:convert:stpl-to-tpl' => array( 'exptemplateconvertstpltotpl', 'Exptemplateconvertstpltotpl', 'stpl', 'tpl' ),
        'exp:template:convert:tpl-to-twig' => array( 'exptemplateconverttpltotwig', 'Exptemplateconverttpltotwig', 'tpl', 'twig' ),
        'exp:template:convert:twig-to-tpl' => array( 'exptemplateconverttwigtotpl', 'Exptemplateconverttwigtotpl', 'twig', 'tpl' ),
        'exp:template:convert:stpl-to-twig' => array( 'exptemplateconvertstpltotwig', 'Exptemplateconvertstpltotwig', 'stpl', 'twig' ),
        'exp:template:convert:twig-to-stpl' => array( 'exptemplateconverttwigtostpl', 'Exptemplateconverttwigtostpl', 'twig', 'stpl' ),
    );

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 6 );
        chdir( self::$root );
        self::$dir = 'var/tmp/phpunit-template-convert-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir, 0777, true );
        expSmartyOperatorParityTest::trustTestDirectory( self::$dir );
        $libraryPath = getenv( 'SMARTY_LIBRARY_PATH' );
        if ( !class_exists( '\Smarty\Smarty' ) && $libraryPath )
            eZINI::instance( 'smarty.ini' )->setVariable( 'SmartySettings', 'LibraryPath', $libraryPath );
        $twigPath = getenv( 'TWIG_LIBRARY_PATH' );
        if ( !class_exists( '\Twig\Environment' ) && $twigPath )
            eZINI::instance( 'twig.ini' )->setVariable( 'TwigSettings', 'LibraryPath', $twigPath );
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
     * Runs the console of this installation; returns array( exit code, output without colors ).
     */
    private function console( $arguments )
    {
        $output = array();
        exec( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( self::$root . '/bin/php/console' ) . ' ' . $arguments . ' 2>&1', $output, $code );
        return array( $code, preg_replace( '/\x1b\[[0-9;]*m/', '', implode( "\n", $output ) ) );
    }

    /**
     * Renders a .tpl text in a template of its own.
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

    /**
     * Runs the design converter and returns array( exit code, output lines, error lines, counts ).
     */
    private function runConverter( $from, $to, array $options )
    {
        $out = array();
        $err = array();
        $converter = new expTemplateDesignConverter( $from, $to );
        $code = $converter->run( $options, function ( $line ) use ( &$out ) { $out[] = $line; }, function ( $line ) use ( &$err ) { $err[] = $line; } );
        return array( $code, $out, $err, $converter->counts() );
    }

    /**
     * Runs the work of a command (the engines checked first) and returns array( exit code, output lines, error lines ).
     */
    private function runCommand( array $options, $from = null, $to = null )
    {
        $out = array();
        $err = array();
        $code = expTemplateDesignConverter::runWithEngines( $options, function ( $line ) use ( &$out ) { $out[] = $line; },
                                                            function ( $line ) use ( &$err ) { $err[] = $line; }, $from, $to );
        return array( $code, $out, $err );
    }

    public function testEveryNamedCommandMapsToItsEngines()
    {
        foreach ( self::$named as $alias => $command )
        {
            list( $script, $class, $from, $to ) = $command;
            $classFile = 'kernel/private/classes/commands/' . $script . '.php';
            $this->assertFileExists( 'bin/php/' . $script . '.php' );
            $this->assertStringContainsString( "@alias $alias\n", file_get_contents( $classFile ) );
            $this->assertStringContainsString( '\\Exponential\\Command\\Kernel\\' . $class . '::main( __FILE__ )', file_get_contents( 'bin/php/' . $script . '.php' ) );
            $fqcn = '\\Exponential\\Command\\Kernel\\' . $class;
            $this->assertSame( array( $from, $to ), array( $fqcn::FROM_ENGINE, $fqcn::TO_ENGINE ), $alias );
            $converter = new expTemplateDesignConverter( $fqcn::FROM_ENGINE, $fqcn::TO_ENGINE );
            $this->assertSame( $alias, $converter->commandName() );
            $this->assertTrue( $converter->available(), "$alias has a converter" );
        }
        $this->assertStringContainsString( "@alias exp:template:convert\n", file_get_contents( 'kernel/private/classes/commands/exptemplateconvert.php' ) );
    }

    public function testTheConsoleRunsTheCommandsByTheirNames()
    {
        foreach ( array( 'exp:template:convert' => 'from one template engine to another',
                         'exp:template:convert:tpl-to-stpl' => 'Convert Exponential templates (.tpl) to Smarty templates (.stpl)',
                         'exp:template:convert:stpl-to-tpl' => 'Convert Smarty templates (.stpl) to Exponential templates (.tpl)',
                         'exp:template:convert:tpl-to-twig' => 'Convert Exponential templates (.tpl) to Twig templates (.twig)',
                         'exp:template:convert:twig-to-tpl' => 'Convert Twig templates (.twig) to Exponential templates (.tpl)',
                         'exp:template:convert:stpl-to-twig' => 'Convert Smarty templates (.stpl) to Twig templates (.twig)',
                         'exp:template:convert:twig-to-stpl' => 'Convert Twig templates (.twig) to Smarty templates (.stpl)',
                         'exp:template:convert:diff' => 'Print the conversion of one template to another engine' ) as $name => $text )
        {
            list( $code, $output ) = $this->console( $name . ' --help --allow-root-user' );
            $this->assertSame( 0, $code, $output );
            $this->assertStringContainsString( $text, $output, $name );
        }
    }

    /**
     * No engine-specific conversion command exists: the names in the engine's own namespace are unknown commands, and
     * their scripts and class are gone. The names are put together here so that a search of the sources for them
     * finds nothing.
     */
    public function testTheSmartyCommandNamesAreUnknown()
    {
        $space = 'exp:' . 'smarty:';
        foreach ( array( $space . 'convert-tpl-to-smarty', $space . 'convert-smarty-to-tpl' ) as $name )
        {
            list( $code, $output ) = $this->console( $name );
            $this->assertSame( 1, $code, $output );
            $this->assertStringContainsString( 'UNKNOWN COMMAND', $output, $name );
        }
        $script = 'bin/php/' . 'expsmarty' . 'convert';
        $this->assertFileDoesNotExist( $script . '.php' );
        $this->assertFileDoesNotExist( $script . 'totpl.php' );
        $this->assertFalse( class_exists( 'expSmarty' . 'DesignConverter' ) );
    }

    public function testEnginesAndRegistry()
    {
        $this->assertSame( 'tpl', expTemplateDesignConverter::engine( 'tpl' ) );
        $this->assertSame( 'tpl', expTemplateDesignConverter::engine( 'exp' ) );
        $this->assertSame( 'tpl', expTemplateDesignConverter::engine( 'Exponential' ) );
        $this->assertSame( 'stpl', expTemplateDesignConverter::engine( 'smarty' ) );
        $this->assertSame( 'twig', expTemplateDesignConverter::engine( 'twig' ) );
        $this->assertNull( expTemplateDesignConverter::engine( 'foo' ) );
        $this->assertSame( 'expSmartyConverter', expTemplateDesignConverter::converterClass( 'tpl', 'stpl' ) );
        $this->assertSame( 'expSmartyReverseConverter', expTemplateDesignConverter::converterClass( 'stpl', 'tpl' ) );
        $this->assertSame( 'expTwigConverter', expTemplateDesignConverter::converterClass( 'tpl', 'twig' ) );
        $this->assertSame( 'expTwigReverseConverter', expTemplateDesignConverter::converterClass( 'twig', 'tpl' ) );
        // Smarty <-> Twig have no converter of their own: they go through .tpl
        $this->assertNull( expTemplateDesignConverter::converterClass( 'stpl', 'twig' ) );
        $this->assertTrue( expTemplateDesignConverter::hasConversion( 'stpl', 'twig' ) );
        $this->assertTrue( expTemplateDesignConverter::hasConversion( 'twig', 'stpl' ) );
        $this->assertInstanceOf( 'expTemplateChainConverter', ( new expTemplateDesignConverter( 'stpl', 'twig' ) )->converter() );
        $this->assertSame( array( 'tpl', 'stpl', 'twig' ), expTemplateDesignConverter::availableEngines() );
        $converter = new expTemplateDesignConverter( 'exponential', 'smarty' );
        $this->assertSame( array( 'tpl->stpl', '.tpl', '.stpl' ), array( $converter->direction(), $converter->sourceSuffix(), $converter->targetSuffix() ) );
    }

    public function testUsageErrorsExit2()
    {
        foreach ( array( array( array(), null, null, 'FAIL --from-engine is needed' ),
                         array( array( 'from-engine' => 'tpl' ), null, null, 'FAIL --to-engine is needed' ),
                         array( array( 'from-engine' => 'foo', 'to-engine' => 'tpl' ), null, null, 'FAIL --from-engine=foo is not a template engine' ),
                         array( array( 'from-engine' => 'smarty', 'to-engine' => 'stpl' ), null, null, 'FAIL --from-engine and --to-engine are the same' ),
                         array( array( 'from' => self::$dir . '/no-such-dir' ), 'tpl', 'stpl', 'FAIL --from must be a design directory' ),
                         array( array(), 'stpl', 'tpl', 'FAIL --from must be a design directory' ),
                         array( array( 'file' => self::$dir . '/no-such.tpl' ), 'tpl', 'stpl', 'FAIL --file must be an existing .tpl file' ) ) as $case )
        {
            list( $options, $from, $to, $message ) = $case;
            list( $code, , $err ) = $this->runCommand( $options, $from, $to );
            $this->assertSame( 2, $code, $message );
            $this->assertStringStartsWith( $message, $err[0] );
        }
    }

    public function testADirectionWithoutAConverterExits3()
    {
        $ini = eZINI::instance( 'site.ini' );
        $saved = $ini->variable( 'TemplateBridgeSettings', 'ConverterList' );
        $list = $saved;
        unset( $list['tpl-twig'], $list['twig-tpl'] );
        $ini->setVariable( 'TemplateBridgeSettings', 'ConverterList', $list );
        try
        {
            foreach ( array( array( 'tpl', 'twig' ), array( 'twig', 'stpl' ), array( 'stpl', 'twig' ) ) as $engines )
            {
                list( $code, $out, $err ) = $this->runCommand( array( 'from-engine' => $engines[0], 'to-engine' => $engines[1], 'from' => self::$dir ) );
                $this->assertSame( 3, $code );
                $this->assertSame( array(), $out );
                $this->assertStringEndsWith( 'is not available yet', $err[0] );
            }
        }
        finally
        {
            $ini->setVariable( 'TemplateBridgeSettings', 'ConverterList', $saved );
        }
    }

    /**
     * Every direction with Twig over a small design: tpl -> twig -> tpl renders the same, Smarty <-> Twig through .tpl in
     * one run, .html.twig read as the template without .html and written with --twig-suffix, x.twig before x.html.twig.
     */
    public function testTheTwigDirectionsOnADesign()
    {
        if ( !expTwigRenderer::loadLibrary() )
            $this->markTestSkipped( 'Twig 3 is not installed (TWIG_LIBRARY_PATH)' );
        $design = self::$dir . '/twig-design';
        mkdir( $design . '/templates/parts', 0777, true );
        $sources = array(
            'templates/page.tpl' => "{def \$n = 3}\n<ul>\n{foreach \$list as \$i}\n    <li>{\$i|wash}</li>\n{delimiter}\n{/delimiter}\n{/foreach}\n</ul>\n{undef \$n}\n",
            'templates/parts/x.tpl' => "{section name=S loop=\$list}{\$S:item}{section-else}none{/section}",
            'templates/parts/y.tpl' => "{switch match=\$a}{case match=1}one{/case}{case}other{/case}{/switch}",
        );
        foreach ( $sources as $relative => $text )
            file_put_contents( $design . '/' . $relative, $text );
        $variables = array( 'list' => array( 'p', 'q' ), 'a' => 1 );
        $dirs = array( 'twig' => self::$dir . '/t-twig', 'tpl' => self::$dir . '/t-tpl', 'stpl' => self::$dir . '/t-stpl',
                       'twig2' => self::$dir . '/t-twig2', 'stpl2' => self::$dir . '/t-stpl2', 'tpl2' => self::$dir . '/t-tpl2' );
        foreach ( array( array( 'tpl', 'twig', $design, $dirs['twig'] ), array( 'twig', 'tpl', $dirs['twig'], $dirs['tpl'] ),
                         array( 'tpl', 'stpl', $design, $dirs['stpl'] ), array( 'stpl', 'twig', $dirs['stpl'], $dirs['twig2'] ),
                         array( 'twig', 'stpl', $dirs['twig2'], $dirs['stpl2'] ), array( 'stpl', 'tpl', $dirs['stpl2'], $dirs['tpl2'] ) ) as $step )
        {
            list( $code, $out, $err, $counts ) = $this->runConverter( $step[0], $step[1], array( 'from' => $step[2], 'to' => $step[3] ) );
            $this->assertSame( 0, $code, $step[0] . '->' . $step[1] . ': ' . implode( "\n", array_merge( $out, $err ) ) );
            $this->assertSame( 3, $counts['written'], $step[0] . '->' . $step[1] );
        }
        $this->assertStringContainsString( '{# exp:template:convert tpl->twig md5:', file_get_contents( $dirs['twig'] . '/templates/page.twig' ) );
        $this->assertStringContainsString( '{# exp:template:convert stpl->twig md5:', file_get_contents( $dirs['twig2'] . '/templates/page.twig' ) );
        foreach ( $sources as $relative => $text )
        {
            $expected = $this->renderTpl( $text, $variables );
            $this->assertSame( $expected, $this->renderTpl( file_get_contents( $dirs['tpl'] . '/' . $relative ), $variables ), "tpl->twig->tpl $relative" );
            $this->assertSame( $expected, $this->renderTpl( file_get_contents( $dirs['tpl2'] . '/' . $relative ), $variables ), "tpl->stpl->twig->stpl->tpl $relative" );
        }

        // .html.twig: written with --twig-suffix, read back as x.tpl (never x.html.tpl); x.twig wins over x.html.twig
        $html = self::$dir . '/t-html';
        list( $code, $out ) = $this->runCommand( array( 'from-engine' => 'tpl', 'to-engine' => 'twig', 'from' => $design, 'to' => $html, 'twig-suffix' => '.html.twig' ) );
        $this->assertSame( 0, $code, implode( "\n", $out ) );
        $this->assertFileExists( $html . '/templates/page.html.twig' );
        list( $code, , $err ) = $this->runCommand( array( 'from-engine' => 'tpl', 'to-engine' => 'twig', 'from' => $design, 'twig-suffix' => '.txt' ) );
        $this->assertSame( 2, $code );
        $this->assertStringStartsWith( 'FAIL --twig-suffix must be .twig or .html.twig', $err[0] );
        copy( $dirs['twig'] . '/templates/parts/x.twig', $html . '/templates/parts/x.twig' );
        list( $code, $out, , $counts ) = $this->runConverter( 'twig', 'tpl', array( 'from' => $html, 'to' => self::$dir . '/t-html-tpl' ) );
        $this->assertSame( 0, $code, implode( "\n", $out ) );
        $this->assertFileExists( self::$dir . '/t-html-tpl/templates/page.tpl' );
        $this->assertFileDoesNotExist( self::$dir . '/t-html-tpl/templates/page.html.tpl' );
        $this->assertSame( 3, $counts['written'] );
        $this->assertNotEmpty( preg_grep( '/^NOTE templates\/parts\/x\.html\.twig: templates\/parts\/x\.twig is the same template/', $out ) );
    }

    /**
     * --override-ini writes a rule for every template of the target design, also for one that was changed by hand and
     * is skipped: without its rule the siteaccess would fall back to the .tpl.
     */
    public function testOverrideRulesKeepATemplateChangedByHand()
    {
        if ( !expTwigRenderer::loadLibrary() )
            $this->markTestSkipped( 'Twig 3 is not installed (TWIG_LIBRARY_PATH)' );
        $design = self::$dir . '/override-design';
        mkdir( $design . '/templates', 0777, true );
        file_put_contents( $design . '/templates/page.tpl', '{$a}' );
        file_put_contents( $design . '/templates/other.tpl', 'other' );
        $target = self::$dir . '/override-twig';
        $ini = self::$dir . '/override.ini.append.php';
        list( $code ) = $this->runConverter( 'tpl', 'twig', array( 'from' => $design, 'to' => $target, 'override-ini' => $ini ) );
        $this->assertSame( 0, $code );
        $this->assertStringContainsString( 'MatchFile=../../templates/page.twig', file_get_contents( $ini ) );
        file_put_contents( $target . '/templates/page.twig', 'by hand ' . file_get_contents( $target . '/templates/page.twig' ) );
        list( $code, $out, , $counts ) = $this->runConverter( 'tpl', 'twig', array( 'from' => $design, 'to' => $target, 'override-ini' => $ini ) );
        $this->assertSame( array( 0, 1 ), array( $code, $counts['skipped'] ), implode( "\n", $out ) );
        $this->assertStringContainsString( 'MatchFile=../../templates/page.twig', file_get_contents( $ini ), 'the rule of the skipped template stays' );
        $this->assertStringContainsString( 'MatchFile=../../templates/other.twig', file_get_contents( $ini ) );
    }

    public function testDiffPrintsTheConversion()
    {
        if ( !expTwigRenderer::loadLibrary() )
            $this->markTestSkipped( 'Twig 3 is not installed (TWIG_LIBRARY_PATH)' );
        $file = self::$dir . '/diff.tpl';
        file_put_contents( $file, '{if $a}A{/if}' );
        list( $code, $output ) = $this->console( 'exp:template:convert:diff --file=' . escapeshellarg( $file ) . ' --to-engine=twig --allow-root-user' );
        $this->assertSame( 0, $code, $output );
        $this->assertStringContainsString( "{% if a %}\nA{% endif %}", $output );
        file_put_contents( self::$dir . '/diff.twig', "{% if a %}\nB{% endif %}" );
        list( $code, $output ) = $this->console( 'exp:template:convert:diff --file=' . escapeshellarg( $file ) . ' --to-engine=twig --against=' . escapeshellarg( self::$dir . '/diff.twig' ) . ' --allow-root-user' );
        $this->assertSame( 1, $code, $output );
        $this->assertStringContainsString( "\n-B{% endif %}\n+A{% endif %}", $output );
        list( $code ) = $this->console( 'exp:template:convert:diff --file=' . escapeshellarg( $file ) . ' --allow-root-user' );
        $this->assertSame( 2, $code );
    }

    public function testMarker()
    {
        $converter = new expTemplateDesignConverter( 'stpl', 'tpl' );
        $text = $converter->withMarker( "A\n", 'templates/a.stpl' );
        $this->assertStringContainsString( '{* exp:template:convert stpl->tpl md5:' . md5( "A\n" ) . ' from templates/a.stpl ', $text );
        list( $content, $marker ) = expTemplateDesignConverter::splitMarker( $text );
        $this->assertSame( "A\n", $content );
        $this->assertSame( array( 'from' => 'stpl', 'to' => 'tpl', 'md5' => md5( "A\n" ), 'source' => 'templates/a.stpl', 'valid' => true ), $marker );
        list( , $marker ) = expTemplateDesignConverter::splitMarker( 'B' . $text );
        $this->assertFalse( $marker['valid'], 'a changed content no longer matches its marker' );
        $this->assertNull( expTemplateDesignConverter::splitMarker( $text . "\n" )[1], 'the marker is the end of the file' );
        // the marker prints nothing
        $this->assertSame( "A\n", $this->renderTpl( $text, array() ) );
        // the comment syntax of an engine without {* *} comments is recognized too
        list( $content, $marker ) = expTemplateDesignConverter::splitMarker( "X{# exp:template:convert tpl->twig md5:" . md5( 'X' ) . " from a.tpl -- generated #}" );
        $this->assertSame( array( 'X', 'twig', true ), array( $content, $marker['to'], $marker['valid'] ) );
    }

    /** exp:template:check: stdin is named by --stdin-name; -q prints the findings at ERROR and nothing else. */
    public function testTemplateCheckStdinNameAndQuiet()
    {
        list( $code, $output ) = $this->console( 'exp:template:check --stdin-engine=tpl --stdin-name=my/stdin.tpl - --allow-root-user < ' .
                                                 escapeshellarg( self::writeTemporary( 'check-stdin.tpl', "{\$a|wash(}\n" ) ) );
        $this->assertSame( 1, $code, $output );
        $this->assertStringContainsString( 'my/stdin.tpl:1', $output );
        $this->assertStringNotContainsString( ' - ', $output );
        list( $code, $output ) = $this->console( 'exp:template:check -q --allow-root-user ' . escapeshellarg( self::writeTemporary( 'check-quiet.tpl', "{\$a|wash(}\n" ) ) );
        $this->assertSame( 1, $code, $output );
        $this->assertStringContainsString( 'TPL-SYNTAX-001', $output );
        $this->assertStringNotContainsString( 'SUMMARY', $output );
    }

    private static function writeTemporary( $name, $text )
    {
        $path = self::$dir . '/' . $name;
        file_put_contents( $path, $text );
        return $path;
    }

    public function testAResultThatDoesNotParseIsAnError()
    {
        $converter = new expTemplateDesignConverter( 'stpl', 'tpl' );
        $this->assertNull( $converter->verify( '{if true()}A{/if}' ) );
        $this->assertStringStartsWith( 'the result does not parse', (string)$converter->verify( '{if true()}A{/if}{$a|' ) );
    }

    public function testBothDirectionsOnADesign()
    {
        $design = self::$dir . '/design';
        mkdir( $design . '/templates/parts', 0777, true );
        mkdir( $design . '/override/templates', 0777, true );
        $sources = array(
            'templates/page.tpl' => "{def \$n = 3}\n<ul>\n{foreach \$list as \$i}\n    <li>{\$i|wash}</li>\n{delimiter}\n{/delimiter}\n{/foreach}\n</ul>\n{include uri='design:parts/x.tpl'}\n{undef \$n}\n",
            'templates/parts/x.tpl' => "{section name=S loop=\$list}{\$S:item}{section-else}none{/section}",
            'override/templates/y.tpl' => "{switch match=\$a}{case match=1}one{/case}{case}other{/case}{/switch}",
        );
        foreach ( $sources as $relative => $text )
            file_put_contents( $design . '/' . $relative, $text );
        $stplDir = self::$dir . '/stpl';
        $tplDir = self::$dir . '/tpl';

        // --dry-run: the list of what would be written, nothing written
        list( $code, $out, $err ) = $this->runCommand( array( 'from' => $design, 'to' => $stplDir, 'dry-run' => true ), 'tpl', 'stpl' );
        $this->assertSame( 0, $code, implode( "\n", $err ) );
        $this->assertSame( 'Dry run: nothing is written.', $out[0] );
        $this->assertCount( 3, preg_grep( '/^WRITE /', $out ) );
        $this->assertStringStartsWith( 'PASS tpl->stpl 3 templates: 3 to write', end( $out ) );
        $this->assertFileDoesNotExist( $stplDir );

        // the general command with the engines as options
        list( $code, $out, $err ) = $this->runCommand( array( 'from-engine' => 'tpl', 'to-engine' => 'stpl', 'from' => $design, 'to' => $stplDir ) );
        $this->assertSame( 0, $code, implode( "\n", $err ) );
        $this->assertSame( array( "WRITE $stplDir/override/templates/y.stpl", "WRITE $stplDir/templates/page.stpl", "WRITE $stplDir/templates/parts/x.stpl" ), array_values( preg_grep( '/^WRITE /', $out ) ) );
        $this->assertStringStartsWith( 'PASS tpl->stpl 3 templates: 3 written, 0 unchanged, 0 skipped, 0 errors', end( $out ) );
        $this->assertStringContainsString( '{* exp:template:convert tpl->stpl md5:', file_get_contents( $stplDir . '/override/templates/y.stpl' ) );

        list( $code, $out, $err, $counts ) = $this->runConverter( 'stpl', 'tpl', array( 'from' => $stplDir, 'to' => $tplDir ) );
        $this->assertSame( 0, $code, implode( "\n", $err ) );
        $this->assertSame( 3, $counts['written'] );
        $this->assertSame( 0, $counts['notes'] );
        foreach ( $sources as $relative => $text )
        {
            $variables = array( 'list' => array( 'p', 'q' ), 'a' => 1 );
            $back = file_get_contents( $tplDir . '/' . $relative );
            if ( $relative !== 'templates/page.tpl' )
                $this->assertSame( $this->renderTpl( $text, $variables ), $this->renderTpl( $back, $variables ), $relative );
        }

        // a second run changes nothing (the same files, unchanged)
        list( $code, , , $counts ) = $this->runConverter( 'stpl', 'tpl', array( 'from' => $stplDir, 'to' => $tplDir ) );
        $this->assertSame( array( 0, 0, 3 ), array( $code, $counts['written'], $counts['unchanged'] ) );

        // a file changed by hand is skipped, --force replaces it; a source of its own (no marker) as well
        file_put_contents( $tplDir . '/templates/parts/x.tpl', 'by hand' . file_get_contents( $tplDir . '/templates/parts/x.tpl' ) );
        file_put_contents( $tplDir . '/templates/own.tpl', 'written by hand' );
        file_put_contents( $stplDir . '/templates/own.stpl', 'from smarty' );
        list( $code, $out, , $counts ) = $this->runConverter( 'stpl', 'tpl', array( 'from' => $stplDir, 'to' => $tplDir ) );
        $this->assertSame( 0, $code );
        $this->assertSame( 2, $counts['skipped'] );
        $this->assertSame( 'written by hand', file_get_contents( $tplDir . '/templates/own.tpl' ) );
        $this->assertNotEmpty( preg_grep( '/^SKIP templates\/own\.stpl: .* has no conversion marker/', $out ) );
        $this->assertNotEmpty( preg_grep( '/^SKIP templates\/parts\/x\.stpl: .* was changed after it was generated/', $out ) );
        // --dry-run with --force: REPLACE listed, still nothing written
        list( $code, $out ) = $this->runConverter( 'stpl', 'tpl', array( 'from' => $stplDir, 'to' => $tplDir, 'force' => true, 'dry-run' => true ) );
        $this->assertSame( 0, $code );
        $this->assertCount( 2, preg_grep( '/^REPLACE /', $out ) );
        $this->assertSame( 'written by hand', file_get_contents( $tplDir . '/templates/own.tpl' ) );
        list( $code, , , $counts ) = $this->runConverter( 'stpl', 'tpl', array( 'from' => $stplDir, 'to' => $tplDir, 'force' => true ) );
        $this->assertSame( array( 0, 2, 0 ), array( $code, $counts['written'], $counts['skipped'] ) );
        $this->assertStringStartsWith( 'from smarty{*', file_get_contents( $tplDir . '/templates/own.tpl' ) );

        // --file: one template; without --from, next to its source
        list( $code, , , $counts ) = $this->runConverter( 'tpl', 'stpl', array( 'file' => $design . '/templates/parts/x.tpl' ) );
        $this->assertSame( array( 0, 1 ), array( $code, $counts['written'] ) );
        $this->assertFileExists( $design . '/templates/parts/x.stpl' );
        list( $code, , , $counts ) = $this->runConverter( 'tpl', 'stpl', array( 'from' => $design, 'to' => $stplDir, 'file' => 'override/templates/y.tpl' ) );
        $this->assertSame( array( 0, 1, 1 ), array( $code, $counts['templates'], $counts['unchanged'] ) );
    }

    /**
     * The design of EXP_SMARTY_TEST_DESIGN, or the media design: every template round trip.
     */
    public function testRoundTripOfAWholeDesign()
    {
        $design = getenv( 'EXP_TEMPLATE_TEST_DESIGN' ) ?: ( getenv( 'EXP_SMARTY_TEST_DESIGN' ) ?: 'extension/sevenx_themes_media/design/media' );
        if ( !is_dir( $design ) )
            $this->markTestSkipped( "no design to convert ($design; set EXP_TEMPLATE_TEST_DESIGN)" );
        $design = rtrim( $design, '/' );
        // the template system's parser warns (string offsets) on the templates of the design whose source is broken;
        // those are the source's, reported by the converter as notes
        set_error_handler( function ( $errno, $message, $file ) {
            return $errno === E_WARNING && substr( $file, -strlen( 'eztemplatemultipassparser.php' ) ) === 'eztemplatemultipassparser.php';
        } );
        try
        {
            $this->roundTripDesign( $design, 'stpl' );
            if ( expTwigRenderer::loadLibrary() )
                $this->roundTripDesign( $design, 'twig' );
        }
        finally
        {
            restore_error_handler();
        }
    }

    private function roundTripDesign( $design, $engine )
    {
        $stplDir = self::$dir . '/whole-' . $engine;
        $tplDir = self::$dir . '/whole-tpl-' . $engine;
        list( $code, , $err, $forward ) = $this->runConverter( 'tpl', $engine, array( 'from' => $design, 'to' => $stplDir ) );
        $this->assertSame( 0, $code, implode( "\n", $err ) );
        $this->assertSame( 0, $forward['errors'] );
        list( $code, , $err, $reverse ) = $this->runConverter( $engine, 'tpl', array( 'from' => $stplDir, 'to' => $tplDir ) );
        $this->assertSame( 0, $code, implode( "\n", $err ) );
        $this->assertSame( $forward['templates'], $reverse['written'] );

        // every template whose source parses: tpl -> X -> tpl -> X gives the first X again
        $toSmarty = new expTemplateDesignConverter( 'tpl', $engine );
        $fromEngine = new expTemplateDesignConverter( $engine, 'tpl' );
        $notFixpoint = array();
        foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $design, FilesystemIterator::SKIP_DOTS ) ) as $entry )
        {
            if ( substr( $entry->getFilename(), -4 ) !== '.tpl' )
                continue;
            $relative = substr( $entry->getPathname(), strlen( $design ) + 1 );
            list( $stpl, $notes ) = $toSmarty->convertText( file_get_contents( $entry->getPathname() ), $relative );
            if ( preg_grep( '/^the source has an error/', $notes ) )
                continue;
            list( $stpl2 ) = $toSmarty->convertText( file_get_contents( $tplDir . '/' . $relative ), $relative );
            // Twig: one round later (Twig reads {{ 'q' }} as the text q, so the first round may change the form only)
            if ( $engine === 'twig' && $stpl2 !== $stpl )
            {
                $stpl = $stpl2;
                list( $tpl3 ) = $fromEngine->convertText( $stpl2, $relative );
                list( $stpl2 ) = $toSmarty->convertText( $tpl3, $relative );
            }
            if ( $stpl2 !== $stpl )
                $notFixpoint[] = $relative;
        }
        $this->assertSame( array(), $notFixpoint, $engine );
    }
}
