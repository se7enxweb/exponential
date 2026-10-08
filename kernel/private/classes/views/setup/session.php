<?php
/**
 * The code of kernel/setup/session.php, moved into a class (#207 stage 1). The file kernel/setup/session.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 * User guide: doc/guides/sessions.md
 */
/*
 * The original header of kernel/setup/session.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{
if ( !function_exists( 'eZFetchActiveSessions' ) ) {
/*
  Get all sessions by limit and offset, and returns it.
  Kept for code that calls it; the work is done by \Exponential\View\Kernel\Setup\Session::fetchSessions().
*/
function eZFetchActiveSessions( $params = array() )
{
    return \Exponential\View\Kernel\Setup\Session::fetchSessions( $params );
}
}

if ( !function_exists( 'eZFetchActiveSessionCount' ) ) {
/*
  Counts active sessions according the filters and returns the count.
  Kept for code that calls it; the work is done by \Exponential\View\Kernel\Setup\Session::countSessions().
*/
function eZFetchActiveSessionCount( $params = array() )
{
    return \Exponential\View\Kernel\Setup\Session::countSessions( $params );
}
}
}

namespace Exponential\View\Kernel\Setup
{

/**
 * Setup > Sessions (setup/session).
 *
 * With a session handler that keeps sessions in the database (ezpSessionHandlerDB) the page lists the sessions,
 * one row per user or, for one user, one row per session, with figures, a filter, a search, sorting and paging,
 * and removes sessions through a confirmation. The viewer's own current session is never removed by a selection.
 * Session keys never reach the page: a row carries a short reference (a hash) and a hint of the key's start.
 *
 * With a handler that keeps sessions elsewhere (PHP's own files, the default) the page says so, explains how to
 * switch, and lists who signed in recently from ezuservisit, which every handler keeps.
 */
class Session extends \Exponential\Runnable\ModuleView
{
    /** sort keys the list accepts, mapped to the column alias it orders by */
    const SORT_COLUMNS = array( 'login' => 'login', 'email' => 'email', 'name' => 'name', 'idle' => 'expiration_time', 'count' => 'count' );

    /** sort keys of the recent sign-in list (no session table) */
    const VISIT_SORT_COLUMNS = array( 'login' => 'login', 'email' => 'email', 'name' => 'name', 'last' => 'current_visit_timestamp', 'logins' => 'login_count' );

    /** the most characters of a search the page keeps */
    const SEARCH_MAX = 100;

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $tpl = \eZTemplate::factory();
        $sessionsRemoved = false;
        $gcSessionsCompleted = true;
        $http = \eZHTTPTool::instance();

        $module = $Params['Module'];
        $ini = \eZINI::instance();
        $sessionTimeout = (int)$ini->variable( 'Session', 'SessionTimeout' );
        $activityTimeout = (int)$ini->variable( 'Session', 'ActivityTimeout' );
        $currentUserID = (int)\eZUser::currentUserID();
        $anonymousID = (int)\eZUser::anonymousId();

        $search = '';
        if ( $http->hasSessionVariable( 'eZSessionSearchText' ) )
            $search = self::cleanSearch( $http->sessionVariable( 'eZSessionSearchText' ) );

        $viewParameters = $scope['Params']['UserParameters'];

        // "Clear search" is a button of its own, outside the module's actions
        if ( $http->hasPostVariable( 'ClearSessionSearchButton' ) )
        {
            $search = '';
            $http->setSessionVariable( 'eZSessionSearchText', '' );
        }

