<?php
/**
 * File containing the expDuckDBFFI class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * DuckDB's C library (libduckdb) through PHP's FFI extension: one database file, one connection, statements and
 * their rows. This is the layer expDuckDBDB runs on; it knows nothing of Exponential.
 *
 * Only DuckDB's stable C API is used: duckdb_open_ext() with a configuration, duckdb_query(), and the rows read chunk
 * by chunk (duckdb_fetch_chunk(), the vectors of a chunk), not the per-value functions DuckDB deprecated in 1.1. So
 * the binding works with DuckDB 1.x and 2.0 alike (1.5.6 and 2.0.1 are tested).
 *
 * Values come back as PHP strings (NULL as null), as the mysqli and pgsql extensions return them: integers and
 * decimals in plain digits, floats as PHP prints them, BOOLEAN as 1 or 0, DATE as Y-m-d, TIMESTAMP as Y-m-d H:i:s (UTC,
 * with microseconds when there are any), TIME as H:i:s, BLOB as its bytes. A type a site's tables do not use (LIST,
 * STRUCT, MAP, UNION, INTERVAL ...) fails the statement with a message asking to CAST it to VARCHAR.
 *
 *   $duck = new expDuckDBFFI( '/usr/local/lib/libduckdb.so' );
 *   $duck->open( '/var/www/site/var/storage/duckdb/site.duckdb', array( 'access_mode' => 'READ_WRITE' ) );
 *   $result = $duck->query( 'SELECT id, name FROM ezsection ORDER BY id' );   // array( 'columns', 'rows', 'changed' )
 *
 * @since 6.0.15
 * @package lib
 */
class expDuckDBFFI
{
    /** The C declarations of the part of duckdb.h used here (the layout of DuckDB 1.1 to 2.0) */
    const CDEF = <<<'CDEF'
typedef uint64_t idx_t;
typedef struct _duckdb_database { void *internal_ptr; } *duckdb_database;
typedef struct _duckdb_connection { void *internal_ptr; } *duckdb_connection;
typedef struct _duckdb_config { void *internal_ptr; } *duckdb_config;
typedef struct _duckdb_data_chunk { void *internal_ptr; } *duckdb_data_chunk;
typedef struct _duckdb_vector { void *internal_ptr; } *duckdb_vector;
typedef struct _duckdb_logical_type { void *internal_ptr; } *duckdb_logical_type;
typedef struct { void *deprecated_data; bool *deprecated_nullmask; int32_t deprecated_type; char *deprecated_name; void *internal_data; } duckdb_column;
typedef struct { idx_t deprecated_column_count; idx_t deprecated_row_count; idx_t deprecated_rows_changed; duckdb_column *deprecated_columns; char *deprecated_error_message; void *internal_data; } duckdb_result;
typedef struct { uint64_t lower; int64_t upper; } duckdb_hugeint;
typedef struct { union { struct { uint32_t length; char prefix[4]; char *ptr; } pointer; struct { uint32_t length; char inlined[12]; } inlined; } value; } duckdb_string_t;
int32_t duckdb_open_ext(const char *path, duckdb_database *out_database, duckdb_config config, char **out_error);
void duckdb_close(duckdb_database *database);
int32_t duckdb_connect(duckdb_database database, duckdb_connection *out_connection);
void duckdb_disconnect(duckdb_connection *connection);
int32_t duckdb_create_config(duckdb_config *out_config);
int32_t duckdb_set_config(duckdb_config config, const char *name, const char *option);
void duckdb_destroy_config(duckdb_config *config);
int32_t duckdb_query(duckdb_connection connection, const char *query, duckdb_result *out_result);
void duckdb_destroy_result(duckdb_result *result);
const char *duckdb_result_error(duckdb_result *result);
int32_t duckdb_result_error_type(duckdb_result *result);
idx_t duckdb_column_count(duckdb_result *result);
const char *duckdb_column_name(duckdb_result *result, idx_t col);
int32_t duckdb_column_type(duckdb_result *result, idx_t col);
duckdb_logical_type duckdb_column_logical_type(duckdb_result *result, idx_t col);
void duckdb_destroy_logical_type(duckdb_logical_type *type);
uint8_t duckdb_decimal_width(duckdb_logical_type type);
uint8_t duckdb_decimal_scale(duckdb_logical_type type);
int32_t duckdb_decimal_internal_type(duckdb_logical_type type);
idx_t duckdb_rows_changed(duckdb_result *result);
duckdb_data_chunk duckdb_fetch_chunk(duckdb_result result);
void duckdb_destroy_data_chunk(duckdb_data_chunk *chunk);
idx_t duckdb_data_chunk_get_size(duckdb_data_chunk chunk);
duckdb_vector duckdb_data_chunk_get_vector(duckdb_data_chunk chunk, idx_t col_idx);
void *duckdb_vector_get_data(duckdb_vector vector);
uint64_t *duckdb_vector_get_validity(duckdb_vector vector);
const char *duckdb_library_version(void);
void duckdb_free(void *ptr);
CDEF;

