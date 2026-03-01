<?php
/**
 * File containing the ezp7xContentRestController class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

/**
 * This controller is used for serving content
 */
class ezp7xRestContentController extends ezpRestMvcController
{
    /**
     * Expected Response groups for content viewing
     * @var string
     */
    const VIEWCONTENT_RESPONSEGROUP_METADATA = 'Metadata',
          VIEWCONTENT_RESPONSEGROUP_LOCATIONS = 'Locations',
          VIEWCONTENT_RESPONSEGROUP_FIELDS = 'Fields';

    /**
     * Expected Response groups for field viewing
     * @var string
     */
    const VIEWFIELDS_RESPONSEGROUP_FIELDVALUES = 'FieldValues',
          VIEWFIELDS_RESPONSEGORUP_METADATA = 'Metadata';

    /**
     * Expected Response groups for content children listing
     * @var string
     */
    const VIEWLIST_RESPONSEGROUP_METADATA = 'Metadata',
          VIEWLIST_RESPONSEGROUP_FIELDS = 'Fields';

    /**
     * Handles content requests per node or object ID
     *
     * Requests:
     * - POST /api/content/node/XXX
     * - POST /api/content/object/XXX
     *
     * Required HTTP parameters:
     * - node details
     * - fields / attributes
     *
     * Optional HTTP parameters:
     * - translation=xxx-XX: an optionally forced locale to return
     *
     * @return ezpRestMvcResult
     */
    public function doUpdateContentNode()
    {
        $this->setDefaultResponseGroups( array( self::VIEWCONTENT_RESPONSEGROUP_METADATA ) );
        $isNodeRequested = false;
	
        if ( isset( $this->nodeId ) )
        {
            $contentNode = eZContentObjectTreeNode::fetch( $this->nodeId );
            $content = eZContentObjectTreeNode::fetch( $this->nodeId )->attribute( 'object' );
            $contentParent = eZContentObjectTreeNode::fetch( $this->nodeId )->fetchParent();
            $isNodeRequested = true;
        }
/*        else if ( isset( $this->objectId ) )
        {
            $content = ezpContent::fromObjectId( $this->objectId );
        }*/

        
	/**
	 * Updates content object
	 *
	 * @param $params array(
	 *     'object'                  => eZContentObject         Content object
	 *     'attributes'              => array(                  Content object`s attributes
	 *         string identifier => string stringValue
	 *     ),
	 *     'parentNode'              => eZContentObjectTreeNode Parent node object, not necessary
	 *     'parentNodeID'            => int                     Content object`s parent node ID, not necessary
	 *     'additionalParentNodeIDs' => array                   additionalParentNodeIDs, Additional parent node ids
	 *     'visibility'              => bool                    Nodes visibility
	 * )
	 * @return bool true if object was updated, otherwise false
	 */

        $http = eZHTTPTool::instance();
	$pc = new nxcPowerContent( false, true );

	$visibility = true;

  	$user = eZUser::currentUser();
  	$userID = $user->attribute( 'contentobject_id' );

	// return var_dump($userID); die();



	// you need the NodeID (parent node)
	/*
	if (!$http->hasPostVariable( 'NodeID')) {
	    $pc->error( 'Missing mandatory parameter NodeID (the parent nodeid) so I know where to create it' );
	} else {
	    $nodeID = $http->postVariable( 'NodeID');
	}
	*/
        $attributes = $_POST;
	
        //return var_dump($attributes); die();

	$updateObjectParams = array(
	'object' => $content,
	'attributes' => $attributes,
	'parentNode' => $contentParent,
	'visibility' => $visibility
	);
	//return var_dump( $pc->updateObject( $updateObjectParams ) ); die('fin');

        $result = new ezpRestMvcResult();
         
        throw new ezpContentFieldNotFoundException( "'$this->nodeId' has been updated we don't know if it worked how do you feel?" );
        return $result;
    }

    /**
     * Handles content requests per node or object ID
     *
     * Requests:
     * - POST /api/content/node/XXX
     * - POST /api/content/object/XXX
     *
     * Required HTTP parameters:
     * - node details
     * - fields / attributes
     *
     * Optional HTTP parameters:
     * - translation=xxx-XX: an optionally forced locale to return
     *
     * @return ezpRestMvcResult
     */
    public function doDeleteContentNode()
    {
        $this->setDefaultResponseGroups( array( self::VIEWCONTENT_RESPONSEGROUP_METADATA ) );
        $isNodeRequested = false;
	
        if ( isset( $this->nodeId ) )
        {
            $contentNode = eZContentObjectTreeNode::fetch( $this->nodeId );
            $content = eZContentObjectTreeNode::fetch( $this->nodeId )->attribute( 'object' );
            // $contentParent = eZContentObjectTreeNode::fetch( $this->nodeId )->fetchParent();
            $isNodeRequested = true;
        }
	else if ( isset( $this->objectId ) )
        {
            $content = ezpContent::fromObjectId( $this->objectId );
        }

	$pc = new nxcPowerContent( false, true );
	$removeResult = $pc->removeObject( $content );

        $result = new ezpRestMvcResult();
         
        throw new ezpContentFieldNotFoundException( "The nodeId ". $this->nodeId ." has been removed. Please update your records." );
        return $result;
    }

