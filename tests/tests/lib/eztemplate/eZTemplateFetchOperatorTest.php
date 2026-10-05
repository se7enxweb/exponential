<?php
/**
 * The fetch and fetch_alias template operators (eZTemplateExecuteOperator, eZFunctionHandler), interpreted and
 * compiled, on a module function definition of their own:
 *   - fetch with literal parameters, with a hash holding variables, with the whole parameter hash in a variable,
 *     an optional parameter left out (its default), a required parameter left out, an unknown module or function
 *   - fetch_alias with parameter names translated by fetchalias.ini, constants (also list constants with an
 *     escaped semicolon), a constant overridden by a parameter, an alias that is not defined
 *
 * The aliases are injected settings of fetchalias.ini; the module path list, the module function cache and the
 * injected settings are restored in tearDown(). Files go to a private directory under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group eztemplate
 */

class eZTemplateFetchOperatorTestFunctions
{
    function greet( $name, $greeting )
    {
        return array( 'result' => "$greeting, $name" );
    }

    function join( $items, $glue )
    {
        return array( 'result' => implode( $glue, (array)$items ) );
    }
}

class eZTemplateFetchOperatorTest extends PHPUnit\Framework\TestCase
{
    private static $dir;
    private $saved = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-eztemplate-fetch-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir . '/compiled', 0777, true );
        mkdir( self::$dir . '/modules/x3fetch', 0777, true );
        file_put_contents( self::$dir . '/modules/x3fetch/function_definition.php', '<?php
$class = array( "class" => "eZTemplateFetchOperatorTestFunctions" );
$FunctionList = array();
$FunctionList["greet"] = array( "call_method" => $class + array( "method" => "greet" ),
    "parameters" => array( array( "name" => "name", "type" => "string", "required" => true ),
                           array( "name" => "greeting", "type" => "string", "required" => false, "default" => "Hello" ) ) );
$FunctionList["join"] = array( "call_method" => $class + array( "method" => "join" ),
    "parameters" => array( array( "name" => "items", "type" => "array", "required" => true ),
                           array( "name" => "glue", "type" => "string", "required" => false, "default" => "," ) ) );
' );
    }

    public static function tearDownAfterClass(): void
    {
        self::removeTree( self::$dir );
    }

    private static function removeTree( $path )
    {
        if ( !$path || !file_exists( $path ) )
            return;
        if ( is_dir( $path ) )
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

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics', 'eZModuleGlobalPathList', 'eZGlobalModuleFunctionList' ) as $name )
            $this->saved[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        unset( $GLOBALS['eZGlobalModuleFunctionList'] );
        eZModule::setGlobalPathList( array( self::$dir . '/modules' ) );
        eZINI::injectSettings( array( 'fetchalias.ini' => array(
            'x3_hello' => array( 'Module' => 'x3fetch', 'FunctionName' => 'greet', 'Parameter' => array( 'name' => 'who' ),
                                 'Constant' => array( 'greeting' => 'Hey' ) ),
            'x3_join' => array( 'Module' => 'x3fetch', 'FunctionName' => 'join', 'Constant' => array( 'items' => 'a;b\;c;;d', 'glue' => '|' ) ),
        ) ) );
    }

    protected function tearDown(): void
    {
        eZINI::injectSettings( array() );
        foreach ( $this->saved as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
    }

    private function render( $source, $variables, $compiled )
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
        $file = self::$dir . '/' . md5( $source ) . '.tpl';
        if ( !file_exists( $file ) )
            file_put_contents( $file, $source );
        $tpl = new eZTemplate();
        $tpl->autoload();
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        $output = $tpl->fetch( $file );
        return array( $output, $tpl->errorLog(), $tpl->warningLog() );
    }

    public static function caseTable()
    {
        return array(
            'literal parameters'      => array( '{fetch("x3fetch","greet",hash("name","Ada","greeting","Hi"))}', array(), 'Hi, Ada' ),
            'default parameter'       => array( '{fetch("x3fetch","greet",hash("name","Ada"))}', array(), 'Hello, Ada' ),
            'variables in the hash'   => array( '{fetch("x3fetch","greet",hash("name",$n,"greeting",$g))}', array( 'n' => 'Bo', 'g' => 'Yo' ), 'Yo, Bo' ),
            'the hash in a variable'  => array( '{fetch("x3fetch","greet",$p)}', array( 'p' => array( 'name' => 'Cy' ) ), 'Hello, Cy' ),
            'array parameter'         => array( '{fetch("x3fetch","join",hash("items",array("x","y"),"glue","+"))}', array(), 'x+y' ),
            'result in a variable'    => array( '{def $r=fetch("x3fetch","greet",hash("name","Di"))}[{$r}]{undef $r}', array(), '[Hello, Di]' ),
            'alias with translation'  => array( '{fetch_alias("x3_hello",hash("who","Ed"))}', array(), 'Hey, Ed' ),
            'alias with a variable'   => array( '{fetch_alias("x3_hello",hash("who",$w))}', array( 'w' => 'Flo' ), 'Hey, Flo' ),
            'alias list constant'     => array( '{fetch_alias("x3_join")}', array(), 'a|b;c|d' ),
            'alias constant overridden' => array( '{fetch_alias("x3_join",hash("glue","/"))}', array(), 'a/b;c/d' ),
        );
    }

    public static function renderProvider()
    {
        $rows = array();
        foreach ( self::caseTable() as $name => $case )
        {
            $rows["$name, interpreted"] = array( $case[0], $case[1], false, $case[2] );
            $rows["$name, compiled"] = array( $case[0], $case[1], true, $case[2] );
        }
        return $rows;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('renderProvider')]
    public function testFetch( $source, $variables, $compiled, $expected )
    {
        list( $output, $errors ) = $this->render( $source, $variables, $compiled );
        $this->assertSame( array(), $errors, 'template errors' );
        $this->assertSame( $expected, $output );
    }

    public static function modeProvider()
    {
        return array( 'interpreted' => array( false ), 'compiled' => array( true ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modeProvider')]
    public function testFailedFetchesRenderNothing( $compiled )
    {
        $mode = $compiled ? 'c' : 'i';
        foreach ( array(
            '[{fetch("x3fetch","greet",hash())}]',
            '[{fetch("x3fetch","no_such_function",hash())}]',
            '[{fetch("no_such_module_x3","greet",hash())}]',
        ) as $source )
        {
            list( $output ) = @$this->render( $source . '{* ' . $mode . ' *}', array(), $compiled );
            $this->assertSame( '[]', $output, $source );
        }
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modeProvider')]
    public function testUndefinedAliasRendersNothing( $compiled )
    {
        list( $output, $errors, $warnings ) = @$this->render( '[{fetch_alias("x3_no_such_alias")}]{* ' . ( $compiled ? 'c' : 'i' ) . ' *}', array(), $compiled );
        $this->assertSame( '[]', $output );
    }
}
