<?php
/**
 * File containing the expDataTableCatalogue class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The catalogue of data tables (doc/eng-us/features/6.0/data-tables.md, "The catalogue"): every persistent class of
 * the installation (an eZPersistentObject subclass with its own definition(), in the kernel's or the extensions'
 * autoload array), described from its definition(): table, fields (type, default, required, short name), keys,
 * increment key, sort, function attributes, the connection it uses, and its relations to content and to other tables.
 *
 *   $catalogue = expDataTableCatalogue::instance();
 *   $catalogue->classes();                     // class => entry
 *   $catalogue->entry( 'eZViewCounter' );      // one entry, or null
 *   $catalogue->byTable( 'ezview_counter' );   // the entries of a table
 *
 * Relations come from, in this order: the definition's 'relations' key (column => class, field), a field's
 * foreign_class / foreign_attribute, datatable.ini [Relation_<class>_<column>], and, with [DataTableSettings]
 * InferRelations=enabled, the column's name (contentobject_id, node_id, ...), marked inferred.
 *
 * Finding the classes reads the autoload arrays and only loads the files that declare a definition() (no file is
 * loaded to find that out); a class whose definition() fails is listed with the error. The result is kept in the
 * cache directory and made again when an autoload array changes.
 *
 * @since 6.0.15
 * @package kernel
 */
class expDataTableCatalogue
{
    /** The format of the cache file */
    const CACHE_VERSION = 3;

    /** The kinds of relation target */
    const KINDS = array( 'object', 'node', 'class', 'user', 'section', 'table' );

    /** @var expDataTableCatalogue|null */
    protected static $instance = null;

    /** @var array class => entry */
    protected $entries;

    /**
     * @param array $entries class => entry
     */
    public function __construct( array $entries )
    {
        ksort( $entries, SORT_STRING | SORT_FLAG_CASE );
        $this->entries = $entries;
    }

    /**
     * The catalogue of this installation (from the cache when it is current).
     *
     * @param bool $rebuild make it again
     * @return expDataTableCatalogue
     */
    public static function instance( $rebuild = false )
    {
        if ( self::$instance !== null && !$rebuild )
            return self::$instance;
        $arrays = self::autoloadArrays();
        $stamp = self::stamp( $arrays );
        $file = self::cacheFile();
        if ( !$rebuild && is_file( $file ) )
        {
            $data = @json_decode( (string)file_get_contents( $file ), true );
            if ( is_array( $data ) && isset( $data['version'], $data['stamp'], $data['entries'] ) && $data['version'] === self::CACHE_VERSION
                 && $data['stamp'] === $stamp && is_array( $data['entries'] ) )
                return self::$instance = new self( $data['entries'] );
        }
        $entries = self::discover( $arrays );
        $dir = dirname( $file );
        if ( is_dir( $dir ) || @mkdir( $dir, eZDir::dirMode( 0775 ), true ) )
        {
            $temp = $file . '.' . getmypid() . '.tmp';
            if ( @file_put_contents( $temp, json_encode( array( 'version' => self::CACHE_VERSION, 'stamp' => $stamp, 'entries' => $entries ),
                                                           JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_PARTIAL_OUTPUT_ON_ERROR ) ) !== false )
                @rename( $temp, $file );
        }
        return self::$instance = new self( $entries );
    }

    /** Forgets the catalogue of this process (tests; after the autoload arrays were written). */
    public static function reset()
    {
        self::$instance = null;
    }

    /**
     * Tests: a catalogue of the given entries for this process (null forgets it).
     *
     * @param expDataTableCatalogue|null $catalogue
     */
    public static function setInstance( $catalogue )
    {
        self::$instance = $catalogue instanceof self ? $catalogue : null;
    }

    /**
     * The catalogue with one more entry (a class made after the catalogue was built), for this process.
     *
     * @param array $entry
     * @return expDataTableCatalogue
     */
    public function with( array $entry )
    {
        $entries = $this->entries;
        $entries[$entry['class']] = $entry;
        return new self( $entries );
    }

