<?php
/**
 * The code of kernel/user/activate.php, moved into a class (#207 stage 1). The file kernel/user/activate.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/user/activate.php:
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

class Activate extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();

        $hash = $http->hasPostVariable( 'Hash' ) ? $http->postVariable( 'Hash' ) : $Params['Hash'];
        $hash = is_string( $hash ) ? trim( $hash ) : '';
        $mainNodeID = (int) ( $http->hasPostVariable( 'MainNodeID' ) ? $http->postVariable( 'MainNodeID' ) : $Params['MainNodeID'] );

        // Prepend or append the hash string with a salt, and md5 the resulting hash
        // Example: use is login name as salt, and a 'secret password' as hash sent to the user
        // A salt posted as an array counts as an empty salt
        if ( $http->hasPostVariable( 'HashSaltPrepend' ) )
        {
            $salt = $http->postVariable( 'HashSaltPrepend' );
            $hash =  md5( ( is_string( $salt ) ? trim( $salt ) : '' ) . $hash );
        }
        else if ( $http->hasPostVariable( 'HashSaltAppend' ) )
        {
            $salt = $http->postVariable( 'HashSaltAppend' );
            $hash =  md5( $hash . ( is_string( $salt ) ? trim( $salt ) : '' ) );
        }


        // Check if key exists
        $accountActivated = false;
        $alreadyActive = false;
        $isPending = false;
        $accountKey = $hash ? \eZUserAccountKey::fetchByKey( $hash ) : false;

        if ( $accountKey )
        {
            $accountActivated = true;
            $userID = $accountKey->attribute( 'user_id' );

            $userContentObject = \eZContentObject::fetch( $userID );
            if ( !$userContentObject instanceof \eZContentObject )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' ) );
            }

            if ( $userContentObject->attribute('main_node_id') != $mainNodeID )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
            }

            // Enable user account
            if ( \eZOperationHandler::operationIsAvailable( 'user_activation' ) )
            {
                $operationResult = \eZOperationHandler::execute( 'user',
                                                                'activation', array( 'user_id'    => $userID,
                                                                                     'user_hash'  => $hash,
                                                                                     'is_enabled' => true ) );
            }
            else
            {
                \eZUserOperationCollection::activation( $userID, $hash, true );
            }

            // Finish the registration: resume user/register when it waits for this activation (publish, the approval
            // mail, post_register), else make those steps by hand. Running user/register afresh for an account without
            // a pending registration sent a new activation mail and disabled the account again.
            $publishResult = \expUserActivation::finish( $userID );
            if( $publishResult['status'] === \eZModuleOperationInfo::STATUS_HALTED )
            {
                $isPending = true;
            }
            else
            {
                // Log in user
                $user = \eZUser::fetch( $userID );

                if ( $user === null )
                    return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' ) );

                \eZUser::updateLastVisit( $userID , true );
                $user->loginCurrent();
            }
        }
        elseif( $mainNodeID )
        {
            $userContentObject = \eZContentObject::fetchByNodeID( $mainNodeID );
            if ( $userContentObject instanceof \eZContentObject )
            {
                $userSetting = \eZUserSetting::fetch( $userContentObject->attribute( 'id' ) );

                if ( $userSetting !== null && $userSetting->attribute( 'is_enabled' ) )
                {
                    $alreadyActive = true;
                }
            }
        }

        // Template handling

        $tpl = \eZTemplate::factory();

        $tpl->setVariable( 'module', $Module );
        $tpl->setVariable( 'account_activated', $accountActivated );
        $tpl->setVariable( 'already_active', $alreadyActive );
        $tpl->setVariable( 'is_pending' , $isPending );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:user/activate.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/user', 'User' ),
                                        'url' => false ),
                                 array( 'text' => \ezpI18n::tr( 'kernel/user', 'Activate' ),
                                        'url' => false ) );
        $ini = \eZINI::instance();
        if ( $ini->variable( 'SiteSettings', 'LoginPage' ) == 'custom' )
            $Result['pagelayout'] = 'loginpagelayout.tpl';

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
