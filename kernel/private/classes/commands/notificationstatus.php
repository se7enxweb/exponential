<?php
/**
 * @description Show the notification system: pending events, digest items, subscriptions, the last runs and problems
 * @alias exp:notification:status
 * @alias exp:notify:status
 *
 * File containing the exp:notification:status command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Notificationstatus extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "Notification status\n" .
                                 "Pending events, the messages waiting for a digest, the subscriptions, the last runs of the\n" .
                                 "notification cronjob and what looks wrong. Nothing is changed.\n" .
                                 "\n" .
                                 "./console exp:notification:status\n" .
                                 "./console exp:notification:status --json\n" .
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
            $s = \expNotificationService::status();
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
            $s['problems'] = array_map( function ( $p ) { return array( 'level' => $p[0], 'text' => \expNotificationService::problemText( $p ) ); }, $s['problems'] );
            $cli->output( json_encode( $s, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
            $this->shutdown( $errors ? 1 : 0 );
            return;
        }

        $when = function ( $t ) { return $t ? date( 'Y-m-d H:i:s', $t ) : 'unknown'; };
        $cli->output( 'Events' );
        $cli->output( sprintf( '  pending                  %d%s', $s['pending_total'],
                               $s['pending'] ? '   (' . implode( ', ', array_map( function ( $k, $v ) { return "$k $v"; }, array_keys( $s['pending'] ), $s['pending'] ) ) . ')' : '' ) );
        if ( $s['oldest_pending'] )
            $cli->output( '  oldest pending           ' . $when( $s['oldest_pending'] ) );
        $cli->output( sprintf( '  handled, kept            %d   (%d with nothing left to send)', $s['handled_kept'], $s['handled_orphans'] ) );
        $cli->output( 'Messages' );
        $cli->output( sprintf( '  collections              %d', $s['collections'] ) );
        $cli->output( sprintf( '  items to send now        %d', $s['items_now'] ) );
        $cli->output( sprintf( '  items kept for a digest  %d   (%d due)', $s['items_digest'], $s['items_due'] ) );
        $cli->output( 'Subscriptions' );
        $cli->output( sprintf( '  subtree                  %d by %d users', $s['subscriptions'], $s['subscribers'] ) );
        $cli->output( sprintf( '  collaboration rules      %d', $s['collab_rules'] ) );
        $cli->output( sprintf( '  digest                   daily %d, weekly %d, monthly %d', $s['digest']['daily'], $s['digest']['weekly'], $s['digest']['monthly'] ) );
        $cli->output( 'Mail' );
        $cli->output( sprintf( '  transport                %s   sender %s', $s['transport'], $s['sender'] ) );
        $cli->output( sprintf( '  last 24 hours            %d messages to %d recipients in %d runs', $s['sent_24h']['mails'], $s['sent_24h']['recipients'], $s['sent_24h']['runs'] ) );
        $cli->output( 'Handlers                    ' . implode( ', ', $s['handlers'] ) );
        $cli->output( 'Runs' );
        if ( $s['running'] )
            $cli->output( sprintf( '  running now              process %d since %s', $s['running']['pid'], $when( $s['running']['since'] ) ) );
        if ( !$s['runs'] )
            $cli->output( '  none recorded' );
        foreach ( $s['runs'] as $run )
            $cli->output( sprintf( '  %s  %-8s %-6s %-6s events %d, removed %d, kept %d, messages %d, %d ms%s', $when( $run['time'] ), $run['source'],
                                   !empty( $run['dry'] ) ? 'dry' : '', $run['result'], $run['events'], $run['removed'], $run['kept'], $run['mails'], $run['ms'],
                                   $run['error'] !== '' ? '  ' . $run['error'] : '' ) );
        $cli->output( 'Problems' );
        if ( !$s['problems'] )
            $cli->output( '  none' );
        foreach ( $s['problems'] as $p )
            $cli->output( sprintf( '  [%s] %s', $p[0], \expNotificationService::problemText( $p ) ) );
        $cli->output( $errors ? 'FAIL: ' . $errors . ' problem(s) of level error' : 'PASS' );
        $this->shutdown( $errors ? 1 : 0 );
    }
}

}