    /** @return array class => entry */
    public function classes()
    {
        return $this->entries;
    }

    /**
     * @param string $class
     * @return array|null
     */
    public function entry( $class )
    {
        if ( isset( $this->entries[$class] ) )
            return $this->entries[$class];
        foreach ( $this->entries as $name => $entry )
            if ( strcasecmp( $name, (string)$class ) === 0 )
                return $entry;
        return null;
    }

    /**
     * The entries of a table (a table can have more than one class).
     *
     * @param string $table
     * @return array class => entry
     */
    public function byTable( $table )
    {
        $found = array();
        foreach ( $this->entries as $class => $entry )
            if ( $entry['table'] === (string)$table )
                $found[$class] = $entry;
        return $found;
    }

    /**
     * The entries grouped by where they come from: kernel, then each extension.
     *
     * @return array origin => class => entry
     */
    public function byOrigin()
    {
        $groups = array();
        foreach ( $this->entries as $class => $entry )
            $groups[$entry['origin']][$class] = $entry;
        uksort( $groups, function ( $a, $b ) {
            if ( $a === $b )
                return 0;
            if ( $a === 'kernel' )
                return -1;
            if ( $b === 'kernel' )
                return 1;
            return strcasecmp( $a, $b );
        } );
        return $groups;
    }

    /**
     * The entries whose relations point at a kind of target (object, node, ...), with the columns that do.
     *
     * @param string $kind
     * @return array class => list of relation
     */
    public function pointingAt( $kind )
    {
        $found = array();
        foreach ( $this->entries as $class => $entry )
            foreach ( $entry['relations'] as $relation )
                if ( $relation['kind'] === $kind )
                    $found[$class][] = $relation;
        return $found;
    }

