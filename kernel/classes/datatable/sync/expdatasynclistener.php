<?php
/**
 * File containing the expDataSyncListener class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The live mirror of the data table synchronisations (doc/eng-us/features/6.0/data-tables.md, "Live mirror"): the
 * kernel notifies content/publish, content/move and content/remove (ezpEvent), these listeners (site.ini [Event]
 * Listeners[]) note the objects, and at the end of the request, after the publish transaction, each is mirrored by the
 * live synchronisations of its class (expDataSync::mirror()). A failure is logged and never reaches the editor's
 * request. Content a synchronisation writes itself is not mirrored back.
 *
 * @since 6.0.15
 * @package kernel
 */
class expDataSyncListener
{
    /** @var array object id => event */
    protected static $pending = array();

    /** @var bool|null whether a live synchronisation exists (asked once per request) */
    protected static $hasLive = null;

    /** @var string the request $hasLive belongs to (a Velocity worker serves many) */
    protected static $request = '';

    /**
     * content/publish: ( object id, version )
     *
     * @param int $objectID
     * @param int $version
     */
    public static function published( $objectID, $version = 0 )
    {
        self::note( (int)$objectID, 'publish' );
    }

    /**
     * content/move: ( node id, object id, old parent, new parent )
     */
    public static function moved( $nodeID, $objectID = 0 )
    {
        if ( !$objectID && ( $node = eZContentObjectTreeNode::fetch( (int)$nodeID ) ) )
            $objectID = (int)$node->attribute( 'contentobject_id' );
        self::note( (int)$objectID, 'move' );
    }

    /**
     * content/remove: ( object ids, node ids )
     *
     * @param int[] $objectIDs
     */
    public static function removed( $objectIDs, $nodeIDs = array() )
    {
        // a removed location: the object is looked at again at the end of the request (another location keeps it,
        // none: its row goes)
        foreach ( (array)$objectIDs as $objectID )
            self::note( (int)$objectID, 'move' );
    }

    /**
     * An object purged or moved to the trash (eZContentObject::purge(), removeThis()).
     *
     * @param int $objectID
     */
    public static function gone( $objectID )
    {
        self::note( (int)$objectID, 'remove' );
    }

    /**
     * Mirrors the noted objects (the cleanup handler of the request; also callable directly).
     *
     * @return int how many objects were mirrored by at least one synchronisation
     */
    public static function flush()
    {
        $pending = self::$pending;
        self::$pending = array();
        $done = 0;
        foreach ( $pending as $objectID => $event )
        {
            try
            {
                $done += expDataSync::mirror( $objectID, $event ) ? 1 : 0;
            }
            catch ( Throwable $e )
            {
                eZDebug::writeError( "live mirror of object $objectID: " . $e->getMessage(), __METHOD__ );
            }
        }
        return $done;
    }

    /** Tests and long processes: forgets what was noted and asked. */
    public static function reset()
    {
        self::$pending = array();
        self::$hasLive = null;
    }

    /**
     * @param int $objectID
     * @param string $event
     */
    protected static function note( $objectID, $event )
    {
        if ( $objectID <= 0 || expDataSync::$applying || !self::hasLive() )
            return;
        // a removal wins over a publish of the same request, a publish over a move
        $rank = array( 'move' => 1, 'publish' => 2, 'remove' => 3 );
        if ( !isset( self::$pending[$objectID] ) || $rank[$event] > $rank[self::$pending[$objectID]] )
            self::$pending[$objectID] = $event;
        // once per request (eZExecution keeps a handler once and starts each request with none)
        eZExecution::addCleanupHandler( array( __CLASS__, 'flush' ) );
        // a command or a long process that never ends its request: at most 50 objects wait
        if ( count( self::$pending ) >= 50 )
            self::flush();
    }

    /** @return bool */
    protected static function hasLive()
    {
        $request = getmypid() . ':' . ( isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (string)$_SERVER['REQUEST_TIME_FLOAT'] : '' );
        if ( $request !== self::$request )
        {
            self::$request = $request;
            self::$hasLive = null;
        }
        if ( self::$hasLive === null )
        {
            try
            {
                self::$hasLive = expDataSync::installed()
                    && (int)eZPersistentObject::count( expDataSyncDefinition::definition(), array( 'run_mode' => 'live', 'state' => 'enabled' ) ) > 0;
            }
            catch ( Throwable $e )
            {
                self::$hasLive = false;
            }
        }
        return self::$hasLive;
    }
}
?>
