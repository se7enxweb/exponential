<?php
/**
 * The feed of the log viewer (setup/logsfeed; doc/eng-us/features/6.0/log-viewer.md, "The feed"): JSON for the page's
 * script, one short request each, never held open (so a Velocity worker or a PHP-FPM child is busy for the
 * milliseconds of one read, not for as long as a page stays open).
 *
 * The options are view parameters, setup/logsfeed/(action)/tail/(file)/<id>/(offset)/<n>..., each value written as
 * every view parameter is (expViewParameters::encode(): "~2F" for "/"), or fields of a POST; a query string is never
 * read.
 * (action):
 *   tail       file, offset (-1: the end), ino      what the file gained since the offset
 *   search     file, scope, rotations, cursor, and the criteria (q, regex, case, levels, from, to, siteaccess, uri, ip)
 *   related    file, offset                         the lines of the same request, and the audit window
 *   reference  reference (ERR-XXXXXXXXXX)           where the reference is, and the lines of its request
 *   files      -                                    the navigator (sizes, times, line rates)
 * unmask=1 shows unmasked lines to a user with setup/unmasklogs. Every request counts against the session's limit
 * ([RateLimitSettings]); over it the answer is 429 with Retry-After. The answer carries cost: the milliseconds the
 * server spent and, for a poll, the stat, open and cache counts.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Setup
{

class Logsfeed extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $http = \eZHTTPTool::instance();
        $Params = isset( $scope['Params'] ) && is_array( $scope['Params'] ) ? $scope['Params'] : array();
        // the options are view parameters (/(action)/tail/(file)/<id>..., values written as every view parameter is,
        // expViewParameters::encode(), and decoded by get()) or fields of a POST; a query string is never read
        $get = function ( $name, $default = '' ) use ( $http, $Params ) {
            $value = \expViewParameters::get( $Params, $name, $name );
            if ( $value !== false )
                return $value;
            if ( $http->hasPostVariable( $name ) )
                return $http->postVariable( $name );
            return $default;
        };
        $askUnmask = (string)$get( 'unmask' ) === '1';
        $viewer = \expLogViewer::forCurrentUser( $askUnmask );
        if ( !$viewer->enabled() )
            return self::send( array( 'error' => 'disabled' ), 404 );
        $config = $viewer->config();

        $limit = \expLogRateLimit::forSession( $config );
        $retry = $limit->hit();
        if ( $retry > 0 )
            return self::send( array( 'error' => 'rate_limited', 'retry_after' => $retry ), 429, array( 'Retry-After: ' . $retry ) );

        $action = (string)$get( 'action', 'tail' );
        $fileID = is_string( $get( 'file' ) ) ? (string)$get( 'file' ) : '';
        $data = null;
        switch ( $action )
        {
            case 'tail':
                $offset = is_numeric( $get( 'offset', -1 ) ) ? (int)$get( 'offset', -1 ) : -1;
                $data = $viewer->tail( $fileID, max( -1, $offset ), max( 0, (int)$get( 'ino', 0 ) ) );
                if ( $data === null )
                    return self::send( array( 'error' => 'not_allowed' ), 404 );
                if ( $offset === -1 )
                {
                    $file = $viewer->locator()->resolve( $fileID );
                    if ( $file !== null )
                        $viewer->auditRead( $file, $limit, $viewer->unmasked() );
                }
                break;

            case 'search':
                $in = array();
                foreach ( array( 'q', 'regex', 'case', 'levels', 'from', 'to', 'siteaccess', 'uri', 'ip', 'reference' ) as $f )
                {
                    $v = $get( $f, null );
                    if ( $v !== null )
                        $in[$f] = $v;
                }
                $criteria = \expLogSearch::criteria( $in, $config->int( 'SearchSettings', 'MaxPatternLength' ) );
                $scopeName = in_array( $get( 'scope' ), array( 'file', 'kernel', 'all' ), true ) ? (string)$get( 'scope' ) : 'file';
                $files = $viewer->searchFiles( $fileID, $scopeName, (string)$get( 'rotations' ) === '1' );
                if ( !$files )
                    return self::send( array( 'error' => 'not_allowed' ), 404 );
                $data = $viewer->search( $files, $criteria, is_string( $get( 'cursor' ) ) ? (string)$get( 'cursor' ) : '' );
                break;

            case 'related':
                $data = $viewer->related( $fileID, max( 0, (int)$get( 'offset', 0 ) ) );
                if ( $data === null )
                    return self::send( array( 'error' => 'not_allowed' ), 404 );
                break;

            case 'reference':
                $data = $viewer->reference( is_string( $get( 'reference' ) ) ? (string)$get( 'reference' ) : '' );
                break;

            case 'files':
                $data = array( 'groups' => \expLogViewer::navigatorForCurrentUser() );
                break;

            default:
                return self::send( array( 'error' => 'unknown_action' ), 400 );
        }
        $data['unmasked'] = $viewer->unmasked();
        $data['server_ms'] = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? round( ( microtime( true ) - (float)$_SERVER['REQUEST_TIME_FLOAT'] ) * 1000, 1 ) : null;
        return self::send( $data, 200 );
    }

    /**
     * Sends JSON and ends the request.
     *
     * @param array $data
     * @param int $status
     * @param string[] $headers
     */
    public static function send( array $data, $status = 200, array $headers = array() )
    {
        if ( method_exists( '\eZExecution', 'discardOutputBuffers' ) )
            \eZExecution::discardOutputBuffers();
        if ( $status !== 200 )
            http_response_code( $status );
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Cache-Control: no-store' );
        header( 'X-Content-Type-Options: nosniff' );
        foreach ( $headers as $h )
            header( $h );
        echo json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE );
        \eZExecution::cleanExit();
    }
}

}