    /** duckdb_type values (duckdb.h) */
    const TYPE_BOOLEAN = 1;
    const TYPE_TINYINT = 2;
    const TYPE_SMALLINT = 3;
    const TYPE_INTEGER = 4;
    const TYPE_BIGINT = 5;
    const TYPE_UTINYINT = 6;
    const TYPE_USMALLINT = 7;
    const TYPE_UINTEGER = 8;
    const TYPE_UBIGINT = 9;
    const TYPE_FLOAT = 10;
    const TYPE_DOUBLE = 11;
    const TYPE_TIMESTAMP = 12;
    const TYPE_DATE = 13;
    const TYPE_TIME = 14;
    const TYPE_HUGEINT = 16;
    const TYPE_VARCHAR = 17;
    const TYPE_BLOB = 18;
    const TYPE_DECIMAL = 19;
    const TYPE_TIMESTAMP_S = 20;
    const TYPE_TIMESTAMP_MS = 21;
    const TYPE_TIMESTAMP_NS = 22;
    const TYPE_UUID = 27;
    const TYPE_TIMESTAMP_TZ = 31;
    const TYPE_UHUGEINT = 32;
    const TYPE_SQLNULL = 36;

    /** duckdb_error_type values the driver tells apart (duckdb.h) */
    const ERROR_CATALOG = 13;
    const ERROR_PARSER = 14;
    const ERROR_CONSTRAINT = 18;
    const ERROR_TRANSACTION = 10;
    const ERROR_IO = 28;
    const ERROR_INTERRUPT = 29;
    const ERROR_SYNTAX = 22;
    const ERROR_BINDER = 24;
    const ERROR_CONVERSION = 2;

    /** @var FFI[] one FFI instance per library file, for the whole process: loading the library again costs ~10 ms */
    protected static $Libraries = array();

    /** @var FFI */
    protected $FFI;

    /** @var string the library file */
    protected $Library;

    /** @var FFI\CData|null duckdb_database */
    protected $Database = null;

    /** @var FFI\CData|null duckdb_connection */
    protected $Connection = null;

    /** @var string the database file that is open, '' when none */
    protected $Path = '';

    /** @var string the error of the last call, '' when it succeeded */
    public $Error = '';

    /** @var int the duckdb_error_type of the last failed statement, 0 for none (or a failure outside a statement) */
    public $ErrorType = 0;

