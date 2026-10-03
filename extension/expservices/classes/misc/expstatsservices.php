<?php
/**
 * ezjscore/call/expstats::<service> - statistics: search phrases, content counts, growth, languages, users and
 * sessions. Content figures follow content/read; search statistics, sessions and user figures need setup/administrate.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expStatsServices extends expAttrServiceBase
{
    public static $services = array(
        'searchtop' => array( 'summary' => 'The most frequent search phrases with their average result count', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of phrase, phrase_count, result_count' ),
        'searchtotal' => array( 'summary' => 'Number of distinct search phrases and of searches', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array(), 'returns' => 'phrases, searches' ),
        'searchnoresults' => array( 'summary' => 'Search phrases that found nothing', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array( 'limit' => 'int' ), 'returns' => 'list of phrase, phrase_count' ),
        'searchphrase' => array( 'summary' => 'The statistics of one search phrase', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array( 'phrase' => 'string' ), 'returns' => 'phrase, phrase_count, result_count' ),
        'contenttotals' => array( 'summary' => 'Counts of objects, nodes, versions and classes', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'objects, nodes, hidden_nodes, trashed, drafts, classes' ),
        'classcounts' => array( 'summary' => 'Published objects per class', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'limit' => 'int' ), 'returns' => 'list of class, name, count' ),
        'sectioncounts' => array( 'summary' => 'Published objects per section', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'list of section_id, name, count' ),
        'statecounts' => array( 'summary' => 'Objects per object state', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'list of state, group, count' ),
        'recentpublished' => array( 'summary' => 'The most recently published objects', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'limit' => 'int' ), 'returns' => 'list of nodes' ),
        'recentmodified' => array( 'summary' => 'The most recently modified objects', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'limit' => 'int' ), 'returns' => 'list of nodes' ),
        'growth' => array( 'summary' => 'Objects published per day for the last N days (at most 90)', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'days' => 'int' ), 'returns' => 'list of day, count' ),
        'topowners' => array( 'summary' => 'The users who own most published objects', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array( 'limit' => 'int' ), 'returns' => 'list of owner id, name, count' ),
        'languagecounts' => array( 'summary' => 'Published object translations per language', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'list of locale, count' ),
        'users' => array( 'summary' => 'Number of user accounts, enabled and disabled', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array(), 'returns' => 'users, enabled, disabled' ),
        'sessions' => array( 'summary' => 'Active sessions (not expired) and how many are logged-in users', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array(), 'returns' => 'active, registered' ),
        'overview' => array( 'summary' => 'The headline figures in one answer', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'objects, nodes, classes, published_today' ),
    );

    public static function searchtop( $args )
    {
        self::guard( __FUNCTION__ );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $rows = eZSearchLog::mostFrequentPhraseArray( array( 'limit' => $limit, 'offset' => $offset ) );
        $items = array();
        foreach ( is_array( $rows ) ? $rows : array() as $r )
            $items[] = array( 'id' => (int)$r['id'], 'phrase' => $r['phrase'], 'phrase_count' => (int)$r['phrase_count'], 'result_count' => round( (float)$r['result_count'], 2 ) );
        return self::page( $items, self::scalar( 'SELECT COUNT(*) AS n FROM ezsearch_search_phrase' ), $offset, $limit );
    }

    public static function searchtotal( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array( 'phrases' => self::scalar( 'SELECT COUNT(*) AS n FROM ezsearch_search_phrase' ),
                                'searches' => self::scalar( 'SELECT COALESCE(SUM(phrase_count),0) AS n FROM ezsearch_search_phrase' ) ) );
    }

    public static function searchnoresults( $args )
    {
        self::guard( __FUNCTION__ );
        list( $limit ) = self::paging( $args, 0, 99 );
        $items = array();
        foreach ( self::rows( 'SELECT phrase, phrase_count FROM ezsearch_search_phrase WHERE result_count=0 ORDER BY phrase_count DESC', array( 'limit' => $limit ) ) as $r )
            $items[] = array( 'phrase' => $r['phrase'], 'phrase_count' => (int)$r['phrase_count'] );
        return self::ok( $items, array( 'limit' => $limit ) );
    }

    public static function searchphrase( $args )
    {
        self::guard( __FUNCTION__ );
        $e = eZDB::instance()->escapeString( self::arg( $args, 0, 'string' ) );
        $rows = self::rows( "SELECT phrase, phrase_count, result_count FROM ezsearch_search_phrase WHERE phrase='$e'" );
        if ( !$rows )
            throw new expServiceException( 'No such search phrase', 404 );
        return self::ok( array( 'phrase' => $rows[0]['phrase'], 'phrase_count' => (int)$rows[0]['phrase_count'],
                                'result_count' => $rows[0]['phrase_count'] ? round( $rows[0]['result_count'] / $rows[0]['phrase_count'], 2 ) : 0 ) );
    }

    public static function contenttotals( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array( 'objects' => self::scalar( 'SELECT COUNT(*) AS n FROM ezcontentobject WHERE status=1' ),
                                'nodes' => self::scalar( 'SELECT COUNT(*) AS n FROM ezcontentobject_tree' ),
                                'hidden_nodes' => self::scalar( 'SELECT COUNT(*) AS n FROM ezcontentobject_tree WHERE is_hidden=1' ),
                                'trashed' => self::scalar( 'SELECT COUNT(*) AS n FROM ezcontentobject_trash' ),
                                'drafts' => self::scalar( 'SELECT COUNT(*) AS n FROM ezcontentobject_version WHERE status=0' ),
                                'classes' => self::scalar( 'SELECT COUNT(*) AS n FROM ezcontentclass WHERE version=0' ) ) );
    }

    public static function classcounts( $args )
    {
        self::guard( __FUNCTION__ );
        list( $limit ) = self::paging( $args, 0, 99 );
        $items = array();
        foreach ( self::rows( 'SELECT c.identifier AS identifier, c.id AS id, COUNT(o.id) AS n FROM ezcontentclass c, ezcontentobject o WHERE o.contentclass_id=c.id AND c.version=0 AND o.status=1 GROUP BY c.id, c.identifier ORDER BY n DESC', array( 'limit' => $limit ) ) as $r )
            $items[] = array( 'class' => $r['identifier'], 'class_id' => (int)$r['id'], 'count' => (int)$r['n'] );
        return self::ok( $items, array( 'limit' => $limit ) );
    }

    public static function sectioncounts( $args )
    {
        self::guard( __FUNCTION__ );
        $items = array();
        foreach ( self::rows( 'SELECT s.id AS id, s.name AS name, COUNT(o.id) AS n FROM ezsection s, ezcontentobject o WHERE o.section_id=s.id AND o.status=1 GROUP BY s.id, s.name ORDER BY n DESC' ) as $r )
            $items[] = array( 'section_id' => (int)$r['id'], 'name' => $r['name'], 'count' => (int)$r['n'] );
        return self::ok( $items );
    }

    public static function statecounts( $args )
    {
        self::guard( __FUNCTION__ );
        $items = array();
        foreach ( self::rows( 'SELECT s.identifier AS state, g.identifier AS grp, COUNT(l.contentobject_id) AS n FROM ezcobj_state s, ezcobj_state_group g, ezcobj_state_link l WHERE s.group_id=g.id AND l.contentobject_state_id=s.id GROUP BY s.id, s.identifier, g.identifier ORDER BY n DESC' ) as $r )
            $items[] = array( 'state' => $r['state'], 'group' => $r['grp'], 'count' => (int)$r['n'] );
        return self::ok( $items );
    }

    protected static function recentBy( $args, $sort )
    {
        list( $limit ) = self::paging( $args, 0, 99 );
        $root = (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' );
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( array( 'SortBy' => array( $sort, false ), 'Limit' => $limit, 'LoadDataMap' => false,
                                                                  'ClassFilterType' => 'exclude', 'ClassFilterArray' => array( 'user', 'user_group' ) ), $root );
        $items = array();
        foreach ( is_array( $nodes ) ? $nodes : array() as $n )
            $items[] = self::exportNode( $n );
        return self::ok( $items, array( 'limit' => $limit ) );
    }

    public static function recentpublished( $args )
    {
        self::guard( __FUNCTION__ );
        return self::recentBy( $args, 'published' );
    }

    public static function recentmodified( $args )
    {
        self::guard( __FUNCTION__ );
        return self::recentBy( $args, 'modified' );
    }

    public static function growth( $args )
    {
        self::guard( __FUNCTION__ );
        $days = max( 1, min( 90, self::arg( $args, 0, 'int', 14 ) ) );
        $from = mktime( 0, 0, 0, (int)date( 'n' ), (int)date( 'j' ) - ( $days - 1 ) );
        $counts = array();
        foreach ( self::rows( "SELECT published FROM ezcontentobject WHERE status=1 AND published>=$from", array( 'limit' => 100000 ) ) as $r )
        {
            $d = date( 'Y-m-d', (int)$r['published'] );
            $counts[$d] = ( isset( $counts[$d] ) ? $counts[$d] : 0 ) + 1;
        }
        $list = array();
        for ( $i = $days - 1; $i >= 0; $i-- )
        {
            $d = date( 'Y-m-d', mktime( 0, 0, 0, (int)date( 'n' ), (int)date( 'j' ) - $i ) );
            $list[] = array( 'day' => $d, 'count' => isset( $counts[$d] ) ? $counts[$d] : 0 );
        }
        return self::ok( $list );
    }

    public static function topowners( $args )
    {
        self::guard( __FUNCTION__ );
        list( $limit ) = self::paging( $args, 0, 99 );
        $items = array();
        foreach ( self::rows( 'SELECT owner_id, COUNT(*) AS n FROM ezcontentobject WHERE status=1 GROUP BY owner_id ORDER BY n DESC', array( 'limit' => $limit ) ) as $r )
        {
            $o = eZContentObject::fetch( (int)$r['owner_id'] );
            $items[] = array( 'owner_id' => (int)$r['owner_id'], 'name' => $o ? $o->attribute( 'name' ) : '', 'count' => (int)$r['n'] );
        }
        return self::ok( $items, array( 'limit' => $limit ) );
    }

    public static function languagecounts( $args )
    {
        self::guard( __FUNCTION__ );
        $items = array();
        foreach ( self::rows( 'SELECT real_translation AS l, COUNT(*) AS n FROM ezcontentobject_name GROUP BY real_translation ORDER BY n DESC' ) as $r )
            $items[] = array( 'locale' => $r['l'], 'count' => (int)$r['n'] );
        return self::ok( $items );
    }

    public static function users( $args )
    {
        self::guard( __FUNCTION__ );
        $enabled = self::scalar( 'SELECT COUNT(*) AS n FROM ezuser u, ezuser_setting s WHERE s.user_id=u.contentobject_id AND s.is_enabled=1' );
        $all = self::scalar( 'SELECT COUNT(*) AS n FROM ezuser' );
        return self::ok( array( 'users' => $all, 'enabled' => $enabled, 'disabled' => max( 0, $all - $enabled ) ) );
    }

    public static function sessions( $args )
    {
        self::guard( __FUNCTION__ );
        $now = time();
        return self::ok( array( 'active' => self::scalar( "SELECT COUNT(*) AS n FROM ezsession WHERE expiration_time>$now" ),
                                'registered' => self::scalar( "SELECT COUNT(*) AS n FROM ezsession WHERE expiration_time>$now AND user_id<>" . (int)eZUser::anonymousId() ) ) );
    }

    public static function overview( $args )
    {
        self::guard( __FUNCTION__ );
        $today = mktime( 0, 0, 0 );
        return self::ok( array( 'objects' => self::scalar( 'SELECT COUNT(*) AS n FROM ezcontentobject WHERE status=1' ),
                                'nodes' => self::scalar( 'SELECT COUNT(*) AS n FROM ezcontentobject_tree' ),
                                'classes' => self::scalar( 'SELECT COUNT(*) AS n FROM ezcontentclass WHERE version=0' ),
                                'published_today' => self::scalar( "SELECT COUNT(*) AS n FROM ezcontentobject WHERE status=1 AND published>=$today" ) ) );
    }
}
