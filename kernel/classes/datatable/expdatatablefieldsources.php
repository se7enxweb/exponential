<?php
/**
 * File containing the expDataTableFieldSources class and the kernel's own field sources.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The field sources of the module table wizard: the kernel's (a content class, an existing table on a connection) and
 * the kernel services tagged datatable.field_source an extension declares (expDataTableFieldSource).
 *
 * @since 6.0.15
 * @package kernel
 */
class expDataTableFieldSources
{
    /** The service tag */
    const TAG = 'datatable.field_source';

    /** The types a field can have */
    const TYPES = array( 'string', 'text', 'integer', 'float', 'boolean', 'date', 'datetime', 'json', 'object', 'node', 'user' );

    /**
     * Every source, id => expDataTableFieldSource.
     *
     * @return expDataTableFieldSource[]
     */
    public static function all()
    {
        $sources = array( 'class' => new expDataTableClassFieldSource(), 'table' => new expDataTableTableFieldSource() );
        if ( class_exists( 'expKernelServices' ) )
        {
            try
            {
                foreach ( expKernelServices::tagged( self::TAG ) as $id => $service )
                {
                    if ( $service instanceof expDataTableFieldSource )
                    {
                        $short = preg_replace( '/^datatable\.field_source\./', '', (string)$id );
                        if ( preg_match( '/^[a-z][a-z0-9_.]{0,63}$/', $short ) && !isset( $sources[$short] ) )
                            $sources[$short] = $service;
                    }
                }
            }
            catch ( Throwable $e )
            {
                eZDebug::writeWarning( 'datatable field sources: ' . $e->getMessage(), __METHOD__ );
            }
        }
        return $sources;
    }

    /**
     * One source, or null.
     *
     * @param string $id
     * @return expDataTableFieldSource|null
     */
    public static function get( $id )
    {
        $all = self::all();
        return isset( $all[(string)$id] ) ? $all[(string)$id] : null;
    }

    /**
     * A field with every key, its name made a column name (pure).
     *
     * @param array $field
     * @return array|null name, label, type, length, required, unique; null when there is no usable name
     */
    public static function normalizeField( array $field )
    {
        $name = isset( $field['name'] ) ? self::columnName( (string)$field['name'] ) : '';
        if ( $name === '' )
            return null;
        $type = isset( $field['type'] ) ? strtolower( trim( (string)$field['type'] ) ) : 'string';
        $aliases = array( 'int' => 'integer', 'bool' => 'boolean', 'double' => 'float', 'decimal' => 'float', 'number' => 'float',
                          'varchar' => 'string', 'char' => 'string', 'longtext' => 'text', 'timestamp' => 'datetime',
                          'object_relation' => 'object', 'node_relation' => 'node', 'user_relation' => 'user' );
        if ( isset( $aliases[$type] ) )
            $type = $aliases[$type];
        if ( !in_array( $type, self::TYPES, true ) )
            $type = 'string';
        // a length belongs to a string; 0 or nothing given is the usual 255 (a normalized field normalized again keeps it)
        $length = $type !== 'string' ? 0
                : ( isset( $field['length'] ) && is_numeric( $field['length'] ) && (int)$field['length'] !== 0 ? max( 1, min( 4000, (int)$field['length'] ) ) : 255 );
        return array( 'name' => $name, 'label' => isset( $field['label'] ) && trim( (string)$field['label'] ) !== '' ? mb_substr( trim( (string)$field['label'] ), 0, 100 ) : $name,
                      'type' => $type, 'length' => $length, 'required' => !empty( $field['required'] ), 'unique' => !empty( $field['unique'] ) );
    }

    /**
     * A column name from any text: lower case letters, digits and _, starting with a letter, at most 30 characters
     * (Oracle's limit) (pure).
     *
     * @param string $text
     * @return string
     */
    public static function columnName( $text )
    {
        $text = strtolower( (string)$text );
        if ( function_exists( 'iconv' ) )
        {
            $ascii = @iconv( 'UTF-8', 'ASCII//TRANSLIT', $text );
            if ( $ascii !== false )
                $text = strtolower( $ascii );
        }
        $text = trim( preg_replace( '/[^a-z0-9]+/', '_', $text ), '_' );
        if ( $text !== '' && !preg_match( '/^[a-z]/', $text ) )
            $text = 'f_' . $text;
        return substr( $text, 0, 30 );
    }

