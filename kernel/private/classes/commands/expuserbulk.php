<?php
/**
 * @description Disable, enable, delete, approve, reject or unlock every account the list filters select, each as the single command does it; --apply needs --confirm with the number the dry run showed
 * @alias exp:user:bulk
 *
 * File containing the exp:user:bulk command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuserbulk extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:bulk';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "One action for many accounts\n" .
                             "disable, enable, delete, approve, reject or unlock every account the filters of exp:user:list\n" .
                             "select (--state, --group, --search, --days), at most --limit. Each account goes through the same\n" .
                             "checks as the single command: protected accounts and the last administrator are listed and never\n" .
                             "changed. The dry run lists the accounts and their number; --apply also needs --confirm=<that\n" .
                             "number>, and runs only while the filter still selects exactly as many.\n" .
                             "\n" .
                             "./console exp:user:bulk disable --state=inactive --days=730 --group=Members\n" .
                             "./console exp:user:bulk disable --state=inactive --days=730 --group=Members --apply --confirm=42\n" .
                             "./console exp:user:bulk approve --state=pending --search=example.edu --apply --confirm=17",
        ) + self::SCRIPT );
        return (array)$this->startup( '[state:][group:][search:][days:][limit:][confirm:][apply][dry-run][as:][format:]', '[action]', array(
            'state' => 'Only accounts in this state: all (default), enabled, disabled, unactivated, pending, locked, never, inactive, orphaned, duplicate_login or duplicate_email',
            'group' => 'A user group by node id or name: list, export and bulk take only its members (below it, at any depth); create and import: the group of the new accounts (repeat for more, the first is the main group; default [UserSettings] DefaultUserPlacement)',
            'search' => 'Only accounts whose login, name or e-mail address contains this text',
            'days' => 'With --state=inactive: not signed in for this many days (default 180)',
            'limit' => 'How many to list (default 25, at most 500); bulk: the most accounts one run may change (default 500, at most 5000)',
            'confirm' => 'With --apply: the number of accounts the filter selects, as the dry run showed it; the action runs only when it still matches',
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
        $action = isset( $options['arguments'][0] ) ? strtolower( trim( (string)$options['arguments'][0] ) ) : '';
        if ( !in_array( $action, \expUserBulk::ACTIONS, true ) )
            $this->usage( 'name the action: exp:user:bulk ' . implode( '|', \expUserBulk::ACTIONS ) . ' [filters]' );
        $limit = isset( $options['limit'] ) && $options['limit'] !== false ? trim( (string)$options['limit'] ) : '500';
        if ( !ctype_digit( $limit ) || (int)$limit < 1 || (int)$limit > \expUserBulk::MAX_BULK )
            $this->usage( '--limit must be a whole number from 1 to ' . \expUserBulk::MAX_BULK . ", not $limit" );
        $checked = \expUserManager::listFilters( array( 'limit' => false ) + $options );
        if ( $checked['errors'] )
            $this->usage( implode( '; ', $checked['errors'] ) );
        if ( $action === 'delete' && $checked['filters']['state'] === 'all' && $checked['filters']['group'] === '' && $checked['filters']['q'] === '' )
            $this->usage( 'bulk delete needs a filter (--state, --group or --search): it never removes every account' );
        $this->database( $format );
        $actor = $this->actor( $options, $format );
        $result = \expUserBulk::run( $action, $checked['filters'], (int)$limit, (int)$actor->attribute( 'contentobject_id' ), $apply,
                                     isset( $options['confirm'] ) ? $options['confirm'] : null );
        $this->finish( array( 'mode' => 'bulk', 'dry_run' => !$apply ) + $result, $format );
    }

    protected function lines( array $report )
    {
        $lines = array( sprintf( '%s: the filter selects %d account(s); %d %s, %d refused', $report['action'], $report['matched'], $report['done'],
                                 !empty( $report['dry_run'] ) ? 'would change' : 'changed', $report['refused'] ) );
        foreach ( $report['accounts'] as $a )
            $lines[] = sprintf( '  %-7d %-24s %-10s %s', $a['id'], self::cut( $a['login'], 24 ), $a['result'], $a['reason'] );
        if ( !empty( $report['dry_run'] ) && $report['accounts'] && empty( $report['errors'] ) )
            $lines[] = sprintf( 'Dry run, nothing changed. To make the change: the same command with --apply --confirm=%d', $report['matched'] );
        return $lines;
    }
}

}
