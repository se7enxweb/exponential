<?php
/**
 * File containing the expCacheCatalogue class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The caches of Setup > Caches, described for a person: what each holds, its id and tags, its directory, its size
 * and number of files, when it was last cleared, which group it belongs to, whether clearing it reaches both servers
 * (Apache with PHP-FPM and Velocity share var/) and what else to do afterwards (restart Velocity, clear its response
 * cache), with the command that does the same from a shell.
 *
 * The catalogue only describes. It reads the cache list and the paths from expCacheManager (describeItem()), and
 * every clear stays expCacheManager's. fromSystem() reads this installation; the constructor takes described items
 * built by hand, so the grouping, the hints, the totals and the commands are tested without a database
 * (tests/tests/kernel/classes/expCacheCatalogueTest.php). Sizes are measured only when asked for, within a time
 * budget, as on Setup > System information.
 */
class expCacheCatalogue
{
    /** The groups, in the order the page shows them */
    const GROUPS = array( 'content', 'templates', 'settings', 'images', 'velocity', 'other' );

    /** Cache id => group; a cache not named here is grouped by its first tag, else "other" */
    protected static $groupOf = array(
        'content' => 'content', 'content_tree_menu' => 'content', 'classid' => 'content', 'sortkey' => 'content',
        'urlalias' => 'content', 'rss_cache' => 'content', 'user_info_cache' => 'content', 'state_limitations' => 'content',
        'content_language' => 'content', 'rest' => 'content',
        'template' => 'templates', 'template-block' => 'templates', 'template-override' => 'templates',
        'design_base' => 'templates', 'ezjscore-packer' => 'templates', 'texttoimage' => 'templates',
        'global_ini' => 'settings', 'ini' => 'settings', 'active_extensions' => 'settings', 'sslzones' => 'settings',
        'codepage' => 'settings', 'rest-routes' => 'settings', 'translation' => 'settings', 'chartrans' => 'settings',
        'imagealias' => 'images',
        'exphttpcache' => 'velocity', 'querycache' => 'velocity',
    );

    /** Tag => group, for caches of extensions */
    protected static $groupOfTag = array( 'content' => 'content', 'template' => 'templates', 'ini' => 'settings',
                                          'i18n' => 'settings', 'image' => 'images' );

    /** Cache id => what it holds */
    protected static $holds = array(
        'content'           => 'The rendered views of content: what a page shows of each object, per view mode and user.',
        'exphttpcache'      => 'Whole pages per permission context, answered before the kernel starts.',
        'querycache'        => 'Results of SQL queries, kept until a write touches their tables.',
        'global_ini'        => 'The settings files read once for every siteaccess.',
        'ini'               => 'The settings of each siteaccess, merged from every override.',
        'codepage'          => 'Character set conversion tables.',
        'classid'           => 'The identifiers of content classes and their attributes.',
        'sortkey'           => 'The sort keys of content classes.',
        'urlalias'          => 'URL wildcards and their translations.',
        'chartrans'         => 'Character transformation tables for URLs and search.',
        'imagealias'        => 'Image variations (aliases) made from uploaded images.',
        'template'          => 'Templates compiled to PHP.',
        'template-block'    => 'The cache-blocks of templates, such as the page layout\'s menus.',
        'template-override' => 'Which template answers which view, worked out from the override settings.',
        'texttoimage'       => 'Images made from text.',
        'rss_cache'         => 'Generated RSS feeds.',
        'user_info_cache'   => 'Each user\'s roles, policies and groups.',
        'content_tree_menu' => 'The content tree of the administration, as browsers keep it.',
        'state_limitations' => 'Object states used in policy limitations.',
        'content_language'  => 'The content languages of the site.',
        'design_base'       => 'Which design directories each siteaccess uses.',
        'active_extensions' => 'The list of active extensions.',
        'translation'       => 'Compiled translations of the interface.',
        'sslzones'          => 'The SSL zones of the site.',
        'rest'              => 'Answers of the REST interface.',
        'rest-routes'       => 'The routes of the REST interface.',
        'ezjscore-packer'   => 'Packed and minified scripts and style sheets.',
    );

