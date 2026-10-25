<?php
/**
 * The code of kernel/user/sessions.php. The file kernel/user/sessions.php is one call to it.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\User
{

/**
 * user/sessions: "Your devices". The signed-in user's sessions (expUserSessionTracker::listForUser()): this one
 * first, then the others by last use, each with the browser, system and device kind, the approximate address, the
 * sign-in method, Remember me, and when it started and was last used; below, the sessions that ended in the last
 * [UserSessionSettings] RetentionDays. Buttons: sign out one session (RevokeSessionButton=<row id>), sign out all
 * other sessions (RevokeOthersButton), and the "New sign-in" mail on or off (SaveMailButton with SignInMail).
 *
 * user/sessions/<user id>: the same for another user, for a viewer with the policy user/sessions (the overview of the
 * account, user/edit/<id>, links to it); RevokeOthersButton signs that user out everywhere. The mail choice stays
 * the user's own.
 *
 * Changes take a POST with the page's token (SessionsToken: an HMAC of this session's own token), besides the form
 * token of ezformtoken, then redirect back here with the result in the session (shown once).
 * Guide: doc/eng-us/features/6.0/user-sessions.md
 */
class Sessions extends \Exponential\Runnable\ModuleView
{
    /** The session variable of the result shown once after a change */
    const MESSAGE_SESSION = 'expUserSessionsMessage';

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $viewer = \eZUser::currentUser();
        if ( !$viewer->isRegistered() )
            return $this->viewResult( null, $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );

        $viewerID = (int)$viewer->attribute( 'contentobject_id' );
        $userID = isset( $Params['UserID'] ) && (int)$Params['UserID'] > 0 ? (int)$Params['UserID'] : $viewerID;
        $own = $userID === $viewerID;
        $canAdminister = \expUserSessionTracker::canAdminister( $viewer );
        if ( !$own && !$canAdminister )
            return $this->viewResult( null, $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
        $account = $own ? $viewer : \eZUser::fetch( $userID );
        if ( !$account instanceof \eZUser )
            return $this->viewResult( null, $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        $enabled = \expUserSessionTracker::enabled();
        $available = $enabled && \expUserSessionTracker::available();
        $self = $own ? 'user/sessions' : 'user/sessions/' . $userID;

        $isPost = \eZSys::serverVariable( 'REQUEST_METHOD', true ) === 'POST';
        if ( $isPost && $available )
        {
            $message = null;
            if ( !self::checkFormToken( $http ) )
                $message = array( 'key' => 'token', 'level' => 'bad' );
            elseif ( $http->hasPostVariable( 'RevokeSessionButton' ) )
            {
                $sessionID = (int)$http->postVariable( 'RevokeSessionButton' );
                $current = \expUserSessionTracker::currentRowID();
                if ( $own && $current !== null && $sessionID === (int)$current )
                    $message = array( 'key' => 'current', 'level' => 'warn' );
                else
                    $message = \expUserSessionTracker::revoke( $userID, $sessionID, $viewerID )
                        ? array( 'key' => 'revoked', 'level' => 'ok' ) : array( 'key' => 'not_found', 'level' => 'warn' );
            }
            elseif ( $http->hasPostVariable( 'RevokeOthersButton' ) )
            {
                $count = $own ? \expUserSessionTracker::revokeOthers( $userID, $viewerID ) : \expUserSessionTracker::revokeAll( $userID, $viewerID );
                $message = array( 'key' => $own ? 'revoked_others' : 'revoked_all', 'level' => $count > 0 ? 'ok' : 'info', 'count' => $count );
            }
            elseif ( $own && $http->hasPostVariable( 'SaveMailButton' ) )
            {
                $on = $http->hasPostVariable( 'SignInMail' );
                \eZPreferences::setValue( \expUserSessionTracker::MAIL_PREFERENCE, $on ? '1' : '0' );
                $message = array( 'key' => $on ? 'mail_on' : 'mail_off', 'level' => 'ok' );
            }
            if ( $message !== null )
            {
                $http->setSessionVariable( self::MESSAGE_SESSION, $message );
                return $this->viewResult( null, $Module->redirectTo( $self ) );
            }
        }

        $message = false;
        if ( $http->hasSessionVariable( self::MESSAGE_SESSION, false ) )
        {
            $message = $http->sessionVariable( self::MESSAGE_SESSION );
            $http->removeSessionVariable( self::MESSAGE_SESSION );
        }

        $list = $available ? \expUserSessionTracker::listForUser( $userID ) : array( 'active' => array(), 'ended' => array(), 'current_id' => 0 );
        if ( !$own )
            $list['current_id'] = 0;
        $object = $account->attribute( 'contentobject' );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'sessions_user', $account );
        $tpl->setVariable( 'sessions_user_name', $object instanceof \eZContentObject ? (string)$object->attribute( 'name' ) : (string)$account->attribute( 'login' ) );
        $tpl->setVariable( 'sessions_user_id', $userID );
        $tpl->setVariable( 'sessions_own', $own );
        $tpl->setVariable( 'sessions_can_administer', $canAdminister );
        $tpl->setVariable( 'sessions_enabled', $enabled );
        $tpl->setVariable( 'sessions_available', $available );
        $tpl->setVariable( 'sessions_active', $list['active'] );
        $tpl->setVariable( 'sessions_ended', $list['ended'] );
        $tpl->setVariable( 'sessions_current_id', (int)$list['current_id'] );
        $tpl->setVariable( 'sessions_retention_days', (int)\expUserSessionTracker::setting( 'RetentionDays', 90 ) );
        $tpl->setVariable( 'sessions_mail_setting', \expUserSessionTracker::setting( 'NewDeviceMail', 'enabled' ) === 'enabled' );
        $tpl->setVariable( 'sessions_mail', $own ? \expUserSessionTracker::userPreference( $account ) : false );
        $tpl->setVariable( 'sessions_message', $message );
        $tpl->setVariable( 'sessions_form_token', self::formToken() );
        $tpl->setVariable( 'sessions_action', $self );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:user/sessions.tpl' );
        $Result['path'] = array(
            array( 'text' => \ezpI18n::tr( 'kernel/user', 'User' ), 'url' => false ),
            array( 'text' => $own ? \ezpI18n::tr( 'kernel/user/sessions', 'Your devices' )
                                  : \ezpI18n::tr( 'kernel/user/sessions', 'Sessions of %name', null,
                                                  array( '%name' => $object instanceof \eZContentObject ? $object->attribute( 'name' ) : $account->attribute( 'login' ) ) ),
                   'url' => false ),
        );
        return $this->viewResult( $Result, $Result );
    }

    /**
     * The token of the page's forms: an HMAC of this session's own token (expUserSessionTracker::SESSION_KEY), so a
     * form of another session or an old page of this one before a new sign-in does not count.
     *
     * @return string
     */
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
        return hash_hmac( 'sha256', 'user/sessions', $token );
    }

    /**
     * @param \eZHTTPTool $http
     * @return bool the post carries this session's token
     */
    protected static function checkFormToken( $http )
    {
        $sent = $http->hasPostVariable( 'SessionsToken' ) ? $http->postVariable( 'SessionsToken' ) : '';
        return is_string( $sent ) && $sent !== '' && hash_equals( self::formToken(), $sent );
    }
}

}