    /**
     * Fields from the wizard's text list, one per line (pure):
     *
     *   name type[(length)] [required] [unique]      type: string, text, integer, float, boolean, date, datetime,
     *                                                json, object, node, user
     *
     * @param string $text
     * @param string[] $errors
     * @return array list of fields
     */
    public static function parseList( $text, &$errors = array() )
    {
        $fields = array();
        foreach ( preg_split( '/\R/', (string)$text ) as $number => $line )
        {
            $line = trim( preg_replace( '/#.*$/', '', $line ) );
            if ( $line === '' )
                continue;
            $words = preg_split( '/[\s,]+/', $line );
            $name = array_shift( $words );
            $typeWord = $words ? strtolower( array_shift( $words ) ) : 'string';
            $length = null;
            if ( preg_match( '/^([a-z_]+)\((\d+)\)$/', $typeWord, $m ) )
            {
                $typeWord = $m[1];
                $length = (int)$m[2];
            }
            $flags = array_map( 'strtolower', $words );
            $field = self::normalizeField( array( 'name' => $name, 'type' => $typeWord, 'length' => $length,
                                                  'required' => in_array( 'required', $flags, true ), 'unique' => in_array( 'unique', $flags, true ) ) );
            if ( $field === null )
            {
                $errors[] = 'line ' . ( $number + 1 ) . ': no field name';
                continue;
            }
            if ( !in_array( $typeWord, self::TYPES, true ) && $field['type'] === 'string' && !in_array( $typeWord, array( 'string', 'varchar', 'char' ), true ) )
                $errors[] = 'line ' . ( $number + 1 ) . ": unknown type '$typeWord', string is used";
            $fields[] = $field;
        }
        return $fields;
    }

    /**
     * The fields as the wizard's text list (pure).
     *
     * @param array $fields
     * @return string
     */
    public static function toList( array $fields )
    {
        $lines = array();
        foreach ( $fields as $field )
        {
            $line = $field['name'] . ' ' . $field['type'] . ( $field['type'] === 'string' && $field['length'] && $field['length'] !== 255 ? '(' . $field['length'] . ')' : '' );
            if ( $field['required'] )
                $line .= ' required';
            if ( $field['unique'] )
                $line .= ' unique';
            $lines[] = $line;
        }
        return implode( "\n", $lines );
    }
}

/**
 * The fields of a content class: one column per attribute, typed by its datatype, and the columns that tie a row to
 * its object (contentobject_id, unique), its main node and its remote id.
 *
 * @since 6.0.15
 * @package kernel
 */
class expDataTableClassFieldSource implements expDataTableFieldSource
{
    /** datatype => field type and length */
    const DATATYPES = array(
        'ezstring' => array( 'string', 255 ), 'ezemail' => array( 'string', 255 ), 'ezisbn' => array( 'string', 32 ),
        'ezurl' => array( 'string', 1000 ), 'ezkeyword' => array( 'string', 1000 ), 'eztags' => array( 'string', 1000 ),
        'ezselection' => array( 'string', 255 ), 'ezcountry' => array( 'string', 255 ), 'ezidentifier' => array( 'string', 64 ),
        'eztext' => array( 'text', 0 ), 'ezxmltext' => array( 'text', 0 ), 'ezrichtext' => array( 'text', 0 ), 'ezmatrix' => array( 'json', 0 ),
        'ezinteger' => array( 'integer', 0 ), 'ezfloat' => array( 'float', 0 ), 'ezprice' => array( 'float', 0 ),
        'ezboolean' => array( 'boolean', 0 ), 'ezdate' => array( 'date', 0 ), 'ezdatetime' => array( 'datetime', 0 ),
        'eztime' => array( 'integer', 0 ), 'ezobjectrelation' => array( 'object', 0 ), 'ezobjectrelationlist' => array( 'json', 0 ),
        'ezimage' => array( 'string', 1000 ), 'ezbinaryfile' => array( 'string', 1000 ), 'ezmedia' => array( 'string', 1000 ),
        'ezgmaplocation' => array( 'json', 0 ), 'ezuser' => array( 'string', 255 ), 'ezauthor' => array( 'json', 0 ),
    );

    public function dataTableSourceName()
    {
        return ezpI18n::tr( 'design/admin/datatable', 'A content class' );
    }

