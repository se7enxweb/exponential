<?php
/**
 * The code of kernel/user/preferences.php, moved into a class (#207 stage 1). The file kernel/user/preferences.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/user/preferences.php:
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

class Preferences extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];
        $http = \eZHTTPTool::instance();

        if ( $http->hasPostVariable( 'Function' ) )
            $function = $http->postVariable( 'Function' );
        else
            $function = $Params['Function'];

        if ( $http->hasPostVariable( 'Key' ) )
            $key = $http->postVariable( 'Key' );
        else
            $key = $Params['Key'];


        if ( $http->hasPostVariable( 'Value' ) )
            $value = $http->postVariable( 'Value' );
        else
            $value = $Params['Value'];

        // Store the preference only for a request the browser marks as coming
        // from the own site. The Sec-Fetch-Site header is an allowlist here:
        // a missing header (older browsers, CLI clients, monitoring) and
        // 'same-origin' or 'same-site' (admin and frontend siteaccesses share a
        // domain) may write; 'cross-site' and 'none' (a link opened from a mail
        // client, chat or PDF) skip the write. The redirect below is unchanged,
        // so the visitor still lands on the target page.
        $secFetchSite = isset( $_SERVER['HTTP_SEC_FETCH_SITE'] ) ? $_SERVER['HTTP_SEC_FETCH_SITE'] : null;
        $allowPreferenceWrite = $secFetchSite === null || $secFetchSite === 'same-origin' || $secFetchSite === 'same-site';

        // Set user preferences
        if ( $allowPreferenceWrite )
        {
            if ( \eZOperationHandler::operationIsAvailable( 'user_preferences' ) )
            {
                $operationResult = \eZOperationHandler::execute( 'user',
                                                                'preferences', array( 'key'    => $key,
                                                                                      'value'  => $value ) );
            }
            else
            {
                \eZPreferences::setValue( $key, $value );
            }
        }
        else
        {
            \eZDebug::writeWarning( "Skipped the preference write for '$key': the request came with Sec-Fetch-Site '$secFetchSite'", 'user/preferences' );
        }

        // For use by ajax calls
        if ( $function === 'set_and_exit' )
        {
            \eZDB::checkTransactionCounter();
            \eZExecution::cleanExit();
        }

        if ( $http->hasPostVariable( 'RedirectURIAfterSet' ) )
        {
            $url = $http->postVariable( 'RedirectURIAfterSet' );
        }
        else
        {
            // Extract URL to redirect to from user parameters.
            $urlArray = array_splice( $Params['Parameters'], 3 );
            foreach ( $urlArray as $key => $val ) // remove all the array elements that don't seem like URL parts
            {
                if ( !is_numeric( $key ) )
                    unset( $urlArray[$key] );
            }
            $url = implode( '/', $urlArray );
            unset( $urlArray );
        }

        if ( $url )
        {
            foreach ( array_keys( $Params['UserParameters'] ) as $key )
            {
                if ( $key == 'offset' )
                    continue;
                $url .= '/(' . $key . ')/' . $Params['UserParameters'][$key];
            }
            $module->redirectTo( '/'.$url );
        }
        else if ( isset( $_SERVER['HTTP_REFERER'] ) )
        {
            $preferredRedirectionURI = \eZURI::decodeURL( $_SERVER['HTTP_REFERER'] );

            // We should exclude OFFSET from $preferredRedirectionURI
            $exploded = explode( '/', $preferredRedirectionURI );
            foreach ( array_keys( $exploded ) as $itemKey )
            {
                $item = $exploded[$itemKey];
                if ( $item == '(offset)' )
                {
                    array_splice( $exploded, $itemKey, 2 );
                    break;
                }
            }
            $redirectURI = implode( '/', $exploded );

            // Protect against redirect loop
            if ( strpos( $redirectURI, '/user/preferences/set'  ) !== false )
                $module->redirectTo( '/' );
            else
                \eZRedirectManager::redirectTo( $module, /* $default = */ false, /* $view = */ true, /* $disallowed = */ false, $redirectURI );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }
        else
        {
            $module->redirectTo( $http->postVariable( 'RedirectURI', $http->sessionVariable( 'LastAccessesURI', '/' ) ) );
        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
