<?php
/**
 * Where the GraphQL resolvers get content from: the kernel's content tree, read as the current user. Every method
 * checks the user's rights (content/read, with its limitations: the node's canRead(), the subtree fetch's policy
 * limitation) and the classes the schema shows; what the user may not read, or what the schema does not show, is
 * answered as not there (null, or left out of a list), so an answer never tells whether it exists.
 *
 * A test extends the class to answer without a database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLRepository
{
    /** The values of the enum ContentSortField and the sort fields of the kernel they stand for. */
    const SORT_FIELDS = array( 'PATH' => 'path', 'PUBLISHED' => 'published', 'MODIFIED' => 'modified', 'SECTION' => 'section',
                               'DEPTH' => 'depth', 'CLASS_IDENTIFIER' => 'class_identifier', 'CLASS_NAME' => 'class_name',
                               'PRIORITY' => 'priority', 'NAME' => 'name', 'NODE_ID' => 'node_id' );

    /** @var expGraphQLClassCatalogue */
    protected $catalogue;

    /** @var array ClassList, ExcludedClassList */
    protected $classSettings;

    /**
     * @param expGraphQLClassCatalogue $catalogue the classes of the schema
     * @param array|null $classSettings ClassList and ExcludedClassList (null: from graphql.ini)
     */
    public function __construct( expGraphQLClassCatalogue $catalogue, $classSettings = null )
    {
        $this->catalogue = $catalogue;
        $this->classSettings = is_array( $classSettings ) ? $classSettings : expGraphQLClassCatalogue::settings();
    }

    /**
     * @return expGraphQLClassCatalogue the classes of the schema
     */
    public function catalogue()
    {
        return $this->catalogue;
    }

    /**
     * @param string $classIdentifier
     * @return bool the schema has a type for the class
     */
    public function isExposed( $classIdentifier )
    {
        return $this->catalogue->classByIdentifier( $classIdentifier ) !== null;
    }

    /**
     * @param eZContentObjectTreeNode|null $node
     * @param string|false $language
     * @return expGraphQLContent|null the content at the node, when the user may read it and the schema shows it
     */
    protected function contentAtNode( $node, $language )
    {
        if ( !$node instanceof eZContentObjectTreeNode || !$node->canRead() )
            return null;
        $object = $node->object();
        if ( !$object instanceof eZContentObject || !$this->isExposed( (string)$object->attribute( 'class_identifier' ) ) )
            return null;
        return new expGraphQLContent( $object, $node, $language );
    }

    /**
     * @param int $nodeId
     * @param string|false $language
     * @return expGraphQLContent|null
     */
    public function contentByNodeId( $nodeId, $language = false )
    {
        return (int)$nodeId > 0 ? $this->contentAtNode( eZContentObjectTreeNode::fetch( (int)$nodeId ), $language ) : null;
    }

    /**
     * @param int $nodeId
     * @param string|false $language
     * @return expGraphQLLocation|null the location, when the user may read it (whatever its class)
     */
    public function locationById( $nodeId, $language = false )
    {
        $node = (int)$nodeId > 0 ? eZContentObjectTreeNode::fetch( (int)$nodeId ) : null;
        return $node instanceof eZContentObjectTreeNode && $node->canRead() ? new expGraphQLLocation( $node, $language ) : null;
    }

    /**
     * @param string $remoteId the remote id of the location
     * @param string|false $language
     * @return expGraphQLContent|null
     */
    public function contentByNodeRemoteId( $remoteId, $language = false )
    {
        return $remoteId !== '' ? $this->contentAtNode( eZContentObjectTreeNode::fetchByRemoteID( (string)$remoteId ), $language ) : null;
    }

    /**
     * @param string $pathString /1/2/58/
     * @param string|false $language
     * @return expGraphQLContent|null
     */
    public function contentByPathString( $pathString, $language = false )
    {
        if ( !preg_match( '#^/?((?:\d+/)*\d+)/?$#', (string)$pathString, $m ) )
            return null;
        $ids = explode( '/', $m[1] );
        return $this->contentByNodeId( (int)end( $ids ), $language );
    }

    /**
     * @param string $urlAlias about/company (with or without a leading /)
     * @param string|false $language
     * @return expGraphQLContent|null
     */
    public function contentByUrlAlias( $urlAlias, $language = false )
    {
        $urlAlias = trim( (string)$urlAlias, '/' );
        if ( $urlAlias === '' )
            return $this->contentByNodeId( (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' ), $language );
        $uri = eZURI::instance( $urlAlias );
        $translated = eZURLAliasML::translate( $uri );
        if ( $translated !== true )
            return null;
        if ( preg_match( '#^content/view/[^/]+/(\d+)#', (string)$uri->elements(), $m ) )
            return $this->contentByNodeId( (int)$m[1], $language );
        return null;
    }

    /**
     * @param int $id the content object id
     * @param string|false $language
     * @return expGraphQLContent|null the content at its main location
     */
    public function contentById( $id, $language = false )
    {
        $object = (int)$id > 0 ? eZContentObject::fetch( (int)$id ) : null;
        return $this->contentOfObject( $object, $language );
    }

    /**
     * @param string $remoteId the remote id of the content object
     * @param string|false $language
     * @return expGraphQLContent|null
     */
    public function contentByRemoteId( $remoteId, $language = false )
    {
        $object = $remoteId !== '' ? eZContentObject::fetchByRemoteID( (string)$remoteId ) : null;
        return $this->contentOfObject( $object, $language );
    }

    /**
     * @param eZContentObject|null $object
     * @param string|false $language
     * @return expGraphQLContent|null
     */
    protected function contentOfObject( $object, $language )
    {
        if ( !$object instanceof eZContentObject || (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED || !$object->canRead()
             || !$this->isExposed( (string)$object->attribute( 'class_identifier' ) ) )
            return null;
        $node = $object->attribute( 'main_node' );
        return new expGraphQLContent( $object, $node instanceof eZContentObjectTreeNode ? $node : null, $language );
    }

    /**
     * @param expGraphQLLocation $location
     * @return expGraphQLContent|null the content of the parent location
     */
    public function parent( expGraphQLLocation $location )
    {
        if ( $location->depth() <= 1 )
            return null;
        return $this->contentByNodeId( $location->parentId(), $location->language() );
    }

    /**
     * @param expGraphQLLocation $location
     * @return expGraphQLContent|null the content at the location (when the user may read it)
     */
    public function contentAtLocation( expGraphQLLocation $location )
    {
        return $this->contentAtNode( $location->node(), $location->language() );
    }

    /**
     * The kernel's subtree fetch parameters of a list.
     *
     * @param expGraphQLLocation $parent
     * @param array $params classes, sortField, sortOrder, depth (limit and offset are added by children())
     * @return array|null null: the list is empty (none of the classes asked for is in the schema)
     */
    protected function subTreeParams( expGraphQLLocation $parent, array $params )
    {
        $fetch = array( 'AsObject' => true, 'LoadDataMap' => true );
        // depth 0: the whole subtree (the content of a tag)
        if ( !isset( $params['depth'] ) || (int)$params['depth'] !== 0 )
        {
            $fetch['Depth'] = max( 1, isset( $params['depth'] ) ? (int)$params['depth'] : 1 );
            $fetch['DepthOperator'] = 'le';
        }
        // tagged with one of these tags or their synonyms (eztags' extended attribute filter)
        if ( !empty( $params['tagIds'] ) )
            $fetch['ExtendedAttributeFilter'] = array( 'id' => 'TagsAttributeFilter',
                                                       'params' => array( 'tag_id' => array_map( 'intval', (array)$params['tagIds'] ), 'include_synonyms' => true ) );
        $classes = isset( $params['classes'] ) && is_array( $params['classes'] ) ? array_values( array_filter( $params['classes'], 'strlen' ) ) : array();
        if ( $classes )
        {
            $classes = array_values( array_filter( $classes, array( $this, 'isExposed' ) ) );
            if ( !$classes )
                return null;
            $fetch['ClassFilterType'] = 'include';
            $fetch['ClassFilterArray'] = $classes;
        }
        else if ( !empty( $this->classSettings['ClassList'] ) )
        {
            $fetch['ClassFilterType'] = 'include';
            $fetch['ClassFilterArray'] = $this->catalogue->identifiers();
        }
        else if ( !empty( $this->classSettings['ExcludedClassList'] ) )
        {
            $fetch['ClassFilterType'] = 'exclude';
            $fetch['ClassFilterArray'] = array_values( $this->classSettings['ExcludedClassList'] );
        }
        if ( !empty( $params['sortField'] ) && isset( self::SORT_FIELDS[$params['sortField']] ) )
            $fetch['SortBy'] = array( self::SORT_FIELDS[$params['sortField']], !isset( $params['sortOrder'] ) || $params['sortOrder'] !== 'DESC' );
        else
            $fetch['SortBy'] = $parent->sortArray();
        return $fetch;
    }

    /**
     * @param expGraphQLLocation $parent
     * @param array $params classes, sortField, sortOrder, depth, limit, offset
     * @return expGraphQLContent[] the children (or the content down to depth), as the user may read them
     */
    public function children( expGraphQLLocation $parent, array $params )
    {
        $fetch = $this->subTreeParams( $parent, $params );
        if ( $fetch === null || ( isset( $params['limit'] ) && (int)$params['limit'] <= 0 ) )
            return array();
        $fetch['Limit'] = isset( $params['limit'] ) ? (int)$params['limit'] : 25;
        $fetch['Offset'] = isset( $params['offset'] ) ? max( 0, (int)$params['offset'] ) : 0;
        $items = array();
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $fetch, $parent->id() );
        foreach ( is_array( $nodes ) ? $nodes : array() as $node )
        {
            $object = $node->object();
            if ( $object instanceof eZContentObject && $this->isExposed( (string)$object->attribute( 'class_identifier' ) ) )
                $items[] = new expGraphQLContent( $object, $node, $parent->language() );
        }
        return $items;
    }

    /**
     * @param expGraphQLLocation $parent
     * @param array $params classes, depth
     * @return int how many there are for the user
     */
    public function childrenCount( expGraphQLLocation $parent, array $params )
    {
        $fetch = $this->subTreeParams( $parent, $params );
        if ( $fetch === null )
            return 0;
        unset( $fetch['SortBy'], $fetch['AsObject'], $fetch['LoadDataMap'] );
        return (int)eZContentObjectTreeNode::subTreeCountByNodeID( $fetch, $parent->id() );
    }

    /**
     * @param int $objectId
     * @param string|false $language
     * @return expGraphQLContent|null a related object, when the user may read it
     */
    public function related( $objectId, $language = false )
    {
        return $this->contentById( $objectId, $language );
    }

    /**
     * The related objects of a page at once (the batch of expGraphQLContext::loader( 'content' )): one query for the
     * main locations with their objects, one for their fields.
     *
     * @param int[] $objectIds
     * @param string|false $language
     * @return array object id => expGraphQLContent|null (null: not there, not readable or not in the schema)
     */
    public function relatedList( array $objectIds, $language = false )
    {
        $ids = array_values( array_unique( array_filter( array_map( 'intval', $objectIds ) ) ) );
        $answer = array_fill_keys( $ids, null );
        if ( !$ids )
            return $answer;
        $nodes = eZContentObjectTreeNode::findMainNodeArray( $ids );
        $readable = array();
        foreach ( is_array( $nodes ) ? $nodes : array() as $node )
        {
            if ( !$node instanceof eZContentObjectTreeNode )
                continue;
            $object = $node->object();
            if ( !$object instanceof eZContentObject || (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED || !$node->canRead()
                 || !$this->isExposed( (string)$object->attribute( 'class_identifier' ) ) )
                continue;
            $readable[(int)$object->attribute( 'id' )] = $node;
        }
        if ( $readable )
            eZContentObject::fillNodeListAttributes( array_values( $readable ) );
        foreach ( $ids as $id )
        {
            if ( isset( $readable[$id] ) )
                $answer[$id] = new expGraphQLContent( $readable[$id]->object(), $readable[$id], $language );
            else if ( !is_array( $nodes ) || !self::listHasObject( $nodes, $id ) )
                $answer[$id] = $this->contentById( $id, $language ); // an object without a location
        }
        return $answer;
    }

    /**
     * @param array $nodes
     * @param int $objectId
     * @return bool one of the nodes is a location of the object
     */
    protected static function listHasObject( array $nodes, $objectId )
    {
        foreach ( $nodes as $node )
        {
            if ( $node instanceof eZContentObjectTreeNode && (int)$node->attribute( 'contentobject_id' ) === (int)$objectId )
                return true;
        }
        return false;
    }

    /**
     * The content at several locations at once (the batch of expGraphQLContext::loader( 'node' ): parents).
     *
     * @param int[] $nodeIds
     * @param string|false $language
     * @return array node id => expGraphQLContent|null
     */
    public function contentByNodeIdList( array $nodeIds, $language = false )
    {
        $ids = array_values( array_unique( array_filter( array_map( 'intval', $nodeIds ) ) ) );
        $answer = array_fill_keys( $ids, null );
        if ( !$ids )
            return $answer;
        $nodes = eZContentObjectTreeNode::fetch( $ids );
        if ( $nodes instanceof eZContentObjectTreeNode )
            $nodes = array( $nodes );
        foreach ( is_array( $nodes ) ? $nodes : array() as $node )
        {
            $content = $this->contentAtNode( $node, $language );
            if ( $content !== null )
                $answer[(int)$node->attribute( 'node_id' )] = $content;
        }
        return $answer;
    }

    /**
     * The names of several objects at once (the owners of a page of content): one query.
     *
     * @param int[] $objectIds
     * @return array object id => name (null: no such object)
     */
    public function objectNames( array $objectIds )
    {
        $ids = array_values( array_unique( array_filter( array_map( 'intval', $objectIds ) ) ) );
        $answer = array_fill_keys( $ids, null );
        if ( !$ids )
            return $answer;
        foreach ( (array)eZContentObject::fetchIDArray( $ids ) as $id => $object )
        {
            if ( $object instanceof eZContentObject )
                $answer[(int)$id] = (string)$object->attribute( 'name', true );
        }
        return $answer;
    }

    /**
     * Searches with the search engine of the installation (site.ini [SearchSettings] SearchEngine: the kernel's own,
     * or Solr through ezfind), which applies the user's rights; only classes of the schema are asked for and answered.
     *
     * @param array $params text, classes (identifiers), subtree (node ids), sections (ids), publishedAfter (ISO 8601),
     *                      facets (fields: class, section, author ...; engines with facets only), language, limit, offset
     * @return array items (expGraphQLContent[]), count, engine (its class), facets (field, values: value, name, count)
     */
    public function search( array $params )
    {
        $empty = array( 'items' => array(), 'count' => 0, 'engine' => self::searchEngineName(), 'facets' => array() );
        $classIds = $this->searchClassIds( isset( $params['classes'] ) ? (array)$params['classes'] : array() );
        if ( $classIds === array() )
            return $empty;
        $search = array( 'SearchOffset' => isset( $params['offset'] ) ? max( 0, (int)$params['offset'] ) : 0,
                         'SearchLimit' => isset( $params['limit'] ) ? max( 0, (int)$params['limit'] ) : 10,
                         'SearchContentClassID' => $classIds );
        if ( !empty( $params['subtree'] ) )
            $search['SearchSubTreeArray'] = array_values( array_filter( array_map( 'intval', (array)$params['subtree'] ) ) );
        if ( !empty( $params['sections'] ) )
            $search['SearchSectionID'] = array_values( array_filter( array_map( 'intval', (array)$params['sections'] ) ) );
        $after = self::searchTime( isset( $params['publishedAfter'] ) ? $params['publishedAfter'] : null, 'publishedAfter' );
        $before = self::searchTime( isset( $params['publishedBefore'] ) ? $params['publishedBefore'] : null, 'publishedBefore' );
        if ( $after !== null || $before !== null )
        {
            // the kernel's engine: SearchTimestamp, a time or array( from, to ); ezfind: a filter on the published date
            $search['SearchDate'] = 5;
            $search['SearchTimestamp'] = $before !== null ? array( $after !== null ? $after : 1, $before ) : $after;
            $search['Filter'] = array( 'meta_published_dt:[' . ( $after !== null ? gmdate( 'Y-m-d\TH:i:s\Z', $after ) : '*' ) . ' TO '
                                       . ( $before !== null ? gmdate( 'Y-m-d\TH:i:s\Z', $before ) : '*' ) . ']' );
        }
        if ( !empty( $params['facets'] ) )
        {
            $search['Facet'] = array();
            foreach ( (array)$params['facets'] as $field )
                $search['Facet'][] = array( 'field' => (string)$field, 'limit' => 20 );
        }
        if ( !empty( $params['language'] ) )
            $search['SearchLanguages'] = array( (string)$params['language'] );
        $result = eZSearch::search( isset( $params['text'] ) ? (string)$params['text'] : '', $search );
        if ( !is_array( $result ) )
            return $empty;
        $items = array();
        foreach ( isset( $result['SearchResult'] ) ? (array)$result['SearchResult'] : array() as $node )
        {
            if ( !$node instanceof eZContentObjectTreeNode )
                continue;
            $object = $node->object();
            if ( $object instanceof eZContentObject && $this->isExposed( (string)$object->attribute( 'class_identifier' ) ) )
                $items[] = new expGraphQLContent( $object, $node, isset( $params['language'] ) ? $params['language'] : false );
        }
        return array( 'items' => $items, 'count' => isset( $result['SearchCount'] ) ? (int)$result['SearchCount'] : count( $items ),
                      'engine' => $empty['engine'],
                      'facets' => self::searchFacets( isset( $result['SearchExtras'] ) ? $result['SearchExtras'] : null, (array)( isset( $params['facets'] ) ? $params['facets'] : array() ) ) );
    }

    /**
     * @param string|null $value an ISO 8601 date or time
     * @param string $argument the argument's name, for the error
     * @return int|null a Unix time
     */
    protected static function searchTime( $value, $argument )
    {
        if ( $value === null || $value === '' )
            return null;
        $time = strtotime( (string)$value );
        if ( $time === false )
            throw new expGraphQLError( expGraphQLMessage::tr( '%argument is not a date (ISO 8601, 2026-10-30).', array( '%argument' => $argument ) ),
                                       expGraphQLError::BAD_USER_INPUT );
        return $time;
    }

    /**
     * @param string[] $identifiers the classes a search asks for (none: every class of the schema)
     * @return int[]|int the class ids to search, -1 for every class; an empty array: none of them is in the schema
     */
    protected function searchClassIds( array $identifiers )
    {
        $identifiers = array_values( array_filter( $identifiers, 'strlen' ) );
        if ( !$identifiers )
        {
            // only when the schema leaves classes out: every class it has
            if ( empty( $this->classSettings['ClassList'] ) && empty( $this->classSettings['ExcludedClassList'] ) )
                return -1;
            $identifiers = $this->catalogue->identifiers();
        }
        $ids = array();
        foreach ( $identifiers as $identifier )
        {
            $class = $this->catalogue->classByIdentifier( $identifier );
            if ( $class !== null && !empty( $class['id'] ) )
                $ids[] = (int)$class['id'];
        }
        return $ids;
    }

    /**
     * @return string the class of the search engine of the siteaccess
     */
    public static function searchEngineName()
    {
        $engine = eZSearch::getEngine();
        return is_object( $engine ) ? get_class( $engine ) : '';
    }

    /**
     * The facets of an engine that has them (ezfind's SearchExtras: facet_fields with nameList and countList).
     *
     * @param mixed $extras
     * @param string[] $fields the facets asked for, in their order
     * @return array[] field, values (value, name, count)
     */
    public static function searchFacets( $extras, array $fields )
    {
        if ( !is_object( $extras ) || !method_exists( $extras, 'attribute' ) )
            return array();
        $facets = array();
        foreach ( array_values( (array)$extras->attribute( 'facet_fields' ) ) as $index => $facet )
        {
            if ( !is_array( $facet ) )
                continue;
            $values = array();
            $names = isset( $facet['nameList'] ) ? (array)$facet['nameList'] : array();
            foreach ( isset( $facet['countList'] ) ? (array)$facet['countList'] : array() as $value => $count )
                $values[] = array( 'value' => (string)$value, 'name' => isset( $names[$value] ) ? (string)$names[$value] : (string)$value, 'count' => (int)$count );
            $facets[] = array( 'field' => isset( $fields[$index] ) ? (string)$fields[$index] : (isset( $facet['field'] ) ? (string)$facet['field'] : ''),
                               'values' => $values );
        }
        return $facets;
    }

    /**
     * Makes the URL aliases of a page of content in two queries, so that url and urlAlias of every item are read
     * from memory (eZURLAliasML::primePathsByActionLists()).
     *
     * @param expGraphQLContent[] $items
     */
    public function primeUrlAliases( array $items )
    {
        $nodes = array();
        foreach ( $items as $item )
        {
            $location = $item instanceof expGraphQLContent ? $item->location() : null;
            $node = $location ? $location->node() : null;
            if ( $node instanceof eZContentObjectTreeNode )
                $nodes[] = $node;
        }
        if ( $nodes && method_exists( 'eZURLAliasML', 'primePathsForNodes' ) )
            eZURLAliasML::primePathsForNodes( $nodes );
    }

    /**
     * @return array the content languages of the installation: code, name, locale
     */
    public function languages()
    {
        $languages = array();
        foreach ( eZContentLanguage::fetchList() as $language )
            $languages[] = array( 'code' => (string)$language->attribute( 'locale' ), 'name' => (string)$language->attribute( 'name' ) );
        return $languages;
    }

    /**
     * @return array the siteaccess: name, languages (the prioritized language codes), rootNodeId
     */
    public function siteAccess()
    {
        return array( 'name' => expGraphQL::currentSiteAccess(),
                      'languages' => array_values( (array)eZContentLanguage::prioritizedLanguageCodes() ),
                      'rootNodeId' => (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' ) );
    }
}

?>
