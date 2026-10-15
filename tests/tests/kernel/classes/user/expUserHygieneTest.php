<?php
/**
 * Check user accounts (user/hygiene, expUserHygiene). The SQL of every filter runs against an SQLite database built
 * here in memory with fixture accounts (no real names); everything else is plain functions and the source files.
 *
 *  UH-01 - Each filter finds exactly its accounts; an account whose object is in the trash is never listed
 *  UH-02 - Below a user group, and the search: name and login always, the e-mail address only with the right to see it
 *  UH-03 - Order and paging: each column both ways, ties by id, a missing name ordered by the login, the page size
 *  UH-04 - A page is read with the same number of queries for 3 and for 60 accounts (no query per row); states,
 *          groups, roles of the account and of the groups above it, shared addresses, the last visit
 *  UH-05 - The filters of a request: unknown values fall back, the days and the page size have bounds, no e-mail order
 *          without the right; the query strings of the links
 *  UH-06 - Which account an action touches: never a protected one (the anonymous user, the administrator account,
 *          the user who acts), nor one the user may not edit, nor an unactivated one to enable; the confirmation only
 *          decides; each account is decided again when it is done
 *  UH-07 - The CSV: byte order mark, header, no e-mail column without the right, cells that start with = + - @ or a
 *          tab are made text, the batches by id
 *  UH-08 - The page: the view needs user/hygiene, changes and the CSV are POSTs with confirmation, e-mail only with
 *          role/assign, the same template in admin and admin4, the links of both menus and the Setup overview, the
 *          texts translated
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expUserHygieneTest extends PHPUnit\Framework\TestCase
{
    const NOW = 1790000000;
    const DAY = 86400;
    const MAX_FAILED = 5;

    /** @var SQLite3 */
    private $db;

    /** @var string[] the SQL of the queries since the last reset */
    private $queries = array();

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The PHP extension sqlite3 is not loaded.' );
        $this->db = new SQLite3( ':memory:' );
        foreach ( array(
            'CREATE TABLE ezuser ( contentobject_id INTEGER PRIMARY KEY, login TEXT NOT NULL DEFAULT \'\', email TEXT NOT NULL DEFAULT \'\' )',
            'CREATE TABLE ezuser_setting ( user_id INTEGER PRIMARY KEY, is_enabled INTEGER NOT NULL DEFAULT 0, max_login INTEGER )',
            'CREATE TABLE ezuservisit ( user_id INTEGER PRIMARY KEY, current_visit_timestamp INTEGER NOT NULL DEFAULT 0,'
                . ' last_visit_timestamp INTEGER NOT NULL DEFAULT 0, login_count INTEGER NOT NULL DEFAULT 0, failed_login_attempts INTEGER NOT NULL DEFAULT 0 )',
            'CREATE TABLE ezcontentobject ( id INTEGER PRIMARY KEY, contentclass_id INTEGER NOT NULL DEFAULT 0, name TEXT, status INTEGER DEFAULT 0 )',
            'CREATE TABLE ezcontentobject_tree ( node_id INTEGER PRIMARY KEY, parent_node_id INTEGER NOT NULL DEFAULT 0, contentobject_id INTEGER,'
                . ' main_node_id INTEGER, depth INTEGER NOT NULL DEFAULT 0, path_string TEXT NOT NULL DEFAULT \'\' )',
            'CREATE TABLE ezuser_accountkey ( id INTEGER PRIMARY KEY, user_id INTEGER NOT NULL DEFAULT 0, hash_key TEXT, time INTEGER )',
            'CREATE TABLE ezrole ( id INTEGER PRIMARY KEY, name TEXT, version INTEGER NOT NULL DEFAULT 0 )',
            'CREATE TABLE ezuser_role ( id INTEGER PRIMARY KEY, contentobject_id INTEGER, role_id INTEGER )',
        ) as $sql )
            $this->db->exec( $sql );

        // the tree: Users (node 5, object 4) > Members (node 12, object 11) > Team (node 60, object 59); Staff (node 13, object 12)
        $this->group( 4, 5, 1, '/1/5/', 'Users' );
        $this->group( 11, 12, 5, '/1/5/12/', 'Members' );
        $this->group( 12, 13, 5, '/1/5/13/', 'Staff' );
        $this->group( 59, 60, 12, '/1/5/12/60/', 'Team' );
        $this->db->exec( "INSERT INTO ezrole ( id, name, version ) VALUES ( 1, 'Member', 0 ), ( 2, 'Editor', 0 ), ( 3, 'Editor draft', 1 ), ( 4, 'Team lead', 0 )" );
        // Member on Members (inherited by Team), Editor on Staff, the draft of a role never counts, Team lead on one user
        $this->db->exec( 'INSERT INTO ezuser_role ( contentobject_id, role_id ) VALUES ( 11, 1 ), ( 12, 2 ), ( 12, 3 ), ( 105, 4 )' );
    }

    private function group( $objectID, $nodeID, $parentID, $path, $name )
    {
        $this->db->exec( "INSERT INTO ezcontentobject ( id, contentclass_id, name, status ) VALUES ( $objectID, 3, '$name', 1 )" );
        $this->db->exec( "INSERT INTO ezcontentobject_tree ( node_id, parent_node_id, contentobject_id, main_node_id, depth, path_string )"
                         . " VALUES ( $nodeID, $parentID, $objectID, $nodeID, " . ( substr_count( $path, '/' ) - 1 ) . ", '$path' )" );
    }

    /**
     * A fixture account. $o: name, email, enabled, key, visit (seconds before NOW, negative = signed out), count,
     * failed, object (published, draft, archived, none), nodes (array of parent node => path of the parent).
     */
    private function account( $id, array $o = array() )
    {
        $o += array( 'login' => "user$id", 'name' => "Sample User $id", 'email' => "user$id@example.invalid", 'enabled' => 1, 'key' => false,
                     'visit' => null, 'count' => 0, 'failed' => 0, 'object' => 'published', 'nodes' => array( 12 => '/1/5/12/' ),
                     'setting' => true );
        $this->db->exec( "INSERT INTO ezuser ( contentobject_id, login, email ) VALUES ( $id, '" . SQLite3::escapeString( $o['login'] ) . "', '"
                         . SQLite3::escapeString( $o['email'] ) . "' )" );
        if ( $o['setting'] )
            $this->db->exec( "INSERT INTO ezuser_setting ( user_id, is_enabled, max_login ) VALUES ( $id, {$o['enabled']}, 0 )" );
        if ( $o['key'] )
            $this->db->exec( "INSERT INTO ezuser_accountkey ( user_id, hash_key, time ) VALUES ( $id, 'k$id', 1 )" );
        if ( $o['visit'] !== null || $o['failed'] > 0 )
        {
            $visit = $o['visit'] === null ? 0 : ( $o['visit'] < 0 ? -( self::NOW + $o['visit'] ) : self::NOW - $o['visit'] );
            $this->db->exec( "INSERT INTO ezuservisit ( user_id, current_visit_timestamp, last_visit_timestamp, login_count, failed_login_attempts )"
                             . " VALUES ( $id, $visit, 0, {$o['count']}, {$o['failed']} )" );
        }
        $status = array( 'published' => 1, 'draft' => 0, 'archived' => 2 );
        if ( $o['object'] !== 'none' )
            $this->db->exec( "INSERT INTO ezcontentobject ( id, contentclass_id, name, status ) VALUES ( $id, 4, "
                             . ( $o['name'] === null ? 'NULL' : "'" . SQLite3::escapeString( $o['name'] ) . "'" ) . ", {$status[$o['object']]} )" );
        $first = true;
        foreach ( $o['object'] === 'published' ? $o['nodes'] : array() as $parent => $parentPath )
        {
            $node = $id * 10 + $parent;
            $this->db->exec( "INSERT INTO ezcontentobject_tree ( node_id, parent_node_id, contentobject_id, main_node_id, depth, path_string )"
                             . " VALUES ( $node, $parent, $id, " . ( $first ? $node : $id * 10 + (int)key( $o['nodes'] ) ) . ", "
                             . substr_count( $parentPath, '/' ) . ", '$parentPath$node/' )" );
            $first = false;
        }
    }

    /** The accounts of the filter tests, by what they are */
    private function fixture()
    {
        $this->account( 101, array( 'visit' => 2 * self::DAY, 'count' => 12 ) );                                   // active
        $this->account( 102 );                                                                                      // never signed in
        $this->account( 103, array( 'visit' => 400 * self::DAY, 'count' => 3 ) );                                  // not seen for 400 days
        $this->account( 104, array( 'visit' => -( 200 * self::DAY ), 'count' => 8 ) );                             // signed out 200 days ago
        $this->account( 105, array( 'enabled' => 0, 'visit' => 10 * self::DAY, 'count' => 1 ) );                  // disabled
        $this->account( 106, array( 'enabled' => 0, 'key' => true ) );                                             // never activated
        $this->account( 107, array( 'visit' => self::DAY, 'count' => 40, 'failed' => 6 ) );                       // locked
        $this->account( 108, array( 'visit' => self::DAY, 'count' => 40, 'failed' => 5 ) );                       // at the limit: not locked
        $this->account( 109, array( 'object' => 'none', 'name' => null ) );                                        // no object
        $this->account( 110, array( 'object' => 'draft' ) );                                                       // never published
        $this->account( 111, array( 'nodes' => array() ) );                                                        // no location
        $this->account( 112, array( 'email' => 'Shared@Example.invalid', 'nodes' => array( 13 => '/1/5/13/' ) ) );  // a shared address
        $this->account( 113, array( 'email' => 'shared@example.invalid', 'nodes' => array( 60 => '/1/5/12/60/' ), 'visit' => 5 * self::DAY, 'count' => 2 ) );
        $this->account( 114, array( 'object' => 'archived', 'email' => 'SHARED@example.invalid' ) );                // in the trash, a third with the address
        $this->account( 115, array( 'email' => '', 'setting' => false, 'nodes' => array( 60 => '/1/5/12/60/', 13 => '/1/5/13/' ) ) ); // two groups
    }

    private function hygiene( $maxFailed = self::MAX_FAILED )
    {
        $db = $this->db;
        $log = &$this->queries;
        return new expUserHygiene(
            function ( $sql, $offset, $limit ) use ( $db, &$log )
            {
                $log[] = $sql;
                if ( $limit > 0 )
                    $sql .= ' LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;
                $result = $db->query( $sql );
                if ( !$result )
                    throw new RuntimeException( $db->lastErrorMsg() . ": $sql" );
                $rows = array();
                while ( $row = $result->fetchArray( SQLITE3_ASSOC ) )
                    $rows[] = $row;
                return $rows;
            },
            function ( $text ) { return SQLite3::escapeString( $text ); },
            self::NOW,
            $maxFailed
        );
    }

    private function ids( array $accounts )
    {
        $ids = array();
        foreach ( $accounts as $account )
            $ids[] = $account['id'];
        sort( $ids );
        return $ids;
    }

    private function filters( array $input = array(), $canSeeEmail = true )
    {
        return expUserHygiene::normaliseFilters( $input + array( 'limit' => 100 ), $canSeeEmail );
    }

    private function find( array $input, $canSeeEmail = true, $groupPath = null, $maxFailed = self::MAX_FAILED )
    {
        $hygiene = $this->hygiene( $maxFailed );
        $filters = $this->filters( $input, $canSeeEmail );
        $page = $hygiene->page( $filters, $canSeeEmail, $groupPath );
        $this->assertSame( count( $page ), $hygiene->count( $filters, $canSeeEmail, $groupPath ), 'the count says how many the list holds' );
        return $this->ids( $page );
    }

    public function testUH01EachFilterFindsItsAccounts()
    {
        $this->fixture();
        $all = array( 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 115 );
        $this->assertSame( $all, $this->find( array() ), 'every account but the one in the trash' );
        $this->assertSame( array( 102, 106, 109, 110, 111, 112, 115 ), $this->find( array( 'filter' => 'never' ) ) );
        $this->assertSame( array( 103, 104 ), $this->find( array( 'filter' => 'inactive', 'days' => '180' ) ) );
        $this->assertSame( array( 103 ), $this->find( array( 'filter' => 'inactive', 'days' => '365' ) ) );
        $this->assertSame( array( 103, 104, 105 ), $this->find( array( 'filter' => 'inactive', 'days' => '9' ) ) );
        $this->assertSame( array( 105, 106 ), $this->find( array( 'filter' => 'disabled' ) ) );
        $this->assertSame( array( 107 ), $this->find( array( 'filter' => 'locked' ) ) );
        $this->assertSame( array(), $this->find( array( 'filter' => 'locked' ), true, null, 0 ), 'no limit, no lock' );
        $this->assertSame( array( 109, 110, 111 ), $this->find( array( 'filter' => 'orphaned' ) ) );
        $this->assertSame( array( 112, 113 ), $this->find( array( 'filter' => 'duplicate_email' ) ), 'the case of an address does not matter; empty ones are no duplicates; one in the trash does not count' );
        $this->db->exec( "UPDATE ezuser SET email = 'other113@example.invalid' WHERE contentobject_id = 113" );
        $this->assertSame( array(), $this->find( array( 'filter' => 'duplicate_email' ) ), 'only the trashed account shares 112\'s address now' );
        $this->db->exec( "UPDATE ezuser SET email = 'shared@example.invalid' WHERE contentobject_id = 113" );
        $this->assertSame( $this->find( array() ), $this->find( array( 'filter' => 'duplicate_email' ), false ), 'not without the right to see addresses' );
    }

    public function testUH02GroupAndSearch()
    {
        $this->fixture();
        $this->assertSame( array( 113, 115 ), $this->find( array(), true, '/1/5/12/60/' ), 'below Team' );
        $this->assertSame( array( 101, 102, 103, 104, 105, 106, 107, 108, 113, 115 ), $this->find( array(), true, '/1/5/12/' ), 'below Members, with Team' );
        $this->assertSame( array( 112, 115 ), $this->find( array(), true, '/1/5/13/' ) );
        $this->assertSame( array( 102, 106, 115 ), $this->find( array( 'filter' => 'never' ), true, '/1/5/12/' ) );
        $this->assertSame( $this->find( array() ), $this->find( array(), true, "/1/5/' OR 1=1 --/" ), 'a path that is no path narrows nothing' );

        $this->assertSame( array( 107 ), $this->find( array( 'q' => 'User 107' ) ), 'by name' );
        $this->assertSame( array( 109 ), $this->find( array( 'q' => 'USER109' ) ), 'by login, any case' );
        $this->assertSame( array( 112, 113 ), $this->find( array( 'q' => 'shared@' ) ), 'by e-mail address' );
        $this->assertSame( array(), $this->find( array( 'q' => 'shared@' ), false ), 'not by e-mail address without the right to see it' );
        $this->assertSame( array(), $this->find( array( 'q' => "%' OR '1'='1" ) ), 'quotes and wildcards are text' );
        $this->assertSame( array(), $this->find( array( 'q' => '_' ) ), 'an underscore is no wildcard' );
    }

    public function testUH03OrderAndPaging()
    {
        $this->fixture();
        $hygiene = $this->hygiene();
        $page = function ( array $input, $offset = 0 ) use ( $hygiene )
        {
            $ids = array();
            foreach ( $hygiene->page( $this->filters( $input ), true, null, $offset ) as $account )
                $ids[] = $account['id'];
            return $ids;
        };
        $byLogin = $page( array( 'sort' => 'login' ) );
        $this->assertSame( array( 101, 102, 103 ), array_slice( $byLogin, 0, 3 ) );
        $this->assertSame( array_reverse( $byLogin ), $page( array( 'sort' => 'login', 'order' => 'desc' ) ) );
        $this->assertSame( array_slice( $byLogin, 10 ), $page( array( 'sort' => 'login' ), 10 ), 'from an offset' );
        $this->assertSame( array( 103, 104, 105 ), $page( array( 'sort' => 'last_visit', 'filter' => 'inactive', 'days' => '9' ) ),
                           'the oldest visit first; that of a signed out account (104, stored negated) counts as well' );
        $this->assertSame( array( 107, 108, 101 ), array_slice( $page( array( 'sort' => 'login_count', 'order' => 'desc' ) ), 0, 3 ), 'ties by id' );
        $byName = $page( array( 'sort' => 'name' ) );
        $this->assertSame( 109, end( $byName ), 'no object, no name: ordered by its login ("user109" after "sample user ...")' );
        $this->assertSame( 101, $byName[0] );
        $this->assertSame( array( 112, 113 ), $page( array( 'filter' => 'duplicate_email', 'sort' => 'email' ) ) );
        for ( $i = 200; $i < 230; $i++ )
            $this->account( $i );
        $this->assertCount( 25, $page( array( 'limit' => '25' ) ), 'at most a page' );
        $this->assertCount( 19, $page( array( 'limit' => '25' ), 25 ), 'the rest' );
    }

    public function testUH04APageIsReadWithoutAQueryPerRow()
    {
        $this->fixture();
        $hygiene = $this->hygiene();
        $this->queries = array();
        $accounts = $hygiene->page( $this->filters( array( 'filter' => 'duplicate_email' ) ), true );
        $few = count( $this->queries );
        for ( $i = 200; $i < 260; $i++ )
            $this->account( $i, array( 'nodes' => array( 60 => '/1/5/12/60/' ), 'email' => "same@example.invalid", 'visit' => $i, 'count' => 1 ) );
        $this->queries = array();
        $many = $hygiene->page( $this->filters( array( 'filter' => 'duplicate_email' ) ), true );
        $this->assertCount( 2, $accounts );
        $this->assertCount( 62, $many );
        $this->assertSame( $few, count( $this->queries ), 'the same queries for 2 and for 62 accounts' );
        $this->assertLessThanOrEqual( 6, count( $this->queries ), 'the list and five for the whole page' );

        $byID = array();
        foreach ( $hygiene->page( $this->filters(), true ) as $account )
            $byID[$account['id']] = $account;
        $this->assertSame( array( 'active' ), $byID[101]['states'] );
        $this->assertSame( 2, $byID[101]['idle_days'] );
        $this->assertSame( self::NOW - 2 * self::DAY, $byID[101]['last_visit'] );
        $this->assertSame( array( 'never' ), $byID[102]['states'] );
        $this->assertFalse( $byID[102]['last_visit'] );
        $this->assertSame( self::NOW - 200 * self::DAY, $byID[104]['last_visit'], 'stored negated after a sign-out' );
        $this->assertSame( array( 'disabled' ), $byID[105]['states'] );
        $this->assertSame( array( 'unactivated', 'never' ), $byID[106]['states'] );
        $this->assertSame( array( 'locked' ), $byID[107]['states'] );
        $this->assertSame( array( 'active' ), $byID[108]['states'] );
        $this->assertSame( array( 'no_object', 'never' ), $byID[109]['states'] );
        $this->assertSame( array( 'draft_only', 'never' ), $byID[110]['states'] );
        $this->assertSame( array( 'no_location', 'never' ), $byID[111]['states'] );
        $this->assertTrue( $byID[115]['enabled'], 'no ezuser_setting row counts as enabled, as eZUser::isEnabled() does' );

        $this->assertSame( array( array( 'node_id' => 12, 'name' => 'Members' ) ), $byID[101]['groups'] );
        $this->assertSame( 1022, $byID[101]['node_id'] );
        $this->assertSame( array( 'Member' ), $byID[101]['roles'], 'from the group' );
        $this->assertSame( array( 'Member', 'Team lead' ), $byID[105]['roles'], 'from the group and its own' );
        $this->assertSame( array( 'Member' ), $byID[113]['roles'], 'from a group two levels up' );
        $this->assertSame( array( 'Editor', 'Member' ), $byID[115]['roles'], 'from both groups, without the draft of a role' );
        $this->assertCount( 2, $byID[115]['groups'] );
        $this->assertSame( 0, $byID[111]['node_id'] );
        $this->assertSame( array(), $byID[109]['groups'] );
        $this->assertSame( 2, $byID[112]['shared_email'], 'not counting the account in the trash' );
        $this->assertSame( 1, $byID[101]['shared_email'] );
        $this->assertSame( 1, $byID[115]['shared_email'], 'an empty address is shared with nobody' );
        $this->assertSame( array(), $hygiene->enrich( array() ) );
    }

    public function testUH05TheFiltersOfARequest()
    {
        $defaults = expUserHygiene::normaliseFilters( array(), true );
        $this->assertSame( array( 'filter' => 'all', 'days' => 180, 'group' => 0, 'q' => '', 'sort' => 'name', 'order' => 'asc', 'limit' => 25 ), $defaults );
        $odd = expUserHygiene::normaliseFilters( array( 'filter' => 'everything', 'days' => '-3', 'group' => '12x', 'q' => array( 'x' ),
                                                        'sort' => 'password_hash', 'order' => 'sideways', 'limit' => '1000' ), true );
        $this->assertSame( $defaults, $odd );
        $this->assertSame( 1, expUserHygiene::normaliseFilters( array( 'days' => '0' ), true )['days'] );
        $this->assertSame( 36500, expUserHygiene::normaliseFilters( array( 'days' => '99999999' ), true )['days'] );
        $this->assertSame( 'email', expUserHygiene::normaliseFilters( array( 'sort' => 'email' ), true )['sort'] );
        $this->assertSame( 'name', expUserHygiene::normaliseFilters( array( 'sort' => 'email' ), false )['sort'], 'no e-mail order without the right' );
        $this->assertSame( 'a b', expUserHygiene::normaliseFilters( array( 'q' => " a/(b) \x00" ), true )['q'] );

        $this->assertSame( '', expUserHygiene::queryString( $defaults ) );
        $this->assertSame( '?filter=inactive&days=30', expUserHygiene::queryString( $defaults, array( 'filter' => 'inactive', 'days' => 30 ) ) );
        $this->assertSame( '?filter=never', expUserHygiene::queryString( $defaults, array( 'filter' => 'never', 'days' => 30 ) ), 'days only for inactive' );
        $this->assertSame( '?q=a%20%26%20b&sort=login&order=desc', expUserHygiene::queryString( $defaults, array( 'q' => 'a & b', 'sort' => 'login', 'order' => 'desc' ) ) );

        $links = expUserHygiene::links( array( 'sort' => 'login', 'q' => 'x', 'group' => 5 ) + $defaults, true );
        $this->assertSame( '?filter=duplicate_email&group=5&q=x&sort=email', $links['filters']['duplicate_email'], 'shared addresses side by side' );
        $this->assertSame( '?group=5&q=x&sort=login&order=desc', $links['sorts']['login'], 'the current column reversed' );
        $this->assertSame( '?group=5&q=x&sort=last_visit&order=desc', $links['sorts']['last_visit'], 'the last visit, the newest first' );
        $this->assertSame( '?sort=login', $links['clear'] );
        $this->assertArrayNotHasKey( 'email', expUserHygiene::links( $defaults, false )['sorts'] );
        $this->assertArrayNotHasKey( 'duplicate_email', expUserHygiene::links( $defaults, false )['filters'], 'no shared addresses without the right' );
        $this->assertSame( array( 'all', 'never', 'inactive', 'disabled', 'locked', 'orphaned' ), expUserHygiene::filters( false ) );
        $this->assertSame( expUserHygiene::FILTERS, expUserHygiene::filters( true ) );
        $this->assertSame( 'all', expUserHygiene::normaliseFilters( array( 'filter' => 'duplicate_email' ), false )['filter'] );

        $this->assertSame( '/1/5/12/', expUserHygiene::normalisePath( '/1/5/12/' ) );
        foreach ( array( '/1/5/12', '1/5/', "/1/5/' --/", '/', '', null, array() ) as $bad )
            $this->assertNull( expUserHygiene::normalisePath( $bad ) );
        $this->assertSame( 'unlock', expUserHygiene::normaliseAction( 'unlock' ) );
        $this->assertNull( expUserHygiene::normaliseAction( 'remove' ), 'no removal: a user object cannot go to the trash' );
    }

    public function testUH06WhichAccountAnActionTouches()
    {
        $protected = expUserHygiene::protectedIDList( '10', 14, 77, 99 );
        $this->assertSame( array( 10, 14, 77, 99 ), $protected );
        $this->assertSame( array( 10, 30, 14 ), expUserHygiene::protectedIDList( 10, 30, 0, '14' ), 'the clean installation\'s administrator always, each id once' );
        $this->assertSame( array( 14 ), expUserHygiene::protectedIDList( 'x', -1, null, 0 ) );

        $state = array( 'exists' => true, 'has_object' => true, 'can_edit' => true, 'enabled' => true, 'has_key' => false, 'failed_attempts' => 0 );
        foreach ( array( 10, 14, 77, 99 ) as $id )
            foreach ( expUserHygiene::ACTIONS as $action )
                $this->assertSame( 'protected', expUserHygiene::decide( $action, $state, $id, $protected ), "$action $id" );
        $this->assertSame( 'protected', expUserHygiene::decide( 'disable', $state, '99', $protected ) );
        $this->assertSame( 'gone', expUserHygiene::decide( 'disable', null, 50, $protected ) );
        $this->assertSame( 'no_object', expUserHygiene::decide( 'disable', array( 'has_object' => false ) + $state, 50, $protected ) );
        $this->assertSame( 'denied', expUserHygiene::decide( 'disable', array( 'can_edit' => false ) + $state, 50, $protected ) );
        $this->assertSame( 'denied', expUserHygiene::decide( 'unlock', array( 'can_edit' => false, 'failed_attempts' => 9 ) + $state, 50, $protected ) );
        $this->assertSame( 'do', expUserHygiene::decide( 'disable', $state, 50, $protected ) );
        $this->assertSame( 'unchanged', expUserHygiene::decide( 'enable', $state, 50, $protected ) );
        $this->assertSame( 'unchanged', expUserHygiene::decide( 'unlock', $state, 50, $protected ) );
        $disabled = array( 'enabled' => false ) + $state;
        $this->assertSame( 'unchanged', expUserHygiene::decide( 'disable', $disabled, 50, $protected ) );
        $this->assertSame( 'do', expUserHygiene::decide( 'enable', $disabled, 50, $protected ) );
        $this->assertSame( 'unactivated', expUserHygiene::decide( 'enable', array( 'has_key' => true ) + $disabled, 50, $protected ) );
        $this->assertSame( 'do', expUserHygiene::decide( 'unlock', array( 'failed_attempts' => 3 ) + $state, 50, $protected ) );
        $this->assertSame( 'unchanged', expUserHygiene::decide( 'remove', $state, 50, $protected ) );

        $states = array( 50 => $state, 51 => array( 'can_edit' => false ) + $state, 52 => $state, 53 => null, 54 => $state );
        $asked = array();
        $performed = array();
        $stateOf = function ( $id ) use ( $states, &$asked ) { $asked[] = $id; return $states[$id]; };
        $perform = function ( $action, $id ) use ( &$performed ) { $performed[] = "$action $id"; return $id !== 54; };
        $dry = expUserHygiene::applyWith( 'disable', array( '50', 51, 52, 53, 54, 99, 50, 'x', -3 ), $stateOf, $perform, $protected, true );
        $this->assertSame( array(), $performed, 'the confirmation only decides' );
        $this->assertSame( array( 50, 52, 54 ), $dry['done'] );
        $this->assertSame( array( 'denied' => array( 51 ), 'gone' => array( 53 ), 'protected' => array( 99 ) ), $dry['skipped'] );
        $asked = array();
        $done = expUserHygiene::applyWith( 'disable', array( 50, 51, 52, 53, 54, 99 ), $stateOf, $perform, $protected );
        $this->assertSame( array( 50, 51, 52, 53, 54 ), $asked, 'each decided again when it is done; a protected one not even read' );
        $this->assertSame( array( 'disable 50', 'disable 52', 'disable 54' ), $performed );
        $this->assertSame( array( 50, 52 ), $done['done'] );
        $this->assertSame( array( 54 ), $done['failed'] );
    }

    public function testUH07TheCSV()
    {
        $this->fixture();
        $this->db->exec( "UPDATE ezcontentobject SET name = '=HYPERLINK(\"http://example.invalid\")' WHERE id = 101" );
        $this->db->exec( "UPDATE ezuser SET login = '+1+cmd|calc', email = '@SUM(A1)@example.invalid' WHERE contentobject_id = 102" );
        $this->db->exec( "UPDATE ezcontentobject SET name = '-2-3' WHERE id = 103" );
        $this->db->exec( "UPDATE ezcontentobject SET name = '\tTab' WHERE id = 104" );
        $hygiene = $this->hygiene();

        $read = function ( $withEmail, $bom = true ) use ( $hygiene )
        {
            $out = fopen( 'php://memory', 'w+' );
            $count = $hygiene->writeCSV( $out, $this->filters(), $withEmail, null, $bom );
            rewind( $out );
            $csv = stream_get_contents( $out );
            fclose( $out );
            return array( $count, $csv );
        };
        list( $count, $csv ) = $read( true );
        $this->assertSame( 14, $count );
        $this->assertStringStartsWith( "\xEF\xBB\xBF\"User ID\",Name,Login,E-mail,Status,\"Last visit\",Sign-ins,Groups,Roles\r\n", $csv );
        $this->assertStringContainsString( "\"'=HYPERLINK(\"\"http://example.invalid\"\")\"", $csv );
        $this->assertStringContainsString( "'+1+cmd|calc,'@SUM(A1)@example.invalid,", $csv );
        $this->assertStringContainsString( ",'-2-3,", $csv );
        $this->assertStringContainsString( ",\"'\tTab\",", $csv );
        $this->assertStringContainsString( '101,', $csv );
        $this->assertStringContainsString( '"' . gmdate( 'Y-m-d H:i', self::NOW - 2 * self::DAY ) . ' UTC",12,Members,Member', $csv );
        $this->assertStringContainsString( ',"Not activated, Never signed in",,0,', $csv );
        $this->assertSame( 15, substr_count( $csv, "\r\n" ) );

        list( , $plain ) = $read( false, false );
        $this->assertStringStartsWith( "\"User ID\",Name,Login,Status,", $plain, 'no byte order mark, no e-mail column' );
        $this->assertStringNotContainsString( 'example.invalid', str_replace( 'http://example.invalid', '', $plain ), 'no address without the right' );

        // the batches: by id, each after the last one written
        $this->queries = array();
        $first = $hygiene->batch( $this->filters(), true, null, 0, 5 );
        $second = $hygiene->batch( $this->filters(), true, null, 105, 5 );
        $this->assertSame( array( 101, 102, 103, 104, 105 ), $this->ids( $first ) );
        $this->assertSame( array( 106, 107, 108, 109, 110 ), $this->ids( $second ) );
        $this->assertStringContainsString( 'u.contentobject_id > 105', $this->queries[count( $this->queries ) - 6] );

        $this->assertSame( array( 'User ID', 'Name', 'Login', 'Status', 'Last visit', 'Sign-ins', 'Groups', 'Roles' ), expUserHygiene::csvHeader( false ) );
        $this->assertSame( 'Never signed in', expUserHygiene::stateName( 'never' ) );
    }

    private static function read( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 5 ) . '/' . $path );
    }

    public function testUH08ThePage()
    {
        // the view and its function
        $module = self::read( 'kernel/user/module.php' );
        $this->assertMatchesRegularExpression( "/\\\$ViewList\\['hygiene'\\] = array\\(\\s*'functions' => array\\( 'hygiene' \\)/", $module );
        $this->assertStringContainsString( "\$FunctionList['hygiene'] = array();", $module );
        $this->assertArrayHasKey( 'user/hygiene', expRolePolicySentence::functionActions() );

        $view = self::read( 'kernel/private/classes/views/user/hygiene.php' );
        $this->assertStringContainsString( "\$isPost = \\eZSys::serverVariable( 'REQUEST_METHOD', true ) === 'POST';", $view );
        $this->assertStringContainsString( "if ( \$hygiene !== null && \$isPost && \$http->hasPostVariable( 'ExportButton' ) )", $view, 'the CSV only by a POST' );
        $this->assertStringContainsString( 'if ( $isPost )', $view, 'an action only by a POST' );
        $this->assertStringContainsString( '$confirmed = $action !== null;', $view );
        $this->assertStringContainsString( '!$confirmed );', $view, 'a button only asks; Confirm does it' );
        $this->assertStringContainsString( "\\expUserHygiene::protectedIDs( \$currentUser->attribute( 'contentobject_id' ) )", $view );

        // e-mail only with role/assign
        $allowed = new expUserHygieneTestUser( array( 'role/assign' => 'yes' ) );
        $limited = new expUserHygieneTestUser( array( 'role/assign' => 'limited' ) );
        $denied = new expUserHygieneTestUser( array( 'role/read' => 'yes' ) );
        $this->assertTrue( \Exponential\View\Kernel\User\Hygiene::canSeeEmail( $allowed ) );
        $this->assertTrue( \Exponential\View\Kernel\User\Hygiene::canSeeEmail( $limited ) );
        $this->assertFalse( \Exponential\View\Kernel\User\Hygiene::canSeeEmail( $denied ) );

        // the template: the same in both designs, POST forms, e-mail behind the right, protected accounts not tickable
        $tpl = self::read( 'design/admin4/templates/user/hygiene.tpl' );
        $this->assertSame( $tpl, self::read( 'design/admin/templates/user/hygiene.tpl' ) );
        preg_match_all( '/<form\b[^>]*>/', $tpl, $forms );
        $this->assertCount( 3, $forms[0] );
        $this->assertSame( 1, preg_match_all( '/method="get"/', implode( '', $forms[0] ) ), 'only the filters are a GET' );
        $this->assertStringContainsString( 'role="search"', $forms[0][1] );
        foreach ( array( 'DisableButton', 'EnableButton', 'UnlockButton', 'ExportButton', 'ConfirmButton' ) as $button )
            $this->assertStringContainsString( 'name="' . $button . '"', $tpl );
        $this->assertSame( 2, substr_count( $tpl, '{if $hygiene_can_see_email}' ), 'the e-mail cell and its help text' );
        $this->assertStringContainsString( "{if and( eq( \$column, 'email' ), \$hygiene_can_see_email|not )}", $tpl );
        $this->assertStringContainsString( '{if $is_protected}<input type="checkbox" disabled="disabled"', $tpl );
        $this->assertStringNotContainsString( '|wash( no )', $tpl );
        foreach ( array( 'name', 'login', 'email', 'groups' ) as $field )
            $this->assertDoesNotMatchRegularExpression( '/\{\$(account|group)\.' . $field . '\}/', $tpl, "$field is washed" );

        // the menus and the Setup overview
        $menu = self::read( 'settings/menu.ini' );
        $this->assertSame( 2, substr_count( $menu, "Links[user_hygiene]=user/hygiene\n" ), 'in the Users and the Setup menu' );
        $this->assertSame( 2, substr_count( $menu, "PolicyList_user_hygiene[]=user/hygiene\n" ) );
        $this->assertStringContainsString( "GroupLinks_users[]=user_hygiene\n", $menu );
        $this->assertStringContainsString( "LinkIcons[user_hygiene]=person-check\n", $menu );
        foreach ( array( 'design/admin', 'design/admin3', 'design/admin4' ) as $design )
            $this->assertStringContainsString( "'user_hygiene',       'Check user accounts'|i18n( 'design/admin/parts/user/menu' )", self::read( "$design/templates/parts/user/menu.tpl" ) );
        foreach ( array( 'design/admin', 'design/admin4' ) as $design )
            $this->assertStringContainsString( "'user_hygiene',       'Check user accounts'|i18n( 'design/admin/parts/setup/menu' )", self::read( "$design/templates/parts/setup/menu.tpl" ) );

        // every text of the page and of the class in both translations
        preg_match_all( "/'((?:[^'\\\\]|\\\\.)+)'\|i18n\( 'design\/admin\/user\/hygiene'/", $tpl, $m );
        $sources = array_unique( array_merge( $m[1], expUserHygiene::csvHeader( true ), array( 'Check user accounts' ) ) );
        foreach ( array( 'active', 'disabled', 'unactivated', 'locked', 'no_object', 'draft_only', 'no_location', 'never' ) as $state )
            $sources[] = expUserHygiene::stateName( $state );
        $this->assertGreaterThan( 60, count( array_unique( $sources ) ) );
        foreach ( array( 'ger-DE', 'eng-US' ) as $locale )
        {
            $ts = self::read( "share/translations/$locale/translation.ts" );
            $start = strpos( $ts, '<name>' . expUserHygiene::CONTEXT . '</name>' );
            $this->assertNotFalse( $start, "$locale has the context" );
            $context = substr( $ts, $start, strpos( $ts, '</context>', $start ) - $start );
            foreach ( array_unique( $sources ) as $source )
                $this->assertStringContainsString( '<source>' . htmlspecialchars( $source, ENT_NOQUOTES ) . '</source>', $context, "$locale: $source" );
            foreach ( array( 'design/admin/parts/user/menu' => 'Check user accounts', 'design/admin/parts/setup/menu' => 'Check user accounts',
                             'design/admin/role/sentence' => 'check user accounts, and disable, enable and unlock them' ) as $name => $source )
            {
                $start = strpos( $ts, "<name>$name</name>" );
                $context = substr( $ts, $start, strpos( $ts, '</context>', $start ) - $start );
                $this->assertStringContainsString( "<source>$source</source>", $context, "$locale $name" );
            }
        }
        $this->assertStringContainsString( "<source>Check user accounts</source>\n        <translation>Benutzerkonten prüfen</translation>",
                                           self::read( 'share/translations/ger-DE/translation.ts' ) );
    }
}

/** A user whose access is given by hand: module/function => accessWord */
class expUserHygieneTestUser
{
    private $access;

    public function __construct( array $access )
    {
        $this->access = $access;
    }

    public function hasAccessTo( $module, $function )
    {
        return array( 'accessWord' => isset( $this->access["$module/$function"] ) ? $this->access["$module/$function"] : 'no' );
    }
}