        if ( !\eZSession::getHandlerInstance()->hasBackendAccess() )
        {
            // The handler keeps no rows we can read; show who signed in recently instead.
            $window = 'session';
            if ( $http->hasSessionVariable( 'eZSessionVisitWindow' ) )
                $window = self::cleanWindow( $http->sessionVariable( 'eZSessionVisitWindow' ) );
            if ( $module->isCurrentAction( 'ChangeFilter' ) )
            {
                if ( $http->hasPostVariable( 'SessionSearch' ) )
                    $search = self::cleanSearch( $http->postVariable( 'SessionSearch' ) );
                if ( $http->hasPostVariable( 'VisitWindow' ) )
                    $window = self::cleanWindow( $http->postVariable( 'VisitWindow' ) );
                if ( $http->hasPostVariable( 'ClearSessionSearchButton' ) )
                    $search = '';
                $http->setSessionVariable( 'eZSessionSearchText', $search );
                $http->setSessionVariable( 'eZSessionVisitWindow', $window );
            }

            list( $limit, $limitChoice, $limitChoices ) = \expAdminPagination::chosen( 'setup/session', 'admin_session_list_limit', array( 50, 25, 100 ) );
            $offset = \expAdminPagination::offset( $scope['Params'] );
            $sortBy = self::cleanSort( isset( $viewParameters['sortby'] ) ? $viewParameters['sortby'] : '', self::VISIT_SORT_COLUMNS, 'last' );
            $order = self::cleanOrder( isset( $viewParameters['order'] ) ? $viewParameters['order'] : '', $sortBy === 'last' || $sortBy === 'logins' ? 'desc' : 'asc' );
            $now = time();
            $since = self::windowStart( $window, $now, $activityTimeout, $sessionTimeout );

            $visitParams = array( 'since' => $since, 'search' => $search, 'anonymous_id' => $anonymousID );
            $visitCount = self::countVisits( $visitParams );
            if ( $offset >= $visitCount )
                $offset = 0;
            $visits = self::fetchVisits( $visitParams + array( 'offset' => $offset, 'limit' => $limit, 'sortby' => $sortBy, 'order' => $order ) );
            foreach ( $visits as $i => $visit )
            {
                $visits[$i]['idle_seconds'] = max( 0, $now - (int)$visit['current_visit_timestamp'] );
                $visits[$i]['idle_text'] = self::durationText( $visits[$i]['idle_seconds'] );
                $visits[$i]['is_self'] = (int)$visit['user_id'] === $currentUserID;
            }

            $tpl->setVariable( 'session_handler', get_class( \eZSession::getHandlerInstance() ) );
            $tpl->setVariable( 'php_save_handler', (string)ini_get( 'session.save_handler' ) );
            $tpl->setVariable( 'session_timeout', $sessionTimeout );
            $tpl->setVariable( 'session_timeout_text', self::durationText( $sessionTimeout ) );
            $tpl->setVariable( 'activity_timeout_text', self::durationText( $activityTimeout ) );
            $tpl->setVariable( 'visit_summary', array(
                'hour'    => self::countVisits( array( 'since' => self::windowStart( 'hour', $now, $activityTimeout, $sessionTimeout ), 'anonymous_id' => $anonymousID ) ),
                'day'     => self::countVisits( array( 'since' => self::windowStart( 'day', $now, $activityTimeout, $sessionTimeout ), 'anonymous_id' => $anonymousID ) ),
                'session' => self::countVisits( array( 'since' => self::windowStart( 'session', $now, $activityTimeout, $sessionTimeout ), 'anonymous_id' => $anonymousID ) ),
            ) );
            $tpl->setVariable( 'visit_window', $window );
            $tpl->setVariable( 'visit_list', $visits );
            $tpl->setVariable( 'visit_count', $visitCount );
            $tpl->setVariable( 'session_search', $search );
            $tpl->setVariable( 'session_sort', $sortBy );
            $tpl->setVariable( 'session_order', $order );
            $tpl->setVariable( 'current_user_id', $currentUserID );
            $tpl->setVariable( 'page_limit', $limit );
            $tpl->setVariable( 'limit_choice', $limitChoice );
            $tpl->setVariable( 'limit_choices', $limitChoices );
            $tpl->setVariable( 'view_parameters', array( 'offset' => $offset, 'sortby' => $sortBy, 'order' => $order ) );

            $Result = array();
            $Result['content'] = $tpl->fetch( "design:setup/session_no_db.tpl" );
            $Result['path'] = array( array( 'url' => false,
                                            'text' => \ezpI18n::tr( 'kernel/setup', 'Session admin' ) ) );
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Result );
        }


        list( $limit, $limitChoice, $limitChoices ) = \expAdminPagination::chosen( 'setup/session', 'admin_session_list_limit', array( 50, 25, 100 ) );
        $param['limit'] = $limit;

        $filterType = 'registered';
        if ( $http->hasSessionVariable( 'eZSessionFilterType' ) )
            $filterType = $http->sessionVariable( 'eZSessionFilterType' );
        $expirationFilterType = 'active';
        if ( $http->hasSessionVariable( 'eZSessionExpirationFilterType' ) )
            $expirationFilterType = $http->sessionVariable( 'eZSessionExpirationFilterType' );

        $userID = $Params['UserID'];
        if ( $userID !== false && $userID !== null && $userID !== '' )
            $userID = (int)$userID;
        $confirmed = $http->hasPostVariable( 'ConfirmSessionRemoval' );
        $feedback = false;

