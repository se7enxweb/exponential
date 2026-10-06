<?php
/**
 * The code of kernel/user/edit.php, moved into a class (#207 stage 1). The file kernel/user/edit.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/user/edit.php:
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

class Edit extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];

        if ( isset( $Params['UserID'] ) && is_numeric( $Params['UserID'] ) )
        {
            $UserID = $Params['UserID'];
        }
        else
        {
            $currentUser = \eZUser::currentUser();
            $UserID      = $currentUser->attribute( 'contentobject_id' );
            if ( $currentUser->isAnonymous() )
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
            }
        }

        if ( isset( $Params['UserParameters'] ) )
        {
            $UserParameters = $Params['UserParameters'];
        }
        else
        {
            $UserParameters = array();
        }

        if ( $Module->isCurrentAction( "ChangePassword" ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( "user/password/" . $UserID  ) );
        }

        if ( $Module->isCurrentAction( "ChangeSetting" ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( "user/setting/" . $UserID ) );
        }

        if ( $Module->isCurrentAction( "Cancel" ) )
        {
            return $this->viewResult( null, $Module->redirectTo( self::cancelURI( $Module ) ) );
        }

        $http = \eZHTTPTool::instance();

        if ( $Module->isCurrentAction( "Edit" ) || ( isset( $UserParameters['action'] ) && $UserParameters['action'] === 'edit' ) )
        {
            $selectedVersion = $http->hasPostVariable( 'SelectedVersion' ) ? $http->postVariable( 'SelectedVersion' ) : 'f';
            $editLanguage = $http->hasPostVariable( 'ContentObjectLanguageCode' ) ? $http->postVariable( 'ContentObjectLanguageCode' ) : '';
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectTo( '/content/edit/' . $UserID . '/' . $selectedVersion . '/' . $editLanguage ) );
        }

        $userAccount = \eZUser::fetch( $UserID );
        if ( !$userAccount )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }

        $userObject = $userAccount->attribute( 'contentobject' );
        if ( !$userObject instanceof \eZContentObject  )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }

        if ( !$userObject->canEdit( ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
        }


        $tpl = \eZTemplate::factory();
        $tpl->setVariable( "module", $Module );
        $tpl->setVariable( "http", $http );
        $tpl->setVariable( "userID", $UserID );
        $tpl->setVariable( "userAccount", $userAccount );
        $tpl->setVariable( 'view_parameters', $UserParameters );
        $tpl->setVariable( 'site_access', $GLOBALS['eZCurrentAccess'] );
        $tpl->setVariable( 'redirect_if_discarded', \eZRedirectManager::formReturnURI( $Module ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:user/edit.tpl" );
        $Result['path'] = array( array( 'text' =>  \ezpI18n::tr( 'kernel/user', 'User profile' ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * Where Cancel goes: the page the form names in RedirectIfDiscarded, as content/edit and user/register read it,
     * else the page last viewed, else the sitemap of the users. It always went to that sitemap, a page an editor
     * changing their own account has no business with. Every page passes the rules of
     * \eZRedirectManager::returnURI() (a path of this site or an allowed host, never user/edit itself, a POST-only
     * view or a page the user can no longer view); doc/features/6.0/safe-redirects.md.
     *
     * @param \eZModule|null $module
     * @param array $options see \eZRedirectManager::returnURI()
     * @return string
     */
    public static function cancelURI( $module, $options = array() )
    {
        return \eZRedirectManager::returnURI( $module, '/content/view/sitemap/5/', \eZRedirectManager::formReturnURIs(), $options );
    }
}

}
