<?php
/**
 * The second sign-in step on the paths outside the web login form (kernel/classes/user/expsigninsecondfactor.php,
 * doc/eng-us/features/6.0/second-factor-on-other-sign-in-paths.md). DB-less: the handlers are the test's own
 * (expSignInSecondFactor::$handlers), the accounts are eZUser objects of the test's own, and the wiring of each path
 * is read from the source files.
 *
 *  SF-01  A refusal: known reasons only, a message of its own when the handler gives none, 401 (other credentials
 *         can help) or 403 (second_factor_setup_required), the channel
 *  SF-02  The decision: no handler allows everything; the first handler that refuses decides; anything that is no
 *         handler is skipped; a blank code is no code
 *  SF-03  check(): anonymous never refused; the refusal of the request is kept (lastRefusal()) and replaced by the
 *         next check, so a persistent worker carries nothing over
 *  SF-04  App passwords: nothing without a login and a secret, a handler that finds none gives none, a disabled
 *         account is never signed in by one
 *  SF-05  The one-time code of a request: the header (X-Exp-OTP), else the field otp; digits, spaces and dashes only
 *  SF-06  The answers: WWW-Authenticate, the status line, the SOAP fault (well formed, escaped), the REST status
 *         (401 with WWW-Authenticate and JSON, 403 without)
 *  SF-07  REST: a password alone is checked before the request is signed in; a personal API key, an OAuth token and
 *         an app password are not "a password alone"; the app password is tried before the password
 *  SF-08  WebDAV, SOAP and the expservices login: app password first (not on expservices), the check after the
 *         password, a refusal answered in the protocol's own way; the error envelope carries the reason
 *  SF-09  The audit knows the four names with their verbs and labels, audit.md (eng-us and ger-de) lists them, the
 *         complete kernel catalogues translate the labels
 *  SF-10  Settings: site.ini has no handler of its own and the header X-Exp-OTP; the classes are in the autoload
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/user/expSignInSecondFactorTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

/** A handler of the test: answers what it is told and records what it was asked */
class expSignInSecondFactorTestHandler implements expSignInSecondFactorHandler
{
    public $answer = null;
    public $appUser = null;
    public $asked = array();

    public function checkSignIn( eZUser $user, $channel, $code )
    {
        $this->asked[] = array( (int)$user->attribute( 'contentobject_id' ), $channel, $code );
        return $this->answer;
    }

    public function appPasswordUser( $login, $secret, $channel )
    {
        $this->asked[] = array( $login, 'app', $channel );
        return $this->appUser;
    }
}