    /**
     * An entry from a class's definition (pure: no database).
     *
     * @param string $class
     * @param array $definition
     * @param string $file
     * @param string $origin kernel or the extension's name
     * @param bool $inferRelations
     * @param array $settingRelations column => array( kind, class, field ) from datatable.ini
     * @return array class, table, file, origin, fields, keys, increment_key, sort, function_attributes, connection,
     *               relations, error
     */
    public static function entryFromDefinition( $class, array $definition, $file = '', $origin = 'kernel', $inferRelations = true,
                                                array $settingRelations = array() )
    {
        $fields = array();
        foreach ( isset( $definition['fields'] ) && is_array( $definition['fields'] ) ? $definition['fields'] : array() as $name => $field )
        {
            if ( !is_string( $name ) )
                continue;
            if ( !is_array( $field ) )
                $field = array( 'name' => (string)$field );
            $fields[$name] = array(
                'name' => $name,
                'label' => isset( $field['name'] ) ? (string)$field['name'] : $name,
                'datatype' => isset( $field['datatype'] ) ? (string)$field['datatype'] : 'string',
                'default' => isset( $field['default'] ) && is_scalar( $field['default'] ) ? $field['default'] : null,
                'required' => !empty( $field['required'] ),
                'short_name' => isset( $field['short_name'] ) ? (string)$field['short_name'] : '',
                'foreign_class' => isset( $field['foreign_class'] ) ? (string)$field['foreign_class'] : '',
                'foreign_attribute' => isset( $field['foreign_attribute'] ) ? (string)$field['foreign_attribute'] : '',
                'multiplicity' => isset( $field['multiplicity'] ) ? (string)$field['multiplicity'] : '',
            );
        }
        $keys = isset( $definition['keys'] ) ? array_values( array_map( 'strval', (array)$definition['keys'] ) ) : array();
        $functionAttributes = isset( $definition['function_attributes'] ) && is_array( $definition['function_attributes'] )
                            ? array_values( array_map( 'strval', array_keys( $definition['function_attributes'] ) ) ) : array();
        $sort = array();
        foreach ( isset( $definition['sort'] ) && is_array( $definition['sort'] ) ? $definition['sort'] : array() as $field => $direction )
            $sort[(string)$field] = is_string( $direction ) ? strtolower( $direction ) : 'asc';

        $relations = array();
        $add = function ( $column, $kind, $targetClass, $targetField, $source ) use ( &$relations, $fields ) {
            if ( !isset( $fields[$column] ) || isset( $relations[$column] ) )
                return;
            $relations[$column] = array( 'column' => $column, 'kind' => $kind, 'class' => $targetClass, 'field' => $targetField, 'source' => $source );
        };
        foreach ( isset( $definition['relations'] ) && is_array( $definition['relations'] ) ? $definition['relations'] : array() as $column => $relation )
        {
            if ( !is_string( $column ) || !is_array( $relation ) )
                continue;
            $targetClass = isset( $relation['class'] ) ? (string)$relation['class'] : '';
            $kind = isset( $relation['kind'] ) && in_array( $relation['kind'], self::KINDS, true ) ? $relation['kind'] : self::kindOfClass( $targetClass );
            $add( $column, $kind, $targetClass, isset( $relation['field'] ) ? (string)$relation['field'] : 'id', 'declared' );
        }
        foreach ( $fields as $name => $field )
        {
            if ( $field['foreign_class'] !== '' )
                $add( $name, self::kindOfClass( $field['foreign_class'] ), $field['foreign_class'], $field['foreign_attribute'] !== '' ? $field['foreign_attribute'] : 'id', 'foreign' );
        }
        foreach ( $settingRelations as $column => $relation )
        {
            $kind = isset( $relation['kind'] ) && in_array( $relation['kind'], self::KINDS, true ) ? $relation['kind'] : self::kindOfClass( isset( $relation['class'] ) ? $relation['class'] : '' );
            $add( (string)$column, $kind, isset( $relation['class'] ) ? (string)$relation['class'] : '', isset( $relation['field'] ) ? (string)$relation['field'] : 'id', 'settings' );
        }
        if ( $inferRelations )
        {
            foreach ( array_keys( $fields ) as $name )
            {
                $guess = self::inferRelation( $name );
                // a table's own key is not a relation to something else
                if ( $guess !== null && !( $keys === array( $name ) && isset( $definition['increment_key'] ) && $definition['increment_key'] === $name ) )
                    $add( $name, $guess['kind'], $guess['class'], $guess['field'], 'inferred' );
            }
        }
        $connection = '';
        if ( isset( $definition['connection'] ) && is_string( $definition['connection'] ) )
            $connection = $definition['connection'] === expDataConnections::SITE ? '' : $definition['connection'];
        return array(
            'class' => (string)$class,
            'table' => isset( $definition['name'] ) ? (string)$definition['name'] : '',
            'file' => (string)$file,
            'origin' => (string)$origin,
            'fields' => $fields,
            'keys' => $keys,
            'increment_key' => isset( $definition['increment_key'] ) ? (string)$definition['increment_key'] : '',
            'sort' => $sort,
            'function_attributes' => $functionAttributes,
            'connection' => $connection,
            'relations' => array_values( $relations ),
            'error' => '',
        );
    }

    /**
     * The relation a column's name suggests, or null (pure).
     *
     * @param string $column
     * @return array|null kind, class, field
     */
    public static function inferRelation( $column )
    {
        $column = strtolower( (string)$column );
        $rules = array(
            '/^(creator|modifier|owner|author|editor)_(contentobject_id|object_id)$/' => array( 'user', 'eZUser', 'contentobject_id' ),
            '/^(contentobject_id|content_object_id|object_id|from_contentobject_id|to_contentobject_id|.+_object_id|.+_contentobject_id)$/' => array( 'object', 'eZContentObject', 'id' ),
            '/^(node_id|main_node_id|parent_node_id|.+_node_id)$/' => array( 'node', 'eZContentObjectTreeNode', 'node_id' ),
            '/^(contentclass_id|content_class_id|class_id)$/' => array( 'class', 'eZContentClass', 'id' ),
            '/^(user_id|creator_id|owner_id|modifier_id)$/' => array( 'user', 'eZUser', 'contentobject_id' ),
            '/^section_id$/' => array( 'section', 'eZSection', 'id' ),
        );
        foreach ( $rules as $pattern => $target )
            if ( preg_match( $pattern, $column ) )
                return array( 'kind' => $target[0], 'class' => $target[1], 'field' => $target[2] );
        return null;
    }

