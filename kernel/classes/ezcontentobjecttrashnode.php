<?php
/**
 * File containing the eZContentObjectTrashNode class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Encapsulates data about and methods to work with content objects which reside in the trash
 */
class eZContentObjectTrashNode extends eZContentObjectTreeNode
{
    /**
     * @inheritdoc
     */
    static function definition()
    {
        $definition = array( 'fields' => array( 'node_id' => array( 'name' => 'NodeID',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ),
                                         'parent_node_id' => array( 'name' => 'ParentNodeID',
                                                                    'datatype' => 'integer',
                                                                    'default' => 0,
                                                                    'required' => true ),
                                         'main_node_id' => array( 'name' => 'MainNodeID',
                                                                  'datatype' => 'integer',
                                                                  'default' => 0,
                                                                  'required' => true ),
                                         'contentobject_id' => array( 'name' => 'ContentObjectID',
                                                                      'datatype' => 'integer',
                                                                      'default' => 0,
                                                                      'required' => true,
                                                                      'foreign_class' => 'eZContentObject',
                                                                      'foreign_attribute' => 'id',
                                                                      'multiplicity' => '1..*' ),
                                         'contentobject_version' => array( 'name' => 'ContentObjectVersion',
                                                                           'datatype' => 'integer',
                                                                           'default' => 0,
                                                                           'required' => true ),
                                         'depth' => array( 'name' => 'Depth',
                                                           'datatype' => 'integer',
                                                           'default' => 0,
                                                           'required' => true ),
                                         'sort_field' => array( 'name' => 'SortField',
                                                                'datatype' => 'integer',
                                                                'default' => 1,
                                                                'required' => true ),
                                         'sort_order' => array( 'name' => 'SortOrder',
                                                                'datatype' => 'integer',
                                                                'default' => 1,
                                                                'required' => true ),
                                         'priority' => array( 'name' => 'Priority',
                                                              'datatype' => 'integer',
                                                              'default' => 0,
                                                              'required' => true ),
                                         'modified_subnode' => array( 'name' => 'ModifiedSubNode',
                                                                      'datatype' => 'integer',
                                                                      'default' => 0,
                                                                      'required' => true ),
                                         'path_string' => array( 'name' => 'PathString',
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => true ),
                                         'path_identification_string' => array( 'name' => 'PathIdentificationString',
                                                                                'datatype' => 'text',
                                                                                'default' => '',
                                                                                'required' => true ),
                                         'remote_id' => array( 'name' => 'RemoteID',
                                                               'datatype' => 'string',
                                                               'default' => '',
                                                               'required' => true ),
                                         'is_hidden' => array( 'name' => 'IsHidden',
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true ),
                                         'is_invisible' => array( 'name' => 'IsInvisible',
                                                                  'datatype' => 'integer',
                                                                  'default' => 0,
                                                                  'required' => true ),
                                         'trashed' => array( 'name' => 'Trashed',
                                                                  'datatype' => 'integer',
                                                                  'default' => 0,
                                                                  'required' => true ),
                                         // who moved the object to the trash (user content object id, 0: not known)
                                         'trashed_by' => array( 'name' => 'TrashedBy',
                                                                'datatype' => 'integer',
                                                                'default' => 0,
                                                                'required' => true ),
                                         // from where: "web <siteaccess>" or "cli <script>"
                                         'trashed_via' => array( 'name' => 'TrashedVia',
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => true,
                                                                 'max_length' => 100 )
                                          ),

                      'keys' => array( 'node_id' ),
                      'function_attributes' => array( // functional attributes derived from ezcontentobjecttreenode
                                                      'name' => 'getName',
                                                      'data_map' => 'dataMap',
                                                      'object' => 'object',
                                                      'contentobject_version_object' => 'contentObjectVersionObject',
                                                      'sort_array' => 'sortArray',
                                                      'can_read' => 'canRead',
                                                      'can_create' => 'canCreate',
                                                      'can_edit' => 'canEdit',
                                                      'can_remove' => 'canRemove',
                                                      'creator' => 'creator',
                                                      'path_array' => 'pathArray',
                                                      'parent' => 'fetchParent',
                                                      'class_identifier' => 'classIdentifier',
                                                      'class_name' => 'className',
                                                      // new functional attributes
                                                      'original_parent' => 'originalParent',
                                                      'original_parent_path_id_string' => 'originalParentPathIdentificationString'
                                                      ),
                      'class_name' => 'eZContentObjectTrashNode',
                      'name' => 'ezcontentobject_trash' );
        // before the database update has added them (see hasTrashedByColumns()), the row is stored and read without them
        if ( self::$trashedByColumns === false )
            unset( $definition['fields']['trashed_by'], $definition['fields']['trashed_via'] );
        return $definition;
    }

