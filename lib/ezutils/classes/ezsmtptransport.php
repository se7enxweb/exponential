<?php
/**
 * File containing the eZSMTPTransport class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZSMTPTransport ezsmtptransport.php
  \brief The class eZSMTPTransport does

*/

class eZSMTPTransport extends eZMailTransport
{
    function sendMail( eZMail $mail )
    {
        $ini = eZINI::instance();
        $parameters = array();
        $parameters['host'] = $ini->variable( 'MailSettings', 'TransportServer' );
        $parameters['helo'] = $ini->variable( 'MailSettings', 'SenderHost' );
        $parameters['port'] = $ini->variable( 'MailSettings', 'TransportPort' );
        $parameters['connectionType'] = $ini->variable( 'MailSettings', 'TransportConnectionType' );
        $user = $ini->variable( 'MailSettings', 'TransportUser' );
        $password = $ini->variable( 'MailSettings', 'TransportPassword' );
        if ( $user and
             $password )
        {
            $parameters['auth'] = true;
            $parameters['user'] = $user;
            $parameters['pass'] = $password;
        }

        /* If email sender hasn't been specified or is empty
         * we substitute it with either MailSettings.EmailSender or AdminEmail.
         */
        if ( !$mail->senderText() )
        {
            $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
            if ( !$emailSender )
                $emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );

            eZMail::extractEmail( $emailSender, $emailSenderAddress, $emailSenderName );

            if ( !eZMail::validate( $emailSenderAddress ) )
                $emailSender = false;

            if ( $emailSender )
                $mail->setSenderText( $emailSender );
        }

        $excludeHeaders = $ini->variable( 'MailSettings', 'ExcludeHeaders' );
        if ( count( $excludeHeaders ) > 0 )
            $mail->Mail->appendExcludeHeaders( $excludeHeaders );

        $options = new ezcMailSmtpTransportOptions();
        if( $parameters['connectionType'] )
        {
            $options->connectionType = $parameters['connectionType'];
        }
        // site.ini [MailSettings] TransportAuthMethod, empty is the library's choice (the
        // strongest the server offers, NTLM before LOGIN and PLAIN)
        if ( isset( $parameters['auth'] ) && $ini->hasVariable( 'MailSettings', 'TransportAuthMethod' ) )
        {
            $authSetting = $ini->variable( 'MailSettings', 'TransportAuthMethod' );
            $authMethod = self::authMethod( $authSetting );
            if ( $authMethod !== null )
                $options->preferredAuthMethod = $authMethod;
            elseif ( is_string( $authSetting ) && trim( $authSetting ) !== '' )
                eZDebug::writeWarning( "Unknown [MailSettings] TransportAuthMethod '$authSetting', the method is chosen from what the server offers", __METHOD__ );
        }
        // Credentials sent in clear to another host only log a warning, so existing setups keep working
        if ( self::sendsCredentialsInClear( $parameters['host'], $parameters['connectionType'], $user, $password ) )
            eZDebug::writeWarning( "SMTP credentials are sent unencrypted to {$parameters['host']}: set [MailSettings] TransportConnectionType to tls or ssl", __METHOD__ );
        $smtp = new ezcMailSmtpTransport( $parameters['host'], $user, $password,
        $parameters['port'], $options );

        // If in debug mode, send to debug email address and nothing else
        if ( $ini->variable( 'MailSettings', 'DebugSending' ) == 'enabled' )
        {
            $mail->Mail->to = array( new ezcMailAddress( $ini->variable( 'MailSettings', 'DebugReceiverEmail' ) ) );
            $mail->Mail->cc = array();
            $mail->Mail->bcc = array();
        }

        // send() from ezcMailSmtpTransport doesn't return anything (it uses exceptions in case
        // something goes bad)
        try
        {
            $smtp->send( $mail->Mail );
        }
        catch ( ezcMailException $e )
        {
            eZDebug::writeError( $e->getMessage(), __METHOD__ );
            return false;
        }
        // Not every failure is an ezcMailException. Measured: a server that offers AUTH NTLM
        // makes ezcMailSmtpTransport::authNtlm() throw ezcBaseExtensionNotFoundException('mcrypt') (mcrypt is gone
        // since PHP 7.2), which left this method and broke registration and password reset requests.
        catch ( Throwable $e )
        {
            eZDebug::writeError( get_class( $e ) . ': ' . $e->getMessage(), __METHOD__ );
            return false;
        }

        // return true in case of no exceptions
        return true;
    }

    /**
     * The ezcMailSmtpTransport::AUTH_* value of site.ini [MailSettings] TransportAuthMethod (PLAIN, LOGIN, CRAM-MD5,
     * DIGEST-MD5, NTLM, XOAUTH2, any case), null for empty or unknown (the library chooses).
     *
     * @param mixed $value
     * @return string|null
     */
    public static function authMethod( $value )
    {
        if ( !is_string( $value ) )
            return null;
        $value = strtoupper( trim( $value ) );
        if ( $value === '' )
            return null;
        return in_array( $value, ezcMailSmtpTransport::getSupportedAuthMethods(), true ) ? $value : null;
    }

    /**
     * Whether a user name and password go to $host over a connection without encryption: no connection type (or
     * tcp), credentials set, and a host that is not loopback (localhost, 127.0.0.0/8, ::1).
     *
     * @param string $host
     * @param string $connectionType site.ini [MailSettings] TransportConnectionType
     * @param string $user
     * @param string $password
     * @return bool
     */
    public static function sendsCredentialsInClear( $host, $connectionType, $user, $password )
    {
        if ( !$user || !$password )
            return false;
        if ( is_string( $connectionType ) && $connectionType !== '' && strtolower( $connectionType ) !== 'tcp' )
            return false;
        $host = strtolower( trim( (string)$host, " []" ) );
        return !( $host === 'localhost' || $host === '::1' || preg_match( '/^127\.\d{1,3}\.\d{1,3}\.\d{1,3}$/D', $host ) );
    }
}

?>
