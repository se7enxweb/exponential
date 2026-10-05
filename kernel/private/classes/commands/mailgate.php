<?php
/**
 * @description Test the mail gate: who of the given addresses would get a mail of a category, and what the decorated mail looks like
 * @alias exp:mail:gate
 *
 * File containing the exp:mail:gate command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Mailgate extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "Mail gate test\n" .
                                 "Says for each address what the gate would decide for a mail of --category (allow, off, pending,\n" .
                                 "master_off, suppressed, unknown_category). With --write the mail goes through the gate with the\n" .
                                 "transport forced to \"file\" in this process: it is written to --mail-file-dir (default\n" .
                                 "var/tmp/mailgate-test), never sent. Addresses are hidden unless --addresses.\n" .
                                 "\n" .
                                 "./console exp:mail:gate --category=newsletter --to=a@example.com,b@example.com\n" .
                                 "./console exp:mail:gate --category=content --to=editor@example.com --write --html",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[category:][to:][write][html][mail-file-dir:][addresses]', '',
            array( 'category' => 'the category of the test mail (empty: a mail without category)', 'to' => 'addresses, comma separated',
                   'write' => 'send the test mail through the gate with the file transport', 'html' => 'an HTML test mail',
                   'mail-file-dir' => 'where --write puts the mail', 'addresses' => 'show the addresses' ) );
        $to = array_filter( array_map( 'trim', explode( ',', isset( $options['to'] ) ? (string)$options['to'] : '' ) ), 'strlen' );
        if ( !$to )
        {
            $cli->error( 'FAIL: --to is needed' );
            $this->shutdown( 1 );
            return;
        }
        $show = function ( $e ) use ( $options ) { return !empty( $options['addresses'] ) ? $e : \expMailPreferencesService::maskAddress( $e ); };
        $ini = \eZINI::instance();
        $mail = new \eZMail();
        $sender = $ini->variable( 'MailSettings', 'EmailSender' ) ?: $ini->variable( 'MailSettings', 'AdminEmail' );
        $mail->setSender( $sender );
        foreach ( $to as $t )
            $mail->addBcc( $t );
        $mail->setSubject( 'Mail gate test' );
        if ( !empty( $options['html'] ) )
        {
            $mail->setContentType( 'text/html' );
            $mail->setBody( "<html><body><p>This is a test of the mail gate.</p></body></html>" );
        }
        else
            $mail->setBody( "This is a test of the mail gate.\n" );
        if ( !empty( $options['category'] ) )
            $mail->setCategory( (string)$options['category'] );

        $check = \expMailGate::check( $mail );
        $cli->output( sprintf( 'Category %s: %s', $check['category'] !== null ? $check['category'] : '(none)', $check['reason'] ) );
        foreach ( $check['recipients'] as $r )
            $cli->output( sprintf( '  %-32s %s', $show( $r['email'] ), $r['decision'] ) );

        if ( !empty( $options['write'] ) )
        {
            $dir = !empty( $options['mail-file-dir'] ) ? rtrim( (string)$options['mail-file-dir'], '/' ) : 'var/tmp/mailgate-test';
            $ini->setVariable( 'MailSettings', 'Transport', 'file' );
            $ini->setVariable( 'MailSettings', 'FileTransportDirectory', $dir );
            if ( trim( $ini->variable( 'MailSettings', 'Transport' ) ) !== 'file' )
            {
                $cli->error( 'FAIL: the transport could not be forced to file; nothing sent' );
                $this->shutdown( 1 );
                return;
            }
            $before = glob( $dir . '/*.mail' ) ?: array();
            $ok = \eZMailTransport::send( $mail );
            $after = array_values( array_diff( glob( $dir . '/*.mail' ) ?: array(), $before ) );
            $result = \expMailGate::lastResult();
            $cli->output( sprintf( 'Written: %d file(s) in %s, decision %s, result %s', count( $after ), $dir, $result['decision'], $ok ? 'true' : 'false' ) );
            foreach ( $after as $f )
                $cli->output( '  ' . $f );
        }
        $cli->output( 'PASS' );
        $this->shutdown( 0 );
    }
}

}
