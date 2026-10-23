<?php
/**
 * Which objects a section assignment changes (eZSection::SCOPE_*): eZSection::applyTo(), the operation
 * content/updatesection, eZContentObjectTreeNode::assignSectionToSubTree() (section/assign) and the content job
 * 'section'. doc/eng-us/guides/sections.md, section 8.
 *
 *  SC-01 - scopes(), isValidScope() and scopeFromHTTP(): three scopes, subtree when a form sends none, false for an
 *          unknown value
 *  SC-02 - pathIsInAny(): a location is in a branch when its path starts with the branch's path, by whole IDs
 *  SC-03 - the operation keeps its two required parameters in front, the new ones are optional with the old
 *          behaviour as default, in the order of updateSection()'s arguments
 *  SC-04 - applyTo() without a scope changes the whole subtree, as before
 *  SC-05 - SCOPE_OBJECT changes the object only
 *  SC-06 - SCOPE_INHERITED stops at an object of another section, with everything below it
 *  SC-07 - an unknown scope is refused and changes nothing
 *  SC-08 - assignSectionToSubTree() and the operation take the scope; without it they change the whole subtree
 *  SC-09 - the content job: SCOPE_INHERITED leaves the branches in another section out of the work list it builds
 *          when it starts, SCOPE_OBJECT works on one node, an unknown scope is refused when the job is created
 *  SC-10 - sectionAssignableObjectIDs(): a user without limitations may assign every object (none is fetched), one
 *          without section/assign none
 *  SC-11 - with limitations, the objects the user may not assign the section to are left out; assignSectionToSubTree()
 *          checks them only when asked (section/assign and applyTo() ask, a new main location does not)
 *  SC-12 - the audit event content.node.section names the scope and the number of objects that change
 *  SC-13 - applyToOrStartJob() (the edit form, the section menu of the node page): in the request up to
 *          content.ini NowLimit nodes, a content job above it, never for the object alone
 *
 * SC-01 to SC-03 and SC-10 need no database. SC-04 to SC-09 and SC-11 to SC-13 run on the installation the tests run on: each test builds a
 * folder tree below node 2 and three sections of its own and removes all of them in tearDown(), also when the test
 * fails; where there is no installation (CI) they are skipped.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/eZSectionScopeTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZSectionScopeTest extends PHPUnit\Framework\TestCase
{
    const ADMIN_ID = 14;

    private $previousUser;
    private $topNodeID;
    private $sections = array();
    private $nodes = array();

    private static function root()
    {
        return dirname( __DIR__, 4 );
    }

    /** SC-01 */
    public function testTheScopes()
    {
        require_once self::root() . '/kernel/classes/ezsection.php';
        $this->assertSame( array( 'subtree', 'inherited', 'object' ), eZSection::scopes() );
        foreach ( eZSection::scopes() as $scope )
            $this->assertTrue( eZSection::isValidScope( $scope ) );
        foreach ( array( '', 'Subtree', 'all', null, 1, array( 'object' ) ) as $scope )
            $this->assertFalse( eZSection::isValidScope( $scope ), var_export( $scope, true ) );

        $http = $this->createStub( 'eZHTTPTool' );
        $post = array();
        $http->method( 'hasPostVariable' )->willReturnCallback( function ( $name ) use ( &$post ) { return array_key_exists( $name, $post ); } );
        $http->method( 'postVariable' )->willReturnCallback( function ( $name ) use ( &$post ) { return $post[$name]; } );
        $this->assertSame( eZSection::SCOPE_SUBTREE, eZSection::scopeFromHTTP( $http ), 'a form without the choice' );
        $post['SectionScope'] = 'object';
        $this->assertSame( eZSection::SCOPE_OBJECT, eZSection::scopeFromHTTP( $http ) );
        $post['SectionScope'] = 'everything';
        $this->assertFalse( eZSection::scopeFromHTTP( $http ) );
    }

    /** SC-02 */
    public function testPathIsInAny()
    {
        require_once self::root() . '/kernel/classes/ezcontentobjecttreenode.php';
        $branches = array( '/1/2/10/' => true, '/1/2/30/31/' => true );
        $this->assertTrue( eZContentObjectTreeNode::pathIsInAny( '/1/2/10/', $branches ), 'the branch itself' );
        $this->assertTrue( eZContentObjectTreeNode::pathIsInAny( '/1/2/10/11/12/', $branches ), 'below it' );
        $this->assertFalse( eZContentObjectTreeNode::pathIsInAny( '/1/2/100/', $branches ), 'a node ID that starts like it' );
        $this->assertFalse( eZContentObjectTreeNode::pathIsInAny( '/1/2/30/', $branches ), 'above a branch' );
        $this->assertFalse( eZContentObjectTreeNode::pathIsInAny( '/1/2/10/', array() ) );
    }

    /** SC-03 */
    public function testTheOperationParameters()
    {
        $OperationList = array();
        include self::root() . '/kernel/content/operation_definition.php';
        $parameters = $OperationList['updatesection']['parameters'];
        $this->assertSame( array( 'node_id', 'selected_section_id', 'update_search_indexes', 'scope', 'old_section_id', 'check_access' ),
                           array_column( $parameters, 'name' ) );
        $this->assertTrue( $parameters[0]['required'] && $parameters[1]['required'] );
        $this->assertSame( array( false, true ), array( $parameters[2]['required'], $parameters[2]['default'] ) );
        $this->assertSame( array( false, 'subtree' ), array( $parameters[3]['required'], $parameters[3]['default'] ) );
        $this->assertSame( array( false, false ), array( $parameters[4]['required'], $parameters[4]['default'] ) );
        $this->assertSame( array( false, false ), array( $parameters[5]['required'], $parameters[5]['default'] ), 'no check unless asked' );
        $this->assertSame( array( 'node_id', 'selected_section_id' ), $OperationList['updatesection']['keys'] );

        $method = new ReflectionMethod( 'eZContentOperationCollection', 'updateSection' );
        $this->assertSame( array( 'nodeID', 'selectedSectionID', 'updateSearchIndexes', 'scope', 'oldSectionID', 'checkAccess' ),
                           array_map( function ( $p ) { return $p->getName(); }, $method->getParameters() ) );
        $method = new ReflectionMethod( 'eZContentObjectTreeNode', 'assignSectionToSubTree' );
        $this->assertSame( array( 'nodeID', 'sectionID', 'oldSectionID', 'updateSearchIndexes', 'scope', 'checkAccess' ),
                           array_map( function ( $p ) { return $p->getName(); }, $method->getParameters() ) );
        $this->assertFalse( $method->getParameters()[5]->getDefaultValue(), 'the kernel\'s own calls (a new main location) are not checked' );
    }

    /** SC-10 */
    public function testAUserWithoutLimitationsMayAssignEveryObjectAndOneWithoutAccessNone()
    {
        $user = $this->createStub( 'eZUser' );
        $user->method( 'hasAccessTo' )->willReturn( array( 'accessWord' => 'yes' ) );
        $this->assertSame( array( 3, 5, 8 ), eZContentObjectTreeNode::sectionAssignableObjectIDs( array( '3', 5, 8 ), 1, $user ) );

        $user = $this->createStub( 'eZUser' );
        $user->method( 'hasAccessTo' )->willReturn( array( 'accessWord' => 'no' ) );
        $this->assertSame( array(), eZContentObjectTreeNode::sectionAssignableObjectIDs( array( 3, 5, 8 ), 1, $user ) );
    }

    /** SC-11 */
    public function testLimitationsLeaveObjectsOut()
    {
        $this->tree();
        $y = $this->id( 'Y' );
        $c1 = (int)$this->object( 'c1' )->attribute( 'id' );
        $user = $this->createStub( 'eZUser' );
        $user->method( 'hasAccessTo' )->willReturn( array( 'accessWord' => 'limited', 'policies' => array() ) );
        $user->method( 'canAssignSectionToObject' )->willReturnCallback( function ( $sectionID, $object ) use ( $c1, $y ) {
            return (int)$sectionID === $y && (int)$object->attribute( 'id' ) !== $c1;
        } );
        $ids = eZContentObjectTreeNode::sectionAssignmentObjectIDs( eZContentObjectTreeNode::fetch( $this->nodes['top'] ) );
        $this->assertCount( 6, $ids );
        $allowed = eZContentObjectTreeNode::sectionAssignableObjectIDs( $ids, $y, $user );
        $this->assertCount( 5, $allowed );
        $this->assertNotContains( $c1, $allowed );

        // the administrator has no limitation: with the check, assignSectionToSubTree() still changes them all
        eZContentObjectTreeNode::assignSectionToSubTree( $this->nodes['top'], $y, false, true, eZSection::SCOPE_SUBTREE, true );
        $this->assertSame( array_fill_keys( array_keys( $this->nodes ), $y ), $this->sections() );
    }

    /** SC-12 */
    public function testTheAuditEventNamesTheScope()
    {
        $this->tree();
        if ( !class_exists( 'expAuditHook' ) || !class_exists( 'expAudit' ) )
            $this->markTestSkipped( 'no audit' );
        require_once self::root() . '/tests/tests/kernel/classes/audit/fixtures/expaudittestfixtures.php';
        $dir = expAuditTestFixtures::setUp( 'section-scope', array( 'AuditEventSettings/Enabled' => array( 'content.*' ) ) );
        try
        {
            $this->sections['Y']->applyTo( $this->object( 'top' ), eZSection::SCOPE_INHERITED );
            expAudit::flush();
            $events = array_values( array_filter( expAuditTestFixtures::records( $dir, 'content' ), function ( $r ) {
                return $r['name'] === 'content.node.section';
            } ) );
        }
        finally
        {
            expAuditHook::reset();
            expAuditTestFixtures::tearDown();
        }
        $this->assertCount( 1, $events, 'one event for the one location' );
        $this->assertSame( eZSection::SCOPE_INHERITED, $events[0]['data']['after']['scope'] ?? $events[0]['after']['scope'] ?? null );
        $this->assertSame( 4, $events[0]['data']['after']['objects'] ?? $events[0]['after']['objects'] ?? null, 'b and b1 keep their sections' );
    }

    /** SC-13 */
    public function testALargeSubtreeFromTheEditFormBecomesAContentJob()
    {
        $s0 = $this->tree();
        $y = $this->id( 'Y' );
        if ( !class_exists( 'expContentJob' ) || !class_exists( 'Exponential\\View\\Kernel\\Content\\Job' ) )
            $this->markTestSkipped( 'no content jobs' );

        // within NowLimit (1000 nodes): in the request, as applyTo()
        $this->assertNotInstanceOf( 'expContentJob', $this->sections['Y']->applyToOrStartJob( $this->object( 'top' ), eZSection::SCOPE_INHERITED ) );
        $this->assertSame( array( 'top' => $y, 'a' => $y, 'b' => $this->id( 'X' ), 'b1' => $s0, 'c' => $y, 'c1' => $y ), $this->sections() );

        // the object alone is never a job
        ezpINIHelper::setINISetting( 'content.ini', 'ContentJobSettings', 'NowLimit', '2' );
        try
        {
            $this->assertNotInstanceOf( 'expContentJob', $this->sections['Z']->applyToOrStartJob( $this->object( 'top' ), eZSection::SCOPE_OBJECT ) );
            $this->assertSame( array( $this->id( 'Z' ), $y ), array( $this->section( 'top' ), $this->section( 'a' ) ) );

            // above it: a content job
            $job = $this->sections['Y']->applyToOrStartJob( $this->object( 'top' ), eZSection::SCOPE_SUBTREE );
            $this->assertInstanceOf( 'expContentJob', $job );
            $this->assertSame( array( 'node_id' => $this->nodes['top'], 'section_id' => $y, 'scope' => eZSection::SCOPE_SUBTREE ),
                               array_intersect_key( $job->params(), array_flip( array( 'node_id', 'section_id', 'scope' ) ) ) );
            $this->waitForJob( $job );
            $this->assertSame( array_fill_keys( array_keys( $this->nodes ), $y ), $this->sections() );
        }
        finally
        {
            ezpINIHelper::restoreINISettings();
        }
    }

    /** The worker the job started, or this process when none started within a few seconds */
    private function waitForJob( expContentJob $job )
    {
        for ( $i = 0; $i < 300; $i++ )
        {
            $now = expContentJob::fetch( $job->id() );
            if ( !$now->isActive() )
                break;
            if ( $i === 50 && $now->state() === expContentJob::STATE_QUEUED && !$now->workerAlive() )
            {
                ( new expContentJobWorker() )->run( $job->id() );
                break;
            }
            usleep( 100000 );
        }
        $done = expContentJob::fetch( $job->id() );
        $this->assertSame( expContentJob::STATE_DONE, $done->state(), 'the job finished' );
        eZContentObject::clearCache();
        return $done;
    }

    // ---- on the installation -----------------------------------------------------------------------------------

    /**
     * The tree: top (S0) / a (S0), b (X) / b1 (S0), c (S0) / c1 (S0), and the sections X, Y, Z.
     */
    private function tree()
    {
        ezpLiveInstallation::requireOrSkip();
        chdir( self::root() );
        $this->previousUser = eZUser::currentUser();
        eZUser::setCurrentlyLoggedInUser( eZUser::fetch( self::ADMIN_ID ), self::ADMIN_ID, eZUser::NO_SESSION_REGENERATE );
        foreach ( array( 'X', 'Y', 'Z' ) as $name )
        {
            $section = new eZSection( array( 'name' => "Scope test $name", 'identifier' => 'scopetest_' . strtolower( $name ) . substr( uniqid(), -6 ),
                                             'navigation_part_identifier' => 'ezcontentnavigationpart', 'locale' => '' ) );
            $section->store();
            $this->sections[$name] = $section;
        }
        $this->topNodeID = $this->nodes['top'] = $this->folder( 'Section scope test', 2 );
        $this->nodes['a'] = $this->folder( 'a', $this->nodes['top'] );
        $this->nodes['b'] = $this->folder( 'b', $this->nodes['top'] );
        $this->nodes['b1'] = $this->folder( 'b1', $this->nodes['b'] );
        $this->nodes['c'] = $this->folder( 'c', $this->nodes['top'] );
        $this->nodes['c1'] = $this->folder( 'c1', $this->nodes['c'] );
        $this->setSection( 'b', $this->id( 'X' ) );
        return $this->section( 'top' );
    }

    protected function tearDown(): void
    {
        if ( $this->topNodeID && eZContentObjectTreeNode::fetch( $this->topNodeID ) )
            eZContentObjectTreeNode::removeSubtrees( array( $this->topNodeID ), false );
        eZContentObject::clearCache();
        foreach ( $this->sections as $section )
        {
            if ( $section->canBeRemoved() )
                $section->removeThis();
        }
        if ( $this->previousUser instanceof eZUser )
            eZUser::setCurrentlyLoggedInUser( $this->previousUser, $this->previousUser->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
    }

    private function folder( $name, $parentNodeID )
    {
        $object = eZContentFunctions::createAndPublishObject( array( 'class_identifier' => 'folder', 'parent_node_id' => $parentNodeID,
                                                                     'attributes' => array( 'name' => $name ) ) );
        $this->assertInstanceOf( 'eZContentObject', $object, "folder $name" );
        return (int)$object->attribute( 'main_node_id' );
    }

    private function id( $name )
    {
        return (int)$this->sections[$name]->attribute( 'id' );
    }

    private function object( $key )
    {
        eZContentObject::clearCache();
        return eZContentObjectTreeNode::fetch( $this->nodes[$key] )->object();
    }

    private function section( $key )
    {
        return (int)$this->object( $key )->attribute( 'section_id' );
    }

    private function setSection( $key, $sectionID )
    {
        eZDB::instance()->query( 'UPDATE ezcontentobject SET section_id=' . (int)$sectionID . ' WHERE id=' . (int)$this->object( $key )->attribute( 'id' ) );
        eZContentObject::clearCache();
    }

    private function sections()
    {
        $sections = array();
        foreach ( array_keys( $this->nodes ) as $key )
            $sections[$key] = $this->section( $key );
        return $sections;
    }

    /** SC-04 */
    public function testWithoutAScopeTheWholeSubtreeChanges()
    {
        $this->tree();
        $this->sections['Y']->applyTo( $this->object( 'top' ) );
        $this->assertSame( array_fill_keys( array_keys( $this->nodes ), $this->id( 'Y' ) ), $this->sections() );
    }

    /** SC-05 */
    public function testObjectChangesTheObjectOnly()
    {
        $s0 = $this->tree();
        $this->sections['Y']->applyTo( $this->object( 'top' ), eZSection::SCOPE_OBJECT );
        $this->assertSame( array( 'top' => $this->id( 'Y' ), 'a' => $s0, 'b' => $this->id( 'X' ), 'b1' => $s0, 'c' => $s0, 'c1' => $s0 ),
                           $this->sections() );
    }

    /** SC-06 */
    public function testInheritedStopsAtAnotherSection()
    {
        $s0 = $this->tree();
        $y = $this->id( 'Y' );
        $this->sections['Y']->applyTo( $this->object( 'top' ), eZSection::SCOPE_INHERITED );
        $this->assertSame( array( 'top' => $y, 'a' => $y, 'b' => $this->id( 'X' ), 'b1' => $s0, 'c' => $y, 'c1' => $y ), $this->sections() );
    }

    /** SC-07 */
    public function testAnUnknownScopeIsRefused()
    {
        $s0 = $this->tree();
        $this->assertFalse( $this->sections['Y']->applyTo( $this->object( 'top' ), 'everything' ) );
        $this->assertSame( $s0, $this->section( 'top' ) );
    }

    /** SC-08 */
    public function testTheTreeFunctionAndTheOperationTakeTheScope()
    {
        $s0 = $this->tree();
        $y = $this->id( 'Y' );
        $z = $this->id( 'Z' );
        $this->setSection( 'c', $z );
        eZContentObjectTreeNode::assignSectionToSubTree( $this->nodes['top'], $y, false, true, eZSection::SCOPE_INHERITED );
        $this->assertSame( array( 'top' => $y, 'a' => $y, 'b' => $this->id( 'X' ), 'b1' => $s0, 'c' => $z, 'c1' => $s0 ), $this->sections() );

        eZOperationHandler::execute( 'content', 'updatesection', array( 'node_id' => $this->nodes['c'], 'selected_section_id' => $y,
                                                                         'scope' => eZSection::SCOPE_OBJECT ), null, true );
        $this->assertSame( array( $y, $s0 ), array( $this->section( 'c' ), $this->section( 'c1' ) ) );

        // the call of every existing caller: node and section only
        eZOperationHandler::execute( 'content', 'updatesection', array( 'node_id' => $this->nodes['top'], 'selected_section_id' => $z ), null, true );
        $this->assertSame( array_fill_keys( array_keys( $this->nodes ), $z ), $this->sections() );
    }

    /** SC-09 */
    public function testTheContentJob()
    {
        $s0 = $this->tree();
        $y = $this->id( 'Y' );
        if ( !class_exists( 'expContentJob' ) )
            $this->markTestSkipped( 'no content jobs' );
        $admin = eZUser::fetch( self::ADMIN_ID );

        $job = expContentJob::create( 'section', array( 'node_id' => $this->nodes['top'], 'section_id' => $y, 'scope' => eZSection::SCOPE_INHERITED ), $admin );
        $this->assertSame( $s0, $job->params()['old_section_id'] );
        $this->assertSame( 4, $this->runJob( $job )->progress()['total'], 'b and b1 are not in the work list' );
        $this->assertSame( array( 'top' => $y, 'a' => $y, 'b' => $this->id( 'X' ), 'b1' => $s0, 'c' => $y, 'c1' => $y ), $this->sections() );

        $job = expContentJob::create( 'section', array( 'node_id' => $this->nodes['top'], 'section_id' => $this->id( 'Z' ), 'scope' => eZSection::SCOPE_OBJECT ), $admin );
        $this->assertSame( 1, $job->progress()['total'] );
        $this->assertSame( 1, $this->runJob( $job )->progress()['total'] );
        $this->assertSame( array( $this->id( 'Z' ), $y ), array( $this->section( 'top' ), $this->section( 'a' ) ) );

        try
        {
            expContentJob::create( 'section', array( 'node_id' => $this->nodes['top'], 'section_id' => $y, 'scope' => 'everything' ), $admin );
            $this->fail( 'an unknown scope is refused' );
        }
        catch ( expContentJobException $e )
        {
            $this->assertStringContainsString( 'everything', $e->getMessage() );
        }
    }

    private function runJob( expContentJob $job )
    {
        $worker = new expContentJobWorker();
        $worker->run( $job->id() );
        $done = expContentJob::fetch( $job->id() );
        $this->assertSame( expContentJob::STATE_DONE, $done->state(), 'the job finished' );
        return $done;
    }
}
