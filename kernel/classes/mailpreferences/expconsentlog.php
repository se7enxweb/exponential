<?php
/**
 * File containing the expConsentLog class.
 *
 * The consent log (table expmail_consent_log): one row per change of a person's e-mail preferences, with the time,
 * the category, the action (on, off, pending, confirm, master_on, master_off, frequency, erase, export, suppress,
 * unsuppress), the old and the new value, the source and exact wording (expConsentContext), the IP address, the user
 * who acted and the siteaccess. Every row is also an audit event (access.user.consent.change and friends) without
 * the address.
 *
 * Erasure anonymises a person's rows: the address, the IP and the user id are removed, the recipient key becomes an
 * irreversible hash, and what was withdrawn and when stays as the proof of the withdrawal.
 *
 * Retention (cleanup(), the mailpreferences cronjob part): rows of a person whose account (or, without account, whose
 * last preference) is gone are removed RetentionDays after they were written; expired pending double opt-ins too.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expConsentLog extends eZPersistentObject
{
    const ACTIONS = array( 'on', 'off', 'pending', 'confirm', 'master_on', 'master_off', 'frequency', 'erase', 'export',
                           'suppress', 'unsuppress', 'email_change' );

    const CSV_COLUMNS = array( 'id', 'created', 'recipient_key', 'user_id', 'email', 'category', 'action', 'old_value', 'new_value',
                               'source', 'wording', 'ip', 'actor_user_id', 'siteaccess', 'anonymised' );

    static function definition()
    {
        $int = array( 'datatype' => 'integer', 'default' => 0, 'required' => true );
        $str = array( 'datatype' => 'string', 'default' => '', 'required' => true );
        return array( 'fields' => array( 'id' => array( 'name' => 'ID' ) + $int,
                                         'created' => array( 'name' => 'Created' ) + $int,
                                         'recipient_key' => array( 'name' => 'RecipientKey' ) + $str,
                                         'user_id' => array( 'name' => 'UserID' ) + $int,
                                         'email' => array( 'name' => 'Email' ) + $str,
                                         'category' => array( 'name' => 'Category' ) + $str,
                                         'action' => array( 'name' => 'Action' ) + $str,
                                         'old_value' => array( 'name' => 'OldValue' ) + $str,
                                         'new_value' => array( 'name' => 'NewValue' ) + $str,
                                         'source' => array( 'name' => 'Source' ) + $str,
                                         'wording' => array( 'name' => 'Wording' ) + $str,
                                         'ip' => array( 'name' => 'IP' ) + $str,
                                         'actor_user_id' => array( 'name' => 'ActorUserID' ) + $int,
                                         'siteaccess' => array( 'name' => 'SiteAccess' ) + $str,
                                         'anonymised' => array( 'name' => 'Anonymised' ) + $int ),
                      'keys' => array( 'id' ),
                      'increment_key' => 'id',
                      'sort' => array( 'created' => 'desc', 'id' => 'desc' ),
                      'class_name' => 'expConsentLog',
                      'name' => 'expmail_consent_log' );
    }

    /**
     * Writes one row and its audit event.
     *
     * @param expMailRecipient $recipient
     * @param string $category '' for none ('_master' is written as '')
     * @param string $action one of ACTIONS
     * @param string $old
     * @param string $new
     * @param expConsentContext $context
     * @return expConsentLog
     */
    public static function record( expMailRecipient $recipient, $category, $action, $old, $new, expConsentContext $context )
    {
        if ( !in_array( $action, self::ACTIONS, true ) )
            throw new InvalidArgumentException( "Not a consent action: '$action'" );
        $row = new self( array(
            'created' => time(), 'recipient_key' => $recipient->key(), 'user_id' => $recipient->userId(),
            'email' => $recipient->email(), 'category' => $category === expMailPreferenceRow::MASTER ? '' : (string)$category,
            'action' => $action, 'old_value' => (string)$old, 'new_value' => (string)$new, 'source' => $context->source,
            'wording' => mb_substr( $context->wording, 0, 4000 ), 'ip' => $context->ip, 'actor_user_id' => $context->actorUserId,
            'siteaccess' => $context->siteaccess, 'anonymised' => 0 ) );
        $row->store();
        if ( class_exists( 'expAudit' ) )
        {
            $name = $action === 'erase' ? 'access.user.consent.erase' : ( $action === 'export' ? 'access.user.consent.export' : 'access.user.consent.change' );
            $data = array( 'object' => $recipient->userId() > 0 ? array( 'type' => 'user', 'id' => $recipient->userId() )
                                                                  : array( 'type' => 'mail_address', 'id' => substr( $recipient->key(), 2, 16 ) ),
                           'before' => array( 'value' => (string)$old ), 'after' => array( 'value' => (string)$new ),
                           'x' => array( 'category' => (string)$row->attribute( 'category' ), 'action' => $action, 'source' => $context->source,
                                         'consent_log_id' => (int)$row->attribute( 'id' ) ) );
            if ( $context->actorUserId > 0 && $context->actorUserId !== $recipient->userId() )
                $data['x']['changed_by_admin'] = $context->source === 'admin';
            expAudit::event( $name, $data );
        }
        return $row;
    }

    /**
     * @param expMailRecipient $recipient
     * @param int $offset
     * @param int $limit 0: all
     * @return expConsentLog[] newest first
     */
    public static function fetchForRecipient( expMailRecipient $recipient, $offset = 0, $limit = 0 )
    {
        return (array)eZPersistentObject::fetchObjectList( self::definition(), null, array( 'recipient_key' => $recipient->key() ),
                                                           array( 'created' => 'desc', 'id' => 'desc' ),
                                                           $limit > 0 ? array( 'offset' => (int)$offset, 'length' => (int)$limit ) : null, true );
    }

    /**
     * The rows matching the filters, for the admin's log page.
     *
     * @param array $filters recipient_key, user_id, email (any case; matches the stored address or its hash key),
     *                       category, action, source, from (timestamp), to (timestamp)
     * @param int $offset
     * @param int $limit 0: all
     * @return expConsentLog[] newest first
     */
    public static function fetchList( array $filters = array(), $offset = 0, $limit = 50 )
    {
        $db = eZDB::instance();
        $sql = 'SELECT * FROM expmail_consent_log' . self::where( $filters ) . ' ORDER BY created DESC, id DESC';
        $rows = $limit > 0 ? $db->arrayQuery( $sql, array( 'offset' => (int)$offset, 'limit' => (int)$limit ) ) : $db->arrayQuery( $sql );
        return eZPersistentObject::handleRows( (array)$rows, 'expConsentLog', true );
    }

    /** @return int */
    public static function countList( array $filters = array() )
    {
        $rows = eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM expmail_consent_log' . self::where( $filters ) );
        return isset( $rows[0]['c'] ) ? (int)$rows[0]['c'] : 0;
    }

    protected static function where( array $filters )
    {
        $db = eZDB::instance();
        $w = array();
        if ( isset( $filters['recipient_key'] ) && $filters['recipient_key'] !== '' )
            $w[] = "recipient_key = '" . $db->escapeString( (string)$filters['recipient_key'] ) . "'";
        if ( !empty( $filters['user_id'] ) )
            $w[] = 'user_id = ' . (int)$filters['user_id'];
        if ( isset( $filters['email'] ) && trim( (string)$filters['email'] ) !== '' )
        {
            $email = strtolower( trim( (string)$filters['email'] ) );
            $w[] = "( email = '" . $db->escapeString( $email ) . "' OR recipient_key = '" . $db->escapeString( 'a:' . expMailSuppression::hash( $email ) ) . "' )";
        }
        foreach ( array( 'category', 'action', 'source' ) as $f )
            if ( isset( $filters[$f] ) && $filters[$f] !== '' )
                $w[] = "$f = '" . $db->escapeString( (string)$filters[$f] ) . "'";
        if ( !empty( $filters['from'] ) )
            $w[] = 'created >= ' . (int)$filters['from'];
        if ( !empty( $filters['to'] ) )
            $w[] = 'created <= ' . (int)$filters['to'];
        return $w ? ' WHERE ' . implode( ' AND ', $w ) : '';
    }

    /**
     * The rows matching the filters as CSV (UTF-8, header line, RFC 4180 quoting; a cell starting with = + - @ is
     * prefixed with ' so a spreadsheet does not run it).
     *
     * @param array $filters as fetchList()
     * @return string
     */
    public static function exportCsv( array $filters = array() )
    {
        $out = fopen( 'php://temp', 'w+' );
        fputcsv( $out, self::CSV_COLUMNS, ',', '"', '' );
        $offset = 0;
        do
        {
            $rows = self::fetchList( $filters, $offset, 500 );
            foreach ( $rows as $row )
                fputcsv( $out, self::csvRow( $row->toCsvArray() ), ',', '"', '' );
            $offset += 500;
        }
        while ( count( $rows ) === 500 );
        rewind( $out );
        $csv = stream_get_contents( $out );
        fclose( $out );
        return $csv;
    }

    /** @return array the row's values in CSV_COLUMNS order, with the time as ISO 8601 */
    public function toCsvArray()
    {
        $out = array();
        foreach ( self::CSV_COLUMNS as $c )
            $out[] = $c === 'created' ? gmdate( 'Y-m-d\TH:i:s\Z', (int)$this->attribute( 'created' ) ) : (string)$this->attribute( $c );
        return $out;
    }

    /** @return string[] the values with formula cells defused */
    public static function csvRow( array $values )
    {
        return array_map( function ( $v ) {
            $v = (string)$v;
            return $v !== '' && strpos( '=+-@', $v[0] ) !== false && !is_numeric( $v ) ? "'" . $v : $v;
        }, $values );
    }

    /**
     * Anonymises a person's rows: address, IP and user id removed, the key made an irreversible hash. What was
     * changed, when, and the wording stay (the proof of a withdrawal).
     *
     * @param expMailRecipient $recipient
     * @return int rows anonymised
     */
    public static function anonymise( expMailRecipient $recipient )
    {
        $db = eZDB::instance();
        $key = $recipient->key();
        $anon = 'x:' . substr( hash( 'sha256', $key . expMailSecret::derive( 'anonymise' ) ), 0, 32 );
        $conds = array( "recipient_key = '" . $db->escapeString( $key ) . "'" );
        if ( $recipient->userId() > 0 )
            $conds[] = 'user_id = ' . (int)$recipient->userId();
        if ( $recipient->email() !== '' )
        {
            $conds[] = "email = '" . $db->escapeString( $recipient->email() ) . "'";
            $conds[] = "recipient_key = '" . $db->escapeString( 'a:' . expMailSuppression::hash( $recipient->email() ) ) . "'";
        }
        $where = '( ' . implode( ' OR ', $conds ) . ' ) AND anonymised = 0';
        $rows = $db->arrayQuery( "SELECT COUNT(*) AS c FROM expmail_consent_log WHERE $where" );
        $n = isset( $rows[0]['c'] ) ? (int)$rows[0]['c'] : 0;
        if ( $n > 0 )
        {
            $actor = $recipient->userId() > 0 ? ', actor_user_id = CASE WHEN actor_user_id = ' . (int)$recipient->userId() . ' THEN 0 ELSE actor_user_id END' : '';
            $db->query( "UPDATE expmail_consent_log SET recipient_key = '" . $db->escapeString( $anon ) . "', email = '', ip = '', user_id = 0, anonymised = 1$actor WHERE $where" );
        }
        return $n;
    }

    /**
     * Retention: removes the rows of people who are gone, RetentionDays after they were written, and expired
     * pending double opt-ins.
     *
     * @param bool $dryRun count only
     * @param int|null $now
     * @return array consent (rows removed), pending (rows removed), suppression (entries removed)
     */
    public static function cleanup( $dryRun = false, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $ini = eZINI::instance( 'mailpreferences.ini' );
        $days = $ini->hasVariable( 'ConsentSettings', 'RetentionDays' ) ? (int)$ini->variable( 'ConsentSettings', 'RetentionDays' ) : 1095;
        $db = eZDB::instance();
        $result = array( 'consent' => 0, 'pending' => 0, 'suppression' => 0 );
        if ( $days > 0 )
        {
            $before = $now - $days * 86400;
            // gone: anonymised, an account that no longer exists, an address with no preference left
            $gone = "created < $before AND ( anonymised = 1"
                  . " OR ( user_id > 0 AND user_id NOT IN ( SELECT contentobject_id FROM ezuser ) )"
                  . " OR ( user_id = 0 AND recipient_key NOT IN ( SELECT recipient_key FROM expmail_preference ) ) )";
            $rows = $db->arrayQuery( "SELECT COUNT(*) AS c FROM expmail_consent_log WHERE $gone" );
            $result['consent'] = isset( $rows[0]['c'] ) ? (int)$rows[0]['c'] : 0;
            if ( !$dryRun && $result['consent'] > 0 )
                $db->query( "DELETE FROM expmail_consent_log WHERE $gone" );
        }
        $rows = $db->arrayQuery( "SELECT COUNT(*) AS c FROM expmail_pending WHERE expires > 0 AND expires < $now" );
        $result['pending'] = isset( $rows[0]['c'] ) ? (int)$rows[0]['c'] : 0;
        if ( !$dryRun && $result['pending'] > 0 )
        {
            // a category still waiting for an expired confirmation is off again
            $db->query( "UPDATE expmail_preference SET state = 'off', modified = $now WHERE state = 'pending' AND NOT EXISTS ("
                      . " SELECT 1 FROM expmail_pending p WHERE p.recipient_key = expmail_preference.recipient_key"
                      . " AND p.category = expmail_preference.category AND p.kind = 'category' AND ( p.expires = 0 OR p.expires >= $now ) )" );
            $db->query( "DELETE FROM expmail_pending WHERE expires > 0 AND expires < $now" );
        }
        if ( !$dryRun )
            $result['suppression'] = expMailSuppression::cleanup();
        return $result;
    }
}
