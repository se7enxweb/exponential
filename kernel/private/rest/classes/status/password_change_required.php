<?php
/**
 * File containing the expRestPasswordChangeRequiredStatus class
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * 403 Forbidden with the error code password_change_required: the credentials are right, but the account is marked
 * "change password at next sign-in" (expPasswordChangeRequired) and must set a new password in the web interface
 * (user/password) before the REST interface answers it.
 */
class expRestPasswordChangeRequiredStatus implements ezcMvcResultStatusObject
{
    public function process( ezcMvcResponseWriter $writer )
    {
        if ( $writer instanceof ezcMvcHttpResponseWriter )
            $writer->headers['HTTP/1.1 403'] = '';
        $writer->headers['Content-Type'] = 'application/json; charset=UTF-8';
        $writer->headers['Cache-Control'] = 'private, no-store';
        $writer->response->body = json_encode( self::body() );
    }

    /**
     * @return array the JSON answer
     */
    public static function body()
    {
        return array( 'error' => 'password_change_required',
                      'error_description' => 'This account must set a new password before it can use the REST interface. '
                                           . 'Sign in to the site and change the password (user/password).' );
    }
}
?>
