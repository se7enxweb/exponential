<?php
/**
 * File containing the expCouchDBReader class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads a CouchDB database for an import (exp:couchdb:import), in the vocabulary of the Access import's readers
 * (expAccessReader), whose import engine (expAccessImport) makes the tables and copies the rows.
 *
 * Which documents make a table:
 *
 * - by a field (--type-field=type): one table per value of that field (documents without it are left out);
 * - a database the CouchDB driver wrote: one table per exp_table, with the definition the driver kept;
 * - otherwise one table, named after the database, of every document (design documents left out).
 *
 * A table without a kept definition has the columns of a sample of its documents (expCouchbaseReader::infer()),
 * plus doc_id (the document's _id, the primary key); _rev, _attachments and _conflicts are left out. Rows are read with
 * Mango (a page at a time, by bookmark) or, for the whole database, from _all_docs in the order of the ids.
 *
 * @since 6.0.15
 * @package kernel
 */
class expCouchDBReader extends expAccessReader
{
    /** The documents a table's columns are inferred from */
    const SAMPLE_SIZE = 500;

    /** Documents a page */
    const PAGE_SIZE = 1000;

    /** The fields of CouchDB itself, never columns */
    const SYSTEM_FIELDS = array( '_id', '_rev', '_attachments', '_conflicts', '_deleted', '_deleted_conflicts', '_local_seq', '_revs_info', '_revisions' );

    /** @var expCouchDBClient */
    protected $client;

    /** @var string */
    protected $database;

    /** @var string|null the field whose values name the tables */
    protected $typeField;

    /** @var array|null value => table name */
    protected $types = null;

    /** @var array table => .dba definition (a database of the driver) */
    protected $definitions = array();

    /** @var array table => description */
    protected $tables = array();

    /** @var int */
    protected $sampleSize;

    /**
     * @param expCouchDBClient $client
     * @param string $database
     * @param string|null $typeField
     * @param int $sampleSize
     * @throws RuntimeException when the database cannot be read
     */
    public function __construct( expCouchDBClient $client, $database, $typeField = null, $sampleSize = self::SAMPLE_SIZE )
    {
        $this->client = $client;
        $this->database = (string)$database;
        $this->typeField = $typeField !== null && $typeField !== '' ? (string)$typeField : null;
        $this->sampleSize = max( 1, (int)$sampleSize );
        $this->file = 'couchdb://' . preg_replace( '#^https?://#', '', $client->base() ) . '/' . $this->database;
        list( $status, $info ) = $client->request( 'GET', expCouchDBClient::path( $this->database ) );
        if ( $status !== 200 )
            throw new RuntimeException( "The CouchDB database {$this->database} cannot be read: " . expCouchDBDB::reason( $status, $info ) );
        if ( $this->typeField === null )
        {
            // a database the driver wrote: its definitions name the tables
            $found = $this->find( array( 'exp_table' => expCouchDBDB::SCHEMA_TABLE ), 10000 );
            foreach ( $found as $doc )
                if ( isset( $doc['name'], $doc['definition'] ) )
                    $this->definitions[(string)$doc['name']] = $doc['definition'];
            if ( $this->definitions )
                $this->typeField = 'exp_table';
        }
    }

    public function name()
    {
        return 'couchdb';
    }

    public function format()
    {
        list( $status, $welcome ) = $this->client->request( 'GET', '/' );
        return $status === 200 && isset( $welcome['version'] ) ? 'Apache CouchDB ' . $welcome['version'] : '';
    }

    /**
     * A table name of a value (pure): lower case letters, digits and _.
     *
     * @param string $value
     * @return string
     */
    public static function tableName( $value )
    {
        $name = strtolower( preg_replace( '/[^A-Za-z0-9_]+/', '_', (string)$value ) );
        $name = trim( $name, '_' );
        if ( $name === '' || !ctype_alpha( $name[0] ) )
            $name = 't_' . $name;
        return substr( $name, 0, 60 );
    }

    /**
     * The values of the type field and their tables (one pass over the database, or the definitions).
     *
     * @return array value => table
     */
    protected function types()
    {
        if ( $this->types !== null )
            return $this->types;
        $this->types = array();
        if ( $this->definitions )
        {
            foreach ( array_keys( $this->definitions ) as $table )
                $this->types[$table] = $table;
            return $this->types;
        }
        if ( $this->typeField === null )
        {
            $this->types[''] = self::tableName( $this->database );
            return $this->types;
        }
        $this->allDocs( function ( $doc ) {
            if ( isset( $doc[$this->typeField] ) && is_scalar( $doc[$this->typeField] ) && !isset( $this->types[(string)$doc[$this->typeField]] ) )
                $this->types[(string)$doc[$this->typeField]] = self::tableName( $doc[$this->typeField] );
        } );
        ksort( $this->types );
        return $this->types;
    }

