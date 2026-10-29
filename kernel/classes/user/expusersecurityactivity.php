<?php
/**
 * File containing the expUserSecurityActivity class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * "Recent security activity" of one account (user/security, user/security/<id>): the security events of the user
 * audit log (expUserAudit, table expuser_audit) about that account, newest first, as a person reads them: signed in,
 * a failed sign-in, a new device or place, the password changed or reset, the e-mail address changed, two-step
 * sign-in turned on or off, a recovery code used, sessions signed out, the account locked.
 *
 * Nothing is recorded here: the audit records the events once (expAuditHook) and the sink useraudit copies those
 * about an account into expuser_audit, so this page, the administrator's user audit log and the security alerts
 * (expUserSecurityAlerts) read the same rows. KINDS maps an event name to what the page shows (a kind, an icon, a
 * level); an event not in it is not shown here (the audit log has it).
 *
 * describe() and summarize() are plain functions over rows, tested without a database
 * (tests/tests/kernel/classes/user/expUserSecurityActivityTest.php).
 *
 * @since 6.0.15
 * @package kernel
 */
class expUserSecurityActivity
{
    /** event name => array( kind, icon, level, group ): group signin (sign-ins and sessions) or account (changes) */
    const KINDS = array(
        'access.session.login' => array( 'signin', 'person-circle', 'ok', 'signin' ),
        'access.session.login.failed' => array( 'signin_failed', 'x-circle-fill', 'bad', 'signin' ),
        'access.session.logout' => array( 'signout', 'box-arrow-right', 'info', 'signin' ),
        'access.session.revoke' => array( 'revoke', 'box-arrow-right', 'warn', 'signin' ),
        'access.session.new_device' => array( 'new_device', 'laptop', 'warn', 'signin' ),
        'access.session.new_location' => array( 'new_location', 'geo-alt', 'warn', 'signin' ),
        'access.session.reauth' => array( 'reauth', 'shield-lock', 'info', 'signin' ),
        'access.session.reauth.failed' => array( 'reauth_failed', 'x-circle-fill', 'bad', 'signin' ),
        'access.passkey.use' => array( 'passkey_use', 'key', 'ok', 'signin' ),
        'access.passkey.use.failed' => array( 'passkey_failed', 'x-circle-fill', 'bad', 'signin' ),
        'access.user.second_factor.refused' => array( 'second_factor_refused', 'x-circle-fill', 'bad', 'signin' ),
        'access.user.two_factor.verify.failed' => array( 'twofactor_failed', 'x-circle-fill', 'bad', 'signin' ),
        'access.user.lock' => array( 'locked', 'lock', 'bad', 'account' ),
        'access.user.unlock' => array( 'unlocked', 'lock', 'info', 'account' ),
        'access.user.password.change' => array( 'password_change', 'key', 'warn', 'account' ),
        'access.user.password.change.failed' => array( 'password_change_failed', 'x-circle-fill', 'bad', 'account' ),
        'access.user.password.reset' => array( 'password_reset', 'key', 'warn', 'account' ),
        'access.user.password.reset.request' => array( 'password_reset_request', 'envelope', 'info', 'account' ),
        'access.user.email.change' => array( 'email_change', 'envelope', 'warn', 'account' ),
        'access.user.login.change' => array( 'login_change', 'person-check', 'warn', 'account' ),
        'access.user.two_factor.enroll' => array( 'twofactor_on', 'shield-lock', 'ok', 'account' ),
        'access.user.two_factor.remove' => array( 'twofactor_off', 'shield-lock', 'bad', 'account' ),
        'access.user.two_factor.recovery_used' => array( 'recovery_used', 'exclamation-triangle', 'warn', 'account' ),
        'access.user.two_factor.recovery_issued' => array( 'recovery_issued', 'shield-lock', 'info', 'account' ),
        'access.user.app_password.create' => array( 'app_password_create', 'key', 'warn', 'account' ),
        'access.user.app_password.revoke' => array( 'app_password_revoke', 'key', 'info', 'account' ),
        'access.passkey.add' => array( 'passkey_add', 'key', 'warn', 'account' ),
        'access.passkey.remove' => array( 'passkey_remove', 'key', 'warn', 'account' ),
        'access.user.enable' => array( 'enabled', 'person-check', 'info', 'account' ),
        'access.user.disable' => array( 'disabled', 'lock', 'bad', 'account' ),
    );

