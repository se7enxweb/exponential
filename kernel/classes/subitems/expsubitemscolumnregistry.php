<?php
/**
 * The registry of the admin subitems list columns ("Table options").
 *
 * Reads settings/subitems.ini ([SubitemsSettings], [Preset_*], [Defaults_*]) and
 * settings/subitemscolumns.ini ([Column_<key>] blocks), adds the 15 built-in columns and the
 * automatic attribute columns (attr:<class>/<attribute>) of the classes present among a
 * parent's children, and answers which columns a user may see under a parent, which are
 * shown by default there, the INI presets, and how a column key sorts.
 *
 * A column block names its implementation with one of: Builtin=true, Class=<subclass of
 * expSubitemsColumn>, Handler=class::method (expSubitemsCallableColumn) or
 * Template=design:subitems/columns/<x>.tpl (expSubitemsTemplateColumn). Every name comes from
 * the INI; nothing from a request is ever used as a class, function or template name, and
 * request keys are only looked up in the registry.
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsColumnRegistry
{
    /** The most column keys one request may name. */
    const MAX_KEYS = 100;

    /** @var array [SubitemsSettings] */
    protected $settings = array();
    /** @var array key => settings: the built-in and the INI columns */
    protected $definitions = array();
    /** @var array id => array( 'name' => ..., 'columns' => array() ) */
    protected $presets = array();
    /** @var array list of [Defaults_*] blocks: array( 'id', 'subtree' => int[], 'classes' => string[], 'columns' => string[] ) */
    protected $defaultBlocks = array();
    /** @var array key => expSubitemsColumn|false */
    protected $instances = array();
    /** @var array parent node id => attr key => expSubitemsAttributeColumn */
    protected $attributeColumnCache = array();

    /** @var expSubitemsColumnRegistry|null */
    protected static $instance = null;
    /** @var callable|null fn( $module, $function ): bool, replaces eZUser::hasAccessTo() (tests) */
    protected static $accessChecker = null;

    /**
     * @param eZINI|null $subitemsIni subitems.ini; null: eZINI::instance( 'subitems.ini' )
     * @param eZINI|null $columnsIni subitemscolumns.ini; null: eZINI::instance( 'subitemscolumns.ini' )
     */
    public function __construct( ?eZINI $subitemsIni = null, ?eZINI $columnsIni = null )
    {
        if ( $subitemsIni === null )
            $subitemsIni = eZINI::instance( 'subitems.ini' );
        if ( $columnsIni === null )
            $columnsIni = eZINI::instance( 'subitemscolumns.ini' );
        $this->load( $subitemsIni->groups(), $columnsIni->groups() );
    }

    /**
     * The registry of the current request.
     *
     * @return expSubitemsColumnRegistry
     */
    public static function instance()
    {
        if ( self::$instance === null )
            self::$instance = new self();
        return self::$instance;
    }

    /**
     * Forgets the request's registry (after INI changes, between Velocity requests, in tests).
     */
    public static function resetInstance()
    {
        self::$instance = null;
    }

    /**
     * Replaces the policy check (tests). null restores eZUser::currentUser()->hasAccessTo().
     *
     * @param callable|null $checker fn( string $module, string $function ): bool
     */
    public static function setAccessChecker( $checker )
    {
        self::$accessChecker = is_callable( $checker ) ? $checker : null;
    }

    /**
     * Whether the current user has module/function (any limitation counts as granted; the rows
     * themselves are filtered by content/read).
     *
     * @param string $module
     * @param string $function
     * @return bool
     */
    public static function hasAccess( $module, $function )
    {
        if ( self::$accessChecker !== null )
            return (bool)call_user_func( self::$accessChecker, $module, $function );
        $access = eZUser::currentUser()->hasAccessTo( $module, $function );
        return isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
    }

    /**
     * Fills the registry from the two INI files' groups.
     *
     * @param array $subitemsGroups subitems.ini groups()
     * @param array $columnGroups subitemscolumns.ini groups()
     */
    protected function load( array $subitemsGroups, array $columnGroups )
    {
        $this->settings = $subitemsGroups['SubitemsSettings'] ?? array();

        foreach ( expSubitemsBuiltinColumn::definitions() as $key => $defaults )
            $this->definitions[$key] = $defaults + array( 'Builtin' => 'true' );

        foreach ( $columnGroups as $group => $block )
        {
            if ( strpos( $group, 'Column_' ) === 0 )
                $this->loadColumnBlock( substr( $group, strlen( 'Column_' ) ), $block );
        }

        foreach ( $subitemsGroups as $group => $block )
        {
            if ( strpos( $group, 'Preset_' ) === 0 )
                $this->loadPresetBlock( substr( $group, strlen( 'Preset_' ) ), $block );
            else if ( strpos( $group, 'Defaults_' ) === 0 )
                $this->loadDefaultsBlock( substr( $group, strlen( 'Defaults_' ) ), $block );
        }
    }

    /** A [Column_<key>] block: a new column, or changes to a built-in one (which stays built in). */
    protected function loadColumnBlock( $key, array $block )
    {
        if ( !self::isValidDefinedKey( $key ) )
        {
            eZDebug::writeWarning( "subitemscolumns.ini: [Column_$key] is not a valid column key (a-z, 0-9, _)", __METHOD__ );
            return;
        }
        $block = self::normaliseBlock( $block );
        if ( $this->isBuiltin( $key ) )
        {
            // a built-in stays built in, the INI changes its name, group, order ...
            $block['Builtin'] = 'true';
            unset( $block['Class'], $block['Handler'], $block['Template'] );
            $this->definitions[$key] = array_merge( $this->definitions[$key], $block );
        }
        else
        {
            $this->definitions[$key] = $block;
        }
    }

    /** A [Preset_<id>] block: Name=, Columns[]. */
    protected function loadPresetBlock( $id, array $block )
    {
        if ( !self::isValidPresetID( $id ) )
            return;
        $this->presets[$id] = array(
            'name' => isset( $block['Name'] ) && $block['Name'] !== '' ? (string)$block['Name'] : $id,
            'columns' => self::splitList( $block['Columns'] ?? array() ),
        );
    }

    /** A [Defaults_<id>] block: Subtree[], ParentClassIdentifiers[], Columns[]. */
    protected function loadDefaultsBlock( $id, array $block )
    {
        $this->defaultBlocks[] = array(
            'id' => $id,
            'subtree' => array_map( 'intval', self::splitList( $block['Subtree'] ?? array() ) ),
            'classes' => self::splitList( $block['ParentClassIdentifiers'] ?? array() ),
            'columns' => self::splitList( $block['Columns'] ?? array() ),
        );
    }

    /**
     * An INI block with Policy as a list.
     */
    protected static function normaliseBlock( array $block )
    {
        $block['Policy'] = self::splitList( $block['Policy'] ?? array() );
        return $block;
    }

    /**
     * A list setting: an array, or one string with ; or , between the items. Empty items and
     * duplicates are dropped.
     *
     * @param mixed $value
     * @return array
     */
    public static function splitList( $value )
    {
        if ( $value === null || $value === false )
            return array();
        $items = array();
        foreach ( (array)$value as $part )
        {
            foreach ( preg_split( '/[;,]/', (string)$part ) as $item )
            {
                $item = trim( $item );
                if ( $item !== '' && !in_array( $item, $items, true ) )
                    $items[] = $item;
            }
        }
        return $items;
    }

    /** Whether an INI value means yes. */
    public static function isTrue( $value )
    {
        return in_array( strtolower( trim( (string)$value ) ), array( 'true', 'enabled', '1', 'yes' ), true );
    }

    /** A key a [Column_<key>] block may have. */
    public static function isValidDefinedKey( $key )
    {
        return is_string( $key ) && (bool)preg_match( '/^[a-z0-9_]{1,64}$/', $key );
    }

    /** A preset id (INI block suffix or a user preset). */
    public static function isValidPresetID( $id )
    {
        return is_string( $id ) && (bool)preg_match( '/^[a-z0-9_\-]{1,40}$/', $id );
    }

    /**
     * A [SubitemsSettings] value.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function setting( $name, $default = null )
    {
        return array_key_exists( $name, $this->settings ) ? $this->settings[$name] : $default;
    }

    /** Whether the automatic attribute columns are on. */
    public function attributeColumnsEnabled()
    {
        return self::isTrue( $this->setting( 'AttributeColumns', 'enabled' ) );
    }

    /** Whether the CSV export is on. */
    public function csvExportEnabled()
    {
        return self::isTrue( $this->setting( 'CSVExport', 'enabled' ) );
    }

    /** The most rows one CSV export writes. */
    public function csvLimit()
    {
        $limit = (int)$this->setting( 'CSVLimit', 5000 );
        return $limit > 0 ? $limit : 5000;
    }

    /** @return int[] the page sizes Table options offers */
    public function pageSizes()
    {
        $sizes = array();
        foreach ( self::splitList( $this->setting( 'PageSizes', array( 10, 25, 50, 100 ) ) ) as $size )
            if ( (int)$size > 0 )
                $sizes[] = (int)$size;
        return $sizes ? $sizes : array( 10, 25, 50, 100 );
    }

    /**
     * @return array key => settings of the built-in and INI columns (attribute columns are not in it)
     */
    public function definitions()
    {
        return $this->definitions;
    }

    /** Whether a key is one of the 15 built-in columns. */
    public function isBuiltin( $key )
    {
        return is_string( $key ) && isset( $this->definitions[$key] ) && self::isTrue( $this->definitions[$key]['Builtin'] ?? false );
    }

    /**
     * Whether a key is known: a built-in or INI column, or an attribute column of an existing class
     * attribute (attr:<class>/<attribute>).
     *
     * @param string $key
     * @return bool
     */
    public function isKnownKey( $key )
    {
        if ( !is_string( $key ) )
            return false;
        if ( isset( $this->definitions[$key] ) )
            return true;
        $parsed = expSubitemsAttributeColumn::parseKey( $key );
        return $parsed !== null && $this->attributeColumnsEnabled() && $this->classAttributeExists( $parsed[0], $parsed[1] );
    }

    /**
     * Whether a class attribute exists (overridable for tests).
     */
    protected function classAttributeExists( $classIdentifier, $attributeIdentifier )
    {
        $class = eZContentClass::fetchByIdentifier( $classIdentifier );
        return $class instanceof eZContentClass && $class->fetchAttributeByIdentifier( $attributeIdentifier ) instanceof eZContentClassAttribute;
    }

    /**
     * The column object for a built-in or INI key, without the policy check; null when the key is
     * unknown or its block is broken. Attribute columns come from attributeColumns() / column().
     *
     * @param string $key
     * @return expSubitemsColumn|null
     */
    public function definedColumn( $key )
    {
        if ( !is_string( $key ) || !isset( $this->definitions[$key] ) )
            return null;
        if ( !array_key_exists( $key, $this->instances ) )
            $this->instances[$key] = $this->createColumn( $key, $this->definitions[$key] );
        return $this->instances[$key] ?: null;
    }

    /**
     * Creates the column for an INI block.
     *
     * @param string $key
     * @param array $settings
     * @return expSubitemsColumn|false false when the block names nothing usable
     */
    protected function createColumn( $key, array $settings )
    {
        try
        {
            if ( self::isTrue( $settings['Builtin'] ?? false ) )
                return new expSubitemsBuiltinColumn( $key, $settings );

            if ( !empty( $settings['Class'] ) )
            {
                $class = (string)$settings['Class'];
                if ( preg_match( '/^[A-Za-z_\\\\][A-Za-z0-9_\\\\]*$/', $class ) && class_exists( $class )
                     && is_subclass_of( $class, 'expSubitemsColumn' ) )
                {
                    $reflection = new ReflectionClass( $class );
                    if ( $reflection->isInstantiable() )
                        return new $class( $key, $settings );
                }
                eZDebug::writeWarning( "Column '$key': Class=$class is not a subclass of expSubitemsColumn", __METHOD__ );
                return false;
            }

            if ( !empty( $settings['Handler'] ) )
                return new expSubitemsCallableColumn( $key, $settings );

            if ( !empty( $settings['Template'] ) )
                return new expSubitemsTemplateColumn( $key, $settings );
        }
        catch ( InvalidArgumentException $e )
        {
            eZDebug::writeWarning( $e->getMessage(), __METHOD__ );
            return false;
        }

        eZDebug::writeWarning( "Column '$key' names no Builtin, Class, Handler or Template", __METHOD__ );
        return false;
    }

    /**
     * The ids of the classes among the parent's children: one query.
     *
     * @param eZContentObjectTreeNode $parent
     * @return int[]
     */
    protected function childClassIDs( eZContentObjectTreeNode $parent )
    {
        $parentID = (int)$parent->attribute( 'node_id' );
        $db = eZDB::instance();
        $rows = false;
        if ( $db->databaseName() !== 'mongo' )
        {
            $rows = $db->arrayQuery(
                "SELECT DISTINCT ezcontentobject.contentclass_id AS class_id
                 FROM ezcontentobject_tree, ezcontentobject
                 WHERE ezcontentobject_tree.parent_node_id = $parentID
                   AND ezcontentobject_tree.contentobject_id = ezcontentobject.id"
            );
        }
        if ( !is_array( $rows ) )
        {
            // no SQL here (MongoDB): the class ids of the children, as rows
            $rows = array();
            $children = eZContentObjectTreeNode::subTreeByNodeID(
                array( 'Depth' => 1, 'DepthOperator' => 'eq', 'AsObject' => false, 'Limit' => 2000, 'Limitation' => array() ),
                $parentID
            );
            foreach ( (array)$children as $child )
                if ( isset( $child['contentclass_id'] ) )
                    $rows[] = array( 'class_id' => $child['contentclass_id'] );
        }

        $ids = array();
        foreach ( $rows as $row )
        {
            $id = (int)( $row['class_id'] ?? reset( $row ) );
            if ( $id > 0 && !in_array( $id, $ids, true ) )
                $ids[] = $id;
        }
        return $ids;
    }

    /**
     * The automatic attribute columns for a parent: every attribute of the classes present among
     * its children, minus the AttributeColumnsExcludedDataTypes[]. Not checked against policies.
     *
     * @param eZContentObjectTreeNode $parent
     * @return array key => expSubitemsAttributeColumn
     */
    public function attributeColumns( eZContentObjectTreeNode $parent )
    {
        if ( !$this->attributeColumnsEnabled() )
            return array();

        $parentID = (int)$parent->attribute( 'node_id' );
        if ( isset( $this->attributeColumnCache[$parentID] ) )
            return $this->attributeColumnCache[$parentID];

        $columns = array();
        foreach ( $this->childClassIDs( $parent ) as $classID )
        {
            $class = eZContentClass::fetch( $classID );
            if ( !$class instanceof eZContentClass )
                continue;
            foreach ( $class->fetchAttributes() as $classAttribute )
            {
                $column = $this->makeAttributeColumn( $class, $classAttribute );
                if ( $column !== null )
                    $columns[$column->key()] = $column;
            }
        }
        return $this->attributeColumnCache[$parentID] = $columns;
    }

    /**
     * The column for a class attribute, or null when its datatype is excluded.
     *
     * @param eZContentClass $class
     * @param eZContentClassAttribute $classAttribute
     * @return expSubitemsAttributeColumn|null
     */
    public function makeAttributeColumn( eZContentClass $class, eZContentClassAttribute $classAttribute )
    {
        $dataType = (string)$classAttribute->attribute( 'data_type_string' );
        if ( in_array( $dataType, $this->excludedDataTypes(), true ) )
            return null;
        if ( expSubitemsAttributeColumn::parseKey( expSubitemsAttributeColumn::makeKey(
                 $class->attribute( 'identifier' ), $classAttribute->attribute( 'identifier' ) ) ) === null )
            return null;
        return expSubitemsAttributeColumn::fromClassAttribute( $class, $classAttribute, $this->dataTypePolicies( $dataType ) );
    }

    /** @return string[] AttributeColumnsExcludedDataTypes[] */
    public function excludedDataTypes()
    {
        return self::splitList( $this->setting( 'AttributeColumnsExcludedDataTypes', array( 'ezuser' ) ) );
    }

    /**
     * The policies an attribute column of a datatype needs: AttributeColumnsDataTypePolicy[<datatype>]=a/b;c/d
     *
     * @param string $dataType
     * @return string[]
     */
    public function dataTypePolicies( $dataType )
    {
        $map = $this->setting( 'AttributeColumnsDataTypePolicy', array() );
        return is_array( $map ) && isset( $map[$dataType] ) ? self::splitList( $map[$dataType] ) : array();
    }

    /**
     * Every column the current user may see under a parent, built-in, INI and attribute columns,
     * in Order= order (then by name).
     *
     * @param eZContentObjectTreeNode $parent
     * @return array key => expSubitemsColumn
     */
    public function availableColumns( eZContentObjectTreeNode $parent )
    {
        $columns = array();
        foreach ( array_keys( $this->definitions ) as $key )
        {
            $column = $this->definedColumn( $key );
            if ( $column !== null && $column->isAvailable( $parent ) )
                $columns[$key] = $column;
        }
        foreach ( $this->attributeColumns( $parent ) as $key => $column )
        {
            if ( $column->isAvailable( $parent ) )
                $columns[$key] = $column;
        }

        $order = array();
        $i = 0;
        foreach ( $columns as $key => $column )
            $order[$key] = array( (int)$column->setting( 'Order', 50000 ), $i++ );
        uksort( $columns, function ( $a, $b ) use ( $order ) {
            return $order[$a] <=> $order[$b];
        } );
        return $columns;
    }

    /**
     * The column for a key if the current user may see it under the parent, else null.
     *
     * @param string $key
     * @param eZContentObjectTreeNode $parent
     * @return expSubitemsColumn|null
     */
    public function column( $key, eZContentObjectTreeNode $parent )
    {
        if ( !is_string( $key ) )
            return null;
        $column = $this->definedColumn( $key );
        if ( $column === null && expSubitemsAttributeColumn::parseKey( $key ) !== null )
        {
            $attributeColumns = $this->attributeColumns( $parent );
            $column = $attributeColumns[$key] ?? null;
        }
        if ( $column === null || !$column->isAvailable( $parent ) )
            return null;
        return $column;
    }

    /**
     * The requested keys that are columns the user may see, in the requested order, without
     * duplicates; unknown and refused keys are dropped. At most MAX_KEYS.
     *
     * @param array|string $keys a list, or "a,b,c"
     * @param eZContentObjectTreeNode $parent
     * @param bool $withBuiltin whether built-in columns are kept (CSV) or dropped (rows)
     * @return array key => expSubitemsColumn
     */
    public function resolveColumns( $keys, eZContentObjectTreeNode $parent, $withBuiltin = false )
    {
        $resolved = array();
        foreach ( array_slice( self::splitList( is_array( $keys ) ? $keys : (string)$keys ), 0, self::MAX_KEYS ) as $key )
        {
            if ( !$withBuiltin && $this->isBuiltin( $key ) )
                continue;
            $column = $this->column( $key, $parent );
            if ( $column !== null )
                $resolved[$key] = $column;
        }
        return $resolved;
    }

    /**
     * The default visible columns under a parent: the first [Defaults_*] block whose Subtree[]
     * holds the parent or one of its ancestors, or whose ParentClassIdentifiers[] holds the
     * parent's class; else DefaultColumns[]. Unknown keys are dropped.
     *
     * @param eZContentObjectTreeNode $parent
     * @return string[]
     */
    public function defaults( eZContentObjectTreeNode $parent )
    {
        $path = array_map( 'intval', array_filter( explode( '/', (string)$parent->attribute( 'path_string' ) ) ) );
        $nodeID = (int)$parent->attribute( 'node_id' );
        if ( !in_array( $nodeID, $path, true ) )
            $path[] = $nodeID;
        $classIdentifier = (string)$parent->attribute( 'class_identifier' );

        $columns = null;
        foreach ( $this->defaultBlocks as $block )
        {
            if ( array_intersect( $block['subtree'], $path ) || ( $classIdentifier !== '' && in_array( $classIdentifier, $block['classes'], true ) ) )
            {
                $columns = $block['columns'];
                break;
            }
        }
        if ( $columns === null )
            $columns = self::splitList( $this->setting( 'DefaultColumns', array() ) );

        $known = array();
        foreach ( $columns as $key )
            if ( $this->isKnownKeyCheap( $key ) )
                $known[] = $key;
        return $known;
    }

    /**
     * isKnownKey() without a database lookup for attribute keys (they are checked when shown).
     */
    protected function isKnownKeyCheap( $key )
    {
        return isset( $this->definitions[$key] ) || expSubitemsAttributeColumn::parseKey( $key ) !== null;
    }

    /**
     * The INI presets.
     *
     * @return array id => array( 'name' => string, 'columns' => string[] )
     */
    public function presets()
    {
        return $this->presets;
    }

    /**
     * What the columns server function says about a column.
     *
     * @param expSubitemsColumn $column
     * @return array
     */
    public function describe( expSubitemsColumn $column )
    {
        return array(
            'key' => $column->key(),
            'name' => $column->name(),
            'group' => (string)$column->setting( 'Group', 'Custom' ),
            'type' => (string)$column->setting( 'Type', 'text' ),
            'sortable' => $column->sortBy() !== false,
            'align' => (string)$column->setting( 'Align', '' ),
            'description' => (string)$column->setting( 'Description', '' ),
            'copy' => self::isTrue( $column->setting( 'Copy', 'false' ) ),
            'builtin' => $this->isBuiltin( $column->key() ),
            'order' => (int)$column->setting( 'Order', 50000 ),
        );
    }

    /**
     * How a sort key sorts the parent's children.
     *
     * The key is a column key whose sortBy() is not false (built-in, INI or attribute column the
     * user may see), or one of the sort names the list used before (name, published_date,
     * modified_date, section, class_name, priority, node_id, contentobject_id,
     * hidden_status_string). Anything else falls back to the parent's own sort.
     *
     * @param string $key
     * @param bool $ascending
     * @param eZContentObjectTreeNode $parent
     * @return array array( 'key' => the accepted key or null, 'sort_by' => SortBy list for subTree(),
     *               'class_filter' => class identifier the sort limits the list to, or null )
     */
    public function sortFor( $key, $ascending, eZContentObjectTreeNode $parent )
    {
        $field = false;
        $accepted = null;
        if ( is_string( $key ) && $key !== '' )
        {
            foreach ( $this->definitions as $defKey => $settings )
            {
                if ( isset( $settings['LegacySort'] ) && $settings['LegacySort'] === $key && $this->isBuiltin( $defKey ) )
                {
                    $key = $defKey;
                    break;
                }
            }
            $column = $this->column( $key, $parent );
            if ( $column !== null )
            {
                $field = $column->sortBy();
                if ( $field !== false )
                    $accepted = $key;
            }
        }

        if ( $field === false )
            return array( 'key' => null, 'sort_by' => $parent->sortArray(), 'class_filter' => null );

        if ( is_array( $field ) )
        {
            // array( 'attribute', '<class>/<attribute>' ): only that class's items have the attribute,
            // so the list is limited to the class (the count agrees with what is shown)
            $identifier = isset( $field[1] ) ? (string)$field[1] : '';
            list( $classIdentifier ) = explode( '/', $identifier . '/' );
            return array( 'key' => $accepted, 'sort_by' => array( array( 'attribute', (bool)$ascending, $identifier ) ),
                          'class_filter' => $classIdentifier !== '' ? $classIdentifier : null );
        }
        return array( 'key' => $accepted, 'sort_by' => array( array( $field, (bool)$ascending ) ), 'class_filter' => null );
    }
}
