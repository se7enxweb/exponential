<?php
/**
 * File containing the expClassicMenuSettings class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads and writes the classic menu settings of one siteaccess: menu.ini [SelectedMenu] CurrentMenu, TopMenu and
 * LeftMenu, which the page layouts of the classic designs (base, ezwebin, ezdemo, simple, ezflow) turn into a top
 * and a left menu. The page Setup > Menu management (visual/menuconfig) is its user.
 *
 * Writing is narrow on purpose: only those three settings, only in settings/siteaccess/<siteaccess>/menu.ini.append.php
 * of a siteaccess the caller names as known, with the rest of that file left as it was (comments, other sections and
 * the PHP wrapper of a .ini.append.php file). Until 6.0.15 the page saved the whole merged menu.ini into that file,
 * which froze every other menu setting (the admin's tabs among them) for the siteaccess.
 *
 * Guide: doc/guides/classic-menu-settings.md
 */
class expClassicMenuSettings
{
    /** The file written, inside the siteaccess directory */
    const FILE_NAME = 'menu.ini.append.php';

    /** The settings directory of the siteaccesses, relative to the installation root */
    const SITEACCESS_ROOT = 'settings/siteaccess';

    /** The three settings this page writes, in [SelectedMenu] */
    const SECTION = 'SelectedMenu';

    /** Menu templates whose effect the page describes in words; any other name is a design's own template */
    protected static $knownTemplates = array( 'flat_top', 'double_top', 'flat_left', 'sub_left' );

    /**
     * The menu choices of menu.ini [MenuSettings] AvailableMenuArray, each with what it puts at the top and on the left.
     *
     * @param array $availableMenuArray the menu types, in their order
     * @param array $groups menu type => its menu.ini group (TitleText, MenuThumbnail, TopMenu, LeftMenu)
     * @return array list of array( type, title, thumbnail, top, left, top_kind, left_kind, has_top, has_second_row, has_left )
     */
    public static function choices( array $availableMenuArray, array $groups )
    {
        $choices = array();
        $seen = array();
        foreach ( $availableMenuArray as $type )
        {
            if ( !is_string( $type ) || $type === '' || isset( $seen[$type] ) || !isset( $groups[$type] ) || !is_array( $groups[$type] ) )
                continue;
            $seen[$type] = true;
            $group = $groups[$type];
            $top = isset( $group['TopMenu'] ) && is_string( $group['TopMenu'] ) ? trim( $group['TopMenu'] ) : '';
            $left = isset( $group['LeftMenu'] ) && is_string( $group['LeftMenu'] ) ? trim( $group['LeftMenu'] ) : '';
            $choices[] = array(
                'type' => $type,
                'title' => isset( $group['TitleText'] ) && is_string( $group['TitleText'] ) && $group['TitleText'] !== '' ? $group['TitleText'] : $type,
                'thumbnail' => isset( $group['MenuThumbnail'] ) && is_string( $group['MenuThumbnail'] ) ? $group['MenuThumbnail'] : '',
                'top' => $top,
                'left' => $left,
                'top_kind' => self::templateKind( $top ),
                'left_kind' => self::templateKind( $left ),
                'has_top' => $top !== '',
                'has_second_row' => $top === 'double_top',
                'has_left' => $left !== '',
            );
        }
        return $choices;
    }

    /**
     * What a menu template is, for its description: one of the known template names, 'none' for no menu, or
     * 'custom' for a template the design brings itself.
     *
     * @param string $template
     * @return string
     */
    public static function templateKind( $template )
    {
        $template = is_string( $template ) ? trim( $template ) : '';
        if ( $template === '' )
            return 'none';
        return in_array( $template, self::$knownTemplates, true ) ? $template : 'custom';
    }

    /**
     * The choice with this type, or false.
     *
     * @param string $menuType
     * @param array $choices from choices()
     * @return array|false
     */
    public static function findChoice( $menuType, array $choices )
    {
        if ( !is_string( $menuType ) )
            return false;
        foreach ( $choices as $choice )
        {
            if ( $choice['type'] === $menuType )
                return $choice;
        }
        return false;
    }

