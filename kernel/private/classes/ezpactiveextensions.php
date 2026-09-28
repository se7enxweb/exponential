<?php
/**
 * File containing the ezpActiveExtensions class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and others. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Changes [ExtensionSettings] ActiveExtensions in settings/override/site.ini.append.php,
 * for Setup > Extensions, without touching anything else in that file.
 *
 * - The file is read from disk, directly and without the INI cache: the cache
 *   of an installation that does not check modification times
 *   ([INISettings] CheckModifiedTime off) can be older than the file, and a
 *   web server that keeps PHP alive across requests keeps eZINI instances.
 * - Only the extensions the form showed can be switched off. The list is
 *   paged, and an extension on another page is kept as it is; before this,
 *   saving one page switched off every active extension on the others.
 * - ActiveAccessExtensions are never moved into ActiveExtensions.
 * - A copy of the file is kept before it is written; afterwards the file is
 *   read again and must hold exactly the old settings plus the new list, or
 *   the copy is put back.
 */
class ezpActiveExtensions
{
    const FILE = 'site.ini.append.php';
    const DIR  = 'settings/override';

    /** @var string Error text of the last failed update() */
    public $error = '';

    /** @var string Path of the copy kept by the last update() */
    public $backup = '';

    /**
     * The file itself, as it is on disk.
     */
    private static function read()
    {
        return new eZINI( self::FILE, self::DIR, null, false, null, true );
    }

    /**
     * ActiveExtensions of the override file.
     */
    public static function current()
    {
        $ini = self::read();
        return $ini->hasVariable( 'ExtensionSettings', 'ActiveExtensions' )
            ? array_values( array_filter( (array)$ini->variable( 'ExtensionSettings', 'ActiveExtensions' ), 'strlen' ) )
            : array();
    }

    /**
     * The new list: the active extensions in their order, without the ones the
     * form showed unchecked, then the newly checked ones at the end.
     *
     * @param array $current   ActiveExtensions now
     * @param array $checked   the extensions checked in the form
     * @param array $shown     the extensions the form showed
     * @param array $available every extension there is
     * @param array $access    ActiveAccessExtensions (never added here)
     * @return array
     */
    public static function merge( array $current, array $checked, array $shown, array $available, array $access = array() )
    {
        $checked = array_values( array_intersect( array_unique( $checked ), $available ) );
        $shown = array_values( array_intersect( array_unique( $shown ), $available ) );
        $list = array();
        foreach ( $current as $extension )
        {
            // Kept unless the form showed it and left it unchecked.
            if ( !in_array( $extension, $shown, true ) || in_array( $extension, $checked, true ) )
                $list[] = $extension;
        }
        foreach ( $checked as $extension )
        {
            if ( !in_array( $extension, $list, true ) && !in_array( $extension, $access, true ) )
                $list[] = $extension;
        }
        return array_values( array_unique( $list ) );
    }

    /**
     * The new loading order, when $order holds exactly the extensions of
     * $current, each once, and nothing else; false otherwise. A reorder only
     * moves extensions: one sent from a page loaded before the list changed
     * elsewhere (another tab, another administrator) is refused instead of
     * switching anything on or off.
     *
     * @param array $current ActiveExtensions now
     * @param array $order   the same extensions in the order wanted
     * @return array|false
     */
    public static function reorder( array $current, array $order )
    {
        $current = array_values( array_unique( $current ) );
        $order = array_values( array_map( 'strval', $order ) );
        if ( count( $order ) !== count( array_unique( $order ) ) )
            return false;
        $a = $current;
        $b = $order;
        sort( $a );
        sort( $b );
        return $a === $b ? $order : false;
    }

    /**
     * Position (1 = loaded first) of each extension in $list, by name.
     */
    public static function positions( array $list )
    {
        $positions = array();
        foreach ( array_values( array_unique( $list ) ) as $index => $extension )
            $positions[$extension] = $index + 1;
        return $positions;
    }

    /**
     * Writes $list as ActiveExtensions. Returns true, or false with $error set
     * (the file is then as it was).
     */
    public function write( array $list )
    {
        $path = self::DIR . '/' . self::FILE;
        if ( !is_file( $path ) || !is_readable( $path ) )
        {
            $this->error = ezpI18n::tr( 'design/admin/setup/extensions', '%file cannot be read.', null, array( '%file' => $path ) );
            return false;
        }
        $before = self::read();
        $beforeGroups = $before->groups();
        if ( !$beforeGroups )
        {
            $this->error = ezpI18n::tr( 'design/admin/setup/extensions', '%file holds no settings; nothing was written.', null, array( '%file' => $path ) );
            return false;
        }

        // A copy first, in the var directory (not served, not in settings/).
        $dir = eZSys::varDirectory() . '/backups/settings-override';
        if ( !is_dir( $dir ) )
            eZDir::mkdir( $dir, false, true );
        $this->backup = $dir . '/' . self::FILE . '.' . date( 'Ymd-His' ) . '-' . substr( md5( uniqid( '', true ) ), 0, 6 );
        if ( !copy( $path, $this->backup ) )
        {
            $this->error = ezpI18n::tr( 'design/admin/setup/extensions', 'No copy of %file could be kept; nothing was written.', null, array( '%file' => $path ) );
            return false;
        }

        $before->setVariable( 'ExtensionSettings', 'ActiveExtensions', $list );
        $saved = $before->save( false, false, false, false, true, true );

        // What must be in the file now: everything as before, and the list.
        $expected = $beforeGroups;
        $expected['ExtensionSettings']['ActiveExtensions'] = $list;
        $after = self::read()->groups();
        if ( !$saved || self::normalise( $after ) != self::normalise( $expected ) )
        {
            copy( $this->backup, $path );
            $this->error = ezpI18n::tr( 'design/admin/setup/extensions',
                'Writing %file did not give the expected settings, so the previous file was put back (a copy is in %backup).',
                null, array( '%file' => $path, '%backup' => $this->backup ) );
            return false;
        }
        eZINI::resetInstance( 'site.ini' );
        return true;
    }

    /**
     * Groups as comparable values: settings in a stable order, empty entries
     * of array settings (the reset marker) left out.
     */
    private static function normalise( array $groups )
    {
        ksort( $groups );
        foreach ( $groups as $name => $settings )
        {
            ksort( $settings );
            foreach ( $settings as $key => $value )
            {
                if ( is_array( $value ) )
                    $settings[$key] = array_values( array_filter( $value, function ( $v ) { return $v !== '' && $v !== null; } ) );
            }
            $groups[$name] = $settings;
        }
        return $groups;
    }
}
