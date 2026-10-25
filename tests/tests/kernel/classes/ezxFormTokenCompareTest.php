<?php
/**
 * ezxFormToken::input() compares the posted form token in constant time:
 *
 *  FT-01  the right token passes, from the form field and from the X-CSRF-Token header
 *  FT-02  a wrong token, one differing only in its last character, a prefix and a longer one are refused
 *  FT-03  a token posted as an array (form_token[]=...) is refused as wrong, not a TypeError of hash_equals()
 *  FT-04  the comparison in the code is hash_equals(), not !==
 *
 * A registered user and a started session are simulated (the user from its cache, no database); the token, the
 * user, the session flag, $_POST, $_SERVER and $_SESSION are restored in tearDown().
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class ezxFormTokenCompareTest extends PHPUnit\Framework\TestCase
{
    const TOKEN = '0123456789abcdef0123456789abcdef01234567';

    private $saved;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->saved = array(
            'post' => $_POST,
            'server' => $_SERVER,
            'session' => isset( $_SESSION ) ? $_SESSION : null,
            'user' => isset( $GLOBALS['eZUserGlobalInstance_'] ) ? $GLOBALS['eZUserGlobalInstance_'] : null,
            'token' => $this->property( 'ezxFormToken', 'token' )->getValue(),
            'enabled' => ezxFormToken::isEnabled(),
            'started' => $this->property( 'eZSession', 'hasStarted' )->getValue(),
        );
        $userId = (int)eZUser::anonymousId() + 1000;
        $user = new eZUser( array( 'contentobject_id' => $userId ) );
        $user->setUserCache( array( 'info' => array( $userId => array( 'is_enabled' => true ) ) ) );
        $GLOBALS['eZUserGlobalInstance_'] = $user;
        $this->property( 'eZSession', 'hasStarted' )->setValue( null, true );
        $_SESSION = array();
        $this->property( 'ezxFormToken', 'token' )->setValue( null, self::TOKEN );
        ezxFormToken::setIsEnabled( true );
        $_SERVER['REQUEST_METHOD'] = 'POST';
        unset( $_SERVER['HTTP_X_CSRF_TOKEN'] );
        $_POST = array( 'x' => '1' );
    }

    protected function tearDown(): void
    {
        $_POST = $this->saved['post'];
        $_SERVER = $this->saved['server'];
        if ( $this->saved['session'] === null )
            unset( $_SESSION );
        else
            $_SESSION = $this->saved['session'];
        if ( $this->saved['user'] === null )
            unset( $GLOBALS['eZUserGlobalInstance_'] );
        else
            $GLOBALS['eZUserGlobalInstance_'] = $this->saved['user'];
        $this->property( 'ezxFormToken', 'token' )->setValue( null, $this->saved['token'] );
        ezxFormToken::setIsEnabled( $this->saved['enabled'] );
        $this->property( 'eZSession', 'hasStarted' )->setValue( null, $this->saved['started'] );
    }

    private function property( $class, $name )
    {
        $property = new ReflectionProperty( $class, $name );
        if ( PHP_VERSION_ID < 80100 )
            $property->setAccessible( true );
        return $property;
    }

    /** @return string|null null when input() let the request through, else the exception's message */
    private function refusal()
    {
        try
        {
            ezxFormToken::input( new eZURI( 'content/edit/1' ) );
            return null;
        }
        catch ( Exception $e )
        {
            return $e->getMessage();
        }
    }

    /** FT-01 */
    public function testTheRightTokenPasses()
    {
        $this->assertTrue( eZUser::isCurrentUserRegistered(), 'the simulated user is registered' );
        $_POST[ezxFormToken::FORM_FIELD] = self::TOKEN;
        $this->assertNull( $this->refusal() );
        unset( $_POST[ezxFormToken::FORM_FIELD] );
        $_SERVER['HTTP_X_CSRF_TOKEN'] = self::TOKEN;
        $this->assertNull( $this->refusal() );
    }

    /** FT-02 */
    public function testAWrongTokenIsRefused()
    {
        foreach ( array( 'wrong', substr( self::TOKEN, 0, -1 ) . '8', substr( self::TOKEN, 0, 20 ), self::TOKEN . '0', ' ' . self::TOKEN ) as $token )
        {
            $_POST[ezxFormToken::FORM_FIELD] = $token;
            $this->assertNotNull( $this->refusal(), $token );
        }
        unset( $_POST[ezxFormToken::FORM_FIELD] );
        $this->assertNotNull( $this->refusal(), 'no token at all' );
    }

    /** FT-03 */
    public function testATokenPostedAsAnArrayIsRefused()
    {
        foreach ( array( array( self::TOKEN ), array( 'a' => 'b' ) ) as $token )
        {
            $_POST[ezxFormToken::FORM_FIELD] = $token;
            $this->assertNotNull( $this->refusal(), json_encode( $token ) );
        }
    }

    /** FT-04 */
    public function testTheComparisonIsHashEquals()
    {
        $code = (string)file_get_contents( dirname( __DIR__, 4 ) . '/extension/ezformtoken/event/ezxformtoken.php' );
        $this->assertStringContainsString( 'hash_equals( (string)self::getToken(), $token )', $code );
        $this->assertStringNotContainsString( '$token !== self::getToken()', $code );
    }
}