    public function dataTableReferences()
    {
        $refs = array();
        foreach ( (array)eZContentClass::fetchList( eZContentClass::VERSION_STATUS_DEFINED, true ) as $class )
            $refs[(string)$class->attribute( 'identifier' )] = (string)$class->attribute( 'name' ) . ' (' . $class->attribute( 'identifier' ) . ')';
        asort( $refs );
        return $refs;
    }

    public function dataTableFields( array $options )
    {
        $identifier = isset( $options['ref'] ) ? (string)$options['ref'] : '';
        $class = $identifier !== '' ? eZContentClass::fetchByIdentifier( $identifier ) : null;
        if ( !$class instanceof eZContentClass )
            throw new RuntimeException( "There is no content class '$identifier'." );
        $fields = array(
            array( 'name' => 'contentobject_id', 'label' => 'Object', 'type' => 'object', 'required' => true, 'unique' => true ),
            array( 'name' => 'node_id', 'label' => 'Main node', 'type' => 'node' ),
            array( 'name' => 'remote_id', 'label' => 'Remote ID', 'type' => 'string', 'length' => 100 ),
            array( 'name' => 'name', 'label' => 'Name', 'type' => 'string', 'length' => 255 ),
            array( 'name' => 'published', 'label' => 'Published', 'type' => 'datetime' ),
            array( 'name' => 'modified', 'label' => 'Modified', 'type' => 'datetime' ),
        );
        foreach ( $class->fetchAttributes() as $attribute )
        {
            $datatype = (string)$attribute->attribute( 'data_type_string' );
            $type = isset( self::DATATYPES[$datatype] ) ? self::DATATYPES[$datatype] : array( 'text', 0 );
            $name = (string)$attribute->attribute( 'identifier' );
            if ( in_array( $name, array( 'id', 'contentobject_id', 'node_id', 'remote_id', 'name', 'published', 'modified' ), true ) )
                $name = 'attr_' . $name;
            $fields[] = array( 'name' => $name, 'label' => (string)$attribute->attribute( 'name' ), 'type' => $type[0], 'length' => $type[1],
                               'required' => (bool)$attribute->attribute( 'is_required' ) && !in_array( $type[0], array( 'text', 'json' ), true ) );
        }
        return $fields;
    }

    public function dataTableSourceLabel( array $options )
    {
        return 'the content class ' . ( isset( $options['ref'] ) ? (string)$options['ref'] : '' );
    }
}

/**
 * The fields of an existing table on a connection (the columns as the database describes them).
 *
 * @since 6.0.15
 * @package kernel
 */
class expDataTableTableFieldSource implements expDataTableFieldSource
{
    public function dataTableSourceName()
    {
        return ezpI18n::tr( 'design/admin/datatable', 'An existing table (connection:table)' );
    }

    public function dataTableReferences()
    {
        return array();
    }

    public function dataTableFields( array $options )
    {
        $ref = isset( $options['ref'] ) ? (string)$options['ref'] : '';
        list( $connection, $table ) = strpos( $ref, ':' ) !== false ? explode( ':', $ref, 2 ) : array( 'site', $ref );
        if ( !preg_match( '/^[A-Za-z_][A-Za-z0-9_]{0,127}$/', $table ) )
            throw new RuntimeException( 'Give the table as connection:table, e.g. site:my_table.' );
        $db = expDataConnections::db( $connection );
        if ( expKernelTableSchema::exists( $table, $db, true ) !== true )
            throw new RuntimeException( "There is no table $table on the connection $connection." );
        $fields = array();
        foreach ( expDataTableDescriber::columns( $db, $table ) as $name => $column )
        {
            if ( $name === '_id' )
                continue;
            $type = $column['type'];
            $kind = preg_match( '/int|serial|bit|bool/', $type ) ? 'integer' : ( preg_match( '/float|double|real|numeric|decimal|number/', $type ) ? 'float'
                  : ( preg_match( '/text|clob|blob|json/', $type ) ? 'text' : 'string' ) );
            $fields[] = array( 'name' => $name, 'type' => $kind, 'required' => !$column['nullable'] );
        }
        return $fields;
    }

    public function dataTableSourceLabel( array $options )
    {
        return 'the table ' . ( isset( $options['ref'] ) ? (string)$options['ref'] : '' );
    }
}
?>
