<?php
/**
 * File containing the Exponential\Service\MailPreferencesPage class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Service;

/**
 * What the views of the mailpreferences module share: the preference page (signed in, by a personal link, or an
 * administrator for a user), the boxes on the registration form, the download of a person's e-mail data, and the
 * texts that are shown and recorded in the consent log.
 *
 * The exact text a person saw is what the consent log keeps, so every text recorded here is built from the same
 * translated strings the templates show (context design/standard/mailpreferences; the names and descriptions of the
 * categories through kernel/mailpreferences/categories).
 *
 * \code
 * $prefs = \expMailPreferences::forRecipient( \expMailRecipient::fromUser( \eZUser::currentUser() ) );
 * $notices = MailPreferencesPage::handlePost( $prefs, \eZHTTPTool::instance(), 'page' );
 * $tpl->setVariables( MailPreferencesPage::templateVariables( $prefs, 'account', 'mailpreferences/settings', $export, $notices ) );
 * \endcode
 */
class MailPreferencesPage
{
    /** The translation context of the person's pages and of the texts recorded from them. */
    const CONTEXT = 'design/standard/mailpreferences';

    /** The translation context of the administrator's pages. */
    const ADMIN_CONTEXT = 'design/admin/mailpreferences';

    /** The translation context of the names and descriptions of the categories (mailpreferences.ini). */
    const CATEGORY_CONTEXT = 'kernel/mailpreferences/categories';

    /** How many consent records the preference page shows; the download has all of them. */
    const HISTORY_LIMIT = 20;

    /**
     * @param string $text
     * @param array|null $args
     * @return string
     */
    public static function tr( $text, ?array $args = null )
    {
        return \ezpI18n::tr( self::CONTEXT, $text, null, $args );
    }

    /**
     * @param string $text
     * @param array|null $args
     * @return string
     */
    public static function trAdmin( $text, ?array $args = null )
    {
        return \ezpI18n::tr( self::ADMIN_CONTEXT, $text, null, $args );
    }

    /** @return bool the classes and tables of the e-mail preferences are there */
    public static function available()
    {
        return class_exists( 'expMailPreferences' ) && class_exists( 'expMailCategoryRegistry' ) && class_exists( 'expConsentLog' );
    }

    /** @return string the category's name in the current language */
    public static function categoryName( \expMailCategory $category )
    {
        return \ezpI18n::tr( self::CATEGORY_CONTEXT, $category->name );
    }

    /** @return string the category's description in the current language, '' when it has none */
    public static function categoryDescription( \expMailCategory $category )
    {
        return $category->description === '' ? '' : \ezpI18n::tr( self::CATEGORY_CONTEXT, $category->description );
    }

    /** @return string what the page shows for a category: its name and its description */
    public static function categoryWording( \expMailCategory $category )
    {
        $description = self::categoryDescription( $category );
        return $description === '' ? self::categoryName( $category ) : self::categoryName( $category ) . ': ' . $description;
    }

    /**
     * @param bool $on the button that was pressed
     * @return string what the page shows for the main switch and the button
     */
    public static function masterWording( $on )
    {
        return self::tr( 'Send me optional e-mail' ) . ': ' . ( $on ? self::tr( 'Turn on optional e-mail' ) : self::tr( 'Turn off all optional e-mail' ) );
    }

    /** @return string[] frequency => its name on the page */
    public static function frequencyNames()
    {
        return array( 'immediate' => self::tr( 'At once' ), 'daily' => self::tr( 'Daily summary' ), 'weekly' => self::tr( 'Weekly summary' ) );
    }

    /** @return string[] consent log source => what the history calls it */
    public static function sourceNames()
    {
        return array( 'page' => self::tr( 'Preference page' ), 'link' => self::tr( 'Link in an e-mail' ),
                      'admin' => self::tr( 'Changed by an administrator' ), 'import' => self::tr( 'Import' ),
                      'signup' => self::tr( 'Registration form' ), 'bridge' => self::tr( 'Newsletter' ),
                      'confirm' => self::tr( 'Confirmation link' ), 'system' => self::tr( 'System' ) );
    }

