<?php
/**
 * File containing the expClassicMenuSiteAccessInspector class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Tells for a siteaccess whether its pages use the classic menu settings (menu.ini [SelectedMenu]).
 *
 * Only a page layout reads them: the {menu} template function (design/base), $pagedata.top_menu and left_menu of the
 * ezpagedata operator (ezwebin, ezdemo) or the page_topmenu.tpl and page_leftmenu.tpl includes. The inspector finds the
 * pagelayout.tpl the siteaccess's designs resolve to, reads it and the design templates it includes directly, with
 * template comments removed, and classifies the siteaccess:
 *
 *  - classic: the page layout reads the settings, so they decide its menus;
 *  - mixed:   it reads them and also renders Exponential Layouts;
 *  - layouts: it renders Exponential Layouts (layouts, zones and blocks resolved per page) and does not read them;
 *  - admin:   an administration design; its menus come from menu.ini [TopAdminMenu] and the Leftmenu_* sections;
 *  - other:   its page layout does not read them;
 *  - unknown: no page layout was found.
 *
 * classify() is pure and does the deciding; inspect() only gathers the files.
 * Guide: doc/guides/classic-menu-settings.md
 */
class expClassicMenuSiteAccessInspector
{
    /** At most this many templates included by the page layout are read */
    const MAX_INCLUDES = 25;

    /** What in a page layout means it reads [SelectedMenu] */
    const CLASSIC_PATTERN = '/\{\s*menu\s+name\s*=|pagedata(?:\(\s*\))?\.(?:top_menu|left_menu|current_menu)\b|SelectedMenu/';

    /** What in a page layout means it renders Exponential Layouts */
    const LAYOUTS_PATTERN = '/fetch\(\s*[\'"]?explayouts[\'"]?\s*,|design:explayouts\//';

    /**
     * The statuses in the order the page lists them: where the settings matter first.
     *
     * @return string[]
     */
    public static function statuses()
    {
        return array( 'classic', 'mixed', 'other', 'unknown', 'layouts', 'admin' );
    }

    /**
     * Whether the settings take effect in a siteaccess with this status.
     *
     * @param string $status
     * @return bool
     */
    public static function isRelevant( $status )
    {
        return $status === 'classic' || $status === 'mixed';
    }

    /**
     * The design list of a siteaccess, in the order templates are looked up: SiteDesign, AdditionalSiteDesignList,
     * StandardDesign, each once.
     *
     * @param string $siteDesign
     * @param array $additional
     * @param string $standardDesign
     * @return string[]
     */
    public static function designList( $siteDesign, array $additional, $standardDesign )
    {
        $list = array();
        foreach ( array_merge( array( $siteDesign ), $additional, array( $standardDesign ) ) as $design )
        {
            if ( is_string( $design ) && $design !== '' && !in_array( $design, $list, true ) )
                $list[] = $design;
        }
        return $list;
    }

    /**
     * Whether a design list is an administration design: one of its designs is admin or starts with admin
     * (admin2, admin3, admin4, admin4l, adminui). The editor siteaccess falls back to them too.
     *
     * @param array $designs
     * @return bool
     */
    public static function isAdminDesign( array $designs )
    {
        foreach ( $designs as $design )
        {
            if ( is_string( $design ) && preg_match( '/^admin/', $design ) )
                return true;
        }
        return false;
    }

    /**
     * A template's text without its {* ... *} comments, so a menu that is commented out does not count.
     *
     * @param string $source
     * @return string
     */
    public static function stripComments( $source )
    {
        return (string)preg_replace( '/\{\*.*?\*\}/s', '', (string)$source );
    }

    /**
     * The design templates a template includes by a fixed name ({include uri='design:page_topmenu.tpl'}).
     *
     * @param string $source with the comments removed
     * @return string[] paths relative to templates/, each once
     */
    public static function includedTemplates( $source )
    {
        $found = array();
        if ( preg_match_all( '/\{\s*include\s+uri\s*=\s*([\'"])design:([A-Za-z0-9_\/.-]+\.tpl)\1/', (string)$source, $matches ) )
        {
            foreach ( $matches[2] as $path )
            {
                if ( strpos( $path, '..' ) === false && !in_array( $path, $found, true ) )
                    $found[] = $path;
            }
        }
        return $found;
    }

