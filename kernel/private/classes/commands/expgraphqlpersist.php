<?php
/**
 * @description Store GraphQL queries on the server as persisted queries (called by name or SHA-256), list them or remove them; a dry run unless --apply
 * @alias exp:graphql:persist
 *
 * File containing the exp:graphql:persist command (bin/php/expgraphqlpersist.php); guide: doc/eng-us/features/6.0/graphql.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expgraphqlpersist extends \Exponential\Runnable\Command
{
    const EXIT_OK = 0;
    const EXIT_ERRORS = 1;
    const EXIT_USAGE = 2;
    const EXIT_UNAVAILABLE = 3;

    /** The machine output of --list --format=json. */
    const SCHEMA = 'exp-graphql-persisted/1';

    public function run()
    {
        $this->script( array( 'description' => "Store GraphQL queries on the server as persisted queries\n" .
                                               "Each .graphql file (a file, or every .graphql file of a directory) is checked against the schema of\n" .
                                               "the siteaccess and stored under its SHA-256 and its file name without .graphql (its name). A client\n" .
                                               "then runs it by name (GET /api/graphql?id=articles) or by hash (Apollo's persisted queries), and only\n" .
                                               "persisted queries are cached (graphql.ini ResponseCache). Nothing is written without --apply.\n" .
                                               "\n" .
                                               "./console exp:graphql:persist queries/                  check every .graphql file, store nothing\n" .
                                               "./console exp:graphql:persist queries/ --apply          store them\n" .
                                               "./console exp:graphql:persist menu.graphql --name=main-menu --apply\n" .
                                               "./console exp:graphql:persist --list [--format=json]\n" .
                                               "./console exp:graphql:persist --remove=main-menu --apply\n" .
                                               "\n" .
                                               "Exit code: 0 fine; 1 a query does not fit the schema (nothing is stored then); 3 the library is\n" .
                                               "missing; 2 wrong options.",
                              'use-session' => false,
                              'use-modules' => true,
                              'use-extensions' => true ) );
        $options = $this->startup( '[name:][list][remove:][apply][format:]', '[path]',
                                   array( 'name' => 'the name of the query (one file only); default: the file name without .graphql',
                                          'list' => 'list the persisted queries',
                                          'remove' => 'remove the persisted query of this name or SHA-256 (and its names)',
                                          'apply' => 'write; without it nothing is changed',
                                          'format' => 'text (the default) or json (with --list)' ) );
        $format = isset( $options['format'] ) && $options['format'] ? (string)$options['format'] : 'text';
        if ( $format !== 'text' && $format !== 'json' )
            $this->usage( "--format must be text or json: $format" );
        $apply = !empty( $options['apply'] );
        $store = \expGraphQLPersistedQueries::fromSettings();
        $out = $this->cli();

        if ( !empty( $options['list'] ) )
        {
            $all = array_values( $store->all() );
            if ( $format === 'json' )
                $out->output( json_encode( array( 'schema' => self::SCHEMA, 'directory' => $store->directory(), 'queries' => $all ),
                                           JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
            else
            {
                foreach ( $all as $query )
                    $out->output( sprintf( '%s  %-30s %6d bytes  %s', $query['hash'], $query['names'] ? implode( ', ', $query['names'] ) : '-',
                                           $query['bytes'], $query['source'] ) );
                $out->output( 'PASS ' . count( $all ) . ' persisted queries in ' . $store->directory() );
            }
            $this->shutdown( self::EXIT_OK );
        }

        if ( isset( $options['remove'] ) && $options['remove'] !== false && $options['remove'] !== '' )
        {
            $id = (string)$options['remove'];
            if ( $store->find( $id ) === null )
                $this->usage( "no persisted query $id in the store" );
            if ( !$apply )
                $out->output( "DRY RUN would remove $id (run again with --apply)" );
            else if ( !$store->remove( $id ) )
            {
                $out->error( "FAIL $id is shipped in a directory of PersistedQueryDirectoryList[], not in the store: remove the file" );
                $this->shutdown( self::EXIT_ERRORS );
            }
            else
                $out->output( "PASS removed $id" );
            $this->shutdown( self::EXIT_OK );
        }

        $path = isset( $options['arguments'][0] ) ? rtrim( (string)$options['arguments'][0], '/' ) : '';
        if ( $path === '' )
            $this->usage( 'give a .graphql file or a directory of them, or --list, or --remove' );
        if ( is_dir( $path ) )
            $files = (array)glob( $path . '/*.graphql' );
        else if ( is_file( $path ) )
            $files = array( $path );
        else
            $this->usage( "no file or directory $path" );
        sort( $files );
        if ( !$files )
            $this->usage( "no .graphql file in $path" );
        $name = isset( $options['name'] ) && $options['name'] !== false && $options['name'] !== '' ? (string)$options['name'] : null;
        if ( $name !== null && count( $files ) !== 1 )
            $this->usage( '--name names one file only' );
        if ( !\expGraphQL::loadLibrary() )
        {
            $this->cli()->error( 'FAIL webonyx/graphql-php is not installed (composer.json, or graphql.ini [GraphQLSettings] LibraryPath)' );
            $this->shutdown( self::EXIT_UNAVAILABLE );
        }
        $schema = \expGraphQLSchemaBuilder::fromSettings()->schema();
        $checked = array();
        $failed = 0;
        foreach ( $files as $file )
        {
            $queryName = $name !== null ? $name : basename( $file, '.graphql' );
            $query = (string)file_get_contents( $file );
            $problems = self::problems( $schema, $query );
            if ( !\expGraphQLPersistedQueries::isName( $queryName ) )
                $problems[] = "not a name for a persisted query: $queryName (letters, digits, _ - . /)";
            if ( $problems )
            {
                $failed++;
                $out->error( "FAIL $file: " . implode( '; ', $problems ) );
                continue;
            }
            $checked[] = array( $file, $queryName, $query );
        }
        if ( $failed )
        {
            $out->error( "FAIL $failed of " . count( $files ) . ' queries do not fit the schema; nothing stored' );
            $this->shutdown( self::EXIT_ERRORS );
        }
        foreach ( $checked as $item )
        {
            $hash = \expGraphQLPersistedQueries::hash( $item[2] );
            if ( $apply )
                $store->store( $item[2], $item[1] );
            $out->output( ( $apply ? 'PASS stored ' : 'DRY RUN would store ' ) . $hash . '  ' . $item[1] . '  (' . $item[0] . ')' );
        }
        if ( !$apply )
            $out->output( 'DRY RUN ' . count( $checked ) . ' queries fit the schema; run again with --apply to store them in ' . $store->directory() );
        $this->shutdown( self::EXIT_OK );
    }

    /**
     * @param \GraphQL\Type\Schema $schema
     * @param string $query
     * @return string[] why the query cannot be stored (syntax, schema)
     */
    public static function problems( $schema, $query )
    {
        if ( trim( $query ) === '' )
            return array( 'the file is empty' );
        try
        {
            $document = \GraphQL\Language\Parser::parse( $query );
        }
        catch ( \GraphQL\Error\SyntaxError $e )
        {
            return array( $e->getMessage() );
        }
        $problems = array();
        foreach ( \GraphQL\Validator\DocumentValidator::validate( $schema, $document ) as $error )
            $problems[] = $error->getMessage();
        return $problems;
    }

    /**
     * @param string $message
     */
    protected function usage( $message )
    {
        $this->cli()->error( "FAIL $message" );
        $this->shutdown( self::EXIT_USAGE );
    }
}

}
?>
