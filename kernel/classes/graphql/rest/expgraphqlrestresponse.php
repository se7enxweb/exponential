<?php
/**
 * The HTTP answer of the GraphQL end point: a status, headers and the answer as JSON (UTF-8 as it is, slashes not
 * escaped, a broken byte sequence replaced rather than the whole answer lost).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLRestResponse extends ezpRestStatusResponse
{
    public function process( ezcMvcResponseWriter $writer )
    {
        if ( $writer instanceof ezcMvcHttpResponseWriter )
        {
            $writer->headers["HTTP/1.1 " . $this->code] = isset( self::$statusCodes[$this->code] ) ? self::$statusCodes[$this->code]
                                                                                                 : ( $this->code === 429 ? 'Too Many Requests' : '' );
            $writer->headers = $this->headers + $writer->headers;
        }
        if ( $this->code === 304 )
            $writer->response->body = '';
        else if ( $this->message !== null )
        {
            $writer->headers['Content-Type'] = 'application/json; charset=UTF-8';
            // a string is an answer already written as JSON (the response cache keeps answers so)
            $writer->response->body = is_string( $this->message ) ? $this->message : self::encode( $this->message );
        }
    }

    /**
     * @param mixed $answer
     * @return string
     */
    public static function encode( $answer )
    {
        $json = json_encode( $answer, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
        return $json === false ? '{"errors":[{"message":"The answer could not be written as JSON.","extensions":{"code":"INTERNAL"}}]}' : $json;
    }
}

?>