    /** @return string[] consent log action => what the history calls it */
    public static function actionNames()
    {
        return array( 'on' => self::tr( 'Turned on' ), 'off' => self::tr( 'Turned off' ),
                      'pending' => self::tr( 'Asked for, waiting for confirmation' ), 'confirm' => self::tr( 'Confirmed' ),
                      'master_on' => self::tr( 'Optional e-mail turned on' ), 'master_off' => self::tr( 'All optional e-mail turned off' ),
                      'frequency' => self::tr( 'Frequency changed' ), 'erase' => self::tr( 'Preferences erased' ),
                      'export' => self::tr( 'Data downloaded' ), 'suppress' => self::tr( 'Address blocked' ),
                      'unsuppress' => self::tr( 'Address block lifted' ), 'email_change' => self::tr( 'E-mail address changed' ) );
    }

    /** @return string[] suppression reason => its name */
    public static function reasonNames()
    {
        return array( 'bounce' => self::trAdmin( 'Hard bounce' ), 'complaint' => self::trAdmin( 'Complaint (marked as spam)' ),
                      'unsubscribe_all' => self::trAdmin( 'The person stopped all e-mail' ), 'legal' => self::trAdmin( 'Legal request' ),
                      'admin' => self::trAdmin( 'Added by an administrator' ), 'bridge' => self::trAdmin( 'Newsletter blacklist' ) );
    }

    /**
     * Applies a posted preference form (parts/preferences.tpl): the main switch, or the categories and their
     * frequencies. Only what changed is stored and recorded.
     *
     * @param \expMailPreferences $prefs
     * @param \eZHTTPTool $http
     * @param string $source page, link or admin (expConsentContext)
     * @return array[] notices: hash( type, text )
     */
    public static function handlePost( \expMailPreferences $prefs, \eZHTTPTool $http, $source )
    {
        if ( !$http->hasPostVariable( 'MailPreferencesForm' ) )
            return array();
        $form = (string)$http->postVariable( 'MailPreferencesForm' );
        $notices = array();
        $db = \eZDB::instance();
        $db->begin();
        try
        {
            if ( $form === 'master' )
                $notices = self::storeMaster( $prefs, $http, $source );
            else if ( $form === 'categories' )
                $notices = self::storeCategories( $prefs, $http, $source );
            $db->commit();
        }
        catch ( \Throwable $e )
        {
            $db->rollback();
            \eZDebug::writeError( $e->getMessage(), __METHOD__ );
            $notices = array( array( 'type' => 'error', 'text' => self::tr( 'Your choices could not be saved. Please try again.' ) ) );
        }
        $prefs->reload();
        return $notices;
    }

    /** @return array[] notices */
    protected static function storeMaster( \expMailPreferences $prefs, \eZHTTPTool $http, $source )
    {
        if ( $http->hasPostVariable( 'MasterOffButton' ) )
        {
            $prefs->setMaster( false, \expConsentContext::fromRequest( $source, self::masterWording( false ) ) );
            return array( array( 'type' => 'success', 'text' => self::tr( 'All optional e-mail is off. Essential messages about your account are still sent.' ) ) );
        }
        if ( $http->hasPostVariable( 'MasterOnButton' ) )
        {
            $prefs->setMaster( true, \expConsentContext::fromRequest( $source, self::masterWording( true ) ) );
            $any = false;
            foreach ( \expMailCategoryRegistry::instance()->optional() as $id => $category )
                $any = $any || $prefs->state( $id ) !== \expMailPreferences::OFF;
            $notices = array( array( 'type' => 'success', 'text' => self::tr( 'Optional e-mail is on: you get the kinds of e-mail you turned on below.' ) ) );
            if ( !$any )
                $notices[] = array( 'type' => 'info', 'text' => self::tr( 'Nothing below is turned on yet. Turn on what you want and save.' ) );
            if ( $prefs->isSuppressed() )
                $notices[] = array( 'type' => 'warning', 'text' => self::tr( 'This address is still blocked because messages to it could not be delivered, or because of a request we must follow. Please contact us to lift the block.' ) );
            return $notices;
        }
        return array();
    }

