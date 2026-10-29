<?php
/**
 * The operators of the kernel without template hints in Smarty templates: each one is classified as reading its
 * parameters only ('param', expSmartyRenderer::PARAMETER_OPERATORS: ezhttp( 'Name', 'get' )) or its input ('input':
 * $text|wordtoimage), and a .stpl converted from a .tpl that calls it gives the output of the .tpl:
 *   - every operator, with a recording stand-in for its modify() (the same name, hints and named parameters), so
 *     what reaches modify() (parameters, named parameters, and for an input operator its input) is compared without
 *     a database: called alone, with parameters, and with a piped value;
 *   - the operators that run without a database with their own modify(): ezhttp and ezhttp_hasvariable (get, post,
 *     cookie, a missing variable), pdf, alphabet, module_params, exp_environment;
 *   - the form op(null, a, b) the converter wrote before an operator was classified still works;
 *   - smarty.ini [SmartySettings] ParameterOperatorList[] for an operator of an extension.
 *
 * Smarty 5 as in expSmartyTemplateRenderTest (Composer, or SMARTY_LIBRARY_PATH); without it the cases are skipped.
 * No database and no siteaccess.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group smarty
 */

require_once __DIR__ . '/expSmartyOperatorParityTest.php';

class expSmartyParameterOperatorTest extends PHPUnit\Framework\TestCase
{
    /** The kernel's operators without template hints, by how they take their arguments (modify() read for each) */
    const CLASSIFICATION = array(
        'ezhttp' => 'param', 'ezhttp_hasvariable' => 'param', 'icon_info' => 'param', 'alphabet' => 'param',
        'content_structure_tree' => 'param', 'csp_nonce' => 'param', 'exp_demo_mode' => 'param',
        'exp_environment' => 'param', 'exp_environment_badge' => 'param', 'ezmodule' => 'param', 'eztoc' => 'param',
        'has_role' => 'param', 'has_role_id' => 'param', 'has_any_role' => 'param', 'has_role_by_user' => 'param',
        'has_role_by_user_id' => 'param', 'has_policy' => 'param', 'has_policy_by_user' => 'param',
        'installation_name' => 'param', 'is_production_system' => 'param', 'language_switcher' => 'param',
        'module_params' => 'param', 'pdf' => 'param', 'topmenu' => 'param', 'transfer_actions' => 'param',
        'transfer_related' => 'param', 'transfer_tool' => 'param', 'transfer_tools' => 'param', 'treemenu' => 'param',
        'exp_smarty_render' => 'param', 'exp_twig_render' => 'param',
        'action_icon' => 'input', 'class_icon' => 'input', 'classgroup_icon' => 'input', 'flag_icon' => 'input',
        'icon' => 'input', 'mimetype_icon' => 'input', 'wordtoimage' => 'input', 'audit_label' => 'input',
        'autolink' => 'input', 'ezpackage' => 'input', 'month_overview' => 'input', 'simpletags' => 'input',
        'view_parameter' => 'input', 'x18n' => 'input',
    );

    /** @var string directory of this class's template files */
    private static $dir;

