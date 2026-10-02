<?php
/**
 * File containing the expIniScope class: one place an INI file can be written.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One INI scope: a settings directory and the rule for the file name in it.
 *
 *   name                                   kind                   file written
 *   global                                 global                 settings/override/<file>.ini.append.php
 *   siteaccess:<sa>                        siteaccess             settings/siteaccess/<sa>/<file>.ini.append.php
 *   extension:<ext>                        extension              extension/<ext>/settings/<file>.ini.append.php
 *   extension:<ext>:siteaccess:<sa>        extension-siteaccess   extension/<ext>/settings/siteaccess/<sa>/<file>.ini.append.php
 *   default                                default                settings/<file>.ini (never writable unless allowed)
 *
 * A provider may create scopes of its own kinds; the kind is free text.
 */
class expIniScope
{
    const KIND_GLOBAL = 'global';
    const KIND_SITEACCESS = 'siteaccess';
    const KIND_EXTENSION = 'extension';
    const KIND_EXTENSION_SITEACCESS = 'extension-siteaccess';
    const KIND_DEFAULT = 'default';

    protected $name;
    protected $kind;
    protected $dir;
    protected $root;
    protected $label;
    protected $options;

    /**
     * @param string $name Canonical spec, e.g. 'siteaccess:admin'
     * @param string $kind One of the KIND_* constants or a provider's own kind
     * @param string $dir Settings directory relative to $root, no trailing slash, e.g. 'settings/override'
     * @param string $root Installation root, with a trailing slash
     * @param string $label Human description
     * @param array $options 'extension' => name, 'siteaccess' => name, 'active' => bool (extension active),
     *                       'suffix' => file suffix (default '.ini.append.php'), 'policyWritable' => bool
     *                       (default true; false for the default scope)
     */
    public function __construct( $name, $kind, $dir, $root, $label = '', array $options = array() )
    {
        $this->name = $name;
        $this->kind = $kind;
        $this->dir = rtrim( $dir, '/' );
        $this->root = rtrim( $root, '/' ) . '/';
        $this->label = $label !== '' ? $label : $name;
        $this->options = $options + array(
            'extension' => null, 'siteaccess' => null, 'active' => null,
            'suffix' => '.ini.append.php', 'policyWritable' => $kind !== self::KIND_DEFAULT,
        );
    }

    /** @return string Canonical spec, e.g. 'extension:ezfind:siteaccess:admin' */
    public function name() { return $this->name; }

    /** @return string The kind (global, siteaccess, extension, extension-siteaccess, default or a provider's own) */
    public function kind() { return $this->kind; }

    /** @return string Human description */
    public function label() { return $this->label; }

    /** @return string Settings directory relative to the root, e.g. 'settings/siteaccess/admin' */
    public function dir() { return $this->dir; }

    /** @return string Absolute settings directory */
    public function absoluteDir() { return $this->root . $this->dir; }

    /** @return string Installation root with a trailing slash */
    public function root() { return $this->root; }

    /** @return string|null The extension of an extension scope */
    public function extension() { return $this->options['extension']; }

    /** @return string|null The siteaccess of a siteaccess or extension-siteaccess scope */
    public function siteAccess() { return $this->options['siteaccess']; }

    /** @return bool|null Whether the extension is active (null when not an extension scope or unknown) */
    public function isActive() { return $this->options['active']; }

    /** @return bool Policy allows writing here without --allow-default (false only for the default scope) */
    public function policyWritable() { return (bool)$this->options['policyWritable']; }

    /**
     * File name in this scope for an INI file.
     *
     * @param string $file 'site', 'site.ini' or 'site.ini.append.php'
     * @return string e.g. 'site.ini.append.php'
     */
    public function fileName( $file )
    {
        $base = self::baseName( $file );
        $suffix = $this->options['suffix'];
        // an existing plain *.ini.append (no .php) in this directory is edited rather than shadowed
        if ( $suffix === '.ini.append.php'
             && !file_exists( $this->absoluteDir() . '/' . $base . '.ini.append.php' )
             && file_exists( $this->absoluteDir() . '/' . $base . '.ini.append' ) )
        {
            return $base . '.ini.append';
        }
        return $base . $suffix;
    }

    /**
     * Absolute path of the file this scope writes for an INI file.
     *
     * @param string $file 'site' or 'site.ini'
     * @return string
     */
    public function path( $file )
    {
        return $this->absoluteDir() . '/' . $this->fileName( $file );
    }

    /**
     * Path relative to the installation root.
     *
     * @param string $file
     * @return string
     */
    public function relativePath( $file )
    {
        return $this->dir . '/' . $this->fileName( $file );
    }

    /** @return bool The settings directory exists */
    public function exists()
    {
        return is_dir( $this->absoluteDir() );
    }

    /**
     * Policy allows it and the file system would let this process write: the directory (or its nearest existing
     * ancestor) is writable.
     *
     * @return bool
     */
    public function writable()
    {
        if ( !$this->policyWritable() )
            return false;
        $dir = $this->absoluteDir();
        while ( !is_dir( $dir ) )
        {
            $parent = dirname( $dir );
            if ( $parent === $dir )
                return false;
            $dir = $parent;
        }
        return is_writable( $dir );
    }

    /**
     * 'site', 'site.ini', 'site.ini.append.php', 'site.ini.append' -> 'site'.
     *
     * @param string $file
     * @return string
     */
    public static function baseName( $file )
    {
        return preg_replace( '#\.ini(\.append)?(\.php)?$#', '', basename( (string)$file ) );
    }

    /** @return array For --json */
    public function toArray()
    {
        return array(
            'name' => $this->name, 'kind' => $this->kind, 'label' => $this->label, 'dir' => $this->dir,
            'extension' => $this->extension(), 'siteaccess' => $this->siteAccess(), 'active' => $this->isActive(),
            'exists' => $this->exists(), 'writable' => $this->writable(),
        );
    }
}
