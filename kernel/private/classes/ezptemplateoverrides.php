<?php
/**
 * File containing the ezpTemplateOverrides class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and others. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Changes the template overrides of one siteaccess, for Design > Templates, in
 * settings/siteaccess/<siteaccess>/override.ini.append.php and nowhere else.
 *
 * - The file is read from disk, directly and without the INI cache or any
 *   other settings file. The view used to load override.ini merged from every
 *   source (settings/, the active extensions, the siteaccess) and save the
 *   whole merge into the siteaccess file, which copied every extension's
 *   overrides into it; and "Update" gave every override in it a Priority, 0
 *   for all the ones the page did not show.
 * - Only the overrides the page showed are changed. An override defined by an
 *   extension gets its Priority (or its edited conditions) as a group of its
 *   own in the siteaccess file, which the settings merge lays over the
 *   extension's; the extension's file is never written.
 * - A copy of the file is kept before it is written; afterwards the file is
 *   read again and must hold exactly the old settings plus the change, or the
 *   copy is put back.
 *
 * The order of the overrides of a template is their Priority: lower first,
 * then the ones without one in the order the settings list them
 * (eZTemplateDesignResource::overrideArray()). The first whose conditions
 * match is used.
 */
class ezpTemplateOverrides
{
    const FILE = 'override.ini.append.php';

    /** @var string Error text of the last failed write */
    public $error = '';

    /** @var string Path of the copy kept by the last write */
    public $backup = '';

    /** @var string */
    private $siteAccess;

    public function __construct( $siteAccess )
    {
        $this->siteAccess = (string)$siteAccess;
    }

    /**
     * settings/siteaccess/<siteaccess>, or false for a name that is not one.
     */
    public function dir()
    {
        if ( !preg_match( '/^[A-Za-z0-9_-]+$/', $this->siteAccess ) )
            return false;
        return 'settings/siteaccess/' . $this->siteAccess;
    }

    /**
     * The siteaccess's own file, as it is on disk (empty if it does not exist).
     */
    private function read()
    {
        return new eZINI( self::FILE, $this->dir(), null, false, null, true );
    }

    /**
     * Override names defined in the siteaccess's own file: the groups that
     * give a Source there. A group with only a Priority or conditions is an
     * extension's override ordered or adjusted here, and remains the
     * extension's.
     */
    public function ownGroups()
    {
        $dir = $this->dir();
        if ( $dir === false || !is_file( $dir . '/' . self::FILE ) )
            return array();
        $own = array();
        foreach ( $this->read()->groups() as $name => $settings )
        {
            if ( isset( $settings['Source'] ) && $settings['Source'] !== '' )
                $own[] = $name;
        }
        return $own;
    }

    /**
     * The new order, when $order holds exactly the overrides of $shown, each
     * once; false otherwise. A page loaded before the overrides changed
     * elsewhere is refused instead of writing an order for a list it has not
     * seen.
     *
     * @return array|false
     */
    public static function reorder( array $shown, array $order )
    {
        $shown = array_values( array_unique( array_map( 'strval', $shown ) ) );
        $order = array_values( array_map( 'strval', $order ) );
        if ( count( $order ) !== count( array_unique( $order ) ) )
            return false;
        $a = $shown;
        $b = $order;
        sort( $a );
        sort( $b );
        return $a === $b ? $order : false;
    }

    /**
     * Priority values for an order: 10, 20, 30 ... Gaps, so that an override
     * added later by hand can go between two without renumbering.
     *
     * @return array name => priority
     */
    public static function priorities( array $order )
    {
        $priorities = array();
        foreach ( array_values( $order ) as $index => $name )
            $priorities[$name] = ( $index + 1 ) * 10;
        return $priorities;
    }

    /**
     * Writes the priorities (name => number) and, when given, the conditions
     * (name => array( key => value ), replacing the override's Match) of the
     * named overrides. Returns true, or false with $error set (the file is
     * then as it was).
     */
    public function update( array $priorities, array $matches = array() )
    {
        return $this->write( function ( array $groups ) use ( $priorities, $matches )
        {
            foreach ( $priorities as $name => $priority )
                $groups[$name]['Priority'] = (string)(int)$priority;
            foreach ( $matches as $name => $match )
            {
                if ( $match )
                    $groups[$name]['Match'] = $match;
                else
                    unset( $groups[$name]['Match'] );
            }
            return $groups;
        } );
    }

