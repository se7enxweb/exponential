<?php
/**
 * exphelp/contents (doc/eng-us/features/6.0/help-system.md): every page of the help as one tree, its folders closed (native
 * <details>: title and page count, they open without JavaScript), with a filter box and the Expand all / Collapse all
 * control (exphelp.js). Each folder has an id (folderIds()), so #exphelp-folder-help links to it and opens it.
 * Template design:exphelp/contents.tpl.
 *
 * Template variables: help_nav_html (expHelpNavigation, every folder closed: safe HTML, not washed), help_nav_count
 * (the number of pages), help_i18n_json.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Contents extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Module = $scope['Params']['Module'];
        if ( !\expHelp::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $navigation = new \expHelpNavigation( \expHelpDocumentIndex::instance() );
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'help_i18n_json', \expHelp::clientStringsJson() );
        $tpl->setVariable( 'help_nav_html', self::folderIds( $navigation->html() ) );
        $tpl->setVariable( 'help_nav_count', $navigation->count() );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:exphelp/contents.tpl' );
        $Result['path'] = array( array( 'url' => 'exphelp/dashboard', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/exphelp', 'All help pages' ) ) );
        return $Result;
    }

    /**
     * Gives every folder of the tree an id from its path (help/editors, under doc/ -> exphelp-folder-help-editors), so an
     * address with that #anchor opens it; a second folder that would get the same id gets -2, -3 ...
     *
     * @param string $html expHelpNavigation::html()
     * @return string
     */
    public static function folderIds( $html )
    {
        $seen = array();
        return (string)preg_replace_callback( '/<details class="a4-help-nav-details" data-nav-path="([^"]*)">/', function ( $m ) use ( &$seen )
        {
            $slug = trim( (string)preg_replace( '/[^a-z0-9_-]+/', '-', strtolower( html_entity_decode( $m[1], ENT_QUOTES, 'UTF-8' ) ) ), '-' );
            $id = 'exphelp-folder-' . ( $slug === '' ? 'root' : $slug );
            $seen[$id] = isset( $seen[$id] ) ? $seen[$id] + 1 : 1;
            if ( $seen[$id] > 1 )
                $id .= '-' . $seen[$id];
            return '<details class="a4-help-nav-details" id="' . $id . '" data-nav-path="' . $m[1] . '">';
        }, (string)$html );
    }
}

}