    /** The filters of the page */
    const FILTERS = array( 'all', 'signin', 'account', 'problems' );

    /**
     * The event names a filter shows.
     *
     * @param string $filter all, signin, account, problems (failures, refusals, locks, new devices and places)
     * @return string[]
     */
    public static function namesFor( $filter )
    {
        $names = array();
        foreach ( self::KINDS as $name => $kind )
        {
            if ( $filter === 'signin' || $filter === 'account' )
            {
                if ( $kind[3] !== $filter )
                    continue;
            }
            elseif ( $filter === 'problems' && $kind[2] !== 'bad' && $kind[2] !== 'warn' )
                continue;
            $names[] = $name;
        }
        return $names;
    }

    /**
     * Whether the security activity can be read: the user audit log's table is there (not on MongoDB).
     *
     * @return bool
     */
    public static function available()
    {
        try
        {
            $db = eZDB::instance();
            return $db->databaseName() !== 'mongo' && expUserAudit::exists( $db ) === true;
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /**
     * The WHERE clause for an account and a filter.
     *
     * @param int $userID
     * @param string $filter
     * @param callable $escape
     * @param int $since only rows from this time on (0: all)
     * @return string
     */
    public static function whereClause( $userID, $filter, $escape, $since = 0 )
    {
        $names = array();
        foreach ( self::namesFor( in_array( $filter, self::FILTERS, true ) ? $filter : 'all' ) as $name )
            $names[] = "'" . $escape( $name ) . "'";
        $where = " WHERE target_type = 'user' AND target_id = " . (int)$userID . ' AND event_name IN ( ' . implode( ', ', $names ) . ' )';
        if ( $since > 0 )
            $where .= ' AND created >= ' . (int)$since;
        return $where;
    }

    /**
     * How many events of an account a filter shows.
     *
     * @param int $userID
     * @param string $filter
     * @return int
     */
    public static function count( $userID, $filter = 'all' )
    {
        if ( (int)$userID <= 0 || !self::available() )
            return 0;
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT COUNT(*) AS n FROM ' . expUserAudit::TABLE . self::whereClause( $userID, $filter, array( $db, 'escapeString' ) ) );
        return isset( $rows[0]['n'] ) ? (int)$rows[0]['n'] : 0;
    }

    /**
     * A page of an account's events, newest first, described for the page.
     *
     * @param int $userID
     * @param string $filter
     * @param int $offset
     * @param int $limit
     * @return array[] describe()
     */
    public static function fetch( $userID, $filter = 'all', $offset = 0, $limit = 50 )
    {
        if ( (int)$userID <= 0 || !self::available() )
            return array();
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT * FROM ' . expUserAudit::TABLE . self::whereClause( $userID, $filter, array( $db, 'escapeString' ) )
                                 . ' ORDER BY time_ms DESC, id DESC', array( 'offset' => max( 0, (int)$offset ), 'limit' => max( 1, (int)$limit ) ) );
        $out = array();
        foreach ( (array)$rows as $row )
            $out[] = self::describe( $row, (int)$userID );
        return $out;
    }

    /**
     * The counts of the last days for the head of the page: sign-ins, failed sign-ins, account changes.
     *
     * @param int $userID
     * @param int $days
     * @param int|null $now
     * @return array signins, failures, changes, days
     */
    public static function summary( $userID, $days = 30, $now = null )
    {
        $out = array( 'signins' => 0, 'failures' => 0, 'changes' => 0, 'days' => (int)$days );
        if ( (int)$userID <= 0 || !self::available() )
            return $out;
        $db = eZDB::instance();
        $since = ( $now === null ? time() : (int)$now ) - (int)$days * 86400;
        $rows = $db->arrayQuery( 'SELECT event_name, COUNT(*) AS n FROM ' . expUserAudit::TABLE
                                 . self::whereClause( $userID, 'all', array( $db, 'escapeString' ), $since ) . ' GROUP BY event_name' );
        return self::summarize( (array)$rows, (int)$days );
    }

    /**
     * The counts of summary() from rows of event_name and n.
     *
     * @param array[] $rows
     * @param int $days
     * @return array signins, failures, changes, days
     */
    public static function summarize( array $rows, $days = 30 )
    {
        $out = array( 'signins' => 0, 'failures' => 0, 'changes' => 0, 'days' => (int)$days );
        foreach ( $rows as $row )
        {
            $name = isset( $row['event_name'] ) ? (string)$row['event_name'] : '';
            $n = isset( $row['n'] ) ? (int)$row['n'] : 0;
            if ( !isset( self::KINDS[$name] ) )
                continue;
            if ( $name === 'access.session.login' || $name === 'access.passkey.use' )
                $out['signins'] += $n;
            elseif ( self::KINDS[$name][3] === 'signin' && self::KINDS[$name][2] === 'bad' )
                $out['failures'] += $n;
            elseif ( self::KINDS[$name][3] === 'account' )
                $out['changes'] += $n;
        }
        return $out;
    }

    /**
     * A row of expuser_audit as the page shows it.
     *
     * @param array $row
     * @param int $userID the account the page is about (whether the user acted or someone else)
     * @return array id, time, name, kind, icon, level, group, result, reason, by (self, admin, system), actor_login,
     *               siteaccess, host, ip, details (key => text: method, device, place, count, channel ...)
     */
    public static function describe( array $row, $userID )
    {
        $name = isset( $row['event_name'] ) ? (string)$row['event_name'] : '';
        $kind = isset( self::KINDS[$name] ) ? self::KINDS[$name] : array( 'other', 'clock-history', 'info', 'account' );
        $after = isset( $row['value_after'] ) && $row['value_after'] !== '' ? json_decode( (string)$row['value_after'], true ) : null;
        $after = is_array( $after ) ? $after : array();
        $actorID = isset( $row['actor_id'] ) ? (int)$row['actor_id'] : 0;
        $by = $actorID === 0 ? 'system' : ( $actorID === (int)$userID ? 'self' : 'admin' );
        // a failed sign-in or a request for a new password is made by nobody signed in: it is about the account
        if ( in_array( $kind[0], array( 'signin_failed', 'password_reset_request', 'passkey_failed', 'locked' ), true ) && $actorID !== (int)$userID )
            $by = 'system';
        $details = array();
        foreach ( array( 'method', 'handler', 'browser', 'os', 'device', 'location', 'country', 'count', 'scope', 'channel', 'attempts', 'left' ) as $key )
            if ( isset( $after[$key] ) && is_scalar( $after[$key] ) && (string)$after[$key] !== '' && $after[$key] !== 'standard' )
                $details[$key] = (string)$after[$key];
        return array(
            'id' => isset( $row['id'] ) ? (int)$row['id'] : 0,
            'time' => isset( $row['created'] ) ? (int)$row['created'] : 0,
            'name' => $name,
            'kind' => $kind[0], 'icon' => $kind[1], 'level' => $kind[2], 'group' => $kind[3],
            'result' => isset( $row['result'] ) ? (string)$row['result'] : 'success',
            'reason' => isset( $row['reason'] ) ? (string)$row['reason'] : '',
            'by' => $by,
            'actor_login' => isset( $row['actor_login'] ) ? (string)$row['actor_login'] : '',
            'siteaccess' => isset( $row['siteaccess'] ) ? (string)$row['siteaccess'] : '',
            'host' => isset( $row['host'] ) ? (string)$row['host'] : '',
            'ip' => isset( $row['client_ip'] ) ? expUserAgent::approximateIP( (string)$row['client_ip'] ) : '',
            'details' => $details,
        );
    }

    /**
     * Every security event of an account, for a data export (GDPR: the right of access), oldest first.
     *
     * @param int $userID
     * @return array[] the rows of expuser_audit as stored
     */
    public static function exportForUser( $userID )
    {
        if ( (int)$userID <= 0 || !self::available() )
            return array();
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT * FROM ' . expUserAudit::TABLE . " WHERE target_type = 'user' AND target_id = " . (int)$userID
                                 . ' ORDER BY time_ms ASC, id ASC' );
        return is_array( $rows ) ? $rows : array();
    }
}
?>
