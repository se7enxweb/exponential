<?php
/**
 * The staticpublish/serve view (/staticpublish/serve/<target>/<expires>/<signature>/<path>): the local preview of a
 * build. It answers with the files of the target's folder as a static host would (expStaticServe::resolve(): a folder
 * gives its index.html, a missing file the build's 404.html), never with .git or .static-publish. The link is made by
 * the preview page and signed for its user and an hour (expStaticAdmin::previewSignature()); the user needs
 * staticpublish/read for the target as well. A build with relative links (the default) works below this address as it
 * will on the host; one written for a base path links outside it.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Staticpublish
{

class Serve extends Page
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $parts = isset( $Params['Parameters'] ) && is_array( $Params['Parameters'] ) ? array_values( array_map( 'strval', $Params['Parameters'] ) ) : array();
        if ( count( $parts ) < 3 )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        list( $targetId, $expires, $signature ) = array_slice( $parts, 0, 3 );
        $userId = (int)\eZUser::currentUser()->attribute( 'contentobject_id' );
        if ( !\expStaticAdmin::previewValid( $targetId, $userId, $expires, $signature ) )
            return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
        $target = self::target( array( 'TargetID' => $targetId ), 'read' );
        if ( $target === null )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );

        $publisher = \expStaticAdmin::publisher( $target );
        $root = rtrim( $publisher->checkoutDirectory() . '/' . $target->folder(), '/' );
        $rest = array_slice( $parts, 3 );
        $requestUri = (string)\eZSys::serverVariable( 'REQUEST_URI', true );
        $uriPath = (string)parse_url( $requestUri, PHP_URL_PATH );
        $trailing = $uriPath !== '' && substr( $uriPath, -1 ) === '/';
        $path = '/' . implode( '/', array_map( 'rawurlencode', array_map( 'rawurldecode', $rest ) ) ) . ( $rest && $trailing ? '/' : '' );
        $answer = \expStaticServe::resolve( $root, '/', $path );

        if ( $answer['status'] === 301 )
        {
            header( 'Location: ' . rtrim( $uriPath, '/' ) . '/', true, 301 );
            \eZExecution::cleanExit();
        }
        if ( method_exists( '\eZExecution', 'discardOutputBuffers' ) )
            \eZExecution::discardOutputBuffers();
        else
            while ( ob_get_level() > 0 && @ob_end_clean() );
        header( 'Cache-Control: private, no-store' );
        header( 'X-Content-Type-Options: nosniff' );
        header( 'X-Robots-Tag: noindex, nofollow' );
        header( "Content-Security-Policy: default-src 'self' data: blob: https:; script-src 'self' 'unsafe-inline' https:; style-src 'self' 'unsafe-inline' https:; frame-ancestors 'self'" );
        if ( $answer['file'] === null )
        {
            header( 'HTTP/1.1 404 Not Found', true, 404 );
            header( 'Content-Type: text/plain; charset=utf-8' );
            echo self::t( 'Not in the build.' );
            \eZExecution::cleanExit();
        }
        if ( $answer['status'] === 404 )
            header( 'HTTP/1.1 404 Not Found', true, 404 );
        header( 'Content-Type: ' . $answer['type'] );
        header( 'Content-Length: ' . filesize( $answer['file'] ) );
        readfile( $answer['file'] );
        \eZExecution::cleanExit();
        return $this->viewResult( null, null );
    }
}

}
?>