    /** @return array[] notices */
    protected static function storeCategories( \expMailPreferences $prefs, \eZHTTPTool $http, $source )
    {
        $shown = (array)$http->postVariable( 'CategoryShown', array() );
        $ticked = $http->hasPostVariable( 'Category' ) ? (array)$http->postVariable( 'Category' ) : array();
        $frequencies = $http->hasPostVariable( 'Frequency' ) ? (array)$http->postVariable( 'Frequency' ) : array();
        $registry = \expMailCategoryRegistry::instance();
        $changed = 0;
        $pending = array();
        foreach ( $shown as $id )
        {
            $category = is_string( $id ) ? $registry->get( $id ) : null;
            if ( !$category || $category->essential )
                continue;
            $id = $category->identifier;
            $want = !empty( $ticked[$id] );
            $state = $prefs->state( $id );
            $context = \expConsentContext::fromRequest( $source, self::categoryWording( $category ) );
            if ( $want && $state === \expMailPreferences::OFF )
            {
                if ( $prefs->set( $id, true, $context ) === 'pending_confirmation' )
                    $pending[] = self::categoryName( $category );
                $changed++;
            }
            else if ( !$want && $state !== \expMailPreferences::OFF )
            {
                $prefs->set( $id, false, $context );
                $changed++;
            }
            if ( $want && isset( $frequencies[$id] ) && is_string( $frequencies[$id] ) && in_array( $frequencies[$id], $category->frequencies, true )
                 && $frequencies[$id] !== $prefs->frequency( $id ) )
            {
                $names = self::frequencyNames();
                $wording = self::categoryWording( $category ) . ' (' . self::tr( 'How often:' ) . ' ' . $names[$frequencies[$id]] . ')';
                $prefs->setFrequency( $id, $frequencies[$id], \expConsentContext::fromRequest( $source, $wording ) );
                $changed++;
            }
        }
        $errors = array();
        foreach ( $shown as $id )
        {
            $category = is_string( $id ) ? $registry->get( $id ) : null;
            if ( !$category || $category->essential )
                continue;
            $result = self::storePart( $prefs->recipient(), $category, $http, $source );
            $changed += $result['changed'];
            foreach ( $result['errors'] as $error )
                $errors[] = array( 'type' => 'error', 'text' => $error );
        }
        if ( $errors )
            return array_merge( $errors, $changed > 0 ? array( array( 'type' => 'info', 'text' => self::tr( 'Your choices were saved.' ) ) ) : array() );
        if ( $changed === 0 )
            return array( array( 'type' => 'info', 'text' => self::tr( 'Nothing was changed.' ) ) );
        $notices = array( array( 'type' => 'success', 'text' => self::tr( 'Your choices were saved.' ) ) );
        if ( $pending )
            $notices[] = array( 'type' => 'info', 'text' => self::tr( 'We sent a confirmation link to %email for: %list. Each starts once you open its link.',
                                                                     array( '%email' => $prefs->recipient()->email(), '%list' => implode( ', ', $pending ) ) ) );
        if ( !$prefs->masterOn() )
            $notices[] = array( 'type' => 'warning', 'text' => self::tr( 'Optional e-mail is off at the moment, so none of these is sent until you turn it on.' ) );
        return $notices;
    }

