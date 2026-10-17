<?php
/**
 * File containing the expLogManager class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Log management (doc/features/6.0/log-rotation.md): the logs of the installation with their archives, sizes, growth
 * and limits; rotation, pruning, compression and emptying on demand, as a plan (a dry run) that apply() carries out;
 * for the command line (./console exp:logs --rotate ...), the cronjob part "logs" and the log viewer (Setup > Log
 * files), which builds its actions on this API.
 *
 * Which files: exactly those the log viewer may open (expLogFileLocator: the directories of logviewer.ini
 * [LogViewerSettings] Directories[], real paths inside the installation, no symbolic links out of them), named by the
 * same keys as its permalinks (error, debug, site-storage ...) or by their id (var/log/error.log). Never a rotated file
 * itself, and never a log configured by key (LogList[]: the web server's), whose rotation belongs to its own program.
 *
 * The limits of a log: logviewer.ini [RotationSettings] MaxSize[<key>], Keep[<key>], Compress[<key>],
 * MaxAgeDays[<key>], else site.ini [FileSettings] LogFileMaxSize, LogFileRotateCount, LogFileCompress, LogFileMaxAge.
 * The size-triggered rotation inside eZDebug and eZLog uses the site.ini values only.
 *
 * How: expLogRotator (locked, keeps the owner, group and mode of a log, never follows a link).
 */
class expLogManager
{
    const OPS = array( 'rotate', 'prune', 'compress', 'truncate' );

    /** @var expLogFileLocator */
    protected $locator;

    /** @var array defaults: max_size, keep, compress, max_age_days, each with its source */
    protected $defaults;

    /** @var array per key overrides: limit => key => value */
    protected $overrides;

    /**
     * @param expLogViewerConfig|null $config null: logviewer.ini
     * @param expLogFileLocator|null $locator null: one on $config
     * @param array|null $settings tests: defaults {max_size, keep, compress, max_age_days} and overrides
     *                             {MaxSize|Keep|Compress|MaxAgeDays => {key => value}}; null: from the INI files
     */
    public function __construct( ?expLogViewerConfig $config = null, ?expLogFileLocator $locator = null, ?array $settings = null )
    {
        $config = $config ?: expLogViewerConfig::instance();
        $this->locator = $locator ?: new expLogFileLocator( $config );
        if ( $settings !== null )
        {
            $source = array( 'file' => 'test', 'section' => '', 'var' => '' );
            $this->defaults = array();
            foreach ( array( 'max_size' => eZLog::MAX_LOGFILE_SIZE, 'keep' => eZLog::MAX_LOGROTATE_FILES, 'compress' => false, 'max_age_days' => 0 ) as $name => $default )
                $this->defaults[$name] = array( 'value' => isset( $settings[$name] ) ? $settings[$name] : $default, 'source' => $source );
            $this->overrides = isset( $settings['overrides'] ) ? $settings['overrides'] : array();
            return;
        }
        $site = array( 'file' => 'site.ini', 'section' => 'FileSettings' );
        $this->defaults = array(
            'max_size' => array( 'value' => (int)eZLog::maxLogSize(), 'source' => $site + array( 'var' => 'LogFileMaxSize' ) ),
            'keep' => array( 'value' => (int)eZLog::maxLogrotateFiles(), 'source' => $site + array( 'var' => 'LogFileRotateCount' ) ),
            'compress' => array( 'value' => eZLog::compressArchives(), 'source' => $site + array( 'var' => 'LogFileCompress' ) ),
            'max_age_days' => array( 'value' => (int)( eZLog::maxArchiveAge() / 86400 ), 'source' => $site + array( 'var' => 'LogFileMaxAge' ) ) );
        $this->overrides = array();
        if ( class_exists( 'eZINI' ) && eZINI::exists( 'logviewer.ini' ) )
        {
            $ini = eZINI::instance( 'logviewer.ini' );
            foreach ( array( 'MaxSize', 'Keep', 'Compress', 'MaxAgeDays' ) as $var )
                if ( $ini->hasVariable( 'RotationSettings', $var ) && is_array( $ini->variable( 'RotationSettings', $var ) ) )
                    $this->overrides[$var] = $ini->variable( 'RotationSettings', $var );
        }
    }

    /**
     * This request's manager, on the log viewer's settings.
     *
     * @return expLogManager
     */
    public static function instance()
    {
        return new self();
    }

    /** @return expLogFileLocator */
    public function locator()
    {
        return $this->locator;
    }

