<?php
/**
 * The code of kernel/setup/preload.php, moved into a class (#207 stage 1). The file kernel/setup/preload.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/setup/preload.php:
 *
 *
 * File containing the setup/preload view.
 *
 * Renders the preloader's console. The page itself does no work: it opens an
 * EventSource against setup/preloadstream and prints what arrives, so the
 * operator sees each page warm as it happens instead of waiting on one long
 * request that a proxy is free to time out.
 *
 * Ported from kernel/ezsitemanager/admin/preload.php in exponentialbasic, which
 * did the same thing against a hand rolled template engine.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Setup
{

class Preload extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];

        $ini = \eZINI::instance( 'site.ini' );

        require_once 'kernel/setup/exppreloadrunner.php';

        // The siteaccesses worth warming: the related ones, minus the one this view is
        // being served from. Running from the administration interface the current
        // siteaccess is the admin one, whose SiteURL is the administration host, so
        // warming it would warm nothing a visitor ever sees.
        $current = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : '';
        $related = $ini->hasVariable( 'SiteAccessSettings', 'RelatedSiteAccessList' )
                 ? (array)$ini->variable( 'SiteAccessSettings', 'RelatedSiteAccessList' ) : array();

        $targets = array();
        foreach ( $related as $name )
        {
            if ( $name === '' || $name === $current )
                continue;
            $probe = new \expPreloadRunner( function () {}, array( 'siteaccess' => $name ) );
            $url = $probe->baseUrl();
            if ( $url !== false )
                $targets[] = array( 'name' => $name, 'url' => $url );
        }

        $selected = isset( $targets[0] ) ? $targets[0]['name'] : '';
        $runner = new \expPreloadRunner( function () {}, array( 'siteaccess' => $selected ) );
        $baseUrl = $runner->baseUrl();

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'targets', $targets );
        $tpl->setVariable( 'selected_siteaccess', $selected );
        $tpl->setVariable( 'base_url', $baseUrl === false ? '' : $baseUrl );
        $tpl->setVariable( 'start_urls', $baseUrl === false ? array() : $runner->startUrls( $baseUrl ) );
        $tpl->setVariable( 'stream_url', 'setup/preloadstream' );
        $tpl->setVariable( 'default_max_pages', 250 );
        $tpl->setVariable( 'default_max_depth', 3 );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:setup/preload.tpl' );
        $Result['path'] = array(
            array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/setup', 'Preload' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
