<?php
/**
 * Searching the audit files and exporting records (exp:audit search --files, exp:audit export; doc/bc/6.0/audit.md,
 * "The command: exp:audit").
 *
 * Filters, the console's parameters: channel, name (a taxonomy pattern), user (user id), login, object and target
 * (type:id), result, severity (the least severe shown), request, job, run, ip (a network such as 203.0.113.0/24, or
 * the start of an address), from and to (YYYY-MM-DD[THH:MM], the site's time zone; to includes that day or
 * minute), q (text, case-insensitive, in the record), legacy_file (a 4.x audit file name: the names it maps to),
 * subject_user (an access request: every record by or about that user, including the pseudonymised ones, matched
 * by hashing the user's login and e-mail address with the pseudonym key).
 *
 * The files read are the live channel files and the imported 4.x records (<LogDir>/imported/), newest first. The
 * console and exp:audit search use the index when it exists (expAuditQuery); this is the path without it.
 *
 * Export formats: jsonl (one record per line, as written), csv (the main columns), bundle (a directory with
 * records.jsonl and manifest.json: count, sha256 of the records, the filters, key_id and an HMAC with the active
 * signing key, so the receiver can check it came from this installation unchanged).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditExporter
{
    /** @var array */
    protected $config;

    /** @var expAuditKeys */
    protected $keys;

    /**
     * @param array|null $config
     */
    public function __construct( ?array $config = null )
    {
        $this->config = $config ?: expAuditConfig::get();
        $this->keys = new expAuditKeys( $this->config );
    }

    /**
     * The filters from command options or URL parameters (unknown keys dropped, empty values dropped).
     *
     * @param array $in
     * @return array
     */
    public static function filters( array $in )
    {
        $keys = array( 'channel', 'name', 'user', 'login', 'object', 'target', 'result', 'severity', 'request', 'job', 'run', 'ip',
                       'from', 'to', 'q', 'legacy_file', 'subject_user' );
        $out = array();
        foreach ( $in as $k => $v )
        {
            $k = str_replace( '-', '_', (string)$k );
            if ( in_array( $k, $keys, true ) && $v !== null && $v !== false && $v !== '' )
                $out[$k] = is_string( $v ) ? trim( $v ) : $v;
        }
        return $out;
    }

    /**
     * The record files, newest first: live channel files and imported ones.
     *
     * @param string|null $channel
     * @return string[] paths
     */
    public function files( $channel = null )
    {
        $paths = array();
        $channels = $channel !== null ? array( $channel ) : $this->config['channels'];
        foreach ( $channels as $c )
        {
            foreach ( expAuditWriter::channelFiles( $this->config['logDir'], $c ) as $f )
                $paths[] = array( expAuditWriter::parseFileName( $f ), $this->config['logDir'] . '/' . $f );
            foreach ( expAuditWriter::channelFiles( $this->config['logDir'] . '/imported', $c ) as $f )
                $paths[] = array( expAuditWriter::parseFileName( $f ), $this->config['logDir'] . '/imported/' . $f );
        }
        usort( $paths, function ( $a, $b ) {
            return array( $b[0]['date'], $b[0]['part'] ) <=> array( $a[0]['date'], $a[0]['part'] );
        } );
        return array_map( function ( $p ) { return $p[1]; }, $paths );
    }

    /**
     * Searches the files.
     *
     * @param array $f filters()
     * @param int $limit 0 = no limit
     * @return array[] records, newest first
     */
    public function search( array $f, $limit = 50 )
    {
        $f = $this->prepare( $f );
        $out = array();
        foreach ( $this->files( isset( $f['channel'] ) ? $f['channel'] : null ) as $path )
        {
            // a file of a day after "to" or before "from" cannot hold a match
            $p = expAuditWriter::parseFileName( basename( $path ) );
            if ( isset( $f['_from_date'] ) && strcmp( $p['date'], $f['_from_date'] ) < 0 )
                continue;
            if ( isset( $f['_to_date'] ) && strcmp( $p['date'], $f['_to_date'] ) > 0 )
                continue;
            $found = array();
            $h = @fopen( $path, 'rb' );
            while ( $h && ( $line = fgets( $h ) ) !== false )
            {
                if ( isset( $f['_q'] ) && stripos( $line, $f['_q'] ) === false )
                    continue;
                $r = json_decode( $line, true );
                if ( is_array( $r ) && isset( $r['name'] ) && $this->matches( $r, $f ) )
                    $found[] = $r;
            }
            if ( $h )
                fclose( $h );
            usort( $found, function ( $a, $b ) { return strcmp( $b['time'], $a['time'] ); } );
            foreach ( $found as $r )
            {
                $out[] = $r;
                if ( $limit > 0 && count( $out ) >= $limit )
                    return $out;
            }
        }
        return $out;
    }

    /**
     * Resolves what the filters need once: times, the legacy names, the subject's pseudonyms.
     */
    protected function prepare( array $f )
    {
        $tz = class_exists( 'expAuditScheduleRule' ) ? expAuditScheduleRule::timeZone() : new DateTimeZone( 'UTC' );
        foreach ( array( 'from', 'to' ) as $k )
        {
            if ( !isset( $f[$k] ) || !preg_match( '/^(\d{4}-\d{2}-\d{2})(?:[T ](\d{2}):(\d{2}))?$/', $f[$k], $m ) )
                continue;
            $d = new DateTime( $m[1] . ( isset( $m[2] ) ? ' ' . $m[2] . ':' . $m[3] : ' 00:00' ), $tz );
            if ( $k === 'to' )
                $d->modify( isset( $m[2] ) ? '+1 minute' : '+1 day' );
            $f['_' . $k . '_ms'] = $d->getTimestamp() * 1000;
            $f['_' . $k . '_date'] = gmdate( 'Y-m-d', $k === 'to' ? $d->getTimestamp() - 1 : $d->getTimestamp() );
        }
        if ( isset( $f['q'] ) )
            $f['_q'] = $f['q'];
        if ( isset( $f['legacy_file'] ) )
        {
            $names = array();
            foreach ( $this->config['auditFileNames'] as $old => $file )
                if ( basename( $file ) === basename( $f['legacy_file'] ) )
                    $names[] = isset( $this->config['compatMap'][$old] ) ? $this->config['compatMap'][$old] : 'system.legacy.' . str_replace( '-', '_', $old );
            // content-delete and content-hide map to more than one name
            foreach ( $names as $n )
            {
                if ( $n === 'content.node.remove.trash' )
                    array_push( $names, 'content.object.remove', 'content.object.purge' );
                if ( $n === 'content.node.hide' )
                    $names[] = 'content.node.reveal';
                if ( $n === 'commerce.order.delete' )
                    $names[] = 'commerce.order.purge';
            }
            $f['_legacy_names'] = array_unique( $names );
        }
        if ( isset( $f['subject_user'] ) )
            $f['_subject'] = $this->subject( (int)$f['subject_user'] );
        return $f;
    }

    /**
     * The identifiers of a user for an access request: id, login, e-mail and their pseudonyms.
     *
     * @param int $userID
     * @return array id, values (strings that identify the user in a record)
     */
    public function subject( $userID )
    {
        $values = array();
        if ( class_exists( 'eZUser' ) && !expAuditConfig::isOverridden() )
        {
            try
            {
                $user = eZUser::fetch( $userID );
                if ( $user )
                {
                    $values[] = (string)$user->attribute( 'login' );
                    $values[] = (string)$user->attribute( 'email' );
                }
            }
            catch ( Throwable $e )
            {
            }
        }
        $privacy = new expAuditPrivacy( $this->config, $this->keys );
        foreach ( $values as $v )
            if ( $v !== '' )
                $values[] = $privacy->hash( $v );
        return array( 'id' => $userID, 'values' => array_values( array_filter( $values, function ( $v ) { return is_string( $v ) && $v !== ''; } ) ) );
    }

    /** Adds the identifiers of a subject given directly (tests, a user that no longer exists). */
    public static function withSubjectValues( array $f, array $values, expAuditPrivacy $privacy )
    {
        $all = array();
        foreach ( $values as $v )
        {
            $all[] = $v;
            $h = $privacy->hash( $v );
            if ( $h !== null )
                $all[] = $h;
        }
        $f['_subject'] = array( 'id' => isset( $f['subject_user'] ) ? (int)$f['subject_user'] : 0, 'values' => $all );
        return $f;
    }

    /**
     * Whether a record matches the filters.
     *
     * @param array $r
     * @param array $f prepared filters
     * @return bool
     */
    public function matches( array $r, array $f )
    {
        if ( isset( $f['channel'] ) && ( !isset( $r['channel'] ) || $r['channel'] !== $f['channel'] ) )
            return false;
        if ( isset( $f['name'] ) && expAuditTaxonomy::match( $f['name'], $r['name'] ) < 0 )
            return false;
        if ( isset( $f['_legacy_names'] ) && !in_array( $r['name'], $f['_legacy_names'], true ) )
            return false;
        if ( isset( $f['user'] ) && ( !isset( $r['actor']['user_id'] ) || (int)$r['actor']['user_id'] !== (int)$f['user'] ) )
            return false;
        if ( isset( $f['login'] ) && ( !isset( $r['actor']['login'] ) || strcasecmp( (string)$r['actor']['login'], $f['login'] ) !== 0 ) )
            return false;
        foreach ( array( 'object', 'target' ) as $part )
        {
            if ( !isset( $f[$part] ) )
                continue;
            list( $type, $id ) = array_pad( explode( ':', $f[$part], 2 ), 2, null );
            if ( !isset( $r[$part]['type'] ) || $r[$part]['type'] !== $type )
                return false;
            if ( $id !== null && $id !== '' && ( !isset( $r[$part]['id'] ) || (string)$r[$part]['id'] !== $id ) )
                return false;
        }
        if ( isset( $f['result'] ) && ( !isset( $r['result'] ) || $r['result'] !== $f['result'] ) )
            return false;
        if ( isset( $f['severity'] ) && expAuditTaxonomy::rank( isset( $r['severity'] ) ? $r['severity'] : 'info' ) > expAuditTaxonomy::rank( $f['severity'] ) )
            return false;
        if ( isset( $f['request'] ) && ( !isset( $r['request']['id'] ) || $r['request']['id'] !== $f['request'] ) )
            return false;
        if ( isset( $f['job'] ) && ( !isset( $r['job'] ) || (string)$r['job'] !== $f['job'] ) )
            return false;
        if ( isset( $f['run'] ) && ( !isset( $r['run'] ) || (string)$r['run'] !== $f['run'] ) )
            return false;
        if ( isset( $f['ip'] ) && !self::ipMatches( isset( $r['actor']['ip'] ) ? (string)$r['actor']['ip'] : '', $f['ip'] ) )
            return false;
        if ( isset( $f['_from_ms'] ) || isset( $f['_to_ms'] ) )
        {
            $t = expAuditAlertRuleBase::timeMs( $r );
            if ( isset( $f['_from_ms'] ) && $t < $f['_from_ms'] )
                return false;
            if ( isset( $f['_to_ms'] ) && $t >= $f['_to_ms'] )
                return false;
        }
        if ( isset( $f['_q'] ) && stripos( expAuditJson::encode( $r ), $f['_q'] ) === false )
            return false;
        if ( isset( $f['_subject'] ) && !$this->isAboutSubject( $r, $f['_subject'] ) )
            return false;
        return true;
    }

    /** @return bool The record is by or about the subject */
    protected function isAboutSubject( array $r, array $subject )
    {
        $id = (int)$subject['id'];
        if ( $id && isset( $r['actor']['user_id'] ) && (int)$r['actor']['user_id'] === $id )
            return true;
        foreach ( array( 'object', 'target' ) as $part )
            if ( $id && isset( $r[$part]['type'], $r[$part]['id'] ) && $r[$part]['type'] === 'user' && (int)$r[$part]['id'] === $id )
                return true;
        if ( $subject['values'] )
        {
            $text = expAuditJson::encode( array( isset( $r['actor'] ) ? $r['actor'] : null, isset( $r['object'] ) ? $r['object'] : null,
                                                 isset( $r['target'] ) ? $r['target'] : null, isset( $r['before'] ) ? $r['before'] : null,
                                                 isset( $r['after'] ) ? $r['after'] : null ) );
            foreach ( $subject['values'] as $v )
                if ( strpos( $text, '"' . $v . '"' ) !== false )
                    return true;
        }
        return false;
    }

    /**
     * Whether an address (as recorded: 203.0.113.0/24, a full address, h:…) matches a filter (a network or a
     * prefix).
     */
    public static function ipMatches( $recorded, $filter )
    {
        if ( $recorded === '' )
            return false;
        if ( $recorded === $filter || strpos( $recorded, $filter ) === 0 )
            return true;
        if ( strpos( $filter, '/' ) !== false )
        {
            list( $net, $bits ) = explode( '/', $filter, 2 );
            $addr = strpos( $recorded, '/' ) !== false ? strstr( $recorded, '/', true ) : $recorded;
            $a = @inet_pton( $addr );
            $n = @inet_pton( $net );
            if ( $a === false || $n === false || strlen( $a ) !== strlen( $n ) )
                return false;
            $bits = (int)$bits;
            $bytes = intdiv( $bits, 8 );
            if ( substr( $a, 0, $bytes ) !== substr( $n, 0, $bytes ) )
                return false;
            if ( $bits % 8 )
            {
                $mask = ( 0xff << ( 8 - $bits % 8 ) ) & 0xff;
                return ( ord( $a[$bytes] ) & $mask ) === ( ord( $n[$bytes] ) & $mask );
            }
            return true;
        }
        return false;
    }

    /**
     * Exports records.
     *
     * @param array[] $records
     * @param string $format jsonl|csv|bundle
     * @param string $out a file (jsonl, csv) or a directory (bundle)
     * @param array $filters for the bundle's manifest
     * @return array path, count, sha256, manifest (bundle)
     */
    public function export( array $records, $format, $out, array $filters = array() )
    {
        $format = strtolower( (string)$format );
        if ( $format === 'csv' )
            $bytes = $this->csv( $records );
        else
        {
            $bytes = '';
            foreach ( $records as $r )
            {
                unset( $r['file'], $r['line'], $r['raw'] );
                $bytes .= expAuditJson::encode( $r ) . "\n";
            }
        }
        $result = array( 'format' => $format, 'count' => count( $records ), 'sha256' => hash( 'sha256', $bytes ), 'path' => $out, 'manifest' => null );
        if ( $format === 'bundle' )
        {
            if ( !expAuditWriter::ensureDirectory( $out ) )
                throw new RuntimeException( "$out cannot be created" );
            $path = rtrim( $out, '/' ) . '/records.jsonl';
            if ( @file_put_contents( $path, $bytes ) === false )
                throw new RuntimeException( "$path cannot be written" );
            $manifest = array( 'format' => 'exponential-audit-export', 'v' => 1, 'installation' => $this->keys->installationId(),
                               'created' => gmdate( 'Y-m-d\TH:i:s\Z' ), 'records' => count( $records ), 'file' => 'records.jsonl',
                               'sha256' => $result['sha256'], 'filters' => (object)array_filter( $filters, function ( $k ) { return $k[0] !== '_'; }, ARRAY_FILTER_USE_KEY ),
                               'key_id' => $this->keys->activeKeyId() );
            $manifest['hmac'] = $this->keys->sign( $manifest );
            $mpath = rtrim( $out, '/' ) . '/manifest.json';
            file_put_contents( $mpath, expAuditJson::encode( $manifest ) . "\n" );
            $result['path'] = $path;
            $result['manifest'] = $mpath;
            return $result;
        }
        $dir = dirname( $out );
        if ( !is_dir( $dir ) && !expAuditWriter::ensureDirectory( $dir ) )
            throw new RuntimeException( "$dir cannot be created" );
        if ( @file_put_contents( $out, $bytes ) === false )
            throw new RuntimeException( "$out cannot be written" );
        return $result;
    }

    /** @return string The records as CSV */
    protected function csv( array $records )
    {
        $h = fopen( 'php://memory', 'w+b' );
        fputcsv( $h, array( 'time', 'id', 'channel', 'name', 'severity', 'result', 'reason', 'user_id', 'login', 'ip', 'object_type',
                            'object_id', 'object_name', 'target_type', 'target_id', 'request', 'job', 'imported' ), ',', '"', '\\' );
        foreach ( $records as $r )
        {
            fputcsv( $h, array(
                isset( $r['time'] ) ? $r['time'] : '', isset( $r['id'] ) ? $r['id'] : '', isset( $r['channel'] ) ? $r['channel'] : '', $r['name'],
                isset( $r['severity'] ) ? $r['severity'] : '', isset( $r['result'] ) ? $r['result'] : '', isset( $r['reason'] ) ? $r['reason'] : '',
                isset( $r['actor']['user_id'] ) ? $r['actor']['user_id'] : '', isset( $r['actor']['login'] ) ? $r['actor']['login'] : '',
                isset( $r['actor']['ip'] ) ? $r['actor']['ip'] : '',
                isset( $r['object']['type'] ) ? $r['object']['type'] : '', isset( $r['object']['id'] ) ? ( is_scalar( $r['object']['id'] ) ? $r['object']['id'] : json_encode( $r['object']['id'] ) ) : '',
                isset( $r['object']['name'] ) ? $r['object']['name'] : '',
                isset( $r['target']['type'] ) ? $r['target']['type'] : '', isset( $r['target']['id'] ) ? ( is_scalar( $r['target']['id'] ) ? $r['target']['id'] : json_encode( $r['target']['id'] ) ) : '',
                isset( $r['request']['id'] ) ? $r['request']['id'] : '', isset( $r['job'] ) ? $r['job'] : '', !empty( $r['imported'] ) ? '1' : '',
            ), ',', '"', '\\' );
        }
        rewind( $h );
        $csv = stream_get_contents( $h );
        fclose( $h );
        return $csv;
    }
}
