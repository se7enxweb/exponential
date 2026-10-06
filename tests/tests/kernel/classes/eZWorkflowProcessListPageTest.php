<?php
/**
 * What the workflow process list page (workflow/processlist) works out before it shows anything:
 * \Exponential\View\Kernel\Workflow\Processlist. No database: processes and triggers are stand-ins and every
 * approval is given, so nothing is looked up.
 *
 *  WP-01 - The status filter is waiting, stopped or all; anything else is waiting
 *  WP-02 - The conditions: waiting keeps the memento condition the page always had (Oracle: LENGTH), the others list every process of their statuses
 *  WP-03 - Every process status has words, a group and a tone; waiting and stopped do not overlap and cover every status
 *  WP-04 - The summary counts waiting by what they wait for, failed and stopped, and the oldest waiting one
 *  WP-05 - Process ids from a form: whole positive numbers, once each
 *  WP-06 - Ages in words
 *  WP-07 - Triggers in words, known and unknown, before and after
 *  WP-08 - The workflow cronjob is found in the part the crontab runs, with its schedule and next run
 *  WP-09 - The page groups keep the trigger order, and a process without a trigger comes last instead of being dropped
 *  WP-10 - A process in words: content and user from its parameters, approval, stuck, search text
 *  WP-11 - Version statuses in words
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Workflow\Processlist;

/** A process or a trigger: only its attributes */
class X2WorkflowPageStandIn
{
    private $attributes;

    public function __construct( $attributes )
    {
        $this->attributes = $attributes;
    }

    public function attribute( $name )
    {
        return isset( $this->attributes[$name] ) ? $this->attributes[$name] : null;
    }
}

class eZWorkflowProcessListPageTest extends PHPUnit\Framework\TestCase
{
    private function process( $id, array $attributes = array() )
    {
        return new X2WorkflowPageStandIn( array_merge( array( 'id' => $id, 'status' => eZWorkflow::STATUS_DEFERRED_TO_CRON,
                                                              'created' => 1000, 'modified' => 1000, 'workflow_id' => 7 ),
                                                       $attributes ) );
    }

    private function trigger( $function, $name, $module = 'content' )
    {
        return new X2WorkflowPageStandIn( array( 'module_name' => $module, 'function_name' => $function, 'name' => $name ) );
    }

    /** WP-01 */
    public function testStatusFilter()
    {
        $this->assertSame( 'waiting', Processlist::statusFilter( null ) );
        $this->assertSame( 'waiting', Processlist::statusFilter( '' ) );
        $this->assertSame( 'waiting', Processlist::statusFilter( 'nonsense' ) );
        $this->assertSame( 'waiting', Processlist::statusFilter( array( 'all' ) ) );
        $this->assertSame( 'stopped', Processlist::statusFilter( 'stopped' ) );
        $this->assertSame( 'all', Processlist::statusFilter( ' ALL ' ) );
    }

    /** WP-02 */
    public function testStatusConditions()
    {
        $waiting = Processlist::statusConditions( 'waiting', 'sqlite' );
        $this->assertSame( array( '!=', '' ), $waiting['memento_key'] );
        $this->assertSame( array( array( 4, 6, 7, 9, 10 ) ), $waiting['status'] );

        $oracle = Processlist::statusConditions( 'waiting', 'oracle' );
        $this->assertSame( array( '!=', 0 ), $oracle['LENGTH(memento_key)'] );
        $this->assertArrayNotHasKey( 'memento_key', $oracle );

        $stopped = Processlist::statusConditions( 'stopped', 'mysql' );
        $this->assertSame( array( array( 0, 1, 2, 3, 5, 8 ) ), $stopped['status'] );
        $this->assertArrayNotHasKey( 'memento_key', $stopped );

        $this->assertSame( array(), Processlist::statusConditions( 'all', 'mysql' ) );
        $this->assertSame( $waiting, Processlist::statusConditions( 'unknown', 'sqlite' ) );
    }

    /** WP-03 */
    public function testEveryStatusHasWords()
    {
        $filters = Processlist::statusFilters();
        $this->assertSame( array(), array_intersect( $filters['waiting'], $filters['stopped'] ) );
        $every = array_merge( $filters['waiting'], $filters['stopped'] );
        sort( $every );
        $this->assertSame( range( 0, 10 ), $every );

        foreach ( range( 0, 10 ) as $status )
        {
            $info = Processlist::statusInfo( $status );
            $this->assertSame( $status, $info['code'] );
            $this->assertNotSame( '', $info['label'] );
            $this->assertNotSame( '', $info['explanation'] );
            $this->assertContains( $info['tone'], array( 'info', 'warn', 'bad', 'ok', 'muted' ) );
            $this->assertSame( in_array( $status, $filters['waiting'], true ), in_array( $info['key'], array( 'cron', 'person', 'parent' ), true ), "status $status" );
        }
        $this->assertSame( 'cron', Processlist::statusInfo( eZWorkflow::STATUS_DEFERRED_TO_CRON )['key'] );
        $this->assertSame( 'failed', Processlist::statusInfo( eZWorkflow::STATUS_FAILED )['key'] );
        $this->assertSame( 'bad', Processlist::statusInfo( eZWorkflow::STATUS_FAILED )['tone'] );
        $this->assertSame( 'ended', Processlist::statusInfo( 99 )['key'] );
        $this->assertSame( '', Processlist::statusInfo( 99 )['name'] );
    }

