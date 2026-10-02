<?php
/**
 * Archives, manifests, verification, restore and retention of the audit channels (doc/bc/6.0/audit.md,
 * "The archive manifest and its HMAC", "Rotation, archives and retention (Q8)").
 *
 * Archive: the live files of a channel older than LiveDays ([AuditChannel_<c>], defaults in
 * [AuditRotationSettings]) are compressed by the channel's ArchiveFormat handler (gzip when that one cannot work
 * here) into <ArchiveDir>/<channel>/<YYYY>/<file>.jsonl.<ext>, one day at a time, with one manifest per channel and
 * day, <channel>-<YYYY-MM-DD>.manifest.json: installation, site, channel, created, created_by, handler and its
 * options, every file (records, first/last seq and time, first prev, last hash, bytes, sha256, archive bytes and
 * sha256), the verification of the day's chain, previous_manifest (name and sha256 of the channel's manifest
 * before it, so removing a whole day is visible), key_id and hmac = "hmac-sha256:" + HMAC-SHA-256 of the canonical
 * manifest without hmac with that signing key. A day is verified before it is archived (VerifyBeforeArchive; a
 * broken day is archived all the same with result broken, and system.audit.chain.broken is recorded); each archive
 * is read back through its handler and compared with the live file's sha256 (VerifyAfterArchive) before the live
 * file is removed. The channel's newest live file is never archived (it holds the head of the chain).
 *
 * Verify (archives): every manifest's HMAC (hmac_invalid; unknown_key for a key id that is not in the settings), its
 * previous_manifest link (previous_manifest: a manifest removed, unless the retention ledger lists it as purged),
 * every archive's sha256 (archive_sha256), the content's sha256 and record count (content), and the hash chain
 * through all archived records, file to file (altered, link, gap, reordered, noncanonical, unparseable).
 *
 * Restore: decompresses a day into <LogDir>/restored/ (or another directory) for reading, verifying and
 * reindexing, and checks the result is byte-identical to the live file that was archived (the manifest's sha256);
 * restored files are never put back into the live chain.
 *
 * Retention (purge): archives older than ArchiveDays are removed, each one listed with its sha256 in
 * <ArchiveDir>/<channel>/purged.jsonl and in system.audit.purge, so the record of what existed outlives the data
 * and the next manifest's previous_manifest still verifies. Signing keys still used by a retained manifest are
 * reported as needed; a key is never removed from the settings by retention.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditArchiver
{
    const MANIFEST_FORMAT = 'exponential-audit-manifest';

    /** @var array expAuditConfig::get() */
    protected $config;

    /** @var expAuditKeys */
    protected $keys;

    /** @var int|null Fixed clock, epoch seconds (tests) */
    protected $now;

    /** @var bool Record system.audit.* events of what is done */
    public $record = true;

    /**
     * @param array|null $config
     * @param expAuditKeys|null $keys
     * @param int|null $now epoch seconds (tests)
     */
    public function __construct( ?array $config = null, ?expAuditKeys $keys = null, $now = null )
    {
        $this->config = $config ?: expAuditConfig::get();
        $this->keys = $keys ?: new expAuditKeys( $this->config );
        $this->now = $now;
    }

    /** @return int epoch seconds */
    protected function now()
    {
        return $this->now !== null ? (int)$this->now : time();
    }

    /** @return string today's UTC date */
    protected function today()
    {
        return gmdate( 'Y-m-d', $this->now() );
    }

    /** @return string The archive directory */
    public function archiveDir()
    {
        return expAuditConfig::path( expAuditConfig::value( 'AuditArchiveSettings', 'ArchiveDir', 'log/audit/archive' ) );
    }

    /** @return string The live directory */
    public function logDir()
    {
        return $this->config['logDir'];
    }

    /**
     * A channel setting: [AuditChannel_<c>], else [AuditRotationSettings].
     *
     * @param string $channel
     * @param string $var
     * @param mixed $default
     * @return mixed
     */
    public function channelSetting( $channel, $var, $default )
    {
        $v = expAuditConfig::value( 'AuditChannel_' . $channel, $var, null );
        if ( $v === null || $v === '' )
            $v = expAuditConfig::value( 'AuditRotationSettings', $var, $default );
        return $v === null || $v === '' ? $default : $v;
    }

    /** @return int LiveDays of a channel */
    public function liveDays( $channel )
    {
        return max( 0, (int)$this->channelSetting( $channel, 'LiveDays', 90 ) );
    }

    /** @return int ArchiveDays of a channel */
    public function archiveDays( $channel )
    {
        return max( 1, (int)$this->channelSetting( $channel, 'ArchiveDays', 730 ) );
    }

    /** @return bool [AuditRotationSettings] variable is enabled */
    protected function flag( $var )
    {
        return strtolower( trim( (string)expAuditConfig::value( 'AuditRotationSettings', $var, 'enabled' ) ) ) !== 'disabled';
    }

    /** @return int An octal mode setting */
    protected function mode( $var, $default )
    {
        $v = trim( (string)expAuditConfig::value( 'AuditArchiveSettings', $var, '' ) );
        return preg_match( '/^0?[0-7]{3,4}$/', $v ) ? octdec( $v ) : $default;
    }

    /** @return string <ArchiveDir>/<channel>/<YYYY> */
    public function dayDir( $channel, $date )
    {
        return $this->archiveDir() . '/' . $channel . '/' . substr( $date, 0, 4 );
    }

    /** @return string The manifest's file name */
    public static function manifestName( $channel, $date )
    {
        return $channel . '-' . $date . '.manifest.json';
    }

    // ------------------------------------------------------------------ archive

    /**
     * The live files of a channel due for the archive, by day.
     *
     * @param string $channel
     * @param string|null $before YYYY-MM-DD: files of days before it (default: today - LiveDays)
     * @return array date => file names
     */
    public function dueFiles( $channel, $before = null )
    {
        $cutoff = $before !== null ? $before : gmdate( 'Y-m-d', $this->now() - $this->liveDays( $channel ) * 86400 );
        if ( strcmp( $cutoff, $this->today() ) > 0 )
            $cutoff = $this->today();
        $files = expAuditWriter::channelFiles( $this->logDir(), $channel );
        if ( count( $files ) < 2 )
            return array();
        $newest = end( $files );
        $newestDate = expAuditWriter::parseFileName( $newest )['date'];
        $due = array();
        foreach ( $files as $f )
        {
            $p = expAuditWriter::parseFileName( $f );
            // never the newest file, nor anything of its day (the head of the chain lives there)
            if ( $f === $newest || $p['date'] === $newestDate )
                continue;
            if ( strcmp( $p['date'], $cutoff ) < 0 )
                $due[$p['date']][] = $f;
        }
        return $due;
    }

    /**
     * Archives the due days of a channel.
     *
     * @param string $channel
     * @param array $options before (YYYY-MM-DD), format (a handler name), dryRun (bool)
     * @return array date => array( files, archives, manifest, handler, verification, removed ) or a dry-run plan
     */
    public function archive( $channel, array $options = array() )
    {
        $results = array();
        $due = $this->dueFiles( $channel, isset( $options['before'] ) ? $options['before'] : null );
        $format = !empty( $options['format'] ) ? $options['format'] : (string)$this->channelSetting( $channel, 'ArchiveFormat', 'gzip' );
        $handler = expAuditFormatRegistry::handler( $format );
        foreach ( $due as $date => $files )
        {
            if ( !empty( $options['dryRun'] ) )
            {
                $results[$date] = array( 'files' => $files, 'handler' => $handler->name(), 'dry_run' => true,
                                         'manifest' => $this->dayDir( $channel, $date ) . '/' . self::manifestName( $channel, $date ) );
                continue;
            }
            $results[$date] = $this->archiveDay( $channel, $date, $files, $handler );
            if ( !empty( $results[$date]['error'] ) )
                break;
        }
        return $results;
    }

    /**
     * Archives one day of a channel.
     *
     * @param string $channel
     * @param string $date
     * @param string[] $files live file names of that day, in order
     * @param expAuditFormatHandler $handler
     * @return array
     */
    protected function archiveDay( $channel, $date, array $files, expAuditFormatHandler $handler )
    {
        $dir = $this->dayDir( $channel, $date );
        $dirMode = $this->mode( 'DirMode', 0750 );
        $fileMode = $this->mode( 'FileMode', 0440 );
        if ( !expAuditWriter::ensureDirectory( $dir ) )
            return array( 'error' => "The archive directory $dir cannot be created" );
        foreach ( array( $this->archiveDir() . '/' . $channel, $dir ) as $d )
            @chmod( $d, $dirMode );
        $manifestPath = $dir . '/' . self::manifestName( $channel, $date );
        if ( is_file( $manifestPath ) )
            return array( 'error' => "$manifestPath exists already: the day was archived before" );

        // the chain of the day, before anything is written
        $verification = array( 'result' => 'unchecked', 'checked' => null );
        if ( $this->flag( 'VerifyBeforeArchive' ) )
        {
            $verifier = $this->verifier();
            $v = $verifier->verifyChannel( $channel, array( 'date' => $date ) );
            $verification = array( 'result' => $v['result'], 'checked' => gmdate( 'Y-m-d\TH:i:s\Z', $this->now() ) );
            if ( $v['breaks'] )
            {
                $b = $v['breaks'][0];
                $verification['first_break'] = array( 'file' => $b['file'], 'line' => $b['line'], 'kind' => $b['kind'] );
                if ( $this->record )
                    expAudit::event( 'system.audit.chain.broken', array(
                        'object' => array( 'type' => 'file', 'id' => $b['file'] ), 'result' => 'failed', 'reason' => $b['kind'],
                        'after' => array( 'channel' => $channel, 'file' => $b['file'], 'line' => $b['line'], 'kind' => $b['kind'], 'found_by' => 'archive' ) ) );
            }
        }

        $entries = array();
        $written = array();
        foreach ( $files as $f )
        {
            $live = $this->logDir() . '/' . $f;
            $info = $this->describeLive( $live );
            $archiveName = $f . $handler->extension();
            $archive = $dir . '/' . $archiveName;
            if ( is_file( $archive ) )
                return array( 'error' => "$archive exists already" );
            if ( !$handler->compress( $live, $archive, expAuditFormatRegistry::level( $handler->name() ) ) )
                return array( 'error' => "Compressing $f with " . $handler->name() . ' failed', 'written' => $written );
            $written[] = $archive;
            if ( $this->flag( 'VerifyAfterArchive' ) )
            {
                $back = $this->digestArchive( $handler, $archive );
                if ( $back === null || $back['sha256'] !== $info['sha256'] || $back['bytes'] !== $info['bytes'] )
                    return array( 'error' => "The archive of $f does not read back to the live file; the live file is kept", 'written' => $written );
            }
            $info['name'] = $f;
            $info['archive'] = $archiveName;
            $info['archive_bytes'] = (int)filesize( $archive );
            $info['archive_sha256'] = hash_file( 'sha256', $archive );
            $entries[] = $info;
        }

        $previous = $this->previousManifest( $channel, $date );
        $manifest = array(
            'format' => self::MANIFEST_FORMAT, 'v' => 1,
            'installation' => $this->keys->installationId(),
            'site' => expAuditWebhookSink::siteName(),
            'channel' => $channel,
            'date' => $date,
            'created' => gmdate( 'Y-m-d\TH:i:s\Z', $this->now() ),
            'created_by' => $this->createdBy(),
            'handler' => $handler->name(),
            'handler_options' => array( 'level' => expAuditFormatRegistry::level( $handler->name() ) ),
            'files' => $entries,
            'verification' => $verification,
            'previous_manifest' => $previous,
            'key_id' => $this->keys->activeKeyId(),
        );
        $manifest = self::withoutNulls( $manifest );
        $hmac = $this->keys->sign( $manifest );
        if ( $hmac === null )
            return array( 'error' => 'No signing key: the manifest cannot be signed', 'written' => $written );
        $manifest['hmac'] = $hmac;
        $tmp = $manifestPath . '.' . getmypid() . '.tmp';
        if ( @file_put_contents( $tmp, expAuditJson::encode( $manifest ) . "\n" ) === false || !@rename( $tmp, $manifestPath ) )
            return array( 'error' => "The manifest $manifestPath cannot be written", 'written' => $written );
        foreach ( array_merge( $written, array( $manifestPath ) ) as $p )
            expAuditWriter::ownLikeParent( $p, $fileMode );

        // the live files go only now: archived, read back and listed in a signed manifest
        $removed = array();
        foreach ( $files as $f )
        {
            if ( @unlink( $this->logDir() . '/' . $f ) )
                $removed[] = $f;
        }
        $rel = expAudit::relativePath( $manifestPath );
        if ( $this->record )
            expAudit::event( 'system.audit.archive', array(
                'object' => array( 'type' => 'channel', 'id' => $channel ),
                'target' => array( 'type' => 'archive', 'id' => $rel ),
                'after' => array( 'date' => $date, 'files' => $files, 'manifest' => $rel, 'handler' => $handler->name(),
                                  'key_id' => $manifest['key_id'], 'verification' => $verification['result'],
                                  'records' => array_sum( array_map( function ( $e ) { return $e['records']; }, $entries ) ) ) ) );
        return array( 'files' => $files, 'archives' => array_map( 'basename', $written ), 'manifest' => $manifestPath,
                      'handler' => $handler->name(), 'verification' => $verification['result'], 'removed' => $removed,
                      'key_id' => $manifest['key_id'] );
    }

    /**
     * The facts of a live file for its manifest entry.
     *
     * @param string $path
     * @return array records, first_seq, last_seq, first_time, last_time, first_prev, last_hash, bytes, sha256
     */
    protected function describeLive( $path )
    {
        $info = array( 'records' => 0, 'first_seq' => null, 'last_seq' => null, 'first_time' => null, 'last_time' => null,
                       'first_prev' => null, 'last_hash' => null );
        $h = @fopen( $path, 'rb' );
        while ( $h && ( $line = fgets( $h ) ) !== false )
        {
            $r = expAuditWriter::parseRecord( rtrim( $line, "\n" ) );
            if ( $r === null )
                continue;
            $info['records']++;
            if ( $info['first_seq'] === null )
            {
                $info['first_seq'] = $r['seq'];
                $info['first_time'] = isset( $r['time'] ) ? $r['time'] : null;
                $info['first_prev'] = isset( $r['prev'] ) ? $r['prev'] : null;
            }
            $info['last_seq'] = $r['seq'];
            $info['last_time'] = isset( $r['time'] ) ? $r['time'] : null;
            $info['last_hash'] = $r['hash'];
        }
        if ( $h )
            fclose( $h );
        clearstatcache( true, $path );
        $info['bytes'] = (int)filesize( $path );
        $info['sha256'] = hash_file( 'sha256', $path );
        return $info;
    }

    /** @return array|null sha256 and bytes of an archive's content, read through its handler */
    protected function digestArchive( expAuditFormatHandler $handler, $archive )
    {
        $s = $handler->open( $archive );
        if ( !$s )
            return null;
        $d = expAuditFormatBase::digestStream( $s );
        return expAuditFormatBase::close( $s ) ? $d : null;
    }

    /** @return array user_id, login, os_user, run */
    protected function createdBy()
    {
        $os = null;
        if ( function_exists( 'posix_geteuid' ) && function_exists( 'posix_getpwuid' ) )
        {
            $pw = @posix_getpwuid( posix_geteuid() );
            $os = $pw ? $pw['name'] : null;
        }
        $state = expAudit::state();
        return self::withoutNulls( array( 'user_id' => null, 'login' => null, 'os_user' => $os,
                                          'run' => $state['run'] !== null ? $state['run'] : expAudit::requestId() ) );
    }

    /**
     * The channel's manifest before a date: name and sha256 (or from the purge ledger).
     *
     * @return array|null name, sha256
     */
    protected function previousManifest( $channel, $date )
    {
        $prev = null;
        foreach ( $this->manifests( $channel ) as $d => $path )
        {
            if ( strcmp( $d, $date ) >= 0 )
                break;
            $prev = array( 'name' => basename( $path ), 'sha256' => hash_file( 'sha256', $path ) );
        }
        if ( $prev === null )
        {
            $ledger = $this->purgedLedger( $channel );
            if ( $ledger )
            {
                $last = end( $ledger );
                $prev = array( 'name' => $last['manifest'], 'sha256' => $last['sha256'] );
            }
        }
        return $prev;
    }

    /**
     * The manifests of a channel, oldest first.
     *
     * @param string $channel
     * @return array date => path
     */
    public function manifests( $channel )
    {
        $out = array();
        foreach ( (array)glob( $this->archiveDir() . '/' . $channel . '/*/' . $channel . '-*.manifest.json' ) as $p )
        {
            if ( is_string( $p ) && preg_match( '/-(\d{4}-\d{2}-\d{2})\.manifest\.json$/', $p, $m ) )
                $out[$m[1]] = $p;
        }
        ksort( $out );
        return $out;
    }

    /** @return array[] The purge ledger of a channel: manifest, sha256, files, purged */
    public function purgedLedger( $channel )
    {
        $out = array();
        $h = @fopen( $this->archiveDir() . '/' . $channel . '/purged.jsonl', 'rb' );
        while ( $h && ( $line = fgets( $h ) ) !== false )
        {
            $r = json_decode( $line, true );
            if ( is_array( $r ) && isset( $r['manifest'] ) )
                $out[] = $r;
        }
        if ( $h )
            fclose( $h );
        return $out;
    }

    /**
     * The last record of every archived file of a channel (for the verifier: the first live file may start from a
     * file that is now in the archive).
     *
     * @param string $channel
     * @return array file name => array( seq, hash )
     */
    public function archivedHeads( $channel )
    {
        $out = array();
        foreach ( $this->manifests( $channel ) as $path )
        {
            $m = json_decode( (string)@file_get_contents( $path ), true );
            foreach ( isset( $m['files'] ) ? (array)$m['files'] : array() as $f )
                if ( isset( $f['name'], $f['last_seq'], $f['last_hash'] ) )
                    $out[$f['name']] = array( 'seq' => (int)$f['last_seq'], 'hash' => (string)$f['last_hash'] );
        }
        foreach ( $this->purgedLedger( $channel ) as $p )
            foreach ( isset( $p['heads'] ) ? (array)$p['heads'] : array() as $name => $head )
                if ( !isset( $out[$name] ) )
                    $out[$name] = $head;
        return $out;
    }

    /** @return expAuditVerifier for the live directory, knowing the archived heads */
    public function verifier()
    {
        $v = new expAuditVerifier( $this->logDir(), $this->keys, $this->config['algorithm'] );
        if ( method_exists( $v, 'setOrigins' ) )
            $v->setOrigins( array( $this, 'archivedHeads' ) );
        return $v;
    }

    // ------------------------------------------------------------------ verify

    /**
     * Verifies the archives of a channel: manifests, their HMAC and links, archive checksums, and the hash chain
     * through all archived records.
     *
     * @param string $channel
     * @return array channel, result (intact|broken|empty), manifests, files, records, breaks (manifest, file, line, kind, detail)
     */
    public function verifyArchives( $channel )
    {
        $result = array( 'channel' => $channel, 'result' => 'empty', 'manifests' => 0, 'files' => 0, 'records' => 0, 'breaks' => array(),
                         'first_time' => null, 'last_time' => null, 'last_hash' => null, 'keys' => array() );
        $manifests = $this->manifests( $channel );
        $ledger = array();
        foreach ( $this->purgedLedger( $channel ) as $p )
            $ledger[$p['manifest']] = $p['sha256'];
        $prevManifest = null;
        $prevHash = null;
        $prevSeq = null;
        $prevFile = null;
        $first = true;
        $break = function ( $manifest, $file, $line, $kind, $detail ) use ( &$result ) {
            $result['breaks'][] = array( 'manifest' => $manifest, 'file' => $file, 'line' => $line, 'kind' => $kind, 'detail' => $detail );
        };
        foreach ( $manifests as $date => $path )
        {
            $result['manifests']++;
            $name = basename( $path );
            $raw = @file_get_contents( $path );
            $m = $raw !== false ? json_decode( $raw, true ) : null;
            if ( !is_array( $m ) || !isset( $m['format'] ) || $m['format'] !== self::MANIFEST_FORMAT )
            {
                $break( $name, null, 0, 'manifest_unreadable', 'not a manifest' );
                $prevManifest = array( 'name' => $name, 'sha256' => hash_file( 'sha256', $path ) );
                continue;
            }
            // the HMAC
            $keyId = isset( $m['key_id'] ) ? (string)$m['key_id'] : '';
            $result['keys'][$keyId] = true;
            $hmac = isset( $m['hmac'] ) ? (string)$m['hmac'] : '';
            // signed over the canonical form of the manifest as written ({} and [] kept apart)
            $unsigned = expAuditJson::decode( trim( $raw ) );
            unset( $unsigned->hmac );
            $check = $this->keys->verify( $unsigned, $hmac, $keyId );
            if ( $check === 'unknown_key' )
                $break( $name, null, 0, 'unknown_key', "signed with key $keyId, which is not in the settings" );
            elseif ( $check !== 'ok' )
                $break( $name, null, 0, 'hmac_invalid', 'the manifest was changed after it was signed' );
            // the link to the manifest before
            $named = isset( $m['previous_manifest']['name'] ) ? $m['previous_manifest'] : null;
            if ( $prevManifest === null )
            {
                if ( $named !== null && !( isset( $ledger[$named['name']] ) && $ledger[$named['name']] === $named['sha256'] ) )
                    $break( $name, null, 0, 'previous_manifest', 'names ' . $named['name'] . ', which is neither here nor in the purge ledger' );
            }
            elseif ( $named === null || $named['name'] !== $prevManifest['name'] || $named['sha256'] !== $prevManifest['sha256'] )
            {
                $missing = $named !== null && $named['name'] !== $prevManifest['name'];
                $break( $name, null, 0, 'previous_manifest', $missing ? 'names ' . $named['name'] . ', the manifest before here is ' . $prevManifest['name']
                                                                       : 'the manifest before it was changed or replaced' );
            }
            $prevManifest = array( 'name' => $name, 'sha256' => hash_file( 'sha256', $path ) );

            // the archives and the chain through them
            $handler = isset( $m['handler'] ) ? expAuditFormatRegistry::get( $m['handler'] ) : null;
            foreach ( isset( $m['files'] ) ? (array)$m['files'] : array() as $f )
            {
                $result['files']++;
                $archive = dirname( $path ) . '/' . $f['archive'];
                if ( !is_file( $archive ) )
                {
                    $break( $name, $f['archive'], 0, 'missing_archive', 'the archive file is gone' );
                    $prevHash = isset( $f['last_hash'] ) ? $f['last_hash'] : null;
                    $prevSeq = isset( $f['last_seq'] ) ? (int)$f['last_seq'] : null;
                    $prevFile = $f['name'];
                    continue;
                }
                if ( hash_file( 'sha256', $archive ) !== $f['archive_sha256'] )
                    $break( $name, $f['archive'], 0, 'archive_sha256', 'the archive does not have the sha256 its manifest lists' );
                $h = $handler ?: expAuditFormatRegistry::forArchive( $archive );
                $stream = $h ? $h->open( $archive ) : false;
                if ( !$stream )
                {
                    $break( $name, $f['archive'], 0, 'unreadable', 'the archive cannot be opened (' . ( $h ? $h->name() . ': ' . $h->problem() : 'no handler' ) . ')' );
                    continue;
                }
                $ctx = hash_init( 'sha256' );
                $bytes = 0;
                $lineNo = 0;
                $count = 0;
                while ( ( $raw = fgets( $stream ) ) !== false )
                {
                    hash_update( $ctx, $raw );
                    $bytes += strlen( $raw );
                    $lineNo++;
                    $line = rtrim( $raw, "\n" );
                    $rec = expAuditJson::decode( $line );
                    if ( !( $rec instanceof stdClass ) || !isset( $rec->hash, $rec->seq, $rec->name ) )
                    {
                        if ( !( isset( $rec->name ) ) )
                            $break( $name, $f['name'], $lineNo, 'unparseable', 'not a record' );
                        continue;
                    }
                    $count++;
                    $result['records']++;
                    if ( expAuditJson::encode( $rec ) !== $line )
                        $break( $name, $f['name'], $lineNo, 'noncanonical', 'the line is not in canonical form' );
                    $hashless = clone $rec;
                    unset( $hashless->hash );
                    $algo = strpos( (string)$rec->hash, ':' ) !== false ? strstr( (string)$rec->hash, ':', true ) : $this->config['algorithm'];
                    if ( !in_array( $algo, hash_algos(), true ) )
                        $algo = $this->config['algorithm'];
                    if ( $algo . ':' . hash( $algo, expAuditJson::encode( $hashless ) ) !== (string)$rec->hash )
                        $break( $name, $f['name'], $lineNo, 'altered', 'the hash does not match the record' );
                    if ( $lineNo === 1 )
                    {
                        if ( $rec->name !== expAuditWriter::FILE_OPEN )
                            $break( $name, $f['name'], 1, 'no_open', 'the file does not start with ' . expAuditWriter::FILE_OPEN );
                        elseif ( !$first && $prevFile !== null && isset( $rec->after->previous_file ) && (string)$rec->after->previous_file !== $prevFile )
                            $break( $name, $f['name'], 1, 'missing_file', 'names ' . $rec->after->previous_file . ", the archived file before it is $prevFile" );
                        if ( !$first && $prevHash !== null && (string)$rec->prev !== $prevHash )
                            $break( $name, $f['name'], 1, 'link', 'prev is not the last hash of the file before' );
                        $prevSeq = 0;
                    }
                    elseif ( $prevHash !== null && (string)$rec->prev !== $prevHash )
                        $break( $name, $f['name'], $lineNo, 'link', 'prev is not the hash of the record before' );
                    if ( $prevSeq !== null && (int)$rec->seq !== $prevSeq + 1 )
                        $break( $name, $f['name'], $lineNo, (int)$rec->seq <= $prevSeq ? 'reordered' : 'gap', 'seq ' . (int)$rec->seq . ' after ' . $prevSeq );
                    if ( isset( $rec->time ) )
                    {
                        if ( $result['first_time'] === null )
                            $result['first_time'] = (string)$rec->time;
                        $result['last_time'] = (string)$rec->time;
                    }
                    $prevHash = (string)$rec->hash;
                    $prevSeq = (int)$rec->seq;
                    $first = false;
                }
                if ( !expAuditFormatBase::close( $stream ) )
                    $break( $name, $f['name'], $lineNo, 'unreadable', 'the archive did not decompress cleanly' );
                $sha = hash_final( $ctx );
                if ( $sha !== $f['sha256'] || $bytes !== (int)$f['bytes'] || $count !== (int)$f['records'] )
                    $break( $name, $f['name'], 0, 'content', 'the decompressed file differs from what the manifest lists' );
                $prevFile = $f['name'];
            }
        }
        $result['last_hash'] = $prevHash;
        $result['keys'] = array_keys( $result['keys'] );
        if ( $result['manifests'] )
            $result['result'] = $result['breaks'] ? 'broken' : 'intact';
        return $result;
    }

    // ------------------------------------------------------------------ restore

    /**
     * Decompresses an archived day for reading; never into the live chain.
     *
     * @param string $channel
     * @param string $date YYYY-MM-DD
     * @param string|null $to directory (default <LogDir>/restored)
     * @return array files (path => identical bool), manifest, error
     */
    public function restore( $channel, $date, $to = null )
    {
        $manifests = $this->manifests( $channel );
        if ( !isset( $manifests[$date] ) )
            return array( 'error' => "No archive of $channel for $date", 'files' => array() );
        $to = rtrim( $to !== null ? $to : $this->logDir() . '/restored', '/' );
        if ( realpath( $to ) !== false && realpath( $to ) === realpath( $this->logDir() ) )
            return array( 'error' => 'Restored files never go back into the live directory', 'files' => array() );
        if ( !expAuditWriter::ensureDirectory( $to ) )
            return array( 'error' => "$to cannot be created", 'files' => array() );
        $m = json_decode( (string)file_get_contents( $manifests[$date] ), true );
        $handler = isset( $m['handler'] ) ? expAuditFormatRegistry::get( $m['handler'] ) : null;
        $out = array( 'manifest' => $manifests[$date], 'files' => array(), 'error' => null );
        foreach ( (array)$m['files'] as $f )
        {
            $archive = dirname( $manifests[$date] ) . '/' . $f['archive'];
            $h = $handler ?: expAuditFormatRegistry::forArchive( $archive );
            $target = $to . '/' . $f['name'];
            $in = $h ? $h->open( $archive ) : false;
            $outH = $in ? @fopen( $target, 'wb' ) : false;
            if ( !$in || !$outH )
            {
                if ( $in )
                    expAuditFormatBase::close( $in );
                $out['error'] = "$archive cannot be restored to $target";
                break;
            }
            stream_copy_to_stream( $in, $outH );
            fclose( $outH );
            expAuditFormatBase::close( $in );
            expAuditWriter::ownLikeParent( $target, 0640 );
            $out['files'][$target] = hash_file( 'sha256', $target ) === $f['sha256'];
        }
        if ( $this->record )
            expAudit::event( 'system.audit.read', array( 'object' => array( 'type' => 'archive', 'id' => expAudit::relativePath( $manifests[$date] ) ),
                                                         'after' => array( 'action' => 'restore', 'channel' => $channel, 'date' => $date,
                                                                           'files' => count( $out['files'] ) ) ) );
        return $out;
    }

    // ------------------------------------------------------------------ retention

    /**
     * Removes archives older than ArchiveDays.
     *
     * @param string $channel
     * @param bool $dryRun
     * @return array purged (manifest names), files (archive names with sha256), keys_needed, keys_unneeded
     */
    public function purge( $channel, $dryRun = false )
    {
        $cutoff = gmdate( 'Y-m-d', $this->now() - $this->archiveDays( $channel ) * 86400 );
        $out = array( 'purged' => array(), 'files' => array(), 'keys_needed' => array(), 'keys_unneeded' => array(), 'dry_run' => (bool)$dryRun,
                      'cutoff' => $cutoff );
        $manifests = $this->manifests( $channel );
        $keep = array();
        foreach ( $manifests as $date => $path )
        {
            $m = json_decode( (string)@file_get_contents( $path ), true );
            if ( strcmp( $date, $cutoff ) >= 0 )
            {
                if ( isset( $m['key_id'] ) )
                    $keep[$m['key_id']] = true;
                continue;
            }
            $entry = array( 'manifest' => basename( $path ), 'sha256' => hash_file( 'sha256', $path ), 'date' => $date,
                            'files' => array(), 'heads' => array(), 'key_id' => isset( $m['key_id'] ) ? $m['key_id'] : null,
                            'purged' => gmdate( 'Y-m-d\TH:i:s\Z', $this->now() ) );
            foreach ( isset( $m['files'] ) ? (array)$m['files'] : array() as $f )
            {
                $archive = dirname( $path ) . '/' . $f['archive'];
                $entry['files'][] = array( 'name' => $f['archive'], 'sha256' => is_file( $archive ) ? hash_file( 'sha256', $archive ) : null );
                $entry['heads'][$f['name']] = array( 'seq' => (int)$f['last_seq'], 'hash' => (string)$f['last_hash'] );
            }
            $out['purged'][] = $entry['manifest'];
            $out['files'] = array_merge( $out['files'], $entry['files'] );
            if ( $dryRun )
                continue;
            // the ledger first: if the removal stops half way, the record of it is there
            $ledger = $this->archiveDir() . '/' . $channel . '/purged.jsonl';
            $created = !is_file( $ledger );
            @file_put_contents( $ledger, expAuditJson::encode( $entry ) . "\n", FILE_APPEND | LOCK_EX );
            if ( $created )
                expAuditWriter::ownLikeParent( $ledger, 0640 );
            foreach ( $entry['files'] as $f )
            {
                $p = dirname( $path ) . '/' . $f['name'];
                if ( is_file( $p ) )
                {
                    @chmod( $p, 0640 );
                    @unlink( $p );
                }
            }
            @chmod( $path, 0640 );
            @unlink( $path );
        }
        // the keys: needed by retained manifests (and by checkpoints in the live system channel)
        foreach ( array_keys( $keep ) as $k )
            $out['keys_needed'][] = $k;
        $known = $this->keys->keys( false );
        foreach ( array_keys( $known['signing'] ) as $k )
            if ( !isset( $keep[$k] ) && $k !== $known['active'] )
                $out['keys_unneeded'][] = $k;
        if ( $out['purged'] && !$dryRun && $this->record )
            expAudit::event( 'system.audit.purge', array(
                'object' => array( 'type' => 'channel', 'id' => $channel ),
                'after' => array( 'archives' => $out['files'], 'manifests' => $out['purged'], 'oldest_kept' => $cutoff ) ) );
        return $out;
    }

    // ------------------------------------------------------------------ status

    /**
     * Per channel: live files, archives, oldest of each, manifests (exp:audit status, the console's archives view).
     *
     * @return array channel => live_files, live_bytes, oldest_live, archives, archive_bytes, oldest_archive, manifests, format
     */
    public function status()
    {
        $out = array();
        foreach ( $this->config['channels'] as $channel )
        {
            $live = expAuditWriter::channelFiles( $this->logDir(), $channel );
            $bytes = 0;
            foreach ( $live as $f )
                $bytes += (int)@filesize( $this->logDir() . '/' . $f );
            $manifests = $this->manifests( $channel );
            $archives = 0;
            $abytes = 0;
            foreach ( $manifests as $p )
            {
                $m = json_decode( (string)@file_get_contents( $p ), true );
                foreach ( isset( $m['files'] ) ? (array)$m['files'] : array() as $f )
                {
                    $archives++;
                    $abytes += isset( $f['archive_bytes'] ) ? (int)$f['archive_bytes'] : 0;
                }
            }
            $out[$channel] = array(
                'live_files' => count( $live ), 'live_bytes' => $bytes,
                'oldest_live' => $live ? expAuditWriter::parseFileName( $live[0] )['date'] : null,
                'archives' => $archives, 'archive_bytes' => $abytes,
                'oldest_archive' => $manifests ? key( $manifests ) : null,
                'manifests' => count( $manifests ),
                'format' => expAuditFormatRegistry::handler( (string)$this->channelSetting( $channel, 'ArchiveFormat', 'gzip' ) )->name(),
                'live_days' => $this->liveDays( $channel ), 'archive_days' => $this->archiveDays( $channel ),
            );
        }
        return $out;
    }

    /** @return array The array without null members, recursively (manifests are canonical JSON like records) */
    public static function withoutNulls( array $a )
    {
        foreach ( $a as $k => $v )
        {
            if ( $v === null )
                unset( $a[$k] );
            elseif ( is_array( $v ) )
                $a[$k] = self::withoutNulls( $v );
        }
        return $a;
    }
}
