<?php
/**
 * File containing the expAccessMdbToolsReader class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads an Access file with the MDBTools programs (Linux, macOS, BSD; packages mdbtools): mdb-tables for the tables,
 * mdb-schema (its MySQL output) for columns, primary key and indexes, mdb-json for the rows (NULL is told from empty
 * text, binary data comes as base64). MDBTools reads JET3 (Access 97), JET4 (2000-2003 .mdb) and ACE (.accdb) files;
 * it never writes. The programs run without a shell, each argument passed as it is.
 *
 * Settings: Bin, the directory of the programs (empty: the PATH).
 *
 * @since 6.0.15
 * @package kernel
 */
class expAccessMdbToolsReader extends expAccessReader
{
    /** @var string the directory of the programs ('' for the PATH) */
    protected $bin = '';

    /** @var array table => description, read once */
    protected $described = array();

    /**
     * @param string $file
     * @param array $settings Bin
     */
    public function __construct( $file, array $settings = array() )
    {
        parent::__construct( $file );
        $this->bin = isset( $settings['Bin'] ) ? rtrim( (string)$settings['Bin'], '/' ) : '';
        if ( !self::available( $this->bin ) )
            throw new RuntimeException( 'The MDBTools programs (mdb-tables, mdb-schema, mdb-json) were not found' . ( $this->bin !== '' ? " in {$this->bin}" : '' ) );
    }

    /**
     * Whether the programs are there.
     *
     * @param string $bin a directory, or '' for the PATH
     * @return bool
     */
    public static function available( $bin = '' )
    {
        foreach ( array( 'mdb-tables', 'mdb-schema', 'mdb-json' ) as $program )
            if ( self::path( $program, $bin ) === null )
                return false;
        return true;
    }

    /**
     * A program's full path, or null.
     *
     * @param string $program
     * @param string $bin
     * @return string|null
     */
    protected static function path( $program, $bin = '' )
    {
        $dirs = $bin !== '' ? array( $bin ) : explode( PATH_SEPARATOR, (string)getenv( 'PATH' ) );
        foreach ( $dirs as $dir )
            if ( $dir !== '' && is_file( "$dir/$program" ) && is_executable( "$dir/$program" ) )
                return "$dir/$program";
        return null;
    }

    public function name()
    {
        return 'mdbtools';
    }

    /**
     * The programs' environment: this process's, in a UTF-8 locale (MDBTools' option parser refuses a table name
     * such as "Umsätze" in the C locale).
     *
     * @return array
     */
    protected static function environment()
    {
        $environment = getenv();
        $environment['LC_ALL'] = 'C.UTF-8';
        return $environment;
    }

    /**
     * Runs a program and gives its output.
     *
     * @param string $program
     * @param string[] $arguments
     * @return string
     * @throws RuntimeException when it fails
     */
    protected function run( $program, array $arguments )
    {
        $command = array_merge( array( self::path( $program, $this->bin ) ), $arguments );
        $process = proc_open( $command, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, null, self::environment() );
        if ( !is_resource( $process ) )
            throw new RuntimeException( "$program could not be started" );
        $out = stream_get_contents( $pipes[1] );
        $err = stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $code = proc_close( $process );
        if ( $code !== 0 )
            throw new RuntimeException( "$program failed ($code): " . trim( $err ) );
        return (string)$out;
    }

    public function format()
    {
        try
        {
            return trim( $this->run( 'mdb-ver', array( $this->file ) ) );
        }
        catch ( Throwable $e )
        {
            return '';
        }
    }

    public function tables()
    {
        $tables = array();
        foreach ( preg_split( '/\r?\n/', $this->run( 'mdb-tables', array( '-1', $this->file ) ) ) as $line )
        {
            $line = rtrim( $line, "\r" );
            if ( $line !== '' && strpos( $line, 'MSys' ) !== 0 )
                $tables[] = $line;
        }
        return $tables;
    }

    public function table( $table )
    {
        if ( !isset( $this->described[$table] ) )
        {
            $ddl = $this->run( 'mdb-schema', array( '--no-relations', '--no-comments', '--no-drop-table', '--not-null', '--indexes',
                                                    '-T', (string)$table, $this->file, 'mysql' ) );
            $this->described[$table] = self::parseSchema( $ddl, $table );
        }
        return $this->described[$table];
    }