    public function tables()
    {
        return array_values( array_unique( $this->types() ) );
    }

    /** The type field's value of a table */
    protected function valueOf( $table )
    {
        $value = array_search( $table, $this->types(), true );
        if ( $value === false )
            throw new RuntimeException( "The CouchDB database {$this->database} has no table '$table'" );
        return (string)$value;
    }

    public function table( $table )
    {
        if ( isset( $this->tables[$table] ) )
            return $this->tables[$table];
        if ( isset( $this->definitions[$table] ) )
            return $this->tables[$table] = expCouchbaseReader::fromDefinition( $this->definitions[$table] );
        $value = $this->valueOf( $table );
        $skip = array_merge( self::SYSTEM_FIELDS, $this->typeField !== null ? array( $this->typeField ) : array() );
        if ( $this->typeField === null )
        {
            $sample = array();
            $this->allDocs( function ( $doc ) use ( &$sample ) { $sample[] = $doc; }, $this->sampleSize );
        }
        else
            $sample = $this->find( array( $this->typeField => $value ), $this->sampleSize );
        return $this->tables[$table] = expCouchbaseReader::infer( $sample, expCouchbaseReader::KEY_COLUMN, $skip );
    }

    public function rows( $table, callable $each )
    {
        $description = $this->table( $table );
        $keyed = in_array( expCouchbaseReader::KEY_COLUMN, $description['primary'], true );
        $convert = function ( $doc ) use ( $description, $keyed, $each ) {
            $row = array();
            foreach ( $description['fields'] as $name => $field )
                $row[$name] = $name === expCouchbaseReader::KEY_COLUMN && $keyed ? (string)$doc['_id'] : expCouchbaseReader::value( array_key_exists( $name, $doc ) ? $doc[$name] : null, $field['type'] );
            $each( $row );
        };
        if ( $this->typeField === null )
            return $this->allDocs( $convert );
        $count = 0;
        $this->find( array( $this->typeField => $this->valueOf( $table ) ), null, function ( $doc ) use ( $convert, &$count ) {
            $convert( $doc );
            $count++;
        } );
        return $count;
    }

    /**
     * Every document but the design documents, in the order of the ids, a page at a time.
     *
     * @param callable $each
     * @param int|null $limit
     * @return int
     */
    protected function allDocs( callable $each, $limit = null )
    {
        $count = 0;
        $start = null;
        do
        {
            $query = '?include_docs=true&limit=' . ( self::PAGE_SIZE + 1 ) . ( $start !== null ? '&startkey=' . rawurlencode( json_encode( $start ) ) : '' );
            list( $status, $page ) = $this->client->request( 'GET', expCouchDBClient::path( $this->database ) . '/_all_docs' . $query );
            if ( $status !== 200 )
                throw new RuntimeException( "The documents of {$this->database} cannot be read: " . expCouchDBDB::reason( $status, $page ) );
            $rows = isset( $page['rows'] ) ? $page['rows'] : array();
            $start = count( $rows ) > self::PAGE_SIZE ? $rows[self::PAGE_SIZE]['id'] : null;
            foreach ( array_slice( $rows, 0, self::PAGE_SIZE ) as $row )
            {
                if ( strncmp( $row['id'], '_design/', 8 ) === 0 || !isset( $row['doc'] ) )
                    continue;
                $each( $row['doc'] );
                if ( $limit !== null && ++$count >= $limit )
                    return $count;
                if ( $limit === null )
                    $count++;
            }
        }
        while ( $start !== null );
        return $count;
    }

    /**
     * Documents by a Mango selector, a page at a time.
     *
     * @param array $selector
     * @param int|null $limit
     * @param callable|null $each called per document (then nothing is collected)
     * @return array[]
     */
    protected function find( array $selector, $limit = null, $each = null )
    {
        $docs = array();
        $bookmark = null;
        $count = 0;
        do
        {
            $request = array( 'selector' => (object)$selector, 'limit' => $limit !== null ? min( self::PAGE_SIZE, $limit - $count ) : self::PAGE_SIZE );
            if ( $bookmark !== null )
                $request['bookmark'] = $bookmark;
            list( $status, $page ) = $this->client->request( 'POST', expCouchDBClient::path( $this->database ) . '/_find', $request );
            if ( $status !== 200 )
                throw new RuntimeException( "The documents of {$this->database} cannot be read: " . expCouchDBDB::reason( $status, $page ) );
            $found = isset( $page['docs'] ) ? $page['docs'] : array();
            foreach ( $found as $doc )
            {
                if ( $each )
                    $each( $doc );
                else
                    $docs[] = $doc;
                $count++;
            }
            $bookmark = isset( $page['bookmark'] ) ? $page['bookmark'] : null;
        }
        while ( count( $found ) === $request['limit'] && $request['limit'] > 0 && $bookmark !== null && ( $limit === null || $count < $limit ) );
        return $docs;
    }
}
?>