    /**
     * Decides the status of a siteaccess.
     *
     * @param array $designs from designList()
     * @param string|false $pagelayout the page layout's path, false when none was found
     * @param array $sources path => template text: the page layout and the templates it includes
     * @return array array( 'status' => string, 'relevant' => bool, 'reads_menus' => bool, 'uses_layouts' => bool,
     *                      'admin' => bool, 'pagelayout' => string, 'found_in' => string[] )
     */
    public static function classify( array $designs, $pagelayout, array $sources )
    {
        $readsMenus = false;
        $usesLayouts = false;
        $foundIn = array();
        foreach ( $sources as $path => $source )
        {
            $text = self::stripComments( $source );
            if ( preg_match( self::CLASSIC_PATTERN, $text ) )
            {
                $readsMenus = true;
                $foundIn[] = (string)$path;
            }
            if ( preg_match( self::LAYOUTS_PATTERN, $text ) )
                $usesLayouts = true;
        }
        $admin = self::isAdminDesign( $designs );

        if ( $admin )
            $status = 'admin';
        elseif ( $pagelayout === false || $pagelayout === '' )
            $status = 'unknown';
        elseif ( $readsMenus && $usesLayouts )
            $status = 'mixed';
        elseif ( $usesLayouts )
            $status = 'layouts';
        elseif ( $readsMenus )
            $status = 'classic';
        else
            $status = 'other';

        return array(
            'status' => $status,
            'relevant' => self::isRelevant( $status ),
            'reads_menus' => $readsMenus,
            'uses_layouts' => $usesLayouts,
            'admin' => $admin,
            'pagelayout' => $pagelayout === false ? '' : (string)$pagelayout,
            'found_in' => $foundIn,
        );
    }

    /**
     * Inspects one siteaccess: its designs, the page layout they resolve to and what that reads.
     *
     * @param string $siteAccess
     * @return array classify()'s result plus 'siteaccess', 'designs' and 'site_design'
     */
    public static function inspect( $siteAccess )
    {
        $siteIni = eZSiteAccess::getIni( $siteAccess, 'site.ini' );
        $designs = self::designList(
            (string)$siteIni->variable( 'DesignSettings', 'SiteDesign' ),
            (array)$siteIni->variable( 'DesignSettings', 'AdditionalSiteDesignList' ),
            (string)$siteIni->variable( 'DesignSettings', 'StandardDesign' )
        );

        $bases = eZTemplateDesignResource::allDesignBases( $siteAccess );
        $tried = array();
        $match = eZTemplateDesignResource::fileMatch( $bases, 'templates', 'pagelayout.tpl', $tried );
        $pagelayout = $match ? $match['path'] : false;

        $sources = array();
        if ( $pagelayout !== false )
        {
            $text = @file_get_contents( $pagelayout );
            if ( $text !== false )
            {
                $sources[$pagelayout] = $text;
                foreach ( array_slice( self::includedTemplates( self::stripComments( $text ) ), 0, self::MAX_INCLUDES ) as $include )
                {
                    $tried = array();
                    $found = eZTemplateDesignResource::fileMatch( $bases, 'templates', $include, $tried );
                    if ( $found && !isset( $sources[$found['path']] ) )
                    {
                        $included = @file_get_contents( $found['path'] );
                        if ( $included !== false )
                            $sources[$found['path']] = $included;
                    }
                }
            }
        }

        $result = self::classify( $designs, $pagelayout, $sources );
        $result['siteaccess'] = $siteAccess;
        $result['designs'] = $designs;
        $result['site_design'] = $designs ? $designs[0] : '';
        return $result;
    }

    /**
     * What one design directory brings for the classic menus.
     *
     *  - draws:          its pagelayout.tpl (or a template it includes from the same directory) reads the settings;
     *  - templates_only: it has menu templates that read menu.ini, but no page layout of its own that uses them;
     *                    a site gets those menus only from a page layout that includes them;
     *  - none:           neither.
     *
     * @param string|false $pagelayout the design's own templates/pagelayout.tpl text, false when it has none
     * @param array $included name => text of the templates that page layout includes from the same directory
     * @param array $menuTemplates name => text of its templates/menu/*.tpl
     * @return array array( 'status' => string, 'menu_templates' => string[] (those that read menu.ini), 'layouts' => bool )
     */
    public static function classifyDesign( $pagelayout, array $included, array $menuTemplates )
    {
        $readers = array();
        foreach ( $menuTemplates as $name => $text )
        {
            if ( preg_match( '/MenuContentSettings|SelectedMenu|\{\s*menu\s+name/', self::stripComments( $text ) ) )
                $readers[] = (string)$name;
        }
        sort( $readers );
        $draws = false;
        $layouts = false;
        if ( $pagelayout !== false )
        {
            $c = self::classify( array(), 'pagelayout.tpl', array_merge( array( 'pagelayout.tpl' => $pagelayout ), $included ) );
            $draws = $c['reads_menus'];
            $layouts = $c['uses_layouts'];
        }
        return array( 'status' => $draws ? 'draws' : ( $readers ? 'templates_only' : 'none' ), 'menu_templates' => $readers, 'layouts' => $layouts );
    }

