<?php
/**
 * exphelp/image/(id)/<sha256> (doc/eng-us/features/6.0/help-system.md, "External images"): the local copy of an
 * external image a help page shows (README.md's screenshots on github.com), stored by exp:help index
 * (expHelpRemoteImages), so the admin's Content Security Policy (img-src 'self') allows it. The id is a view
 * parameter, never a GET parameter; anything but the 64 lower case hex characters of a stored copy is a 404. Sent
 * with the content type of its suffix, nosniff and a long private cache lifetime (the copy of an address never
 * changes).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Image extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !\expHelp::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $file = self::file( $Params );
        if ( $file === null )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );

        header( 'Content-Type: ' . \expHelpRemoteImages::contentType( $file ) );
        header( 'Content-Length: ' . filesize( $file ) );
        header( 'X-Content-Type-Options: nosniff' );
        header( 'Cache-Control: private, max-age=31536000, immutable' );
        header( "Content-Security-Policy: default-src 'none'" );
        readfile( $file );
        \eZExecution::cleanExit();
    }

    /**
     * The stored copy a request names: (id)/<sha256> and nothing else (no further path, no GET parameter).
     *
     * @param array $Params the view parameters
     * @param \expHelpRemoteImages|null $images the store (tests give their own)
     * @return string|null
     */
    public static function file( array $Params, $images = null )
    {
        $id = isset( $Params['Id'] ) ? $Params['Id'] : null;
        if ( !\expHelpRemoteImages::isId( $id ) )
            return null;
        // an address with more than the view parameter (exphelp/image/../x, exphelp/image/x/(id)/...) names nothing
        if ( !empty( $Params['Parameters'] ) )
            return null;
        $images = $images instanceof \expHelpRemoteImages ? $images : new \expHelpRemoteImages();
        $file = $images->file( $id );
        return $file !== null && \expHelpRemoteImages::contentType( $file ) !== null ? $file : null;
    }
}

}
