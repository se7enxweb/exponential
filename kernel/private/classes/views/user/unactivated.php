<?php
/**
 * The code of kernel/user/unactivated.php, moved into a class (#207 stage 1). The file kernel/user/unactivated.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/user/unactivated.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */


namespace Exponential\View\Kernel\User
{

/**
 * user/unactivated: the users who registered and never activated their account (expUnactivatedUsers). A page of
 * them searched (GET q, by login, e-mail or name), ordered (the view parameters SortField and SortOrder: time,
 * login, email, name; asc or desc) and paged ((offset)), and three actions on the ticked ones (DeleteIDArray[]):
 * ActivateButton, ResendButton (the activation mail again, with a new link) and RemoveButton, and RemoveAllButton for
 * every unactivated user (or every one the search matches), removed in batches. Every action is done
 * only to users who are still unactivated (disabled, with an account key), so a stale form cannot remove or activate
 * anyone else. The template variables of the old page are all still set.
 */
class Unactivated extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Offset = max( 0, (int)$Params['Offset'] );
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();

        $tpl = \eZTemplate::factory();
        $search = \expUnactivatedUsers::normaliseSearch( $http->hasGetVariable( 'q' ) ? $http->getVariable( 'q' ) : '' );

        $success = array();
        $errors = array();
        if ( $Module->isCurrentAction( 'ActivateUsers' ) )
        {
            foreach ( \expUnactivatedUsers::normaliseIDs( $Module->actionParameter( 'UserIDs' ) ) as $id )
            {
                $accountKey = \eZUserAccountKey::fetchByUserID( $id );
                if ( $accountKey instanceof \eZUserAccountKey && \expUnactivatedUsers::isUnactivated( $id ) )
                {
                    // run the activation as in kernel/user/activate.php
                    if ( \eZOperationHandler::operationIsAvailable( 'user_activation' ) )
                    {
                        \eZOperationHandler::execute( 'user', 'activation',
                                                      array( 'user_id' => $id, 'user_hash' => $accountKey->attribute( 'hash_key' ), 'is_enabled' => true ) );
                    }
                    else
                    {
                        \eZUserOperationCollection::activation( $id, $accountKey->attribute( 'hash_key' ), true );
                    }
                    // Finish the registration as the user's own activation link does (#18886): resume user/register
                    // when it is pending (publish, the approval mail, post_register), else make those steps by hand.
                    // The approval mail follows site.ini [UserSettings] ActivationByAdministratorSendsApprovalMail.
                    \expUserActivation::finish( $id, true );
                    $success[] = $id;
                }
                else
                {
                    \eZDebug::writeError( "User #{$id} is not an unactivated user", 'user/unactivated' );
                    $errors[] = $id;
                }
            }
            if ( !empty( $success ) )
                \eZContentObject::clearCache( $success );

            $tpl->setVariable( 'success_activate', empty( $success ) ? false : $success );
            $tpl->setVariable( 'errors_activate', empty( $errors ) ? false : $errors );
        }
        else if ( $Module->isCurrentAction( 'RemoveUsers' ) )
        {
            foreach ( \expUnactivatedUsers::normaliseIDs( $Module->actionParameter( 'UserIDs' ) ) as $id )
            {
                $object = \eZContentObject::fetch( $id );
                // Never an activated user, whatever the form says
                if ( $object instanceof \eZContentObject && \expUnactivatedUsers::isUnactivated( $id ) )
                {
                    $success[] = $object->attribute( 'name' );
                    $object->purge();
                }
                else
                {
                    \eZDebug::writeError( "User #{$id} is not an unactivated user; not removed", 'user/unactivated' );
                    $errors[] = $id;
                }
            }
            $tpl->setVariable( 'success_remove', empty( $success ) ? false : $success );
            $tpl->setVariable( 'errors_remove', empty( $errors ) ? false : $errors );
        }
        else if ( $Module->isCurrentAction( 'ResendActivation' ) )
        {
            $reasons = array();
            foreach ( \expUnactivatedUsers::normaliseIDs( $Module->actionParameter( 'UserIDs' ) ) as $id )
            {
                $result = \expUnactivatedUsers::resend( $id );
                if ( $result === 'sent' )
                    $success[] = $id;
                else
                {
                    $errors[] = $id;
                    $reasons[$result] = isset( $reasons[$result] ) ? $reasons[$result] + 1 : 1;
                }
            }
            $tpl->setVariable( 'success_resend', count( $success ) );
            $tpl->setVariable( 'errors_resend', $reasons );
        }
        else if ( $Module->isCurrentAction( 'RemoveAllUsers' ) )
        {
            // Every unactivated user, or every one the search of the page matches (?q= in the form's address),
            // in batches, each checked again just before; never the anonymous user, "admin" or the user removing
            $tpl->setVariable( 'remove_all_result', \expUnactivatedUsers::removeAll( $search, \eZUser::currentUserID() ) );
        }
        else if ( $http->hasPostVariable( 'ActivateButton' ) || $http->hasPostVariable( 'RemoveButton' ) || $http->hasPostVariable( 'ResendButton' ) )
        {
            // A button without a ticked user
            $tpl->setVariable( 'nothing_selected', true );
        }

