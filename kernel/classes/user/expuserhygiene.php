<?php
/**
 * File containing the expUserHygiene class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The accounts the page user/hygiene ("Check user accounts") lists: every user account, or those below a user group,
 * narrowed by one filter (never signed in, not seen for N days, disabled, locked after failed sign-ins, orphaned, sharing a login,
 * sharing an e-mail address with another account) and a search, ordered and paged, with their state, last visit,
 * number of sign-ins, groups and roles; the bulk actions disable, enable and unlock; and the CSV of the list.
 *
 * Made for many accounts: the list and its count are one query each over ezuser with its one-row companions
 * (ezuser_setting, ezuservisit, ezcontentobject), and the groups, roles, account keys and shared addresses of a page
 * are read with one query each for the whole page, never one per row (see enrich()). The CSV is read in batches by
 * the id of the account (keyset, not offset).
 *
 * The database work goes through two callables (query, escape), so the SQL of every filter runs in the tests against
 * an SQLite database made for them (tests/tests/kernel/classes/user/expUserHygieneTest.php); fromDatabase() gives the
 * instance of this installation. Which account an action may touch is decided by decide(), a plain function.
 *
 * Accounts whose object is in the trash (status archived) are not listed. The last visit is the time of the last
 * sign-in or of the last request after a longer pause (ezuservisit.current_visit_timestamp, which a sign-out stores
 * negated), not the visit before it that eZUser::lastVisit() gives.
 *
 * Guide: doc/features/6.0/user-hygiene.md
 */
class expUserHygiene
{
    /** The filters of the page, the first is the default */
    const FILTERS = array( 'all', 'never', 'inactive', 'disabled', 'locked', 'orphaned', 'duplicate_login', 'duplicate_email' );

    /** The orders the page offers: the name the address carries => the column */
    const SORTS = array(
        'name' => 'LOWER( COALESCE( o.name, u.login ) )',
        'login' => 'LOWER( u.login )',
        'email' => 'LOWER( u.email )',
        'last_visit' => 'COALESCE( ABS( v.current_visit_timestamp ), 0 )',
        'login_count' => 'COALESCE( v.login_count, 0 )',
        'id' => 'u.contentobject_id',
    );

    /** The bulk actions */
    const ACTIONS = array( 'disable', 'enable', 'unlock' );

    /** The page sizes the page offers, the first is the default */
    const LIMITS = array( 25, 50, 100 );

    /** "Not seen for N days" without a number, and the bounds of the number */
    const DEFAULT_DAYS = 180;
    const MAX_DAYS = 36500;

    /** The administrator account of a clean installation, never touched by an action */
    const ADMIN_USER_ID = 14;

    /** Accounts the CSV holds at most, and reads per query */
    const EXPORT_LIMIT = 100000;
    const EXPORT_BATCH = 500;

    /** The translation context of the texts of this class and of its page */
    const CONTEXT = 'design/admin/user/hygiene';

    /** @var callable ( string $sql, int $offset, int $limit ) => array rows; $limit 0 = all */
    protected $query;

    /** @var callable ( string $text ) => string, escaped for a quoted SQL string */
    protected $escape;

    /** @var int */
    protected $now;

    /** @var int [UserSettings] MaxNumberOfFailedLogin, 0 = no locking */
    protected $maxFailed;

    /** @var int [UserSettings] AnonymousUserID, 0 = none: never counted as "never signed in" (it cannot sign in) */
    protected $anonymousID;

    /**
     * @param callable $query ( string $sql, int $offset, int $limit ) => array rows; $limit 0 means all
     * @param callable $escape ( string $text ) => string, escaped for a quoted SQL string
     * @param int|null $now the time the days are counted to
     * @param int $maxFailed [UserSettings] MaxNumberOfFailedLogin
     * @param int $anonymousID [UserSettings] AnonymousUserID
     */
    public function __construct( $query, $escape, $now = null, $maxFailed = 0, $anonymousID = 0 )
    {
        $this->query = $query;
        $this->escape = $escape;
        $this->now = $now === null ? time() : (int)$now;
        $this->maxFailed = max( 0, (int)$maxFailed );
        $this->anonymousID = max( 0, (int)$anonymousID );
    }