    /** WP-04 */
    public function testSummary()
    {
        $now = 100000;
        $summary = Processlist::summary( array( 4 => 3, 6 => 1, 9 => 2, 3 => 2, 5 => 1 ), $now - 2 * 86400, $now );
        $this->assertSame( 6, $summary['waiting'] );
        $this->assertSame( 3, $summary['cron'] );
        $this->assertSame( 1, $summary['person'] );
        $this->assertSame( 2, $summary['parent'] );
        $this->assertSame( 2, $summary['failed'] );
        $this->assertSame( 3, $summary['stopped'] );
        $this->assertSame( 9, $summary['total'] );
        $this->assertSame( $now - 2 * 86400, $summary['oldest'] );
        $this->assertSame( '2 days', $summary['oldest_age'] );
        $this->assertTrue( $summary['stuck'] );

        $none = Processlist::summary( array( 3 => 1 ), false, $now );
        $this->assertSame( 0, $none['waiting'] );
        $this->assertSame( 0, $none['oldest'] );
        $this->assertSame( '', $none['oldest_age'] );
        $this->assertFalse( $none['stuck'] );

        $this->assertFalse( Processlist::summary( array( 4 => 1 ), $now - 600, $now )['stuck'] );
    }

    /** WP-05 */
    public function testProcessIDList()
    {
        $this->assertSame( array( 3 ), Processlist::processIDList( '3' ) );
        $this->assertSame( array( 3, 5 ), Processlist::processIDList( array( '3', '5', '3', 5 ) ) );
        $this->assertSame( array(), Processlist::processIDList( array( '0', '-1', 'x', '2; DROP', '1.5', null, array( 4 ) ) ) );
        $this->assertSame( array(), Processlist::processIDList( null ) );
    }

    /** WP-06 */
    public function testAgeText()
    {
        $this->assertSame( 'less than a minute', Processlist::ageText( 0 ) );
        $this->assertSame( 'less than a minute', Processlist::ageText( -5 ) );
        $this->assertSame( '1 minute', Processlist::ageText( 60 ) );
        $this->assertSame( '59 minutes', Processlist::ageText( 3599 ) );
        $this->assertSame( '1 hour', Processlist::ageText( 3600 ) );
        $this->assertSame( '23 hours', Processlist::ageText( 86399 ) );
        $this->assertSame( '1 day', Processlist::ageText( 86400 ) );
        $this->assertSame( '40 days', Processlist::ageText( 40 * 86400 ) );
    }

    /** WP-07 */
    public function testTriggerLabel()
    {
        $this->assertSame( 'Before publishing content', Processlist::triggerLabel( 'content', 'publish', 'pre_publish' ) );
        $this->assertSame( 'After publishing content', Processlist::triggerLabel( 'content', 'publish', 'post_publish' ) );
        $this->assertSame( 'Before an order is confirmed', Processlist::triggerLabel( 'shop', 'confirmorder', 'pre_confirmorder' ) );
        $this->assertSame( 'Before custom/thing', Processlist::triggerLabel( 'custom', 'thing', 'pre_thing' ) );
        $this->assertSame( 'After custom/thing', Processlist::triggerLabel( 'custom', 'thing', 'post_thing' ) );
    }

    /** WP-08 */
    public function testWorkflowCronjob()
    {
        $parts = array( array( 'name' => '-', 'label' => 'Default', 'scripts' => array( array( 'name' => 'workflow.php' ) ) ),
                        array( 'name' => 'frequent', 'label' => 'Frequent', 'scripts' => array( array( 'name' => 'notification.php' ), array( 'name' => 'workflow.php' ) ) ),
                        array( 'name' => 'infrequent', 'label' => 'Infrequent', 'scripts' => array( array( 'name' => 'linkcheck.php' ) ) ) );

        $unscheduled = Processlist::workflowCronjob( $parts, array(), 0 );
        $this->assertTrue( $unscheduled['found'] );
        $this->assertSame( '-', $unscheduled['part'], 'the first part when the crontab runs none' );
        $this->assertFalse( $unscheduled['scheduled'] );
        $this->assertSame( 0, $unscheduled['next_run'] );
        $this->assertSame( array( 'frequent' ), $unscheduled['other_parts'] );

        $now = mktime( 10, 2, 0, 6, 1, 2026 );
        $scheduled = Processlist::workflowCronjob( $parts, array( 'frequent' => '*/5 * * * * cd /x && php runcronjobs.php -q -s site frequent' ), $now );
        $this->assertSame( 'frequent', $scheduled['part'], 'the part the crontab runs' );
        $this->assertSame( 'Frequent', $scheduled['label'] );
        $this->assertSame( 'cronjob-part-frequent', $scheduled['anchor'] );
        $this->assertTrue( $scheduled['scheduled'] );
        $this->assertSame( '*/5 * * * *', $scheduled['schedule'] );
        $this->assertSame( 'Every 5 minutes', $scheduled['schedule_text'] );
        $this->assertSame( mktime( 10, 5, 0, 6, 1, 2026 ), $scheduled['next_run'] );

        $none = Processlist::workflowCronjob( array( $parts[2], array( 'name' => 'broken' ) ), array(), 0 );
        $this->assertFalse( $none['found'] );
        $this->assertSame( '', $none['anchor'] );
    }

