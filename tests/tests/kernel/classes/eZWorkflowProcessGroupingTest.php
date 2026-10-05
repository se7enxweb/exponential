<?php
/**
 * workflow/processlist groups the waiting processes by the trigger they wait in:
 * \Exponential\View\Kernel\Workflow\Processlist::processesByTrigger(). No database: the processes and triggers are
 * stand-ins and the trigger of each process is given by a function instead of being read from its memento.
 *
 *  WG-01 - Processes are grouped by <module>/<function>/<name> of their trigger, in the order they come
 *  WG-02 - The count is the number of processes listed
 *  WG-03 - A process whose trigger is not found is left out (and not counted); a main memento is never asked for
 *  WG-04 - An empty list gives an empty grouping; entries that are not objects are skipped
 *  WG-05 - Each process is looked up once
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** A process or a trigger: only its attributes */
class X1WorkflowGroupingStandIn
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

class eZWorkflowProcessGroupingTest extends PHPUnit\Framework\TestCase
{
    private function trigger( $function, $name )
    {
        return new X1WorkflowGroupingStandIn( array( 'module_name' => 'content', 'function_name' => $function, 'name' => $name ) );
    }

    private function process( $id, $trigger )
    {
        return new X1WorkflowGroupingStandIn( array( 'id' => $id, 'memento_key' => 'key' . $id, 'trigger' => $trigger ) );
    }

    private function group( array $processes, &$asked = null )
    {
        $asked = array();
        return \Exponential\View\Kernel\Workflow\Processlist::processesByTrigger( $processes, function ( $process ) use ( &$asked )
        {
            $asked[] = $process->attribute( 'id' );
            return $process->attribute( 'trigger' );
        } );
    }

    /** WG-01, WG-02 */
    public function testProcessesAreGroupedByTrigger()
    {
        $prePublish = $this->trigger( 'publish', 'pre_publish' );
        $postPublish = $this->trigger( 'publish', 'post_publish' );
        $a = $this->process( 1, $prePublish );
        $b = $this->process( 2, $postPublish );
        $c = $this->process( 3, $prePublish );

        list( $groups, $count ) = $this->group( array( $a, $b, $c ) );

        $this->assertSame( array( 'content/publish/pre_publish', 'content/publish/post_publish' ), array_keys( $groups ) );
        $this->assertSame( $prePublish, $groups['content/publish/pre_publish']['trigger'] );
        $this->assertSame( array( $a, $c ), $groups['content/publish/pre_publish']['process_list'] );
        $this->assertSame( array( $b ), $groups['content/publish/post_publish']['process_list'] );
        $this->assertSame( 3, $count );
    }

    /** WG-03 */
    public function testAProcessWithoutTriggerIsLeftOut()
    {
        $kept = $this->process( 1, $this->trigger( 'publish', 'pre_publish' ) );
        list( $groups, $count ) = $this->group( array( $kept, $this->process( 2, null ) ) );
        $this->assertSame( array( $kept ), $groups['content/publish/pre_publish']['process_list'] );
        $this->assertSame( 1, $count );
    }

    /** WG-04 */
    public function testAnEmptyListGivesNoGroups()
    {
        $this->assertSame( array( array(), 0 ), $this->group( array() ) );
        $this->assertSame( array( array(), 0 ), $this->group( array( null, false, 'x' ), $asked ) );
        $this->assertSame( array(), $asked );
    }

    /** WG-05 */
    public function testEachProcessIsLookedUpOnce()
    {
        $trigger = $this->trigger( 'read', 'pre_read' );
        $this->group( array( $this->process( 1, $trigger ), $this->process( 2, $trigger ), $this->process( 3, null ) ), $asked );
        $this->assertSame( array( 1, 2, 3 ), $asked );
    }
}
