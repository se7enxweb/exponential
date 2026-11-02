<?php
/**
 * File containing the expMigrationStepMail class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "mail": one e-mail, for a migration that has to tell someone it ran (the editors of a section whose
 * classes changed, the operator of a search service).
 *
 *   - type: mail
 *     to: [ editors@example.com ]
 *     cc: [ ]
 *     bcc: [ ]
 *     subject: The news section has a new class
 *     body: |
 *       From today on, ...
 *     html: false                     true: body is HTML
 *     from: noreply@example.com       default: site.ini [MailSettings] EmailSender, else AdminEmail
 *     reply_to: webmaster@example.com
 *     transport: file                 a transport of site.ini [MailSettings] TransportAlias for this mail only (file
 *                                     writes it below var/log/mail); default: the installation's
 *     category: admin                 the mail category (mailpreferences.ini Categories[]); default admin
 *
 * The mail goes through the kernel's mail transport (eZMailTransport::send()), so the mail gate and the settings of
 * the installation apply as for any other mail. Like the kernel's other notices it carries a mail category: admin
 * (essential, sent as before) unless the step names another; an optional one (newsletter, system ...) reaches only
 * the recipients whose preferences allow it, with the footer and unsubscribe link of the mail gate. A dry run sends nothing. Each mail is recorded in the audit log
 * (system.migration.mail: how many recipients and whether it was sent; never an address or the text). A mail cannot be
 * taken back: a migration with this step needs a down: or a rollback to its caption.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepMail extends expMigrationStepTypeBase
{
    /** The mail category without a category: key (mailpreferences.ini [Category_admin], essential) */
    const DEFAULT_CATEGORY = 'admin';

    /** @return string */
    public function name()
    {
        return 'mail';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Sends one e-mail through the mail transport of the installation',
            'keys' => array(
                'to' => array( 'type' => 'list', 'required' => true, 'description' => 'the addresses it goes to' ),
                'cc' => array( 'type' => 'list', 'description' => 'addresses in copy' ),
                'bcc' => array( 'type' => 'list', 'description' => 'addresses in blind copy' ),
                'subject' => array( 'type' => 'string', 'required' => true, 'description' => 'the subject' ),
                'body' => array( 'type' => 'string', 'required' => true, 'description' => 'the text' ),
                'html' => array( 'type' => 'bool', 'default' => false, 'description' => 'true: body is HTML' ),
                'from' => array( 'type' => 'string', 'description' => 'the sender (default: site.ini [MailSettings] EmailSender, else AdminEmail)' ),
                'reply_to' => array( 'type' => 'string', 'description' => 'the address answers go to' ),
                'transport' => array( 'type' => 'string', 'description' => 'a transport of site.ini [MailSettings] TransportAlias (file, sendmail, smtp) for this mail only' ),
                'category' => array( 'type' => 'string', 'default' => self::DEFAULT_CATEGORY,
                                     'description' => 'the mail category of mailpreferences.ini; an optional one reaches only the recipients whose preferences allow it' ),
            ),
            'values' => array(
                'recipients' => 'how many addresses it went to',
            ),
            'example' => "- type: mail\n  to: [ editors@example.com ]\n  subject: The news section has a new class\n"
                       . "  body: \"From today on, news items have a teaser image.\"\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $errors = array();
        foreach ( array( 'to', 'cc', 'bcc' ) as $key )
            if ( isset( $step[$key] ) && is_array( $step[$key] ) )
                foreach ( $step[$key] as $i => $address )
                    if ( !is_scalar( $address ) || !expMigrationReferences::contains( (string)$address ) && !eZMail::validate( trim( (string)$address ) ) )
                        $errors[] = "{$key}[$i]: an e-mail address";
        if ( isset( $step['to'] ) && $step['to'] === array() )
            $errors[] = 'to: at least one address';
        foreach ( array( 'from', 'reply_to' ) as $key )
            if ( isset( $step[$key] ) && !expMigrationReferences::contains( $step[$key] ) && !eZMail::validate( trim( (string)$step[$key] ) ) )
                $errors[] = "$key: an e-mail address";
        if ( isset( $step['subject'] ) && preg_match( '/[\r\n]/', (string)$step['subject'] ) )
            $errors[] = 'subject: one line';
        if ( isset( $step['transport'] ) && !preg_match( '/^[a-z0-9_]+$/', (string)$step['transport'] ) )
            $errors[] = 'transport: the name of a transport (file, sendmail, smtp ...)';
        if ( isset( $step['category'] ) && !expMigrationReferences::contains( $step['category'] ) && !self::knownCategory( $step['category'] ) )
            $errors[] = 'category: a mail category of mailpreferences.ini (admin, system, newsletter ...)';
        return $errors;
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $count = count( (array)$step['to'] ) + ( isset( $step['cc'] ) ? count( (array)$step['cc'] ) : 0 ) + ( isset( $step['bcc'] ) ? count( (array)$step['bcc'] ) : 0 );
        return array( 'send the mail "' . $step['subject'] . "\" to $count " . ( $count === 1 ? 'address' : 'addresses' )
                      . ( isset( $step['transport'] ) ? ' (transport ' . $step['transport'] . ')' : '' ) . ' (not sent in a dry run)' );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        $mail = new eZMail();
        $count = 0;
        foreach ( (array)$step['to'] as $address )
        {
            $mail->addReceiver( trim( (string)$address ) );
            $count++;
        }
        foreach ( isset( $step['cc'] ) ? (array)$step['cc'] : array() as $address )
        {
            $mail->addCc( trim( (string)$address ) );
            $count++;
        }
        foreach ( isset( $step['bcc'] ) ? (array)$step['bcc'] : array() as $address )
        {
            $mail->addBcc( trim( (string)$address ) );
            $count++;
        }
        $ini = eZINI::instance();
        $from = isset( $step['from'] ) ? trim( (string)$step['from'] ) : '';
        if ( $from === '' )
            $from = trim( (string)$ini->variable( 'MailSettings', 'EmailSender' ) ) ?: trim( (string)$ini->variable( 'MailSettings', 'AdminEmail' ) );
        if ( $from !== '' )
            $mail->setSender( $from );
        if ( isset( $step['reply_to'] ) )
            $mail->setReplyTo( trim( (string)$step['reply_to'] ) );
        $mail->setSubject( (string)$step['subject'] );
        $mail->setBody( (string)$step['body'] );
        if ( !empty( $step['html'] ) )
            $mail->setContentType( 'text/html' );
        $mail->setCategory( isset( $step['category'] ) && trim( (string)$step['category'] ) !== '' ? trim( (string)$step['category'] ) : self::DEFAULT_CATEGORY );
        $sent = (bool)eZMailTransport::send( $mail, isset( $step['transport'] ) ? (string)$step['transport'] : null );
        if ( $context instanceof expMigrationContext )
            $context->audit( 'system.migration.mail', array( 'object' => array( 'type' => 'mail', 'id' => $context->runId() ), 'verb' => 'send',
                                                             'result' => $sent ? 'success' : 'failure',
                                                             'x' => array( 'recipients' => $count, 'transport' => isset( $step['transport'] ) ? (string)$step['transport'] : '' ) ) );
        if ( !$sent )
            throw new RuntimeException( 'the mail "' . $step['subject'] . '" could not be sent (the debug log of the mail transport says why)' );
        return self::result( array( 'sent the mail "' . $step['subject'] . "\" to $count " . ( $count === 1 ? 'address' : 'addresses' ) ), array( 'recipients' => $count ) );
    }

    /**
     * Whether a mail category exists: a name of mailpreferences.ini Categories[] (expMailCategoryRegistry); without
     * the registry, any identifier.
     *
     * @param mixed $category
     * @return bool
     */
    protected static function knownCategory( $category )
    {
        if ( !is_string( $category ) || !preg_match( '/^[a-z0-9_]+$/', $category ) )
            return false;
        if ( !class_exists( 'expMailCategoryRegistry' ) )
            return true;
        try
        {
            return expMailCategoryRegistry::instance()->get( $category ) !== null;
        }
        catch ( Throwable $e )
        {
            return true;
        }
    }
}
