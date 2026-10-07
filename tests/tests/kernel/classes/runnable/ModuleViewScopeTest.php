<?php
/**
 * What a module view's run() receives in $scope, and the two ways its code reads it (guide
 * doc/bc/6.0/cli_cronjob_view_abstractions.md). The view classes take the including file's variables by reference
 * ($Params, $Module, $Result, ...), and newer lines read the same values as $scope['Params'] and $scope['Result'].
 *
 *  MS-01 — Through eZProcess::run(), as eZModule runs a view: $Params and $scope['Params'] are one value, and so are
 *          $Result and $scope['Result'], before and after the view writes $Result; $Params['Module'] is the module
 *  MS-02 — A module without 'variable_params' hands the view no $Module and no $scope['Module'];
 *          $scope['Params']['Module'] is there either way
 *  MS-03 — A view of the kernel that reads $scope['Module'] without a fallback belongs to a module with
 *          'variable_params' => true, the only case in which it is set
 *  MS-04 — settings/view and settings/edit answer through the module of $scope['Params'], with and without
 *          'variable_params': the "Select" button redirects, a file the installation does not list is not available
 *
 * No database.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group runnable
 */

class ezpTestModuleViewScopeProbe extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...), as every view class takes them
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $seen = array(
            'keys' => array_keys( $scope ),
            'params_same' => $Params === $scope['Params'],
            'module_variable' => isset( $Module ) ? $Module : null,
            'module_scope' => $scope['Module'] ?? null,
            'module_params' => $scope['Params']['Module'],
            'result_variable_before' => isset( $Result ) ? $Result : null,
            'result_scope_before' => $scope['Result'] ?? null,
        );
        $Result = array( 'content' => 'written through $Result' );
        $Params['Offset'] = 20;
        $seen['result_scope_after'] = $scope['Result'] ?? null;
        $seen['offset_scope_after'] = $scope['Params']['Offset'];
        return $seen;
    }
}

/** A module that records what a view asks of it */
class ezpTestModuleViewScopeModule
{
    public $calls = array();

    public function redirectTo( $uri )
    {
        $this->calls[] = 'redirectTo ' . $uri;
        return 'redirected';
    }

    public function handleError( $errorCode, $errorType )
    {
        $this->calls[] = 'handleError ' . $errorCode . ' ' . $errorType;
        return 'error';
    }

    public function isCurrentAction( $name )
    {
        return false;
    }

    public function hasActionParameter( $name )
    {
        return false;
    }
}

class ModuleViewScopeTest extends PHPUnit\Framework\TestCase
{
    private $post;

    protected function setUp(): void
    {
        parent::setUp();
        $this->post = $_POST;
    }

    protected function tearDown(): void
    {
        $_POST = $this->post;
        parent::tearDown();
    }

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function runProbe( $paramsAsVar, $module )
    {
        return eZProcess::run( __DIR__ . '/fixtures/moduleview_scope_probe.php',
                               array( 'Module' => $module, 'Offset' => 10, 'UserParameters' => array( 'sort' => 'name' ) ),
                               $paramsAsVar );
    }

    /** MS-01 */
    public function testBothWaysReadTheSameValues()
    {
        $module = new stdClass();
        $seen = self::runProbe( true, $module );

        $this->assertIsArray( $seen, 'the view ran and its return value came back' );
        $this->assertContains( 'Params', $seen['keys'] );
        $this->assertContains( 'Result', $seen['keys'], 'eZProcess::runFile() defines $Result before the include' );
        $this->assertTrue( $seen['params_same'] );
        $this->assertSame( $module, $seen['module_params'] );
        $this->assertNull( $seen['result_variable_before'] );
        $this->assertNull( $seen['result_scope_before'], '$scope[\'Result\'] ?? null is what isset( $Result ) ? $Result : null was' );
        $this->assertSame( array( 'content' => 'written through $Result' ), $seen['result_scope_after'], 'one value: written as $Result, read as $scope[\'Result\']' );
        $this->assertSame( 20, $seen['offset_scope_after'], 'one value: written as $Params, read as $scope[\'Params\']' );
    }

