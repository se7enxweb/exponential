<?php
/**
 * exphelp/asset/<path> (doc/eng-us/features/6.0/help-system.md): an image below a help root. Only files
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

        $id = \expHelp::requestedId( $Params );
        $file = $id === '' ? null : self::file( $id, \expHelpDocumentIndex::instance() );
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
     * The file of an image id: from the reader's index, else from the index of another help language. A page shown
     * in another language than the reader's (/(lang)/ger-de for a reader whose language is English, or the English
     * original for a German reader) names its own language's images (hilfe/.../bilder/x.webp), which the reader's
     * index does not list; each index still allows only its own git-tracked files.
     *
     * @param string $id
     * @param \expHelpDocumentIndex $readerIndex
     * @return string|null
     */
    public static function file( $id, \expHelpDocumentIndex $readerIndex )
    {
        $file = $readerIndex->asset( $id );
        if ( $file !== null )
            return $file;
        $languages = $readerIndex->languages();
        foreach ( $languages->directories() as $directory )
        {
            if ( $directory === $readerIndex->language() )
                continue;
            $file = \expHelpDocumentIndex::forLanguage( $directory, $languages )->asset( $id );
            if ( $file !== null )
                return $file;
        }
        return null;
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
