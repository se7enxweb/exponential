<?php
/**
 * exphelp/navigation/<folder> (doc/eng-us/features/6.0/help-system.md, "Viewer"): one folder of the help tree.
 *
 * The viewer's tree (expHelpNavigation::pathHtml()) opens only the way to its page; every other folder is a closed
 * <details> whose list exphelp.js loads from here when the reader opens it:
 *   (format)/fragment  the folder's list items alone (its overview link, its folders closed, its pages), text/html
 *   (format)/tree      the whole tree, every folder closed (the tree's filter box, which has to see every page)
 * Without (format) it is a page of the administration that lists the folder (design:exphelp/navigation.tpl), the
 * link a closed folder carries for a browser without JavaScript. An unknown folder is a 404; the policy is the
 * viewer's (exphelp/read).
 *
 * Template variables: help_folder (path, title, count, index_id), help_nav_html (safe HTML, not washed), help_i18n_json.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Navigation extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !\expHelp::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $path = \expHelp::requestedId( $Params );
        $viewParameters = isset( $Params['UserParameters'] ) && is_array( $Params['UserParameters'] ) ? $Params['UserParameters'] : array();
        $format = isset( $viewParameters['format'] ) && is_string( $viewParameters['format'] ) ? $viewParameters['format'] : '';
        $navigation = new \expHelpNavigation( \expHelpDocumentIndex::instance() );

        if ( $format === 'fragment' || $format === 'tree' )
        {
            $html = self::fragment( $navigation, $path, $format );
            if ( $html === null )
                return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
            header( 'Content-Type: text/html; charset=utf-8' );
            // the tree changes with the index; a reader's browser may keep it for a minute
            header( 'Cache-Control: private, max-age=60' );
            header( 'X-Content-Type-Options: nosniff' );
            echo $html;
            \eZExecution::cleanExit();
        }

        $folder = $navigation->folderInfo( $path );
        $html = $folder === null ? null : $navigation->folderHtml( $path );
        if ( $folder === null || $html === null )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'help_i18n_json', \expHelp::clientStringsJson() );
        $tpl->setVariable( 'help_folder', $folder );
        $tpl->setVariable( 'help_nav_html', '<ul class="a4-help-nav-list is-top" role="list">' . $html . '</ul>' );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:exphelp/navigation.tpl' );
        $Result['path'] = self::breadcrumb( $navigation, $path );
        return $Result;
    }

    /**
     * The HTML a (format) asks for: 'fragment' the list items of one folder, 'tree' the whole tree (every folder
     * closed). Null for an unknown folder or format.
     *
     * @param \expHelpNavigation $navigation
     * @param string $path
     * @param string $format
     * @return string|null
     */
    public static function fragment( $navigation, $path, $format )
    {
        if ( $format === 'tree' )
            return $navigation->html();
        if ( $format === 'fragment' )
            return $navigation->folderHtml( trim( (string)$path, '/' ) );
        return null;
    }

    /**
     * Help > All help pages > each folder above > the folder.
     *
     * @param \expHelpNavigation $navigation
     * @param string $path
     * @return array the $Result['path'] entries
     */
    public static function breadcrumb( $navigation, $path )
    {
        $crumbs = array( array( 'url' => 'exphelp/dashboard', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help' ) ),
                         array( 'url' => 'exphelp/contents', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'All help pages' ) ) );
        $path = trim( (string)$path, '/' );
        if ( $path === '' )
            return $crumbs;
        $segments = explode( '/', $path );
        $dir = '';
        $last = count( $segments ) - 1;
        foreach ( $segments as $i => $segment )
        {
            $dir = $dir === '' ? $segment : $dir . '/' . $segment;
            $info = $navigation->folderInfo( $dir );
            $crumbs[] = array( 'url' => $i === $last ? false : 'exphelp/navigation/' . implode( '/', array_map( 'rawurlencode', explode( '/', $dir ) ) ),
                               'text' => $info !== null && $info['title'] !== '' ? $info['title'] : ucfirst( str_replace( array( '-', '_' ), ' ', $segment ) ) );
        }
        return $crumbs;
    }
}

}
