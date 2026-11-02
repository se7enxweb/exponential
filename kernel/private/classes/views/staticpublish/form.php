<?php
/**
 * The staticpublish/form view: the form endpoint of static publishing (doc/eng-us/specifications/6.0/
 * static-publishing-form-endpoint.md). expStaticFormEndpoint decides (enabled, the target, the origin, the token, the
 * rate); this view reads the request, sends the answer and hands an accepted post to the normal information
 * collection (content/action with ActionCollectInformation), so the collector's validation, e-mail and settings apply
 * as on the live site. Anyone may post (site.ini PolicyOmitList[]=staticpublish/form); the information collection
 * runs as the current user, the anonymous user for a visitor of a static copy, with its policies.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Staticpublish
{

class Form extends Page
{
    public function run( array $scope )
    {
        $settings = \expStaticFormEndpoint::settings();
        $rateDir = \eZSys::cacheDirectory() . '/staticpublish/rate';
        $endpoint = new \expStaticFormEndpoint( array(
            'enabled' => $settings['enabled'], 'targets' => $settings['targets'], 'days' => $settings['days'],
            'rate' => function ( $key ) use ( $rateDir, $settings )
            {
                return ( new \expLogRateLimit( $rateDir, $key, $settings['limit'], $settings['window'] ) )->hit();
            },
            'hand_over' => array( __CLASS__, 'handOver' ),
        ) );
        $headers = array();
        foreach ( array( 'origin' => 'HTTP_ORIGIN', 'referer' => 'HTTP_REFERER', 'accept' => 'HTTP_ACCEPT', 'x-requested-with' => 'HTTP_X_REQUESTED_WITH' ) as $name => $key )
            if ( isset( $_SERVER[$key] ) )
                $headers[$name] = (string)$_SERVER[$key];
        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? (string)$_SERVER['REQUEST_METHOD'] : 'GET';
        $answer = $endpoint->handle( $method, $headers, is_array( $_POST ) ? $_POST : array(), isset( $_SERVER['REMOTE_ADDR'] ) ? (string)$_SERVER['REMOTE_ADDR'] : '' );

        if ( method_exists( '\eZExecution', 'discardOutputBuffers' ) )
            \eZExecution::discardOutputBuffers();
        else
            while ( ob_get_level() > 0 && @ob_end_clean() );
        http_response_code( (int)$answer['status'] );
        header( 'X-Content-Type-Options: nosniff' );
        header( 'X-Robots-Tag: noindex, nofollow' );
        foreach ( $answer['headers'] as $name => $value )
            header( $name . ': ' . $value );
        if ( $answer['redirect'] !== null )
            header( 'Location: ' . $answer['redirect'] );
        else if ( $answer['json'] !== null )
        {
            header( 'Content-Type: application/json; charset=utf-8' );
            echo json_encode( $answer['json'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        }
        \eZExecution::cleanExit();
        return $this->viewResult( null, null );
    }

    /**
     * Hands an accepted post to the information collection of content/action.
     *
     * @param \expStaticTarget $target
     * @param array $fields the form's own fields
     * @return array ok, message
     */
    public static function handOver( \expStaticTarget $target, array $fields )
    {
        $saved = $_POST;
        $_POST = $fields + array( 'ActionCollectInformation' => '1' );
        try
        {
            $content = \eZModule::exists( 'content' );
            if ( !$content instanceof \eZModule )
                return array( false, self::t( 'The form could not be sent.' ) );
            $content->run( 'action', array() );
            $ok = $content->exitStatus() == \eZModule::STATUS_REDIRECT;
        }
        finally
        {
            $_POST = $saved;
        }
        return $ok ? array( true, self::t( 'Thank you: the form was sent.' ) )
                   : array( false, self::t( 'The form was not accepted: check what you entered and send it again.' ) );
    }
}

}
?>
