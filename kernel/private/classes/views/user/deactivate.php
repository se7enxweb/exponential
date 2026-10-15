<?php
/**
 * The code of kernel/user/deactivate.php. The file kernel/user/deactivate.php is one call to it.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\User
{

/**
 * user/deactivate: "Deactivate and move" and "Reactivate and move back" for the accounts sent by POST (see
 * expUserDeactivation): UserIDArray[] from Check user accounts (user/hygiene), or ContentObjectID from a user's node
 * view or the context menu. DeactivateMoveButton or ReactivateMoveButton shows a confirmation page (each account,
 * its groups and where it goes; the others counted by why they are left out), whose ConfirmButton (with
 * DeactivateAction) does it, each account decided again just before (expUserDeactivation::decide()), then shows the
 * result with a link back (RedirectURI: user/hygiene or a node).
 *
 * Off (not found) unless site.ini [UserSettings] DeactivateAndMove=enabled. Opens with the function
 * user/deactivate; each account also needs content/edit on its object and, for its locations, content/read,
 * content/move (edit and remove at the main location), content/remove (or the right to remove a location) at the
 * others and content/create (or add location) where it goes. Never touches the anonymous user, the administrator
 * account or the user who acts. Changes take a POST (with the form token where ezformtoken is active); at most
 * expUserDeactivation::MAX_IDS accounts at once. Confirm also needs the token of the confirmation page, kept in the
 * session (DeactivateToken), so a change is never done by a request the page did not make, even without ezformtoken.
 * Guide: doc/features/6.0/user-hygiene.md, section "Deactivate and move"
 */