    /**
     * The three settings saving this menu type writes, or false for a type that is not one of the choices: a posted
     * value is never written as it is.
     *
     * @param string $menuType
     * @param array $choices from choices()
     * @return array|false array( 'CurrentMenu' => ..., 'TopMenu' => ..., 'LeftMenu' => ... )
     */
    public static function selection( $menuType, array $choices )
    {
        $choice = self::findChoice( $menuType, $choices );
        if ( $choice === false )
            return false;
        return array( 'CurrentMenu' => $choice['type'], 'TopMenu' => $choice['top'], 'LeftMenu' => $choice['left'] );
    }

    /**
     * The lines saving writes into the [SelectedMenu] section, as they will stand in the file.
     *
     * @param array $selection from selection()
     * @return string[]
     */
    public static function plannedLines( array $selection )
    {
        $lines = array( '[' . self::SECTION . ']' );
        foreach ( array( 'CurrentMenu', 'TopMenu', 'LeftMenu' ) as $name )
            $lines[] = $name . '=' . ( isset( $selection[$name] ) ? $selection[$name] : '' );
        return $lines;
    }

    /**
     * What the menus of a choice list, as the menu templates of the classic designs fetch them (design/base and
     * ezwebin templates/menu/*.tpl; the simple design's copies do the same):
     *
     *  - flat_top:   the children of the start page, classes of TopIdentifierList, one level;
     *  - double_top: the same row, and under it the children of the first-level page the visitor is in, also with
     *                TopIdentifierList;
     *  - flat_left:  with CurrentMenu=LeftOnly the children of the start page, otherwise those of the first-level page
     *                the visitor is in, classes of LeftIdentifierList, opened further below the page the visitor is in
     *                (ezwebin: one more level; design/base: a tree along the path, up to five levels);
     *  - sub_left:   the children of the first-level page the visitor is in, classes of LeftIdentifierList (base).
     *
     * All of them sort as the parent's children are sorted and leave hidden pages out (site.ini [SiteAccessSettings]
     * ShowHiddenNodes=false, the default). A template not listed here is the design's own: nothing is claimed.
     *
     * @param array $choice from choices()
     * @return array list of array( 'position' => top|second|left, 'template' => name, 'parent' => root|section,
     *               'classes' => TopIdentifierList|LeftIdentifierList, 'deeper' => bool )
     */
    public static function menuSources( array $choice )
    {
        $sources = array();
        switch ( $choice['top_kind'] )
        {
            case 'flat_top':
                $sources[] = array( 'position' => 'top', 'template' => 'flat_top', 'parent' => 'root', 'classes' => 'TopIdentifierList', 'deeper' => false );
                break;
            case 'double_top':
                $sources[] = array( 'position' => 'top', 'template' => 'double_top', 'parent' => 'root', 'classes' => 'TopIdentifierList', 'deeper' => false );
                $sources[] = array( 'position' => 'second', 'template' => 'double_top', 'parent' => 'section', 'classes' => 'TopIdentifierList', 'deeper' => false );
                break;
        }
        switch ( $choice['left_kind'] )
        {
            case 'flat_left':
                $sources[] = array( 'position' => 'left', 'template' => 'flat_left', 'parent' => $choice['type'] === 'LeftOnly' ? 'root' : 'section',
                                    'classes' => 'LeftIdentifierList', 'deeper' => true );
                break;
            case 'sub_left':
                $sources[] = array( 'position' => 'left', 'template' => 'sub_left', 'parent' => 'section', 'classes' => 'LeftIdentifierList', 'deeper' => false );
                break;
        }
        return $sources;
    }