    /**
     * Whether a file (as the locator describes it) may be managed: a current file, not a configured one.
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
     * The limits of a log, each with the INI file, section and variable it comes from.
     *
     * @param string $key
     * @return array max_size, keep, compress, max_age_days => {value, source{file, section, var}}
     */
    public function effectiveSettings( $key )
    {
        $out = $this->defaults;
        $map = array( 'MaxSize' => 'max_size', 'Keep' => 'keep', 'Compress' => 'compress', 'MaxAgeDays' => 'max_age_days' );
        foreach ( $map as $var => $name )
        {
            if ( !isset( $this->overrides[$var][$key] ) )
                continue;
            $raw = trim( (string)$this->overrides[$var][$key] );
            if ( $name === 'compress' )
                $value = $raw === 'enabled';
            else if ( ctype_digit( $raw ) )
                $value = (int)$raw;
            else
                continue;
            $out[$name] = array( 'value' => $value, 'source' => array( 'file' => 'logviewer.ini', 'section' => 'RotationSettings', 'var' => $var . '[' . $key . ']' ) );
        }
        return $out;
    }

    /** @return array the limits' values only */
    protected function limits( $key )
    {
        $limits = array();
        foreach ( $this->effectiveSettings( $key ) as $name => $entry )
            $limits[$name] = $entry['value'];
        return $limits;
    }

    /**
     * The logs that may be managed, with their archives, growth and limits.
     *
     * @return array key => key, id, path, name, group, size, mtime, archives [{id, name, rotation, size, gz, mtime}],
     *               archive_bytes, growth (bytes an hour since the last rotation, null when not known), limits
     *               {max_size, keep, compress, max_age_days, source{<limit>: {file, section, var}}}, writable, owner, owner_group
     *               (group is the log viewer's group: kernel, site, extensions)
     */
    public function logs()
    {
        $out = array();
        foreach ( $this->locator->listFiles() as $files )
        {
            foreach ( $files as $info )
            {
                if ( !self::manageable( $info ) || isset( $out[$info['key']] ) )
                    continue;
                $file = $this->locator->resolve( $info['id'] );
                if ( $file !== null )
                    $out[$file['key']] = $this->describe( $file );
            }
        }
        ksort( $out );
        return $out;
    }

    /**
     * A log with its archives, growth and limits.
     *
     * @param array $file a resolved file
     * @return array
     */
    protected function describe( array $file )
    {
        $archives = array();
        $bytes = 0;
        $rotatedAt = null;
        foreach ( expLogRotator::archives( $file['path'] ) as $archive )
        {
            $archives[] = array( 'id' => ( $file['dir'] !== '' ? $file['dir'] . '/' : '' ) . $archive['name'], 'name' => $archive['name'],
                                 'rotation' => $archive['rotation'], 'size' => $archive['size'], 'gz' => $archive['gz'], 'mtime' => $archive['mtime'] );
            $bytes += $archive['size'];
            if ( $archive['rotation'] === 1 )
                $rotatedAt = $archive['mtime'];
        }
        // .1 was last written when the log was rotated: the current file grew since then
        $growth = null;
        if ( $rotatedAt !== null && $file['mtime'] > $rotatedAt )
            $growth = (int)round( $file['size'] / max( 1, ( time() - $rotatedAt ) / 3600 ) );
        $settings = $this->effectiveSettings( $file['key'] );
        $limits = array();
        $sources = array();
        foreach ( $settings as $name => $entry )
        {
            $limits[$name] = $entry['value'];
            $sources[$name] = $entry['source'];
        }
        $limits['source'] = $sources;
        $stat = @stat( $file['path'] );
        return array( 'key' => $file['key'], 'id' => $file['id'], 'path' => $file['path'], 'name' => $file['name'],
                      'group' => $file['group'], 'size' => (int)$file['size'], 'mtime' => (int)$file['mtime'],
                      'archives' => $archives, 'archive_bytes' => $bytes, 'growth' => $growth, 'limits' => $limits,
                      'writable' => self::writableByOthers( $stat ), 'owner' => self::userName( $stat ? $stat['uid'] : null ),
                      'owner_group' => self::groupName( $stat ? $stat['gid'] : null ) );
    }

    /**
     * Whether a log stays writable for the other writer: world-writable, or this process may write it (the web
     * server's user is the owner in a default installation).
     *
     * @param array|false $stat
     * @return bool
     */
    protected static function writableByOthers( $stat )
    {
        if ( !$stat )
            return false;
        return ( $stat['mode'] & 0002 ) !== 0 || ( $stat['mode'] & 0200 ) !== 0 && function_exists( 'posix_geteuid' ) && posix_geteuid() === (int)$stat['uid'];
    }

