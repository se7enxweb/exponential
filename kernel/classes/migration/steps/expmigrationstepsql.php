<?php
/**
 * File containing the expMigrationStepSql class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "sql": statements of the engine the installation runs on, written in the step or in files next to
 * the migration. It is also what an SQL migration file (YYYYMMDDHHMMSS_name.sql, .<engine>.sql, .mongodb.json) runs
 * (expMigrationSql).
 *
 *   - type: sql
 *     sql: |                          for every SQL engine (MySQL/MariaDB, PostgreSQL, SQLite, Oracle)
 *       UPDATE ezcontentclass SET always_available = 1 WHERE identifier = 'news';
 *     mysql: ...                      the statements of one engine win over sql: (postgresql, sqlite, oracle too)
 *     mongodb:                        MongoDB: commands (see below)
 *       - { update: ezcontentclass, filter: { identifier: news }, set: { always_available: 1 } }
 *     missing: fail                   fail (the default) or skip: what happens on an engine with no statements
 *
 *   - type: sql
 *     file: sql/news_index.sql        a file next to the migration: the engine's own file wins
 *                                     (sql/news_index.mysql.sql, .pgsql.sql or .postgresql.sql, .sqlite.sql,
 *                                     .oracle.sql, .mongodb.json), else the portable one (SQL engines only)
 *     part: up                        up (the default) or down: the part of the file after a line "-- down"
 *                                     (in a .mongodb.json file the list under "down")
 *
 * Statements end with ";" at the end of a line; lines starting with "--" are comments. Placeholders (${ref.name})
 * in sql: and the engine keys are replaced as they are (not quoted): values go into rows with the step type "data".
 * The text of a file is never changed.
 *
 * MongoDB commands, each a map with one command key:
 *   { insert: <collection>, documents: [ {...}, ... ] }                    the counter of an id field is used
 *   { update: <collection>, filter: {...}, set: {...}, unset: [ field ] }  every matching document
 *   { delete: <collection>, filter: {...} }
 *   { create_collection: <collection> }   { drop_collection: <collection> }
 *   { create_index: <collection>, keys: { field: 1 }, name: <name>, unique: false }
 *   { drop_index: <collection>, name: <name> }
 *
 * MySQL/MariaDB and Oracle commit a schema change (CREATE, ALTER, DROP, RENAME, TRUNCATE) at once: when a later
 * statement of the migration fails, it stays. The step says so in its lines, and the runner in its message. PostgreSQL
 * and SQLite undo it with the rest; MongoDB has no transactions. A migration with "transactional: no" in its header
 * (or transactional: false in a YAML document) runs without a transaction (CREATE INDEX CONCURRENTLY on PostgreSQL).
 *
 * The step cannot undo itself: a migration with it needs a down: (a "-- down" part of its file) or a rollback to its
 * caption.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepSql extends expMigrationStepTypeBase
{
    /** The keys of the engines' own statements. */
    const ENGINE_KEYS = array( 'mysql', 'postgresql', 'sqlite', 'oracle', 'mongodb' );

    /** The MongoDB commands and their keys. */
    const MONGO_COMMANDS = array(
        'insert' => array( 'documents' ),
        'update' => array( 'filter', 'set', 'unset' ),
        'delete' => array( 'filter' ),
        'create_collection' => array(),
        'drop_collection' => array(),
        'create_index' => array( 'keys', 'name', 'unique' ),
        'drop_index' => array( 'name' ),
    );

    /** @return string */
    public function name()
    {
        return 'sql';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Runs statements of the engine the installation runs on, written in the step or in files next to the migration',
            'keys' => array(
                'sql' => array( 'type' => 'string', 'description' => 'statements for every SQL engine (MySQL/MariaDB, PostgreSQL, SQLite, Oracle)' ),
                'mysql' => array( 'type' => 'string', 'description' => 'statements for MySQL/MariaDB (win over sql)' ),
                'postgresql' => array( 'type' => 'string', 'description' => 'statements for PostgreSQL (win over sql)' ),
                'sqlite' => array( 'type' => 'string', 'description' => 'statements for SQLite (win over sql)' ),
                'oracle' => array( 'type' => 'string', 'description' => 'statements for Oracle (win over sql)' ),
                'mongodb' => array( 'type' => 'list', 'description' => 'commands for MongoDB: insert, update, delete, create_collection, drop_collection, create_index, drop_index' ),
                'file' => array( 'type' => 'string', 'description' => 'a file next to the migration; the engine\'s own file (<name>.<engine>.sql, <name>.mongodb.json) wins over <name>.sql' ),
                'part' => array( 'type' => 'string', 'default' => 'up', 'values' => array( 'up', 'down' ), 'description' => 'the part of the file: up, or down (after a line "-- down")' ),
                'missing' => array( 'type' => 'string', 'default' => 'fail', 'values' => array( 'fail', 'skip' ), 'description' => 'what happens on an engine with no statements: fail or skip' ),
            ),
            'values' => array(
                'statements' => 'how many statements or commands ran',
                'engine' => 'the engine they ran on',
            ),
            'example' => "- type: sql\n  sql: \"UPDATE ezcontentclass SET always_available = 1 WHERE identifier = 'news';\"\n"
                       . "  mongodb:\n    - { update: ezcontentclass, filter: { identifier: news }, set: { always_available: 1 } }\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $errors = array();
        $given = array_values( array_intersect( array_merge( array( 'sql' ), self::ENGINE_KEYS ), array_keys( $step ) ) );
        if ( isset( $step['file'] ) && $given )
            $errors[] = 'file: either a file or statements (' . implode( ', ', $given ) . '), not both';
        if ( !isset( $step['file'] ) && !$given )
            $errors[] = 'sql, an engine key (' . implode( ', ', self::ENGINE_KEYS ) . ') or file: the statements to run';
        if ( isset( $step['part'] ) && $step['part'] !== 'up' && !isset( $step['file'] ) )
            $errors[] = 'part: only with file';
        if ( isset( $step['file'] ) && is_string( $step['file'] ) && !expMigrationReferences::contains( $step['file'] ) && !self::safeFile( $step['file'] ) )
            $errors[] = 'file: a relative path below the migration\'s folder';
        if ( isset( $step['mongodb'] ) && is_array( $step['mongodb'] ) )
            foreach ( self::mongoProblems( $step['mongodb'] ) as $problem )
                $errors[] = "mongodb$problem";
        return $errors;
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $engine = $context->engine();
        try
        {
            $work = $this->work( $step, $context );
        }
        catch ( RuntimeException $e )
        {
            return array( 'sql: ' . $e->getMessage() );
        }
        if ( $work === null )
            return array( "sql: nothing for $engine" . ( $step['missing'] === 'skip' ? ' (skipped)' : ' (fails)' ) );
        $lines = array();
        foreach ( $work['items'] as $item )
            $lines[] = "$engine: " . self::show( $item );
        if ( !$lines )
            $lines[] = "$engine: no statements";
        return $lines;
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        $engine = $context->engine();
        $work = $this->work( $step, $context );
        if ( $work === null )
        {
            if ( $step['missing'] === 'skip' )
                return self::result( array( "sql: nothing for $engine, skipped" ), array( 'statements' => 0, 'engine' => $engine ) );
            throw new RuntimeException( "no statements for $engine" . ( isset( $step['file'] ) ? ' (no file for it next to ' . $step['file'] . ')' : '' ) . '; missing: skip lets the step pass' );
        }
        $db = $context->db();
        $lines = array();
        if ( $engine === 'mongodb' )
        {
            foreach ( $work['items'] as $command )
                $lines[] = self::runMongo( $db, $command );
        }
        else
        {
            $lines = self::runStatements( $context, $work['items'] );
        }
        expKernelTableSchema::reset();
        return self::result( $lines, array( 'statements' => count( $work['items'] ), 'engine' => $engine ) );
    }

    /**
     * What runs on the context's engine: array( 'items' => statements or MongoDB commands ), or null when the step has
     * nothing for it.
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return array|null
     * @throws RuntimeException when a file is missing or broken
     */
    protected function work( array $step, $context )
    {
        $engine = $context->engine();
        if ( isset( $step['file'] ) )
        {
            $dir = $context->migrationFile() !== null ? dirname( $context->migrationFile() ) : $context->root();
            $file = self::fileFor( $dir . '/' . $step['file'], $engine );
            if ( $file === null )
                return null;
            $parts = self::readFile( $file );
            return array( 'items' => $parts[$step['part'] === 'down' ? 'down' : 'up'] ?: array(), 'file' => $file );
        }
        if ( $engine === 'mongodb' )
            return isset( $step['mongodb'] ) ? array( 'items' => array_values( (array)$step['mongodb'] ) ) : null;
        if ( isset( $step[$engine] ) && in_array( $engine, expMigrationDefinition::SQL_ENGINES, true ) )
            return array( 'items' => expUpgradeCatalog::statements( (string)$step[$engine] ) );
        if ( isset( $step['sql'] ) && in_array( $engine, expMigrationDefinition::SQL_ENGINES, true ) )
            return array( 'items' => expUpgradeCatalog::statements( (string)$step['sql'] ) );
        return null;
    }

    /**
     * The file of an engine for a file name: <name>.<engine>.sql (and <name>.pgsql.sql for PostgreSQL),
     * <name>.mongodb.json for MongoDB, else <name>.sql on the SQL engines. Null when there is none.
     *
     * @param string $path the file as named (<name>.sql, or one engine's file)
     * @param string $engine
     * @return string|null
     */
    public static function fileFor( $path, $engine )
    {
        $base = preg_replace( '/(\.(mysql|postgresql|pgsql|sqlite|oracle|mongodb))?\.(sql|json)$/', '', $path );
        $candidates = $engine === 'mongodb' ? array( "$base.mongodb.json" )
                                            : array( "$base.$engine.sql" );
        if ( $engine === 'postgresql' )
            $candidates[] = "$base.pgsql.sql";
        if ( in_array( $engine, expMigrationDefinition::SQL_ENGINES, true ) )
            $candidates[] = "$base.sql";
        foreach ( $candidates as $candidate )
            if ( is_file( $candidate ) )
                return $candidate;
        return null;
    }

    /**
     * The parts of an SQL or MongoDB file: array( 'up' => items, 'down' => items or null ).
     *
     * @param string $file
     * @return array
     * @throws RuntimeException
     */
    public static function readFile( $file )
    {
        $text = @file_get_contents( $file );
        if ( $text === false )
            throw new RuntimeException( basename( $file ) . ' cannot be read' );
        $errors = array();
        $parts = substr( $file, -5 ) === '.json' ? self::parseMongoFile( $text, $errors ) : self::parseSqlFile( $text );
        if ( $errors )
            throw new RuntimeException( basename( $file ) . ': ' . implode( '; ', $errors ) );
        return $parts;
    }

    /**
     * The parts of SQL text: the statements before a line "-- down" and after it (null without). Pure.
     *
     * @param string $text
     * @return array up => string[], down => string[]|null
     */
    public static function parseSqlFile( $text )
    {
        $parts = preg_split( '/^[ \t]*--[ \t]*down[ \t]*:?[ \t]*$/mi', (string)$text, 2 );
        return array( 'up' => expUpgradeCatalog::statements( $parts[0] ),
                      'down' => count( $parts ) > 1 ? expUpgradeCatalog::statements( $parts[1] ) : null );
    }

    /**
     * The parts of a .mongodb.json file: a list of commands (up), or { "up": [...], "down": [...] }. Pure.
     *
     * @param string $text
     * @param string[] $errors
     * @return array up => array[], down => array[]|null
     */
    public static function parseMongoFile( $text, array &$errors = array() )
    {
        $data = json_decode( (string)$text, true );
        if ( !is_array( $data ) )
        {
            $errors[] = 'not JSON: a list of commands, or an object with "up" and "down" (' . json_last_error_msg() . ')';
            return array( 'up' => array(), 'down' => null );
        }
        if ( !expMigrationYaml::isList( $data ) || $data === array() )
        {
            foreach ( array_diff( array_keys( $data ), array( 'up', 'down', 'description' ) ) as $key )
                $errors[] = "the unknown key $key (up, down, description)";
            $data += array( 'up' => array(), 'down' => null );
            $up = $data['up'];
            $down = $data['down'];
        }
        else
        {
            $up = $data;
            $down = null;
        }
        foreach ( array( 'up' => $up, 'down' => $down ) as $name => $list )
            if ( $list !== null )
                foreach ( is_array( $list ) ? self::mongoProblems( $list ) : array( ': a list of commands' ) as $problem )
                    $errors[] = $name . $problem;
        return array( 'up' => is_array( $up ) ? array_values( $up ) : array(), 'down' => is_array( $down ) ? array_values( $down ) : null );
    }

    /**
     * The problems of a list of MongoDB commands, each starting with "[<index>]". Pure.
     *
     * @param array $commands
     * @return string[]
     */
    public static function mongoProblems( array $commands )
    {
        $problems = array();
        foreach ( array_values( $commands ) as $i => $command )
        {
            if ( !is_array( $command ) )
            {
                $problems[] = "[$i]: a command is a map ({ insert: <collection>, documents: [...] } ...)";
                continue;
            }
            $names = array_values( array_intersect( array_keys( $command ), array_keys( self::MONGO_COMMANDS ) ) );
            if ( count( $names ) !== 1 )
            {
                $problems[] = "[$i]: one of " . implode( ', ', array_keys( self::MONGO_COMMANDS ) );
                continue;
            }
            $name = $names[0];
            if ( !is_string( $command[$name] ) || !expMigrationStepSchemaChange::safeName( $command[$name] ) )
                $problems[] = "[$i].$name: the name of a collection";
            foreach ( array_diff( array_keys( $command ), array_merge( array( $name ), self::MONGO_COMMANDS[$name] ) ) as $key )
                $problems[] = "[$i]: the unknown key $key of $name (" . implode( ', ', self::MONGO_COMMANDS[$name] ) . ')';
            if ( $name === 'insert' && ( !isset( $command['documents'] ) || !is_array( $command['documents'] ) ) )
                $problems[] = "[$i].documents: a list of documents";
            if ( in_array( $name, array( 'update', 'delete' ), true ) && ( !isset( $command['filter'] ) || !is_array( $command['filter'] ) ) )
                $problems[] = "[$i].filter: a map (an empty one for every document)";
            if ( $name === 'update' && empty( $command['set'] ) && empty( $command['unset'] ) )
                $problems[] = "[$i]: set or unset";
            if ( $name === 'create_index' && ( empty( $command['keys'] ) || !is_array( $command['keys'] ) ) )
                $problems[] = "[$i].keys: a map of fields (1 ascending, -1 descending)";
            if ( in_array( $name, array( 'create_index', 'drop_index' ), true ) && ( !isset( $command['name'] ) || !expMigrationStepSchemaChange::safeName( $command['name'] ) ) )
                $problems[] = "[$i].name: the name of the index";
        }
        return $problems;
    }

    /**
     * Runs SQL statements on the context's database, one by one; a schema change on an engine that commits it at once
     * is said in the lines.
     *
     * @param expMigrationContext $context
     * @param string[] $statements
     * @return string[]
     * @throws Exception when a statement fails
     */
    public static function runStatements( $context, array $statements )
    {
        $db = $context->db();
        $engine = $context->engine();
        $lines = array();
        $kept = false;
        foreach ( $statements as $statement )
        {
            expUpgradeCatalog::execute( $db, $statement );
            $lines[] = 'ran ' . self::show( $statement );
            if ( !$kept && self::isSchemaChange( $statement ) && $context->transactionMode() === expMigrationContext::TRANSACTION_DATA
                 && $db->transactionCounter() > 0 )
            {
                $lines[] = "$engine committed the schema change at once: a failure later in this migration does not undo it";
                $kept = true;
            }
        }
        return $lines;
    }

    /**
     * Whether a statement changes the schema (and is committed at once by MySQL/MariaDB and Oracle). Pure.
     *
     * @param string $statement
     * @return bool
     */
    public static function isSchemaChange( $statement )
    {
        return (bool)preg_match( '/^\s*(CREATE|ALTER|DROP|RENAME|TRUNCATE|COMMENT\s+ON|GRANT|REVOKE)\b/i', (string)$statement );
    }

    /**
     * Runs one MongoDB command through the kernel's driver.
     *
     * @param expMongoDB $db
     * @param array $command
     * @return string what was done
     * @throws RuntimeException
     */
    public static function runMongo( $db, array $command )
    {
        $problems = self::mongoProblems( array( $command ) );
        if ( $problems )
            throw new RuntimeException( 'mongodb' . implode( '; ', $problems ) );
        $name = key( array_intersect_key( $command, self::MONGO_COMMANDS ) );
        $table = (string)$command[$name];
        $collection = $db->getClient()->selectCollection( $db->DB, $table );
        switch ( $name )
        {
            case 'insert':
                foreach ( $command['documents'] as $document )
                    if ( !$db->insert( $table, (array)$document ) )
                        throw new RuntimeException( "insert into $table failed" );
                return 'inserted ' . count( $command['documents'] ) . " into $table";
            case 'update':
                $update = array();
                if ( !empty( $command['set'] ) )
                    $update['$set'] = (array)$command['set'];
                if ( !empty( $command['unset'] ) )
                    $update['$unset'] = array_fill_keys( array_map( 'strval', (array)$command['unset'] ), '' );
                $result = $collection->updateMany( (array)$command['filter'], $update );
                return "updated {$result->getModifiedCount()} in $table";
            case 'delete':
                $result = $collection->deleteMany( (array)$command['filter'] );
                return "deleted {$result->getDeletedCount()} from $table";
            case 'create_collection':
                if ( in_array( $table, $db->listCollectionNames(), true ) )
                    return "the collection $table exists already";
                $db->getClient()->selectDatabase( $db->DB )->createCollection( $table );
                return "created the collection $table";
            case 'drop_collection':
                $collection->drop();
                return "dropped the collection $table";
            case 'create_index':
                $options = array( 'name' => (string)$command['name'] );
                if ( !empty( $command['unique'] ) )
                    $options['unique'] = true;
                $collection->createIndex( array_map( 'intval', (array)$command['keys'] ), $options );
                return "created the index {$command['name']} on $table";
            case 'drop_index':
                foreach ( $collection->listIndexes() as $index )
                {
                    if ( $index->getName() === (string)$command['name'] )
                    {
                        $collection->dropIndex( (string)$command['name'] );
                        return "dropped the index {$command['name']} of $table";
                    }
                }
                return "no index {$command['name']} on $table";
        }
        throw new RuntimeException( "unknown command $name" );
    }

    /**
     * A statement or command on one line, for the plan and the log. Pure.
     *
     * @param string|array $item
     * @return string
     */
    public static function show( $item )
    {
        $text = is_array( $item ) ? json_encode( $item, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) : trim( preg_replace( '/\s+/', ' ', (string)$item ) );
        return mb_strlen( $text ) > 200 ? mb_substr( $text, 0, 199 ) . '…' : $text;
    }

    /**
     * Whether a file name stays below the migration's folder. Pure.
     *
     * @param string $file
     * @return bool
     */
    public static function safeFile( $file )
    {
        $file = (string)$file;
        return $file !== '' && $file[0] !== '/' && !preg_match( '#(^|/)\.\.(/|$)#', $file ) && strpos( $file, "\0" ) === false;
    }
}
