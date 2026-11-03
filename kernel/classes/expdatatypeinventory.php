<?php
/**
 * File containing the expDataTypeInventory class.
 *
 * The datatypes of the installation, for class/datatype: every datatype content.ini registers (the kernel's and
 * each extension's), who provides it, its PHP class and file and its flags, how much the content model uses it
 * (class attributes, classes, object attributes and objects of the current published versions) and the datatypes the
 * database still uses but nothing registers any more (they break the content that has them).
 *
 * The counts are two grouped queries (ezcontentclass_attribute, ezcontentobject_attribute joined to
 * ezcontentobject), the same SQL on SQLite, MySQL, PostgreSQL and Oracle and an aggregation pipeline on MongoDB. They
 * are kept for CACHE_TTL seconds in one file of the cache directory, since the second one reads every object
 * attribute. build(), filter() and the other static functions without a database are pure: the tests feed them a
 * registry and counts of their own.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expDataTypeInventory
{
    /** How long the counts are kept, in seconds */
    const CACHE_TTL = 300;

    /** The group of the kernel's datatypes, and the one of the datatypes nothing registers */
    const GROUP_KERNEL = 'kernel';
    const GROUP_ORPHANS = 'orphans';

    /** The filters of the list (view parameter filter) */
    const FILTERS = array( 'all', 'used', 'unused', 'orphans' );

    /** The sorts of the content list of a datatype (view parameter sort), the first the default, and its page sizes */
    const NODE_SORTS = array( 'modified', 'published', 'name', 'class' );
    const NODE_PAGE_SIZES = array( 10, 25, 50, 100 );
    const NODE_DEFAULT_LIMIT = 25;

    /** The settings columns of a class attribute */
    const SETTING_COLUMNS = array( 'data_int1', 'data_int2', 'data_int3', 'data_int4', 'data_float1', 'data_float2',
                                   'data_float3', 'data_float4', 'data_text1', 'data_text2', 'data_text3', 'data_text4',
                                   'data_text5' );

    /** Tables a datatype's code may name that are the content model's own, not the datatype's storage */
    const MODEL_TABLES = array( 'ezcontentclass_attribute', 'ezcontentobject_attribute', 'ezcontentobject',
                                'ezcontentobject_version', 'ezcontentobject_tree', 'ezcontentclass' );

    // --------------------------------------------------------------------------------------------------------
    // Registry

    /**
     * Every datatype content.ini lists, located the way eZDataType::loadAndRegisterType() does and described.
     *
     * @return array datatype string => row (see describe())
     */
    public static function registry()
    {
        $ini = eZINI::instance( 'content.ini' );
        $available = array_values( array_unique( (array)$ini->variable( 'DataTypeSettings', 'AvailableDataTypes' ) ) );
        $directories = self::directories( (array)$ini->variable( 'DataTypeSettings', 'RepositoryDirectories' ),
                                          array_values( array_unique( (array)$ini->variable( 'DataTypeSettings', 'ExtensionDirectories' ) ) ) );
        $registry = array();
        foreach ( $available as $string )
        {
            $string = (string)$string;
            if ( $string === '' )
                continue;
            $located = self::locate( $string, $directories, function ( $path ) { return file_exists( $path ); } );
            $className = false;
            if ( $located )
            {
                eZDataType::loadAndRegisterType( $string );
                $className = isset( $GLOBALS['eZDataTypes'][$string] ) ? $GLOBALS['eZDataTypes'][$string] : false;
            }
            $object = false;
            if ( $className && class_exists( $className ) )
            {
                try
                {
                    $object = eZDataType::create( $string );
                }
                catch ( \Throwable $e )
                {
                    eZDebug::writeWarning( "Datatype $string could not be created: " . $e->getMessage(), __METHOD__ );
                }
            }
            $registry[$string] = self::describe( $string, $located, $className, $object ? $object : null );
        }
        return $registry;
    }

    /**
     * The directories datatypes are looked for in, in eZDataType::loadAndRegisterType()'s order, each with its
     * provider: "kernel", or the extension's name.
     *
     * @param array $repositoryDirectories [DataTypeSettings] RepositoryDirectories
     * @param array $extensionDirectories [DataTypeSettings] ExtensionDirectories
     * @return array of hash( dir, provider )
     */
    public static function directories( array $repositoryDirectories, array $extensionDirectories )
    {
        $list = array();
        foreach ( $repositoryDirectories as $dir )
            $list[] = array( 'dir' => rtrim( (string)$dir, '/' ), 'provider' => self::providerOfPath( (string)$dir ) );
        foreach ( $extensionDirectories as $extension )
        {
            $base = class_exists( 'eZExtension' ) ? eZExtension::extensionPath( $extension ) : 'extension/' . $extension;
            if ( $base === false )
                continue;
            $list[] = array( 'dir' => $base . '/datatypes', 'provider' => (string)$extension );
        }
        $seen = array();
        $unique = array();
        foreach ( $list as $item )
        {
            if ( isset( $seen[$item['dir']] ) )
                continue;
            $seen[$item['dir']] = true;
            $unique[] = $item;
        }
        return $unique;
    }

    /**
     * The file of a datatype, <dir>/<string>/<string>type.php in the first directory that has it.
     *
     * @param string $string
     * @param array $directories directories()
     * @param callable $exists function( $path ): bool
     * @return array|false hash( file, provider )
     */
    public static function locate( $string, array $directories, $exists )
    {
        foreach ( $directories as $item )
        {
            $file = $item['dir'] . '/' . $string . '/' . $string . 'type.php';
            if ( $exists( $file ) )
                return array( 'file' => $file, 'provider' => $item['provider'] );
        }
        return false;
    }

    /**
     * The provider of a path: the extension it is in, else the kernel.
     *
     * @param string $path relative to the installation, or absolute
     * @return string
     */
    public static function providerOfPath( $path )
    {
        $path = str_replace( '\\', '/', (string)$path );
        if ( preg_match( '#(?:^|/)extension/([^/]+)/#', $path, $m ) )
            return $m[1];
        return self::GROUP_KERNEL;
    }

    /**
     * One row of the registry.
     *
     * @param string $string the datatype string
     * @param array|false $located locate()
     * @param string|false $className the class it registered
     * @param eZDataType|null $object
     * @return array
     */
    public static function describe( $string, $located, $className, $object = null )
    {
        $file = $located ? $located['file'] : '';
        if ( $className && class_exists( $className ) )
        {
            $reflected = ( new ReflectionClass( $className ) )->getFileName();
            if ( $reflected )
                $file = self::relativePath( $reflected );
        }
        $row = array( 'string' => (string)$string, 'name' => '', 'class' => $className ? (string)$className : '',
                      'file' => $file, 'provider' => $located ? $located['provider'] : '', 'registered' => (bool)$located,
                      'created' => $object !== null, 'flags' => self::flagsOf( $object ) );
        if ( $row['provider'] !== '' && $file !== '' )
            $row['provider'] = self::providerOfPath( $file );
        if ( $object !== null )
            $row['name'] = (string)$object->Name;
        return $row;
    }

    /**
     * What a datatype says of itself.
     *
     * @param eZDataType|null $object
     * @return array translatable, indexable, information_collector, sortable (sort key type or ''), relation,
     *               string_insertion, file_insertion, serialize
     */
    public static function flagsOf( $object )
    {
        $flags = array( 'translatable' => false, 'indexable' => false, 'information_collector' => false, 'sortable' => '',
                        'relation' => false, 'string_insertion' => false, 'file_insertion' => false, 'serialize' => false );
        if ( !is_object( $object ) )
            return $flags;
        $ask = function ( $method ) use ( $object )
        {
            try
            {
                return method_exists( $object, $method ) ? $object->$method() : false;
            }
            catch ( \Throwable $e )
            {
                return false;
            }
        };
        $flags['translatable'] = (bool)$ask( 'isTranslatable' );
        $flags['indexable'] = (bool)$ask( 'isIndexable' );
        $flags['information_collector'] = (bool)$ask( 'isInformationCollector' );
        $sort = $ask( 'sortKeyType' );
        $flags['sortable'] = is_string( $sort ) && $sort !== '' ? $sort : '';
        $flags['relation'] = (bool)$ask( 'isRelationType' );
        $flags['string_insertion'] = (bool)$ask( 'isSimpleStringInsertionSupported' );
        $flags['file_insertion'] = (bool)$ask( 'isHTTPFileInsertionSupported' ) || (bool)$ask( 'isRegularFileInsertionSupported' );
        $flags['serialize'] = isset( $object->Attributes['properties']['serialize_supported'] ) && (bool)$object->Attributes['properties']['serialize_supported'];
        return $flags;
    }

    /**
     * @param string $path
     * @return string relative to the installation where it is below it
     */
    public static function relativePath( $path )
    {
        $path = str_replace( '\\', '/', (string)$path );
        $root = rtrim( str_replace( '\\', '/', class_exists( 'eZSys' ) ? (string)eZSys::rootDir() : (string)getcwd() ), '/' ) . '/';
        if ( $root !== '/' && strpos( $path, $root ) === 0 )
            return substr( $path, strlen( $root ) );
        return $path;
    }

    // --------------------------------------------------------------------------------------------------------
    // Counts

    /**
     * The usage counts, from the cache when it is younger than CACHE_TTL.
     *
     * @param bool $refresh count again
     * @return array hash( class => classUsage(), object => objectUsage(), counted => timestamp )
     */
    public static function usage( $refresh = false )
    {
        return self::cached( 'usage', $refresh, function ()
        {
            $db = eZDB::instance();
            return array( 'class' => self::classUsage( $db ), 'object' => self::objectUsage( $db ) );
        } );
    }

    /**
     * The object attributes and objects of each class attribute, from the cache when it is younger than CACHE_TTL.
     *
     * @param bool $refresh
     * @return array hash( attributes => attributeUsage(), counted => timestamp )
     */
    public static function attributeUsageCached( $refresh = false )
    {
        return self::cached( 'attributes', $refresh, function ()
        {
            return array( 'attributes' => self::attributeUsage( eZDB::instance() ) );
        } );
    }

    /**
     * @param string $name
     * @param bool $refresh
     * @param callable $count
     * @return array the counted data with counted => timestamp
     */
    protected static function cached( $name, $refresh, $count, $extraKey = '' )
    {
        $db = eZDB::instance();
        $key = md5( $db->databaseName() . '|' . ( isset( $db->DB ) && is_string( $db->DB ) ? $db->DB : '' ) . '|' . $extraKey );
        $file = eZSys::cacheDirectory() . '/class-datatype-' . $name . '-' . substr( $key, 0, 12 ) . '.json';
        if ( !$refresh )
        {
            $handler = eZClusterFileHandler::instance( $file );
            if ( $handler->exists() && $handler->mtime() > time() - self::CACHE_TTL )
            {
                $data = json_decode( (string)$handler->fetchContents(), true );
                if ( is_array( $data ) && isset( $data['counted'] ) )
                    return $data;
            }
        }
        $data = $count();
        $data['counted'] = time();
        try
        {
            eZClusterFileHandler::instance( $file )->storeContents( json_encode( $data ), 'classdatatype', 'json' );
        }
        catch ( \Throwable $e )
        {
            eZDebug::writeWarning( 'The datatype counts could not be cached: ' . $e->getMessage(), __METHOD__ );
        }
        return $data;
    }

    /**
     * Class attributes and classes per datatype, of the defined classes. One query.
     *
     * @param eZDBInterface $db
     * @return array datatype string => hash( class_attributes, classes )
     */
    public static function classUsage( $db )
    {
        if ( $db->databaseName() === 'mongo' )
        {
            $rows = $db->aggregate( 'ezcontentclass_attribute', array(
                array( '$match' => array( 'version' => (int)eZContentClass::VERSION_STATUS_DEFINED ) ),
                array( '$group' => array( '_id' => '$data_type_string', 'attribute_count' => array( '$sum' => 1 ),
                                          'class_ids' => array( '$addToSet' => '$contentclass_id' ) ) ),
                array( '$project' => array( 'data_type_string' => '$_id', 'attribute_count' => 1,
                                            'class_count' => array( '$size' => '$class_ids' ) ) ) ) );
        }
        else
        {
            $rows = $db->arrayQuery( 'SELECT data_type_string, COUNT(*) AS attribute_count, COUNT(DISTINCT contentclass_id) AS class_count'
                                   . ' FROM ezcontentclass_attribute WHERE version = ' . (int)eZContentClass::VERSION_STATUS_DEFINED
                                   . ' GROUP BY data_type_string' );
        }
        $usage = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            $row = array_change_key_case( (array)$row, CASE_LOWER );
            $string = (string)( isset( $row['data_type_string'] ) ? $row['data_type_string'] : '' );
            $usage[$string] = array( 'class_attributes' => (int)$row['attribute_count'], 'classes' => (int)$row['class_count'] );
        }
        ksort( $usage, SORT_STRING );
        return $usage;
    }

    /**
     * Object attributes (every language) and objects per datatype, of the current version of every published
     * object. One query.
     *
     * @param eZDBInterface $db
     * @return array datatype string => hash( object_attributes, objects )
     */
    public static function objectUsage( $db )
    {
        $rows = self::objectAttributeRows( $db, 'data_type_string' );
        $usage = array();
        foreach ( $rows as $row )
            $usage[(string)$row['key']] = array( 'object_attributes' => $row['attribute_count'], 'objects' => $row['object_count'] );
        ksort( $usage, SORT_STRING );
        return $usage;
    }

    /**
     * Object attributes and objects per class attribute, of the current version of every published object. One query.
     *
     * @param eZDBInterface $db
     * @return array class attribute id => hash( object_attributes, objects )
     */
    public static function attributeUsage( $db )
    {
        $usage = array();
        foreach ( self::objectAttributeRows( $db, 'contentclassattribute_id' ) as $row )
            $usage[(int)$row['key']] = array( 'object_attributes' => $row['attribute_count'], 'objects' => $row['object_count'] );
        return $usage;
    }

    /**
     * @param eZDBInterface $db
     * @param string $column data_type_string or contentclassattribute_id
     * @return array of hash( key, attribute_count, object_count )
     */
    protected static function objectAttributeRows( $db, $column )
    {
        $published = (int)eZContentObject::STATUS_PUBLISHED;
        if ( $db->databaseName() === 'mongo' )
        {
            $rows = $db->aggregate( 'ezcontentobject_attribute', array(
                array( '$lookup' => array( 'from' => 'ezcontentobject', 'localField' => 'contentobject_id', 'foreignField' => 'id', 'as' => 'o' ) ),
                array( '$unwind' => '$o' ),
                array( '$match' => array( '$expr' => array( '$and' => array( array( '$eq' => array( '$version', '$o.current_version' ) ),
                                                                              array( '$eq' => array( '$o.status', $published ) ) ) ) ) ),
                array( '$group' => array( '_id' => '$' . $column, 'attribute_count' => array( '$sum' => 1 ),
                                          'object_ids' => array( '$addToSet' => '$contentobject_id' ) ) ),
                array( '$project' => array( 'group_key' => '$_id', 'attribute_count' => 1, 'object_count' => array( '$size' => '$object_ids' ) ) ) ) );
        }
        else
        {
            $rows = $db->arrayQuery( 'SELECT a.' . $column . ' AS group_key, COUNT(*) AS attribute_count, COUNT(DISTINCT a.contentobject_id) AS object_count'
                                   . ' FROM ezcontentobject_attribute a, ezcontentobject o'
                                   . ' WHERE o.id = a.contentobject_id AND a.version = o.current_version AND o.status = ' . $published
                                   . ' GROUP BY a.' . $column );
        }
        $list = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            $row = array_change_key_case( (array)$row, CASE_LOWER );
            $list[] = array( 'key' => isset( $row['group_key'] ) ? $row['group_key'] : '', 'attribute_count' => (int)$row['attribute_count'],
                             'object_count' => (int)$row['object_count'] );
        }
        return $list;
    }

    // --------------------------------------------------------------------------------------------------------
    // The list

    /**
     * The list of the page: one row per datatype, registered or found in the database, in groups (the kernel, each
     * extension by name, then the datatypes nothing registers), and the figures over all of them.
     *
     * @param array $registry registry()
     * @param array $classUsage classUsage()
     * @param array $objectUsage objectUsage()
     * @return array hash( groups => group key => hash( key, kind, label, rows, used, unused, orphans ), summary )
     */
    public static function build( array $registry, array $classUsage, array $objectUsage )
    {
        $rows = array();
        foreach ( $registry as $string => $info )
            $rows[(string)$string] = self::row( $info, $classUsage, $objectUsage );
        foreach ( array_merge( array_keys( $classUsage ), array_keys( $objectUsage ) ) as $string )
        {
            $string = (string)$string;
            if ( isset( $rows[$string] ) )
                continue;
            $rows[$string] = self::row( array( 'string' => $string, 'name' => '', 'class' => '', 'file' => '', 'provider' => '',
                                               'registered' => false, 'created' => false, 'flags' => self::flagsOf( null ),
                                               'listed' => false ), $classUsage, $objectUsage );
        }
        $groups = array();
        foreach ( $rows as $row )
        {
            $key = $row['status'] === 'orphan' ? self::GROUP_ORPHANS : ( $row['provider'] !== '' ? $row['provider'] : self::GROUP_ORPHANS );
            if ( !isset( $groups[$key] ) )
                $groups[$key] = array( 'key' => $key, 'kind' => $key === self::GROUP_KERNEL ? 'kernel' : ( $key === self::GROUP_ORPHANS ? 'orphans' : 'extension' ),
                                       'rows' => array(), 'used' => 0, 'unused' => 0, 'orphans' => 0 );
            $groups[$key]['rows'][] = $row;
            if ( $row['status'] === 'used' )
                $groups[$key]['used']++;
            elseif ( $row['status'] === 'unused' )
                $groups[$key]['unused']++;
            else
                $groups[$key]['orphans']++;
        }
        uksort( $groups, function ( $a, $b )
        {
            $rank = function ( $k ) { return $k === expDataTypeInventory::GROUP_KERNEL ? 0 : ( $k === expDataTypeInventory::GROUP_ORPHANS ? 2 : 1 ); };
            return $rank( $a ) - $rank( $b ) ?: strcasecmp( $a, $b );
        } );
        foreach ( $groups as &$group )
        {
            usort( $group['rows'], function ( $a, $b ) { return strcmp( $a['string'], $b['string'] ); } );
            $group['count'] = count( $group['rows'] );
        }
        unset( $group );
        return array( 'groups' => $groups, 'summary' => self::summary( $rows ) );
    }

    /**
     * @param array $info a registry row
     * @param array $classUsage
     * @param array $objectUsage
     * @return array the row with its counts, status (used, unused, orphan, missing) and search text
     */
    public static function row( array $info, array $classUsage, array $objectUsage )
    {
        $string = (string)$info['string'];
        $c = isset( $classUsage[$string] ) ? $classUsage[$string] : array( 'class_attributes' => 0, 'classes' => 0 );
        $o = isset( $objectUsage[$string] ) ? $objectUsage[$string] : array( 'object_attributes' => 0, 'objects' => 0 );
        $info += array( 'listed' => true, 'name' => '', 'class' => '', 'file' => '', 'provider' => '', 'registered' => false,
                        'created' => false, 'flags' => self::flagsOf( null ) );
        $used = $c['class_attributes'] > 0 || $o['object_attributes'] > 0;
        if ( !$info['listed'] )
            $status = 'orphan';
        elseif ( !$info['registered'] || !$info['created'] )
            $status = $used ? 'orphan' : 'missing';
        else
            $status = $used ? 'used' : 'unused';
        $info['class_attributes'] = (int)$c['class_attributes'];
        $info['classes'] = (int)$c['classes'];
        $info['object_attributes'] = (int)$o['object_attributes'];
        $info['objects'] = (int)$o['objects'];
        $info['status'] = $status;
        $info['used'] = $used;
        $info['search'] = mb_strtolower( implode( ' ', array( $string, $info['name'], $info['class'], $info['provider'], $info['file'] ) ) );
        return $info;
    }

    /**
     * @param array $rows rows by datatype string
     * @return array registered, used, unused, orphans, missing, class_attributes, object_attributes, extensions
     */
    public static function summary( array $rows )
    {
        $summary = array( 'datatypes' => count( $rows ), 'registered' => 0, 'used' => 0, 'unused' => 0, 'orphans' => 0, 'missing' => 0,
                          'class_attributes' => 0, 'object_attributes' => 0, 'extensions' => 0, 'orphan_class_attributes' => 0,
                          'orphan_object_attributes' => 0 );
        $extensions = array();
        foreach ( $rows as $row )
        {
            if ( $row['status'] === 'used' || $row['status'] === 'unused' )
                $summary['registered']++;
            if ( $row['status'] === 'orphan' )
            {
                $summary['orphans']++;
                $summary['orphan_class_attributes'] += $row['class_attributes'];
                $summary['orphan_object_attributes'] += $row['object_attributes'];
            }
            elseif ( isset( $summary[$row['status']] ) )
            {
                $summary[$row['status']]++;
            }
            $summary['class_attributes'] += $row['class_attributes'];
            $summary['object_attributes'] += $row['object_attributes'];
            if ( $row['provider'] !== '' && $row['provider'] !== self::GROUP_KERNEL && $row['status'] !== 'orphan' )
                $extensions[$row['provider']] = true;
        }
        $summary['extensions'] = count( $extensions );
        return $summary;
    }

    /**
     * The groups with only the rows of a filter and a group.
     *
     * @param array $groups build()['groups']
     * @param string $filter all, used, unused, orphans
     * @param string $group '' for every group, a group key
     * @return array
     */
    public static function filter( array $groups, $filter, $group = '' )
    {
        $filter = in_array( $filter, self::FILTERS, true ) ? $filter : 'all';
        $result = array();
        foreach ( $groups as $key => $info )
        {
            if ( $group !== '' && (string)$key !== (string)$group )
                continue;
            $info['rows'] = array_values( array_filter( $info['rows'], function ( $row ) use ( $filter )
            {
                switch ( $filter )
                {
                    case 'used': return $row['status'] === 'used';
                    case 'unused': return $row['status'] === 'unused' || $row['status'] === 'missing';
                    case 'orphans': return $row['status'] === 'orphan' || $row['status'] === 'missing';
                }
                return true;
            } ) );
            $info['shown'] = count( $info['rows'] );
            if ( $info['shown'] )
                $result[$key] = $info;
        }
        return $result;
    }

    // --------------------------------------------------------------------------------------------------------
    // One datatype

    /**
     * The settings columns and attribute columns a datatype's class names (a heuristic, the same as the generated
     * help pages use), and the tables its code names.
     *
     * @param string $source the PHP source of the datatype's class (and its directory)
     * @return array hash( settings => columns, content => columns, tables => names )
     */
    public static function readSource( $source )
    {
        preg_match_all( "/['\"](data_(?:text|int|float)\d?|sort_key_(?:int|string))['\"]/", (string)$source, $f );
        $fields = array_values( array_unique( $f[1] ) );
        $settings = array_values( array_intersect( self::SETTING_COLUMNS, $fields ) );
        $content = array_values( array_diff( $fields, self::SETTING_COLUMNS ) );
        sort( $content, SORT_STRING );
        preg_match_all( '/\b(?:FROM|INTO|UPDATE|JOIN)\s+([a-z][a-z0-9_]+)/i', (string)$source, $t1 );
        preg_match_all( "/['\"]name['\"]\s*=>\s*['\"]([a-z][a-z0-9_]+)['\"]/", (string)$source, $t2 );
        $tables = array();
        foreach ( array_merge( $t1[1], $t2[1] ) as $table )
        {
            $table = strtolower( $table );
            if ( preg_match( '/^(?:ez|exp|eztags|cjw|xrow|ng|sck|sevenx)[a-z0-9_]*$/', $table ) && !in_array( $table, self::MODEL_TABLES, true ) )
                $tables[$table] = true;
        }
        $tables = array_keys( $tables );
        sort( $tables, SORT_STRING );
        return array( 'settings' => $settings, 'content' => $content, 'tables' => $tables );
    }

    /**
     * The PHP source of a datatype: its class file and the other PHP files of its directory.
     *
     * @param string $file the class file, relative to the installation
     * @return string
     */
    public static function sourceOf( $file )
    {
        if ( $file === '' || !is_file( $file ) )
            return '';
        $source = (string)file_get_contents( $file );
        foreach ( (array)glob( dirname( $file ) . '/*.php' ) as $other )
            if ( $other !== $file && is_file( $other ) && filesize( $other ) < 500000 )
                $source .= "\n" . file_get_contents( $other );
        return $source;
    }

    /**
     * The templates of a datatype in a siteaccess's designs (or the current one), by kind.
     *
     * @param string $string
     * @param string|false $siteAccess
     * @return array kind => hash( template, file ) (file false where no design has it)
     */
    public static function templates( $string, $siteAccess = false )
    {
        $bases = eZTemplateDesignResource::allDesignBases( $siteAccess );
        $list = array();
        foreach ( array( 'view' => 'content/datatype/view/', 'edit' => 'content/datatype/edit/', 'collect' => 'content/datatype/collect/',
                         'result' => 'content/datatype/result/info/', 'class_view' => 'class/datatype/view/', 'class_edit' => 'class/datatype/edit/' ) as $kind => $dir )
        {
            $tried = array();
            $match = eZTemplateDesignResource::fileMatch( $bases, 'templates', $dir . $string . '.tpl', $tried );
            $list[$kind] = array( 'template' => $dir . $string . '.tpl', 'file' => $match ? $match['path'] : false );
        }
        return $list;
    }

    /**
     * The defined class attributes of one datatype, with their class and the non-empty settings columns.
     *
     * @param string $string
     * @param array $attributeUsage attributeUsage()
     * @param array $settingColumns the columns to show (readSource()['settings'], or every one)
     * @return array hash( attributes => list, classes => class id => hash )
     */
    public static function classAttributes( $string, array $attributeUsage, array $settingColumns )
    {
        $attributes = eZContentClassAttribute::fetchFilteredList( array( 'data_type_string' => (string)$string,
                                                                         'version' => eZContentClass::VERSION_STATUS_DEFINED ), true );
        $list = array();
        $classes = array();
        foreach ( is_array( $attributes ) ? $attributes : array() as $attribute )
        {
            $id = (int)$attribute->attribute( 'id' );
            $classID = (int)$attribute->attribute( 'contentclass_id' );
            if ( !isset( $classes[$classID] ) )
            {
                $class = eZContentClass::fetch( $classID );
                if ( !$class )
                    continue;
                $classes[$classID] = array( 'id' => $classID, 'name' => (string)$class->attribute( 'name' ),
                                            'identifier' => (string)$class->attribute( 'identifier' ),
                                            'attributes' => 0, 'object_attributes' => 0, 'objects' => 0 );
            }
            $settings = array();
            foreach ( $settingColumns ? $settingColumns : self::SETTING_COLUMNS as $column )
            {
                $value = $attribute->attribute( $column );
                if ( $value !== null && $value !== '' && $value !== 0 && $value !== '0' && $value !== 0.0 )
                    $settings[$column] = mb_strlen( (string)$value ) > 120 ? mb_substr( (string)$value, 0, 120 ) . '...' : (string)$value;
            }
            $use = isset( $attributeUsage[$id] ) ? $attributeUsage[$id] : array( 'object_attributes' => 0, 'objects' => 0 );
            $list[] = array( 'id' => $id, 'identifier' => (string)$attribute->attribute( 'identifier' ),
                             'name' => (string)$attribute->attribute( 'name' ), 'class_id' => $classID,
                             'class_name' => $classes[$classID]['name'], 'class_identifier' => $classes[$classID]['identifier'],
                             'required' => (bool)$attribute->attribute( 'is_required' ),
                             'searchable' => (bool)$attribute->attribute( 'is_searchable' ),
                             'translatable' => (bool)$attribute->attribute( 'can_translate' ),
                             'information_collector' => (bool)$attribute->attribute( 'is_information_collector' ),
                             'settings' => $settings, 'object_attributes' => $use['object_attributes'], 'objects' => $use['objects'] );
            $classes[$classID]['attributes']++;
            $classes[$classID]['object_attributes'] += $use['object_attributes'];
            $classes[$classID]['objects'] = max( $classes[$classID]['objects'], $use['objects'] );
        }
        usort( $list, function ( $a, $b ) { return strcasecmp( $a['class_name'], $b['class_name'] ) ?: strcmp( $a['identifier'], $b['identifier'] ); } );
        uasort( $classes, function ( $a, $b ) { return strcasecmp( $a['name'], $b['name'] ); } );
        return array( 'attributes' => $list, 'classes' => $classes );
    }

    // --------------------------------------------------------------------------------------------------------
    // The content using a datatype (the paged list of the detail page)

    /**
     * The view parameters of the content list, checked: sort (NODE_SORTS), order (asc|desc; the default is desc for
     * the dates and asc for name and class), offset, limit (NODE_PAGE_SIZES) and class (one of the classes that use
     * the datatype, 0 for all of them).
     *
     * @param array $userParameters the view's UserParameters
     * @param int[] $classIDs the classes that use the datatype
     * @return array hash( sort, order, offset, limit, class )
     */
    public static function nodeParameters( array $userParameters, array $classIDs )
    {
        $sort = isset( $userParameters['sort'] ) ? (string)$userParameters['sort'] : '';
        if ( !in_array( $sort, self::NODE_SORTS, true ) )
            $sort = self::NODE_SORTS[0];
        $order = isset( $userParameters['order'] ) ? strtolower( (string)$userParameters['order'] ) : '';
        if ( $order !== 'asc' && $order !== 'desc' )
            $order = self::defaultNodeOrder( $sort );
        $limit = isset( $userParameters['limit'] ) && ctype_digit( (string)$userParameters['limit'] ) ? (int)$userParameters['limit'] : 0;
        if ( !in_array( $limit, self::NODE_PAGE_SIZES, true ) )
            $limit = self::NODE_DEFAULT_LIMIT;
        $offset = isset( $userParameters['offset'] ) && ctype_digit( (string)$userParameters['offset'] ) ? (int)$userParameters['offset'] : 0;
        $class = isset( $userParameters['class'] ) && ctype_digit( (string)$userParameters['class'] ) ? (int)$userParameters['class'] : 0;
        if ( !in_array( $class, array_map( 'intval', $classIDs ), true ) )
            $class = 0;
        return array( 'sort' => $sort, 'order' => $order, 'offset' => min( $offset, 10000000 ), 'limit' => $limit, 'class' => $class );
    }

    /**
     * @param string $sort
     * @return string the order a column starts with: newest first for the dates, A to Z for the names
     */
    public static function defaultNodeOrder( $sort )
    {
        return $sort === 'name' || $sort === 'class' ? 'asc' : 'desc';
    }

    /**
     * The view parameters of the content list that differ from the defaults, without the offset (the pager adds it).
     *
     * @param array $params nodeParameters()
     * @param array $overrides keys of $params to change
     * @return array name => value, in the order sort, order, limit, class
     */
    public static function nodeViewParameters( array $params, array $overrides = array() )
    {
        $p = array_merge( $params, $overrides );
        $out = array();
        if ( $p['sort'] !== self::NODE_SORTS[0] || $p['order'] !== self::defaultNodeOrder( $p['sort'] ) )
        {
            $out['sort'] = $p['sort'];
            $out['order'] = $p['order'];
        }
        if ( (int)$p['limit'] !== self::NODE_DEFAULT_LIMIT )
            $out['limit'] = (int)$p['limit'];
        if ( (int)$p['class'] > 0 )
            $out['class'] = (int)$p['class'];
        return $out;
    }

    /**
     * The address of the detail page with the content list's view parameters ("/(name)/value" each).
     *
     * @param string $string the datatype string
     * @param array $params nodeParameters()
     * @param array $overrides
     * @return string module-relative
     */
    public static function nodeListURL( $string, array $params, array $overrides = array() )
    {
        $url = 'class/datatype/view/' . $string;
        foreach ( self::nodeViewParameters( $params, $overrides ) as $name => $value )
            $url .= '/(' . $name . ')/' . $value;
        $offset = array_key_exists( 'offset', $overrides ) ? (int)$overrides['offset'] : 0;
        return $url . ( $offset > 0 ? '/(offset)/' . $offset : '' );
    }

    /**
     * The links of the sortable column heads: the same filter and page size, from the first page, that column, the
     * other order when it is the current one.
     *
     * @param string $string
     * @param array $params nodeParameters()
     * @return array sort => hash( url, current, order (of the link) )
     */
    public static function nodeSortLinks( $string, array $params )
    {
        $out = array();
        foreach ( self::NODE_SORTS as $sort )
        {
            $current = $params['sort'] === $sort;
            $order = $current ? ( $params['order'] === 'asc' ? 'desc' : 'asc' ) : self::defaultNodeOrder( $sort );
            $out[$sort] = array( 'url' => self::nodeListURL( $string, $params, array( 'sort' => $sort, 'order' => $order ) ),
                                 'current' => $current, 'order' => $order );
        }
        return $out;
    }

    /**
     * The parameters of the tree fetch of the content list: the main nodes of the objects of the classes, below the
     * top node, with the read check of the current user (or the limitation given), sorted, one page.
     *
     * @param int[] $classIDs
     * @param array $params nodeParameters()
     * @param array|null $limitation null for the current user's, array() for none (the tests)
     * @return array
     */
    public static function nodeFetchParameters( array $classIDs, array $params, $limitation = null )
    {
        $classes = $params['class'] > 0 ? array( (int)$params['class'] ) : array_values( array_map( 'intval', $classIDs ) );
        $asc = $params['order'] === 'asc';
        $sortBy = array( 'modified' => array( array( 'modified', $asc ), array( 'node_id', $asc ) ),
                         'published' => array( array( 'published', $asc ), array( 'node_id', $asc ) ),
                         'name' => array( array( 'name', $asc ), array( 'node_id', true ) ),
                         'class' => array( array( 'class_name', $asc ), array( 'name', true ) ) );
        $fetch = array( 'ClassFilterType' => 'include', 'ClassFilterArray' => $classes, 'MainNodeOnly' => true,
                        'SortBy' => $sortBy[$params['sort']], 'Offset' => (int)$params['offset'], 'Limit' => (int)$params['limit'],
                        'LoadDataMap' => false );
        if ( $limitation !== null )
            $fetch['Limitation'] = $limitation;
        return $fetch;
    }

    /**
     * One page of the content using a datatype, as the current user may read it, and how much there is. The count
     * is kept CACHE_TTL seconds per classes and read policy; an offset past the end goes to the last page.
     *
     * @param int[] $classIDs the classes that use the datatype
     * @param array $params nodeParameters()
     * @param bool $refresh count again
     * @param array|null $limitation see nodeFetchParameters()
     * @return array hash( rows => nodeRows(), total, offset )
     */
    public static function nodePage( array $classIDs, array $params, $refresh = false, $limitation = null )
    {
        if ( !$classIDs )
            return array( 'rows' => array(), 'total' => 0, 'offset' => 0 );
        $fetch = self::nodeFetchParameters( $classIDs, $params, $limitation );
        $count = $fetch;
        unset( $count['SortBy'], $count['Offset'], $count['Limit'] );
        if ( $limitation === null )
        {
            $access = eZUser::currentUser()->hasAccessTo( 'content', 'read' );
            $key = md5( json_encode( $count['ClassFilterArray'] ) . '|' . json_encode( $access ) );
            $counted = self::cached( 'nodes', $refresh, function () use ( $count )
            {
                return array( 'total' => (int)eZContentObjectTreeNode::subTreeCountByNodeID( $count, 1 ) );
            }, $key );
            $total = (int)$counted['total'];
        }
        else
            $total = (int)eZContentObjectTreeNode::subTreeCountByNodeID( $count, 1 );
        if ( $total > 0 && $fetch['Offset'] >= $total )
            $fetch['Offset'] = (int)( floor( ( $total - 1 ) / $fetch['Limit'] ) * $fetch['Limit'] );
        $nodes = $total > 0 ? eZContentObjectTreeNode::subTreeByNodeID( $fetch, 1 ) : array();
        return array( 'rows' => self::nodeRows( is_array( $nodes ) ? $nodes : array() ), 'total' => $total, 'offset' => $total > 0 ? $fetch['Offset'] : 0 );
    }

    /**
     * The rows of the content list: name, class, section, languages (every translation), owner and the dates of each node.
     *
     * @param eZContentObjectTreeNode[] $nodes
     * @return array
     */
    public static function nodeRows( array $nodes )
    {
        $sections = array();
        foreach ( (array)eZSection::fetchList( false ) as $section )
            $sections[(int)$section['id']] = (string)$section['name'];
        $owners = array();
        $rows = array();
        foreach ( $nodes as $node )
        {
            $object = $node->attribute( 'object' );
            if ( !$object )
                continue;
            $ownerID = (int)$object->attribute( 'owner_id' );
            if ( !array_key_exists( $ownerID, $owners ) )
            {
                $owner = $ownerID > 0 ? eZContentObject::fetch( $ownerID ) : null;
                $owners[$ownerID] = $owner ? array( 'name' => (string)$owner->attribute( 'name' ), 'node_id' => (int)$owner->attribute( 'main_node_id' ) )
                                           : array( 'name' => '', 'node_id' => 0 );
            }
            $sectionID = (int)$object->attribute( 'section_id' );
            $rows[] = array( 'node_id' => (int)$node->attribute( 'node_id' ), 'name' => (string)$node->attribute( 'name' ),
                             'url_alias' => (string)$node->attribute( 'url_alias' ),
                             'class_name' => (string)$node->attribute( 'class_name' ), 'class_id' => (int)$object->attribute( 'contentclass_id' ),
                             'section_id' => $sectionID, 'section_name' => isset( $sections[$sectionID] ) ? $sections[$sectionID] : '',
                             'languages' => array_values( array_map( function ( $language ) { return (string)$language->attribute( 'locale' ); }, (array)$object->allLanguages() ) ),
                             'owner_id' => $ownerID, 'owner_name' => $owners[$ownerID]['name'], 'owner_node_id' => $owners[$ownerID]['node_id'],
                             'modified' => (int)$object->attribute( 'modified' ), 'published' => (int)$object->attribute( 'published' ) );
        }
        return $rows;
    }

    /**
     * The help page of a datatype, when the help has one.
     *
     * @param string $string
     * @return string|false the page id (below doc/eng-us/)
     */
    public static function helpPage( $string )
    {
        $page = 'examples/datatypes/' . $string . '.md';
        return preg_match( '/^[a-z0-9_]+$/', (string)$string ) && is_file( 'doc/eng-us/' . $page ) ? $page : false;
    }

    /**
     * The tables of a list that the database has. The drivers list only the tables whose name starts with "ez", so
     * the others are kept as they are; so is the whole list where the database cannot tell.
     *
     * @param array $tables
     * @return array
     */
    public static function existingTables( array $tables )
    {
        if ( !$tables )
            return array();
        try
        {
            $db = eZDB::instance();
            $all = method_exists( $db, 'eZTableList' ) ? $db->eZTableList() : false;
        }
        catch ( \Throwable $e )
        {
            $all = false;
        }
        if ( !is_array( $all ) || !$all )
            return $tables;
        $names = array_flip( array_map( 'strtolower', array_keys( $all ) ) );
        return array_values( array_filter( $tables, function ( $t ) use ( $names ) { return strpos( $t, 'ez' ) !== 0 || isset( $names[$t] ); } ) );
    }
}
