<?php
/**
 * @description The user audit log of an account (Users > Audit): what happened to it and what it did, newest first, filtered by event, result and days
 * @alias exp:user:audit
 *
 * File containing the exp:user:audit command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuseraudit extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:audit';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "The user audit log of an account\n" .
                             "Lists the rows of the user audit log (Users > Audit, the table expuser_audit) about the account or\n" .
                             "by it, newest first: time, event, result, who acted, about whom, and the siteaccess. Filter by an\n" .
                             "event name or prefix, a result and a range of days. The values before and after are in the JSON\n" .
                             "output; passwords and secrets are never in the log. Changes nothing.\n" .
                             "\n" .
                             "./console exp:user:audit jane\n" .
                             "./console exp:user:audit jane --event=access.user.password --from=2026-10-01\n" .
                             "./console exp:user:audit jane --result=refused --limit=100 --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[event:][result:][from:][to:][limit:][offset:][format:]', '[user]', array(
            'event' => 'Only events with this name or a name below it (access.user.password)',
            'result' => 'Only events with this result: success, refused or failed',
            'from' => 'Only events on or after this day (YYYY-MM-DD)',
            'to' => 'groups move: the group to move it below (node id or name); audit: only events on or before this day (YYYY-MM-DD)',
            'limit' => 'How many to list (default 25, at most 500); bulk: the most accounts one run may change (default 500, at most 5000)',
            'offset' => 'Skip this many first (the next page: --offset=25)',
            'format' => 'text (default) or json (the machine output exp-user/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $value = function ( $name, $default = '' ) use ( $options ) {
            return isset( $options[$name] ) && $options[$name] !== false && $options[$name] !== null ? trim( (string)$options[$name] ) : $default;
        };
        $filters = \expUserManager::auditFilters( array( 'event' => $value( 'event' ), 'result' => $value( 'result' ), 'from' => $value( 'from' ),
                                                         'to' => $value( 'to' ), 'limit' => $value( 'limit', '25' ), 'offset' => $value( 'offset', '0' ) ) );
        if ( $filters['errors'] )
            $this->usage( implode( '; ', $filters['errors'] ) );
        $this->database( $format );
        $user = $this->account( $options, $format );
        $audit = \expUserManager::auditRows( $user, $filters['filters'] );
        $this->finish( array( 'mode' => 'audit', 'user' => \expUserManager::summary( $user ), 'total' => $audit['total'], 'offset' => $filters['filters']['offset'],
                              'rows' => $audit['rows'], 'errors' => $audit['error'] !== '' ? array( $audit['error'] ) : array(), 'warnings' => array() ), $format );
    }

    protected function lines( array $report )
    {
        $lines = array( sprintf( 'Account %s (id %d): %d audit row(s)', $report['user']['login'], $report['user']['id'], isset( $report['total'] ) ? $report['total'] : 0 ) );
        foreach ( isset( $report['rows'] ) ? $report['rows'] : array() as $r )
            $lines[] = sprintf( '  %s  %-44s %-8s by %-20s %s', $r['time'], $r['event'], $r['result'], $r['actor_login'] !== '' ? $r['actor_login'] : '-', $r['siteaccess'] );
        return $lines;
    }
}

}
