<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expUsersTestCase.php';

class expCollaborationServicesTest extends expUsersTestCase
{
    /** An approval item between an author and an approver, built without the notification event. */
    protected function approval( $author, $approver, $status = 0 )
    {
        $item = eZCollaborationItem::create( 'ezapprove', $author );
        $item->setAttribute( 'data_int1', 1 );
        $item->setAttribute( 'data_int2', 1 );
        $item->setAttribute( 'data_int3', $status );
        $item->store();
        $id = (int)$item->attribute( 'id' );
        self::$made['items'][] = $id;
        foreach ( array( array( $author, eZCollaborationItemParticipantLink::ROLE_AUTHOR ), array( $approver, eZCollaborationItemParticipantLink::ROLE_APPROVER ) ) as $p )
        {
            eZCollaborationItemParticipantLink::create( $id, $p[0], $p[1], eZCollaborationItemParticipantLink::TYPE_USER )->store();
            $profile = eZCollaborationProfile::instance( $p[0] );
            eZCollaborationItemGroupLink::addItem( $profile->attribute( 'main_group' ), $id, $p[0] );
        }
        return $id;
    }

    public function testEmptyListAndSummary()
    {
        $this->loginAs( $this->memberUser() );
        $this->assertPaged( $this->call( 'expCollaborationServices', 'items' ) );
        $this->assertSame( 0, $this->okCall( 'expCollaborationServices', 'itemCount' )['count'] );
        $this->assertSame( 0, $this->okCall( 'expCollaborationServices', 'summary' )['pending_approvals'] );
        $this->assertNotEmpty( $this->okCall( 'expCollaborationServices', 'handlers' ) );
        $this->assertError( $this->call( 'expCollaborationServices', 'items', array( '5', '0', 'bogus' ) ), 400 );
    }

    public function testItemVisibleToParticipantsOnly()
    {
        $author = $this->newUser();
        $approver = $this->newUser();
        $stranger = $this->newUser();
        $id = $this->approval( $author, $approver );
        $this->loginAs( $approver );
        $item = $this->okCall( 'expCollaborationServices', 'fetch', array( (string)$id ) );
        $this->assertSame( 'ezapprove', $item['type'] );
        $this->assertSame( 1, $this->okCall( 'expCollaborationServices', 'itemCount' )['count'] );
        $this->loginAs( $stranger );
        $this->assertError( $this->call( 'expCollaborationServices', 'fetch', array( (string)$id ) ), 404 );
        $this->assertError( $this->call( 'expCollaborationServices', 'messages', array( (string)$id ) ), 404 );
    }

    public function testApprove()
    {
        $author = $this->newUser();
        $approver = $this->newUser();
        $id = $this->approval( $author, $approver );
        $this->loginAs( $approver );
        $this->assertSame( 1, $this->okCall( 'expCollaborationServices', 'summary' )['pending_approvals'] );
        $st = $this->okCall( 'expCollaborationServices', 'approvalStatus', array( (string)$id ) );
        $this->assertSame( 'waiting', $st['state'] );
        $this->assertTrue( $st['can_decide'] );
        $d = $this->okWrite( 'expCollaborationServices', 'approve', array( 'item' => $id, 'comment' => 'Looks good' ) );
        $this->assertSame( 'accepted', $d['state'] );
        $this->assertSame( 1, $this->okCall( 'expCollaborationServices', 'messageCount', array( (string)$id ) )['count'] );
        $this->assertError( $this->write( 'expCollaborationServices', 'deny', array( 'item' => $id ) ), 409 );
    }

    public function testDeny()
    {
        $author = $this->newUser();
        $approver = $this->newUser();
        $id = $this->approval( $author, $approver );
        $this->loginAs( $approver );
        $this->assertSame( 'denied', $this->okWrite( 'expCollaborationServices', 'deny', array( 'item' => $id ) )['state'] );
    }

