<?php
/**
 * The review hold (expReviewHold): its states and their order, the role's policies, which policies the upgrade step
 * limits and which it leaves, the publish operation's two checks and the step's registration.
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