    /** @var array superglobals saved by setUp() */
    private $saved = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-smarty-parameter-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir, 0777, true );
        expSmartyOperatorParityTest::trustTestDirectory( self::$dir );
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

    protected function setUp(): void
    {
        $this->saved = array( $_GET, $_POST, $_COOKIE );
        if ( !expSmartyRenderer::loadLibrary() )
            $this->markTestSkipped( 'Smarty 5 is not installed (composer require smarty/smarty, or SMARTY_LIBRARY_PATH)' );
    }

    protected function tearDown(): void
    {
        list( $_GET, $_POST, $_COOKIE ) = $this->saved;
    }

    private function file( $source, $suffix )
    {
        $file = self::$dir . '/' . md5( $source ) . $suffix;
        if ( !file_exists( $file ) )
            file_put_contents( $file, $source );
        return $file;
    }

    private function fetch( $file, array $variables )
    {
        $tpl = eZTemplate::factory();
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        $output = $tpl->fetch( $file );
        foreach ( array_keys( $variables ) as $name )
            $tpl->unsetVariable( $name );
        return $output;
    }

    /**
     * Renders $tplSource as a .tpl and, converted, as a .stpl, and checks both give the same output.
     *
     * @return string the output
     */
    private function assertParity( $tplSource, array $variables = array() )
    {
        $converter = new expSmartyConverter( eZTemplate::factory() );
        $stplSource = $converter->convert( $tplSource, 'parameter.tpl' );
        $expected = $this->fetch( $this->file( $tplSource, '.tpl' ), $variables );
        $actual = $this->fetch( $this->file( $stplSource, '.stpl' ), $variables );
        $this->assertSame( $expected, $actual, "tpl: $tplSource\nstpl: $stplSource" );
        return $actual;
    }

    public static function operatorProvider()
    {
        $rows = array();
        foreach ( self::CLASSIFICATION as $name => $mode )
            $rows[$name] = array( $name, $mode );
        return $rows;
    }

    /** Every operator of the kernel without hints for its name is classified here, and nothing else is. */
    public function testEveryHintlessKernelOperatorIsClassified()
    {
        $tpl = eZTemplate::factory();
        $root = getcwd() . '/';
        $hintless = array();
        foreach ( array_keys( $tpl->Operators ) as $name )
        {
            if ( is_array( $tpl->Operators[$name] ) )
                $tpl->loadAndRegisterOperators( $tpl->Operators[$name] );
            $object = $tpl->Operators[$name];
            if ( !is_object( $object ) )
                continue;
            $file = ( new ReflectionClass( $object ) )->getFileName();
            $relative = strpos( $file, $root ) === 0 ? substr( $file, strlen( $root ) ) : $file;
            if ( strncmp( $relative, 'lib/', 4 ) !== 0 && strncmp( $relative, 'kernel/', 7 ) !== 0 )
                continue;
            $hints = method_exists( $object, 'operatorTemplateHints' ) ? $object->operatorTemplateHints() : array();
            if ( !isset( $hints[$name] ) )
                $hintless[] = $name;
        }
        sort( $hintless );
        $classified = array_keys( self::CLASSIFICATION );
        sort( $classified );
        $this->assertSame( $classified, $hintless );
        $parameterOperators = expSmartyRenderer::PARAMETER_OPERATORS;
        sort( $parameterOperators );
        $expected = array_keys( array_filter( self::CLASSIFICATION, function ( $mode ) { return $mode === 'param'; } ) );
        sort( $expected );
        $this->assertSame( $expected, $parameterOperators );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('operatorProvider')]
    public function testOperatorReceivesTheSameArguments( $name, $mode )
    {
        $this->assertSame( $mode, expSmartyRenderer::operatorInputMode( $name ) );
        $this->assertSame( $mode === 'param', expSmartyRenderer::isParameterOperator( $name ) );
        // the operator of the stub every .stpl is rendered through cannot stand aside while one renders
        if ( $name === 'exp_smarty_render' || $name === 'exp_twig_render' )
            return;

        $tpl = eZTemplate::factory();
        if ( is_array( $tpl->Operators[$name] ) )
            $tpl->loadAndRegisterOperators( $tpl->Operators[$name] );
        $original = $tpl->Operators[$name];
        $tpl->Operators[$name] = new expSmartyRecordingOperator( $original, $name, $mode === 'input' );
        try
        {
            $this->assertParity( "{{$name}()}" );
            $this->assertParity( "{{$name}( 'a', 2 )}" );
            $this->assertParity( "{{$name}( \$p, 'b' )}", array( 'p' => 'from a variable' ) );
            $this->assertParity( "{\$v|{$name}( 'a' )}", array( 'v' => 'piped' ) );
            $output = $this->assertParity( "{'piped'|{$name}( 'a', 'b', 'c' )}" );
            $record = json_decode( $output, true );
            $this->assertSame( array( 'a', 'b', 'c' ), $record['parameters'] );
            if ( $mode === 'input' )
                $this->assertSame( 'piped', $record['input'] );
        }
        finally
        {
            $tpl->Operators[$name] = $original;
        }
    }

    public static function parameterOperatorProvider()
    {
        $rows = array();
        foreach ( self::CLASSIFICATION as $name => $mode )
        {
            // exp_smarty_render: the operator of the stub every .stpl is rendered through cannot stand aside
            if ( $mode === 'param' && $name !== 'exp_smarty_render' && $name !== 'exp_twig_render' )
                $rows[$name] = array( $name );
        }
        return $rows;
    }

    /** The natural form of a parameter operator in a hand-written .stpl is the call of the .tpl */
    #[\PHPUnit\Framework\Attributes\DataProvider('parameterOperatorProvider')]
    public function testHandWrittenCallOfAParameterOperator( $name )
    {
        $tpl = eZTemplate::factory();
        if ( is_array( $tpl->Operators[$name] ) )
            $tpl->loadAndRegisterOperators( $tpl->Operators[$name] );
        $original = $tpl->Operators[$name];
        $tpl->Operators[$name] = new expSmartyRecordingOperator( $original, $name, false );
        try
        {
            $expected = $this->fetch( $this->file( "{{$name}( 'x', 'y' )}", '.tpl' ), array() );
            $this->assertSame( $expected, $this->fetch( $this->file( "{{$name}('x', 'y')}", '.stpl' ), array() ) );
            // the form the converter wrote before the operator was classified
            $this->assertSame( $expected, $this->fetch( $this->file( "{{$name}(null, 'x', 'y')}", '.stpl' ), array() ) );
            $this->assertSame( $expected, $this->fetch( $this->file( "{'x'|{$name}:'y'}", '.stpl' ), array() ) );
        }
        finally
        {
            $tpl->Operators[$name] = $original;
        }
    }

    public function testEzhttpWithItsOwnModify()
    {
        $_GET['ExpSmartyGet'] = 'from get';
        $_POST['ExpSmartyPost'] = 'from post';
        $_COOKIE['ExpSmartyCookie'] = 'from cookie';
        $this->assertSame( 'from get', $this->assertParity( "{ezhttp( 'ExpSmartyGet', 'get' )}" ) );
        $this->assertSame( 'from post', $this->assertParity( "{ezhttp( 'ExpSmartyPost' )}" ) );
        $this->assertSame( 'from post', $this->assertParity( "{ezhttp( 'ExpSmartyPost', 'post' )}" ) );
        $this->assertSame( 'from cookie', $this->assertParity( "{ezhttp( 'ExpSmartyCookie', 'cookie' )}" ) );
        $this->assertSame( 'yes', $this->assertParity( "{if ezhttp( 'ExpSmartyGet', 'get', 'hasVariable' )}yes{/if}" ) );
        $this->assertSame( 'yes', $this->assertParity( "{if ezhttp_hasvariable( 'ExpSmartyGet', 'get' )}yes{/if}" ) );
        $this->assertSame( 'no', $this->assertParity( "{if ezhttp_hasvariable( 'ExpSmartyMissing', 'get' )}yes{else}no{/if}" ) );
        $this->assertSame( 'no', $this->assertParity( "{if ezhttp_hasvariable( 'ExpSmartyGet', 'post' )}yes{else}no{/if}" ) );
        $this->assertSame( 'FROM GET', $this->assertParity( "{ezhttp( \$name, 'get' )|upcase}", array( 'name' => 'ExpSmartyGet' ) ) );
        $this->assertSame( 'from get', $this->assertParity( "{cond( ezhttp_hasvariable( 'ExpSmartyGet', 'get' ), ezhttp( 'ExpSmartyGet', 'get' ), 'none' )}" ) );

        // hand-written: the function form, the form the converter wrote before, and as a modifier
        $this->assertSame( 'from get', $this->fetch( $this->file( "{ezhttp('ExpSmartyGet', 'get')}", '.stpl' ), array() ) );
        $this->assertSame( 'from get', $this->fetch( $this->file( "{ezhttp(null, 'ExpSmartyGet', 'get')}", '.stpl' ), array() ) );
        $this->assertSame( 'from get', $this->fetch( $this->file( "{'ExpSmartyGet'|ezhttp:'get'}", '.stpl' ), array() ) );
        $this->assertSame( 'yes', $this->fetch( $this->file( "{if ezhttp_hasvariable('ExpSmartyGet', 'get')}yes{/if}", '.stpl' ), array() ) );
        $this->assertSame( 'yes', $this->fetch( $this->file( "{if ezhttp_hasvariable(null, 'ExpSmartyGet', 'get')}yes{/if}", '.stpl' ), array() ) );
    }

    public function testOtherDatabaseFreeOperatorsWithTheirOwnModify()
    {
        $this->assertSame( '<C:callNewLine>', $this->assertParity( "{pdf( 'new_line' )}" ) );
        $this->assertSame( '<C:callNewPage><C:callNewLine>', $this->assertParity( "{pdf( 'new_page' )}" ) );
        $this->assertSame( '<C:callNewLine>', $this->fetch( $this->file( "{pdf('new_line')}", '.stpl' ), array() ) );
        $this->assertSame( '<C:callNewLine>', $this->fetch( $this->file( "{pdf(null, 'new_line')}", '.stpl' ), array() ) );
        $this->assertParity( "{alphabet()|count}" );
        $this->assertParity( "{if alphabet()}letters{else}none{/if}" );
        $this->assertParity( "[{module_params()|count}]" );
        $this->assertParity( "[{exp_environment()}]" );
        $this->assertParity( "{if is_production_system()}production{else}not production{/if}" );
    }

    public function testParameterOperatorListSetting()
    {
        $tpl = eZTemplate::factory();
        $ini = eZINI::instance( 'smarty.ini' );
        $saved = $ini->hasVariable( 'SmartySettings', 'ParameterOperatorList' ) ? $ini->variable( 'SmartySettings', 'ParameterOperatorList' ) : array();
        $tpl->Operators['exp_test_parameters_only'] = new expSmartyTestParametersOnlyOperator();
        try
        {
            $tplOutput = $this->fetch( $this->file( "{exp_test_parameters_only( 'a', 'b' )}", '.tpl' ), array() );
            $this->assertSame( 'a|b', $tplOutput );
            $stpl = $this->file( "{exp_test_parameters_only('a', 'b')}", '.stpl' );

            expSmartyRenderer::resetSettings();
            $ini->setVariable( 'SmartySettings', 'ParameterOperatorList', array() );
            $this->assertSame( 'input', expSmartyRenderer::operatorInputMode( 'exp_test_parameters_only' ) );
            $this->assertSame( 'b', $this->fetch( $stpl, array() ), 'without the setting, the first argument is the input' );

            expSmartyRenderer::resetSettings();
            $ini->setVariable( 'SmartySettings', 'ParameterOperatorList', array( 'exp_test_parameters_only' ) );
            $this->assertSame( 'param', expSmartyRenderer::operatorInputMode( 'exp_test_parameters_only' ) );
            $this->assertTrue( expSmartyRenderer::isParameterOperator( 'ezhttp' ), 'the kernel list stays' );
            $this->assertSame( $tplOutput, $this->fetch( $stpl, array() ) );
            $this->assertSame( $tplOutput, $this->fetch( $this->file( "{exp_test_parameters_only(null, 'a', 'b')}", '.stpl' ), array() ) );
            $this->assertSame( $tplOutput, $this->fetch( $this->file( "{'a'|exp_test_parameters_only:'b'}", '.stpl' ), array() ) );
            $this->assertSame( '', $this->fetch( $this->file( "{exp_test_parameters_only()}", '.stpl' ), array() ) );
        }
        finally
        {
            unset( $tpl->Operators['exp_test_parameters_only'] );
            $ini->setVariable( 'SmartySettings', 'ParameterOperatorList', $saved );
            expSmartyRenderer::resetSettings();
        }
    }
}

