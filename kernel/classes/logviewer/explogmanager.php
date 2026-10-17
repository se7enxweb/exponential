<?php
/**
 * File containing the expLogManager class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Log management (doc/features/6.0/log-rotation.md): the logs of the installation with their archives and sizes,
 * rotation on demand of one, several or all of them, and pruning of old archives, for the command line
 * (./console exp:logs --rotate, --prune, --archives) and the log viewer (Setup > Log files).
 *
 * Which files: exactly those the log viewer may open (expLogFileLocator: the directories of logviewer.ini
 * [LogViewerSettings] Directories[], real paths inside the installation, no symbolic links out of them), named by
 * the same keys as its permalinks (error, debug, site-storage ...) or by their id (var/log/error.log). Never a
 * rotated file itself, and never a log configured by key (LogList[]: the web server's), whose rotation belongs to
 * the program that writes it.
 *
 * How: expLogRotator, the rotation eZDebug and eZLog use when a log outgrows site.ini [FileSettings]
 * LogFileMaxSize, with that file's LogFileRotateCount (archives kept), LogFileCompress and LogFileMaxAge.
 */
class expLogManager
{
    /** @var expLogFileLocator */
    protected $locator;

    /** @var array keep, compress, maxAge (seconds) */
    protected $settings;

    /**
     * @param expLogFileLocator $locator
     * @param array|null $settings keep (int), compress (bool), maxAge (seconds); null: from site.ini [FileSettings]
     */
    public function __construct( expLogFileLocator $locator, $settings = null )
    {
        $this->locator = $locator;
        if ( $settings === null )
        {
            $settings = array( 'keep' => (int)eZLog::maxLogrotateFiles(), 'compress' => eZLog::compressArchives(),
                               'maxAge' => eZLog::maxArchiveAge() );
        }
        $this->settings = array_merge( array( 'keep' => eZLog::MAX_LOGROTATE_FILES, 'compress' => false, 'maxAge' => 0 ), $settings );
    }

    /**
     * This request's manager, on the log viewer's settings.
     *
     * @return expLogManager
     */
    public static function instance()
    {
        return new self( new expLogFileLocator( expLogViewerConfig::instance() ) );
    }

    /** @return array keep, compress, maxAge */
    public function settings()
    {
        return $this->settings;
    }

    /**
     * Whether a file (as the locator describes it) may be rotated or pruned: a current file, not a configured one.
     *
     * @param array $file
     * @return bool
     */
    public static function manageable( array $file )
    {
        return empty( $file['archive'] ) && empty( $file['configured'] ) && isset( $file['group'] )
               && $file['group'] !== expLogFileLocator::GROUP_WEBSERVER && $file['group'] !== expLogFileLocator::GROUP_ARCHIVES;
    }

    /**
     * The logs that may be managed, each with its archives: key => key, id, path, name, group, size, mtime,
     * archives (list of name, rotation, size, mtime, gz), archive_bytes.
     *
     * @return array
     */
    public function logs()
    {
        $out = array();
        foreach ( $this->locator->listFiles() as $group => $files )
        {
            foreach ( $files as $info )
            {
                if ( !self::manageable( $info ) || isset( $out[$info['key']] ) )
                    continue;
                $file = $this->locator->resolve( $info['id'] );
                if ( $file === null )
                    continue;
                $out[$info['key']] = $this->describe( $file );
            }
        }
        ksort( $out );
        return $out;
    }

    /**
     * A log with its archives.
     *
     * @param array $file a resolved file
     * @return array
     */
    protected function describe( array $file )
    {
        $archives = array();
        $bytes = 0;
        foreach ( expLogRotator::archives( $file['path'] ) as $archive )
        {
            unset( $archive['path'] );
            $archives[] = $archive;
            $bytes += $archive['size'];
        }
        return array( 'key' => $file['key'], 'id' => $file['id'], 'path' => $file['path'], 'name' => $file['name'],
                      'group' => $file['group'], 'size' => $file['size'], 'mtime' => $file['mtime'],
                      'archives' => $archives, 'archive_bytes' => $bytes );
    }

