<?php
/**
 * The workflow process list (workflow/processlist) against the database of the installation the tests run on.
 *
 *  WL-01 - statusCounts() agrees with counting the processes of each status, and its oldest waiting time with theirs (read-only)
 *  WL-02 - The page groups of the waiting list hold every waiting process listed, none twice (read-only)
 *  WL-03 - Cancelling removes the process, its mementos and its approval link, and only those
 *  WL-04 - A workflow counts its own events (eZWorkflow::fetchEventCount() read an undefined id and counted none) (read-only)
 *
 * WL-03 works on a throwaway process it creates itself: status Failed and a workflow id no workflow has, so the
 * workflow cronjob never picks it up, and no content object (object 0), so no version is touched. Whatever the
 * test leaves behind is removed in tearDown(). No real process is cancelled, resumed or changed. Where there is no
 * installation (CI) the tests are skipped.
 * Run on alpha through ai/bin/one/run_alpha_db_heavy_command_one_at_a_time.sh.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Workflow\Processlist;

class eZWorkflowProcessListLiveTest extends PHPUnit\Framework\TestCase
{
    private static $installation;

    /** @var string memento key of the throwaway process */
    private $key;

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 4 );
        ezpLiveInstallation::requireOrSkip();
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
    }

    protected function tearDown(): void
    {
        if ( $this->key === null )
            return;
        $db = eZDB::instance();
        $key = $db->escapeString( $this->key );
        foreach ( (array)$db->arrayQuery( "SELECT id FROM ezworkflow_process WHERE memento_key = '$key'" ) as $row )
            $db->query( 'DELETE FROM ezapprove_items WHERE workflow_process_id = ' . (int)$row['id'] );
        $db->query( "DELETE FROM ezworkflow_process WHERE memento_key = '$key'" );
        $db->query( "DELETE FROM ezoperation_memento WHERE memento_key = '$key' OR main_key = '$key'" );
    }

    /** WL-01 */
    public function testStatusCountsAgreeWithTheProcesses()
    {
        $counts = Processlist::statusCounts();
        $filters = Processlist::statusFilters();
        $oldest = false;
        foreach ( range( 0, 10 ) as $status )
        {
            $processes = eZWorkflowProcess::fetchList( array( 'status' => $status ), true );
            $n = is_array( $processes ) ? count( $processes ) : 0;
            $this->assertSame( $n, isset( $counts['by_status'][$status] ) ? $counts['by_status'][$status] : 0, "status $status" );
            if ( in_array( $status, $filters['waiting'], true ) )
                foreach ( (array)$processes as $p )
                    if ( $oldest === false || (int)$p->attribute( 'created' ) < $oldest )
                        $oldest = (int)$p->attribute( 'created' );
        }
        $this->assertSame( $oldest, $counts['oldest_waiting'] );
    }

    /** WL-02 */
    public function testPageGroupsHoldEveryListedProcess()
    {
        $list = eZWorkflowProcess::fetchList( Processlist::statusConditions( 'waiting', eZDB::instance()->databaseName() ), true, 0, 100 );
        $list = is_array( $list ) ? $list : array();
        list( $byTrigger ) = Processlist::processesByTrigger( $list );
        $groups = Processlist::pageGroups( $byTrigger, $list, time() );
        $ids = array();
        foreach ( $groups as $group )
            foreach ( $group['processes'] as $view )
                $ids[] = $view['id'];
        $expected = array();
        foreach ( $list as $p )
            $expected[] = (int)$p->attribute( 'id' );
        sort( $ids );
        sort( $expected );
        $this->assertSame( $expected, $ids );
    }

    /** WL-03 */
    public function testCancelRemovesTheProcessAndWhatBelongsToIt()
    {
        $db = eZDB::instance();
        $before = Processlist::statusCounts();
        $this->key = 'exptest' . md5( uniqid( __METHOD__, true ) );

        $process = eZWorkflowProcess::create( 'exptest-process', array( 'workflow_id' => 2147483000, 'user_id' => 0 ) );
        $process->setAttribute( 'memento_key', $this->key );
        $process->setAttribute( 'status', eZWorkflow::STATUS_FAILED );
        $process->store();
        $id = (int)$process->attribute( 'id' );
        $this->assertGreaterThan( 0, $id );

        $main = eZOperationMemento::create( $this->key, array( 'module_name' => 'exptest' ), true );
        $main->store();
        $child = eZOperationMemento::create( $this->key, array( 'module_name' => 'exptest', 'operation_name' => 'none', 'name' => 'pre_none' ), false, $this->key );
        $child->store();
        // an approval link to a collaboration item that does not exist: the link goes, nothing else is touched
        $db->query( "INSERT INTO ezapprove_items ( workflow_process_id, collaboration_id ) VALUES ( $id, 0 )" );
        $this->assertSame( array( $id => 0 ), Processlist::approvalItems( array( $id ) ) );

        $result = Processlist::cancelProcess( eZWorkflowProcess::fetch( $id ) );

        $this->assertTrue( $result['ok'], $result['message'] );
        $this->assertFalse( $result['version_to_draft'] );
        $this->assertNull( eZWorkflowProcess::fetch( $id ) );
        $this->assertSame( array(), Processlist::approvalItems( array( $id ) ) );
        $rows = $db->arrayQuery( "SELECT COUNT(*) AS n FROM ezoperation_memento WHERE memento_key = '" . $db->escapeString( $this->key ) . "'" );
        $this->assertSame( 0, (int)$rows[0]['n'] );
        $this->assertEquals( $before, Processlist::statusCounts(), 'every other process is where it was' );

        $feedback = Processlist::cancelProcesses( array( $id ) );
        $this->assertFalse( $feedback[0]['ok'], 'a process that is gone is reported, not an error' );
        $this->assertFalse( Processlist::cancelProcesses( array() )[0]['ok'] );
    }

    /** WL-04 */
    public function testWorkflowEventCountIsItsOwn()
    {
        $workflows = eZWorkflow::fetchList( 0, true );
        if ( !$workflows )
            $this->markTestSkipped( 'The installation has no workflows.' );
        foreach ( $workflows as $workflow )
        {
            $events = $workflow->fetchEvents();
            $this->assertSame( is_array( $events ) ? count( $events ) : 0, (int)$workflow->attribute( 'event_count' ), $workflow->attribute( 'name' ) );
        }
    }
}
