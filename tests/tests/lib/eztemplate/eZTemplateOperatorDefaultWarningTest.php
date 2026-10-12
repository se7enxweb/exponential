<?php
/**
 * The warning for a template operator parameter that has no default value.
 *
 * An operator's namedParameterList() may leave out the 'default' of a parameter. When a template leaves that
 * parameter out too, the operator gets null for it and the template engine writes
 * "Operator '<op>' has no default value for parameter '<name>'" (eZTemplateOperatorElement):
 *   - interpreted templates: once per operator and parameter in a request, however often the operator runs
 *   - compiled templates: once per operator and parameter when the template is compiled; running the compiled
 *     template again writes nothing (its code does not check it on every run)
 *   - a parameter that has a default value, given or not, and a parameter the template gives, warn nothing
 *   - the list of warnings already written starts again at the next request (a persistent worker keeps the
 *     template object) and after reset()
 *
 * No kernel bootstrap, no database and no siteaccess: a bare eZTemplate with the lib/eztemplate autoloads. Template
 * files and compiled templates go to a private directory under var/tmp that tearDownAfterClass() removes.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 0bb0bac618), Copyright (C) Mugo Web, GPL v2
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group eztemplate
 */

/** An operator whose second parameter has no default value. */
class eZTemplateOperatorDefaultWarningTestOperator
{
    public function operatorList()
    {
        return array( 'w6nodefault' );
    }

    public function namedParameterPerOperator()
    {
        return true;
    }

    public function namedParameterList()
    {
        return array( 'w6nodefault' => array(
            'first' => array( 'type' => 'string', 'required' => false, 'default' => 'a' ),
            'second' => array( 'type' => 'string', 'required' => false ),
        ) );
    }

    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement = false )
    {
        $operatorValue = $namedParameters['first'] . '-' . ( $namedParameters['second'] === null ? 'null' : $namedParameters['second'] );
    }
}

class eZTemplateOperatorDefaultWarningTest extends PHPUnit\Framework\TestCase
{
    /** @var string directory of this class's template files and compiled templates */
    private static $dir;

    private $savedGlobals = array();