    /**
     * The logs named by keys or ids. Each name is looked up only among the files the viewer may open; a name
     * that is not one (a path outside the log directories, "..", an archive, a web server log) is returned in
     * unknown, never touched.
     *
     * @param string[] $names keys (error, site-storage) or ids (var/log/error.log)
     * @return array found (key => resolved file), unknown (list of names)
     */
    public function select( array $names )
    {
        $found = array();
        $unknown = array();
        foreach ( $names as $name )
        {
            $name = trim( (string)$name );
            if ( $name === '' )
                continue;
            $file = null;
            if ( preg_match( expLogFileLocator::KEY_PATTERN, $name ) )
                $file = $this->locator->resolveKey( $name, 0 );
            if ( $file === null && strpos( $name, '/' ) !== false && expLogFileLocator::validId( $name ) )
                $file = $this->locator->resolve( $name );
            if ( $file === null || !self::manageable( $file ) )
            {
                $unknown[] = $name;
                continue;
            }
            $found[$file['key']] = $file;
        }
        return array( 'found' => $found, 'unknown' => $unknown );
    }

    /**
     * Rotates one log now, whatever its size.
     *
     * @param array $file a resolved file (expLogFileLocator::resolve())
     * @return array ok, error ('' | not_allowed | disabled | missing | locked | rename | moved ...), renamed (list)
     */
    public function rotateFile( array $file )
    {
        if ( !self::manageable( $file ) || !isset( $file['path'] ) )
            return array( 'ok' => false, 'error' => 'not_allowed', 'renamed' => array() );
        return expLogRotator::rotate( $file['path'], max( 1, (int)$this->settings['keep'] ),
                                      array( 'compress' => (bool)$this->settings['compress'] ) );
    }

    /**
     * Rotates logs now.
     *
     * @param string[]|null $names keys or ids; null: every log that may be managed
     * @param bool $skipEmpty leave an empty log as it is (rotate all would otherwise make empty archives)
     * @return array results (key => rotateFile() result + id + size; error "empty" for a skipped empty file),
     *               unknown (names that are not a log that may be managed)
     */
    public function rotate( $names = null, $skipEmpty = true )
    {
        if ( $names === null )
        {
            $files = array();
            foreach ( $this->logs() as $key => $log )
                $files[$key] = $this->locator->resolve( $log['id'] );
            $selection = array( 'found' => array_filter( $files ), 'unknown' => array() );
        }
        else
        {
            $selection = $this->select( $names );
        }
        $results = array();
        foreach ( $selection['found'] as $key => $file )
        {
            if ( $skipEmpty && (int)$file['size'] === 0 )
            {
                $results[$key] = array( 'ok' => false, 'error' => 'empty', 'renamed' => array() );
                continue;
            }
            $results[$key] = $this->rotateFile( $file ) + array( 'id' => $file['id'], 'size' => (int)$file['size'] );
        }
        return array( 'results' => $results, 'unknown' => $selection['unknown'] );
    }

    /**
     * Removes the archives beyond the limits: a number above LogFileRotateCount, a bare .gz, an archive older than
     * LogFileMaxAge days.
     *
     * @param string[]|null $names keys or ids; null: every log that may be managed
     * @param bool $dryRun only report
     * @return array results (key => list of name, size, reason, removed), ids (key => id), unknown, bytes (freed, or that would be)
     */
    public function prune( $names = null, $dryRun = false )
    {
        if ( $names === null )
        {
            $files = array();
            foreach ( $this->logs() as $key => $log )
                $files[$key] = array( 'path' => $log['path'], 'id' => $log['id'] );
            $selection = array( 'found' => $files, 'unknown' => array() );
        }
        else
        {
            $selection = $this->select( $names );
        }
        $results = array();
        $ids = array();
        $bytes = 0;
        foreach ( $selection['found'] as $key => $file )
        {
            $list = expLogRotator::prune( $file['path'], (int)$this->settings['keep'], (int)$this->settings['maxAge'], $dryRun );
            if ( !$list )
                continue;
            $results[$key] = $list;
            $ids[$key] = $file['id'];
            foreach ( $list as $entry )
                if ( $dryRun || $entry['removed'] )
                    $bytes += $entry['size'];
        }
        return array( 'results' => $results, 'ids' => $ids, 'unknown' => $selection['unknown'], 'bytes' => $bytes );
    }
}