    /**
     * A table's description from mdb-schema's MySQL output (pure).
     *
     * @param string $ddl
     * @param string $table
     * @return array fields, primary, indexes
     */
    public static function parseSchema( $ddl, $table )
    {
        $description = array( 'fields' => array(), 'primary' => array(), 'indexes' => array() );
        $quoted = preg_quote( (string)$table, '/' );
        if ( preg_match( '/CREATE TABLE `' . $quoted . '`\s*\((.*?)\n\);/s', (string)$ddl, $m ) )
        {
            foreach ( preg_split( '/\n/', $m[1] ) as $line )
            {
                if ( !preg_match( '/^\s*`([^`]+)`\s+([a-z]+)\s*(?:\(\s*(\d+)\s*(?:,\s*(\d+)\s*)?\))?(.*?),?\s*$/i', $line, $c ) )
                    continue;
                $rest = strtolower( $c[5] );
                $auto = strpos( $rest, 'auto_increment' ) !== false;
                $description['fields'][$c[1]] = array( 'type' => $auto ? 'counter' : self::type( $c[2], isset( $c[3] ) ? (int)$c[3] : 0 ),
                                                       'length' => isset( $c[3] ) && $c[3] !== '' ? (int)$c[3] : 0,
                                                       'scale' => isset( $c[4] ) && $c[4] !== '' ? (int)$c[4] : 0,
                                                       'not_null' => $auto || strpos( $rest, 'not null' ) !== false,
                                                       'auto' => $auto );
            }
        }
        if ( preg_match_all( '/ALTER TABLE `' . $quoted . '` ADD (PRIMARY KEY|UNIQUE INDEX|INDEX) (?:`([^`]+)` )?\(([^)]*)\);/', (string)$ddl, $all, PREG_SET_ORDER ) )
        {
            foreach ( $all as $index )
            {
                preg_match_all( '/`([^`]+)`/', $index[3], $columns );
                if ( $index[1] === 'PRIMARY KEY' )
                    $description['primary'] = $columns[1];
                else
                    $description['indexes'][$index[2] !== '' ? $index[2] : implode( '_', $columns[1] )] = array( 'unique' => $index[1] === 'UNIQUE INDEX', 'fields' => $columns[1] );
            }
        }
        return $description;
    }

    /**
     * The reader's type of an mdb-schema MySQL type.
     *
     * @param string $mysqlType
     * @param int $length
     * @return string
     */
    public static function type( $mysqlType, $length = 0 )
    {
        switch ( strtolower( (string)$mysqlType ) )
        {
            case 'int':
                return 'integer';
            case 'smallint':
                return 'smallint';
            case 'tinyint':
                return 'byte';
            case 'bigint':
                return 'bigint';
            case 'boolean':
            case 'bool':
                return 'boolean';
            case 'float':
            case 'double':
                return 'double';
            case 'real':
                return 'single';
            case 'numeric':
            case 'decimal':
                return 'decimal';
            case 'datetime':
            case 'date':
            case 'time':
            case 'timestamp':
                return 'datetime';
            case 'varchar':
            case 'char':
                return 'text';
            case 'text':
                return 'memo';
            case 'blob':
                return 'binary';
        }
        return 'memo';
    }

    public function rows( $table, callable $each )
    {
        $fields = $this->table( $table )['fields'];
        $command = array( self::path( 'mdb-json', $this->bin ), '-D', '%Y-%m-%d %H:%M:%S', '-T', '%Y-%m-%d %H:%M:%S', $this->file, (string)$table );
        $process = proc_open( $command, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, null, self::environment() );
        if ( !is_resource( $process ) )
            throw new RuntimeException( 'mdb-json could not be started' );
        $count = 0;
        while ( ( $line = fgets( $pipes[1] ) ) !== false )
        {
            $line = trim( $line );
            if ( $line === '' )
                continue;
            $data = json_decode( $line, true, 512, JSON_BIGINT_AS_STRING | JSON_INVALID_UTF8_SUBSTITUTE );
            if ( !is_array( $data ) )
                continue;
            $row = array();
            foreach ( $fields as $name => $field )
            {
                $value = array_key_exists( $name, $data ) ? $data[$name] : null;
                if ( is_array( $value ) && isset( $value['$binary'] ) )
                    $value = new expAccessBinary( (string)base64_decode( (string)$value['$binary'] ) );
                elseif ( $field['type'] === 'datetime' )
                    $value = self::date( $value );
                elseif ( $field['type'] === 'boolean' && $value !== null )
                    $value = $value ? 1 : 0;
                $row[$name] = $value;
            }
            $each( $row );
            $count++;
        }
        $err = stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $code = proc_close( $process );
        if ( $code !== 0 )
            throw new RuntimeException( "mdb-json failed ($code) after $count rows: " . trim( (string)$err ) );
        return $count;
    }
}
?>
