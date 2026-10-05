<?php
/**
 * @description List the notification events, or remove the old ones
 * @alias exp:notification:events
 * @alias exp:notify:events
 *
 * File containing the exp:notification:events command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Notificationevents extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "Notification events\n" .
                                 "  list                      the newest events: id, status, type, age, messages waiting\n" .
                                 "  cleanup                   remove the handled events nothing waits for, and with --older-than the\n" .
                                 "                            events (and their messages) older than that\n" .
                                 "\n" .
                                 "An event has no date of its own; its age is read from what it is about (the published version, the\n" .
                                 "collaboration item, the time of a time event). Events whose age cannot be told, because the content\n" .
                                 "is gone, are listed as unknown and removed only with --include-unknown.\n" .
                                 "\n" .
                                 "./console exp:notification:events list --status=pending --limit=20\n" .
                                 "./console exp:notification:events cleanup --older-than=30d --dry-run\n" .
                                 "./console exp:notification:events cleanup --older-than=30d",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup(
            '[status:][limit:][older-than:][include-unknown][dry-run][json]', '',
            array( 'status'          => 'list, cleanup --older-than: pending or handled (default both)',
                   'limit'           => 'list: how many (default 50)',
                   'older-than'      => 'cleanup: 90s, 15m, 12h, 30d, 2w or a date (2026-09-01)',
                   'include-unknown' => 'cleanup: also remove events whose age cannot be told',
                   'dry-run'         => 'cleanup: count, remove nothing',
                   'json'            => 'list: JSON' ) );
        $args = isset( $options['arguments'] ) ? array_values( $options['arguments'] ) : array();
        $action = $args ? strtolower( (string)$args[0] ) : 'list';
        $status = null;
        if ( !empty( $options['status'] ) )
        {
            if ( $options['status'] === 'pending' )
                $status = \eZNotificationEvent::STATUS_CREATED;
            else if ( $options['status'] === 'handled' )
                $status = \eZNotificationEvent::STATUS_HANDLED;
            else
            {
                $cli->error( 'FAIL: --status is pending or handled' );
                $this->shutdown( 2 );
                return;
            }
        }

        if ( $action === 'list' )
        {
            $rows = \expNotificationService::eventsReport( $status, !empty( $options['limit'] ) ? (int)$options['limit'] : 50 );
            if ( !empty( $options['json'] ) )
            {
                $cli->output( json_encode( $rows, JSON_PRETTY_PRINT ) );
                $this->shutdown( 0 );
                return;
            }
            foreach ( $rows as $row )
                $cli->output( sprintf( '%7d  %-8s %-16s %-20s %d message(s) waiting', $row['id'], $row['status'], $row['type'],
                                       $row['created'] ? date( 'Y-m-d H:i:s', $row['created'] ) : 'age unknown', $row['items'] ) );
            $cli->output( sprintf( 'PASS: %d event(s) shown', count( $rows ) ) );
            $this->shutdown( 0 );
            return;
        }

        if ( $action === 'cleanup' )
        {
            $age = null;
            if ( isset( $options['older-than'] ) && $options['older-than'] !== null && $options['older-than'] !== false )
            {
                $age = \expNotificationService::parseAge( $options['older-than'] );
                if ( $age === false )
                {
                    $cli->error( 'FAIL: --older-than is a number of days, or 90s, 15m, 12h, 30d, 2w, or a date' );
                    $this->shutdown( 2 );
                    return;
                }
            }
            $dry = !empty( $options['dry-run'] );
            $r = \expNotificationService::cleanup( $age, $status, !empty( $options['include-unknown'] ), $dry );
            $cli->output( sprintf( '%s%d handled event(s) with nothing left to send', $dry ? 'Would remove: ' : 'Removed: ', $r['handled'] ) );
            if ( $age !== null )
                $cli->output( sprintf( '%s%d event(s) older than %s (%d newer kept, %d of unknown age %s)', $dry ? 'Would remove: ' : 'Removed: ',
                                       $r['removed'], $options['older-than'], $r['kept'], $r['unknown'],
                                       !empty( $options['include-unknown'] ) ? 'included' : 'left alone' ) );
            $cli->output( 'PASS' );
            $this->shutdown( 0 );
            return;
        }

        $cli->error( 'FAIL: the actions are list and cleanup' );
        $this->shutdown( 2 );
    }
}

}
