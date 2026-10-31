<?php
/**
 * The code of kernel/user/security.php. The file kernel/user/security.php is one call to it.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\User
{

/**
 * user/security: "Security activity", the recent security events of the signed-in user's own account
 * (expUserSecurityActivity, read from the user audit log expuser_audit): sign-ins and failed attempts, new devices and
 * places, sessions signed out, the password, the e-mail address, two-step sign-in, recovery codes, passkeys, the
 * account locked. Filtered by (filter): all, signin, account, problems; paged ((offset)). The head counts the last 30
 * days and links to "Your devices" (user/sessions) and the password page.
 *
 * user/security/<user id>: the same for another account, for a viewer with the policy user/sessions (the account page
 * and the sessions page link to it). Read only for another account.
 *
 * On the own account, like Google's "Recent security activity":
 * - "Was this you?" on every event of the last 30 days that needs attention (expUserSecurityActivity::needsReview()):
 *   "Yes, it was me" (ConfirmEventButton=<audit row id>) remembers the answer, "No, secure my account" is SecureButton;
 * - "Secure your account" (SecureButton): every other session of the account is signed out
 *   (expUserSessionTracker::revokeOthers(), also those kept by Remember me), then the page leads on to the password.
 *   "Verify it's you" asks first ([ReauthGroup_account]). (secure)/1, the link of the security mails, shows that
 *   card first and highlighted.
 * Changes take a POST with the page's token (SecurityToken: an HMAC of this session's own token), besides the form token
 * of ezformtoken, then redirect back here with the result in the session (shown once).
 * Guide: doc/eng-us/features/6.0/user-sessions.md
 */
class Security extends \Exponential\Runnable\ModuleView
{
    const LIMIT = 50;

