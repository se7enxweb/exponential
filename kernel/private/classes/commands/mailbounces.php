<?php
/**
 * @description Read the bounce mailbox: hard bounces and spam complaints go on the suppression list
 * @alias exp:mail:bounces
 *
 * File containing the exp:mail:bounces command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Mailbounces extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "Bounce mailbox\n" .
                                 "Reads the mailbox of mailpreferences.ini [BounceSettings] (IMAP or POP3, set in a settings\n" .
                                 "override): hard bounces (delivery status notifications with a permanent failure of\n" .
                                 "HardStatusCodes[]) and spam complaints (feedback loop reports) put the address on the\n" .
                                 "suppression list. Soft bounces and other mail are counted only. No address is shown in full.\n" .
                                 "\n" .
                                 "./console exp:mail:bounces                      read the mailbox\n" .
                                 "./console exp:mail:bounces --dry-run            show what it would do; nothing is changed\n" .
                                 "./console exp:mail:bounces --file=dsn.eml       read saved messages (a file or a directory of .eml)\n" .
                                 "./console exp:mail:bounces --status             the last run\n" .
                                 "\n" .
                                 "Ends with PASS, SKIP (the reader is disabled or not configured) or FAIL (exit 1).",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[dry-run][file:][status][json]', '',
            array( 'dry-run' => 'Report only: nothing is suppressed, no message is changed, the status is not written',
                   'file'    => 'Read these saved messages (.eml file, or a directory of them) instead of the mailbox',
                   'status'  => 'Show the last run and stop',
                   'json'    => 'One JSON object instead of the text' ) );
        $dryRun = !empty( $options['dry-run'] );
        try
        {
            if ( !empty( $options['status'] ) )
            {
                $s = \expMailBounceReader::status();
                if ( !empty( $options['json'] ) )
                    $cli->output( json_encode( $s, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
                else
                    $this->printStatus( $s );
                $this->shutdown( 0 );
                return;
            }
            if ( !\expMailPreferencesService::tableExists( 'expmail_suppression' ) )
            {
                $cli->error( 'FAIL: the e-mail preference tables are not installed (database update)' );
                $this->shutdown( 1 );
                return;
            }
            $json = !empty( $options['json'] );
            $report = $json ? null : function ( $line ) use ( $cli ) { $cli->output( '  ' . $line ); };
            $files = !empty( $options['file'] ) ? (array)$options['file'] : array();
            if ( $files )
                $r = \expMailBounceReader::runFiles( $files, $dryRun, $report );
            else
            {
                if ( !$json )
                {
                    $s = \expMailBounceReader::status();
                    $cli->output( sprintf( 'Reading %s %s%s%s', strtoupper( $s['protocol'] ), $s['server'] !== '' ? $s['server'] : '(no server)',
                                           $s['protocol'] === 'imap' ? ' folder ' . $s['folder'] : '', $dryRun ? ' (dry run)' : '' ) );
                }
                $r = \expMailBounceReader::run( $dryRun, $report );
            }
        }
        catch ( \Throwable $e )
        {
            $cli->error( 'FAIL: ' . $e->getMessage() );
            $this->shutdown( 1 );
            return;
        }
        if ( !empty( $options['json'] ) )
        {
            $cli->output( json_encode( $r + array( 'dry_run' => $dryRun ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
            $this->shutdown( $r['ok'] ? 0 : 1 );
            return;
        }
        if ( $r['skipped'] !== '' )
        {
            $cli->output( $r['skipped'] === 'not_configured'
                ? 'SKIP: no bounce mailbox is configured (mailpreferences.ini [BounceSettings] Server, in a settings override)'
                : 'SKIP: the bounce reader is disabled ([BounceSettings] Reader=disabled)' );
            $this->shutdown( 0 );
            return;
        }
        $c = $r['counts'];
        $cli->output( sprintf( '%d messages: %d hard bounces, %d soft bounces, %d complaints, %d other%s',
                               $c['messages'], $c['hard'], $c['soft'], $c['complaints'], $c['other'], $dryRun ? '' : '; ' . $c['suppressed'] . ' addresses newly suppressed' ) );
        if ( $dryRun )
        {
            $would = 0;
            foreach ( $r['items'] as $item )
                if ( $item['kind'] === 'hard' || $item['kind'] === 'complaint' )
                    $would += count( $item['addresses'] );
            $cli->output( sprintf( 'Dry run: %d address(es) reported by bounces and complaints; nothing was changed', $would ) );
        }
        if ( !$r['ok'] )
        {
            $cli->error( 'FAIL: ' . $r['error'] );
            $this->shutdown( 1 );
            return;
        }
        $cli->output( 'PASS' );
        $this->shutdown( 0 );
    }

    protected function printStatus( array $s )
    {
        $cli = $this->cli();
        $cli->output( sprintf( '  %-24s %s', 'reader', $s['enabled'] ? 'enabled' : ( $s['configured'] ? 'disabled' : 'not configured' ) ) );
        $cli->output( sprintf( '  %-24s %s %s', 'mailbox', strtoupper( $s['protocol'] ), $s['server'] !== '' ? $s['server'] : '(none)' ) );
        $cli->output( sprintf( '  %-24s %s', 'last read', $s['last_read'] ? date( 'Y-m-d H:i', $s['last_read'] ) : 'never' ) );
        if ( $s['last_error'] !== '' )
            $cli->output( sprintf( '  %-24s %s', 'last error', $s['last_error'] ) );
        foreach ( array( 'last run' => $s['last'], 'all runs' => $s['total'] ) as $label => $c )
            $cli->output( sprintf( '  %-24s %d messages, %d hard, %d soft, %d complaints, %d suppressed', $label,
                                   $c['messages'], $c['hard'], $c['soft'], $c['complaints'], $c['suppressed'] ) );
    }
}

}
