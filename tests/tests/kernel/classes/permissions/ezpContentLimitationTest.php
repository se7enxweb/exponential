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
        return new eZContentObject( array( 'id' => 990101, 'contentclass_id' => 16, 'section_id' => 1,
                                           'owner_id' => 14, 'current_version' => 1,
                                           'status' => eZContentObject::STATUS_PUBLISHED ) );
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
        $this->assertSame( array( array( 'X1Limitation', array( 1 ), 't', 14 ) ), X1ContentLimitationHandler::$asked );
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
        $this->assertSame( array( array( 'X1Limitation', array( 7 ), 'read', $object, $userID ) ), X1ContentLimitationHandler::$asked );
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
        $this->assertSame( array( array( 'X1Limitation', array( 7 ), 'read', $node, $userID ) ), X1ContentLimitationHandler::$asked );
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
        $this->assertSame( array( array( 'X1Limitation', array( 7 ), 'versionread', $version, $userID ) ), X1ContentLimitationHandler::$asked );
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
}
