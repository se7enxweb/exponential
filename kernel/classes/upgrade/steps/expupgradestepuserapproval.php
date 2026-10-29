<?php
/**
 * File containing the expUpgradeStepUserApproval class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The approval of new user accounts (workflow, trigger, event, role), through expUserApproval as
 * bin/php/adduserapproval.php with its default options. "unknown" when the groups it needs cannot be found.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepUserApproval extends expUpgradeScriptStep
{
    /** The administrator the workflow runs as, as the command's default (--admin-id). */
    const ADMIN_ID = 14;

    public function id()
    {
        return '6.0.15-user-approval';
    }

    public function description()
    {
        return 'The approval of new user accounts by an administrator (bin/php/adduserapproval.php)';
    }

    public function after()
    {
        return array( '6.0.15-member-management' );
    }

    protected function changes( expUpgradeContext $context )
    {
        $plan = expUserApproval::plan( array( 'role' => true ) );
        if ( $plan['errors'] )
        {
            $this->reason = implode( '; ', $plan['errors'] );
            return null;
        }
        $changes = expUserApproval::changes( $plan );
        $lines = array();
        if ( $changes['workflow'] === 'create' )
            $lines[] = 'create workflow "' . expUserApproval::WORKFLOW_NAME . '"';
        // 'add_event' only says the trigger runs another workflow, which the event goes into: the event line below
        // tells whether that is still to do (the command does not count it either)
        if ( $changes['trigger'] === 'create' )
            $lines[] = 'create trigger content/publish/before';
        if ( $changes['event'] === 'create' || $changes['event'] === 'update' )
            $lines[] = $changes['event'] . ' the event "Approve user accounts"';
        if ( $changes['role'] === 'create' )
            $lines[] = 'create role "' . expUserApproval::ROLE_NAME . '"';
        foreach ( $changes['add'] as $policy )
            $lines[] = 'add policy ' . expMemberManagement::describe( $policy );
        foreach ( $changes['assign'] as $group )
            $lines[] = "assign the role to {$group['name']}";
        return $lines;
    }

    protected function write( expUpgradeContext $context )
    {
        $plan = expUserApproval::plan( array( 'role' => true ) );
        expUserApproval::apply( $plan, self::ADMIN_ID );
        return array( 'the approval of new accounts is set up' );
    }
}