    /** MS-02 */
    public function testTheModuleVariableNeedsVariableParams()
    {
        $module = new stdClass();

        $with = self::runProbe( true, $module );
        $this->assertSame( $module, $with['module_variable'] );
        $this->assertSame( $module, $with['module_scope'] );
        $this->assertSame( $module, $with['module_params'] );

        $without = self::runProbe( false, $module );
        $this->assertNotContains( 'Module', $without['keys'] );
        $this->assertNull( $without['module_variable'] );
        $this->assertNull( $without['module_scope'], 'no $scope[\'Module\'] without variable_params' );
        $this->assertSame( $module, $without['module_params'], '$scope[\'Params\'][\'Module\'] is always there' );
    }

    /** The module.php of a module of the kernel, by name */
    private static function moduleDefinition( $name )
    {
        foreach ( array( "kernel/$name/module.php", "kernel/private/modules/$name/module.php" ) as $path )
            if ( is_file( self::root() . '/' . $path ) )
                return $path;
        return null;
    }

    /** MS-03 */
    public function testViewsReadingScopeModuleHaveVariableParams()
    {
        $views = glob( self::root() . '/kernel/private/classes/views/*/*.php' );
        $this->assertGreaterThan( 100, count( $views ), 'the view classes of the kernel were found' );
        foreach ( $views as $file )
        {
            $code = (string)file_get_contents( $file );
            $unguarded = preg_replace( '/isset\( \$scope\[\'Module\'\] \) \? \$scope\[\'Module\'\]/', '', $code );
            if ( strpos( $unguarded, '$scope[\'Module\']' ) === false )
                continue;
            $name = basename( dirname( $file ) );
            $definition = self::moduleDefinition( $name );
            $relative = substr( $file, strlen( self::root() ) + 1 );
            $this->assertNotNull( $definition, "$relative: the module $name is defined in the kernel" );
            // Read, not included: some module.php files fetch from the database for their function limitations
            $source = (string)file_get_contents( self::root() . '/' . $definition );
            $this->assertMatchesRegularExpression( '/\$Module\s*=\s*array\s*\(([^;]*?)[\'"]variable_params[\'"]\s*=>\s*true\b[^;]*\);/s', $source,
                               "$relative reads \$scope['Module'], which $definition does not set: read \$scope['Params']['Module']" );
        }
    }

    /** @return array the settings view's or edit view's answer and what it asked of the module */
    private static function runSettings( $view, $paramsAsVar, array $post )
    {
        $_POST = $post;
        $module = new ezpTestModuleViewScopeModule();
        $params = array( 'Module' => $module, 'SiteAccess' => false, 'INIFile' => false, 'Block' => false,
                         'Setting' => false, 'Placement' => false, 'UserParameters' => array() );
        $result = eZProcess::run( self::root() . '/kernel/settings/' . $view . '.php', $params, $paramsAsVar );
        return array( $result, $module->calls );
    }

    /** MS-04 */
    public function testSettingsViewsAnswerThroughTheModuleOfTheParameters()
    {
        foreach ( array( true, false ) as $paramsAsVar )
        {
            $label = $paramsAsVar ? 'with variable_params' : 'without variable_params';

            list( $result, $calls ) = self::runSettings( 'view', $paramsAsVar, array( 'ChangeINIFile' => '1', 'selectedINIFile' => 'site.ini' ) );
            $this->assertSame( 'redirected', $result, "settings/view $label" );
            $this->assertCount( 1, $calls );
            $this->assertMatchesRegularExpression( '#^redirectTo /settings/view/[^/]*/site\.ini$#', $calls[0] );

            list( $result, $calls ) = self::runSettings( 'edit', $paramsAsVar, array( 'INIFile' => 'no-such-file.ini' ) );
            $this->assertSame( 'error', $result, "settings/edit $label" );
            $this->assertSame( array( 'handleError ' . eZError::KERNEL_NOT_AVAILABLE . ' kernel' ), $calls );
        }
    }
}
