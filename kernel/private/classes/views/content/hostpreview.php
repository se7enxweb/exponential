<?php
/**
 * File containing the Exponential\View\Kernel\Content\Hostpreview class: the view content/hostpreview/<token>.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Content
{

/**
 * The preview of a version on the host of its siteaccess (expContentHostPreview): content/versionview in the
 * administration sends the preview frame here with a signed one-time token when the siteaccess of the preview is
 * served on another host. The version is shown once, in this siteaccess's design, with this host's menus, links and
 * headers, as content/versionview shows it in the administration; nobody is signed in and no session is started.
 *
 * No policy (site.ini [RoleSettings] PolicyOmitList[]): the token is the permission, for that one version. The
 * page may be framed by the administration that asked for it (its origin is in the token): X-Frame-Options is
 * dropped and the origin is added to frame-ancestors. Never kept in a cache, never indexed, no referrer. A token
 * that does not work answers 404 (not a token, another siteaccess's) or 410 (used, expired, the version is gone).
 * Guide: doc/eng-us/features/6.0/preview-in-owning-siteaccess.md
 */
class Hostpreview extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $token = isset( $scope['Params']['Token'] ) ? (string)$scope['Params']['Token'] : '';

        if ( class_exists( 'ezpRequestRuleKernel' ) )
            \ezpRequestRuleKernel::keepOutOfSharedCaches();
        else
            $GLOBALS['ezpRequestRuleNoStore'] = true;
        if ( !headers_sent() )
        {
            header( 'Cache-Control: private, no-store' );
            header( 'X-Robots-Tag: noindex, nofollow' );
            header( 'Referrer-Policy: no-referrer' );
        }

        $current = \eZSiteAccess::current();
        $shown = \expContentHostPreview::redeem( $token, isset( $current['name'] ) ? (string)$current['name'] : '' );
        if ( isset( $shown['error'] ) )
            return $this->unavailable( (string)$shown['error'] );
        $claims = $shown['claims'];

        self::allowFramingBy( $claims['a'] );

        $viewMode = Versionview::viewMode( $claims['m'] );
        list( $Result ) = Sharedpreview::renderVersion( $shown['object'], $shown['version'], $claims['l'],
                                                         $viewMode === null ? 'full' : $viewMode, $claims['p'] );
        $Result['requested_uri_string'] = 'content/hostpreview';
        $Result['ui_context'] = 'view';
        return $Result;
    }

    /**
     * Lets the administration at $origin show this response in its frame: X-Frame-Options goes, and $origin is added
     * to the frame-ancestors of every Content-Security-Policy already set (the other directives stay).
     *
     * @param string $origin
     * @return void
     */
    public static function allowFramingBy( $origin )
    {
        if ( headers_sent() || !\expContentHostPreview::validOrigin( $origin ) )
            return;
        $policies = array();
        foreach ( headers_list() as $line )
        {
            if ( stripos( $line, 'Content-Security-Policy:' ) === 0 )
                $policies[] = trim( substr( $line, strlen( 'Content-Security-Policy:' ) ) );
        }
        header_remove( 'X-Frame-Options' );
        if ( !$policies )
            return;
        header_remove( 'Content-Security-Policy' );
        foreach ( $policies as $policy )
            header( 'Content-Security-Policy: ' . \expContentHostPreview::frameAncestors( $policy, $origin ), false );
    }

    /**
     * The page of a token that does not work: 404 for no token or another siteaccess's, 410 for one used or ended.
     *
     * @param string $reason invalid, siteaccess, used, expired, gone
     * @return array
     */
    protected function unavailable( $reason )
    {
        if ( !headers_sent() )
            http_response_code( in_array( $reason, array( 'invalid', 'siteaccess' ), true ) ? 404 : 410 );
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'state', $reason );
        $title = \ezpI18n::tr( 'kernel/content/hostpreview', 'Preview not available' );
        return array( 'content' => $tpl->fetch( 'design:content/hostpreview.tpl' ),
                      'path' => array( array( 'text' => $title, 'url' => false ) ),
                      'requested_uri_string' => 'content/hostpreview' );
    }
}

}
