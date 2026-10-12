<?php
/**
 * File containing the eZMailTransport class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZMailTransport ezmailtransport.php
  \brief Interface for mail transport handling

*/

class eZMailTransport
{
    /*!
     Tries to send the contents of the email object \a $mail and
     returns \c true if succesful.
    */
    function sendMail( eZMail $mail )
    {
        return false;
    }

    /**
     * Sends the contents of the email object $mail, with the transport of site.ini [MailSettings] Transport,
     * or for this one message with $transportType: a name of [MailSettings] TransportAlias (file, sendmail,
     * smtp or one an extension adds). The setting itself is not changed, so the next message is sent as
     * configured again. The mail gate (kernel/classes/mailpreferences) applies either way.
     *
     * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit df07cb1ef5), Copyright (C) Mugo Web, GPL v2
     *
     * @param eZMail $mail
     * @param string|null $transportType null or '' for the configured transport
     * @return bool
     */
    static function send( eZMail $mail, ?string $transportType = null )
    {
        $ini = eZINI::instance();

        $transportType = $transportType !== null && trim( $transportType ) !== ''
                       ? trim( $transportType )
                       : trim( $ini->variable( 'MailSettings', 'Transport' ) );

        $optionArray = array( 'iniFile'      => 'site.ini',
                              'iniSection'   => 'MailSettings',
                              'iniVariable'  => 'TransportAlias',
                              'handlerIndex' => strtolower( $transportType ) );
        $options = new ezpExtensionOptions( $optionArray );
        $transportClass = eZExtension::getHandlerClass( $options );

        if ( !is_object( $transportClass ) )
        {
            eZDebug::writeError( "No class available for mail transport type '$transportType', cannot send mail", __METHOD__ );
            return false;
        }
        // The mail gate (kernel/classes/mailpreferences): mail of an optional category goes only to people who
        // switched it on, with footer and List-Unsubscribe headers; mail without a category is sent as before.
        if ( class_exists( 'expMailGate' ) )
            return expMailGate::dispatch( $mail, $transportClass );
        return $transportClass->sendMail( $mail );
    }
}

?>
