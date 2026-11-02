<?php
/**
 * File containing the expMigrationGraphQLExtension class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * The schema extension of the migrations (graphql.ini SchemaExtensionList[]; doc/eng-us/features/6.0/graphql.md,
 * "Migrations"): read-only root queries, answered from expMigrationService as the REST routes /api/migration/v1/*:
 *
 *   migrationStatus: MigrationStatus                 the counts, environment, engine, lock, warnings and errors
 *   migrations(state, source): [Migration!]!         every migration found, with its state
 *   migrationRuns(limit, offset): [MigrationRun!]!   the latest runs, newest first
 *   migrationRun(id): MigrationRun                   one run and the migrations it wrote
 *
 * Each answers only for a user with the policy migration/read (null or an empty list otherwise). No mutation: a
 * migration runs from the console or Setup > Migrations, where the administration asks "Verify it's you".
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationGraphQLExtension implements expGraphQLSchemaExtension
{
    /** @var expMigrationService */
    protected $service;

    /** @var array the status of the request asked last: requestId => status (one report per request) */
    protected $status = array();

    /**
     * @param expMigrationService|null $service null: the kernel's
     */
    public function __construct( $service = null )
    {
        $this->service = $service instanceof expMigrationService ? $service : new expMigrationService();
    }

    public function isAvailable()
    {
        return class_exists( 'expMigrationService' );
    }

    public function name()
    {
        return 'migration/1';
    }

    public function datatypes()
    {
        return array();
    }

    public function fieldMapping( array $attribute, expGraphQLTypes $types )
    {
        return null;
    }

    public function locationFields( expGraphQLTypes $types )
    {
        return array();
    }

    /**
     * @param expGraphQLContext $ctx
     * @return array|null the status, once per request; null without migration/read
     */
    public function statusOfRequest( expGraphQLContext $ctx )
    {
        if ( !$this->service->mayRead( $ctx->user ) )
            return null;
        $key = (string)$ctx->requestId;
        if ( !isset( $this->status[$key] ) )
            $this->status = array( $key => $this->service->status() );
        return $this->status[$key];
    }

    public function queryFields( expGraphQLTypes $types )
    {
        $service = $this->service;
        $extension = $this;
        $statusFor = function ( expGraphQLContext $ctx ) use ( $extension ) { return $extension->statusOfRequest( $ctx ); };
        return array(
            'migrationStatus' => array(
                'type' => $this->statusType( $types ),
                'description' => 'The state of the migrations of the installation (policy migration/read; null without it).',
                'resolve' => function ( $root, $args, expGraphQLContext $ctx ) use ( $statusFor ) { return $statusFor( $ctx ); },
            ),
            'migrations' => array(
                'type' => Type::nonNull( Type::listOf( Type::nonNull( $this->migrationType( $types ) ) ) ),
                'description' => 'The migrations found for the environment, the upgrade steps of the kernel first, with their state (policy migration/read).',
                'args' => array( 'state' => array( 'type' => Type::string(), 'description' => 'Only those in this state: done, pending, not-needed, failed, unknown.' ),
                                 'source' => array( 'type' => Type::string(), 'description' => 'Only those of this source: kernel, site, or an extension.' ) ),
                'resolve' => function ( $root, $args, expGraphQLContext $ctx ) use ( $statusFor ) {
                    $status = $statusFor( $ctx );
                    if ( $status === null )
                        return array();
                    return array_values( array_filter( $status['migrations'], function ( array $m ) use ( $args ) {
                        return ( !isset( $args['state'] ) || $m['state'] === $args['state'] ) && ( !isset( $args['source'] ) || $m['source'] === $args['source'] );
                    } ) );
                },
            ),
            'migrationRuns' => array(
                'type' => Type::nonNull( Type::listOf( Type::nonNull( $this->runType( $types ) ) ) ),
                'description' => 'The latest runs of migrations, newest first (policy migration/read).',
                'args' => array( 'limit' => array( 'type' => Type::int(), 'defaultValue' => 20, 'description' => 'At most this many (1 to 100).' ),
                                 'offset' => array( 'type' => Type::int(), 'defaultValue' => 0 ) ),
                'complexity' => function ( $childrenComplexity, $args ) { return 1 + $childrenComplexity * expMigrationService::page( isset( $args['limit'] ) ? $args['limit'] : 20, 0 )[0]; },
                'resolve' => function ( $root, $args, expGraphQLContext $ctx ) use ( $service ) {
                    return $service->mayRead( $ctx->user ) ? $service->runs( $args['limit'], $args['offset'] ) : array();
                },
            ),
            'migrationRun' => array(
                'type' => $this->runType( $types ),
                'description' => 'One run of migrations and the migrations it wrote (policy migration/read; null without it or when there is no such run).',
                'args' => array( 'id' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'The id of the run.' ) ),
                'resolve' => function ( $root, $args, expGraphQLContext $ctx ) use ( $service ) {
                    return $service->mayRead( $ctx->user ) ? $service->run( (string)$args['id'] ) : null;
                },
            ),
        );
    }

    /**
     * @param expGraphQLTypes $types
     * @return ObjectType
     */
    public function statusType( expGraphQLTypes $types )
    {
        return $types->register( 'MigrationStatus', function () {
            $value = function ( $key ) { return expMigrationGraphQLExtension::valueOf( $key ); };
            $summary = function ( $key ) { return function ( array $s ) use ( $key ) { return $s['summary'][$key]; }; };
            $texts = Type::nonNull( Type::listOf( Type::nonNull( Type::string() ) ) );
            return new ObjectType( array(
                'name' => 'MigrationStatus',
                'description' => 'The state of the migrations of the installation, as ./console exp:migration:status tells it.',
                'fields' => function () use ( $value, $summary, $texts ) {
                    return array(
                        'pending' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'Migrations that wait to be applied.', 'resolve' => $summary( 'pending' ) ),
                        'failed' => array( 'type' => Type::nonNull( Type::int() ), 'resolve' => $summary( 'failed' ) ),
                        'orphans' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'Records whose migration file is gone.', 'resolve' => $summary( 'orphans' ) ),
                        'total' => array( 'type' => Type::nonNull( Type::int() ), 'resolve' => $summary( 'total' ) ),
                        'ready' => array( 'type' => Type::nonNull( Type::boolean() ), 'description' => 'The tables of the migrations exist.', 'resolve' => $summary( 'ready' ) ),
                        'environment' => array( 'type' => Type::string(), 'resolve' => $value( 'environment' ) ),
                        'engine' => array( 'type' => Type::string(), 'description' => 'The database engine: mysql, postgresql, sqlite, oracle, mongodb.', 'resolve' => $value( 'engine' ) ),
                        'lock' => array( 'type' => Type::string(), 'description' => 'Who holds the lock of the installation (empty: nobody).', 'resolve' => $value( 'lock' ) ),
                        'warnings' => array( 'type' => $texts, 'resolve' => $value( 'warnings' ) ),
                        'errors' => array( 'type' => $texts, 'resolve' => $value( 'errors' ) ),
                    );
                },
            ) );
        } );
    }

    /**
     * @param expGraphQLTypes $types
     * @return ObjectType
     */
    public function migrationType( expGraphQLTypes $types )
    {
        return $types->register( 'Migration', function () {
            $value = function ( $key ) { return expMigrationGraphQLExtension::valueOf( $key ); };
            $record = function ( $key ) { return function ( array $m ) use ( $key ) { return isset( $m['record'][$key] ) ? $m['record'][$key] : null; }; };
            return new ObjectType( array(
                'name' => 'Migration',
                'description' => 'A migration (or an upgrade step of the kernel) and its state.',
                'fields' => array(
                    'id' => array( 'type' => Type::nonNull( Type::string() ), 'resolve' => $value( 'id' ) ),
                    'source' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'kernel, site, or the extension it comes from.', 'resolve' => $value( 'source' ) ),
                    'description' => array( 'type' => Type::string(), 'resolve' => $value( 'description' ) ),
                    'stage' => array( 'type' => Type::string(), 'resolve' => $value( 'stage' ) ),
                    'file' => array( 'type' => Type::string(), 'description' => 'The file, relative to the root of the installation.', 'resolve' => $value( 'file' ) ),
                    'state' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'done, pending, not-needed, failed or unknown.', 'resolve' => $value( 'state' ) ),
                    'detail' => array( 'type' => Type::string(), 'resolve' => $value( 'detail' ) ),
                    'changed' => array( 'type' => Type::nonNull( Type::boolean() ), 'description' => 'The file changed after it was applied.', 'resolve' => $value( 'changed' ) ),
                    'runId' => array( 'type' => Type::string(), 'description' => 'The run that recorded it last.', 'resolve' => $record( 'run_id' ) ),
                    'finishedAt' => array( 'type' => Type::string(), 'description' => 'When that run finished it (ISO 8601).', 'resolve' => $record( 'finished_at' ) ),
                ),
            ) );
        } );
    }

    /**
     * @param expGraphQLTypes $types
     * @return ObjectType
     */
    public function runType( expGraphQLTypes $types )
    {
        $extension = $this;
        return $types->register( 'MigrationRun', function () use ( $types, $extension ) {
            $value = function ( $key ) { return expMigrationGraphQLExtension::valueOf( $key ); };
            return new ObjectType( array(
                'name' => 'MigrationRun',
                'description' => 'A run of migrations (or a rollback).',
                'fields' => function () use ( $types, $extension, $value ) {
                    return array(
                        'id' => array( 'type' => Type::nonNull( Type::string() ), 'resolve' => $value( 'run_id' ) ),
                        'state' => array( 'type' => Type::nonNull( Type::string() ), 'resolve' => $value( 'state' ) ),
                        'startedAt' => array( 'type' => Type::string(), 'resolve' => $value( 'started_at' ) ),
                        'finishedAt' => array( 'type' => Type::string(), 'resolve' => $value( 'finished_at' ) ),
                        'runBy' => array( 'type' => Type::string(), 'description' => 'Who ran it: cli or web, and the system user.', 'resolve' => $value( 'run_by' ) ),
                        'environment' => array( 'type' => Type::string(), 'resolve' => $value( 'environment' ) ),
                        'captionBefore' => array( 'type' => Type::string(), 'description' => 'The caption taken before the run.', 'resolve' => $value( 'caption_pre' ) ),
                        'captionAfter' => array( 'type' => Type::string(), 'resolve' => $value( 'caption_post' ) ),
                        'commitId' => array( 'type' => Type::string(), 'resolve' => $value( 'commit_id' ) ),
                        'tagName' => array( 'type' => Type::string(), 'resolve' => $value( 'tag_name' ) ),
                        'pushState' => array( 'type' => Type::string(), 'resolve' => $value( 'push_state' ) ),
                        'message' => array( 'type' => Type::string(), 'resolve' => $value( 'message' ) ),
                        'migrations' => array(
                            'type' => Type::nonNull( Type::listOf( Type::nonNull( $extension->runEntryType( $types ) ) ) ),
                            'description' => 'The migrations the run wrote (only when one run is asked for: migrationRun).',
                            'resolve' => function ( array $run ) { return isset( $run['migrations'] ) ? $run['migrations'] : array(); },
                        ),
                    );
                },
            ) );
        } );
    }

    /**
     * @param expGraphQLTypes $types
     * @return ObjectType
     */
    public function runEntryType( expGraphQLTypes $types )
    {
        return $types->register( 'MigrationRunEntry', function () {
            $value = function ( $key ) { return expMigrationGraphQLExtension::valueOf( $key ); };
            return new ObjectType( array(
                'name' => 'MigrationRunEntry',
                'description' => 'A migration as a run recorded it.',
                'fields' => array(
                    'id' => array( 'type' => Type::nonNull( Type::string() ), 'resolve' => $value( 'id' ) ),
                    'source' => array( 'type' => Type::nonNull( Type::string() ), 'resolve' => $value( 'source' ) ),
                    'state' => array( 'type' => Type::nonNull( Type::string() ), 'resolve' => $value( 'state' ) ),
                    'file' => array( 'type' => Type::string(), 'resolve' => $value( 'file' ) ),
                    'startedAt' => array( 'type' => Type::string(), 'resolve' => $value( 'started_at' ) ),
                    'finishedAt' => array( 'type' => Type::string(), 'resolve' => $value( 'finished_at' ) ),
                    'durationMs' => array( 'type' => Type::int(), 'resolve' => $value( 'duration_ms' ) ),
                    'runBy' => array( 'type' => Type::string(), 'resolve' => $value( 'run_by' ) ),
                    'engine' => array( 'type' => Type::string(), 'resolve' => $value( 'engine' ) ),
                    'message' => array( 'type' => Type::string(), 'resolve' => $value( 'message' ) ),
                ),
            ) );
        } );
    }

    /**
     * A resolver of one key of an array. Pure.
     *
     * @param string $key
     * @return callable
     */
    public static function valueOf( $key )
    {
        return function ( array $row ) use ( $key ) { return isset( $row[$key] ) ? $row[$key] : null; };
    }
}
