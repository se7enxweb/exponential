<?php
/**
 * eZModule (lib/ezutils), the module and view dispatcher, on a module of its own in a private directory:
 *   - loading module.php: views, functions (sorted), the view URIs, a module file that does not exist, a single
 *     view module, setCurrentName(), the attribute interface, UI context and component (also per view)
 *   - run(): ordered, unordered (also one that ends the URL without a value) and user parameters reach the view as
 *     $Params, override parameters, the view's $Result is returned, an undefined view fails, the exit status
 *   - redirection URIs: ordered, unordered and user parameters, an anchor, redirectToView() and redirectTo() with
 *     the redirect status
 *   - actions: default actions, single post actions, post actions, action parameters (named and by value
 *     pattern), setCurrentAction()
 *   - hooks: functions and object methods, with and without parameters, priorities and append, a cancelled run,
 *     a missing hook function
 *   - view results, exists() and findModule() with a path list, the global path list
 *
 * POST variables, the eZHTTPTool instance and the current view globals are restored in tearDown(). Module files go
 * to a private directory under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 */

function eZModuleTestHookFunction( $module, $a = null, $b = null )
{
    $GLOBALS['eZModuleTestHookLog'][] = 'function:' . $a . ',' . $b;
    return eZModule::HOOK_STATUS_OK;
}

function eZModuleTestCancelHook( $module )
{
    $GLOBALS['eZModuleTestHookLog'][] = 'cancel';
    return eZModule::HOOK_STATUS_CANCEL_RUN;
}

class eZModuleTestHookObject
{
    function hook( $module, $parameters = null )
    {
        $GLOBALS['eZModuleTestHookLog'][] = 'method:' . ( is_array( $parameters ) ? implode( ',', $parameters ) : var_export( $parameters, true ) );
        return eZModule::HOOK_STATUS_OK;
    }
}