    private $savedRequestTime;

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-eztemplate-opdefault-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir . '/compiled', 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        self::removeTree( self::$dir );
    }

    protected function setUp(): void
    {
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $this->savedRequestTime = array_key_exists( 'REQUEST_TIME_FLOAT', $_SERVER ) ? array( $_SERVER['REQUEST_TIME_FLOAT'] ) : null;
    }

    protected function tearDown(): void
    {
        foreach ( $this->savedGlobals as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
        if ( $this->savedRequestTime === null )
            unset( $_SERVER['REQUEST_TIME_FLOAT'] );
        else
            $_SERVER['REQUEST_TIME_FLOAT'] = $this->savedRequestTime[0];
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

    private function mode( $compiled )
    {
        if ( $compiled )
        {
            unset( $GLOBALS['eZSiteBasics'] );
            $GLOBALS['eZTemplateCompilerSettings']['compile'] = true;
            $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = self::$dir . '/compiled';
        }
        else
        {
            $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
            $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
        }
    }

    private function template()
    {
        $tpl = new eZTemplate();
        $tpl->autoload();
        $operator = new eZTemplateOperatorDefaultWarningTestOperator();
        $tpl->registerOperators( $operator );
        return $tpl;
    }

    private static function file( $source )
    {
        $file = self::$dir . '/' . md5( $source ) . '.tpl';
        if ( !file_exists( $file ) )
            file_put_contents( $file, $source );
        return $file;
    }

    /** The warnings of $tpl about operator parameters without a default value, as array( text, ... ). */
    private static function defaultWarnings( eZTemplate $tpl )
    {
        $texts = array();
        foreach ( $tpl->warningLog() as $warning )
        {
            if ( strpos( $warning['text'], 'has no default value for parameter' ) !== false )
                $texts[] = $warning['text'];
        }
        return $texts;
    }

    /**
     * @testdox interpreted: the warning names operator and parameter, once however often the operator runs
     */
    public function testInterpretedWarnsOncePerOperatorAndParameter(): void
    {
        $this->mode( false );
        $_SERVER['REQUEST_TIME_FLOAT'] = 1000.5;
        $tpl = $this->template();
        $output = $tpl->fetch( self::file( "{w6nodefault()}|{w6nodefault('b')}|{w6nodefault('c')}" ) );

        $this->assertSame( 'a-null|b-null|c-null', $output );
        $warnings = self::defaultWarnings( $tpl );
        $this->assertCount( 1, $warnings, implode( "\n", $warnings ) );
        $this->assertSame( "Operator 'w6nodefault' has no default value for parameter 'second'", $warnings[0] );
    }

    /**
     * @testdox interpreted: a parameter that is given or has a default value warns nothing
     */
    public function testGivenParametersAndDefaultsWarnNothing(): void
    {
        $this->mode( false );
        $tpl = $this->template();
        $output = $tpl->fetch( self::file( "{w6nodefault('x','y')}" ) );

        $this->assertSame( 'x-y', $output );
        $this->assertSame( array(), self::defaultWarnings( $tpl ) );
    }

    /**
     * @testdox interpreted: the next request, and reset(), warn again
     */
    public function testNextRequestAndResetWarnAgain(): void
    {
        $this->mode( false );
        $file = self::file( "{w6nodefault()}" );
        $tpl = $this->template();

        // fetch() starts each warning log afresh, so each fetch is looked at on its own
        $_SERVER['REQUEST_TIME_FLOAT'] = 2000.25;
        $tpl->fetch( $file );
        $this->assertCount( 1, self::defaultWarnings( $tpl ), 'first use in the request' );
        $tpl->fetch( $file );
        $this->assertCount( 0, self::defaultWarnings( $tpl ), 'the same request again: written already' );

        $_SERVER['REQUEST_TIME_FLOAT'] = 2001.75;
        $tpl->fetch( $file );
        $this->assertCount( 1, self::defaultWarnings( $tpl ), 'a persistent worker\'s next request warns again' );

        $tpl->reset();
        $operator = new eZTemplateOperatorDefaultWarningTestOperator();
        $tpl->registerOperators( $operator );
        $tpl->fetch( $file );
        $this->assertCount( 1, self::defaultWarnings( $tpl ), 'after reset() (which empties the warning log too)' );
    }

    /**
     * @testdox compiled: warned once when the template is compiled, not when the compiled template runs
     */
    public function testCompiledWarnsOncePerCompile(): void
    {
        $this->mode( true );
        $source = "{w6nodefault()}|{w6nodefault('b')}|{\$v|w6nodefault()}";
        $file = self::file( $source );

        $tpl = $this->template();
        $tpl->setVariable( 'v', 'ignored' );
        $output = $tpl->fetch( $file );
        $this->assertSame( 'a-null|b-null|a-null', $output );
        $warnings = self::defaultWarnings( $tpl );
        $this->assertCount( 1, $warnings, implode( "\n", $warnings ) );
        $this->assertStringStartsWith( "Operator 'w6nodefault' has no default value for parameter 'second'", $warnings[0] );
        $this->assertNotEmpty( glob( self::$dir . '/compiled/*.php' ), 'the template was compiled' );

        // The compiled template again, in another template object: no compile, no warning
        $again = $this->template();
        $again->setVariable( 'v', 'ignored' );
        $this->assertSame( 'a-null|b-null|a-null', $again->fetch( $file ) );
        $this->assertSame( array(), self::defaultWarnings( $again ) );
    }

    /**
     * @testdox operatorParametersWithoutDefault() lists only the left-out parameters without a default
     */
    public function testOperatorParametersWithoutDefault(): void
    {
        $tpl = $this->template();
        $this->assertSame( array( 'second' ), $tpl->operatorParametersWithoutDefault( 'w6nodefault', array() ) );
        $given = array( array( array( eZTemplate::TYPE_STRING, 'x' ) ), array( array( eZTemplate::TYPE_STRING, 'y' ) ) );
        $this->assertSame( array(), $tpl->operatorParametersWithoutDefault( 'w6nodefault', $given ) );
        $this->assertSame( array(), $tpl->operatorParametersWithoutDefault( 'no-such-operator', array() ) );
    }
}
