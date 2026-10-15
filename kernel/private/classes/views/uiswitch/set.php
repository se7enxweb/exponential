<?php
/**
 * The uiswitch/set view (doc/features/6.0/ui-switch.md): changes one of the signed-in user's interface choices on
 * the current siteaccess and goes back to the page it came from.
 *
 * POST only (405 otherwise): Provider, Value, RedirectURI. The kernel refuses a POST without the form token (403)
 * before this view runs; this view also refuses a request the browser marks as coming from another site
 * (Sec-Fetch-Site: cross-site, 403), a provider the user may not use (403), a value outside the provider's
 * allow-list (400) and a siteaccess without the switch (404). RedirectURI is followed only when it is a local path.
 * A change is recorded in the audit (access.uiswitch.change).
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Uiswitch
{

class Set extends \Exponential\Runnable\ModuleView
{
    /** where the browser goes when RedirectURI is missing or not a local path */
    const FALLBACK = '/content/dashboard';

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $module = $Params['Module'];

        $decision = self::decide( isset( $_SERVER['REQUEST_METHOD'] ) ? (string)$_SERVER['REQUEST_METHOD'] : '',
                                  isset( $_SERVER['HTTP_SEC_FETCH_SITE'] ) ? (string)$_SERVER['HTTP_SEC_FETCH_SITE'] : null,
                                  $_POST );
        if ( $decision['step'] === 'refused' )
            return $this->refuse( $decision['reason'] );

        $outcome = \expUISwitch::set( $decision['provider'], $decision['value'] );
        if ( $outcome !== 'ok' )
            return $this->refuse( $outcome );

        return $module->redirectTo( $decision['redirect'] );
    }

    /**
     * What a request leads to, from its method, its Sec-Fetch-Site header and its posted fields only.
     *
     * @param string $method
     * @param string|null $secFetchSite
     * @param array $post
     * @return array array( 'step' => refused|set, 'reason' => '' | method | cross-site | invalid,
     *               'provider' => string, 'value' => string, 'redirect' => local path )
     */
    public static function decide( $method, $secFetchSite, array $post )
    {
        $result = array( 'step' => 'refused', 'reason' => '', 'provider' => '', 'value' => '',
                         'redirect' => \expUISwitch::safeRedirect( isset( $post['RedirectURI'] ) ? $post['RedirectURI'] : null, self::FALLBACK ) );
        if ( strtoupper( $method ) !== 'POST' )
            return array( 'reason' => 'method' ) + $result;
        if ( !\expUISwitch::isSameSiteRequest( $secFetchSite ) )
            return array( 'reason' => 'cross-site' ) + $result;
        $provider = isset( $post['Provider'] ) && is_string( $post['Provider'] ) ? $post['Provider'] : '';
        $value = isset( $post['Value'] ) && is_string( $post['Value'] ) ? $post['Value'] : '';
        if ( !preg_match( \expUISwitch::IDENTIFIER_PATTERN, $provider ) || $value === '' )
            return array( 'reason' => 'invalid' ) + $result;
        return array( 'step' => 'set', 'provider' => $provider, 'value' => $value ) + $result;
    }

    /**
     * The HTTP status of a refusal.
     *
     * @param string $reason
     * @return int
     */
    public static function status( $reason )
    {
        switch ( $reason )
        {
            case 'method': return 405;
            case 'invalid': return 400;
            case 'unavailable': return 404;
            default: return 403;
        }
    }

    private function refuse( $reason )
    {
        if ( !headers_sent() )
        {
            http_response_code( self::status( $reason ) );
            if ( $reason === 'method' )
                header( 'Allow: POST' );
        }
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'reason', $reason );
        return array( 'content' => $tpl->fetch( 'design:uiswitch/refused.tpl' ),
                      'path' => array( array( 'text' => \ezpI18n::tr( 'kernel/uiswitch', 'Interface' ), 'url' => false ) ) );
    }
}

}
