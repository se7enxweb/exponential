<?php
/**
 * The code of kernel/user/sessionreport.php. The file kernel/user/sessionreport.php is one call to it.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\User
{

/**
 * user/sessionreport, "Sign-in report" (policy user/sessions_manage): the running sessions of the site and the users
 * they belong to, the sign-ins, failed attempts and new sessions of the last 14 days, and the running sessions of
 * every user, most recently used first (expUserSessionReport). Works with every session handler: it reads
 * expuser_session, not the handler's storage.
 *
 * Buttons (POST, with the page's token, an HMAC of this session's own token, besides the form token of ezformtoken):
 * RevokeSessionButton=<row id> signs one session out (of the user the row belongs to); ForceSignOutButton with SignOutUser (a user
 * id, login or e-mail address) signs that user out everywhere (expUserSessionTracker::revokeAll(): every row ended as
 * revoked_admin, and with the database session handler the user's sessions deleted at once), recorded as
 * access.session.revoke (by admin, scope all). The viewer's own current session is never signed out from here.
 * "Verify it's you" asks before both ([ReauthGroup_users]).
 * Guide: doc/eng-us/features/6.0/user-sessions.md
 */
class Sessionreport extends \Exponential\Runnable\ModuleView
{
    const MESSAGE_SESSION = 'expUserSessionReportMessage';
    const DAYS = 14;
    const LIMIT = 100;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $viewer = \eZUser::currentUser();
        $viewerID = (int)$viewer->attribute( 'contentobject_id' );
        $enabled = \expUserSessionTracker::enabled();
        $available = $enabled && \expUserSessionTracker::available();

        if ( $available && \eZSys::serverVariable( 'REQUEST_METHOD', true ) === 'POST' )
        {
            $message = null;
            if ( !self::checkFormToken( $http ) )
                $message = array( 'key' => 'token', 'level' => 'bad' );
            elseif ( $http->hasPostVariable( 'RevokeSessionButton' ) )
            {
                $sessionID = (int)$http->postVariable( 'RevokeSessionButton' );
                $row = \expUserSession::fetchByID( $sessionID );
                $userID = $row ? (int)$row->attribute( 'user_id' ) : 0;
                $current = \expUserSessionTracker::currentRowID();
                if ( $current !== null && $sessionID === (int)$current )
                    $message = array( 'key' => 'current', 'level' => 'warn' );
                else
                    $message = \expUserSessionTracker::revoke( $userID, $sessionID, $viewerID )
                        ? array( 'key' => 'revoked', 'level' => 'ok' ) : array( 'key' => 'not_found', 'level' => 'warn' );
            }
            elseif ( $http->hasPostVariable( 'ForceSignOutButton' ) )
            {
                $user = \expUserSessionReport::findUser( $http->hasPostVariable( 'SignOutUser' ) ? (string)$http->postVariable( 'SignOutUser' ) : '' );
                if ( !$user instanceof \eZUser || $user->isAnonymous() )
                    $message = array( 'key' => 'no_user', 'level' => 'warn' );
                else
                {
                    $count = \expUserSessionTracker::revokeAll( (int)$user->attribute( 'contentobject_id' ), $viewerID );
                    $message = array( 'key' => 'signed_out', 'level' => $count > 0 ? 'ok' : 'info', 'count' => $count,
                                      'login' => (string)$user->attribute( 'login' ) );
                }
            }
            if ( $message !== null )
            {
                $http->setSessionVariable( self::MESSAGE_SESSION, $message );
                return $this->viewResult( null, $Module->redirectTo( 'user/sessionreport' ) );
            }
        }

        $message = false;
        if ( $http->hasSessionVariable( self::MESSAGE_SESSION, false ) )
        {
            $message = $http->sessionVariable( self::MESSAGE_SESSION );
            $http->removeSessionVariable( self::MESSAGE_SESSION );
        }

        $active = $available ? \expUserSessionReport::activeSessions( self::LIMIT ) : array( 'sessions' => array(), 'total' => 0, 'users' => 0 );
        $days = $available ? \expUserSessionReport::days( self::DAYS ) : array();
        $today = $days ? $days[0] : array( 'signins' => 0, 'failures' => 0, 'sessions' => 0 );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'report_enabled', $enabled );
        $tpl->setVariable( 'report_available', $available );
        $tpl->setVariable( 'report_audit', \expUserSecurityActivity::available() );
        $tpl->setVariable( 'report_sessions', $active['sessions'] );
        $tpl->setVariable( 'report_total', $active['total'] );
        $tpl->setVariable( 'report_users', $active['users'] );
        $tpl->setVariable( 'report_limit', self::LIMIT );
        $tpl->setVariable( 'report_days', $days );
        $tpl->setVariable( 'report_today', $today );
        $tpl->setVariable( 'report_message', $message );
        $tpl->setVariable( 'report_form_token', self::formToken() );
        $tpl->setVariable( 'report_handler', get_class( \eZSession::getHandlerInstance() ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:user/sessionreport.tpl' );
        $Result['path'] = array(
            array( 'text' => \ezpI18n::tr( 'kernel/user', 'User' ), 'url' => false ),
            array( 'text' => \ezpI18n::tr( 'kernel/user/sessions', 'Sign-in report' ), 'url' => 'user/sessionreport' ),
        );
        return $this->viewResult( $Result, $Result );
    }

    /** @return string the token of the page's forms (an HMAC of this session's own token) */
    protected static function formToken()
    {
        $token = \expUserSessionTracker::currentToken();
        if ( $token === null )
        {
            $http = \eZHTTPTool::instance();
            $token = $http->hasSessionVariable( 'expUserSessionsFormKey', false ) ? (string)$http->sessionVariable( 'expUserSessionsFormKey' ) : '';
            if ( strlen( $token ) !== 64 )
            {
                $token = bin2hex( random_bytes( 32 ) );
                $http->setSessionVariable( 'expUserSessionsFormKey', $token );
            }
        }
        return hash_hmac( 'sha256', 'user/sessionreport', $token );
    }

    /**
     * @param \eZHTTPTool $http
     * @return bool the post carries this session's token
     */
    protected static function checkFormToken( $http )
    {
        $sent = $http->hasPostVariable( 'ReportToken' ) ? $http->postVariable( 'ReportToken' ) : '';
        return is_string( $sent ) && $sent !== '' && hash_equals( self::formToken(), $sent );
    }
}

}
