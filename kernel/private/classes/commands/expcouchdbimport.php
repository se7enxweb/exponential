<?php
/**
 * @description Import the documents of an Apache CouchDB database into the site's database or a data table connection
 * @alias exp:couchdb:import
 *
 * File containing the exp:couchdb:import command (bin/php/expcouchdbimport.php); guide:
 * doc/eng-us/features/6.0/couchdb-driver.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expcouchdbimport extends Expcouchbaseimport
{
    /** The machine output (--format=json). */
    const SCHEMA = 'exp-couchdb-import/1';

    protected function sourceOptions()
    {
        return array(
            'source' => 'The data table connection to read (engine couchdb)',
            'server' => 'Without --source: the node (host, host:port; https://... for TLS)',
            'port' => 'Without --source: the port (5984)',
            'database' => 'Without --source: the CouchDB database',
            'user' => 'Without --source: the user',
            'secret' => 'Without --source: the password, as the name of a secret (Setup > Secrets) or env:VARIABLE',
            'type-field' => 'One table per value of this field (e.g. type); without it: one table of every document',
            'sample' => 'Documents a table\'s columns are inferred from (500)',
        );
    }

    protected function description()
    {
        return "Import the documents of an Apache CouchDB database into a database of this installation\n" .
               "Reads the database through CouchDB's HTTP API (PHP's curl extension), makes the tables in the site's database\n" .
               "or in a data table connection (--connection), and copies the documents page by page. The documents become\n" .
               "one table, or one table per value of --type-field; their columns are the fields of a sample of them and doc_id.\n" .
               "A database the CouchDB driver wrote keeps its tables, columns and keys. Without --apply it shows the plan.\n" .
               "\n" .
               "./console exp:couchdb:import --source=orders --list\n" .
               "./console exp:couchdb:import --server=couch1 --database=shop --type-field=type --user=reader --secret=env:COUCH_PASSWORD\n" .
               "./console exp:couchdb:import --source=orders --type-field=type --prefix=shop_ --apply\n" .
               "./console exp:couchdb:import --source=orders --connection=archive --mode=replace --apply --format=json\n" .
               "\n" .
               "Exit code: 0 done (or planned); 1 a table or row failed; 2 wrong options or no connection.";
    }

    protected function openReader( array $options )
    {
        $sample = isset( $options['sample'] ) && (int)$options['sample'] > 0 ? (int)$options['sample'] : \expCouchDBReader::SAMPLE_SIZE;
        $typeField = isset( $options['type-field'] ) && $options['type-field'] !== '' ? (string)$options['type-field'] : null;
        if ( !empty( $options['source'] ) )
        {
            $db = \expDataConnections::db( (string)$options['source'] );
            if ( !$db instanceof \expCouchDBDB )
                throw new \RuntimeException( 'The source is no CouchDB connection (engine ' . \expKernelTableSchema::type( $db ) . ')' );
            return new \expCouchDBReader( $db->client(), $db->DB, $typeField, $sample );
        }
        if ( empty( $options['server'] ) || empty( $options['database'] ) )
            throw new \RuntimeException( 'Name the source: --source=<connection>, or --server and --database' );
        // the name of a secret of the kernel's store, or env:VARIABLE
        $password = !empty( $options['secret'] ) ? (string)\expSecrets::get( (string)$options['secret'] ) : '';
        $settings = \expCouchDBDB::settings();
        $client = new \expCouchDBClient( (string)$options['server'], isset( $options['port'] ) ? (int)$options['port'] : 0,
                                         isset( $options['user'] ) ? (string)$options['user'] : '', $password, $settings );
        return new \expCouchDBReader( $client, (string)$options['database'], $typeField, $sample );
    }
}

}
?>