    /**
     * Creates a new eZContentObjectTrashNode based on an eZContentObjectTreeNode
     *
     * @param eZContentObjectTreeNode $node
     * @return eZContentObjectTrashNode
     */
    static function createFromNode( $node )
    {
        $row = array( 'node_id' => $node->attribute( 'node_id' ),
                      'parent_node_id' => $node->attribute( 'parent_node_id' ),
                      'main_node_id' => $node->attribute( 'main_node_id' ),
                      'contentobject_id' => $node->attribute( 'contentobject_id' ),
                      'contentobject_version' => $node->attribute( 'contentobject_version' ),
                      'contentobject_is_published' => $node->attribute( 'contentobject_is_published' ),
                      'depth' => $node->attribute( 'depth' ),
                      'sort_field' => $node->attribute( 'sort_field' ),
                      'sort_order' => $node->attribute( 'sort_order' ),
                      'priority' => $node->attribute( 'priority' ),
                      'modified_subnode' => $node->attribute( 'modified_subnode' ),
                      'path_string' => $node->attribute( 'path_string' ),
                      'path_identification_string' => $node->attribute( 'path_identification_string' ),
                      'remote_id' => $node->attribute( 'remote_id' ),
                      'is_hidden' => $node->attribute( 'is_hidden' ),
                      'is_invisible' => $node->attribute( 'is_invisible' ),
                      'trashed' => time(),
                      'trashed_by' => (int)eZUser::currentUserID(),
                      'trashed_via' => self::currentVia() );

        $trashNode = new eZContentObjectTrashNode( $row );
        return $trashNode;
    }

    /**
     * Stores this object to the trash
     *
     * Loops through all attributes of the object and stores them to the trash
     *
     * @see eZDataType::trashStoredObjectAttribute()
     */
    function storeToTrash()
    {
        // without the columns (the database update has not run yet) the row is stored without them and who moved
        // the object to the trash goes to the old file, from where movetrashrecords.php copies it after the update
        $columns = self::hasTrashedByColumns();
        $this->store();
        if ( !$columns )
            self::callTrashRecord( 'record', $this );

        $db = eZDB::instance();
        $db->begin();

        /** @var eZContentObject $contentObject */
        $contentObject = $this->attribute( 'object' );
        if ( $contentObject === null )
        {
            $db->commit();
            return;
        }
        $offset = 0;
        $limit = 20;
        while (
            $contentobjectAttributes = $contentObject->allContentObjectAttributes(
                $contentObject->attribute( 'id' ), true,
                array( 'limit' => $limit, 'offset' => $offset )
            )
        )
        {
            foreach ( $contentobjectAttributes as $contentobjectAttribute )
            {
                $dataType = $contentobjectAttribute->dataType();
                if ( !$dataType )
                    continue;
                $dataType->trashStoredObjectAttribute( $contentobjectAttribute );
            }
            $offset += $limit;
        }

        $db->commit();
    }

    /**
     * Purges an object from the trash, effectively deleting it from the database
     *
     * @param int $contentObjectID
     * @return bool
     */
    static function purgeForObject( $contentObjectID )
    {
        if ( !is_numeric( $contentObjectID ) )
            return false;
        $db = eZDB::instance();
        $db->begin();
        $db->query( "DELETE FROM ezcontentobject_trash WHERE contentobject_id='$contentObjectID'" );
        $db->commit();
        self::callTrashRecord( 'forget', (int)$contentObjectID );
    }

    /**
     * Where a trash move comes from, for trashed_via: "web <siteaccess>" or "cli <script>" (for ezexec.php, the
     * script it runs).
     *
     * @return string at most 100 characters
     */
    static function currentVia()
    {
        if ( PHP_SAPI === 'cli' && !isset( $_SERVER['REQUEST_URI'] ) )
        {
            $script = isset( $_SERVER['argv'][0] ) ? basename( (string)$_SERVER['argv'][0] ) : 'php';
            // ezexec.php runs another script: name that one
            if ( $script === 'ezexec.php' && isset( $_SERVER['argv'][1] ) )
                $script = basename( (string)$_SERVER['argv'][1] );
            $via = 'cli ' . $script;
        }
        else
        {
            $access = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? $GLOBALS['eZCurrentAccess']['name'] : '';
            $via = trim( 'web ' . $access );
        }
        return self::cleanVia( $via );
    }

    /**
     * A value for trashed_via: printable ASCII, at most 100 characters. A script name in another encoding is no
     * valid UTF-8, which PostgreSQL refuses (and a refused INSERT stops the transaction); in ASCII 100 characters
     * are 100 bytes, which fits a column whose length counts bytes (Oracle) as well.
     *
     * @param string $via
     * @return string
     */
    static function cleanVia( $via )
    {
        return substr( (string)preg_replace( '/[^\x20-\x7E]/', '?', (string)$via ), 0, 100 );
    }