class expSignInSecondFactorTest extends PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        expSignInSecondFactor::$handlers = null;
        parent::tearDown();
    }

    private static function file( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 5 ) . '/' . $path );
    }

    /** An account that needs no database */
    private static function account( $id, $enabled = true )
    {
        $user = new class( array( 'contentobject_id' => $id, 'login' => 'sf' . $id, 'email' => 'sf' . $id . '@example.org',
                                  'password_hash' => 'h', 'password_hash_type' => 7 ) ) extends eZUser {
            public $enabledForTest = true;
            function isEnabled( $useCache = true )
            {
                return $this->enabledForTest;
            }
            function isAnonymous()
            {
                return false;
            }
        };
        $user->enabledForTest = $enabled;
        return $user;
    }

    private static function handler( $answer = null, $appUser = null )
    {
        $handler = new expSignInSecondFactorTestHandler();
        $handler->answer = $answer;
        $handler->appUser = $appUser;
        return $handler;
    }

    // ------------------------------------------------------------------ SF-01

    public function testARefusal()
    {
        $r = expSignInSecondFactor::refusal( 'second_factor_required', '', 'WebDAV' );
        $this->assertSame( array( 'reason', 'message', 'status', 'channel' ), array_keys( $r ) );
        $this->assertSame( 401, $r['status'] );
        $this->assertSame( 'webdav', $r['channel'] );
        $this->assertStringContainsString( 'app password', $r['message'] );

        $setup = expSignInSecondFactor::refusal( 'second_factor_setup_required', "Set it up\nfirst.", 'rest' );
        $this->assertSame( 403, $setup['status'], 'only the web interface can help' );
        $this->assertSame( 'Set it up first.', $setup['message'], 'one line' );

        $odd = expSignInSecondFactor::refusal( 'something else', 'x', 'ftp' );
        $this->assertSame( 'second_factor_required', $odd['reason'], 'an unknown reason is the plain one' );
        $this->assertSame( 'other', $odd['channel'] );
        $this->assertSame( 401, expSignInSecondFactor::refusal( 'second_factor_invalid', '', 'soap' )['status'] );
        $this->assertSame( array( 'rest', 'webdav', 'soap', 'expservices' ), expSignInSecondFactor::channels() );
    }

    // ------------------------------------------------------------------ SF-02

    public function testTheDecision()
    {
        $user = self::account( 41 );
        $this->assertNull( expSignInSecondFactor::decide( array(), $user, 'rest', null ), 'no handler: nothing changes' );

        $allow = self::handler( null );
        $refuse = self::handler( array( 'reason' => 'second_factor_invalid', 'message' => 'Wrong code.' ) );
        $later = self::handler( array( 'reason' => 'second_factor_setup_required' ) );
        $r = expSignInSecondFactor::decide( array( new stdClass(), $allow, $refuse, $later ), $user, 'expservices', ' 123 456 ' );
        $this->assertSame( 'second_factor_invalid', $r['reason'] );
        $this->assertSame( 'Wrong code.', $r['message'] );
        $this->assertSame( array( array( 41, 'expservices', '123 456' ) ), $allow->asked, 'the code is passed trimmed' );
        $this->assertSame( array(), $later->asked, 'the first refusal decides' );

        expSignInSecondFactor::decide( array( $allow ), $user, 'webdav', '   ' );
        $this->assertNull( end( $allow->asked )[2], 'a blank code is no code' );
    }

    // ------------------------------------------------------------------ SF-03

    public function testCheck()
    {
        $handler = self::handler( array( 'reason' => 'second_factor_required', 'message' => '' ) );
        expSignInSecondFactor::$handlers = array( $handler );
        $this->assertNull( expSignInSecondFactor::check( null, 'rest' ) );
        $this->assertSame( array(), $handler->asked, 'no account, no question' );

        $r = expSignInSecondFactor::check( self::account( 42 ), 'webdav' );
        $this->assertSame( 'second_factor_required', $r['reason'] );
        $this->assertSame( $r, expSignInSecondFactor::lastRefusal() );

        $handler->answer = null;
        $this->assertNull( expSignInSecondFactor::check( self::account( 42 ), 'webdav' ) );
        $this->assertNull( expSignInSecondFactor::lastRefusal(), 'the next check replaces it' );
    }

    // ------------------------------------------------------------------ SF-04

    public function testAppPasswords()
    {
        $owner = self::account( 43 );
        $handler = self::handler( null, null );
        expSignInSecondFactor::$handlers = array( $handler );
        $this->assertNull( expSignInSecondFactor::appPasswordUser( '', 'secret', 'webdav' ) );
        $this->assertNull( expSignInSecondFactor::appPasswordUser( 'sf43', '', 'webdav' ) );
        $this->assertSame( array(), $handler->asked, 'nothing to ask without a login and a secret' );
        $this->assertNull( expSignInSecondFactor::appPasswordUser( 'sf43', 'abcd-efgh', 'WEBDAV' ) );
        $this->assertSame( array( array( 'sf43', 'app', 'webdav' ) ), $handler->asked );

        $handler->appUser = self::account( 43, false );
        $this->assertNull( expSignInSecondFactor::appPasswordUser( 'sf43', 'abcd-efgh', 'rest' ), 'a disabled account never' );
        $this->assertTrue( $owner->isEnabled() );
    }

    // ------------------------------------------------------------------ SF-05

    public function testTheOneTimeCodeOfARequest()
    {
        $this->assertSame( 'HTTP_X_EXP_OTP', expSignInSecondFactor::serverKey( 'X-Exp-OTP' ) );
        $this->assertSame( '123456', expSignInSecondFactor::presentedCode( array( 'HTTP_X_EXP_OTP' => ' 123456 ' ), array(), 'X-Exp-OTP' ) );
        $this->assertSame( '654321', expSignInSecondFactor::presentedCode( array(), array( 'otp' => '654321' ), 'X-Exp-OTP' ) );
        $this->assertSame( '111111', expSignInSecondFactor::presentedCode( array( 'HTTP_X_EXP_OTP' => '111111' ), array( 'otp' => '222222' ), 'X-Exp-OTP' ),
                           'the header first' );
        $this->assertSame( '123 456', expSignInSecondFactor::presentedCode( array(), array( 'otp' => '123 456' ), 'X-Exp-OTP' ) );
        $this->assertNull( expSignInSecondFactor::presentedCode( array(), array(), 'X-Exp-OTP' ) );
        $this->assertNull( expSignInSecondFactor::presentedCode( array( 'HTTP_X_EXP_OTP' => '12a456' ), array(), 'X-Exp-OTP' ) );
        $this->assertNull( expSignInSecondFactor::presentedCode( array(), array( 'otp' => array( '1' ) ), 'X-Exp-OTP' ) );
        $this->assertNull( expSignInSecondFactor::presentedCode( array(), array( 'otp' => str_repeat( '1', 40 ) ), 'X-Exp-OTP' ) );
        $this->assertSame( 'HTTP_X_MY_CODE', expSignInSecondFactor::serverKey( 'X-My-Code' ) );
    }

    // ------------------------------------------------------------------ SF-06

    public function testTheAnswers()
    {
        $this->assertSame( 'Basic realm="Exponential \"DAV\"", charset="UTF-8"', expSignInSecondFactor::wwwAuthenticate( 'Exponential "DAV"' ) );
        $this->assertSame( '401 Unauthorized', expSignInSecondFactor::refusalStatus( expSignInSecondFactor::refusal( 'second_factor_required', '', 'soap' ) ) );
        $this->assertSame( '403 Forbidden', expSignInSecondFactor::refusalStatus( expSignInSecondFactor::refusal( 'second_factor_setup_required', '', 'soap' ) ) );

        $fault = expSignInSecondFactor::soapFault( expSignInSecondFactor::refusal( 'second_factor_required', 'Use <an> app & password', 'soap' ) );
        $xml = simplexml_load_string( $fault );
        $this->assertNotFalse( $xml, 'well formed' );
        $this->assertStringContainsString( '<faultcode>SOAP-ENV:Client</faultcode>', $fault );
        $this->assertStringContainsString( 'Use &lt;an&gt; app &amp; password', $fault );
        $this->assertStringContainsString( '<reason>second_factor_required</reason>', $fault );

        $body = expRestSecondFactorRequiredStatus::body( 'second_factor_setup_required', '' );
        $this->assertSame( 'second_factor_setup_required', $body['error'] );
        $this->assertNotSame( '', $body['error_description'] );

        $writer = new ezpRestHttpResponseWriter( new ezcMvcResponse() );
        ( new expRestSecondFactorRequiredStatus( 'second_factor_required', 'Use an app password.' ) )->process( $writer );
        $this->assertArrayHasKey( 'HTTP/1.1 401', $writer->headers );
        $this->assertSame( 'Basic realm="Exponential REST", charset="UTF-8"', $writer->headers['WWW-Authenticate'] );
        $this->assertSame( 'second_factor_required', $writer->headers['X-Exp-Sign-In-Refused'] );
        $this->assertSame( 'private, no-store', $writer->headers['Cache-Control'] );
        $this->assertSame( array( 'error' => 'second_factor_required', 'error_description' => 'Use an app password.' ),
                           json_decode( $writer->response->body, true ) );

        $writer = new ezpRestHttpResponseWriter( new ezcMvcResponse() );
        ( new expRestSecondFactorRequiredStatus( 'second_factor_setup_required' ) )->process( $writer );
        $this->assertArrayHasKey( 'HTTP/1.1 403', $writer->headers );
        $this->assertArrayNotHasKey( 'WWW-Authenticate', $writer->headers );
    }

    // ------------------------------------------------------------------ SF-07

    public function testTheRestInterface()
    {
        $config = self::file( 'kernel/private/rest/classes/auth/auth_configuration.php' );
        $check = strpos( $config, "expSignInSecondFactor::check( \$user, expSignInSecondFactor::CHANNEL_REST, expSignInSecondFactor::presentedCode() )" );
        $signIn = strpos( $config, 'eZUser::setCurrentlyLoggedInUser( $user, $userID )' );
        $this->assertNotFalse( $check );
        $this->assertLessThan( $signIn, $check, 'refused before the request is signed in' );
        $this->assertStringContainsString( "self::signedInWithPassword( \$this->filter )", $config );
        $this->assertStringContainsString( "expRestPasswordAuthFilter::\$viaAppPassword = false;", $config, 'reset per request' );

        $basic = ( new ReflectionClass( 'ezpRestBasicAuthStyle' ) )->newInstanceWithoutConstructor();
        $oauth = ( new ReflectionClass( 'ezpRestOauthAuthenticationStyle' ) )->newInstanceWithoutConstructor();
        $before = array( expRestPasswordAuthFilter::$user, expRestPasswordAuthFilter::$viaAppPassword );
        expApiKeyRest::reset();
        expRestPasswordAuthFilter::$user = self::account( 44 );
        expRestPasswordAuthFilter::$viaAppPassword = false;
        $this->assertTrue( ezpRestAuthConfiguration::signedInWithPassword( $basic ) );
        $this->assertFalse( ezpRestAuthConfiguration::signedInWithPassword( $oauth ), 'an OAuth token' );
        $this->assertFalse( ezpRestAuthConfiguration::signedInWithPassword( null ) );
        expRestPasswordAuthFilter::$viaAppPassword = true;
        $this->assertFalse( ezpRestAuthConfiguration::signedInWithPassword( $basic ), 'an app password' );
        expRestPasswordAuthFilter::$user = null;
        expRestPasswordAuthFilter::$viaAppPassword = false;
        $this->assertFalse( ezpRestAuthConfiguration::signedInWithPassword( $basic ), 'nobody signed in' );
        list( expRestPasswordAuthFilter::$user, expRestPasswordAuthFilter::$viaAppPassword ) = $before;

        $filter = self::file( 'kernel/private/rest/classes/auth/password_auth_filter.php' );
        $app = strpos( $filter, 'expSignInSecondFactor::appPasswordUser( $login, $password, expSignInSecondFactor::CHANNEL_REST )' );
        $this->assertNotFalse( $app );
        $this->assertLessThan( strpos( $filter, '$user = self::findUser( $login );' ), $app, 'an app password never counts as a failed password' );

        $controller = self::file( 'kernel/private/rest/classes/controllers/auth.php' );
        $this->assertStringContainsString( 'new expRestSecondFactorRequiredStatus( $statusCode', $controller );
        $this->assertStringContainsString( 'in_array( $statusCode, expSignInSecondFactor::reasons(), true )', $controller );

        // there is no OAuth password grant to guard: the token end point takes authorization codes and refresh tokens
        $token = self::file( 'kernel/private/rest/classes/controllers/oauth_token.php' );
        $this->assertMatchesRegularExpression( "/allowedGrantTypes = array\\( 'authorization_code',\\s*'refresh_token',\\s*\\);/", $token );
    }

    // ------------------------------------------------------------------ SF-08

    public function testWebdavSoapAndTheServicesLogin()
    {
        $dav = self::file( 'kernel/private/classes/webdav/ezwebdavcontentbackendauth.php' );
        $app = strpos( $dav, 'expSignInSecondFactor::appPasswordUser( $data->username, $data->password, expSignInSecondFactor::CHANNEL_WEBDAV )' );
        $login = strpos( $dav, '$user = $userClass->loginUser( $data->username, $data->password );' );
        $check = strpos( $dav, 'expSignInSecondFactor::check( $user, expSignInSecondFactor::CHANNEL_WEBDAV' );
        $ok = strrpos( $dav, 'eZWebDAVContentBackend::appendLogEntry( "AuthenticatedBasic" );' );
        $this->assertNotFalse( $app );
        $this->assertTrue( $app < $login && $login < $check && $check < $ok );
        $this->assertStringContainsString( 'expSignInSecondFactor::signInForRequest( $appUser );', $dav );
        $this->assertStringContainsString( 'header( expSignInSecondFactor::REFUSAL_HEADER', $dav );

        $soap = self::file( 'index_soap.php' );
        $app = strpos( $soap, 'expSignInSecondFactor::appPasswordUser( eZHTTPTool::username(), eZHTTPTool::password(), expSignInSecondFactor::CHANNEL_SOAP )' );
        $check = strpos( $soap, 'expSignInSecondFactor::check( $soapUser, expSignInSecondFactor::CHANNEL_SOAP' );
        $this->assertNotFalse( $app );
        $this->assertGreaterThan( $app, $check );
        $this->assertStringContainsString( "expSignInSecondFactor::sendRefusalHeaders( \$soapRefusal, 'Exponential SOAP' );", $soap );
        $this->assertStringContainsString( 'echo expSignInSecondFactor::soapFault( $soapRefusal );', $soap );

        // the services' classes without an installation (the kernel's autoload does not list an extension's)
        $root = dirname( __DIR__, 5 );
        if ( !class_exists( 'ezjscServerFunctions' ) )
            require_once $root . '/extension/ezjscore/classes/ezjscserverfunctions.php';
        if ( !class_exists( 'expServiceBase' ) )
            require_once $root . '/extension/expservices/classes/expservicebase.php';
        if ( !class_exists( 'expSessionServices' ) )
            require_once $root . '/extension/expservices/classes/expsessionservices.php';

        $services = self::file( 'extension/expservices/classes/expsessionservices.php' );
        $login = strpos( $services, '$user = eZUser::loginUser( $username, $password );' );
        $check = strpos( $services, 'expSignInSecondFactor::check( $user, expSignInSecondFactor::CHANNEL_SERVICES' );
        $answer = strpos( $services, "return self::ok( array( 'user' => self::exportUser( \$user )" );
        $this->assertTrue( $login < $check && $check < $answer );
        $this->assertStringNotContainsString( 'appPasswordUser', $services, 'no app password where a session is made' );
        $this->assertArrayHasKey( 'otp', expSessionServices::$services['login']['args'] );

        $this->assertSame( array( 'ok' => false, 'error' => array( 'code' => 401, 'message' => 'm', 'reason' => 'second_factor_required' ) ),
                           expServiceBase::error( 401, 'm', 'second_factor_required' ) );
        $this->assertSame( array( 'ok' => false, 'error' => array( 'code' => 404, 'message' => 'x' ) ), expServiceBase::error( 404, 'x' ) );
        $e = new expServiceException( 'm', 401, null, 'second_factor_invalid' );
        $this->assertSame( 'second_factor_invalid', $e->reason );
        $this->assertNull( ( new expServiceException( 'm' ) )->reason );
    }

    // ------------------------------------------------------------------ SF-09

    public function testTheAudit()
    {
        $catalogue = expAuditTaxonomy::catalogue();
        $names = array( expSignInSecondFactor::AUDIT_REFUSED => 'refuse',
                        expSignInSecondFactor::AUDIT_APP_PASSWORD_CREATE => 'create',
                        expSignInSecondFactor::AUDIT_APP_PASSWORD_REVOKE => 'revoke',
                        expSignInSecondFactor::AUDIT_APP_PASSWORD_USE => 'use' );
        $doc = self::file( 'doc/eng-us/bc/6.0/audit.md' );
        $docDe = self::file( 'doc/ger-de/bc/6.0/audit.md' );
        foreach ( $names as $name => $verb )
        {
            $this->assertArrayHasKey( $name, $catalogue );
            $this->assertSame( 'access', $catalogue[$name]['channel'] );
            $this->assertSame( $verb, expAuditHook::$verbs[$name] );
            $this->assertStringContainsString( '`' . $name . '`', $doc );
            $this->assertStringContainsString( '`' . $name . '`', $docDe );
            foreach ( array( 'ger-DE', 'fre-FR', 'esl-ES', 'ukr-UA', 'untranslated' ) as $locale )
                $this->assertStringContainsString( '<source>' . $catalogue[$name]['label'] . '</source>',
                                                   self::file( 'share/translations/' . $locale . '/translation.ts' ), $locale );
        }
        $this->assertSame( 'access.user.second_factor.refused', expSignInSecondFactor::AUDIT_REFUSED );
    }

    // ------------------------------------------------------------------ SF-10

    public function testSettingsAndAutoload()
    {
        $site = self::file( 'settings/site.ini' );
        $block = substr( $site, strpos( $site, "[SignInSecondFactorSettings]\n" ) );
        $block = substr( $block, 0, strpos( $block, "\n[", 2 ) );
        $this->assertStringContainsString( "\nHandlers[]\n", $block, 'the kernel registers no handler of its own' );
        $this->assertDoesNotMatchRegularExpression( '/^Handlers\[\]=/m', $block );
        $this->assertStringContainsString( "\nCodeHeader=X-Exp-OTP\n", $block );
        $this->assertSame( 'X-Exp-OTP', expSignInSecondFactor::DEFAULT_CODE_HEADER );

        $autoload = self::file( 'autoload/ezp_kernel.php' );
        foreach ( array( 'expSignInSecondFactor' => 'kernel/classes/user/expsigninsecondfactor.php',
                         'expSignInSecondFactorHandler' => 'kernel/classes/user/expsigninsecondfactorhandler.php',
                         'expRestSecondFactorRequiredStatus' => 'kernel/private/rest/classes/status/second_factor_required.php' ) as $class => $path )
            $this->assertMatchesRegularExpression( "~'" . $class . "'\\s+=> '" . preg_quote( $path, '~' ) . "'~", $autoload );
        $this->assertTrue( interface_exists( 'expSignInSecondFactorHandler' ) );
    }
}
