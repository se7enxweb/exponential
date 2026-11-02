<?php
/**
 * File containing the expModerationList class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The list of Dashboard > Recent content (moderation/recent, doc/eng-us/features/6.0/moderation.md): the most recently
 * created or modified objects, newest first, one page at a time, with their status, the review hold, the draft or
 * pending version and its scheduled publication, the creator, and where the submission came from (the address and the
 * browser the audit recorded).
 *
 * The queries are built for a large database on every engine (MySQL/MariaDB, PostgreSQL, SQLite, Oracle, MongoDB):
 *
 *   - every query reads one table (MongoDB answers single-table selects), with literal values only;
 *   - the page is one select on ezcontentobject, ordered by an indexed column (modified: the index
 *     ezcontentobject_modified, published: ezcontentobject_pub) and cut with offset and limit, plus one count;
 *   - a filter on another table (held, hidden, pending, siteaccess) is first read as a list of object ids from that
 *     table's own index, then applied as IN or NOT IN;
 *   - the page is decorated with one query per table for all its rows (locations, versions, states, scheduled
 *     publications, hold data, creators and their locations, audit events): about ten queries whatever the page size,
 *     never one per row.
 *
 * filters(), where(), order() and links are pure and tested without a database (expModerationListTest).
 *
 * @since 6.0.15
 * @package kernel
 */
class expModerationList
{
    const STATUSES = array( 'published', 'draft', 'pending', 'archived' );
    const SORTS = array( 'modified', 'published', 'name', 'class', 'section', 'creator', 'id' );
    const DATES = array( 'modified', 'published' );
    const YES_NO = array( 'yes', 'no' );

    /** The page sizes when the settings name none */
    const DEFAULT_SIZES = array( 25, 100, 300, 700, 1000 );

    /** The largest page whatever the settings say */
    const MAX_SIZE = 1000;

    /** The preference that remembers the user's page size */
    const PREFERENCE = 'admin_moderation_limit';

    /** @var array|null settings handed in by tests */
    public static $settings = null;

    /**
     * The settings: content.ini [ModerationSettings] and the page sizes of admininterface.ini [PaginationSettings]
     * ItemsPerPageList_moderation_recent[] (expAdminPagination; the first is the default).
     *
     * @return array sizes (int[]), default (int), job_threshold (int), events (string[])
     */
    public static function settings()
    {
        if ( is_array( self::$settings ) )
            $raw = self::$settings;
        else
        {
            $ini = eZINI::instance( 'content.ini' );
            $raw = array( 'PageSizes' => class_exists( 'expAdminPagination' ) ? expAdminPagination::sizes( 'moderation/recent', self::DEFAULT_SIZES ) : self::DEFAULT_SIZES );
            foreach ( array( 'JobThreshold', 'SubmissionEvents' ) as $name )
                $raw[$name] = $ini->hasVariable( 'ModerationSettings', $name ) ? $ini->variable( 'ModerationSettings', $name ) : null;
        }
        $sizes = array();
        foreach ( (array)( isset( $raw['PageSizes'] ) ? $raw['PageSizes'] : array() ) as $size )
            if ( is_numeric( $size ) && (int)$size > 0 && (int)$size <= self::MAX_SIZE && !in_array( (int)$size, $sizes, true ) )
                $sizes[] = (int)$size;
        $sizes = $sizes ? $sizes : self::DEFAULT_SIZES;
        $events = array();
        foreach ( (array)( isset( $raw['SubmissionEvents'] ) ? $raw['SubmissionEvents'] : array() ) as $event )
            if ( preg_match( '/^[a-z0-9_.]+$/', trim( (string)$event ) ) )
                $events[] = trim( (string)$event );
        return array( 'sizes' => $sizes, 'default' => $sizes[0],
                      'job_threshold' => isset( $raw['JobThreshold'] ) && is_numeric( $raw['JobThreshold'] ) ? max( 0, (int)$raw['JobThreshold'] ) : 100,
                      'events' => $events ? array_values( array_unique( $events ) ) : array( 'content.object.hold.auto', 'content.object.publish', 'content.object.create' ) );
    }

