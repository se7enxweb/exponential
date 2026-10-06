<?php
/**
 * Access checks made for another user than the current one, without the database: eZUser::accessUser() and the
 * user argument of eZContentObject::checkAccess() and editAccess().
 *
 *  AU-01 - No user (false, 0, null, '') is the current user
 *  AU-02 - The ID of the current user gives the current user's object
 *  AU-03 - An ID that is no positive whole number gives no user, and checks for it give no access
 *  AU-04 - Other users are kept for the request and forgotten for the next one (Velocity) and when purged
 *  AU-05 - The check reads the other user's roles, never the current user's, and keeps nothing on the object
 *  AU-06 - editAccess() for another user: that user's policies, its user/selfedit, the filter gets its ID
 *  AU-07 - An extension limitation handler is asked with the other user's ID
 *  AU-08 - The report of exp:access:check names the policy and the limitation that refused, and its handler
 *
 * The current user and the other users are stand-ins with the access arrays given to them; a database handler that
 * runs nothing stands in for the real one, so a user that was not given is not found.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/fixtures/ezcontentpermissionsqltestdb.php';

/** Records the user it is asked for and allows user 990202 only */
class X1AnotherUserLimitationHandler implements ezpContentLimitationHandler
{
    public static $asked = array();

    public function checkAccess( $limitation, array $values, $functionName, $subject, $userID )
    {
        self::$asked[] = $userID;
        return $userID === 990202;
    }

    public function permissionSQL( $limitation, array $values, $tableAliasName, $userID )
    {
        return false;
    }
}

class eZContentAccessForAnotherUserTest extends PHPUnit\Framework\TestCase
{
    const CURRENT = 990201;
    const OTHER = 990202;
    const THIRD = 990203;

