<?php
/**
 * File containing the expMailPreferences class.
 *
 * The e-mail preferences of one person (expMailRecipient): the master switch "all optional mail", the state of each
 * category (on, off, or pending while a double opt-in waits for its confirmation) and its frequency.
 *
 *  - Opt-in: an optional category without a stored preference is off, unless its default says on or its handler
 *    reads older data (a subtree notification, a newsletter subscription) as on.
 *  - The master switch keeps the categories as they are ("dormant"): switched off, no optional mail goes out;
 *    switched on again, the categories that were on are on again. Essential mail is always sent.
 *  - Every change is written to the consent log (expConsentLog) with its expConsentContext.
 *
 * \code
 * $prefs = expMailPreferences::forRecipient( expMailRecipient::fromUser( eZUser::currentUser() ) );
 * $context = expConsentContext::fromRequest( 'page', $wording );
 * $prefs->set( 'newsletter', true, $context );     // 'pending_confirmation': a confirmation mail was sent
 * $prefs->set( 'content', true, $context );        // 'on'
 * $prefs->setFrequency( 'content', 'daily', $context );
 * $prefs->allows( 'content' );                     // master on, category on, not suppressed, not pending
 * \endcode
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expMailPreferences
{
    const ON = 'on';
    const OFF = 'off';
    const PENDING = 'pending';

    /** @var expMailRecipient */
    protected $recipient;

    /** @var expMailPreferenceRow[]|null category => row */
    protected $rows = null;

    protected function __construct( expMailRecipient $recipient )
    {
        $this->recipient = $recipient;
    }

    /**
     * @param expMailRecipient $recipient
     * @return expMailPreferences
     */
    public static function forRecipient( expMailRecipient $recipient )
    {
        return new self( $recipient );
    }

    /** @return expMailRecipient */
    public function recipient()
    {
        return $this->recipient;
    }

    /** @return expMailPreferenceRow[] */
    protected function rows()
    {
        if ( $this->rows === null )
            $this->rows = expMailPreferenceRow::fetchForKey( $this->recipient->key() );
        return $this->rows;
    }

    /** Reads the stored preferences again. */
    public function reload()
    {
        $this->rows = null;
    }

    /** @return bool the master switch: false when the person switched all optional mail off */
    public function masterOn()
    {
        $rows = $this->rows();
        return !isset( $rows[expMailPreferenceRow::MASTER] ) || $rows[expMailPreferenceRow::MASTER]->attribute( 'state' ) !== self::OFF;
    }

    /**
     * The master switch. The categories keep their state.
     *
     * @param bool $on
     * @param expConsentContext $context
     * @return bool it changed
     */
    public function setMaster( $on, expConsentContext $context )
    {
        $old = $this->masterOn();
        if ( $old === (bool)$on && isset( $this->rows()[expMailPreferenceRow::MASTER] ) )
            return false;
        $this->store( expMailPreferenceRow::MASTER, $on ? self::ON : self::OFF, null );
        expConsentLog::record( $this->recipient, '', $on ? 'master_on' : 'master_off', $old ? self::ON : self::OFF, $on ? self::ON : self::OFF, $context );
        if ( $on )
            $this->liftOwnUnsubscribeAll( $context );
        return $old !== (bool)$on;
    }

    /**
     * The state of a category: 'on', 'off' or 'pending'. Essential categories are always 'on'. Not affected by the
     * master switch (see allows()).
     *
     * @param string $category
     * @return string
     */
    public function state( $category )
    {
        $cat = expMailCategoryRegistry::instance()->get( $category );
        if ( !$cat )
            return self::OFF;
        if ( $cat->essential )
            return self::ON;
        $rows = $this->rows();
        if ( isset( $rows[$cat->identifier] ) )
        {
            $state = (string)$rows[$cat->identifier]->attribute( 'state' );
            return in_array( $state, array( self::ON, self::OFF, self::PENDING ), true ) ? $state : self::OFF;
        }
        $handler = $cat->handler();
        if ( $handler )
        {
            $mapped = $handler->stateFor( $this->recipient, $cat );
            if ( $mapped !== null )
                return $mapped ? self::ON : self::OFF;
        }
        return $cat->defaultOn ? self::ON : self::OFF;
    }

    /** @return bool the person switched the category on (and confirmed it); essential: true */
    public function isOn( $category )
    {
        return $this->state( $category ) === self::ON;
    }

    /** @return bool a double opt-in of the category waits for its confirmation */
    public function isPending( $category )
    {
        return $this->state( $category ) === self::PENDING;
    }

    /** @return bool the person stored a choice for the category (not the default) */
    public function isStored( $category )
    {
        return isset( $this->rows()[expMailCategory::cleanIdentifier( $category )] );
    }

    /**
     * Switches a category on or off. Switching on a category with double opt-in stores 'pending' and sends the
     * confirmation mail (unless $context->sendConfirmation is false), except for the sources confirm, bridge and
     * import, whose consent was confirmed elsewhere.
     *
     * @param string $category
     * @param bool $on
     * @param expConsentContext $context
     * @return string 'on', 'pending_confirmation' or 'off'
     * @throws InvalidArgumentException for an unknown or essential category
     */
    public function set( $category, $on, expConsentContext $context )
    {
        $cat = $this->optionalCategory( $category );
        $old = $this->state( $cat->identifier );
        $stored = $this->isStored( $cat->identifier );
        if ( !$on )
        {
            $this->removePending( $cat->identifier );
            if ( $old === self::OFF && $stored )
                return self::OFF;
            $this->store( $cat->identifier, self::OFF, null );
            expConsentLog::record( $this->recipient, $cat->identifier, 'off', $old, self::OFF, $context );
            $this->notifyHandler( $cat, self::OFF, $context );
            return self::OFF;
        }
        if ( $old === self::ON && $stored )
            return self::ON;
        if ( $cat->doubleOptIn && !in_array( $context->source, array( 'confirm', 'bridge', 'import' ), true ) )
        {
            if ( $this->recipient->email() === '' )
                throw new InvalidArgumentException( 'A double opt-in needs the address of the recipient' );
            $this->removePending( $cat->identifier );
            $pending = new expMailPendingRow( array(
                'recipient_key' => $this->recipient->key(), 'user_id' => $this->recipient->userId(), 'category' => $cat->identifier,
                'kind' => 'category', 'data' => '', 'created' => time(), 'expires' => time() + self::pendingSeconds() ) );
            $pending->store();
            $this->store( $cat->identifier, self::PENDING, null );
            expConsentLog::record( $this->recipient, $cat->identifier, 'pending', $old, self::PENDING, $context );
            $this->notifyHandler( $cat, self::PENDING, $context );
            if ( $context->sendConfirmation )
                expMailPreferencesService::sendConfirmation( $this->recipient, $cat, $pending );
            return 'pending_confirmation';
        }
        $this->removePending( $cat->identifier );
        $this->store( $cat->identifier, self::ON, null );
        expConsentLog::record( $this->recipient, $cat->identifier, $context->source === 'confirm' ? 'confirm' : 'on', $old, self::ON, $context );
        $this->liftOwnUnsubscribeAll( $context );
        $this->notifyHandler( $cat, self::ON, $context );
        return self::ON;
    }

    /**
     * The frequency of a category: the stored one, what the handler reads, or the category's first; '' for a
     * category without frequencies.
     *
     * @param string $category
     * @return string immediate|daily|weekly or ''
     */
    public function frequency( $category )
    {
        $cat = expMailCategoryRegistry::instance()->get( $category );
        if ( !$cat || !$cat->frequencies )
            return '';
        $rows = $this->rows();
        if ( isset( $rows[$cat->identifier] ) && in_array( $rows[$cat->identifier]->attribute( 'frequency' ), $cat->frequencies, true ) )
            return (string)$rows[$cat->identifier]->attribute( 'frequency' );
        $handler = $cat->handler();
        if ( $handler )
        {
            $f = $handler->frequencyFor( $this->recipient, $cat );
            if ( $f !== null && in_array( $f, $cat->frequencies, true ) )
                return $f;
        }
        return $cat->frequencies[0];
    }

    /**
     * @param string $category
     * @param string $frequency one of the category's frequencies
     * @param expConsentContext $context
     * @return bool it changed
     */
    public function setFrequency( $category, $frequency, expConsentContext $context )
    {
        $cat = $this->optionalCategory( $category );
        if ( !in_array( $frequency, $cat->frequencies, true ) )
            throw new InvalidArgumentException( "Not a frequency of '{$cat->identifier}': '$frequency'" );
        $old = $this->frequency( $cat->identifier );
        if ( $old === $frequency && $this->isStored( $cat->identifier ) && (string)$this->rows()[$cat->identifier]->attribute( 'frequency' ) === $frequency )
            return false;
        $this->store( $cat->identifier, null, $frequency );
        expConsentLog::record( $this->recipient, $cat->identifier, 'frequency', $old, $frequency, $context );
        return $old !== $frequency;
    }

    /**
     * May mail of the category go to this person? Essential: always. Optional: the master switch is on, the
     * category is on (not pending), and the address is not suppressed.
     *
     * @param string $category
     * @return bool
     */
    public function allows( $category )
    {
        return $this->decision( $category ) === 'allow';
    }

    /**
     * allows() with the reason.
     *
     * @param string $category
     * @return string 'allow', 'unknown_category', 'suppressed', 'master_off', 'pending', 'off'
     */
    public function decision( $category )
    {
        $cat = expMailCategoryRegistry::instance()->get( $category );
        if ( !$cat )
            return 'unknown_category';
        if ( $cat->essential )
            return 'allow';
        if ( $this->isSuppressed() )
            return 'suppressed';
        if ( !$this->masterOn() )
            return 'master_off';
        $state = $this->state( $cat->identifier );
        if ( $state === self::PENDING )
            return 'pending';
        return $state === self::ON ? 'allow' : 'off';
    }

    /** @return bool the address is on the suppression list */
    public function isSuppressed()
    {
        if ( $this->recipient->email() !== '' )
            return expMailSuppression::isSuppressed( $this->recipient->email() );
        if ( strncmp( $this->recipient->key(), 'a:', 2 ) === 0 )
            return expMailSuppression::fetchByHash( substr( $this->recipient->key(), 2 ) ) !== null;
        return false;
    }

    /**
     * Every category with its state, for the preference page.
     *
     * @return array[] identifier => category (expMailCategory), state, frequency, stored, essential
     */
    public function overview()
    {
        $out = array();
        foreach ( expMailCategoryRegistry::instance()->all() as $id => $cat )
            $out[$id] = array( 'identifier' => $id, 'category' => $cat, 'state' => $this->state( $id ), 'frequency' => $this->frequency( $id ),
                               'stored' => $this->isStored( $id ), 'essential' => $cat->essential, 'allows' => $this->allows( $id ) );
        return $out;
    }

    /**
     * Everything stored about the person's e-mail preferences ("Download my e-mail data").
     *
     * @return array
     */
    public function export()
    {
        $categories = array();
        foreach ( expMailCategoryRegistry::instance()->all() as $id => $cat )
            $categories[] = array( 'identifier' => $id, 'name' => $cat->name, 'essential' => $cat->essential, 'state' => $this->state( $id ),
                                   'frequency' => $this->frequency( $id ), 'stored' => $this->isStored( $id ),
                                   'modified' => $this->isStored( $id ) ? gmdate( 'Y-m-d\TH:i:s\Z', (int)$this->rows()[$id]->attribute( 'modified' ) ) : null );
        $pending = array();
        foreach ( expMailPendingRow::fetchForKey( $this->recipient->key() ) as $p )
            if ( $p->attribute( 'kind' ) !== 'link' )
                $pending[] = array( 'category' => (string)$p->attribute( 'category' ), 'kind' => (string)$p->attribute( 'kind' ),
                                    'created' => gmdate( 'Y-m-d\TH:i:s\Z', (int)$p->attribute( 'created' ) ),
                                    'expires' => $p->attribute( 'expires' ) ? gmdate( 'Y-m-d\TH:i:s\Z', (int)$p->attribute( 'expires' ) ) : null );
        $log = array();
        foreach ( expConsentLog::fetchForRecipient( $this->recipient ) as $row )
            $log[] = array_combine( expConsentLog::CSV_COLUMNS, $row->toCsvArray() );
        return array( 'recipient' => array( 'key' => $this->recipient->key(), 'user_id' => $this->recipient->userId(), 'email' => $this->recipient->email() ),
                      'generated' => gmdate( 'Y-m-d\TH:i:s\Z' ), 'master' => $this->masterOn() ? self::ON : self::OFF,
                      'suppressed' => $this->isSuppressed(), 'categories' => $categories, 'pending' => $pending, 'consent_log' => $log );
    }

    /**
     * export() as CSV: one line per category, then one per consent log row (section column first).
     *
     * @param array $export
     * @return string
     */
    public static function exportToCsv( array $export )
    {
        $out = fopen( 'php://temp', 'w+' );
        fputcsv( $out, array( 'section', 'key', 'value', 'detail' ), ',', '"', '' );
        foreach ( $export['recipient'] as $k => $v )
            fputcsv( $out, expConsentLog::csvRow( array( 'recipient', $k, (string)$v, '' ) ), ',', '"', '' );
        fputcsv( $out, array( 'recipient', 'master', $export['master'], '' ), ',', '"', '' );
        fputcsv( $out, array( 'recipient', 'suppressed', $export['suppressed'] ? 'yes' : 'no', '' ), ',', '"', '' );
        foreach ( $export['categories'] as $c )
            fputcsv( $out, expConsentLog::csvRow( array( 'category', $c['identifier'], $c['state'], $c['frequency'] ) ), ',', '"', '' );
        foreach ( $export['pending'] as $p )
            fputcsv( $out, expConsentLog::csvRow( array( 'pending', $p['category'], $p['kind'], $p['created'] ) ), ',', '"', '' );
        fputcsv( $out, array_merge( array( 'consent_log' ), expConsentLog::CSV_COLUMNS ), ',', '"', '' );
        foreach ( $export['consent_log'] as $row )
            fputcsv( $out, expConsentLog::csvRow( array_merge( array( 'consent_log' ), array_values( $row ) ) ), ',', '"', '' );
        rewind( $out );
        $csv = stream_get_contents( $out );
        fclose( $out );
        return $csv;
    }

    /**
     * Erasure (account removal or a request): the preferences and pending confirmations are removed, the consent
     * log is anonymised (a last row "erase" is written first, so the withdrawal stays provable).
     *
     * @param expConsentContext $context
     * @return array preferences, pending, anonymised (row counts)
     */
    public function erase( expConsentContext $context )
    {
        $db = eZDB::instance();
        $key = $db->escapeString( $this->recipient->key() );
        $count = function ( $table ) use ( $db, $key ) {
            $r = $db->arrayQuery( "SELECT COUNT(*) AS c FROM $table WHERE recipient_key = '$key'" );
            return isset( $r[0]['c'] ) ? (int)$r[0]['c'] : 0;
        };
        $result = array( 'preferences' => $count( 'expmail_preference' ), 'pending' => $count( 'expmail_pending' ), 'anonymised' => 0 );
        $db->begin();
        $db->query( "DELETE FROM expmail_preference WHERE recipient_key = '$key'" );
        $db->query( "DELETE FROM expmail_pending WHERE recipient_key = '$key'" );
        expConsentLog::record( $this->recipient, '', 'erase', '', '', $context );
        $result['anonymised'] = expConsentLog::anonymise( $this->recipient );
        $db->commit();
        $this->rows = null;
        return $result;
    }

    // ------------------------------------------------------------------ internals

    /** @return expMailCategory */
    protected function optionalCategory( $category )
    {
        $cat = expMailCategoryRegistry::instance()->get( $category );
        if ( !$cat )
            throw new InvalidArgumentException( "Unknown mail category: '$category'" );
        if ( $cat->essential )
            throw new InvalidArgumentException( "The mail category '{$cat->identifier}' is essential and cannot be switched" );
        return $cat;
    }

    /**
     * @param string $category
     * @param string|null $state null: keep
     * @param string|null $frequency null: keep
     */
    protected function store( $category, $state, $frequency )
    {
        $rows = $this->rows();
        $now = time();
        if ( isset( $rows[$category] ) )
            $row = $rows[$category];
        else
        {
            $cat = expMailCategoryRegistry::instance()->get( $category );
            $row = new expMailPreferenceRow( array(
                'recipient_key' => $this->recipient->key(), 'user_id' => $this->recipient->userId(), 'category' => $category,
                'state' => $category === expMailPreferenceRow::MASTER ? self::ON : ( $cat ? $this->state( $category ) : self::OFF ),
                'frequency' => '', 'created' => $now ) );
        }
        if ( $state !== null )
            $row->setAttribute( 'state', $state );
        if ( $frequency !== null )
            $row->setAttribute( 'frequency', $frequency );
        $row->setAttribute( 'modified', $now );
        $row->store();
        $this->rows[$category] = $row;
    }

    protected function removePending( $category )
    {
        foreach ( expMailPendingRow::fetchForKey( $this->recipient->key(), 'category', $category ) as $p )
            $p->remove();
    }

    protected function notifyHandler( expMailCategory $cat, $state, expConsentContext $context )
    {
        $handler = $cat->handler();
        if ( !$handler )
            return;
        try
        {
            $handler->changed( $this->recipient, $cat, $state, $context );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( "Mail category handler {$cat->handlerClass}: " . $e->getMessage(), __METHOD__ );
        }
    }

    /**
     * A person who switches optional mail on again (on the page, by a link or a confirmation) ends their own
     * "unsubscribe from all"; a suppression for a bounce, a complaint or a legal request stays.
     */
    protected function liftOwnUnsubscribeAll( expConsentContext $context )
    {
        if ( !in_array( $context->source, array( 'page', 'link', 'confirm', 'signup' ), true ) || $this->recipient->email() === '' )
            return;
        if ( expMailSuppression::reason( $this->recipient->email() ) === 'unsubscribe_all' )
        {
            expMailSuppression::lift( $this->recipient->email() );
            expConsentLog::record( $this->recipient, '', 'unsuppress', 'unsubscribe_all', '', $context );
        }
    }

    /** @return int */
    public static function pendingSeconds()
    {
        $ini = eZINI::instance( 'mailpreferences.ini' );
        $days = $ini->hasVariable( 'ConsentSettings', 'PendingDays' ) ? (int)$ini->variable( 'ConsentSettings', 'PendingDays' ) : 7;
        return max( 1, $days ) * 86400;
    }
}