    /**
     * Whether ezcontentobject_trash has the columns trashed_by and trashed_via, which the database update adds
     * (update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql). Until it has, definition() leaves them out, so
     * moving content to the trash, the trash view and restoring keep working with the old table.
     *
     * Asked of the database's own catalogue, never by a query that could fail: a failed query inside a
     * transaction stops the request. Once the columns are there the answer is kept for the rest of the
     * process (a Velocity worker included); while they are missing it is asked again on the next call, so the
     * update takes effect without a restart.
     *
     * @param bool $refresh ask again even when the columns were found before
     * @return bool
     */
    public static function hasTrashedByColumns( $refresh = false )
    {
        if ( self::$trashedByColumns === true && !$refresh )
            return true;
        $found = true;
        try
        {
            $db = eZDB::instance();
            $rows = null;
            switch ( $db->databaseName() )
            {
                case 'mysql':
                    $rows = $db->arrayQuery( "SELECT COUNT(*) AS c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() "
                                           . "AND TABLE_NAME = 'ezcontentobject_trash' AND COLUMN_NAME IN ( 'trashed_by', 'trashed_via' )" );
                    break;
                case 'postgresql':
                    $rows = $db->arrayQuery( "SELECT COUNT(*) AS c FROM information_schema.columns WHERE table_schema = current_schema() "
                                           . "AND table_name = 'ezcontentobject_trash' AND column_name IN ( 'trashed_by', 'trashed_via' )" );
                    break;
                case 'oracle':
                    $rows = $db->arrayQuery( "SELECT COUNT(*) AS c FROM user_tab_columns WHERE table_name = 'EZCONTENTOBJECT_TRASH' "
                                           . "AND column_name IN ( 'TRASHED_BY', 'TRASHED_VIA' )" );
                    break;
                case 'sqlite':
                    $names = array();
                    foreach ( (array)$db->arrayQuery( 'PRAGMA table_info(ezcontentobject_trash)' ) as $column )
                        $names[] = isset( $column['name'] ) ? $column['name'] : '';
                    $rows = array( array( 'c' => count( array_intersect( array( 'trashed_by', 'trashed_via' ), $names ) ) ) );
                    break;
                case 'mongo':
                    // a collection has no columns: the driver works from the .dba schema it ships with, which
                    // declares them, and reads a document written before them with their defaults. Only a schema
                    // that declares the table without them says no; none read at all keeps the documents' answer.
                    $declared = is_callable( array( $db, 'declaredColumns' ) ) ? (array)$db->declaredColumns( 'ezcontentobject_trash' ) : array();
                    $rows = array( array( 'c' => $declared
                        ? count( array_intersect_key( array( 'trashed_by' => 1, 'trashed_via' => 1 ), $declared ) )
                        : 2 ) );
                    break;
                // any other engine is taken to have them
            }
            if ( is_array( $rows ) )
                $found = isset( $rows[0] ) && (int)reset( $rows[0] ) === 2;
        }
        catch ( \Throwable $e )
        {
            eZDebug::writeError( $e->getMessage(), __METHOD__ );
        }
        self::$trashedByColumns = $found;
        return $found;
    }

    /**
     * The entries of <VarDir>/trash/trashed.json, where who moved what to the trash was kept before the columns
     * trashed_by and trashed_via existed (Exponential\Service\TrashRecord, doc/bc/6.0/trash.md): purging or
     * restoring an object forgets its entry. Loaded by path when the autoload array of a long-running worker
     * predates the class; a failure never stops the purge.
     *
     * @param string $method forget, or record (while the columns are missing)
     * @param mixed $argument
     */
    protected static function callTrashRecord( $method, $argument )
    {
        try
        {
            if ( !class_exists( 'Exponential\\Service\\TrashRecord' ) )
            {
                $file = __DIR__ . '/../private/classes/services/trashrecord.php';
                if ( !is_file( $file ) )
                    return;
                require_once $file;
            }
            call_user_func( array( 'Exponential\\Service\\TrashRecord', $method ), $argument );
        }
        catch ( \Throwable $e )
        {
            eZDebug::writeError( $e->getMessage(), __METHOD__ );
        }
    }

