<?php
/**
 * The forum, topic and reply services on a forum created in a test folder under the Media root; tearDown removes
 * the folder with everything below it.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../commerce/expCommerceTestCase.php';

class expForumServicesTest extends expCommerceTestCase
{
    protected function newForum( $name = 'Test forum' )
    {
        $f = $this->ok( 'expForumServices', 'create', array(), array( 'parent_node_id' => $this->testFolder(), 'fields' => json_encode( array( 'name' => $name, 'description' => 'd' ) ) ) );
        return $f['node_id'];
    }

    protected function newTopic( $forum, $subject = 'A topic', $message = 'The first message' )
    {
        $t = $this->ok( 'expTopicServices', 'create', array(), array( 'forum_node_id' => $forum, 'fields' => json_encode( array( 'subject' => $subject, 'message' => $message ) ) ) );
        return $t['node_id'];
    }

    protected function newReply( $topic, $subject = 'Re: a topic', $message = 'A reply' )
    {
        $r = $this->ok( 'expReplyServices', 'create', array(), array( 'topic_node_id' => $topic, 'fields' => json_encode( array( 'subject' => $subject, 'message' => $message ) ) ) );
        return $r['node_id'];
    }

    // ---------------------------------------------------------------- forums

    public function testForumFieldsShowTheConfiguredClasses()
    {
        $f = $this->ok( 'expForumServices', 'fields' );
        $this->assertSame( 'forum', $f['forum']['class'] );
        $this->assertTrue( $f['topic']['exists'] );
        $this->assertArrayHasKey( 'subject', $f['topic']['fields'] );
    }

    public function testCreateViewEditAndListAForum()
    {
        $id = $this->newForum( 'Alpha forum' );
        $v = $this->ok( 'expForumServices', 'view', array( $id ) );
        $this->assertSame( 'Alpha forum', $v['name'] );
        $this->assertSame( 0, $v['topics'] );
        $this->assertNull( $v['latest_topic'] );
        $e = $this->ok( 'expForumServices', 'edit', array(), array( 'node_id' => $id, 'fields' => json_encode( array( 'name' => 'Beta forum' ) ) ) );
        $this->assertSame( 'Beta forum', $e['name'] );
        $list = $this->call( 'expForumServices', 'list', array( $this->testFolder(), 10, 0 ) );
        $this->assertSame( 1, $list['meta']['total'] );
        $this->assertSame( $id, $list['data'][0]['node_id'] );
    }

    public function testForumInputIsValidated()
    {
        $this->fails( 422, 'expForumServices', 'create', array(), array( 'parent_node_id' => $this->testFolder(), 'fields' => json_encode( array( 'name' => '' ) ) ) );
        $this->fails( 422, 'expForumServices', 'create', array(), array( 'parent_node_id' => $this->testFolder(), 'fields' => json_encode( array( 'nonsense' => 'x', 'name' => 'n' ) ) ) );
        $this->fails( 400, 'expForumServices', 'create', array(), array( 'parent_node_id' => $this->testFolder(), 'fields' => '{broken' ) );
        $this->fails( 404, 'expForumServices', 'view', array( 43 ) );
    }

    public function testStatsLatestSearchAndPath()
    {
        $f = $this->newForum( 'Searchable forum zeta' );
        $t = $this->newTopic( $f, 'Topic one' );
        $this->newReply( $t );
        $s = $this->ok( 'expForumServices', 'stats', array( $f ) );
        $this->assertSame( 1, $s['topics'] );
        $this->assertSame( 1, $s['replies'] );
        $this->assertNotNull( $s['last'] );
        $this->assertContains( $t, array_column( $this->ok( 'expForumServices', 'latest', array( 5 ) ), 'node_id' ) );
        $hits = $this->ok( 'expForumServices', 'search', array( 'forum zeta' ) );
        $this->assertSame( $f, $hits[0]['node_id'] );
        $path = $this->ok( 'expForumServices', 'path', array( $t ) );
        $this->assertSame( $t, end( $path )['node_id'] );
        $this->assertContains( $f, array_column( $path, 'node_id' ) );
    }

    public function testHideAndUnhideAForum()
    {
        $f = $this->newForum();
        $this->assertTrue( $this->ok( 'expForumServices', 'hide', array(), array( 'node_id' => $f ) )['is_hidden'] );
        $this->assertFalse( $this->ok( 'expForumServices', 'unhide', array(), array( 'node_id' => $f ) )['is_hidden'] );
    }

    public function testRemoveAForum()
    {
        $f = $this->newForum();
        $this->assertSame( array( 'removed' => $f ), $this->ok( 'expForumServices', 'remove', array(), array( 'node_id' => $f ) ) );
        $this->fails( 404, 'expForumServices', 'view', array( $f ) );
    }

    // ---------------------------------------------------------------- topics

    public function testCreateViewAndEditATopic()
    {
        $f = $this->newForum();
        $t = $this->newTopic( $f, 'Hello', 'World' );
        $v = $this->ok( 'expTopicServices', 'view', array( $t ) );
        $this->assertSame( 'Hello', $v['fields']['subject'] );
        $this->assertSame( 'World', $v['fields']['message'] );
        $this->assertSame( 0, $v['replies'] );
        $e = $this->ok( 'expTopicServices', 'edit', array(), array( 'node_id' => $t, 'fields' => json_encode( array( 'message' => 'Changed' ) ) ) );
        $this->assertSame( 'Changed', $e['fields']['message'] );
        $this->assertSame( 'Hello', $e['fields']['subject'] );
    }

    public function testTopicFieldsCanComeAsPostFields()
    {
        $f = $this->newForum();
        $t = $this->ok( 'expTopicServices', 'create', array(), array( 'forum_node_id' => $f, 'subject' => 'By field', 'message' => 'Body' ) );
        $this->assertSame( 'By field', $t['fields']['subject'] );
    }

    public function testTopicRequiresItsRequiredFields()
    {
        $f = $this->newForum();
        $this->fails( 422, 'expTopicServices', 'create', array(), array( 'forum_node_id' => $f, 'fields' => json_encode( array( 'message' => 'no subject' ) ) ) );
        $this->fails( 400, 'expTopicServices', 'edit', array(), array( 'node_id' => $this->newTopic( $f ) ) );
        $this->fails( 404, 'expTopicServices', 'view', array( $f ) );
    }

    public function testTopicListPutsStickyFirstAndCounts()
    {
        $f = $this->newForum();
        $a = $this->newTopic( $f, 'First' );
        $b = $this->newTopic( $f, 'Second' );
        $c = $this->newTopic( $f, 'Third' );
        $this->assertSame( 3, $this->ok( 'expTopicServices', 'count', array( $f ) )['count'] );
        $s = $this->ok( 'expTopicServices', 'setSticky', array(), array( 'node_id' => $a, 'sticky' => '1' ) );
        $this->assertTrue( $s['sticky'] );
        $list = $this->call( 'expTopicServices', 'list', array( $f, 2, 0 ) );
        $this->assertSame( $a, $list['data'][0]['node_id'] );
        $this->assertSame( 3, $list['meta']['total'] );
        $this->assertTrue( $list['meta']['has_more'] );
        $this->assertSame( array( $a ), array_column( $this->ok( 'expTopicServices', 'sticky', array( $f ) ), 'node_id' ) );
        $this->assertFalse( $this->ok( 'expTopicServices', 'setSticky', array(), array( 'node_id' => $a, 'sticky' => '0' ) )['sticky'] );
    }

    public function testLatestSearchMineAndByUser()
    {
        $f = $this->newForum();
        $t = $this->newTopic( $f, 'Findable quuxfoo', 'text' );
        $this->assertSame( $t, $this->ok( 'expTopicServices', 'latest', array( 1, $f ) )[0]['node_id'] );
        $hits = $this->ok( 'expTopicServices', 'search', array( 'quuxfoo', $f ) );
        $this->assertCount( 1, $hits );
        $admin = (int)eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' );
        $by = $this->call( 'expTopicServices', 'byUser', array( $admin, 100, 0 ) );
        $this->assertContains( $t, array_column( $by['data'], 'node_id' ) );
        $mine = $this->call( 'expTopicServices', 'mine', array( 100, 0 ) );
        $this->assertContains( $t, array_column( $mine['data'], 'node_id' ) );
    }

    public function testMoveATopicToAnotherForum()
    {
        $a = $this->newForum( 'From' );
        $b = $this->newForum( 'To' );
        $t = $this->newTopic( $a );
        $m = $this->ok( 'expTopicServices', 'move', array(), array( 'node_id' => $t, 'forum_node_id' => $b ) );
        $this->assertSame( $b, $m['parent_node_id'] );
        $this->assertSame( 0, $this->ok( 'expTopicServices', 'count', array( $a ) )['count'] );
        $this->assertSame( 1, $this->ok( 'expTopicServices', 'count', array( $b ) )['count'] );
    }

    public function testHideAndRemoveATopic()
    {
        $f = $this->newForum();
        $t = $this->newTopic( $f );
        $this->assertTrue( $this->ok( 'expTopicServices', 'hide', array(), array( 'node_id' => $t ) )['is_hidden'] );
        $this->assertFalse( $this->ok( 'expTopicServices', 'unhide', array(), array( 'node_id' => $t ) )['is_hidden'] );
        $this->ok( 'expTopicServices', 'remove', array(), array( 'node_id' => $t ) );
        $this->assertSame( 0, $this->ok( 'expTopicServices', 'count', array( $f ) )['count'] );
    }

    public function testCanCreateATopic()
    {
        $f = $this->newForum();
        $this->assertTrue( $this->ok( 'expTopicServices', 'canCreate', array( $f ) )['can'] );
    }

    // ---------------------------------------------------------------- replies

    public function testRepliesAreListedOldestFirst()
    {
        $f = $this->newForum();
        $t = $this->newTopic( $f );
        $a = $this->newReply( $t, 'one', 'first' );
        $b = $this->newReply( $t, 'two', 'second' );
        $list = $this->call( 'expReplyServices', 'list', array( $t, 10, 0 ) );
        $this->assertSame( 2, $list['meta']['total'] );
        $this->assertSame( array( $a, $b ), array_column( $list['data'], 'node_id' ) );
        $desc = $this->ok( 'expReplyServices', 'list', array( $t, 10, 0, 'true' ) );
        $this->assertSame( array( $b, $a ), array_column( $desc, 'node_id' ) );
        $this->assertSame( 2, $this->ok( 'expReplyServices', 'count', array( $t ) )['count'] );
        $this->assertSame( 2, $this->ok( 'expTopicServices', 'view', array( $t ) )['replies'] );
        $this->assertSame( $b, $this->ok( 'expTopicServices', 'lastReply', array( $t ) )['node_id'] );
    }

    public function testEditHideAndRemoveAReply()
    {
        $f = $this->newForum();
        $t = $this->newTopic( $f );
        $r = $this->newReply( $t, 'x', 'before' );
        $e = $this->ok( 'expReplyServices', 'edit', array(), array( 'node_id' => $r, 'fields' => json_encode( array( 'message' => 'after' ) ) ) );
        $this->assertSame( 'after', $e['fields']['message'] );
        $this->assertTrue( $this->ok( 'expReplyServices', 'hide', array(), array( 'node_id' => $r ) )['is_hidden'] );
        $this->ok( 'expReplyServices', 'unhide', array(), array( 'node_id' => $r ) );
        $this->ok( 'expReplyServices', 'remove', array(), array( 'node_id' => $r ) );
        $this->assertSame( 0, $this->ok( 'expReplyServices', 'count', array( $t ) )['count'] );
    }

    public function testReplyQuoteSearchLatestAndByUser()
    {
        $f = $this->newForum();
        $t = $this->newTopic( $f );
        $r = $this->newReply( $t, 'Wise words', 'Zxcvbn is a password' );
        $q = $this->ok( 'expReplyServices', 'quote', array( $r ) );
        $this->assertSame( 'Re: Wise words', $q['subject'] );
        $this->assertStringContainsString( '[quote=', $q['message'] );
        $this->assertStringContainsString( 'Zxcvbn', $q['message'] );
        $this->assertCount( 1, $this->ok( 'expReplyServices', 'search', array( 'zxcvbn', $t ) ) );
        $this->assertSame( $r, $this->ok( 'expReplyServices', 'latest', array( 1, $t ) )[0]['node_id'] );
        $admin = (int)eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' );
        $this->assertContains( $r, array_column( $this->call( 'expReplyServices', 'byUser', array( $admin, 100, 0 ) )['data'], 'node_id' ) );
        $this->assertContains( $r, array_column( $this->call( 'expReplyServices', 'mine', array( 100, 0 ) )['data'], 'node_id' ) );
    }

    public function testReplyNeedsATopic()
    {
        $f = $this->newForum();
        $this->fails( 404, 'expReplyServices', 'list', array( $f ) );
        $this->fails( 404, 'expReplyServices', 'create', array(), array( 'topic_node_id' => $f, 'fields' => json_encode( array( 'subject' => 's', 'message' => 'm' ) ) ) );
        $this->fails( 404, 'expReplyServices', 'view', array( $f ) );
    }

    public function testAnonymousReadsButCannotWrite()
    {
        $f = $this->newForum();
        $t = $this->newTopic( $f );
        $this->loginAnonymous();
        $this->assertTrue( $this->call( 'expTopicServices', 'list', array( $f ) )['ok'] );
        $this->fails( 401, 'expTopicServices', 'mine' );
        $r = $this->call( 'expTopicServices', 'create', array(), array( 'forum_node_id' => $f, 'fields' => json_encode( array( 'subject' => 's', 'message' => 'm' ) ) ) );
        $this->assertFalse( $r['ok'] );
        $this->assertContains( $r['error']['code'], array( 401, 403 ) );
    }
}
