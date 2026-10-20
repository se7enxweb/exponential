<?php
/**
 * exphelp/context/<help_id> and exphelp/context/manual/<remote_id> (doc/en/us/features/6.0/help-content.md, "Context help
 * by help_id"): a help topic, or a manual's table of contents, as JSON for other clients (a mobile app, a JavaScript
 * front end). Markdown body, ETag (If-None-Match answers 304). Only what the user may see: a Released topic they may
 * read (content/read with its section and state), or a preview for those who may edit it. Anything else, unknown or
 * not, is the same 404 JSON, so the answer does not tell whether an internal topic exists.
 *
 * Policy function exphelp/context (not read): a site may give it to readers of its public help without giving them
 * the help of the administration. Anonymous does not have it unless exphelp.ini [ContentSettings]
 * AnonymousContextHelp=enabled and the roles step of exp:help:content ran.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Context extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        if ( !\expHelp::isEnabled() || !\expHelpContentProvider::isEnabled() )
            self::send( 404, array( 'error' => 'not_found' ) );

        $id = \expHelp::requestedId( $Params, $_GET );
        $provider = \expHelpContentProvider::instance();
        $data = null;
        if ( strpos( $id, 'manual/' ) === 0 )
        {
            $manual = $provider->manual( substr( $id, 7 ) );
            $data = $manual !== null ? $provider->manualJson( $manual ) : null;
        }
        elseif ( $id !== '' )
        {
            $topic = $provider->topic( $id );
            $data = $topic !== null ? $provider->json( $topic ) : null;
        }
        if ( $data === null )
            self::send( 404, array( 'error' => 'not_found' ) );
        self::send( 200, $data );
    }

    /**
     * Sends the JSON and ends the request (an ETag of the body; a matching If-None-Match is a 304)
     *
     * @param int $status
     * @param array $data
     */
    public static function send( $status, array $data )
    {
        $json = (string)json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE );
        $etag = self::etag( $json );
        $match = isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) && is_string( $_SERVER['HTTP_IF_NONE_MATCH'] ) ? $_SERVER['HTTP_IF_NONE_MATCH'] : '';
        header( 'Content-Type: application/json; charset=utf-8' );
        // what a user may see depends on who asks: never in a shared cache
        header( 'Cache-Control: private, no-cache' );
        header( 'Vary: Cookie, Authorization' );
        header( 'X-Content-Type-Options: nosniff' );
        if ( $status === 200 )
        {
            header( 'ETag: ' . $etag );
            if ( self::etagMatches( $match, $etag ) )
            {
                header( 'HTTP/1.1 304 Not Modified', true, 304 );
                \eZExecution::cleanExit();
            }
        }
        else
            header( 'HTTP/1.1 404 Not Found', true, 404 );
        echo $json;
        \eZExecution::cleanExit();
    }

    /**
     * @param string $json
     * @return string a strong ETag of the body
     */
    public static function etag( $json )
    {
        return '"' . substr( sha1( (string)$json ), 0, 32 ) . '"';
    }

    /**
     * Whether an If-None-Match header names this ETag (or *)
     *
     * @param string $header
     * @param string $etag
     * @return bool
     */
    public static function etagMatches( $header, $etag )
    {
        foreach ( explode( ',', (string)$header ) as $candidate )
        {
            $candidate = trim( preg_replace( '/^W\//', '', trim( $candidate ) ) );
            if ( $candidate === '*' || $candidate === $etag )
                return true;
        }
        return false;
    }
}

}
