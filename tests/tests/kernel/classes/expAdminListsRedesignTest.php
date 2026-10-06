<?php
/**
 * What the redesigned RSS list, trigger list, workflow group list and class group list work out before they show
 * anything. No database: rows, triggers and groups are stand-ins.
 *
 *  AL-01 - Ids from a form: whole positive numbers, once each
 *  AL-02 - The remote id pattern of an import's objects escapes its wildcards, so import 1 does not match import 12
 *  AL-03 - The cached copy of a feed is named as rss/feed names it; the overview figures never go negative
 *  AL-04 - The cronjob of a script is found in the part the crontab runs; workflowCronjob() still finds workflow.php
 *  AL-05 - Triggers: changed count, rows with the workflow in words, a workflow the select does not offer, a removed one
 *  AL-06 - Triggers: grouped by module, stored triggers the list does not offer, the summary
 *  AL-07 - Workflow groups: a workflow in another group stays, one only in removed groups goes
 *  AL-08 - Workflow groups: the card counts workflows, triggers, waiting processes and what removing does
 *  AL-09 - Class groups: classes, objects, last change and what removing removes; the summary
 *  AL-10 - An RSS import needs a name and an http or https source address to be stored
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Rss\ListView as RssList;
use Exponential\View\Kernel\Trigger\ListView as TriggerList;
use Exponential\View\Kernel\Workflow\Grouplist as WorkflowGroups;
use Exponential\View\Kernel\Workflow\Processlist;
use Exponential\View\Kernel\Class\Grouplist as ClassGroups;

/** A row object: only its attributes */
class X2AdminListStandIn
{
    private $attributes;

    public function __construct( array $attributes )
    {
        $this->attributes = $attributes;
    }

    public function attribute( $name )
    {
        return isset( $this->attributes[$name] ) ? $this->attributes[$name] : null;
    }
}

class expAdminListsRedesignTest extends PHPUnit\Framework\TestCase
{
    /** AL-01 */
    public function testIdList()
    {
        $this->assertSame( array( 3, 1 ), RssList::idList( array( '3', '1', '3', '0', '-2', 'x', '1.5', 4.0 ) ) );
        $this->assertSame( array( 7 ), RssList::idList( '7' ) );
        $this->assertSame( array(), RssList::idList( null ) );
        $this->assertSame( array( 2 ), WorkflowGroups::idList( array( '2' ) ) );
    }

    /** AL-02 */
    public function testImportedObjectPattern()
    {
        $this->assertSame( 'RSSImport!_1!_%', RssList::importedObjectPattern( 1 ) );
        $this->assertSame( 'RSSImport!_%', RssList::importedObjectPattern() );
        $this->assertSame( 'RSSImport!_0!_%', RssList::importedObjectPattern( 'abc' ) );
    }

    /** AL-03 */
    public function testFeedCacheFileAndSummary()
    {
        $this->assertSame( 'rss/' . md5( 'sitemy_feed' ) . '.xml', RssList::feedCacheFile( 'site', 'my_feed' ) );
        $this->assertSame( array( 'exports' => 3, 'active_exports' => 2, 'inactive_exports' => 1,
                                  'imports' => 1, 'active_imports' => 1, 'inactive_imports' => 0, 'imported' => 40 ),
                           RssList::summaryOf( 3, 2, 1, 5, 40 ) );
        $this->assertSame( 0, RssList::summaryOf( -1, 4, 0, 0, -3 )['inactive_exports'] );
    }

    /** AL-10 */
    public function testImportValidation()
    {
        $this->assertSame( array(), \Exponential\View\Kernel\Rss\EditImport::validate( 'News', 'https://example.com/feed.xml' ) );
        $this->assertCount( 1, \Exponential\View\Kernel\Rss\EditImport::validate( '  ', 'https://example.com/feed.xml' ) );
        $this->assertCount( 1, \Exponential\View\Kernel\Rss\EditImport::validate( 'News', 'file:///etc/passwd' ) );
        $this->assertCount( 2, \Exponential\View\Kernel\Rss\EditImport::validate( null, '' ) );
    }

    /** AL-04 */
    public function testScriptCronjob()
    {
        $parts = array( array( 'name' => '', 'label' => 'Global', 'scripts' => array( array( 'name' => 'rssimport.php' ), array( 'name' => 'workflow.php' ) ) ),
                        array( 'name' => 'frequent', 'label' => 'frequent', 'scripts' => array( array( 'name' => 'rssimport.php' ) ) ) );
        $found = Processlist::scriptCronjob( 'rssimport.php', $parts, array( 'frequent' => '*/5 * * * * cd x && php runcronjobs.php frequent' ), 1000 );
        $this->assertTrue( $found['found'] );
        $this->assertSame( 'frequent', $found['part'] );
        $this->assertTrue( $found['scheduled'] );
        $this->assertSame( array( '' ), $found['other_parts'] );
        $this->assertSame( 'cronjob-part-frequent', $found['anchor'] );

        $workflow = Processlist::workflowCronjob( $parts, array(), 1000 );
        $this->assertTrue( $workflow['found'] );
        $this->assertSame( '', $workflow['part'] );
        $this->assertFalse( $workflow['scheduled'] );

        $this->assertFalse( Processlist::scriptCronjob( 'nothere.php', $parts, array(), 1000 )['found'] );
    }

