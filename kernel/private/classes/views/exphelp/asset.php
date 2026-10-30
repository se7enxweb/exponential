<?php
/**
 * exphelp/asset/<path> (doc/eng-us/features/6.0/help-system.md): an image below a help root. Only files
 * expHelpDocumentIndex::asset() allows (git-tracked, allowed suffix); anything else is a 404. Sent with the content
 * type of its suffix, nosniff and a private cache lifetime.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
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

        // a regenerated image (the language map) keeps its address, and its links never carry a version query: the
        // browser keeps its copy but asks again on every use, and gets a 304 while the file is the same
        clearstatcache( true, $file );
        $etag = self::etag( $file );
        header( 'Cache-Control: private, no-cache' );
        header( 'ETag: ' . $etag );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', (int)filemtime( $file ) ) . ' GMT' );
        header( 'X-Content-Type-Options: nosniff' );
        header( "Content-Security-Policy: default-src 'none'" );
        if ( self::notModified( $etag, isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) ? (string)$_SERVER['HTTP_IF_NONE_MATCH'] : '' ) )
        {
            header( 'HTTP/1.1 304 Not Modified', true, 304 );
            \eZExecution::cleanExit();
        }
        header( 'Content-Type: ' . $type );
        header( 'Content-Length: ' . filesize( $file ) );
        readfile( $file );
        \eZExecution::cleanExit();
    }

    /**
     * The validator of a file: its modification time, size and content hash, so a rewrite with the same size in the
     * same second is still a new version.
     *
     * @param string $file
     * @return string a quoted entity tag
     */
    public static function etag( $file )
    {
        return '"' . dechex( (int)filemtime( $file ) ) . '-' . dechex( (int)filesize( $file ) ) . '-' .
               substr( (string)md5_file( $file ), 0, 16 ) . '"';
    }

    /**
     * Whether the browser's If-None-Match header names $etag (a list, weak tags or "*").
     *
     * @param string $etag
     * @param string $ifNoneMatch
     * @return bool
     */
    public static function notModified( $etag, $ifNoneMatch )
    {
        if ( trim( $ifNoneMatch ) === '' )
            return false;
        foreach ( explode( ',', $ifNoneMatch ) as $tag )
        {
            $tag = trim( $tag );
            if ( $tag === '*' || $tag === $etag || $tag === 'W/' . $etag )
                return true;
        }
        return false;
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
