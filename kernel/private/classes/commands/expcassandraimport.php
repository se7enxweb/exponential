<?php
/**
 * @description Import the tables of an Apache Cassandra or ScyllaDB keyspace into the site's database or a data table connection
 * @alias exp:cassandra:import
 *
 * File containing the exp:cassandra:import command (bin/php/expcassandraimport.php); guide:
 * doc/eng-us/features/6.0/cassandra-scylladb-driver.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expcassandraimport extends \Exponential\Runnable\Command
{
    const EXIT_OK = 0;
    const EXIT_FAILED = 1;
    const EXIT_USAGE = 2;

    /** The machine output (--format=json). */
    const SCHEMA = 'exp-cassandra-import/1';

    public function run()
    {
        $this->script( array( 'description' => "Import the tables of an Apache Cassandra or ScyllaDB keyspace into a database of this installation\n" .
                                               "Reads the keyspace through CQL (PHP 8.1 and the library mroosz/php-cassandra), makes the tables in the\n" .
                                               "site's database or in a data table connection (--connection), and copies the rows page by page. The\n" .
                                               "source is a data table connection of the engine cassandra or scylladb (--source), or a server named here.\n" .
                                               "Without --apply it shows the tables it would make and does nothing.\n" .
                                               "\n" .
                                               "./console exp:cassandra:import --source=events --list\n" .
                                               "./console exp:cassandra:import --source=events --prefix=ev_\n" .
                                               "./console exp:cassandra:import --server=cass1,cass2 --keyspace=shop --user=reader --secret=env:CASSANDRA_PASSWORD --apply\n" .
                                               "./console exp:cassandra:import --source=events --tables=orders --connection=archive --mode=replace --apply --format=json\n" .
                                               "\n" .
                                               "Exit code: 0 done (or planned); 1 a table or row failed; 2 wrong options or no connection.",
                              'use-session' => false,
                              'use-modules' => false,
                              'use-extensions' => true ) );
        $options = $this->startup( '[list][apply][dry-run][source:][engine:][server:][port:][keyspace:][user:][secret:][connection:][tables:][prefix:][mode:][format:]', '', array(
            'list' => 'List the tables of the keyspace with their columns, and stop',
            'apply' => 'Make the tables and copy the rows (without it: show the plan only)',
            'dry-run' => 'Show the plan only (the default)',
            'source' => 'The data table connection to read (engine cassandra or scylladb)',
            'engine' => 'Without --source: cassandra (the default) or scylladb',
            'server' => 'Without --source: the node or nodes (host, host:port, a comma list)',
            'port' => 'Without --source: the port (9042)',
            'keyspace' => 'Without --source: the keyspace',
            'user' => 'Without --source: the user',
            'secret' => 'Without --source: the password, as the name of a secret (Setup > Secrets) or env:VARIABLE',
            'connection' => 'The database to import into: site (the default) or the identifier of a data table connection',
            'tables' => 'Only these tables (a comma list)',
            'prefix' => 'Put this in front of every table name (lower case letters, digits, _), e.g. ev_',
            'mode' => 'create (the default: new tables only), append (add rows), replace (delete the rows first)',
            'format' => 'text (the default) or json',
        ) );
        $format = isset( $options['format'] ) && $options['format'] ? (string)$options['format'] : 'text';
        if ( $format !== 'text' && $format !== 'json' )
            $this->fail( "--format must be text or json, not $format", self::EXIT_USAGE );
        $apply = self::applyRequested( $options );
        if ( $apply === null )
            $this->fail( '--apply and --dry-run exclude each other', self::EXIT_USAGE );
        $mode = isset( $options['mode'] ) && $options['mode'] ? (string)$options['mode'] : 'create';
        if ( !in_array( $mode, array( 'create', 'append', 'replace' ), true ) )
            $this->fail( "--mode must be create, append or replace, not $mode", self::EXIT_USAGE );
        $prefix = isset( $options['prefix'] ) ? (string)$options['prefix'] : '';
        if ( $prefix !== '' && !preg_match( '/^[a-z][a-z0-9_]{0,30}$/', $prefix ) )
            $this->fail( "--prefix must be lower case letters, digits and _, starting with a letter: $prefix", self::EXIT_USAGE );
        $tables = isset( $options['tables'] ) && $options['tables'] ? array_values( array_filter( array_map( 'trim', explode( ',', (string)$options['tables'] ) ), 'strlen' ) ) : array();

        try
        {
            $source = self::openSource( $options );
            $reader = new \expCassandraReader( $source );
        }
        catch ( \Throwable $e )
        {
            $this->fail( $e->getMessage(), self::EXIT_USAGE );
            return;
        }

        if ( !empty( $options['list'] ) )
        {
            $list = array();
            foreach ( $reader->tables() as $table )
                $list[$table] = $reader->table( $table );
            if ( $format === 'json' )
                $this->cli()->output( json_encode( array( 'schema' => self::SCHEMA, 'source' => $reader->file(), 'server' => $reader->format(), 'tables' => $list ),
                                                   JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            else
            {
                $this->cli()->output( $reader->file() . ' (' . $reader->format() . '): ' . count( $list ) . ' tables' );
                foreach ( $list as $table => $description )
                {
                    $this->cli()->output( "  $table (key " . implode( ', ', $description['primary'] ) . ')' );
                    foreach ( $description['fields'] as $name => $field )
                        $this->cli()->output( sprintf( '    %-30s %s%s', $name, $field['type'], $field['length'] ? '(' . $field['length'] . ')' : '' ) );
                }
            }
            $this->shutdown( self::EXIT_OK );
            return;
        }

        $connection = isset( $options['connection'] ) && $options['connection'] ? (string)$options['connection'] : \expDataConnections::SITE;
        try
        {
            $db = $connection === \expDataConnections::SITE ? \eZDB::instance() : \expDataConnections::db( $connection );
        }
        catch ( \Throwable $e )
        {
            $this->fail( "The connection $connection cannot be opened: " . $e->getMessage(), self::EXIT_USAGE );
            return;
        }
        $importOptions = array( 'tables' => $tables, 'prefix' => $prefix, 'mode' => $mode );
        if ( !$apply )
        {
            $plan = \expAccessImport::plan( $reader, $db, $importOptions );
            $report = array( 'mode' => 'plan', 'source' => $reader->file(), 'connection' => $connection, 'engine' => $plan['plan']['engine'],
                             'tables' => array(), 'notes' => $plan['notes'] );
            foreach ( $plan['map'] as $from => $map )
            {
                $step = isset( $plan['plan']['tables'][$map['table']] ) ? $plan['plan']['tables'][$map['table']] : array( 'action' => 'none', 'statements' => array() );
                $report['tables'][$from] = array( 'table' => $map['table'], 'action' => $step['action'], 'columns' => $map['columns'], 'statements' => $step['statements'] );
            }
            $this->report( $report, $format );
            $this->shutdown( self::EXIT_OK );
            return;
        }
        $started = microtime( true );
        $result = \expAccessImport::run( $reader, $db, $importOptions );
        $report = array( 'mode' => 'import', 'source' => $reader->file(), 'connection' => $connection, 'seconds' => round( microtime( true ) - $started, 3 ) ) + $result;
        $this->report( $report, $format );
        $this->shutdown( $result['ok'] ? self::EXIT_OK : self::EXIT_FAILED );
    }

    /**
     * The keyspace to read: a data table connection (--source) or a server named by the options.
     *
     * @param array $options
     * @return \expCassandraDB
     * @throws \RuntimeException
     */
    public static function openSource( array $options )
    {
        if ( !empty( $options['source'] ) )
            $db = \expDataConnections::db( (string)$options['source'] );
        else
        {
            if ( empty( $options['server'] ) || empty( $options['keyspace'] ) )
                throw new \RuntimeException( 'Name the source: --source=<connection>, or --server and --keyspace' );
            $engine = !empty( $options['engine'] ) ? strtolower( (string)$options['engine'] ) : 'cassandra';
            if ( !in_array( $engine, array( 'cassandra', 'scylladb', 'scylla' ), true ) )
                throw new \RuntimeException( "--engine must be cassandra or scylladb, not $engine" );
            $db = \expDataConnections::open( array( 'identifier' => 'import_source', 'engine' => $engine, 'server' => (string)$options['server'],
                                                    'port' => isset( $options['port'] ) ? (int)$options['port'] : 0, 'database' => (string)$options['keyspace'],
                                                    'user' => isset( $options['user'] ) ? (string)$options['user'] : '',
                                                    'secret' => isset( $options['secret'] ) ? (string)$options['secret'] : '', 'origin' => 'settings' ) );
        }
        if ( !$db instanceof \expCassandraDB )
            throw new \RuntimeException( 'The source is no Cassandra or ScyllaDB connection (engine ' . \expKernelTableSchema::type( $db ) . ')' );
        return $db;
    }

    /**
     * @param array $report
     * @param string $format
     */
    protected function report( array $report, $format )
    {
        if ( $format === 'json' )
        {
            $this->cli()->output( json_encode( array( 'schema' => self::SCHEMA ) + $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            return;
        }
        $out = $this->cli();
        $out->output( sprintf( '%s %s into %s (%s)%s', $report['mode'] === 'plan' ? 'Plan for' : 'Imported', $report['source'], $report['connection'],
                               $report['engine'], isset( $report['seconds'] ) ? ' in ' . $report['seconds'] . ' s' : '' ) );
        foreach ( $report['tables'] as $from => $table )
        {
            if ( $report['mode'] === 'plan' )
                $out->output( sprintf( '  %-30s -> %-30s %s, %d columns', $from, $table['table'], $table['action'], count( $table['columns'] ) ) );
            else
            {
                $out->output( sprintf( '  %-30s -> %-30s %s, %d read, %d written', $from, $table['table'], $table['action'], $table['read'], $table['written'] ) );
                foreach ( $table['errors'] as $error )
                    $out->output( "      FAIL $error" );
            }
        }
        foreach ( $report['notes'] as $note )
            $out->output( "  note: $note" );
        if ( $report['mode'] === 'plan' )
            $out->output( 'Nothing was written: add --apply to import.' );
        else
            $out->output( $report['ok'] ? 'PASS every table and row was imported' : 'FAIL some tables or rows were not imported (above)' );
    }

    /**
     * @param string $message
     * @param int $code
     */
    protected function fail( $message, $code )
    {
        $this->cli()->error( "FAIL $message" );
        $this->shutdown( $code );
    }
}

}
?>
