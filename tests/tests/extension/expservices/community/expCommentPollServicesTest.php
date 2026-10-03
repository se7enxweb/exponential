<?php
/**
 * The comment, review and poll services on content created in a test folder under the Media root; tearDown removes
 * the folder with everything below it, and the poll votes with it.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../commerce/expCommerceTestCase.php';

class expCommentPollServicesTest extends expCommerceTestCase
{
    protected $pollObjects = array();

    public function tearDown(): void
    {
        if ( self::$bootError === null )
            foreach ( $this->pollObjects as $id )
                eZInformationCollection::removeContentObject( $id );
        parent::tearDown();
    }

    protected function comment( $node, $subject = 'Nice', $message = 'I like it' )
    {
        return $this->ok( 'expCommentServices', 'create', array(), array( 'node_id' => $node,
            'fields' => json_encode( array( 'subject' => $subject, 'author' => 'Tester', 'message' => $message ) ) ) )['node_id'];
    }

    protected function newPoll( array $choices = array( 'Yes', 'No', 'Maybe' ) )
    {
        $p = $this->ok( 'expPollServices', 'create', array(), array( 'parent_node_id' => $this->testFolder(), 'name' => 'Test poll',
            'question' => 'Do you like polls?', 'choices' => implode( ',', $choices ) ) );
        $this->pollObjects[] = $p['object_id'];
        $this->createdObjects[] = $p['object_id'];
        return $p;
    }

    // ---------------------------------------------------------------- comments

    public function testCommentOnANode()
    {
        $p = $this->createProduct();
        $this->assertTrue( $this->ok( 'expCommentServices', 'canComment', array( $p ) )['can'] );
        $c = $this->comment( $p, 'Great', 'Works well' );
        $v = $this->ok( 'expCommentServices', 'view', array( $c ) );
        $this->assertSame( 'Great', $v['fields']['subject'] );
        $this->assertSame( 'Tester', $v['fields']['author'] );
        $this->assertSame( $p, $v['parent_node_id'] );
        $this->assertSame( 1, $this->ok( 'expCommentServices', 'count', array( $p ) )['count'] );
    }

    public function testCommentsAreListedInBothOrders()
    {
        $p = $this->createProduct();
        $a = $this->comment( $p, 'one' );
        $b = $this->comment( $p, 'two' );
        $asc = $this->call( 'expCommentServices', 'list', array( $p, 10, 0 ) );
        $this->assertSame( array( $a, $b ), array_column( $asc['data'], 'node_id' ) );
        $this->assertSame( 2, $asc['meta']['total'] );
        $desc = $this->ok( 'expCommentServices', 'list', array( $p, 10, 0, 'true' ) );
        $this->assertSame( array( $b, $a ), array_column( $desc, 'node_id' ) );
    }

    public function testEditAndRemoveAComment()
    {
        $p = $this->createProduct();
        $c = $this->comment( $p, 'before' );
        $e = $this->ok( 'expCommentServices', 'edit', array(), array( 'node_id' => $c, 'fields' => json_encode( array( 'subject' => 'after' ) ) ) );
        $this->assertSame( 'after', $e['fields']['subject'] );
        $this->assertSame( array( 'removed' => $c ), $this->ok( 'expCommentServices', 'remove', array(), array( 'node_id' => $c ) ) );
        $this->assertSame( 0, $this->ok( 'expCommentServices', 'count', array( $p ) )['count'] );
    }

    public function testModerationHidesAndRevealsAComment()
    {
        $p = $this->createProduct();
        $c = $this->comment( $p );
        $this->assertTrue( $this->ok( 'expCommentServices', 'hide', array(), array( 'node_id' => $c ) )['is_hidden'] );
        $hidden = $this->call( 'expCommentServices', 'hidden', array( $p, 10, 0 ) );
        $this->assertSame( array( $c ), array_column( $hidden['data'], 'node_id' ) );
        $this->assertFalse( $this->ok( 'expCommentServices', 'unhide', array(), array( 'node_id' => $c ) )['is_hidden'] );
        $this->assertCount( 0, $this->call( 'expCommentServices', 'hidden', array( $p, 10, 0 ) )['data'] );
    }

    public function testSearchLatestMineAndByUser()
    {
        $p = $this->createProduct();
        $c = $this->comment( $p, 'Searchable', 'contains xyzzyplugh' );
        $this->assertSame( $c, $this->ok( 'expCommentServices', 'search', array( 'xyzzyplugh', $p ) )[0]['node_id'] );
        $this->assertSame( $c, $this->ok( 'expCommentServices', 'latest', array( 1, $p ) )[0]['node_id'] );
        $admin = (int)eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' );
        $this->assertContains( $c, array_column( $this->call( 'expCommentServices', 'byUser', array( $admin, 100, 0 ) )['data'], 'node_id' ) );
        $this->assertContains( $c, array_column( $this->call( 'expCommentServices', 'mine', array( 100, 0 ) )['data'], 'node_id' ) );
    }

    public function testCommentInputIsValidated()
    {
        $p = $this->createProduct();
        $this->fails( 422, 'expCommentServices', 'create', array(), array( 'node_id' => $p, 'fields' => json_encode( array( 'author' => 'x' ) ) ) );
        $this->fails( 404, 'expCommentServices', 'create', array(), array( 'node_id' => 99999999, 'fields' => '{}' ) );
        $this->fails( 404, 'expCommentServices', 'view', array( $p ) );
    }

    public function testReviewsAndTheRatingSummary()
    {
        $p = $this->createProduct();
        foreach ( array( 5, 4, 5 ) as $stars )
            $this->ok( 'expCommentServices', 'addReview', array(), array( 'node_id' => $p,
                'fields' => json_encode( array( 'title' => 'Review', 'rating' => $stars, 'author' => 'T', 'body' => 'text' ) ) ) );
        $r = $this->call( 'expCommentServices', 'reviews', array( $p, 10, 0 ) );
        $this->assertSame( 3, $r['meta']['total'] );
        $this->assertEqualsWithDelta( 4.67, $r['meta']['average'], 0.01 );
        $s = $this->ok( 'expCommentServices', 'ratingSummary', array( $p ) );
        $this->assertSame( 3, $s['count'] );
        $this->assertSame( 2, $s['stars'][5] );
        $this->assertSame( 1, $s['stars'][4] );
        $this->fails( 422, 'expCommentServices', 'addReview', array(), array( 'node_id' => $p, 'fields' => json_encode( array( 'title' => 't', 'rating' => 9 ) ) ) );
    }

    // ---------------------------------------------------------------- polls

    public function testCreateAndViewAPoll()
    {
        $p = $this->newPoll();
        $this->assertSame( 'Do you like polls?', $p['question'] );
        $this->assertSame( array( 'Yes', 'No', 'Maybe' ), array_column( $p['choices'], 'value' ) );
        $this->assertSame( 0, $p['total'] );
        $v = $this->ok( 'expPollServices', 'view', array( $p['node_id'] ) );
        $this->assertSame( $p['node_id'], $v['node_id'] );
        $this->assertCount( 3, $this->ok( 'expPollServices', 'choices', array( $p['node_id'] ) ) );
    }

    public function testPollInputIsValidated()
    {
        $f = $this->testFolder();
        $this->fails( 422, 'expPollServices', 'create', array(), array( 'parent_node_id' => $f, 'name' => 'n', 'question' => 'q', 'choices' => 'only one' ) );
        $this->fails( 422, 'expPollServices', 'create', array(), array( 'parent_node_id' => $f, 'name' => ' ', 'question' => 'q', 'choices' => 'a,b' ) );
        $this->fails( 404, 'expPollServices', 'view', array( $f ) );
    }

    public function testVotingCountsAndComputesPercentages()
    {
        $p = $this->newPoll( array( 'A', 'B' ) );
        $choices = array_column( $p['choices'], 'id' );
        $this->assertTrue( $this->ok( 'expPollServices', 'canVote', array( $p['node_id'] ) )['can'] );
        $r = $this->ok( 'expPollServices', 'vote', array(), array( 'node_id' => $p['node_id'], 'choice' => $choices[0] ) );
        $this->assertSame( 1, $r['total'] );
        $this->assertSame( 1, $r['choices'][0]['votes'] );
        $this->assertEqualsWithDelta( 100.0, $r['choices'][0]['percent'], 0.1 );
        $res = $this->ok( 'expPollServices', 'results', array( $p['node_id'] ) );
        $this->assertSame( 1, $res['total'] );
        $my = $this->ok( 'expPollServices', 'myVote', array( $p['node_id'] ) );
        $this->assertTrue( $my['voted'] );
        $this->assertSame( $choices[0], $my['choice'] );
    }

    public function testAPollAllowsOneVotePerUser()
    {
        $p = $this->newPoll( array( 'A', 'B' ) );
        $choices = array_column( $p['choices'], 'id' );
        $this->ok( 'expPollServices', 'vote', array(), array( 'node_id' => $p['node_id'], 'choice' => $choices[0] ) );
        $c = $this->ok( 'expPollServices', 'canVote', array( $p['node_id'] ) );
        $this->assertFalse( $c['can'] );
        $this->assertSame( 'already voted', $c['reason'] );
        $this->fails( 409, 'expPollServices', 'vote', array(), array( 'node_id' => $p['node_id'], 'choice' => $choices[1] ) );
    }

    public function testVoteForAChoiceThatDoesNotExist()
    {
        $p = $this->newPoll();
        $this->fails( 422, 'expPollServices', 'vote', array(), array( 'node_id' => $p['node_id'], 'choice' => 99999 ) );
        $this->assertSame( 0, $this->ok( 'expPollServices', 'results', array( $p['node_id'] ) )['total'] );
    }

    public function testPollListAndLatest()
    {
        $p = $this->newPoll();
        $list = $this->call( 'expPollServices', 'list', array( $this->testFolder(), 10, 0 ) );
        $this->assertSame( 1, $list['meta']['total'] );
        $this->assertSame( $p['node_id'], $list['data'][0]['node_id'] );
        $this->assertContains( $p['node_id'], array_column( $this->ok( 'expPollServices', 'latest', array( 50 ) ), 'node_id' ) );
    }

    public function testResetVotesAndRemoveAPoll()
    {
        $p = $this->newPoll( array( 'A', 'B' ) );
        $choices = array_column( $p['choices'], 'id' );
        $this->ok( 'expPollServices', 'vote', array(), array( 'node_id' => $p['node_id'], 'choice' => $choices[0] ) );
        $this->assertSame( array( 'removed' => 1 ), $this->ok( 'expPollServices', 'resetVotes', array(), array( 'node_id' => $p['node_id'] ) ) );
        $this->assertSame( 0, $this->ok( 'expPollServices', 'results', array( $p['node_id'] ) )['total'] );
        $this->assertSame( array( 'removed' => $p['node_id'] ), $this->ok( 'expPollServices', 'remove', array(), array( 'node_id' => $p['node_id'] ) ) );
        $this->fails( 404, 'expPollServices', 'view', array( $p['node_id'] ) );
    }

    public function testAnonymousMayReadPollsButNotCreateThem()
    {
        $p = $this->newPoll();
        $this->loginAnonymous();
        $this->assertTrue( $this->call( 'expPollServices', 'results', array( $p['node_id'] ) )['ok'] );
        $r = $this->call( 'expPollServices', 'create', array(), array( 'parent_node_id' => $this->testFolder(), 'name' => 'x', 'question' => 'q', 'choices' => 'a,b' ) );
        $this->assertFalse( $r['ok'] );
        $this->assertContains( $r['error']['code'], array( 401, 403 ) );
        $this->fails( 401, 'expPollServices', 'resetVotes', array(), array( 'node_id' => $p['node_id'] ) );
    }
}
