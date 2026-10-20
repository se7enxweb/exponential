<?php
/**
 * exphelp/asset/<path> (doc/en/us/features/6.0/help-system.md): an image below a help root. Only files
 * expHelpDocumentIndex::asset() allows (git-tracked, allowed suffix); anything else is a 404. Sent with the content
 * type of its suffix, nosniff and a private cache lifetime.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Asset extends \Exponential\Runnable\ModuleView
{
    /** The content types of the allowed image suffixes */
    const TYPES = array( 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif',
                         'webp' => 'image/webp' );

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !\expHelp::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $id = \expHelp::requestedId( $Params, $_GET );
        $file = $id === '' ? null : \expHelpDocumentIndex::instance()->asset( $id );
        $type = self::contentType( (string)$file );
        if ( $file === null || $type === null || !is_file( $file ) )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );

        header( 'Content-Type: ' . $type );
        header( 'Content-Length: ' . filesize( $file ) );
        header( 'X-Content-Type-Options: nosniff' );
        header( 'Cache-Control: private, max-age=3600' );
        header( "Content-Security-Policy: default-src 'none'" );
        readfile( $file );
        \eZExecution::cleanExit();
    }

    /**
     * @param string $file
     * @return string|null the content type, null for a suffix that is not an allowed image
     */
    public static function contentType( $file )
    {
        $suffix = strtolower( pathinfo( (string)$file, PATHINFO_EXTENSION ) );
        return isset( self::TYPES[$suffix] ) ? self::TYPES[$suffix] : null;
    }
}

}