    /**
     * Returns a list or the number of nodes from the trash
     *
     * @see eZContentObjectTreeNode::subTreeByNodeID()
     *
     * @param array|bool $params
     * @param bool $asCount If true, returns the number of items in the trash
     * @return array|int|null
     */
    static function trashList( $params = false, $asCount = false )
    {
        if ( $params === false )
        {
            $params = array( 'Offset'                   => false,
                             'Limit'                    => false,
                             'SortBy'                   => false,
                             'AttributeFilter'          => false,
                             'Trashed'                  => false,
                             );
        }

        $offset           = ( isset( $params['Offset'] ) && is_numeric( $params['Offset'] ) ) ? $params['Offset']             : false;
        $limit            = ( isset( $params['Limit']  ) && is_numeric( $params['Limit']  ) ) ? $params['Limit']              : false;
        $asObject         = ( isset( $params['AsObject']          ) )                         ? $params['AsObject']           : true;
        $objectNameFilter = ( isset( $params['ObjectNameFilter']  ) )                         ? $params['ObjectNameFilter']   : false;
        $sortBy           = ( isset( $params['SortBy']  ) && is_array( $params['SortBy']  ) ) ? $params['SortBy']              : array( array( 'name' ) );
        // MongoDB cannot run the joins below: the same list from an aggregation
        if ( eZDB::instance()->databaseName() === 'mongo' )
            return self::trashListMongo( $params, $asCount, $offset, $limit, $asObject, $objectNameFilter, $sortBy );
        $trashed          = ( isset( $params['Trashed']  ) && is_int( $params['Trashed'] )  ) ? " AND trashed <= {$params['Trashed']}"   : '';
        $trashed         .= self::trashListFilterSQL( $params );

        if ( $asCount )
        {
            $sortingInfo = eZContentObjectTreeNode::createSortingSQLStrings( false );
        }
        else
        {
            $sortingInfo = eZContentObjectTreeNode::createSortingSQLStrings( $sortBy, 'ezcot' );
        }

        $attributeFilter         = eZContentObjectTreeNode::createAttributeFilterSQLStrings( $params['AttributeFilter'], $sortingInfo );
        if ( $attributeFilter === false )
        {
            return null;
        }

        $objectNameFilterSQL = eZContentObjectTreeNode::createObjectNameFilterConditionSQLString( $objectNameFilter );

        $limitation = ( isset( $params['Limitation']  ) && is_array( $params['Limitation']  ) ) ? $params['Limitation']: false;
        $limitationList = eZContentObjectTreeNode::getLimitationList( $limitation );
        $sqlPermissionChecking = eZContentObjectTreeNode::createPermissionCheckingSQL( $limitationList, 'ezcontentobject_trash', 'ezcot' );

        if ( $asCount )
        {
            $query = "SELECT count(*) as count ";
        }
        else
        {
            $query = "SELECT
                        ezcontentobject.*,
                        ezcot.*,
                        ezcontentclass.serialized_name_list as class_serialized_name_list,
                        ezcontentclass.identifier as class_identifier,
                        ezcontentobject_name.name as name,
                        ezcontentobject_name.real_translation
                        $sortingInfo[attributeTargetSQL] ";
        }
        $query .= "FROM
                        ezcontentobject_trash ezcot
                        INNER JOIN ezcontentobject ON ezcot.contentobject_id = ezcontentobject.id
                        INNER JOIN ezcontentclass ON ezcontentclass.version = 0 AND ezcontentclass.id = ezcontentobject.contentclass_id
                        INNER JOIN ezcontentobject_name ON (
                            ezcot.contentobject_id = ezcontentobject_name.contentobject_id AND
                            ezcot.contentobject_version = ezcontentobject_name.content_version
                        )
                        $sortingInfo[attributeFromSQL]
                        $attributeFilter[from]
                        $sqlPermissionChecking[from]
                   WHERE
                        $sortingInfo[attributeWhereSQL]
                        $attributeFilter[where]
                        " . eZContentLanguage::sqlFilter( 'ezcontentobject_name', 'ezcontentobject' ) . "
                        $sqlPermissionChecking[where]
                        $objectNameFilterSQL
                        AND " . eZContentLanguage::languagesSQLFilter( 'ezcontentobject' )
                        . $trashed;

        if ( !$asCount && $sortingInfo['sortingFields'] && strlen( $sortingInfo['sortingFields'] ) > 5  )
            $query .= " ORDER BY $sortingInfo[sortingFields]";

        $db = eZDB::instance();
        if ( !$offset && !$limit )
            $trashRowsArray = $db->arrayQuery( $query );
        else
            $trashRowsArray = $db->arrayQuery( $query, array( 'offset' => $offset,
                                                              'limit'  => $limit ) );

        // cleanup temp tables
        $db->dropTempTableList( $sqlPermissionChecking['temp_tables'] );

        if ( $asCount )
        {
            return $trashRowsArray[0]['count'];
        }
        else if ( $asObject )
        {
            $retTrashNodes = array();
            foreach ( array_keys( $trashRowsArray ) as $key )
            {
                $trashRow =& $trashRowsArray[ $key ];
                $retTrashNodes[] = new eZContentObjectTrashNode( $trashRow );
            }
            return $retTrashNodes;
        }
        else
        {
            return $trashRowsArray;
        }
    }

