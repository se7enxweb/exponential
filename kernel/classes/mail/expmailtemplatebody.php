<?php
/**
 * File containing the expMailTemplateBody class.
 *
 * The body of a mail made from a template: the template's output, and what the template set at root scope. A
 * template that sets content_type to text/html and body_text (its plain-text version, e.g. from
 * design:mail/layout_text.tpl) makes a multipart/alternative mail with a text part and an HTML part; one that sets
 * only content_type gets that type, as before; one that sets neither stays plain text. The registration mails use it
 * (doc/features/6.0/registration-admin-links.md).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expMailTemplateBody
{
    /**
     * Sets the body (and the content type) of a mail from a template's output.
     *
     * @param eZMail $mail
     * @param eZTemplate $tpl the template object the output was fetched with
     * @param string $output the template's output
     * @return bool true when the mail is multipart/alternative
     */
    public static function apply( eZMail $mail, eZTemplate $tpl, $output )
    {
        $type = $tpl->hasVariable( 'content_type' ) ? (string)$tpl->variable( 'content_type' ) : '';
        if ( $type !== '' && stripos( $type, 'html' ) !== false && $tpl->hasVariable( 'body_text' ) &&
             trim( (string)$tpl->variable( 'body_text' ) ) !== '' )
        {
            self::setAlternative( $mail, trim( (string)$tpl->variable( 'body_text' ) ) . "\n", (string)$output );
            return true;
        }
        if ( $type !== '' )
            $mail->setContentType( $type );
        $mail->setBody( $output );
        return false;
    }

    /**
     * Makes a mail multipart/alternative: a plain-text part and an HTML part. Both the transports that send the
     * body text (sendmail, file) and the one that sends the ezcMail object (SMTP) get the same two parts.
     *
     * @param eZMail $mail
     * @param string $text
     * @param string $html
     */
    public static function setAlternative( eZMail $mail, $text, $html )
    {
        $boundary = 'exp-alt-' . bin2hex( random_bytes( 12 ) );
        $nl = "\n";
        $raw = 'This is a message in MIME format: a plain-text part and an HTML part.' . $nl . $nl
             . '--' . $boundary . $nl
             . 'Content-Type: text/plain; charset=utf-8' . $nl
             . 'Content-Transfer-Encoding: 8bit' . $nl . $nl
             . $text . $nl . $nl
             . '--' . $boundary . $nl
             . 'Content-Type: text/html; charset=utf-8' . $nl
             . 'Content-Transfer-Encoding: 8bit' . $nl . $nl
             . $html . $nl . $nl
             . '--' . $boundary . '--' . $nl;
        $mail->setContentType( 'multipart/alternative', false, '8bit', false, $boundary );
        $mail->setBody( $raw );
        if ( class_exists( 'ezcMailMultipartAlternative' ) )
        {
            $textPart = new ezcMailText( $text, 'utf-8' );
            $textPart->subType = 'plain';
            $htmlPart = new ezcMailText( $html, 'utf-8' );
            $htmlPart->subType = 'html';
            $mail->Mail->body = new ezcMailMultipartAlternative( $textPart, $htmlPart );
        }
    }
}