    /**
     * The worked examples of the page and the guide, each with the file it goes in and its exact lines. A recipe that
     * picks an arrangement uses selection() and plannedLines(), so it is what saving that arrangement writes.
     *
     * @param array $choices from choices()
     * @param string $siteAccess the siteaccess the page shows
     * @param string $firstSiteAccess the first siteaccess of the per-siteaccess recipe
     * @param string $secondSiteAccess the second one
     * @return array id => array( 'files' => array( file => lines ) )
     */
    public static function recipes( array $choices, $siteAccess, $firstSiteAccess, $secondSiteAccess )
    {
        $file = self::SITEACCESS_ROOT . '/' . $siteAccess . '/' . self::FILE_NAME;
        $recipes = array();
        foreach ( array( 'top_only' => 'TopOnly', 'top_and_left' => 'LeftTop' ) as $id => $type )
        {
            $selection = self::selection( $type, $choices );
            if ( $selection !== false )
                $recipes[$id] = array( 'files' => array( $file => self::plannedLines( $selection ) ) );
        }
        $recipes['limit_classes'] = array( 'files' => array( $file => array(
            '[MenuContentSettings]',
            'TopIdentifierList[]',
            'TopIdentifierList[]=folder',
            'TopIdentifierList[]=frontpage',
            'LeftIdentifierList[]',
            'LeftIdentifierList[]=folder',
            'LeftIdentifierList[]=frontpage',
        ) ) );
        $recipes['hide_node'] = array( 'files' => array( self::SITEACCESS_ROOT . '/' . $siteAccess . '/site.ini.append.php' => array(
            '[SiteAccessSettings]',
            'ShowHiddenNodes=false',
        ) ) );
        $first = self::selection( 'TopOnly', $choices );
        $second = self::selection( 'LeftTop', $choices );
        if ( $first !== false && $second !== false && $firstSiteAccess !== '' && $secondSiteAccess !== '' && $firstSiteAccess !== $secondSiteAccess )
        {
            $recipes['per_siteaccess'] = array( 'files' => array(
                self::SITEACCESS_ROOT . '/' . $firstSiteAccess . '/' . self::FILE_NAME => self::plannedLines( $first ),
                self::SITEACCESS_ROOT . '/' . $secondSiteAccess . '/' . self::FILE_NAME => self::plannedLines( $second ),
            ) );
        }
        return $recipes;
    }

    /**
     * The template examples of the page and the guide ("Use in your templates"), from kernel/classes/classicmenu/
     * examples/: one file each, so the page, the guide (doc/guides/classic-menu-settings.md, checked by a test to hold
     * the same text) and the rendering check use the same snippets.
     *
     * @return array id => array( 'file' => file name, 'text' => content ), in the order they are shown
     */
    public static function templateExamples()
    {
        $examples = array();
        foreach ( array( 'pagelayout_menus' => 'pagelayout_menus.tpl', 'menu_my_top' => 'menu_my_top.tpl',
                         'menu_my_section_left' => 'menu_my_section_left.tpl', 'design_extension' => 'design_extension.txt' ) as $id => $file )
        {
            $text = @file_get_contents( __DIR__ . '/examples/' . $file );
            if ( $text !== false )
                $examples[$id] = array( 'file' => $file, 'text' => rtrim( $text, "\n" ) );
        }
        return $examples;
    }

    /**
     * Whether a siteaccess name has the form of one: letters, digits, underscore and hyphen, 1 to 64 characters.
     *
     * @param mixed $name
     * @return bool
     */
    public static function isSiteAccessName( $name )
    {
        return is_string( $name ) && preg_match( '/^[A-Za-z0-9_-]{1,64}$/D', $name ) === 1;
    }

    /**
     * The settings directory of a known siteaccess, relative to $root (settings/siteaccess/<name>), or false.
     *
     * The name must be one of $known and have the form of a siteaccess name, and the directory must exist and, with
     * every link resolved, lie directly inside <root>/settings/siteaccess. Nothing is created.
     *
     * @param mixed $siteAccess
     * @param array $known the siteaccesses the page offers
     * @param string|null $root the installation root, the current directory by default
     * @return string|false
     */
    public static function siteAccessDirectory( $siteAccess, array $known, $root = null )
    {
        if ( !self::isSiteAccessName( $siteAccess ) || !in_array( $siteAccess, $known, true ) )
            return false;
        $root = rtrim( $root === null ? getcwd() : $root, '/' );
        $base = realpath( $root . '/' . self::SITEACCESS_ROOT );
        $directory = realpath( $root . '/' . self::SITEACCESS_ROOT . '/' . $siteAccess );
        if ( $base === false || $directory === false || !is_dir( $directory ) )
            return false;
        if ( dirname( $directory ) !== $base || basename( $directory ) !== $siteAccess )
            return false;
        return self::SITEACCESS_ROOT . '/' . $siteAccess;
    }