    public function testOnlyApproversDecide()
    {
        $author = $this->newUser();
        $approver = $this->newUser();
        $id = $this->approval( $author, $approver );
        $this->loginAs( $author );
        $this->assertFalse( $this->okCall( 'expCollaborationServices', 'approvalStatus', array( (string)$id ) )['can_decide'] );
        $this->assertError( $this->write( 'expCollaborationServices', 'approve', array( 'item' => $id ) ), 403 );
    }

    public function testMessagesAndParticipants()
    {
        $author = $this->newUser();
        $approver = $this->newUser();
        $id = $this->approval( $author, $approver );
        $this->loginAs( $author );
        $this->okWrite( 'expCollaborationServices', 'addMessage', array( 'item' => $id, 'text' => 'Please review' ) );
        $r = $this->call( 'expCollaborationServices', 'messages', array( (string)$id ) );
        $this->assertPaged( $r );
        $this->assertSame( 'Please review', $r['data'][0]['text'] );
        $this->assertCount( 2, $this->okCall( 'expCollaborationServices', 'participants', array( (string)$id ) ) );
        $this->assertError( $this->write( 'expCollaborationServices', 'addMessage', array( 'item' => $id, 'text' => ' ' ) ), 422 );
    }

    public function testMarkReadAndActive()
    {
        $author = $this->newUser();
        $approver = $this->newUser();
        $id = $this->approval( $author, $approver );
        $this->loginAs( $approver );
        $this->assertTrue( $this->okWrite( 'expCollaborationServices', 'markRead', array( 'item' => $id ) )['read'] );
        $this->assertFalse( $this->okWrite( 'expCollaborationServices', 'setActive', array( 'item' => $id, 'active' => '0' ) )['active'] );
    }

    public function testGroupsLifecycle()
    {
        $this->loginAs( $this->memberUser() );
        $g = $this->okWrite( 'expCollaborationServices', 'groupCreate', array( 'title' => 'Mine' ) );
        $this->assertSame( 'Mine', $this->okCall( 'expCollaborationServices', 'groupInfo', array( (string)$g['id'] ) )['title'] );
        $this->assertSame( 'Renamed', $this->okWrite( 'expCollaborationServices', 'groupRename', array( 'group' => $g['id'], 'title' => 'Renamed' ) )['title'] );
        $this->assertContains( $g['id'], array_column( $this->call( 'expCollaborationServices', 'groups' )['data'], 'id' ) );
        $this->assertPaged( $this->call( 'expCollaborationServices', 'groupItems', array( (string)$g['id'] ) ) );
        $this->assertTrue( $this->okWrite( 'expCollaborationServices', 'groupRemove', array( 'group' => $g['id'] ) )['removed'] );
        $this->assertError( $this->call( 'expCollaborationServices', 'groupInfo', array( (string)$g['id'] ) ), 404 );
    }

    public function testMoveItemToGroup()
    {
        $author = $this->newUser();
        $approver = $this->newUser();
        $id = $this->approval( $author, $approver );
        $this->loginAs( $approver );
        $g = $this->okWrite( 'expCollaborationServices', 'groupCreate', array( 'title' => 'Moved' ) );
        $this->okWrite( 'expCollaborationServices', 'moveToGroup', array( 'item' => $id, 'group' => $g['id'] ) );
        $this->assertSame( 1, $this->meta( $this->call( 'expCollaborationServices', 'groupItems', array( (string)$g['id'] ) ) )['total'] );
        $this->assertError( $this->write( 'expCollaborationServices', 'groupRemove', array( 'group' => $g['id'] ) ), 409 );
        $this->assertError( $this->write( 'expCollaborationServices', 'groupCreate', array( 'title' => 'Sub', 'parent' => 99999999 ) ), 404 );
    }

    public function testAnonymousHasNoCollaboration()
    {
        $this->loginAnonymous();
        $this->assertError( $this->call( 'expCollaborationServices', 'items' ), 401 );
    }
}