    /**
     * The kind of a relation's target class (pure).
     *
     * @param string $class
     * @return string
     */
    public static function kindOfClass( $class )
    {
        switch ( strtolower( (string)$class ) )
        {
            case 'ezcontentobject':
                return 'object';
            case 'ezcontentobjecttreenode':
                return 'node';
            case 'ezcontentclass':
                return 'class';
            case 'ezuser':
                return 'user';
            case 'ezsection':
                return 'section';
        }
        return 'table';
    }

    /**
     * Whether a file declares a definition() (read as text; nothing is loaded).
     *
     * @param string $source
     * @return bool
     */
    public static function declaresDefinition( $source )
    {
        return stripos( (string)$source, 'function definition' ) !== false && stripos( (string)$source, 'extends' ) !== false;
    }

    /**
     * Finds the classes (loads the candidate files).
     *
     * @param array $arrays origin => array( class => file )
     * @return array class => entry
     */
    protected static function discover( array $arrays )
    {
        $ignore = array();
        $infer = true;
        try
        {
            $ini = eZINI::instance( 'datatable.ini' );
            if ( $ini->hasVariable( 'DataTableSettings', 'IgnoreClasses' ) )
                $ignore = array_flip( array_filter( (array)$ini->variable( 'DataTableSettings', 'IgnoreClasses' ), 'strlen' ) );
            if ( $ini->hasVariable( 'DataTableSettings', 'InferRelations' ) )
                $infer = strtolower( (string)$ini->variable( 'DataTableSettings', 'InferRelations' ) ) !== 'disabled';
        }
        catch ( Throwable $e )
        {
        }
        $settingRelations = self::settingRelations();
        $root = rtrim( eZSys::rootDir(), '/' ) . '/';
        $entries = array();
        $seenFiles = array();
        foreach ( $arrays as $kind => $classes )
        {
            foreach ( $classes as $class => $file )
            {
                if ( !is_string( $class ) || !is_string( $file ) || isset( $ignore[$class] ) || isset( $entries[$class] ) )
                    continue;
                // tests and namespaced view classes are not persistent classes
                if ( strpos( $class, '\\' ) !== false || strpos( $file, 'tests/' ) !== false )
                    continue;
                $path = $file[0] === '/' ? $file : $root . $file;
                if ( !isset( $seenFiles[$path] ) )
                {
                    $source = is_readable( $path ) ? (string)@file_get_contents( $path ) : '';
                    $seenFiles[$path] = self::declaresDefinition( $source ) && stripos( $source, 'class ' . $class ) !== false ? $source : false;
                }
                if ( $seenFiles[$path] === false || !preg_match( '/class\s+' . preg_quote( $class, '/' ) . '\s+extends\s/i', $seenFiles[$path] ) )
                    continue;
                $origin = preg_match( '#(?:^|/)extension/([^/]+)/#', $file, $match ) ? $match[1] : 'kernel';
                $entry = self::describeClass( $class, $file, $origin, $infer, isset( $settingRelations[$class] ) ? $settingRelations[$class] : array() );
                if ( $entry !== null )
                    $entries[$class] = $entry;
            }
        }
        return $entries;
    }