    /**
     * Loads libduckdb.
     *
     * @param string $library the shared library: an absolute path, or a name the system's loader finds
     *                        (libduckdb.so, libduckdb.dylib, duckdb.dll)
     * @throws RuntimeException without the FFI extension, with FFI switched off for this process
     *                          (ffi.enable=preload outside the command line), or a library that does not load
     */
    public function __construct( $library )
    {
        if ( !extension_loaded( 'ffi' ) || !class_exists( 'FFI', false ) )
            throw new RuntimeException( 'PHP has no FFI extension: DuckDB is reached through FFI (install php-ffi and set ffi.enable=true)' );
        $library = (string)$library;
        if ( $library === '' )
            throw new RuntimeException( 'No DuckDB library was given ([DuckDBSettings] Library)' );
        $this->Library = $library;
        if ( !isset( self::$Libraries[$library] ) )
        {
            try
            {
                self::$Libraries[$library] = FFI::cdef( self::CDEF, $library );
            }
            catch ( Throwable $e )
            {
                $hint = ini_get( 'ffi.enable' ) === 'preload' && PHP_SAPI !== 'cli' ? ' (ffi.enable=preload allows FFI on the command line only: set ffi.enable=true)' : '';
                throw new RuntimeException( "The DuckDB library $library could not be loaded: " . $e->getMessage() . $hint, 0, $e );
            }
        }
        $this->FFI = self::$Libraries[$library];
    }

    /**
     * The library file the default settings use on this system.
     *
     * @return string
     */
    public static function defaultLibrary()
    {
        if ( PHP_OS_FAMILY === 'Windows' )
            return 'duckdb.dll';
        if ( PHP_OS_FAMILY === 'Darwin' )
            return 'libduckdb.dylib';
        return 'libduckdb.so';
    }

    /**
     * Whether FFI can be used in this process at all (the extension, and not ffi.enable=false or, outside the
     * command line, preload).
     *
     * @return bool
     */
    public static function ffiUsable()
    {
        if ( !extension_loaded( 'ffi' ) )
            return false;
        $enable = strtolower( (string)ini_get( 'ffi.enable' ) );
        if ( in_array( $enable, array( '1', 'true', 'on', 'yes' ), true ) )
            return true;
        return $enable === 'preload' && PHP_SAPI === 'cli';
    }

    /**
     * The library's version, e.g. v2.0.1.
     *
     * @return string
     */
    public function libraryVersion()
    {
        return (string)self::text( $this->FFI->duckdb_library_version() );
    }

    /**
     * Opens the database file (created when it is not there) and one connection to it.
     *
     * @param string $path the file, or :memory:
     * @param array $config DuckDB configuration options, name => value (access_mode, threads, memory_limit ...)
     * @return bool false with Error set
     */
    public function open( $path, array $config = array() )
    {
        $this->close();
        $this->Error = '';
        $this->ErrorType = 0;
        $ffi = $this->FFI;
        $configHandle = $ffi->new( 'duckdb_config' );
        if ( $ffi->duckdb_create_config( FFI::addr( $configHandle ) ) !== 0 )
        {
            $this->Error = 'DuckDB could not make a configuration';
            return false;
        }
        foreach ( $config as $name => $value )
        {
            if ( $ffi->duckdb_set_config( $configHandle, (string)$name, (string)$value ) !== 0 )
            {
                $ffi->duckdb_destroy_config( FFI::addr( $configHandle ) );
                $this->Error = "DuckDB does not accept the setting $name=$value";
                return false;
            }
        }
        $database = $ffi->new( 'duckdb_database' );
        $error = $ffi->new( 'char*' );
        $state = $ffi->duckdb_open_ext( $path === ':memory:' ? null : (string)$path, FFI::addr( $database ), $configHandle, FFI::addr( $error ) );
        $ffi->duckdb_destroy_config( FFI::addr( $configHandle ) );
        if ( $state !== 0 )
        {
            $this->Error = !FFI::isNull( $error ) ? FFI::string( $error ) : "DuckDB could not open $path";
            if ( !FFI::isNull( $error ) )
                $ffi->duckdb_free( $error );
            $this->ErrorType = self::ERROR_IO;
            return false;
        }
        $connection = $ffi->new( 'duckdb_connection' );
        if ( $ffi->duckdb_connect( $database, FFI::addr( $connection ) ) !== 0 )
        {
            $ffi->duckdb_close( FFI::addr( $database ) );
            $this->Error = "DuckDB could not connect to $path";
            return false;
        }
        $this->Database = $database;
        $this->Connection = $connection;
        $this->Path = (string)$path;
        return true;
    }