    /** Caches a running Velocity keeps in memory as well: clearing the files is not enough for it */
    protected static $velocityRestart = array( 'global_ini', 'ini', 'active_extensions', 'sslzones' );

    /** Caches whose pages Velocity's response cache may still serve until its entries expire */
    protected static $responseCacheHint = array( 'content', 'template-block', 'template', 'template-override', 'design_base',
                                                 'exphttpcache', 'ezjscore-packer', 'urlalias' );

    /** @var array the described items */
    protected $items = array();
    /** @var array */
    protected $options;

    /**
     * @param array $described items as expCacheManager::describeItem() gives them (id, name, tags, enabled, how,
     *                         path, exists), plus files, bytes, complete when measured
     * @param array $options root (installation root, for paths), last_cleared (id => timestamp), measured (bool),
     *                       translate (bool), time (now)
     */
    public function __construct( array $described, array $options = array() )
    {
        $this->options = $options + array( 'root' => '', 'last_cleared' => array(), 'audit' => array(), 'audit_available' => false,
                                           'audit_who' => false, 'measured' => false, 'translate' => false, 'time' => time() );
        $this->options['translate'] = $this->options['translate'] && class_exists( 'ezpI18n' );
        foreach ( $described as $item )
            $this->items[] = $this->complete( $item );
    }

    /**
     * The catalogue of this installation.
     *
     * @param array $options sizes (bool: measure), budget (seconds, default 3), translate (bool)
     * @return expCacheCatalogue
     */
    public static function fromSystem( array $options = array() )
    {
        $options += array( 'sizes' => false, 'budget' => 3.0, 'translate' => false, 'cleared_now' => array() );
        $manager = new expCacheManager();
        $deadline = microtime( true ) + (float)$options['budget'];
        $described = array();
        $complete = true;
        foreach ( $manager->cacheList() as $item )
        {
            $desc = $manager->describeItem( $item, false );
            if ( $options['sizes'] && $desc['path'] !== null && $desc['exists'] )
            {
                if ( microtime( true ) > $deadline )
                    $complete = false;
                elseif ( strpos( $desc['how'], 'files ' ) === 0 )
                    $desc = $manager->describeItem( $item, true );
                else
                {
                    $size = expSystemReport::directorySize( $desc['path'], $deadline );
                    $desc += array( 'files' => (int)$size['files'], 'bytes' => (int)$size['bytes'], 'complete' => $size['complete'] );
                }
            }
            $described[] = $desc;
        }
        // The audit trail's clears (one index query, a bounded read of the newest file), for every cache
        $audit = array( 'available' => false, 'who' => false, 'records' => array() );
        $auditMap = array();
        if ( !empty( $options['audit'] ) )
        {
            $audit = self::auditRecords();
            $allIds = array();
            foreach ( $manager->cacheList() as $item )
                $allIds[] = $item['id'];
            $auditMap = self::lastClearedFromAudit( $audit['records'], $manager->tagMap(), $allIds );
        }
        return new self( $described, array(
            'root' => class_exists( 'eZSys' ) ? rtrim( (string)eZSys::rootDir(), '/' ) : '',
            'last_cleared' => self::lastCleared(),
            'audit' => $auditMap,
            'audit_available' => $audit['available'],
            'audit_who' => $audit['who'],
            'audit_file' => isset( $audit['file'] ) ? $audit['file'] : '',
            'audit_records' => count( $audit['records'] ) . '/' . ( isset( $audit['from_file'] ) ? $audit['from_file'] : 0 ),
            'cleared_now' => $options['cleared_now'],
            'measured' => (bool)$options['sizes'],
            'measured_all' => $complete,
            'translate' => $options['translate'],
        ) );
    }

