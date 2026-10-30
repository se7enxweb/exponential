<?php
/**
 * File containing the eZSection class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZSection ezsection.php
  \brief eZSection handles grouping of content in eZ Publish

*/

class eZSection extends eZPersistentObject
{
    /**
     * Which objects a section assignment at a node changes (applyTo(), section/assign, the content job 'section'):
     * the node's object and every object below it whose main location is there (the default, as before) ...
     */
    const SCOPE_SUBTREE = 'subtree';
    /** ... the node's object only ... */
    const SCOPE_OBJECT = 'object';
    /**
     * ... or the node's object and the objects below it that have the section it had; an object below with another
     * section keeps it, and so does everything below that object.
     */
    const SCOPE_INHERITED = 'inherited';

    public function __construct( $row )
    {
        if ( !isset( $row['id'] ) )
        {
            $row['id'] = null;
        }
        parent::__construct( $row );
    }

    /*!
     \return the persistent object definition for the eZSection class.
    */
    static function definition()
    {
        static $definition = array( "fields" => array( "id" => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         "name" => array( 'name' => "Name",
                                                          'datatype' => 'string',
                                                          'default' => 0,
                                                          'required' => true ),
                                         "navigation_part_identifier" => array( 'name' => "NavigationPartIdentifier",
                                                                                'datatype' => 'string',
                                                                                'default' => 'ezcontentnavigationpart',
                                                                                'required' => true ),
                                         "locale" => array( 'name' => "Locale",
                                                            'datatype' => 'string',
                                                            'default' => '',
                                                            'required' => true ),
                                         "identifier" => array( 'name' => "Identifier",
                                                            'datatype' => 'string',
                                                            'default' => '',
                                                            'required' => true ) ),
                      "keys" => array( "id" ),
                      "increment_key" => "id",
                      "class_name" => "eZSection",
                      "sort" => array( "name" => "asc" ),
                      "name" => "ezsection" );
        return $definition;
    }

    /*!
     \return the section object with the given id.
    */
    static function fetch( $sectionID, $asObject = true )
    {
        global $eZContentSectionObjectCache;

        // No id (a template asking for the section of an object it does not have): no section, no query,
        // and no null array offset in the cache below
        if ( $sectionID === null || $sectionID === '' || $sectionID === false )
        {
            return null;
        }

        // If the object given by its id is not cached or should be returned as array
        // then we fetch it from the DB (objects are always cached as arrays).
        if ( !isset( $eZContentSectionObjectCache[$sectionID] ) or $asObject === false )
        {
            $section = eZPersistentObject::fetchObject( eZSection::definition(),
                                                null,
                                                array( "id" => $sectionID ),
                                                $asObject );
            if ( $asObject )
            {
                $eZContentSectionObjectCache[$sectionID] = $section;
            }
        }
        else
        {
            $section = $eZContentSectionObjectCache[$sectionID];
        }
        return $section;
    }

    /**
     * fetch object by section identifier
     * @param string $sectionIdentifier
     * @param boolean $asObject
     * @return object|null
     */
    static function fetchByIdentifier( $sectionIdentifier, $asObject = true )
    {
        global $eZContentSectionObjectCache;
        if( !isset( $eZContentSectionObjectCache[$sectionIdentifier] ) || $asObject === false )
        {
            $sectionFetched = eZPersistentObject::fetchObject( eZSection::definition(),
                                                       null,
                                                       array( "identifier" => $sectionIdentifier ),
                                                       $asObject );
            if( !$sectionFetched )
            {
                return null;
            }

            if( $asObject )
            {
                // the section identifier index refers to the id index object
                $sectionID = $sectionFetched->attribute( 'id' );
                if( !isset( $eZContentSectionObjectCache[$sectionID] ) )
                {
                    $eZContentSectionObjectCache[$sectionID] = $sectionFetched;
                }
                $eZContentSectionObjectCache[$sectionIdentifier] = $eZContentSectionObjectCache[$sectionID];
            }
            else
            {
                return $sectionFetched;
            }
        }
        $section = $eZContentSectionObjectCache[$sectionIdentifier];
        return $section;
    }

    static function fetchFilteredList( $conditions = null, $offset = false, $limit = false, $asObject = true )
    {
        $limits = null;
        if ( $offset or $limit )
            $limits = array( 'offset' => $offset,
                             'length' => $limit );
        return eZPersistentObject::fetchObjectList( eZSection::definition(),
                                                    null,
                                                    $conditions, null, $limits,
                                                    $asObject );
    }

    static function fetchList( $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( eZSection::definition(),
                                                    null, null, null, null,
                                                    $asObject );
    }

