<?php
/**
 * File containing the eZFileTransport class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZFileTransport ezfiletransport.php
  \brief Sends the email message to a file.

*/

class eZFileTransport extends eZMailTransport
{
    function sendMail( eZMail $mail )
    {
        $ini = eZINI::instance();
        $sendmailOptions = '';
        $emailFrom = $mail->sender();
        $emailSender = $emailFrom['email'];
        if ( !$emailSender || ( is_array( $emailSender ) && count( $emailSender) <= 0 ) )
            $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
        if ( !$emailSender )
            $emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );
        if ( !eZMail::validate( $emailSender ) )
            $emailSender = false;

        $filename = time() . '-' . mt_rand() . '.mail';

        $data = preg_replace('/(\r\n|\r|\n)/', "\r\n", $mail->headerText() . "\n" . $mail->body() );
        // [MailSettings] FileTransportDirectory (default var/log/mail), so a test or a trial run can keep the
        // mail it makes somewhere of its own
        $directory = 'var/log/mail';
        if ( $ini->hasVariable( 'MailSettings', 'FileTransportDirectory' ) &&
             trim( $ini->variable( 'MailSettings', 'FileTransportDirectory' ) ) !== '' )
            $directory = rtrim( trim( $ini->variable( 'MailSettings', 'FileTransportDirectory' ) ), '/' );
        $returnedValue = eZFile::create( $filename, $directory, $data );
        if ( $returnedValue === false )
        {
            eZDebug::writeError( 'An error occurred writing the e-mail file in ' . $directory, __METHOD__ );
        }

        return $returnedValue;
    }
}

?>
