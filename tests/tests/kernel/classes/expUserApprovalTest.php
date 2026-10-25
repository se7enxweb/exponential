<?php
/**
 * Approval of new user accounts (expUserApproval, the workflow event expUserApproveType): which placements need
 * approval, who needs none, how the event reads its settings, the requesters' policies, the link of the mail, and that
 * the kernel offers the event.
 *
 * No database: groups are path strings, the event a row in memory, the settings the kernel's own files.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expUserApprovalTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expUserApprovalTest extends PHPUnit\Framework\TestCase
{
    const MEMBERS = '/1/5/12/';
    const ADMINS = '/1/5/13/';
    const EDITORS = '/1/5/14/';

    protected function tearDown(): void
    {
        expUserApproval::reset();
    }

    private static function event( array $fields )
    {
        return new eZWorkflowEvent( $fields + array( 'id' => 7, 'version' => 0, 'workflow_id' => 3, 'workflow_type_string' => expUserApproval::TYPE_STRING,
                                                     'placement' => 1, 'description' => '', 'data_int1' => 0, 'data_int2' => 0, 'data_int3' => 0,
                                                     'data_int4' => 0, 'data_text1' => '', 'data_text2' => '', 'data_text3' => '',
                                                     'data_text4' => '', 'data_text5' => '' ) );
    }

    public function testIdListKeepsPositiveIdsOnce()
    {
        $this->assertSame( array( 14, 12 ), expUserApproval::idList( '14, 12,14,,x,-3,0' ) );
        $this->assertSame( array(), expUserApproval::idList( '' ) );
        $this->assertSame( array( 13 ), expUserApproval::idList( array( '13', 13, '' ) ) );
    }

    public function testPlacementsBelowARequiredGroupNeedApproval()
    {
        // directly in Editors, and in a group below Editors
        $this->assertSame( array( self::EDITORS, self::EDITORS . '60/' ),
                           expUserApproval::placedBelow( array( self::EDITORS, self::MEMBERS, self::EDITORS . '60/' ), array( self::EDITORS ) ) );
        // Members only: nothing to approve
        $this->assertSame( array(), expUserApproval::placedBelow( array( self::MEMBERS, self::MEMBERS . '61/' ), array( self::EDITORS ) ) );
        // a path that only starts with the same digits is another node
        $this->assertSame( array(), expUserApproval::placedBelow( array( '/1/5/140/' ), array( self::EDITORS ) ) );
        // no required group: nothing
        $this->assertSame( array(), expUserApproval::placedBelow( array( self::EDITORS ), array( '' ) ) );
    }

    public function testApproversAndExcludedGroupsNeedNoApproval()
    {
        $config = array( 'required_groups' => array( 13 ), 'excluded_groups' => array( 42 ), 'approve_users' => array( 99 ),
                         'approve_groups' => array( 12 ), 'mail' => true, 'event_id' => 7, 'workflow_id' => 3 );
        // an administrator (in the approving group)
        $this->assertSame( 'approver', expUserApproval::exemptBy( 14, array( 12, 4 ), $config ) );
        // a user who approves by name
        $this->assertSame( 'approver', expUserApproval::exemptBy( 99, array( 13 ), $config ) );
        // a member of a group without approval
        $this->assertSame( 'excluded', expUserApproval::exemptBy( 50, array( 42 ), $config ) );
        // an editor: waits
        $this->assertSame( '', expUserApproval::exemptBy( 60, array( 13, 4 ), $config ) );
        // the anonymous user of a self-registration: waits
        $this->assertSame( '', expUserApproval::exemptBy( 10, array( 42 + 1 ), $config ) );
    }

    public function testEventSettings()
    {
        $event = self::event( array( 'data_text1' => '13', 'data_text2' => '', 'data_text3' => '99', 'data_text4' => '12,12' ) );
        $config = expUserApproval::config( $event );
        $this->assertSame( array( 13 ), $config['required_groups'] );
        $this->assertSame( array(), $config['excluded_groups'] );
        $this->assertSame( array( 99 ), $config['approve_users'] );
        $this->assertSame( array( 12 ), $config['approve_groups'] );
        $this->assertTrue( $config['mail'], 'a new event sends the mail' );
        $this->assertSame( 7, $config['event_id'] );
        $this->assertFalse( expUserApproval::config( self::event( array( 'data_int1' => 1 ) ) )['mail'] );
    }

    public function testTheEventTypeDecodesItsFields()
    {
        $this->assertTrue( eZWorkflowType::loadAndRegisterType( expUserApproval::TYPE_STRING ) );
        $type = new expUserApproveType();
        $this->assertSame( expUserApproval::TYPE_STRING, $type->attribute( 'type_string' ) );
        $this->assertSame( array( 'content' => array( 'publish' => array( 'before' ) ) ), $type->attribute( 'allowed_triggers' ) );
        $event = self::event( array( 'data_text1' => '13,60', 'data_text2' => '42', 'data_text3' => '99', 'data_text4' => '12', 'data_int1' => 1 ) );
        $this->assertSame( array( 13, 60 ), $type->attributeDecoder( $event, 'required_groups' ) );
        $this->assertSame( array( 42 ), $type->attributeDecoder( $event, 'selected_usergroups' ) );
        $this->assertSame( array( 99 ), $type->attributeDecoder( $event, 'approve_users' ) );
        $this->assertSame( array( 12 ), $type->attributeDecoder( $event, 'approve_groups' ) );
        $this->assertFalse( $type->attributeDecoder( $event, 'send_mail' ) );
        $this->assertContains( 'required_groups', $type->typeFunctionalAttributes() );
    }

    public function testTheKernelOffersTheEvent()
    {
        $ini = eZINI::instance( 'workflow.ini', 'settings', null, null, false, true );
        $this->assertContains( expUserApproval::TYPE_STRING, $ini->variable( 'EventSettings', 'AvailableEventTypes' ) );
    }

    public function testRequestersMayOnlyCreateUsersDirectlyInTheGroup()
    {
        $policies = expUserApproval::requestPolicies( array( 14 ), 4, 3 );
        $described = array_map( array( 'expMemberManagement', 'describe' ), $policies );
        $this->assertCount( 2, $policies );
        $this->assertSame( 'content', $policies[0]['module'] );
        $this->assertSame( 'read', $policies[0]['function'] );
        $this->assertSame( array( 'Node' => array( '14' ) ), $policies[0]['limitations'] );
        $this->assertSame( 'create', $policies[1]['function'] );
        $this->assertSame( array( 'Class' => array( '4' ), 'Node' => array( '14' ), 'ParentClass' => array( '3' ) ), $policies[1]['limitations'] );
        // no edit, remove, move or location policy: the accounts in the group stay out of the requesters' reach
        foreach ( $policies as $policy )
            $this->assertNotContains( $policy['function'], array( 'edit', 'remove', 'manage_locations', 'move', 'hide' ) );
        // a second run adds nothing
        $this->assertSame( array(), expMemberManagement::missing( $policies, $policies ) );
        $this->assertNotEmpty( $described );
    }

    public function testTheMailLinksToTheApprovalItemWithoutAKey()
    {
        $url = expUserApproval::itemURL( 42, 'https://admin.example.invalid/' );
        $this->assertSame( 'https://admin.example.invalid/collaboration/item/full/42', $url );
        $this->assertStringNotContainsString( '?', $url, 'no key, no token: the approver signs in' );
    }

    public function testWithoutActiveEventsEveryPlacementIsAllowed()
    {
        expUserApproval::reset( array() );
        $this->assertSame( array(), expUserApproval::activeConfigs() );
        $this->assertTrue( expUserApproval::placementAllowed( null, 14 ) );
    }
}