/**
 * Stands in for an operator: the same name, hints and named parameters; modify() gives what reached it as JSON
 * (the parameters, the named parameters and, for an operator that reads its input, the input).
 */
class expSmartyRecordingOperator
{
    private $inner;
    private $name;
    private $recordInput;

    public function __construct( $inner, $name, $recordInput )
    {
        $this->inner = $inner;
        $this->name = $name;
        $this->recordInput = $recordInput;
    }

    public function operatorList()
    {
        return array( $this->name );
    }

    public function namedParameterPerOperator()
    {
        return method_exists( $this->inner, 'namedParameterPerOperator' ) ? $this->inner->namedParameterPerOperator() : false;
    }

    public function namedParameterList()
    {
        return method_exists( $this->inner, 'namedParameterList' ) ? $this->inner->namedParameterList() : array();
    }

    public function operatorTemplateHints()
    {
        return method_exists( $this->inner, 'operatorTemplateHints' ) ? $this->inner->operatorTemplateHints() : array();
    }

    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement = false )
    {
        $parameters = array();
        foreach ( $operatorParameters as $parameter )
            $parameters[] = $tpl->elementValue( $parameter, $rootNamespace, $currentNamespace );
        $record = array( 'parameters' => $parameters, 'named' => $namedParameters );
        if ( $this->recordInput )
            $record['input'] = $operatorValue;
        $operatorValue = json_encode( $record );
    }
}

/** An operator of an extension without hints that reads its parameters only: they joined with | */
class expSmartyTestParametersOnlyOperator
{
    public function operatorList()
    {
        return array( 'exp_test_parameters_only' );
    }

    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement = false )
    {
        $values = array();
        foreach ( $operatorParameters as $parameter )
            $values[] = $tpl->elementValue( $parameter, $rootNamespace, $currentNamespace );
        $operatorValue = implode( '|', $values );
    }
}
