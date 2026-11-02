<?php
/**
 * File containing the expMigrationStepGraphql class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "graphql": one query or mutation of the installation's GraphQL API, run in process
 * (expGraphQLServer, as ./console exp:graphql:query runs it) as the user the migration runs as. No HTTP request, no
 * token and no network: what the API answers a signed-in console, the step gets.
 *
 *   - type: graphql
 *     persisted: news-teasers            a persisted query by name or SHA-256 (exp:graphql:persist, or a shipped
 *                                        *.graphql of graphql.ini PersistedQueryDirectoryList[]); or
 *     query: |                           the text of a query or mutation; or
 *       { node(id: 2) { name } }
 *     file: graphql/teasers.graphql      a file next to the migration
 *     variables: { parentId: "${ref.news_node}" }
 *     operation: Teasers                 the operation to run when the text has several
 *     allow_errors: false                true: an answer with errors passes when it has data
 *     references: { teasers: data }      data, result (the value of the first root field), errors
 *
 * A mutation needs graphql.ini [GraphQLSettings] MutationsEnabled=enabled, and checks the rights of the run's user on
 * the content it changes, as it does for any client. Its changes are part of the migration: inside its transaction,
 * in the event journal tagged with the migration (the header events:), undone by its down: or a rollback to its
 * caption, never by the step itself. A dry run runs nothing. Without webonyx/graphql-php the step fails and says so.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepGraphql extends expMigrationStepTypeBase
{
    /** @var callable|null function( string $query, array|null $variables, string|null $operation, string|null $id ): array an answer (tests) */
    protected static $executor;

    /**
     * Replaces how a query is run (a test's); null puts the GraphQL server back.
     *
     * @param callable|null $executor
     */
    public static function setExecutor( $executor )
    {
        self::$executor = $executor;
    }

    /** @return string */
    public function name()
    {
        return 'graphql';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Runs a query or mutation of the GraphQL API in process as the run\'s user, and keeps its answer for references',
            'keys' => array(
                'persisted' => array( 'type' => 'string', 'description' => 'a persisted query: its name or its SHA-256' ),
                'query' => array( 'type' => 'string', 'description' => 'the text of the query or mutation' ),
                'file' => array( 'type' => 'string', 'description' => 'a .graphql file next to the migration' ),
                'variables' => array( 'type' => 'map', 'description' => 'the variables, name => value (references allowed)' ),
                'operation' => array( 'type' => 'string', 'description' => 'the operation to run when the text has several' ),
                'allow_errors' => array( 'type' => 'bool', 'default' => false, 'description' => 'true: an answer with errors passes when it has data' ),
            ),
            'values' => array(
                'data' => 'the data of the answer (a map of the root fields)',
                'result' => 'the value of the first root field of the answer',
                'errors' => 'the messages of the errors (a list, empty when there were none)',
            ),
            'example' => "- type: graphql\n  query: |\n    mutation (\$parent: Int!) {\n      createContent(parentId: \$parent, classIdentifier: \"folder\", fields: [ { identifier: \"name\", value: \"News\" } ]) { contentId nodeId }\n    }\n"
                       . "  variables: { parent: 2 }\n  references: { news: result }\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $errors = array();
        $given = array_values( array_intersect( array( 'persisted', 'query', 'file' ), array_keys( $step ) ) );
        if ( count( $given ) !== 1 )
            $errors[] = 'exactly one of persisted, query and file: the query to run' . ( $given ? ' (given: ' . implode( ', ', $given ) . ')' : '' );
        if ( isset( $step['persisted'] ) && is_string( $step['persisted'] ) && !expMigrationReferences::contains( $step['persisted'] )
             && !self::persistedName( $step['persisted'] ) )
            $errors[] = 'persisted: the name of a persisted query (letters, digits, . _ / -) or its SHA-256';
        if ( isset( $step['query'] ) && is_string( $step['query'] ) && trim( $step['query'] ) === '' )
            $errors[] = 'query: the text of a query';
        if ( isset( $step['file'] ) && is_string( $step['file'] ) && !expMigrationReferences::contains( $step['file'] ) )
        {
            if ( !expMigrationStepSql::safeFile( $step['file'] ) )
                $errors[] = 'file: a relative path below the migration\'s folder';
            elseif ( !preg_match( '/\.(graphql|gql)$/i', $step['file'] ) )
                $errors[] = 'file: a .graphql or .gql file';
        }
        if ( isset( $step['operation'] ) && is_string( $step['operation'] ) && !preg_match( '/^[_A-Za-z][_0-9A-Za-z]*$/', $step['operation'] ) )
            $errors[] = 'operation: the name of an operation';
        return $errors;
    }

    /**
     * Whether a text can name a persisted query: a name or a SHA-256 (as expGraphQLPersistedQueries takes them). Pure.
     *
     * @param string $id
     * @return bool
     */
    public static function persistedName( $id )
    {
        return expGraphQLPersistedQueries::isHash( $id ) || expGraphQLPersistedQueries::isName( $id );
    }

    /**
     * Whether a GraphQL text is a mutation (its first operation). Pure, without the library: the plan of a dry run
     * says what it would change.
     *
     * @param string $query
     * @return bool
     */
    public static function isMutation( $query )
    {
        $text = preg_replace( '/#[^\n]*/', '', (string)$query );
        return (bool)preg_match( '/^\s*mutation\b/', (string)$text );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        if ( isset( $step['persisted'] ) )
            $what = 'the persisted query ' . $step['persisted'];
        elseif ( isset( $step['file'] ) )
            $what = 'the query of ' . $step['file'];
        else
            $what = ( self::isMutation( (string)$step['query'] ) ? 'a mutation' : 'a query' );
        $variables = isset( $step['variables'] ) && is_array( $step['variables'] ) && $step['variables'] ? ' with ' . implode( ', ', array_keys( $step['variables'] ) ) : '';
        return array( 'run ' . $what . ( isset( $step['operation'] ) ? ' (' . $step['operation'] . ')' : '' ) . $variables . ' of the GraphQL API (not run in a dry run)' );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        $id = null;
        $query = '';
        if ( isset( $step['persisted'] ) )
        {
            $id = (string)$step['persisted'];
            if ( !self::persistedName( $id ) )
                throw new RuntimeException( "persisted: $id is not the name of a persisted query" );
            $shown = "the persisted query $id";
        }
        elseif ( isset( $step['file'] ) )
        {
            if ( !expMigrationStepSql::safeFile( $step['file'] ) )
                throw new RuntimeException( 'file: a relative path below the migration\'s folder' );
            $dir = $context instanceof expMigrationContext && $context->migrationFile() !== null ? dirname( $context->migrationFile() )
                 : ( $context instanceof expMigrationContext ? $context->root() : '.' );
            $path = $dir . '/' . $step['file'];
            if ( !is_file( $path ) )
                throw new RuntimeException( 'file: ' . $step['file'] . ' is not there' );
            $query = (string)file_get_contents( $path );
            $shown = 'the query of ' . $step['file'];
        }
        else
        {
            $query = (string)$step['query'];
            $shown = self::isMutation( $query ) ? 'a mutation' : 'a query';
        }
        $variables = isset( $step['variables'] ) && is_array( $step['variables'] ) ? $step['variables'] : null;
        $operation = isset( $step['operation'] ) && $step['operation'] !== '' ? (string)$step['operation'] : null;

        $answer = self::execute( $query, $variables, $operation, $id );
        $errors = array();
        foreach ( isset( $answer['errors'] ) ? (array)$answer['errors'] : array() as $error )
            $errors[] = is_array( $error ) && isset( $error['message'] ) ? (string)$error['message'] : (string)json_encode( $error );
        $data = isset( $answer['data'] ) && is_array( $answer['data'] ) ? $answer['data'] : null;
        if ( $errors && ( empty( $step['allow_errors'] ) || $data === null ) )
            throw new RuntimeException( "$shown answered: " . implode( '; ', array_slice( $errors, 0, 3 ) ) . ( count( $errors ) > 3 ? ' (and ' . ( count( $errors ) - 3 ) . ' more)' : '' ) );
        if ( $data === null )
            throw new RuntimeException( "$shown answered no data" );
        $result = $data ? reset( $data ) : null;
        $lines = array( "ran $shown: " . ( $data ? implode( ', ', array_keys( $data ) ) : 'no fields' ) . ( $errors ? ' (' . count( $errors ) . ' error(s) allowed)' : '' ) );
        return self::result( $lines, array( 'data' => $data, 'result' => $result, 'errors' => $errors ) );
    }

    /**
     * Runs a query through the GraphQL server of this siteaccess, as the current user (the run's) and as the console.
     *
     * @param string $query
     * @param array|null $variables
     * @param string|null $operation
     * @param string|null $id a persisted query
     * @return array the answer: data and/or errors
     * @throws RuntimeException when the library is missing
     */
    protected static function execute( $query, $variables, $operation, $id )
    {
        if ( self::$executor !== null )
            return (array)call_user_func( self::$executor, $query, $variables, $operation, $id );
        if ( !expGraphQL::loadLibrary() )
            throw new RuntimeException( 'the GraphQL API cannot run: ' . expGraphQL::libraryProblem() );
        $builder = expGraphQLSchemaBuilder::fromSettings();
        $context = new expGraphQLContext( eZUser::currentUser(), expGraphQLContext::AUTH_CONSOLE, new expGraphQLRepository( $builder->catalogue() ),
                                          (string)expGraphQLContext::requestBaseUrl() );
        $run = ( new expGraphQLServer( $builder ) )->run( array( 'query' => $id === null ? $query : '', 'variables' => $variables,
                                                                  'operationName' => $operation, 'id' => $id ), $context );
        return isset( $run['answer'] ) && is_array( $run['answer'] ) ? $run['answer'] : array( 'errors' => array( array( 'message' => 'no answer' ) ) );
    }
}
