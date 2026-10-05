<?php
/**
 * workflow/processlist groups the waiting workflow processes by trigger
 * (\Exponential\View\Kernel\Workflow\Processlist::processesByTrigger()).
 *
 * The trigger is read from the child memento of a process. A process without a main memento, for example one deferred
 * to cron, used to be left out, so the list stayed empty although workflows were waiting.
 *
 * Database tests: each test stores a trigger and mementos in the test database.
 *
 * @group database
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

#[\PHPUnit\Framework\Attributes\Group('database')]
class eZWorkflowProcessListTest extends ezpDatabaseTestCase
{
    protected $backupGlobals = false;

    private function storeTrigger()
    {
        $trigger = eZTrigger::createNew( 'content', 'publish', 'b', 1 );
        return $trigger;
    }

    private function storeChildMemento( $key )
    {
        $memento = eZOperationMemento::create( $key, array( 'module_name' => 'content',
                                                            'operation_name' => 'publish',
                                                            'name' => 'pre_publish' ) );
        $memento->store();
        return $memento;
    }

    private function process( $key )
    {
        return new eZWorkflowProcess( array( 'memento_key' => $key ) );
    }

    public function testProcessWithoutMainMementoIsListedUnderItsTrigger()
    {
        $this->storeTrigger();
        $key = md5( __FUNCTION__ );
        $this->storeChildMemento( $key );
        $process = $this->process( $key );

        list( $list, $count ) = \Exponential\View\Kernel\Workflow\Processlist::processesByTrigger( array( $process ) );

        $this->assertSame( 1, $count );
        $this->assertSame( array( 'content/publish/pre_publish' ), array_keys( $list ) );
        $this->assertSame( array( $process ), $list['content/publish/pre_publish']['process_list'] );
    }

    public function testProcessWithoutChildMementoIsLeftOut()
    {
        $this->storeTrigger();

        list( $list, $count ) = \Exponential\View\Kernel\Workflow\Processlist::processesByTrigger( array( $this->process( md5( __FUNCTION__ ) ) ) );

        $this->assertSame( 0, $count );
        $this->assertSame( array(), $list );
    }
}
