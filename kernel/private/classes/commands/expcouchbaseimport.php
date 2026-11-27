<?php
/**
 * @description Import the collections of a Couchbase scope into the site's database or a data table connection
 * @alias exp:couchbase:import
 *
 * File containing the exp:couchbase:import command (bin/php/expcouchbaseimport.php); guide:
 * doc/eng-us/features/6.0/couchbase-driver.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expcouchbaseimport extends \Exponential\Runnable\Command
{
    const EXIT_OK = 0;
    const EXIT_FAILED = 1;
    const EXIT_USAGE = 2;

    /** The machine output (--format=json). */
    const SCHEMA = 'exp-couchbase-import/1';

    /** The source's options of this command, with their help */
    protected function sourceOptions()
    {
        return array(
            'source' => 'The data table connection to read (engine couchbase)',
            'server' => 'Without --source: the Query service node or nodes (host, host:port, a comma list; https://... for TLS)',
            'port' => 'Without --source: the Query service port (8093)',
            'bucket' => 'Without --source: the bucket, or bucket.scope',
            'scope' => 'Without --source: the scope (_default when --bucket names none)',
            'user' => 'Without --source: the user',
            'secret' => 'Without --source: the password, as the name of a secret (Setup > Secrets) or env:VARIABLE',
            'sample' => 'Documents a collection\'s columns are inferred from (500)',
        );
    }

    /** The help text */
    protected function description()
    {
        return "Import the collections of a Couchbase scope into a database of this installation\n" .
               "Reads the scope through the Query service (SQL++ over REST, PHP's curl extension), makes the tables in the\n" .
               "site's database or in a data table connection (--connection), and copies the documents page by page. A\n" .
               "collection the Couchbase driver made keeps its columns and key; any other gets the fields of a sample of its\n" .
               "documents and doc_id, the document's key. Without --apply it shows the tables it would make and does nothing.\n" .
               "\n" .
               "./console exp:couchbase:import --source=catalogue --list\n" .
               "./console exp:couchbase:import --source=catalogue --prefix=cb_\n" .
               "./console exp:couchbase:import --server=cb1,cb2 --bucket=travel-sample.inventory --user=reader --secret=env:CB_PASSWORD --apply\n" .
               "./console exp:couchbase:import --source=catalogue --tables=airline --connection=archive --mode=replace --apply --format=json\n" .
               "\n" .
               "Exit code: 0 done (or planned); 1 a table or row failed; 2 wrong options or no connection.";
    }

    public function run()
    {
        $this->script( array( 'description' => $this->description(),
                              'use-session' => false,
                              'use-modules' => false,
                              'use-extensions' => true ) );
        $sourceOptions = $this->sourceOptions();
        $spec = '[list][apply][dry-run]';
        foreach ( array_keys( $sourceOptions ) as $name )
            $spec .= '[' . $name . ':]';
        $spec .= '[connection:][tables:][prefix:][mode:][format:]';
        $options = $this->startup( $spec, '', array(
            'list' => 'List the tables of the source with their columns, and stop',
            'apply' => 'Make the tables and copy the rows (without it: show the plan only)',
            'dry-run' => 'Show the plan only (the default)',
        ) + $sourceOptions + array(
            'connection' => 'The database to import into: site (the default) or the identifier of a data table connection',
            'tables' => 'Only these tables (a comma list)',
            'prefix' => 'Put this in front of every table name (lower case letters, digits, _), e.g. cb_',
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
            $reader = $this->openReader( $options );
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
                $this->cli()->output( json_encode( array( 'schema' => static::SCHEMA, 'source' => $reader->file(), 'server' => $reader->format(), 'tables' => $list ),
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
     * The reader of the source: a data table connection (--source) or a server named by the options.
     *
     * @param array $options
     * @return \expAccessReader
     * @throws \RuntimeException
     */
    protected function openReader( array $options )
    {
        $sample = isset( $options['sample'] ) && (int)$options['sample'] > 0 ? (int)$options['sample'] : \expCouchbaseReader::SAMPLE_SIZE;
        return new \expCouchbaseReader( self::openSource( $options ), $sample );
    }

    /**
     * The scope to read.
     *
     * @param array $options
     * @return \expCouchbaseDB
     * @throws \RuntimeException
     */
    public static function openSource( array $options )
    {
        if ( !empty( $options['source'] ) )
            $db = \expDataConnections::db( (string)$options['source'] );
        else
        {
            if ( empty( $options['server'] ) || empty( $options['bucket'] ) )
                throw new \RuntimeException( 'Name the source: --source=<connection>, or --server and --bucket' );
            $database = (string)$options['bucket'];
            if ( !empty( $options['scope'] ) && strpbrk( $database, './' ) === false )
                $database .= '.' . $options['scope'];
            $db = \expDataConnections::open( array( 'identifier' => 'import_source', 'engine' => 'couchbase', 'server' => (string)$options['server'],
                                                    'port' => isset( $options['port'] ) ? (int)$options['port'] : 0, 'database' => $database,
                                                    'user' => isset( $options['user'] ) ? (string)$options['user'] : '',
                                                    'secret' => isset( $options['secret'] ) ? (string)$options['secret'] : '', 'origin' => 'settings' ) );
        }
        if ( !$db instanceof \expCouchbaseDB )
            throw new \RuntimeException( 'The source is no Couchbase connection (engine ' . \expKernelTableSchema::type( $db ) . ')' );
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
            $this->cli()->output( json_encode( array( 'schema' => static::SCHEMA ) + $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
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
