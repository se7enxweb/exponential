<?php
/**
 * exp:template:check (expTemplateChecker): for each engine a valid template passes and an invalid one gives its rule id,
 * line and message; every output format; exit codes 0-3; baseline; ignore globs and ignore comments; stdin; target
 * resolution of directories, design directories and extensions; engine selection; the rules list.
 *
 * Smarty 5 and Twig 3 from Composer, or SMARTY_LIBRARY_PATH and TWIG_LIBRARY_PATH. No database and no siteaccess.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 */

require_once __DIR__ . '/../smarty/expSmartyOperatorParityTest.php';

class expTemplateCheckerTest extends PHPUnit\Framework\TestCase
{
    private static $dir;

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-template-check-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir . '/design/templates', 0777, true );
        expSmartyOperatorParityTest::trustTestDirectory( self::$dir );
        if ( !class_exists( '\Smarty\Smarty' ) && getenv( 'SMARTY_LIBRARY_PATH' ) )
            eZINI::instance( 'smarty.ini' )->setVariable( 'SmartySettings', 'LibraryPath', getenv( 'SMARTY_LIBRARY_PATH' ) );
        if ( !class_exists( '\Twig\Environment' ) && getenv( 'TWIG_LIBRARY_PATH' ) )
            eZINI::instance( 'twig.ini' )->setVariable( 'TwigSettings', 'LibraryPath', getenv( 'TWIG_LIBRARY_PATH' ) );
        $files = array(
            'ok.tpl' => "{if \$a}A{/if}\n",
            'bad.tpl' => "{foreach \$l as \$x}{\$x}\n{/if}\n",
            'ok.stpl' => "{\$a|wash}\n",
            'bad.stpl' => "{if \$a}\nA\n",
            'ok.twig' => "{{ a|wash }}\n",
            'ok.html.twig' => "{% if a %}A{% endif %}\n",
            'bad.twig' => "line one\n{{ a|wash }\n",
            'unknown.twig' => "{{ a|exp_washh }}\n",
            'ignored.twig' => "{# exp:template:check ignore-next-line TWIG-STRICT-UNKNOWN-OPERATOR #}\n{{ a|exp_washh }}\n",
            'refused.twig' => "{% do 1 %}\n",
            'include.twig' => "{% expinclude uri='design:no/such/template.tpl' %}\n",
            'unknown.tpl' => "ok\n{\$a|wahs}\n",
            'extra.tpl' => "{\$a|wash(}\n",
            'secured.stpl' => "a\n{\$smarty.server.HOME}\n",
        );
        foreach ( $files as $name => $text )
            file_put_contents( self::$dir . '/design/templates/' . $name, $text );
    }

    public static function tearDownAfterClass(): void
    {
        $remove = function ( $path ) use ( &$remove ) {
            if ( is_dir( $path ) )
            {
                foreach ( scandir( $path ) as $entry )
                    if ( $entry !== '.' && $entry !== '..' )
                        $remove( "$path/$entry" );
                rmdir( $path );
            }
            else if ( file_exists( $path ) )
                unlink( $path );
        };
        $remove( self::$dir );
    }

    private $handler;

    protected function setUp(): void
    {
        if ( !expSmartyRenderer::loadLibrary() || !expTwigRenderer::loadLibrary() )
            $this->markTestSkipped( 'Smarty 5 and Twig 3 are needed (SMARTY_LIBRARY_PATH, TWIG_LIBRARY_PATH)' );
        $this->handler = self::exceptionHandler();
    }

    protected function tearDown(): void
    {
        // reading a siteaccess's settings registers eZExecution's handlers on their first use
        while ( self::exceptionHandler() !== $this->handler && self::exceptionHandler() !== null )
            restore_exception_handler();
    }

    private static function exceptionHandler()
    {
        $handler = set_exception_handler( null );
        restore_exception_handler();
        return $handler;
    }

    /** @return array( exit code, output lines, error lines ) */
    private function check( array $targets, array $options = array() )
    {
        $out = array();
        $err = array();
        $checker = new expTemplateChecker( $options, function ( $line ) use ( &$out ) { $out[] = $line; }, function ( $line ) use ( &$err ) { $err[] = $line; } );
        $code = $checker->run( $targets );
        return array( $code, $out, $err );
    }

    private function file( $name )
    {
        return self::$dir . '/design/templates/' . $name;
    }

    private function json( array $targets, array $options = array() )
    {
        list( $code, $out ) = $this->check( $targets, $options + array( 'format' => 'json' ) );
        $document = json_decode( implode( "\n", $out ), true );
        $this->assertIsArray( $document, implode( "\n", $out ) );
        return array( $code, $document );
    }

    public static function fileProvider()
    {
        return array(
            'tpl ok'         => array( 'ok.tpl', array(), 0, array() ),
            'tpl bad'        => array( 'bad.tpl', array(), 1, array( array( 'TPL-SYNTAX-001', 2 ) ) ),
            'stpl ok'        => array( 'ok.stpl', array(), 0, array() ),
            'stpl bad'       => array( 'bad.stpl', array(), 1, array( array( 'STPL-SYNTAX-001', null ) ) ),
            'twig ok'        => array( 'ok.twig', array(), 0, array() ),
            'html.twig ok'   => array( 'ok.html.twig', array(), 0, array() ),
            'twig bad'       => array( 'bad.twig', array(), 1, array( array( 'TWIG-SYNTAX-001', 2 ) ) ),
            'unknown operator at compile' => array( 'unknown.twig', array(), 0, array() ),
            'unknown operator at strict'  => array( 'unknown.twig', array( 'level' => 'strict', 'fail-on' => 'warn' ), 1, array( array( 'TWIG-STRICT-UNKNOWN-OPERATOR', 1 ) ) ),
            'ignore comment' => array( 'ignored.twig', array( 'level' => 'strict', 'fail-on' => 'warn' ), 0, array() ),
            'sandbox at strict' => array( 'refused.twig', array( 'level' => 'strict' ), 1, array( array( 'TWIG-SECURITY-001', 1 ) ) ),
            'include at strict' => array( 'include.twig', array( 'level' => 'strict' ), 1, array( array( 'TWIG-STRICT-INCLUDE-MISSING', 1 ) ) ),
            'syntax level'   => array( 'refused.twig', array( 'level' => 'syntax' ), 0, array() ),
            'tpl unknown operator at compile' => array( 'unknown.tpl', array(), 0, array() ),
            'tpl unknown operator at strict'  => array( 'unknown.tpl', array( 'level' => 'strict', 'fail-on' => 'warn' ), 1, array( array( 'TPL-STRICT-UNKNOWN-OPERATOR', 2 ) ) ),
            'stpl security'  => array( 'secured.stpl', array( 'security' => true ), 1, array( array( 'STPL-SECURITY-001', 2 ) ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('fileProvider')]
    public function testFile( $name, array $options, $exit, array $rules )
    {
        list( $code, $document ) = $this->json( array( $this->file( $name ) ), $options );
        $found = array();
        foreach ( $document['findings'] as $finding )
            $found[] = array( $finding['rule'], $rules && $rules[0][1] === null ? null : $finding['line'] );
        $this->assertSame( $rules, $found, json_encode( $document['findings'] ) );
        $this->assertSame( $exit, $code );
        $this->assertSame( 'exp-template-check/1', $document['schema'] );
    }

    public function testADirectoryOfEveryEngine()
    {
        list( $code, $document ) = $this->json( array( self::$dir . '/design' ) );
        $this->assertSame( 1, $code );
        $this->assertSame( 14, $document['summary']['files'] );
        $this->assertSame( array( 'stpl' => 3, 'tpl' => 4, 'twig' => 7 ), self::sorted( $document['summary']['engines'] ) );
        list( , $document ) = $this->json( array(), array( 'design-dir' => array( self::$dir . '/design' ), 'engine' => 'twig' ) );
        $this->assertSame( 7, $document['summary']['files'] );
        // bad.tpl, bad.stpl, bad.twig and unknown.twig left out
        list( , $document ) = $this->json( array( self::$dir . '/design' ), array( 'ignore' => array( 'bad.*', '*/unknown.twig' ) ) );
        $this->assertSame( 10, $document['summary']['files'] );
    }

    private static function sorted( array $a )
    {
        ksort( $a );
        return $a;
    }

    public function testFormats()
    {
        $bad = array( $this->file( 'bad.twig' ) );
        list( , $out ) = $this->check( $bad );
        $text = implode( "\n", $out );
        $this->assertStringContainsString( 'ERROR  twig  ' . $this->file( 'bad.twig' ) . ':2', $text );
        $this->assertStringContainsString( '[TWIG-SYNTAX-001]', $text );
        $this->assertStringContainsString( '> 2 | {{ a|wash }', $text );
        $this->assertStringEndsWith( 'FAIL', $text );
        list( , $out ) = $this->check( $bad, array( 'format' => 'jsonl' ) );
        $out = explode( "\n", implode( "\n", $out ) );
        $this->assertSame( 'finding', json_decode( $out[0], true )['type'] );
        $this->assertSame( 'summary', json_decode( end( $out ), true )['type'] );
        foreach ( array( 'junit' => '<testsuites', 'checkstyle' => '<checkstyle', 'github' => '::error file=', 'sarif' => '"version": "2.1.0"' ) as $format => $needle )
        {
            list( , $out ) = $this->check( $bad, array( 'format' => $format ) );
            $this->assertStringContainsString( $needle, implode( "\n", $out ), $format );
        }
        $output = self::$dir . '/out.json';
        list( , $out ) = $this->check( $bad, array( 'output' => $output ) );
        $this->assertSame( 'exp-template-check/1', json_decode( file_get_contents( $output ), true )['schema'] );
        $this->assertStringEndsWith( 'FAIL', implode( "\n", $out ), 'the console keeps the text' );
    }

    public function testUsageErrorsAndRules()
    {
        foreach ( array( array( array( 'no/such/file.tpl' ), array() ), array( array(), array( 'level' => 'deep' ) ), array( array(), array( 'format' => 'xml' ) ),
                         array( array(), array( 'engine' => 'jinja' ) ), array( array(), array( 'design' => array( 'no_such_design' ) ) ),
                         array( array(), array( 'siteaccess' => array( 'no_such_siteaccess' ) ) ), array( array(), array( 'explain' => 'NO-SUCH-RULE' ) ) ) as $case )
        {
            list( $code, , $err ) = $this->check( $case[0], $case[1] );
            $this->assertSame( 2, $code, json_encode( $case ) );
            $this->assertStringStartsWith( 'FAIL ', $err[0] );
        }
        list( $code, $out ) = $this->check( array(), array( 'rules' => true ) );
        $this->assertSame( 0, $code );
        $this->assertCount( count( expTemplateChecker::RULES ), $out );
        list( $code, $out ) = $this->check( array(), array( 'explain' => 'TWIG-SYNTAX-001' ) );
        $this->assertSame( 0, $code );
        $this->assertStringStartsWith( 'TWIG-SYNTAX-001 (ERROR):', $out[0] );
    }

    /** Messages on one line, without the engines' own heads; a hint for an unknown operator. */
    public function testMessages()
    {
        list( , $document ) = $this->json( array( $this->file( 'extra.tpl' ), $this->file( 'secured.stpl' ), $this->file( 'bad.stpl' ), $this->file( 'unknown.tpl' ) ),
                                           array( 'level' => 'strict', 'security' => true ) );
        $messages = array();
        foreach ( $document['findings'] as $finding )
        {
            $this->assertNotSame( '', trim( $finding['message'] ), json_encode( $finding ) );
            $this->assertStringNotContainsString( "\n", $finding['message'] );
            $this->assertDoesNotMatchRegularExpression( '/^(parser error|on line \d+|Syntax error in template)/', $finding['message'] );
            $messages[$finding['rule']][] = $finding['message'];
            if ( $finding['rule'] === 'TPL-STRICT-UNKNOWN-OPERATOR' )
                $this->assertSame( 'did you mean "wash"?', $finding['hint'] );
        }
        $this->assertContains( "special variable '\$smarty.server' not allowed by security setting", $messages['STPL-SECURITY-001'] );
        $this->assertContains( 'unclosed {if} tag', $messages['STPL-SYNTAX-001'] );
        $this->assertContains( "Extra characters found in expression, they will be ignored: |wash(' (\$a)", $messages['TPL-SYNTAX-001'] );
    }

    /** quiet (-q): the findings at ERROR only, no summary and no result line (the exit code says it). */
    public function testQuiet()
    {
        $out = array();
        $quiet = new expTemplateChecker( array( 'quiet' => true ), function ( $line ) use ( &$out ) { $out[] = $line; }, function () {} );
        $quiet->run( array( $this->file( 'bad.tpl' ), $this->file( 'unknown.tpl' ) ) );
        $text = implode( "\n", $out );
        $this->assertStringContainsString( 'TPL-SYNTAX-001', $text );
        $this->assertStringNotContainsString( 'SUMMARY', $text );
        $this->assertStringNotContainsString( 'PASS', $text );
        $this->assertStringNotContainsString( 'FAIL', $text );
    }

    public function testBaseline()
    {
        $baseline = self::$dir . '/baseline.json';
        list( $code ) = $this->check( array( $this->file( 'bad.twig' ), $this->file( 'bad.tpl' ) ), array( 'generate-baseline' => $baseline ) );
        $this->assertSame( 1, $code );
        list( $code, $document ) = $this->json( array( $this->file( 'bad.twig' ), $this->file( 'bad.tpl' ) ), array( 'baseline' => $baseline ) );
        $this->assertSame( 0, $code, 'only the known findings: nothing new' );
        $this->assertSame( array(), $document['findings'] );
        foreach ( $document['files'] as $file )
            $this->assertSame( 'OK', $file['status'], 'a file whose findings the baseline holds is OK: ' . $file['file'] );
        $this->assertSame( 2, $document['summary']['ok'] );
        $this->assertSame( 0, $document['summary']['failed'] );
        list( $code ) = $this->check( array( $this->file( 'bad.stpl' ) ), array( 'baseline' => $baseline ) );
        $this->assertSame( 1, $code, 'a new finding' );
    }

    public function testAnUnavailableEngineExits3()
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'TemplateBridgeSettings', 'CheckerList', array( 'twig' => 'expNoSuchCheckerClass' ) );
        try
        {
            list( $code, $out ) = $this->check( array( $this->file( 'ok.twig' ) ) );
            $this->assertSame( 3, $code );
            $this->assertContains( 'UNAVAILABLE twig', $out );
            list( $code ) = $this->check( array( $this->file( 'ok.twig' ) ), array( 'allow-missing-engine' => true ) );
            $this->assertSame( 0, $code );
        }
        finally
        {
            $ini->setVariable( 'TemplateBridgeSettings', 'CheckerList', array() );
        }
    }

    public function testParityAndEncoding()
    {
        list( , $document ) = $this->json( array( $this->file( 'ok.tpl' ), $this->file( 'ok.twig' ), $this->file( 'ok.stpl' ) ), array( 'parity' => true ) );
        $this->assertSame( array( 'PARITY-PAIR' ), array_column( $document['findings'], 'rule' ) );
        $bom = self::$dir . '/bom.tpl';
        file_put_contents( $bom, "\xEF\xBB\xBFtext\r\nmore\n" );
        list( , $document ) = $this->json( array( $bom ), array( 'encoding' => true ) );
        $this->assertSame( array( 'ENCODING-BOM-002', 'ENCODING-CRLF-003' ), array_column( $document['findings'], 'rule' ) );
    }

    public function testCompiledOutputStaysPrivate()
    {
        $before = glob( 'var/cache/template-check/*' ) ?: array();
        $this->check( array( self::$dir . '/design' ) );
        $this->assertSame( $before, glob( 'var/cache/template-check/*' ) ?: array(), 'the private directory is removed' );
    }
}
