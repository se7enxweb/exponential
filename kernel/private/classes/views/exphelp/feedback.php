<?php
/**
 * exphelp/feedback/<help_id> (doc/eng-us/features/6.0/help-content.md, "Was this helpful?"): stores one answer to "Was
 * this helpful?" of a Released help topic (expHelpContentFeedback). POST only: Helpful (yes, no), Reason (for a no,
 * one of expHelpContentFeedback::REASONS), RedirectURI (optional: a path of this site to go back to, the topic's page
 * on the public site). Without RedirectURI back to exphelp/topic/<help_id>; the address gets (helpful)/thanks.
 *
 * Policy: exphelp/read or exphelp/feedback (a site gives feedback to the readers of its public help without the help
 * of the administration; Anonymous gets it with exphelp.ini [ContentSettings] AnonymousFeedback=enabled and the roles
 * step). Only a topic the reader may see and that is Released can be answered; anything else is a 404. One answer per
 * topic and session is counted; a second one is thanked for and not stored.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Feedback extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !\expHelp::isEnabled() || !\expHelpContentProvider::isEnabled() || !\expHelpContentFeedback::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $helpId = \expHelp::requestedId( $Params );
        $topic = $helpId !== '' ? \expHelpContentProvider::instance()->topic( $helpId ) : null;
        if ( $topic === null || !empty( $topic['preview'] ) || ( isset( $topic['state'] ) && $topic['state'] !== \expHelpContentSchema::STATE_RELEASED ) )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );

        $http = \eZHTTPTool::instance();
        $back = self::redirectTarget( $http->hasPostVariable( 'RedirectURI' ) ? $http->postVariable( 'RedirectURI' ) : '', 'exphelp/topic/' . $topic['help_id'] );
        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string)$_SERVER['REQUEST_METHOD'] ) : 'GET';
        if ( $method !== 'POST' )
            return $Module->redirectTo( $back );

        $answer = \expHelpContentFeedback::normalise( $http->hasPostVariable( 'Helpful' ) ? $http->postVariable( 'Helpful' ) : null,
                                                      $http->hasPostVariable( 'Reason' ) ? $http->postVariable( 'Reason' ) : '' );
        if ( $answer === null )
            return $Module->redirectTo( $back );
        if ( !\expHelpContentFeedback::answeredInSession( (int)$topic['object_id'] ) )
        {
            \expHelpContentFeedback::record( $topic, $answer );
            \expHelpContentFeedback::rememberInSession( (int)$topic['object_id'] );
        }
        return $Module->redirectTo( rtrim( $back, '/' ) . '/(helpful)/thanks' );
    }

    /**
     * Where to go after the answer: a path of this site (no scheme, no host, no "//"), else the default
     *
     * @param mixed $uri
     * @param string $default
     * @return string
     */
    public static function redirectTarget( $uri, $default )
    {
        if ( !is_string( $uri ) )
            return $default;
        $uri = trim( $uri );
        if ( $uri === '' || strlen( $uri ) > 500 || !preg_match( '#^/[^/\\\\]#', $uri ) || preg_match( '#[\x00-\x1f]|\(helpful\)#', $uri ) )
            return $default;
        if ( strpos( $uri, '?' ) !== false || strpos( $uri, '#' ) !== false )
            return $default;
        return $uri;
    }
}

}
