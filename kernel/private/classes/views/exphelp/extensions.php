<?php
/**
 * exphelp/extensions (doc/eng-us/features/6.0/help-system.md, "Documentation of the extensions"): the documentation of
 * the active extensions in the help.
 *
 *   exphelp/extensions          every extension that has pages: its name, the title of its overview, the number of
 *                               pages and a link to its own index
 *   exphelp/extensions/<name>   one extension's index: its pages by folder (opened in the help's viewer) and a search
 *                               limited to them (exphelp/search/(extension)/<name>)
 *
 * The pages are those of expHelpDocumentIndex (ext/<name>/...), so only what the allowlist shows: tracked files of an
 * active extension's doc/ directory and its README, CHANGELOG, LICENSE ... An unknown extension, or one without pages,
 * is a 404. Template design:exphelp/extensions.tpl.
 *
 * Template variables: help_extensions (expHelpDocumentIndex::extensions(), name => name, title, count, index_id,
 * pages), help_extension (the one extension shown, or false), help_folders (for one extension: list of array( 'name'
 * (folder below the extension, '' at its top), 'pages' => list of array( 'id', 'title', 'summary' ) )), help_i18n_json.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Extensions extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !\expHelp::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $extensions = \expHelpDocumentIndex::instance()->extensions();
        $name = isset( $Params['Extension'] ) && is_string( $Params['Extension'] ) ? $Params['Extension'] : '';
        $extension = false;
        if ( $name !== '' )
        {
            $extension = self::find( $extensions, $name );
            if ( $extension === null )
                return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'help_i18n_json', \expHelp::clientStringsJson() );
        $tpl->setVariable( 'help_extensions', $extensions );
        $tpl->setVariable( 'help_extension', $extension === false ? false : $extension );
        $tpl->setVariable( 'help_folders', $extension === false ? array() : self::folders( $extension ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:exphelp/extensions.tpl' );
        $Result['path'] = array( array( 'url' => 'exphelp/dashboard', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help' ) ) );
        if ( $extension === false )
            $Result['path'][] = array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Extensions' ) );
        else
        {
            $Result['path'][] = array( 'url' => 'exphelp/extensions', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Extensions' ) );
            $Result['path'][] = array( 'url' => false, 'text' => $extension['name'] );
        }
        return $Result;
    }

    /**
     * The extension of that name (compared without case), or null.
     *
     * @param array $extensions expHelpDocumentIndex::extensions()
     * @param string $name
     * @return array|null
     */
    public static function find( array $extensions, $name )
    {
        if ( !preg_match( '/^[A-Za-z0-9_][A-Za-z0-9_.-]{0,63}$/', (string)$name ) )
            return null;
        foreach ( $extensions as $extension )
            if ( strcasecmp( $extension['name'], $name ) === 0 )
                return $extension;
        return null;
    }

    /**
     * One extension's pages by folder, in the order of the navigation: list of array( 'name', 'pages' ).
     *
     * @param array $extension one entry of expHelpDocumentIndex::extensions()
     * @return array
     */
    public static function folders( array $extension )
    {
        $folders = array();
        foreach ( $extension['pages'] as $page )
        {
            $folder = (string)$page['folder'];
            if ( !isset( $folders[$folder] ) )
                $folders[$folder] = array( 'name' => $folder, 'pages' => array() );
            $folders[$folder]['pages'][] = array( 'id' => $page['id'], 'title' => $page['title'], 'summary' => $page['summary'] );
        }
        return array_values( $folders );
    }
}

}