    /**
     * Where a setting's value comes from, as a path relative to the installation root, and the kind of place:
     * default (settings/), extension, siteaccess, extension siteaccess, override or injected.
     *
     * @param mixed $placement a placement of eZINI::groupPlacements() (a path, or a list of paths for an array)
     * @param string|null $root the installation root
     * @return array array( 'file' => string, 'kind' => string )
     */
    public static function describePlacement( $placement, $root = null )
    {
        if ( is_array( $placement ) )
            $placement = $placement ? end( $placement ) : '';
        if ( !is_string( $placement ) || $placement === '' )
            return array( 'file' => '', 'kind' => 'unknown' );
        if ( $placement === 'injected' || ( defined( 'eZINI::INJECTED_PATH' ) && $placement === eZINI::INJECTED_PATH ) )
            return array( 'file' => '', 'kind' => 'injected' );
        $root = rtrim( str_replace( '\\', '/', $root === null ? getcwd() : $root ), '/' ) . '/';
        $file = str_replace( '\\', '/', $placement );
        if ( strpos( $file, $root ) === 0 )
            $file = substr( $file, strlen( $root ) );
        $file = preg_replace( '#^\./#', '', $file );
        $file = preg_replace( '#/+#', '/', $file );

        if ( preg_match( '#^settings/override/#', $file ) )
            $kind = 'override';
        elseif ( preg_match( '#^settings/siteaccess/#', $file ) )
            $kind = 'siteaccess';
        elseif ( preg_match( '#^extension/[^/]+/settings/siteaccess/#', $file ) )
            $kind = 'extension_siteaccess';
        elseif ( preg_match( '#^extension/[^/]+/settings/#', $file ) )
            $kind = 'extension';
        elseif ( preg_match( '#^settings/[^/]+$#', $file ) )
            $kind = 'default';
        else
            $kind = 'other';
        return array( 'file' => $file, 'kind' => $kind );
    }

    /**
     * The current classic menu settings of a siteaccess as that siteaccess reads them, each with the file it comes
     * from, the classes its menus list, and whether a file read after the siteaccess's own (settings/override)
     * decides the value, so that saving would change nothing.
     *
     * @param string $siteAccess
     * @param string|null $root
     * @return array
     */
    public static function read( $siteAccess, $root = null )
    {
        $ini = eZSiteAccess::getIni( $siteAccess, 'menu.ini' );
        // groupPlacements() reloads the instance it is called on without its values; for the current siteaccess
        // getIni() returns the shared instance, so the placements are read from a copy
        $copy = clone $ini;
        $placements = $copy->groupPlacements();
        unset( $copy );
        return self::describeCurrent( $ini->hasGroup( self::SECTION ) ? $ini->group( self::SECTION ) : array(),
                                      isset( $placements[self::SECTION] ) ? $placements[self::SECTION] : array(),
                                      array(
                                          'TopIdentifierList' => $ini->hasVariable( 'MenuContentSettings', 'TopIdentifierList' ) ? $ini->variable( 'MenuContentSettings', 'TopIdentifierList' ) : array(),
                                          'LeftIdentifierList' => $ini->hasVariable( 'MenuContentSettings', 'LeftIdentifierList' ) ? $ini->variable( 'MenuContentSettings', 'LeftIdentifierList' ) : array(),
                                      ),
                                      $root );
    }

    /**
     * read() without the INI: the values and placements of [SelectedMenu] put in the form the page shows.
     *
     * @param array $values [SelectedMenu] as read
     * @param array $placements [SelectedMenu] placements
     * @param array $contentLists TopIdentifierList and LeftIdentifierList
     * @param string|null $root
     * @return array
     */
    public static function describeCurrent( array $values, array $placements, array $contentLists, $root = null )
    {
        $settings = array();
        $overridden = false;
        foreach ( array( 'CurrentMenu', 'TopMenu', 'LeftMenu' ) as $name )
        {
            $value = isset( $values[$name] ) && is_string( $values[$name] ) ? $values[$name] : '';
            $from = self::describePlacement( isset( $placements[$name] ) ? $placements[$name] : '', $root );
            if ( $from['kind'] === 'override' || $from['kind'] === 'injected' )
                $overridden = true;
            $settings[$name] = array( 'value' => $value, 'file' => $from['file'], 'kind' => $from['kind'] );
        }
        $lists = array();
        foreach ( array( 'TopIdentifierList', 'LeftIdentifierList' ) as $name )
        {
            $list = isset( $contentLists[$name] ) && is_array( $contentLists[$name] ) ? $contentLists[$name] : array();
            $lists[$name] = array_values( array_unique( array_filter( array_map( 'strval', $list ), 'strlen' ) ) );
        }
        return array(
            'current_menu' => $settings['CurrentMenu']['value'],
            'settings' => $settings,
            'overridden' => $overridden,
            'top_classes' => $lists['TopIdentifierList'],
            'left_classes' => $lists['LeftIdentifierList'],
        );
    }