    /** WP-09 */
    public function testPageGroupsKeepProcessesWithoutTrigger()
    {
        $pre = $this->trigger( 'publish', 'pre_publish' );
        $post = $this->trigger( 'publish', 'post_publish' );
        $a = $this->process( 1 );
        $b = $this->process( 2 );
        $orphan = $this->process( 3 );
        $c = $this->process( 4 );
        $triggers = array( 1 => $pre, 2 => $post, 4 => $pre );
        list( $byTrigger, $count ) = Processlist::processesByTrigger( array( $a, $b, $orphan, $c ), function ( $p ) use ( $triggers ) {
            $id = $p->attribute( 'id' );
            return isset( $triggers[$id] ) ? $triggers[$id] : null;
        } );
        $this->assertSame( 3, $count );

        $groups = Processlist::pageGroups( $byTrigger, array( $a, $b, $orphan, $c ), 2000, array() );
        $this->assertSame( array( 'content/publish/pre_publish', 'content/publish/post_publish', '' ), array_column( $groups, 'key' ) );
        $this->assertSame( array( 2, 1, 1 ), array_column( $groups, 'count' ) );
        $this->assertSame( array( 1, 4 ), array_column( $groups[0]['processes'], 'id' ) );
        $this->assertSame( 'before', $groups[0]['connect'] );
        $this->assertSame( 'after', $groups[1]['connect'] );
        $this->assertSame( 'Before publishing content', $groups[0]['label'] );
        $this->assertSame( array( 3 ), array_column( $groups[2]['processes'], 'id' ) );
        $this->assertSame( 'Trigger not recorded', $groups[2]['label'] );

        $this->assertSame( array(), Processlist::pageGroups( array(), array(), 0, array() ) );
    }

    /** WP-10 */
    public function testProcessView()
    {
        $now = 1000 + 2 * 86400;
        $process = $this->process( 12, array( 'content_id' => 0, 'content_version' => 0, 'user_id' => 0,
                                              'event_id' => 5, 'event_position' => 1, 'event_status' => eZWorkflowType::STATUS_DEFERRED_TO_CRON_REPEAT,
                                              'memento_key' => 'abc',
                                              'parameter_list' => array( 'object_id' => 77, 'version' => 3, 'user_id' => 14 ) ) );
        $view = Processlist::processView( $process, $now, array( 12 => 40 ) );
        $this->assertSame( 12, $view['id'] );
        $this->assertSame( 77, $view['object']['id'], 'the object comes from the parameters when the column is empty' );
        $this->assertSame( 3, $view['object']['version'] );
        $this->assertFalse( $view['object']['exists'], 'a stand-in is never looked up' );
        $this->assertSame( 14, $view['user']['id'] );
        $this->assertSame( 7, $view['workflow']['id'] );
        $this->assertSame( 40, $view['collaboration_id'] );
        $this->assertStringContainsString( 'collaboration inbox', $view['waiting_for'] );
        $this->assertSame( 'cron', $view['status']['key'] );
        $this->assertSame( eZWorkflowType::STATUS_DEFERRED_TO_CRON_REPEAT, $view['current_event']['status']['code'] );
        $this->assertNotSame( '', $view['current_event']['status']['name'] );
        $this->assertSame( '2 days', $view['age'] );
        $this->assertTrue( $view['stuck'] );
        $this->assertSame( 'abc', $view['memento_key'] );
        $this->assertStringContainsString( '#12', $view['search'] );
        $this->assertStringContainsString( 'v3', $view['search'] );

        $failed = Processlist::processView( $this->process( 13, array( 'status' => eZWorkflow::STATUS_FAILED, 'content_id' => 9, 'content_version' => 2,
                                                                       'parameter_list' => array( 'object_id' => 77 ) ) ), 1500, array() );
        $this->assertSame( 9, $failed['object']['id'], 'the column wins over the parameters' );
        $this->assertSame( 0, $failed['collaboration_id'] );
        $this->assertSame( $failed['status']['explanation'], $failed['waiting_for'] );
        $this->assertFalse( $failed['stuck'] );
        $this->assertSame( '', $failed['object']['version_status_text'] );
    }

    /** WP-11 */
    public function testVersionStatusText()
    {
        $this->assertSame( 'waiting to be published', Processlist::versionStatusText( eZContentObjectVersion::STATUS_PENDING ) );
        $this->assertSame( 'draft', Processlist::versionStatusText( eZContentObjectVersion::STATUS_DRAFT ) );
        $this->assertSame( '', Processlist::versionStatusText( -1 ) );
    }
}