    /**
     * One class's entry, or null when it is not a persistent class with its own table.
     *
     * @param string $class
     * @param string $file
     * @param string $origin
     * @param bool $infer
     * @param array $settingRelations
     * @return array|null
     */
    protected static function describeClass( $class, $file, $origin, $infer, array $settingRelations )
    {
        try
        {
            if ( !class_exists( $class ) )
                return null;
            $reflection = new ReflectionClass( $class );
            if ( $reflection->isAbstract() || !$reflection->isSubclassOf( 'eZPersistentObject' ) )
                return null;
            $method = $reflection->getMethod( 'definition' );
            // a class that inherits its parent's definition() is the parent's table, already listed
            if ( $method->getDeclaringClass()->getName() !== $reflection->getName() )
                return null;
            $definition = $method->isStatic() ? $class::definition() : $reflection->newInstanceWithoutConstructor()->definition();
            if ( !is_array( $definition ) || empty( $definition['name'] ) || empty( $definition['fields'] ) )
                return null;
            $entry = self::entryFromDefinition( $class, $definition, $file, $origin, $infer, $settingRelations );
            $entry['connection'] = expDataConnections::connectionOf( $definition );
            return $entry;
        }
        catch ( Throwable $e )
        {
            return array( 'class' => $class, 'table' => '', 'file' => $file, 'origin' => $origin, 'fields' => array(), 'keys' => array(),
                          'increment_key' => '', 'sort' => array(), 'function_attributes' => array(), 'connection' => '',
                          'relations' => array(), 'error' => mb_substr( $e->getMessage(), 0, 300 ) );
        }
    }

    /**
     * The relations of datatable.ini [Relation_<class>_<column>].
     *
     * @return array class => column => array( kind, class, field )
     */
    protected static function settingRelations()
    {
        $relations = array();
        try
        {
            $ini = eZINI::instance( 'datatable.ini' );
            foreach ( $ini->groups() as $group => $values )
            {
                if ( strncmp( $group, 'Relation_', 9 ) !== 0 || !is_array( $values ) )
                    continue;
                // the class name has no _ in practice; the column may: split at the first _ after the class
                if ( !preg_match( '/^Relation_([A-Za-z][A-Za-z0-9]*)_([A-Za-z0-9_]+)$/', $group, $match ) )
                    continue;
                $relations[$match[1]][$match[2]] = array(
                    'kind' => isset( $values['Kind'] ) ? strtolower( trim( (string)$values['Kind'] ) ) : '',
                    'class' => isset( $values['Class'] ) ? trim( (string)$values['Class'] ) : '',
                    'field' => isset( $values['Field'] ) ? trim( (string)$values['Field'] ) : 'id' );
            }
        }
        catch ( Throwable $e )
        {
        }
        return $relations;
    }

    /** @return array kernel|extension => class => file */
    protected static function autoloadArrays()
    {
        $root = rtrim( eZSys::rootDir(), '/' ) . '/';
        $arrays = array();
        foreach ( array( 'kernel' => 'autoload/ezp_kernel.php', 'extension' => 'var/autoload/ezp_extension.php' ) as $kind => $file )
        {
            $array = is_file( $root . $file ) ? @include $root . $file : array();
            $arrays[$kind] = is_array( $array ) ? $array : array();
        }
        return $arrays;
    }

    /**
     * @param array $arrays
     * @return string
     */
    protected static function stamp( array $arrays )
    {
        $root = rtrim( eZSys::rootDir(), '/' ) . '/';
        $parts = array( count( $arrays['kernel'] ), count( $arrays['extension'] ) );
        foreach ( array( 'autoload/ezp_kernel.php', 'var/autoload/ezp_extension.php', 'settings/datatable.ini' ) as $file )
            $parts[] = is_file( $root . $file ) ? filemtime( $root . $file ) : 0;
        return md5( implode( ':', $parts ) );
    }

    /** @return string */
    protected static function cacheFile()
    {
        $dir = 'datatable';
        try
        {
            $ini = eZINI::instance( 'datatable.ini' );
            if ( $ini->hasVariable( 'DataTableSettings', 'CatalogueCacheDir' ) && preg_match( '/^[A-Za-z0-9_-]+$/', (string)$ini->variable( 'DataTableSettings', 'CatalogueCacheDir' ) ) )
                $dir = (string)$ini->variable( 'DataTableSettings', 'CatalogueCacheDir' );
        }
        catch ( Throwable $e )
        {
        }
        return eZSys::cacheDirectory() . '/' . $dir . '/catalogue.json';
    }
}
?>