    /**
     * Writes the three [SelectedMenu] settings into settings/siteaccess/<siteaccess>/menu.ini.append.php and nothing
     * else, through expIniEditor: only the touched lines change, comments, other sections and the PHP wrapper stay, a
     * missing file is created with the wrapper, the former file is backed up in var/backup/ini, the write is atomic
     * and keeps the file's owner and mode, and it is recorded in the audit (system.setting.write).
     *
     * The siteaccess must be one of $known with an existing directory (siteAccessDirectory()). What was written is
     * read back; if it does not hold the three values, or the wrapper of a .php file is no longer intact, the former
     * content is put back and the result says so.
     *
     * @param string $siteAccess
     * @param array $selection from selection()
     * @param array $known the siteaccesses the caller offers
     * @return array array( 'ok' => bool, 'changed' => bool, 'created' => bool, 'file' => string, 'backup' => string,
     *                      'error' => string ); error: '', 'no_directory', 'not_writable', 'refused', 'write_failed',
     *                      'verify_failed'
     */
    public static function write( $siteAccess, array $selection, array $known )
    {
        $result = array( 'ok' => false, 'changed' => false, 'created' => false, 'file' => '', 'backup' => '', 'error' => '' );
        $root = expIniEditor::root();
        $directory = self::siteAccessDirectory( $siteAccess, $known, $root );
        if ( $directory === false )
        {
            $result['error'] = 'no_directory';
            return $result;
        }

        try
        {
            $editor = new expIniEditor( expIniEditor::scope( 'siteaccess:' . $siteAccess ), 'menu' );
            $result['file'] = $editor->relativePath();
            if ( strpos( $result['file'], $directory . '/' ) !== 0 )
            {
                // the scope must be the directory checked above, never an extension's or another place
                $result['error'] = 'no_directory';
                return $result;
            }
            $path = $editor->path();
            $existed = $editor->fileExists();
            if ( ( $existed && !is_writable( $path ) ) || ( !$existed && !is_writable( dirname( $path ) ) ) )
            {
                $result['error'] = 'not_writable';
                return $result;
            }
            $before = $existed ? $editor->originalContent() : false;

            foreach ( array( 'CurrentMenu', 'TopMenu', 'LeftMenu' ) as $name )
                $editor->set( self::SECTION, $name, isset( $selection[$name] ) ? (string)$selection[$name] : '' );
            if ( !$editor->hasChanges() )
            {
                $result['ok'] = true;
                return $result;
            }
            $saved = $editor->save();
        }
        catch ( expIniException $e )
        {
            $result['error'] = $e->getCode() === expIniException::REFUSED ? 'refused' : 'write_failed';
            eZDebug::writeError( 'Classic menu settings of ' . $siteAccess . ': ' . $e->getMessage(), __METHOD__ );
            return $result;
        }

        if ( !self::verify( $siteAccess, $path, $selection, $before ) )
        {
            self::restore( $path, $before );
            $result['error'] = 'verify_failed';
            return $result;
        }

        $result['ok'] = true;
        $result['changed'] = true;
        $result['created'] = !$existed;
        $backup = (string)$saved->backup();
        $result['backup'] = strpos( $backup, $root ) === 0 ? substr( $backup, strlen( $root ) ) : $backup;
        return $result;
    }

