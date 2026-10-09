<?php
/**
 * Child process of eZSendmailEnvelopeSenderTest: sends one mail through eZSendmailTransport with
 * SendmailEnvelopeSender set to the first argument ("-" removes the setting), when the second argument is a non-empty
 * address that sender on the mail, EmailSender set to the third argument (default site@example.com) and, when the
 * fourth argument is not empty, a From extra header with it (eZMail::addExtraHeader()).
 * SendmailOptions[] is -r bounce@example.com. sendmail_path points to sendmail_capture.php. Prints "sent" or "failed",
 * with EZSENDMAIL_REPORT_SENDER=1 in the environment followed by a line with the mail's sender after sending (JSON).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

// Nothing may reach a real sendmail: the test hands this process a capturing sendmail_path, and without it nothing
// is sent.
if ( strpos( (string)ini_get( 'sendmail_path' ), 'sendmail_capture.php' ) === false )
{
    fwrite( STDERR, "sendmail_path is not the capturing script, nothing is sent\n" );
    exit( 2 );
}

chdir( dirname( __DIR__, 5 ) );
require 'autoload.php';
// The transport of this tree, also where the autoloader maps classes to another checkout (a worktree whose vendor/
// is a link)
require_once 'lib/ezutils/classes/ezsendmailtransport.php';

$ini = eZINI::instance();
$ini->setVariable( 'MailSettings', 'Transport', 'sendmail' );
if ( $argv[1] === '-' )
    $ini->removeSetting( 'MailSettings', 'SendmailEnvelopeSender' );
else
    $ini->setVariable( 'MailSettings', 'SendmailEnvelopeSender', $argv[1] );
$ini->setVariable( 'MailSettings', 'SendmailOptions', array( '-r', 'bounce@example.com' ) );
$ini->setVariable( 'MailSettings', 'EmailSender', isset( $argv[3] ) ? $argv[3] : 'site@example.com' );
$ini->setVariable( 'MailSettings', 'AdminEmail', 'admin@example.com' );
$ini->setVariable( 'MailSettings', 'DebugSending', 'disabled' );

$mail = new eZMail();
if ( isset( $argv[2] ) && $argv[2] !== '' )
    $mail->setSender( $argv[2], 'Editor' );
if ( isset( $argv[4] ) && $argv[4] !== '' )
    $mail->addExtraHeader( 'From', $argv[4] );
$mail->addReceiver( 'reader@example.com' );
$mail->setSubject( 'Envelope test' );
$mail->setBody( 'Body' );

$transport = new eZSendmailTransport();
echo $transport->sendMail( $mail ) ? 'sent' : 'failed';
// The sender of the mail after sending, as the caller sees it
if ( getenv( 'EZSENDMAIL_REPORT_SENDER' ) === '1' )
    echo "\n", json_encode( array( 'From' => $mail->sender( false ), 'from' => $mail->Mail->from === null ? null : $mail->Mail->from->email ) );