    /**
     * Whether a database is open.
     *
     * @return bool
     */
    public function isOpen()
    {
        return $this->Connection !== null;
    }

    /**
     * The database file that is open, '' when none.
     *
     * @return string
     */
    public function path()
    {
        return $this->Path;
    }

    /**
     * Closes the connection and the database, which releases the file's lock for other processes.
     */
    public function close()
    {
        if ( $this->Connection !== null )
            $this->FFI->duckdb_disconnect( FFI::addr( $this->Connection ) );
        if ( $this->Database !== null )
            $this->FFI->duckdb_close( FFI::addr( $this->Database ) );
        $this->Connection = null;
        $this->Database = null;
        $this->Path = '';
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * Runs one statement (or several, separated by semicolons: the result is the last one's).
     *
     * @param string $sql
     * @param bool $wantRows false: read no rows, only the count of changed rows
     * @return array|false array( 'columns' => string[], 'rows' => array[] (column name => string|null),
     *                     'changed' => int ); false with Error and ErrorType set
     */
    public function query( $sql, $wantRows = true )
    {
        $this->Error = '';
        $this->ErrorType = 0;
        if ( $this->Connection === null )
        {
            $this->Error = 'No DuckDB database is open';
            return false;
        }
        $ffi = $this->FFI;
        $result = $ffi->new( 'duckdb_result' );
        $state = $ffi->duckdb_query( $this->Connection, (string)$sql, FFI::addr( $result ) );
        if ( $state !== 0 )
        {
            $message = $ffi->duckdb_result_error( FFI::addr( $result ) );
            $this->Error = (string)self::text( $message ) !== '' ? (string)self::text( $message ) : 'The statement failed';
            $this->ErrorType = (int)$ffi->duckdb_result_error_type( FFI::addr( $result ) );
            $ffi->duckdb_destroy_result( FFI::addr( $result ) );
            return false;
        }
        try
        {
            $out = array( 'columns' => array(), 'rows' => array(), 'changed' => (int)$ffi->duckdb_rows_changed( FFI::addr( $result ) ) );
            $count = (int)$ffi->duckdb_column_count( FFI::addr( $result ) );
            $types = array();
            for ( $c = 0; $c < $count; $c++ )
            {
                $out['columns'][$c] = (string)self::text( $ffi->duckdb_column_name( FFI::addr( $result ), $c ) );
                $types[$c] = $this->columnType( $result, $c );
            }
            if ( !$wantRows || $count === 0 )
                return $out;
            $names = $out['columns'];
            while ( true )
            {
                $chunk = $ffi->duckdb_fetch_chunk( $result );
                if ( $chunk === null || FFI::isNull( $chunk ) )
                    break;
                $size = (int)$ffi->duckdb_data_chunk_get_size( $chunk );
                if ( $size === 0 )
                {
                    $ffi->duckdb_destroy_data_chunk( FFI::addr( $chunk ) );
                    break;
                }
                $columns = array();
                for ( $c = 0; $c < $count; $c++ )
                    $columns[$c] = $this->readVector( $ffi->duckdb_data_chunk_get_vector( $chunk, $c ), $types[$c], $size, $names[$c] );
                $ffi->duckdb_destroy_data_chunk( FFI::addr( $chunk ) );
                for ( $r = 0; $r < $size; $r++ )
                {
                    $row = array();
                    for ( $c = 0; $c < $count; $c++ )
                        $row[$names[$c]] = $columns[$c][$r];
                    $out['rows'][] = $row;
                }
            }
            return $out;
        }
        catch ( UnexpectedValueException $e )
        {
            $this->Error = $e->getMessage();
            $this->ErrorType = self::ERROR_CONVERSION;
            return false;
        }
        finally
        {
            $ffi->duckdb_destroy_result( FFI::addr( $result ) );
        }
    }

    /**
     * The type of a result column: array( duckdb_type, decimal scale, decimal internal type ).
     *
     * @param FFI\CData $result
     * @param int $column
     * @return array
     */
    protected function columnType( $result, $column )
    {
        $ffi = $this->FFI;
        $type = (int)$ffi->duckdb_column_type( FFI::addr( $result ), $column );
        if ( $type !== self::TYPE_DECIMAL )
            return array( $type, 0, 0 );
        $logical = $ffi->duckdb_column_logical_type( FFI::addr( $result ), $column );
        $scale = (int)$ffi->duckdb_decimal_scale( $logical );
        $internal = (int)$ffi->duckdb_decimal_internal_type( $logical );
        $ffi->duckdb_destroy_logical_type( FFI::addr( $logical ) );
        return array( $type, $scale, $internal );
    }

    /**
     * The values of one vector of a chunk, as strings (null for NULL).
     *
     * @param FFI\CData $vector
     * @param array $type columnType()
     * @param int $size rows in the chunk
     * @param string $name the column, for the message of a type that is not read
     * @return array
     * @throws UnexpectedValueException for a type that is not read
     */
    protected function readVector( $vector, array $type, $size, $name )
    {
        $ffi = $this->FFI;
        $data = $ffi->duckdb_vector_get_data( $vector );
        $validity = $ffi->duckdb_vector_get_validity( $vector );
        $allValid = $validity === null || FFI::isNull( $validity );
        $values = array();
        list( $id, $scale, $internal ) = $type;
        switch ( $id )
        {
            case self::TYPE_SQLNULL:
                return array_fill( 0, $size, null );
            case self::TYPE_BOOLEAN:
            case self::TYPE_UTINYINT:
                $cells = $ffi->cast( 'uint8_t*', $data );
                break;
            case self::TYPE_TINYINT:
                $cells = $ffi->cast( 'int8_t*', $data );
                break;
            case self::TYPE_SMALLINT:
                $cells = $ffi->cast( 'int16_t*', $data );
                break;
            case self::TYPE_USMALLINT:
                $cells = $ffi->cast( 'uint16_t*', $data );
                break;
            case self::TYPE_INTEGER:
            case self::TYPE_DATE:
                $cells = $ffi->cast( 'int32_t*', $data );
                break;
            case self::TYPE_UINTEGER:
                $cells = $ffi->cast( 'uint32_t*', $data );
                break;
            case self::TYPE_BIGINT:
            case self::TYPE_TIMESTAMP:
            case self::TYPE_TIMESTAMP_TZ:
            case self::TYPE_TIMESTAMP_S:
            case self::TYPE_TIMESTAMP_MS:
            case self::TYPE_TIMESTAMP_NS:
            case self::TYPE_TIME:
                $cells = $ffi->cast( 'int64_t*', $data );
                break;
            case self::TYPE_UBIGINT:
                $cells = $ffi->cast( 'uint64_t*', $data );
                break;
            case self::TYPE_FLOAT:
                $cells = $ffi->cast( 'float*', $data );
                break;
            case self::TYPE_DOUBLE:
                $cells = $ffi->cast( 'double*', $data );
                break;
            case self::TYPE_VARCHAR:
            case self::TYPE_BLOB:
                $cells = $ffi->cast( 'duckdb_string_t*', $data );
                break;
            case self::TYPE_HUGEINT:
            case self::TYPE_UHUGEINT:
                $cells = $ffi->cast( 'duckdb_hugeint*', $data );
                break;
            case self::TYPE_DECIMAL:
                $cells = $ffi->cast( array( self::TYPE_SMALLINT => 'int16_t*', self::TYPE_INTEGER => 'int32_t*',
                                           self::TYPE_BIGINT => 'int64_t*' )[$internal] ?? 'duckdb_hugeint*', $data );
                break;
            default:
                throw new UnexpectedValueException( "The column $name has a DuckDB type the driver does not read (type $id): CAST it to VARCHAR in the query" );
        }
        for ( $r = 0; $r < $size; $r++ )
        {
            if ( !$allValid && ( ( $validity[$r >> 6] >> ( $r & 63 ) ) & 1 ) === 0 )
            {
                $values[$r] = null;
                continue;
            }
            $cell = $cells[$r];
            switch ( $id )
            {
                case self::TYPE_VARCHAR:
                case self::TYPE_BLOB:
                    $length = $cell->value->inlined->length;
                    $values[$r] = $length <= 12 ? FFI::string( $cell->value->inlined->inlined, $length ) : FFI::string( $cell->value->pointer->ptr, $length );
                    break;
                case self::TYPE_FLOAT:
                case self::TYPE_DOUBLE:
                    $values[$r] = self::floatText( $cell );
                    break;
                case self::TYPE_DATE:
                    $values[$r] = gmdate( 'Y-m-d', $cell * 86400 );
                    break;
                case self::TYPE_TIMESTAMP:
                case self::TYPE_TIMESTAMP_TZ:
                    $values[$r] = self::timestampText( $cell, 1000000 );
                    break;
                case self::TYPE_TIMESTAMP_S:
                    $values[$r] = self::timestampText( $cell, 1 );
                    break;
                case self::TYPE_TIMESTAMP_MS:
                    $values[$r] = self::timestampText( $cell, 1000 );
                    break;
                case self::TYPE_TIMESTAMP_NS:
                    $values[$r] = self::timestampText( intdiv( $cell, 1000 ), 1000000 );
                    break;
                case self::TYPE_TIME:
                    $seconds = intdiv( $cell, 1000000 );
                    $values[$r] = sprintf( '%02d:%02d:%02d', intdiv( $seconds, 3600 ), intdiv( $seconds % 3600, 60 ), $seconds % 60 )
                                . ( $cell % 1000000 ? sprintf( '.%06d', $cell % 1000000 ) : '' );
                    break;
                case self::TYPE_HUGEINT:
                case self::TYPE_UHUGEINT:
                    $values[$r] = self::hugeintText( $cell->upper, $cell->lower, $id === self::TYPE_UHUGEINT );
                    break;
                case self::TYPE_UBIGINT:
                    $values[$r] = $cell < 0 ? sprintf( '%u', $cell ) : (string)$cell;
                    break;
                case self::TYPE_DECIMAL:
                    $digits = is_int( $cell ) ? (string)$cell : self::hugeintText( $cell->upper, $cell->lower, false );
                    $values[$r] = self::decimalText( $digits, $scale );
                    break;
                default:
                    $values[$r] = (string)$cell;
            }
        }
        return $values;
    }

    /**
     * A C string a function returned: FFI hands const char * back as a PHP string already, other pointers as CData.
     *
     * @param mixed $value
     * @return string|null
     */
    protected static function text( $value )
    {
        if ( $value === null || is_string( $value ) )
            return $value;
        return FFI::isNull( $value ) ? null : FFI::string( $value );
    }

    /**
     * A float as text: integral values without a fraction, others with the precision PHP prints.
     *
     * @param float $value
     * @return string
     */
    public static function floatText( $value )
    {
        if ( is_nan( $value ) )
            return 'NaN';
        if ( is_infinite( $value ) )
            return $value > 0 ? 'Infinity' : '-Infinity';
        return (string)$value;
    }

    /**
     * A timestamp of $units per second since 1970 as Y-m-d H:i:s[.uuuuuu] (UTC).
     *
     * @param int $value
     * @param int $perSecond 1, 1000 or 1000000
     * @return string
     */
    public static function timestampText( $value, $perSecond )
    {
        $seconds = intdiv( $value, $perSecond );
        $fraction = $value % $perSecond;
        if ( $fraction < 0 )
        {
            $seconds--;
            $fraction += $perSecond;
        }
        $text = gmdate( 'Y-m-d H:i:s', $seconds );
        if ( $fraction )
            $text .= '.' . str_pad( (string)( $fraction * intdiv( 1000000, $perSecond ) ), 6, '0', STR_PAD_LEFT );
        return $text;
    }

    /**
     * A 128-bit integer (upper 64 bits signed, lower 64 unsigned) in decimal digits.
     *
     * @param int $upper
     * @param int $lower as PHP's signed int
     * @param bool $unsigned the upper half is unsigned too
     * @return string
     */
    public static function hugeintText( $upper, $lower, $unsigned = false )
    {
        if ( !$unsigned && ( ( $upper === 0 && $lower >= 0 ) || ( $upper === -1 && $lower < 0 ) ) )
            return (string)$lower;   // fits in 64 bits
        $upperText = $upper < 0 ? sprintf( '%u', $upper ) : (string)$upper;
        $lowerText = $lower < 0 ? sprintf( '%u', $lower ) : (string)$lower;
        $value = self::decimalAdd( self::decimalMultiply( $upperText, '18446744073709551616' ), $lowerText );
        if ( $unsigned || $upper >= 0 )
            return $value;
        // a negative signed value: the unsigned reading minus 2^128
        return '-' . self::decimalSubtract( '340282366920938463463374607431768211456', $value );
    }

    /**
     * Decimal text of $a - $b, both non-negative decimal texts with $a >= $b.
     *
     * @return string
     */
    public static function decimalSubtract( $a, $b )
    {
        if ( function_exists( 'bcsub' ) )
            return bcsub( $a, $b, 0 );
        $a = strrev( $a );
        $b = strrev( $b );
        $out = '';
        $borrow = 0;
        for ( $i = 0, $n = strlen( $a ); $i < $n; $i++ )
        {
            $d = (int)$a[$i] - ( $i < strlen( $b ) ? (int)$b[$i] : 0 ) - $borrow;
            $borrow = $d < 0 ? 1 : 0;
            $out .= $d < 0 ? $d + 10 : $d;
        }
        $text = ltrim( strrev( $out ), '0' );
        return $text === '' ? '0' : $text;
    }


    /**
     * Decimal text of $a * $b, both non-negative decimal texts.
     *
     * @return string
     */
    public static function decimalMultiply( $a, $b )
    {
        if ( function_exists( 'bcmul' ) )
            return bcmul( $a, $b, 0 );
        $result = array_fill( 0, strlen( $a ) + strlen( $b ), 0 );
        for ( $i = strlen( $a ) - 1; $i >= 0; $i-- )
            for ( $j = strlen( $b ) - 1; $j >= 0; $j-- )
            {
                $p = (int)$a[$i] * (int)$b[$j] + $result[$i + $j + 1];
                $result[$i + $j + 1] = $p % 10;
                $result[$i + $j] += intdiv( $p, 10 );
            }
        $text = ltrim( implode( '', $result ), '0' );
        return $text === '' ? '0' : $text;
    }

    /**
     * Decimal text of $a + $b, both non-negative decimal texts.
     *
     * @return string
     */
    public static function decimalAdd( $a, $b )
    {
        if ( function_exists( 'bcadd' ) )
            return bcadd( $a, $b, 0 );
        $a = strrev( $a );
        $b = strrev( $b );
        $out = '';
        $carry = 0;
        for ( $i = 0, $n = max( strlen( $a ), strlen( $b ) ); $i < $n; $i++ )
        {
            $d = ( $i < strlen( $a ) ? (int)$a[$i] : 0 ) + ( $i < strlen( $b ) ? (int)$b[$i] : 0 ) + $carry;
            $out .= $d % 10;
            $carry = intdiv( $d, 10 );
        }
        if ( $carry )
            $out .= $carry;
        $text = ltrim( strrev( $out ), '0' );
        return $text === '' ? '0' : $text;
    }

    /**
     * Integer digits of a DECIMAL with its scale as a decimal number (12345, 2 => 123.45).
     *
     * @param string $digits
     * @param int $scale
     * @return string
     */
    public static function decimalText( $digits, $scale )
    {
        if ( $scale <= 0 )
            return $digits;
        $negative = $digits !== '' && $digits[0] === '-';
        $digits = ltrim( $digits, '-' );
        $digits = str_pad( $digits, $scale + 1, '0', STR_PAD_LEFT );
        return ( $negative ? '-' : '' ) . substr( $digits, 0, -$scale ) . '.' . substr( $digits, -$scale );
    }
}
