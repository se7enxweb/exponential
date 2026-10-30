<?php
/**
 * The stability contract of the GraphQL commands (exp:graphql:*; doc/eng-us/features/6.0/graphql.md): command names
 * and aliases, option names, exit codes, the formats and the machine output schemas are permanent, and so are the
 * error codes of the API (extensions.code) and the names of the schema's own types and content fields.
 *
 * NOTE for whoever changes this file: an ADDITION (a new alias, option, exit code, format, error code, type) is added
 * to the snapshot below in the same commit. A REMOVAL or RENAME is not allowed: a released name stays, a better one
 * is added next to it.
 *
 * No database, no siteaccess, no library.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expGraphQLCommandContractTest extends PHPUnit\Framework\TestCase
{
    /** bin script => its aliases */
    const COMMANDS = array(
        'expgraphqlstatus' => array( 'exp:graphql:status' ),
        'expgraphqlschema' => array( 'exp:graphql:schema' ),
        'expgraphqlquery' => array( 'exp:graphql:query' ),
        'expgraphqlpersist' => array( 'exp:graphql:persist' ),
    );

    /** command => its option names */
    const OPTIONS = array(
        'expgraphqlstatus' => array( 'format' ),
        'expgraphqlschema' => array( 'format', 'output' ),
        'expgraphqlquery' => array( 'variables', 'operation', 'as', 'format', 'id' ),
        'expgraphqlpersist' => array( 'name', 'list', 'remove', 'apply', 'format' ),
    );

    /** command class => exit code constants */
    const EXIT_CODES = array(
        'Expgraphqlstatus' => array( 'EXIT_OK' => 0, 'EXIT_USAGE' => 2, 'EXIT_UNAVAILABLE' => 3 ),
        'Expgraphqlschema' => array( 'EXIT_OK' => 0, 'EXIT_ERRORS' => 1, 'EXIT_USAGE' => 2, 'EXIT_UNAVAILABLE' => 3 ),
        'Expgraphqlquery' => array( 'EXIT_OK' => 0, 'EXIT_ERRORS' => 1, 'EXIT_USAGE' => 2, 'EXIT_UNAVAILABLE' => 3 ),
        'Expgraphqlpersist' => array( 'EXIT_OK' => 0, 'EXIT_ERRORS' => 1, 'EXIT_USAGE' => 2, 'EXIT_UNAVAILABLE' => 3 ),
    );

    const ERROR_CODES = array( 'FORBIDDEN', 'NOT_FOUND', 'VALIDATION', 'PARSE', 'BAD_USER_INPUT', 'DEPTH_LIMIT', 'COMPLEXITY_LIMIT',
                               'INTROSPECTION_DISABLED', 'RATE_LIMITED', 'DISABLED', 'INTERNAL', 'PERSISTED_QUERY_NOT_FOUND',
                               'PERSISTED_QUERY_NOT_SUPPORTED', 'PERSISTED_QUERY_REQUIRED' );

    const CONTENT_FIELDS = array( 'id', 'remoteId', 'name', 'classIdentifier', 'className', 'published', 'modified', 'language',
                                  'languages', 'mainLanguage', 'ownerId', 'ownerName', 'sectionId', 'url', 'urlAlias', 'location',
                                  'mainLocation', 'locations', 'parent', 'children' );

    const RESERVED_TYPES = array( 'Query', 'Mutation', 'Subscription', 'Content', 'Location', 'ContentConnection', 'ContentEdge',
                                  'PageInfo', 'ContentSortField', 'SortOrder', 'Image', 'ImageVariation', 'RichText', 'BinaryFile',
                                  'Price', 'Link', 'SelectionOption', 'Country', 'Author', 'Matrix', 'User', 'SiteAccess', 'Language',
                                  'Int', 'Float', 'String', 'Boolean', 'ID',
                                  'GeoLocation', 'Rating', 'Metadata', 'EnhancedLink', 'Option', 'OptionList', 'OptionGroup', 'Range',
                                  'CurrencyPrice', 'EnumValue', 'SearchResult', 'SearchFacet', 'SearchFacetValue', 'Tag', 'Layout',
                                  'LayoutZone', 'LayoutBlock', 'LayoutBlockParameter' );

    const SORT_FIELDS = array( 'PATH', 'PUBLISHED', 'MODIFIED', 'SECTION', 'DEPTH', 'CLASS_IDENTIFIER', 'CLASS_NAME', 'PRIORITY', 'NAME', 'NODE_ID' );

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 6 ) );
    }

    private static function sameSet( array $expected, array $actual, $what )
    {
        self::assertSame( array(), array_values( array_diff( $expected, $actual ) ), "$what removed or renamed: not allowed" );
        self::assertSame( array(), array_values( array_diff( $actual, $expected ) ), "$what added: add it to the snapshot of expGraphQLCommandContractTest in the same commit" );
    }

    public function testCommandNamesAndAliases()
    {
        foreach ( self::COMMANDS as $script => $aliases )
        {
            $this->assertFileExists( "bin/php/$script.php" );
            $this->assertTrue( is_executable( "bin/php/$script.php" ), "bin/php/$script.php is executable" );
            foreach ( array( "bin/php/$script.php", "kernel/private/classes/commands/$script.php" ) as $file )
            {
                preg_match_all( '/^\s*\*\s*@alias\s+(\S+)$/m', file_get_contents( $file ), $m );
                self::sameSet( $aliases, $m[1], "an alias of $file" );
            }
        }
        $all = array();
        foreach ( glob( 'bin/php/*.php' ) as $file )
        {
            preg_match_all( '/^\s*\*\s*@alias\s+(exp:graphql:\S+)$/m', file_get_contents( $file ), $m );
            $all = array_merge( $all, $m[1] );
        }
        self::sameSet( array_merge( ...array_values( self::COMMANDS ) ), $all, 'an exp:graphql:* command' );
    }

    public function testOptionNames()
    {
        foreach ( self::OPTIONS as $script => $options )
        {
            $code = file_get_contents( "kernel/private/classes/commands/$script.php" );
            $this->assertSame( 1, preg_match( "/startup\\( '([^']*)'/", $code, $m ), $script );
            preg_match_all( '/\[([a-z-]+)[:;]?\*?\]/', $m[1], $names );
            self::sameSet( $options, $names[1], "an option of $script" );
        }
    }

    public function testExitCodesAndFormats()
    {
        foreach ( self::EXIT_CODES as $class => $codes )
        {
            foreach ( $codes as $name => $value )
                $this->assertSame( $value, constant( "Exponential\\Command\\Kernel\\$class::$name" ), "$class::$name" );
        }
        $this->assertSame( 'exp-graphql-status/1', Exponential\Command\Kernel\Expgraphqlstatus::SCHEMA );
        $this->assertSame( 'exp-graphql-persisted/1', Exponential\Command\Kernel\Expgraphqlpersist::SCHEMA );
        self::sameSet( array( 'sdl', 'json', 'summary' ), Exponential\Command\Kernel\Expgraphqlschema::FORMATS, 'a format of exp:graphql:schema' );
        $this->assertSame( array( array( 'data.a.0', '1' ), array( 'data.a.1', 'true' ), array( 'data.b', 'null' ), array( 'data.c', '[]' ) ),
                           Exponential\Command\Kernel\Expgraphqlquery::rows( array( 'data' => array( 'a' => array( 1, true ), 'b' => null, 'c' => array() ) ) ) );
    }

    public function testErrorCodesTypesAndFields()
    {
        $codes = array();
        foreach ( ( new ReflectionClass( 'expGraphQLError' ) )->getConstants() as $value )
            $codes[] = $value;
        self::sameSet( self::ERROR_CODES, $codes, 'an error code' );
        self::sameSet( self::CONTENT_FIELDS, expGraphQLNaming::CONTENT_FIELDS, 'a field of Content' );
        self::sameSet( self::RESERVED_TYPES, expGraphQLNaming::RESERVED_TYPES, 'a type of the schema' );
        self::sameSet( self::SORT_FIELDS, array_keys( expGraphQLRepository::SORT_FIELDS ), 'a value of ContentSortField' );
    }
}