        if ( $module->isCurrentAction( 'ShowAllUsers' ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'session' ) );
        }
        else if ( $module->isCurrentAction( 'ChangeFilter' ) )
        {
            $filterType = $module->actionParameter( 'FilterType' );
            if ( !in_array( $filterType, array( 'everyone', 'registered', 'anonymous' ) ) )
                $filterType = 'registered';
            if ( $module->hasActionParameter( 'InactiveUsersCheckExists' ) )
            {
                $expirationFilterType = 'active';
                if ( $module->hasActionParameter( 'InactiveUsersCheck' ) )
                    $expirationFilterType = 'all';
            }
            if ( $module->hasActionParameter( 'ExpirationFilterType' ) )
            {
                $expirationFilterType = $module->actionParameter( 'ExpirationFilterType' );
            }
            if ( !in_array( $expirationFilterType, array( 'all', 'active' ) ) )
                $expirationFilterType = 'active';
            if ( $http->hasPostVariable( 'SessionSearch' ) )
                $search = self::cleanSearch( $http->postVariable( 'SessionSearch' ) );
            if ( $http->hasPostVariable( 'ClearSessionSearchButton' ) )
                $search = '';
            $http->setSessionVariable( 'eZSessionFilterType', $filterType );
            $http->setSessionVariable( 'eZSessionExpirationFilterType', $expirationFilterType );
            $http->setSessionVariable( 'eZSessionSearchText', $search );
        }
        else if ( $module->isCurrentAction( 'RemoveAllSessions' ) )
        {
            if ( !$confirmed )
            {
                return $this->confirmation( $tpl, 'all', array(), array(), $userID, $currentUserID );
            }
            $feedback = array( 'type' => 'all', 'count' => (int)\eZSession::countActive() );
            \eZSession::cleanup();
            $sessionsRemoved = true;
        }
        else if ( $module->isCurrentAction( 'RemoveTimedOutSessions' ) )
        {
            // the expired sessions and the baskets they leave, as the command and the cronjob part do. Loaded by path:
            // a server whose workers kept an autoload array from before the class existed (Velocity) still works.
            require_once 'kernel/private/classes/services/sessiongarbagecollector.php';
            $before = self::countExpired( time() );
            $gcSessionsCompleted = \Exponential\Service\SessionGarbageCollector::collect();
            $feedback = array( 'type' => 'timed_out', 'count' => max( 0, $before - self::countExpired( time() ) ) );
            $sessionsRemoved = true;
        }
        else if ( $module->isCurrentAction( 'RemoveSelectedSessions' ) )
        {
            if ( $userID )
            {
                // One user's sessions: the page posts references (SessionRefArray[]); SessionKeyArray[] is still
                // accepted from older overrides. Either way only keys of this user's sessions are removed.
                $keys = self::keysOfUser( $userID );
                $chosen = array();
                if ( $http->hasPostVariable( 'SessionRefArray' ) )
                    $chosen = self::keysForRefs( (array)$http->postVariable( 'SessionRefArray' ), $keys );
                if ( $http->hasPostVariable( 'SessionKeyArray' ) )
                    $chosen = array_values( array_unique( array_merge( $chosen, array_values( array_intersect( $keys, array_map( 'strval', (array)$http->postVariable( 'SessionKeyArray' ) ) ) ) ) ) );
                list( $chosen, $keptOwn ) = self::withoutKey( $chosen, (string)session_id() );
                if ( !$chosen )
                {
                    $feedback = array( 'type' => $keptOwn ? 'own_only' : 'none_selected' );
                }
                else if ( !$confirmed )
                {
                    return $this->confirmation( $tpl, 'sessions', array( $userID ), array_map( array( __CLASS__, 'sessionRef' ), $chosen ), $userID, $currentUserID, $keptOwn );
                }
                else
                {
                    foreach ( $chosen as $key )
                        \eZSession::getHandlerInstance()->destroy( $key );
                    $feedback = array( 'type' => 'sessions', 'count' => count( $chosen ), 'kept_own' => $keptOwn );
                    $sessionsRemoved = true;
                }
            }
            else
            {
                $userIDArray = array();
                if ( $http->hasPostVariable( 'UserIDArray' ) )
                    $userIDArray = self::cleanIDs( (array)$http->postVariable( 'UserIDArray' ) );
                if ( !$userIDArray )
                {
                    $feedback = array( 'type' => 'none_selected' );
                }
                else if ( !$confirmed )
                {
                    return $this->confirmation( $tpl, 'users', $userIDArray, array(), false, $currentUserID );
                }
                else
                {
                    $count = 0;
                    $keptOwn = false;
                    $others = array_values( array_diff( $userIDArray, array( $currentUserID ) ) );
                    if ( $others )
                    {
                        $count += self::countSessionsOfUsers( $others );
                        \eZSession::getHandlerInstance()->deleteByUserIDs( $others );
                    }
                    if ( in_array( $currentUserID, $userIDArray, true ) )
                    {
                        // Your own user: its other sessions go, the one this request runs in stays.
                        list( $ownKeys, $keptOwn ) = self::withoutKey( self::keysOfUser( $currentUserID ), (string)session_id() );
                        foreach ( $ownKeys as $key )
                            \eZSession::getHandlerInstance()->destroy( $key );
                        $count += count( $ownKeys );
                    }
                    $feedback = array( 'type' => 'users', 'users' => count( $userIDArray ), 'count' => $count, 'kept_own' => $keptOwn );
                    $sessionsRemoved = true;
                }
            }
        }

        if ( isset( $viewParameters['offset'] ) and
             is_numeric( $viewParameters['offset'] ) )
        {
            $param['offset'] = max( 0, (int)$viewParameters['offset'] );
        }
        else
        {
            $param['offset'] = 0;
            $viewParameters['offset'] = 0;
        }

        $param['sortby'] = false;
        $param['filter_type'] = $filterType;
        $param['expiration_filter'] = $expirationFilterType;
        $param['user_id'] = $userID;
        $param['search'] = $search;
        if ( isset( $viewParameters['sortby'] ) )
            $param['sortby'] = self::cleanSort( $viewParameters['sortby'], self::SORT_COLUMNS, 'idle' );
        $defaultOrder = ( !$param['sortby'] || $param['sortby'] === 'idle' || $param['sortby'] === 'count' ) ? 'desc' : 'asc';
        $param['order'] = self::cleanOrder( isset( $viewParameters['order'] ) ? $viewParameters['order'] : '', $defaultOrder );
        $sessionsActive = \eZSession::countActive();
        $sessionsCount = eZFetchActiveSessionCount( $param );
        if ( $param['offset'] >= $sessionsCount && $sessionsCount > 0 )
        {
            // a page past the end (rows were removed): show the first one
            $param['offset'] = 0;
            $viewParameters['offset'] = 0;
        }
        $sessionsList = eZFetchActiveSessions( $param );

        $ownKey = (string)session_id();
        $ownRef = $ownKey !== '' ? self::sessionRef( $ownKey ) : '';
        foreach ( $sessionsList as $i => $session )
        {
            $sessionsList[$i]['is_self'] = (int)$session['user_id'] === $currentUserID;
            $sessionsList[$i]['is_current_session'] = $userID && $ownRef !== '' && $session['ref'] === $ownRef;
            $sessionsList[$i]['is_anonymous'] = (int)$session['user_id'] === $anonymousID;
        }

        $tpl->setVariable( "gc_sessions_completed", $gcSessionsCompleted );
        $tpl->setVariable( "sessions_removed", $sessionsRemoved );
        $tpl->setVariable( "sessions_active", $sessionsActive );
        $tpl->setVariable( "sessions_count", $sessionsCount );
        $tpl->setVariable( "sessions_list", $sessionsList );
        $tpl->setVariable( "page_limit", $param['limit'] );
        $tpl->setVariable( 'limit_choice', $limitChoice );
        $tpl->setVariable( 'limit_choices', $limitChoices );
        $viewParameters['sortby'] = $param['sortby'] ? $param['sortby'] : '';
        $viewParameters['order'] = $param['sortby'] ? $param['order'] : '';
        $tpl->setVariable( "view_parameters", $viewParameters );
        $tpl->setVariable( "form_parameter_string", $viewParameters );
        $tpl->setVariable( 'filter_type', $filterType );
        $tpl->setVariable( 'expiration_filter_type', $expirationFilterType );
        $tpl->setVariable( 'user_id', $userID );
        $tpl->setVariable( 'session_search', $search );
        $tpl->setVariable( 'session_sort', $param['sortby'] ? $param['sortby'] : 'idle' );
        $tpl->setVariable( 'session_order', $param['order'] );
        $tpl->setVariable( 'session_feedback', $feedback );
        $tpl->setVariable( 'session_summary', self::summary( time(), $sessionTimeout, $activityTimeout, $anonymousID ) );
        $tpl->setVariable( 'session_timeout_text', self::durationText( $sessionTimeout ) );
        $tpl->setVariable( 'activity_timeout_text', self::durationText( $activityTimeout ) );
        $tpl->setVariable( 'current_user_id', $currentUserID );
        $tpl->setVariable( 'session_handler', get_class( \eZSession::getHandlerInstance() ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:setup/session.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/setup', 'Session admin' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * The confirmation page before sessions are removed.
     *
     * @param \eZTemplate $tpl
     * @param string $type 'all', 'users' or 'sessions'
     * @param int[] $userIDs
     * @param string[] $refs session references (type 'sessions')
     * @param int|false $userID the user whose sessions are listed, if any
     * @param int $currentUserID
     * @param bool $keptOwn the viewer's current session was among the chosen and is left out
     */
    private function confirmation( $tpl, $type, array $userIDs, array $refs, $userID, $currentUserID, $keptOwn = false )
    {
        $users = array();
        $sessionCount = 0;
        if ( $type === 'all' )
        {
            $sessionCount = (int)\eZSession::countActive();
        }
        else if ( $userIDs )
        {
            foreach ( self::usersWithSessionCounts( $userIDs ) as $row )
            {
                $row['is_self'] = (int)$row['user_id'] === (int)$currentUserID;
                $users[] = $row;
                $sessionCount += (int)$row['count'];
            }
            if ( $type === 'sessions' )
                $sessionCount = count( $refs );
        }

        $tpl->setVariable( 'confirm_type', $type );
        $tpl->setVariable( 'confirm_users', $users );
        $tpl->setVariable( 'confirm_user_ids', $userIDs );
        $tpl->setVariable( 'confirm_refs', $refs );
        $tpl->setVariable( 'confirm_session_count', $sessionCount );
        $tpl->setVariable( 'confirm_kept_own', $keptOwn );
        $tpl->setVariable( 'user_id', $userID );
        $tpl->setVariable( 'current_user_id', (int)$currentUserID );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:setup/session_confirmremove.tpl' );
        $Result['path'] = array( array( 'url' => '/setup/session', 'text' => \ezpI18n::tr( 'kernel/setup', 'Session admin' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/setup', 'Remove sessions' ) ) );
        return $this->viewResult( $Result, null );
    }

    // ---- Data ---------------------------------------------------------------------------------------------------

    /**
     * The WHERE conditions (without WHERE) shared by the list and its count.
     *
     * @param array $params filter_type, expiration_filter, user_id, search
     * @return string[]
     */
    public static function conditions( array $params, $now = null )
    {
        $db = \eZDB::instance();
        $anonymousID = isset( $params['anonymous_id'] ) ? (int)$params['anonymous_id'] : (int)\eZUser::anonymousId();
        $conditions = array();

        $userID = isset( $params['user_id'] ) ? (int)$params['user_id'] : 0;
        if ( $userID )
        {
            $conditions[] = 'ezsession.user_id = ' . $userID;
        }
        else
        {
            $filterType = isset( $params['filter_type'] ) ? $params['filter_type'] : 'everyone';
            if ( $filterType === 'registered' )
                $conditions[] = 'ezsession.user_id != ' . $anonymousID;
            else if ( $filterType === 'anonymous' )
                $conditions[] = 'ezsession.user_id = ' . $anonymousID;
        }

        if ( isset( $params['expiration_filter'] ) && $params['expiration_filter'] === 'active' )
        {
            $ini = \eZINI::instance();
            $time = ( $now === null ? time() : (int)$now ) + (int)$ini->variable( 'Session', 'SessionTimeout' ) - (int)$ini->variable( 'Session', 'ActivityTimeout' );
            $conditions[] = 'ezsession.expiration_time > ' . (int)$time;
        }

        $search = isset( $params['search'] ) ? self::cleanSearch( $params['search'] ) : '';
        if ( $search !== '' )
        {
            $like = $db->escapeString( '%' . self::escapeLike( mb_strtolower( $search ) ) . '%' );
            $conditions[] = "( LOWER( ezuser.login ) LIKE '$like' ESCAPE '!' OR LOWER( ezuser.email ) LIKE '$like' ESCAPE '!' OR LOWER( ezcontentobject.name ) LIKE '$like' ESCAPE '!' )";
        }
        return $conditions;
    }

    /**
     * One page of sessions: per user (count, latest expiry) or, for one user, per session.
     * Each row: user_id, count (per user), expiration_time, session_key (kept for overrides; one key of the user),
     * ref, key_hint, idle_time, idle (hour, minute, second), idle_seconds, idle_text, last_activity, login, email, name.
     *
     * @param array $params limit, offset, sortby, order, filter_type, expiration_filter, user_id, search
     * @return array
     */
    public static function fetchSessions( array $params = array() )
    {
        $limit = isset( $params['limit'] ) ? (int)$params['limit'] : 20;
        $offset = isset( $params['offset'] ) ? (int)$params['offset'] : 0;
        $userID = isset( $params['user_id'] ) ? (int)$params['user_id'] : 0;
        $sortBy = self::cleanSort( isset( $params['sortby'] ) ? $params['sortby'] : '', self::SORT_COLUMNS, 'idle' );
        if ( $userID && $sortBy === 'count' )
            $sortBy = 'idle';
        $order = self::cleanOrder( isset( $params['order'] ) ? $params['order'] : '', $sortBy === 'idle' || $sortBy === 'count' ? 'desc' : 'asc' );

        if ( $userID )
        {
            $select = 'ezsession.session_key AS session_key, MAX( ezsession.expiration_time ) AS expiration_time, MAX( ezsession.user_id ) AS user_id, 1 AS count';
            $group = 'GROUP BY ezsession.session_key';
        }
        else
        {
            $select = 'ezsession.user_id AS user_id, MAX( ezsession.expiration_time ) AS expiration_time, MAX( ezsession.session_key ) AS session_key, COUNT( ezsession.user_id ) AS count';
            $group = 'GROUP BY ezsession.user_id';
        }

        $conditions = self::conditions( $params );
        $where = $conditions ? ' AND ' . implode( ' AND ', $conditions ) : '';
        $direction = $order === 'desc' ? 'DESC' : 'ASC';
        $orderBy = self::SORT_COLUMNS[$sortBy] . ' ' . $direction;
        if ( $sortBy !== 'idle' )
            $orderBy .= ', expiration_time DESC';

        $db = \eZDB::instance();
        $query = "SELECT $select, MAX( ezuser.login ) AS login, MAX( ezuser.email ) AS email, MAX( ezcontentobject.name ) AS name
FROM ezsession, ezuser, ezcontentobject
WHERE ezsession.user_id = ezuser.contentobject_id AND
      ezsession.user_id = ezcontentobject.id
      $where
$group
ORDER BY $orderBy";

        $rows = $db->arrayQuery( $query, array( 'offset' => $offset, 'limit' => $limit ) );

        $time = time();
        $sessionTimeout = (int)\eZINI::instance()->variable( 'Session', 'SessionTimeout' );

        $resultArray = array();
        foreach ( (array)$rows as $row )
        {
            $resultArray[] = self::describeRow( $row, $time, $sessionTimeout, !$userID );
        }
        return $resultArray;
    }

    /**
     * One row of the list as the template gets it. Pure: no database.
     *
     * @param array $row user_id, expiration_time, session_key, count, login, email, name
     * @param int $time now
     * @param int $sessionTimeout
     * @param bool $perUser rows are per user (with a count)
     * @return array
     */
    public static function describeRow( array $row, $time, $sessionTimeout, $perUser = true )
    {
        $session = array();
        $session['user_id'] = (int)$row['user_id'];
        if ( $perUser )
            $session['count'] = (int)$row['count'];
        $session['expiration_time'] = (int)$row['expiration_time'];
        $session['session_key'] = (string)$row['session_key'];
        $session['ref'] = self::sessionRef( (string)$row['session_key'] );
        $session['key_hint'] = self::keyHint( (string)$row['session_key'] );
        $session['idle_time'] = (int)$row['expiration_time'] - (int)$sessionTimeout;
        $session['last_activity'] = $session['idle_time'];
        $idleTime = (int)$time - (int)$row['expiration_time'] + (int)$sessionTimeout;
        $session['idle_seconds'] = $idleTime;
        $session['idle_text'] = self::durationText( max( 0, $idleTime ) );
        $session['expired'] = (int)$row['expiration_time'] < (int)$time;
        $session['idle']['hour'] = intdiv( $idleTime, 3600 );
        $session['idle']['minute'] = intdiv( $idleTime, 60 ) % 60;
        $session['idle']['second'] = abs( $idleTime % 60 );
        if ( $session['idle']['minute'] < 10 && $session['idle']['minute'] >= 0 )
            $session['idle']['minute'] = "0" . $session['idle']['minute'];
        if ( $session['idle']['second'] < 10 )
            $session['idle']['second'] = "0" . $session['idle']['second'];
        $session['email'] = isset( $row['email'] ) ? (string)$row['email'] : '';
        $session['login'] = isset( $row['login'] ) ? (string)$row['login'] : '';
        $session['name'] = isset( $row['name'] ) ? (string)$row['name'] : '';
        return $session;
    }

    /**
     * How many rows the list has with the filters: users, or one user's sessions.
     *
     * @param array $params filter_type, expiration_filter, user_id, search
     * @return int
     */
    public static function countSessions( array $params = array() )
    {
        $userID = isset( $params['user_id'] ) ? (int)$params['user_id'] : 0;
        $conditions = self::conditions( $params );
        $where = $conditions ? ' AND ' . implode( ' AND ', $conditions ) : '';
        $what = $userID ? 'COUNT( ezsession.session_key )' : 'COUNT( DISTINCT ezsession.user_id )';
        $rows = \eZDB::instance()->arrayQuery( "SELECT $what AS count
FROM ezsession, ezuser, ezcontentobject
WHERE ezsession.user_id = ezuser.contentobject_id AND
      ezsession.user_id = ezcontentobject.id
      $where" );
        return isset( $rows[0]['count'] ) ? (int)$rows[0]['count'] : 0;
    }

    /**
     * The figures above the list.
     *
     * @return array total, registered, anonymous, users_active, expired
     */
    public static function summary( $now, $sessionTimeout, $activityTimeout, $anonymousID )
    {
        $db = \eZDB::instance();
        $anonymousID = (int)$anonymousID;
        $activeSince = (int)$now + (int)$sessionTimeout - (int)$activityTimeout;
        $rows = $db->arrayQuery( "SELECT COUNT( * ) AS total,
       SUM( CASE WHEN user_id = $anonymousID THEN 1 ELSE 0 END ) AS anonymous,
       SUM( CASE WHEN expiration_time < " . (int)$now . " THEN 1 ELSE 0 END ) AS expired
FROM ezsession" );
        $users = $db->arrayQuery( "SELECT COUNT( DISTINCT user_id ) AS count FROM ezsession
WHERE user_id != $anonymousID AND expiration_time > $activeSince" );
        $total = isset( $rows[0]['total'] ) ? (int)$rows[0]['total'] : 0;
        $anonymous = isset( $rows[0]['anonymous'] ) ? (int)$rows[0]['anonymous'] : 0;
        return array(
            'total'        => $total,
            'registered'   => $total - $anonymous,
            'anonymous'    => $anonymous,
            'users_active' => isset( $users[0]['count'] ) ? (int)$users[0]['count'] : 0,
            'expired'      => isset( $rows[0]['expired'] ) ? (int)$rows[0]['expired'] : 0,
        );
    }

    /** @return int sessions whose expiry time has passed */
    public static function countExpired( $now )
    {
        $rows = \eZDB::instance()->arrayQuery( 'SELECT COUNT( * ) AS count FROM ezsession WHERE expiration_time < ' . (int)$now );
        return isset( $rows[0]['count'] ) ? (int)$rows[0]['count'] : 0;
    }

    /** @return string[] the session keys of one user */
    public static function keysOfUser( $userID )
    {
        $rows = \eZDB::instance()->arrayQuery( 'SELECT session_key FROM ezsession WHERE user_id = ' . (int)$userID );
        $keys = array();
        foreach ( (array)$rows as $row )
            $keys[] = (string)$row['session_key'];
        return $keys;
    }

    /** @return int the sessions of these users */
    public static function countSessionsOfUsers( array $userIDs )
    {
        $userIDs = self::cleanIDs( $userIDs );
        if ( !$userIDs )
            return 0;
        $rows = \eZDB::instance()->arrayQuery( 'SELECT COUNT( * ) AS count FROM ezsession WHERE user_id IN ( ' . implode( ', ', $userIDs ) . ' )' );
        return isset( $rows[0]['count'] ) ? (int)$rows[0]['count'] : 0;
    }

    /** @return array user_id, name, login, count for each of these users that exists */
    public static function usersWithSessionCounts( array $userIDs )
    {
        $userIDs = self::cleanIDs( $userIDs );
        if ( !$userIDs )
            return array();
        $rows = \eZDB::instance()->arrayQuery( 'SELECT ezuser.contentobject_id AS user_id, MAX( ezcontentobject.name ) AS name, MAX( ezuser.login ) AS login, COUNT( ezsession.session_key ) AS count
FROM ezuser
LEFT JOIN ezsession ON ezsession.user_id = ezuser.contentobject_id
LEFT JOIN ezcontentobject ON ezcontentobject.id = ezuser.contentobject_id
WHERE ezuser.contentobject_id IN ( ' . implode( ', ', $userIDs ) . ' )
GROUP BY ezuser.contentobject_id
ORDER BY name ASC' );
        $result = array();
        foreach ( (array)$rows as $row )
            $result[] = array( 'user_id' => (int)$row['user_id'], 'name' => (string)$row['name'], 'login' => (string)$row['login'], 'count' => (int)$row['count'] );
        return $result;
    }

    /**
     * Who signed in since a time, from ezuservisit (kept by every session handler).
     *
     * @param array $params since, search, anonymous_id, offset, limit, sortby, order
     * @return array user_id, name, login, email, current_visit_timestamp, last_visit_timestamp, login_count
     */
    public static function fetchVisits( array $params )
    {
        $sortBy = self::cleanSort( isset( $params['sortby'] ) ? $params['sortby'] : '', self::VISIT_SORT_COLUMNS, 'last' );
        $order = self::cleanOrder( isset( $params['order'] ) ? $params['order'] : '', 'desc' );
        $orderBy = self::VISIT_SORT_COLUMNS[$sortBy] . ' ' . ( $order === 'desc' ? 'DESC' : 'ASC' );
        if ( $sortBy !== 'last' )
            $orderBy .= ', current_visit_timestamp DESC';
        $rows = \eZDB::instance()->arrayQuery( 'SELECT ezuservisit.user_id AS user_id, ezcontentobject.name AS name, ezuser.login AS login, ezuser.email AS email,
       ezuservisit.current_visit_timestamp AS current_visit_timestamp, ezuservisit.last_visit_timestamp AS last_visit_timestamp,
       ezuservisit.login_count AS login_count
FROM ezuservisit, ezuser, ezcontentobject
WHERE ezuservisit.user_id = ezuser.contentobject_id AND ezuservisit.user_id = ezcontentobject.id
      AND ' . implode( ' AND ', self::visitConditions( $params ) ) . '
ORDER BY ' . $orderBy,
            array( 'offset' => isset( $params['offset'] ) ? (int)$params['offset'] : 0, 'limit' => isset( $params['limit'] ) ? (int)$params['limit'] : 50 ) );
        $result = array();
        foreach ( (array)$rows as $row )
        {
            $result[] = array( 'user_id' => (int)$row['user_id'], 'name' => (string)$row['name'], 'login' => (string)$row['login'],
                               'email' => (string)$row['email'], 'current_visit_timestamp' => (int)$row['current_visit_timestamp'],
                               'last_visit_timestamp' => (int)$row['last_visit_timestamp'], 'login_count' => (int)$row['login_count'] );
        }
        return $result;
    }

    /** @return int how many users fetchVisits() would list in all */
    public static function countVisits( array $params )
    {
        $rows = \eZDB::instance()->arrayQuery( 'SELECT COUNT( * ) AS count
FROM ezuservisit, ezuser, ezcontentobject
WHERE ezuservisit.user_id = ezuser.contentobject_id AND ezuservisit.user_id = ezcontentobject.id
      AND ' . implode( ' AND ', self::visitConditions( $params ) ) );
        return isset( $rows[0]['count'] ) ? (int)$rows[0]['count'] : 0;
    }

    /** @return string[] */
    private static function visitConditions( array $params )
    {
        $conditions = array( 'ezuservisit.current_visit_timestamp > ' . (int)$params['since'] );
        $conditions[] = 'ezuservisit.user_id != ' . (int)( isset( $params['anonymous_id'] ) ? $params['anonymous_id'] : \eZUser::anonymousId() );
        $search = isset( $params['search'] ) ? self::cleanSearch( $params['search'] ) : '';
        if ( $search !== '' )
        {
            $like = \eZDB::instance()->escapeString( '%' . self::escapeLike( mb_strtolower( $search ) ) . '%' );
            $conditions[] = "( LOWER( ezuser.login ) LIKE '$like' ESCAPE '!' OR LOWER( ezuser.email ) LIKE '$like' ESCAPE '!' OR LOWER( ezcontentobject.name ) LIKE '$like' ESCAPE '!' )";
        }
        return $conditions;
    }

    // ---- Pure helpers (tested in tests/tests/kernel/classes/setup/SessionPageTest.php) --------------------------

    /** A reference to a session that does not give its key away: 16 hex digits of its SHA-256. */
    public static function sessionRef( $key )
    {
        return substr( hash( 'sha256', 'exp-session-ref:' . (string)$key ), 0, 16 );
    }

    /** The first four characters of a key and an ellipsis, enough to tell two sessions apart. */
    public static function keyHint( $key )
    {
        $key = (string)$key;
        if ( $key === '' )
            return '';
        return substr( $key, 0, 4 ) . "\u{2026}";
    }

    /**
     * The keys among $keys whose reference is in $refs.
     *
     * @param string[] $refs
     * @param string[] $keys
     * @return string[]
     */
    public static function keysForRefs( array $refs, array $keys )
    {
        $refs = array_flip( array_map( 'strval', $refs ) );
        $result = array();
        foreach ( $keys as $key )
        {
            if ( isset( $refs[self::sessionRef( $key )] ) )
                $result[] = (string)$key;
        }
        return $result;
    }

    /**
     * $keys without $own.
     *
     * @return array( string[] $keys, bool $ownWasThere )
     */
    public static function withoutKey( array $keys, $own )
    {
        $own = (string)$own;
        $result = array();
        $found = false;
        foreach ( $keys as $key )
        {
            if ( $own !== '' && hash_equals( $own, (string)$key ) )
            {
                $found = true;
                continue;
            }
            $result[] = (string)$key;
        }
        return array( $result, $found );
    }

    /** @return int[] positive whole numbers, each once */
    public static function cleanIDs( array $ids )
    {
        $result = array();
        foreach ( $ids as $id )
        {
            if ( is_scalar( $id ) && preg_match( '/^\d+$/', trim( (string)$id ) ) && (int)$id > 0 )
                $result[(int)$id] = (int)$id;
        }
        return array_values( $result );
    }

    /** A search as typed, trimmed, without control characters and at most SEARCH_MAX characters. */
    public static function cleanSearch( $search )
    {
        if ( !is_scalar( $search ) )
            return '';
        $search = preg_replace( '/[\x00-\x1F\x7F]+/u', ' ', (string)$search );
        $search = trim( preg_replace( '/\s+/u', ' ', (string)$search ) );
        return mb_substr( $search, 0, self::SEARCH_MAX );
    }

    /** Escapes LIKE's wildcards with "!" (the queries say ESCAPE '!', which reads the same on every database). */
    public static function escapeLike( $text )
    {
        return str_replace( array( '!', '%', '_' ), array( '!!', '!%', '!_' ), (string)$text );
    }

    /** @return string a key of $columns, or $default */
    public static function cleanSort( $sort, array $columns, $default )
    {
        return is_string( $sort ) && isset( $columns[$sort] ) ? $sort : $default;
    }

    /** @return string 'asc' or 'desc' */
    public static function cleanOrder( $order, $default = 'asc' )
    {
        return $order === 'asc' || $order === 'desc' ? $order : $default;
    }

    /** @return string 'hour', 'day' or 'session' */
    public static function cleanWindow( $window )
    {
        return in_array( $window, array( 'hour', 'day', 'session' ), true ) ? $window : 'session';
    }

    /** @return int the time a window of the recent sign-in list starts */
    public static function windowStart( $window, $now, $activityTimeout, $sessionTimeout )
    {
        switch ( self::cleanWindow( $window ) )
        {
            case 'hour': return (int)$now - max( 60, (int)$activityTimeout );
            case 'day':  return (int)$now - 86400;
        }
        return (int)$now - max( 60, (int)$sessionTimeout );
    }

    /** A duration in words: "45 s", "12 min", "3 h 5 min", "2 d 4 h". */
    public static function durationText( $seconds )
    {
        $seconds = max( 0, (int)$seconds );
        if ( $seconds < 60 )
            return \ezpI18n::tr( 'kernel/setup', '%count s', null, array( '%count' => $seconds ) );
        $minutes = intdiv( $seconds, 60 );
        if ( $minutes < 60 )
            return \ezpI18n::tr( 'kernel/setup', '%count min', null, array( '%count' => $minutes ) );
        $hours = intdiv( $minutes, 60 );
        if ( $hours < 24 )
        {
            $rest = $minutes % 60;
            return $rest ? \ezpI18n::tr( 'kernel/setup', '%hours h %minutes min', null, array( '%hours' => $hours, '%minutes' => $rest ) )
                         : \ezpI18n::tr( 'kernel/setup', '%count h', null, array( '%count' => $hours ) );
        }
        $days = intdiv( $hours, 24 );
        $rest = $hours % 24;
        return $rest ? \ezpI18n::tr( 'kernel/setup', '%days d %hours h', null, array( '%days' => $days, '%hours' => $rest ) )
                     : \ezpI18n::tr( 'kernel/setup', '%count d', null, array( '%count' => $days ) );
    }
}

}
