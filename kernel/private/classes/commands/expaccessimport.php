<?php
/**
 * @description Import the tables of a Microsoft Access file (.accdb, .mdb) into the site's database or a data table connection
 * @alias exp:access:import
 *
 * File containing the exp:access:import command (bin/php/expaccessimport.php); guide: doc/eng-us/features/6.0/ms-access-support.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expaccessimport extends \Exponential\Runnable\Command
{
    const EXIT_OK = 0;
    const EXIT_FAILED = 1;
    const EXIT_USAGE = 2;

    /** The machine output (--format=json). */
    const SCHEMA = 'exp-access-import/1';

    public function run()
    {
        $this->script( array( 'description' => "Import the tables of a Microsoft Access file into a database of this installation\n" .
                                               "Reads the file with MDBTools (Linux; read only) or with Microsoft's Access ODBC driver (Windows), makes\n" .
                                               "the tables in the site's database or in a data table connection (--connection), and copies the rows,\n" .
                                               "ids and all. Without --apply it shows the tables it would make and does nothing.\n" .
                                               "\n" .
                                               "./console exp:access:import shop.accdb --list\n" .
                                               "./console exp:access:import shop.accdb --prefix=shop_\n" .
                                               "./console exp:access:import shop.accdb --prefix=shop_ --connection=archive --apply\n" .
                                               "./console exp:access:import shop.mdb --tables=Customers,Orders --mode=replace --apply --format=json\n" .
                                               "\n" .
                                               "Exit code: 0 done (or planned); 1 a table or row failed; 2 wrong options or an unreadable file.",
                              'use-session' => false,
                              'use-modules' => false,
                              'use-extensions' => true ) );
        $options = $this->startup( '[list][apply][dry-run][connection:][tables:][prefix:][mode:][reader:][mdbtools-bin:][driver:][format:]', '[file]', array(
            'list' => 'List the tables of the file with their columns, and stop',
            'apply' => 'Make the tables and copy the rows (without it: show the plan only)',
            'dry-run' => 'Show the plan only (the default)',
            'connection' => 'The database to import into: site (the default) or the identifier of a data table connection',
            'tables' => 'Only these Access tables (a comma list; names as in the file)',
            'prefix' => 'Put this in front of every table name (lower case letters, digits, _), e.g. shop_',
            'mode' => 'create (the default: new tables only), append (add rows), replace (delete the rows first)',
            'reader' => 'auto (the default), mdbtools or odbc',
            'mdbtools-bin' => 'The directory of the MDBTools programs (default: the PATH)',
            'driver' => 'The ODBC driver for --reader=odbc (default: Microsoft Access Driver (*.mdb, *.accdb))',
            'format' => 'text (the default) or json',
        ) );
        $format = isset( $options['format'] ) && $options['format'] ? (string)$options['format'] : 'text';
        if ( $format !== 'text' && $format !== 'json' )
            $this->fail( "--format must be text or json, not $format", self::EXIT_USAGE );
        $file = isset( $options['arguments'][0] ) ? (string)$options['arguments'][0] : '';
        if ( $file === '' )
            $this->fail( 'Name the Access file: exp:access:import <file>', self::EXIT_USAGE );
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
        $settings = array();
        if ( !empty( $options['mdbtools-bin'] ) )
            $settings['Bin'] = (string)$options['mdbtools-bin'];
        if ( !empty( $options['driver'] ) )
            $settings['Driver'] = (string)$options['driver'];
        try
        {
            $reader = \expAccessReader::create( $file, isset( $options['reader'] ) && $options['reader'] ? (string)$options['reader'] : 'auto', $settings );
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
                $this->cli()->output( json_encode( array( 'schema' => self::SCHEMA, 'file' => $file, 'reader' => $reader->name(), 'format' => $reader->format(), 'tables' => $list ),
                                                   JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            else
            {
                $this->cli()->output( "$file (" . $reader->format() . ', read with ' . $reader->name() . '): ' . count( $list ) . ' tables' );
                foreach ( $list as $table => $description )
                {
                    $this->cli()->output( "  $table" . ( $description['primary'] ? ' (key ' . implode( ', ', $description['primary'] ) . ')' : '' ) );
                    foreach ( $description['fields'] as $name => $field )
                        $this->cli()->output( sprintf( '    %-30s %s%s%s', $name, $field['type'], $field['length'] ? '(' . $field['length'] . ')' : '', $field['not_null'] ? ' not null' : '' ) );
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
            $report = array( 'mode' => 'plan', 'file' => $file, 'reader' => $reader->name(), 'connection' => $connection, 'engine' => $plan['plan']['engine'],
                             'tables' => array(), 'notes' => $plan['notes'] );
            foreach ( $plan['map'] as $access => $map )
            {
                $step = isset( $plan['plan']['tables'][$map['table']] ) ? $plan['plan']['tables'][$map['table']] : array( 'action' => 'none', 'statements' => array() );
                $report['tables'][$access] = array( 'table' => $map['table'], 'action' => $step['action'], 'columns' => $map['columns'], 'statements' => $step['statements'] );
            }
            $this->report( $report, $format );
            $this->shutdown( self::EXIT_OK );
            return;
        }
        $result = \expAccessImport::run( $reader, $db, $importOptions );
        $report = array( 'mode' => 'import', 'file' => $file, 'reader' => $reader->name(), 'connection' => $connection ) + $result;
        $this->report( $report, $format );
        $this->shutdown( $result['ok'] ? self::EXIT_OK : self::EXIT_FAILED );
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
        $out->output( sprintf( '%s %s into %s (%s), read with %s', $report['mode'] === 'plan' ? 'Plan for' : 'Imported', $report['file'], $report['connection'],
                               $report['engine'], $report['reader'] ) );
        foreach ( $report['tables'] as $access => $table )
        {
            if ( $report['mode'] === 'plan' )
                $out->output( sprintf( '  %-30s -> %-30s %s, %d columns', $access, $table['table'], $table['action'], count( $table['columns'] ) ) );
            else
            {
                $out->output( sprintf( '  %-30s -> %-30s %s, %d read, %d written', $access, $table['table'], $table['action'], $table['read'], $table['written'] ) );
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
