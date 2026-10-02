<?php
/**
 * Verifies the hash chain of audit channels (doc/bc/6.0/audit.md, "Verification").
 *
 * For each line of a channel's files, oldest first: parse it (else unparseable), check it is canonical (else
 * noncanonical, a sign of editing), recompute the hash (else altered), check prev against the previous record's
 * hash (else link) and seq against the previous seq + 1 (else gap, or reordered when lower). At each file boundary
 * the first record must be system.audit.file.open naming the previous file, its last seq and hash (else
 * missing_file when that file is gone, truncated when its last record is not the one named); the first file must
 * start from the channel's genesis value (else no_origin). Checkpoints (system.audit.checkpoint in the system
 * channel) are compared with the channel at their seq: a different hash there is rewritten, and a checkpoint
 * whose HMAC does not verify is checkpoint_invalid (unknown_key when its key is not in the settings).
 *
 * A torn line followed by the writer's system.audit.chain.repair record naming its offset is "repaired", not a
 * break. Verification carries on after a break from the record's own hash, so every damaged stretch is listed.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditVerifier
{
    /** @var string */
    protected $dir;

    /** @var string */
    protected $algorithm;

    /** @var expAuditKeys|null */
    protected $keys;

    /**
     * @param string $dir the live directory
     * @param expAuditKeys|null $keys for the genesis values and the checkpoint HMACs
     * @param string $algorithm
     */
    public function __construct( $dir, ?expAuditKeys $keys = null, $algorithm = 'sha256' )
    {
        $this->dir = rtrim( $dir, '/' );
        $this->keys = $keys;
        $this->algorithm = $algorithm;
    }

    /**
     * The channels that have files.
     *
     * @return string[]
     */
    public function channels()
    {
        $channels = array();
        foreach ( (array)@scandir( $this->dir ) as $f )
        {
            $p = is_string( $f ) ? expAuditWriter::parseFileName( $f ) : null;
            if ( $p )
                $channels[$p['channel']] = true;
        }
        $channels = array_keys( $channels );
        sort( $channels );
        return $channels;
    }

    /**
     * Verifies every channel.
     *
     * @param array $options see verifyChannel()
     * @return array channel => result
     */
    public function verifyAll( array $options = array() )
    {
        $out = array();
        foreach ( $this->channels() as $channel )
            $out[$channel] = $this->verifyChannel( $channel, $options );
        return $out;
    }

    /**
     * Verifies one channel.
     *
     * @param string $channel
     * @param array $options date (YYYY-MM-DD: that day's files only), from (YYYY-MM-DD: files of that day and
     *                       later), files (file names to check, in order)
     * @return array channel, result (intact | repaired | broken | empty), records, files, first_time, last_time,
     *               breaks (file, line, id, kind, detail), repairs (file, line), notices (file, kind, detail),
     *               checkpoints (number compared)
     */
    public function verifyChannel( $channel, array $options = array() )
    {
        $all = expAuditWriter::channelFiles( $this->dir, $channel );
        $files = isset( $options['files'] ) ? array_values( $options['files'] ) : $all;
        $partial = false;
        if ( !empty( $options['date'] ) || !empty( $options['from'] ) )
        {
            $files = array_values( array_filter( $files, function ( $f ) use ( $options ) {
                $p = expAuditWriter::parseFileName( $f );
                if ( !empty( $options['date'] ) )
                    return $p['date'] === $options['date'];
                return strcmp( $p['date'], $options['from'] ) >= 0;
            } ) );
            $partial = $files && $files[0] !== reset( $all );
        }
        $result = array( 'channel' => $channel, 'result' => 'empty', 'records' => 0, 'files' => count( $files ),
                         'first_time' => null, 'last_time' => null, 'breaks' => array(), 'repairs' => array(),
                         'notices' => array(), 'checkpoints' => 0, 'last_seq' => null, 'last_hash' => null );
        if ( !$files )
            return $result;

        $checkpoints = $this->checkpoints( $channel );
        $expectedPrev = null;
        $lastSeq = null;
        $lastName = null;
        $previousFile = null;
        $previousLast = null; // seq and hash of the previous file's last record

        if ( $partial )
        {
            // checking from a later file: its link to the file before is still checked
            $idx = array_search( $files[0], $all, true );
            if ( $idx > 0 )
            {
                $previousFile = $all[$idx - 1];
                $head = $this->lastRecord( $this->dir . '/' . $previousFile );
                $previousLast = $head;
                if ( $head )
                {
                    $expectedPrev = $head['hash'];
                    $lastSeq = $head['seq'];
                }
            }
        }

        foreach ( $files as $fileIndex => $file )
        {
            $path = $this->dir . '/' . $file;
            $h = @fopen( $path, 'rb' );
            if ( !$h )
            {
                $this->addBreak( $result, $file, 0, null, 'unreadable', 'the file cannot be read' );
                continue;
            }
            $lineNo = 0;
            $offset = 0;
            $pendingTorn = null;
            $firstInFile = true;
            $fileLastName = null;
            while ( ( $raw = fgets( $h ) ) !== false )
            {
                $lineNo++;
                $len = strlen( $raw );
                $lineOffset = $offset;
                $offset += $len;
                $line = rtrim( $raw, "\n" );
                $rec = ( substr( $raw, -1 ) === "\n" ) ? expAuditJson::decode( $line ) : null;
                if ( !( $rec instanceof stdClass ) || !isset( $rec->hash, $rec->seq, $rec->name ) )
                {
                    if ( $pendingTorn === null )
                        $pendingTorn = array( 'line' => $lineNo, 'offset' => $lineOffset, 'length' => $len );
                    else
                        $pendingTorn['length'] += $len;
                    continue;
                }
                if ( $pendingTorn !== null )
                {
                    if ( $rec->name === expAuditWriter::CHAIN_REPAIR && isset( $rec->after->offset ) && (int)$rec->after->offset === $pendingTorn['offset'] )
                        $result['repairs'][] = array( 'file' => $file, 'line' => $pendingTorn['line'], 'bytes' => $pendingTorn['length'] );
                    else
                        $this->addBreak( $result, $file, $pendingTorn['line'], null, 'unparseable', 'not a record' );
                    $pendingTorn = null;
                }
                $result['records']++;
                $id = isset( $rec->id ) ? (string)$rec->id : null;
                $kinds = array();

                $hashless = clone $rec;
                unset( $hashless->hash );
                if ( expAuditJson::encode( $rec ) !== $line )
                    $kinds['noncanonical'] = 'the line is not in canonical form';
                $algo = strpos( (string)$rec->hash, ':' ) !== false ? strstr( (string)$rec->hash, ':', true ) : $this->algorithm;
                if ( !in_array( $algo, hash_algos(), true ) )
                    $algo = $this->algorithm;
                if ( $algo . ':' . hash( $algo, expAuditJson::encode( $hashless ) ) !== (string)$rec->hash )
                    $kinds['altered'] = 'the hash does not match the record';

                $boundaryReported = false;
                if ( $firstInFile )
                {
                    $firstInFile = false;
                    if ( $rec->name !== expAuditWriter::FILE_OPEN )
                        $kinds['no_open'] = 'the file does not start with ' . expAuditWriter::FILE_OPEN;
                    else
                    {
                        $named = isset( $rec->after->previous_file ) ? (string)$rec->after->previous_file : null;
                        if ( $previousFile === null && $fileIndex === 0 && !$partial )
                        {
                            if ( $named !== null && !in_array( $named, $all, true ) )
                            {
                                $kinds['no_origin'] = "the chain starts from $named, which is not here";
                                $boundaryReported = true;
                            }
                            elseif ( $named === null && $this->keys && (string)$rec->prev !== $this->genesis( $channel ) )
                            {
                                $kinds['no_origin'] = 'the first record does not start from the genesis value of this installation and channel';
                                $boundaryReported = true;
                            }
                        }
                        elseif ( $previousFile !== null )
                        {
                            if ( $named !== $previousFile )
                            {
                                $kinds[$named !== null && !in_array( $named, $all, true ) ? 'missing_file' : 'link'] =
                                    'names ' . ( $named === null ? 'no previous file' : $named ) . ", the previous file here is $previousFile";
                                $boundaryReported = true;
                            }
                            elseif ( $previousLast === null || !isset( $rec->after->previous_hash )
                                     || (string)$rec->after->previous_hash !== $previousLast['hash']
                                     || (int)$rec->after->previous_seq !== (int)$previousLast['seq'] )
                            {
                                $kinds['truncated'] = "$previousFile does not end with the record this file names (seq "
                                    . ( isset( $rec->after->previous_seq ) ? (int)$rec->after->previous_seq : '?' ) . ')';
                                $boundaryReported = true;
                            }
                        }
                    }
                    $lastSeq = 0;
                }
                if ( !$boundaryReported && $expectedPrev !== null && (string)$rec->prev !== $expectedPrev )
                    $kinds['link'] = 'prev is not the hash of the record before';
                if ( $lastSeq !== null && (int)$rec->seq !== $lastSeq + 1 )
                    $kinds[(int)$rec->seq <= $lastSeq ? 'reordered' : 'gap'] = 'seq ' . (int)$rec->seq . ' after ' . $lastSeq;

                if ( isset( $checkpoints[$file][(int)$rec->seq] ) )
                {
                    $result['checkpoints']++;
                    if ( $checkpoints[$file][(int)$rec->seq] !== (string)$rec->hash )
                        $kinds['rewritten'] = 'a signed checkpoint recorded another hash at seq ' . (int)$rec->seq;
                }
                if ( $rec->name === 'system.audit.checkpoint' )
                {
                    $problem = $this->checkpointProblem( $rec );
                    if ( $problem !== null )
                        $kinds[$problem[0]] = $problem[1];
                }

                foreach ( $kinds as $kind => $detail )
                    $this->addBreak( $result, $file, $lineNo, $id, $kind, $detail );

                if ( isset( $rec->time ) )
                {
                    if ( $result['first_time'] === null )
                        $result['first_time'] = (string)$rec->time;
                    $result['last_time'] = (string)$rec->time;
                }
                $expectedPrev = (string)$rec->hash;
                $lastSeq = (int)$rec->seq;
                $lastName = $fileLastName = (string)$rec->name;
            }
            fclose( $h );
            if ( $pendingTorn !== null )
                $result['notices'][] = array( 'file' => $file, 'line' => $pendingTorn['line'], 'kind' => 'torn_tail',
                                              'detail' => 'the last ' . $pendingTorn['length'] . ' bytes are not a record yet; the next write repairs them' );
            $isLast = $fileIndex === count( $files ) - 1;
            if ( !$isLast && $fileLastName !== expAuditWriter::FILE_CLOSE )
            {
                $next = expAuditWriter::parseFileName( $files[$fileIndex + 1] );
                $cur = expAuditWriter::parseFileName( $file );
                if ( $next && $cur && $next['date'] === $cur['date'] )
                    $result['notices'][] = array( 'file' => $file, 'line' => $lineNo, 'kind' => 'unclosed',
                                                  'detail' => 'a part without ' . expAuditWriter::FILE_CLOSE );
            }
            $previousFile = $file;
            $previousLast = $expectedPrev !== null ? array( 'seq' => $lastSeq, 'hash' => $expectedPrev ) : null;
        }
        $result['last_seq'] = $lastSeq;
        $result['last_hash'] = $expectedPrev;
        $result['result'] = $result['breaks'] ? 'broken' : ( $result['repairs'] ? 'repaired' : ( $result['records'] ? 'intact' : 'empty' ) );
        return $result;
    }

    /**
     * @param array $result
     * @param string $file
     * @param int $line
     * @param string|null $id
     * @param string $kind
     * @param string $detail
     */
    protected function addBreak( array &$result, $file, $line, $id, $kind, $detail )
    {
        $result['breaks'][] = array( 'file' => $file, 'line' => $line, 'id' => $id, 'kind' => $kind, 'detail' => $detail );
    }

    /** @return string The genesis value of a channel */
    protected function genesis( $channel )
    {
        return expAuditWriter::genesisFor( $this->keys->installationId(), $channel, $this->algorithm );
    }

    /**
     * The checkpoint entries for a channel, from the system channel's files.
     *
     * @param string $channel
     * @return array file => seq => hash
     */
    protected function checkpoints( $channel )
    {
        $out = array();
        foreach ( expAuditWriter::channelFiles( $this->dir, 'system' ) as $file )
        {
            $h = @fopen( $this->dir . '/' . $file, 'rb' );
            while ( $h && ( $line = fgets( $h ) ) !== false )
            {
                if ( strpos( $line, '"system.audit.checkpoint"' ) === false )
                    continue;
                $rec = expAuditJson::decode( rtrim( $line, "\n" ) );
                if ( !$rec || !isset( $rec->after->channels->$channel ) )
                    continue;
                $c = $rec->after->channels->$channel;
                if ( isset( $c->file, $c->seq, $c->hash ) )
                    $out[(string)$c->file][(int)$c->seq] = (string)$c->hash;
            }
            if ( $h )
                fclose( $h );
        }
        return $out;
    }

    /**
     * The HMAC of a checkpoint record.
     *
     * @param stdClass $rec
     * @return array|null array( kind, detail ) or null when it verifies (or no keys are known here)
     */
    protected function checkpointProblem( $rec )
    {
        if ( !$this->keys || !isset( $rec->after->hmac, $rec->after->key_id ) )
            return $this->keys ? array( 'checkpoint_invalid', 'the checkpoint carries no signature' ) : null;
        $keyId = (string)$rec->after->key_id;
        if ( $this->keys->signingKey( $keyId ) === null )
            return array( 'unknown_key', "the checkpoint is signed with the key $keyId, which is not in the settings" );
        $payload = clone $rec->after;
        unset( $payload->hmac );
        if ( !hash_equals( (string)$this->keys->sign( $payload, $keyId ), (string)$rec->after->hmac ) )
            return array( 'checkpoint_invalid', 'the checkpoint HMAC does not verify' );
        return null;
    }

    /**
     * The last record of a file (seq, hash), or null.
     *
     * @param string $path
     * @return array|null
     */
    protected function lastRecord( $path )
    {
        $last = null;
        $h = @fopen( $path, 'rb' );
        while ( $h && ( $line = fgets( $h ) ) !== false )
        {
            $rec = expAuditWriter::parseRecord( rtrim( $line, "\n" ) );
            if ( $rec )
                $last = array( 'seq' => $rec['seq'], 'hash' => $rec['hash'] );
        }
        if ( $h )
            fclose( $h );
        return $last;
    }
}
