<?php
/**
 * @description List user accounts: filter by state (enabled, disabled, unactivated, pending, locked, never signed in, inactive ...), group and search, with paging
 * @alias exp:user:list
 *
 * File containing the exp:user:list command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuserlist extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:list';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "List user accounts\n" .
                             "Lists the accounts with their id, login, e-mail address, last visit, state and main group, the\n" .
                             "same list as Users > Hygiene in the administration. Filter by state, by group (its members at\n" .
                             "any depth) and by a text in the login, name or e-mail address; page with --limit and --offset.\n" .
                             "Changes nothing.\n" .
                             "\n" .
                             "./console exp:user:list\n" .
                             "./console exp:user:list --state=disabled --group=Editors\n" .
                             "./console exp:user:list --state=inactive --days=365 --sort=last_visit\n" .
                             "./console exp:user:list --search=example.com --limit=100 --offset=100 --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[state:][group:][search:][days:][sort:][order:][limit:][offset:][format:]', '', array(
            'state' => 'Only accounts in this state: all (default), enabled, disabled, unactivated, pending, locked, never, inactive, orphaned, duplicate_login or duplicate_email',
            'group' => 'A user group by node id or name: list, export and bulk take only its members (below it, at any depth); create and import: the group of the new accounts (repeat for more, the first is the main group; default [UserSettings] DefaultUserPlacement)',
            'search' => 'Only accounts whose login, name or e-mail address contains this text',
            'days' => 'With --state=inactive: not signed in for this many days (default 180)',
            'sort' => 'The order: name (default), login, email, last_visit, login_count or id',
            'order' => 'asc (default) or desc',
            'limit' => 'How many to list (default 25, at most 500); bulk: the most accounts one run may change (default 500, at most 5000)',
            'offset' => 'Skip this many first (the next page: --offset=25)',
            'format' => 'text (default) or json (the machine output exp-user/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $checked = \expUserManager::listFilters( $options );
        if ( $checked['errors'] )
            $this->usage( implode( '; ', $checked['errors'] ) );
        $this->database( $format );
        $filters = $checked['filters'];
        $list = \expUserManager::listAccounts( $filters );
        $this->finish( array( 'mode' => 'list', 'filters' => array( 'state' => $filters['state'], 'group' => $filters['group'], 'search' => $filters['q'],
                                                                  'days' => $filters['days'], 'sort' => $filters['sort'], 'order' => $filters['order'] ),
                              'total' => $list['total'], 'offset' => $filters['offset'], 'limit' => $filters['limit'], 'accounts' => $list['accounts'],
                              'errors' => $list['error'] !== '' ? array( $list['error'] ) : array(), 'warnings' => array() ), $format );
    }

    protected function lines( array $report )
    {
        return isset( $report['accounts'] ) ? self::listLines( $report['accounts'], $report['total'], $report['offset'] ) : array();
    }
}

}