        $limitPreference = 'admin_user_actions_list_limit';
        switch ( \eZPreferences::value( $limitPreference ) )
        {
            case 2:
                $limit = 25;
                break;
            case 3:
                $limit = 50;
                break;
            case 1:
            default:
                $limit = 10;
        }

        $SortField = \expUnactivatedUsers::normaliseSort( isset( $Params['SortField'] ) ? $Params['SortField'] : '' );
        $SortOrder = \expUnactivatedUsers::normaliseOrder( isset( $Params['SortOrder'] ) ? $Params['SortOrder'] : '' );

        // The count is of the same users the list holds (disabled, with an account key); it used to count every
        // account key, so it could say a number the list did not show.
        $unactivatedTotal = \expUnactivatedUsers::count();
        $unactivatedCount = $search === '' ? $unactivatedTotal : \expUnactivatedUsers::count( $search );
        if ( $Offset >= $unactivatedCount )
            $Offset = $unactivatedCount > 0 ? (int)( floor( ( $unactivatedCount - 1 ) / $limit ) * $limit ) : 0;
        $rows = $unactivatedCount > 0 ? \expUnactivatedUsers::page( $search, $SortField, $SortOrder, $limit, $Offset ) : array();

        // The users of the page as eZUser objects, as the old template variable had them
        $unactivated = array();
        foreach ( $rows as $row )
        {
            $user = \eZUser::fetch( $row['contentobject_id'] );
            if ( $user instanceof \eZUser )
                $unactivated[] = $user;
        }

        $tpl->setVariable( 'unactivated_count', $unactivatedCount );
        $tpl->setVariable( 'unactivated_total', $unactivatedTotal );
        $tpl->setVariable( 'unactivated_users', $unactivated );
        $tpl->setVariable( 'unactivated_rows', $rows );
        $tpl->setVariable( 'unactivated_search', $search );
        $tpl->setVariable( 'unactivated_search_suffix', $search === '' ? '' : '?q=' . rawurlencode( $search ) );
        $tpl->setVariable( 'unactivated_old_days', \expUnactivatedUsers::OLD_DAYS );
        $tpl->setVariable( 'unactivated_mail_transport', (string)\eZINI::instance()->variable( 'MailSettings', 'Transport' ) );
        $tpl->setVariable( 'sort_field', $SortField );
        $tpl->setVariable( 'sort_order', $SortOrder );
        $tpl->setVariable( 'limit_preference', $limitPreference );
        $tpl->setVariable( 'number_of_items', $limit );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $Offset ) );
        $tpl->setVariable( 'module', $Module );

        $functions = $Module->attribute( 'functions' );
        $Result = array();
        $Result['path'] = array(
            array(
                'text' => \ezpI18n::tr( 'kernel/user', 'User' ),
                'url' => false
            ),
            array(
                'text' => \ezpI18n::tr( 'kernel/user', 'Unactivated users' ),
                'url' => $functions['unactivated']['uri']
            )
        );
        $Result['content'] = $tpl->fetch( 'design:user/unactivated.tpl' );

        return $this->viewResult( $Result, $Result );
    }
}

}
