<?php
/**
 * Content policy limitations of extensions, without the database: the handler of site.ini [RoleSettings]
 * LimitationHandlers[] (ezpContentLimitation), the limitation in the checkAccess() methods of objects, nodes and
 * versions, and the filter module/functionlist that adds a limitation to a module.
 *
 *  CL-01 - A kernel limitation never goes to a handler, even when one is registered for it
 *  CL-02 - Without a handler an extension limitation denies, and its SQL matches nothing
 *  CL-03 - A registered handler gets the limitation, its values, the function, the subject and the user
 *  CL-04 - Only true allows; a handler that does not implement the interface denies
 *  CL-05 - The SQL of a handler is put in parentheses; false or an empty string denies
 *  CL-06 - eZContentObject::checkAccess() asks the handler and reports the limitation that denied
 *  CL-07 - eZContentObjectTreeNode::checkAccess() asks the handler with the node
 *  CL-08 - eZContentObjectVersion::checkAccess() asks the handler with the version
 *  CL-09 - A version limitation that denies is not allowed again by the next limitation of the policy
 *  CL-10 - The filter module/functionlist adds a limitation to the functions of a module
 *  CL-11 - A handler is made once per request, again for the next request (Velocity) and when the setting changes
 *  CL-12 - A missing, unmakeable or throwing handler denies and is logged once per request; nothing escapes
 *  CL-13 - The SQL condition of a handler must stay inside its parentheses; other answers deny; column conditions
 *  CL-14 - String values of a column condition are escaped by the database handler
 *  CL-15 - The handler gets the values of the limitation as a list of strings
 *  CL-16 - The listeners of module/functionlist run once per module and request, again for the next request
 *  CL-17 - A listener that throws or returns no function list leaves the list of module.php
 *  CL-18 - Limitations from the filter that are not of the form of module.php are left out
 *
 * The current user is a stand-in anonymous user with the access array given to it.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

/** Records what it is asked and answers with $answer and $sql */
class X1ContentLimitationHandler implements ezpContentLimitationHandler
{
    public static $answer = true;
    public static $sql = 'ezcontentobject.id > 0';
    public static $asked = array();
    public static $made = 0;

    public function __construct()
    {
        ++self::$made;
    }

    public function checkAccess( $limitation, array $values, $functionName, $subject, $userID )
    {
        self::$asked[] = array( $limitation, $values, $functionName, $subject, $userID );
        return self::$answer;
    }

    public function permissionSQL( $limitation, array $values, $tableAliasName, $userID )
    {
        self::$asked[] = array( $limitation, $values, $tableAliasName, $userID );
        return self::$sql;
    }
}

/** Not a handler: does not implement ezpContentLimitationHandler */
class X1ContentLimitationNoHandler
{
    public function checkAccess()
    {
        return true;
    }
}

/** Throws in the method named by $throwIn */
class X1ContentLimitationThrowingHandler implements ezpContentLimitationHandler
{
    public static $throwIn = 'checkAccess';

    public function checkAccess( $limitation, array $values, $functionName, $subject, $userID )
    {
        if ( self::$throwIn === 'checkAccess' )
            throw new RuntimeException( 'x1 checkAccess failed' );
        return true;
    }

    public function permissionSQL( $limitation, array $values, $tableAliasName, $userID )
    {
        if ( self::$throwIn === 'permissionSQL' )
            throw new TypeError( 'x1 permissionSQL failed' );
        return '1 = 1';
    }
}

/** Cannot be made: its constructor throws */
class X1ContentLimitationUnmakeableHandler extends X1ContentLimitationHandler
{
    public function __construct()
    {
        throw new LogicException( 'x1 cannot be made' );
    }
}

/** A node whose object is given instead of fetched */
class X1ContentLimitationNode extends eZContentObjectTreeNode
{
    public $standInObject;

    public function object()
    {
        return $this->standInObject;
    }
}

