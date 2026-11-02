<?php
/**
 * File containing the expStaticManifest class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The manifest of a static build: every file the build wrote, by its path in the export, with its kind (page, file,
 * storage, extra), the address it came from, its SHA-256, its size and, for a page, the node and when its content
 * was modified. It is written to .static-publish/manifest.json in the export; the next build compares with it
 * (diff()) to know what was added, changed and removed, and removes only files a manifest lists.
 *
 * The file is JSON with sorted keys, / separators and \n line ends whatever the system, so the same build gives the
 * same bytes on Windows and elsewhere (format exp-static-manifest/1, see
 * doc/eng-us/specifications/6.0/static-publishing-build-format.md). Pure apart from load() and save().
 */
class expStaticManifest
{
    const FORMAT = 'exp-static-manifest/1';

    /** The manifest's place in the export */
    const FILE = '.static-publish/manifest.json';

    /** The manifest of the build before, kept next to it */
    const PREVIOUS_FILE = '.static-publish/manifest.previous.json';

    const KINDS = array( 'page', 'file', 'storage', 'extra' );

    /** path => entry */
    private $entries = array();

    /** target, run_id, built_at, links, base_path, public_url, siteaccess */
    private $meta = array();

    /**
     * @param array $meta
     */
    public function __construct( array $meta = array() )
    {
        $this->meta = $meta;
    }

    /**
     * Adds or replaces a file.
     *
     * @param string $path the path in the export, relative, / separated
     * @param string $kind page, file, storage or extra
     * @param string $sha256
     * @param int $size
     * @param array $extra url (where it came from), node (int|null), modified (unix time|null)
     * @throws InvalidArgumentException for a path that is not relative and safe, or an unknown kind
     */
    public function add( $path, $kind, $sha256, $size, array $extra = array() )
    {
        $path = self::normalisePath( $path );
        if ( $path === null )
            throw new InvalidArgumentException( 'Not a path in the export: ' . var_export( func_get_arg( 0 ), true ) );
        if ( !in_array( $kind, self::KINDS, true ) )
            throw new InvalidArgumentException( "Unknown kind $kind" );
        $this->entries[$path] = array(
            'kind' => $kind,
            'sha256' => (string)$sha256,
            'size' => (int)$size,
            'url' => isset( $extra['url'] ) ? (string)$extra['url'] : '',
            'node' => isset( $extra['node'] ) && $extra['node'] !== null ? (int)$extra['node'] : null,
            'modified' => isset( $extra['modified'] ) && $extra['modified'] !== null ? (int)$extra['modified'] : null,
        );
        // a page of a copy of several siteaccesses names its siteaccess
        if ( isset( $extra['siteaccess'] ) && (string)$extra['siteaccess'] !== '' )
            $this->entries[$path]['siteaccess'] = (string)$extra['siteaccess'];
    }

    /**
     * A path in its one form (/ separators, no leading slash, no dot segment), or null when it leaves the export.
     *
     * @param string $path
     * @return string|null
     */
    public static function normalisePath( $path )
    {
        $path = str_replace( '\\', '/', (string)$path );
        if ( $path === '' || $path[0] === '/' || preg_match( '#^[a-zA-Z]:#', $path ) )
            return null;
        $segments = explode( '/', $path );
        foreach ( $segments as $segment )
            if ( $segment === '' || $segment === '.' || $segment === '..' )
                return null;
        return implode( '/', $segments );
    }

    /** @param string $path */
    public function remove( $path )
    {
        unset( $this->entries[(string)$path] );
    }

    /** @return bool */
    public function has( $path )
    {
        return isset( $this->entries[(string)$path] );
    }

    /** @return array|null the entry of a path */
    public function get( $path )
    {
        return isset( $this->entries[(string)$path] ) ? $this->entries[(string)$path] : null;
    }

    /** @return array path => entry, sorted by path */
    public function entries()
    {
        ksort( $this->entries, SORT_STRING );
        return $this->entries;
    }

    /** @return array */
    public function meta()
    {
        return $this->meta;
    }

    /** @param array $meta merged into the meta data */
    public function setMeta( array $meta )
    {
        $this->meta = $meta + $this->meta;
    }

    /**
     * @param string|null $kind only this kind
     * @return int
     */
    public function count( $kind = null )
    {
        if ( $kind === null )
            return count( $this->entries );
        $n = 0;
        foreach ( $this->entries as $entry )
            if ( $entry['kind'] === $kind )
                $n++;
        return $n;
    }