    /**
     * Whether the written file holds the three values and, for a .php file, still is one PHP comment.
     *
     * @param string $siteAccess
     * @param string $path the file written
     * @param array $selection
     * @param string|false $before the content before the write, false when there was no file
     * @return bool
     */
    public static function verify( $siteAccess, $path, array $selection, $before )
    {
        clearstatcache( true, $path );
        $after = @file_get_contents( $path );
        if ( $after === false )
            return false;
        if ( substr( $path, -4 ) === '.php' && !self::wrapperIntact( $after, $before ) )
            return false;
        $check = new expIniEditor( expIniEditor::scope( 'siteaccess:' . $siteAccess ), 'menu' );
        foreach ( array( 'CurrentMenu', 'TopMenu', 'LeftMenu' ) as $name )
        {
            $expected = isset( $selection[$name] ) ? (string)$selection[$name] : '';
            if ( $check->get( self::SECTION, $name ) !== $expected )
                return false;
        }
        return true;
    }


    /**
     * Whether a .ini.append.php text is still wrapped as it must be: the open tag and comment start first, the
     * comment end and close tag last, and no comment end in between that would let the rest run as PHP. A text that
     * had no wrapper before is not asked to have one.
     *
     * @param string $after
     * @param string|false $before
     * @return bool
     */
    public static function wrapperIntact( $after, $before = false )
    {
        $hadWrapper = $before === false || $before === '' || preg_match( '#^\s*<\?php\s*/\*#', $before ) === 1;
        if ( !$hadWrapper )
            return strpos( $after, '<?' ) === false;
        if ( preg_match( '#^\s*<\?php\s*/\*#', $after ) !== 1 )
            return false;
        if ( preg_match( '#\*/\s*\?>\s*$#', $after ) !== 1 )
            return false;
        // exactly one comment end, the last one
        return substr_count( $after, '*/' ) === 1;
    }

    /**
     * Puts a file back as it was: its former content, or no file when there was none.
     *
     * @param string $file
     * @param string|false $before
     */
    protected static function restore( $file, $before )
    {
        if ( $before === false )
        {
            if ( file_exists( $file ) )
                @unlink( $file );
            return;
        }
        @file_put_contents( $file, $before );
    }

    /**
     * Clears what holds the old menus after a save: the INI caches (every siteaccess reads its settings again on its
     * next request, under Apache and Velocity alike), the compiled page layouts of the siteaccess's cache directory and
     * the template blocks.
     *
     * @param string $directory the siteaccess's settings directory
     * @return string[] the caches cleared: ini, pagelayout, template-block
     */
    public static function clearCaches( $directory )
    {
        $cleared = array();
        eZCache::clearByID( array( 'global_ini', 'ini' ) );
        $cleared[] = 'ini';

        $compiled = self::cacheDirectory( $directory ) . '/template/compiled';
        if ( is_dir( $compiled ) )
        {
            eZDir::unlinkWildcard( $compiled . '/', '*pagelayout*.*' );
            $cleared[] = 'pagelayout';
        }

        eZContentCacheManager::clearTemplateBlockCacheIfNeeded( false );
        $cleared[] = 'template-block';
        return $cleared;
    }

    /**
     * The cache directory a siteaccess uses: its own [FileSettings] CacheDir or VarDir, else the current one.
     *
     * @param string $directory
     * @return string
     */
    protected static function cacheDirectory( $directory )
    {
        $ini = eZINI::instance();
        $siteINI = eZINI::instance( 'site.ini.append', $directory );
        if ( $siteINI->hasVariable( 'FileSettings', 'CacheDir' ) )
        {
            $cacheDir = $siteINI->variable( 'FileSettings', 'CacheDir' );
            if ( $cacheDir !== '' && $cacheDir[0] == '/' )
                return eZDir::path( array( $cacheDir ) );
            if ( $siteINI->hasVariable( 'FileSettings', 'VarDir' ) )
                return eZDir::path( array( $siteINI->variable( 'FileSettings', 'VarDir' ), $cacheDir ) );
            return eZDir::path( array( $ini->variable( 'FileSettings', 'VarDir' ), $cacheDir ) );
        }
        if ( $siteINI->hasVariable( 'FileSettings', 'VarDir' ) )
            return eZDir::path( array( $siteINI->variable( 'FileSettings', 'VarDir' ), $ini->variable( 'FileSettings', 'CacheDir' ) ) );
        return eZSys::cacheDirectory();
    }
}
?>
