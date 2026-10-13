<?php
/**
 * The login pages offer registering only to a user who may register (L02): user/login.tpl of design/admin,
 * design/admin4 and design/standard, and the login toolbar (design/standard toolbar/full/login.tpl).
 *
 *  RL-01 - every template asks fetch( 'user', 'can_open', hash( 'uri', 'user/register' ) ), once
 *  RL-02 - when the user may open user/register: the register link (or the Sign Up button) is there
 *  RL-03 - when not: it is not there, and the rest of the login form still is
 *  RL-04 - the fetch function can_open is declared by the user module, with the required parameter uri
 *
 * Whether a user may open a view is expViewAccess's answer (tested in expViewAccessTest); here each template is
 * rendered as it is by eZTemplate::factory() (the kernel's operators, no database), with only that fetch call put in
 * a template variable of the test, true or false.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 98854ab935), Copyright (C) Mugo Web, GPL v2
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/content/eZLoginTemplatesRegisterLinkTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class eZLoginTemplatesRegisterLinkTest extends PHPUnit\Framework\TestCase
{
    const FETCH = "fetch( 'user', 'can_open', hash( 'uri', 'user/register' ) )";

    /** @var string private directory of the prepared templates */
    private static $dir;

    private $savedGlobals = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-login-register-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir, 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        foreach ( glob( self::$dir . '/*' ) as $file )
            unlink( $file );
        rmdir( self::$dir );
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
        $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
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
    }

    /** template => the text that only the register branch writes */
    public static function templateProvider()
    {
        return array(
            'admin login'      => array( 'design/admin/templates/user/login.tpl', 'Register new account' ),
            'admin4 login'     => array( 'design/admin4/templates/user/login.tpl', 'Register new account' ),
            'standard login'   => array( 'design/standard/templates/user/login.tpl', 'name="RegisterButton"' ),
            'standard toolbar' => array( 'design/standard/templates/toolbar/full/login.tpl', 'Not registered?' ),
        );
    }

    /** The exception handler in place, without changing it */
    private static function exceptionHandler()
    {
        $current = set_exception_handler( null );
        restore_exception_handler();
        return $current;
    }

    private function render( $template, $mayRegister )
    {
        $source = file_get_contents( $template );
        $this->assertSame( 1, substr_count( $source, self::FETCH ), "RL-01 $template asks can_open once" );
        $file = self::$dir . '/' . md5( $template ) . '.tpl';
        file_put_contents( $file, str_replace( self::FETCH, '$t1_may_register', $source ) );
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 't1_may_register', $mayRegister );
        $tpl->setVariable( 'current_user', array( 'is_logged_in' => false ) );
        $tpl->setVariable( 'placement', 'toolbar-t1' );
        $handler = self::exceptionHandler();
        try
        {
            return $tpl->fetch( $file );
        }
        finally
        {
            // the kernel installs its exception handler when it registers its shutdown handler (eZExecution); the
            // test takes it away again, so PHPUnit finds the handlers it left
            if ( self::exceptionHandler() !== $handler )
                restore_exception_handler();
            $tpl->unsetVariable( 't1_may_register' );
            $tpl->unsetVariable( 'current_user' );
            $tpl->unsetVariable( 'placement' );
        }
    }

    /** RL-01, RL-02 */
    #[\PHPUnit\Framework\Attributes\DataProvider('templateProvider')]
    public function testOfferedToWhoMayRegister( $template, $registerText )
    {
        $html = $this->render( $template, true );
        $this->assertStringContainsString( $registerText, $html );
        if ( strpos( $template, 'templates/user/login.tpl' ) !== false && strpos( $template, 'standard' ) === false )
            $this->assertStringContainsString( '/user/register', $html, 'the link goes to user/register' );
    }

    /** RL-03 */
    #[\PHPUnit\Framework\Attributes\DataProvider('templateProvider')]
    public function testNotOfferedOtherwise( $template, $registerText )
    {
        $html = $this->render( $template, false );
        $this->assertStringNotContainsString( $registerText, $html );
        $this->assertStringNotContainsString( '/user/register', $html );
        $this->assertStringContainsString( 'Login', $html, 'the login form is still there' );
    }

    /** RL-04 */
    public function testTheFetchFunctionIsDeclared()
    {
        $FunctionList = array();
        include 'kernel/user/function_definition.php';
        $this->assertArrayHasKey( 'can_open', $FunctionList );
        $this->assertSame( array( 'class' => 'eZUserFunctionCollection', 'method' => 'canOpen' ), $FunctionList['can_open']['call_method'] );
        $this->assertSame( array( array( 'name' => 'uri', 'type' => 'string', 'required' => true ) ), $FunctionList['can_open']['parameters'] );
    }
}
