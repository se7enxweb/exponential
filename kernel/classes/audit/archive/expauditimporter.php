<?php
/**
 * Import of the 4.x text audit logs (doc/bc/6.0/audit.md, "Import of the old logs (Z4)"; exp:audit import).
 *
 * Reads each file named in [AuditSettings] AuditFileNames[] and its rotated copies (.3, .2, .1, then the file
 * itself: oldest first) in the old log directory, splits entries at lines starting with "[ " — the current form
 *
 *   [ Oct 02 2026 13:30:01 ][ admin ][ https://example.com/admin/user/login ] [203.0.113.7] [editor1:14]
 *   User id: 14
 *
 * and the older form without siteaccess and address — maps the file to its old event name and the name through
 * [AuditCompatSettings] Map[] (expAudit::legacyData(), the same mapping as eZAudit::writeAudit()), drops
 * NeverRecord[] and secret attributes, applies the privacy rules, and writes
 * <LogDir>/imported/<channel>-<YYYY-MM-DD>.jsonl with "imported": true and "source" (file, line, sha256 of the
 * original file). Imported records have no seq, prev or hash: they are outside the chain. Their ids are ULIDs of
 * the entry's time whose random part is derived from the source, so an entry always gets the same id.
 *
 * Re-running is safe: a file whose sha256 was imported before is skipped, and a file that has grown since (the
 * old writer still appended to it) is imported from where the last import stopped, when its start is unchanged.
 * The originals are then compressed into <ArchiveDir>/legacy/ with a signed manifest listing each file's sha256
 * (and removed from the old directory unless keepOriginals), and each import is recorded as system.audit.import.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditImporter
{
    /** @var array */
    protected $config;

    /** @var expAuditKeys */
    protected $keys;

    /** @var string The old log directory */
    protected $dir;

    /** @var string Where imported records go */
    protected $out;

    /** @var bool Record system.audit.import */
    public $record = true;

    /**
     * @param string|null $dir the old directory (default: the 4.x LogDir, which is the same var/<site>/log/audit)
     * @param string|null $out the import directory (default <LogDir>/imported)
     * @param array|null $config
     */
    public function __construct( $dir = null, $out = null, ?array $config = null )
    {
        $this->config = $config ?: expAuditConfig::get();
        $this->keys = new expAuditKeys( $this->config );
        $this->dir = rtrim( $dir !== null ? $dir : $this->config['logDir'], '/' );
        $this->out = rtrim( $out !== null ? $out : $this->config['logDir'] . '/imported', '/' );
    }

    /** @return string */
    public function outDir()
    {
        return $this->out;
    }

    /**
     * The old files to import, oldest first, with their old event name.
     *
     * @param string|null $only one file name (login.log)
     * @return array path => old name
     */
    public function files( $only = null )
    {
        $map = $this->config['auditFileNames'];
        if ( !$map )
            $map = array( 'user-login' => 'login.log', 'user-failed-login' => 'failed_login.log', 'content-delete' => 'content_delete.log',
                          'content-move' => 'content_move.log', 'content-hide' => 'content_hide.log', 'role-change' => 'role_change.log',
                          'role-assign' => 'role_assign.log', 'section-assign' => 'section_assign.log', 'state-assign' => 'state_assign.log',
                          'order-delete' => 'order_delete.log' );
        $out = array();
        foreach ( $map as $old => $file )
        {
            $file = basename( (string)$file );
            if ( $file === '' || ( $only !== null && $file !== basename( $only ) && basename( $only ) !== $file ) )
                continue;
            for ( $i = 9; $i >= 1; $i-- )
                if ( is_file( $this->dir . '/' . $file . '.' . $i ) )
                    $out[$this->dir . '/' . $file . '.' . $i] = $old;
            if ( is_file( $this->dir . '/' . $file ) )
                $out[$this->dir . '/' . $file] = $old;
        }
        return $out;
    }

    /**
     * Imports the old files.
     *
     * @param array $options file (one file name), dryRun, keepOriginals
     * @return array files: path => array( sha256, entries, records, skipped (reason) ), records (total), archive (manifest path)
     */
    public function import( array $options = array() )
    {
        $dryRun = !empty( $options['dryRun'] );
        $result = array( 'files' => array(), 'records' => 0, 'archive' => null, 'dry_run' => $dryRun );
        $ledger = $this->ledger();
        $done = array();
        foreach ( $this->files( isset( $options['file'] ) ? $options['file'] : null ) as $path => $oldName )
        {
            $sha = hash_file( 'sha256', $path );
            $name = basename( $path );
            $entry = array( 'sha256' => $sha, 'entries' => 0, 'records' => 0, 'skipped' => null, 'old_name' => $oldName );
            $from = 0;
            $prior = isset( $ledger[$name] ) ? $ledger[$name] : null;
            if ( $prior && $prior['sha256'] === $sha )
            {
                $entry['skipped'] = 'imported before (same sha256)';
                $result['files'][$path] = $entry;
                continue;
            }
            if ( $prior && filesize( $path ) > $prior['bytes'] && self::prefixHash( $path, $prior['bytes'] ) === $prior['sha256'] )
                $from = (int)$prior['bytes']; // grown since: only what was added
            $records = $this->parse( $path, $oldName, $sha, $from );
            $entry['entries'] = count( $records );
            if ( !$dryRun )
            {
                $entry['records'] = $this->write( $records );
                $ledger[$name] = array( 'sha256' => $sha, 'bytes' => (int)filesize( $path ), 'records' => ( $prior && $from ? (int)$prior['records'] : 0 ) + $entry['records'],
                                        'imported' => gmdate( 'Y-m-d\TH:i:s\Z' ) );
                $done[$path] = $entry;
                if ( $this->record )
                    expAudit::event( 'system.audit.import', array(
                        'object' => array( 'type' => 'file', 'id' => expAudit::relativePath( $path ) ),
                        'after' => array( 'file' => $name, 'sha256' => $sha, 'records' => $entry['records'], 'from_byte' => $from,
                                          'old_name' => $oldName, 'to' => expAudit::relativePath( $this->out ) ) ) );
            }
            else
                $entry['records'] = count( $records );
            $result['records'] += $entry['records'];
            $result['files'][$path] = $entry;
        }
        if ( !$dryRun )
        {
            $this->saveLedger( $ledger );
            if ( $done )
                $result['archive'] = $this->archiveOriginals( array_keys( $done ), !empty( $options['keepOriginals'] ) );
        }
        return $result;
    }

    /**
     * The records of one old file.
     *
     * @param string $path
     * @param string $oldName
     * @param string $sha
     * @param int $from byte offset to start at
     * @return array[]
     */
    public function parse( $path, $oldName, $sha, $from = 0 )
    {
        $h = @fopen( $path, 'rb' );
        if ( !$h )
            return array();
        $records = array();
        $lineNo = 0;
        $offset = 0;
        $current = null;
        $flush = function () use ( &$current, &$records, $oldName, $sha, $path ) {
            if ( $current !== null )
            {
                $r = $this->record( $current, $oldName, $sha, basename( $path ) );
                if ( $r !== null )
                    $records[] = $r;
            }
            $current = null;
        };
        while ( ( $raw = fgets( $h ) ) !== false )
        {
            $lineNo++;
            $start = $offset;
            $offset += strlen( $raw );
            if ( $start < $from )
                continue;
            $line = rtrim( $raw, "\r\n" );
            if ( strncmp( $line, '[ ', 2 ) === 0 && ( $head = self::parseHeader( $line ) ) !== null )
            {
                $flush();
                $current = $head + array( 'line' => $lineNo, 'attributes' => array() );
                continue;
            }
            if ( $current === null || trim( $line ) === '' )
                continue;
            $pos = strpos( $line, ':' );
            if ( $pos !== false )
                $current['attributes'][trim( substr( $line, 0, $pos ) )] = trim( substr( $line, $pos + 1 ) );
        }
        $flush();
        fclose( $h );
        return $records;
    }

    /**
     * The header line of an old entry.
     *
     * @param string $line
     * @return array|null time_ms, siteaccess, url, ip, login, user_id
     */
    public static function parseHeader( $line )
    {
        if ( !preg_match( '/^\[ ([A-Z][a-z]{2} \d{1,2} \d{4} \d{2}:\d{2}:\d{2}) \](.*)$/', $line, $m ) )
            return null;
        $ts = strtotime( $m[1] );
        if ( $ts === false )
            return null;
        preg_match_all( '/\[ ?([^\]]*?) ?\]/', $m[2], $parts );
        $groups = $parts[1];
        $head = array( 'time_ms' => $ts * 1000, 'siteaccess' => null, 'url' => null, 'ip' => null, 'login' => null, 'user_id' => null );
        // [ siteaccess ][ context ] [ip] [login:id] (current), [ip] [login:id] (older)
        if ( count( $groups ) >= 4 )
        {
            $head['siteaccess'] = $groups[0] !== '-' ? $groups[0] : null;
            $head['url'] = $groups[1];
            $groups = array_slice( $groups, 2 );
        }
        if ( isset( $groups[0] ) )
            $head['ip'] = filter_var( $groups[0], FILTER_VALIDATE_IP ) ? $groups[0] : null;
        if ( isset( $groups[1] ) && preg_match( '/^(.*):(\d+)$/', $groups[1], $u ) )
        {
            $head['login'] = $u[1];
            $head['user_id'] = (int)$u[2];
        }
        return $head;
    }

    /**
     * One imported record.
     *
     * @param array $e the parsed entry
     * @param string $oldName
     * @param string $sha
     * @param string $file
     * @return array|null
     */
    protected function record( array $e, $oldName, $sha, $file )
    {
        $mapped = expAudit::legacyData( $oldName, $e['attributes'], '', $this->config );
        if ( $mapped === null )
            return null;
        list( $name, $data ) = $mapped;
        $decision = expAuditTaxonomy::decide( $name, $this->config );
        $channel = $decision['valid'] ? $decision['channel'] : 'system';
        $severity = $decision['valid'] ? $decision['severity'] : 'info';
        $result = isset( $data['result'] ) ? $data['result'] : 'success';
        if ( $result === 'refused' && expAuditTaxonomy::rank( $severity ) > expAuditTaxonomy::rank( 'notice' ) )
            $severity = 'notice';
        if ( $result === 'failed' && expAuditTaxonomy::rank( $severity ) > expAuditTaxonomy::rank( 'warning' ) )
            $severity = 'warning';
        $ranks = explode( '.', $name );
        $actor = array( 'user_id' => $e['user_id'], 'login' => $e['login'], 'ip' => $e['ip'] );
        if ( isset( $data['actor'] ) )
        {
            // the user who logged in: not the roles they hold today
            $actor = array( 'user_id' => $data['actor']['user_id'], 'login' => $data['actor']['login'], 'ip' => $e['ip'] );
        }
        $record = array(
            'v' => expAudit::VERSION,
            'id' => self::stableId( $e['time_ms'], $sha . ':' . $e['line'] ),
            'name' => $name,
            'channel' => $channel,
            'time' => expAudit::timeString( $e['time_ms'] ),
            'severity' => $severity,
            'imported' => true,
            'source' => array( 'file' => $file, 'line' => (int)$e['line'], 'sha256' => $sha ),
            'request' => array( 'siteaccess' => $e['siteaccess'], 'url' => $e['url'] !== null ? self::pathOf( $e['url'] ) : null ),
            'actor' => $actor,
            'verb' => isset( $ranks[2] ) ? $ranks[2] : $ranks[1],
            'object' => isset( $data['object'] ) ? $data['object'] : null,
            'target' => isset( $data['target'] ) ? $data['target'] : null,
            'before' => isset( $data['before'] ) ? $data['before'] : null,
            'after' => isset( $data['after'] ) ? $data['after'] : null,
            'result' => $result,
            'reason' => isset( $data['reason'] ) ? $data['reason'] : null,
            'x' => isset( $data['x'] ) ? $data['x'] : null,
        );
        $privacy = new expAuditPrivacy( $this->config, $this->keys );
        $record = $privacy->apply( $record );
        return expAuditArchiver::withoutNulls( $record );
    }

    /** @return string The path and query of an address (the privacy rule truncates it further) */
    protected static function pathOf( $url )
    {
        $p = parse_url( $url );
        if ( $p === false || !isset( $p['path'] ) )
            return $url;
        return $p['path'] . ( isset( $p['query'] ) ? '?' . $p['query'] : '' );
    }

    /**
     * A ULID of a time whose random part comes from a seed (the same source always gives the same id).
     *
     * @param int $ms
     * @param string $seed
     * @return string
     */
    public static function stableId( $ms, $seed )
    {
        $time = substr( expAudit::ulid( $ms ), 0, 10 );
        $bits = '';
        foreach ( str_split( substr( hash( 'sha256', $seed, true ), 0, 10 ) ) as $c )
            $bits .= str_pad( decbin( ord( $c ) ), 8, '0', STR_PAD_LEFT );
        $rand = '';
        for ( $i = 0; $i < 16; $i++ )
            $rand .= expAudit::ULID_ALPHABET[bindec( substr( $bits, $i * 5, 5 ) )];
        return $time . $rand;
    }

    /**
     * Appends records to the import files by channel and day.
     *
     * @param array[] $records
     * @return int written
     */
    protected function write( array $records )
    {
        if ( !$records || !expAuditWriter::ensureDirectory( $this->out ) )
            return 0;
        $by = array();
        foreach ( $records as $r )
            $by[$r['channel'] . '-' . substr( $r['time'], 0, 10 ) . '.jsonl'][] = expAuditJson::encode( $r ) . "\n";
        $n = 0;
        foreach ( $by as $file => $lines )
        {
            $path = $this->out . '/' . $file;
            $created = !is_file( $path );
            $h = @fopen( $path, 'ab' );
            if ( !$h )
                continue;
            flock( $h, LOCK_EX );
            fwrite( $h, implode( '', $lines ) );
            fflush( $h );
            flock( $h, LOCK_UN );
            fclose( $h );
            if ( $created )
                expAuditWriter::ownLikeParent( $path, 0640 );
            $n += count( $lines );
        }
        return $n;
    }

    /** @return array file name => sha256, bytes, records, imported */
    public function ledger()
    {
        $s = @file_get_contents( $this->out . '/.imported.json' );
        $l = $s !== false ? json_decode( $s, true ) : null;
        return is_array( $l ) ? $l : array();
    }

    protected function saveLedger( array $ledger )
    {
        if ( !expAuditWriter::ensureDirectory( $this->out ) )
            return;
        $path = $this->out . '/.imported.json';
        $created = !is_file( $path );
        $tmp = $path . '.' . getmypid() . '.tmp';
        if ( @file_put_contents( $tmp, json_encode( $ledger, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) ) !== false )
            @rename( $tmp, $path );
        if ( $created )
            expAuditWriter::ownLikeParent( $path, 0640 );
    }

    /** @return string sha256 of a file's first $bytes bytes */
    protected static function prefixHash( $path, $bytes )
    {
        $h = @fopen( $path, 'rb' );
        if ( !$h )
            return '';
        $ctx = hash_init( 'sha256' );
        $left = (int)$bytes;
        while ( $left > 0 && !feof( $h ) )
        {
            $chunk = fread( $h, min( 1048576, $left ) );
            if ( $chunk === false || $chunk === '' )
                break;
            hash_update( $ctx, $chunk );
            $left -= strlen( $chunk );
        }
        fclose( $h );
        return hash_final( $ctx );
    }

    /**
     * Compresses the originals into <ArchiveDir>/legacy/ with a signed manifest.
     *
     * @param string[] $paths
     * @param bool $keep leave the originals in place
     * @return string|null the manifest path
     */
    protected function archiveOriginals( array $paths, $keep )
    {
        $archiver = new expAuditArchiver( $this->config, $this->keys );
        $dir = $archiver->archiveDir() . '/legacy';
        if ( !expAuditWriter::ensureDirectory( $dir ) )
            return null;
        // unique per run: two imports within one second never overwrite each other's archive
        $stamp = gmdate( 'Ymd-His' ) . '-' . strtolower( substr( expAudit::ulid(), 20, 6 ) );
        $gzip = new expAuditGzipFormat();
        $files = array();
        foreach ( $paths as $p )
        {
            $archive = $dir . '/' . basename( $p ) . '-' . $stamp . $gzip->extension();
            if ( !$gzip->compress( $p, $archive, 9 ) )
                return null;
            $s = $gzip->open( $archive );
            $d = expAuditFormatBase::digestStream( $s );
            expAuditFormatBase::close( $s );
            $sha = hash_file( 'sha256', $p );
            if ( $d['sha256'] !== $sha )
                return null;
            $files[] = array( 'name' => basename( $p ), 'archive' => basename( $archive ), 'sha256' => $sha, 'bytes' => (int)filesize( $p ),
                              'archive_sha256' => hash_file( 'sha256', $archive ), 'archive_bytes' => (int)filesize( $archive ) );
            expAuditWriter::ownLikeParent( $archive, 0440 );
        }
        $manifest = array( 'format' => 'exponential-audit-legacy-manifest', 'v' => 1, 'installation' => $this->keys->installationId(),
                           'created' => gmdate( 'Y-m-d\TH:i:s\Z' ), 'handler' => 'gzip', 'files' => $files,
                           'imported_to' => expAudit::relativePath( $this->out ), 'originals_kept' => (bool)$keep,
                           'key_id' => $this->keys->activeKeyId() );
        $manifest['hmac'] = $this->keys->sign( $manifest );
        $path = $dir . '/legacy-' . $stamp . '.manifest.json';
        @file_put_contents( $path, expAuditJson::encode( $manifest ) . "\n" );
        expAuditWriter::ownLikeParent( $path, 0440 );
        if ( !$keep )
            foreach ( $paths as $p )
                @unlink( $p );
        return $path;
    }
}
