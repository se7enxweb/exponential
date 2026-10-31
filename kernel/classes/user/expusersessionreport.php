<?php
/**
 * File containing the expUserSessionReport class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The sign-in report of the whole site (user/sessionreport, policy user/sessions_manage): how many sessions run and
 * how many users they belong to, the sign-ins and failed attempts per day, and the running sessions of every user,
 * most recently used first, each with a button to sign it out; a form signs a user out everywhere.
 *
 * It reads expuser_session (and expuser_session_info) for the sessions, so it works with every session handler: the
 * PHP files of the default handler too, whose sessions setup/session cannot list. The sign-ins and failures per day
 * come from the user audit log (expuser_audit: access.session.login and access.session.login.failed) when it is
 * there, else the sign-ins from expuser_session alone.
 *
 * byDay() is a plain function over timestamps, tested without a database (expUserSessionReportTest).
 *
 * @since 6.0.15
 * @package kernel
 */
class expUserSessionReport
{
    /** The policy function of the report and of signing users out everywhere */
    const FUNCTION_NAME = 'sessions_manage';

    /**
     * Whether a viewer may open the report (policy user/sessions_manage).
     *
     * @param eZUser $viewer
     * @return bool
     */
    public static function canManage( eZUser $viewer )
    {
        $access = $viewer->hasAccessTo( 'user', self::FUNCTION_NAME );
        return $access['accessWord'] !== 'no';
    }

    /**
     * Counts per day, the newest day first, every day of the window present (0 when nothing happened).
     *
     * @param int[] $times timestamps
     * @param int $days
     * @param int $now
     * @return array Y-m-d => count
     */
    public static function byDay( array $times, $days, $now )
    {
        $out = array();
        for ( $i = 0; $i < (int)$days; $i++ )
            $out[date( 'Y-m-d', (int)$now - $i * 86400 )] = 0;
        foreach ( $times as $t )
        {
            $key = date( 'Y-m-d', (int)$t );
            if ( isset( $out[$key] ) )
                $out[$key]++;
        }
        return $out;
    }

    /**
     * The days of the report: sign-ins, failed attempts and sessions started, the newest day first.
     *
     * @param int $days
     * @param int|null $now
     * @return array[] day (Y-m-d), signins, failures, sessions, max (the largest number of the window, for the bars)
     */
    public static function days( $days = 14, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $since = mktime( 0, 0, 0, (int)date( 'n', $now ), (int)date( 'j', $now ), (int)date( 'Y', $now ) ) - ( (int)$days - 1 ) * 86400;
        $db = eZDB::instance();
        $logins = array();
        $failures = array();
        if ( class_exists( 'expUserSecurityActivity' ) && expUserSecurityActivity::available() )
        {
            $rows = $db->arrayQuery( 'SELECT event_name, created FROM ' . expUserAudit::TABLE
                                     . " WHERE event_name IN ( 'access.session.login', 'access.session.login.failed' ) AND created >= " . (int)$since );
            foreach ( (array)$rows as $row )
            {
                if ( $row['event_name'] === 'access.session.login' )
                    $logins[] = (int)$row['created'];
                else
                    $failures[] = (int)$row['created'];
            }
        }
        $sessions = array();
        if ( expUserSessionTracker::available() )
        {
            $rows = expUserSession::fetchObjectList( expUserSession::definition(), array( 'created' ), array( 'created' => array( '>=', $since ) ), null, null, false );
            foreach ( (array)$rows as $row )
                $sessions[] = (int)$row['created'];
        }
        $byLogin = self::byDay( $logins, $days, $now );
        $byFailure = self::byDay( $failures, $days, $now );
        $bySession = self::byDay( $sessions, $days, $now );
        $out = array();
        $max = 0;
        foreach ( array_keys( $bySession ) as $day )
        {
            $item = array( 'day' => $day, 'time' => strtotime( $day . ' 12:00:00' ), 'signins' => $byLogin[$day], 'failures' => $byFailure[$day],
                           'sessions' => $bySession[$day] );
            $max = max( $max, $item['signins'], $item['failures'], $item['sessions'] );
            $out[] = $item;
        }
        foreach ( $out as $i => $item )
        {
            $out[$i]['max'] = $max;
            foreach ( array( 'signins', 'failures', 'sessions' ) as $key )
                $out[$i][$key . '_pct'] = self::percentStep( $item[$key], $max );
        }
        return $out;
    }

    /**
     * A share as one of the administration's width classes (.a4-pct-0 ... .a4-pct-100); at least 1 for a value above 0.
     *
     * @param int $value
     * @param int $max
     * @return int
     */
    public static function percentStep( $value, $max )
    {
        if ( $max <= 0 || $value <= 0 )
            return 0;
        return max( 1, min( 100, (int)round( 100 * $value / $max ) ) );
    }

