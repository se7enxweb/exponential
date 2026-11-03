<?php
/**
 * @description List the registrations that wait: for the e-mail address to be confirmed, for an administrator, or for an approval item, newest first
 * @alias exp:user:pending
 *
 * File containing the exp:user:pending command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuserpending extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:pending';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "List the registrations that wait\n" .
                             "Lists the accounts whose registration is not finished, with what each waits for: email (the\n" .
                             "person has not confirmed the address), administrator (an administrator activates it) or workflow\n" .
                             "(an approval item waits for an approver). exp:user:approve approves or rejects one. Changes\n" .
                             "nothing; the same as exp:user:list --state=pending.\n" .
                             "\n" .
                             "./console exp:user:pending\n" .
                             "./console exp:user:pending --search=example.org --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[search:][limit:][offset:][format:]', '', array(
            'search' => 'Only accounts whose login, name or e-mail address contains this text',
            'limit' => 'How many to list (default 25, at most 500); bulk: the most accounts one run may change (default 500, at most 5000)',
            'offset' => 'Skip this many first (the next page: --offset=25)',
            'format' => 'text (default) or json (the machine output exp-user/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $checked = \expUserManager::listFilters( array( 'state' => 'pending', 'sort' => 'id', 'order' => 'desc' ) + $options );
        if ( $checked['errors'] )
            $this->usage( implode( '; ', $checked['errors'] ) );
        $this->database( $format );
        $filters = $checked['filters'];
        $list = \expUserManager::listAccounts( $filters );
        $this->finish( array( 'mode' => 'pending', 'filters' => array( 'state' => 'pending', 'search' => $filters['q'] ), 'total' => $list['total'],
                              'offset' => $filters['offset'], 'limit' => $filters['limit'], 'accounts' => $list['accounts'],
                              'errors' => $list['error'] !== '' ? array( $list['error'] ) : array(), 'warnings' => array() ), $format );
    }

    protected function lines( array $report )
    {
        return isset( $report['accounts'] ) ? self::listLines( $report['accounts'], $report['total'], $report['offset'] ) : array();
    }
}

}