    /** The session variable of the result shown once after a change */
    const MESSAGE_SESSION = 'expUserSecurityMessage';

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
        return hash_hmac( 'sha256', 'user/security', $token );
    }

    /**
     * @param \eZHTTPTool $http
     * @return bool the post carries this session's token
     */
    protected static function checkFormToken( $http )
    {
        $sent = $http->hasPostVariable( 'SecurityToken' ) ? $http->postVariable( 'SecurityToken' ) : '';
        return is_string( $sent ) && $sent !== '' && hash_equals( self::formToken(), $sent );
    }

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $viewer = \eZUser::currentUser();
        if ( !$viewer->isRegistered() )
            return $this->viewResult( null, $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );

        $viewerID = (int)$viewer->attribute( 'contentobject_id' );
        $userID = isset( $Params['UserID'] ) && (int)$Params['UserID'] > 0 ? (int)$Params['UserID'] : $viewerID;
        $own = $userID === $viewerID;
        if ( !$own && !\expUserSessionTracker::canAdminister( $viewer ) )
            return $this->viewResult( null, $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
        $account = $own ? $viewer : \eZUser::fetch( $userID );
        if ( !$account instanceof \eZUser )
            return $this->viewResult( null, $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        // "Download my sign-in data" (own account only, a POST with the form token of ezformtoken): the sessions with
        // their details and the security events, as JSON (the right of access)
        $http = \eZHTTPTool::instance();
        $isPost = \eZSys::serverVariable( 'REQUEST_METHOD', true ) === 'POST';
        if ( $own && $isPost && $http->hasPostVariable( 'DownloadButton' ) )
            return $this->download( $account );

        if ( $own && $isPost && ( $http->hasPostVariable( 'ConfirmEventButton' ) || $http->hasPostVariable( 'SecureButton' ) ) )
        {
            if ( !self::checkFormToken( $http ) )
                $message = array( 'key' => 'token', 'level' => 'bad' );
            elseif ( $http->hasPostVariable( 'SecureButton' ) )
            {
                $available = \expUserSessionTracker::enabled() && \expUserSessionTracker::available();
                $count = $available ? \expUserSessionTracker::revokeOthers( $userID, $viewerID ) : 0;
                $message = array( 'key' => 'secured', 'level' => 'ok', 'count' => $count, 'tracked' => $available );
            }
            else
                $message = \expUserSecurityActivity::confirm( $account, (int)$http->postVariable( 'ConfirmEventButton' ) )
                    ? array( 'key' => 'confirmed', 'level' => 'ok' ) : array( 'key' => 'not_found', 'level' => 'warn' );
            $http->setSessionVariable( self::MESSAGE_SESSION, $message );
            return $this->viewResult( null, $Module->redirectTo( 'user/security' ) );
        }
        $message = false;
        if ( $http->hasSessionVariable( self::MESSAGE_SESSION, false ) )
        {
            $message = $http->sessionVariable( self::MESSAGE_SESSION );
            $http->removeSessionVariable( self::MESSAGE_SESSION );
        }

        $filter = isset( $_GET['filter'] ) && is_string( $_GET['filter'] ) && in_array( $_GET['filter'], \expUserSecurityActivity::FILTERS, true )
            ? $_GET['filter'] : 'all';
        $available = \expUserSecurityActivity::available();
        $offset = isset( $Params['Offset'] ) ? max( 0, (int)$Params['Offset'] ) : 0;
        $count = $available ? \expUserSecurityActivity::count( $userID, $filter ) : 0;
        if ( $offset >= $count )
            $offset = $count > 0 ? (int)( floor( ( $count - 1 ) / self::LIMIT ) * self::LIMIT ) : 0;
        $events = $available && $count > 0 ? \expUserSecurityActivity::fetch( $userID, $filter, $offset, self::LIMIT ) : array();
        $object = $account->attribute( 'contentobject' );
        $name = $object instanceof \eZContentObject ? (string)$object->attribute( 'name' ) : (string)$account->attribute( 'login' );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'security_user', $account );
        $tpl->setVariable( 'security_user_name', $name );
        $tpl->setVariable( 'security_user_id', $userID );
        $tpl->setVariable( 'security_own', $own );
        $tpl->setVariable( 'security_available', $available );
        $tpl->setVariable( 'security_filter', $filter );
        $tpl->setVariable( 'security_filters', \expUserSecurityActivity::FILTERS );
        $tpl->setVariable( 'security_events', $events );
        $tpl->setVariable( 'security_count', $count );
        $tpl->setVariable( 'security_limit', self::LIMIT );
        $tpl->setVariable( 'security_summary', $available ? \expUserSecurityActivity::summary( $userID ) : \expUserSecurityActivity::summarize( array() ) );
        $tpl->setVariable( 'security_retention_days', (int)\expUserAudit::settings()['retention_days'] );
        $tpl->setVariable( 'security_action', $own ? 'user/security' : 'user/security/' . $userID );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $offset ) );
        $tpl->setVariable( 'security_review', $own ? \expUserSecurityActivity::reviewMap( $events, \expUserSecurityActivity::confirmedIDs( $account ) ) : array() );
        $tpl->setVariable( 'security_review_days', \expUserSecurityActivity::REVIEW_DAYS );
        $tpl->setVariable( 'security_secure', $own && !empty( $Params['Secure'] ) );
        $tpl->setVariable( 'security_message', $message );
        $tpl->setVariable( 'security_form_token', $own ? self::formToken() : '' );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:user/security.tpl' );
        $Result['path'] = array(
            array( 'text' => \ezpI18n::tr( 'kernel/user', 'User' ), 'url' => false ),
            array( 'text' => $own ? \ezpI18n::tr( 'kernel/user/security', 'Security activity' )
                                  : \ezpI18n::tr( 'kernel/user/security', 'Security activity of %name', null, array( '%name' => $name ) ),
                   'url' => false ),
        );
        return $this->viewResult( $Result, $Result );
    }

    /**
     * Sends the account's sign-in data as JSON and ends the request: every session row with its details (never the
     * token's hash) and every security event of the user audit log about the account. Recorded as data.export.json
     * with the numbers of rows, never the data.
     */
    protected function download( \eZUser $account )
    {
        $userID = (int)$account->attribute( 'contentobject_id' );
        $data = array( 'account' => array( 'user_id' => $userID, 'login' => (string)$account->attribute( 'login' ) ),
                       'generated' => gmdate( 'Y-m-d\TH:i:s\Z' ),
                       'sessions' => \expUserSessionTracker::exportForUser( $userID ),
                       'security_events' => \expUserSecurityActivity::exportForUser( $userID ) );
        $fileName = 'sign-in-data-' . gmdate( 'Ymd-His' ) . '.json';
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $fileName . '"' );
        header( 'Cache-Control: private, no-store, max-age=0' );
        header( 'X-Content-Type-Options: nosniff' );
        while ( @ob_end_clean() );
        echo json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR );
        if ( class_exists( 'expAuditHook' ) )
            \expAuditHook::emit( 'data.export.json', array( 'object' => array( 'type' => 'view', 'id' => 'user/security' ), 'verb' => 'export',
                'after' => array( 'sessions' => count( $data['sessions'] ), 'events' => count( $data['security_events'] ), 'file' => $fileName,
                                  'source' => 'user_security' ) ) );
        // no catch( Exception ) around the download and cleanExit(): under Velocity cleanExit() throws
        \eZExecution::cleanExit();
        return $this->viewResult( null, null );
    }
}

}
