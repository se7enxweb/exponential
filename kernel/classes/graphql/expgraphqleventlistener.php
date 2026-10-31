<?php
/**
 * Notes what happens to content for the event journal of the GraphQL API (expGraphQLEvents; doc/eng-us/features/6.0/
 * graphql.md, "Real time: webhooks and subscriptions"). The kernel calls it where content changes, in every process (a
 * web request, a command, a queue worker, a cronjob):
 *
 *   published()  the publish operation: publish (the first version of an object) or update (a later one)
 *   moved()      eZContentObjectTreeNode::move()
 *   hidden()     eZContentObjectTreeNode::hideSubTree() / unhideSubTree() of the node itself: hide or reveal
 *   trashed()    eZContentObject::removeThis(): the object went to the trash
 *   deleted()    eZContentObject::purge(): the object is gone for good
 *   mutation()   a GraphQL mutation that succeeded (expGraphQLMutations::run())
 *
 * It only notes: at the end of the request (eZExecution's cleanup handler), after the transaction of the change, each
 * note becomes a record (with the content's class, nodes, parents, languages) and all of them are appended to the
 * journal in one write. Nothing is sent from here: the webhooks are sent by the cronjob part graphqlwebhooks, and a
 * subscription reads the journal on its own. A failure is logged and never reaches the request that changed content.
 *
 * Notes only when there is somebody to tell: the API is switched on (graphql.ini [GraphQLSettings] Enabled, in any
 * siteaccess) and subscriptions are on or a webhook is configured (active()).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLEventListener
{
    /** @var array[] the notes of this request: event data, in order */
    protected static $pending = array();

    /** @var bool|null whether events are noted (asked once per request) */
    protected static $active = null;

    /** @var string the request $active belongs to (a Velocity worker serves many) */
    protected static $request = '';

    /** @var expGraphQLEvents|null the journal (tests set another) */
    public static $journal = null;

    /** @var bool|null overrides active() (tests) */
    public static $forceActive = null;

    /**
     * The publish operation: a version of an object was published.
     *
     * @param int $objectID
     * @param int $version
     * @param bool $wasPublished the object had a published version before (update), else it is new (publish)
     */
    public static function published( $objectID, $version, $wasPublished = false )
    {
        self::note( array( 'event' => $wasPublished ? expGraphQLEvents::UPDATE : expGraphQLEvents::PUBLISH,
                           'objectId' => (int)$objectID, 'version' => (int)$version ) );
    }

    /**
     * A node moved to another parent.
     *
     * @param int $nodeID
     * @param int $objectID
     * @param int $oldParentNodeID
     * @param int $newParentNodeID
     */
    public static function moved( $nodeID, $objectID, $oldParentNodeID, $newParentNodeID )
    {
        self::note( array( 'event' => expGraphQLEvents::MOVE, 'objectId' => (int)$objectID, 'nodeIds' => array( (int)$nodeID ),
                           'oldParentNodeId' => (int)$oldParentNodeID, 'newParentNodeId' => (int)$newParentNodeID ) );
    }

    /**
     * A node (and the subtree below it) was hidden or revealed.
     *
     * @param eZContentObjectTreeNode $node
     * @param bool $hidden
     */
    public static function hidden( $node, $hidden )
    {
        if ( !$node instanceof eZContentObjectTreeNode || !self::active() )
            return;
        self::note( array( 'event' => $hidden ? expGraphQLEvents::HIDE : expGraphQLEvents::REVEAL,
                           'objectId' => (int)$node->attribute( 'contentobject_id' ), 'nodeIds' => array( (int)$node->attribute( 'node_id' ) ) ) );
    }

    /**
     * An object went to the trash. Described now, while its nodes are still there.
     *
     * @param eZContentObject $object
     */
    public static function trashed( $object )
    {
        if ( $object instanceof eZContentObject && self::active() )
            self::note( array( 'event' => expGraphQLEvents::TRASH ) + self::describe( $object ) );
    }

    /**
     * An object is removed for good. Described now, while it is still there.
     *
     * @param eZContentObject $object
     */
    public static function deleted( $object )
    {
        if ( $object instanceof eZContentObject && self::active() )
            self::note( array( 'event' => expGraphQLEvents::DELETE ) + self::describe( $object ) );
    }

    /**
     * A GraphQL mutation succeeded.
     *
     * @param string $name createContent, updateContent ...
     * @param array $answer its answer: contentId, nodeId, version, status
     */
    public static function mutation( $name, array $answer )
    {
        $data = array( 'event' => expGraphQLEvents::MUTATION, 'mutation' => (string)$name );
        if ( !empty( $answer['contentId'] ) )
            $data['objectId'] = (int)$answer['contentId'];
        if ( !empty( $answer['nodeId'] ) )
            $data['nodeIds'] = array( (int)$answer['nodeId'] );
        if ( !empty( $answer['version'] ) )
            $data['version'] = (int)$answer['version'];
        self::note( $data );
    }

    /**
     * Appends the notes of this request to the journal (the cleanup handler of the request; also callable directly).
     *
     * @return int the events written
     */
    public static function flush()
    {
        $pending = self::$pending;
        self::$pending = array();
        if ( !$pending )
            return 0;
        $records = array();
        foreach ( $pending as $data )
        {
            try
            {
                $records[] = expGraphQLEvents::record( self::complete( $data ) );
            }
            catch ( Throwable $e )
            {
                eZDebug::writeError( 'GraphQL event of object ' . ( isset( $data['objectId'] ) ? $data['objectId'] : '?' ) . ': ' . $e->getMessage(), __METHOD__ );
            }
        }
        try
        {
            $journal = self::$journal instanceof expGraphQLEvents ? self::$journal : expGraphQLEvents::fromSettings();
            return $journal->append( $records ) !== null ? count( $records ) : 0;
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'The GraphQL event journal could not be written: ' . $e->getMessage(), __METHOD__ );
            return 0;
        }
    }

    /** Tests and long processes: forgets what was noted and asked. */
    public static function reset()
    {
        self::$pending = array();
        self::$active = null;
        self::$request = '';
    }

    /**
     * @return array[] what was noted and not written yet (tests)
     */
    public static function pending()
    {
        return self::$pending;
    }

    /**
     * Whether events are noted: the API is switched on and somebody listens (subscriptions, or a webhook).
     *
     * @return bool
     */
    public static function active()
    {
        if ( self::$forceActive !== null )
            return (bool)self::$forceActive;
        $request = getmypid() . ':' . ( isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (string)$_SERVER['REQUEST_TIME_FLOAT'] : '' );
        if ( $request !== self::$request )
        {
            self::$request = $request;
            self::$active = null;
        }
        if ( self::$active === null )
        {
            try
            {
                self::$active = class_exists( 'eZINI' ) && expGraphQL::setting( 'Enabled', 'disabled' ) === 'enabled'
                                && ( expGraphQLSubscriptions::enabled() || expGraphQLWebhooks::configuredNames() );
            }
            catch ( Throwable $e )
            {
                self::$active = false;
            }
        }
        return self::$active;
    }

    /**
     * @param array $data
     */
    protected static function note( array $data )
    {
        if ( empty( $data['objectId'] ) && $data['event'] !== expGraphQLEvents::MUTATION )
            return;
        if ( !self::active() )
            return;
        $data['time'] = microtime( true );
        $data['id'] = expGraphQLEvents::newId( $data['time'] );
        self::$pending[] = $data;
        // once per request (eZExecution keeps a handler once and starts each request with none)
        if ( class_exists( 'eZExecution' ) )
            eZExecution::addCleanupHandler( array( __CLASS__, 'flush' ) );
        // a command or a long process that never ends its request: at most 50 events wait
        if ( count( self::$pending ) >= 50 )
            self::flush();
    }

    /**
     * What an event says about its content, read from the database at the end of the request (or, for a removal,
     * when it is noted): class, section, owner, name, languages, nodes, parents, paths, states.
     *
     * @param array $data
     * @return array
     */
    protected static function complete( array $data )
    {
        $data += array( 'userId' => class_exists( 'eZUser' ) ? (int)eZUser::currentUserID() : 0,
                        'siteaccess' => class_exists( 'expGraphQL' ) ? expGraphQL::currentSiteAccess() : '' );
        if ( isset( $data['classIdentifier'] ) || empty( $data['objectId'] ) )
            return $data;
        $object = eZContentObject::fetch( (int)$data['objectId'] );
        if ( !$object instanceof eZContentObject )
            return $data;
        $described = self::describe( $object, isset( $data['version'] ) ? (int)$data['version'] : 0 );
        // a move or a hide keeps the node it names; the parents are those of every node
        if ( isset( $data['nodeIds'] ) )
            unset( $described['nodeIds'] );
        return $data + $described;
    }

    /**
     * @param eZContentObject $object
     * @param int $version the version published (its languages); 0: the object's current one
     * @return array
     */
    public static function describe( eZContentObject $object, $version = 0 )
    {
        $described = array( 'objectId' => (int)$object->attribute( 'id' ),
                            'remoteId' => (string)$object->attribute( 'remote_id' ),
                            'classIdentifier' => (string)$object->attribute( 'class_identifier' ),
                            'classId' => (int)$object->attribute( 'contentclass_id' ),
                            'sectionId' => (int)$object->attribute( 'section_id' ),
                            'ownerId' => (int)$object->attribute( 'owner_id' ),
                            'name' => (string)$object->attribute( 'name' ) );
        $versionObject = $version > 0 ? $object->version( $version ) : $object->currentVersion();
        if ( $versionObject instanceof eZContentObjectVersion )
        {
            $described['version'] = (int)$versionObject->attribute( 'version' );
            $described['language'] = (string)$versionObject->initialLanguageCode();
            $described['languages'] = array_values( array_map( 'strval', (array)$versionObject->translationList( false, false ) ) );
        }
        $nodes = eZContentObjectTreeNode::fetchByContentObjectID( (int)$object->attribute( 'id' ) );
        $described['nodeIds'] = array();
        $described['parentNodeIds'] = array();
        $described['pathStrings'] = array();
        foreach ( is_array( $nodes ) ? $nodes : array() as $node )
        {
            $described['nodeIds'][] = (int)$node->attribute( 'node_id' );
            $described['parentNodeIds'][] = (int)$node->attribute( 'parent_node_id' );
            $described['pathStrings'][] = (string)$node->attribute( 'path_string' );
        }
        $described['mainNodeId'] = (int)$object->attribute( 'main_node_id' );
        if ( method_exists( $object, 'stateIDArray' ) )
            $described['stateIds'] = array_values( array_map( 'intval', (array)$object->stateIDArray() ) );
        return $described;
    }
}

?>
