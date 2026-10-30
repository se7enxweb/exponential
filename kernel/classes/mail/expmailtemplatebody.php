<?php
/**
 * File containing the expMailTemplateBody class.
 *
 * The body of a mail made from a template: the template's output, and what the template set at root scope. A
 * template that sets content_type to text/html and body_text (its plain-text version, e.g. from
 * design:mail/layout_text.tpl) makes a multipart/alternative mail with a text part and an HTML part; one that sets
 * only content_type gets that type, as before; one that sets neither stays plain text. The registration mails use it
 * (doc/eng-us/features/6.0/registration-admin-links.md).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
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
            $alternative = new ezcMailMultipartAlternative( $textPart, $htmlPart );
            // The random boundary of the raw body, not the library's
            // date( "YmdGHjs" ) . ':' . getmypid() . ':' . counter (ezcMailMultipart::generateBoundary()), which is
            // predictable and wrote the PHP process id into every mail sent by SMTP
            $alternative->boundary = $boundary;
            $mail->Mail->body = $alternative;
        }
        $mail->AlternativeParts = array( 'text' => $text, 'html' => $html );
    }

    /**
     * Sets the body from the result of expMailPreferencesService::renderTemplate() (body, subject, content_type,
     * body_text): multipart/alternative when the template set an HTML content type and a text part, else $body as it
     * is (the rendered body, or the built-in text when there was no template). The caller has set the content type.
     *
     * @param eZMail $mail
     * @param array|null $rendered
     * @param string $body
     * @return bool true when the mail is multipart/alternative
     */
    public static function applyRendered( eZMail $mail, $rendered, $body )
    {
        if ( is_array( $rendered ) && isset( $rendered['content_type'], $rendered['body_text'], $rendered['body'] ) &&
             stripos( (string)$rendered['content_type'], 'html' ) !== false &&
             trim( (string)$rendered['body_text'] ) !== '' && trim( (string)$rendered['body'] ) !== '' )
        {
            self::setAlternative( $mail, trim( (string)$rendered['body_text'] ) . "\n", (string)$rendered['body'] );
            return true;
        }
        $mail->setBody( $body );
        return false;
    }

    /**
     * Makes a plain-text mail (a notification, an alert, a report) multipart/alternative: the text stays the text part
     * as it is, and the HTML part is the same text in design:mail/layout.tpl. Paragraphs are split at blank lines
     * and keep their line breaks; addresses (http, https) become links, and with 'button' the first paragraph that is
     * nothing but an address becomes that button; the paragraphs after it are the small print below the button.
     * Everything is washed by the layout.
     *
     * Options: title (the heading, e.g. the subject), button (the label of the button), site_name, footer, preheader.
     *
     * @param eZMail $mail
     * @param string $text
     * @param array $options
     * @return bool true when the HTML part was made; false leaves the plain text body
     */
    public static function fromText( eZMail $mail, $text, array $options = array() )
    {
        $text = str_replace( array( "\r\n", "\r" ), "\n", (string)$text );
        $html = self::textLayout( $text, $options );
        if ( $html === '' )
        {
            $mail->setBody( $text );
            return false;
        }
        self::setAlternative( $mail, rtrim( $text ) . "\n", $html );
        return true;
    }

    /**
     * The HTML of fromText(), '' when the layout cannot be rendered.
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    public static function textLayout( $text, array $options = array() )
    {
        $paragraphs = array();
        $outro = array();
        $buttons = array();
        $buttonLabel = isset( $options['button'] ) ? trim( (string)$options['button'] ) : '';
        foreach ( preg_split( '/\n[ \t]*\n+/', trim( (string)$text ) ) as $block )
        {
            $block = trim( $block, "\n" );
            if ( trim( $block ) === '' || trim( $block ) === '--' || trim( $block ) === '-- ' )
                continue;
            if ( $buttonLabel !== '' && !$buttons && preg_match( '#^\s*(https?://\S+)\s*$#', $block, $m ) )
            {
                $buttons[] = array( 'label' => $buttonLabel, 'url' => $m[1], 'kind' => 'primary' );
                continue;
            }
            $parts = array();
            $lines = explode( "\n", $block );
            foreach ( $lines as $i => $line )
            {
                // a signature separator on its own line ("-- ") starts the footer of a text: it is not needed in HTML
                if ( rtrim( $line ) === '--' )
                    continue;
                if ( $i > 0 && $parts )
                    $parts[] = array( 'break' => true );
                foreach ( preg_split( '#(https?://[^\s<>"]+)#', $line, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY ) as $piece )
                {
                    if ( preg_match( '#^https?://#', $piece ) )
                    {
                        // a full stop or a comma right after an address ends the sentence, not the address
                        $tail = '';
                        if ( preg_match( '#^(.*?)([.,;:)!?]+)$#', $piece, $t ) )
                        {
                            $piece = $t[1];
                            $tail = $t[2];
                        }
                        $parts[] = array( 'url' => $piece, 'text' => $piece );
                        if ( $tail !== '' )
                            $parts[] = array( 'text' => $tail );
                    }
                    else
                        $parts[] = array( 'text' => $piece );
                }
            }
            // what follows the button (how to stop these mails, the signature) is the small print below it
            if ( $parts && $buttons )
                $outro[] = $parts;
            elseif ( $parts )
                $paragraphs[] = $parts;
        }
        if ( !$paragraphs && !$buttons && !$outro )
            return '';
        $vars = array( 'mail_title' => isset( $options['title'] ) ? (string)$options['title'] : '',
                       'mail_paragraphs' => $paragraphs, 'mail_buttons' => $buttons, 'mail_outro' => $outro );
        foreach ( array( 'site_name' => 'mail_site_name', 'footer' => 'mail_footer', 'preheader' => 'mail_preheader' ) as $option => $name )
        {
            if ( isset( $options[$option] ) && (string)$options[$option] !== '' )
                $vars[$name] = (string)$options[$option];
        }
        if ( $buttons )
        {
            $vars['mail_links_note'] = ezpI18n::tr( 'design/standard/mail', 'If the button does not work, copy this address into your browser: %url',
                                                    null, array( '%url' => $buttons[0]['url'] ) );
        }
        return self::renderLayout( $vars );
    }

    /**
     * Renders design:mail/layout.tpl with the given mail_* inputs, '' on failure. The variables are removed again,
     * so the template object of the request is left as it was.
     *
     * @param array $vars
     * @return string
     */
    public static function renderLayout( array $vars )
    {
        if ( !class_exists( 'eZTemplate' ) )
            return '';
        try
        {
            $tpl = eZTemplate::factory();
            $saved = array();
            foreach ( $vars as $name => $value )
            {
                $saved[$name] = $tpl->hasVariable( $name ) ? array( $tpl->variable( $name ) ) : null;
                $tpl->setVariable( $name, $value );
            }
            $html = (string)$tpl->fetch( 'design:mail/layout.tpl' );
            foreach ( $saved as $name => $value )
            {
                if ( $value === null )
                    $tpl->unsetVariable( $name );
                else
                    $tpl->setVariable( $name, $value[0] );
            }
            return stripos( $html, '<html' ) !== false ? $html : '';
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'The HTML part of a mail could not be made: ' . $e->getMessage(), __METHOD__ );
            return '';
        }
    }
}
