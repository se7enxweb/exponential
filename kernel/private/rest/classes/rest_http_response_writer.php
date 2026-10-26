<?php
/**
 * File containing the ezpRestHttpResponseWriter class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

class ezpRestHttpResponseWriter extends ezcMvcHttpResponseWriter
{
    /**
     * The response struct object.
     *
     * In the ezp rest version this variable is public, so that error messages
     * can be injected into the response body.
     *
     * @var ezcMvcResponse
     */
    public $response;

    /**
     * Sends the response, with its status through http_response_code().
     *
     * The REST status objects put the status into the header list as a
     * "HTTP/1.1 <code>" entry, and the parent writer sends every entry as
     * header( "$name: $value" ), so the status line became "HTTP/1.1 401: ".
     * Apache and PHP-FPM read the code out of it, but the PHP built-in server
     * (php -S) sends it as it stands, which is not a valid status line, and it
     * claimed HTTP/1.1 whatever the request's protocol. The status entries are
     * taken out of the list and the last one is set with http_response_code(),
     * which writes "<protocol> <code> <phrase>" under every SAPI. It is set
     * after the other headers, because header( 'WWW-Authenticate: ...' ) makes
     * PHP answer 401 and a 403 or 400 challenge has to win over that.
     */
    public function handleResponse()
    {
        $this->processStandardHeaders();
        if ( $this->response->cache instanceof ezcMvcResultCache )
        {
            $this->processCacheHeaders();
        }
        if ( $this->response->content instanceof ezcMvcResultContent )
        {
            $this->processContentHeaders();
        }

        if ( $this->response->status instanceof ezcMvcResultStatusObject )
        {
            $this->response->status->process( $this );
        }

        $this->headers['Content-Length'] = strlen( (string)$this->response->body );

        $status = self::takeStatusCode( $this->headers );
        foreach ( $this->headers as $header => $value )
        {
            header( "$header: $value" );
        }
        if ( $status !== null )
        {
            http_response_code( $status );
        }

        foreach ( $this->response->cookies as $cookie )
        {
            $this->sendCookie( $cookie );
        }
        echo $this->response->body;
    }

    /**
     * Removes the status line entries ("HTTP/1.1 401", "HTTP/1.0 404" ...)
     * from $headers and returns the code of the last one.
     *
     * @param array(string=>string) $headers
     * @return int|null null when there is none.
     */
    public static function takeStatusCode( array &$headers )
    {
        $status = null;
        foreach ( array_keys( $headers ) as $name )
        {
            if ( preg_match( '#^HTTP/\d+(?:\.\d+)?\s+(\d{3})\b#i', (string)$name, $match ) )
            {
                $status = (int)$match[1];
                unset( $headers[$name] );
            }
        }

        return $status;
    }

    /**
     * Sends $cookie with setcookie(), as the parent writer does (whose
     * processCookie() is private), with path and domain in setcookie()'s order.
     *
     * @param ezcMvcResultCookie $cookie
     */
    protected function sendCookie( ezcMvcResultCookie $cookie )
    {
        setcookie(
            $cookie->name,
            (string)$cookie->value,
            $cookie->expire instanceof DateTime ? (int)$cookie->expire->format( 'U' ) : 0,
            (string)$cookie->path,
            (string)$cookie->domain,
            (bool)$cookie->secure,
            (bool)$cookie->httpOnly
        );
    }
}