    /**
     * The running sessions of every user, most recently used first.
     *
     * @param int $limit
     * @param int|null $now
     * @return array sessions (rows of expUserSessionTracker::describe() with user_id, login, name), total, users
     */
    public static function activeSessions( $limit = 100, $now = null )
    {
        $out = array( 'sessions' => array(), 'total' => 0, 'users' => 0 );
        if ( !expUserSessionTracker::available() )
            return $out;
        $now = $now === null ? time() : (int)$now;
        $timeout = expUserSessionTracker::sessionTimeout();
        $def = expUserSession::definition();
        $conditions = array( 'ended' => 0 );
        $rows = expUserSession::fetchObjectList( $def, array( 'user_id', 'last_active', 'remember_until' ), $conditions, null, null, false );
        $users = array();
        foreach ( (array)$rows as $row )
        {
            if ( expUserSessionTracker::hasTimedOut( (int)$row['last_active'], (int)$row['remember_until'], $timeout, $now ) )
                continue;
            $out['total']++;
            $users[(int)$row['user_id']] = true;
        }
        $out['users'] = count( $users );
        $list = expUserSession::fetchObjectList( $def, null, $conditions, array( 'last_active' => 'desc', 'id' => 'desc' ),
                                                 array( 'offset' => 0, 'length' => (int)$limit * 2 ) );
        $list = is_array( $list ) ? $list : array();
        $infos = expUserSessionInfo::fetchForSessions( array_map( function ( $r ) { return (int)$r->attribute( 'id' ); }, $list ) );
        $current = expUserSessionTracker::currentTokenHash();
        $accounts = array();
        foreach ( $list as $row )
        {
            if ( count( $out['sessions'] ) >= (int)$limit )
                break;
            if ( expUserSessionTracker::hasTimedOut( (int)$row->attribute( 'last_active' ), (int)$row->attribute( 'remember_until' ), $timeout, $now ) )
                continue;
            $userID = (int)$row->attribute( 'user_id' );
            if ( !array_key_exists( $userID, $accounts ) )
            {
                $user = eZUser::fetch( $userID );
                $object = $user instanceof eZUser ? eZContentObject::fetch( $userID ) : null;
                $accounts[$userID] = array( 'login' => $user instanceof eZUser ? (string)$user->attribute( 'login' ) : '',
                                            'name' => $object instanceof eZContentObject ? (string)$object->attribute( 'name' ) : '' );
            }
            $item = expUserSessionTracker::describe( $row, $current, isset( $infos[(int)$row->attribute( 'id' )] ) ? $infos[(int)$row->attribute( 'id' )] : null );
            $out['sessions'][] = $item + array( 'user_id' => $userID ) + $accounts[$userID];
        }
        return $out;
    }

    /**
     * Suspicious patterns of the last hours, worst first, from plain rows (tested without a database):
     * - failures: an account with at least $threshold failed sign-ins (someone guessing its password);
     * - network: one network (the stored address, shortened) failing on at least $networkAccounts different accounts
     *   (someone trying many accounts);
     * - locked: an account locked after failed sign-ins;
     * - countries: an account signed in right now from more than one country (only with a GeoIP database).
     *
     * @param array[] $failures rows with user_id and ip (one per failed sign-in)
     * @param int[] $locked the user ids of the accounts locked
     * @param array $countries user id => string[] the countries of its running sessions
     * @param int $threshold failed sign-ins of one account that count
     * @param int $networkAccounts accounts one network must fail on to count
     * @return array[] kind, user_id (0 for network), count, network, countries
     */
    public static function findPatterns( array $failures, array $locked, array $countries, $threshold = 5, $networkAccounts = 3 )
    {
        $byUser = array();
        $byNetwork = array();
        foreach ( $failures as $row )
        {
            $userID = isset( $row['user_id'] ) ? (int)$row['user_id'] : 0;
            $ip = isset( $row['ip'] ) ? expUserAgent::approximateIP( (string)$row['ip'] ) : '';
            if ( $userID > 0 )
                $byUser[$userID] = ( isset( $byUser[$userID] ) ? $byUser[$userID] : 0 ) + 1;
            if ( $ip !== '' )
            {
                if ( !isset( $byNetwork[$ip] ) )
                    $byNetwork[$ip] = array( 'accounts' => array(), 'count' => 0 );
                $byNetwork[$ip]['count']++;
                if ( $userID > 0 )
                    $byNetwork[$ip]['accounts'][$userID] = true;
            }
        }
        $out = array();
        foreach ( $byNetwork as $ip => $n )
            if ( count( $n['accounts'] ) >= max( 2, (int)$networkAccounts ) )
                $out[] = array( 'kind' => 'network', 'user_id' => 0, 'count' => $n['count'], 'accounts' => count( $n['accounts'] ),
                                'network' => (string)$ip, 'countries' => array() );
        foreach ( array_unique( array_map( 'intval', $locked ) ) as $userID )
            if ( $userID > 0 )
                $out[] = array( 'kind' => 'locked', 'user_id' => $userID, 'count' => isset( $byUser[$userID] ) ? $byUser[$userID] : 0,
                                'accounts' => 1, 'network' => '', 'countries' => array() );
        arsort( $byUser );
        foreach ( $byUser as $userID => $count )
            if ( (int)$threshold > 0 && $count >= (int)$threshold && !in_array( $userID, $locked ) )
                $out[] = array( 'kind' => 'failures', 'user_id' => (int)$userID, 'count' => $count, 'accounts' => 1, 'network' => '', 'countries' => array() );
        foreach ( $countries as $userID => $list )
        {
            $list = array_values( array_unique( array_filter( array_map( 'strval', (array)$list ) ) ) );
            sort( $list );
            if ( count( $list ) > 1 )
                $out[] = array( 'kind' => 'countries', 'user_id' => (int)$userID, 'count' => count( $list ), 'accounts' => 1, 'network' => '',
                                'countries' => $list );
        }
        return $out;
    }