    protected static function userName( $uid )
    {
        if ( $uid === null )
            return '';
        $info = function_exists( 'posix_getpwuid' ) ? @posix_getpwuid( (int)$uid ) : false;
        return $info ? $info['name'] : (string)$uid;
    }

    protected static function groupName( $gid )
    {
        if ( $gid === null )
            return '';
        $info = function_exists( 'posix_getgrgid' ) ? @posix_getgrgid( (int)$gid ) : false;
        return $info ? $info['name'] : (string)$gid;
    }

    /**
     * The logs named by keys or ids, looked up only among the files the viewer may open; anything else is unknown
     * and never touched.
     *
     * @param string[] $names
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
     * What an operation would do (a dry run: nothing is changed).
     *
     * @param string[]|string $keys keys or ids, or 'all'
     * @param string $op rotate | prune | compress | truncate
     * @param array $opts force (rotate: also a log under MaxSize and an empty one; for 'all' the default is false: only
     *                    logs over MaxSize), days (prune: an age limit instead of MaxAgeDays), archive_first (truncate:
     *                    rotate instead of emptying, default true)
     * @return array op, steps [{key, op, from, to, bytes}], unknown []
     */
    public function plan( $keys, $op, array $opts = array() )
    {
        if ( !in_array( $op, self::OPS, true ) )
            throw new InvalidArgumentException( 'Unknown log operation: ' . $op );
        if ( $keys === 'all' )
        {
            $selection = array( 'found' => array(), 'unknown' => array() );
            foreach ( $this->logs() as $key => $log )
            {
                $file = $this->locator->resolve( $log['id'] );
                if ( $file !== null )
                    $selection['found'][$key] = $file;
            }
            $force = !empty( $opts['force'] );
        }
        else
        {
            $selection = $this->select( (array)$keys );
            $force = !isset( $opts['force'] ) || $opts['force'];
        }
        $steps = array();
        foreach ( $selection['found'] as $key => $file )
        {
            $limits = $this->limits( $key );
            $path = $file['path'];
            $size = (int)$file['size'];
            $base = basename( $path );
            switch ( $op )
            {
                case 'rotate':
                    if ( $size === 0 || ( !$force && $size <= (int)$limits['max_size'] ) || (int)$limits['keep'] < 1 )
                        break;
                    $steps[] = array( 'key' => $key, 'op' => 'rotate', 'from' => $base, 'to' => $base . '.1', 'bytes' => $size );
                    break;
                case 'truncate':
                    if ( $size === 0 )
                        break;
                    $archive = !isset( $opts['archive_first'] ) || $opts['archive_first'];
                    $steps[] = $archive && (int)$limits['keep'] >= 1
                               ? array( 'key' => $key, 'op' => 'rotate', 'from' => $base, 'to' => $base . '.1', 'bytes' => $size )
                               : array( 'key' => $key, 'op' => 'truncate', 'from' => $base, 'to' => $base, 'bytes' => $size );
                    break;
                case 'prune':
                    $days = isset( $opts['days'] ) && $opts['days'] !== null ? (int)$opts['days'] : (int)$limits['max_age_days'];
                    foreach ( expLogRotator::prune( $path, (int)$limits['keep'], $days * 86400, true ) as $entry )
                        $steps[] = array( 'key' => $key, 'op' => 'delete', 'from' => $entry['name'], 'to' => '', 'bytes' => $entry['size'], 'reason' => $entry['reason'] );
                    break;
                case 'compress':
                    $from = isset( $opts['from'] ) ? max( 1, (int)$opts['from'] ) : 2;
                    foreach ( expLogRotator::archives( $path ) as $archive )
                        if ( !$archive['gz'] && $archive['rotation'] >= $from && !file_exists( $archive['path'] . '.gz' ) )
                            $steps[] = array( 'key' => $key, 'op' => 'compress', 'from' => $archive['name'], 'to' => $archive['name'] . '.gz', 'bytes' => $archive['size'] );
                    break;
            }
        }
        return array( 'op' => $op, 'steps' => $steps, 'unknown' => $selection['unknown'] );
    }