    /**
     * When caches were last cleared, as far as the installation knows: the expiry timestamps the kernel sets for
     * the caches it expires instead of deleting.
     *
     * @return array id => timestamp
     */
    public static function lastCleared()
    {
        $map = array( 'content' => 'content-view-cache', 'template-block' => 'global-template-block-cache',
                      'user_info_cache' => 'user-info-cache', 'translation' => 'ts-translation-cache',
                      'imagealias' => 'image-manager-alias', 'content_tree_menu' => 'content-tree-menu' );
        $out = array();
        if ( !class_exists( 'eZExpiryHandler' ) )
            return $out;
        foreach ( $map as $id => $key )
        {
            $time = eZExpiryHandler::getTimestamp( $key, false );
            if ( $time )
                $out[$id] = (int)$time;
        }
        return $out;
    }

    /**
     * The newest system.cache.clear records of the audit trail, normalised: one query of the audit index (at most
     * $limit rows) and a bounded read of the newest system channel file from its end (at most $tailLines lines),
     * for the clears the indexer has not reached yet. Clears from the page, Setup > System information and the
     * command line (ezcache.php, exp:cache, exp:velocity deploy) are all recorded. Who cleared is only given to a
     * user who may read the system channel of the audit; never fails the page.
     *
     * @param int $limit
     * @param int $tailLines
     * @return array available (bool), who (bool: names included), records (see normaliseRecord())
     */
    public static function auditRecords( $limit = 200, $tailLines = 3000 )
    {
        $out = array( 'available' => false, 'who' => false, 'records' => array(), 'file' => '', 'from_file' => 0 );
        try
        {
            if ( !class_exists( 'expAuditConsole' ) || !class_exists( 'expAuditConfig' ) || !expAuditConsole::available() )
                return $out;
            $allowed = expAuditConsole::allowedChannels();
            $out['who'] = expAuditConsole::channelAllowed( 'system', $allowed );
            $seen = array();
            // what the indexer has not reached yet: the newest system file of every live directory, read from its end
            $config = expAuditConfig::get();
            $files = array();
            foreach ( self::auditLiveDirs( $config['logDir'] ) as $dir )
            {
                $reader = new expAuditReader( $dir );
                $channels = $reader->channels();
                if ( !isset( $channels['system']['newest'] ) )
                    continue;
                $files[] = expSystemReportMask::path( $reader->dir() . '/' . $channels['system']['newest'], class_exists( 'eZSys' ) ? eZSys::rootDir() : '' );
                $n = 0;
                foreach ( $reader->linesBackwards( $reader->dir() . '/' . $channels['system']['newest'] ) as $line )
                {
                    if ( ++$n > $tailLines )
                        break;
                    if ( strpos( $line, '"system.cache.clear"' ) === false )
                        continue;
                    $rec = json_decode( $line, true );
                    if ( is_array( $rec ) && ( $norm = self::normaliseRecord( $rec, '', $out['who'] ) ) && !isset( $seen[$norm['id']] ) )
                    {
                        $seen[$norm['id']] = true;
                        $out['records'][] = $norm;
                        $out['from_file']++;
                    }
                }
            }
            $out['file'] = implode( ', ', $files );
            // older clears: one query of the index
            if ( expAuditConsole::indexUsable() )
            {
                $query = new expAuditQuery();
                $rows = $query->fetch( expAuditQuery::normalise( array( 'name' => 'system.cache.clear' ) ), null, 0, $limit,
                                       'id, time_ms, login, record' );
                foreach ( (array)$rows as $row )
                {
                    $rec = json_decode( (string)$row['record'], true );
                    if ( !is_array( $rec ) )
                        continue;
                    $norm = self::normaliseRecord( $rec, isset( $row['login'] ) ? (string)$row['login'] : '', $out['who'] );
                    if ( $norm && !isset( $seen[$norm['id']] ) )
                    {
                        $seen[$norm['id']] = true;
                        $out['records'][] = $norm;
                    }
                }
            }
            $out['available'] = true;
        }
        catch ( Throwable $e )
        {
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeWarning( 'The cache clears of the audit could not be read: ' . $e->getMessage(), 'setup/cache' );
        }
        return $out;
    }