    /**
     * The filters of the trash view, as SQL appended to trashList()'s WHERE:
     *   ClassIDList                 content class ids
     *   TrashedFrom, TrashedTo      timestamps, both inclusive
     *   ContentObjectIDList         only these objects (an empty array matches nothing)
     *   ExcludeContentObjectIDList  not these objects
     *   TrashedBy                   moved to the trash by this user (content object id), or one of
     *                               TrashedByFileObjectIDList (known only from <VarDir>/trash/trashed.json)
     *   TrashedByUnknown            true: by nobody known, neither in trashed_by nor one of TrashedByFileObjectIDList
     *
     * @param array $params
     * @return string
     */
    protected static function trashListFilterSQL( $params )
    {
        $db = eZDB::instance();
        $sql = '';
        if ( isset( $params['ClassIDList'] ) && is_array( $params['ClassIDList'] ) && $params['ClassIDList'] )
            $sql .= ' AND ' . $db->generateSQLINStatement( array_map( 'intval', $params['ClassIDList'] ), 'ezcontentobject.contentclass_id', false, true, 'int' );
        if ( isset( $params['TrashedFrom'] ) && is_int( $params['TrashedFrom'] ) )
            $sql .= " AND ezcot.trashed >= {$params['TrashedFrom']}";
        if ( isset( $params['TrashedTo'] ) && is_int( $params['TrashedTo'] ) )
            $sql .= " AND ezcot.trashed <= {$params['TrashedTo']}";
        if ( isset( $params['ContentObjectIDList'] ) && is_array( $params['ContentObjectIDList'] ) )
        {
            $ids = $params['ContentObjectIDList'] ? array_map( 'intval', $params['ContentObjectIDList'] ) : array( 0 );
            $sql .= ' AND ' . $db->generateSQLINStatement( $ids, 'ezcot.contentobject_id', false, true, 'int' );
        }
        if ( isset( $params['ExcludeContentObjectIDList'] ) && is_array( $params['ExcludeContentObjectIDList'] ) && $params['ExcludeContentObjectIDList'] )
            $sql .= ' AND ' . $db->generateSQLINStatement( array_map( 'intval', $params['ExcludeContentObjectIDList'] ), 'ezcot.contentobject_id', true, true, 'int' );
        $fileIDs = isset( $params['TrashedByFileObjectIDList'] ) && is_array( $params['TrashedByFileObjectIDList'] )
                   ? array_map( 'intval', $params['TrashedByFileObjectIDList'] ) : array();
        if ( isset( $params['TrashedBy'] ) && is_numeric( $params['TrashedBy'] ) )
        {
            $condition = 'ezcot.trashed_by = ' . (int)$params['TrashedBy'];
            if ( $fileIDs )
                $condition = '( ' . $condition . ' OR ' . $db->generateSQLINStatement( $fileIDs, 'ezcot.contentobject_id', false, true, 'int' ) . ' )';
            $sql .= ' AND ' . $condition;
        }
        if ( !empty( $params['TrashedByUnknown'] ) )
        {
            $sql .= ' AND ezcot.trashed_by = 0';
            if ( $fileIDs )
                $sql .= ' AND ' . $db->generateSQLINStatement( $fileIDs, 'ezcot.contentobject_id', true, true, 'int' );
        }
        return $sql;
    }