    /**
     * The variables of parts/preferences.tpl.
     *
     * @param \expMailPreferences $prefs
     * @param string $mode account, token or admin
     * @param string $formAction the address the forms post to
     * @param array|false $export hash( json, csv ) addresses of the download, or false
     * @param array[] $notices
     * @return array
     */
    public static function templateVariables( \expMailPreferences $prefs, $mode, $formAction, $export, array $notices = array() )
    {
        $recipient = $prefs->recipient();
        $categories = array();
        $essential = array();
        foreach ( \expMailCategoryRegistry::instance()->all() as $id => $category )
        {
            if ( $category->essential )
            {
                $essential[] = array( 'identifier' => $id, 'name' => self::categoryName( $category ), 'description' => self::categoryDescription( $category ) );
                continue;
            }
            $state = $prefs->state( $id );
            $categories[] = array( 'identifier' => $id, 'name' => self::categoryName( $category ), 'description' => self::categoryDescription( $category ),
                                   'on' => $state === \expMailPreferences::ON, 'pending' => $state === \expMailPreferences::PENDING,
                                   'frequency' => $prefs->frequency( $id ), 'frequencies' => $category->frequencies,
                                   'double_opt_in' => (bool)$category->doubleOptIn,
                                   'subscriptions' => self::subscriptions( $recipient, $category ) )
                            + self::part( $recipient, $category, $mode );
        }
        // a new address of the account waits for its confirmation (expMailAddressChange)
        if ( $recipient->hasAccount() )
        {
            foreach ( \expMailPendingRow::fetchForKey( $recipient->key(), 'email_change' ) as $pending )
            {
                if ( (int)$pending->attribute( 'expires' ) > 0 && (int)$pending->attribute( 'expires' ) < time() )
                    continue;
                $data = $pending->dataArray();
                $notices[] = array( 'type' => 'info', 'text' => self::tr( 'The new e-mail address %email waits for its confirmation: open the link we sent there. Until then this account keeps %current.',
                    array( '%email' => \expMailPreferencesService::maskAddress( isset( $data['email'] ) ? (string)$data['email'] : '' ), '%current' => $recipient->email() ) ) );
            }
        }
        $notice = $notices ? array_shift( $notices ) : false;
        return array( 'mode' => $mode, 'form_action' => $formAction, 'email' => $recipient->email(), 'master' => $prefs->masterOn(),
                      'suppressed' => $prefs->isSuppressed(), 'categories' => $categories, 'essential' => $essential,
                      'history' => self::history( $recipient, self::HISTORY_LIMIT ),
                      'history_total' => \expConsentLog::countList( array( 'recipient_key' => $recipient->key() ) ),
                      'export' => $export, 'notice' => $notice, 'notices' => $notices,
                      'privacy_url' => class_exists( 'expMailSenderDetails' ) ? \expMailSenderDetails::privacyURL() : '' );
    }

    /**
     * The subscriptions an older system keeps for a category (the lists of a newsletter), when the category's
     * handler offers them with the optional method subscriptions( expMailRecipient, expMailCategory ), which returns
     * a list of hash( name, status, active, url ).
     *
     * @param \expMailRecipient $recipient
     * @param \expMailCategory $category
     * @return array[]
     */
    protected static function subscriptions( \expMailRecipient $recipient, \expMailCategory $category )
    {
        try
        {
            $handler = $category->handler();
            if ( !$handler || !method_exists( $handler, 'subscriptions' ) )
                return array();
            $out = array();
            foreach ( (array)$handler->subscriptions( $recipient, $category ) as $s )
            {
                if ( !is_array( $s ) || !isset( $s['name'] ) )
                    continue;
                $out[] = array( 'name' => (string)$s['name'], 'status' => isset( $s['status'] ) ? (string)$s['status'] : '',
                                'active' => !empty( $s['active'] ), 'url' => isset( $s['url'] ) ? (string)$s['url'] : '' );
            }
            return $out;
        }
        catch ( \Throwable $e )
        {
            \eZDebug::writeError( $e->getMessage(), __METHOD__ );
            return array();
        }
    }

