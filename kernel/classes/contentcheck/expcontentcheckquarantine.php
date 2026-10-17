<?php
/**
 * File containing the expContentCheckQuarantine class.
 *
 * The actions of the storage check (doc/features/6.0/content-checks.md, "Quarantine"). Nothing is ever deleted from
 * the storage itself:
 *
 *   quarantine   moves orphan files to var/<site>/storage-quarantine/<date>/<their path below the storage>
 *   restore      moves a quarantined file back to its place (when nothing is there now)
 *   delete       deletes a file from the quarantine, after its own confirmation
 *   permissions  sets the expected owner and mode of a used file (nothing else)
 *
 * Each action is planned first (the dry run: what would happen to every file, nothing changed), the plan is shown
 * and confirmed with a token bound to exactly the files it allows, and every file is checked again when it is done:
 * a file that a content now uses, that is younger than MinAge, that is a link or lies outside the storage is never
 * moved. Every action done is recorded in the audit (content.file.orphan.quarantine, content.file.orphan.restore,
 * content.file.orphan.delete, content.file.permissions.fix). With a cluster file handler other than the file system
 * nothing can be done (report only).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentCheckQuarantine
{
    /** The actions */
    const ACTIONS = array( 'quarantine', 'restore', 'delete', 'permissions' );

    /** The policy function each action needs (besides contentcheck/storage) */
    const POLICIES = array( 'quarantine' => 'quarantine', 'restore' => 'quarantine', 'delete' => 'quarantine', 'permissions' => 'permissions' );

    /** The audit event of each action */
    const EVENTS = array( 'quarantine' => 'content.file.orphan.quarantine', 'restore' => 'content.file.orphan.restore',
                          'delete' => 'content.file.orphan.delete', 'permissions' => 'content.file.permissions.fix' );

    /** The file in each date directory of the quarantine that lists what was moved there */
    const MANIFEST = '.manifest.jsonl';

    /** @var int|null the effective user for tests */
    protected static $testUid = null;

    /** @var array|null the reference index for tests */
    protected static $testIndex = null;

    /**
     * At most this many files are taken from one confirmation.
     *
     * @return int
     */
    public static function batchLimit()
    {
        return expContentCheck::intSetting( 'StorageSettings', 'MaxActionBatch', 200, 1, 1000 );
    }

    /**
     * The quarantine directory: var/<site>/<QuarantineDirectory> (storage-quarantine).
     *
     * @return string
     */
    public static function root()
    {
        $name = expContentCheckStorage::cleanRelative( (string)expContentCheck::setting( 'StorageSettings', 'QuarantineDirectory', 'storage-quarantine' ) );
        return dirname( expContentCheckStorage::storageDirectory() ) . '/' . ( $name !== null && strpos( $name, '/' ) === false ? $name : 'storage-quarantine' );
    }

    /**
     * @param int|null $uid the effective user for tests; null: the process's
     */
    public static function setTestUid( $uid )
    {
        self::$testUid = $uid === null ? null : (int)$uid;
    }

    /**
     * @param array|null $index a reference index for tests
     */
    public static function setTestIndex( $index )
    {
        self::$testIndex = $index;
    }

    /**
     * @return int|null
     */
    protected static function uid()
    {
        if ( self::$testUid !== null )
            return self::$testUid;
        return function_exists( 'posix_geteuid' ) ? (int)posix_geteuid() : null;
    }

    /**
     * A fresh reference index, or null when none can be had here (then nothing can be quarantined).
     *
     * @return array|null
     */
    protected static function freshIndex()
    {
        if ( self::$testIndex !== null )
            return self::$testIndex;
        $index = expContentCheckReferences::index( true );
        return is_array( $index ) && empty( $index['failed'] ) ? $index : null;
    }

    /**
     * Whether a path lies inside a directory, links resolved.
     *
     * @param string $path an existing file
     * @param string $directory
     * @return bool
     */
    public static function isInside( $path, $directory )
    {
        $real = realpath( dirname( $path ) );
        $base = realpath( $directory );
        return $real !== false && $base !== false && strncmp( $real . '/', $base . '/', strlen( $base ) + 1 ) === 0;
    }

    /**
     * A quarantine item "<date>/<path below the storage>", checked.
     *
     * @param mixed $item
     * @return array|null array( 'date' => string, 'path' => string )
     */
    public static function parseItem( $item )
    {
        if ( !is_string( $item ) || !preg_match( '#^([0-9]{4}-[0-9]{2}-[0-9]{2})/(.+)$#', $item, $m ) )
            return null;
        $path = expContentCheckStorage::cleanRelative( $m[2] );
        return $path === null ? null : array( 'date' => $m[1], 'path' => $path );
    }

    /**
     * The posted list of an action: paths below the storage (quarantine, permissions) or quarantine items (restore,
     * delete); clean, unique, at most batchLimit() + 1 (one more tells that too many were ticked).
     *
     * @param string $action
     * @param mixed $list
     * @return string[]
     */
    public static function cleanList( $action, $list )
    {
        $clean = array();
        foreach ( is_array( $list ) ? $list : array() as $item )
        {
            if ( in_array( $action, array( 'restore', 'delete' ), true ) )
                $item = self::parseItem( $item ) ? $item : null;
            else
                $item = expContentCheckStorage::cleanRelative( $item );
            if ( $item !== null && !in_array( $item, $clean, true ) )
                $clean[] = $item;
            if ( count( $clean ) > self::batchLimit() )
                break;
        }
        return $clean;
    }

    // ------------------------------------------------------------------ plans (the dry run)

    /**
     * What an action would do with each file; changes nothing.
     *
     * @param string $action quarantine | restore | delete | permissions
     * @param string[] $list from cleanList()
     * @param int|null $now
     * @return array array( 'action', 'items' => list of array( 'item', 'path', 'size', 'mtime', 'allowed', 'reason',
     *               'detail' ), 'allowed' => string[], 'bytes' => int, 'too_many' => bool, 'fingerprint' => string )
     *               reason: '' | not_found | outside | link | ignored | recent | used | exists | no_index | cluster |
     *               needs_root | not_owner | fine
     */
    public static function plan( $action, array $list, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $limit = self::batchLimit();
        $tooMany = count( $list ) > $limit;
        $list = array_slice( $list, 0, $limit );
        $plan = array( 'action' => (string)$action, 'items' => array(), 'allowed' => array(), 'bytes' => 0, 'too_many' => $tooMany,
                       'fingerprint' => '' );
        if ( !in_array( $action, self::ACTIONS, true ) )
            return $plan;
        $storage = expContentCheckStorage::storageDirectory();
        $cluster = !expContentCheckStorage::canChangeFiles();

        $used = array();
        if ( !$cluster && in_array( $action, array( 'quarantine', 'permissions' ), true ) && $list )
        {
            $paths = array();
            foreach ( $list as $relative )
                $paths[] = $storage . '/' . $relative;
            $index = $action === 'quarantine' ? self::freshIndex() : null;
            if ( $action === 'quarantine' && $index === null )
                $used = false;
            else
                $used = expContentCheckStorage::usedAmong( $paths, $index );
        }
        foreach ( $list as $item )
        {
            if ( $cluster )
                $entry = self::refused( array( 'item' => $item, 'path' => $item ), 'cluster' );
            else if ( $action === 'quarantine' )
                $entry = self::planQuarantine( $item, $storage, $used, $now );
            else if ( $action === 'permissions' )
                $entry = self::planPermissions( $item, $storage, (array)$used );
            else
                $entry = self::planQuarantined( $action, $item, $storage );
            if ( $entry['allowed'] )
            {
                $plan['allowed'][] = $item;
                $plan['bytes'] += max( 0, (int)$entry['size'] );
            }
            $plan['items'][] = $entry;
        }
        $allowed = $plan['allowed'];
        sort( $allowed );
        $plan['fingerprint'] = 'storage|' . $action . '|' . hash( 'sha256', implode( "\n", $allowed ) );
        return $plan;
    }

    /**
     * @param array $entry
     * @param string $reason
     * @return array
     */
    protected static function refused( array $entry, $reason )
    {
        return array_merge( array( 'item' => '', 'path' => '', 'size' => -1, 'mtime' => 0, 'detail' => '' ), $entry,
                            array( 'allowed' => false, 'reason' => (string)$reason ) );
    }

    /**
     * @param string $relative
     * @param string $storage
     * @param array|false $used false: no reference index
     * @param int $now
     * @return array
     */
    protected static function planQuarantine( $relative, $storage, $used, $now )
    {
        $path = $storage . '/' . $relative;
        $entry = array( 'item' => $relative, 'path' => $relative, 'size' => -1, 'mtime' => 0, 'detail' => '' );
        $top = explode( '/', $relative );
        if ( !in_array( $top[0], expContentCheckStorage::scanDirectories(), true ) )
            return self::refused( $entry, 'outside' );
        if ( expContentCheckStorage::isIgnored( $relative ) )
            return self::refused( $entry, 'ignored' );
        if ( is_link( $path ) )
            return self::refused( $entry, 'link' );
        if ( !is_file( $path ) )
            return self::refused( $entry, 'not_found' );
        if ( !self::isInside( $path, $storage ) )
            return self::refused( $entry, 'outside' );
        $entry['size'] = (int)@filesize( $path );
        $entry['mtime'] = (int)@filemtime( $path );
        if ( $now - $entry['mtime'] < expContentCheckStorage::minAge() )
            return self::refused( $entry, 'recent' );
        if ( $used === false )
            return self::refused( $entry, 'no_index' );
        if ( isset( $used[$path] ) )
            return self::refused( $entry, 'used' );
        $target = self::root() . '/' . date( 'Y-m-d', $now ) . '/' . $relative;
        if ( file_exists( $target ) || is_link( $target ) )
            return self::refused( $entry, 'exists' );
        $entry['detail'] = substr( $target, strlen( dirname( $storage ) ) + 1 );
        $entry['allowed'] = true;
        $entry['reason'] = '';
        return $entry;
    }

    /**
     * @param string $action restore | delete
     * @param string $item <date>/<path>
     * @param string $storage
     * @return array
     */
    protected static function planQuarantined( $action, $item, $storage )
    {
        $parsed = self::parseItem( $item );
        $entry = array( 'item' => $item, 'path' => $parsed ? $parsed['path'] : $item, 'size' => -1, 'mtime' => 0, 'detail' => $parsed ? $parsed['date'] : '' );
        if ( !$parsed )
            return self::refused( $entry, 'not_found' );
        $root = self::root();
        $file = $root . '/' . $parsed['date'] . '/' . $parsed['path'];
        if ( is_link( $file ) )
            return self::refused( $entry, 'link' );
        if ( !is_file( $file ) )
            return self::refused( $entry, 'not_found' );
        if ( !self::isInside( $file, $root ) )
            return self::refused( $entry, 'outside' );
        $entry['size'] = (int)@filesize( $file );
        $entry['mtime'] = (int)@filemtime( $file );
        if ( $action === 'restore' )
        {
            $target = $storage . '/' . $parsed['path'];
            if ( file_exists( $target ) || is_link( $target ) )
                return self::refused( $entry, 'exists' );
            $top = explode( '/', $parsed['path'] );
            if ( !in_array( $top[0], expContentCheckStorage::scanDirectories(), true ) )
                return self::refused( $entry, 'outside' );
        }
        $entry['allowed'] = true;
        $entry['reason'] = '';
        return $entry;
    }

    /**
     * @param string $relative
     * @param string $storage
     * @param array $used
     * @return array
     */
    protected static function planPermissions( $relative, $storage, array $used )
    {
        $path = $storage . '/' . $relative;
        $entry = array( 'item' => $relative, 'path' => $relative, 'size' => -1, 'mtime' => 0, 'detail' => '' );
        if ( is_link( $path ) )
            return self::refused( $entry, 'link' );
        if ( !is_file( $path ) )
            return self::refused( $entry, 'not_found' );
        if ( !self::isInside( $path, $storage ) )
            return self::refused( $entry, 'outside' );
        if ( !isset( $used[$path] ) )
            return self::refused( $entry, 'not_used' );
        $stat = @stat( $path );
        $entry['size'] = (int)$stat['size'];
        $entry['mtime'] = (int)$stat['mtime'];
        $change = self::permissionChange( array( 'uid' => (int)$stat['uid'], 'mode' => (int)$stat['mode'] & 07777 ),
                                          expContentCheckStorage::expectedOwner(), expContentCheckStorage::expectedMode(), self::uid() );
        $entry['detail'] = $change['detail'];
        $entry['owner'] = $change['owner'];
        $entry['mode'] = $change['mode'];
        if ( $change['reason'] !== '' )
            return self::refused( $entry, $change['reason'] );
        $entry['allowed'] = true;
        $entry['reason'] = '';
        return $entry;
    }

    /**
     * What fixing the owner and mode of a file means (no file system access).
     *
     * @param array $stat array( 'uid' => int, 'mode' => int )
     * @param int|null $expectedOwner
     * @param int $expectedMode
     * @param int|null $uid the effective user of this process (null: unknown)
     * @return array array( 'owner' => int|null (to set), 'mode' => int|null (to set), 'reason' => '' | fine | needs_root |
     *               not_owner, 'detail' => text like "root 0644 -> alpha 0666" )
     */
    public static function permissionChange( array $stat, $expectedOwner, $expectedMode, $uid )
    {
        $owner = $expectedOwner !== null && (int)$stat['uid'] !== (int)$expectedOwner ? (int)$expectedOwner : null;
        $mode = ( (int)$stat['mode'] & (int)$expectedMode ) !== (int)$expectedMode ? (int)$expectedMode : null;
        $detail = expContentCheckStorage::userName( (int)$stat['uid'] ) . ' ' . sprintf( '%04o', (int)$stat['mode'] & 07777 ) . ' -> ' .
                  expContentCheckStorage::userName( $owner === null ? (int)$stat['uid'] : $owner ) . ' ' .
                  sprintf( '%04o', $mode === null ? (int)$stat['mode'] & 07777 : $mode );
        $reason = '';
        if ( $owner === null && $mode === null )
            $reason = 'fine';
        else if ( $owner !== null && $uid !== 0 )
            $reason = 'needs_root';
        else if ( $mode !== null && $uid !== 0 && $uid !== (int)$stat['uid'] )
            $reason = 'not_owner';
        return array( 'owner' => $owner, 'mode' => $mode, 'reason' => $reason, 'detail' => $detail );
    }

    // ------------------------------------------------------------------ doing it

    /**
     * Plans again and does the action when the plan is still the one confirmed (the same files, the same user).
     *
     * @param string $action
     * @param string[] $list
     * @param mixed $confirmToken
     * @param string $via web | cli
     * @return array array( 'done' => bool, 'reason' => '' | changed | nothing, 'count' => int, 'bytes' => int,
     *               'failed' => int, 'plan' => array )
     */
    public static function execute( $action, array $list, $confirmToken, $via = 'web' )
    {
        $plan = self::plan( $action, $list );
        $outcome = array( 'done' => false, 'reason' => '', 'count' => 0, 'bytes' => 0, 'failed' => 0, 'plan' => $plan, 'items' => array() );
        if ( !$plan['allowed'] )
            return array_merge( $outcome, array( 'reason' => 'nothing' ) );
        if ( $via !== 'cli' && !expContentCheck::matches( $confirmToken, expContentCheck::confirmToken( $plan['fingerprint'] ) ) )
            return array_merge( $outcome, array( 'reason' => 'changed' ) );
        return self::run( $plan, $via );
    }

    /**
     * Does what a plan allows (the plan must be fresh: execute() makes it).
     *
     * @param array $plan
     * @param string $via
     * @return array
     */
    protected static function run( array $plan, $via )
    {
        $storage = expContentCheckStorage::storageDirectory();
        $root = self::root();
        $now = time();
        $date = date( 'Y-m-d', $now );
        $dirMode = octdec( (string)eZINI::instance()->variable( 'FileSettings', 'StorageDirPermissions' ) );
        $outcome = array( 'done' => false, 'reason' => '', 'count' => 0, 'bytes' => 0, 'failed' => 0, 'plan' => $plan, 'items' => array() );
        $userID = $plan['action'] === 'quarantine' ? (int)eZUser::currentUserID() : 0;
        $manifest = array();
        foreach ( $plan['items'] as $entry )
        {
            if ( !$entry['allowed'] )
                continue;
            $ok = false;
            switch ( $plan['action'] )
            {
                case 'quarantine':
                    $from = $storage . '/' . $entry['item'];
                    $to = $root . '/' . $date . '/' . $entry['item'];
                    $ok = self::ensureDirectory( dirname( $to ), $dirMode ) && !file_exists( $to ) && @rename( $from, $to );
                    if ( $ok )
                        $manifest[] = array( 'path' => $entry['item'], 'size' => $entry['size'], 'mtime' => $entry['mtime'], 'moved' => $now, 'by' => $userID );
                    break;
                case 'restore':
                    $parsed = self::parseItem( $entry['item'] );
                    $from = $root . '/' . $parsed['date'] . '/' . $parsed['path'];
                    $to = $storage . '/' . $parsed['path'];
                    $ok = self::ensureDirectory( dirname( $to ), $dirMode ) && !file_exists( $to ) && @rename( $from, $to );
                    break;
                case 'delete':
                    $parsed = self::parseItem( $entry['item'] );
                    $file = $root . '/' . $parsed['date'] . '/' . $parsed['path'];
                    $ok = self::isInside( $file, $root ) && @unlink( $file );
                    if ( $ok )
                        self::removeEmptyDirectories( dirname( $file ), $root . '/' . $parsed['date'] );
                    break;
                case 'permissions':
                    $file = $storage . '/' . $entry['item'];
                    $ok = true;
                    if ( isset( $entry['owner'] ) && $entry['owner'] !== null )
                        $ok = @chown( $file, (int)$entry['owner'] );
                    if ( $ok && isset( $entry['mode'] ) && $entry['mode'] !== null )
                        $ok = @chmod( $file, eZFile::fileMode( (int)$entry['mode'] ) );
                    clearstatcache( true, $file );
                    break;
            }
            if ( $ok )
            {
                $outcome['count']++;
                $outcome['bytes'] += max( 0, (int)$entry['size'] );
                $outcome['items'][] = $entry['item'];
            }
            else
                $outcome['failed']++;
        }
        if ( $manifest )
            self::appendManifest( $root . '/' . $date, $manifest );
        $outcome['done'] = $outcome['count'] > 0;
        if ( $outcome['done'] )
            expContentCheckStorage::forget( $plan['action'], $outcome['items'] );
        self::audit( $plan['action'], $outcome, $via );
        return $outcome;
    }

    /**
     * @param string $directory
     * @param int $mode
     * @return bool
     */
    protected static function ensureDirectory( $directory, $mode )
    {
        if ( is_dir( $directory ) )
            return true;
        return eZDir::mkdir( $directory, $mode ? $mode : 0777, true ) && is_dir( $directory );
    }

    /**
     * Removes the empty directories from $directory up to (not including) $stop.
     *
     * @param string $directory
     * @param string $stop
     */
    protected static function removeEmptyDirectories( $directory, $stop )
    {
        while ( strlen( $directory ) > strlen( $stop ) && strncmp( $directory, $stop . '/', strlen( $stop ) + 1 ) === 0 )
        {
            $names = @scandir( $directory );
            if ( !is_array( $names ) || count( $names ) > 2 || !@rmdir( $directory ) )
                return;
            $directory = dirname( $directory );
        }
    }

    /**
     * @param string $directory a date directory of the quarantine
     * @param array $lines
     */
    protected static function appendManifest( $directory, array $lines )
    {
        $text = '';
        foreach ( $lines as $line )
            $text .= json_encode( $line, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";
        @file_put_contents( $directory . '/' . self::MANIFEST, $text, FILE_APPEND | LOCK_EX );
    }

    /**
     * Records an action in the audit: one event per action, with the number of files, their bytes and their paths.
     *
     * @param string $action
     * @param array $outcome
     * @param string $via
     */
    protected static function audit( $action, array $outcome, $via )
    {
        if ( !class_exists( 'expAudit' ) || !isset( self::EVENTS[$action] ) )
            return;
        expAudit::event( self::EVENTS[$action], array(
            'object' => array( 'type' => 'storage', 'id' => $action === 'quarantine' || $action === 'permissions' ? 'storage' : 'storage-quarantine' ),
            'after' => array( 'count' => $outcome['count'], 'bytes' => $outcome['bytes'], 'failed' => $outcome['failed'],
                              'files' => array_slice( $outcome['items'], 0, 200 ), 'via' => (string)$via ),
            'result' => $outcome['failed'] > 0 ? ( $outcome['count'] > 0 ? 'partial' : 'failure' ) : 'success' ) );
    }

    // ------------------------------------------------------------------ what is in the quarantine

    /**
     * The files in the quarantine, newest date first.
     *
     * @param int|null $max
     * @return array array( 'items' => list of array( 'item', 'date', 'path', 'size', 'mtime' ), 'count' => int, 'bytes' => int )
     */
    public static function listing( $max = null )
    {
        $max = $max === null ? expContentCheckStorage::maxListed() : (int)$max;
        $root = self::root();
        $result = array( 'items' => array(), 'count' => 0, 'bytes' => 0 );
        $dates = is_dir( $root ) ? @scandir( $root, SCANDIR_SORT_DESCENDING ) : array();
        foreach ( is_array( $dates ) ? $dates : array() as $date )
        {
            if ( !preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $date ) || !is_dir( $root . '/' . $date ) || is_link( $root . '/' . $date ) )
                continue;
            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root . '/' . $date, FilesystemIterator::SKIP_DOTS ) );
            $items = array();
            foreach ( $iterator as $file )
            {
                if ( !$file->isFile() || $file->isLink() || $file->getFilename() === self::MANIFEST )
                    continue;
                $relative = substr( $file->getPathname(), strlen( $root . '/' . $date ) + 1 );
                $result['count']++;
                $result['bytes'] += (int)$file->getSize();
                if ( count( $result['items'] ) + count( $items ) < $max )
                    $items[] = array( 'item' => $date . '/' . $relative, 'date' => $date, 'path' => $relative,
                                      'size' => (int)$file->getSize(), 'mtime' => (int)$file->getMTime() );
            }
            usort( $items, function ( $a, $b ) { return strcmp( $a['path'], $b['path'] ); } );
            $result['items'] = array_merge( $result['items'], $items );
        }
        return $result;
    }
}

?>