    /**
     * The instance of this installation's database, or null on MongoDB (the list is read with SQL).
     *
     * @return expUserHygiene|null
     */
    public static function fromDatabase()
    {
        $db = eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
            return null;
        return new self(
            function ( $sql, $offset, $limit ) use ( $db )
            {
                $params = $limit > 0 ? array( 'offset' => max( 0, (int)$offset ), 'limit' => (int)$limit ) : array();
                $rows = $db->arrayQuery( $sql, $params );
                return is_array( $rows ) ? $rows : array();
            },
            function ( $text ) use ( $db )
            {
                return $db->escapeString( (string)$text );
            },
            null,
            (int)eZUser::maxNumberOfFailedLogin(),
            (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' )
        );
    }

    /**
     * The filters a viewer may use: FILTERS, without duplicate_email for a viewer who may not see e-mail addresses
     * (which accounts share an address is itself something about their addresses).
     *
     * @param bool $canSeeEmail
     * @return string[]
     */
    public static function filters( $canSeeEmail )
    {
        return $canSeeEmail ? self::FILTERS : array_values( array_diff( self::FILTERS, array( 'duplicate_email' ) ) );
    }

    /**
     * The filters of a request (its GET variables), each normalised: filter (one of filters()), days (1 to MAX_DAYS),
     * group (a node id or 0), q (the search), sort (one of SORTS; email only for a viewer who may see e-mail
     * addresses), order (asc, desc), limit (one of LIMITS).
     *
     * @param array $input
     * @param bool $canSeeEmail
     * @return array
     */
    public static function normaliseFilters( array $input, $canSeeEmail )
    {
        $get = function ( $name ) use ( $input )
        {
            return isset( $input[$name] ) && is_scalar( $input[$name] ) ? (string)$input[$name] : '';
        };
        $filter = in_array( $get( 'filter' ), self::filters( $canSeeEmail ), true ) ? $get( 'filter' ) : self::FILTERS[0];
        $days = ctype_digit( $get( 'days' ) ) ? (int)$get( 'days' ) : self::DEFAULT_DAYS;
        $days = min( self::MAX_DAYS, max( 1, $days ) );
        $group = ctype_digit( $get( 'group' ) ) ? (int)$get( 'group' ) : 0;
        $sort = array_key_exists( $get( 'sort' ), self::SORTS ) ? $get( 'sort' ) : '';
        if ( $sort === '' || ( $sort === 'email' && !$canSeeEmail ) )
            $sort = 'name';
        $order = strtolower( $get( 'order' ) ) === 'desc' ? 'desc' : 'asc';
        $limit = in_array( (int)$get( 'limit' ), self::LIMITS, true ) ? (int)$get( 'limit' ) : self::LIMITS[0];
        return array( 'filter' => $filter, 'days' => $days, 'group' => $group,
                      'q' => expUnactivatedUsers::normaliseSearch( $get( 'q' ) ),
                      'sort' => $sort, 'order' => $order, 'limit' => $limit );
    }

    /**
     * The query string of a set of filters, for links and forms: only the values that differ from the defaults,
     * with an optional change (e.g. array( 'sort' => 'login' )). Starts with "?" unless empty.
     *
     * @param array $filters see normaliseFilters()
     * @param array $change
     * @return string
     */
    public static function queryString( array $filters, array $change = array() )
    {
        $filters = $change + $filters;
        $defaults = array( 'filter' => self::FILTERS[0], 'days' => self::DEFAULT_DAYS, 'group' => 0, 'q' => '',
                           'sort' => 'name', 'order' => 'asc', 'limit' => self::LIMITS[0] );
        $parts = array();
        foreach ( $defaults as $name => $default )
        {
            if ( !isset( $filters[$name] ) || (string)$filters[$name] === (string)$default )
                continue;
            if ( $name === 'days' && $filters['filter'] !== 'inactive' )
                continue;
            $parts[$name] = (string)$filters[$name];
        }
        return $parts ? '?' . http_build_query( $parts, '', '&', PHP_QUERY_RFC3986 ) : '';
    }

    /**
     * The query strings of the page's links (see queryString()): filters (filter => the list with that filter, from
     * its first page; shared e-mail addresses ordered by address), sorts (column => that order; the current column reversed; a new column descending for the
     * last visit and the sign-ins, ascending for the rest; email only with $canSeeEmail) and clear (no search, every
     * group).
     *
     * @param array $filters see normaliseFilters()
     * @param bool $canSeeEmail
     * @return array
     */
    public static function links( array $filters, $canSeeEmail )
    {
        $links = array( 'filters' => array(), 'sorts' => array(),
                        'clear' => self::queryString( $filters, array( 'q' => '', 'group' => 0 ) ) );
        foreach ( self::filters( $canSeeEmail ) as $filter )
        {
            $change = array( 'filter' => $filter );
            // the accounts of one address side by side
            if ( $filter === 'duplicate_email' )
                $change += array( 'sort' => 'email', 'order' => 'asc' );
            elseif ( $filter === 'duplicate_login' )
                $change += array( 'sort' => 'login', 'order' => 'asc' );
            $links['filters'][$filter] = self::queryString( $filters, $change );
        }
        foreach ( array( 'name', 'login', 'email', 'last_visit', 'login_count' ) as $column )
        {
            if ( $column === 'email' && !$canSeeEmail )
                continue;
            if ( $column === $filters['sort'] )
                $order = $filters['order'] === 'asc' ? 'desc' : 'asc';
            else
                $order = in_array( $column, array( 'last_visit', 'login_count' ), true ) ? 'desc' : 'asc';
            $links['sorts'][$column] = self::queryString( $filters, array( 'sort' => $column, 'order' => $order ) );
        }
        return $links;
    }

    /**
     * The path_string of a node a list may be narrowed to: digits between slashes, else null.
     *
     * @param mixed $path
     * @return string|null
     */
    public static function normalisePath( $path )
    {
        return is_string( $path ) && preg_match( '#^/(?:[0-9]+/)+$#', $path ) ? $path : null;
    }

    /**
     * The FROM and WHERE of the list, its count and the CSV.
     *
     * @param array $filters see normaliseFilters()
     * @param bool $searchEmail whether the search looks into the e-mail addresses (only for a viewer who sees them)
     * @param string|null $groupPath the path_string of the group node of $filters['group'], null for every account
     * @param int $afterID only accounts with a higher id (the batches of the CSV)
     * @return string
     */
    public function fromWhere( array $filters, $searchEmail, $groupPath = null, $afterID = 0 )
    {
        $sql = ' FROM ezuser u'
             . ' LEFT JOIN ezuser_setting s ON s.user_id = u.contentobject_id'
             . ' LEFT JOIN ezuservisit v ON v.user_id = u.contentobject_id'
             . ' LEFT JOIN ezcontentobject o ON o.id = u.contentobject_id'
             // an account whose object is in the trash is not listed
             . ' WHERE ( o.id IS NULL OR o.status <> ' . (int)eZContentObject::STATUS_ARCHIVED . ' )';

        switch ( $filters['filter'] )
        {
            case 'never':
                // the anonymous user cannot sign in: it is not an account nobody uses
                $sql .= ' AND ( v.user_id IS NULL OR v.login_count = 0 )'
                      . ( $this->anonymousID > 0 ? ' AND u.contentobject_id <> ' . $this->anonymousID : '' );
                break;
            case 'inactive':
                $cutoff = $this->now - (int)$filters['days'] * 86400;
                $sql .= ' AND v.login_count > 0 AND ABS( v.current_visit_timestamp ) < ' . (int)$cutoff;
                break;
            case 'disabled':
                $sql .= ' AND s.is_enabled = 0';
                break;
            case 'locked':
                // eZUser::isEnabledAfterFailedLogin(): locked once the attempts exceed the limit; no limit, no lock
                $sql .= $this->maxFailed > 0 ? ' AND v.failed_login_attempts > ' . $this->maxFailed : ' AND 1 = 0';
                break;
            case 'orphaned':
                // no object, an object that was never published, or one without a location in the tree
                $sql .= ' AND ( o.id IS NULL OR o.status <> ' . (int)eZContentObject::STATUS_PUBLISHED
                      . ' OR NOT EXISTS ( SELECT 1 FROM ezcontentobject_tree t WHERE t.contentobject_id = u.contentobject_id ) )';
                break;
            case 'duplicate_login':
                // LoveStack L04: a login (any case, as eZUser::fetchByName() compares) that another listed account
                // has as well; resolve them, then ./console exp:user-login-check --fix-index adds the unique index
                $sql .= ' AND LENGTH( u.login ) > 0 AND LOWER( u.login ) IN ( SELECT LOWER( d.login ) FROM ezuser d'
                      . ' WHERE LENGTH( d.login ) > 0 AND ' . self::LISTED_D . ' GROUP BY LOWER( d.login ) HAVING COUNT( * ) > 1 )';
                break;
            case 'duplicate_email':
                // LENGTH() rather than <> '': Oracle stores an empty string as NULL
                // (only accounts that are listed: not one whose object is in the trash)
                $sql .= ' AND LENGTH( u.email ) > 0 AND LOWER( u.email ) IN ( SELECT LOWER( d.email ) FROM ezuser d'
                      . ' WHERE LENGTH( d.email ) > 0 AND ' . self::LISTED_D . ' GROUP BY LOWER( d.email ) HAVING COUNT( * ) > 1 )';
                break;
        }

        $groupPath = self::normalisePath( $groupPath );
        if ( $groupPath !== null )
            $sql .= ' AND EXISTS ( SELECT 1 FROM ezcontentobject_tree g WHERE g.contentobject_id = u.contentobject_id'
                  . " AND g.path_string LIKE '" . $groupPath . "%' AND g.path_string <> '" . $groupPath . "' )";

        if ( $filters['q'] !== '' )
        {
            $like = "'" . call_user_func( $this->escape, expUnactivatedUsers::likePattern( $filters['q'] ) ) . "'";
            $sql .= " AND ( LOWER( u.login ) LIKE $like ESCAPE '!' OR LOWER( o.name ) LIKE $like ESCAPE '!'"
                  . ( $searchEmail ? " OR LOWER( u.email ) LIKE $like ESCAPE '!'" : '' ) . ' )';
        }

        if ( (int)$afterID > 0 )
            $sql .= ' AND u.contentobject_id > ' . (int)$afterID;
        return $sql;
    }

    /**
     * How many accounts the filters match.
     *
     * @param array $filters
     * @param bool $searchEmail
     * @param string|null $groupPath
     * @return int
     */
    public function count( array $filters, $searchEmail, $groupPath = null )
    {
        $rows = call_user_func( $this->query, 'SELECT COUNT( * ) AS c' . $this->fromWhere( $filters, $searchEmail, $groupPath ), 0, 0 );
        return isset( $rows[0]['c'] ) ? (int)$rows[0]['c'] : 0;
    }

    /**
     * One page of accounts, enriched (see enrich()).
     *
     * @param array $filters
     * @param bool $searchEmail
     * @param string|null $groupPath
     * @param int $offset
     * @return array[]
     */
    public function page( array $filters, $searchEmail, $groupPath = null, $offset = 0 )
    {
        $sort = array_key_exists( $filters['sort'], self::SORTS ) ? $filters['sort'] : 'name';
        $order = $filters['order'] === 'desc' ? 'DESC' : 'ASC';
        $sql = self::SELECT . $this->fromWhere( $filters, $searchEmail, $groupPath )
             . ' ORDER BY ' . self::SORTS[$sort] . ' ' . $order . ', u.contentobject_id ASC';
        $rows = call_user_func( $this->query, $sql, max( 0, (int)$offset ), max( 1, (int)$filters['limit'] ) );
        return $this->enrich( $rows );
    }

    /**
     * The next batch of the CSV: accounts with an id above $afterID, by id, enriched.
     *
     * @param array $filters
     * @param bool $searchEmail
     * @param string|null $groupPath
     * @param int $afterID
     * @param int $limit
     * @return array[]
     */
    public function batch( array $filters, $searchEmail, $groupPath, $afterID, $limit = self::EXPORT_BATCH )
    {
        $sql = self::SELECT . $this->fromWhere( $filters, $searchEmail, $groupPath, $afterID ) . ' ORDER BY u.contentobject_id ASC';
        return $this->enrich( call_user_func( $this->query, $sql, 0, max( 1, (int)$limit ) ) );
    }

    /** The account d is listed: its object is not in the trash */
    const LISTED_D = 'NOT EXISTS ( SELECT 1 FROM ezcontentobject x WHERE x.id = d.contentobject_id AND x.status = 2 )';

    /** The columns of a listed account */
    const SELECT = 'SELECT u.contentobject_id, u.login, LOWER( u.login ) AS login_key, u.email, LOWER( u.email ) AS email_key, o.name, o.status AS object_status, s.is_enabled,'
                 . ' v.login_count, v.current_visit_timestamp, v.failed_login_attempts';

    /**
     * The rows of a page made into accounts: id, login, email, name, enabled, unactivated (disabled with an account
     * key), locked, failed_attempts, login_count, last_visit (false when never), idle_days (whole days since the last
     * visit, false when never), object (published, draft, none), node_id (the main node, 0 without one), groups
     * (node_id, name), roles (names), shared_email (how many accounts have the same address, 1 when none), shared_login (the same for the login, any case), orphaned,
     * and states (see states()). Six queries for the whole page, whatever its size.
     *
     * @param array $rows
     * @return array[]
     */
    public function enrich( array $rows )
    {
        $ids = array();
        foreach ( $rows as $row )
            $ids[] = (int)$row['contentobject_id'];
        if ( !$ids )
            return array();
        $in = implode( ', ', $ids );

        // 1. the locations of the accounts with the names of their parents (the groups)
        $nodes = array();
        $ancestors = array();
        foreach ( call_user_func( $this->query, 'SELECT t.contentobject_id, t.node_id, t.main_node_id, t.path_string, p.node_id AS parent_id,'
                                  . ' po.name AS parent_name FROM ezcontentobject_tree t'
                                  . ' LEFT JOIN ezcontentobject_tree p ON p.node_id = t.parent_node_id'
                                  . ' LEFT JOIN ezcontentobject po ON po.id = p.contentobject_id'
                                  . " WHERE t.contentobject_id IN ( $in ) ORDER BY t.contentobject_id, t.node_id", 0, 0 ) as $node )
        {
            $id = (int)$node['contentobject_id'];
            $nodes[$id][] = $node;
            foreach ( explode( '/', trim( (string)$node['path_string'], '/' ) ) as $ancestor )
            {
                if ( ctype_digit( $ancestor ) && (int)$ancestor !== (int)$node['node_id'] && (int)$ancestor > 1 )
                    $ancestors[(int)$ancestor][$id] = $id;
            }
        }

        // 2. the roles assigned to the accounts or to a group above them
        $holders = array();
        foreach ( $ids as $id )
            $holders[$id] = array( $id => $id );
        if ( $ancestors )
        {
            foreach ( call_user_func( $this->query, 'SELECT node_id, contentobject_id FROM ezcontentobject_tree WHERE node_id IN ( '
                                      . implode( ', ', array_keys( $ancestors ) ) . ' )', 0, 0 ) as $row )
            {
                foreach ( $ancestors[(int)$row['node_id']] as $id )
                    $holders[$id][(int)$row['contentobject_id']] = (int)$row['contentobject_id'];
            }
        }
        $holderIDs = array();
        foreach ( $holders as $list )
            $holderIDs += $list;
        $rolesOf = array();
        foreach ( call_user_func( $this->query, 'SELECT ur.contentobject_id, r.name FROM ezuser_role ur INNER JOIN ezrole r ON r.id = ur.role_id'
                                  . ' WHERE r.version = 0 AND ur.contentobject_id IN ( ' . implode( ', ', $holderIDs ) . ' )', 0, 0 ) as $row )
            $rolesOf[(int)$row['contentobject_id']][(string)$row['name']] = (string)$row['name'];

        // 3. the accounts with an account key (unactivated when disabled)
        $keys = array();
        foreach ( call_user_func( $this->query, "SELECT DISTINCT user_id FROM ezuser_accountkey WHERE user_id IN ( $in )", 0, 0 ) as $row )
            $keys[(int)$row['user_id']] = true;

        // 4. how many listed accounts share the address of each account of the page (compared in SQL, as the filter)
        $shared = array();
        foreach ( call_user_func( $this->query, 'SELECT LOWER( d.email ) AS email_key, COUNT( * ) AS c FROM ezuser d'
                                  . ' WHERE LOWER( d.email ) IN ( SELECT LOWER( u.email ) FROM ezuser u'
                                  . " WHERE u.contentobject_id IN ( $in ) AND LENGTH( u.email ) > 0 ) AND " . self::LISTED_D
                                  . ' GROUP BY LOWER( d.email )', 0, 0 ) as $row )
            $shared[(string)$row['email_key']] = (int)$row['c'];

        // 5. how many listed accounts share the login of each account of the page (any case, LoveStack L04)
        $sharedLogins = array();
        foreach ( call_user_func( $this->query, 'SELECT LOWER( d.login ) AS login_key, COUNT( * ) AS c FROM ezuser d'
                                  . ' WHERE LOWER( d.login ) IN ( SELECT LOWER( u.login ) FROM ezuser u'
                                  . " WHERE u.contentobject_id IN ( $in ) AND LENGTH( u.login ) > 0 ) AND " . self::LISTED_D
                                  . ' GROUP BY LOWER( d.login )', 0, 0 ) as $row )
            $sharedLogins[(string)$row['login_key']] = (int)$row['c'];

        $accounts = array();
        foreach ( $rows as $row )
        {
            $id = (int)$row['contentobject_id'];
            $groups = array();
            $nodeID = 0;
            foreach ( isset( $nodes[$id] ) ? $nodes[$id] : array() as $node )
            {
                if ( (int)$node['node_id'] === (int)$node['main_node_id'] )
                    $nodeID = (int)$node['node_id'];
                if ( (int)$node['parent_id'] > 0 )
                    $groups[] = array( 'node_id' => (int)$node['parent_id'], 'name' => (string)$node['parent_name'] );
            }
            $roles = array();
            foreach ( $holders[$id] as $holder )
                $roles += isset( $rolesOf[$holder] ) ? $rolesOf[$holder] : array();
            ksort( $roles );
            $status = $row['object_status'];
            $object = $status === null || $status === '' ? 'none'
                    : ( (int)$status === eZContentObject::STATUS_PUBLISHED ? 'published' : 'draft' );
            $loginCount = (int)$row['login_count'];
            $account = array(
                'id' => $id,
                'login' => (string)$row['login'],
                'email' => (string)$row['email'],
                'name' => (string)$row['name'],
                // no ezuser_setting row: eZUser::isEnabled() counts the account as enabled
                'enabled' => $row['is_enabled'] === null || $row['is_enabled'] === '' || (int)$row['is_enabled'] !== 0,
                'has_key' => isset( $keys[$id] ),
                'failed_attempts' => (int)$row['failed_login_attempts'],
                'locked' => $this->maxFailed > 0 && (int)$row['failed_login_attempts'] > $this->maxFailed,
                'login_count' => $loginCount,
                'last_visit' => abs( (int)$row['current_visit_timestamp'] ) > 0 ? abs( (int)$row['current_visit_timestamp'] ) : false,
                'object' => $object,
                'node_id' => $nodeID,
                'groups' => $groups,
                'roles' => array_values( $roles ),
                'shared_email' => isset( $shared[(string)$row['email_key']] ) ? max( 1, $shared[(string)$row['email_key']] ) : 1,
                'shared_login' => isset( $sharedLogins[(string)$row['login_key']] ) ? max( 1, $sharedLogins[(string)$row['login_key']] ) : 1,
            );
            $account['idle_days'] = $account['last_visit'] === false ? false : (int)max( 0, floor( ( $this->now - $account['last_visit'] ) / 86400 ) );
            $account['unactivated'] = !$account['enabled'] && $account['has_key'];
            $account['orphaned'] = $object !== 'published' || !isset( $nodes[$id] );
            $account['states'] = self::states( $account );
            $accounts[] = $account;
        }
        return $accounts;
    }

    /**
     * The states of an account, the most pressing first: disabled or unactivated, locked, no_object, draft_only,
     * no_location, never (signed in); active when none applies. Each is a key for the template and the CSV.
     *
     * @param array $account see enrich()
     * @return string[]
     */
    public static function states( array $account )
    {
        $states = array();
        if ( !$account['enabled'] )
            $states[] = !empty( $account['unactivated'] ) ? 'unactivated' : 'disabled';
        if ( !empty( $account['locked'] ) )
            $states[] = 'locked';
        if ( $account['object'] === 'none' )
            $states[] = 'no_object';
        elseif ( $account['object'] === 'draft' )
            $states[] = 'draft_only';
        elseif ( !empty( $account['orphaned'] ) )
            $states[] = 'no_location';
        if ( (int)$account['login_count'] === 0 )
            $states[] = 'never';
        return $states ? $states : array( 'active' );
    }

    /**
     * The accounts no action ever touches: the anonymous user ([UserSettings] AnonymousUserID), the user who is
     * the creator of new users ([UserSettings] UserCreatorID), the administrator account of a clean installation
     * (ADMIN_USER_ID), the account with the login "admin" and the user who acts.
     *
     * @param int $currentUserID
     * @return int[]
     */
    public static function protectedIDs( $currentUserID )
    {
        $ini = eZINI::instance();
        $admin = eZUser::fetchByName( 'admin' );
        return self::protectedIDList( $ini->variable( 'UserSettings', 'AnonymousUserID' ), $ini->variable( 'UserSettings', 'UserCreatorID' ),
                                      $admin instanceof eZUser ? $admin->attribute( 'contentobject_id' ) : 0, $currentUserID );
    }

    /**
     * The list of protectedIDs() from its parts: each a positive id once, ADMIN_USER_ID always.
     *
     * @param mixed $anonymousID
     * @param mixed $creatorID
     * @param mixed $adminLoginID the id of the account with the login "admin", 0 when there is none
     * @param mixed $currentUserID
     * @return int[]
     */
    public static function protectedIDList( $anonymousID, $creatorID, $adminLoginID, $currentUserID )
    {
        $ids = array();
        foreach ( array( $anonymousID, $creatorID, self::ADMIN_USER_ID, $adminLoginID, $currentUserID ) as $id )
        {
            if ( is_numeric( $id ) && (int)$id > 0 )
                $ids[(int)$id] = (int)$id;
        }
        return array_values( $ids );
    }

    /**
     * @param mixed $action
     * @return string|null one of ACTIONS
     */
    public static function normaliseAction( $action )
    {
        return is_string( $action ) && in_array( $action, self::ACTIONS, true ) ? $action : null;
    }

    /**
     * Whether an action may be done to one account, decided again at the moment it is done: 'do', or why not:
     * 'protected' (see protectedIDs()), 'gone' (no account any more), 'no_object' (no object to check the access
     * on), 'denied' (the user who acts may not edit the account's object), 'unactivated' (enable: the account never
     * activated; that is done on the page of unactivated users, which finishes the registration), 'unchanged'
     * (already in the state the action makes).
     *
     * @param string $action one of ACTIONS
     * @param array|null $state exists, has_object, can_edit, enabled, has_key, failed_attempts; null when gone
     * @param int $userID
     * @param int[] $protected
     * @return string
     */
    public static function decide( $action, $state, $userID, array $protected )
    {
        if ( in_array( (int)$userID, array_map( 'intval', $protected ), true ) )
            return 'protected';
        if ( !is_array( $state ) || empty( $state['exists'] ) )
            return 'gone';
        if ( empty( $state['has_object'] ) )
            return 'no_object';
        if ( empty( $state['can_edit'] ) )
            return 'denied';
        switch ( $action )
        {
            case 'disable':
                return empty( $state['enabled'] ) ? 'unchanged' : 'do';
            case 'enable':
                if ( !empty( $state['enabled'] ) )
                    return 'unchanged';
                return empty( $state['has_key'] ) ? 'do' : 'unactivated';
            case 'unlock':
                return isset( $state['failed_attempts'] ) && (int)$state['failed_attempts'] > 0 ? 'do' : 'unchanged';
        }
        return 'unchanged';
    }

    /**
     * Decides for each account and does the action to those it may, each decided again just before.
     *
     * @param string $action
     * @param int[] $ids
     * @param callable $state ( int $id ) => array|null see decide()
     * @param callable $perform ( string $action, int $id ) => bool
     * @param int[] $protected
     * @param bool $dryRun only decide (the confirmation page)
     * @return array done (ids), skipped (reason => ids), failed (ids)
     */
    public static function applyWith( $action, array $ids, $state, $perform, array $protected, $dryRun = false )
    {
        $result = array( 'done' => array(), 'skipped' => array(), 'failed' => array() );
        $protected = array_map( 'intval', $protected );
        foreach ( expUnactivatedUsers::normaliseIDs( $ids ) as $id )
        {
            // a protected account is not even looked at
            $decision = in_array( $id, $protected, true ) ? 'protected' : self::decide( $action, call_user_func( $state, $id ), $id, $protected );
            if ( $decision !== 'do' )
                $result['skipped'][$decision][] = $id;
            elseif ( $dryRun || call_user_func( $perform, $action, $id ) )
                $result['done'][] = $id;
            else
                $result['failed'][] = $id;
        }
        return $result;
    }

    /**
     * The state decide() needs, read from this installation for the current user.
     *
     * @param int $id
     * @return array|null
     */
    public static function accountState( $id )
    {
        $user = eZUser::fetch( (int)$id );
        if ( !$user instanceof eZUser )
            return null;
        $object = eZContentObject::fetch( (int)$id );
        $setting = eZUserSetting::fetch( (int)$id );
        return array(
            'exists' => true,
            'has_object' => $object instanceof eZContentObject,
            'can_edit' => $object instanceof eZContentObject && $object->canEdit(),
            'enabled' => !$setting || (int)$setting->attribute( 'is_enabled' ) !== 0,
            'has_key' => eZUserAccountKey::fetchByUserID( (int)$id ) instanceof eZUserAccountKey,
            'failed_attempts' => (int)eZUser::failedLoginAttemptsByUserID( (int)$id ),
        );
    }

    /**
     * Does one action to one account through the kernel's own paths, which also write the audit records
     * (access.user.disable, access.user.enable, access.user.unlock): the setsettings operation of the user module
     * (as Edit user settings does; disabling ends the account's sessions) and eZUser::setFailedLoginAttempts().
     *
     * @param string $action
     * @param int $id
     * @return bool
     */
    public static function perform( $action, $id )
    {
        $id = (int)$id;
        switch ( $action )
        {
            case 'disable':
            case 'enable':
                $setting = eZUserSetting::fetch( $id );
                $maxLogin = $setting ? (int)$setting->attribute( 'max_login' ) : 0;
                $enabled = $action === 'enable' ? 1 : 0;
                if ( eZOperationHandler::operationIsAvailable( 'user_setsettings' ) )
                    $result = eZOperationHandler::execute( 'user', 'setsettings', array( 'user_id' => $id, 'is_enabled' => $enabled, 'max_login' => $maxLogin ) );
                else
                    $result = eZUserOperationCollection::setSettings( $id, $enabled, $maxLogin );
                eZContentObject::clearCache( array( $id ) );
                return !is_array( $result ) || !isset( $result['status'] ) || $result['status'] !== false;
            case 'unlock':
                eZUser::setFailedLoginAttempts( $id, 0, true );
                return true;
        }
        return false;
    }

    /**
     * The cells of the CSV header.
     *
     * @param bool $withEmail
     * @param callable|null $t ( string $source ) => string, the translation
     * @return string[]
     */
    public static function csvHeader( $withEmail, $t = null )
    {
        $t = $t ?: function ( $s ) { return $s; };
        $cells = array( call_user_func( $t, 'User ID' ), call_user_func( $t, 'Name' ), call_user_func( $t, 'Login' ) );
        if ( $withEmail )
            $cells[] = call_user_func( $t, 'E-mail' );
        foreach ( array( 'Status', 'Last visit', 'Sign-ins', 'Groups', 'Roles' ) as $source )
            $cells[] = call_user_func( $t, $source );
        return $cells;
    }

    /**
     * The cells of one account in the CSV (made safe for spreadsheets by expSubitemsCSVExport::writeLine()).
     *
     * @param array $account see enrich()
     * @param bool $withEmail
     * @param callable|null $t ( string $source ) => string, the translation of a state's name
     * @return string[]
     */
    public static function csvRow( array $account, $withEmail, $t = null )
    {
        $t = $t ?: function ( $s ) { return $s; };
        $states = array();
        foreach ( $account['states'] as $state )
            $states[] = call_user_func( $t, self::stateName( $state ) );
        $groups = array();
        foreach ( $account['groups'] as $group )
            $groups[] = $group['name'];
        $cells = array( (string)$account['id'], $account['name'], $account['login'] );
        if ( $withEmail )
            $cells[] = $account['email'];
        $cells[] = implode( ', ', $states );
        $cells[] = $account['last_visit'] === false ? '' : gmdate( 'Y-m-d H:i', (int)$account['last_visit'] ) . ' UTC';
        $cells[] = (string)(int)$account['login_count'];
        $cells[] = implode( ', ', $groups );
        $cells[] = implode( ', ', $account['roles'] );
        return $cells;
    }

    /**
     * The name of a state (see states()), the source text of its translation.
     *
     * @param string $state
     * @return string
     */
    public static function stateName( $state )
    {
        $names = array( 'active' => 'Active', 'disabled' => 'Disabled', 'unactivated' => 'Not activated', 'locked' => 'Locked',
                        'no_object' => 'No user object', 'draft_only' => 'Never published', 'no_location' => 'No location',
                        'never' => 'Never signed in' );
        return isset( $names[$state] ) ? $names[$state] : $state;
    }

    /**
     * Writes the CSV of every account the filters match, at most EXPORT_LIMIT, in batches.
     *
     * @param resource $handle
     * @param array $filters
     * @param bool $withEmail
     * @param string|null $groupPath
     * @param bool $bom whether to start with the UTF-8 byte order mark
     * @param callable|null $t the translation
     * @return int the accounts written
     */
    public function writeCSV( $handle, array $filters, $withEmail, $groupPath = null, $bom = true, $t = null )
    {
        if ( $bom )
            fwrite( $handle, expSubitemsCSVExport::BOM );
        expSubitemsCSVExport::writeLine( $handle, self::csvHeader( $withEmail, $t ) );
        $written = 0;
        $after = 0;
        while ( $written < self::EXPORT_LIMIT )
        {
            $accounts = $this->batch( $filters, $withEmail, $groupPath, $after, min( self::EXPORT_BATCH, self::EXPORT_LIMIT - $written ) );
            foreach ( $accounts as $account )
            {
                expSubitemsCSVExport::writeLine( $handle, self::csvRow( $account, $withEmail, $t ) );
                $after = $account['id'];
                $written++;
            }
            if ( count( $accounts ) < self::EXPORT_BATCH )
                break;
        }
        return $written;
    }

    /**
     * The other side of "orphaned": how many published objects of a class with a user account attribute (datatype
     * ezuser) have no account row in ezuser. They cannot sign in and no action of the page applies to them, so the
     * list (which is read from ezuser) never holds them; the page reports their number only. One query.
     *
     * @return int
     */
    public function accountlessObjectCount()
    {
        $rows = call_user_func( $this->query, 'SELECT COUNT( * ) AS c FROM ezcontentobject o'
                                . ' WHERE o.status = ' . (int)eZContentObject::STATUS_PUBLISHED
                                . " AND o.contentclass_id IN ( SELECT a.contentclass_id FROM ezcontentclass_attribute a WHERE a.data_type_string = 'ezuser' AND a.version = 0 )"
                                . ' AND NOT EXISTS ( SELECT 1 FROM ezuser u WHERE u.contentobject_id = o.id )', 0, 0 );
        return isset( $rows[0]['c'] ) ? (int)$rows[0]['c'] : 0;
    }

    /**
     * The user groups a list may be narrowed to: node_id, name, depth, path_string of every published object of the
     * user group class ([UserSettings] UserGroupClassID), in the order of the tree, at most $limit.
     *
     * @param int $groupClassID
     * @param int $limit
     * @return array[]
     */
    public function groups( $groupClassID, $limit = 500 )
    {
        $groups = array();
        foreach ( call_user_func( $this->query, 'SELECT t.node_id, t.depth, t.path_string, o.name FROM ezcontentobject_tree t'
                                  . ' INNER JOIN ezcontentobject o ON o.id = t.contentobject_id'
                                  . ' WHERE o.contentclass_id = ' . (int)$groupClassID . ' AND o.status = ' . (int)eZContentObject::STATUS_PUBLISHED
                                  . ' ORDER BY t.path_string', 0, max( 1, (int)$limit ) ) as $row )
            $groups[] = array( 'node_id' => (int)$row['node_id'], 'depth' => (int)$row['depth'],
                               'path_string' => (string)$row['path_string'], 'name' => (string)$row['name'] );
        return $groups;
    }
}