/** A version whose object is given instead of fetched */
class X1ContentLimitationVersion extends eZContentObjectVersion
{
    public $standInObject;

    public function contentObject()
    {
        return $this->standInObject;
    }
}

class ezpContentLimitationTest extends PHPUnit\Framework\TestCase
{
    private $hadUser;
    private $user;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        ezpContentLimitation::resetCache();
        X1ContentLimitationHandler::$made = 0;
        X1ContentLimitationThrowingHandler::$throwIn = 'checkAccess';
        X1ContentLimitationHandler::$answer = true;
        X1ContentLimitationHandler::$sql = 'ezcontentobject.id > 0';
        X1ContentLimitationHandler::$asked = array();
        $this->hadUser = array_key_exists( 'eZUserGlobalInstance_', $GLOBALS );
        $this->user = $this->hadUser ? $GLOBALS['eZUserGlobalInstance_'] : null;
    }

    protected function tearDown(): void
    {
        if ( $this->hadUser )
            $GLOBALS['eZUserGlobalInstance_'] = $this->user;
        else
            unset( $GLOBALS['eZUserGlobalInstance_'] );
        ezpINIHelper::restoreINISettings();
    }

    private function registerHandlers( array $handlers )
    {
        ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'LimitationHandlers', $handlers );
    }

    /** Makes a stand-in anonymous user with $policies for content/$function the current user */
    private function currentUserWith( $function, array $policies )
    {
        $user = new eZUser( array( 'contentobject_id' => eZUser::anonymousId(), 'login' => 'x1anonymous',
                                   'email' => 'x1anonymous@x1.example.invalid' ) );
        $user->AccessArray = array( 'content' => array( $function => $policies ) );
        $GLOBALS['eZUserGlobalInstance_'] = $user;
        return (int)eZUser::anonymousId();
    }

    private function object()
    {
        $object = new eZContentObject( array( 'id' => 990101, 'contentclass_id' => 16, 'section_id' => 1,
                                              'owner_id' => 14, 'current_version' => 1,
                                              'status' => eZContentObject::STATUS_PUBLISHED ) );
        // the access list of a refusal names the main node; known here, so it is not looked up in the database
        $object->MainNodeID = 990102;
        return $object;
    }

    /** CL-01 */
    public function testKernelLimitationsNeverGoToAHandler()
    {
        $this->registerHandlers( array( 'Section' => 'X1ContentLimitationHandler', 'StateGroup_x' => 'X1ContentLimitationHandler' ) );
        $this->assertNull( ezpContentLimitation::handler( 'Section' ) );
        $this->assertNull( ezpContentLimitation::handler( 'StateGroup_x' ) );
        $this->assertTrue( ezpContentLimitation::isKernelLimitation( 'User_Subtree' ) );
        $this->assertFalse( ezpContentLimitation::isKernelLimitation( 'X1Limitation' ) );
    }

    /** CL-02 */
    public function testWithoutAHandlerTheLimitationDenies()
    {
        $this->registerHandlers( array() );
        $this->assertNull( ezpContentLimitation::handler( 'X1Limitation' ) );
        $this->assertFalse( ezpContentLimitation::checkAccess( 'X1Limitation', array( 1 ), 'read', $this->object(), 14 ) );
        $this->assertSame( ezpContentLimitation::DENY_SQL, ezpContentLimitation::permissionSQL( 'X1Limitation', array( 1 ), 'ezcontentobject_tree', 14 ) );
    }

    /** CL-03 */
    public function testTheHandlerGetsWhatItNeeds()
    {
        $this->registerHandlers( array( 'X1Limitation' => 'X1ContentLimitationHandler' ) );
        $this->assertInstanceOf( 'X1ContentLimitationHandler', ezpContentLimitation::handler( 'X1Limitation' ) );
        $object = $this->object();
        $this->assertTrue( ezpContentLimitation::checkAccess( 'X1Limitation', array( '1' ), 'read', $object, '14' ) );
        $this->assertSame( array( array( 'X1Limitation', array( '1' ), 'read', $object, 14 ) ), X1ContentLimitationHandler::$asked );
    }

    /** CL-04 */
    public function testOnlyTrueAllowsAndAForeignClassDenies()
    {
        $this->registerHandlers( array( 'X1Limitation' => 'X1ContentLimitationHandler', 'X1Other' => 'X1ContentLimitationNoHandler' ) );
        X1ContentLimitationHandler::$answer = 1;
        $this->assertFalse( ezpContentLimitation::checkAccess( 'X1Limitation', array( 1 ), 'read', $this->object(), 14 ) );
        $this->assertNull( ezpContentLimitation::handler( 'X1Other' ) );
        $this->assertFalse( ezpContentLimitation::checkAccess( 'X1Other', array( 1 ), 'read', $this->object(), 14 ) );
    }

    /** CL-05 */
    public function testTheSQLOfAHandler()
    {
        $this->registerHandlers( array( 'X1Limitation' => 'X1ContentLimitationHandler' ) );
        $this->assertSame( '( ezcontentobject.id > 0 )', ezpContentLimitation::permissionSQL( 'X1Limitation', array( 1 ), 't', 14 ) );
        $this->assertSame( array( array( 'X1Limitation', array( '1' ), 't', 14 ) ), X1ContentLimitationHandler::$asked );
        X1ContentLimitationHandler::$sql = false;
        $this->assertSame( ezpContentLimitation::DENY_SQL, ezpContentLimitation::permissionSQL( 'X1Limitation', array( 1 ), 't', 14 ) );
        X1ContentLimitationHandler::$sql = ' ';
        $this->assertSame( ezpContentLimitation::DENY_SQL, ezpContentLimitation::permissionSQL( 'X1Limitation', array( 1 ), 't', 14 ) );
    }

    /** CL-06 */
    public function testObjectAccessAsksTheHandler()
    {
        $userID = $this->currentUserWith( 'read', array( 'p_1' => array( 'Class' => array( 16 ), 'X1Limitation' => array( 7 ) ) ) );
        $object = $this->object();

        $this->registerHandlers( array() );
        $this->assertSame( 0, $object->checkAccess( 'read' ) );
        $denied = $object->checkAccess( 'read', false, false, true );
        $this->assertSame( array( 'Limitation' => 'X1Limitation', 'Required' => array( 7 ) ), $denied['PolicyList'][0]['LimitationList'] );

        $this->registerHandlers( array( 'X1Limitation' => 'X1ContentLimitationHandler' ) );
        $this->assertSame( 1, $object->checkAccess( 'read' ) );
        $this->assertSame( array( array( 'X1Limitation', array( '7' ), 'read', $object, $userID ) ), X1ContentLimitationHandler::$asked );
        X1ContentLimitationHandler::$answer = false;
        $this->assertSame( 0, $object->checkAccess( 'read' ) );
    }

    /** CL-07 */
    public function testNodeAccessAsksTheHandler()
    {
        $userID = $this->currentUserWith( 'read', array( 'p_1' => array( 'X1Limitation' => array( 7 ) ) ) );
        $node = new X1ContentLimitationNode( array( 'node_id' => 990102, 'parent_node_id' => 2, 'contentobject_id' => 990101,
                                                    'path_string' => '/1/2/990102/' ) );
        $node->standInObject = $this->object();

        $this->registerHandlers( array() );
        $this->assertSame( 0, $node->checkAccess( 'read' ) );

        $this->registerHandlers( array( 'X1Limitation' => 'X1ContentLimitationHandler' ) );
        $this->assertSame( 1, $node->checkAccess( 'read' ) );
        $this->assertSame( array( array( 'X1Limitation', array( '7' ), 'read', $node, $userID ) ), X1ContentLimitationHandler::$asked );
    }

    private function version()
    {
        $version = new X1ContentLimitationVersion( array( 'id' => 990103, 'contentobject_id' => 990101, 'version' => 2,
                                                          'status' => eZContentObjectVersion::STATUS_ARCHIVED,
                                                          'creator_id' => 14, 'initial_language_id' => 2 ) );
        $version->standInObject = $this->object();
        return $version;
    }

    /** CL-08 */
    public function testVersionAccessAsksTheHandler()
    {
        $userID = $this->currentUserWith( 'versionread', array( 'p_1' => array( 'X1Limitation' => array( 7 ) ) ) );
        $version = $this->version();

        $this->registerHandlers( array() );
        $this->assertSame( 0, $version->checkAccess( 'versionread' ) );

        $this->registerHandlers( array( 'X1Limitation' => 'X1ContentLimitationHandler' ) );
        $this->assertSame( 1, $version->checkAccess( 'versionread' ) );
        $this->assertSame( array( array( 'X1Limitation', array( '7' ), 'versionread', $version, $userID ) ), X1ContentLimitationHandler::$asked );
    }

    /** CL-09 */
    public function testADeniedVersionLimitationIsNotAllowedAgain()
    {
        $this->registerHandlers( array() );
        // The extension limitation denies (no handler); the Section after it allowed the policy again
        $this->currentUserWith( 'versionread', array( 'p_1' => array( 'X1Limitation' => array( 7 ), 'Section' => array( 1 ) ) ) );
        $this->assertSame( 0, $this->version()->checkAccess( 'versionread' ) );

        $this->currentUserWith( 'versionread', array( 'p_1' => array( 'Section' => array( 1 ), 'Class' => array( 16 ) ) ) );
        $this->assertSame( 1, $this->version()->checkAccess( 'versionread' ) );
    }

    /** CL-10 */
    public function testTheFunctionListFilterAddsALimitation()
    {
        $event = ezpEvent::getInstance();
        $id = $event->attach( 'module/functionlist', function ( $functionList, $moduleName )
        {
            if ( $moduleName === 'k1perm' )
            {
                $functionList['read']['X1Limitation'] = array( 'name' => 'X1Limitation', 'values' => array( array( 'Name' => 'On', 'value' => 1 ) ) );
            }
            return $functionList;
        } );
        try
        {
            $module = eZModule::findModule( 'k1perm', null, __DIR__ . '/fixtures/modules' );
            $functions = $module->attribute( 'available_functions' );
            $this->assertSame( array( 'X1Limitation' ), array_keys( $functions['read'] ) );
            $this->assertSame( array(), $functions['edit'] );
        }
        finally
        {
            $event->detach( 'module/functionlist', $id );
        }
        $module = eZModule::findModule( 'k1perm', null, __DIR__ . '/fixtures/modules' );
        $functions = $module->attribute( 'available_functions' );
        $this->assertSame( array(), $functions['read'] );
    }

    private function logged()
    {
        $logged = new ReflectionProperty( 'ezpContentLimitation', 'logged' );
        return array_keys( $logged->getValue() );
    }

    /** CL-11 */
    public function testAHandlerIsMadeOncePerRequestAndAgainForTheNext()
    {
        $hadTime = array_key_exists( 'REQUEST_TIME_FLOAT', $_SERVER );
        $time = $hadTime ? $_SERVER['REQUEST_TIME_FLOAT'] : null;
        try
        {
            $_SERVER['REQUEST_TIME_FLOAT'] = 1000.25;
            $this->registerHandlers( array( 'X1Limitation' => 'X1ContentLimitationHandler' ) );
            for ( $i = 0; $i < 50; ++$i )
            {
                ezpContentLimitation::checkAccess( 'X1Limitation', array( 1 ), 'read', $this->object(), 14 );
                ezpContentLimitation::permissionSQL( 'X1Limitation', array( 1 ), 't', 14 );
            }
            $this->assertSame( 1, X1ContentLimitationHandler::$made, 'one handler for the whole request' );

            // the next request of a persistent worker (Velocity) makes it again
            $_SERVER['REQUEST_TIME_FLOAT'] = 1001.5;
            ezpContentLimitation::checkAccess( 'X1Limitation', array( 1 ), 'read', $this->object(), 14 );
            $this->assertSame( 2, X1ContentLimitationHandler::$made );

            // so does a change of the setting within a request
            $this->registerHandlers( array( 'X1Limitation' => 'X1ContentLimitationHandler', 'X1Other' => 'X1ContentLimitationHandler' ) );
            ezpContentLimitation::checkAccess( 'X1Limitation', array( 1 ), 'read', $this->object(), 14 );
            $this->assertSame( 3, X1ContentLimitationHandler::$made );
        }
        finally
        {
            if ( $hadTime )
                $_SERVER['REQUEST_TIME_FLOAT'] = $time;
            else
                unset( $_SERVER['REQUEST_TIME_FLOAT'] );
        }
    }

    /** CL-12 */
    public function testAnUnusableOrFailingHandlerDeniesAndIsLoggedOnce()
    {
        $this->registerHandlers( array( 'X1Missing' => 'X1NoSuchLimitationHandlerClass',
                                        'X1Unmakeable' => 'X1ContentLimitationUnmakeableHandler',
                                        'X1Throwing' => 'X1ContentLimitationThrowingHandler' ) );
        for ( $i = 0; $i < 3; ++$i )
        {
            $this->assertFalse( ezpContentLimitation::checkAccess( 'X1Missing', array( 1 ), 'read', $this->object(), 14 ) );
            $this->assertSame( ezpContentLimitation::DENY_SQL, ezpContentLimitation::permissionSQL( 'X1Missing', array( 1 ), 't', 14 ) );
            $this->assertFalse( ezpContentLimitation::checkAccess( 'X1Unmakeable', array( 1 ), 'read', $this->object(), 14 ) );
            $this->assertFalse( ezpContentLimitation::checkAccess( 'X1Throwing', array( 1 ), 'read', $this->object(), 14 ) );
            $this->assertFalse( ezpContentLimitation::checkAccess( 'X1Unknown', array( 1 ), 'read', $this->object(), 14 ) );
        }
        X1ContentLimitationThrowingHandler::$throwIn = 'permissionSQL';
        $this->assertSame( ezpContentLimitation::DENY_SQL, ezpContentLimitation::permissionSQL( 'X1Throwing', array( 1 ), 't', 14 ) );
        X1ContentLimitationThrowingHandler::$throwIn = 'none';
        $this->assertTrue( ezpContentLimitation::checkAccess( 'X1Throwing', array( 1 ), 'read', $this->object(), 14 ) );

        $logged = $this->logged();
        $this->assertCount( 5, $logged, implode( "\n", $logged ) );
        $this->assertStringContainsString( 'X1NoSuchLimitationHandlerClass of the limitation X1Missing does not exist', $logged[0] );
        $this->assertStringContainsString( 'X1ContentLimitationUnmakeableHandler of the limitation X1Unmakeable could not be made (LogicException: x1 cannot be made)', $logged[1] );
        $this->assertStringContainsString( 'threw RuntimeException: x1 checkAccess failed', $logged[2] );
        $this->assertStringContainsString( 'No handler is registered for the limitation X1Unknown', $logged[3] );
        $this->assertStringContainsString( 'threw TypeError in permissionSQL(): x1 permissionSQL failed', $logged[4] );
    }

    /**
     * CL-13: a string condition must stay inside its parentheses and its statement; anything else that is not a
     * condition the kernel accepts denies
     *
     */
    #[\PHPUnit\Framework\Attributes\DataProvider( 'sqlConditions' )]
    public function testTheConditionOfAHandlerIsChecked( $sql, $expected )
    {
        $this->registerHandlers( array( 'X1Limitation' => 'X1ContentLimitationHandler' ) );
        X1ContentLimitationHandler::$sql = $sql;
        // the IN statement of a column condition is written by the database handler: one that runs nothing
        require_once __DIR__ . '/fixtures/ezcontentpermissionsqltestdb.php';
        $hadDB = array_key_exists( 'eZDBGlobalInstance', $GLOBALS );
        $db = $hadDB ? $GLOBALS['eZDBGlobalInstance'] : null;
        eZDB::setInstance( new eZContentPermissionSQLTestDB() );
        try
        {
            $this->assertSame( $expected, ezpContentLimitation::permissionSQL( 'X1Limitation', array( 1 ), 't', 14 ) );
        }
        finally
        {
            if ( $hadDB )
                $GLOBALS['eZDBGlobalInstance'] = $db;
            else
                unset( $GLOBALS['eZDBGlobalInstance'] );
        }
    }

    public static function sqlConditions()
    {
        $deny = ezpContentLimitation::DENY_SQL;
        return array(
            'plain' => array( 't.depth <= 3', '( t.depth <= 3 )' ),
            'subquery' => array( 'ezcontentobject.id IN ( SELECT id FROM x WHERE a = 1 )', '( ezcontentobject.id IN ( SELECT id FROM x WHERE a = 1 ) )' ),
            'semicolon in a string' => array( "x.name = 'a;b -- c # d /* e'", "( x.name = 'a;b -- c # d /* e' )" ),
            'doubled quote' => array( "x.name = 'it''s'", "( x.name = 'it''s' )" ),
            'escaped quote' => array( "x.name = 'it\\'s'", "( x.name = 'it\\'s' )" ),
            'breaks out of its parentheses' => array( '1 = 1 ) OR ( 1 = 1', $deny ),
            'closes before it opens' => array( ') OR (', $deny ),
            'unbalanced' => array( '( 1 = 1', $deny ),
            'second statement' => array( '1 = 1; DELETE FROM ezcontentobject', $deny ),
            'line comment' => array( '1 = 1 -- AND x', $deny ),
            'hash comment' => array( '1 = 1 # AND x', $deny ),
            'block comment' => array( '1 = 1 /* AND x */', $deny ),
            'unclosed string' => array( "x.name = 'abc", $deny ),
            'nul byte' => array( "x.id = 1\0", $deny ),
            'empty' => array( '  ', $deny ),
            'false' => array( false, $deny ),
            'true' => array( true, $deny ),
            'null' => array( null, $deny ),
            'number' => array( 1, $deny ),
            'object' => array( new stdClass(), $deny ),
            'empty array' => array( array(), $deny ),
            'column with integers' => array( array( 'column' => 'ezcontentobject.section_id', 'values' => array( '1', 2, ' 3 ' ) ),
                                             '( ezcontentobject.section_id IN ( 1, 2, 3 ) )' ),
            'duplicate values' => array( array( 'column' => 'ezcontentobject.section_id', 'values' => array( 1, '1' ) ),
                                         '( ezcontentobject.section_id IN ( 1 ) )' ),
            'not in' => array( array( 'column' => 'section_id', 'values' => array( 4 ), 'not' => true ), '( section_id NOT IN ( 4 ) )' ),
            'no values' => array( array( 'column' => 'section_id', 'values' => array() ), '( 1 = 0 )' ),
            'not in no values' => array( array( 'column' => 'section_id', 'values' => array(), 'not' => true ), '( 1 = 1 )' ),
            'not false' => array( array( 'column' => 'section_id', 'values' => array( 4 ), 'not' => false ), '( section_id IN ( 4 ) )' ),
            // only a real boolean inverts: anything else would turn "only these" into "all but these", or back
            'not as the string false' => array( array( 'column' => 'section_id', 'values' => array( 4 ), 'not' => 'false' ), $deny ),
            'not as the string true' => array( array( 'column' => 'section_id', 'values' => array( 4 ), 'not' => 'true' ), $deny ),
            'not as the integer 1' => array( array( 'column' => 'section_id', 'values' => array( 4 ), 'not' => 1 ), $deny ),
            'not as the integer 0' => array( array( 'column' => 'section_id', 'values' => array( 4 ), 'not' => 0 ), $deny ),
            'not as an array' => array( array( 'column' => 'section_id', 'values' => array( 4 ), 'not' => array( true ) ), $deny ),
            'not as a string with no values' => array( array( 'column' => 'section_id', 'values' => array(), 'not' => 'false' ), $deny ),
            'not as a string in one entry of a list' => array( array( array( 'column' => 'a.x', 'values' => array( 1 ) ),
                                                                      array( 'column' => 'b.y', 'values' => array( 2 ), 'not' => 'no' ) ), $deny ),
            'list joined by and' => array( array( array( 'column' => 'a.x', 'values' => array( 1 ) ), array( 'column' => 'b.y', 'values' => array( 2 ) ) ),
                                           '( a.x IN ( 1 ) AND b.y IN ( 2 ) )' ),
            'column that is no name' => array( array( 'column' => 'a.x) OR (1', 'values' => array( 1 ) ), $deny ),
            'integer that is none' => array( array( 'column' => 'a.x', 'values' => array( '1 OR 1' ) ), $deny ),
            'array value' => array( array( 'column' => 'a.x', 'values' => array( array( 1 ) ) ), $deny ),
            'unknown type' => array( array( 'column' => 'a.x', 'values' => array( 1 ), 'type' => 'raw' ), $deny ),
            'values missing' => array( array( 'column' => 'a.x' ), $deny ),
            'one bad entry of a list' => array( array( array( 'column' => 'a.x', 'values' => array( 1 ) ), 'a.y = 1' ), $deny ),
        );
    }

    /** CL-14: string values of a column condition are escaped by the database handler */
    public function testStringValuesOfAColumnConditionAreEscaped()
    {
        require_once __DIR__ . '/fixtures/ezcontentpermissionsqltestdb.php';
        $hadDB = array_key_exists( 'eZDBGlobalInstance', $GLOBALS );
        $db = $hadDB ? $GLOBALS['eZDBGlobalInstance'] : null;
        eZDB::setInstance( new eZContentPermissionSQLTestDB() );
        try
        {
            $this->assertSame( "( x.code IN ( 'a', 'b\\' OR \\'1' ) )",
                               ezpContentLimitation::sqlCondition( array( 'column' => 'x.code', 'type' => 'string', 'values' => array( 'a', "b' OR '1" ) ) ) );
        }
        finally
        {
            if ( $hadDB )
                $GLOBALS['eZDBGlobalInstance'] = $db;
            else
                unset( $GLOBALS['eZDBGlobalInstance'] );
        }
    }

    /** CL-15: the handler gets the values as a list of strings, whatever the policy cache held */
    public function testTheHandlerGetsTheValuesAsStrings()
    {
        $this->registerHandlers( array( 'X1Limitation' => 'X1ContentLimitationHandler' ) );
        ezpContentLimitation::checkAccess( 'X1Limitation', array( 'a' => 3, 'b' => '4', 'c' => array( 5 ), 'd' => null ), 'read', $this->object(), 14 );
        $this->assertSame( array( '3', '4' ), X1ContentLimitationHandler::$asked[0][1] );
        $this->assertNull( ezpContentLimitation::handler( '' ) );
        $this->assertNull( ezpContentLimitation::handler( array( 'X1Limitation' ) ) );
    }

    /** Finds the fixture module k1perm with the listener $listener of module/functionlist attached */
    private function functionsWith( $listener, $times = 1 )
    {
        $event = ezpEvent::getInstance();
        $id = $event->attach( 'module/functionlist', $listener );
        try
        {
            for ( $i = 0; $i < $times; ++$i )
            {
                $module = eZModule::findModule( 'k1perm', null, __DIR__ . '/fixtures/modules' );
                $functions = $module->attribute( 'available_functions' );
            }
        }
        finally
        {
            $event->detach( 'module/functionlist', $id );
        }
        return $functions;
    }

    /** CL-16: the listeners run once per module and request, however often the module is looked up */
    public function testTheFunctionListFilterRunsOncePerModuleAndRequest()
    {
        $hadTime = array_key_exists( 'REQUEST_TIME_FLOAT', $_SERVER );
        $time = $hadTime ? $_SERVER['REQUEST_TIME_FLOAT'] : null;
        $calls = 0;
        $listener = function ( $functionList, $moduleName ) use ( &$calls )
        {
            if ( $moduleName === 'k1perm' )
            {
                ++$calls;
                $functionList['read']['X1Limitation'] = array( 'name' => 'X1Limitation', 'values' => array() );
            }
            return $functionList;
        };
        $event = ezpEvent::getInstance();
        $id = $event->attach( 'module/functionlist', $listener );
        try
        {
            $_SERVER['REQUEST_TIME_FLOAT'] = 3000.5;
            for ( $i = 0; $i < 5; ++$i )
            {
                $module = eZModule::findModule( 'k1perm', null, __DIR__ . '/fixtures/modules' );
                $this->assertSame( array( 'X1Limitation' ), array_keys( $module->attribute( 'available_functions' )['read'] ) );
            }
            $this->assertSame( 1, $calls );
            // the next request of a persistent worker asks the listeners again
            $_SERVER['REQUEST_TIME_FLOAT'] = 3001.5;
            eZModule::findModule( 'k1perm', null, __DIR__ . '/fixtures/modules' );
            $this->assertSame( 2, $calls );
        }
        finally
        {
            $event->detach( 'module/functionlist', $id );
            if ( $hadTime )
                $_SERVER['REQUEST_TIME_FLOAT'] = $time;
            else
                unset( $_SERVER['REQUEST_TIME_FLOAT'] );
        }
    }

    /** CL-17: a listener that throws or returns no function list leaves the list of module.php */
    public function testAFailingFunctionListListenerLeavesTheModuleAlone()
    {
        $expected = array( 'create' => array(), 'edit' => array(), 'read' => array(), 'readall' => array() );
        $this->assertSame( $expected, $this->functionsWith( function ( $functionList, $moduleName )
        {
            throw new RuntimeException( 'x1 functionlist failed' );
        } ) );
        $this->assertSame( $expected, $this->functionsWith( function ( $functionList, $moduleName )
        {
            return 'no list';
        } ) );
    }

    /** CL-18: limitations that are not of the form of module.php are left out; values default to none */
    public function testMalformedLimitationsFromTheFilterAreLeftOut()
    {
        $functions = $this->functionsWith( function ( $functionList, $moduleName )
        {
            $functionList['read']['Good'] = array( 'name' => 'Good' );
            $functionList['read']['Dynamic'] = array( 'name' => 'Dynamic', 'class' => 'X1Lister', 'function' => 'all' );
            $functionList['read']['NoName'] = array( 'values' => array() );
            $functionList['read']['BadName'] = array( 'name' => 'Bad Name"', 'values' => array() );
            $functionList['read']['NotAnArray'] = 'Region';
            $functionList['read']['ClassWithoutFunction'] = array( 'name' => 'ClassWithoutFunction', 'class' => 'X1Lister' );
            $functionList['edit'] = 'not a list';
            $functionList['extra'] = array();
            return $functionList;
        } );
        $this->assertSame( array( 'Good' => array( 'name' => 'Good', 'values' => array() ),
                                  'Dynamic' => array( 'name' => 'Dynamic', 'class' => 'X1Lister', 'function' => 'all',
                                                      'values' => array(), 'parameter' => array() ) ),
                           $functions['read'] );
        $this->assertSame( array(), $functions['edit'], 'a function the listener broke keeps its list from module.php' );
        $this->assertSame( array(), $functions['extra'], 'a function a listener adds is kept' );
    }
}
