<?php
/**
 * expsessionadmin: the sessions of the installation. Own sessions are visible to their user; the administration
 * of other sessions is gated by the setup/administrate policy. A session is named by a short identifier derived
 * from its key, never by the key itself, and the session data is never returned.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expSessionAdminServices extends expUsersBase
{
    /**
     * The services (expServiceBase), declared here rather than assigned after the class: a persistent worker
     * (Velocity) puts the statics of a class loaded during a request back to their declared value before the
     * next one, and an assignment after the class would not run again.
     */
    public static $services = array(
        'own' => array( 'summary' => 'The current session: user, started, cookie, short identifier', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'session' ),
        'ownList' => array( 'summary' => 'The active sessions of the current user (other devices)', 'access' => 'user', 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged sessions' ),
        'ownCount' => array( 'summary' => 'The number of active sessions of the current user', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'count' ),
        'ownRemove' => array( 'summary' => 'End one of your own sessions (POST id)', 'access' => 'user', 'write' => true, 'args' => array( 'id' => 'string' ), 'returns' => 'removed' ),
        'ownRemoveOthers' => array( 'summary' => 'End all your sessions except this one (POST)', 'access' => 'user', 'write' => true, 'args' => array(), 'returns' => 'removed' ),
        'regenerate' => array( 'summary' => 'Issue a new session key for this session (POST)', 'access' => 'user', 'write' => true, 'args' => array(), 'returns' => 'regenerated' ),
        'listAll' => array( 'summary' => 'The active sessions of the installation, paged', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int', 'registered' => 'bool' ), 'returns' => 'paged sessions' ),
        'byUser' => array( 'summary' => 'The sessions of a user', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array( 'id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged sessions' ),
        'count' => array( 'summary' => 'Active, expired and total sessions', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'active, expired, total' ),
        'stats' => array( 'summary' => 'Registered and anonymous sessions and expired ones', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'registered, anonymous, expired' ),
        'expiredCount' => array( 'summary' => 'The number of expired sessions', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'count' ),
        'removeExpired' => array( 'summary' => 'Delete expired sessions (POST)', 'access' => array( 'setup', 'administrate' ), 'write' => true, 'args' => array(), 'returns' => 'removed' ),
        'remove' => array( 'summary' => 'End a session by its identifier (POST id)', 'access' => array( 'setup', 'administrate' ), 'write' => true, 'args' => array( 'id' => 'string' ), 'returns' => 'removed' ),
        'removeByUser' => array( 'summary' => 'End all sessions of a user but your own (POST id)', 'access' => array( 'setup', 'administrate' ), 'write' => true, 'args' => array( 'id' => 'int' ), 'returns' => 'removed' ),
        'settings' => array( 'summary' => 'Session handler and timeouts', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'handler, timeouts' ),
        'handler' => array( 'summary' => 'The session handler class and PHP save handler', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'class, save_handler' ),
    );

    protected static function sid( $key )
    {
        return substr( sha1( 'expservices:' . $key ), 0, 16 );
    }

    protected static function rows( $where = '1=1', $limit = 5000, $offset = 0 )
    {
        return eZDB::instance()->arrayQuery( "SELECT session_key, user_id, expiration_time FROM ezsession WHERE $where ORDER BY expiration_time DESC",
                                              array( 'limit' => $limit, 'offset' => $offset ) );
    }

    protected static function exportRow( array $r )
    {
        $user = (int)$r['user_id'] > 0 ? eZContentObject::fetch( (int)$r['user_id'] ) : null;
        return array( 'id' => self::sid( $r['session_key'] ), 'user_id' => (int)$r['user_id'], 'name' => $user ? $user->attribute( 'name' ) : null,
                      'anonymous' => (int)$r['user_id'] === (int)eZUser::anonymousId() || (int)$r['user_id'] === 0,
                      'expires' => self::iso( $r['expiration_time'] ), 'expired' => (int)$r['expiration_time'] < time(),
                      'current' => function_exists( 'session_id' ) && session_id() !== '' && $r['session_key'] === session_id() );
    }

    protected static function scalar( $sql )
    {
        $r = eZDB::instance()->arrayQuery( $sql );
        return isset( $r[0]['c'] ) ? (int)$r[0]['c'] : 0;
    }

    /** @return array the ezsession row named by its short identifier */
    protected static function find( $id )
    {
        foreach ( self::rows( '1=1', 100000 ) as $r )
            if ( self::sid( $r['session_key'] ) === $id )
                return $r;
        throw new expServiceException( 'No such session', 404 );
    }

    protected static function drop( array $r )
    {
        eZDB::instance()->query( "DELETE FROM ezsession WHERE session_key = '" . eZDB::instance()->escapeString( $r['session_key'] ) . "'" );
    }

    public static function own( array $a = array() )
    {
        self::guard( 'own' );
        $user = eZUser::currentUser();
        return self::ok( array( 'user_id' => (int)$user->attribute( 'contentobject_id' ), 'registered' => (bool)$user->isRegistered(),
                                'started' => eZSession::hasStarted(), 'has_cookie' => (bool)eZSession::userHasSessionCookie(),
                                'id' => session_id() !== '' ? self::sid( session_id() ) : null ) );
    }

    public static function ownList( array $a = array() )
    {
        self::guard( 'ownList' );
        $rows = self::rows( 'user_id = ' . (int)eZUser::currentUserID() . ' AND expiration_time > ' . time() );
        return self::pageOf( array_map( array( 'expSessionAdminServices', 'exportRow' ), $rows ), $a, 0, 1 );
    }

    public static function ownCount( array $a = array() )
    {
        self::guard( 'ownCount' );
        return self::ok( array( 'count' => self::scalar( 'SELECT COUNT(*) AS c FROM ezsession WHERE user_id = ' . (int)eZUser::currentUserID() . ' AND expiration_time > ' . time() ) ) );
    }

    public static function ownRemove( array $a = array() )
    {
        self::guard( 'ownRemove' );
        $r = self::find( self::post( 'id', 'string' ) );
        if ( (int)$r['user_id'] !== (int)eZUser::currentUserID() )
            throw new expServiceException( 'No such session', 404 );
        self::drop( $r );
        return self::ok( array( 'removed' => true ) );
    }

    public static function ownRemoveOthers( array $a = array() )
    {
        self::guard( 'ownRemoveOthers' );
        $n = 0;
        foreach ( self::rows( 'user_id = ' . (int)eZUser::currentUserID() ) as $r )
            if ( $r['session_key'] !== session_id() )
            {
                self::drop( $r );
                $n++;
            }
        return self::ok( array( 'removed' => $n ) );
    }

    public static function regenerate( array $a = array() )
    {
        self::guard( 'regenerate' );
        eZSession::regenerate();
        return self::ok( array( 'regenerated' => true ) );
    }

    public static function listAll( array $a = array() )
    {
        self::guard( 'listAll' );
        list( $limit, $offset ) = self::paging( $a, 0, 1 );
        $registeredOnly = self::arg( $a, 2, 'bool', false );
        $where = ( $registeredOnly ? 'user_id <> ' . (int)eZUser::anonymousId() . ' AND user_id <> 0 AND ' : '' ) . 'expiration_time > ' . time();
        $total = self::scalar( "SELECT COUNT(*) AS c FROM ezsession WHERE $where" );
        return self::page( array_map( array( 'expSessionAdminServices', 'exportRow' ), self::rows( $where, $limit, $offset ) ), $total, $offset, $limit );
    }

    public static function byUser( array $a = array() )
    {
        self::guard( 'byUser' );
        $id = self::arg( $a, 0, 'int' );
        return self::pageOf( array_map( array( 'expSessionAdminServices', 'exportRow' ), self::rows( "user_id = $id" ) ), $a, 1, 2 );
    }

    public static function count( array $a = array() )
    {
        self::guard( 'count' );
        return self::ok( array( 'active' => self::scalar( 'SELECT COUNT(*) AS c FROM ezsession WHERE expiration_time > ' . time() ),
                                'expired' => self::scalar( 'SELECT COUNT(*) AS c FROM ezsession WHERE expiration_time <= ' . time() ),
                                'total' => self::scalar( 'SELECT COUNT(*) AS c FROM ezsession' ) ) );
    }

    public static function stats( array $a = array() )
    {
        self::guard( 'stats' );
        $anon = (int)eZUser::anonymousId();
        $now = time();
        return self::ok( array( 'registered' => self::scalar( "SELECT COUNT(DISTINCT user_id) AS c FROM ezsession WHERE expiration_time > $now AND user_id <> $anon AND user_id <> 0" ),
                                'anonymous' => self::scalar( "SELECT COUNT(*) AS c FROM ezsession WHERE expiration_time > $now AND ( user_id = $anon OR user_id = 0 )" ),
                                'expired' => self::scalar( "SELECT COUNT(*) AS c FROM ezsession WHERE expiration_time <= $now" ) ) );
    }

    public static function expiredCount( array $a = array() )
    {
        self::guard( 'expiredCount' );
        return self::ok( array( 'count' => self::scalar( 'SELECT COUNT(*) AS c FROM ezsession WHERE expiration_time <= ' . time() ) ) );
    }

    public static function removeExpired( array $a = array() )
    {
        self::guard( 'removeExpired' );
        $n = self::scalar( 'SELECT COUNT(*) AS c FROM ezsession WHERE expiration_time <= ' . time() );
        eZDB::instance()->query( 'DELETE FROM ezsession WHERE expiration_time <= ' . time() );
        return self::ok( array( 'removed' => $n ) );
    }

    public static function remove( array $a = array() )
    {
        self::guard( 'remove' );
        $r = self::find( self::post( 'id', 'string' ) );
        if ( $r['session_key'] === session_id() )
            throw new expServiceException( 'This is your own session; sign out instead', 409 );
        self::drop( $r );
        return self::ok( array( 'removed' => true, 'user_id' => (int)$r['user_id'] ) );
    }

    public static function removeByUser( array $a = array() )
    {
        self::guard( 'removeByUser' );
        $id = self::post( 'id', 'int' );
        if ( $id === (int)eZUser::anonymousId() )
            throw new expServiceException( 'The sessions of the anonymous user are not removed one user at a time', 409 );
        $n = 0;
        foreach ( self::rows( "user_id = $id" ) as $r )
            if ( $r['session_key'] !== session_id() )
            {
                self::drop( $r );
                $n++;
            }
        return self::ok( array( 'user_id' => $id, 'removed' => $n ) );
    }

    public static function settings( array $a = array() )
    {
        self::guard( 'settings' );
        $ini = eZINI::instance();
        return self::ok( array( 'handler' => (string)$ini->variable( 'Session', 'Handler' ) ?: 'ezpSessionHandlerPHP',
                                'session_timeout' => (int)$ini->variable( 'Session', 'SessionTimeout' ),
                                'activity_timeout' => (int)$ini->variable( 'Session', 'ActivityTimeout' ),
                                'database_backed' => eZSession::getHandlerInstance() instanceof ezpSessionHandlerDB ) );
    }

    public static function handler( array $a = array() )
    {
        self::guard( 'handler' );
        return self::ok( array( 'class' => get_class( eZSession::getHandlerInstance() ), 'save_handler' => (string)ini_get( 'session.save_handler' ) ) );
    }
}
