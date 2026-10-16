<?php
/**
 * The code of kernel/user/registration.php. The file kernel/user/registration.php is one call to it.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\User
{

/**
 * user/registration/<token>: the page the signed links of the administrator's "New user registered" mail open
 * (expRegistrationAdminAction, doc/features/6.0/registration-admin-links.md).
 *
 * Needs a signed-in user: an anonymous one gets the kernel's access denied, which in the administration is the login
 * form, back to this address after the login. The token is checked first (verify(): a bad, changed, expired, used or
 * wrong token is refused with a page that says why, HTTP 410 when it expired and 403 otherwise, and audited), then
 * the right to act (expRegistrationAdminAction::may(): user/activation, an approver of the waiting approval item, or
 * for a view content/edit on the account; else the kernel's access denied, audited as "policy").
 *
 * A GET never changes anything. A view token shows the account (name, login, e-mail address, created, its state,
 * the waiting approval item, links to the account and to the unactivated users) and, when the user may act and the
 * account waits, Approve and Reject, which lead to the confirmation of a new action token. An approve or reject
 * token shows the confirmation (a POST to the same address, protected by the form token): ConfirmButton uses the
 * token (consume(), once) and approves or rejects the account when its state still allows it; CancelButton goes
 * back to the account's view page and leaves the token unused.
 */
