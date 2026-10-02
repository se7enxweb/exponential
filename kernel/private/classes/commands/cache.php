<?php
/**
 * The code of bin/php/cache.php, moved into a class (#207 stage 1). The file bin/php/cache.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/cache.php:
 *
 *
 * File containing the cache.php script.
 *
 * @description Clear and inspect caches by tag, id or all: content, static, HTTP, Velocity, OPcache, APCu
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\Command\Kernel
{

class Cache extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'action', 'args', 'asJson', 'by', 'cacheArgv', 'chosen', 'cli', 'colours', 'd', 'data', 'describeItem', 'dryRun', 'emit', 'expiry', 'finish', 'group', 'groupHelp', 'h', 'ids', 'inv', 'item', 'items', 'k', 'lines', 'list', 'manager', 'name', 'names', 'needAction', 'options', 'overview', 'positional', 'purge', 'r', 's', 'script', 'select', 'site', 'tag', 'usage', 'wantsHelp' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        require_once 'kernel/classes/expcachemanager.php';

        // ── Help, one text per group, readable without a database ────────────────

        $groupHelp = array(
            'list' => array( 'list [--sizes]',
                "Every cache of the cache list (kernel and extensions): id, enabled, tags,\n" .
                "how it is cleared and where it lives. --sizes counts its files and bytes.\n\n" .
                "  exp:cache list\n  exp:cache list --sizes --json" ),
            'tags' => array( 'tags',
                "Every tag and the caches a clear by that tag reaches.\n\n  exp:cache tags" ),
            'status' => array( 'status',
                "Everything at a glance: the cache list, the static cache, the HTTP cache,\n" .
                "the SQL query cache, Velocity's response cache, precompressed static files,\n" .
                "and OPcache/APCu of this process.\n\n  exp:cache status --json" ),
            'clear' => array( 'clear --all | --tag=<t>[,<t>] | --id=<id>[,<id>] [--purge [--expiry=<when>]]',
                "Clears caches of the cache list, as Setup > Cache does:\n" .
                "  --all             every cache (\"Clear all caches\")\n" .
                "  --tag=content     every cache with that tag (\"Clear content caches\"; also\n" .
                "                    template, ini, i18n, image, user, codepage ...)\n" .
                "  --id=a,b          exactly these caches (\"Clear selected\")\n" .
                "  --purge           remove the files for real instead of expiring them\n" .
                "  --expiry=<when>   with --purge: only entries older than this ('-2 days')\n" .
                "  --iteration-sleep=<s> --iteration-max=<n>  pace a purge\n\n" .
                "INI: --tag=ini clears every configuration cache (the global INI cache in\n" .
                "var/cache/ini, the per-site INI cache, the active extensions list, the query\n" .
                "cache and the SSL zones), which is what the INI button does. --id=ini clears\n" .
                "only the per-site INI cache and leaves var/cache/ini in place, where the\n" .
                "merged settings are read from: after a settings change use --tag=ini.\n\n" .
                "  exp:cache clear --tag=ini --dry-run\n  exp:cache clear --id=template-override,template\n" .
                "  exp:cache clear --all" ),
            'content'  => array( 'content',  "Shortcut for clear --tag=content (\"Clear content caches\").\n\n  exp:cache content --dry-run" ),
            'template' => array( 'template', "Shortcut for clear --tag=template (\"Clear template caches\": compiled\ntemplates, override cache, template blocks, design base).\n\n  exp:cache template" ),
            'ini'      => array( 'ini',      "Shortcut for clear --tag=ini (\"Clear Ini caches\"; see clear --help about\n--id=ini).\n\n  exp:cache ini" ),
            'all'      => array( 'all',      "Shortcut for clear --all (\"Clear all caches\").\n\n  exp:cache all --dry-run" ),
            'imagealias' => array( 'imagealias [clear|purge]',
                "Image aliases. clear (the default) expires every alias, so each is made\n" .
                "again when next viewed; purge deletes the alias files of every image\n" .
                "attribute and keeps the originals.\n\n  exp:cache imagealias\n  exp:cache imagealias purge --dry-run" ),
            'static' => array( 'static status|regenerate|clear [--site=<siteaccess>] [--path=<p>[,<p>]] [--node=<id>[,<id>]]',
                "The static cache (html files the web server answers with):\n" .
                "  status              where pages are written and what each site holds\n" .
                "  regenerate          every page the site links to (\"Create new\"): its stored\n" .
                "                      pages are removed first unless --keep\n" .
                "  regenerate --path=/about-us   only these pages (relative to the site)\n" .
                "  regenerate --node=62          only the pages of these nodes\n" .
                "  clear [--path|--node]         remove stored pages (all of the site's by default)\n" .
                "  --site=<siteaccess>  one site; default every cacheable one\n" .
                "  --max-pages=<n> --max-depth=<n>  crawl limits for a full regeneration\n\n" .
                "  exp:cache static status\n  exp:cache static regenerate --site=site --dry-run\n" .
                "  exp:cache static regenerate --node=2\n  exp:cache static clear --site=site" ),
            'httpcache' => array( 'httpcache status|clear|purge|gc|reset-stats [--url=<u>] [--node=<id>] [--tag=<t>]',
                "The role-aware HTTP cache (httpcache.ini, exphttpcache):\n" .
                "  status             enabled, entries on disk, generation, hit rate\n" .
                "  clear              every page in every context (\"Clear HTTP cache\")\n" .
                "  purge --node=62    the pages of these nodes (tag l62)\n" .
                "  purge --url=/about-us   the page a url shows (resolved to its node)\n" .
                "  purge --tag=c17,pl2     pages carrying these tags\n" .
                "  gc                 remove expired and purged entries from disk\n" .
                "  reset-stats        start the hit/miss counters again\n\n" .
                "  exp:cache httpcache status\n  exp:cache httpcache purge --url=https://example.com/about-us --dry-run" ),
            'querycache' => array( 'querycache status|clear',
                "The SQL query cache (querycache.ini). clear starts a new generation: every\n" .
                "stored result is stale at once (\"Clear query cache\").\n\n  exp:cache querycache clear" ),
            'velocity' => array( 'velocity status|clear',
                "Velocity's response cache; the same functions as exp:velocity cache stats\n" .
                "and exp:velocity cache clear. clear touches the generation marker, so every\n" .
                "page stored before now is rendered again; no restart is needed.\n\n  exp:cache velocity status\n  exp:cache velocity clear" ),
            'precompress' => array( 'precompress status|clear',
                "Precompressed static files Velocity keeps (velocity.ini PrecompressStatic,\n" .
                "var/tmp/precompress). clear removes them; the server compresses a file again\n" .
                "on its next request. The engine has no rebuild of its own: files are built\n" .
                "on first request, and an edited file gets a new entry by itself.\n\n  exp:cache precompress status\n  exp:cache precompress clear --dry-run" ),
            'opcache' => array( 'opcache status|reset',
                "OPcache of THIS process. A command-line script has its own OPcache (if\n" .
                "opcache.enable_cli is on), so this never reaches a web server: reset the\n" .
                "web server's on Setup > Cache, or reload that server.\n\n  exp:cache opcache status" ),
            'apcu' => array( 'apcu status|clear',
                "APCu of THIS process; like opcache, a command-line script has its own.\n" .
                "Empty the web server's on Setup > Cache.\n\n  exp:cache apcu status" ),
        );

        $cacheArgv = array_slice( $_SERVER['argv'], 1 );
        $positional = array_values( array_filter( $cacheArgv, function ( $a ) { return $a === '' || $a[0] !== '-'; } ) );
        $wantsHelp = (bool)array_intersect( $cacheArgv, array( '--help', '-h' ) );
        $group = isset( $positional[0] ) ? strtolower( $positional[0] ) : '';
        if ( $group === 'help' )
        {
            $wantsHelp = true;
            $group = isset( $positional[1] ) ? strtolower( $positional[1] ) : '';
        }

        $overview = "Exponential cache control: every Setup > Cache action from the command line\n\n" .
                    "Usage: exp:cache <group> [action] [options]      (php bin/php/cache.php ...)\n\n" .
                    "Groups:\n";
        foreach ( $groupHelp as $name => $h )
            $overview .= sprintf( "  %-12s %s\n", $name, $h[0] );
        $overview .= "\nOptions for every group:\n" .
                     "  --dry-run      list what would be cleared, change nothing\n" .
                     "  --json         one JSON object: ok, message, items, dry_run, data\n" .
                     "  --siteaccess=<name>  the siteaccess whose settings and var directory are used\n" .
                     "  --allow-root-user    required when run as root, as for every script\n\n" .
                     "Output ends with PASS or FAIL. Exit codes: 0 done, 1 failed, 2 usage error.\n" .
                     "exp:cache <group> --help (or exp:cache help <group>) explains a group.";

        if ( $wantsHelp && isset( $groupHelp[$group] ) )
        {
            fwrite( STDOUT, "Usage: exp:cache " . $groupHelp[$group][0] . "\n\n" . $groupHelp[$group][1] . "\n\n"
                    . "Options: --dry-run, --json, --siteaccess=<name>, --allow-root-user\n" );
            exit( 0 );
        }

        $cli = $this->cli();
        $script = $this->script( array( 'description' => $overview,
                                             'use-session' => false,
                                             'use-modules' => false,
                                             'use-extensions' => true ) );
        $options = $this->startup(
            '[dry-run][json][all][tag:][id:][purge][expiry:][iteration-sleep:][iteration-max:][sizes]' .
            '[site:][path:][node:][url:][max-pages:][max-depth:][keep]',
            '[group][action]',
            array( 'dry-run' => 'List what would be cleared and change nothing',
                   'json' => 'Report as one JSON object',
                   'all' => 'clear: every cache',
                   'tag' => 'clear: caches with these tags (comma separated); httpcache purge: these HTTP cache tags',
                   'id' => 'clear: caches with these ids (comma separated)',
                   'purge' => 'clear, imagealias: remove the entries for real',
                   'expiry' => 'with --purge: only entries older than this, e.g. "-2 days"',
                   'iteration-sleep' => 'with --purge: seconds to sleep between iterations',
                   'iteration-max' => 'with --purge: entries per iteration',
                   'sizes' => 'list: count the files and bytes of each cache',
                   'site' => 'static: the siteaccess to generate or clear (default every cacheable one)',
                   'path' => 'static: pages relative to the site, comma separated',
                   'node' => 'static, httpcache purge: node ids, comma separated',
                   'url' => 'httpcache purge: urls, comma separated',
                   'max-pages' => 'static regenerate: stop after this many pages per site (default 2500)',
                   'max-depth' => 'static regenerate: follow links this many steps (default 12)',
                   'keep' => 'static regenerate: add to what is stored instead of replacing it' ) );

        $args = $options['arguments'];
        $group = isset( $args[0] ) ? strtolower( $args[0] ) : '';
        $action = isset( $args[1] ) ? strtolower( $args[1] ) : '';
        $dryRun = (bool)$options['dry-run'];
        $asJson = (bool)$options['json'];
        $list = function ( $name ) use ( $options ) { return \expCacheManager::splitList( $options[$name] ?? '' ); };

        // ── Output ───────────────────────────────────────────────────────────────

        $describeItem = function ( array $i )
        {
            if ( isset( $i['id'] ) )
            {
                $line = sprintf( '%-20s %-3s %-26s %s', $i['id'], $i['enabled'] ? 'on' : 'off',
                                 implode( ',', $i['tags'] ), $i['how'] );
                if ( $i['path'] !== null )
                    $line .= "\n" . str_repeat( ' ', 26 ) . $i['path'] . ( $i['exists'] ? '' : '  (does not exist)' );
                if ( isset( $i['files'] ) )
                    $line .= ' ' . ( $i['complete'] ? '' : '>' ) . $i['files'] . ' files, ' . \expCacheManager::bytes( $i['bytes'] );
                return $line;
            }
            $parts = array();
            foreach ( array( 'siteaccess', 'tag', 'path', 'what' ) as $k )
                if ( isset( $i[$k] ) && $i[$k] !== '' )
                    $parts[] = is_array( $i[$k] ) ? implode( ', ', $i[$k] ) : $i[$k];
            if ( isset( $i['files'] ) && is_int( $i['files'] ) )
                $parts[] = $i['files'] . ' files' . ( isset( $i['bytes'] ) ? ', ' . \expCacheManager::bytes( $i['bytes'] ) : '' );
            elseif ( isset( $i['files'] ) && is_array( $i['files'] ) )
                $parts[] = '-> ' . implode( ', ', $i['files'] );
            if ( !empty( $i['removed_first'] ) )
                $parts[] = 'removed first: ' . implode( ', ', $i['removed_first'] );
            return implode( '  ', $parts );
        };

        $finish = function ( array $result, array $extraLines = array() ) use ( $cli, $script, $asJson, $describeItem )
        {
            if ( $asJson )
            {
                fwrite( STDOUT, json_encode( $result, JSON_UNESCAPED_SLASHES ) . "\n" );
                $script->shutdown( $result['ok'] ? 0 : 1 );
            }
            foreach ( $extraLines as $line )
                $cli->output( $line );
            foreach ( $result['items'] as $item )
                $cli->output( '  ' . $describeItem( $item ) );
            // Colour only for a terminal: a log or a pipe gets plain PASS/FAIL.
            $tty = function_exists( 'posix_isatty' ) && @posix_isatty( STDOUT );
            $status = $result['ok'] ? ( $tty ? $cli->stylize( 'success', 'PASS' ) : 'PASS' )
                                    : ( $tty ? $cli->stylize( 'failure', 'FAIL' ) : 'FAIL' );
            $cli->output( ( $result['dry_run'] ? 'DRY RUN ' : '' ) . $status . '  ' . $result['message'] );
            $script->shutdown( $result['ok'] ? 0 : 1 );
        };

        $usage = function ( $message ) use ( $cli, $script, $asJson )
        {
            if ( $asJson )
                fwrite( STDOUT, json_encode( \expCacheManager::result( false, $message ) ) . "\n" );
            else
                $cli->error( $message . ' (exp:cache --help)' );
            $script->shutdown( 2 );
        };

        $needAction = function ( array $allowed, $default = null ) use ( &$action, $group, $usage )
        {
            if ( $action === '' && $default !== null )
                $action = $default;
            if ( !in_array( $action, $allowed, true ) )
                $usage( 'exp:cache ' . $group . ': ' . ( $action === '' ? 'no action' : 'unknown action "' . $action . '"' )
                        . '; use ' . implode( ', ', $allowed ) );
        };

        $manager = new \expCacheManager();

        // ── Groups ───────────────────────────────────────────────────────────────

        $this->runGroup();

        $script->shutdown( 0 );
    }

    /**
     * Part of run(), moved here unchanged (#207 stage 6); the script's global variables are bound as in run().
     */
    protected function runGroup()
    {
        foreach ( array( 'group', 'cli', 'overview', 'script', 'items', 'manager', 'item', 'options', 'finish', 'asJson', 'tag', 'ids', 'data', 'lines', 'k', 'purge', 'expiry', 'usage', 'chosen', 'by', 'names', 'list', 'dryRun', 'needAction', 'action', 'select', 'r', 'site', 'd', 'colours', 'emit', 'inv', 's', 'groupHelp' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        switch ( $group )
        {
            case '':
                $cli->output( $overview );
                $script->shutdown( 0 );
                break;

            case 'list':
            {
                $items = array();
                foreach ( $manager->cacheList() as $item )
                    $items[] = $manager->describeItem( $item, (bool)$options['sizes'] );
                $finish( \expCacheManager::result( true, count( $items ) . ' caches; clear them with exp:cache clear --id=<id> or --tag=<tag>', $items ),
                         $asJson ? array() : array( sprintf( '  %-20s %-3s %-26s %s', 'ID', '', 'TAGS', 'HOW IT IS CLEARED' ) ) );
                break;
            }

            case 'tags':
            {
                $items = array();
                foreach ( $manager->tagMap() as $tag => $ids )
                    $items[] = array( 'tag' => $tag, 'what' => implode( ', ', $ids ) );
                $finish( \expCacheManager::result( true, count( $items ) . ' tags; clear one with exp:cache clear --tag=<tag>', $items,
                                                  false, array( 'tags' => $manager->tagMap() ) ) );
                break;
            }

            case 'status':
            {
                $data = array(
                    'cache_list'  => count( $manager->cacheList() ),
                    'static'      => \expCacheManager::staticCacheStatus(),
                    'httpcache'   => \expCacheManager::httpCacheStatus(),
                    'querycache'  => \expCacheManager::queryCacheStatus(),
                    'velocity'    => \expCacheManager::velocityCacheStatus(),
                    'precompress' => \expCacheManager::precompressStatus(),
                    'php'         => \expCacheManager::phpCacheState(),
                );
                $lines = array();
                if ( !$asJson )
                {
                    $lines[] = sprintf( '  %-12s %d caches (exp:cache list)', 'cache list', $data['cache_list'] );
                    foreach ( array( 'static', 'httpcache', 'querycache', 'velocity', 'precompress' ) as $k )
                        $lines[] = sprintf( '  %-12s %s%s', $k, $data[$k]['ok'] ? '' : 'FAIL ', $data[$k]['message'] );
                    $lines[] = sprintf( '  %-12s OPcache %s, APCu %s (this process only)', 'php',
                                        $data['php']['opcache']['text'], $data['php']['apcu']['text'] );
                }
                $finish( \expCacheManager::result( true, 'status read', array(), false, $data ), $lines );
                break;
            }

            case 'clear':
            case 'content':
            case 'template':
            case 'ini':
            case 'all':
            {
                $purge = null;
                if ( $options['purge'] )
                {
                    $expiry = time();
                    if ( $options['expiry'] )
                    {
                        $expiry = strtotime( trim( $options['expiry'] ) );
                        if ( $expiry === false )
                            $usage( 'invalid date in --expiry: ' . $options['expiry'] );
                    }
                    $purge = array( 'expiry' => $expiry,
                                    'sleep' => $options['iteration-sleep'] ? (int)( $options['iteration-sleep'] * 1000000 ) : false,
                                    'max' => $options['iteration-max'] ? (int)$options['iteration-max'] : false );
                }
                elseif ( $options['expiry'] )
                    $usage( '--expiry can only be used together with --purge' );

                if ( $group === 'clear' )
                {
                    $chosen = (int)(bool)$options['all'] + (int)(bool)$options['tag'] + (int)(bool)$options['id'];
                    if ( $chosen !== 1 )
                        $usage( 'exp:cache clear needs exactly one of --all, --tag=<tag> or --id=<id>' );
                    $by = $options['all'] ? 'all' : ( $options['tag'] ? 'tag' : 'id' );
                    $names = $by === 'all' ? array() : $list( $by );
                }
                else
                {
                    $by = $group === 'all' ? 'all' : 'tag';
                    $names = $group === 'all' ? array() : array( $group );
                }
                $finish( $manager->clear( $by, $names, $dryRun, $purge ) );
                break;
            }

            case 'imagealias':
            {
                $needAction( array( 'clear', 'purge' ), 'clear' );
                $finish( $manager->clear( 'id', array( 'imagealias' ), $dryRun,
                                          $action === 'purge' ? array( 'expiry' => time() ) : null ) );
                break;
            }

            case 'static':
            {
                $needAction( array( 'status', 'regenerate', 'clear' ), 'status' );
                $select = array( 'siteaccess' => (string)$options['site'], 'paths' => $list( 'path' ), 'nodes' => $list( 'node' ) );
                if ( $action === 'status' )
                {
                    $r = \expCacheManager::staticCacheStatus();
                    $lines = array();
                    if ( !$asJson )
                    {
                        $lines[] = '  storage: ' . $r['data']['storage_dir'];
                        foreach ( $r['data']['sites'] as $site )
                        {
                            $lines[] = sprintf( '  %-18s %s', $site['name'], $site['url'] . $site['path'] );
                            foreach ( $site['directories'] as $d )
                                $lines[] = sprintf( '  %-18s %s  %d files, %s', '', $d['path'], $d['files'], \expCacheManager::bytes( $d['bytes'] ) );
                        }
                    }
                    $finish( $r, $lines );
                }
                if ( $action === 'clear' )
                    $finish( \expCacheManager::clearStaticCache( $select, $dryRun ) );

                $colours = array( 'phase' => 'cyan', 'phase-item' => 'white', 'ok' => 'green',
                                  'warn' => 'yellow', 'error' => 'red', 'info' => 'gray', 'done' => 'cyan' );
                $emit = $asJson ? null : function ( $type, $message, array $data = array() ) use ( $cli, $colours )
                {
                    $cli->output( $cli->stylize( isset( $colours[$type] ) ? $colours[$type] : 'default', $message ) );
                };
                $finish( \expCacheManager::regenerateStaticCache( $emit, $select + array(
                             'max_pages' => $options['max-pages'] ? (int)$options['max-pages'] : 2500,
                             'max_depth' => $options['max-depth'] !== null && $options['max-depth'] !== false ? (int)$options['max-depth'] : 12,
                             'purge' => !$options['keep'] ), $dryRun ) );
                break;
            }

            case 'httpcache':
            {
                $needAction( array( 'status', 'clear', 'purge', 'gc', 'reset-stats' ), 'status' );
                switch ( $action )
                {
                    case 'status':
                        $r = \expCacheManager::httpCacheStatus();
                        $lines = array();
                        if ( !$asJson && $r['data']['inventory'] )
                        {
                            $inv = $r['data']['inventory'];
                            $lines[] = '  dir: ' . $r['data']['dir'];
                            $lines[] = sprintf( '  %d entries (%s), %d user records, generation %d, %d purged tags, content changes purge %s',
                                                $inv['entries'], \expCacheManager::bytes( $inv['bytes'] ), $inv['records'],
                                                $inv['generation'], $inv['purged_tags'], $r['data']['purges'] );
                            $s = $r['data']['statistics'];
                            if ( $s )
                                $lines[] = sprintf( '  %d hits, %d misses, %d stores on %d servers', $s['hits'], $s['misses'], $s['stores'], $s['servers'] );
                        }
                        $finish( $r, $lines );
                    case 'clear':
                        $finish( \expCacheManager::clearHttpCache( $dryRun ) );
                    case 'gc':
                        $finish( \expCacheManager::httpCacheGC( $dryRun ) );
                    case 'reset-stats':
                        $finish( \expCacheManager::httpCacheResetStatistics( $dryRun ) );
                    case 'purge':
                        if ( $options['url'] )
                            $finish( \expCacheManager::purgeHttpCacheURLs( $list( 'url' ), $dryRun ) );
                        if ( $options['node'] )
                            $finish( \expCacheManager::purgeHttpCacheNodes( $list( 'node' ), $dryRun ) );
                        if ( $options['tag'] )
                            $finish( \expCacheManager::purgeHttpCacheTags( $list( 'tag' ), $dryRun ) );
                        $usage( 'exp:cache httpcache purge needs --url, --node or --tag (clear purges everything)' );
                }
                break;
            }

            case 'querycache':
                $needAction( array( 'status', 'clear' ), 'status' );
                $finish( $action === 'status' ? \expCacheManager::queryCacheStatus() : \expCacheManager::clearQueryCache( $dryRun ) );
                break;

            case 'velocity':
                $needAction( array( 'status', 'stats', 'clear' ), 'status' );
                $finish( $action === 'clear' ? \expCacheManager::clearVelocityCache( $dryRun ) : \expCacheManager::velocityCacheStatus() );
                break;

            case 'precompress':
                if ( $action === 'rebuild' )
                    $usage( 'exp:cache precompress: the engine has no rebuild; each file is compressed on its first request' );
                $needAction( array( 'status', 'clear' ), 'status' );
                $finish( $action === 'status' ? \expCacheManager::precompressStatus() : \expCacheManager::clearPrecompress( $dryRun ) );
                break;

            case 'opcache':
                $needAction( array( 'status', 'reset' ), 'status' );
                if ( $action === 'status' )
                {
                    $s = \expCacheManager::phpCacheState();
                    $finish( \expCacheManager::result( true, 'OPcache of this process: ' . $s['opcache']['text']
                                                            . ' (the web server has its own; reset it on Setup > Cache)', array(), false, $s['opcache'] ) );
                }
                $finish( \expCacheManager::resetOPcache( $dryRun ) );
                break;

            case 'apcu':
                $needAction( array( 'status', 'clear' ), 'status' );
                if ( $action === 'status' )
                {
                    $s = \expCacheManager::phpCacheState();
                    $finish( \expCacheManager::result( true, 'APCu of this process: ' . $s['apcu']['text']
                                                            . ' (the web server has its own; empty it on Setup > Cache)', array(), false, $s['apcu'] ) );
                }
                $finish( \expCacheManager::clearAPCu( $dryRun ) );
                break;

            default:
                $usage( 'unknown group "' . $group . '"; use one of ' . implode( ', ', array_keys( $groupHelp ) ) );
        }
    }
}

}
