<?php
/**
 * File containing the eZSendmailTransport class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZSendmailTransport ezsendmailtransport.php
  \brief Sends the email message to sendmail which takes care of sending the actual message.

  Uses the mail() function in PHP to pass the email to the sendmail system.

*/

class eZSendmailTransport extends eZMailTransport
{
    function sendMail( eZMail $mail )
    {
        $ini = eZINI::instance();
        $emailFrom = $mail->sender();
        $emailSender = isset( $emailFrom['email'] ) ? $emailFrom['email'] : false;
        if ( !$emailSender || ( is_countable( $emailSender ) && count( $emailSender) <= 0 ) )
            $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
        if ( !$emailSender )
            $emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );
        if ( !eZMail::validate( $emailSender ) )
            $emailSender = false;

        $useEnvelopeSender = static::useEnvelopeSender();
        $sendmailOptions = $this->sendmailOptions( $emailSender, $useEnvelopeSender );

        if( function_exists( 'mail' ) )
        {
            $message = $mail->body();
            $sys = eZSys::instance();
            $excludeHeaders = array( 'Subject' );
            // If not Windows PHP mail() implementation, we can not specify a To: header in the $additional_headers parameter,
            // because then there will be 2 To: headers in the resulting e-mail.
            // However, we can use "undisclosed-recipients:;" in $to.
            if ( $sys->osType() != 'win32' )
            {
                $excludeHeaders[] = 'To';
                $insertUndisclosedRecipient = $ini->variable( 'MailSettings', 'SendmailInsertUndisclosedRecipient' );
                $recipientText = $insertUndisclosedRecipient == 'disabled' ? '' : 'undisclosed-recipients:;';
                $receiverEmailText = count( $mail->ReceiverElements ) > 0 ? $mail->receiverEmailText() : $recipientText;
            }
            // If Windows PHP mail() implementation, we can specify a To: header in the $additional_headers parameter,
            // it will be used as the only To: header.
            // We can not use "undisclosed-recipients:;" in $to, it will result in a SMTP server response: 501 5.1.3 Bad recipient address syntax
            else
            {
                $receiverEmailText = $mail->receiverEmailText();
            }

            // If in debug mode, send to debug email address and nothing else
            if ( $ini->variable( 'MailSettings', 'DebugSending' ) == 'enabled' )
            {
                $receiverEmailText = $ini->variable( 'MailSettings', 'DebugReceiverEmail' );
                $excludeHeaders[] = 'To';
                $excludeHeaders[] = 'Cc';
                $excludeHeaders[] = 'Bcc';
            }

            // Without -f the MTA takes the envelope sender from the From header (msmtp --read-envelope-from), so a
            // mail without a sender of its own gets one for its headers; the caller's mail is the same again afterwards.
            $previousSender = $useEnvelopeSender ? null : self::ensureFromHeader( $mail );
            $extraHeaders = $mail->headerText( array( 'exclude-headers' => $excludeHeaders ) );
            if ( $previousSender !== null )
            {
                $mail->From = $previousSender['From'];
                $mail->Mail->from = $previousSender['from'];
            }

            $returnedValue = mail( $receiverEmailText, $mail->subject(), $message, $extraHeaders, $sendmailOptions );
            if ( $returnedValue === false )
            {
                eZDebug::writeError( 'An error occurred while sending e-mail. Check the Sendmail error message for further information (usually in /var/log/messages)',
                                     __METHOD__ );
            }

            return $returnedValue;
        }
        else
        {
            eZDebug::writeWarning( "Unable to send mail: 'mail' function is not compiled into PHP.", __METHOD__ );
        }

        return false;
    }

    /**
     * The options handed to sendmail: SendmailOptions[] of site.ini, and "-f <sender>" when $useEnvelopeSender.
     *
     * @param string|false $emailSender the address of the sender, false for none
     * @param bool $useEnvelopeSender
     * @return string
     */
    protected function sendmailOptions( $emailSender, $useEnvelopeSender = true )
    {
        $ini = eZINI::instance();
        $sendmailOptions = '';
        $sendmailOptionsArray = $ini->variable( 'MailSettings', 'SendmailOptions' );
        if ( is_array( $sendmailOptionsArray ) )
            $sendmailOptions = implode( ' ', $sendmailOptionsArray );
        elseif ( !is_string( $sendmailOptionsArray ) )
            $sendmailOptions = $sendmailOptionsArray;
        if ( $emailSender && $useEnvelopeSender )
            $sendmailOptions .= ' -f' . escapeshellarg( $emailSender );
        return (string)$sendmailOptions;
    }

    /**
     * Gives a mail without a sender of its own the first valid one of EmailSender and AdminEmail, written as
     * "address" or as "Name <address>". A mail has a sender of its own when it has a sender address or a From extra
     * header (eZMail::addExtraHeader( 'From', ... )), which is then left as it is. Nothing changes when neither
     * setting is valid.
     *
     * @param eZMail $mail
     * @return array|null the sender before ('From' => eZMail::$From, 'from' => ezcMail::$from) when it was changed,
     *                    else null
     */
    protected static function ensureFromHeader( eZMail $mail )
    {
        $from = $mail->sender( false );
        if ( is_array( $from ) && trim( (string)( $from['email'] ?? '' ) ) !== '' )
            return null;
        if ( is_array( $mail->ExtraHeaders ) )
        {
            foreach ( $mail->ExtraHeaders as $key => $header )
            {
                $name = is_array( $header ) && isset( $header['name'] ) ? $header['name'] : $key;
                if ( is_string( $name ) && strcasecmp( trim( $name ), 'From' ) === 0 )
                    return null;
            }
        }
        $ini = eZINI::instance();
        foreach ( array( 'EmailSender', 'AdminEmail' ) as $setting )
        {
            $text = $ini->hasVariable( 'MailSettings', $setting ) ? $ini->variable( 'MailSettings', $setting ) : '';
            if ( !is_string( $text ) || trim( $text ) === '' )
                continue;
            eZMail::extractEmail( eZMail::cleanHeaderValue( $text ), $address, $name );
            if ( is_string( $address ) && eZMail::validate( $address ) )
            {
                $previous = array( 'From' => $mail->From, 'from' => $mail->Mail->from );
                $mail->setSenderText( $text );
                return $previous;
            }
        }
        return null;
    }

    /**
     * Whether sendmail is told the envelope sender with -f (site.ini [MailSettings] SendmailEnvelopeSender, enabled
     * unless set to disabled).
     *
     * @return bool
     */
    protected static function useEnvelopeSender()
    {
        $ini = eZINI::instance();
        if ( !$ini->hasVariable( 'MailSettings', 'SendmailEnvelopeSender' ) )
            return true;
        return $ini->variable( 'MailSettings', 'SendmailEnvelopeSender' ) !== 'disabled';
    }
}

?>