    /**
     * The directories audit records are written to now: the configured one (expAuditConfig), [AuditSettings] LogDir
     * under the request's var directory and under var/ itself. Records are split between them: a command without a
     * siteaccess writes under var/, a site under var/<site>/, and a persistent Velocity worker can hold either
     * from before its siteaccess was known. Only directories that exist are returned.
     *
     * @param string $configured the logDir of expAuditConfig::get()
     * @return string[]
     */
    public static function auditLiveDirs( $configured )
    {
        $dirs = array( rtrim( (string)$configured, '/' ) );
        if ( class_exists( 'eZSys' ) && class_exists( 'eZINI' ) )
        {
            $ini = eZINI::instance( 'audit.ini' );
            $logDir = $ini->hasVariable( 'AuditSettings', 'LogDir' ) ? trim( (string)$ini->variable( 'AuditSettings', 'LogDir' ) ) : 'log/audit';
            if ( $logDir !== '' && $logDir[0] !== '/' )
            {
                $root = rtrim( (string)eZSys::rootDir(), '/' );
                foreach ( array( (string)eZSys::varDirectory(), 'var' ) as $var )
                {
                    $var = rtrim( $var, '/' );
                    if ( $var === '' )
                        continue;
                    $dirs[] = ( $var[0] === '/' ? $var : $root . '/' . $var ) . '/' . rtrim( $logDir, '/' );
                }
            }
        }
        $out = array();
        foreach ( $dirs as $dir )
        {
            $real = realpath( $dir );
            if ( $real !== false && is_dir( $real ) && !in_array( $real, $out, true ) )
                $out[] = $real;
        }
        return $out;
    }

    /**
     * One system.cache.clear record as the catalogue uses it.
     *
     * @param array $rec the record (a line of a channel file, or the record column of the index)
     * @param string $login the login of the index row, when the record does not carry one
     * @param bool $who whether to keep who cleared
     * @return array|null id, time, how (id|tag|all|purge), asked (ids or tags), ids (cleared), who, shell
     */
    public static function normaliseRecord( array $rec, $login = '', $who = true )
    {
        if ( !isset( $rec['name'] ) || $rec['name'] !== 'system.cache.clear' || !isset( $rec['time'] ) )
            return null;
        $time = strtotime( (string)$rec['time'] );
        if ( !$time )
            return null;
        $actor = isset( $rec['actor'] ) && is_array( $rec['actor'] ) ? $rec['actor'] : array();
        $shell = isset( $actor['cli'] ) || ( isset( $rec['request']['engine'] ) && $rec['request']['engine'] === 'cli' );
        // a shell names its operating system user (its eZ user is the anonymous one the script runs as)
        if ( isset( $actor['cli']['os_user'] ) && (string)$actor['cli']['os_user'] !== '' )
            $name = 'os:' . $actor['cli']['os_user'];
        else
            $name = isset( $actor['login'] ) ? (string)$actor['login'] : (string)$login;
        $asked = isset( $rec['object']['id'] ) ? (string)$rec['object']['id'] : '';
        return array(
            'id' => isset( $rec['id'] ) ? (string)$rec['id'] : md5( json_encode( $rec ) ),
            'time' => $time,
            'how' => isset( $rec['object']['how'] ) ? (string)$rec['object']['how'] : '',
            'asked' => $asked === '' || $asked === 'all' ? array() : explode( ',', $asked ),
            'ids' => isset( $rec['after']['ids'] ) ? array_values( array_map( 'strval', (array)$rec['after']['ids'] ) ) : array(),
            'who' => $who ? $name : '',
            'shell' => $shell,
        );
    }

