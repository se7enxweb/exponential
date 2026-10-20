<?php
/**
 * Log management on the log viewer's own classes (expLogManagerInterface; doc/en/us/features/6.0/log-viewer.md, "Managing
 * logs"), used while the kernel's service expLogManager is not there (expLogManagerFactory).
 *
 * Every change is a plan first: plan() lists each step (delete, rename, copy, gzip, truncate) and changes nothing,
 * which is the dry run the page shows; apply() runs a plan under a lock per log, checking each step against the disk
 * again (a step whose file is gone or whose target exists is skipped, never forced). Only the logs of the allowed
 * directories are managed, by key; the web server's logs (configured by key) never are, and a step's files must be the
 * log's own file or its rotations, in its directory.
 *
 * Limits per key: logviewer.ini [RotationSettings] MaxSize[<key>], Keep[<key>], Compress[<key>], MaxAgeDays[<key>];
 * else site.ini [FileSettings] LogFileMaxSize, LogFileRotateCount, LogFileCompress, LogFileMaxAge; else 2 MB, 3, no, 0.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expLogManagerFallback implements expLogManagerInterface
{
    /** The defaults of the limits */
    const DEFAULTS = array( 'max_size' => 2097152, 'keep' => 3, 'compress' => false, 'max_age_days' => 0 );

    /** limit => array( [RotationSettings] variable, site.ini [FileSettings] variable ) */
    const SOURCES = array( 'max_size' => array( 'MaxSize', 'LogFileMaxSize' ), 'keep' => array( 'Keep', 'LogFileRotateCount' ),
                           'compress' => array( 'Compress', 'LogFileCompress' ), 'max_age_days' => array( 'MaxAgeDays', 'LogFileMaxAge' ) );

    /** @var expLogViewerConfig */
    protected $config;

    /** @var expLogFileLocator */
    protected $locator;

    /** @var int|null the time, set by tests */
    public $now = null;

    /** @var array|null site.ini [FileSettings] values, set by tests (null: read through eZINI) */
    public $siteFileSettings = null;

    /** @var string|null the lock directory; null: <cache dir>/logviewer/lock */
    public $lockDir = null;

    /** @var bool record the audit (tests switch it off) */
    public $audit = true;

    /** @var array|null this instance's logs() */
    protected $logs = null;

    public function __construct( ?expLogViewerConfig $config = null, ?expLogFileLocator $locator = null )
    {
        $this->config = $config !== null ? $config : expLogViewerConfig::instance();
        $this->locator = $locator !== null ? $locator : new expLogFileLocator( $this->config );
    }

    /** @return int */
    protected function now()
    {
        return $this->now !== null ? (int)$this->now : time();
    }

    // ---- reading ----

    public function logs()
    {
        if ( $this->logs !== null )
            return $this->logs;
        $current = array();
        $archives = array();
        foreach ( $this->locator->listFiles() as $list )
        {
            foreach ( $list as $info )
            {
                if ( $info['archive'] )
                    $archives[$info['key']][] = $info;
                else
                    $current[$info['key']] = $info;
            }
        }
        $root = $this->config->root();
        $out = array();
        foreach ( $current as $key => $info )
        {
            $file = $this->locator->resolve( $info['id'] );
            if ( $file === null )
                continue;
            $list = array();
            $bytes = 0;
            $newest = null;
            foreach ( isset( $archives[$key] ) ? $archives[$key] : array() as $a )
            {
                $list[] = array( 'id' => $a['id'], 'name' => $a['name'], 'rotation' => (int)$a['rotation'], 'size' => (int)$a['size'],
                                 'gz' => (bool)$a['gz'], 'mtime' => (int)$a['mtime'] );
                $bytes += (int)$a['size'];
                if ( (int)$a['rotation'] === 1 )
                    $newest = (int)$a['mtime'];
            }
            usort( $list, function ( $a, $b ) {
                $ra = $a['rotation'] < 0 ? PHP_INT_MAX : $a['rotation'];
                $rb = $b['rotation'] < 0 ? PHP_INT_MAX : $b['rotation'];
                return $ra === $rb ? strcmp( $a['name'], $b['name'] ) : ( $ra < $rb ? -1 : 1 );
            } );
            $growth = null;
            if ( $newest !== null && $this->now() > $newest )
                $growth = (int)round( (int)$file['size'] / max( 1, $this->now() - $newest ) * 3600 );
            $avg = expLogFileStats::averageLineLength( $file['path'], (int)$file['size'] );
            $dir = dirname( $file['path'] );
            $key = (string)$key;
            $out[$key] = array( 'key' => $key, 'id' => $file['id'], 'path' => $file['path'], 'name' => $file['name'],
                                'label' => isset( $file['label'] ) ? $file['label'] : $file['name'], 'group' => $file['group'],
                                'size' => (int)$file['size'], 'mtime' => (int)$file['mtime'],
                                'lines' => (int)round( (int)$file['size'] / max( 1.0, $avg ) ),
                                'archives' => $list, 'archive_bytes' => $bytes, 'growth' => $growth,
                                'limits' => $this->limits( $key ),
                                'writable' => is_writable( $file['path'] ) && is_writable( $dir ),
                                'owner' => self::ownerName( @fileowner( $file['path'] ) ), 'owner_group' => self::groupName( @filegroup( $file['path'] ) ),
                                'managed' => empty( $file['configured'] ) );
        }
        ksort( $out, SORT_STRING );
        return $this->logs = $out;
    }

    /** @return string the name of a user id, or the id */
    protected static function ownerName( $uid )
    {
        if ( $uid === false || $uid === null )
            return '';
        if ( function_exists( 'posix_getpwuid' ) && ( $pw = @posix_getpwuid( (int)$uid ) ) )
            return (string)$pw['name'];
        return (string)$uid;
    }

    /** @return string the name of a group id, or the id */
    protected static function groupName( $gid )
    {
        if ( $gid === false || $gid === null )
            return '';
        if ( function_exists( 'posix_getgrgid' ) && ( $gr = @posix_getgrgid( (int)$gid ) ) )
            return (string)$gr['name'];
        return (string)$gid;
    }

    /**
     * The limits of a log, each from the most specific place that sets it.
     *
     * @param string $key
     * @return array max_size, keep, compress, max_age_days, source
     */
    public function limits( $key )
    {
        $out = self::DEFAULTS;
        $out['source'] = array();
        $site = $this->siteFileSettings();
        foreach ( self::SOURCES as $limit => $names )
        {
            $perKey = $this->rotationSetting( $names[0] );
            if ( isset( $perKey[$key] ) && trim( (string)$perKey[$key] ) !== '' )
            {
                $out[$limit] = self::cast( $limit, $perKey[$key] );
                $out['source'][$limit] = array( 'file' => 'logviewer.ini', 'section' => 'RotationSettings', 'var' => $names[0] . '[' . $key . ']' );
            }
            else if ( isset( $site[$names[1]] ) && trim( (string)$site[$names[1]] ) !== '' )
            {
                $out[$limit] = self::cast( $limit, $site[$names[1]] );
                $out['source'][$limit] = array( 'file' => 'site.ini', 'section' => 'FileSettings', 'var' => $names[1] );
            }
            else
                $out['source'][$limit] = array( 'file' => '', 'section' => '', 'var' => 'default' );
        }
        return $out;
    }

    /** @return mixed a limit's value from a setting */
    protected static function cast( $limit, $value )
    {
        if ( $limit === 'compress' )
            return in_array( strtolower( trim( (string)$value ) ), array( 'enabled', 'true', '1', 'yes', 'on' ), true );
        $n = (int)$value;
        if ( $limit === 'max_size' )
            return max( 1024, $n );
        if ( $limit === 'keep' )
            return max( 1, min( 99, $n ) );
        return max( 0, $n );
    }

    /** @return array key => value of a [RotationSettings] variable (the configuration's, else logviewer.ini) */
    protected function rotationSetting( $name )
    {
        $value = $this->config->get( 'RotationSettings', $name );
        if ( $value === null && class_exists( 'eZINI' ) && class_exists( 'eZSys', false ) && eZINI::exists( 'logviewer.ini' ) )
        {
            $ini = eZINI::instance( 'logviewer.ini' );
            if ( $ini->hasVariable( 'RotationSettings', $name ) )
                $value = $ini->variable( 'RotationSettings', $name );
        }
        return is_array( $value ) ? $value : array();
    }

    /** @return array site.ini [FileSettings] */
    protected function siteFileSettings()
    {
        if ( $this->siteFileSettings !== null )
            return $this->siteFileSettings;
        $out = array();
        if ( class_exists( 'eZINI' ) && class_exists( 'eZSys', false ) )
        {
            $ini = eZINI::instance();
            foreach ( self::SOURCES as $names )
                if ( $ini->hasVariable( 'FileSettings', $names[1] ) )
                    $out[$names[1]] = $ini->variable( 'FileSettings', $names[1] );
        }
        return $this->siteFileSettings = $out;
    }

    public function effectiveSettings( $key )
    {
        $logs = $this->logs();
        return isset( $logs[$key] ) ? $logs[$key]['limits'] : null;
    }

    public function select( array $names )
    {
        $logs = $this->logs();
        $keys = array();
        $refused = array();
        foreach ( $names as $name )
        {
            $name = (string)$name;
            if ( isset( $logs[$name] ) )
            {
                if ( !$logs[$name]['managed'] )
                    $refused[$name] = 'webserver';
                else
                    $keys[] = $name;
                continue;
            }
            // a file id: the log it is the current file of
            $file = expLogFileLocator::validId( $name ) ? $this->locator->resolve( $name ) : null;
            if ( $file === null )
                $refused[$name] = preg_match( expLogFileLocator::KEY_PATTERN, $name ) ? 'unknown' : 'outside';
            else if ( $file['archive'] )
                $refused[$name] = 'archive';
            else if ( !empty( $file['configured'] ) )
                $refused[$name] = 'webserver';
            else if ( isset( $logs[$file['key']] ) )
                $keys[] = (string)$file['key'];
            else
                $refused[$name] = 'unknown';
        }
        return array( 'keys' => array_values( array_unique( $keys ) ), 'refused' => $refused );
    }

    // ---- planning ----

    public function plan( $keys, $op, array $opts = array() )
    {
        $logs = $this->logs();
        $unknown = array();
        if ( $keys === 'all' )
            $keys = array_keys( $logs );
        $steps = array();
        foreach ( (array)$keys as $key )
        {
            // array_keys() gives a key of digits only (404) as an integer
            $key = (string)$key;
            if ( !isset( $logs[$key] ) || !$logs[$key]['managed'] )
            {
                $unknown[] = (string)$key;
                continue;
            }
            $log = $logs[$key];
            switch ( $op )
            {
                case 'rotate':
                    if ( $log['size'] === 0 || ( empty( $opts['force'] ) && $log['size'] < $log['limits']['max_size'] ) )
                        break;
                    foreach ( $this->shiftSteps( $log ) as $s )
                        $steps[] = $s;
                    $steps[] = $this->step( $key, 'rename', $log['id'], $log['id'] . '.1', $log['size'] );
                    if ( $log['limits']['compress'] )
                        $steps[] = $this->step( $key, 'gzip', $log['id'] . '.1', $log['id'] . '.1.gz', $log['size'] );
                    break;
                case 'truncate':
                    if ( $log['size'] === 0 )
                        break;
                    if ( !isset( $opts['archive_first'] ) || $opts['archive_first'] )
                    {
                        foreach ( $this->shiftSteps( $log ) as $s )
                            $steps[] = $s;
                        $steps[] = $this->step( $key, 'copy', $log['id'], $log['id'] . '.1', $log['size'] );
                    }
                    $steps[] = $this->step( $key, 'truncate', $log['id'], '', $log['size'] );
                    break;
                case 'compress':
                    foreach ( $log['archives'] as $a )
                        if ( !$a['gz'] && $a['rotation'] > 0 && !$this->archiveExists( $log, $a['rotation'], true ) )
                            $steps[] = $this->step( $key, 'gzip', $a['id'], $a['id'] . '.gz', $a['size'] );
                    break;
                case 'prune':
                    $days = isset( $opts['days'] ) && $opts['days'] !== null && $opts['days'] !== '' ? max( 0, (int)$opts['days'] ) : (int)$log['limits']['max_age_days'];
                    foreach ( $log['archives'] as $a )
                    {
                        $old = $days > 0 && $a['mtime'] < $this->now() - $days * 86400;
                        $beyond = $a['rotation'] > $log['limits']['keep'] || $a['rotation'] < 0;
                        if ( $old || $beyond )
                            $steps[] = $this->step( $key, 'delete', $a['id'], '', $a['size'] );
                    }
                    break;
                default:
                    $unknown[] = (string)$key;
            }
        }
        return array( 'op' => (string)$op, 'steps' => $steps, 'unknown' => $unknown );
    }

    /** @return array a step */
    protected function step( $key, $op, $from, $to, $bytes )
    {
        return array( 'key' => (string)$key, 'op' => $op, 'from' => (string)$from, 'to' => (string)$to, 'bytes' => (int)$bytes );
    }

    /** @return bool whether the log has the archive .N (or .N.gz when $gz) */
    protected function archiveExists( array $log, $rotation, $gz )
    {
        foreach ( $log['archives'] as $a )
            if ( $a['rotation'] === (int)$rotation && $a['gz'] === (bool)$gz )
                return true;
        return false;
    }

    /**
     * The steps that make room for a new .1: the archives beyond Keep - 1 are removed, the others move one number up,
     * oldest first (a .N.gz moves as .N+1.gz).
     *
     * @param array $log
     * @return array
     */
    protected function shiftSteps( array $log )
    {
        $keep = (int)$log['limits']['keep'];
        $list = $log['archives'];
        usort( $list, function ( $a, $b ) { return $b['rotation'] - $a['rotation']; } );
        $steps = array();
        foreach ( $list as $a )
        {
            if ( $a['rotation'] < 1 )
                continue;
            if ( $a['rotation'] >= $keep )
                $steps[] = $this->step( $log['key'], 'delete', $a['id'], '', $a['size'] );
            else
                $steps[] = $this->step( $log['key'], 'rename', $a['id'], $log['id'] . '.' . ( $a['rotation'] + 1 ) . ( $a['gz'] ? '.gz' : '' ), $a['size'] );
        }
        return $steps;
    }

    // ---- applying ----

    public function apply( array $plan )
    {
        $steps = isset( $plan['steps'] ) && is_array( $plan['steps'] ) ? $plan['steps'] : array();
        $logs = $this->logs();
        $byKey = array();
        foreach ( $steps as $i => $s )
            $byKey[isset( $s['key'] ) ? (string)$s['key'] : ''][] = $i;
        $ok = true;
        $error = '';
        foreach ( $byKey as $key => $indexes )
        {
            $key = (string)$key;
            $lock = isset( $logs[$key] ) && $logs[$key]['managed'] ? $this->lock( $key ) : null;
            foreach ( $indexes as $i )
            {
                $s = $steps[$i];
                if ( !isset( $logs[$key] ) || !$logs[$key]['managed'] )
                    $result = 'not_allowed';
                else if ( $lock === false )
                    $result = 'locked';
                else
                    $result = $this->runStep( $logs[$key], $s );
                $steps[$i]['done'] = $result === '';
                $steps[$i]['error'] = $result;
                if ( $result !== '' )
                {
                    $ok = false;
                    if ( $error === '' )
                        $error = $result;
                }
                if ( $result === '' && $this->audit )
                    $this->auditStep( $logs[$key], $s );
            }
            if ( is_resource( $lock ) )
            {
                flock( $lock, LOCK_UN );
                fclose( $lock );
            }
        }
        $this->logs = null;
        clearstatcache();
        return array( 'ok' => $ok, 'error' => $error, 'steps' => $steps );
    }

    /**
     * Takes the lock of a log (a file per key in the lock directory), without waiting.
     *
     * @return resource|false|null null when no lock directory can be had (the step runs unlocked)
     */
    protected function lock( $key )
    {
        $dir = $this->lockDir !== null ? $this->lockDir : ( class_exists( 'eZSys', false ) ? eZSys::cacheDirectory() . '/logviewer/lock' : null );
        if ( $dir === null )
            return null;
        if ( !is_dir( $dir ) && !@mkdir( $dir, eZDir::dirMode( 0770 ), true ) && !is_dir( $dir ) )
            return null;
        $handle = @fopen( $dir . '/' . sha1( (string)$key ) . '.lock', 'c' );
        if ( $handle === false )
            return null;
        if ( !flock( $handle, LOCK_EX | LOCK_NB ) )
        {
            fclose( $handle );
            return false;
        }
        return $handle;
    }

    /**
     * The real path of a step's file id, when it is the log's file or one of its rotations in the log's directory.
     *
     * @param array $log
     * @param string $id
     * @return string|null
     */
    protected function stepPath( array $log, $id )
    {
        if ( !expLogFileLocator::validId( $id ) || dirname( $id ) !== dirname( $log['id'] ) )
            return null;
        $name = basename( $id );
        if ( !expLogFileLocator::isRotationName( $log['name'], $name ) )
            return null;
        return dirname( $log['path'] ) . '/' . $name;
    }

    /**
     * Runs one step, checking it against the disk.
     *
     * @return string '' or an error
     */
    protected function runStep( array $log, array $s )
    {
        $from = $this->stepPath( $log, isset( $s['from'] ) ? (string)$s['from'] : '' );
        if ( $from === null )
            return 'not_allowed';
        clearstatcache( true, $from );
        if ( !is_file( $from ) || is_link( $from ) )
            return 'missing';
        $to = null;
        if ( in_array( $s['op'], array( 'rename', 'copy', 'gzip' ), true ) )
        {
            $to = $this->stepPath( $log, isset( $s['to'] ) ? (string)$s['to'] : '' );
            if ( $to === null )
                return 'not_allowed';
            if ( file_exists( $to ) || is_link( $to ) )
                return 'moved';
        }
        switch ( $s['op'] )
        {
            case 'delete':
                if ( $from === $log['path'] )
                    return 'not_allowed';
                return @unlink( $from ) ? '' : 'rename';
            case 'rename':
                return @rename( $from, $to ) ? '' : 'rename';
            case 'copy':
                if ( !@copy( $from, $to ) )
                    return 'rename';
                @chmod( $to, eZFile::fileMode( @fileperms( $from ) & 0777 ) );
                return '';
            case 'gzip':
                if ( substr( $to, -3 ) !== '.gz' || substr( $from, -3 ) === '.gz' )
                    return 'not_allowed';
                return self::gzipFile( $from, $to ) ? '' : 'rename';
            case 'truncate':
                if ( $from !== $log['path'] )
                    return 'not_allowed';
                $handle = @fopen( $from, 'r+' );
                if ( $handle === false )
                    return 'rename';
                $done = ftruncate( $handle, 0 );
                fclose( $handle );
                return $done ? '' : 'rename';
        }
        return 'skipped';
    }

    /**
     * Compresses a file to <file>.gz as a stream, then removes the file (the mode is kept).
     *
     * @return bool
     */
    public static function gzipFile( $from, $to )
    {
        $in = @fopen( $from, 'rb' );
        if ( $in === false )
            return false;
        $tmp = $to . '.' . getmypid() . '.tmp';
        $out = @gzopen( $tmp, 'wb6' );
        if ( $out === false )
        {
            fclose( $in );
            return false;
        }
        while ( !feof( $in ) )
        {
            $block = fread( $in, 1048576 );
            if ( $block === false || ( $block !== '' && gzwrite( $out, $block ) === false ) )
            {
                fclose( $in );
                gzclose( $out );
                @unlink( $tmp );
                return false;
            }
        }
        fclose( $in );
        gzclose( $out );
        @chmod( $tmp, eZFile::fileMode( @fileperms( $from ) & 0777 ) );
        if ( !@rename( $tmp, $to ) )
        {
            @unlink( $tmp );
            return false;
        }
        @unlink( $from );
        return true;
    }

    /** Records a step that was done: rename, copy (rotate), gzip (rotate, compress), truncate, delete */
    protected function auditStep( array $log, array $s )
    {
        $actions = array( 'rename' => 'rotate', 'copy' => 'rotate', 'gzip' => 'rotate', 'truncate' => 'truncate', 'delete' => 'delete' );
        if ( !isset( $actions[$s['op']] ) )
            return;
        $after = array( 'step' => $s['op'], 'from' => $s['from'], 'to' => $s['to'], 'bytes' => (int)$s['bytes'] );
        if ( $s['op'] === 'gzip' )
            $after = array( 'action' => 'compress' ) + $after;
        expLogViewer::audit( $actions[$s['op']], array( 'id' => $s['from'], 'size' => (int)$s['bytes'] ), $after );
    }

    // ---- one log at a time ----

    public function rotate( $key )
    {
        return $this->one( $key, 'rotate', array( 'force' => true ) );
    }

    public function prune( $key, $days = null )
    {
        return $this->one( $key, 'prune', array( 'days' => $days ) );
    }

    public function compress( $key )
    {
        return $this->one( $key, 'compress', array() );
    }

    public function truncate( $key, $archiveFirst = true )
    {
        return $this->one( $key, 'truncate', array( 'archive_first' => (bool)$archiveFirst ) );
    }

    /** @return array ok, error, steps */
    protected function one( $key, $op, array $opts )
    {
        $logs = $this->logs();
        if ( !isset( $logs[$key] ) )
            return array( 'ok' => false, 'error' => 'unknown', 'steps' => array() );
        if ( !$logs[$key]['managed'] )
            return array( 'ok' => false, 'error' => 'not_allowed', 'steps' => array() );
        $plan = $this->plan( array( $key ), $op, $opts );
        if ( !$plan['steps'] )
            return array( 'ok' => false, 'error' => 'empty', 'steps' => array() );
        return $this->apply( $plan );
    }
}
