<?php
/**
 * ezjscore/call/exppoll::<method>: polls (content objects of the poll class with an option attribute that collects
 * information): list, results, vote. A vote is one information collection; the collect.ini rules decide whether
 * a visitor may vote once or often and whether anonymous voting is allowed.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expPollServices extends expCommunityBase
{
    public static $services = array(
        'list' => array( 'summary' => 'The polls below a node with their question and number of votes', 'access' => 'public', 'write' => false,
            'args' => array( 'parent_node_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of polls' ),
        'view' => array( 'summary' => 'One poll: question, choices, votes per choice', 'access' => 'public', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'poll' ),
        'results' => array( 'summary' => 'The results of a poll: votes and percent per choice', 'access' => 'public', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'total, choices' ),
        'choices' => array( 'summary' => 'The choices of a poll', 'access' => 'public', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'list of choices' ),
        'canVote' => array( 'summary' => 'Whether the current visitor may vote now, and why not', 'access' => 'public', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'can, reason' ),
        'myVote' => array( 'summary' => 'The choice the current visitor voted for', 'access' => 'public', 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => 'choice id or null' ),
        'vote' => array( 'summary' => 'Votes for a choice (POST node_id, choice)', 'access' => array( 'content', 'read' ), 'write' => true, 'args' => array( 'node_id' => 'int POST', 'choice' => 'int POST' ), 'returns' => 'the results' ),
        'latest' => array( 'summary' => 'The newest polls', 'access' => 'public', 'write' => false, 'args' => array( 'limit' => 'int' ), 'returns' => 'list of polls' ),
        'create' => array( 'summary' => 'Creates a poll under a node: name, question and choices', 'access' => array( 'content', 'create' ), 'write' => true,
            'args' => array( 'parent_node_id' => 'int POST', 'name' => 'string POST', 'question' => 'string POST', 'choices' => 'list POST' ), 'returns' => 'the poll' ),
        'remove' => array( 'summary' => 'Removes a poll (to the trash) with its votes', 'access' => array( 'content', 'remove' ), 'write' => true, 'args' => array( 'node_id' => 'int POST' ), 'returns' => 'removed node' ),
        'resetVotes' => array( 'summary' => 'Removes every vote of a poll', 'access' => array( 'infocollector', 'read' ), 'write' => true, 'args' => array( 'node_id' => 'int POST' ), 'returns' => 'removed votes' ),
    );

    protected static function pollClass()
    {
        $ini = eZINI::instance( 'expservices.ini' );
        return $ini->hasVariable( 'Community', 'PollClass' ) ? $ini->variable( 'Community', 'PollClass' ) : 'poll';
    }

    /** The poll node and its option attribute that collects votes. */
    protected static function poll( $nodeId )
    {
        $node = self::node( $nodeId, 'read' );
        if ( $node->attribute( 'class_identifier' ) !== self::pollClass() )
            throw new expServiceException( "Node $nodeId is not a poll", 404 );
        foreach ( $node->attribute( 'object' )->currentVersion()->contentObjectAttributes() as $a )
            if ( $a->attribute( 'data_type_string' ) === 'ezoption' && $a->contentClassAttributeIsInformationCollector() )
                return array( $node, $a );
        throw new expServiceException( 'The poll has no option attribute that collects votes', 422 );
    }

    protected static function tally( eZContentObjectAttribute $a )
    {
        $counts = eZInformationCollection::fetchCountList( $a->attribute( 'id' ) );
        $total = array_sum( array_map( 'intval', $counts ) );
        $out = array();
        foreach ( (array)$a->content()->attribute( 'option_list' ) as $c )
        {
            $n = isset( $counts[$c['id']] ) ? (int)$counts[$c['id']] : 0;
            $out[] = array( 'id' => (int)$c['id'], 'value' => $c['value'], 'votes' => $n, 'percent' => $total ? round( $n * 100 / $total, 1 ) : 0.0 );
        }
        return array( 'total' => $total, 'choices' => $out );
    }

    protected static function encode( eZContentObjectTreeNode $node, eZContentObjectAttribute $a )
    {
        $r = self::tally( $a );
        return array( 'node_id' => (int)$node->attribute( 'node_id' ), 'object_id' => (int)$node->attribute( 'contentobject_id' ), 'name' => $node->attribute( 'name' ),
            'question' => (string)$a->content()->attribute( 'name' ), 'total' => $r['total'], 'choices' => $r['choices'] );
    }

    protected static function myCollection( eZContentObject $o )
    {
        return eZInformationCollection::fetchByUserIdentifier( eZInformationCollection::currentUserIdentifier(), $o->attribute( 'id' ) );
    }

    public static function list( array $args )
    {
        self::guard( 'list' );
        $parent = self::arg( $args, 0, 'int', 1 );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        self::node( $parent, 'read' );
        $p = array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::pollClass() ), 'SortBy' => array( array( 'published', false ), array( 'node_id', false ) ), 'Limit' => $limit, 'Offset' => $offset );
        $items = array();
        foreach ( (array)eZContentObjectTreeNode::subTreeByNodeID( $p, $parent ) as $n )
        {
            try
            {
                list( $node, $a ) = self::poll( $n->attribute( 'node_id' ) );
                $items[] = self::encode( $node, $a );
            }
            catch ( expServiceException $e )
            {
                $items[] = array( 'node_id' => (int)$n->attribute( 'node_id' ), 'name' => $n->attribute( 'name' ), 'question' => null, 'total' => 0, 'choices' => array() );
            }
        }
        return self::page( $items, eZContentObjectTreeNode::subTreeCountByNodeID( $p, $parent ), $offset, $limit );
    }

    public static function view( array $args )
    {
        self::guard( 'view' );
        list( $node, $a ) = self::poll( self::arg( $args, 0, 'int' ) );
        return self::ok( self::encode( $node, $a ) );
    }


    public static function results( array $args )
    {
        self::guard( 'results' );
        list( $node, $a ) = self::poll( self::arg( $args, 0, 'int' ) );
        return self::ok( self::tally( $a ), array( 'node_id' => (int)$node->attribute( 'node_id' ) ) );
    }

    public static function choices( array $args )
    {
        self::guard( 'choices' );
        list( $node, $a ) = self::poll( self::arg( $args, 0, 'int' ) );
        $out = array();
        foreach ( (array)$a->content()->attribute( 'option_list' ) as $c )
            $out[] = array( 'id' => (int)$c['id'], 'value' => $c['value'] );
        return self::ok( $out );
    }

    public static function canVote( array $args )
    {
        self::guard( 'canVote' );
        list( $node, $a ) = self::poll( self::arg( $args, 0, 'int' ) );
        $o = $node->attribute( 'object' );
        $logged = eZUser::currentUser()->attribute( 'is_logged_in' );
        if ( !$logged && !eZInformationCollection::allowAnonymous( $o ) )
            return self::ok( array( 'can' => false, 'reason' => 'login required' ) );
        if ( eZInformationCollection::userDataHandling( $o ) === 'unique' && self::myCollection( $o ) )
            return self::ok( array( 'can' => false, 'reason' => 'already voted' ) );
        return self::ok( array( 'can' => true, 'reason' => null ) );
    }

    public static function myVote( array $args )
    {
        self::guard( 'myVote' );
        list( $node, $a ) = self::poll( self::arg( $args, 0, 'int' ) );
        $c = self::myCollection( $node->attribute( 'object' ) );
        $choice = null;
        if ( $c )
            foreach ( $c->informationCollectionAttributes() as $ca )
                if ( (int)$ca->attribute( 'contentobject_attribute_id' ) === (int)$a->attribute( 'id' ) )
                    $choice = (int)$ca->attribute( 'data_int' );
        return self::ok( array( 'voted' => $choice !== null, 'choice' => $choice ) );
    }

    public static function vote( array $args )
    {
        self::guard( 'vote' );
        list( $node, $a ) = self::poll( self::post( 'node_id', 'int' ) );
        $choice = self::post( 'choice', 'int' );
        $valid = false;
        foreach ( (array)$a->content()->attribute( 'option_list' ) as $c )
            if ( (int)$c['id'] === $choice )
                $valid = true;
        if ( !$valid )
            throw new expServiceException( "The poll has no choice $choice", 422 );
        $ident = $a->contentClassAttribute()->attribute( 'identifier' );
        $saved = self::$postData;
        try
        {
            expServiceBase::$postData = array( 'object_id' => (int)$node->attribute( 'contentobject_id' ), 'fields' => array( $ident => $choice ) );
            $r = expInfoCollectionServices::invoke( 'expInfoCollectionServices', 'submit' );
        }
        finally
        {
            expServiceBase::$postData = $saved;
        }
        if ( !$r['ok'] )
            throw new expServiceException( $r['error']['message'], $r['error']['code'] );
        return self::ok( self::tally( $a ), array( 'collection_id' => $r['data']['collection_id'] ) );
    }

    public static function latest( array $args )
    {
        self::guard( 'latest' );
        $limit = min( self::arg( $args, 0, 'int', 5 ), 50 );
        $items = array();
        foreach ( (array)eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( self::pollClass() ),
            'SortBy' => array( array( 'published', false ), array( 'node_id', false ) ), 'Limit' => $limit ), 1 ) as $n )
        {
            try
            {
                list( $node, $a ) = self::poll( $n->attribute( 'node_id' ) );
                $items[] = self::encode( $node, $a );
            }
            catch ( expServiceException $e )
            {
            }
        }
        return self::ok( $items );
    }

    public static function create( array $args )
    {
        self::guard( 'create' );
        $parent = self::node( self::post( 'parent_node_id', 'int' ), 'create' );
        $class = self::pollClass();
        if ( !self::canCreateClass( $parent, $class ) )
            throw new expServiceException( 'You may not create polls here', 403 );
        $name = trim( self::post( 'name', 'string' ) );
        $question = trim( self::post( 'question', 'string' ) );
        $choices = array_values( array_filter( array_map( 'trim', self::post( 'choices', 'list' ) ), 'strlen' ) );
        if ( $name === '' || $question === '' || count( $choices ) < 2 )
            throw new expServiceException( 'A poll needs a name, a question and at least two choices', 422 );
        $cls = eZContentClass::fetchByIdentifier( $class );
        $optionIdent = null;
        foreach ( $cls->fetchAttributes() as $ca )
            if ( $ca->attribute( 'data_type_string' ) === 'ezoption' )
                $optionIdent = $ca->attribute( 'identifier' );
        if ( !$optionIdent )
            throw new expServiceException( 'The poll class has no option attribute', 500 );
        $option = new eZOption( $question );
        foreach ( $choices as $c )
            $option->addOption( array( 'value' => $c ) );
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => $parent->attribute( 'node_id' ), 'class_identifier' => $class,
            'attributes' => array( 'name' => $name ) ) );
        if ( !$object )
            throw new expServiceException( 'The poll could not be created', 422 );
        $dm = $object->dataMap();
        $dm[$optionIdent]->setContent( $option );
        $dm[$optionIdent]->store();
        eZContentCacheManager::clearContentCache( $object->attribute( 'id' ) );
        $fresh = eZContentObjectTreeNode::fetch( $object->attribute( 'main_node_id' ) );
        list( $node, $a ) = self::poll( $fresh->attribute( 'node_id' ) );
        return self::ok( self::encode( $node, $a ) );
    }

    public static function remove( array $args )
    {
        self::guard( 'remove' );
        list( $node ) = self::poll( self::post( 'node_id', 'int' ) );
        if ( !$node->canRemove() )
            throw new expServiceException( 'No remove access to this poll', 403 );
        $id = (int)$node->attribute( 'node_id' );
        eZContentObjectTreeNode::removeSubtrees( array( $id ), true );
        return self::ok( array( 'removed' => $id ) );
    }

    public static function resetVotes( array $args )
    {
        self::guard( 'resetVotes' );
        list( $node ) = self::poll( self::post( 'node_id', 'int' ) );
        $n = (int)eZInformationCollection::fetchCollectionsCount( $node->attribute( 'contentobject_id' ) );
        eZInformationCollection::removeContentObject( $node->attribute( 'contentobject_id' ) );
        return self::ok( array( 'removed' => $n ) );
    }
}