    /**
     * Every design directory of the installation that has a page layout or menu templates (design/* and the
     * designs of the extensions named in design.ini [ExtensionSettings] DesignExtensions), with classifyDesign().
     *
     * @return array list of array( 'design', 'path', 'status', 'menu_templates', 'layouts' ), by path
     */
    public static function designSurvey()
    {
        $dirs = array();
        foreach ( (array)glob( 'design/*', GLOB_ONLYDIR ) as $dir )
            $dirs[] = $dir;
        foreach ( eZTemplateDesignResource::designExtensions() as $extension )
        {
            $path = eZExtension::extensionPath( eZExtension::extensionName( $extension ) );
            if ( $path === false )
                continue;
            foreach ( (array)glob( $path . '/design/*', GLOB_ONLYDIR ) as $dir )
                $dirs[] = $dir;
        }
        $survey = array();
        foreach ( array_unique( $dirs ) as $dir )
        {
            $pagelayoutFile = $dir . '/templates/pagelayout.tpl';
            $pagelayout = is_file( $pagelayoutFile ) ? (string)@file_get_contents( $pagelayoutFile ) : false;
            $menus = array();
            foreach ( (array)glob( $dir . '/templates/menu/*.tpl' ) as $file )
                $menus[basename( $file, '.tpl' )] = (string)@file_get_contents( $file );
            if ( $pagelayout === false && !$menus )
                continue;
            $included = array();
            if ( $pagelayout !== false )
            {
                foreach ( array_slice( self::includedTemplates( self::stripComments( $pagelayout ) ), 0, self::MAX_INCLUDES ) as $include )
                {
                    if ( is_file( $dir . '/templates/' . $include ) )
                        $included[$include] = (string)@file_get_contents( $dir . '/templates/' . $include );
                }
            }
            $result = self::classifyDesign( $pagelayout, $included, $menus );
            if ( $result['status'] === 'none' && !self::isAdminDesign( array( basename( $dir ) ) ) && $pagelayout === false )
                continue;
            if ( self::isAdminDesign( array( basename( $dir ) ) ) )
                continue;
            $result['design'] = basename( $dir );
            $result['path'] = $dir;
            $survey[] = $result;
        }
        usort( $survey, function ( $a, $b ) { return strcmp( $a['path'], $b['path'] ); } );
        return $survey;
    }

    /**
     * The file a design template resolves to in a siteaccess's designs (templates/<path>), relative to the
     * installation root, or '' when none of its designs has it.
     *
     * @param string $siteAccess
     * @param string $path e.g. menu/flat_top.tpl
     * @return string
     */
    public static function templateFile( $siteAccess, $path )
    {
        if ( !is_string( $path ) || $path === '' || strpos( $path, '..' ) !== false )
            return '';
        $tried = array();
        $found = eZTemplateDesignResource::fileMatch( eZTemplateDesignResource::allDesignBases( $siteAccess ), 'templates', $path, $tried );
        return $found ? (string)$found['path'] : '';
    }

    /**
     * Inspects every siteaccess of a list and orders them: where the settings matter first, then by status in the
     * order of statuses(), each group in the order given.
     *
     * @param array $siteAccesses
     * @return array siteaccess => inspect() result
     */
    public static function inspectAll( array $siteAccesses )
    {
        $results = array();
        foreach ( $siteAccesses as $siteAccess )
            $results[$siteAccess] = self::inspect( $siteAccess );
        return self::sortResults( $results );
    }

    /**
     * Orders inspect() results by status (statuses()), keeping the given order within a status.
     *
     * @param array $results siteaccess => result with a 'status'
     * @return array
     */
    public static function sortResults( array $results )
    {
        $rank = array_flip( self::statuses() );
        $position = 0;
        $keyed = array();
        foreach ( $results as $siteAccess => $result )
        {
            $status = isset( $result['status'], $rank[$result['status']] ) ? $rank[$result['status']] : count( $rank );
            $keyed[] = array( $status, $position++, $siteAccess, $result );
        }
        usort( $keyed, function ( $a, $b ) { return $a[0] === $b[0] ? $a[1] - $b[1] : $a[0] - $b[0]; } );
        $sorted = array();
        foreach ( $keyed as $item )
            $sorted[$item[2]] = $item[3];
        return $sorted;
    }
}
?>