    /**
     * Handles content requests per node or object ID
     *
     * Requests:
     * - POST /api/content/node/XXX
     * - POST /api/content/object/XXX
     *
     * Required HTTP parameters:
     * - node details
     * - fields / attributes
     *
     * Optional HTTP parameters:
     * - translation=xxx-XX: an optionally forced locale to return
     *
     * @return ezpRestMvcResult
     */
    public function doCreateContentNode()
    {
        $http = eZHTTPTool::instance();

        $this->setDefaultResponseGroups( array( self::VIEWCONTENT_RESPONSEGROUP_METADATA ) );
        $isNodeRequested = false;
	
	$pc = new nxcPowerContent( false, true );

	$visibility = true;

  	$user = eZUser::currentUser();
  	$userID = $user->attribute( 'contentobject_id' );

	// you need the NodeID (parent node)
	if (!$http->hasPostVariable( 'parentNodeID')) {
	    $pc->error( 'Missing mandatory parameter parentNodeID (the parent nodeid) so I know where to create it' );
	} else {
	    $parentNodeID = $http->postVariable( 'parentNodeID');
	}

	// you need the classIdentifier
	if (!$http->hasPostVariable( 'classIdentifier')) {
	    $pc->error( 'Missing mandatory parameter classIdentifier so I know who to create' );
	} else {
	    $classIdentifier = $http->postVariable( 'classIdentifier');
	}

	$class = eZContentClass::fetchByIdentifier( $classIdentifier );

	// you need the classIdentifier
	if (!$http->hasPostVariable( 'languageLocale')) {
	    $pc->error( 'Missing mandatory parameter languageLocale so I know who to create' );
	} else {
	    $languageLocale = $http->postVariable( 'languageLocale');
	}

        $attributes = $_POST;
	
	/**
         * Creates new content object and store it
         *
         * @param $params array(
         *     'class'                   => eZContentClass          Content class object
         *     'classIdentifier'         => string                  Content object`s class identifier, not necessary if class is set
         *     'parentNode'              => eZContentObjectTreeNode Parent node object
         *     'parentNodeID'            => int                     Content object`s parent node ID, not necessary if parentNode is set
         *     'attributes'              => array(                  Content object`s attributes
         *         string identifier => string stringValue
         *     ),
         *     'remoteID'                => string                  Content object`s remote ID, not necessary
         *     'ownerID'                 => int                     Owner`s content object ID, not necessary
         *     'sectionID'               => int                     Section ID, not necessary
         *     'languageLocal'           => string                  Language local, not necessary
         *     'publishDate'             => int                     Creation timestamp, if not specified - current timestamp will be used
         *     'additionalParentNodeIDs' => array                   additionalParentNodes, Additional parent node ids
         *     'versionStatus'           => int                     Published version status, not necessary
         *     'visibility'              => bool                    Nodes visibility
         * )
         * @return eZContentObject|bool Created content object if it was created, otherwise false
         */

	$parentNode = eZContentObjectTreeNode::fetch( $parentNodeID );

	$createObjectParams = array(
	'parentNode' => $parentNode,
	'class' => $class,
	'languageLocale' => $languageLocale,
	'attributes' => $attributes,
	'visibility' => $visibility
	);

	$pc->createObject( $createObjectParams );

        $result = new ezpRestMvcResult();
        $result->variables['message'] = 'Success: Created Node';

        return $result;
    }