class eZModuleTest extends PHPUnit\Framework\TestCase
{
    private $dir;
    private $saved;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/phpunit-ezmodule-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir . '/x3mod', 0777, true );
        mkdir( $this->dir . '/x3single', 0777, true );
        file_put_contents( $this->dir . '/x3mod/module.php', '<?php
$Module = array( "name" => "X3 test module", "variable_params" => true, "ui_component_match" => "view" );
$ViewList = array();
$ViewList["show"] = array(
    "script" => "show.php",
    "params" => array( "ID", "Language" ),
    "unordered_params" => array( "offset" => "Offset", "view" => "ViewMode" ),
    "ui_context" => "browse",
    "default_action" => array( array( "name" => "Store", "type" => "post", "parameters" => array( "StoreButton", "Data" ) ) ),
    "single_post_actions" => array( "RemoveButton" => "Remove" ),
    "post_actions" => array( "ActionName" ),
    "post_action_parameters" => array( "Remove" => array( "Items" => "DeleteIDArray" ) ),
    "post_value_action_parameters" => array( "Edit" => array( "Item" => "EditItem" ) ) );
$ViewList["list"] = array( "script" => "list.php", "params" => array(), "ui_component" => "lists" );
$FunctionList = array( "write" => array(), "read" => array() );
' );
        file_put_contents( $this->dir . '/x3mod/show.php', '<?php
$Result = array( "content" => "ID=" . $ID . " Language=" . var_export( $Language, true ) . " Offset=" . var_export( $Offset, true ) .
                 " ViewMode=" . var_export( $ViewMode, true ) . " User=" . json_encode( $UserParameters ?? null ) .
                 " Extra=" . ( $Extra ?? "-" ) . " Module=" . $ModuleName . "/" . $FunctionName );
' );
        file_put_contents( $this->dir . '/x3mod/list.php', '<?php
$Result = array( "content" => "list" );
' );
        file_put_contents( $this->dir . '/x3single/module.php', '<?php
$Module = array( "name" => "Single", "function" => array( "script" => "single.php", "params" => array( "Name" ) ) );
' );
        file_put_contents( $this->dir . '/x3single/single.php', '<?php
$Result = array( "content" => "single " . $Params["Name"] );
' );

        $this->saved = array(
            'post' => $_POST,
            'http' => $GLOBALS['eZHTTPToolInstance'] ?? null,
            'view' => array_key_exists( 'eZModuleCurrentView', $GLOBALS ) ? array( $GLOBALS['eZModuleCurrentView'] ) : null,
            'paths' => array_key_exists( 'eZModuleGlobalPathList', $GLOBALS ) ? array( $GLOBALS['eZModuleGlobalPathList'] ) : null,
        );
        $_POST = array();
        unset( $GLOBALS['eZHTTPToolInstance'] );
        $GLOBALS['eZModuleTestHookLog'] = array();
    }

    protected function tearDown(): void
    {
        $_POST = $this->saved['post'];
        if ( $this->saved['http'] !== null )
            $GLOBALS['eZHTTPToolInstance'] = $this->saved['http'];
        else
            unset( $GLOBALS['eZHTTPToolInstance'] );
        foreach ( array( 'view' => 'eZModuleCurrentView', 'paths' => 'eZModuleGlobalPathList' ) as $key => $global )
        {
            if ( $this->saved[$key] === null )
                unset( $GLOBALS[$global] );
            else
                $GLOBALS[$global] = $this->saved[$key][0];
        }
        unset( $GLOBALS['eZModuleTestHookLog'], $GLOBALS['eZRequestedModuleParams'] );
        foreach ( array( 'x3mod', 'x3single' ) as $sub )
        {
            foreach ( glob( $this->dir . '/' . $sub . '/*' ) as $file )
                unlink( $file );
            rmdir( $this->dir . '/' . $sub );
        }
        rmdir( $this->dir );
    }

    private function module()
    {
        return new eZModule( $this->dir, $this->dir . '/x3mod/module.php', 'x3mod' );
    }

    public function testLoading()
    {
        $m = $this->module();
        $this->assertSame( '/x3mod', $m->uri() );
        $this->assertSame( '/x3mod/show', $m->functionURI( 'show' ) );
        $this->assertSame( '/x3mod', $m->functionURI( '' ) );
        $this->assertNull( $m->functionURI( 'nothing' ) );
        $this->assertFalse( $m->singleFunction() );
        $this->assertSame( array( 'read', 'write' ), array_keys( $m->FunctionList ), 'functions sorted by name' );
        $this->assertSame( array( 'ID', 'Language' ), $m->parameters( 'show' ) );
        $this->assertSame( array( 'offset' => 'Offset', 'view' => 'ViewMode' ), $m->unorderedParameters( 'show' ) );
        $this->assertNull( $m->parameters( 'nothing' ), 'a view the module does not have' );
        $this->assertSame( 'navigation', $m->uiContextName() );
        $this->assertSame( 'x3mod', $m->uiComponentName() );

        $m->setCurrentName( 'renamed' );
        $this->assertSame( '/renamed/show', $m->functionURI( 'show' ) );

        $this->assertTrue( $m->hasAttribute( 'name' ) );
        $this->assertSame( 'renamed', $m->attribute( 'name' ) );
        $this->assertSame( '/renamed', $m->attribute( 'uri' ) );
        $this->assertArrayHasKey( 'show', $m->attribute( 'views' ) );
    }

    public function testMissingModuleFile()
    {
        $m = new eZModule( $this->dir, $this->dir . '/nothing/module.php', 'nothing' );
        $this->assertTrue( $m->singleFunction() );
        $this->assertSame( 'null', $m->Module['name'] );
        $this->assertSame( '/nothing', $m->functionURI( 'anything' ) );
    }

    public function testRunPassesTheParameters()
    {
        $m = $this->module();
        $result = $m->run( 'show', array( '42', 'eng-GB', 'offset', '20', 'view', 'full' ), array( 'Extra' => 'x' ), array( 'year' => '2025' ) );
        $this->assertSame( 'ID=42 Language=\'eng-GB\' Offset=\'20\' ViewMode=\'full\' User={"year":"2025"} Extra=x Module=x3mod/show', $result['content'] );
        $this->assertSame( eZModule::STATUS_OK, $m->exitStatus() );
        $this->assertSame( 'browse', $m->uiContextName() );
        $this->assertSame( 'show', $m->uiComponentName(), 'the component follows the view' );
        $this->assertSame( 'show', $GLOBALS['eZRequestedModuleParams']['function_name'] );
        $this->assertSame( '42', $GLOBALS['eZRequestedModuleParams']['parameters']['ID'] );
        $this->assertSame( array( 'ID' => '42', 'Language' => 'eng-GB' ), $m->getNamedParameters(), 'the ordered parameters by name' );
    }

    public function testRunWithMissingParameters()
    {
        $m = $this->module();
        $result = $m->run( 'show', array( '7' ) );
        $this->assertSame( 'ID=7 Language=NULL Offset=false ViewMode=false User=null Extra=- Module=x3mod/show', $result['content'] );
    }

    /**
     * An unordered parameter at the very end of the URL, without its value, is no value, as if it were absent.
     */
    public function testUnorderedParameterWithoutAValue()
    {
        $m = $this->module();
        $result = $m->run( 'show', array( '7', 'eng-GB', 'offset' ) );
        $this->assertStringContainsString( 'Offset=false', $result['content'] );
    }

    public function testRunOfAnUndefinedView()
    {
        $m = $this->module();
        $this->assertNull( @$m->run( 'nothing' ) );
        $this->assertSame( eZModule::STATUS_FAILED, $m->exitStatus() );
    }

    public function testSingleViewModule()
    {
        $m = new eZModule( $this->dir, $this->dir . '/x3single/module.php', 'x3single' );
        $this->assertTrue( $m->singleFunction() );
        $result = $m->run( '', array( 'there' ) );
        $this->assertSame( 'single there', $result['content'] );
        $this->assertSame( array( 'Name' ), $m->parameters( 'anything' ) );
    }

    public function testRedirectionUris()
    {
        $m = $this->module();
        // view URIs keep one trailing slash; several are reduced to one
        $this->assertSame( '/x3mod/show/42/eng-GB/', $m->redirectionURIForModule( $m, 'show', array( 42, 'eng-GB' ) ) );
        $this->assertSame( '/x3mod/show/42/offset/10/', $m->redirectionURIForModule( $m, 'show', array( 42 ), array( 'Offset' => 10 ) ) );
        $this->assertSame( '/x3mod/show/42/(year)/2025/(page)/2', $m->redirectionURIForModule( $m, 'show', array( 42 ), null, array( 'year' => 2025, 'page' => 2 ) ) );
        $this->assertSame( '/x3mod/show/42/#part+1', $m->redirectionURIForModule( $m, 'show', array( 42 ), null, false, 'part 1' ) );
        $this->assertSame( '/x3mod/list/', $m->redirectionURIForModule( $m, 'list' ) );
    }

    public function testRedirectToView()
    {
        $m = $this->module();
        $this->assertTrue( $m->redirectToView( 'show', array( 5 ) ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $m->exitStatus() );
        $this->assertSame( '/x3mod/show/5', $m->redirectURI() );

        $m->redirectTo( '/some/path///' );
        $this->assertSame( '/some/path', $m->redirectURI(), 'trailing slashes go' );
        $m->redirectTo( '/' );
        $this->assertSame( '/', $m->redirectURI() );
        $m->setRedirectStatus( '301 Moved Permanently' );
        $this->assertSame( '301 Moved Permanently', $m->redirectStatus() );
        $m->setRedirectURI( '/elsewhere' );
        $this->assertSame( '/elsewhere', $m->redirectURI() );
    }

    public function testDefaultAction()
    {
        $_POST = array( 'StoreButton' => '1', 'Data' => 'x' );
        $m = $this->module();
        $this->assertSame( 'Store', $m->currentAction( 'show' ) );
        $this->assertTrue( $m->isCurrentAction( 'Store', 'show' ) );
    }

    public function testDefaultActionNeedsAllItsParameters()
    {
        $_POST = array( 'StoreButton' => '1' );
        $this->assertFalse( $this->module()->currentAction( 'show' ) );
    }

    public function testSinglePostActionAndItsParameters()
    {
        $_POST = array( 'RemoveButton' => '1', 'DeleteIDArray' => array( 3, 4 ) );
        $m = $this->module();
        $this->assertSame( 'Remove', $m->currentAction( 'show' ) );
        $this->assertTrue( $m->hasActionParameter( 'Items', 'show' ) );
        $this->assertSame( array( 3, 4 ), $m->actionParameter( 'Items', 'show' ) );
        $this->assertFalse( $m->hasActionParameter( 'Other', 'show' ) );
    }

    public function testPostActionByValueAndValueParameters()
    {
        $_POST = array( 'ActionName' => 'Edit', 'EditItem_17' => 'Go' );
        $m = $this->module();
        $this->assertSame( 'Edit', $m->currentAction( 'show' ) );
        $this->assertTrue( $m->hasActionParameter( 'Item', 'show' ) );
        $this->assertSame( '17', $m->actionParameter( 'Item', 'show' ) );
    }

    public function testSetCurrentActionAndParameters()
    {
        $m = $this->module();
        $this->assertFalse( $m->setCurrentAction( '', 'show' ) );
        $m->setCurrentAction( 'Custom', 'show' );
        $this->assertSame( 'Custom', $m->currentAction( 'show' ) );
        $m->setActionParameter( 'P', 'v', 'show' );
        $this->assertTrue( $m->hasActionParameter( 'P', 'show' ) );
        $this->assertSame( 'v', $m->actionParameter( 'P', 'show' ) );
        $this->assertFalse( $m->isCurrentAction( '', 'show' ) );
    }

    public function testHooksRunInPriorityOrder()
    {
        $m = $this->module();
        $object = new eZModuleTestHookObject();
        $m->addHook( 'h', 'eZModuleTestHookFunction', 2 );
        $m->addHook( 'h', array( $object, 'hook' ), 1, false );
        $m->addHook( 'h', 'eZModuleTestHookFunction', 2, true, true ); // appended after the first of priority 2
        $this->assertNull( $m->runHooks( 'h', array( 'a', 'b' ) ) );
        $this->assertSame( array( 'method:a,b', 'function:a,b', 'function:a,b' ), $GLOBALS['eZModuleTestHookLog'] );
    }

    /**
     * An object hook run without parameters is called by its method name; the whole callable array was used as the
     * method name, which fails.
     */
    public function testObjectHookWithoutParameters()
    {
        $m = $this->module();
        $m->addHook( 'h', array( new eZModuleTestHookObject(), 'hook' ) );
        $m->runHooks( 'h' );
        $this->assertSame( array( 'method:NULL' ), $GLOBALS['eZModuleTestHookLog'] );
    }

    public function testCancelledRunStopsTheHooks()
    {
        $m = $this->module();
        $m->addHook( 'h', 'eZModuleTestCancelHook', 1 );
        $m->addHook( 'h', 'eZModuleTestHookFunction', 5 );
        $this->assertSame( eZModule::HOOK_STATUS_CANCEL_RUN, $m->runHooks( 'h' ) );
        $this->assertSame( array( 'cancel' ), $GLOBALS['eZModuleTestHookLog'] );
    }

    /**
     * A hook function that does not exist is reported and the hooks after it still run, without the status of
     * the hook before it.
     */
    public function testMissingHookFunction()
    {
        $m = $this->module();
        $m->addHook( 'h', 'eZModuleTestCancelHook', 1 );
        $m->addHook( 'h', 'no_such_hook_function_x3', 2 );
        $m->addHook( 'h', 'eZModuleTestHookFunction', 3 );
        $this->assertSame( eZModule::HOOK_STATUS_CANCEL_RUN, @$m->runHooks( 'h' ) );

        $n = $this->module();
        $n->addHook( 'h', 'no_such_hook_function_x3', 1 );
        $n->addHook( 'h', 'eZModuleTestHookFunction', 2 );
        $this->assertNull( @$n->runHooks( 'h' ) );
        $this->assertSame( array( 'cancel', 'function:,' ), $GLOBALS['eZModuleTestHookLog'] );
        $this->assertNull( $n->runHooks( 'no-such-hook' ) );
    }

    public function testViewResults()
    {
        $m = $this->module();
        $this->assertFalse( $m->hasViewResult( 'show' ) );
        $this->assertNull( $m->viewResult( 'show' ) );
        $m->setViewResult( array( 'x' ), 'show' );
        $this->assertTrue( $m->hasViewResult( 'show' ) );
        $this->assertSame( array( 'x' ), $m->viewResult( 'show' ) );
    }

    public function testExistsAndFindModuleWithAPathList()
    {
        $module = eZModule::exists( 'x3mod', array( $this->dir ) );
        $this->assertInstanceOf( eZModule::class, $module );
        $this->assertSame( '/x3mod/list', $module->functionURI( 'list' ) );
        $this->assertNull( eZModule::exists( 'no_such_module_x3', array( $this->dir ) ) );

        eZModule::setGlobalPathList( array( $this->dir ) );
        $this->assertSame( array( $this->dir ), eZModule::globalPathList() );
        eZModule::addGlobalPathList( array( 'another' ) );
        $this->assertContains( 'another', eZModule::globalPathList() );
    }

    public function testAddGlobalPathListBeforeAnyIsSetRaisesNoWarning()
    {
        unset( $GLOBALS['eZModuleGlobalPathList'] );
        $warnings = array();
        set_error_handler( function ( $no, $str ) use ( &$warnings ) {
            $warnings[] = $str;
            return true;
        } );
        try
        {
            eZModule::addGlobalPathList( 'first' );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertSame( array(), $warnings );
        $this->assertSame( array( 'first' ), eZModule::globalPathList() );
    }

    public function testTitleAndStatus()
    {
        $m = $this->module();
        $m->setTitle( 'A title' );
        $this->assertSame( 'A title', $m->title() );
        $this->assertSame( eZModule::STATUS_IDLE, $m->exitStatus() );
        $m->setExitStatus( eZModule::STATUS_RERUN );
        $this->assertSame( eZModule::STATUS_RERUN, $m->exitStatus() );
        $m->setErrorCode( 3 );
        $this->assertSame( 3, $m->errorCode() );
        $m->setUIContextName( 'edit' );
        $m->setUIComponentName( 'comp' );
        $this->assertSame( 'edit', $m->uiContextName() );
        $this->assertSame( 'comp', $m->uiComponentName() );
    }
}
