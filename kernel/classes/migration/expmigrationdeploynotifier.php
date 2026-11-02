<?php
/**
 * File containing the expMigrationDeployNotifier class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The notice of a deploy of migrations to remotes (expMigrationRemoteDeploy) by e-mail, to migration.ini
 * [RemoteSettings] NotifyEmail (default site.ini [MailSettings] AdminEmail), when [RemoteSettings] Notify[] names the
 * kind of the end:
 *
 *   failure  the deploy was refused, failed, was taken back or is out of sync
 *   gate     a staged deploy waits at a gate for someone to approve the next stage
 *   success  the deploy (or its last stage) was deployed, or the files were transferred
 *
 * Made like the kernel's other notices to administrators (expStaticNotifier, expUserSessionTracker::sendAdminMail()):
 * plain text from the template design:migration/deploy_notice_mail.tpl (design:mail/layout_text.tpl), the texts
 * translated, the sender site.ini [MailSettings] EmailSender (else AdminEmail), the mail category admin (essential,
 * through the mail gate), and the address of the deploy's page in the administration. A notice holds the remotes' ids,
 * states and problems as the report has them (redacted), never a credential. A notice that cannot be sent is logged,
 * never an error of the deploy. The sender is an option, so tests send nothing.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationDeployNotifier
{
    /** The mail category of the notice (mailpreferences.ini [Category_admin]: essential, for the administrators) */
    const MAIL_CATEGORY = 'admin';

    /** The template of the notice */
    const MAIL_TEMPLATE = 'design:migration/deploy_notice_mail.tpl';

    /** The kinds of notice [RemoteSettings] Notify[] may name */
    const KINDS = array( 'failure', 'gate', 'success' );

    /** @var array */
    private $options;

    /**
     * @param array $options mail (callable( eZMail $mail ) => bool, default eZMailTransport::send()), email (the
     *        address, default from the settings), notify (the kinds, default [RemoteSettings] Notify[]), site (the
     *        site's name), admin_url (the administration's address)
     */
    public function __construct( array $options = array() )
    {
        $this->options = $options + array( 'mail' => array( __CLASS__, 'sendMail' ), 'email' => null, 'notify' => null, 'site' => null, 'admin_url' => null );
    }

    /**
     * The kind of notice a report is. Pure.
     *
     * @param array $report exp-migration-deploy/1
     * @return string failure, gate, success, or '' (a dry run, a status)
     */
    public static function kind( array $report )
    {
        $state = isset( $report['state'] ) ? (string)$report['state'] : '';
        // a rollback someone asked for (rollbackDeploy()) that took every remote back is its success
        if ( $state === 'rolled-back' && !empty( $report['rollback'] ) && isset( $report['exit'] ) && (int)$report['exit'] === 0 )
            return 'success';
        if ( in_array( $state, expMigrationRemoteDeploy::FAILED_STATES, true ) )
            return 'failure';
        if ( $state === 'gated' )
            return empty( $report['errors'] ) ? 'gate' : 'failure';
        if ( in_array( $state, array( 'deployed', 'transferred' ), true ) )
            return 'success';
        return '';
    }

    /**
     * Sends the notice when the settings ask for its kind.
     *
     * @param array $report
     * @return array mail (bool|null: null when none was wanted), problems
     */
    public function notify( array $report )
    {
        $result = array( 'mail' => null, 'problems' => array() );
        $kind = self::kind( $report );
        $wanted = $this->options['notify'] !== null ? (array)$this->options['notify'] : self::settings()['notify'];
        if ( $kind === '' || !in_array( $kind, $wanted, true ) )
            return $result;
        $to = $this->options['email'] !== null ? (string)$this->options['email'] : self::settings()['email'];
        if ( $to === '' )
            $result['problems'][] = 'no address for the notice: set migration.ini [RemoteSettings] NotifyEmail';
        else if ( class_exists( 'eZMail' ) && !eZMail::validate( $to ) )
        {
            $result['mail'] = false;
            $result['problems'][] = 'the address for the notice is not valid: [RemoteSettings] NotifyEmail';
        }
        else
        {
            $site = $this->options['site'] !== null ? (string)$this->options['site'] : self::siteName();
            $url = self::pageURL( isset( $report['deploy_id'] ) ? (string)$report['deploy_id'] : '',
                                  $this->options['admin_url'] !== null ? (string)$this->options['admin_url'] : self::adminURL() );
            $result['mail'] = (bool)call_user_func( $this->options['mail'], self::message( $to, $report, $site, $url ) );
            if ( !$result['mail'] )
                $result['problems'][] = 'the notice could not be sent by e-mail';
        }
        foreach ( $result['problems'] as $problem )
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeWarning( 'Deploy ' . ( isset( $report['deploy_id'] ) ? $report['deploy_id'] : '' ) . ': ' . $problem, __METHOD__ );
        return $result;
    }

    /**
     * [RemoteSettings] Notify[] and NotifyEmail (default site.ini [MailSettings] AdminEmail).
     *
     * @return array notify (kinds), email
     */
    public static function settings()
    {
        $ini = eZINI::instance( 'migration.ini' );
        $notify = $ini->hasVariable( 'RemoteSettings', 'Notify' ) ? array_values( array_intersect( array_map( 'trim', (array)$ini->variable( 'RemoteSettings', 'Notify' ) ), self::KINDS ) )
                                                                : array( 'failure', 'gate' );
        $email = $ini->hasVariable( 'RemoteSettings', 'NotifyEmail' ) ? trim( (string)$ini->variable( 'RemoteSettings', 'NotifyEmail' ) ) : '';
        if ( $email === '' )
        {
            $site = eZINI::instance();
            $email = $site->hasVariable( 'MailSettings', 'AdminEmail' ) ? trim( (string)$site->variable( 'MailSettings', 'AdminEmail' ) ) : '';
        }
        return array( 'notify' => $notify, 'email' => $email );
    }

    /**
     * The variables of the notice's template. Pure.
     *
     * @param array $report
     * @param string $site
     * @param string $url the deploy's page in the administration, '' when unknown
     * @return array kind, deploy_id, state, mode, in_sync (yes, no, unknown), user, remotes (list of id, environment,
     *               state, problems), stage (the stage that waits, 0 for none), stages, errors, url, site_name
     */
    public static function mailVariables( array $report, $site, $url )
    {
        $remotes = array();
        foreach ( isset( $report['remotes'] ) ? (array)$report['remotes'] : array() as $id => $remote )
            $remotes[] = array( 'id' => (string)$id, 'environment' => isset( $remote['remote']['environment'] ) ? (string)$remote['remote']['environment'] : '',
                                'state' => isset( $remote['state'] ) ? (string)$remote['state'] : '',
                                'problems' => array_values( array_map( 'strval', isset( $remote['problems'] ) ? (array)$remote['problems'] : array() ) ) );
        $inSync = !array_key_exists( 'in_sync', $report ) || $report['in_sync'] === null ? 'unknown' : ( $report['in_sync'] ? 'yes' : 'no' );
        return array(
            'kind' => self::kind( $report ),
            'deploy_id' => isset( $report['deploy_id'] ) ? (string)$report['deploy_id'] : '',
            'state' => isset( $report['state'] ) ? (string)$report['state'] : '',
            'mode' => isset( $report['mode'] ) ? (string)$report['mode'] : '',
            'in_sync' => $inSync,
            'user' => isset( $report['user'] ) ? (string)$report['user'] : '',
            'remotes' => $remotes,
            'stage' => isset( $report['next_stage'] ) && $report['next_stage'] !== null ? (int)$report['next_stage'] + 1 : 0,
            'stages' => isset( $report['stages'] ) ? count( (array)$report['stages'] ) : 0,
            'errors' => array_values( array_slice( array_map( 'strval', isset( $report['errors'] ) ? (array)$report['errors'] : array() ), 0, 10 ) ),
            'url' => (string)$url,
            'site_name' => (string)$site,
        );
    }

    /**
     * The state of a deploy in words, as the administration shows it. Pure but for the translation.
     *
     * @param string $state
     * @return string
     */
    public static function stateText( $state )
    {
        $texts = array( 'planned' => 'planned', 'transferred' => 'transferred', 'deployed' => 'deployed', 'gated' => 'waits at a gate',
                        'rolled-back' => 'rolled back', 'out-of-sync' => 'out of sync', 'failed' => 'failed', 'refused' => 'refused' );
        return isset( $texts[$state] ) ? self::tr( 'design/admin/migration', $texts[$state] ) : (string)$state;
    }

    /**
     * The subject and the plain text of the notice without its template (the template is missing or empty).
     *
     * @param array $report
     * @param string $site
     * @param string $url
     * @return string[] subject, body
     */
    public static function mail( array $report, $site, $url = '' )
    {
        $v = self::mailVariables( $report, $site, $url );
        $m = 'design/admin/migration';
        $mailContext = 'design/standard/migration/mail';
        $args = array( '%deploy' => $v['deploy_id'], '%site' => $v['site_name'] );
        $subject = $v['kind'] === 'gate' ? self::tr( $mailContext, 'The deploy %deploy waits at a gate on %site', $args )
                 : ( $v['kind'] === 'success' ? self::tr( $mailContext, 'The deploy %deploy succeeded on %site', $args )
                                              : self::tr( $mailContext, 'The deploy %deploy failed on %site', $args ) );
        $lines = array( $subject, '' );
        $lines[] = self::tr( $m, 'Deploy' ) . ': ' . $v['deploy_id'];
        $lines[] = self::tr( $m, 'Result' ) . ': ' . self::stateText( $v['state'] );
        $lines[] = self::tr( $m, 'In sync' ) . ': ' . self::tr( $m, $v['in_sync'] );
        if ( $v['stage'] > 0 )
            $lines[] = self::tr( $m, 'Next stage' ) . ': ' . self::tr( $m, '%stage of %stages', array( '%stage' => $v['stage'], '%stages' => $v['stages'] ) );
        $lines[] = '';
        $lines[] = self::tr( $m, 'Remotes' ) . ':';
        foreach ( $v['remotes'] as $remote )
        {
            $lines[] = '- ' . $remote['id'] . ' (' . $remote['environment'] . '): ' . $remote['state'];
            foreach ( $remote['problems'] as $problem )
                $lines[] = '  ' . str_replace( array( "\r", "\n" ), ' ', $problem );
        }
        if ( $v['errors'] )
        {
            $lines[] = '';
            $lines[] = self::tr( 'design/admin/setup/cronjobs', 'Errors' ) . ':';
            foreach ( $v['errors'] as $error )
                $lines[] = '- ' . str_replace( array( "\r", "\n" ), ' ', $error );
        }
        if ( $v['url'] !== '' )
        {
            $lines[] = '';
            $lines[] = self::tr( $m, 'Deploy' ) . ': ' . $v['url'];
        }
        $lines[] = '';
        $lines[] = '--';
        $lines[] = self::tr( $mailContext, 'This mail was sent by %site because migration.ini [RemoteSettings] Notify[] asks for it.', array( '%site' => $v['site_name'] ) );
        return array( $subject, implode( "\n", $lines ) . "\n" );
    }

    /**
     * The notice as a mail: plain text from the template (else mail()), sender, recipient, category admin.
     *
     * @param string $to
     * @param array $report
     * @param string $site
     * @param string $url
     * @return eZMail
     */
    public static function message( $to, array $report, $site, $url )
    {
        list( $subject, $body ) = self::mail( $report, $site, $url );
        $rendered = class_exists( 'expMailPreferencesService' ) ? expMailPreferencesService::renderTemplate( self::MAIL_TEMPLATE, self::mailVariables( $report, $site, $url ) ) : null;
        if ( $rendered !== null && trim( $rendered['body'] ) !== '' )
        {
            $body = trim( $rendered['body'] ) . "\n";
            if ( $rendered['subject'] !== null && trim( $rendered['subject'] ) !== '' )
                $subject = trim( $rendered['subject'] );
        }
        $ini = eZINI::instance();
        $sender = $ini->variable( 'MailSettings', 'EmailSender' );
        if ( !$sender )
            $sender = $ini->variable( 'MailSettings', 'AdminEmail' );
        $mail = new eZMail();
        $mail->setSender( $sender );
        $mail->setReceiver( (string)$to );
        $mail->setSubject( $subject );
        $mail->setBody( $body );
        $mail->setCategory( self::MAIL_CATEGORY );
        return $mail;
    }

    /**
     * The absolute address of a deploy's page in the administration (migration/deployment/<deploy id>). Pure.
     *
     * @param string $deployId
     * @param string $adminUrl
     * @return string '' without one of them
     */
    public static function pageURL( $deployId, $adminUrl )
    {
        $adminUrl = rtrim( trim( (string)$adminUrl ), '/' );
        if ( $adminUrl === '' || (string)$deployId === '' )
            return '';
        return $adminUrl . '/migration/deployment/' . rawurlencode( (string)$deployId );
    }

    /** @return string a text translated by ezpI18n, the English text with its arguments when there is no ezpI18n */
    protected static function tr( $context, $source, array $arguments = array() )
    {
        if ( class_exists( 'ezpI18n' ) )
            return (string)ezpI18n::tr( $context, $source, null, $arguments );
        return strtr( $source, $arguments );
    }

    /** @return string the administration's address (expRegistrationAdminAction::adminBaseURL()), '' when unknown */
    public static function adminURL()
    {
        try
        {
            return class_exists( 'expRegistrationAdminAction' ) ? (string)expRegistrationAdminAction::adminBaseURL() : '';
        }
        catch ( Throwable $e )
        {
            return '';
        }
    }

    /** @return string the name of the public site, as every mail of the kernel names it (expMailSenderDetails) */
    public static function siteName()
    {
        if ( class_exists( 'expMailSenderDetails' ) )
        {
            try
            {
                $details = expMailSenderDetails::get();
                if ( isset( $details['name'] ) && trim( (string)$details['name'] ) !== '' )
                    return (string)$details['name'];
            }
            catch ( Throwable $e )
            {
            }
        }
        $ini = eZINI::instance();
        return $ini->hasVariable( 'SiteSettings', 'SiteName' ) ? (string)$ini->variable( 'SiteSettings', 'SiteName' ) : '';
    }

    /** Sends the notice through the installation's mail transport (and so through the mail gate). */
    public static function sendMail( eZMail $mail )
    {
        return (bool)eZMailTransport::send( $mail );
    }
}