    /**
     * Carries out a plan. Each step is checked again against the disk (the plan may be stale): a log is resolved
     * through the allow-list again, a rotation happens under expLogRotator's lock and only while the file is still
     * the size planned or bigger, an archive is removed or compressed only while it still exists.
     *
     * @param array $plan as plan() returns it
     * @return array ok (every step done or skipped), error (first error), steps (each with done and error)
     */
    public function apply( array $plan )
    {
        $ok = true;
        $firstError = '';
        $out = array();
        foreach ( isset( $plan['steps'] ) ? $plan['steps'] : array() as $step )
        {
            $step['done'] = false;
            $step['error'] = '';
            $file = isset( $step['key'] ) ? $this->locator->resolveKey( (string)$step['key'], 0 ) : null;
            if ( $file === null || !self::manageable( $file ) )
                $step['error'] = 'not_allowed';
            else
            {
                $limits = $this->limits( $file['key'] );
                $dir = dirname( $file['path'] );
                $target = $dir . '/' . basename( (string)$step['from'] );
                // a step names a file of this log only: the log itself or one of its archives
                if ( basename( (string)$step['from'] ) !== (string)$step['from']
                     || !expLogFileLocator::isRotationName( basename( $file['path'] ), basename( $target ) ) )
                    $step['error'] = 'not_allowed';
                else switch ( $step['op'] )
                {
                    case 'rotate':
                        $result = expLogRotator::rotate( $file['path'], max( 1, (int)$limits['keep'] ),
                                                         array( 'compress' => (bool)$limits['compress'], 'minSize' => 0 ) );
                        $step['done'] = $result['ok'];
                        $step['error'] = $result['ok'] ? '' : $result['error'];
                        $step['renamed'] = $result['renamed'];
                        break;
                    case 'truncate':
                        $step['done'] = self::truncateLocked( $file['path'] );
                        $step['error'] = $step['done'] ? '' : 'locked';
                        break;
                    case 'delete':
                        if ( $target === $file['path'] || !is_file( $target ) || is_link( $target ) )
                            $step['error'] = is_file( $target ) ? 'not_allowed' : 'missing';
                        else
                        {
                            $step['done'] = @unlink( $target );
                            $step['error'] = $step['done'] ? '' : 'unlink';
                        }
                        break;
                    case 'compress':
                        if ( $target === $file['path'] || substr( $target, -3 ) === '.gz' || !is_file( $target ) || is_link( $target ) )
                            $step['error'] = is_file( $target ) ? 'not_allowed' : 'missing';
                        else
                        {
                            $step['done'] = expLogRotator::gzip( $target );
                            $step['error'] = $step['done'] ? '' : 'compress';
                        }
                        break;
                    default:
                        $step['error'] = 'unknown';
                }
            }
            if ( !$step['done'] && !in_array( $step['error'], array( 'missing', 'moved', 'skipped' ), true ) )
            {
                $ok = false;
                if ( $firstError === '' )
                    $firstError = $step['error'];
            }
            $out[] = $step;
        }
        return array( 'ok' => $ok, 'error' => $firstError, 'steps' => $out );
    }

    /**
     * Empties a log in place under the rotation lock (owner, mode and inode stay; writers append on).
     *
     * @param string $path
     * @return bool
     */
    protected static function truncateLocked( $path )
    {
        $handle = @fopen( $path, 'r+' );
        if ( $handle === false )
            return false;
        $done = false;
        if ( flock( $handle, LOCK_EX ) )
        {
            $done = ftruncate( $handle, 0 );
            flock( $handle, LOCK_UN );
        }
        fclose( $handle );
        return $done;
    }

    /** One log: plan + apply */
    protected function one( $key, $op, array $opts )
    {
        $plan = $this->plan( array( $key ), $op, $opts );
        if ( $plan['unknown'] )
            return array( 'ok' => false, 'error' => 'unknown', 'steps' => array() );
        if ( !$plan['steps'] )
            return array( 'ok' => true, 'error' => '', 'steps' => array() );
        return $this->apply( $plan );
    }

    /** Rotates one log now (an empty one is left). @return array ok, error, steps */
    public function rotate( $key )
    {
        return $this->one( $key, 'rotate', array( 'force' => true ) );
    }

    /** Removes its archives beyond the limits; $days instead of MaxAgeDays. @return array ok, error, steps */
    public function prune( $key, $days = null )
    {
        return $this->one( $key, 'prune', array( 'days' => $days ) );
    }

    /** Compresses its plain archives from .2 on. @return array ok, error, steps */
    public function compress( $key )
    {
        return $this->one( $key, 'compress', array() );
    }

    /** Empties a log: by rotating it (default), or in place. @return array ok, error, steps */
    public function truncate( $key, $archiveFirst = true )
    {
        return $this->one( $key, 'truncate', array( 'archive_first' => (bool)$archiveFirst ) );
    }
}