    /**
     * The newest clear of each cache from normalised records: the ids a record cleared (after.ids), else what it
     * asked for (ids, or tags through $tagMap), else every cache for a "clear all".
     *
     * @param array $records normaliseRecord() results, in any order
     * @param array $tagMap tag => ids
     * @param array $allIds every cache id
     * @return array id => array( time, who, shell )
     */
    public static function lastClearedFromAudit( array $records, array $tagMap, array $allIds )
    {
        $out = array();
        foreach ( $records as $r )
        {
            $ids = $r['ids'];
            if ( !$ids && $r['how'] === 'tag' )
                foreach ( $r['asked'] as $tag )
                    $ids = array_merge( $ids, isset( $tagMap[$tag] ) ? $tagMap[$tag] : array() );
            elseif ( !$ids && ( $r['how'] === 'id' || $r['how'] === 'purge' ) && $r['asked'] )
                $ids = $r['asked'];
            elseif ( !$ids && $r['how'] === 'all' )
                $ids = $allIds;
            foreach ( array_unique( $ids ) as $id )
                if ( !isset( $out[$id] ) || $out[$id]['time'] < $r['time'] )
                    $out[$id] = array( 'time' => $r['time'], 'who' => $r['who'], 'shell' => $r['shell'] );
        }
        return $out;
    }

    /**
     * A time as the page says it: "just now", "5 minutes ago", "3 hours ago", else the date.
     *
     * @param int $time
     * @return string
     */
    public function ago( $time )
    {
        $s = max( 0, (int)$this->options['time'] - (int)$time );
        if ( $s < 120 )
            return $this->t( 'just now' );
        if ( $s < 7200 )
            return $this->t( '%n minutes ago', array( '%n' => (int)floor( $s / 60 ) ) );
        if ( $s < 86400 )
            return $this->t( '%n hours ago', array( '%n' => (int)floor( $s / 3600 ) ) );
        return date( 'Y-m-d H:i', (int)$time );
    }

    protected function t( $text, array $params = array() )
    {
        if ( $this->options['translate'] )
            return ezpI18n::tr( 'design/admin/setup/cache', $text, null, $params );
        return strtr( $text, $params );
    }

    /**
     * The group of a cache.
     *
     * @param string $id
     * @param array $tags
     * @return string one of GROUPS
     */
    public static function groupOf( $id, array $tags = array() )
    {
        if ( isset( self::$groupOf[$id] ) )
            return self::$groupOf[$id];
        foreach ( $tags as $tag )
            if ( isset( self::$groupOfTag[$tag] ) )
                return self::$groupOfTag[$tag];
        return 'other';
    }

    /** Whether a running Velocity has to be restarted after the cache is cleared */
    public static function needsVelocityRestart( $id )
    {
        return in_array( $id, self::$velocityRestart, true );
    }

    /** Whether Velocity's response cache may still serve pages made from the cache */
    public static function needsResponseCacheClear( $id )
    {
        return in_array( $id, self::$responseCacheHint, true );
    }

    /**
     * The shell command that clears these caches.
     *
     * @param array $ids
     * @return string
     */
    public static function command( array $ids )
    {
        return $ids ? 'php bin/php/ezcache.php --clear-id=' . implode( ',', $ids ) . ' --allow-root-user' : '';
    }

