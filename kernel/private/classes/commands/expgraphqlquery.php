<?php
/**
 * @description Run a GraphQL query from the console, as a user of the installation (anonymous unless --as names one), and print the answer
 * @alias exp:graphql:query
 *
 * File containing the exp:graphql:query command (bin/php/expgraphqlquery.php); guide: doc/eng-us/features/6.0/graphql.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expgraphqlquery extends \Exponential\Runnable\Command
{
    const EXIT_OK = 0;
    const EXIT_ERRORS = 1;
    const EXIT_USAGE = 2;
    const EXIT_UNAVAILABLE = 3;

    public function run()
    {
        $this->script( array( 'description' => "Run a GraphQL query and print the answer\n" .
                                               "The query is the argument, or a file of it (a path ending in .graphql or .gql, or - for standard\n" .
                                               "input). It runs in the siteaccess of --siteaccess with the rights of the user of --as (a login),\n" .
                                               "else as the anonymous user; the console may always read the schema (introspection). The API need\n" .
                                               "not be switched on.\n" .
                                               "\n" .
                                               "./console exp:graphql:query '{ node(id: 2) { name children { totalCount } } }'\n" .
                                               "./console exp:graphql:query queries/menu.graphql --variables='{\"depth\": 2}' --as=admin\n" .
                                               "./console exp:graphql:query '{ siteaccess { name schemaHash } }' --format=table\n" .
                                               "./console exp:graphql:query --id=articles     a persisted query, by its name or its SHA-256\n" .
                                               "\n" .
                                               "Exit code: 0 an answer without errors; 1 an answer with errors; 3 the library is missing;\n" .
                                               "2 wrong options (no query, variables not JSON, an unknown user).",
                              'use-session' => false,
                              'use-modules' => true,
                              'use-extensions' => true ) );
        $options = $this->startup( '[variables:][operation:][as:][format:][id:]', '[query]',
                                   array( 'variables' => 'the variables, a JSON object', 'operation' => 'the operation to run when the query has several',
                                          'as' => 'run as this user (a login); default: anonymous',
                                          'format' => 'json (the default) or table (one line per value: its path and the value)',
                                          'id' => 'run the persisted query of this name or SHA-256 instead of a query text' ) );
        $format = isset( $options['format'] ) && $options['format'] ? (string)$options['format'] : 'json';
        if ( $format !== 'json' && $format !== 'table' )
            $this->usage( "--format must be json or table: $format" );
        $argument = isset( $options['arguments'][0] ) ? (string)$options['arguments'][0] : '';
        if ( $argument === '-' )
            $query = (string)stream_get_contents( STDIN );
        else if ( preg_match( '/\.(graphql|gql)$/i', $argument ) )
        {
            if ( !is_file( $argument ) )
                $this->usage( "no file $argument" );
            $query = (string)file_get_contents( $argument );
        }
        else
            $query = $argument;
        $id = isset( $options['id'] ) && $options['id'] !== false && $options['id'] !== '' ? (string)$options['id'] : null;
        if ( trim( $query ) === '' && $id === null )
            $this->usage( 'give the query (or a .graphql file, or - for standard input, or --id)' );
        $variables = null;
        if ( isset( $options['variables'] ) && $options['variables'] !== '' && $options['variables'] !== false )
        {
            $variables = json_decode( (string)$options['variables'], true );
            if ( !is_array( $variables ) )
                $this->usage( '--variables is not a JSON object' );
        }
        if ( !\expGraphQL::loadLibrary() )
        {
            $this->cli()->error( 'FAIL webonyx/graphql-php is not installed (composer.json, or graphql.ini [GraphQLSettings] LibraryPath)' );
            $this->shutdown( self::EXIT_UNAVAILABLE );
        }
        if ( !empty( $options['as'] ) )
        {
            $user = \eZUser::fetchByName( (string)$options['as'] );
            if ( !$user instanceof \eZUser )
                $this->usage( 'no user with the login ' . $options['as'] );
            \eZUser::setCurrentlyLoggedInUser( $user, (int)$user->attribute( 'contentobject_id' ), \eZUser::NO_SESSION_REGENERATE );
        }
        $user = \eZUser::currentUser();
        $builder = \expGraphQLSchemaBuilder::fromSettings();
        $context = new \expGraphQLContext( $user, \expGraphQLContext::AUTH_CONSOLE, new \expGraphQLRepository( $builder->catalogue() ),
                                           (string)\expGraphQLContext::requestBaseUrl() );
        $answer = ( new \expGraphQLServer( $builder ) )->run( array( 'query' => $id === null ? $query : '', 'variables' => $variables,
                                                                      'operationName' => !empty( $options['operation'] ) ? (string)$options['operation'] : null,
                                                                      'id' => $id ), $context )['answer'];
        if ( $format === 'json' )
            fwrite( STDOUT, json_encode( $answer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE ) . "\n" );
        else
        {
            foreach ( self::rows( $answer ) as $row )
                fwrite( STDOUT, $row[0] . "\t" . $row[1] . "\n" );
        }
        $this->shutdown( empty( $answer['errors'] ) ? self::EXIT_OK : self::EXIT_ERRORS );
    }

    /**
     * @param string $message
     */
    protected function usage( $message )
    {
        $this->cli()->error( "FAIL $message" );
        $this->shutdown( self::EXIT_USAGE );
    }

    /**
     * The answer as rows: the path of every value (data.node.children.nodes.0.name) and the value.
     *
     * @param mixed $value
     * @param string $path
     * @return array[]
     */
    public static function rows( $value, $path = '' )
    {
        if ( !is_array( $value ) )
            return array( array( $path, $value === null ? 'null' : ( is_bool( $value ) ? ( $value ? 'true' : 'false' ) : (string)$value ) ) );
        if ( !$value )
            return array( array( $path, '[]' ) );
        $rows = array();
        foreach ( $value as $key => $item )
            $rows = array_merge( $rows, self::rows( $item, $path === '' ? (string)$key : $path . '.' . $key ) );
        return $rows;
    }
}

}
