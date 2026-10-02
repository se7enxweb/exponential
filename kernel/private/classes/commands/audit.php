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
                                                "  exp:audit verify [--channel=<c>] [--date=YYYY-MM-DD] [--json]\n" .
                                                "  exp:audit checkpoint",
                              'use-session' => false,
                              'use-modules' => false,
                              'use-extensions' => true ) );
        $this->options = $this->startup( '[channel:][name:][lines:][follow][json][date:]', '[action][id]',
                                         array( 'channel' => 'Only this channel (content, access, system, commerce, read)',
                                                'name' => 'tail: only names matching this pattern (access.*, content.node.move)',
                                                'lines' => 'tail: how many records (default 20)',
                                                'follow' => 'tail: keep printing new records as they are written (Ctrl-C ends)',
                                                'json' => 'Print records and results as JSON',
                                                'date' => 'verify: only that day\'s files (YYYY-MM-DD, UTC)' ) );
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
                default:
                    $this->error( "Unknown action '$action': status, channels, tail, show, verify, checkpoint (--help)" );
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
        return new \expAuditVerifier( $config['logDir'], new \expAuditKeys( $config ), $config['algorithm'] );
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
        if ( $this->options['json'] )
        {
            $this->output( json_encode( $results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            return $broken ? 1 : 0;
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
