<?php
/**
 * @description The suppression list of the e-mail gate: list, check, add and lift entries (only hashes are stored)
 * @alias exp:mail:suppression
 *
 * File containing the exp:mail:suppression command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Mailsuppression extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "E-mail suppression list\n" .
                                 "No optional mail goes to a suppressed address. The table holds a salted sha256 hash of each\n" .
                                 "address, never the address: an entry is found by entering the address.\n" .
                                 "  list                      the entries: hash (start), reason, date, note\n" .
                                 "  check                     is --email suppressed, and why\n" .
                                 "  add                       suppress --email with --reason (bounce, complaint, unsubscribe_all, legal, admin, bridge)\n" .
                                 "  lift                      lift the entry of --email or --hash\n" .
                                 "\n" .
                                 "./console exp:mail:suppression list --reason=bounce\n" .
                                 "./console exp:mail:suppression add --email=someone@example.com --reason=legal --note=\"Request of 2026-10-04\"\n" .
                                 "./console exp:mail:suppression lift --hash=3f2a...",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[email:][hash:][reason:][note:][limit:][offset:][json]', '',
            array( 'email'  => 'the address (check, add, lift)',
                   'hash'   => 'lift: the hash of an entry (list shows its start; the whole hash is in --json)',
                   'reason' => 'add: the reason; list: only this reason',
                   'note'   => 'add: a note for the admins (the address is removed from it)',
                   'limit'  => 'list: how many (default 50)',
                   'offset' => 'list: skip this many',
                   'json'   => 'list: JSON' ) );
        $args = isset( $options['arguments'] ) ? array_values( $options['arguments'] ) : array();
        $action = $args ? strtolower( (string)$args[0] ) : 'list';
        $email = isset( $options['email'] ) && $options['email'] ? (string)$options['email'] : '';
        try
        {
            switch ( $action )
            {
                case 'list':
                    $reason = !empty( $options['reason'] ) ? (string)$options['reason'] : null;
                    $rows = \expMailSuppression::fetchList( !empty( $options['offset'] ) ? (int)$options['offset'] : 0, !empty( $options['limit'] ) ? (int)$options['limit'] : 50, $reason );
                    $out = array();
                    foreach ( $rows as $r )
                        $out[] = array( 'hash' => $r->attribute( 'email_hash' ), 'reason' => $r->attribute( 'reason' ), 'created' => date( 'Y-m-d H:i', (int)$r->attribute( 'created' ) ),
                                        'created_by' => (int)$r->attribute( 'created_by' ), 'note' => (string)$r->attribute( 'note' ) );
                    if ( !empty( $options['json'] ) )
                        $cli->output( json_encode( array( 'total' => \expMailSuppression::countList( $reason ), 'rows' => $out ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
                    else
                    {
                        $cli->output( sprintf( '%d entries%s', \expMailSuppression::countList( $reason ), $reason ? " ($reason)" : '' ) );
                        foreach ( $out as $o )
                            $cli->output( sprintf( '  %s…  %-16s %s  %s', substr( $o['hash'], 0, 16 ), $o['reason'], $o['created'], $o['note'] ) );
                    }
                    break;
                case 'check':
                    if ( $email === '' )
                        throw new \InvalidArgumentException( 'check needs --email' );
                    $reason = \expMailSuppression::reason( $email );
                    $cli->output( $reason === null ? 'not suppressed' : 'suppressed: ' . $reason . ' (hash ' . substr( \expMailSuppression::hash( $email ), 0, 16 ) . '…)' );
                    break;
                case 'add':
                    if ( $email === '' || empty( $options['reason'] ) )
                        throw new \InvalidArgumentException( 'add needs --email and --reason' );
                    \expMailSuppression::add( $email, (string)$options['reason'], isset( $options['note'] ) ? (string)$options['note'] : 'console' );
                    $recipient = \expMailRecipient::fromAddress( $email );
                    if ( $recipient )
                        \expConsentLog::record( $recipient, '', 'suppress', '', (string)$options['reason'], \expConsentContext::system( 'Suppressed on the console', 'admin' ) );
                    $cli->output( 'suppressed (hash ' . substr( \expMailSuppression::hash( $email ), 0, 16 ) . '…)' );
                    break;
                case 'lift':
                    $key = $email !== '' ? $email : ( isset( $options['hash'] ) ? (string)$options['hash'] : '' );
                    if ( $key === '' )
                        throw new \InvalidArgumentException( 'lift needs --email or --hash' );
                    if ( !\expMailSuppression::lift( $key ) )
                        throw new \RuntimeException( 'no such entry' );
                    if ( $email !== '' && ( $recipient = \expMailRecipient::fromAddress( $email ) ) )
                        \expConsentLog::record( $recipient, '', 'unsuppress', '', '', \expConsentContext::system( 'Lifted on the console', 'admin' ) );
                    $cli->output( 'lifted' );
                    break;
                default:
                    throw new \InvalidArgumentException( "unknown action '$action' (list, check, add, lift)" );
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
