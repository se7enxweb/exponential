<?php
/**
 * What the views of the audit module share (doc/bc/6.0/audit.md, "The console: module audit"): the channels the
 * current user may read (audit/read and its Channel limitation), the console filters from a URL and back, the
 * index brought up to date when a view opens, the rows of the index or of the files (when the index is off or not
 * there) in the form the templates show, the chain state per channel and the system.audit.read record.
 *
 * Used by the views (kernel/private/classes/views/audit/), the fetch functions (expAuditFunctionCollection) and the
 * dashboard block.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditConsole
{
    /** The filters of the console and their URL parameter names (module.php unordered_params) */
    const FILTERS = array( 'channel' => 'Channel', 'name' => 'Name', 'user' => 'User', 'login' => 'Login',
                           'object' => 'Object', 'target' => 'Target', 'result' => 'Result', 'severity' => 'Severity',
                           'request' => 'Request', 'job' => 'Job', 'run' => 'Run', 'ip' => 'IP', 'from' => 'From',
                           'to' => 'To', 'q' => 'Q', 'legacy_file' => 'LegacyFile', 'parent' => 'Parent' );

    /** Seconds an index run may take when a view opens */
    const OPEN_INDEX_SECONDS = 3.0;

    /** Minutes a stored chain verification is trusted before the console verifies today's files again */
    const VERIFY_MINUTES = 10;

    /** Records read from the files when there is no index */
    const FILE_SCAN = 5000;

    /** @return bool the audit classes are loaded (a Velocity worker started before them has not) */
    public static function available()
    {
        foreach ( array( 'expAudit', 'expAuditConfig', 'expAuditReader', 'expAuditVerifier', 'expAuditKeys', 'expAuditWriter',
                         'expAuditQuery', 'expAuditIndexer', 'expAuditIndexSchema', 'expAuditIndexRow', 'expAuditIndexSettings',
                         'expAuditTaxonomy', 'expAuditJson', 'expAuditPrivacy' ) as $class )
            if ( !class_exists( $class ) )
                return false;
        return true;
    }

    /**
     * A view result with the audit left menu and the path Audit / <title>.
     *
     * @param string $content
     * @param string $title
     * @param array $more more path elements
     * @return array
     */
    public static function result( $content, $title, array $more = array() )
    {
        $path = array( array( 'text' => ezpI18n::tr( 'design/admin/audit', 'Audit' ), 'url' => 'audit/dashboard' ),
                       array( 'text' => $title, 'url' => false ) );
        return array( 'content' => $content, 'left_menu' => 'design:audit/menu.tpl',
                      'path' => array_merge( $path, $more ) );
    }

    /**
     * The event names in the index (the name filter's suggestions), at most 300.
     *
     * @param string[]|null $allowed
     * @return string[]
     */
    public static function knownNames( $allowed )
    {
        if ( !self::indexUsable() )
            return array();
        $q = new expAuditQuery();
        $names = array_column( $q->groupCount( array( 'name' ), array(), $allowed, 300 ), 'name' );
        sort( $names );
        return $names;
    }

    /**
     * The channels the user may read: null = every channel, array() = none.
     *
     * @param eZUser|null $user
     * @return string[]|null
     */
    public static function allowedChannels( $user = null )
    {
        $user = $user ?: eZUser::currentUser();
        $access = $user->hasAccessTo( 'audit', 'read' );
        if ( $access['accessWord'] === 'yes' )
            return null;
        if ( $access['accessWord'] !== 'limited' )
            return array();
        $channels = array();
        foreach ( (array)$access['policies'] as $policy )
        {
            if ( !isset( $policy['Channel'] ) )
                return null; // a policy without the limitation: everything
            foreach ( (array)$policy['Channel'] as $c )
                $channels[(string)$c] = true;
        }
        return array_keys( $channels );
    }

    /** @return bool the user may read at least one channel */
    public static function canRead( $user = null )
    {
        $c = self::allowedChannels( $user );
        return $c === null || count( $c ) > 0;
    }

    /** @return bool the user holds audit/manage */
    public static function canManage( $user = null )
    {
        $user = $user ?: eZUser::currentUser();
        $access = $user->hasAccessTo( 'audit', 'manage' );
        return $access['accessWord'] !== 'no';
    }

    /**
     * @param string|null $channel
     * @param string[]|null $allowed
     * @return bool
     */
    public static function channelAllowed( $channel, $allowed )
    {
        return $allowed === null || in_array( (string)$channel, $allowed, true );
    }

    /**
     * The filters of a view's parameters ($Params['Channel'] ...), and of the filter form (POST Channel=...).
     *
     * @param array $params
     * @return array raw filters, key => string
     */
    public static function rawFilters( array $params )
    {
        $raw = array();
        $http = class_exists( 'eZHTTPTool' ) ? eZHTTPTool::instance() : null;
        foreach ( self::FILTERS as $key => $param )
        {
            $v = isset( $params[$param] ) && $params[$param] !== false && is_scalar( $params[$param] ) ? rawurldecode( (string)$params[$param] ) : null;
            if ( $v === null && $http && $http->hasPostVariable( $param ) )
                $v = $http->postVariable( $param );
            if ( is_scalar( $v ) && trim( (string)$v ) !== '' )
                $raw[$key] = trim( (string)$v );
        }
        return $raw;
    }

    /** @return bool the request is a filter form submission (POST; redirected to the canonical URL, so a filter can be bookmarked) */
    public static function isFormRequest()
    {
        if ( !class_exists( 'eZHTTPTool' ) )
            return false;
        $http = eZHTTPTool::instance();
        foreach ( self::FILTERS as $param )
            if ( $http->hasPostVariable( $param ) )
                return true;
        return $http->hasPostVariable( 'Filter' );
    }

    /**
     * The URL of a view with filters: audit/console/(channel)/access/(q)/login%20failed
     *
     * @param string $view audit/console
     * @param array $filters key => value (raw)
     * @param array $extra more ordered parameters: name => value (offset, limit, format)
     * @return string
     */
    public static function url( $view, array $filters, array $extra = array() )
    {
        $url = $view;
        foreach ( array_merge( $filters, $extra ) as $k => $v )
        {
            if ( $v === null || $v === '' || $v === false || ( $k === 'offset' && (int)$v === 0 ) )
                continue;
            if ( is_array( $v ) )
                $v = implode( ':', $v );
            $url .= '/(' . $k . ')/' . rawurlencode( (string)$v );
        }
        return $url;
    }

    /**
     * Brings the index up to date (a bounded run); the cronjob part does the rest.
     *
     * @return array|null the indexer's stats; null without an index
     */
    public static function refreshIndex( $seconds = null )
    {
        if ( !self::indexUsable() )
            return null;
        $settings = expAuditIndexSettings::get();
        $indexer = new expAuditIndexer();
        return $indexer->run( array( 'maxRows' => $settings['batchSize'] * 5, 'maxSeconds' => $seconds === null ? self::OPEN_INDEX_SECONDS : (float)$seconds ) );
    }

    /** @return bool the index is switched on and its tables exist */
    public static function indexUsable()
    {
        if ( !class_exists( 'expAuditIndexer' ) || !expAuditIndexSettings::get()['index'] )
            return false;
        try
        {
            return expAuditIndexSchema::isInstalled();
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /**
     * One page of events: the index (with the records of the live files not indexed yet on the first page), or
     * the files when there is no index.
     *
     * @param array $f normalised filters
     * @param string[]|null $channels
     * @param int $offset
     * @param int $limit
     * @return array rows (index rows), total, source (index | files), unindexed (rows from the files on top)
     */
    public static function page( array $f, $channels, $offset, $limit )
    {
        if ( self::indexUsable() )
        {
            $query = new expAuditQuery();
            $total = $query->count( $f, $channels );
            $rows = $query->fetch( $f, $channels, $offset, $limit );
            if ( $total !== null && $rows !== null )
            {
                $extra = array();
                if ( $offset === 0 )
                {
                    $indexer = new expAuditIndexer();
                    $extra = $indexer->unindexedRows( $f, $channels, $limit );
                    if ( $extra )
                    {
                        $ids = array_flip( array_column( $rows, 'id' ) );
                        $extra = array_values( array_filter( $extra, function ( $r ) use ( $ids ) { return !isset( $ids[$r['id']] ); } ) );
                        $rows = array_slice( array_merge( $extra, $rows ), 0, $limit );
                        usort( $rows, function ( $a, $b ) { return array( (int)$b['time_ms'], $b['id'] ) <=> array( (int)$a['time_ms'], $a['id'] ); } );
                        $total += count( $extra );
                    }
                }
                return array( 'rows' => $rows, 'total' => $total, 'source' => 'index', 'unindexed' => count( $extra ),
                              'fulltext' => $query->fullTextKind() );
            }
        }
        // no index: the newest records of the files, filtered here
        $config = expAuditConfig::get();
        $reader = new expAuditReader( $config['logDir'] );
        $match = $f;
        if ( $channels !== null )
            $match['channels'] = isset( $f['channel'] ) ? array_values( array_intersect( $channels, array( $f['channel'] ) ) ) : $channels;
        $all = array();
        foreach ( $reader->latest( self::FILE_SCAN, isset( $f['channel'] ) ? $f['channel'] : null ) as $rec )
        {
            $line = json_encode( $rec, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
            $file = isset( $rec['file'] ) ? $rec['file'] : '';
            unset( $rec['file'] );
            $row = expAuditIndexRow::fromRecord( $rec, $line, $file );
            if ( $row && expAuditIndexRow::matches( $row, $match ) )
                $all[] = $row;
        }
        return array( 'rows' => array_slice( $all, $offset, $limit ), 'total' => count( $all ), 'source' => 'files',
                      'unindexed' => 0, 'fulltext' => 'like' );
    }

    /**
     * A row in the form the templates show.
     *
     * @param array $row an index row
     * @return array
     */
    public static function view( array $row )
    {
        $ms = (int)$row['time_ms'];
        $rec = isset( $row['record'] ) ? json_decode( (string)$row['record'], true ) : null;
        $rec = is_array( $rec ) ? $rec : array();
        $actor = $row['login'] !== null && $row['login'] !== '' ? $row['login'] : '-';
        if ( $row['user_id'] !== null && $row['user_id'] !== '' )
            $actor .= ' (' . (int)$row['user_id'] . ')';
        $object = self::thing( isset( $rec['object'] ) ? $rec['object'] : null, $row['object_type'], $row['object_id'], $row['object_name'] );
        $target = self::thing( isset( $rec['target'] ) ? $rec['target'] : null, $row['target_type'], $row['target_id'], null );
        return array(
            'id' => $row['id'],
            'time' => date( 'Y-m-d H:i:s', (int)floor( $ms / 1000 ) ),
            'time_short' => date( 'H:i:s', (int)floor( $ms / 1000 ) ),
            'date' => date( 'Y-m-d', (int)floor( $ms / 1000 ) ),
            'time_utc' => gmdate( 'Y-m-d\TH:i:s', (int)floor( $ms / 1000 ) ) . sprintf( '.%03dZ', $ms % 1000 ),
            'time_ms' => $ms,
            'name' => $row['name'],
            'label' => self::label( $row['name'] ),
            'channel' => $row['channel'],
            'seq' => (int)$row['seq'],
            'file' => $row['file_name'],
            'severity' => expAuditIndexRow::severityName( $row['severity'] ),
            'severity_number' => (int)$row['severity'],
            'actor' => $actor,
            'login' => (string)$row['login'],
            'user_id' => $row['user_id'] === null || $row['user_id'] === '' ? 0 : (int)$row['user_id'],
            'ip' => (string)$row['ip'],
            'object' => $object,
            'object_type' => (string)$row['object_type'],
            'object_id' => (string)$row['object_id'],
            'target' => $target,
            'target_type' => (string)$row['target_type'],
            'target_id' => (string)$row['target_id'],
            'result' => (string)$row['result'],
            'reason' => (string)$row['reason'],
            'request' => (string)$row['request_id'],
            'engine' => (string)$row['engine'],
            'siteaccess' => (string)$row['siteaccess'],
            'module_view' => (string)$row['module_view'],
            'job' => (string)$row['job_id'],
            'run' => (string)$row['run_id'],
            'parent' => (string)$row['parent_id'],
            'depth' => (int)$row['depth'],
            'imported' => !empty( $row['imported'] ),
            'pseudonymised' => !empty( $row['pseudonymised'] ),
            // the console filtered by this event's actor, object and request
            'login_url' => (string)$row['login'] !== '' ? self::url( 'audit/console', array( 'login' => $row['login'] ) ) : '',
            'object_url' => (string)$row['object_type'] !== '' ? self::url( 'audit/console', array( 'object' => $row['object_type'] . ( (string)$row['object_id'] !== '' ? ':' . $row['object_id'] : '' ) ) ) : '',
            'request_url' => (string)$row['request_id'] !== '' ? self::url( 'audit/console', array( 'request' => $row['request_id'] ) ) : '',
        );
    }

    /** @return string "node 275 Workout" */
    protected static function thing( $data, $type, $id, $name )
    {
        if ( $type === null || $type === '' )
            return '';
        $text = $type . ( $id !== null && $id !== '' ? ' ' . $id : '' );
        if ( $name === null && is_array( $data ) )
        {
            foreach ( array( 'name', 'login', 'file', 'identifier' ) as $k )
                if ( isset( $data[$k] ) && is_scalar( $data[$k] ) && (string)$data[$k] !== (string)$id )
                {
                    $name = (string)$data[$k];
                    break;
                }
        }
        if ( $name !== null && $name !== '' )
            $text .= ' ' . $name;
        return expAuditIndexRow::cut( $text, 120 );
    }

    /**
     * The label of a name, translated (context kernel/audit).
     *
     * @param string $name
     * @return string
     */
    public static function label( $name )
    {
        $label = class_exists( 'expAuditTaxonomy' ) ? expAuditTaxonomy::label( $name ) : $name;
        return class_exists( 'ezpI18n' ) ? ezpI18n::tr( 'kernel/audit', $label ) : $label;
    }

    /**
     * The chain state of each channel the user may read, without reading the files: the last verification
     * (expAuditVerifier::loadState(), written by every verification: the daily maintenance, exp:audit verify and
     * "Verify now"), and a break the indexer found on the way (expaudit_file), which wins.
     *
     * @param string[]|null $channels
     * @param bool $verify true: verify every readable channel now ("Verify now"), full chain; never on a plain view
     * @return array[] channel, result (intact | repaired | broken | unchecked | empty), files, first_break, verified_at
     */
    public static function chainStates( $channels, $verify = false )
    {
        $config = expAuditConfig::get();
        $reader = new expAuditReader( $config['logDir'] );
        $indexer = self::indexUsable() ? new expAuditIndexer() : null;
        $live = $reader->channels();
        if ( $verify )
        {
            $verifier = new expAuditVerifier( $config['logDir'], new expAuditKeys( $config ), $config['algorithm'] );
            foreach ( array_keys( $live ) as $channel )
            {
                if ( !self::channelAllowed( $channel, $channels ) )
                    continue;
                $v = $verifier->verifyChannel( $channel );
                if ( $indexer )
                    $indexer->storeVerification( $channel, $v, array_keys( $live[$channel]['files'] ) );
            }
        }
        $state = method_exists( 'expAuditVerifier', 'loadState' ) ? expAuditVerifier::loadState( $config['logDir'] ) : array();
        $stored = $indexer ? $indexer->fileStates() : array();
        $out = array();
        foreach ( $live as $channel => $info )
        {
            if ( !self::channelAllowed( $channel, $channels ) )
                continue;
            $s = isset( $state[$channel] ) ? $state[$channel] : null;
            $row = array( 'channel' => $channel, 'result' => $s ? (string)$s['result'] : 'unchecked', 'files' => count( $info['files'] ),
                          'first_break' => $s && !empty( $s['first_break'] ) ? (string)$s['first_break'] : '',
                          'verified_at' => $s && !empty( $s['verified_at'] ) ? date( 'Y-m-d H:i', (int)$s['verified_at'] ) : '',
                          'partial' => $s && !empty( $s['partial'] ) );
            foreach ( array_keys( $info['files'] ) as $file )
            {
                $f = isset( $stored[$channel . "\n" . $file] ) ? $stored[$channel . "\n" . $file] : null;
                if ( $f && $f['verified'] === 'broken' && $row['result'] !== 'broken' )
                {
                    $row['result'] = 'broken';
                    $row['first_break'] = $file . ' line ' . (int)$f['break_line'];
                }
            }
            $out[] = $row;
        }
        return $out;
    }

    /**
     * Records the use of a view (system.audit.read, Q9).
     *
     * @param string $view audit/console
     * @param array $filters
     * @param int|null $count
     * @param array $more
     */
    public static function recordRead( $view, array $filters = array(), $count = null, array $more = array() )
    {
        if ( !class_exists( 'expAudit' ) )
            return;
        $after = array_merge( array( 'filters' => $filters ?: null, 'count' => $count ), $more );
        expAudit::event( 'system.audit.read', array( 'object' => array( 'type' => 'view', 'id' => $view ),
                                                     'after' => array_filter( $after, function ( $v ) { return $v !== null; } ) ) );
    }

    /**
     * The channels to offer in a filter: the configured ones the user may read.
     *
     * @param string[]|null $allowed
     * @return string[]
     */
    public static function channelNames( $allowed )
    {
        $config = expAuditConfig::get();
        $names = array();
        foreach ( $config['channels'] as $c )
            if ( self::channelAllowed( $c, $allowed ) )
                $names[] = $c;
        return $names;
    }

    /**
     * The address of what a record is about, when it has one in the admin.
     *
     * @param string $type node, object, user, role, section, class, job, view, ...
     * @param string $id
     * @return string|null a module/view path
     */
    public static function linkFor( $type, $id )
    {
        if ( $id === null || $id === '' || !preg_match( '/^[A-Za-z0-9_.\/:-]+$/', (string)$id ) )
            return null;
        switch ( $type )
        {
            case 'node':
                return ctype_digit( (string)$id ) ? 'content/view/full/' . (int)$id : null;
            case 'object':
            case 'user':
                if ( !ctype_digit( (string)$id ) || !class_exists( 'eZContentObject' ) )
                    return null;
                $object = eZContentObject::fetch( (int)$id );
                $node = $object ? $object->attribute( 'main_node_id' ) : null;
                return $node ? 'content/view/full/' . (int)$node : null;
            case 'role':
                return ctype_digit( (string)$id ) ? 'role/view/' . (int)$id : null;
            case 'section':
                return ctype_digit( (string)$id ) ? 'section/view/' . (int)$id : null;
            case 'class':
                return ctype_digit( (string)$id ) ? 'class/view/' . (int)$id : null;
            case 'job':
                return 'content/job/' . $id;
            case 'view':
                return strpos( (string)$id, '/' ) !== false ? (string)$id : null;
        }
        return null;
    }
}