    /**
     * trashList() on MongoDB, which has no joins: one aggregation over ezcontentobject_trash that looks up the
     * object, its class, its name and its class name, with the same filters (trashListFilterMongo()), the same
     * sort keys, offset and limit, and the same rows (the object's fields, then the trash row's, then the class
     * and name columns). A trash row written before the columns trashed_by and trashed_via existed reads as
     * trashed_by 0 and trashed_via '', as the SQL engines read the column defaults.
     *
     * The permission check is SQL generated from the policies: for a user whose content/read is limited it
     * cannot be applied here, so such a user is shown no trash (and a warning is logged) rather than all of it.
     * An AttributeFilter is refused the same way, so that nothing (an Empty, a purge) reaches more than asked.
     *
     * @param array $params
     * @param bool $asCount
     * @param int|bool $offset
     * @param int|bool $limit
     * @param bool $asObject
     * @param string|bool $objectNameFilter
     * @param array $sortBy
     * @return array|int
     */
    protected static function trashListMongo( $params, $asCount, $offset, $limit, $asObject, $objectNameFilter, $sortBy )
    {
        $none = $asCount ? 0 : array();
        $limitation = ( isset( $params['Limitation'] ) && is_array( $params['Limitation'] ) ) ? $params['Limitation'] : false;
        $limitationList = eZContentObjectTreeNode::getLimitationList( $limitation );
        if ( $limitationList === false )
            return $none;
        if ( is_array( $limitationList ) && count( $limitationList ) > 0 )
        {
            eZDebug::writeWarning( 'The trash list cannot apply limited content/read policies on MongoDB: nothing is listed', __METHOD__ );
            return $none;
        }
        if ( !empty( $params['AttributeFilter'] ) )
        {
            eZDebug::writeWarning( 'The trash list has no AttributeFilter on MongoDB: nothing is listed', __METHOD__ );
            return $none;
        }

        // the name in the most prioritized language the object has, as eZContentLanguage::sqlFilter() picks it
        $languageIDs = array();
        $mask = 1;
        foreach ( eZContentLanguage::prioritizedLanguages() as $language )
        {
            $languageIDs[] = (int)$language->attribute( 'id' );
            $mask += (int)$language->attribute( 'id' );
        }
        $rank = array( '$let' => array(
            'vars' => array( 'at' => array( '$indexOfArray' => array( $languageIDs, array( '$bitAnd' => array(
                array( '$convert' => array( 'input' => '$language_id', 'to' => 'long', 'onError' => 0, 'onNull' => 0 ) ), ~1 ) ) ) ) ),
            'in' => array( '$cond' => array( array( '$lt' => array( '$$at', 0 ) ), 1000000, '$$at' ) ) ) );

        $pipeline = array();
        $match = self::trashListFilterMongo( $params );
        if ( $match )
            $pipeline[] = array( '$match' => $match );
        $pipeline[] = array( '$lookup' => array( 'from' => 'ezcontentobject', 'localField' => 'contentobject_id',
                                                 'foreignField' => 'id', 'as' => '_obj' ) );
        $pipeline[] = array( '$unwind' => '$_obj' );
        $pipeline[] = array( '$match' => array( '$expr' => array( '$gt' => array( array( '$bitAnd' => array(
            array( '$convert' => array( 'input' => '$_obj.language_mask', 'to' => 'long', 'onError' => 0, 'onNull' => 0 ) ), $mask ) ), 0 ) ) ) );
        if ( isset( $params['ClassIDList'] ) && is_array( $params['ClassIDList'] ) && $params['ClassIDList'] )
            $pipeline[] = array( '$match' => array( '_obj.contentclass_id' => array( '$in' => array_values( array_map( 'intval', $params['ClassIDList'] ) ) ) ) );
        $pipeline[] = array( '$lookup' => array( 'from' => 'ezcontentclass', 'let' => array( 'cid' => '$_obj.contentclass_id' ),
                                                 'pipeline' => array( array( '$match' => array( '$expr' => array( '$and' => array(
                                                     array( '$eq' => array( '$id', '$$cid' ) ), array( '$eq' => array( '$version', 0 ) ) ) ) ) ),
                                                     array( '$limit' => 1 ) ),
                                                 'as' => '_cls' ) );
        $pipeline[] = array( '$unwind' => '$_cls' );
        $pipeline[] = array( '$lookup' => array( 'from' => 'ezcontentobject_name',
                                                 'let' => array( 'oid' => '$contentobject_id', 'ver' => '$contentobject_version' ),
                                                 'pipeline' => array( array( '$match' => array( '$expr' => array( '$and' => array(
                                                     array( '$eq' => array( '$contentobject_id', '$$oid' ) ),
                                                     array( '$eq' => array( '$content_version', '$$ver' ) ) ) ) ) ),
                                                     array( '$addFields' => array( '_rank' => $rank ) ),
                                                     array( '$sort' => array( '_rank' => 1 ) ),
                                                     array( '$limit' => 1 ) ),
                                                 'as' => '_name' ) );
        $pipeline[] = array( '$unwind' => '$_name' );
        if ( $objectNameFilter )
        {
            if ( $objectNameFilter == 'others' )
            {
                $letters = array();
                $alphabet = eZAlphabetOperator::fetchAlphabet();
                foreach ( is_array( $alphabet ) ? $alphabet : array() as $letter )
                    $letters[] = new MongoDB\BSON\Regex( '^' . preg_quote( (string)$letter ), 'i' );
                if ( $letters )
                    $pipeline[] = array( '$match' => array( '_name.name' => array( '$nin' => $letters ) ) );
            }
            else
                $pipeline[] = array( '$match' => array( '_name.name' => new MongoDB\BSON\Regex( '^' . preg_quote( (string)$objectNameFilter ), 'i' ) ) );
        }

        $db = eZDB::instance();
        if ( $asCount )
        {
            $pipeline[] = array( '$count' => 'count' );
            $rows = $db->aggregate( 'ezcontentobject_trash', $pipeline );
            return is_array( $rows ) && isset( $rows[0]['count'] ) ? (int)$rows[0]['count'] : 0;
        }

        $sortStage = self::trashListSortMongo( $sortBy );
        if ( isset( $sortStage['contentclass_name'] ) )
            $pipeline[] = array( '$lookup' => array( 'from' => 'ezcontentclass_name',
                                                     'let' => array( 'cid' => '$_obj.contentclass_id' ),
                                                     'pipeline' => array( array( '$match' => array( '$expr' => array( '$and' => array(
                                                         array( '$eq' => array( '$contentclass_id', '$$cid' ) ),
                                                         array( '$eq' => array( '$contentclass_version', 0 ) ) ) ) ) ),
                                                         array( '$addFields' => array( '_rank' => $rank ) ),
                                                         array( '$sort' => array( '_rank' => 1 ) ),
                                                         array( '$limit' => 1 ) ),
                                                     'as' => '_cname' ) );
        $pipeline[] = array( '$replaceRoot' => array( 'newRoot' => array( '$mergeObjects' => array(
            '$_obj', '$$ROOT',
            array( 'trashed_by' => array( '$ifNull' => array( '$trashed_by', 0 ) ),
                   'trashed_via' => array( '$ifNull' => array( '$trashed_via', '' ) ),
                   'class_serialized_name_list' => '$_cls.serialized_name_list',
                   'class_identifier' => '$_cls.identifier',
                   'name' => '$_name.name',
                   'real_translation' => '$_name.real_translation' ) ) ) ) );
        if ( isset( $sortStage['contentclass_name'] ) )
            $pipeline[] = array( '$addFields' => array( 'contentclass_name' => array( '$ifNull' => array( array( '$first' => '$_cname.name' ), '' ) ) ) );
        $pipeline[] = array( '$project' => array( '_id' => 0, '_obj' => 0, '_cls' => 0, '_name' => 0, '_cname' => 0 ) );
        $pipeline[] = array( '$sort' => $sortStage );
        if ( $offset > 0 )
            $pipeline[] = array( '$skip' => (int)$offset );
        if ( $limit > 0 )
            $pipeline[] = array( '$limit' => (int)$limit );
        $rows = $db->aggregate( 'ezcontentobject_trash', $pipeline );
        if ( !is_array( $rows ) )
            $rows = array();
        if ( !$asObject )
            return $rows;
        $nodes = array();
        foreach ( $rows as $row )
            $nodes[] = new eZContentObjectTrashNode( $row );
        return $nodes;
    }

