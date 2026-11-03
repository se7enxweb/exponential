<?php
/**
 * @description The counts of Users > Hygiene: accounts per state (disabled, unactivated, pending, locked, never signed in, inactive, orphaned, duplicate logins and addresses), each with the command that lists them
 * @alias exp:user:hygiene
 *
 * File containing the exp:user:hygiene command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuserhygiene extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:hygiene';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "The state of the accounts at a glance\n" .
                             "Counts the accounts in each state of Users > Hygiene: all, enabled, disabled, unactivated, pending,\n" .
                             "locked, never signed in, inactive (not signed in for --days days), orphaned, and logins and e-mail\n" .
                             "addresses more than one account has; each with the exp:user:list command that lists them. Changes\n" .
                             "nothing. Run it before a clean-up, and from a monitoring job with --format=json.\n" .
                             "\n" .
                             "./console exp:user:hygiene\n" .
                             "./console exp:user:hygiene --days=365 --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[days:][format:]', '', array(
            'days' => 'With --state=inactive: not signed in for this many days (default 180)',
            'format' => 'text (default) or json (the machine output exp-user/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $checked = \expUserManager::listFilters( array( 'days' => isset( $options['days'] ) ? $options['days'] : false ) );
        if ( $checked['errors'] )
            $this->usage( implode( '; ', $checked['errors'] ) );
        $this->database( $format );
        $counts = \expUserManager::hygieneCounts( $checked['filters']['days'] );
        $commands = array();
        foreach ( array_keys( $counts['counts'] ) as $state )
            $commands[$state] = './console exp:user:list --state=' . $state . ( $state === 'inactive' ? ' --days=' . $checked['filters']['days'] : '' );
        $this->finish( array( 'mode' => 'hygiene', 'days' => $checked['filters']['days'], 'counts' => $counts['counts'], 'commands' => $commands,
                              'errors' => $counts['error'] !== '' ? array( $counts['error'] ) : array(), 'warnings' => array() ), $format );
    }

    protected function lines( array $report )
    {
        $lines = array( 'Accounts by state:' );
        foreach ( isset( $report['counts'] ) ? $report['counts'] : array() as $state => $n )
            $lines[] = sprintf( '  %-16s %7d   %s', $state, $n, $n > 0 && $state !== 'all' ? $report['commands'][$state] : '' );
        return $lines;
    }
}

}
