<?php
/**
 * File containing the expUpgradeStepMemberManagement class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Member management: the roles of [MemberManagementSettings] Rules[], through expMemberManagement as
 * bin/php/addmembermanagement.php --apply. Not applicable when member management is disabled; "unknown" when a
 * rule cannot be resolved (a group it names does not exist), so the run goes on and the reason is shown.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepMemberManagement extends expUpgradeScriptStep
{
    public function id()
    {
        return '6.0.15-member-management';
    }

    public function description()
    {
        return 'Member management: editors manage the accounts below Members (bin/php/addmembermanagement.php)';
    }

    public function after()
    {
        return array( '6.0.15-editor-policies' );
    }

    /** @return array name => prepared rule */
    protected function rules()
    {
        $rules = array();
        foreach ( expMemberManagement::rules() as $name => $rule )
            $rules[$name] = expMemberManagement::prepare( $rule );
        return $rules;
    }

    protected function changes( expUpgradeContext $context )
    {
        if ( !expMemberManagement::enabled() )
        {
            $this->notApplicable = true;
            $this->reason = 'member management is disabled ([MemberManagementSettings] MemberManagement)';
            return null;
        }
        $lines = array();
        foreach ( $this->rules() as $name => $rule )
        {
            if ( $rule['errors'] )
            {
                $this->reason = "rule $name: " . implode( '; ', $rule['errors'] );
                return null;
            }
            $changes = expMemberManagement::changes( $rule );
            if ( $changes['create_role'] )
                $lines[] = "rule $name: create role \"{$rule['role']}\"";
            foreach ( $changes['add'] as $policy )
                $lines[] = "rule $name: add " . expMemberManagement::describe( $policy );
            foreach ( $changes['assign'] as $group )
                $lines[] = "rule $name: assign to {$group['name']}";
        }
        return $lines;
    }

    protected function write( expUpgradeContext $context )
    {
        $done = array();
        foreach ( $this->rules() as $name => $rule )
        {
            $changes = expMemberManagement::changes( $rule );
            if ( !$changes['create_role'] && !$changes['add'] && !$changes['assign'] )
                continue;
            expMemberManagement::apply( $rule );
            $done[] = "rule $name: applied";
        }
        return $done;
    }
}
