<?php
/**
 * @description Run the notification filter: send the notifications of the pending events and the due digests (--dry-run: only list them)
 * @alias exp:notification:run
 * @alias exp:notify:run
 *
 * File containing the exp:notification:run command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Notificationrun extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "Run the notifications\n" .
                                 "One pass of the notification cronjob part: a time event is made, every pending event goes through\n" .
                                 "every handler (subtree, collaboration, general digest), messages are sent or kept for a digest,\n" .
                                 "events nothing waits for are removed. The run is locked (a second one is refused while one runs),\n" .
                                 "recorded (exp:notification:status shows it) and audited.\n" .
                                 "\n" .
                                 "--dry-run lists what would be sent (subject, number of recipients, masked addresses) and changes\n" .
                                 "nothing: the pass runs in a transaction that is rolled back and no mail is handed to the transport.\n" .
                                 "--mail-file-dir=<dir> forces the file transport for this process: the mail is written to <dir>\n" .
                                 "instead of being sent, whatever site.ini says. Use it for every trial.\n" .
                                 "--at=<time> sets the time of the time event (a date or timestamp), to try digest windows.\n" .
                                 "\n" .
                                 "./console exp:notification:run --dry-run\n" .
                                 "./console exp:notification:run --mail-file-dir=var/tmp/notification-mail\n" .
                                 "./console exp:notification:run",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup(
            '[dry-run][at:][event:][no-time-event][mail-file-dir:][addresses][json][job:][source:]', '',
            array( 'dry-run'        => 'List what would be sent and change nothing',
                   'at'             => 'The time of the time event: a date (2026-10-05 08:00) or a timestamp, default now',
                   'event'          => 'Only these events (ids, comma separated); no time event is made then',
                   'no-time-event'  => 'Make no time event: only the pending events are handled (no digest is due because of this run)',
                   'mail-file-dir'  => 'Write the mail to files in this directory instead of sending it (this process only)',
                   'addresses'      => 'Show whole recipient addresses in the dry run (they are masked by default)',
                   'json'           => 'One JSON object instead of the text',
                   'job'            => 'The id of a background job whose progress file is written (the status page uses this)',
                   'source'         => 'console (default) or web; what the record of the run says' ) );

        $job = !empty( $options['job'] ) && \expNotificationJob::isID( $options['job'] ) ? $options['job'] : false;
        $say = function ( $text ) use ( $cli, $job, $options ) {
            if ( empty( $options['json'] ) )
                $cli->output( $text );
            if ( $job )
                \expNotificationJob::emit( $job, array( 'type' => 'log', 'message' => $text ) );
        };
        $finish = function ( $code, $result ) use ( $job ) {
            if ( $job )
                \expNotificationJob::emit( $job, array( 'type' => 'end', 'result' => $result ) );
            $this->shutdown( $code );
        };

        // Mail kept local: the file transport, in this process only (nothing is written to a settings file)
        if ( !empty( $options['mail-file-dir'] ) )
        {
            $ini = \eZINI::instance();
            $ini->setVariable( 'MailSettings', 'Transport', 'file' );
            $ini->setVariable( 'MailSettings', 'FileTransportDirectory', rtrim( $options['mail-file-dir'], '/' ) );
            $say( 'Mail goes to files in ' . rtrim( $options['mail-file-dir'], '/' ) . ' (file transport, this process only).' );
        }

        $run = array( 'source' => !empty( $options['source'] ) && $options['source'] === 'web' ? 'web' : 'console' );
        if ( !empty( $options['no-time-event'] ) )
            $run['time_event'] = false;
        if ( !empty( $options['event'] ) )
        {
            $run['events'] = array_values( array_filter( array_map( 'intval', explode( ',', $options['event'] ) ) ) );
            if ( !$run['events'] )
            {
                $cli->error( 'FAIL: --event needs event ids' );
                $finish( 2, 'failed' );
                return;
            }
        }
        if ( !empty( $options['at'] ) )
        {
            $at = ctype_digit( (string)$options['at'] ) ? (int)$options['at'] : strtotime( $options['at'] );
            if ( !$at )
            {
                $cli->error( 'FAIL: --at needs a date or a timestamp' );
                $finish( 2, 'failed' );
                return;
            }
            $run['at'] = $at;
            $say( 'Time event at ' . date( 'Y-m-d H:i:s', $at ) . '.' );
        }

        if ( !empty( $options['dry-run'] ) )
        {
            $say( 'Dry run: nothing is sent, marked or removed.' );
            $plan = \expNotificationService::plan( $run );
            if ( $plan['result'] !== 'ok' )
            {
                $cli->error( 'FAIL: ' . $plan['error'] );
                $finish( 1, 'failed' );
                return;
            }
            if ( !empty( $options['json'] ) )
            {
                foreach ( $plan['mails'] as &$m )
                {
                    if ( empty( $options['addresses'] ) )
                        unset( $m['raw'] );
                }
                unset( $m );
                $cli->output( json_encode( $plan, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
            }
            else
            {
                foreach ( $plan['mails'] as $m )
                    $say( sprintf( '  would send "%s" to %d recipient(s): %s', $m['subject'], $m['to'],
                                   implode( ', ', !empty( $options['addresses'] ) ? $m['raw'] : $m['addresses'] ) ) );
                $say( sprintf( 'PASS: dry run, %d event(s) handled, %d message(s) would be sent, %d event(s) would be kept for a digest.',
                               $plan['events'], count( $plan['mails'] ), $plan['kept'] ) );
            }
            $finish( 0, 'ok' );
            return;
        }

        $say( 'Starting notification event processing' );
        $result = \expNotificationService::run( $run );
        if ( !empty( $options['json'] ) )
            $cli->output( json_encode( $result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
        if ( $result['result'] === 'busy' )
        {
            $cli->error( 'FAIL: ' . $result['error'] );
            $finish( 1, 'busy' );
            return;
        }
        if ( $result['result'] !== 'ok' )
        {
            $cli->error( 'FAIL: ' . $result['error'] );
            $finish( 1, 'failed' );
            return;
        }
        $say( sprintf( 'PASS: %d event(s) handled, %d removed, %d kept for a digest, %d message(s) to %d recipient(s)%s, %d ms.',
                       $result['events'], $result['removed'], $result['kept'], $result['mails'], $result['recipients'],
                       $result['failed'] ? ', ' . $result['failed'] . ' handler failure(s)' : '', $result['ms'] ) );
        $finish( 0, 'ok' );
    }
}

}