    /**
     * The filters of trashListFilterSQL() (and Trashed) as a MongoDB filter on ezcontentobject_trash. A row
     * without trashed_by (written before the column existed) counts as trashed_by 0, the column's default.
     *
     * @param array $params
     * @return array an empty array: no filter
     */
    public static function trashListFilterMongo( $params )
    {
        $and = array();
        if ( isset( $params['Trashed'] ) && is_int( $params['Trashed'] ) )
            $and[] = array( 'trashed' => array( '$lte' => $params['Trashed'] ) );
        if ( isset( $params['TrashedFrom'] ) && is_int( $params['TrashedFrom'] ) )
            $and[] = array( 'trashed' => array( '$gte' => $params['TrashedFrom'] ) );
        if ( isset( $params['TrashedTo'] ) && is_int( $params['TrashedTo'] ) )
            $and[] = array( 'trashed' => array( '$lte' => $params['TrashedTo'] ) );
        if ( isset( $params['ContentObjectIDList'] ) && is_array( $params['ContentObjectIDList'] ) )
        {
            $ids = $params['ContentObjectIDList'] ? array_values( array_map( 'intval', $params['ContentObjectIDList'] ) ) : array( 0 );
            $and[] = array( 'contentobject_id' => array( '$in' => $ids ) );
        }
        if ( isset( $params['ExcludeContentObjectIDList'] ) && is_array( $params['ExcludeContentObjectIDList'] ) && $params['ExcludeContentObjectIDList'] )
            $and[] = array( 'contentobject_id' => array( '$nin' => array_values( array_map( 'intval', $params['ExcludeContentObjectIDList'] ) ) ) );
        $fileIDs = isset( $params['TrashedByFileObjectIDList'] ) && is_array( $params['TrashedByFileObjectIDList'] )
                   ? array_values( array_map( 'intval', $params['TrashedByFileObjectIDList'] ) ) : array();
        if ( isset( $params['TrashedBy'] ) && is_numeric( $params['TrashedBy'] ) )
        {
            $condition = self::trashedByIsMongo( (int)$params['TrashedBy'] );
            if ( $fileIDs )
                $condition = array( '$or' => array( $condition, array( 'contentobject_id' => array( '$in' => $fileIDs ) ) ) );
            $and[] = $condition;
        }
        if ( !empty( $params['TrashedByUnknown'] ) )
        {
            $and[] = self::trashedByIsMongo( 0 );
            if ( $fileIDs )
                $and[] = array( 'contentobject_id' => array( '$nin' => $fileIDs ) );
        }
        if ( !$and )
            return array();
        return count( $and ) === 1 ? $and[0] : array( '$and' => $and );
    }

    /**
     * trashed_by = $userID on MongoDB: for 0 (nobody known) also a row without the field.
     *
     * @param int $userID
     * @return array
     */
    protected static function trashedByIsMongo( $userID )
    {
        if ( $userID !== 0 )
            return array( 'trashed_by' => $userID );
        return array( '$or' => array( array( 'trashed_by' => 0 ), array( 'trashed_by' => array( '$exists' => false ) ) ) );
    }