    protected function complete( array $item )
    {
        $id = (string)$item['id'];
        $tags = array_values( (array)( isset( $item['tags'] ) ? $item['tags'] : array() ) );
        $path = isset( $item['path'] ) && $item['path'] !== null ? (string)$item['path'] : '';
        $measured = array_key_exists( 'bytes', $item );
        // the newer of the kernel's expiry timestamp and the audit trail's newest clear, with who cleared from the audit
        $last = isset( $this->options['last_cleared'][$id] ) ? (int)$this->options['last_cleared'][$id] : 0;
        $who = '';
        $shell = false;
        $source = $last ? 'expiry' : '';
        // the same clear: the expiry and the record's time are a moment apart, so a record up to five seconds
        // before the expiry names who cleared
        if ( isset( $this->options['audit'][$id] ) && (int)$this->options['audit'][$id]['time'] >= $last - 5 )
        {
            $last = max( $last, (int)$this->options['audit'][$id]['time'] );
            $who = (string)$this->options['audit'][$id]['who'];
            $source = 'audit';
            $shell = !empty( $this->options['audit'][$id]['shell'] );
        }
        // cleared by this very request: its audit record is written when the request ends
        if ( !empty( $this->options['cleared_now']['ids'] ) && in_array( $id, $this->options['cleared_now']['ids'], true ) )
        {
            $last = (int)$this->options['time'];
            $who = isset( $this->options['cleared_now']['who'] ) ? (string)$this->options['cleared_now']['who'] : '';
            $shell = false;
            $source = 'this request';
        }
        return array(
            'id' => $id,
            'name' => (string)$item['name'],
            'holds' => isset( self::$holds[$id] ) ? $this->t( self::$holds[$id] ) : '',
            'group' => self::groupOf( $id, $tags ),
            'tags' => $tags,
            'enabled' => !empty( $item['enabled'] ),
            'how' => isset( $item['how'] ) ? (string)$item['how'] : '',
            'path' => $path !== '' ? expSystemReportMask::path( $path, (string)$this->options['root'] ) : '',
            'exists' => !empty( $item['exists'] ),
            'measured' => $measured,
            'files' => $measured ? (int)$item['files'] : null,
            'bytes' => $measured ? (int)$item['bytes'] : null,
            'complete' => $measured ? !empty( $item['complete'] ) : true,
            'size_text' => $measured ? ( empty( $item['complete'] ) ? '≥ ' : '' ) . expSystemReport::size( (int)$item['bytes'] ) : '',
            'last_cleared' => $last,
            'last_cleared_text' => $last ? $this->ago( $last ) : '',
            'last_cleared_by' => $who,
            'last_cleared_shell' => $shell,
            'last_cleared_source' => $source,
            'last_audit' => isset( $this->options['audit'][$id]['time'] ) ? (int)$this->options['audit'][$id]['time'] : 0,
            'restart' => self::needsVelocityRestart( $id ),
            'response_cache' => self::needsResponseCacheClear( $id ),
            'command' => self::command( array( $id ) ),
            'search' => strtolower( implode( ' ', array_merge( array( $id, (string)$item['name'], $path ), $tags ) ) ),
        );
    }

    /**
     * Every cache, in the order of the cache list.
     *
     * @return array
     */
    public function items()
    {
        return $this->items;
    }

    /**
     * The ids of a group, or of every cache for "all"; disabled caches are left out, as they cannot be cleared.
     *
     * @param string $group
     * @return array
     */
    public function ids( $group = 'all' )
    {
        $ids = array();
        foreach ( $this->items as $item )
            if ( $item['enabled'] && ( $group === 'all' || $item['group'] === $group ) )
                $ids[] = $item['id'];
        return $ids;
    }

    /**
     * The groups in page order, each with its caches, totals, hints and command; empty groups are left out except
     * "velocity", which also holds the server's own caches on the page.
     *
     * @return array key => title, intro, items, ids, count, enabled, files, bytes, measured, size_text, restart,
     *               response_cache, command
     */
    public function groups()
    {
        $titles = array(
            'content'   => array( 'Content and views', 'Rendered views, URL aliases, class and user information.' ),
            'templates' => array( 'Templates', 'Compiled templates, template blocks, overrides and packed scripts.' ),
            'settings'  => array( 'INI and settings', 'Settings, extensions and translations. A running Velocity keeps settings in memory: restart it after clearing them.' ),
            'images'    => array( 'Images', 'Image variations, made again when an image is next shown.' ),
            'velocity'  => array( 'Pages, queries and the server', 'The HTTP cache, the SQL query cache, Velocity\'s response cache and the PHP caches of the server that answered.' ),
            'other'     => array( 'Other', 'Caches of extensions that belong to no other group.' ),
        );
        $out = array();
        foreach ( self::GROUPS as $key )
        {
            $group = array( 'key' => $key, 'title' => $this->t( $titles[$key][0] ), 'intro' => $this->t( $titles[$key][1] ),
                            'items' => array(), 'ids' => array(), 'count' => 0, 'enabled' => 0, 'files' => 0, 'bytes' => 0,
                            'measured' => false, 'complete' => true, 'restart' => false, 'response_cache' => false );
            foreach ( $this->items as $item )
            {
                if ( $item['group'] !== $key )
                    continue;
                $group['items'][] = $item;
                $group['count']++;
                if ( $item['enabled'] )
                {
                    $group['enabled']++;
                    $group['ids'][] = $item['id'];
                }
                if ( $item['measured'] )
                {
                    $group['measured'] = true;
                    $group['files'] += $item['files'];
                    $group['bytes'] += $item['bytes'];
                    $group['complete'] = $group['complete'] && $item['complete'];
                }
                $group['restart'] = $group['restart'] || $item['restart'];
                $group['response_cache'] = $group['response_cache'] || $item['response_cache'];
            }
            if ( !$group['count'] && $key !== 'velocity' )
                continue;
            $group['size_text'] = $group['measured'] ? ( $group['complete'] ? '' : '≥ ' ) . expSystemReport::size( $group['bytes'] ) : '';
            $group['command'] = self::command( $group['ids'] );
            $out[$key] = $group;
        }
        return $out;
    }

