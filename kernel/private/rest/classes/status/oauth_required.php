<?php
/**
 * File containing the ezpOauthRequired class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * This result type is used to signal a HTTP basic auth header
 */
class ezpOauthRequired implements ezcMvcResultStatusObject
{
    const DEFAULT_REALM = 'Exponential REST';

    /**
     * The realm is the unique ID to identify a login area
     *
     * @var string
     */
    public $realm;

    /**
     * The error type identifier as defined per section 5.2.1 of oauth2.0 #10
     *
     * @var string
     */
    public $errorType;

    /**
     * An optional human-readable error message.
     *
     * @var string
     */
    public $errorMessage;

    public function __construct( $realm, $errorType = null, $errorMessage = null )
    {
        $this->realm = $realm;
        $this->errorType = $errorType;
        $this->errorMessage = $errorMessage;
    }

    /**
     * Uses the passed in $writer to set the HTTP authentication header.
     *
     * @param ezcMvcResponseWriter $writer
     */
    public function process( ezcMvcResponseWriter $writer )
    {
        if ( $writer instanceof ezcMvcHttpResponseWriter )
        {
            // No error type: the request carried no credentials at all, which
            // is a 401 challenge (RFC 6750 section 3.1). httpCodeforError()
            // answers 500 for it; PHP's own header() used to hide that by
            // turning any response with WWW-Authenticate into a 401, which a
            // server with its own header() handling does not do.
            $code = $this->errorType === null
                ? ezpHttpResponseCodes::UNAUTHORIZED
                : ezpOauthErrorType::httpCodeforError( $this->errorType );
            $writer->headers['HTTP/1.1 ' . $code] = "";
            $writer->headers['WWW-Authenticate'] = "OAuth realm='{$this->realm}'{$this->createErrorString()}";
        }

        if ( isset( $this->errorType) )
        {
            $writer->headers['Content-Type'] = 'application/json; charset=UTF-8';
            $body = array( 'error' => $this->errorType );

            if ( isset( $this->errorMessage ) )
                $body['error_description'] = $this->errorMessage;

            $writer->response->body = json_encode( $body );
        }
    }

    /**
     * Creates for use in authentcation challenge header
     *
     * @return string
     */
    protected function createErrorString()
    {
        $str = '';
        if ( $this->errorType !== null )
        {
            $str .= ", error='{$this->errorType}'";
        }

        if ( $this->errorMessage !== null )
        {
            $str .= ", error_description='{$this->errorMessage}'";
        }
        return $str;
    }
}
?>