    static function fetchByOffset( $offset, $limit, $asObject = true )
    {
        $sectionList = eZPersistentObject::fetchObjectList( eZSection::definition(),
                                                             null,
                                                             null,
                                                             array( 'name' => 'ASC' ),
                                                             array( 'offset' => $offset, 'length' => $limit ),
                                                             $asObject );
        return $sectionList;
    }

     /*!
     \return the number of active orders
     */
    static function sectionCount()
    {
        $db = eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
        {
            $rows = $db->aggregate( 'ezsection', [ [ '$count' => 'count' ] ] );
            return !empty( $rows ) ? (int) $rows[0]['count'] : 0;
        }
        $countArray = $db->arrayQuery(  "SELECT count( * ) AS count FROM ezsection" );
        return $countArray[0]['count'];
    }

    /*!
     Will remove the current section from the database.
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    function removeThis( $conditions = null, $extraConditions = null )
    {
        $this->remove( array( "id" => $this->ID ), $extraConditions );
        // fetch() and fetchByIdentifier() keep the sections they read; a removed
        // one must not be found there for the rest of the request (or worker)
        unset( $GLOBALS['eZContentSectionObjectCache'][$this->ID] );
        if ( isset( $this->Identifier ) && $this->Identifier !== '' )
            unset( $GLOBALS['eZContentSectionObjectCache'][$this->Identifier] );
    }

    /*
     Check if this section is allowed to remove from the system
    */
    function canBeRemoved( $sectionID = false )
    {
        if ( $sectionID === false )
        {
            $sectionID = $this->attribute( 'id' );
        }

        $objects = eZPersistentObject::fetchObjectList( eZContentObject::definition(), null,
                                                        array( 'section_id' => $sectionID ) );
        $limitations = eZPolicyLimitation::findByType( 'Section', $sectionID, true, false );
        $userRoles = eZRole::fetchRolesByLimitation( 'section', $sectionID );

        if ( count( $objects ) > 0 or
             count( $limitations ) > 0 or
             count( $userRoles ) > 0 )
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * The scopes a section can be assigned with (SCOPE_*), in the order the administration interface offers them.
     *
     * @return string[]
     */
    static function scopes()
    {
        return array( self::SCOPE_SUBTREE, self::SCOPE_INHERITED, self::SCOPE_OBJECT );
    }

    /**
     * @param mixed $scope
     * @return bool true when $scope is one of the SCOPE_* constants
     */
    static function isValidScope( $scope )
    {
        return is_string( $scope ) && in_array( $scope, self::scopes(), true );
    }

    /**
     * The scope a form of the administration interface sent (the POST variable SectionScope).
     *
     * @param eZHTTPTool $http
     * @return string|false SCOPE_SUBTREE when the form sent none (as before there was a choice), false when the
     *                      value is not known
     */
    static function scopeFromHTTP( eZHTTPTool $http )
    {
        if ( !$http->hasPostVariable( 'SectionScope' ) )
            return self::SCOPE_SUBTREE;
        $scope = $http->postVariable( 'SectionScope' );
        return self::isValidScope( $scope ) ? $scope : false;
    }

    /**
     * applyTo(), except for a subtree too large to change in the request: when $object has one location and the
     * subtree below it has more nodes than content.ini [ContentJobSettings] NowLimit, the content job 'section' does
     * the work in the background, in batches, and this returns the job (the caller redirects to /content/job/<id>).
     * Without the content jobs engine, for SCOPE_OBJECT, for an object with several locations, or when the job is
     * refused (a lock, no permission: logged), it is applyTo() as before.
     *
     * @param eZContentObject $object
     * @param string $scope SCOPE_*
     * @return expContentJob|false|null the job that was started, else what applyTo() returned
     */
    public function applyToOrStartJob( eZContentObject $object, $scope = self::SCOPE_SUBTREE )
    {
        $jobView = 'Exponential\\View\\Kernel\\Content\\Job';
        $nodes = $scope !== self::SCOPE_OBJECT && self::isValidScope( $scope ) ? $object->attribute( 'assigned_nodes' ) : array();
        if ( count( $nodes ) === 1 && class_exists( $jobView ) && $jobView::engineReady() )
        {
            $node = reset( $nodes );
            $params = array( 'node_id' => (int)$node->attribute( 'node_id' ), 'section_id' => (int)$this->attribute( 'id' ), 'scope' => $scope );
            try
            {
                if ( !expContentJob::nowAllowed( 'section', $params ) )
                {
                    $error = '';
                    $job = $jobView::createJob( 'section', $params, (int)$node->attribute( 'node_id' ), $error );
                    if ( $job )
                        return $job;
                    eZDebug::writeWarning( 'The section is assigned in the request, the content job was refused: ' . $error, __METHOD__ );
                }
            }
            catch ( Exception $e )
            {
                // the engine failing must not break the assignment: it is done as before
                eZDebug::writeError( 'Content jobs: ' . $e->getMessage(), __METHOD__ );
            }
        }
        return $this->applyTo( $object, $scope );
    }

    /**
     * Assigns this section to $object, when the current user may (section/assign), at each of its locations with
     * the objects below it that $scope names (SCOPE_*; the default SCOPE_SUBTREE is what this method always did).
     * With SCOPE_OBJECT the object alone changes, whatever its locations.
     *
     * @param eZContentObject $object
     * @param string $scope
     * @return false|null false when the user may not assign the section or $scope is not known
     */
    public function applyTo( eZContentObject $object, $scope = self::SCOPE_SUBTREE )
    {
        $sectionID = $this->attribute( "id" );

        if ( !self::isValidScope( $scope ) )
        {
            eZDebug::writeError( "Unknown section assignment scope <" . ( is_scalar( $scope ) ? $scope : gettype( $scope ) ) . ">.", __METHOD__ );
            return false;
        }

        $currentUser = eZUser::currentUser();
        if ( !$currentUser->canAssignSectionToObject( $sectionID, $object ) )
        {
            eZDebug::writeError(
                "You do not have permissions to assign the section <" . $this->attribute( "name" ) .  "> to the object <" . $object->attribute( "name" ) . ">."
            );
            return false;
        }

        $assignedNodes = $object->attribute( "assigned_nodes" );
        if ( $scope === self::SCOPE_OBJECT && !empty( $assignedNodes ) )
        {
            // one object, one assignment, at its main location
            $mainNode = $object->attribute( "main_node" );
            $assignedNodes = array( $mainNode instanceof eZContentObjectTreeNode ? $mainNode : reset( $assignedNodes ) );
        }
        // SCOPE_INHERITED follows the section the object has now, also at its second location, when the first one has
        // changed it already
        $oldSectionID = $scope === self::SCOPE_INHERITED ? (int)$object->attribute( "section_id" ) : false;
        // The objects that change, found before anything changes, for the search index below: as the content job does,
        // without those below the object the user may not assign the section to (section/assign limitations)
        $objectIDArrays = array();
        foreach ( $assignedNodes as $node )
        {
            $objectIDArray = eZContentObjectTreeNode::sectionAssignmentObjectIDs( $node, $scope, $oldSectionID );
            $objectIDArrays[] = $objectIDArray ? eZContentObjectTreeNode::sectionAssignableObjectIDs( $objectIDArray, $sectionID, $currentUser ) : $objectIDArray;
        }

        $db = eZDB::instance();
        $db->begin();
        if ( !empty( $assignedNodes ) )
        {
            if ( eZOperationHandler::operationIsAvailable( "content_updatesection" ) )
            {
                foreach ( $assignedNodes as $node )
                {
                    eZOperationHandler::execute(
                        "content",
                        "updatesection",
                        array(
                            "node_id" => $node->attribute( "node_id" ),
                            "selected_section_id" => $sectionID,
                            "update_search_indexes" => false,
                            "scope" => $scope,
                            "old_section_id" => $oldSectionID,
                            "check_access" => true
                        ),
                        null,
                        true
                    );
                }
            }
            else
            {
                foreach ( $assignedNodes as $node )
                {
                    eZContentOperationCollection::updateSection( $node->attribute( "node_id" ), $sectionID, false, $scope, $oldSectionID, true );
                }
            }
        }
        else
        {
            // If there are no assigned nodes we should update db for the current object.
            $objectID = $object->attribute( "id" );
            $db->query( "UPDATE ezcontentobject SET section_id='$sectionID' WHERE id = '$objectID'" );
            $db->query( "UPDATE ezsearch_object_word_link SET section_id='$sectionID' WHERE  contentobject_id = '$objectID'" );
        }
        eZContentCacheManager::clearContentCacheIfNeeded( $object->attribute( "id" ) );
        $object->expireAllViewCache();
        $commitResult = $db->commit();

        // Update search indexes AFTER transaction is completed, so changes are actually available for Search Engine.
        if ( $commitResult ) {
            if ( !empty( $assignedNodes ) ) {
                foreach ( $objectIDArrays as $objectIDArray ) {
                    if ( is_array( $objectIDArray ) )
                    {
                        foreach (array_chunk($objectIDArray, 100) as $pagedObjectIDs) {
                            // Clear cache of affected objects in sub tree so they are up-to-date when indexed.
                            eZContentCacheManager::clearObjectViewCacheArray($pagedObjectIDs);
                            eZSearch::updateObjectsSection($pagedObjectIDs, $sectionID);
                        }
                    }
                }
            } else {
                eZSearch::updateObjectsSection(array($object->attribute("id")), $sectionID);
            }
        }
    }
}

?>
