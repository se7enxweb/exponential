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
     * ActiveExtensions of an override file's text, in the order written (the
     * reset line and comments left out). Lines outside [ExtensionSettings]
     * are not looked at.
     */
    public static function fromText( $text )
    {
        $list = array();
        foreach ( self::splitLines( $text ) as $i => $line )
        {
            if ( self::groupOf( $text, $i ) !== 'ExtensionSettings' )
                continue;
            if ( preg_match( '/^\s*ActiveExtensions\[\]\s*=\s*(\S.*?)\s*$/', rtrim( $line, "\r\n" ), $m ) && !in_array( $m[1], $list, true ) )
                $list[] = $m[1];
        }
        return $list;
    }

    /**
     * The override file's text with ActiveExtensions replaced by $list and
     * every other byte as it was: other groups and settings, comments,
     * blank lines, line endings and the <?php wrapper. A comment directly
     * above an extension's line moves with that extension. Without a
     * [ExtensionSettings] group one is added before the closing wrapper.
     * When $list is what the text holds, the text comes back unchanged.
     *
     * @param string $text
     * @param array $list
     * @return string
     */
    public static function replaceInText( $text, array $list )
    {
        $list = array_values( array_unique( array_filter( array_map( 'strval', $list ), 'strlen' ) ) );
        if ( self::fromText( $text ) === $list )
            return $text;
        $eol = strpos( $text, "\r\n" ) !== false ? "\r\n" : "\n";
        $lines = self::splitLines( $text );

        // The group's lines
        $start = null;
        $end = count( $lines );
        foreach ( $lines as $i => $line )
        {
            $trim = trim( $line );
            if ( $start === null )
            {
                if ( $trim === '[ExtensionSettings]' )
                    $start = $i;
                continue;
            }
            if ( preg_match( '/^\[[^\]]+\]$/', $trim ) || strpos( $trim, '*/' ) === 0 )
            {
                $end = $i;
                break;
            }
        }

        $entryLines = array();
        foreach ( $list as $name )
            $entryLines[$name] = 'ActiveExtensions[]=' . $name . $eol;

        if ( $start === null )
        {
            // A new group, before the closing wrapper if there is one
            $block = $eol . '[ExtensionSettings]' . $eol . 'ActiveExtensions[]' . $eol . implode( '', $entryLines );
            $close = null;
            for ( $i = count( $lines ) - 1; $i >= 0; $i-- )
            {
                if ( strpos( trim( $lines[$i] ), '*/' ) === 0 ) { $close = $i; break; }
                if ( trim( $lines[$i] ) !== '' ) break;
            }
            if ( $close === null )
            {
                if ( $text !== '' && substr( $text, -1 ) !== "\n" )
                    $text .= $eol;
                return $text . ltrim( $block, "\r\n" );
            }
            array_splice( $lines, $close, 0, array( ltrim( $block, "\r\n" ) ) );
            return implode( '', $lines );
        }

        // The run of ActiveExtensions lines inside the group, and the comments right above each
        $first = null;
        $last = null;
        for ( $i = $start + 1; $i < $end; $i++ )
        {
            if ( preg_match( '/^\s*ActiveExtensions\[\]/', $lines[$i] ) )
            {
                if ( $first === null )
                    $first = $i;
                $last = $i;
            }
        }
        if ( $first === null )
        {
            array_splice( $lines, $start + 1, 0, array( 'ActiveExtensions[]' . $eol . implode( '', $entryLines ) ) );
            return implode( '', $lines );
        }

        $reset = array();
        $attached = array();
        $other = array();
        $pending = array();
        for ( $i = $first; $i <= $last; $i++ )
        {
            $line = $lines[$i];
            $bare = rtrim( $line, "\r\n" );
            if ( preg_match( '/^\s*[#;]/', $bare ) )
            {
                $pending[] = $line;
                continue;
            }
            if ( preg_match( '/^\s*ActiveExtensions\[\]\s*$/', $bare ) )
            {
                $reset[] = $line;
                $other = array_merge( $other, $pending );
                $pending = array();
                continue;
            }
            if ( preg_match( '/^\s*ActiveExtensions\[\]\s*=\s*(\S.*?)\s*$/', $bare, $m ) )
            {
                if ( !isset( $attached[$m[1]] ) )
                    $attached[$m[1]] = array( 'comments' => $pending, 'line' => $line );
                else
                    $other = array_merge( $other, $pending );
                $pending = array();
                continue;
            }
            // a blank line or another setting inside the run stays, after the list
            $other = array_merge( $other, $pending, array( $line ) );
            $pending = array();
        }

        $run = $reset ? $reset : array( 'ActiveExtensions[]' . $eol );
        foreach ( $list as $name )
        {
            if ( isset( $attached[$name] ) )
            {
                $line = $attached[$name]['line'];
                if ( substr( $line, -1 ) !== "\n" )
                    $line .= $eol;
                $run = array_merge( $run, $attached[$name]['comments'], array( $line ) );
            }
            else
            {
                $run[] = $entryLines[$name];
            }
        }
        // the comments of extensions no longer in the list are kept, after it
        foreach ( $attached as $name => $entry )
            if ( !in_array( $name, $list, true ) )
                $other = array_merge( $other, $entry['comments'] );
        $run = array_merge( $run, $other );
        // the file's last line may have had no line ending
        if ( $last === count( $lines ) - 1 && substr( $lines[$last], -1 ) !== "\n" )
            $run[count( $run ) - 1] = rtrim( $run[count( $run ) - 1], "\r\n" );

        array_splice( $lines, $first, $last - $first + 1, $run );
        return implode( '', $lines );
    }

    /**
     * Lines with their line endings.
     */
    private static function splitLines( $text )
    {
        return $text === '' ? array() : preg_split( '/(?<=\n)/', $text, -1, PREG_SPLIT_NO_EMPTY );
    }

    /**
     * The group line $index belongs to, or ''.
     */
    private static function groupOf( $text, $index )
    {
        static $cache = array();
        $key = md5( $text );
        if ( !isset( $cache[$key] ) )
        {
            $cache = array();
            $groups = array();
            $group = '';
            foreach ( self::splitLines( $text ) as $i => $line )
            {
                if ( preg_match( '/^\s*\[([^\]]+)\]\s*$/', $line, $m ) )
                    $group = $m[1];
                $groups[$i] = $group;
            }
            $cache[$key] = $groups;
        }
        return isset( $cache[$key][$index] ) ? $cache[$key][$index] : '';
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

        // Only the ActiveExtensions lines change: the file is edited as text, so its comments, its layout and the
        // <?php /* wrapper that keeps it from being served stay as they are (eZINI::save() wrote the file anew and
        // dropped every comment in it).
        $text = file_get_contents( $path );
        $newText = $text === false ? false : self::replaceInText( $text, $list );
        $saved = false;
        if ( $newText !== false && ( strpos( ltrim( $text ), '<?php' ) !== 0 || strpos( ltrim( $newText ), '<?php' ) === 0 ) )
            $saved = $newText === $text || file_put_contents( $path, $newText, LOCK_EX ) === strlen( $newText );
        clearstatcache( true, $path );

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

        // Audit (doc/bc/6.0/audit.md, system.extension.change): the list and its order, before and after
        if ( class_exists( 'expAuditHook' ) )
        {
            $old = isset( $beforeGroups['ExtensionSettings']['ActiveExtensions'] ) ? array_values( array_filter( (array)$beforeGroups['ExtensionSettings']['ActiveExtensions'], 'strlen' ) ) : array();
            $new = array_values( array_filter( $list, 'strlen' ) );
            if ( $old !== $new )
                expAuditHook::emit( 'system.extension.change', array(
                    'object' => array( 'type' => 'setting', 'id' => 'site.ini/ExtensionSettings/ActiveExtensions', 'path' => $path ),
                    'before' => array( 'list' => $old ), 'after' => array( 'list' => $new,
                                                                            'added' => array_values( array_diff( $new, $old ) ),
                                                                            'removed' => array_values( array_diff( $old, $new ) ) ) ) );
        }
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