    /**
     * Handles content requests per node or object ID
     *
     * Requests:
     * - GET /api/content/node/XXX
     * - GET /api/content/object/XXX
     *
     * Optional HTTP parameters:
     * - translation=xxx-XX: an optionally forced locale to return
     *
     * @return ezpRestMvcResult
     */
    public function doViewContent()
    {
        $this->setDefaultResponseGroups( array( self::VIEWCONTENT_RESPONSEGROUP_METADATA ) );
        $isNodeRequested = false;
        if ( isset( $this->nodeId ) )
        {
            $content = ezpContent::fromNodeId( $this->nodeId );
            $isNodeRequested = true;
        }
        else if ( isset( $this->objectId ) )
        {
            $content = ezpContent::fromObjectId( $this->objectId );
        }

        $result = new ezpRestMvcResult();

        // translation parameter
        if ( $this->hasContentVariable( 'Translation' ) )
            $content->setActiveLanguage( $this->getContentVariable( 'Translation' ) );

        // Handle metadata
        if ( $this->hasResponseGroup( self::VIEWCONTENT_RESPONSEGROUP_METADATA ) )
        {
            $objectMetadata = ezpRestContentModel::getMetadataByContent( $content );
            if ( $isNodeRequested )
            {
                $nodeMetadata = ezpRestContentModel::getMetadataByLocation( ezpContentLocation::fetchByNodeId( $this->nodeId ) );
                $objectMetadata = array_merge( $objectMetadata, $nodeMetadata );
            }
            $result->variables['metadata'] = $objectMetadata;
        }

        // Handle locations if requested
        if ( $this->hasResponseGroup( self::VIEWCONTENT_RESPONSEGROUP_LOCATIONS ) )
        {
            $result->variables['locations'] = ezpRestContentModel::getLocationsByContent( $content );
        }

        // Handle fields content if requested
        if ( $this->hasResponseGroup( self::VIEWCONTENT_RESPONSEGROUP_FIELDS ) )
        {
            $result->variables['fields'] = ezpRestContentModel::getFieldsByContent( $content );
        }

        // Add links to fields resources
        $result->variables['links'] = ezpRestContentModel::getFieldsLinksByContent( $content, $this->request );

        if ( $outputFormat = $this->getContentVariable( 'OutputFormat' ) )
        {
            $renderer = ezpRestContentRenderer::getRenderer( $outputFormat, $content, $this );
            $result->variables['renderedOutput'] = $renderer->render();
        }

        return $result;
    }

    /**
     * Handles a content request with fields per object or node id
     * Request: GET /api/content/object/XXX/fields
     * Request: GET /api/content/node/XXX/fields
     *
     * @return ezpRestMvcResult
     */
    public function doViewFields()
    {
        $this->setDefaultResponseGroups( array( self::VIEWFIELDS_RESPONSEGROUP_FIELDVALUES ) );

        $isNodeRequested = false;
        if ( isset( $this->nodeId ) )
        {
            $content = ezpContent::fromNodeId( $this->nodeId );
            $isNodeRequested = true;
        }
        else if ( isset( $this->objectId ) )
        {
            $content = ezpContent::fromObjectId( $this->objectId );
        }

        $result = new ezpRestMvcResult();

        // translation parameter
        if ( $this->hasContentVariable( 'Translation' ) )
            $content->setActiveLanguage( $this->getContentVariable( 'Translation' ) );

        // Handle field values
        if ( $this->hasResponseGroup( self::VIEWFIELDS_RESPONSEGROUP_FIELDVALUES ) )
        {
            $result->variables['fields'] = ezpRestContentModel::getFieldsByContent( $content );
        }

        // Handle object/node metadata
        if ( $this->hasResponseGroup( self::VIEWFIELDS_RESPONSEGORUP_METADATA ) )
        {
            $objectMetadata = ezpRestContentModel::getMetadataByContent( $content );
            if ( $isNodeRequested )
            {
                $nodeMetadata = ezpRestContentModel::getMetadataByLocation( ezpContentLocation::fetchByNodeId( $this->nodeId ) );
                $objectMetadata = array_merge( $objectMetadata, $nodeMetadata );
            }
            $result->variables['metadata'] = $objectMetadata;
        }

        return $result;
    }

    /**
     * Handles a content unique field request through an object or node ID
     *
     * Requests:
     * - GET /api/content/node/:nodeId/field/:fieldIdentifier
     * - GET /api/content/object/:objectId/field/:fieldIdentifier
     *
     * @return ezpRestMvcResult
     */
    public function doViewField()
    {
        $this->setDefaultResponseGroups( array( self::VIEWFIELDS_RESPONSEGROUP_FIELDVALUES ) );

        $isNodeRequested = false;
        if ( isset( $this->nodeId ) )
        {
            $isNodeRequested = true;
            $content = ezpContent::fromNodeId( $this->nodeId );
        }
        else if ( isset( $this->objectId ) )
        {
            $content = ezpContent::fromObjectId( $this->objectId );
        }

        if ( !isset( $content->fields->{$this->fieldIdentifier} ) )
        {
            throw new ezpContentFieldNotFoundException( "'$this->fieldIdentifier' field is not available for this content." );
        }

        // Translation parameter
        if ( $this->hasContentVariable( 'Translation' ) )
            $content->setActiveLanguage( $this->getContentVariable( 'Translation' ) );

        $result = new ezpRestMvcResult();

        // Field data
        if ( $this->hasResponseGroup( self::VIEWFIELDS_RESPONSEGROUP_FIELDVALUES ) )
        {
            $result->variables['fields'][$this->fieldIdentifier] = ezpRestContentModel::attributeOutputData( $content->fields->{$this->fieldIdentifier} );
        }

        // Handle object/node metadata
        if ( $this->hasResponseGroup( self::VIEWFIELDS_RESPONSEGORUP_METADATA ) )
        {
            $objectMetadata = ezpRestContentModel::getMetadataByContent( $content, $isNodeRequested );
            if ( $isNodeRequested )
            {
                $nodeMetadata = ezpRestContentModel::getMetadataByLocation( ezpContentLocation::fetchByNodeId( $this->nodeId ) );
                $objectMetadata = array_merge( $objectMetadata, $nodeMetadata );
            }
            $result->variables['metadata'] = $objectMetadata;
        }

        return $result;
    }

