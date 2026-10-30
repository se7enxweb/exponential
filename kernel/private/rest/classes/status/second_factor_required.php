<?php
/**
 * File containing the expRestSecondFactorRequiredStatus class
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The REST answer to a password that was right for an account that needs a second sign-in step
 * (expSignInSecondFactor): 401 with WWW-Authenticate (an app password, or a one-time code in X-Exp-OTP where the site
 * accepts one, can help) for second_factor_required and second_factor_invalid, 403 for second_factor_setup_required
 * (only the web interface can help). The body is JSON: error (the reason) and error_description.
 */
class expRestSecondFactorRequiredStatus implements ezcMvcResultStatusObject
{
    /** @var string */
    public $reason;

    /** @var string */
    public $message;

    /** The realm of the WWW-Authenticate header */
    const REALM = 'Exponential REST';

    /**
     * @param string $reason expSignInSecondFactor::REASON_*
     * @param string $message
     */
    public function __construct( $reason = expSignInSecondFactor::REASON_REQUIRED, $message = '' )
    {
        $refusal = expSignInSecondFactor::refusal( $reason, $message, expSignInSecondFactor::CHANNEL_REST );
        $this->reason = $refusal['reason'];
        $this->message = $refusal['message'];
    }

    public function process( ezcMvcResponseWriter $writer )
    {
        $refusal = expSignInSecondFactor::refusal( $this->reason, $this->message, expSignInSecondFactor::CHANNEL_REST );
        if ( $writer instanceof ezcMvcHttpResponseWriter )
            $writer->headers['HTTP/1.1 ' . $refusal['status']] = '';
        if ( $refusal['status'] === 401 )
            $writer->headers['WWW-Authenticate'] = expSignInSecondFactor::wwwAuthenticate( self::REALM );
        $writer->headers[expSignInSecondFactor::REFUSAL_HEADER] = $refusal['reason'];
        $writer->headers['Content-Type'] = 'application/json; charset=UTF-8';
        $writer->headers['Cache-Control'] = 'private, no-store';
        $writer->response->body = json_encode( self::body( $refusal['reason'], $refusal['message'] ) );
    }

    /**
     * @param string $reason
     * @param string $message
     * @return array the JSON answer
     */
    public static function body( $reason = expSignInSecondFactor::REASON_REQUIRED, $message = '' )
    {
        $refusal = expSignInSecondFactor::refusal( $reason, $message, expSignInSecondFactor::CHANNEL_REST );
        return array( 'error' => $refusal['reason'], 'error_description' => $refusal['message'] );
    }
}
?>
