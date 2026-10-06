<?php
/**
 * File containing the expRestRateLimitedStatus class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * 429 Too Many Requests with Retry-After, for a personal API key over its rate limit
 * (rest.ini [ApiKeySettings] RateLimitPerMinute).
 */
class expRestRateLimitedStatus implements ezcMvcResultStatusObject
{
    /**
     * Seconds until the next minute starts and the key may send again.
     *
     * @var int
     */
    public $retryAfter;

    public function __construct( $retryAfter = 60 )
    {
        $this->retryAfter = max( 1, (int)$retryAfter );
    }

    public function process( ezcMvcResponseWriter $writer )
    {
        if ( $writer instanceof ezcMvcHttpResponseWriter )
        {
            $writer->headers['HTTP/1.1 429'] = '';
            $writer->headers['Retry-After'] = (string)$this->retryAfter;
        }
        $writer->headers['Content-Type'] = 'application/json; charset=UTF-8';
        $writer->response->body = json_encode( array( 'error' => 'rate_limited',
                                                      'error_description' => 'Too many requests for this API key. Try again in ' . $this->retryAfter . ' seconds.' ) );
    }
}
?>