    /**
     * The suspicious patterns of the last $hours (findPatterns()) read from the user audit log and the running
     * sessions, each with the login and name of its account.
     *
     * @param int $hours
     * @param int|null $now
     * @return array[] findPatterns() with login and name
     */
    public static function patterns( $hours = 24, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $since = $now - (int)$hours * 3600;
        $failures = array();
        $locked = array();
        if ( class_exists( 'expUserSecurityActivity' ) && expUserSecurityActivity::available() )
        {
            $db = eZDB::instance();
            $rows = $db->arrayQuery( 'SELECT event_name, target_type, target_id, client_ip FROM ' . expUserAudit::TABLE
                                     . " WHERE event_name IN ( 'access.session.login.failed', 'access.user.lock' ) AND created >= " . (int)$since );
            foreach ( (array)$rows as $row )
            {
                $userID = $row['target_type'] === 'user' ? (int)$row['target_id'] : 0;
                if ( $row['event_name'] === 'access.user.lock' )
                    $locked[] = $userID;
                else
                    $failures[] = array( 'user_id' => $userID, 'ip' => (string)$row['client_ip'] );
            }
        }
        $countries = array();
        if ( expUserSessionTracker::available() && expUserSessionInfo::available() )
        {
            $timeout = expUserSessionTracker::sessionTimeout();
            $rows = expUserSession::fetchObjectList( expUserSession::definition(), array( 'id', 'user_id', 'last_active', 'remember_until' ),
                                                     array( 'ended' => 0 ), null, null, false );
            $owner = array();
            foreach ( (array)$rows as $row )
                if ( !expUserSessionTracker::hasTimedOut( (int)$row['last_active'], (int)$row['remember_until'], $timeout, $now ) )
                    $owner[(int)$row['id']] = (int)$row['user_id'];
            foreach ( expUserSessionInfo::fetchForSessions( array_keys( $owner ) ) as $sessionID => $info )
                if ( isset( $owner[$sessionID] ) && !empty( $info['country'] ) )
                    $countries[$owner[$sessionID]][] = (string)$info['country'];
        }
        $threshold = (int)expUserSessionTracker::setting( 'FailedSignInAlertThreshold', 5 );
        $out = array();
        foreach ( self::findPatterns( $failures, $locked, $countries, $threshold > 0 ? $threshold : 5 ) as $item )
        {
            $item['login'] = '';
            $item['name'] = '';
            if ( $item['user_id'] > 0 )
            {
                $user = eZUser::fetch( $item['user_id'] );
                $object = $user instanceof eZUser ? eZContentObject::fetch( $item['user_id'] ) : null;
                $item['login'] = $user instanceof eZUser ? (string)$user->attribute( 'login' ) : '';
                $item['name'] = $object instanceof eZContentObject ? (string)$object->attribute( 'name' ) : '';
            }
            $out[] = $item;
        }
        return $out;
    }

    /**
     * The account a form names: a user id or a login (exactly, any case), or an e-mail address.
     *
     * @param string $value
     * @return eZUser|null
     */
    public static function findUser( $value )
    {
        $value = trim( (string)$value );
        if ( $value === '' )
            return null;
        if ( ctype_digit( $value ) )
        {
            $user = eZUser::fetch( (int)$value );
            return $user instanceof eZUser ? $user : null;
        }
        $user = eZUser::fetchByName( $value );
        if ( !$user instanceof eZUser && eZMail::validate( $value ) )
            $user = eZUser::fetchByEmail( $value );
        return $user instanceof eZUser ? $user : null;
    }
}
?>
