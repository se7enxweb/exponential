<?php
/**
 * The review hold (expReviewHold): its states and their order, the role's policies, which policies the upgrade step
 * limits and which it leaves, the objects without a state of the group it links on every engine, the publish
 * operation's two checks and the step's registration.
 *
 * No database, no files.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expReviewHoldTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class expReviewHoldTest extends PHPUnit\Framework\TestCase
{
    public function testTheFirstStateIsNotHeldSoEveryObjectStartsFree()
    {
        $this->assertSame( array( 'not_held', 'held' ), array_keys( expReviewHold::states() ) );
        foreach ( expReviewHold::states() as $texts )
        {
            $this->assertCount( 2, $texts );
            $this->assertNotSame( '', $texts[0] );
        }
    }

    public function testTheGroupIsSeenAsALimitation()
    {
        // eZContentObjectStateGroup::limitations() leaves out the groups whose identifier starts with ez
        $this->assertStringStartsNotWith( 'ez', expReviewHold::GROUP );
        $this->assertSame( 'StateGroup_' . expReviewHold::GROUP, expReviewHold::LIMITATION );
    }

    public function testTheRoleAssignsBothStatesOfTheGroupOnly()
    {
        $policies = expReviewHold::rolePolicies();
        $this->assertCount( 1, $policies );
        list( $module, $function, $limitations ) = $policies[0];
        $this->assertSame( array( 'state', 'assign' ), array( $module, $function ) );
        $this->assertSame( array( 'not_held', 'held' ), $limitations[expReviewHold::LIMITATION] );
        $this->assertSame( array( 'not_held', 'held' ), $limitations['NewState'] );
    }

    private static function roles()
    {
        return array(
            array( 'id' => 3, 'name' => 'Editor', 'policies' => array(
                array( 'id' => 10, 'module' => 'content', 'function' => 'edit', 'limitations' => array() ),
                array( 'id' => 11, 'module' => 'content', 'function' => 'read', 'limitations' => array( 'Section' => array( 1 ) ) ),
                array( 'id' => 12, 'module' => 'content', 'function' => 'create', 'limitations' => array() ),
                array( 'id' => 13, 'module' => 'content', 'function' => 'publish', 'limitations' => array( 'Class' => array( 2 ) ) ),
            ) ),
            array( 'id' => 2, 'name' => 'Administrator', 'policies' => array(
                array( 'id' => 20, 'module' => '*', 'function' => '*', 'limitations' => array() ),
            ) ),
            array( 'id' => 7, 'name' => 'Lawyer', 'policies' => array(
                // a role that may edit held objects says so: left as it is
                array( 'id' => 30, 'module' => 'content', 'function' => 'edit', 'limitations' => array( 'StateGroup_review_hold' => array( 5, 6 ) ) ),
                array( 'id' => 31, 'module' => 'content', 'function' => '*', 'limitations' => array() ),
                array( 'id' => 32, 'module' => 'state', 'function' => 'assign', 'limitations' => array() ),
            ) ),
            array( 'id' => 8, 'name' => 'Empty' ),
        );
    }

    public function testEveryEditAndPublishPolicyIsLimitedOnceAndNothingElse()
    {
        $plan = expReviewHold::policyPlan( self::roles() );
        $this->assertSame( array( array( 3, 'Editor', 10, 'edit' ), array( 3, 'Editor', 13, 'publish' ) ), $plan['limit'] );
    }

    public function testModuleWidePoliciesAreListedNotChanged()
    {
        $plan = expReviewHold::policyPlan( self::roles() );
        $this->assertSame( array( array( 2, 'Administrator', '*/*' ), array( 7, 'Lawyer', 'content/*' ) ), $plan['unlimited'] );
    }

    public function testAPlanOfLimitedRolesIsEmpty()
    {
        $roles = self::roles();
        $roles[0]['policies'][0]['limitations'] = array( 'StateGroup_review_hold' => array( 5 ) );
        $roles[0]['policies'][3]['limitations']['StateGroup_review_hold'] = array( 5 );
        $this->assertSame( array(), expReviewHold::policyPlan( $roles )['limit'] );
    }

    public function testThePublishOperationChecksBeforeAnythingChangesAndAfterTheWorkflows()
    {
        $OperationList = array();
        include __DIR__ . '/../../../../kernel/content/operation_definition.php';
        $names = array_map( function ( $body ) { return $body['name']; }, $OperationList['publish']['body'] );
        $check = array_search( 'check-review-hold', $names, true );
        $after = array_search( 'check-review-hold-after-workflow', $names, true );
        $this->assertSame( array_search( 'begin-transaction', $names, true ) + 1, $check );
        $this->assertLessThan( array_search( 'set-version-pending', $names, true ), $check );
        $this->assertSame( array_search( 'pre_publish', $names, true ) + 1, $after );
        $this->assertLessThan( array_search( 'copy-translations', $names, true ), $after );
        foreach ( array( $check, $after ) as $i )
        {
            $body = $OperationList['publish']['body'][$i];
            $this->assertSame( 'checkReviewHold', $body['method'] );
            $this->assertSame( 'once', $body['frequency'] );
            $this->assertTrue( method_exists( 'eZContentOperationCollection', 'checkReviewHold' ) );
        }
    }

    public function testObjectsWithoutAStateOfTheGroupGetTheFreeStateOnEveryEngine()
    {
        $link = new ReflectionMethod( 'expReviewHold', 'linkUnlinked' );
        if ( PHP_VERSION_ID < 80100 )
            $link->setAccessible( true );
        $entry = array( 'group_id' => 3, 'group_identifier' => 'review_hold', 'state_id' => 6, 'state_identifier' => 'not_held',
                        'missing' => 2, 'missing_ids' => array( 149, 150 ) );

        // SQL engines: one statement, only for objects with no state of this group, so a second run adds nothing
        $sql = new expReviewHoldTestDb( 'postgresql' );
        $link->invoke( null, $sql, $entry );
        $this->assertCount( 1, $sql->queries );
        $query = preg_replace( '/\s+/', ' ', $sql->queries[0] );
        $this->assertStringContainsString( 'INSERT INTO ezcobj_state_link (contentobject_id, contentobject_state_id) SELECT o.id, 6 FROM ezcontentobject o', $query );
        $this->assertStringContainsString( 'WHERE NOT EXISTS', $query );
        $this->assertStringContainsString( 's.group_id = 3', $query );
        $this->assertSame( array( 'begin', 'commit' ), $sql->transaction );

        // MongoDB: one link per object the plan found
        $mongo = new expReviewHoldTestDb( 'mongo' );
        $link->invoke( null, $mongo, $entry );
        $this->assertSame( array( array( 'ezcobj_state_link', array( 'contentobject_id' => 149, 'contentobject_state_id' => 6 ) ),
                                  array( 'ezcobj_state_link', array( 'contentobject_id' => 150, 'contentobject_state_id' => 6 ) ) ), $mongo->inserts );
        $this->assertSame( array(), $mongo->queries );
    }

    public function testTheStepIsListedAndTheRefusalIsInTheAuditCatalogue()
    {
        $step = new expUpgradeStepReviewHold();
        $this->assertSame( '6.0.15-review-hold', $step->id() );
        $this->assertSame( 'scripts', $step->stage() );
        $ini = file_get_contents( __DIR__ . '/../../../../settings/upgrade.ini' );
        $this->assertStringContainsString( "\nStepList[]=expUpgradeStepReviewHold\n", $ini );
        $catalogue = expAuditTaxonomy::catalogue();
        $this->assertArrayHasKey( 'content.object.hold.refused', $catalogue );
        $this->assertSame( 'content', $catalogue['content.object.hold.refused']['channel'] );
    }
}

/** A database that records what it is asked to do. */
class expReviewHoldTestDb
{
    public $queries = array();
    public $inserts = array();
    public $transaction = array();
    private $name;
    private $depth = 0;

    public function __construct( $name )
    {
        $this->name = $name;
    }

    public function databaseName()
    {
        return $this->name;
    }

    public function begin()
    {
        $this->transaction[] = 'begin';
        ++$this->depth;
    }

    public function commit()
    {
        $this->transaction[] = 'commit';
        --$this->depth;
    }

    public function rollback()
    {
        $this->transaction[] = 'rollback';
        $this->depth = 0;
    }

    public function transactionCounter()
    {
        return $this->depth;
    }

    public function query( $sql )
    {
        $this->queries[] = $sql;
        return true;
    }

    public function insert( $table, array $row )
    {
        $this->inserts[] = array( $table, $row );
        return true;
    }
}