    private function possible()
    {
        $wf = function ( $id, $name ) { return new X2AdminListStandIn( array( 'id' => $id, 'name' => $name ) ); };
        return array(
            array( 'connect_type' => 'before', 'module' => 'content', 'operation' => 'publish', 'workflow_id' => 5, 'key' => 'content_publish_b', 'allowed_workflows' => array( $wf( 5, 'Approve' ) ) ),
            array( 'connect_type' => 'after', 'module' => 'content', 'operation' => 'publish', 'workflow_id' => 6, 'key' => 'content_publish_a', 'allowed_workflows' => array( $wf( 5, 'Approve' ) ) ),
            array( 'connect_type' => 'before', 'module' => 'shop', 'operation' => 'checkout', 'workflow_id' => 9, 'key' => 'shop_checkout_b', 'allowed_workflows' => array() ),
            array( 'connect_type' => 'after', 'module' => 'shop', 'operation' => 'checkout', 'workflow_id' => 0, 'key' => 'shop_checkout_a', 'allowed_workflows' => array() ),
        );
    }

    private function facts()
    {
        return array( 5 => array( 'id' => 5, 'name' => 'Approve', 'enabled' => true, 'events' => 1, 'modified' => 100 ),
                      6 => array( 'id' => 6, 'name' => 'Notify', 'enabled' => false, 'events' => 2, 'modified' => 300 ) );
    }

    /** AL-05 */
    public function testTriggerRows()
    {
        $this->assertSame( 2, TriggerList::changedCount( array( 'a' => 1, 'b' => 2 ), array( 'a' => 1, 'b' => 3, 'c' => 4 ) ) );
        $this->assertSame( 0, TriggerList::changedCount( array( 'a' => 1 ), array( 'a' => 1 ) ) );

        $rows = TriggerList::rows( $this->possible(), $this->facts(), array( 5 => 3 ) );
        $this->assertCount( 4, $rows );
        $this->assertSame( 'Before publishing content', $rows[0]['label'] );
        $this->assertTrue( $rows[0]['is_set'] );
        $this->assertFalse( $rows[0]['not_allowed'] );
        $this->assertSame( 3, $rows[0]['waiting'] );
        $this->assertSame( 'After publishing content', $rows[1]['label'] );
        $this->assertTrue( $rows[1]['not_allowed'], 'Notify is stored but the select does not offer it' );
        $this->assertTrue( $rows[2]['missing'], 'workflow 9 does not exist' );
        $this->assertFalse( $rows[3]['is_set'] );
        $this->assertSame( 'After checkout', $rows[3]['label'] );
    }

    /** AL-06 */
    public function testTriggerGroupsOrphansSummary()
    {
        $rows = TriggerList::rows( $this->possible(), $this->facts(), array( 5 => 3 ) );
        $groups = TriggerList::groupByModule( $rows );
        $this->assertSame( array( 'content', 'shop' ), array( $groups[0]['module'], $groups[1]['module'] ) );
        $this->assertSame( 2, $groups[0]['set'] );
        $this->assertSame( 1, $groups[1]['set'] );

        $stored = array( new X2AdminListStandIn( array( 'id' => 1, 'module_name' => 'content', 'function_name' => 'publish', 'connect_type' => 'b', 'workflow_id' => 5 ) ),
                         new X2AdminListStandIn( array( 'id' => 2, 'module_name' => 'content', 'function_name' => 'hide', 'connect_type' => 'a', 'workflow_id' => 6 ) ) );
        $orphans = TriggerList::orphans( $this->possible(), $stored );
        $this->assertCount( 1, $orphans );
        $this->assertSame( 2, $orphans[0]['id'] );
        $this->assertSame( 'after', $orphans[0]['connect_type'] );
        $this->assertSame( 'After content is hidden or shown', $orphans[0]['label'] );

        $summary = TriggerList::summary( $rows, $orphans );
        $this->assertSame( array( 'possible' => 4, 'set' => 3, 'unset' => 1, 'attention' => 2, 'orphans' => 1, 'waiting' => 3 ), $summary );
    }

