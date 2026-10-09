<?php
/**
 * @description The consent log of the e-mail preferences: list, export as CSV, retention cleanup
 * @alias exp:mail:consent
 *
 * File containing the exp:mail:consent command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Mailconsent extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "E-mail consent log\n" .
                                 "  list                      the newest rows (time, person, category, action, old -> new, source)\n" .
                                 "  export                    the rows as CSV (--output, or the screen); the CSV holds addresses and IPs\n" .
                                 "  cleanup                   retention: remove the rows of people who are gone after RetentionDays, expired\n" .
                                 "                            confirmations and old suppression entries (--dry-run counts)\n" .
                                 "Filters: --user, --email, --category, --action, --source, --from, --to (YYYY-MM-DD).\n" .
                                 "\n" .
                                 "./console exp:mail:consent list --category=newsletter --limit=20\n" .
                                 "./console exp:mail:consent export --from=2026-01-01 --output=var/tmp/consent.csv\n" .
                                 "./console exp:mail:consent cleanup --dry-run",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[user:][email:][category:][action:][source:][from:][to:][limit:][offset:][output:][dry-run][addresses]', '',
            array( 'user' => 'a login or user id', 'email' => 'an address', 'category' => 'a category identifier', 'action' => 'on, off, pending, confirm, ...',
                   'source' => 'page, link, admin, import, signup, bridge, confirm, system', 'from' => 'from this day', 'to' => 'up to this day',
                   'limit' => 'list: how many (default 50)', 'offset' => 'list: skip this many', 'output' => 'export: the file (mode 0600)',
                   'dry-run' => 'cleanup: count only', 'addresses' => 'list: show addresses instead of hiding them' ) );
        $args = isset( $options['arguments'] ) ? array_values( $options['arguments'] ) : array();
        $action = $args ? strtolower( (string)$args[0] ) : 'list';
        try
        {
            $filters = array();
            if ( !empty( $options['user'] ) )
            {
                $r = \expMailPreferencesService::recipientFor( $options['user'], null );
                if ( !$r )
                    throw new \InvalidArgumentException( 'no such user: ' . $options['user'] );
                $filters['user_id'] = $r->userId();
            }
            if ( !empty( $options['email'] ) )
                $filters['email'] = (string)$options['email'];
            foreach ( array( 'category', 'action', 'source' ) as $f )
                if ( !empty( $options[$f] ) )
                    $filters[$f] = (string)$options[$f];
            if ( !empty( $options['from'] ) )
                $filters['from'] = strtotime( $options['from'] . ' 00:00:00' );
            if ( !empty( $options['to'] ) )
                $filters['to'] = strtotime( $options['to'] . ' 23:59:59' );

            switch ( $action )
            {
                case 'list':
                    $cli->output( \expConsentLog::countList( $filters ) . ' rows' );
                    foreach ( \expConsentLog::fetchList( $filters, !empty( $options['offset'] ) ? (int)$options['offset'] : 0, !empty( $options['limit'] ) ? (int)$options['limit'] : 50 ) as $row )
                    {
                        $email = (string)$row->attribute( 'email' );
                        $who = (int)$row->attribute( 'user_id' ) > 0 ? 'user ' . $row->attribute( 'user_id' ) : ( $email !== '' ? ( !empty( $options['addresses'] ) ? $email : \expMailPreferencesService::maskAddress( $email ) ) : substr( $row->attribute( 'recipient_key' ), 0, 14 ) );
                        $cli->output( sprintf( '  %s  %-22s %-14s %-11s %s -> %s  %s%s', date( 'Y-m-d H:i:s', (int)$row->attribute( 'created' ) ), $who,
                                               $row->attribute( 'category' ) !== '' ? $row->attribute( 'category' ) : '-', $row->attribute( 'action' ),
                                               $row->attribute( 'old_value' ) !== '' ? $row->attribute( 'old_value' ) : '-', $row->attribute( 'new_value' ) !== '' ? $row->attribute( 'new_value' ) : '-',
                                               $row->attribute( 'source' ), (int)$row->attribute( 'actor_user_id' ) > 0 ? ' by user ' . $row->attribute( 'actor_user_id' ) : '' ) );
                    }
                    break;
                case 'export':
                    $csv = \expConsentLog::exportCsv( $filters );
                    if ( !empty( $options['output'] ) )
                    {
                        if ( file_put_contents( (string)$options['output'], $csv ) === false )
                            throw new \RuntimeException( 'cannot write ' . $options['output'] );
                        @chmod( (string)$options['output'], \eZFile::fileMode( 0600 ) );
                        $cli->output( sprintf( 'written to %s (%d rows, mode 0600)', $options['output'], max( 0, substr_count( $csv, "\n" ) - 1 ) ) );
                    }
                    else
                        $cli->output( rtrim( $csv, "\n" ) );
                    if ( class_exists( 'expAudit' ) )
                        \expAudit::event( 'data.export.csv', array( 'object' => array( 'type' => 'consent_log', 'id' => 'expmail_consent_log' ), 'x' => array( 'filters' => array_keys( $filters ) ) ) );
                    break;
                case 'cleanup':
                    $r = \expConsentLog::cleanup( !empty( $options['dry-run'] ) );
                    $cli->output( sprintf( '%s: %d consent log rows, %d expired confirmations%s', !empty( $options['dry-run'] ) ? 'Would remove' : 'Removed',
                                           $r['consent'], $r['pending'], !empty( $options['dry-run'] ) ? '' : ', ' . $r['suppression'] . ' suppression entries' ) );
                    break;
                default:
                    throw new \InvalidArgumentException( "unknown action '$action' (list, export, cleanup)" );
            }
        }
        catch ( \Throwable $e )
        {
            $cli->error( 'FAIL: ' . $e->getMessage() );
            $this->shutdown( 1 );
            return;
        }
        $cli->output( 'PASS' );
        $this->shutdown( 0 );
    }
}

}
