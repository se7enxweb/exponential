<?php
/**
 * The query-count budget of the GraphQL API (decision 18; doc/eng-us/features/6.0/graphql.md, "Speed"): 50 articles
 * with their image, owner and address are read in about 11 SQL statements, however many articles there are, because
 * the loaders fetch a whole page at once. One statement per article would be 50 more; this test fails long before.
 *
 * It needs real content: a COPY of an SQLite database with at least 50 objects of a class below a node, named by
 *
 *   GRAPHQL_BUDGET_DB      the database file (never a live one: the test only reads, but a copy keeps it so)
 *   GRAPHQL_BUDGET_CLASS   the class of the articles (default ng_article)
 *   GRAPHQL_BUDGET_PARENT  the node they are below (default 2)
 *
 * Without GRAPHQL_BUDGET_DB it is skipped (the DB-less suite of CI). The statements are counted from the start of the
 * query to its answer, in a schema built before; the anonymous user is the reader.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

/** The SQLite driver, counting the statements it runs. */
class expGraphQLCountingSQLite3DB extends eZSQLite3DB
{
    /** @var string[] */
    public $statements = array();

    function query( $sql, $server = false )
    {
        $this->statements[] = (string)$sql;
        return parent::query( $sql, $server );
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        $this->statements[] = (string)$sql;
        return parent::arrayQuery( $sql, $params, $server );
    }
}

class expGraphQLQueryBudgetTest extends PHPUnit\Framework\TestCase
{
    /** The budget: "about 11" statements for 50 articles with image, owner and address. */
    const BUDGET = 12;

    /** @var eZDBInterface|null the instance before the test */
    protected $previous;

    /** @var expGraphQLCountingSQLite3DB */
    protected $db;

    protected function setUp(): void
    {
        $file = getenv( 'GRAPHQL_BUDGET_DB' );
        if ( !is_string( $file ) || $file === '' )
            $this->markTestSkipped( 'set GRAPHQL_BUDGET_DB to a copy of an SQLite database with content' );
        if ( !is_file( $file ) || !extension_loaded( 'sqlite3' ) )
            $this->fail( "GRAPHQL_BUDGET_DB is not an SQLite file here: $file" );
        if ( !expGraphQL::loadLibrary() )
            $this->markTestSkipped( 'webonyx/graphql-php is not installed; set GRAPHQL_LIBRARY_PATH' );
        $this->previous = isset( $GLOBALS['eZDBGlobalInstance'] ) ? $GLOBALS['eZDBGlobalInstance'] : null;
        $this->db = new expGraphQLCountingSQLite3DB( array(
            'server' => '', 'port' => '', 'user' => '', 'password' => '', 'database' => realpath( $file ),
            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => '', 'slave_user' => '', 'slave_password' => '',
            'slave_database' => '', 'socket' => false, 'charset' => 'utf-8', 'is_internal_charset' => true,
            'builtin_encoding' => false, 'connect_retries' => 1, 'use_persistent_connection' => 'disabled' ) );
        eZDB::setInstance( $this->db );
    }

    protected function tearDown(): void
    {
        if ( $this->db !== null )
            $this->db->close();
        eZDB::setInstance( $this->previous );
    }

    public function testFiftyArticlesWithImageOwnerAndAddressStayWithinTheBudget()
    {
        $class = getenv( 'GRAPHQL_BUDGET_CLASS' ) ?: 'ng_article';
        $parent = (int)( getenv( 'GRAPHQL_BUDGET_PARENT' ) ?: 2 );
        $builder = expGraphQLSchemaBuilder::fromSettings();
        if ( !in_array( $class, $builder->catalogue()->identifiers(), true ) )
            $this->markTestSkipped( "the class $class is not in the schema of this siteaccess" );
        $builder->schema();
        $user = eZUser::fetch( (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) );
        $this->assertInstanceOf( 'eZUser', $user );
        $context = new expGraphQLContext( $user, expGraphQLContext::AUTH_ANONYMOUS, new expGraphQLRepository( $builder->catalogue() ), 'https://example.com' );
        $type = array_search( $class, $builder->typeNames(), true );
        $image = expGraphQLNaming::fromSettings()->fieldName( $class, 'image' );
        $query = 'query($parent: Int!, $classes: [String!], $limit: Int) { children(parentId: $parent, depth: 10, classes: $classes, limit: $limit, sortField: PUBLISHED, sortOrder: DESC) {'
               . " nodes { name url ownerName ... on $type { $image { url(alias: \"medium\") width(alias: \"medium\") alt } } } } }";
        $server = new expGraphQLServer( $builder );
        $run = function ( $limit ) use ( $server, $query, $parent, $class, $context ) {
            $this->db->statements = array();
            $answer = $server->execute( $query, array( 'parent' => $parent, 'classes' => array( $class ), 'limit' => $limit ), null, $context );
            $this->assertArrayNotHasKey( 'errors', $answer, json_encode( isset( $answer['errors'] ) ? $answer['errors'] : null ) );
            return array( count( $answer['data']['children']['nodes'] ), $this->db->statements );
        };
        // a small page first, as a request is warmed up (the classes, the user's rights); then 3 and 50
        $run( 2 );
        list( $few, $fewStatements ) = $run( 3 );
        list( $count, $statements ) = $run( 50 );
        if ( $count < 20 )
            $this->markTestSkipped( "$count objects of $class below node $parent that the anonymous user may read; at least 20 are needed" );
        $list = "\n" . implode( "\n", array_map( function ( $sql ) { return substr( preg_replace( '/\s+/', ' ', $sql ), 0, 160 ); }, $statements ) );
        $this->assertLessThanOrEqual( self::BUDGET, count( $statements ), count( $statements ) . " SQL statements for $count articles (budget " . self::BUDGET . "):$list" );
        // the size of the page does not count: one statement per article would be $count more
        $this->assertLessThanOrEqual( count( $fewStatements ) + 1, count( $statements ),
                                      count( $fewStatements ) . " statements for $few articles, " . count( $statements ) . " for $count:$list" );
    }
}
