<?php
/**
 * The read permission and the languages of a subtree fetch on MongoDB (eZContentObjectTreeNode::mongoPermissionStages(),
 * mongoLanguageStages(), mongoNameLookupPipeline()). The MongoDB path of subTreeByNodeID() and subTreeCountByNodeID()
 * checked neither: a fetch listed every node of the subtree, restricted sections included, to anybody, in every
 * language. The stages give the conditions createPermissionCheckingSQL() and eZContentLanguage give the SQL engines.
 *
 * Database: none (the stages are compared, not run).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class eZContentObjectTreeNodeMongoPermissionTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        // the policies as given, without the merging and pruning of the optimization (tested on the SQL side)
        ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'PermissionSQLOptimization', 'disabled' );
    }

    protected function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
    }

    public function testNoAccessMatchesNothingAndFullAccessAddsNothing()
    {
        $this->assertSame( array( array( '$match' => array( '$expr' => false ) ) ), eZContentObjectTreeNode::mongoPermissionStages( false ) );
        $this->assertSame( array(), eZContentObjectTreeNode::mongoPermissionStages( array() ) );
    }

    public function testOnePolicyAndsItsLimitationsAndOrsItsPlacements()
    {
        $stages = eZContentObjectTreeNode::mongoPermissionStages( array(
            array( 'Class' => array( '1', '16' ), 'Section' => array( 1 ), 'Subtree' => array( '/1/2/', '/1/43/' ), 'Node' => array( 60 ) ),
        ) );
        $this->assertSame( array( array( '$match' => array( '$and' => array(
            array( 'contentclass_id' => array( '$in' => array( 1, 16 ) ) ),
            array( 'section_id' => array( '$in' => array( 1 ) ) ),
            array( '$or' => array(
                array( 'path_string' => array( '$regex' => '^/1/2/' ) ),
                array( 'path_string' => array( '$regex' => '^/1/43/' ) ),
                array( 'node_id' => array( '$in' => array( 60 ) ) ),
            ) ),
        ) ) ) ), $stages );
    }

    public function testPoliciesAreOred()
    {
        $stages = eZContentObjectTreeNode::mongoPermissionStages( array(
            array( 'Section' => array( 1 ) ),
            array( 'Class' => array( 4 ), 'User_Section' => array( 6 ) ),
            array( 'User_Subtree' => array( '/1/5/', '/1/12/' ) ),
        ) );
        $this->assertSame( array( '$or' => array(
            array( 'section_id' => array( '$in' => array( 1 ) ) ),
            array( '$and' => array( array( 'contentclass_id' => array( '$in' => array( 4 ) ) ),
                                    array( 'section_id' => array( '$in' => array( 6 ) ) ) ) ),
            array( '$or' => array( array( 'path_string' => array( '$regex' => '^/1/5/' ) ),
                                   array( 'path_string' => array( '$regex' => '^/1/12/' ) ) ) ),
        ) ), $stages[0]['$match'] );
        $this->assertCount( 1, $stages );
    }

    public function testStateLimitationsJoinTheStateLinksOnce()
    {
        $stages = eZContentObjectTreeNode::mongoPermissionStages( array(
            array( 'StateGroup_ez_lock' => array( 1 ), 'StateGroup_review' => array( '7', '8' ) ),
        ) );
        $this->assertCount( 3, $stages );
        $this->assertSame( array( 'from' => 'ezcobj_state_link', 'localField' => 'id', 'foreignField' => 'contentobject_id', 'as' => '_perm_states' ),
                           $stages[0]['$lookup'] );
        $this->assertSame( array( '$and' => array(
            array( '_perm_states.contentobject_state_id' => array( '$in' => array( 1 ) ) ),
            array( '_perm_states.contentobject_state_id' => array( '$in' => array( 7, 8 ) ) ),
        ) ), $stages[1]['$match'] );
        $this->assertSame( array( '_perm_states' => 0 ), $stages[2]['$project'] );
    }

    public function testAnExtensionLimitationGivesNoAccessAndAReadIgnoredLimitationNoCondition()
    {
        $stages = eZContentObjectTreeNode::mongoPermissionStages( array(
            array( 'Section' => array( 1 ), 'MyExtensionLimitation' => array( 'x' ) ),
            array( 'Language' => array( 'eng-US' ) ),
        ) );
        $this->assertSame( array( '$and' => array( array( 'section_id' => array( '$in' => array( 1 ) ) ), array( '$expr' => false ) ) ),
                           $stages[0]['$match'] );
    }

    public function testThePathIsMatchedLiterally()
    {
        $stages = eZContentObjectTreeNode::mongoPermissionStages( array( array( 'Subtree' => array( '/1/2.*/' ) ) ) );
        $this->assertSame( array( 'path_string' => array( '$regex' => '^/1/2\.\*/' ) ), $stages[0]['$match'] );
    }

    public function testTheLanguageFilterIsTheMaskOfThePrioritizedLanguages()
    {
        $this->assertSame( array( array( '$match' => array( 'language_mask' => array( '$bitsAnySet' => 7 ) ) ) ),
                           eZContentObjectTreeNode::mongoLanguageStages( array( 'mask' => 7, 'ids' => array( 2, 4 ) ) ) );
    }

    public function testTheNameIsTheOneOfTheMostPrioritizedLanguage()
    {
        $pipeline = eZContentObjectTreeNode::mongoNameLookupPipeline( array( 'mask' => 7, 'ids' => array( 4, 2 ) ) );
        $rank = $pipeline[1]['$addFields']['_rank']['$switch'];
        $this->assertSame( array( '$in' => array( '$language_id', array( 4, 5 ) ) ), $rank['branches'][0]['case'] );
        $this->assertSame( 0, $rank['branches'][0]['then'] );
        $this->assertSame( array( '$in' => array( '$language_id', array( 2, 3 ) ) ), $rank['branches'][1]['case'] );
        $this->assertSame( 1, $rank['branches'][1]['then'] );
        $this->assertSame( array( '_rank' => array( '$lt' => 1000 ) ), $pipeline[2]['$match'], 'no name in a prioritized or always available language: none' );
        $this->assertSame( array( '_rank' => 1 ), $pipeline[3]['$sort'] );
        $this->assertSame( 1, $pipeline[4]['$limit'] );
    }
}