    /**
     * trashList()'s SortBy as a MongoDB sort on the rows trashListMongo() builds, with the sort keys and the
     * directions of eZContentObjectTreeNode::createSortingSQLStrings() (a true or missing direction ascends),
     * and the path when no key is known. Ties are broken by node id, so pages do not overlap.
     *
     * @param array $sortBy
     * @return array field => 1|-1
     */
    public static function trashListSortMongo( $sortBy )
    {
        $fields = array( 'path' => 'path_string', 'path_string' => 'path_identification_string', 'published' => 'published',
                         'modified' => 'modified', 'modified_subnode' => 'modified_subnode', 'section' => 'section_id',
                         'node_id' => 'node_id', 'contentobject_id' => 'contentobject_id', 'depth' => 'depth',
                         'class_identifier' => 'class_identifier', 'class_name' => 'contentclass_name', 'priority' => 'priority',
                         'visibility' => 'is_invisible', 'name' => 'name', 'trashed' => 'trashed' );
        if ( is_array( $sortBy ) && count( $sortBy ) > 1 && !is_array( $sortBy[0] ) )
            $sortBy = array( $sortBy );
        $sort = array();
        foreach ( is_array( $sortBy ) ? $sortBy : array() as $entry )
        {
            if ( !is_array( $entry ) || !$entry || !is_scalar( $entry[0] ) || !isset( $fields[(string)$entry[0]] ) )
                continue;
            $field = $fields[(string)$entry[0]];
            if ( !isset( $sort[$field] ) )
                $sort[$field] = ( !array_key_exists( 1, $entry ) || $entry[1] ) ? 1 : -1;
        }
        if ( !$sort )
            $sort['path_string'] = 1;
        if ( !isset( $sort['node_id'] ) )
            $sort['node_id'] = 1;
        return $sort;
    }

    /**
     * Returns the number of nodes in the trash
     *
     * @param array|bool $params
     * @return int
     */
    static function trashListCount( $params = false )
    {
        return eZContentObjectTrashNode::trashList( $params, true );
    }

    /**
     * Returns the parent of the current node in the tree before it has been moved to the trash or null when the
     * original parent couldn't be retrieved (e.g. because it has been deleted, too, or moved)
     *
     * @return eZContentObjectTreeNode|null
     */
    function originalParent()
    {
        if ( $this->originalNodeParent === 0 )
        {
            $this->originalNodeParent = eZContentObjectTreeNode::fetch( $this->attribute( 'parent_node_id' ) );
            if ( $this->originalNodeParent === null )
                return false;
        }

        if ( $this->pathArray === 0 && $this->originalNodeParent instanceof eZContentObjectTreeNode )
            $this->pathArray = $this->attribute( 'path_array' );

        if ( $this->pathArray && count( $this->pathArray ) > 0 )
        {
            $realParentPathArray = $this->originalNodeParent->attribute( 'path_array' );
            $realParentPath = implode( '/', $realParentPathArray );

            $thisParentPathArray = array_slice( $this->pathArray, 0, -1 );
            $thisParentPath = implode( '/', $thisParentPathArray );

            if ( $thisParentPath == $realParentPath )
            {
                // original parent exists at the same placement
                return $this->originalNodeParent;
            }
        }
        // original parent was moved or deleted
        return null;
    }

    /**
     * Returns the path identification string of the node's parent, if available. Otherwise returns
     * the node's path identification string
     *
     * @see originalParent()
     * @return string
     */
    function originalParentPathIdentificationString()
    {
        $originalParent = $this->originalParent();
        if ( $originalParent )
        {
            return $originalParent->attribute( 'path_identification_string' );
        }
        // original parent was moved or does not exist, return original parent path
        $path = $this->attribute( 'path_identification_string' );
        $path = substr( $path, 0, strrpos( $path, '/') );
        return $path;
    }

    /**
     * Fetches a trash node by its content object id
     *
     * @param int $contentObjectID
     * @param bool $asObject
     * @param int|bool $contentObjectVersion
     * @return eZContentObjectTrashNode|null
     */
    public static function fetchByContentObjectID( $contentObjectID, $asObject = true, $contentObjectVersion = false )
    {
        // the columns this definition() reads
        self::hasTrashedByColumns();
        $conds = array( 'contentobject_id' => $contentObjectID );
        if ( $contentObjectVersion !== false )
        {
            $conds['contentobject_version'] = $contentObjectVersion;
        }

        return self::fetchObject(
            self::definition(),
            null,
            $conds,
            $asObject
        );
    }

    /**
     * @var bool|null whether the trash table has trashed_by and trashed_via (hasTrashedByColumns()); null: not asked yet
     */
    protected static $trashedByColumns = null;

    /**
     * @var eZContentObjectTreeNode|int|null The current trash node's original parent in the node tree
     */
    protected $originalNodeParent = 0;

    /**
     * @var array
     */
    protected $pathArray = 0;
}

?>
