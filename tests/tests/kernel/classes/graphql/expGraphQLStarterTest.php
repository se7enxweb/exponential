<?php
/**
 * The starter kits of the GraphQL API without a database (exp:graphql:starter; doc/eng-us/guides/graphql-walkthrough.md):
 * every kit is shipped with its README and gets the generated queries in its own module and as .graphql files; every
 * placeholder is filled; a template named _env becomes .env; every generated query fits the schema it was made from;
 * a field that needs an argument is left out; writing refuses a directory with files and a path that leaves the kit.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expGraphQLStarterTest extends PHPUnit\Framework\TestCase
{
    /** @var string[] directories made by a test */
    protected $dirs = array();

    public static function setUpBeforeClass(): void
    {
        if ( !expGraphQL::loadLibrary() )
        {
            if ( getenv( 'CI' ) )
                self::fail( 'webonyx/graphql-php is not installed (composer.json requires it)' );
            self::markTestSkipped( 'webonyx/graphql-php is not installed; set GRAPHQL_LIBRARY_PATH' );
        }
    }

    protected function tearDown(): void
    {
        foreach ( $this->dirs as $dir )
        {
            if ( !is_dir( $dir ) )
                continue;
            $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
            foreach ( $files as $file )
                $file->isDir() ? rmdir( $file->getPathname() ) : unlink( $file->getPathname() );
            rmdir( $dir );
        }
    }

    protected static function builder()
    {
        $attribute = function ( $identifier, $datatype ) {
            return array( 'id' => crc32( $identifier ), 'identifier' => $identifier, 'datatype' => $datatype, 'name' => ucfirst( $identifier ),
                          'description' => '', 'required' => false, 'options' => array() );
        };
        $classes = array(
            array( 'id' => 1, 'identifier' => 'folder', 'name' => 'Folder', 'description' => '',
                   'attributes' => array( $attribute( 'name', 'ezstring' ), $attribute( 'description', 'ezxmltext' ) ) ),
            array( 'id' => 2, 'identifier' => 'article', 'name' => 'Article', 'description' => '',
                   'attributes' => array( $attribute( 'title', 'ezstring' ), $attribute( 'intro', 'ezxmltext' ), $attribute( 'image', 'ezimage' ),
                                          $attribute( 'rating', 'ezinteger' ), $attribute( 'related', 'ezobjectrelationlist' ),
                                          $attribute( 'kind', 'ezselection' ), $attribute( 'file', 'ezbinaryfile' ), $attribute( 'where', 'ezgmaplocation' ) ) ),
            array( 'id' => 3, 'identifier' => 'news-item', 'name' => 'News item', 'description' => '',
                   'attributes' => array( $attribute( 'title', 'ezstring' ) ) ),
        );
        $settings = array( 'ClassList' => array(), 'ExcludedClassList' => array(), 'HiddenFieldList' => array(), 'HiddenDatatypeList' => array() );
        return new expGraphQLSchemaBuilder( expGraphQLClassCatalogue::filtered( $classes, $settings ), new expGraphQLNaming( 'identifier' ) );
    }

    public function testEveryKitIsShipped()
    {
        $root = dirname( __DIR__, 5 ) . '/share/graphql/starters';
        $this->assertSame( array( 'html', 'next', 'nuxt', 'astro', 'expo' ), expGraphQLStarter::kits() );
        foreach ( expGraphQLStarter::kits() as $kit )
        {
            $this->assertFileExists( "$root/$kit/README.md", $kit );
            $this->assertStringContainsString( "exp:graphql:starter --kit=$kit", file_get_contents( "$root/$kit/README.md" ) );
        }
    }

    public function testQueriesFitTheSchema()
    {
        $builder = self::builder();
        $starter = new expGraphQLStarter( $builder );
        $queries = $starter->queries();
        $this->assertSame( array( 'Menu', 'Node', 'Content_folder', 'Content_article', 'Content_news-item' ), array_keys( $queries ) );
        foreach ( $queries as $name => $query )
        {
            $errors = GraphQL\Validator\DocumentValidator::validate( $builder->schema(), GraphQL\Language\Parser::parse( $query ) );
            $this->assertSame( array(), array_map( function ( $e ) { return $e->getMessage(); }, $errors ), $name . "\n" . $query );
        }
        $article = $queries['Content_article'];
        $this->assertStringContainsString( 'image { url(alias: "large") width(alias: "large") height(alias: "large") alt }', $article );
        $this->assertStringContainsString( 'intro { html }', $article );
        $this->assertStringContainsString( 'related { id name urlAlias classIdentifier }', $article );
        $this->assertStringContainsString( 'rating', $article );
        $this->assertStringContainsString( 'query Content_news_item($id: Int!)', $queries['Content_news-item'], 'a valid operation name' );
        $this->assertStringNotContainsString( 'children', $article, 'the fields of every content are not repeated' );
    }

    public function testFilesOfEveryKit()
    {
        $starter = new expGraphQLStarter( self::builder() );
        foreach ( expGraphQLStarter::kits() as $kit )
        {
            $files = $starter->files( $kit, array( 'endpoint' => 'https://www.example.com/api/graphql', 'site' => 'site' ) );
            foreach ( $files as $path => $content )
                $this->assertStringNotContainsString( '__EXP_', $content, "$kit $path" );
            $this->assertArrayHasKey( 'README.md', $files, $kit );
            $this->assertArrayHasKey( 'graphql/menu.graphql', $files, $kit );
            $this->assertArrayHasKey( 'graphql/content-news-item.graphql', $files, $kit );
            $module = $files[expGraphQLStarter::KITS[$kit]['queries']];
            foreach ( array( 'MENU', 'NODE', 'BY_CLASS', '"article"' ) as $needle )
                $this->assertStringContainsString( $needle, $module, $kit );
            $this->assertStringContainsString( self::builder()->schemaHash(), $module );
            $this->assertStringContainsString( 'https://www.example.com/api/graphql', implode( '', $files ), "$kit names the end point" );
            foreach ( array_keys( $files ) as $path )
                $this->assertDoesNotMatchRegularExpression( '#(^|/)_#', $path, "$kit: a template's _ became a dot" );
        }
        $next = $starter->files( 'next', array( 'endpoint' => 'https://x/api/graphql', 'site' => 'site' ) );
        $this->assertArrayHasKey( '.env.local', $next );
        $this->assertStringContainsString( 'EXP_GRAPHQL_ENDPOINT=https://x/api/graphql', $next['.env.local'] );
        $html = $starter->files( 'html', array( 'endpoint' => 'https://x/api/graphql', 'site' => 'site' ) );
        $this->assertStringStartsWith( '// Generated by', $html['queries.js'] );
        $this->assertStringContainsString( 'window.EXP_QUERIES = {', $html['queries.js'] );

        $this->expectException( 'expGraphQLError' );
        $starter->files( 'angular', array( 'endpoint' => '', 'site' => '' ) );
    }

    public function testWrite()
    {
        $dir = sys_get_temp_dir() . '/exp-graphql-starter-' . getmypid() . '-' . mt_rand();
        $this->dirs[] = $dir;
        $this->assertSame( 2, expGraphQLStarter::write( $dir, array( 'a.txt' => 'A', 'sub/b.txt' => 'B' ) ) );
        $this->assertSame( 'B', file_get_contents( "$dir/sub/b.txt" ) );
        try
        {
            expGraphQLStarter::write( $dir, array( 'c.txt' => 'C' ) );
            $this->fail( 'a directory with files is refused' );
        }
        catch ( expGraphQLError $e )
        {
            $this->assertFileDoesNotExist( "$dir/c.txt" );
        }
        $other = $dir . '-2';
        $this->dirs[] = $other;
        foreach ( array( '../escape.txt', '/etc/x', 'a/../../b' ) as $path )
        {
            try
            {
                expGraphQLStarter::write( $other, array( $path => 'x' ) );
                $this->fail( "$path is refused" );
            }
            catch ( expGraphQLError $e )
            {
                $this->assertSame( 'BAD_USER_INPUT', $e->errorCode() );
            }
        }
    }
}

?>