    /**
     * The page size of the current user: the position stored in the preference admin_moderation_limit (counting from
     * one, as the administration's other lists keep it), else the first size.
     *
     * @param int[] $sizes
     * @param mixed $choice the preference's value
     * @return int
     */
    public static function chosenSize( array $sizes, $choice )
    {
        $choice = (int)$choice;
        return $choice >= 1 && $choice <= count( $sizes ) ? (int)$sizes[$choice - 1] : (int)$sizes[0];
    }

    /**
     * A date of the filter (YYYY-MM-DD) as a timestamp: the start of the day, or its end for "to". Pure.
     *
     * @param mixed $value
     * @param bool $end
     * @return int 0 when empty or not a date
     */
    public static function parseDate( $value, $end = false )
    {
        $value = trim( (string)$value );
        if ( !preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m ) || !checkdate( (int)$m[2], (int)$m[3], (int)$m[1] ) )
            return 0;
        return $end ? mktime( 23, 59, 59, (int)$m[2], (int)$m[3], (int)$m[1] ) : mktime( 0, 0, 0, (int)$m[2], (int)$m[3], (int)$m[1] );
    }

    /**
     * The filters, sort and page of a request, checked. Unknown values fall back to their default. Pure.
     *
     * @param array $in from, to (YYYY-MM-DD), date (modified|published), class (identifier), status, hidden, held
     *                  (yes|no), section (id), siteaccess, creator (user id), sort, order (asc|desc), offset, limit
     * @param int[] $sizes the page sizes allowed
     * @param int $defaultSize
     * @return array the same keys, normalised; from_ts and to_ts the timestamps
     */
    public static function filters( array $in, array $sizes, $defaultSize )
    {
        $get = function ( $key ) use ( $in ) { return isset( $in[$key] ) && !is_array( $in[$key] ) ? trim( (string)$in[$key] ) : ''; };
        $f = array();
        $f['from'] = self::parseDate( $get( 'from' ) ) ? $get( 'from' ) : '';
        $f['to'] = self::parseDate( $get( 'to' ), true ) ? $get( 'to' ) : '';
        $f['from_ts'] = self::parseDate( $f['from'] );
        $f['to_ts'] = self::parseDate( $f['to'], true );
        if ( $f['from_ts'] && $f['to_ts'] && $f['from_ts'] > $f['to_ts'] )
        {
            list( $f['from'], $f['to'] ) = array( $f['to'], $f['from'] );
            $f['from_ts'] = self::parseDate( $f['from'] );
            $f['to_ts'] = self::parseDate( $f['to'], true );
        }
        $f['date'] = in_array( $get( 'date' ), self::DATES, true ) ? $get( 'date' ) : 'modified';
        $f['class'] = preg_match( '/^[A-Za-z0-9_]{1,50}$/', $get( 'class' ) ) ? $get( 'class' ) : '';
        $f['status'] = in_array( $get( 'status' ), self::STATUSES, true ) ? $get( 'status' ) : '';
        $f['hidden'] = in_array( $get( 'hidden' ), self::YES_NO, true ) ? $get( 'hidden' ) : '';
        $f['held'] = in_array( $get( 'held' ), self::YES_NO, true ) ? $get( 'held' ) : '';
        $f['section'] = ctype_digit( $get( 'section' ) ) && (int)$get( 'section' ) > 0 ? (int)$get( 'section' ) : 0;
        $f['siteaccess'] = preg_match( '/^[A-Za-z0-9_-]{1,64}$/', $get( 'siteaccess' ) ) ? $get( 'siteaccess' ) : '';
        $f['creator'] = ctype_digit( $get( 'creator' ) ) && (int)$get( 'creator' ) > 0 ? (int)$get( 'creator' ) : 0;
        $f['sort'] = in_array( $get( 'sort' ), self::SORTS, true ) ? $get( 'sort' ) : $f['date'];
        $f['order'] = strtolower( $get( 'order' ) ) === 'asc' ? 'asc' : 'desc';
        $sizes = array_values( array_filter( array_map( 'intval', $sizes ) ) );
        $f['limit'] = in_array( (int)$get( 'limit' ), $sizes, true ) ? (int)$get( 'limit' ) : ( in_array( (int)$defaultSize, $sizes, true ) ? (int)$defaultSize : ( $sizes ? $sizes[0] : 25 ) );
        $offset = ctype_digit( $get( 'offset' ) ) ? (int)$get( 'offset' ) : 0;
        // always the first row of a page of this size
        $f['offset'] = $offset - $offset % max( 1, $f['limit'] );
        return $f;
    }

    /**
     * The filters as the unordered view parameters of a link, without the defaults; $change replaces some. Pure.
     *
     * @param array $f filters()
     * @param array $change key => value ('' or 0 removes it)
     * @param bool $withOffset
     * @return string e.g. /(status)/draft/(held)/yes
     */
    public static function viewParameters( array $f, array $change = array(), $withOffset = false )
    {
        $f = array_merge( $f, $change );
        $out = '';
        foreach ( array( 'from', 'to', 'date', 'class', 'status', 'hidden', 'held', 'section', 'siteaccess', 'creator', 'sort', 'order' ) as $key )
        {
            if ( !isset( $f[$key] ) || $f[$key] === '' || $f[$key] === 0 || $f[$key] === null )
                continue;
            if ( $key === 'date' && $f[$key] === 'modified' )
                continue;
            if ( $key === 'sort' && $f[$key] === ( isset( $f['date'] ) ? $f['date'] : 'modified' ) )
                continue;
            if ( $key === 'order' && $f[$key] === 'desc' )
                continue;
            $out .= '/(' . $key . ')/' . rawurlencode( (string)$f[$key] );
        }
        if ( $withOffset && !empty( $f['offset'] ) )
            $out .= '/(offset)/' . (int)$f['offset'];
        return $out;
    }

    /**
     * The conditions on ezcontentobject. Pure.
     *
     * @param array $f filters()
     * @param array $sets class_id (int|null: the class filter resolved, 0: no such class), in (list of int[]: each
     *                    the ids the row must be one of), not_in (list of int[]: ids it must not be)
     * @return string[]|null the conditions (ANDed), null when nothing can match
     */
    public static function where( array $f, array $sets )
    {
        $where = array();
        $column = $f['date'] === 'published' ? 'published' : 'modified';
        if ( $f['from_ts'] )
            $where[] = $column . ' >= ' . (int)$f['from_ts'];
        if ( $f['to_ts'] )
            $where[] = $column . ' <= ' . (int)$f['to_ts'];
        if ( $f['date'] === 'published' && !$f['from_ts'] )
            $where[] = 'published > 0';
        if ( $f['class'] !== '' )
        {
            if ( empty( $sets['class_id'] ) )
                return null;
            $where[] = 'contentclass_id = ' . (int)$sets['class_id'];
        }
        switch ( $f['status'] )
        {
            case 'published':
                $where[] = 'status = ' . (int)eZContentObject::STATUS_PUBLISHED;
                break;
            case 'draft':
                $where[] = 'status = ' . (int)eZContentObject::STATUS_DRAFT;
                break;
            case 'archived':
                $where[] = 'status = ' . (int)eZContentObject::STATUS_ARCHIVED;
                break;
        }
        if ( $f['section'] )
            $where[] = 'section_id = ' . (int)$f['section'];
        if ( $f['creator'] )
            $where[] = 'owner_id = ' . (int)$f['creator'];
        foreach ( isset( $sets['in'] ) ? (array)$sets['in'] : array() as $ids )
        {
            $ids = array_values( array_unique( array_map( 'intval', (array)$ids ) ) );
            if ( !$ids )
                return null;
            $where[] = 'id IN (' . implode( ', ', $ids ) . ')';
        }
        foreach ( isset( $sets['not_in'] ) ? (array)$sets['not_in'] : array() as $ids )
        {
            $ids = array_values( array_unique( array_map( 'intval', (array)$ids ) ) );
            if ( $ids )
                $where[] = 'id NOT IN (' . implode( ', ', $ids ) . ')';
        }
        return $where;
    }

    /**
     * The order of the page: the chosen column, then the id, the same way. Pure.
     *
     * @param array $f
     * @return string
     */
    public static function order( array $f )
    {
        $columns = array( 'modified' => 'modified', 'published' => 'published', 'name' => 'name', 'class' => 'contentclass_id',
                          'section' => 'section_id', 'creator' => 'owner_id', 'id' => 'id' );
        $dir = $f['order'] === 'asc' ? 'ASC' : 'DESC';
        $column = isset( $columns[$f['sort']] ) ? $columns[$f['sort']] : 'modified';
        return $column === 'id' ? "id $dir" : "$column $dir, id $dir";
    }

    /**
     * The status of a row, as the page shows it. Pure.
     *
     * @param int $objectStatus ezcontentobject.status
     * @param int[] $versionStatuses the statuses of its versions besides the published one
     * @return array status (published, draft, archived), pending (bool), draft (bool: a draft newer than the published version)
     */
    public static function status( $objectStatus, array $versionStatuses )
    {
        $status = (int)$objectStatus === eZContentObject::STATUS_PUBLISHED ? 'published'
                : ( (int)$objectStatus === eZContentObject::STATUS_ARCHIVED ? 'archived' : 'draft' );
        $versionStatuses = array_map( 'intval', $versionStatuses );
        return array( 'status' => $status,
                      'pending' => in_array( eZContentObjectVersion::STATUS_PENDING, $versionStatuses, true ),
                      'draft' => $status === 'published' && in_array( eZContentObjectVersion::STATUS_DRAFT, $versionStatuses, true ) );
    }

    /**
     * Pure: the submission of an object from its audit events (rows with name, ip, ua, siteaccess, time_ms): the
     * earliest of them.
     *
     * @param array $rows
     * @return array|null ip, ua, siteaccess, time (seconds), event
     */
    public static function submission( array $rows )
    {
        $first = null;
        foreach ( $rows as $row )
            if ( $first === null || (int)$row['time_ms'] < (int)$first['time_ms'] )
                $first = $row;
        if ( $first === null )
            return null;
        return array( 'ip' => isset( $first['ip'] ) ? (string)$first['ip'] : '', 'ua' => isset( $first['ua'] ) ? (string)$first['ua'] : '',
                      'siteaccess' => isset( $first['siteaccess'] ) ? (string)$first['siteaccess'] : '',
                      'time' => (int)floor( (int)$first['time_ms'] / 1000 ), 'event' => (string)$first['name'] );
    }

    // ---- with the database ------------------------------------------------------------------------------------------

    /** @var eZDBInterface */
    protected $db;

    /** @var bool whether the audit index can be asked */
    protected $audit;

    /** @var int number of queries run (the tests and the page's footer) */
    public $queries = 0;

    /**
     * @param eZDBInterface|null $db
     * @param bool|null $audit null: the audit index when it is switched on and its tables exist
     */
    public function __construct( $db = null, $audit = null )
    {
        $this->db = $db ?: eZDB::instance();
        if ( $audit === null )
            $audit = class_exists( 'expAuditConsole' ) && expAuditConsole::indexUsable();
        $this->audit = (bool)$audit;
    }

    /** @return bool the audit index is there */
    public function auditUsable()
    {
        return $this->audit;
    }

    protected function rows( $sql, array $params = array() )
    {
        $this->queries++;
        $rows = $this->db->arrayQuery( $sql, $params );
        return is_array( $rows ) ? $rows : array();
    }

    /** @return int[] one column of a query, as ints */
    protected function ids( $sql, $column = 'contentobject_id' )
    {
        $ids = array();
        foreach ( $this->rows( $sql ) as $row )
            $ids[(int)$row[$column]] = (int)$row[$column];
        return array_values( $ids );
    }

    /** @return string a list of ints for IN () */
    protected static function idList( array $ids )
    {
        return implode( ', ', array_map( 'intval', $ids ) );
    }

    /** @return string a list of quoted strings for IN () */
    protected function stringList( array $values )
    {
        $db = $this->db;
        return implode( ', ', array_map( function ( $v ) use ( $db ) { return "'" . $db->escapeString( (string)$v ) . "'"; }, $values ) );
    }

    /**
     * The id sets of the filters that read other tables.
     *
     * @param array $f
     * @return array class_id, in, not_in
     */
    public function sets( array $f )
    {
        $sets = array( 'class_id' => null, 'in' => array(), 'not_in' => array() );
        if ( $f['class'] !== '' )
        {
            $class = eZContentClass::fetchByIdentifier( $f['class'], false );
            $sets['class_id'] = is_array( $class ) ? (int)$class['id'] : 0;
        }
        if ( $f['held'] !== '' )
        {
            $held = class_exists( 'expReviewHold' ) ? expReviewHold::state( expReviewHold::STATE_HELD ) : null;
            $ids = $held ? $this->ids( 'SELECT contentobject_id FROM ezcobj_state_link WHERE contentobject_state_id = ' . (int)$held->attribute( 'id' ) ) : array();
            $sets[$f['held'] === 'yes' ? 'in' : 'not_in'][] = $ids;
        }
        if ( $f['hidden'] !== '' )
            $sets[$f['hidden'] === 'yes' ? 'in' : 'not_in'][] = $this->ids( 'SELECT contentobject_id FROM ezcontentobject_tree WHERE is_hidden = 1' );
        if ( $f['status'] === 'pending' )
            $sets['in'][] = $this->ids( 'SELECT contentobject_id FROM ezcontentobject_version WHERE status = ' . (int)eZContentObjectVersion::STATUS_PENDING );
        if ( $f['siteaccess'] !== '' )
        {
            $ids = array();
            if ( $this->audit )
            {
                $events = self::settings()['events'];
                foreach ( $this->rows( "SELECT object_id FROM expaudit_event WHERE object_type = 'object' AND siteaccess = '" . $this->db->escapeString( $f['siteaccess'] )
                                       . "' AND name IN (" . $this->stringList( $events ) . ')' ) as $row )
                    if ( ctype_digit( (string)$row['object_id'] ) )
                        $ids[(int)$row['object_id']] = (int)$row['object_id'];
            }
            $sets['in'][] = array_values( $ids );
        }
        return $sets;
    }

    /**
     * One page: the rows, decorated, and the total.
     *
     * @param array $f filters()
     * @param bool $personal read the address and the browser (moderation/personaldata)
     * @return array rows, total, offset, limit, queries, audit (the index was asked)
     */
    public function page( array $f, $personal = false )
    {
        $where = self::where( $f, $this->sets( $f ) );
        if ( $where === null )
            return array( 'rows' => array(), 'total' => 0, 'offset' => 0, 'limit' => $f['limit'], 'queries' => $this->queries, 'audit' => $this->audit );
        $condition = $where ? ' WHERE ' . implode( ' AND ', $where ) : '';
        $count = $this->rows( 'SELECT COUNT(*) AS c FROM ezcontentobject' . $condition );
        $total = $count ? (int)$count[0]['c'] : 0;
        $offset = $f['offset'] < $total ? $f['offset'] : 0;
        $rows = $total ? $this->rows( 'SELECT id, name, contentclass_id, owner_id, section_id, status, published, modified, current_version FROM ezcontentobject'
                                      . $condition . ' ORDER BY ' . self::order( $f ), array( 'offset' => $offset, 'limit' => $f['limit'] ) ) : array();
        return array( 'rows' => $this->decorate( $rows, $personal ), 'total' => $total, 'offset' => $offset, 'limit' => $f['limit'],
                      'queries' => $this->queries, 'audit' => $this->audit );
    }

    /**
     * Adds to the rows of a page what the page shows, with one query per table.
     *
     * @param array $rows ezcontentobject rows
     * @param bool $personal
     * @return array
     */
    public function decorate( array $rows, $personal = false )
    {
        if ( !$rows )
            return array();
        $ids = array();
        $owners = array();
        foreach ( $rows as $row )
        {
            $ids[] = (int)$row['id'];
            if ( (int)$row['owner_id'] > 0 )
                $owners[(int)$row['owner_id']] = (int)$row['owner_id'];
        }
        $in = self::idList( $ids );

        // locations: the main one, hidden or not
        $nodes = array();
        foreach ( $this->rows( "SELECT node_id, contentobject_id, main_node_id, is_hidden, is_invisible FROM ezcontentobject_tree WHERE contentobject_id IN ($in)" ) as $n )
            $nodes[(int)$n['contentobject_id']][] = $n;

        // versions other than the published one: drafts and pending versions, newest first
        $versions = array();
        foreach ( $this->rows( "SELECT contentobject_id, version, status, modified FROM ezcontentobject_version WHERE contentobject_id IN ($in) AND status IN ("
                               . (int)eZContentObjectVersion::STATUS_DRAFT . ', ' . (int)eZContentObjectVersion::STATUS_PENDING . ')' ) as $v )
            $versions[(int)$v['contentobject_id']][] = $v;

        // scheduled publication: a workflow process waiting until a date (the event "Wait until date")
        $scheduled = array();
        foreach ( $this->rows( "SELECT content_id, content_version, activation_date FROM ezworkflow_process WHERE content_id IN ($in)" ) as $p )
            if ( (int)$p['activation_date'] > 0 )
                $scheduled[(int)$p['content_id']][(int)$p['content_version']] = (int)$p['activation_date'];

        // the review hold
        $held = array();
        $heldState = class_exists( 'expReviewHold' ) ? expReviewHold::state( expReviewHold::STATE_HELD ) : null;
        if ( $heldState )
            foreach ( $this->ids( "SELECT contentobject_id FROM ezcobj_state_link WHERE contentobject_id IN ($in) AND contentobject_state_id = " . (int)$heldState->attribute( 'id' ) ) as $id )
                $held[$id] = true;
        $meta = array();
        if ( $held && class_exists( 'expReviewHoldAuto' ) )
        {
            $this->queries++;
            $meta = expReviewHoldAuto::metaList( array_keys( $held ) );
        }

        // creators: their names and main locations
        $ownerNames = array();
        $ownerNodes = array();
        if ( $owners )
        {
            $ownerIn = self::idList( $owners );
            foreach ( $this->rows( "SELECT id, name FROM ezcontentobject WHERE id IN ($ownerIn)" ) as $o )
                $ownerNames[(int)$o['id']] = (string)$o['name'];
            foreach ( $this->rows( "SELECT node_id, contentobject_id, main_node_id FROM ezcontentobject_tree WHERE contentobject_id IN ($ownerIn)" ) as $n )
                if ( (int)$n['node_id'] === (int)$n['main_node_id'] )
                    $ownerNodes[(int)$n['contentobject_id']] = (int)$n['node_id'];
        }

        // where the submission came from
        $events = array();
        if ( $personal && $this->audit )
        {
            $quoted = array_map( 'strval', $ids );
            foreach ( $this->rows( "SELECT object_id, name, ip, ua, siteaccess, time_ms FROM expaudit_event WHERE object_type = 'object' AND object_id IN ("
                                   . $this->stringList( $quoted ) . ') AND name IN (' . $this->stringList( self::settings()['events'] ) . ')' ) as $e )
                $events[(int)$e['object_id']][] = $e;
        }

        $classes = self::classNames();
        $sections = self::sectionNames();
        $out = array();
        foreach ( $rows as $row )
        {
            $id = (int)$row['id'];
            $main = 0;
            $hidden = false;
            $invisible = false;
            foreach ( isset( $nodes[$id] ) ? $nodes[$id] : array() as $n )
            {
                if ( (int)$n['node_id'] === (int)$n['main_node_id'] )
                {
                    $main = (int)$n['node_id'];
                    $hidden = (bool)$n['is_hidden'];
                    $invisible = (bool)$n['is_invisible'];
                }
            }
            $other = isset( $versions[$id] ) ? $versions[$id] : array();
            usort( $other, function ( $a, $b ) { return (int)$b['version'] <=> (int)$a['version']; } );
            $status = self::status( (int)$row['status'], array_column( $other, 'status' ) );
            $latest = $other ? $other[0] : null;
            $when = null;
            foreach ( isset( $scheduled[$id] ) ? $scheduled[$id] : array() as $version => $date )
                $when = $when === null ? $date : min( $when, $date );
            $classID = (int)$row['contentclass_id'];
            $owner = (int)$row['owner_id'];
            $m = isset( $meta[$id] ) ? $meta[$id] : null;
            $submission = isset( $events[$id] ) ? self::submission( $events[$id] ) : null;
            $out[] = array(
                'id' => $id,
                'name' => (string)$row['name'],
                'class_identifier' => isset( $classes[$classID] ) ? $classes[$classID]['identifier'] : '',
                'class_name' => isset( $classes[$classID] ) ? $classes[$classID]['name'] : (string)$classID,
                'status' => $status['status'],
                'pending' => $status['pending'],
                'has_draft' => $status['draft'],
                'version' => $latest ? (int)$latest['version'] : (int)$row['current_version'],
                'version_status' => $latest ? ( (int)$latest['status'] === eZContentObjectVersion::STATUS_PENDING ? 'pending' : 'draft' ) : 'published',
                'version_modified' => $latest ? (int)$latest['modified'] : 0,
                'current_version' => (int)$row['current_version'],
                'scheduled' => $when === null ? 0 : (int)$when,
                'published' => (int)$row['published'],
                'modified' => (int)$row['modified'],
                'section_id' => (int)$row['section_id'],
                'section_name' => isset( $sections[(int)$row['section_id']] ) ? $sections[(int)$row['section_id']] : (string)$row['section_id'],
                'owner_id' => $owner,
                'owner_name' => isset( $ownerNames[$owner] ) ? $ownerNames[$owner] : '',
                'owner_node_id' => isset( $ownerNodes[$owner] ) ? $ownerNodes[$owner] : 0,
                'node_id' => $main,
                'hidden' => $hidden,
                'invisible' => $invisible,
                'held' => isset( $held[$id] ),
                'auto' => is_array( $m ) && !empty( $m['auto'] ),
                'hold_reason' => is_array( $m ) && isset( $m['reason'] ) ? (string)$m['reason'] : '',
                'hold_by' => is_array( $m ) && isset( $m['name'] ) ? (string)$m['name'] : '',
                'recorded' => $submission !== null,
                'ip' => $submission ? $submission['ip'] : '',
                'ua' => $submission ? $submission['ua'] : '',
                'siteaccess' => $submission ? $submission['siteaccess'] : '',
            );
        }
        return $out;
    }

    /** @return array class id => array( identifier, name ), once per request */
    public static function classNames()
    {
        static $cache = null;
        if ( $cache === null )
        {
            $cache = array();
            foreach ( (array)eZContentClass::fetchList( eZContentClass::VERSION_STATUS_DEFINED, true ) as $class )
                $cache[(int)$class->attribute( 'id' )] = array( 'identifier' => (string)$class->attribute( 'identifier' ), 'name' => (string)$class->attribute( 'name' ) );
            uasort( $cache, function ( $a, $b ) { return strcasecmp( $a['name'], $b['name'] ); } );
        }
        return $cache;
    }

    /** @return array section id => name, once per request */
    public static function sectionNames()
    {
        static $cache = null;
        if ( $cache === null )
        {
            $cache = array();
            foreach ( (array)eZSection::fetchList( true ) as $section )
                $cache[(int)$section->attribute( 'id' )] = (string)$section->attribute( 'name' );
        }
        return $cache;
    }

    /**
     * The counts of the dashboard block: objects held now, and objects created today (first published or, for drafts,
     * modified since midnight).
     *
     * @return array held, new_today, auto (held automatically)
     */
    public function counts()
    {
        $midnight = mktime( 0, 0, 0 );
        $held = 0;
        $state = class_exists( 'expReviewHold' ) ? expReviewHold::state( expReviewHold::STATE_HELD ) : null;
        if ( $state )
        {
            $rows = $this->rows( 'SELECT COUNT(*) AS c FROM ezcobj_state_link WHERE contentobject_state_id = ' . (int)$state->attribute( 'id' ) );
            $held = $rows ? (int)$rows[0]['c'] : 0;
        }
        $rows = $this->rows( 'SELECT COUNT(*) AS c FROM ezcontentobject WHERE published >= ' . (int)$midnight );
        $new = $rows ? (int)$rows[0]['c'] : 0;
        return array( 'held' => $held, 'new_today' => $new, 'today' => date( 'Y-m-d', $midnight ) );
    }
}
