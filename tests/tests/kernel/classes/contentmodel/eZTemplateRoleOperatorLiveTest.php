<?php
/**
 * The role and policy template operators (lib/eztemplate/classes/eztemplateroleoperator.php) answer the documented
 * examples of doc/eng-us/features/6.0/role-and-policy-template-operators.md and
 * doc/eng-us/help/template-operators/role-operators.md correctly, for the anonymous user, the admin user and a
 * throwaway user, rendered by the template engine, interpreted and compiled.
 *
 * A throwaway user group with one user gets two throwaway roles: "k1c tpl editor ..." (content/read, content/edit
 * limited to section 1) and "k1c tpl module ..." (every function of the module k1c_module). Everything is removed
 * again.
 *
 *  RPL-01  The current user: has_role, has_role_id, has_any_role (any and match_all, names and ids mixed)
 *  RPL-02  The piped value is ignored; user_id names another user, also as a string of digits
 *  RPL-03  has_role_by_user / has_role_by_user_id; a value that is no user object gives false, not an error
 *  RPL-04  has_policy / has_policy_by_user: exact, every function of a module (k1c_module/*), unknown function
 *  RPL-05  The admin user: Administrator role, and has_policy true through the policy for every module (*)
 *  RPL-06  The anonymous user: the Anonymous role, content/read, no content/edit
 *  RPL-07  The examples of the feature page, verbatim
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expContentModelLiveTestCase.php';

class eZTemplateRoleOperatorLiveTest extends expContentModelLiveTestCase
{
    protected static $group;
    protected static $user;
    protected static $login;

    /** @var eZRole */
    protected static $editorRole;
    /** @var eZRole */
    protected static $moduleRole;

    /** @var int[] roles to remove */
    protected static $roles = array();

    /** @var string */
    protected static $dir;

    private $savedGlobals = array();

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        foreach ( array( 'user', 'user_group' ) as $identifier )
        {
            if ( !eZContentClass::fetchByIdentifier( $identifier ) )
                self::markTestSkipped( "needs the content class '$identifier'" );
        }
        static::$roles = array();
        $suffix = substr( md5( uniqid( '', true ) ), 0, 10 );
        static::$login = 'k1c' . $suffix;
        $email = 'k1c-' . $suffix . '@k1c.example.invalid';
        $password = 'K1c-pass-' . $suffix;
        static::$group = static::createObject( 'user_group', static::$root['node'], array( 'name' => 'k1c group ' . $suffix ) );
        $hash = eZUser::createHash( static::$login, $password, eZUser::site(), eZUser::hashType() );
        static::$user = static::createObject( 'user', static::$group['node'], array(
            'first_name' => 'K1c', 'last_name' => 'Roles',
            'user_account' => static::$login . '|' . $email . '|' . $hash . '|' . eZUser::passwordHashTypeName( eZUser::hashType() ) . '|1' ) );

        static::$editorRole = eZRole::create( 'k1c tpl editor ' . $suffix );
        static::$editorRole->store();
        static::$roles[] = (int)static::$editorRole->attribute( 'id' );
        static::$editorRole->appendPolicy( 'content', 'read' );
        static::$editorRole->appendPolicy( 'content', 'edit', array( 'Section' => array( 1 ) ) );
        static::$editorRole->store();
        static::$editorRole->assignToUser( static::$group['object'] );

        static::$moduleRole = eZRole::create( 'k1c tpl module ' . $suffix );
        static::$moduleRole->store();
        static::$roles[] = (int)static::$moduleRole->attribute( 'id' );
        static::$moduleRole->appendPolicy( 'k1c_module', '*' );
        static::$moduleRole->store();
        static::$moduleRole->assignToUser( static::$group['object'] );

        eZRole::expireCache();
        eZUser::purgeUserCacheByUserId( static::$user['object'] );

        static::$dir = 'var/tmp/phpunit-role-operator-live-' . getmypid() . '-' . $suffix;
        mkdir( static::$dir . '/compiled', 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        foreach ( static::$roles as $roleID )
        {
            if ( eZRole::fetch( $roleID ) )
                eZRole::removeRole( $roleID );
        }
        static::$roles = array();
        if ( static::$user )
        {
            eZUser::removeSessionData( static::$user['object'] );
            eZUser::purgeUserCacheByUserId( static::$user['object'] );
        }
        if ( static::$dir )
        {
            foreach ( array_merge( glob( static::$dir . '/compiled/*' ) ?: array(), glob( static::$dir . '/*' ) ?: array() ) as $f )
                is_file( $f ) && unlink( $f );
            @rmdir( static::$dir . '/compiled' );
            @rmdir( static::$dir );
        }
        parent::tearDownAfterClass();
        if ( static::$login && eZUser::fetchByName( static::$login ) )
            throw new RuntimeException( 'the test user is still there' );
    }

    protected function setUp(): void
    {
        parent::setUp();
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
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
        $this->savedGlobals = array();
        parent::tearDown();
    }

    // ---------------------------------------------------------------- helpers

    private static function loginAs( $userID )
    {
        $user = eZUser::fetch( $userID );
        if ( !$user instanceof eZUser )
            throw new RuntimeException( "no user $userID" );
        eZUser::setCurrentlyLoggedInUser( $user, (int)$userID, eZUser::NO_SESSION_REGENERATE );
    }

    /**
     * Renders the template source, interpreted and compiled, and returns the output (the same for both, else fails).
     */
    private function render( $source, array $variables = array() )
    {
        $outputs = array();
        foreach ( array( 'interpreted' => false, 'compiled' => true ) as $mode => $compiled )
        {
            if ( $compiled )
            {
                unset( $GLOBALS['eZSiteBasics'] );
                $GLOBALS['eZTemplateCompilerSettings']['compile'] = true;
                $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = static::$dir . '/compiled';
            }
            else
            {
                $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
                $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
            }
            $file = static::$dir . '/' . md5( $source ) . '.tpl';
            if ( !file_exists( $file ) )
                file_put_contents( $file, $source );
            $tpl = new eZTemplate();
            $tpl->autoload();
            foreach ( $variables as $name => $value )
                $tpl->setVariable( $name, $value );
            $output = $tpl->fetch( $file );
            $this->assertSame( array(), $tpl->errorLog(), "no template error ($mode): $source" );
            $outputs[$mode] = $output;
        }
        $this->assertSame( $outputs['interpreted'], $outputs['compiled'], "interpreted and compiled agree: $source" );
        return $outputs['interpreted'];
    }

    /**
     * Each expression answered 1 or 0, separated by a semicolon (a block tag eats the line break after it).
     *
     * @param array $expressions expression => expected bool
     */
    private function assertAnswers( array $expressions, array $variables = array() )
    {
        $source = '';
        $expected = '';
        foreach ( $expressions as $expression => $answer )
        {
            $source .= '{if ' . $expression . '}1{else}0{/if};';
            $expected .= ( $answer ? '1' : '0' ) . ';';
        }
        $output = $this->render( $source, $variables );
        $got = explode( ';', trim( $output ) );
        $want = explode( ';', $expected );
        $i = 0;
        foreach ( $expressions as $expression => $answer )
        {
            $this->assertSame( $want[$i], $got[$i] ?? '', $expression );
            ++$i;
        }
    }

    private static function editorName()
    {
        return static::$editorRole->attribute( 'name' );
    }

    private static function editorID()
    {
        return (int)static::$editorRole->attribute( 'id' );
    }

    private static function moduleID()
    {
        return (int)static::$moduleRole->attribute( 'id' );
    }

    // ---------------------------------------------------------------- tests

    public function testRolesOfTheCurrentUser()
    {
        static::loginAs( static::$user['object'] );
        $e = "'" . static::editorName() . "'";
        $eid = static::editorID();
        $mid = static::moduleID();
        $this->assertAnswers( array(
            "has_role( $e )" => true,
            "\$current_user|has_role( $e )" => true,
            "has_role( 'Administrator' )" => false,
            "has_role( '" . strtoupper( static::editorName() ) . "' )" => false,
            "has_role_id( $eid )" => true,
            'has_role_id( 0 )' => false,
            "has_any_role( array( 'Administrator', $e ) )" => true,
            "has_any_role( array( 'Administrator', $mid ) )" => true,
            "has_any_role( array( 'Administrator', 'Anonymous-k1c-none' ) )" => false,
            "has_any_role( array( $e, $mid ), 0, true() )" => true,
            "has_any_role( array( $e, '$mid' ), 0, true() )" => true,
            "has_any_role( array( $e, 'Administrator' ), 0, true() )" => false,
            'has_any_role( array() )' => false,
        ), array( 'current_user' => eZUser::currentUser() ) );
    }

    public function testPipedValueIgnoredAndUserIdNamesAnotherUser()
    {
        $uid = static::$user['object'];
        static::loginAdmin();
        $e = "'" . static::editorName() . "'";
        $eid = static::editorID();
        $this->assertAnswers( array(
            // the piped user is not the one checked: the admin is
            "\$someone|has_role( $e )" => false,
            "has_role( $e, $uid )" => true,
            "has_role( $e, \$uid_string )" => true,
            "has_role_id( $eid, $uid )" => true,
            "has_any_role( array( $e ), $uid )" => true,
            "has_any_role( array( $e, 'Administrator' ), $uid, true() )" => false,
            "has_policy( 'content', 'edit', $uid )" => true,
            "has_policy( 'content', 'remove', $uid )" => false,
        ), array( 'someone' => eZUser::fetch( $uid ), 'uid_string' => (string)$uid ) );
    }

    public function testRoleOfAGivenUser()
    {
        static::loginAdmin();
        $uid = static::$user['object'];
        $eid = static::editorID();
        $this->assertAnswers( array(
            "has_role_by_user( \$someone, $eid )" => true,
            'has_role_by_user( $someone, 0 )' => false,
            "has_role_by_user_id( $uid, $eid )" => true,
            "has_role_by_user_id( \$someone.contentobject_id, $eid )" => true,
            "has_role_by_user_id( 0, $eid )" => false,
            // no user object: false, and no error
            "has_role_by_user( 42, $eid )" => false,
            "has_role_by_user( \$nothing, $eid )" => false,
            "has_policy_by_user( 42, 'content', 'read' )" => false,
        ), array( 'someone' => eZUser::fetch( $uid ), 'nothing' => false ) );
    }

    public function testPoliciesOfTheCurrentUser()
    {
        static::loginAs( static::$user['object'] );
        $this->assertAnswers( array(
            "has_policy( 'content', 'read' )" => true,
            // a limited policy still counts: limitations are not evaluated
            "has_policy( 'content', 'edit' )" => true,
            "has_policy( 'content', 'remove' )" => false,
            // a policy for every function of k1c_module
            "has_policy( 'k1c_module', 'anything' )" => true,
            "has_policy( 'setup', 'administrate' )" => false,
            "has_policy( '', 'read' )" => false,
            "has_policy_by_user( \$someone, 'content', 'edit' )" => true,
            "has_policy_by_user( \$someone, 'k1c_module', 'view' )" => true,
            "has_policy_by_user( \$someone, 'setup', 'administrate' )" => false,
        ), array( 'someone' => eZUser::currentUser() ) );
    }

    public function testTheAdminUser()
    {
        static::loginAdmin();
        $admin = eZUser::currentUser();
        $administrator = eZRole::fetchByName( 'Administrator' );
        if ( !$administrator instanceof eZRole || !in_array( (int)$administrator->attribute( 'id' ), array_map( 'intval', $admin->roleIDList() ) ) )
            $this->markTestSkipped( 'the admin user has no role named Administrator on this installation' );
        $aid = (int)$administrator->attribute( 'id' );
        $this->assertAnswers( array(
            "has_role( 'Administrator' )" => true,
            "has_role_id( $aid )" => true,
            "has_any_role( array( 'Administrator', 'Publisher' ) )" => true,
            "has_role_by_user( \$me, $aid )" => true,
            // the Administrator role's policy for every module and function
            "has_policy( 'content', 'edit' )" => true,
            "has_policy( 'setup', 'administrate' )" => true,
            "has_policy_by_user( \$me, 'user', 'login' )" => true,
            "has_role( '" . static::editorName() . "' )" => false,
        ), array( 'me' => $admin ) );
    }

    public function testTheAnonymousUser()
    {
        $anonymousID = (int)eZUser::anonymousId();
        static::loginAs( $anonymousID );
        $anonymous = eZRole::fetchByName( 'Anonymous' );
        $user = eZUser::currentUser();
        if ( !$anonymous instanceof eZRole || !in_array( (int)$anonymous->attribute( 'id' ), array_map( 'intval', $user->roleIDList() ) ) )
            $this->markTestSkipped( 'the anonymous user has no role named Anonymous on this installation' );
        $nid = (int)$anonymous->attribute( 'id' );
        $readable = $this->roleSetHasPolicy( $user, 'content', 'read' );
        $editable = $this->roleSetHasPolicy( $user, 'content', 'edit' );
        $this->assertAnswers( array(
            "has_role( 'Anonymous' )" => true,
            "'any value'|has_role( 'Anonymous' )" => true,
            "has_role( 'Administrator' )" => false,
            "has_role_id( $nid )" => true,
            "has_any_role( array( 'Editor', $nid ) )" => true,
            "has_any_role( array( 'Anonymous', 'Administrator' ), 0, true() )" => false,
            "has_role_by_user( \$me, $nid )" => true,
            "has_role_by_user_id( \$me.contentobject_id, $nid )" => true,
            "has_policy( 'content', 'read' )" => $readable,
            "has_policy( 'content', 'edit' )" => $editable,
        ), array( 'me' => $user ) );
        $this->assertTrue( $readable, 'the anonymous user can read content on a standard installation' );
        $this->assertFalse( $editable, 'the anonymous user cannot edit content on a standard installation' );
    }

    public function testTheFeaturePageExamples()
    {
        static::loginAdmin();
        $source = <<<'TPL'
{def $current_user = fetch( 'user', 'current_user' )}
{* a block for editors only *}
{if $current_user|has_role('Editor')}
    <a href={'content/edit'|ezurl}>Edit</a>
{/if}

{* administrator or publisher *}
{if $current_user|has_any_role(array('Administrator', 'Publisher'))}
    A-OR-P
{/if}

{* must hold both roles, mixing names and ids *}
{if $current_user|has_any_role(array('Editor', 2), 0, true())}
    BOTH
{/if}

{* does the visitor's role set contain a content/edit policy? *}
{if $current_user|has_policy('content', 'edit')}
    POLICY
{/if}

{* a specific other user, by id *}
{if has_role_by_user_id(USERID, ROLEID)}
    OTHER
{/if}
{def $is_editor = $current_user|has_role('Editor')}{if $is_editor|not}NOT-EDITOR{/if}
TPL;
        $source = str_replace( array( 'USERID', 'ROLEID' ), array( static::$user['object'], static::editorID() ), $source );
        $output = $this->render( $source );
        $hasAdministrator = in_array( 'Administrator', array_map( function ( $r ) { return $r->attribute( 'name' ); }, eZUser::currentUser()->roles() ) );
        if ( $hasAdministrator )
        {
            $this->assertStringContainsString( 'A-OR-P', $output );
            $this->assertStringContainsString( 'POLICY', $output, 'the Administrator role has a content/edit policy through *' );
        }
        $this->assertStringContainsString( 'OTHER', $output );
        $hasEditor = in_array( 'Editor', array_map( function ( $r ) { return $r->attribute( 'name' ); }, eZUser::currentUser()->roles() ) );
        $this->assertSame( !$hasEditor, strpos( $output, 'NOT-EDITOR' ) !== false );
        $this->assertSame( $hasEditor, strpos( $output, 'Edit</a>' ) !== false );
    }

    /**
     * The same question the operator answers, asked of the policy rows directly.
     */
    private function roleSetHasPolicy( eZUser $user, $module, $function )
    {
        foreach ( $user->roles() as $role )
        {
            foreach ( $role->policyList() as $policy )
            {
                $m = $policy->attribute( 'module_name' );
                $f = $policy->attribute( 'function_name' );
                if ( ( $m === $module || $m === '*' ) && ( $f === $function || $f === '*' ) )
                    return true;
            }
        }
        return false;
    }
}
