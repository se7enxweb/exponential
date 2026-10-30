<?php
/**
 * File containing the expWebDAVPathFactory class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * The path factory of index_webdav.php: paths inside the server are plain (decoded) text, URIs are encoded.
 *
 * A path such as "/site/Content/Annual report 2026.docx" stays decoded everywhere inside the server and the
 * backend, so the paths of requests, of PROPFIND responses and of the lock plugin's own PROPFIND and PROPPATCH
 * requests compare equal; the URIs written into responses (href, lock root) get each path element URL encoded
 * (in UTF-8, from the [CharacterSettings] Charset of i18n.ini), and URIs read from headers (If, Destination) are
 * decoded.
 */
class expWebDAVPathFactory extends ezcWebdavAutomaticPathFactory
{
    /**
     * Returns the decoded path of $uri.
     *
     * @param string $uri
     * @return string
     */
    public function parseUriToPath( $uri )
    {
        return rawurldecode( parent::parseUriToPath( $uri ) );
    }

    /**
     * Returns the URI of $path, each path element URL encoded.
     *
     * @param string $path
     * @return string
     */
    public function generateUriFromPath( $path )
    {
        $uri = parent::generateUriFromPath( $path );
        $plain = parent::generateUriFromPath( '' );
        $encoded = self::encodePath( substr( $uri, strlen( $plain ) ) );
        return $plain . $encoded;
    }

    /**
     * Returns $path with each element URL encoded (rawurlencode()), the slashes kept.
     *
     * @param string $path Eg. '/site/Content/Annual report.docx'
     * @return string Eg. '/site/Content/Annual%20report.docx'
     */
    public static function encodePath( $path )
    {
        $dataCharset = eZINI::instance( 'i18n.ini' )->variable( 'CharacterSettings', 'Charset' );
        if ( $dataCharset && strtolower( $dataCharset ) !== 'utf-8' )
        {
            $codec = eZTextCodec::instance( $dataCharset, 'utf-8', false );
            if ( $codec )
            {
                $path = $codec->convertString( $path );
            }
        }
        return self::encodePathElements( $path );
    }

    /**
     * Returns $path with each element URL encoded (rawurlencode()), the slashes kept, without a charset
     * conversion. Pure.
     *
     * @param string $path
     * @return string
     */
    public static function encodePathElements( $path )
    {
        return implode( '/', array_map( 'rawurlencode', explode( '/', (string)$path ) ) );
    }
}