    /**
     * The part a category's handler adds to its row on the page (more choices of its own: the interests and the
     * language of a newsletter, a phone number), when the handler offers the optional methods
     * partTemplate( expMailRecipient, expMailCategory, $mode ), which returns a design: template name or null, and
     * partVariables( expMailRecipient, expMailCategory, $mode ), which returns the template's variables (a hash, given
     * to the template as $part). The template is included inside the category form, so its fields are posted with
     * "Save my choices" and reach storePart().
     *
     * @param \expMailRecipient $recipient
     * @param \expMailCategory $category
     * @param string $mode account, token or admin
     * @return array hash( part: the template name or false, part_variables: hash )
     */
    protected static function part( \expMailRecipient $recipient, \expMailCategory $category, $mode )
    {
        $none = array( 'part' => false, 'part_variables' => array() );
        try
        {
            $handler = $category->handler();
            if ( !$handler || !method_exists( $handler, 'partTemplate' ) )
                return $none;
            $template = $handler->partTemplate( $recipient, $category, $mode );
            if ( !is_string( $template ) || $template === '' )
                return $none;
            $variables = method_exists( $handler, 'partVariables' ) ? $handler->partVariables( $recipient, $category, $mode ) : array();
            return array( 'part' => $template, 'part_variables' => is_array( $variables ) ? $variables : array() );
        }
        catch ( \Throwable $e )
        {
            \eZDebug::writeError( $e->getMessage(), __METHOD__ );
            return $none;
        }
    }

    /**
     * Hands the posted category form to the handler's optional storePart( expMailRecipient, expMailCategory,
     * eZHTTPTool, expConsentContext ), which stores the fields of its part. It returns a list of error strings
     * (shown to the person; the handler stores nothing that is wrong), and may add 'changed' => <int> for the
     * number of choices it changed, so that "Nothing was changed." is not shown after a change of the part alone.
     *
     * @param \expMailRecipient $recipient
     * @param \expMailCategory $category
     * @param \eZHTTPTool $http
     * @param string $source page, link or admin
     * @return array hash( changed: int, errors: string[] )
     */
    protected static function storePart( \expMailRecipient $recipient, \expMailCategory $category, \eZHTTPTool $http, $source )
    {
        $out = array( 'changed' => 0, 'errors' => array() );
        $handler = $category->handler();
        if ( !$handler || !method_exists( $handler, 'storePart' ) )
            return $out;
        $result = $handler->storePart( $recipient, $category, $http, \expConsentContext::fromRequest( $source, self::categoryWording( $category ) ) );
        if ( !is_array( $result ) )
            return $out;
        if ( isset( $result['changed'] ) )
        {
            $out['changed'] = max( 0, (int)$result['changed'] );
            unset( $result['changed'] );
        }
        foreach ( $result as $error )
        {
            if ( is_string( $error ) && $error !== '' )
                $out['errors'][] = $error;
        }
        return $out;
    }

    /**
     * The newest consent records of a person, as the page shows them.
     *
     * @param \expMailRecipient $recipient
     * @param int $limit
     * @return array[] hash( time, category, action, change, source, wording )
     */
    public static function history( \expMailRecipient $recipient, $limit )
    {
        $rows = array();
        foreach ( \expConsentLog::fetchForRecipient( $recipient, 0, $limit ) as $row )
            $rows[] = self::logRow( $row );
        return $rows;
    }

    /**
     * One consent record for a template: the names instead of the identifiers.
     *
     * @param \expConsentLog $row
     * @return array
     */
    public static function logRow( \expConsentLog $row )
    {
        static $actions = null, $sources = null, $frequencies = null;
        if ( $actions === null )
        {
            $actions = self::actionNames();
            $sources = self::sourceNames();
            $frequencies = self::frequencyNames();
        }
        $identifier = (string)$row->attribute( 'category' );
        $category = $identifier !== '' ? \expMailCategoryRegistry::instance()->get( $identifier ) : null;
        $action = (string)$row->attribute( 'action' );
        $change = isset( $actions[$action] ) ? $actions[$action] : $action;
        $new = (string)$row->attribute( 'new_value' );
        if ( $action === 'frequency' && isset( $frequencies[$new] ) )
            $change .= ': ' . $frequencies[$new];
        // a new e-mail address of the account (expMailPreferencesService::requestEmailChange()): not a category
        $addressChange = $identifier === '' && ( $action === 'email_change' || ( $action === 'pending' && $new === 'email_change' ) );
        if ( $addressChange && $action === 'pending' )
            $change = self::tr( 'New address asked for, waiting for confirmation' );
        $source = (string)$row->attribute( 'source' );
        if ( $addressChange )
            $categoryName = self::tr( 'E-mail address' );
        else
            $categoryName = $category ? self::categoryName( $category ) : ( $identifier !== '' ? $identifier : self::tr( 'All optional e-mail' ) );
        return array( 'time' => (int)$row->attribute( 'created' ),
                      'category' => $categoryName,
                      'category_identifier' => $identifier, 'action' => $action, 'change' => $change, 'source' => $source,
                      'source_name' => isset( $sources[$source] ) ? $sources[$source] : $source,
                      'wording' => (string)$row->attribute( 'wording' ), 'ip' => (string)$row->attribute( 'ip' ),
                      'user_id' => (int)$row->attribute( 'user_id' ), 'email' => (string)$row->attribute( 'email' ),
                      'recipient_key' => (string)$row->attribute( 'recipient_key' ), 'actor_user_id' => (int)$row->attribute( 'actor_user_id' ),
                      'anonymised' => (bool)$row->attribute( 'anonymised' ) );
    }