    /**
     * Removes the named overrides from the siteaccess's file. Names not
     * defined there (an extension's) are left out and listed in $notOwned.
     */
    public function remove( array $names, &$notOwned = array() )
    {
        $own = $this->ownGroups();
        $notOwned = array_values( array_diff( $names, $own ) );
        $names = array_values( array_intersect( $names, $own ) );
        if ( !$names )
            return true;
        return $this->write( function ( array $groups ) use ( $names )
        {
            foreach ( $names as $name )
                unset( $groups[$name] );
            return $groups;
        } );
    }

    /**
     * Applies $change (groups => groups) to the file: copy first, write, read
     * back and compare, and put the copy back if the file is not what was
     * meant.
     */
    private function write( $change )
    {
        $dir = $this->dir();
        if ( $dir === false || !is_dir( $dir ) )
        {
            $this->error = ezpI18n::tr( 'design/admin/visual/templateview', 'The siteaccess %siteaccess has no settings directory.', null, array( '%siteaccess' => $this->siteAccess ) );
            return false;
        }
        $path = $dir . '/' . self::FILE;
        $exists = is_file( $path );
        if ( $exists && !is_writable( $path ) || !$exists && !is_writable( $dir ) )
        {
            $this->error = ezpI18n::tr( 'design/admin/visual/templateview', '%file cannot be written.', null, array( '%file' => $path ) );
            return false;
        }

        $ini = $this->read();
        $before = $ini->groups();
        $expected = $change( $before );

        $this->backup = '';
        if ( $exists )
        {
            $backupDir = eZSys::varDirectory() . '/backups/settings-siteaccess/' . $this->siteAccess;
            if ( !is_dir( $backupDir ) )
                eZDir::mkdir( $backupDir, false, true );
            $this->backup = $backupDir . '/' . self::FILE . '.' . date( 'Ymd-His' ) . '-' . substr( md5( uniqid( '', true ) ), 0, 6 );
            if ( !copy( $path, $this->backup ) )
            {
                $this->error = ezpI18n::tr( 'design/admin/visual/templateview', 'No copy of %file could be kept; nothing was written.', null, array( '%file' => $path ) );
                return false;
            }
        }

        foreach ( array_keys( $before ) as $name )
        {
            if ( !isset( $expected[$name] ) )
                $ini->removeGroup( $name );
        }
        foreach ( $expected as $name => $settings )
        {
            foreach ( $settings as $key => $value )
                $ini->setVariable( $name, $key, $value );
            if ( isset( $before[$name] ) )
            {
                foreach ( array_keys( $before[$name] ) as $key )
                {
                    if ( !array_key_exists( $key, $settings ) )
                        $ini->removeSetting( $name, $key );
                }
            }
        }
        // No reset markers (Match[]) in front of the array settings: one would
        // throw away conditions an extension gives the same override in a file
        // read before this one. The file's own values are written as they are.
        $saved = $ini->save( false, false, false, false, true, false );

        $after = $this->read()->groups();
        if ( !$saved || self::normalise( $after ) != self::normalise( $expected ) )
        {
            if ( $this->backup !== '' )
                copy( $this->backup, $path );
            else if ( !$exists && is_file( $path ) )
                @unlink( $path );
            $this->error = ezpI18n::tr( 'design/admin/visual/templateview',
                'Writing %file did not give the expected settings, so the previous file was put back.',
                null, array( '%file' => $path ) );
            return false;
        }
        return true;
    }

    /**
     * Clears what is built on the overrides: the INI and override caches, and
     * the content view cache, whose pages were rendered with the old ones.
     */
    public static function expireCaches()
    {
        eZCache::clearByID( array( 'global_ini', 'template-override' ) );
        eZCache::clearByTag( 'ini' );
        eZContentCacheManager::clearAllContentCache();
    }

    /**
     * Groups as comparable values: in a stable order, values as strings,
     * empty entries of array settings (the reset marker) left out.
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
                {
                    $value = array_filter( $value, function ( $v ) { return $v !== '' && $v !== null; } );
                    ksort( $value );
                    $settings[$key] = array_map( 'strval', $value );
                }
                else
                    $settings[$key] = (string)$value;
            }
            $groups[$name] = $settings;
        }
        return $groups;
    }
}