    private $hadUser;
    private $user;
    private $hadDB;
    private $db;
    private $hadTime;
    private $time;
    private $listenerIDs = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->hadUser = array_key_exists( 'eZUserGlobalInstance_', $GLOBALS );
        $this->user = $this->hadUser ? $GLOBALS['eZUserGlobalInstance_'] : null;
        $this->hadDB = array_key_exists( 'eZDBGlobalInstance', $GLOBALS );
        $this->db = $this->hadDB ? $GLOBALS['eZDBGlobalInstance'] : null;
        $this->hadTime = array_key_exists( 'REQUEST_TIME_FLOAT', $_SERVER );
        $this->time = $this->hadTime ? $_SERVER['REQUEST_TIME_FLOAT'] : null;
        eZDB::setInstance( new eZContentPermissionSQLTestDB() );
        $_SERVER['REQUEST_TIME_FLOAT'] = 2000.5;
        eZUser::resetAccessUsers();
        ezpContentLimitation::resetCache();
        X1AnotherUserLimitationHandler::$asked = array();
    }

    protected function tearDown(): void
    {
        foreach ( $this->listenerIDs as $id )
            ezpEvent::getInstance()->detach( 'content/edit/access', $id );
        eZUser::resetAccessUsers();
        if ( $this->hadUser )
            $GLOBALS['eZUserGlobalInstance_'] = $this->user;
        else
            unset( $GLOBALS['eZUserGlobalInstance_'] );
        if ( $this->hadDB )
            $GLOBALS['eZDBGlobalInstance'] = $this->db;
        else
            unset( $GLOBALS['eZDBGlobalInstance'] );
        if ( $this->hadTime )
            $_SERVER['REQUEST_TIME_FLOAT'] = $this->time;
        else
            unset( $_SERVER['REQUEST_TIME_FLOAT'] );
        ezpINIHelper::restoreINISettings();
    }

    /** A stand-in user with $contentPolicies as its content policies (function => policies) */
    private function standIn( $id, array $contentPolicies, array $more = array() )
    {
        $user = new eZUser( array( 'contentobject_id' => $id, 'login' => "x1user$id", 'email' => "x1user$id@x1.example.invalid" ) );
        $user->AccessArray = array( 'content' => $contentPolicies ) + $more;
        return $user;
    }

    private function makeCurrent( eZUser $user )
    {
        $GLOBALS['eZUserGlobalInstance_'] = $user;
    }

    /** Puts $users in the request store of accessUser(), as if they had been fetched in this request */
    private function keep( array $users )
    {
        $store = array();
        foreach ( $users as $user )
            $store[(int)$user->attribute( 'contentobject_id' )] = $user;
        ( new ReflectionProperty( 'eZUser', 'accessUsers' ) )->setValue( null, $store );
        ( new ReflectionProperty( 'eZUser', 'accessUsersRequest' ) )->setValue( null, (string)$_SERVER['REQUEST_TIME_FLOAT'] );
    }

    private function object( $id = 990301 )
    {
        $object = new eZContentObject( array( 'id' => $id, 'contentclass_id' => 16, 'section_id' => 1,
                                              'owner_id' => 14, 'current_version' => 1,
                                              'status' => eZContentObject::STATUS_PUBLISHED ) );
        $object->MainNodeID = 990401;
        return $object;
    }

    /** AU-01 */
    public function testNoUserIsTheCurrentUser()
    {
        $current = $this->standIn( self::CURRENT, array() );
        $this->makeCurrent( $current );
        foreach ( array( false, 0, null, '', '0' ) as $none )
            $this->assertSame( $current, eZUser::accessUser( $none ), var_export( $none, true ) );
    }

    /** AU-02 */
    public function testTheCurrentUsersIDGivesTheCurrentUser()
    {
        $current = $this->standIn( self::CURRENT, array() );
        $this->makeCurrent( $current );
        $this->assertSame( $current, eZUser::accessUser( self::CURRENT ) );
        $this->assertSame( $current, eZUser::accessUser( (string)self::CURRENT ) );
    }

    /** AU-03 */
    public function testAnIDThatIsNoUserGivesNoAccess()
    {
        $this->makeCurrent( $this->standIn( self::CURRENT, array( '*' => array( '*' => '*' ) ), array( '*' => array( '*' => array( '*' => '*' ) ) ) ) );
        foreach ( array( 'abc', '-3', '1 OR 1=1', '1.5', 1.5, array( 1 ), true, self::THIRD ) as $bad )
        {
            $this->assertNull( eZUser::accessUser( $bad ), var_export( $bad, true ) );
        }
        $object = $this->object();
        $this->assertSame( 0, $object->checkAccess( 'read', false, false, false, false, 'abc' ) );
        $this->assertSame( 0, $object->checkAccess( 'read', false, false, false, false, self::THIRD ) );
        $this->assertFalse( $object->editAccess( null, false, self::THIRD ) );
        // the current user (who may do everything) is not asked instead
        $this->assertSame( 1, $object->checkAccess( 'read' ) );
    }

    /** AU-04 */
    public function testOtherUsersAreKeptForTheRequestOnly()
    {
        $this->makeCurrent( $this->standIn( self::CURRENT, array() ) );
        $other = $this->standIn( self::OTHER, array() );
        $this->keep( array( $other ) );
        $this->assertSame( $other, eZUser::accessUser( self::OTHER ) );

        // the next request of a persistent worker: fetched again, and the stand-in database knows no such user
        $_SERVER['REQUEST_TIME_FLOAT'] = 2001.75;
        $this->assertNull( eZUser::accessUser( self::OTHER ) );

        $this->keep( array( $other ) );
        $this->assertSame( $other, eZUser::accessUser( self::OTHER ) );
        ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'EnableCaching', 'false' );
        eZUser::purgeUserCacheByUserId( self::OTHER );
        $this->assertNull( eZUser::accessUser( self::OTHER ) );
    }

    /** AU-05 */
    public function testTheOtherUsersRolesAreRead()
    {
        $everything = array( 'read' => array( '*' => '*' ) );
        $class16 = array( 'read' => array( 'p_1' => array( 'Class' => array( 16 ) ) ) );
        $class2 = array( 'read' => array( 'p_1' => array( 'Class' => array( 2 ) ) ) );

        // the current user may read everything, the other one nothing
        $this->makeCurrent( $this->standIn( self::CURRENT, $everything ) );
        $this->keep( array( $this->standIn( self::OTHER, array() ), $this->standIn( self::THIRD, $class16 ) ) );
        $object = $this->object();
        $this->assertSame( 0, $object->checkAccess( 'read', false, false, false, false, self::OTHER ) );
        $this->assertSame( 1, $object->checkAccess( 'read', false, false, false, false, self::THIRD ) );
        $this->assertSame( 1, $object->checkAccess( 'read' ) );

        // the other way round
        $this->makeCurrent( $this->standIn( self::CURRENT, $class2 ) );
        $object = $this->object();
        $this->assertSame( 1, $object->checkAccess( 'read', false, false, false, false, self::THIRD ) );
        $this->assertFalse( $object->canRead() );

        // a check for another user keeps nothing on the object for the current one
        $object = $this->object();
        $object->checkAccess( 'read', false, false, false, false, self::THIRD );
        $this->assertFalse( $object->canRead() );
    }

    /** AU-06 */
    public function testEditAccessForAnotherUser()
    {
        $this->makeCurrent( $this->standIn( self::CURRENT, array( 'edit' => array( '*' => '*' ) ) ) );
        $this->keep( array( $this->standIn( self::OTHER, array( 'edit' => array( 'p_1' => array( 'Class' => array( 2 ) ) ) ) ),
                            $this->standIn( self::THIRD, array( 'edit' => array( 'p_1' => array( 'Class' => array( 16 ) ) ) ) ) ) );
        $seen = array();
        $this->listenerIDs[] = ezpEvent::getInstance()->attach( 'content/edit/access', function ( $allowed, $object, $version, $userID ) use ( &$seen )
        {
            $seen[] = array( $allowed, $userID );
            return $allowed;
        } );

        $object = $this->object();
        $this->assertFalse( $object->editAccess( null, false, self::OTHER ) );
        $this->assertTrue( $object->editAccess( null, false, self::THIRD ) );
        $this->assertTrue( $object->editAccess() );
        $this->assertSame( array( array( false, self::OTHER ), array( true, self::THIRD ), array( true, self::CURRENT ) ), $seen );

        // the user object of the other user itself: its user/selfedit policy counts, not the current user's
        $self = $this->object( self::OTHER );
        $this->keep( array( $this->standIn( self::OTHER, array(), array( 'user' => array( 'selfedit' => array( '*' => '*' ) ) ) ),
                            $this->standIn( self::THIRD, array() ) ) );
        $this->assertTrue( $self->editAccess( null, false, self::OTHER ) );
        $this->assertFalse( $this->object( self::THIRD )->editAccess( null, false, self::THIRD ) );
    }

    /** AU-07 */
    public function testTheLimitationHandlerGetsTheOtherUser()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'LimitationHandlers', array( 'X1Region' => 'X1AnotherUserLimitationHandler' ) );
        $policies = array( 'read' => array( 'p_1' => array( 'X1Region' => array( 1 ) ) ) );
        $this->makeCurrent( $this->standIn( self::CURRENT, $policies ) );
        $this->keep( array( $this->standIn( self::OTHER, $policies ) ) );
        $object = $this->object();
        $this->assertSame( 1, $object->checkAccess( 'read', false, false, false, false, self::OTHER ) );
        $this->assertSame( 0, $object->checkAccess( 'read' ) );
        $this->assertSame( array( self::OTHER, self::CURRENT ), X1AnotherUserLimitationHandler::$asked );
    }

    /** AU-08: the report of exp:access:check names the policy and the limitation that refused, and its handler */
    public function testTheAccessReportNamesWhatRefused()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'LimitationHandlers', array( 'X1Region' => 'X1AnotherUserLimitationHandler' ) );
        $this->makeCurrent( $this->standIn( self::CURRENT, array( 'read' => array( '*' => '*' ) ) ) );
        $this->keep( array( $this->standIn( self::OTHER, array( 'read' => array( 'p_7' => array( 'Class' => array( 2 ) ),
                                                                                   'p_8' => array( 'X1Unhandled' => array( 4 ) ) ) ) ),
                            $this->standIn( self::THIRD, array( 'read' => array( 'p_9' => array( 'Class' => array( 16 ), 'X1Region' => array( 1 ) ) ) ) ) ) );
        $object = $this->object();

        $denied = expContentAccessReport::check( $object, self::OTHER, 'read' );
        $this->assertFalse( $denied['allowed'] );
        $this->assertNull( $denied['error'] );
        $this->assertSame( self::OTHER, $denied['user_id'] );
        $this->assertSame( array( 'p_7', 'p_8' ), array_column( $denied['refused_by'], 'policy' ) );
        $this->assertSame( array( 'Class', 'X1Unhandled' ), array_column( $denied['refused_by'], 'limitation' ) );
        $this->assertSame( 'X1Unhandled( 4 ), no handler evaluates it', $denied['refused_by'][1]['text'] );

        // the handler of X1Region refuses everybody but OTHER
        $third = expContentAccessReport::check( $object, self::THIRD, 'read' );
        $this->assertFalse( $third['allowed'] );
        $this->assertSame( 'X1Region( 1 ), evaluated by X1AnotherUserLimitationHandler', $third['refused_by'][0]['text'] );

        $this->assertTrue( expContentAccessReport::check( $object, self::CURRENT, 'read' )['allowed'] );
        $none = expContentAccessReport::check( $object, self::OTHER, 'pdf' );
        $this->assertSame( 'no policy for content/pdf', $none['refused_by'][0]['text'] );

        $this->assertStringContainsString( 'is not checked on an object', expContentAccessReport::check( $object, self::OTHER, 'create' )['error'] );
        $this->assertStringContainsString( 'no such user', expContentAccessReport::check( $object, 990299, 'read' )['error'] );
        $this->assertStringContainsString( 'no such object', expContentAccessReport::check( null, self::OTHER, 'read' )['error'] );
    }
}
