<?php
/**
 * @description User groups: list the group tree with its members, create, rename and move a group, remove an empty group to the trash (changes are a dry run unless --apply)
 * @alias exp:user:groups
 *
 * File containing the exp:user:groups command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expusergroups extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:groups';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "User groups\n" .
                             "list (the default) shows the user group tree below a group, or below the users root, with each\n" .
                             "group's node id, its direct members and its subgroups. create makes a group (--name, below\n" .
                             "--parent), rename gives it a new name (a new version, published with its workflows), move puts it\n" .
                             "below another group, and delete moves an empty group to the trash: a group that still holds\n" .
                             "accounts or groups is refused, because removing a group never removes accounts. Groups are named\n" .
                             "by node id or name. Without --apply nothing is changed.\n" .
                             "\n" .
                             "./console exp:user:groups\n" .
                             "./console exp:user:groups list Editors\n" .
                             "./console exp:user:groups create --name=Partners --parent=Members --apply\n" .
                             "./console exp:user:groups rename Partners --name=\"Partner agencies\" --apply\n" .
                             "./console exp:user:groups move 245 --to=Editors --apply\n" .
                             "./console exp:user:groups delete 245 --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[parent:][name:][to:][apply][dry-run][as:][format:]', '[action][group]', array(
            'parent' => 'groups create: the group the new group goes below (node id or name; default the users root, content.ini [NodeSettings] UserRootNode)',
            'name' => 'groups create and rename: the name of the group',
            'to' => 'groups move: the group to move it below (node id or name); audit: only events on or before this day (YYYY-MM-DD)',
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
        $action = isset( $options['arguments'][0] ) ? strtolower( trim( (string)$options['arguments'][0] ) ) : 'list';
        $group = isset( $options['arguments'][1] ) ? (string)$options['arguments'][1] : '';
        if ( !in_array( $action, \expUserGroupManager::GROUP_ACTIONS, true ) )
            $this->usage( 'the action is one of ' . implode( ', ', \expUserGroupManager::GROUP_ACTIONS ) . ", not $action" );
        $value = function ( $name ) use ( $options ) {
            return isset( $options[$name] ) && $options[$name] !== false && $options[$name] !== null ? (string)$options[$name] : '';
        };
        $this->database( $format );
        $this->actor( $options, $format );
        if ( $action === 'list' )
        {
            $tree = \expUserGroupManager::tree( $group );
            $this->finish( array( 'mode' => 'groups', 'action' => 'list', 'groups' => $tree['groups'],
                                  'errors' => $tree['error'] !== '' ? array( $tree['error'] ) : array(), 'warnings' => array() ), $format );
        }
        switch ( $action )
        {
            case 'create':
                $result = \expUserGroupManager::create( $value( 'name' ), $value( 'parent' ), $apply );
                break;
            case 'rename':
                $result = \expUserGroupManager::rename( $group, $value( 'name' ), $apply );
                break;
            case 'move':
                $result = \expUserGroupManager::move( $group, $value( 'to' ), $apply );
                break;
            default:
                $result = \expUserGroupManager::delete( $group, $apply );
        }
        $this->finish( array( 'mode' => 'groups', 'action' => $action, 'dry_run' => !$apply ) + $result, $format );
    }

    protected function lines( array $report )
    {
        if ( !isset( $report['groups'] ) )
            return self::actionLines( $report );
        $lines = array( sprintf( '%d user group(s)', count( $report['groups'] ) ), sprintf( '  %-7s %-8s %-10s %s', 'Node', 'Members', 'Subgroups', 'Name' ) );
        foreach ( $report['groups'] as $g )
            $lines[] = sprintf( '  %-7d %-8d %-10d %s%s', $g['node_id'], $g['members'], $g['subgroups'], str_repeat( '  ', max( 0, $g['depth'] ) ), $g['name'] );
        return $lines;
    }
}

}