class Registration extends \Exponential\Runnable\ModuleView
{
    const CONTEXT = 'kernel/user/registration';

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !\expRegistrationAdminAction::enabled() )
            return $this->viewResult( null, $Module->handleError( \eZError::KERNEL_MODULE_VIEW_NOT_FOUND, 'kernel' ) );

        $current = \eZUser::currentUser();
        // the login form, which comes back to this address after the login
        if ( !$current instanceof \eZUser || $current->isAnonymous() )
            return $this->viewResult( null, $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );

        $token = isset( $Params['Token'] ) ? trim( (string)$Params['Token'] ) : '';
        $isPost = \eZSys::serverVariable( 'REQUEST_METHOD', true ) === 'POST';

        $data = \expRegistrationAdminAction::verify( $token );
        if ( $data === false )
            return $this->refused( $token, (string)\expRegistrationAdminAction::lastError() );

        $userID = (int)$data['user_id'];
        $action = (string)$data['action'];
        if ( !\expRegistrationAdminAction::may( $current, $userID, $action ) )
        {
            \expRegistrationAdminAction::auditRefused( $userID, 'policy', $action );
            return $this->viewResult( null, $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel',
                array( 'AccessList' => array( 'FunctionRequired' => array( 'Module' => 'user', 'Function' => 'activation' ) ) ) ) );
        }

        // Cancel: back to the account's view page; the action token stays unused
        if ( $isPost && $Module->isCurrentAction( 'Cancel' ) )
        {
            $viewToken = $action === \expRegistrationAdminAction::ACTION_VIEW ? $token : \expRegistrationAdminAction::create( $userID, \expRegistrationAdminAction::ACTION_VIEW );
            return $this->viewResult( null, $Module->redirectTo( '/user/registration/' . rawurlencode( $viewToken ) ) );
        }

        $mode = $action === \expRegistrationAdminAction::ACTION_VIEW ? 'view' : 'confirm';
        $result = null;
        $account = self::account( $userID );
        $state = $account ? $account['state'] : \expRegistrationAdminAction::STATE_MISSING;

        if ( $mode === 'confirm' && $isPost && $Module->isCurrentAction( 'Confirm' ) )
        {
            // used once: of two requests with the same token only one gets here
            if ( \expRegistrationAdminAction::consume( $token, $action ) === false )
                return $this->refused( $token, (string)\expRegistrationAdminAction::lastError() );
            $state = \expRegistrationAdminAction::state( $userID );
            if ( !\expRegistrationAdminAction::allowedInState( $action, $state ) )
            {
                \expRegistrationAdminAction::auditRefused( $userID, 'state', $action );
                $result = array( 'result' => 'refused', 'state_before' => $state, 'state' => $state );
            }
            elseif ( $action === \expRegistrationAdminAction::ACTION_APPROVE )
            {
                $result = \expRegistrationAdminAction::approve( $userID );
            }
            else
            {
                $result = \expRegistrationAdminAction::reject( $userID );
            }
            $mode = 'done';
            // the account as it is now; after a rejection its details from before, to name it
            $after = self::account( $userID );
            if ( $after )
                $account = $after;
            elseif ( $account )
                $account['state'] = \expRegistrationAdminAction::STATE_MISSING;
            $state = $result['state'];
        }
        elseif ( $mode === 'confirm' && !\expRegistrationAdminAction::allowedInState( $action, $state ) )
        {
            // nothing to confirm: the account is active already or gone; the token is left as it is
            $mode = 'nothing';
        }

        // Approve and Reject on the view page: the confirmation of a new action token
        $actions = array();
        if ( $mode === 'view' )
        {
            foreach ( array( \expRegistrationAdminAction::ACTION_APPROVE, \expRegistrationAdminAction::ACTION_REJECT ) as $next )
            {
                if ( \expRegistrationAdminAction::allowedInState( $next, $state ) && \expRegistrationAdminAction::may( $current, $userID, $next ) )
                    $actions[$next] = 'user/registration/' . rawurlencode( \expRegistrationAdminAction::create( $userID, $next ) );
            }
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'registration_mode', $mode );
        $tpl->setVariable( 'registration_action', $action );
        $tpl->setVariable( 'registration_problem', '' );
        $tpl->setVariable( 'registration_account', $account ? $account : false );
        $tpl->setVariable( 'registration_state', $state );
        $tpl->setVariable( 'registration_result', $result ? $result : false );
        $tpl->setVariable( 'registration_uri', 'user/registration/' . rawurlencode( $token ) );
        $tpl->setVariable( 'registration_expiry', (int)$data['expiry'] );
        $tpl->setVariable( 'registration_actions', $actions );
        $tpl->setVariable( 'registration_view_uri', $mode === 'view' ? '' : 'user/registration/' . rawurlencode( \expRegistrationAdminAction::create( $userID, \expRegistrationAdminAction::ACTION_VIEW ) ) );
        $tpl->setVariable( 'registration_unactivated_uri', 'user/unactivated' );

        return $this->page( $tpl );
    }

    /**
     * The page of a refused token: why, with HTTP 410 when it expired and 403 otherwise; audited.
     *
     * @param string $token
     * @param string $reason malformed, signature, expired, action, used
     * @return array
     */
    protected function refused( $token, $reason )
    {
        $parts = explode( '.', $token );
        $userID = isset( $parts[0] ) && ctype_digit( $parts[0] ) ? (int)$parts[0] : 0;
        $action = isset( $parts[1] ) && in_array( $parts[1], \expRegistrationAdminAction::actions(), true ) ? $parts[1] : '';
        // a token that is not ours says nothing reliable about the account
        if ( $reason === 'malformed' || $reason === 'signature' )
            $userID = 0;
        \expRegistrationAdminAction::auditRefused( $userID, $reason === '' ? 'malformed' : $reason, $action );
        http_response_code( $reason === 'expired' ? 410 : 403 );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'registration_mode', 'refused' );
        $tpl->setVariable( 'registration_action', $action );
        $tpl->setVariable( 'registration_problem', $reason === '' ? 'malformed' : $reason );
        $tpl->setVariable( 'registration_account', false );
        $tpl->setVariable( 'registration_state', '' );
        $tpl->setVariable( 'registration_result', false );
        $tpl->setVariable( 'registration_uri', '' );
        $tpl->setVariable( 'registration_expiry', 0 );
        $tpl->setVariable( 'registration_actions', array() );
        $tpl->setVariable( 'registration_view_uri', '' );
        $tpl->setVariable( 'registration_unactivated_uri', 'user/unactivated' );
        return $this->page( $tpl );
    }

    /**
     * @param \eZTemplate $tpl
     * @return array the view's result
     */
    protected function page( $tpl )
    {
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:user/registration.tpl' );
        $Result['path'] = array(
            array( 'text' => \ezpI18n::tr( 'kernel/user', 'User' ), 'url' => false ),
            array( 'text' => \ezpI18n::tr( self::CONTEXT, 'New registration' ), 'url' => false ),
        );
        return $this->viewResult( $Result, $Result );
    }

    /**
     * The details of an account for the page; false when there is no such account.
     *
     * @param int $userID
     * @return array|false
     */
    protected static function account( $userID )
    {
        $userID = (int)$userID;
        $user = \eZUser::fetch( $userID );
        $object = \eZContentObject::fetch( $userID );
        if ( !$user instanceof \eZUser || !$object instanceof \eZContentObject )
            return false;
        $published = (int)$object->attribute( 'status' ) === \eZContentObject::STATUS_PUBLISHED;
        $currentVersion = (int)$object->attribute( 'current_version' );
        $version = $object->version( $currentVersion );
        $created = (int)$object->attribute( 'published' );
        if ( $created <= 0 && $version instanceof \eZContentObjectVersion )
            $created = (int)$version->attribute( 'created' );
        $name = (string)$object->attribute( 'name' );
        if ( trim( $name ) === '' && $version instanceof \eZContentObjectVersion )
            $name = (string)$version->attribute( 'name' );
        $mainNodeID = (int)$object->attribute( 'main_node_id' );
        $collaborationID = \expRegistrationAdminAction::pendingCollaborationID( $userID );
        return array(
            'id' => $userID,
            'name' => $name,
            'login' => (string)$user->attribute( 'login' ),
            'email' => (string)$user->attribute( 'email' ),
            'created' => $created,
            'published' => $published,
            'state' => \expRegistrationAdminAction::state( $userID ),
            'collaboration_uri' => $collaborationID ? 'collaboration/item/full/' . $collaborationID : '',
            'node_uri' => $published && $mainNodeID > 0 ? 'content/view/full/' . $mainNodeID : '',
            'version_uri' => $currentVersion > 0 ? 'content/versionview/' . $userID . '/' . $currentVersion : '',
        );
    }
}

}