    /** AL-07 */
    public function testWorkflowRemovalPlan()
    {
        $links = array( array( 'workflow_id' => 5, 'group_id' => 1 ), array( 'workflow_id' => 6, 'group_id' => 1 ),
                        array( 'workflow_id' => 6, 'group_id' => 2 ), array( 'workflow_id' => 7, 'group_id' => 2 ),
                        array( 'workflow_id' => 8, 'group_id' => 1 ), array( 'workflow_id' => 8, 'group_id' => 3 ) );
        $this->assertSame( array( 'remove' => array( 5 ), 'unlink' => array( 6 => array( 2 ), 8 => array( 3 ) ) ),
                           WorkflowGroups::removalPlan( $links, array( 1 ) ) );
        $this->assertSame( array( 'remove' => array( 5, 6, 7 ), 'unlink' => array( 8 => array( 3 ) ) ),
                           WorkflowGroups::removalPlan( $links, array( 1, 2 ) ) );
        $this->assertSame( array( 'remove' => array(), 'unlink' => array() ), WorkflowGroups::removalPlan( $links, array( 9 ) ) );
    }

    /** AL-08 */
    public function testWorkflowGroupOverview()
    {
        $groups = array( new X2AdminListStandIn( array( 'id' => 1, 'name' => 'Standard', 'modified' => 50 ) ),
                         new X2AdminListStandIn( array( 'id' => 2, 'name' => 'Empty', 'modified' => 10 ) ) );
        $links = array( array( 'workflow_id' => 5, 'group_id' => 1 ), array( 'workflow_id' => 6, 'group_id' => 1 ),
                        array( 'workflow_id' => 6, 'group_id' => 3 ) );
        $overview = WorkflowGroups::overview( $groups, $links, $this->facts(), array( 5 => array( 'Before publishing content' ) ),
                                              array( 5 => 2 ), array( 1 => 'Standard', 2 => 'Empty', 3 => 'Other' ) );
        $one = $overview[1];
        $this->assertSame( 2, $one['workflow_count'] );
        $this->assertSame( 1, $one['enabled'] );
        $this->assertSame( 1, $one['triggered'] );
        $this->assertSame( 2, $one['waiting'] );
        $this->assertSame( 1, $one['removes'] );
        $this->assertSame( 1, $one['unlinks'] );
        $this->assertSame( 1, $one['removes_triggered'] );
        $this->assertSame( 2, $one['removes_waiting'] );
        $this->assertSame( 300, $one['last_modified'] );
        $this->assertSame( array( 'Approve', 'Notify' ), array( $one['workflows'][0]['name'], $one['workflows'][1]['name'] ) );
        $this->assertSame( array( array( 'id' => 3, 'name' => 'Other' ) ), $one['workflows'][1]['other_groups'] );
        $this->assertStringContainsString( 'before publishing content', $one['search'] );
        $this->assertSame( 0, $overview[2]['workflow_count'] );
        $this->assertSame( 10, $overview[2]['last_modified'] );
    }

    /** AL-09 */
    public function testClassGroupOverviewAndSummary()
    {
        $groups = array( new X2AdminListStandIn( array( 'id' => 1, 'name' => 'Content', 'modified' => 50 ) ),
                         new X2AdminListStandIn( array( 'id' => 2, 'name' => 'Media', 'modified' => 900 ) ) );
        $classes = array( 10 => array( 'id' => 10, 'name' => 'Folder', 'identifier' => 'folder', 'modified' => 100, 'objects' => 4 ),
                          11 => array( 'id' => 11, 'name' => 'Article', 'identifier' => 'article', 'modified' => 200, 'objects' => 7 ),
                          12 => array( 'id' => 12, 'name' => 'Image', 'identifier' => 'image', 'modified' => 30, 'objects' => 1 ),
                          13 => array( 'id' => 13, 'name' => 'Lost', 'identifier' => 'lost', 'modified' => 30, 'objects' => 0 ) );
        $links = array( array( 'class_id' => 10, 'group_id' => 1 ), array( 'class_id' => 11, 'group_id' => 1 ),
                        array( 'class_id' => 10, 'group_id' => 2 ), array( 'class_id' => 12, 'group_id' => 2 ),
                        array( 'class_id' => 99, 'group_id' => 1 ) );
        $overview = ClassGroups::overview( $groups, $links, $classes, ClassGroups::groupNames( $groups ) );
        $content = $overview[1];
        $this->assertSame( 2, $content['class_count'] );
        $this->assertSame( 11, $content['objects'] );
        $this->assertSame( 1, $content['removes'] );
        $this->assertSame( 7, $content['removes_objects'] );
        $this->assertSame( 1, $content['shared'] );
        $this->assertSame( 200, $content['last_modified'] );
        $this->assertSame( 'Article', $content['last_class']['name'] );
        $this->assertSame( array( 'Article', 'Folder' ), array( $content['classes'][0]['name'], $content['classes'][1]['name'] ) );
        $this->assertSame( 0, $content['more'] );
        $this->assertSame( 900, $overview[2]['last_modified'], 'the group changed after its classes' );
        $this->assertFalse( $overview[2]['last_class'] );

        $this->assertSame( array( 'groups' => 2, 'classes' => 4, 'objects' => 12, 'shared' => 1, 'ungrouped' => 1 ),
                           ClassGroups::summary( $groups, $links, $classes ) );
    }
}