class Deactivate extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !\expUserDeactivation::isEnabled() )
            return $this->viewResult( null, $Module->handleError( \eZError::KERNEL_MODULE_VIEW_NOT_FOUND, 'kernel' ) );

        $http = \eZHTTPTool::instance();
        $tpl = \eZTemplate::factory();
        $currentUser = \eZUser::currentUser();
        $isPost = \eZSys::serverVariable( 'REQUEST_METHOD', true ) === 'POST';

        $action = null;
        $confirmed = false;
        $ids = array();
        $redirect = '';
        if ( $isPost )
        {
            if ( $http->hasPostVariable( 'DeactivateMoveButton' ) )
                $action = 'deactivate_move';
            elseif ( $http->hasPostVariable( 'ReactivateMoveButton' ) )
                $action = 'reactivate_move';
            if ( $http->hasPostVariable( 'ConfirmButton' ) && $http->hasPostVariable( 'DeactivateAction' ) )
            {
                $action = \expUserDeactivation::normaliseAction( $http->postVariable( 'DeactivateAction' ) );
                // only from the confirmation page of this session: its token, besides the form token of ezformtoken
                // (which an installation may not have active)
                $confirmed = $action !== null && self::checkConfirmToken( $http );
            }
            if ( $http->hasPostVariable( 'UserIDArray' ) )
                $ids = \expUnactivatedUsers::normaliseIDs( $http->postVariable( 'UserIDArray' ) );
            elseif ( $http->hasPostVariable( 'ContentObjectID' ) )
                $ids = \expUnactivatedUsers::normaliseIDs( array( $http->postVariable( 'ContentObjectID' ) ) );
            $redirect = self::normaliseRedirect( $http->hasPostVariable( 'RedirectURI' ) ? $http->postVariable( 'RedirectURI' ) : '' );
        }
        $ids = array_slice( $ids, 0, \expUserDeactivation::MAX_IDS );
        // one account from its page or the context menu: back to its node (the node keeps its id when it moves)
        if ( $redirect === '' && count( $ids ) === 1 )
        {
            $object = \eZContentObject::fetch( $ids[0] );
            if ( $object instanceof \eZContentObject && $object->canRead() && (int)$object->attribute( 'main_node_id' ) > 0 )
                $redirect = 'content/view/full/' . (int)$object->attribute( 'main_node_id' );
        }
        if ( $redirect === '' )
            $redirect = 'user/hygiene';

        $Result = array();
        $Result['path'] = array(
            array( 'text' => \ezpI18n::tr( 'kernel/user', 'User' ), 'url' => false ),
            array( 'text' => \ezpI18n::tr( \expUserDeactivation::CONTEXT, 'Deactivate and move' ), 'url' => false ),
        );
        $tpl->setVariable( 'deactivate_redirect', $redirect );
        $tpl->setVariable( 'deactivate_max', \expUserDeactivation::MAX_IDS );

        if ( $action === null || !$ids )
        {
            $tpl->setVariable( 'deactivate_nothing_selected', $isPost );
            $Result['content'] = $tpl->fetch( 'design:user/deactivate.tpl' );
            return $this->viewResult( $Result, $Result );
        }

        // the group: configured, found by its remote id, or created on first use by the user who acts
        $group = \expUserDeactivation::groupNode();
        $groupProblem = '';
        $groupCreatable = false;
        if ( !$group && $action === 'deactivate_move' )
        {
            if ( \expUserDeactivation::configuredNodeID() > 0 )
                $groupProblem = 'configured_missing';
            elseif ( !\expUserDeactivation::groupMayBeCreated() )
                $groupProblem = 'unpublished';
            elseif ( !\expUserDeactivation::canCreateGroup() )
                $groupProblem = 'create_denied';
            else
                $groupCreatable = true;
        }
        // the group is created on first use only when at least one account would go into it
        if ( $confirmed && $groupCreatable && !\expUserDeactivation::applyWith( $action, $ids, function ( $id )
             {
                 return \expUserDeactivation::accountState( $id, null, true );
             }, function () { return false; }, \expUserHygiene::protectedIDs( $currentUser->attribute( 'contentobject_id' ) ), true )['done'] )
            $confirmed = false;
        if ( $confirmed && $groupCreatable )
        {
            $created = \expUserDeactivation::createGroup();
            if ( $created['ok'] )
            {
                $group = $created['node'];
                $groupCreatable = false;
            }
            else
                $groupProblem = 'create_failed';
        }
        $tpl->setVariable( 'deactivate_group_problem', $groupProblem );

        $protected = \expUserHygiene::protectedIDs( $currentUser->attribute( 'contentobject_id' ) );
        $stateOf = function ( $id ) use ( $group, $groupCreatable )
        {
            return \expUserDeactivation::accountState( $id, $group, $groupCreatable );
        };
        $perform = function ( $action, $id ) use ( $group )
        {
            if ( $action === 'deactivate_move' )
                return $group ? \expUserDeactivation::deactivate( $id, $group ) : array( 'ok' => false, 'error' => 'no_group' );
            return \expUserDeactivation::reactivate( $id, $group );
        };
        $result = \expUserDeactivation::applyWith( $action, $ids, $stateOf, $perform, $protected,
                                                   !$confirmed || ( $action === 'deactivate_move' && !$group ) );

        if ( !$confirmed || ( $action === 'deactivate_move' && !$group ) )
        {
            $previews = array();
            foreach ( $result['done'] as $id )
                $previews[] = \expUserDeactivation::preview( $id, $action, $group );
            $tpl->setVariable( 'deactivate_confirm', array( 'action' => $action, 'ids' => $ids, 'result' => $result, 'accounts' => $previews,
                                                            'can_confirm' => $result['done'] && $groupProblem === '',
                                                            'token' => self::confirmToken( $http ) ) );
            $Result['content'] = $tpl->fetch( 'design:user/deactivate.tpl' );
            return $this->viewResult( $Result, $Result );
        }

        $fallback = 0;
        $notRestored = 0;
        foreach ( $result['details'] as $detail )
        {
            if ( !empty( $detail['fallback'] ) )
                $fallback++;
            if ( isset( $detail['not_restored'] ) )
                $notRestored += (int)$detail['not_restored'];
        }
        $names = array();
        foreach ( array_slice( $result['done'], 0, 20 ) as $id )
        {
            $object = \eZContentObject::fetch( $id );
            if ( $object instanceof \eZContentObject )
                $names[] = (string)$object->attribute( 'name' );
        }
        $tpl->setVariable( 'deactivate_result', array( 'action' => $action, 'result' => $result, 'names' => $names,
                                                       'fallback' => $fallback, 'not_restored' => $notRestored ) );
        $Result['content'] = $tpl->fetch( 'design:user/deactivate.tpl' );
        return $this->viewResult( $Result, $Result );
    }

    /** The session variable of the confirmation token */
    const TOKEN_SESSION = 'expUserDeactivateToken';

    /**
     * The token of the confirmation form: random, kept in the session, the same for the session's confirmation pages.
     *
     * @param \eZHTTPTool $http
     * @return string
     */
    protected static function confirmToken( $http )
    {
        $token = $http->hasSessionVariable( self::TOKEN_SESSION ) ? (string)$http->sessionVariable( self::TOKEN_SESSION ) : '';
        if ( strlen( $token ) !== 64 )
        {
            $token = bin2hex( random_bytes( 32 ) );
            $http->setSessionVariable( self::TOKEN_SESSION, $token );
        }
        return $token;
    }

    /**
     * Whether the POST carries the session's confirmation token (DeactivateToken).
     *
     * @param \eZHTTPTool $http
     * @return bool
     */
    protected static function checkConfirmToken( $http )
    {
        $expected = $http->hasSessionVariable( self::TOKEN_SESSION ) ? (string)$http->sessionVariable( self::TOKEN_SESSION ) : '';
        $given = $http->hasPostVariable( 'DeactivateToken' ) ? $http->postVariable( 'DeactivateToken' ) : '';
        return strlen( $expected ) === 64 && is_string( $given ) && hash_equals( $expected, $given );
    }

    /**
     * The page to go back to: user/hygiene (with its query string) or content/view/full/<node>; '' for anything
     * else, so the link never leaves the site.
     *
     * @param mixed $uri
     * @return string
     */
    public static function normaliseRedirect( $uri )
    {
        if ( !is_string( $uri ) )
            return '';
        $uri = ltrim( trim( $uri ), '/' );
        if ( preg_match( '#^content/view/full/[0-9]+$#', $uri ) )
            return $uri;
        if ( preg_match( '#^user/hygiene(\?[A-Za-z0-9_=&%.+\-]*)?$#', $uri ) )
            return $uri;
        return '';
    }
}

}