    /**
     * Handles a content request to view a node children list
     * Requests :
     *   - GET /api/v1/content/node/<nodeId>/list(/offset/<offset>/limit/<limit>/sort/<sortKey>/<sortType>)
     *   - Every parameters in parenthesis are optional. However, to have offset/limit and sort, the order is mandatory
     *     (you can't provide sorting params before limit params). This is due to a limitation in the regexp route.
     *   - Following requests are valid :
     *     - /api/ezp/content/node/2/list/sort/name => will display 10 (default limit) children of node 2, sorted by ascending name
     *     - /api/ezp/content/node/2/list/limit/50/sort/published/desc => will display 50 children of node 2, sorted by descending publishing date
     *     - /api/ezp/content/node/2/list/offset/100/limit/50/sort/published/desc => will display 50 children of node 2 starting from offset 100, sorted by descending publishing date
     *
     * Default values :
     *   - offset : 0
     *   - limit : 10
     *   - sortType : asc
     */
    public function doList()
    {
        $this->setDefaultResponseGroups( array( self::VIEWLIST_RESPONSEGROUP_METADATA ) );
        $result = new ezpRestMvcResult();
        $crit = new ezpContentCriteria();

        // Location criteria
        // Hmm, the following sequence is too long...
        $crit->accept[] = ezpContentCriteria::location()->subtree( ezpContentLocation::fetchByNodeId( $this->nodeId ) );
        $crit->accept[] = ezpContentCriteria::depth( 1 ); // Fetch children only

        // Limit criteria
        $offset = isset( $this->offset ) ? $this->offset : 0;
        $limit = isset( $this->limit ) ? $this->limit : 10;
        $crit->accept[] = ezpContentCriteria::limit()->offset( $offset )->limit( $limit );

        // Sort criteria
        if ( isset( $this->sortKey ) )
        {
            $sortOrder = isset( $this->sortType ) ? $this->sortType : 'asc';
            $crit->accept[] = ezpContentCriteria::sorting( $this->sortKey, $sortOrder );
        }

        $result->variables['childrenNodes'] = ezpRestContentModel::getChildrenList( $crit, $this->request, $this->getResponseGroups() );
        // REST links to children nodes
        // Little dirty since this should belong to the model layer, but I don't want to pass the router nor the full controller to the model
        $contentQueryString = $this->request->getContentQueryString( true );
        for ( $i = 0, $iMax = count( $result->variables['childrenNodes'] ); $i < $iMax; ++$i )
        {
            $linkURI = $this->getRouter()->generateUrl( 'ezpNode', array( 'nodeId' => $result->variables['childrenNodes'][$i]['nodeId'] ) );
            $result->variables['childrenNodes'][$i]['link'] = $this->request->getHostURI().$linkURI.$contentQueryString;
        }

        // Handle Metadata
        if ( $this->hasResponseGroup( self::VIEWLIST_RESPONSEGROUP_METADATA ) )
        {
            $childrenCount = ezpRestContentModel::getChildrenCount( $crit );
            $result->variables['metadata'] = array(
                'childrenCount' => $childrenCount,
                'parentNodeId'  => $this->nodeId
            );

        }

        return $result;
    }

    /**
     * Counts children of a given node
     * Request :
     *   - GET /api/ezp/content/node/childrenCount
     */
    public function doCountChildren()
    {
        $this->setDefaultResponseGroups( array( self::VIEWLIST_RESPONSEGROUP_METADATA ) );
        $result = new ezpRestMvcResult();

        if ( $this->hasResponseGroup( self::VIEWLIST_RESPONSEGROUP_METADATA ) )
        {
            $crit = new ezpContentCriteria();
            $crit->accept[] = ezpContentCriteria::location()->subtree( ezpContentLocation::fetchByNodeId( $this->nodeId ) );
            $crit->accept[] = ezpContentCriteria::depth( 1 ); // Fetch children only
            $childrenCount = ezpRestContentModel::getChildrenCount( $crit );
            $result->variables['metadata'] = array(
                'childrenCount' => $childrenCount,
                'parentNodeId'  => $this->nodeId
            );
        }

        return $result;
    }
}
?>
