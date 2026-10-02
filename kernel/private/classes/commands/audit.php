<?php
/**
 * The audit command, exp:audit: the records of the audit channels and their hash chains.
 * @description Audit log: tail, show one event, verify the hash chains, list the channels
 * Guide: doc/bc/6.0/audit.md ("The command: exp:audit", "Stage 2 — built")
 *
 *   exp:audit status                                   audit on/off, channels, today's records, chain state
 *   exp:audit channels                                 the channels: routing, files, sizes, chain head
 *   exp:audit tail [--channel=access] [--name=access.*] [--lines=20] [--follow] [--json]
 *   exp:audit show <event id> [--json]                 one record with its request siblings and children
 *   exp:audit verify [--channel=content] [--date=YYYY-MM-DD] [--json]
 *                                                      exit 0 intact, 1 broken, 2 error
 *   exp:audit checkpoint                               writes a signed checkpoint of every channel's head now
 *
 * Reading records is itself recorded (system.audit.read), as are verification (system.audit.verify) and every
 * break found (system.audit.chain.broken). Run as root, files it writes get the site user's owner and group.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Audit extends \Exponential\Runnable\Command
{
    /** @var array the parsed options */
    protected $options = array();

    public function run()
    {
        $this->script( array( 'description' => "Audit log: the records of the audit channels and their hash chains\n\n" .
                                                "  exp:audit status|channels\n" .
                                                "  exp:audit tail [--channel=<c>] [--name=<pattern>] [--lines=20] [--follow] [--json]\n" .
                                                "  exp:audit show <event id> [--json]\n" .
                                                "  exp:audit search [filters] [--files] [--limit=50] [--json]\n" .
                                                "  exp:audit verify [--channel=<c>] [--date=YYYY-MM-DD] [--archives] [--json]\n" .
                                                "  exp:audit rotate [--channel=<c>] [--dry-run]\n" .
                                                "  exp:audit archive [--channel=<c>] [--before=YYYY-MM-DD] [--format=gzip|bzip2|xz|zstd|zip] [--dry-run]\n" .
                                                "  exp:audit restore --channel=<c> --date=YYYY-MM-DD [--to=<dir>]\n" .
                                                "  exp:audit purge [--channel=<c>] [--dry-run]\n" .
                                                "  exp:audit reindex [--incremental] [--archives] [--channel=<c>]\n" .
                                                "  exp:audit pseudonymise [--dry-run]\n" .
                                                "  exp:audit export [filters] [--subject-user=<id>] [--format=jsonl|csv|bundle] --out=<path>\n" .
                                                "  exp:audit import [--dir=<old log dir>] [--file=login.log] [--dry-run] [--keep-originals]\n" .
                                                "  exp:audit key list|rotate|fingerprint [--pseudonym]\n" .
                                                "  exp:audit checkpoint\n" .
                                                "  exp:audit sinks list|test <name>|flush [<name>]\n" .
                                                "  exp:audit alerts list|test <rule> [--replay=YYYY-MM-DD]\n" .
                                                "  exp:audit cron [--daily]           one run of the cronjob part (--daily: the daily tasks now)\n\n" .
                                                "Filters (search, export): --name=<pattern> --user=<id> --login= --object=<type:id> --target=<type:id>\n" .
                                                "  --result=success|refused|failed --severity=<min> --request= --job= --run= --ip=<network>\n" .
                                                "  --from=YYYY-MM-DD[THH:MM] --to=... --q=<text> --legacy-file=<4.x file name> --channel=",
                              'use-session' => false,
                              'use-modules' => false,
                              'use-extensions' => true ) );
        $this->options = $this->startup( '[channel:][name:][lines:][follow][json][date:][archives][dry-run][before:][format:][to:][incremental]' .
                                         '[user:][login:][object:][target:][result:][severity:][request:][job:][run:][ip:][from:][q:]' .
                                         '[legacy-file:][limit:][files][out:][subject-user:][dir:][file:][keep-originals][pseudonym][replay:][daily]',
                                         '[action][id][extra]',
                                         array( 'channel' => 'Only this channel (content, access, system, commerce, read)',
                                                'name' => 'tail, search, export: only names matching this pattern (access.*, content.node.move)',
                                                'lines' => 'tail: how many records (default 20)',
                                                'follow' => 'tail: keep printing new records as they are written (Ctrl-C ends)',
                                                'json' => 'Print records and results as JSON',
                                                'date' => 'verify: only that day\'s files; restore: the archived day (YYYY-MM-DD, UTC)',
                                                'archives' => 'verify: also the archives (manifests, HMAC, checksums, the chain through them); reindex: also the archives',
                                                'dry-run' => 'rotate, archive, purge, import, pseudonymise: only say what would be done',
                                                'before' => 'archive: the days before this date (default: older than LiveDays)',
                                                'format' => 'archive: the format handler; export: jsonl, csv or bundle',
                                                'to' => 'restore: the directory (default <LogDir>/restored); search, export: up to this time',
                                                'incremental' => 'reindex: only what is new (default: rebuild)',
                                                'limit' => 'search: how many records (default 50); export: at most MaxExportRecords',
                                                'files' => 'search: read the files, not the index',
                                                'out' => 'export: the file (jsonl, csv) or directory (bundle)',
                                                'subject-user' => 'export: every record by or about this user id (an access request)',
                                                'dir' => 'import: the directory of the 4.x audit files (default: the audit LogDir)',
                                                'file' => 'import: only this 4.x file (login.log)',
                                                'keep-originals' => 'import: leave the 4.x files in place after archiving them',
                                                'pseudonym' => 'key rotate: replace the pseudonym key instead of the signing key',
                                                'replay' => 'alerts test: run the rule over the records since this date, without firing',
                                                'daily' => 'cron: run the daily tasks now' ) );
        $args = $this->options['arguments'];
        $action = isset( $args[0] ) ? $args[0] : 'status';
        if ( !class_exists( 'expAudit' ) || !class_exists( 'expAuditVerifier' ) )
        {
            $this->error( 'The audit classes are not in the autoload array: run php bin/php/ezpgenerateautoloads.php -k' );
            $this->shutdown( 2 );
        }
        try
        {
            switch ( $action )
            {
                case 'status':
                    $code = $this->status();
                    break;
                case 'channels':
                    $code = $this->channels();
                    break;
                case 'tail':
                    $code = $this->tail();
                    break;
                case 'show':
                    $code = $this->show( isset( $args[1] ) ? $args[1] : '' );
                    break;
                case 'verify':
                    $code = $this->verify();
                    break;
                case 'checkpoint':
                    $code = $this->checkpoint();
                    break;
                case 'search':
                    $code = $this->search();
                    break;
                case 'rotate':
                    $code = $this->rotate();
                    break;
                case 'archive':
                    $code = $this->archive();
                    break;
                case 'restore':
                    $code = $this->restore();
                    break;
                case 'purge':
                    $code = $this->purge();
                    break;
                case 'reindex':
                    $code = $this->reindex();
                    break;
                case 'pseudonymise':
                case 'pseudonymize':
                    $code = $this->pseudonymise();
                    break;
                case 'export':
                    $code = $this->export();
                    break;
                case 'import':
                    $code = $this->import();
                    break;
                case 'key':
                case 'keys':
                    $code = $this->key( isset( $args[1] ) ? $args[1] : 'list' );
                    break;
                case 'sinks':
                    $code = $this->sinks( isset( $args[1] ) ? $args[1] : 'list', isset( $args[2] ) ? $args[2] : null );
                    break;
                case 'alerts':
                    $code = $this->alerts( isset( $args[1] ) ? $args[1] : 'list', isset( $args[2] ) ? $args[2] : null );
                    break;
                case 'cron':
                    $code = $this->cron();
                    break;
                default:
                    $this->error( "Unknown action '$action': status, channels, tail, show, search, verify, rotate, archive, restore, purge, " .
                                  "reindex, pseudonymise, export, import, key, checkpoint, sinks, alerts, cron (--help)" );
                    $code = 2;
            }
        }
        catch ( \Throwable $e )
        {
            $this->error( get_class( $e ) . ': ' . $e->getMessage() );
            $code = 2;
        }
        $this->shutdown( $code );
    }

    /** @return array expAuditConfig::get() */
    protected function config()
    {
        return \expAuditConfig::get();
    }

    /** @return \expAuditReader */
    protected function reader()
    {
        return new \expAuditReader( $this->config()['logDir'] );
    }

    /** @return \expAuditVerifier */
    protected function verifier()
    {
        $config = $this->config();
        if ( class_exists( 'expAuditArchiver' ) )
            return $this->archiver()->verifier();
        return new \expAuditVerifier( $config['logDir'], new \expAuditKeys( $config ), $config['algorithm'] );
    }

    /** @return \expAuditArchiver */
    protected function archiver()
    {
        return new \expAuditArchiver( $this->config() );
    }

    /** @return string[] The channels to work on: --channel, or all configured */
    protected function channelList()
    {
        $c = $this->channelOption();
        return $c !== null ? array( $c ) : $this->config()['channels'];
    }

    /** @return bool --dry-run */
    protected function dryRun()
    {
        return !empty( $this->options['dry-run'] );
    }

    /** @return array The search filters from the options */
    protected function filterOptions()
    {
        $in = array();
        foreach ( array( 'channel', 'name', 'user', 'login', 'object', 'target', 'result', 'severity', 'request', 'job', 'run', 'ip',
                         'from', 'to', 'q', 'legacy-file', 'subject-user' ) as $k )
            if ( isset( $this->options[$k] ) && $this->options[$k] !== false && $this->options[$k] !== null && $this->options[$k] !== '' )
                $in[$k] = $this->options[$k];
        return \expAuditExporter::filters( $in );
    }

    /** Prints a result as JSON with --json, else nothing; returns whether it printed. */
    protected function json( $value )
    {
        if ( empty( $this->options['json'] ) )
            return false;
        $this->output( json_encode( $value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        return true;
    }

    /** @return string|null the --channel option */
    protected function channelOption()
    {
        $c = $this->options['channel'];
        if ( $c === null || $c === false || $c === '' )
            return null;
        if ( !preg_match( '/^[a-z][a-z0-9_]{0,31}$/', $c ) )
            throw new \InvalidArgumentException( "Malformed channel '$c'" );
        return $c;
    }

    /** @return string The installation-relative path */
    protected function rel( $path )
    {
        return \expAudit::relativePath( $path );
    }

    // ------------------------------------------------------------------ actions

    protected function status()
    {
        $config = $this->config();
        $reader = $this->reader();
        $channels = $reader->channels();
        $today = gmdate( 'Y-m-d' );
        $keys = new \expAuditKeys( $config );
        $known = $keys->keys( false );
        $out = array( 'audit' => $config['enabled'] ? 'enabled' : 'disabled', 'log_dir' => $this->rel( $config['logDir'] ),
                      'installation' => $known['installation'], 'signing_key' => $known['active'],
                      'fingerprint' => $known['active'] !== '' && isset( $known['signing'][$known['active']] ) ? \expAuditKeys::fingerprint( $known['signing'][$known['active']] ) : '',
                      'channels' => array() );
        $verifier = $this->verifier();
        foreach ( $config['channels'] as $c )
        {
            $files = isset( $channels[$c] ) ? $channels[$c]['files'] : array();
            $todayCount = 0;
            foreach ( $files as $f => $bytes )
                if ( strpos( $f, $c . '-' . $today ) === 0 )
                    $todayCount += $this->countLines( $config['logDir'] . '/' . $f );
            $v = $files ? $verifier->verifyChannel( $c ) : null;
            $out['channels'][$c] = array( 'files' => count( $files ), 'bytes' => array_sum( $files ), 'records_today' => $todayCount,
                                          'chain' => $v ? $v['result'] : 'empty', 'records' => $v ? $v['records'] : 0 );
        }
        \expAudit::event( 'system.audit.read', array( 'object' => array( 'type' => 'audit', 'id' => 'status' ), 'after' => array( 'via' => 'exp:audit status' ) ) );
        if ( $this->options['json'] )
        {
            $this->output( json_encode( $out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            return 0;
        }
        $this->output( "Audit:        {$out['audit']}" );
        $this->output( "Directory:    {$out['log_dir']}" );
        $this->output( 'Installation: ' . ( $out['installation'] ?: '(no keys yet: generated on the first event)' ) );
        if ( $out['signing_key'] )
            $this->output( "Signing key:  {$out['signing_key']}  fingerprint {$out['fingerprint']}" );
        $this->output( '' );
        $this->output( sprintf( '%-10s %6s %10s %8s  %s', 'Channel', 'Files', 'Bytes', 'Today', 'Chain' ) );
        foreach ( $out['channels'] as $c => $info )
            $this->output( sprintf( '%-10s %6d %10d %8d  %s (%d records)', $c, $info['files'], $info['bytes'], $info['records_today'], $info['chain'], $info['records'] ) );
        return 0;
    }

    protected function channels()
    {
        $config = $this->config();
        $channels = $this->reader()->channels();
        $writer = \expAudit::writerFor( $config );
        $rows = array();
        foreach ( array_unique( array_merge( $config['channels'], array_keys( $channels ) ) ) as $c )
        {
            $routes = array();
            foreach ( $config['routes'] as $pattern => $target )
                if ( $target === $c )
                    $routes[] = $pattern;
            if ( $config['defaultChannel'] === $c )
                $routes[] = '(default)';
            $info = isset( $channels[$c] ) ? $channels[$c] : array( 'files' => array(), 'bytes' => 0, 'newest' => null );
            $head = $info['newest'] ? $writer->tail( $config['logDir'] . '/' . $info['newest'] ) : null;
            $rows[$c] = array( 'configured' => in_array( $c, $config['channels'], true ), 'routes' => $routes,
                               'files' => count( $info['files'] ), 'bytes' => $info['bytes'], 'newest' => $info['newest'],
                               'max_file_size' => isset( $config['maxFileSize'][$c] ) ? $config['maxFileSize'][$c] : null,
                               'last_seq' => $head ? $head['seq'] : null, 'last_hash' => $head ? $head['hash'] : null );
        }
        if ( $this->options['json'] )
        {
            $this->output( json_encode( $rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            return 0;
        }
        foreach ( $rows as $c => $r )
        {
            $this->output( $c . ( $r['configured'] ? '' : '  (not in Channels[])' ) );
            $this->output( '  routes:  ' . ( $r['routes'] ? implode( ', ', $r['routes'] ) : '-' ) );
            $this->output( '  files:   ' . $r['files'] . ' (' . $r['bytes'] . ' bytes)' . ( $r['newest'] ? ', newest ' . $r['newest'] : '' ) );
            if ( $r['last_seq'] !== null )
                $this->output( '  head:    seq ' . $r['last_seq'] . ' ' . substr( $r['last_hash'], 0, 23 ) . '…' );
        }
        return 0;
    }

    protected function tail()
    {
        $config = $this->config();
        $channel = $this->channelOption();
        $name = $this->options['name'] ? (string)$this->options['name'] : null;
        $lines = $this->options['lines'] ? max( 1, (int)$this->options['lines'] ) : 20;
        $reader = $this->reader();
        $records = array_reverse( $reader->latest( $lines, $channel, $name ) );
        \expAudit::event( 'system.audit.read', array( 'object' => array( 'type' => 'audit', 'id' => 'tail' ),
                                                      'after' => array( 'via' => 'exp:audit tail', 'channel' => $channel, 'name' => $name,
                                                                        'count' => count( $records ), 'follow' => (bool)$this->options['follow'] ) ) );
        // the read is written at once; flush anything else of this command too, so --follow shows it
        \expAudit::flush();
        foreach ( $records as $r )
            $this->printRecord( $r );
        if ( !$this->options['follow'] )
            return 0;

        // follow: remember each file's size, print the lines added since
        $sizes = array();
        foreach ( $reader->channels() as $c => $info )
            foreach ( $info['files'] as $f => $bytes )
                $sizes[$f] = $bytes;
        while ( true )
        {
            usleep( 500000 );
            clearstatcache();
            foreach ( $reader->channels() as $c => $info )
            {
                if ( $channel !== null && $c !== $channel )
                    continue;
                foreach ( $info['files'] as $f => $bytes )
                {
                    $from = isset( $sizes[$f] ) ? $sizes[$f] : 0;
                    if ( $bytes <= $from )
                        continue;
                    $h = @fopen( $config['logDir'] . '/' . $f, 'rb' );
                    if ( !$h )
                        continue;
                    fseek( $h, $from );
                    $chunk = stream_get_contents( $h );
                    fclose( $h );
                    $end = strrpos( $chunk, "\n" );
                    if ( $end === false )
                        continue;
                    $sizes[$f] = $from + $end + 1;
                    foreach ( explode( "\n", substr( $chunk, 0, $end ) ) as $line )
                    {
                        $r = json_decode( $line, true );
                        if ( !is_array( $r ) || !isset( $r['name'] ) )
                            continue;
                        if ( $name !== null && \expAuditTaxonomy::match( $name, $r['name'] ) < 0 )
                            continue;
                        $r['file'] = $f;
                        $this->printRecord( $r );
                    }
                }
            }
        }
    }

    /**
     * One line per record: time (UTC), channel, name, actor, object, result, request id.
     */
    protected function printRecord( array $r )
    {
        if ( $this->options['json'] )
        {
            unset( $r['file'] );
            $this->output( \expAuditJson::encode( $r ) );
            return;
        }
        $time = isset( $r['time'] ) ? str_replace( array( 'T', 'Z' ), array( ' ', '' ), $r['time'] ) : '-';
        $this->output( sprintf( '%s  %-8s %-34s %-22s %-34s %-8s %s  %s',
            $time, isset( $r['channel'] ) ? $r['channel'] : '-', $r['name'], \expAuditReader::actorText( $r ), \expAuditReader::objectText( $r ),
            isset( $r['result'] ) ? $r['result'] : '-', isset( $r['request']['id'] ) ? $r['request']['id'] : '-',
            isset( $r['id'] ) ? $r['id'] : '' ) );
    }

    protected function show( $id )
    {
        $reader = $this->reader();
        $r = $reader->find( $id );
        \expAudit::event( 'system.audit.read', array( 'object' => array( 'type' => 'event', 'id' => (string)$id ),
                                                      'after' => array( 'via' => 'exp:audit show', 'found' => $r !== null ) ) );
        if ( $r === null )
        {
            $this->error( "No audit record with the id '$id' in the live files" );
            return 1;
        }
        $raw = $r['raw'];
        unset( $r['raw'] );
        $related = $reader->related( $r );
        if ( $this->options['json'] )
        {
            $this->output( $raw );
            return 0;
        }
        $file = $r['file'];
        $line = $r['line'];
        unset( $r['file'], $r['line'] );
        $this->output( "Event {$r['id']}   {$r['name']}   " . ( isset( $r['result'] ) ? $r['result'] : '' ) );
        $this->output( "  where      $file line $line, seq {$r['seq']}" );
        foreach ( array( 'time', 'channel', 'severity', 'verb', 'reason', 'parent', 'depth', 'job', 'run' ) as $k )
            if ( isset( $r[$k] ) )
                $this->output( sprintf( '  %-10s %s', $k, is_scalar( $r[$k] ) ? $r[$k] : json_encode( $r[$k] ) ) );
        foreach ( array( 'request', 'actor', 'object', 'target', 'before', 'after', 'error', 'x' ) as $k )
            if ( isset( $r[$k] ) )
                $this->output( sprintf( '  %-10s %s', $k, json_encode( $r[$k], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ) );
        $this->output( "  prev       {$r['prev']}" );
        $this->output( "  hash       {$r['hash']}" );
        $recomputed = \expAuditWriter::hashOf( \expAuditJson::decode( $raw ), strstr( $r['hash'], ':', true ) );
        $this->output( '  check      ' . ( $recomputed === $r['hash'] ? 'the hash matches the record' : 'THE HASH DOES NOT MATCH (recomputed ' . $recomputed . ')' ) );
        if ( $related['siblings'] )
        {
            $this->output( '  same request:' );
            foreach ( $related['siblings'] as $s )
                $this->output( "    {$s['time']}  {$s['name']}  {$s['id']}" );
        }
        if ( $related['children'] )
        {
            $this->output( '  children:' );
            foreach ( $related['children'] as $s )
                $this->output( "    {$s['time']}  {$s['name']}  {$s['id']}" );
        }
        return 0;
    }

    protected function verify()
    {
        $verifier = $this->verifier();
        $channel = $this->channelOption();
        $options = array();
        if ( $this->options['date'] )
        {
            if ( !preg_match( '/^\d{4}-\d{2}-\d{2}$/', $this->options['date'] ) )
                throw new \InvalidArgumentException( '--date must be YYYY-MM-DD' );
            $options['date'] = $this->options['date'];
        }
        $results = $channel !== null ? array( $channel => $verifier->verifyChannel( $channel, $options ) ) : $verifier->verifyAll( $options );
        $broken = false;
        foreach ( $results as $c => $res )
        {
            \expAudit::event( 'system.audit.verify', array( 'object' => array( 'type' => 'channel', 'id' => $c ),
                                                            'result' => $res['result'] === 'broken' ? 'failed' : 'success',
                                                            'after' => array( 'result' => $res['result'], 'records' => $res['records'], 'files' => $res['files'],
                                                                              'date' => isset( $options['date'] ) ? $options['date'] : null, 'via' => 'exp:audit verify' ) ) );
            if ( $res['result'] === 'broken' )
            {
                $broken = true;
                $first = $res['breaks'][0];
                \expAudit::event( 'system.audit.chain.broken', array( 'object' => array( 'type' => 'file', 'id' => $first['file'] ),
                                                                      'result' => 'failed', 'reason' => $first['kind'],
                                                                      'after' => array( 'channel' => $c, 'file' => $first['file'], 'line' => $first['line'],
                                                                                        'kind' => $first['kind'], 'breaks' => count( $res['breaks'] ) ) ) );
            }
        }
        $archives = array();
        if ( !empty( $this->options['archives'] ) && class_exists( 'expAuditArchiver' ) )
        {
            $archiver = $this->archiver();
            foreach ( $this->channelList() as $c )
            {
                $a = $archiver->verifyArchives( $c );
                if ( $a['result'] === 'empty' )
                    continue;
                $archives[$c] = $a;
                \expAudit::event( 'system.audit.verify', array( 'object' => array( 'type' => 'archive', 'id' => $c ),
                                                                'result' => $a['result'] === 'broken' ? 'failed' : 'success',
                                                                'after' => array( 'result' => $a['result'], 'manifests' => $a['manifests'], 'files' => $a['files'],
                                                                                  'records' => $a['records'], 'via' => 'exp:audit verify --archives' ) ) );
                if ( $a['result'] === 'broken' )
                {
                    $broken = true;
                    $first = $a['breaks'][0];
                    \expAudit::event( 'system.audit.chain.broken', array( 'object' => array( 'type' => 'archive', 'id' => $first['manifest'] ),
                                                                          'result' => 'failed', 'reason' => $first['kind'],
                                                                          'after' => array( 'channel' => $c, 'manifest' => $first['manifest'], 'file' => $first['file'],
                                                                                            'line' => $first['line'], 'kind' => $first['kind'], 'breaks' => count( $a['breaks'] ) ) ) );
                }
            }
        }
        if ( $this->options['json'] )
        {
            $this->output( json_encode( $archives ? array( 'live' => $results, 'archives' => $archives ) : $results,
                                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            return $broken ? 1 : 0;
        }
        foreach ( $archives as $c => $a )
        {
            $this->output( sprintf( '%-10s %-9s archives: %d manifests, %d files, %d records%s', $c, strtoupper( $a['result'] ), $a['manifests'],
                                    $a['files'], $a['records'], $a['first_time'] ? ', ' . $a['first_time'] . ' to ' . $a['last_time'] : '' ) );
            foreach ( array_slice( $a['breaks'], 0, 20 ) as $b )
                $this->output( "           break: {$b['manifest']}" . ( $b['file'] ? " {$b['file']}" : '' ) . ( $b['line'] ? " line {$b['line']}" : '' ) . ": {$b['kind']} ({$b['detail']})" );
        }
        if ( !$results )
            $this->output( 'No audit files yet in ' . $this->rel( $this->config()['logDir'] ) );
        foreach ( $results as $c => $res )
        {
            $span = $res['first_time'] ? ', ' . $res['first_time'] . ' to ' . $res['last_time'] : '';
            $this->output( sprintf( '%-10s %-9s %d records in %d file%s%s%s', $c, strtoupper( $res['result'] ), $res['records'], $res['files'],
                                    $res['files'] === 1 ? '' : 's', $span, $res['checkpoints'] ? ', ' . $res['checkpoints'] . ' checkpoint(s) matched' : '' ) );
            foreach ( array_slice( $res['breaks'], 0, 20 ) as $b )
                $this->output( "           break: {$b['file']} line {$b['line']}: {$b['kind']} ({$b['detail']})" . ( $b['id'] ? " id {$b['id']}" : '' ) );
            if ( count( $res['breaks'] ) > 20 )
                $this->output( '           ... ' . ( count( $res['breaks'] ) - 20 ) . ' more' );
            foreach ( $res['repairs'] as $rp )
                $this->output( "           repaired: {$rp['file']} line {$rp['line']} ({$rp['bytes']} torn bytes kept)" );
            foreach ( $res['notices'] as $n )
                $this->output( "           notice: {$n['file']}: {$n['kind']} ({$n['detail']})" );
        }
        return $broken ? 1 : 0;
    }

    protected function checkpoint()
    {
        $id = \expAudit::checkpoint();
        if ( $id === null )
        {
            $this->error( 'No checkpoint written (audit disabled, no channel files yet, or it could not be written: see error.log)' );
            return 2;
        }
        $this->output( "Checkpoint written: $id (system channel)" );
        return 0;
    }

    // ------------------------------------------------------------------ stage 5: search, archives, sinks, alerts

    protected function search()
    {
        $f = $this->filterOptions();
        $limit = $this->options['limit'] ? max( 1, min( 500, (int)$this->options['limit'] ) ) : 50;
        $records = null;
        $via = 'files';
        if ( empty( $this->options['files'] ) && class_exists( 'expAuditQuery' ) && class_exists( 'expAuditIndexSchema' )
             && \expAuditIndexSchema::isInstalled() )
        {
            try
            {
                $q = new \expAuditQuery();
                $records = array();
                foreach ( $q->fetch( \expAuditQuery::normalise( $f ), null, 0, $limit ) as $row )
                {
                    $r = isset( $row['record'] ) ? json_decode( $row['record'], true ) : null;
                    if ( is_array( $r ) )
                        $records[] = $r;
                }
                $via = 'index';
            }
            catch ( \Throwable $e )
            {
                $records = null;
            }
        }
        if ( $records === null )
            $records = ( new \expAuditExporter( $this->config() ) )->search( $f, $limit );
        \expAudit::event( 'system.audit.read', array( 'object' => array( 'type' => 'audit', 'id' => 'search' ),
                                                      'after' => array( 'via' => 'exp:audit search', 'source' => $via, 'filters' => (object)$f,
                                                                        'count' => count( $records ) ) ) );
        foreach ( $records as $r )
            $this->printRecord( $r );
        if ( empty( $this->options['json'] ) )
            $this->output( count( $records ) . ' record(s) from the ' . $via . ( count( $records ) >= $limit ? " (limit $limit: --limit=)" : '' ) );
        return 0;
    }

    protected function rotate()
    {
        $maintenance = new \expAuditMaintenance( function ( $line ) { $this->output( $line ); } );
        $closed = $maintenance->rotate( $this->dryRun(), $this->channelOption() );
        // rotation also runs retention (exp:audit purge alone does only that)
        $purged = array();
        foreach ( $this->channelList() as $c )
            $purged[$c] = $this->archiver()->purge( $c, $this->dryRun() );
        if ( $this->json( array( 'rotated' => $closed, 'retention' => $purged ) ) )
            return 0;
        foreach ( $this->channelList() as $c )
        {
            if ( isset( $closed[$c] ) )
                $this->output( sprintf( '%-10s %s %s (%d records)', $c, $this->dryRun() ? 'would close' : 'closed', $closed[$c]['file'], $closed[$c]['seq'] ) );
            else
                $this->output( sprintf( '%-10s nothing to close (today\'s file is the newest, or the newest is closed)', $c ) );
        }
        foreach ( $purged as $c => $p )
            if ( $p['purged'] )
                $this->output( sprintf( '%-10s retention %s %d archived day(s) before %s', $c, $this->dryRun() ? 'would remove' : 'removed',
                                        count( $p['purged'] ), $p['cutoff'] ) );
        return 0;
    }

    protected function archive()
    {
        $before = $this->options['before'] ? (string)$this->options['before'] : null;
        if ( $before !== null && !preg_match( '/^\d{4}-\d{2}-\d{2}$/', $before ) )
            throw new \InvalidArgumentException( '--before must be YYYY-MM-DD' );
        $format = $this->options['format'] ? (string)$this->options['format'] : null;
        if ( $format !== null && !\expAuditFormatRegistry::get( $format ) )
            throw new \InvalidArgumentException( "Unknown format '$format': " . implode( ', ', array_keys( \expAuditFormatRegistry::classes() ) ) );
        $archiver = $this->archiver();
        $out = array();
        $failed = false;
        foreach ( $this->channelList() as $c )
        {
            $out[$c] = $archiver->archive( $c, array( 'before' => $before, 'format' => $format, 'dryRun' => $this->dryRun() ) );
            foreach ( $out[$c] as $r )
                if ( !empty( $r['error'] ) )
                    $failed = true;
        }
        if ( $this->json( $out ) )
            return $failed ? 2 : 0;
        foreach ( $out as $c => $days )
        {
            if ( !$days )
                $this->output( sprintf( '%-10s nothing due', $c ) );
            foreach ( $days as $date => $r )
            {
                if ( !empty( $r['error'] ) )
                    $this->error( "$c $date: " . $r['error'] );
                elseif ( !empty( $r['dry_run'] ) )
                    $this->output( sprintf( '%-10s %s would be archived with %s: %s', $c, $date, $r['handler'], implode( ', ', $r['files'] ) ) );
                else
                    $this->output( sprintf( '%-10s %s archived with %s (%s, chain %s): %s', $c, $date, $r['handler'], $r['key_id'], $r['verification'],
                                            $this->rel( $r['manifest'] ) ) );
            }
        }
        return $failed ? 2 : 0;
    }

    protected function restore()
    {
        $c = $this->channelOption();
        $date = (string)$this->options['date'];
        if ( $c === null || !preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) )
            throw new \InvalidArgumentException( 'restore needs --channel= and --date=YYYY-MM-DD' );
        $r = $this->archiver()->restore( $c, $date, $this->options['to'] ? (string)$this->options['to'] : null );
        if ( $this->json( $r ) )
            return $r['error'] ? 2 : 0;
        if ( $r['error'] )
        {
            $this->error( $r['error'] );
            return 2;
        }
        foreach ( $r['files'] as $path => $same )
            $this->output( $this->rel( $path ) . ( $same ? '  (identical to the archived live file)' : '  DIFFERS from the manifest\'s sha256' ) );
        return in_array( false, $r['files'], true ) ? 1 : 0;
    }

    protected function purge()
    {
        $archiver = $this->archiver();
        $out = array();
        foreach ( $this->channelList() as $c )
            $out[$c] = $archiver->purge( $c, $this->dryRun() );
        $index = null;
        if ( !$this->dryRun() && class_exists( 'expAuditIndexer' ) && method_exists( 'expAuditIndexer', 'purgeOld' ) && \expAuditIndexSchema::isInstalled() )
        {
            try
            {
                $index = ( new \expAuditIndexer() )->purgeOld();
            }
            catch ( \Throwable $e )
            {
                $index = array( 'error' => $e->getMessage() );
            }
        }
        if ( $this->json( array( 'archives' => $out, 'index' => $index ) ) )
            return 0;
        foreach ( $out as $c => $p )
        {
            $this->output( sprintf( '%-10s %s %d archived day(s) before %s%s', $c, $this->dryRun() ? 'would remove' : 'removed', count( $p['purged'] ),
                                    $p['cutoff'], $p['purged'] ? ': ' . implode( ', ', $p['purged'] ) : '' ) );
            if ( $p['keys_needed'] )
                $this->output( '           signing keys still needed by retained archives: ' . implode( ', ', $p['keys_needed'] ) );
        }
        return 0;
    }

    protected function reindex()
    {
        if ( !class_exists( 'expAuditIndexer' ) || !class_exists( 'expAuditIndexSchema' ) )
        {
            $this->error( 'The audit index is not available in this installation (its classes are missing)' );
            return 2;
        }
        $indexer = new \expAuditIndexer();
        $indexer->setProgress( function ( $line ) { $this->output( $line ); } );
        $options = array( 'channel' => $this->channelOption(), 'archives' => !empty( $this->options['archives'] ), 'wait' => true );
        $r = !empty( $this->options['incremental'] ) ? $indexer->run( $options ) : $indexer->rebuild( $options );
        \expAudit::event( 'system.audit.reindex', array( 'object' => array( 'type' => 'index', 'id' => 'expaudit_event' ),
                                                         'result' => !empty( $r['ok'] ) ? 'success' : 'failed',
                                                         'after' => array( 'rows' => isset( $r['rows'] ) ? $r['rows'] : 0, 'ms' => isset( $r['ms'] ) ? $r['ms'] : 0,
                                                                           'incremental' => !empty( $this->options['incremental'] ) ) ) );
        if ( $this->json( $r ) )
            return !empty( $r['ok'] ) ? 0 : 2;
        $this->output( sprintf( 'Index: %d rows in %d ms%s', isset( $r['rows'] ) ? $r['rows'] : 0, isset( $r['ms'] ) ? $r['ms'] : 0,
                                !empty( $r['error'] ) ? ' (' . $r['error'] . ')' : '' ) );
        return !empty( $r['ok'] ) ? 0 : 2;
    }

    protected function pseudonymise()
    {
        if ( !class_exists( 'expAuditIndexer' ) || !method_exists( 'expAuditIndexer', 'pseudonymise' ) )
        {
            $this->error( 'The audit index is not available in this installation (its classes are missing)' );
            return 2;
        }
        $r = ( new \expAuditIndexer() )->pseudonymise( array( 'dryRun' => $this->dryRun() ) );
        if ( !$this->json( $r ) )
            $this->output( 'Pseudonymised: ' . json_encode( $r ) );
        return !empty( $r['error'] ) ? 2 : 0;
    }

    protected function export()
    {
        $out = (string)$this->options['out'];
        if ( $out === '' )
            throw new \InvalidArgumentException( 'export needs --out=<file> (jsonl, csv) or --out=<directory> (bundle)' );
        $format = $this->options['format'] ? strtolower( (string)$this->options['format'] ) : 'jsonl';
        if ( !in_array( $format, array( 'jsonl', 'csv', 'bundle' ), true ) )
            throw new \InvalidArgumentException( '--format must be jsonl, csv or bundle' );
        $f = $this->filterOptions();
        $max = 100000;
        if ( class_exists( 'expAuditIndexSettings' ) )
            $max = (int)\expAuditIndexSettings::get()['maxExport'] ?: $max;
        $limit = $this->options['limit'] ? min( $max, max( 1, (int)$this->options['limit'] ) ) : $max;
        $exporter = new \expAuditExporter( $this->config() );
        $records = $exporter->search( $f, $limit );
        $r = $exporter->export( $records, $format, $out, $f );
        \expAudit::event( 'system.audit.export', array( 'object' => array( 'type' => 'audit', 'id' => 'export' ),
                                                        'after' => array( 'filters' => (object)$f, 'format' => $format, 'count' => $r['count'],
                                                                          'sha256' => $r['sha256'], 'via' => 'exp:audit export' ) ) );
        if ( $this->json( $r ) )
            return 0;
        $this->output( sprintf( '%d record(s) exported as %s to %s (sha256 %s)%s', $r['count'], $format, $this->rel( $r['path'] ), $r['sha256'],
                                $r['manifest'] ? ', signed manifest ' . $this->rel( $r['manifest'] ) : '' ) );
        return 0;
    }

    protected function import()
    {
        $importer = new \expAuditImporter( $this->options['dir'] ? (string)$this->options['dir'] : null );
        $r = $importer->import( array( 'file' => $this->options['file'] ? (string)$this->options['file'] : null, 'dryRun' => $this->dryRun(),
                                       'keepOriginals' => !empty( $this->options['keep-originals'] ) ) );
        if ( $this->json( $r ) )
            return 0;
        if ( !$r['files'] )
            $this->output( 'No 4.x audit files found' );
        foreach ( $r['files'] as $path => $e )
            $this->output( sprintf( '%-40s %s', $this->rel( $path ), $e['skipped'] ? 'skipped: ' . $e['skipped']
                                    : sprintf( '%d entries, %d record(s) %s', $e['entries'], $e['records'], $this->dryRun() ? 'would be imported' : 'imported' ) ) );
        if ( !$this->dryRun() && $r['records'] )
            $this->output( 'Imported into ' . $this->rel( $importer->outDir() ) . ' (outside the chain, marked imported)' .
                           ( $r['archive'] ? '; originals archived: ' . $this->rel( $r['archive'] ) : '' ) );
        return 0;
    }

    protected function key( $sub )
    {
        $keys = new \expAuditKeys( $this->config() );
        switch ( $sub )
        {
            case 'list':
            case 'fingerprint':
                $list = $keys->listKeys();
                if ( $this->json( $list ) )
                    return 0;
                foreach ( $list as $k )
                    $this->output( sprintf( '%s  %s%s', $k['id'], $k['fingerprint'], $k['active'] ? '  (active)' : '' ) );
                return 0;
            case 'rotate':
                if ( !empty( $this->options['pseudonym'] ) )
                {
                    $r = $keys->rotatePseudonym();
                    if ( empty( $r['error'] ) )
                        \expAudit::event( 'system.audit.key.rotate', array( 'object' => array( 'type' => 'key', 'id' => 'pseudonym' ),
                                                                            'before' => array( 'fingerprint' => $r['old'] ), 'after' => array( 'fingerprint' => $r['new'], 'file' => $r['file'] ) ) );
                }
                else
                {
                    $r = $keys->rotate();
                    if ( empty( $r['error'] ) )
                        \expAudit::event( 'system.audit.key.rotate', array( 'object' => array( 'type' => 'key', 'id' => $r['new'] ),
                                                                            'before' => array( 'key_id' => $r['old'] ),
                                                                            'after' => array( 'key_id' => $r['new'], 'fingerprint' => $r['fingerprint'], 'file' => $r['file'] ) ) );
                }
                if ( empty( $r['error'] ) && class_exists( 'eZCache' ) )
                    \eZCache::clearByTag( 'ini' ); // other processes read the new active key from the settings
                if ( $this->json( $r ) )
                    return empty( $r['error'] ) ? 0 : 2;
                if ( !empty( $r['error'] ) )
                {
                    $this->error( $r['error'] );
                    return 2;
                }
                $this->output( !empty( $this->options['pseudonym'] )
                               ? "Pseudonym key replaced ({$r['old']} -> {$r['new']}); run exp:audit pseudonymise for the index"
                               : "Signing key rotated: {$r['old']} -> {$r['new']} (fingerprint {$r['fingerprint']}); the old key stays for its archives" );
                return 0;
        }
        $this->error( "Unknown key action '$sub': list, rotate [--pseudonym], fingerprint" );
        return 2;
    }

    protected function sinks( $sub, $name )
    {
        switch ( $sub )
        {
            case 'list':
                $s = \expAuditSinkRegistry::status();
                if ( $this->json( $s ) )
                    return 0;
                foreach ( $s as $n => $i )
                    $this->output( sprintf( '%-10s %-22s %s; spooled %d; channels %s%s', $n, $i['class'], $i['problem'] === '' ? 'ready' : 'not ready: ' . $i['problem'],
                                            $i['spooled'], $i['channels'] ? implode( ', ', $i['channels'] ) : '-',
                                            $i['last_delivery'] ? '; last delivery ' . $i['last_delivery'] : '' ) );
                return 0;
            case 'test':
                if ( $name === null )
                    throw new \InvalidArgumentException( 'sinks test <name>' );
                $r = \expAuditSinkRegistry::test( $name );
                if ( $this->json( $r ) )
                    return $r['delivered'] ? 0 : 1;
                $this->output( $r['delivered'] ? "Test record {$r['record']['id']} delivered through $name"
                                               : "Not delivered through $name: " . ( $r['error'] ?: $r['problem'] ) );
                return $r['delivered'] ? 0 : 1;
            case 'flush':
                $r = \expAuditSinkRegistry::deliverSpools( true, $name );
                if ( $this->json( $r ) )
                    return 0;
                if ( !$r )
                    $this->output( 'Nothing spooled' );
                foreach ( $r as $n => $i )
                    $this->output( sprintf( '%-10s %d delivered, %d waiting%s', $n, $i['delivered'], $i['remaining'],
                                            $i['failed'] ? ' (failed: ' . $i['error'] . ')' : ( $i['waiting'] ? ' (' . $i['waiting'] . ')' : '' ) ) );
                return 0;
        }
        $this->error( "Unknown sinks action '$sub': list, test <name>, flush [<name>]" );
        return 2;
    }

    protected function alerts( $sub, $rule )
    {
        switch ( $sub )
        {
            case 'list':
                $rules = \expAuditAlertEvaluator::rules();
                if ( $this->json( $rules ) )
                    return 0;
                $this->output( 'Alerts: ' . ( \expAuditAlertEvaluator::isEnabled() ? 'enabled' : 'disabled' ) . ', evaluated in ' .
                               implode( ', ', \expAuditConfig::lists( 'AuditAlertSettings', 'EvaluateIn', array( 'flush', 'cronjob' ) ) ) );
                foreach ( $rules as $n => $r )
                {
                    $c = $r['config'];
                    $this->output( sprintf( '%-24s %-9s %-34s %s%s -> %s%s', $n, $r['class_key'], $c['Event'],
                                            isset( $c['Threshold'] ) ? $c['Threshold'] . ' in ' . ( isset( $c['Window'] ) ? $c['Window'] : '?' ) . ' s by ' . ( isset( $c['GroupBy'] ) ? $c['GroupBy'] : '?' ) . ', ' : '',
                                            $c['Severity'], $c['Sinks'] ? implode( ',', $c['Sinks'] ) : 'file only',
                                            $r['problem'] !== '' ? '  BROKEN: ' . $r['problem'] : '' ) );
                }
                return 0;
            case 'test':
                if ( $rule === null )
                    throw new \InvalidArgumentException( 'alerts test <rule> [--replay=YYYY-MM-DD]' );
                $from = $this->options['replay'] ? (string)$this->options['replay'] : gmdate( 'Y-m-d' );
                $r = \expAuditAlertEvaluator::replay( $rule, $from );
                if ( $this->json( $r ) )
                    return 0;
                $this->output( sprintf( '%s over %d record(s) since %s: %d alert(s) would fire (nothing recorded)', $rule, $r['records'], $from, count( $r['alerts'] ) ) );
                foreach ( $r['alerts'] as $a )
                    $this->output( "  {$a['severity']}  group {$a['group']}  count {$a['count']}  {$a['message']}" );
                return 0;
        }
        $this->error( "Unknown alerts action '$sub': list, test <rule> [--replay=]" );
        return 2;
    }

    protected function cron()
    {
        $m = new \expAuditMaintenance( function ( $line ) { $this->output( $line ); } );
        $r = $m->run( array( 'daily' => !empty( $this->options['daily'] ) ) );
        if ( !$this->json( $r ) )
            $this->output( 'Audit cronjob run done' . ( $r['daily'] ? ' (with the daily tasks)' : '' ) . ( !empty( $r['error'] ) ? ': ' . $r['error'] : '' ) );
        return !empty( $r['error'] ) ? 2 : 0;
    }

    /** @return int Lines of a file */
    protected function countLines( $path )
    {
        $n = 0;
        $h = @fopen( $path, 'rb' );
        while ( $h && !feof( $h ) )
            $n += substr_count( (string)fread( $h, 65536 ), "\n" );
        if ( $h )
            fclose( $h );
        return $n;
    }
}

}