    /**
     * The optional categories for the registration form, each ticked only if the person ticked it in the form that
     * came back (a form shown again after an error).
     *
     * @param \eZHTTPTool $http
     * @return array[] hash( identifier, name, description, checked, double_opt_in )
     */
    public static function signupCategories( \eZHTTPTool $http )
    {
        if ( !self::available() )
            return array();
        $ticked = $http->hasPostVariable( 'MailPreferenceCategory' ) ? array_map( 'strval', (array)$http->postVariable( 'MailPreferenceCategory' ) ) : array();
        $out = array();
        try
        {
            foreach ( \expMailCategoryRegistry::instance()->optional() as $id => $category )
                $out[] = array( 'identifier' => $id, 'name' => self::categoryName( $category ), 'description' => self::categoryDescription( $category ),
                                'checked' => in_array( $id, $ticked, true ), 'double_opt_in' => (bool)$category->doubleOptIn );
        }
        catch ( \Throwable $e )
        {
            \eZDebug::writeError( $e->getMessage(), __METHOD__ );
        }
        return $out;
    }

    /**
     * Stores what a new user ticked on the registration form. A category with a double opt-in sends its
     * confirmation mail and waits for it. Nothing ticked: nothing stored, every optional category stays off.
     *
     * @param \eZUser $user
     * @param \eZHTTPTool $http
     * @return int the categories turned on or asked for
     */
    public static function storeSignup( \eZUser $user, \eZHTTPTool $http )
    {
        if ( !self::available() || !$http->hasPostVariable( 'MailPreferenceSignupShown' ) || !$http->hasPostVariable( 'MailPreferenceCategory' ) )
            return 0;
        $count = 0;
        try
        {
            $prefs = \expMailPreferences::forRecipient( \expMailRecipient::fromUser( $user ) );
            $registry = \expMailCategoryRegistry::instance();
            foreach ( array_unique( array_map( 'strval', (array)$http->postVariable( 'MailPreferenceCategory' ) ) ) as $id )
            {
                $category = $registry->get( $id );
                if ( !$category || $category->essential )
                    continue;
                $wording = self::tr( 'E-mail from us (optional)' ) . ': ' . self::categoryWording( $category );
                $prefs->set( $category->identifier, true, \expConsentContext::fromRequest( 'signup', $wording ) );
                $count++;
            }
        }
        catch ( \Throwable $e )
        {
            \eZDebug::writeError( $e->getMessage(), __METHOD__ );
        }
        return $count;
    }

    /**
     * Sends a person's e-mail data as a download and ends the request. The download is recorded in the consent log.
     *
     * @param \expMailPreferences $prefs
     * @param string $format json or csv
     * @param string $source page, link or admin
     */
    public static function sendExport( \expMailPreferences $prefs, $format, $source )
    {
        $format = $format === 'csv' ? 'csv' : 'json';
        \expConsentLog::record( $prefs->recipient(), '', 'export', '', $format,
                                \expConsentContext::fromRequest( $source, self::tr( 'Download my e-mail data' ) . ' (' . strtoupper( $format ) . ')' ) );
        $data = $prefs->export();
        if ( $format === 'csv' )
        {
            $body = "\xEF\xBB\xBF" . \expMailPreferences::exportToCsv( $data );
            $type = 'text/csv; charset=utf-8';
        }
        else
        {
            $body = json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
            $type = 'application/json; charset=utf-8';
        }
        self::sendResponse( $body, $type, 200, 'email-data-' . gmdate( 'Y-m-d' ) . '.' . $format );
    }