    /**
     * The overview: how many caches, enabled, groups, with directories, the total size and files when measured,
     * the latest known clear.
     *
     * @return array
     */
    public function overview()
    {
        $o = array( 'caches' => count( $this->items ), 'enabled' => 0, 'disabled' => 0, 'with_directory' => 0,
                    'groups' => count( $this->groups() ), 'measured' => (bool)$this->options['measured'],
                    'measured_all' => !isset( $this->options['measured_all'] ) || $this->options['measured_all'],
                    'files' => 0, 'bytes' => 0, 'complete' => true, 'last_cleared' => 0, 'restart' => array() );
        foreach ( $this->items as $item )
        {
            $item['enabled'] ? $o['enabled']++ : $o['disabled']++;
            if ( $item['path'] !== '' )
                $o['with_directory']++;
            if ( $item['measured'] )
            {
                $o['files'] += $item['files'];
                $o['bytes'] += $item['bytes'];
                $o['complete'] = $o['complete'] && $item['complete'];
            }
            $o['last_cleared'] = max( $o['last_cleared'], $item['last_cleared'] );
            if ( $item['restart'] )
                $o['restart'][] = $item['id'];
        }
        $o['complete'] = $o['complete'] && $o['measured_all'];
        $o['size_text'] = $o['measured'] ? ( $o['complete'] ? '' : '≥ ' ) . expSystemReport::size( $o['bytes'] ) : '';
        $o['last_cleared_text'] = $o['last_cleared'] ? $this->ago( $o['last_cleared'] ) : '';
        $o['audit'] = (bool)$this->options['audit_available'];
        $o['audit_who'] = (bool)$this->options['audit_who'];
        $o['audit_file'] = isset( $this->options['audit_file'] ) ? (string)$this->options['audit_file'] : '';
        $o['audit_records'] = isset( $this->options['audit_records'] ) ? (string)$this->options['audit_records'] : '';
        return $o;
    }

    /**
     * What a clear of these ids would reach, for the confirmation and the result: names, and whether Velocity
     * needs a restart or its response cache a clear afterwards.
     *
     * @param array $ids
     * @return array names, restart (bool), response_cache (bool), command
     */
    public function consequences( array $ids )
    {
        $out = array( 'names' => array(), 'ids' => array(), 'restart' => false, 'response_cache' => false, 'command' => self::command( $ids ) );
        foreach ( $this->items as $item )
        {
            if ( !in_array( $item['id'], $ids, true ) )
                continue;
            $out['names'][] = $item['name'];
            $out['ids'][] = $item['id'];
            $out['restart'] = $out['restart'] || $item['restart'];
            $out['response_cache'] = $out['response_cache'] || $item['response_cache'];
        }
        return $out;
    }
}
