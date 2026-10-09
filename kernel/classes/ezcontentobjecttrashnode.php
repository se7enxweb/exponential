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
        return array( 'fields' => array( 'node_id' => array( 'name' => 'NodeID',
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
        $this->store();

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
        return function_exists( 'mb_substr' ) ? mb_substr( $via, 0, 100, 'UTF-8' ) : substr( $via, 0, 100 );
    }

    /**
     * The entries of <VarDir>/trash/trashed.json, where who moved what to the trash was kept before the columns
     * trashed_by and trashed_via existed (Exponential\Service\TrashRecord, doc/bc/6.0/trash.md): purging or
     * restoring an object forgets its entry. Loaded by path when the autoload array of a long-running worker
     * predates the class; a failure never stops the purge.
     *
     * @param string $method forget
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
     * @var eZContentObjectTreeNode|int|null The current trash node's original parent in the node tree
     */
    protected $originalNodeParent = 0;

    /**
     * @var array
     */
    protected $pathArray = 0;
}

?>