    /**
     * Answers the request with this body alone and ends it: the downloads (a person's data, the consent log, the
     * suppression list) and the plain answer of the RFC 8058 one-click unsubscribe. Every such answer of the module
     * goes through here, so it ends the same way on every web server.
     *
     * What was printed before is thrown away (discardOutput()). The headers are private (privateHeaders()).
     * eZExecution::cleanExit() ends the request: under PHP-FPM it exits, under a persistent worker (Velocity) it
     * throws, so a caller must never call this inside try { } catch ( Exception or Throwable ).
     *
     * @param string $body
     * @param string $contentType e.g. 'text/plain; charset=utf-8'
     * @param int $status the HTTP status, 200 or e.g. 400
     * @param string|null $filename a download of this name (Content-Disposition: attachment), null: shown inline
     */
    public static function sendResponse( $body, $contentType, $status = 200, $filename = null )
    {
        $body = (string)$body;
        $clean = self::discardOutput();
        $status = (int)$status;
        if ( $status !== 200 )
        {
            $reasons = array( 400 => 'Bad Request', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 500 => 'Internal Server Error' );
            $protocol = isset( $_SERVER['SERVER_PROTOCOL'] ) && preg_match( '#^HTTP/\d(\.\d)?$#', (string)$_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
            header( $protocol . ' ' . $status . ' ' . ( isset( $reasons[$status] ) ? $reasons[$status] : 'Error' ) );
            http_response_code( $status );
        }
        header( 'Content-Type: ' . $contentType );
        if ( $filename !== null )
            header( 'Content-Disposition: attachment; filename="' . str_replace( array( '"', "\r", "\n" ), '', (string)$filename ) . '"' );
        // the length is right only when nothing printed before is left in front of the body
        if ( $clean )
            header( 'Content-Length: ' . strlen( $body ) );
        self::privateHeaders();
        echo $body;
        \eZExecution::cleanExit();
    }

    /**
     * Throws away the output printed so far, buffer by buffer, down to $floor (eZExecution::discardOutputBuffers()).
     *
     * A buffer that cannot be removed (a persistent worker keeps one under the script for the whole process: Velocity's
     * capture buffer) is emptied instead of removed. The loop stops when PHP refuses to end a buffer: a loop on
     * ob_get_level() alone never ends there, and the request hangs until the server gives up (504).
     *
     * @param int $floor the buffer level to stop at (0: all of them; tests pass their own level)
     * @return bool nothing printed before is left (the buffers above $floor are empty or gone)
     */
    public static function discardOutput( $floor = 0 )
    {
        if ( method_exists( '\eZExecution', 'discardOutputBuffers' ) )
            return \eZExecution::discardOutputBuffers( $floor );
        // a persistent worker that still runs eZExecution from before the update
        $floor = max( 0, (int)$floor );
        $guard = 0;
        while ( ob_get_level() > $floor && $guard++ < 64 )
        {
            if ( !@ob_end_clean() )
                break;
        }
        if ( ob_get_level() > $floor )
        {
            @ob_clean();
            return (int)ob_get_length() === 0;
        }
        return true;
    }

    /**
     * The headers of a page that belongs to one person and may carry a personal link in its address: never stored
     * by a cache, never indexed, and the address is not sent on to other sites.
     */
    public static function privateHeaders()
    {
        if ( headers_sent() )
            return;
        header( 'Cache-Control: private, no-store, max-age=0' );
        header( 'Referrer-Policy: no-referrer' );
        header( 'X-Robots-Tag: noindex, nofollow' );
    }
}
