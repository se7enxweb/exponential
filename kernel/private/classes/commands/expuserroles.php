<?php
/**
 * @description Role assignments: list those of a user or group, assign a role with or without a subtree or section limitation, unassign it, as role/assign does (changes are a dry run unless --apply)
 * @alias exp:user:roles
 *
 * File containing the exp:user:roles command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuserroles extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:roles';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Role assignments of users and groups\n" .
                             "list <target> shows the roles that reach a user or a group, with where each comes from (the\n" .
                             "account, the group or a group above it) and its limitation. assign <role> <target> assigns a role,\n" .
                             "limited to a subtree (--subtree=<node id>) or a section (--section=<id or identifier>) or without a\n" .
                             "limitation; unassign <role> <target> removes the role's assignments from the target (with --subtree\n" .
                             "or --section only that limited one). The role is named by id or name; the target is a user (login,\n" .
                             "e-mail or id) or group:<node id or name>. The audit records access.role.assign and\n" .
                             "access.role.unassign, and the role, policy and content caches are cleared as role/assign does.\n" .
                             "Without --apply nothing is changed.\n" .
                             "\n" .
                             "./console exp:user:roles list jane\n" .
                             "./console exp:user:roles list group:Editors\n" .
                             "./console exp:user:roles assign Editor group:Partners --subtree=2 --apply\n" .
                             "./console exp:user:roles assign \"Member management\" jane --section=standard --apply\n" .
                             "./console exp:user:roles unassign Editor jane --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[subtree:][section:][apply][dry-run][as:][format:]', '[action][role][target]', array(
            'subtree' => 'roles assign and unassign: the assignment limited to the subtree of this node (node id)',
            'section' => 'roles assign and unassign: the assignment limited to this section (id or identifier)',
            'apply' => 'Make the change; without it the command only lists what it would change (a dry run)',
            'dry-run' => 'The default, kept for scripts that pass it: list what would change, change nothing',
            'as' => 'Act as this user (login, e-mail or id; default the user of [UserSettings] UserCreatorID); the audit records it as who did it',
            'format' => 'text (default) or json (the machine output exp-user/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = $this->apply( $options );
        $args = isset( $options['arguments'] ) ? array_values( (array)$options['arguments'] ) : array();
        $action = isset( $args[0] ) ? strtolower( trim( (string)$args[0] ) ) : '';
        if ( !in_array( $action, \expUserGroupManager::ROLE_ACTIONS, true ) )
            $this->usage( 'the action is one of ' . implode( ', ', \expUserGroupManager::ROLE_ACTIONS ) . ': exp:user:roles list <target>, exp:user:roles assign|unassign <role> <target>' );
        $this->database( $format );
        $this->actor( $options, $format );
        if ( $action === 'list' )
        {
            $target = \expUserGroupManager::findTarget( isset( $args[1] ) ? (string)$args[1] : '' );
            if ( $target['error'] !== '' )
                $this->finish( array( 'mode' => 'roles', 'action' => 'list', 'errors' => array( $target['error'] ), 'warnings' => array() ), $format );
            $this->finish( array( 'mode' => 'roles', 'action' => 'list', 'target' => array( 'type' => $target['type'], 'id' => $target['object_id'], 'label' => $target['label'] ),
                                  'assignments' => \expUserGroupManager::assignments( $target ), 'errors' => array(), 'warnings' => array() ), $format );
        }
        $role = \expUserGroupManager::findRole( isset( $args[1] ) ? (string)$args[1] : '' );
        $target = \expUserGroupManager::findTarget( isset( $args[2] ) ? (string)$args[2] : '' );
        $limitation = \expUserGroupManager::limitation( isset( $options['subtree'] ) && $options['subtree'] !== false ? (string)$options['subtree'] : '',
                                                        isset( $options['section'] ) && $options['section'] !== false ? (string)$options['section'] : '' );
        $errors = array_values( array_filter( array( $role['error'], $target['error'], $limitation['error'] ) ) );
        if ( $errors )
            $this->finish( array( 'mode' => 'roles', 'action' => $action, 'errors' => $errors, 'warnings' => array() ), $format );
        $result = $action === 'assign' ? \expUserGroupManager::assign( $role['role'], $target, $limitation, $apply )
                                       : \expUserGroupManager::unassign( $role['role'], $target, $limitation, $apply );
        $this->finish( array( 'mode' => 'roles', 'action' => $action, 'dry_run' => !$apply, 'role' => $role['role']->attribute( 'name' ),
                              'target' => array( 'type' => $target['type'], 'id' => $target['object_id'], 'label' => $target['label'] ) ) + $result, $format );
    }

    protected function lines( array $report )
    {
        if ( !isset( $report['assignments'] ) )
            return self::actionLines( $report );
        $lines = array( 'Roles of ' . $report['target']['label'] . ':' );
        foreach ( $report['assignments'] as $a )
            $lines[] = sprintf( '  %s%s, via %s', $a['role'], $a['limitation'] !== '' ? ' (' . $a['limitation'] . ')' : '',
                                $a['via'] === $report['target']['id'] ? 'itself' : $a['via_name'] . ' (object ' . $a['via'] . ')' );
        if ( !$report['assignments'] )
            $lines[] = '  none';
        return $lines;
    }
}

}