    /**
     * @param string|null $kind only this kind
     * @return int the bytes of the files
     */
    public function bytes( $kind = null )
    {
        $n = 0;
        foreach ( $this->entries as $entry )
            if ( $kind === null || $entry['kind'] === $kind )
                $n += $entry['size'];
        return $n;
    }

    /**
     * The entries of another manifest that this one does not have, added: what a partial build keeps of the build
     * before it.
     *
     * @param expStaticManifest $older
     */
    public function keepFrom( expStaticManifest $older )
    {
        foreach ( $older->entries() as $path => $entry )
            if ( !isset( $this->entries[$path] ) )
                $this->entries[$path] = $entry;
    }

    /**
     * What changed from one manifest to the next.
     *
     * @param expStaticManifest|null $old null: everything is new
     * @param expStaticManifest $new
     * @return array added, changed, removed (paths, sorted), unchanged (a count), bytes_added, bytes_removed
     */
    public static function diff( $old, expStaticManifest $new )
    {
        $diff = array( 'added' => array(), 'changed' => array(), 'removed' => array(), 'unchanged' => 0, 'bytes_added' => 0, 'bytes_removed' => 0 );
        $before = $old instanceof expStaticManifest ? $old->entries() : array();
        foreach ( $new->entries() as $path => $entry )
        {
            if ( !isset( $before[$path] ) )
            {
                $diff['added'][] = $path;
                $diff['bytes_added'] += $entry['size'];
            }
            else if ( $before[$path]['sha256'] !== $entry['sha256'] )
            {
                $diff['changed'][] = $path;
                $diff['bytes_added'] += $entry['size'];
                $diff['bytes_removed'] += $before[$path]['size'];
            }
            else
                $diff['unchanged']++;
        }
        foreach ( $before as $path => $entry )
            if ( !$new->has( $path ) )
            {
                $diff['removed'][] = $path;
                $diff['bytes_removed'] += $entry['size'];
            }
        return $diff;
    }

    /** @return array the manifest as data (format, meta, files) */
    public function toArray()
    {
        $meta = $this->meta;
        ksort( $meta, SORT_STRING );
        return array( 'format' => self::FORMAT, 'meta' => $meta, 'files' => $this->entries() );
    }

    /** @return string the manifest as JSON: sorted, \n line ends, a final newline */
    public function toJson()
    {
        $json = json_encode( $this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        return str_replace( "\r\n", "\n", (string)$json ) . "\n";
    }

    /**
     * @param array $data toArray()
     * @return expStaticManifest
     * @throws InvalidArgumentException for data that is not a manifest of this format
     */
    public static function fromArray( array $data )
    {
        if ( !isset( $data['format'] ) || $data['format'] !== self::FORMAT || !isset( $data['files'] ) || !is_array( $data['files'] ) )
            throw new InvalidArgumentException( 'Not a manifest of the format ' . self::FORMAT );
        $manifest = new self( isset( $data['meta'] ) && is_array( $data['meta'] ) ? $data['meta'] : array() );
        foreach ( $data['files'] as $path => $entry )
        {
            if ( !is_array( $entry ) || !isset( $entry['kind'], $entry['sha256'], $entry['size'] ) )
                throw new InvalidArgumentException( "The entry of $path is incomplete" );
            $manifest->add( (string)$path, (string)$entry['kind'], (string)$entry['sha256'], (int)$entry['size'], $entry );
        }
        return $manifest;
    }

    /**
     * The manifest of an export, or null when it has none (or an unreadable one).
     *
     * @param string $dir the export
     * @param string $file the manifest's path in it (default FILE)
     * @return expStaticManifest|null
     */
    public static function load( $dir, $file = self::FILE )
    {
        $path = rtrim( (string)$dir, '/\\' ) . '/' . $file;
        if ( !is_file( $path ) )
            return null;
        $data = json_decode( (string)file_get_contents( $path ), true );
        if ( !is_array( $data ) )
            return null;
        try
        {
            return self::fromArray( $data );
        }
        catch ( InvalidArgumentException $e )
        {
            return null;
        }
    }

    /**
     * Writes the manifest into an export.
     *
     * @param string $dir
     * @param string $file the manifest's path in it (default FILE)
     * @return bool
     */
    public function save( $dir, $file = self::FILE )
    {
        $path = rtrim( (string)$dir, '/\\' ) . '/' . $file;
        if ( !is_dir( dirname( $path ) ) && !mkdir( dirname( $path ), eZDir::dirMode( 0775 ), true ) && !is_dir( dirname( $path ) ) )
            return false;
        return file_put_contents( $path, $this->toJson() ) !== false;
    }
}
