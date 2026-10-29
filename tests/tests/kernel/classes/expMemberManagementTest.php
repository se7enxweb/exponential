<?php
/**
 * Member management (expMemberManagement, membermanagement.ini): how a rule's policy set becomes the policies of its
 * role, which rules are refused because they reach a protected group, and what a second run adds.
 *
 * No database: groups are path strings, classes an identifier => id map, the role's policies plain arrays as
 * policiesOf() makes them. The settings file itself is read with eZINI (settings/membermanagement.ini, no override).
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expMemberManagementTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expMemberManagementTest extends PHPUnit\Framework\TestCase
{
    const MEMBERS = '/1/5/12/';
    const ADMINS = '/1/5/13/';
    const EDITORS = '/1/5/14/';

    private static function classes()
    {
        return array( 'user' => 4, 'user_group' => 3 );
    }

    private static function defaultLines()
    {
        return array( 'content/read', 'content/create;Class=user,user_group;ParentClass=user_group', 'content/edit;Class=user,user_group',
                      'content/remove;Class=user,user_group', 'content/manage_locations;Class=user,user_group', 'content/versionread',
                      'content/versionremove', 'content/diff', 'user/preferences' );
    }

    private static function described( array $policies )
    {
        return array_map( array( 'expMemberManagement', 'describe' ), $policies );
    }

    public function testParsePolicyLine()
    {
        $this->assertSame( array( 'module' => 'content', 'function' => 'create',
                                  'limitations' => array( 'Class' => array( 'user', 'user_group' ), 'ParentClass' => array( 'user_group' ) ) ),
                           expMemberManagement::parsePolicy( 'content/create;Class=user, user_group;ParentClass=user_group' ) );
        $this->assertSame( array( 'module' => 'user', 'function' => 'preferences', 'limitations' => array() ),
                           expMemberManagement::parsePolicy( 'user/preferences' ) );
        $this->assertNull( expMemberManagement::parsePolicy( 'content' ) );
        $this->assertNull( expMemberManagement::parsePolicy( 'content/edit;Class=' ) );
        $this->assertNull( expMemberManagement::parsePolicy( 'content/edit;Class' ) );
    }

    /** The default policy set: every content policy limited to Members, classes by id, the Users root readable on its own. */
    public function testDefaultPlan()
    {
        $plan = expMemberManagement::plan( self::defaultLines(), array( self::MEMBERS ), array( 5 ), self::classes() );
        $this->assertSame( array(), $plan['errors'] );
        $this->assertSame( array(
            'content/read Subtree=/1/5/12/',
            'content/create Class=3,4 ParentClass=3 Subtree=/1/5/12/',
            'content/edit Class=3,4 Subtree=/1/5/12/',
            'content/remove Class=3,4 Subtree=/1/5/12/',
            'content/manage_locations Class=3,4 Subtree=/1/5/12/',
            'content/versionread Subtree=/1/5/12/',
            'content/versionremove Subtree=/1/5/12/',
            'content/diff Subtree=/1/5/12/',
            'user/preferences',
            'content/read Node=5',
        ), self::described( $plan['policies'] ) );
    }

    /** Wildcards, administration modules, unknown classes and hand-written Subtree/Node limitations are refused. */
    public function testPlanRefusesWhatAManagerMustNotGet()
    {
        $plan = expMemberManagement::plan( array( 'content/*', 'role/assign', 'setup/managecache', 'content/edit;Class=nosuchclass',
                                                  'content/edit;Subtree=/1/5/13/', 'not a policy' ),
                                           array( self::MEMBERS ), array(), self::classes() );
        $this->assertCount( 6, $plan['errors'] );
        $this->assertSame( array(), $plan['policies'] );
    }

    /** Content policies without a managed group are not planned unlimited. */
    public function testNoManagedGroupNoContentPolicy()
    {
        $plan = expMemberManagement::plan( array( 'content/edit', 'user/preferences' ), array(), array(), self::classes() );
        $this->assertSame( array( 'user/preferences' ), self::described( $plan['policies'] ) );
        $this->assertCount( 1, $plan['errors'] );
    }

    /** A managed group that is, contains or lies below a protected group is refused; so is managing the own group. */
    public function testValidateProtectedGroups()
    {
        $protected = array( self::ADMINS, self::EDITORS );
        $this->assertSame( array(), expMemberManagement::validate( array( self::MEMBERS ), $protected, array( self::EDITORS ) ) );
        $this->assertCount( 1, expMemberManagement::validate( array( self::ADMINS ), $protected ) );
        $this->assertCount( 2, expMemberManagement::validate( array( '/1/5/' ), $protected ) );
        $this->assertCount( 1, expMemberManagement::validate( array( '/1/5/13/200/' ), $protected ) );
        $this->assertCount( 1, expMemberManagement::validate( array( self::MEMBERS ), $protected, array( '/1/5/12/40/' ) ) );
    }

    /** A second run adds nothing; a policy with other limitations does not count as the planned one. */
    public function testMissingIsIdempotent()
    {
        $planned = expMemberManagement::plan( self::defaultLines(), array( self::MEMBERS ), array( 5 ), self::classes() )['policies'];
        $this->assertCount( count( $planned ), expMemberManagement::missing( $planned, array() ) );
        $this->assertSame( array(), expMemberManagement::missing( $planned, array_reverse( $planned ) ) );
        $wider = $planned;
        $wider[2]['limitations']['Subtree'] = array( '/1/5/' );
        $this->assertSame( array( 'content/edit Class=3,4 Subtree=/1/5/12/' ),
                           self::described( expMemberManagement::missing( $planned, $wider ) ) );
    }

    /** The shipped settings reproduce the owner's decision: Editors manage Members; Administrator users and Editors are protected. */
    public function testShippedSettings()
    {
        $ini = eZINI::fetchFromFile( 'settings/membermanagement.ini' );
        $this->assertSame( 'enabled', $ini->variable( 'MemberManagementSettings', 'MemberManagement' ) );
        $this->assertSame( array( 'editors_manage_members' ), $ini->variable( 'MemberManagementSettings', 'Rules' ) );
        $this->assertSame( array( 'remote:9b47a45624b023b1a76c73b74d704acf', 'remote:3c160cca19fb135f83bd02d911f04db2' ),
                           $ini->variable( 'MemberManagementSettings', 'ProtectedGroups' ) );
        $this->assertSame( array( 'remote:3c160cca19fb135f83bd02d911f04db2' ), $ini->variable( 'MemberRule_editors_manage_members', 'ManagerGroups' ) );
        $this->assertSame( array( 'remote:5f7f0bdb3381d6a461d8c29ff53d908f' ), $ini->variable( 'MemberRule_editors_manage_members', 'ManagedGroups' ) );
        $this->assertSame( self::defaultLines(), $ini->variable( 'MemberPolicySet_member_management', 'Policies' ) );
        $this->assertSame( 'enabled', $ini->variable( 'MemberGuardSettings', 'Guard' ) );
        $this->assertSame( 'enabled', $ini->variable( 'MemberGuardSettings', 'SettingRequiresEdit' ) );
        foreach ( $ini->variable( 'MemberPolicySet_member_management', 'Policies' ) as $line )
        {
            $policy = expMemberManagement::parsePolicy( $line );
            $this->assertNotNull( $policy, $line );
            $this->assertNotContains( $policy['module'], array( 'role', 'setup', 'section', 'class' ), $line );
        }
    }

    /**
     * bin/php/addmembermanagement.php is a dry run by default: no flag and --dry-run (kept as a synonym) do not
     * write, --apply writes (the option parsing itself: expEditorPoliciesTest::testCommandWritesOnlyWithApply()).
     */
    public function testCommandWritesOnlyWithApply()
    {
        $this->assertFalse( \Exponential\Runnable\Command::applyRequested( array( 'apply' => false, 'dry-run' => false ) ) );
        $this->assertTrue( \Exponential\Runnable\Command::applyRequested( array( 'apply' => true, 'dry-run' => false ) ) );
        $this->assertNull( \Exponential\Runnable\Command::applyRequested( array( 'apply' => true, 'dry-run' => true ) ) );
        require_once __DIR__ . '/expEditorPoliciesTest.php';
        expEditorPoliciesTest::assertWritesOnlyOutsideADryRun( $this, 'kernel/private/classes/commands/addmembermanagement.php',
                                                               array( '\\expMemberManagement::apply(' ) );
    }

    public function testCommandRefusesApplyTogetherWithDryRun()
    {
        require_once __DIR__ . '/expEditorPoliciesTest.php';
        list( $code, $out ) = expEditorPoliciesTest::runCommand( 'addmembermanagement', array( '--apply', '--dry-run' ) );
        $this->assertSame( 2, $code, $out );
        $this->assertStringContainsString( '--apply and --dry-run exclude each other', $out );
    }
}
