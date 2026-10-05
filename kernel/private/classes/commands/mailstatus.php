<?php
/**
 * @description Show the e-mail preference system: tables, gate, footer, categories, suppression, consent log, the gate's last decisions and problems
 * @alias exp:mail:status
 *
 * File containing the exp:mail:status command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Mailstatus extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "E-mail preferences status\n" .
                                 "The tables, the mail gate, the footer settings, the categories with their counts, the\n" .
                                 "suppression list, the consent log, what the gate decided in the last 24 hours and 7 days,\n" .
                                 "and what looks wrong. Nothing is changed. No address is shown.\n" .
                                 "\n" .
                                 "./console exp:mail:status\n" .
                                 "./console exp:mail:status --json\n" .
                                 "\n" .
                                 "Ends with PASS, or FAIL (exit 1) when a problem of level error is found.",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[json]', '', array( 'json' => 'One JSON object instead of the text' ) );
        try
        {
            $s = \expMailPreferencesService::status();
        }
        catch ( \Throwable $e )
        {
            $cli->error( 'FAIL: ' . $e->getMessage() );
            $this->shutdown( 1 );
            return;
        }
        $errors = 0;
        foreach ( $s['problems'] as $p )
            if ( $p[0] === 'error' )
                ++$errors;
        if ( !empty( $options['json'] ) )
        {
            $s['problems'] = array_map( function ( $p ) { return array( 'level' => $p[0], 'code' => $p[1], 'text' => \expMailPreferencesService::problemText( $p ) ); }, $s['problems'] );
            $cli->output( json_encode( $s, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            $this->shutdown( $errors ? 1 : 0 );
            return;
        }
        $cli->output( 'Tables' );
        foreach ( $s['tables'] as $t => $ok )
            $cli->output( sprintf( '  %-24s %s', $t, $ok ? 'ok' : 'MISSING' ) );
        $cli->output( 'Gate' );
        $cli->output( sprintf( '  %-24s %s', 'gate', $s['gate'] ) );
        $cli->output( sprintf( '  %-24s %s', 'site secret', $s['secret'] ? 'present' : 'not generated yet' ) );
        $cli->output( sprintf( '  %-24s %s', 'links point to', $s['base_url'] ) );
        $cli->output( sprintf( '  %-24s %s', 'organisation name', $s['footer']['organisation_name'] !== '' ? $s['footer']['organisation_name'] : '(empty)' ) );
        $cli->output( sprintf( '  %-24s %s', 'postal address', $s['footer']['organisation_address'] !== '' ? str_replace( "\n", ', ', $s['footer']['organisation_address'] ) : '(empty)' ) );
        $cli->output( 'Categories                   on    off  pending' );
        foreach ( $s['categories'] as $id => $c )
            $cli->output( sprintf( '  %-24s %5s %6s %8s  %s%s%s', $id, $c['essential'] ? '-' : $c['on'], $c['essential'] ? '-' : $c['off'], $c['essential'] ? '-' : $c['pending'],
                                   $c['essential'] ? 'essential' : 'optional', $c['double_opt_in'] ? ', double opt-in' : '', $c['source'] !== 'ini' ? ', ' . $c['source'] : '' ) );
        $cli->output( 'People' );
        $cli->output( sprintf( '  %-24s %d', 'with preferences', $s['recipients'] ) );
        $cli->output( sprintf( '  %-24s %d', 'all optional mail off', $s['master_off'] ) );
        $cli->output( sprintf( '  %-24s %d   (%d expired)', 'pending confirmations', $s['pending'], $s['pending_expired'] ) );
        $cli->output( sprintf( '  %-24s %d%s', 'suppressed addresses', $s['suppression']['total'],
                               $s['suppression']['by_reason'] ? '   (' . implode( ', ', array_map( function ( $k, $v ) { return "$k $v"; }, array_keys( $s['suppression']['by_reason'] ), $s['suppression']['by_reason'] ) ) . ')' : '' ) );
        $cli->output( sprintf( '  %-24s %d   (%d anonymised)', 'consent log rows', $s['consent_log'], $s['consent_log_anonymised'] ) );
        if ( isset( $s['bounce'] ) && is_array( $s['bounce'] ) )
        {
            $b = $s['bounce'];
            $cli->output( 'Bounce mailbox' );
            $cli->output( sprintf( '  %-24s %s', 'reader', $b['enabled'] ? 'enabled, ' . strtoupper( $b['protocol'] ) . ' ' . $b['server'] : ( $b['configured'] ? 'disabled' : 'not configured' ) ) );
            $cli->output( sprintf( '  %-24s %s', 'last read', $b['last_read'] ? date( 'Y-m-d H:i', $b['last_read'] ) : 'never' ) );
            if ( $b['last_error'] !== '' )
                $cli->output( sprintf( '  %-24s %s', 'last error', $b['last_error'] ) );
            $cli->output( sprintf( '  %-24s %d messages, %d hard bounces, %d complaints, %d suppressed', 'last run',
                                   $b['last']['messages'], $b['last']['hard'], $b['last']['complaints'], $b['last']['suppressed'] ) );
        }
        foreach ( array( '24 hours' => $s['gate_24h'], '7 days' => $s['gate_7d'] ) as $label => $g )
        {
            $cli->output( 'Gate, last ' . $label );
            $cli->output( sprintf( '  sent %d, blocked %d, essential %d, uncategorised %d, errors %d', $g['sent'], $g['blocked'], $g['essential'], $g['uncategorised'], $g['error'] ) );
            if ( $g['blocked_reasons'] )
                $cli->output( '  blocked because: ' . implode( ', ', array_map( function ( $k, $v ) { return "$k $v"; }, array_keys( $g['blocked_reasons'] ), $g['blocked_reasons'] ) ) );
            foreach ( array_slice( $g['uncategorised_senders'], 0, 10, true ) as $sender => $n )
                $cli->output( sprintf( '  uncategorised from %s: %d', $sender, $n ) );
        }
        $cli->output( 'Problems' );
        if ( !$s['problems'] )
            $cli->output( '  none' );
        foreach ( $s['problems'] as $p )
            $cli->output( sprintf( '  [%s] %s', $p[0], \expMailPreferencesService::problemText( $p ) ) );
        $cli->output( $errors ? 'FAIL: ' . $errors . ' problem(s) of level error' : 'PASS' );
        $this->shutdown( $errors ? 1 : 0 );
    }
}

}
