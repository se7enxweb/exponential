<?php
/**
 * eZModuleFunctionInfo (lib/ezutils), the fetch functions of modules (fetch('module','function',hash(...)) in
 * templates), on a function_definition.php of its own:
 *   - loadDefinition() from the global module path list, a module without a definition file
 *   - execute(): required and optional parameters, defaults, the result array, an error result, every internal
 *     error (missing class, missing method, missing required parameter), an unknown function, a definition
 *     without call method or parameters
 *   - preExecute() and isParameterArray(), the shared object per class
 *
 * The module path list and the shared class objects are restored in tearDown(). Files go to a private directory
 * under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 */

class eZModuleFunctionInfoTestFunctions
{
    public static $instances = 0;

    public function __construct()
    {
        ++self::$instances;
    }

    function greet( $name, $greeting )
    {
        return array( 'result' => "$greeting, $name" );
    }

    function listItems( $items )
    {
        return array( 'result' => count( $items ) );
    }

    function fail()
    {
        return array( 'error' => 'something went wrong' );
    }

    function nothing()
    {
        return array();
    }

    function notAnArray()
    {
        return 'text';
    }
}

class eZModuleFunctionInfoTest extends PHPUnit\Framework\TestCase
{
    private $dir;
    private $savedPaths;
    private $savedObjects;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/phpunit-ezmodulefunction-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir . '/x3fn', 0777, true );
        mkdir( $this->dir . '/x3empty', 0777, true );
        file_put_contents( $this->dir . '/x3fn/function_definition.php', '<?php
$class = array( "class" => "eZModuleFunctionInfoTestFunctions" );
$FunctionList = array();
$FunctionList["greet"] = array( "call_method" => $class + array( "method" => "greet" ),
    "parameters" => array( array( "name" => "name", "type" => "string", "required" => true ),
                           array( "name" => "greeting", "type" => "string", "required" => false, "default" => "Hello" ) ) );
$FunctionList["list"] = array( "call_method" => $class + array( "method" => "listItems" ),
    "parameters" => array( array( "name" => "items", "type" => "array", "required" => true ) ) );
$FunctionList["fail"] = array( "call_method" => $class + array( "method" => "fail" ), "parameters" => array() );
$FunctionList["nothing"] = array( "call_method" => $class + array( "method" => "nothing" ), "parameters" => array() );
$FunctionList["text"] = array( "call_method" => $class + array( "method" => "notAnArray" ), "parameters" => array() );
$FunctionList["noclass"] = array( "call_method" => array( "class" => "NoSuchClassX3", "method" => "x" ), "parameters" => array() );
$FunctionList["nomethod"] = array( "call_method" => $class + array( "method" => "noSuchMethod" ), "parameters" => array() );
$FunctionList["nocall"] = array( "parameters" => array() );
$FunctionList["noparams"] = array( "call_method" => $class + array( "method" => "greet" ) );
$FunctionList["nomethodkey"] = array( "call_method" => $class, "parameters" => array() );
' );
        file_put_contents( $this->dir . '/x3empty/function_definition.php', '<?php
$Nothing = true;
' );
        $this->savedPaths = array_key_exists( 'eZModuleGlobalPathList', $GLOBALS ) ? array( $GLOBALS['eZModuleGlobalPathList'] ) : null;
        $this->savedObjects = array_key_exists( 'eZModuleFunctionClassObjectList', $GLOBALS ) ? array( $GLOBALS['eZModuleFunctionClassObjectList'] ) : null;
        unset( $GLOBALS['eZModuleFunctionClassObjectList'] );
        eZModule::setGlobalPathList( array( $this->dir ) );
    }

    protected function tearDown(): void
    {
        foreach ( array( 'savedPaths' => 'eZModuleGlobalPathList', 'savedObjects' => 'eZModuleFunctionClassObjectList' ) as $property => $global )
        {
            if ( $this->$property === null )
                unset( $GLOBALS[$global] );
            else
                $GLOBALS[$global] = $this->$property[0];
        }
        foreach ( array( 'x3fn', 'x3empty' ) as $sub )
        {
            unlink( $this->dir . '/' . $sub . '/function_definition.php' );
            rmdir( $this->dir . '/' . $sub );
        }
        rmdir( $this->dir );
    }

    private function info()
    {
        $info = new eZModuleFunctionInfo( 'x3fn' );
        $this->assertFalse( $info->isValid() );
        $this->assertTrue( $info->loadDefinition() );
        $this->assertTrue( $info->isValid() );
        return $info;
    }

    public function testExecute()
    {
        $info = $this->info();
        $this->assertSame( 'Hello, Ada', $info->execute( 'greet', array( 'name' => 'Ada' ) ), 'the default of an optional parameter' );
        $this->assertSame( 'Hi, Ada', $info->execute( 'greet', array( 'name' => 'Ada', 'greeting' => 'Hi' ) ) );
        $this->assertSame( 3, $info->execute( 'list', array( 'items' => array( 1, 2, 3 ) ) ) );
    }

    public function testErrorsReturnNull()
    {
        $info = $this->info();
        foreach ( array( 'fail', 'nothing', 'text', 'noclass', 'nomethod', 'nocall', 'noparams', 'nomethodkey', 'no_such_function' ) as $function )
            $this->assertNull( @$info->execute( $function, array() ), $function );
        $this->assertNull( @$info->execute( 'greet', array() ), 'a required parameter is missing' );
    }

    public function testInternalErrors()
    {
        $info = $this->info();
        $this->assertSame( eZModuleFunctionInfo::ERROR_NO_CLASS, $info->executeClassMethod( 'NoSuchClassX3', 'x', array(), array() )['internal_error'] );
        $this->assertSame( eZModuleFunctionInfo::ERROR_NO_CLASS_METHOD, $info->executeClassMethod( 'eZModuleFunctionInfoTestFunctions', 'nope', array(), array() )['internal_error'] );
        $result = $info->executeClassMethod( 'eZModuleFunctionInfoTestFunctions', 'greet',
                                             array( array( 'name' => 'name', 'required' => true ) ), array() );
        $this->assertSame( eZModuleFunctionInfo::ERROR_MISSING_PARAMETER, $result['internal_error'] );
        $this->assertSame( 'name', $result['internal_error_parameter_name'] );
    }

    public function testPreExecute()
    {
        $info = $this->info();
        $definition = $info->preExecute( 'greet' );
        $this->assertSame( 'greet', $definition['call_method']['method'] );
        foreach ( array( 'noclass', 'nomethod', 'nocall', 'noparams', 'nomethodkey', 'no_such_function' ) as $function )
            $this->assertFalse( @$info->preExecute( $function ), $function );
    }

    public function testIsParameterArray()
    {
        $info = $this->info();
        $this->assertTrue( $info->isParameterArray( 'list', 'items' ) );
        $this->assertFalse( $info->isParameterArray( 'greet', 'name' ) );
        $this->assertFalse( $info->isParameterArray( 'greet', 'unknown' ) );
        $this->assertFalse( $info->isParameterArray( 'no_such_function', 'items' ) );
    }

    public function testOneObjectPerClass()
    {
        $info = $this->info();
        $before = eZModuleFunctionInfoTestFunctions::$instances;
        $info->execute( 'greet', array( 'name' => 'a' ) );
        $info->execute( 'greet', array( 'name' => 'b' ) );
        $this->assertSame( $before + 1, eZModuleFunctionInfoTestFunctions::$instances );
        $this->assertSame( $info->objectForClass( 'eZModuleFunctionInfoTestFunctions' ), $info->objectForClass( 'eZModuleFunctionInfoTestFunctions' ) );
    }

    public function testMissingDefinitions()
    {
        $this->assertFalse( @( new eZModuleFunctionInfo( 'x3empty' ) )->loadDefinition(), 'a file without $FunctionList' );
        $this->assertFalse( @( new eZModuleFunctionInfo( 'no_such_module_x3' ) )->loadDefinition() );
        unset( $GLOBALS['eZModuleGlobalPathList'] );
        $this->assertFalse( @( new eZModuleFunctionInfo( 'x3fn' ) )->loadDefinition(), 'no module path list' );
    }
}
