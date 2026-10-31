<?php
/**
 * The subscriptions of the GraphQL API (doc/eng-us/features/6.0/graphql.md, "Subscriptions"): a client subscribes to
 * what happens to content and gets each event it may read, as it happens.
 *
 *   subscription { contentPublished(classes: ["article"], parentId: 2) { event objectId content { name url } } }
 *
 * The transports:
 *
 *   SSE        POST (or GET) /api/graphql/subscribe with Accept: text/event-stream, the protocol of graphql-sse
 *              ("distinct connections"): every event is "event: next" with the answer as data, "event: complete"
 *              ends a subscription that is finished. Works with PHP-FPM behind Apache (each open stream holds one
 *              PHP process) and with Velocity (each open stream holds one worker, within its StreamShare).
 *   WebSocket  the protocol graphql-transport-ws on Velocity: Velocity speaks the WebSocket and opens the SSE stream
 *              above for each subscription (Velocity's WebSocket bridge), so the rules here hold for both.
 *
 * A stream follows the event journal (expGraphQLEvents) through files; it holds no database connection while it
 * waits. For each event it matches (the root field's arguments) it opens one, reads the user again (the rights of
 * this moment), checks the content can be read by the user (a trashed or deleted object: its policies against what
 * the event recorded), runs the subscription's selection with the event as its root, and closes the connection again.
 *
 * Limits: MaxSubscriptions open at once, MaxSubscriptionsPerUser for one signed-in user, MaxSubscriptionsPerAddress
 * for one IP address (all three counted in var/<site>/graphql/subscriptions/ by every worker alike); a comment every
 * SubscriptionHeartbeat seconds (it also finds clients that left); a stream ends after SubscriptionMaxDuration seconds
 * (the client reconnects with Last-Event-ID and misses nothing the journal still has) or after
 * SubscriptionIdleTimeout seconds in which it sent no event.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class expGraphQLSubscriptions
{
    /** extensions.code of a subscription over a limit. */
    const LIMIT = expGraphQLError::SUBSCRIPTION_LIMIT;

    /** The root fields of the type Subscription and the events each follows (null: the events argument). */
    const FIELDS = array(
        'contentEvents' => null,
        'contentPublished' => array( expGraphQLEvents::PUBLISH, expGraphQLEvents::UPDATE ),
        'contentUpdated' => array( expGraphQLEvents::UPDATE ),
        'contentDeleted' => array( expGraphQLEvents::TRASH, expGraphQLEvents::DELETE ),
        'contentMoved' => array( expGraphQLEvents::MOVE ),
        'contentHidden' => array( expGraphQLEvents::HIDE, expGraphQLEvents::REVEAL ),
    );

    /** @var string the directory of the open subscriptions */
    protected $dir;

    /** @var array the limits and times: max, perUser, perAddress, heartbeat, maxDuration, idleTimeout, poll (ms) */
    protected $settings;

    /** @var string|null the file of this process's open subscription */
    protected $registered = null;

    /**
     * @param string $dir
     * @param array $settings
     */
    public function __construct( $dir, array $settings = array() )
    {
        $this->dir = rtrim( (string)$dir, '/' );
        $this->settings = $settings + array( 'max' => 200, 'perUser' => 5, 'perAddress' => 10, 'heartbeat' => 15, 'maxDuration' => 280,
                                             'idleTimeout' => 0, 'poll' => 500 );
    }

    /**
     * The subscriptions of the installation, with the limits of graphql.ini.
     *
     * @return self
     */
    public static function fromSettings()
    {
        return new self( eZSys::varDirectory() . '/graphql/subscriptions', array(
            'max' => (int)expGraphQL::setting( 'MaxSubscriptions', 200 ),
            'perUser' => (int)expGraphQL::setting( 'MaxSubscriptionsPerUser', 5 ),
            'perAddress' => (int)expGraphQL::setting( 'MaxSubscriptionsPerAddress', 10 ),
            'heartbeat' => expGraphQL::intSetting( 'SubscriptionHeartbeat', 15 ),
            'maxDuration' => expGraphQL::intSetting( 'SubscriptionMaxDuration', 280 ),
            'idleTimeout' => (int)expGraphQL::setting( 'SubscriptionIdleTimeout', 0 ),
            'poll' => expGraphQL::intSetting( 'SubscriptionPollInterval', 500 ),
        ) );
    }

    /**
     * Whether subscriptions are on (graphql.ini [GraphQLSettings] Subscriptions, enabled by default; they answer only
     * where the API does).
     *
     * @return bool
     */
    public static function enabled()
    {
        return expGraphQL::setting( 'Subscriptions', 'enabled' ) === 'enabled';
    }

    /**
     * @return array the settings (limits and times)
     */
    public function settings()
    {
        return $this->settings;
    }

    // ---- the schema -----------------------------------------------------------------------------------------------

    /**
     * The type Subscription.
     *
     * @param expGraphQLTypes $types
     * @return ObjectType
     */
    public static function type( expGraphQLTypes $types )
    {
        return new ObjectType( array(
            'name' => 'Subscription',
            'description' => 'What happens to content, as it happens, as far as the subscriber may read it: over SSE (/api/graphql/subscribe) or a WebSocket (graphql-transport-ws, Velocity).',
            'fields' => function () use ( $types ) { return expGraphQLSubscriptions::fields( $types ); },
        ) );
    }

    /**
     * @param expGraphQLTypes $types
     * @return array the fields of Subscription
     */
    public static function fields( expGraphQLTypes $types )
    {
        $event = self::eventType( $types );
        $filters = array(
            'classes' => array( 'type' => Type::listOf( Type::nonNull( Type::string() ) ), 'description' => 'Only content of these class identifiers.' ),
            'parentId' => array( 'type' => Type::int(), 'description' => 'Only content directly below this node.' ),
            'subtreeId' => array( 'type' => Type::int(), 'description' => 'Only content at or anywhere below this node.' ),
        );
        $resolve = function ( $root ) { return is_array( $root ) ? $root : null; };
        $descriptions = array(
            'contentEvents' => 'Every event the arguments ask for.',
            'contentPublished' => 'A version was published: new content (event publish) or a new version (event update).',
            'contentUpdated' => 'A new version of existing content was published.',
            'contentDeleted' => 'Content went to the trash (event trash) or was removed for good (event delete); content is null.',
            'contentMoved' => 'A location moved to another parent.',
            'contentHidden' => 'A location was hidden (event hide) or revealed (event reveal).',
        );
        $fields = array();
        foreach ( self::FIELDS as $name => $events )
        {
            $args = $filters;
            if ( $events === null )
                $args = array( 'events' => array( 'type' => Type::listOf( Type::nonNull( self::kindType( $types ) ) ),
                                                  'description' => 'Only these events (empty: every event).' ) ) + $args;
            $fields[$name] = array( 'type' => $event, 'description' => $descriptions[$name], 'args' => $args, 'resolve' => $resolve );
        }
        return $fields;
    }

    /**
     * @param expGraphQLTypes $types
     * @return EnumType ContentEventKind
     */
    public static function kindType( expGraphQLTypes $types )
    {
        return $types->register( 'ContentEventKind', function () {
            $values = array();
            foreach ( expGraphQLEvents::ALL as $event )
                $values[$event] = array( 'value' => $event );
            return new EnumType( array( 'name' => 'ContentEventKind', 'description' => 'What happened to content.', 'values' => $values ) );
        } );
    }

    /**
     * @param expGraphQLTypes $types
     * @return ObjectType ContentEvent
     */
    public static function eventType( expGraphQLTypes $types )
    {
        return $types->register( 'ContentEvent', function () use ( $types ) {
            $field = function ( $type, $key, $description ) {
                return array( 'type' => $type, 'description' => $description,
                              'resolve' => function ( $event ) use ( $key ) { return isset( $event[$key] ) ? $event[$key] : null; } );
            };
            $ints = Type::nonNull( Type::listOf( Type::nonNull( Type::int() ) ) );
            return new ObjectType( array(
                'name' => 'ContentEvent',
                'description' => 'Something that happened to content (the event journal of the installation).',
                'fields' => function () use ( $types, $field, $ints ) {
                    $public = function ( $event, $key ) { $fields = expGraphQLEvents::publicFields( $event ); return $fields[$key]; };
                    return array(
                        'id' => array( 'type' => Type::nonNull( Type::id() ), 'description' => 'The id of the event (the same in a webhook).',
                                       'resolve' => function ( $event ) use ( $public ) { return $public( $event, 'id' ); } ),
                        'event' => array( 'type' => Type::nonNull( expGraphQLSubscriptions::kindType( $types ) ), 'resolve' => function ( $event ) { return $event['event']; } ),
                        'time' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'When it happened (ISO 8601, UTC).',
                                         'resolve' => function ( $event ) use ( $public ) { return $public( $event, 'time' ); } ),
                        'objectId' => $field( Type::int(), 'objectId', 'The content object id.' ),
                        'remoteId' => $field( Type::string(), 'remoteId', 'The remote id of the content object.' ),
                        'version' => $field( Type::int(), 'version', 'The version published.' ),
                        'classIdentifier' => $field( Type::string(), 'classIdentifier', 'The class of the content.' ),
                        'name' => $field( Type::string(), 'name', 'The name of the content when it happened.' ),
                        'language' => $field( Type::string(), 'language', 'The language of the version published.' ),
                        'languages' => array( 'type' => Type::nonNull( Type::listOf( Type::nonNull( Type::string() ) ) ),
                                              'resolve' => function ( $event ) { return isset( $event['languages'] ) ? $event['languages'] : array(); } ),
                        'nodeIds' => array( 'type' => $ints, 'description' => 'The locations (for a move, hide or reveal: the one it happened to).',
                                            'resolve' => function ( $event ) { return isset( $event['nodeIds'] ) ? $event['nodeIds'] : array(); } ),
                        'mainNodeId' => $field( Type::int(), 'mainNodeId', 'The main location.' ),
                        'parentNodeIds' => array( 'type' => $ints, 'resolve' => function ( $event ) { return isset( $event['parentNodeIds'] ) ? $event['parentNodeIds'] : array(); } ),
                        'oldParentNodeId' => $field( Type::int(), 'oldParentNodeId', 'A move: the parent before.' ),
                        'newParentNodeId' => $field( Type::int(), 'newParentNodeId', 'A move: the parent after.' ),
                        'mutation' => $field( Type::string(), 'mutation', 'The event mutation: the GraphQL mutation (createContent ...).' ),
                        'content' => array(
                            'type' => $types->content(),
                            'description' => 'The content as it is now, as the subscriber may read it (null when it is gone or may not be read).',
                            'args' => array( 'language' => array( 'type' => Type::string(), 'description' => 'The translation to read; default: the languages of the siteaccess.' ) ),
                            'resolve' => function ( $event, $args, expGraphQLContext $ctx ) {
                                if ( empty( $event['objectId'] ) || in_array( $event['event'], array( expGraphQLEvents::TRASH, expGraphQLEvents::DELETE ), true ) )
                                    return null;
                                return $ctx->repository->contentById( (int)$event['objectId'], isset( $args['language'] ) ? (string)$args['language'] : false );
                            },
                        ),
                    );
                },
            ) );
        } );
    }

    // ---- a subscription request ---------------------------------------------------------------------------------

    /**
     * Checks a subscription before its stream opens: the document is valid against the schema (and the limits of
     * depth and complexity), it is a subscription with one root field, and its arguments are read.
     *
     * @param \GraphQL\Type\Schema $schema
     * @param string $query
     * @param array|null $variables
     * @param string|null $operationName
     * @param bool $allowIntrospection
     * @return array document, field, filter (events, classes, parentId, subtreeId), operationName, variables, operation
     *               (query, mutation or subscription); or error (message, code)
     */
    public static function prepare( $schema, $query, $variables, $operationName, $allowIntrospection )
    {
        try
        {
            $document = \GraphQL\Language\Parser::parse( (string)$query );
        }
        catch ( \GraphQL\Error\SyntaxError $e )
        {
            return array( 'error' => array( $e->getMessage(), expGraphQLError::PARSE ) );
        }
        $errors = \GraphQL\Validator\DocumentValidator::validate( $schema, $document, expGraphQLServer::validationRules( $allowIntrospection, (array)$variables ) );
        if ( $errors )
        {
            $first = $errors[0];
            return array( 'error' => array( expGraphQLServer::translatedMessage( expGraphQLServer::codeOf( $first ), $first->getMessage() ), expGraphQLServer::codeOf( $first ) ) );
        }
        $operation = \GraphQL\Utils\AST::getOperationAST( $document, $operationName );
        if ( $operation === null )
            return array( 'error' => array( expGraphQLMessage::tr( 'The request names no operation of the document.' ), expGraphQLError::BAD_USER_INPUT ) );
        $prepared = array( 'document' => $document, 'operationName' => $operationName, 'variables' => $variables,
                           'operation' => (string)$operation->operation, 'field' => null, 'filter' => array() );
        if ( $prepared['operation'] !== 'subscription' )
            return $prepared;
        $selections = $operation->selectionSet->selections;
        $node = count( $selections ) === 1 ? $selections[0] : null;
        if ( !$node instanceof \GraphQL\Language\AST\FieldNode )
            return array( 'error' => array( expGraphQLMessage::tr( 'A subscription asks for exactly one field.' ), expGraphQLError::VALIDATION ) );
        $name = $node->name->value;
        $type = $schema->getSubscriptionType();
        if ( $type === null || !$type->hasField( $name ) || !array_key_exists( $name, self::FIELDS ) )
            return array( 'error' => array( expGraphQLMessage::tr( 'A subscription asks for exactly one field.' ), expGraphQLError::VALIDATION ) );
        list( $variableErrors, $coerced ) = \GraphQL\Executor\Values::getVariableValues( $schema, $operation->variableDefinitions, (array)$variables );
        if ( $variableErrors )
            return array( 'error' => array( $variableErrors[0]->getMessage(), expGraphQLError::BAD_USER_INPUT ) );
        $args = \GraphQL\Executor\Values::getArgumentValues( $type->getField( $name ), $node, (array)$coerced );
        $prepared['field'] = $name;
        $prepared['filter'] = array(
            'events' => self::FIELDS[$name] !== null ? self::FIELDS[$name] : ( isset( $args['events'] ) ? array_values( (array)$args['events'] ) : array() ),
            'classes' => isset( $args['classes'] ) ? array_values( (array)$args['classes'] ) : array(),
            'parentId' => isset( $args['parentId'] ) ? (int)$args['parentId'] : null,
            'subtreeId' => isset( $args['subtreeId'] ) ? (int)$args['subtreeId'] : null,
        );
        return $prepared;
    }

    // ---- who may read an event ----------------------------------------------------------------------------------

    /**
     * Whether a user may read the content of a trashed or deleted object, by the policies of content/read against
     * what the event recorded (its class, section, owner, locations, states). A limitation it cannot judge refuses.
     *
     * @param array $access the answer of eZUser::hasAccessTo( 'content', 'read' )
     * @param array $event
     * @param int $userId
     * @return bool
     */
    public static function snapshotReadable( array $access, array $event, $userId )
    {
        $word = isset( $access['accessWord'] ) ? $access['accessWord'] : 'no';
        if ( $word === 'yes' )
            return true;
        if ( $word !== 'limited' || empty( $access['policies'] ) )
            return false;
        foreach ( (array)$access['policies'] as $limitations )
        {
            if ( self::policyAllows( (array)$limitations, $event, (int)$userId ) )
                return true;
        }
        return false;
    }

    /**
     * @param array $limitations identifier => values
     * @param array $event
     * @param int $userId
     * @return bool every limitation of the policy holds
     */
    protected static function policyAllows( array $limitations, array $event, $userId )
    {
        foreach ( $limitations as $identifier => $values )
        {
            $values = array_map( 'strval', (array)$values );
            switch ( $identifier )
            {
                case 'Class':
                    if ( !isset( $event['classId'] ) || !in_array( (string)$event['classId'], $values, true ) )
                        return false;
                    break;
                case 'Section':
                    if ( !isset( $event['sectionId'] ) || !in_array( (string)$event['sectionId'], $values, true ) )
                        return false;
                    break;
                case 'Owner':
                    if ( !isset( $event['ownerId'] ) || (int)$event['ownerId'] !== (int)$userId )
                        return false;
                    break;
                case 'Node':
                    if ( !array_intersect( array_map( 'strval', isset( $event['nodeIds'] ) ? (array)$event['nodeIds'] : array() ), $values ) )
                        return false;
                    break;
                case 'Subtree':
                    $inside = false;
                    foreach ( isset( $event['pathStrings'] ) ? (array)$event['pathStrings'] : array() as $path )
                        foreach ( $values as $subtree )
                            if ( $subtree !== '' && strpos( (string)$path, $subtree ) === 0 )
                                $inside = true;
                    if ( !$inside )
                        return false;
                    break;
                default:
                    if ( strpos( (string)$identifier, 'StateGroup_' ) === 0 )
                    {
                        if ( !array_intersect( array_map( 'strval', isset( $event['stateIds'] ) ? (array)$event['stateIds'] : array() ), $values ) )
                            return false;
                        break;
                    }
                    return false;
            }
        }
        return true;
    }

    // ---- the limits ---------------------------------------------------------------------------------------------

    /**
     * Registers an open subscription when it is within the limits.
     *
     * @param string $user 'user:<id>' for a signed-in user, '' for an anonymous one (counted by address only)
     * @param string $address the IP address
     * @param int|null $now
     * @return string|null null when registered; else the message of the limit it passes
     */
    public function acquire( $user, $address, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        if ( !expGraphQLEvents::ensureDirectory( $this->dir ) )
            return null; // cannot count: the stream still runs, as the rate limiter does without its files
        $lock = @fopen( $this->dir . '/registry.lock', 'cb' );
        if ( $lock )
            flock( $lock, LOCK_EX );
        try
        {
            $open = $this->open( $now );
            $total = count( $open );
            $byUser = 0;
            $byAddress = 0;
            foreach ( $open as $entry )
            {
                if ( $user !== '' && isset( $entry['user'] ) && $entry['user'] === $user )
                    $byUser++;
                if ( isset( $entry['address'] ) && $entry['address'] === $address )
                    $byAddress++;
            }
            if ( (int)$this->settings['max'] > 0 && $total >= (int)$this->settings['max'] )
                return expGraphQLMessage::tr( 'Too many subscriptions are open; try again later.' );
            if ( $user !== '' && (int)$this->settings['perUser'] > 0 && $byUser >= (int)$this->settings['perUser'] )
                return expGraphQLMessage::tr( 'You have %max subscriptions open, the most allowed.', array( '%max' => (int)$this->settings['perUser'] ) );
            if ( (int)$this->settings['perAddress'] > 0 && $byAddress >= (int)$this->settings['perAddress'] )
                return expGraphQLMessage::tr( 'Your address has %max subscriptions open, the most allowed.', array( '%max' => (int)$this->settings['perAddress'] ) );
            $this->registered = $this->dir . '/' . getmypid() . '-' . bin2hex( random_bytes( 6 ) ) . '.json';
            @file_put_contents( $this->registered, json_encode( array( 'user' => (string)$user, 'address' => (string)$address, 'started' => $now ) ) );
            @touch( $this->registered, $now );
            expGraphQLEvents::ownLikeParent( $this->registered, 0640 );
            return null;
        }
        finally
        {
            if ( $lock )
            {
                flock( $lock, LOCK_UN );
                fclose( $lock );
            }
        }
    }

    /**
     * The subscription of this process is still open (its file's time is its last heartbeat).
     *
     * @param int|null $now
     */
    public function beat( $now = null )
    {
        if ( $this->registered !== null )
            @touch( $this->registered, $now === null ? time() : (int)$now );
    }

    /**
     * The subscription of this process has ended.
     */
    public function release()
    {
        if ( $this->registered !== null )
            @unlink( $this->registered );
        $this->registered = null;
    }

    /**
     * The open subscriptions; one whose process stopped beating (three heartbeats, at least a minute) is removed.
     *
     * @param int|null $now
     * @return array[] user, address, started
     */
    public function open( $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $stale = max( 60, 3 * (int)$this->settings['heartbeat'] );
        $open = array();
        foreach ( (array)glob( $this->dir . '/*.json' ) as $file )
        {
            clearstatcache( true, $file );
            $time = @filemtime( $file );
            if ( $time === false )
                continue;
            if ( $now - $time > $stale )
            {
                @unlink( $file );
                continue;
            }
            $entry = json_decode( (string)@file_get_contents( $file ), true );
            if ( is_array( $entry ) )
                $open[] = $entry;
        }
        return $open;
    }

    // ---- the stream ---------------------------------------------------------------------------------------------

    /**
     * One message of the SSE stream.
     *
     * @param string|null $event next, complete, or null (a comment, or only an id)
     * @param string|null $data
     * @param string|null $id the position after the event (Last-Event-ID)
     * @param string|null $comment
     * @return string
     */
    public static function sseMessage( $event, $data = null, $id = null, $comment = null )
    {
        $text = '';
        if ( $comment !== null )
            $text .= ': ' . str_replace( array( "\r", "\n" ), ' ', (string)$comment ) . "\n";
        if ( $event !== null )
            $text .= 'event: ' . $event . "\n";
        if ( $id !== null )
            $text .= 'id: ' . $id . "\n";
        if ( $data !== null )
        {
            foreach ( explode( "\n", str_replace( "\r", '', (string)$data ) ) as $line )
                $text .= 'data: ' . $line . "\n";
        }
        else if ( $event === 'complete' )
            $text .= "data:\n";
        return $text . "\n";
    }

    /**
     * Follows the journal and writes every event a subscription matches, until the client leaves, the stream has run
     * SubscriptionMaxDuration seconds, it has sent no event for SubscriptionIdleTimeout seconds, or the process is
     * asked to stop.
     *
     * @param expGraphQLEvents $journal
     * @param array $prepared of prepare()
     * @param string|null $position where to start (Last-Event-ID); null: the end of the journal
     * @param callable $deliver function( array $event ): ?string the data of the event for this subscriber, or null
     *                 (the subscriber may not read it); called with a database connection, closed again after it
     * @param callable $write function( string $text ): bool writes and flushes; false when the client is gone
     * @param array $hooks optional: clock (function(): float), sleep (function( int $ms )), aborted (function(): bool),
     *                     beat (function()), database (function( bool $open ))
     * @return array events (sent), reason (gone, duration, idle), position
     */
    public function stream( expGraphQLEvents $journal, array $prepared, $position, callable $deliver, callable $write, array $hooks = array() )
    {
        $clock = isset( $hooks['clock'] ) ? $hooks['clock'] : function () { return microtime( true ); };
        $sleep = isset( $hooks['sleep'] ) ? $hooks['sleep'] : function ( $ms ) { usleep( (int)$ms * 1000 ); };
        $aborted = isset( $hooks['aborted'] ) ? $hooks['aborted'] : function () { return false; };
        $beat = isset( $hooks['beat'] ) ? $hooks['beat'] : function () {};
        $database = isset( $hooks['database'] ) ? $hooks['database'] : function ( $open ) {};
        $position = expGraphQLEvents::isPosition( $position ) ? $position : $journal->end();
        $started = $clock();
        $lastBeat = $started;
        $lastSent = $started;
        $sentPosition = $position;
        $sent = 0;
        if ( !$write( self::sseMessage( null, null, null, 'subscribed' ) ) )
            return array( 'events' => 0, 'reason' => 'gone', 'position' => $position );
        while ( true )
        {
            if ( $aborted() )
                return array( 'events' => $sent, 'reason' => 'gone', 'position' => $position );
            $read = $journal->read( $position, 100 );
            foreach ( $read['events'] as $event )
            {
                $eventPosition = $event['_position'];
                unset( $event['_position'] );
                if ( !expGraphQLEvents::matches( $event, $prepared['filter'] ) )
                {
                    $position = $eventPosition;
                    continue;
                }
                $database( true );
                try
                {
                    $data = $deliver( $event );
                }
                finally
                {
                    $database( false );
                }
                $position = $eventPosition;
                if ( $data === null )
                    continue;
                if ( !$write( self::sseMessage( 'next', $data, $position ) ) )
                    return array( 'events' => $sent, 'reason' => 'gone', 'position' => $position );
                $sentPosition = $position;
                $sent++;
                $lastSent = $clock();
            }
            $position = $read['position'];
            $now = $clock();
            if ( $now - $started >= (int)$this->settings['maxDuration'] )
            {
                // the client resumes from here (Last-Event-ID): the events it was not sent are behind it
                $write( self::sseMessage( null, null, $position, 'reconnect' ) );
                return array( 'events' => $sent, 'reason' => 'duration', 'position' => $position );
            }
            if ( (int)$this->settings['idleTimeout'] > 0 && $now - $lastSent >= (int)$this->settings['idleTimeout'] )
            {
                $write( self::sseMessage( null, null, $position, 'idle' ) );
                return array( 'events' => $sent, 'reason' => 'idle', 'position' => $position );
            }
            if ( $now - $lastBeat >= (int)$this->settings['heartbeat'] )
            {
                // a comment keeps proxies from closing the stream and finds a client that left; the id moves the
                // client's Last-Event-ID past the events it was not sent
                if ( !$write( self::sseMessage( null, null, $position !== $sentPosition ? $position : null, 'heartbeat' ) ) )
                    return array( 'events' => $sent, 'reason' => 'gone', 'position' => $position );
                $sentPosition = $position;
                $lastBeat = $now;
                $beat();
            }
            if ( !$read['events'] )
                $sleep( (int)$this->settings['poll'] );
        }
    }

    // ---- the request ---------------------------------------------------------------------------------------------

    /**
     * Answers a request of /api/graphql/subscribe (expGraphQLRestController::doSubscribe()): a subscription opens a
     * stream that runs until it ends; a query is answered as a stream of one event (next, then complete); a
     * mutation is refused (send it to /api/graphql).
     *
     * @param expGraphQLSchemaBuilder $builder
     * @param array $request of expGraphQLServer::parseHttpRequest()
     * @param expGraphQLContext $context
     * @param array $server $_SERVER
     * @param callable $headers function( int $status, array $headers ): sends the status and headers
     * @param callable $write function( string $text ): bool
     * @param callable $aborted function(): bool
     * @return array status (the HTTP status: 200 for a stream), refusal (a GraphQL answer for a refused request), result
     */
    public function respond( expGraphQLSchemaBuilder $builder, array $request, expGraphQLContext $context, array $server,
                             callable $headers, callable $write, callable $aborted )
    {
        $context->operationType = 'subscription';
        if ( !self::enabled() )
            return array( 'status' => 404, 'refusal' => expGraphQLServer::refusal( expGraphQLMessage::tr( 'Subscriptions are switched off in this siteaccess.' ), expGraphQLError::DISABLED ) );
        if ( !$context->isSignedIn() && expGraphQL::setting( 'AnonymousSubscriptions', 'disabled' ) !== 'enabled' )
            return array( 'status' => 403, 'refusal' => expGraphQLServer::refusal( expGraphQLMessage::tr( 'Sign in to subscribe (a token or a personal API key).' ), expGraphQLError::FORBIDDEN ) );
        $resolved = expGraphQLServer::resolvePersisted( $request, $context );
        if ( isset( $resolved['error'] ) )
            return array( 'status' => 400, 'refusal' => expGraphQLServer::refusal( $resolved['error'][0], $resolved['error'][1] ) );
        $query = (string)$resolved['query'];
        if ( trim( $query ) === '' )
            return array( 'status' => 400, 'refusal' => expGraphQLServer::refusal( expGraphQLMessage::tr( 'The request has no query.' ), expGraphQLError::BAD_USER_INPUT ) );
        $schema = $builder->schema();
        $prepared = self::prepare( $schema, $query, isset( $request['variables'] ) ? $request['variables'] : null,
                                   isset( $request['operationName'] ) ? $request['operationName'] : null, $context->allowsIntrospection() );
        if ( isset( $prepared['error'] ) )
            return array( 'status' => 400, 'refusal' => expGraphQLServer::refusal( $prepared['error'][0], $prepared['error'][1] ) );
        if ( $prepared['operation'] === 'mutation' )
            return array( 'status' => 405, 'refusal' => expGraphQLServer::refusal( expGraphQLMessage::tr( 'Send mutations to /api/graphql.' ), expGraphQLError::BAD_USER_INPUT ) );
        $streamHeaders = array( 'Content-Type' => 'text/event-stream; charset=UTF-8', 'Cache-Control' => 'no-cache, no-store', 'X-Accel-Buffering' => 'no',
                                'X-GraphQL-Schema' => $builder->schemaHash(), 'X-Request-Id' => $context->requestId );
        if ( $prepared['operation'] === 'query' )
        {
            $context->operationType = 'query';
            $answer = ( new expGraphQLServer( $builder ) )->execute( $query, $prepared['variables'], $prepared['operationName'], $context );
            $headers( 200, $streamHeaders );
            $write( self::sseMessage( 'next', expGraphQLRestResponse::encode( $answer ) ) . self::sseMessage( 'complete' ) );
            return array( 'status' => 200, 'refusal' => null, 'result' => array( 'events' => 1, 'reason' => 'complete' ) );
        }
        $address = isset( $server['REMOTE_ADDR'] ) ? (string)$server['REMOTE_ADDR'] : '';
        $userKey = $context->isSignedIn() && $context->user instanceof eZUser ? 'user:' . (int)$context->user->id() : '';
        $refused = $this->acquire( $userKey, $address );
        if ( $refused !== null )
            return array( 'status' => 429, 'refusal' => expGraphQLServer::refusal( $refused, self::LIMIT ) );
        try
        {
            $start = null;
            foreach ( array( 'HTTP_LAST_EVENT_ID' ) as $header )
                if ( isset( $server[$header] ) && expGraphQLEvents::isPosition( (string)$server[$header] ) )
                    $start = (string)$server[$header];
            if ( $start === null && isset( $request['lastEventId'] ) && expGraphQLEvents::isPosition( (string)$request['lastEventId'] ) )
                $start = (string)$request['lastEventId'];
            $headers( 200, $streamHeaders );
            $userId = $context->user instanceof eZUser ? (int)$context->user->id() : 0;
            $mode = $context->authMode;
            $baseUrl = $context->baseUrl;
            $catalogue = $builder->catalogue();
            $deliver = function ( array $event ) use ( $schema, $prepared, $userId, $mode, $baseUrl, $catalogue, $context ) {
                return expGraphQLSubscriptions::deliverEvent( $schema, $prepared, $event, $userId, $mode, $baseUrl, $catalogue, $context );
            };
            $subscriptions = $this;
            // no database connection while the stream waits: one per event, closed again (expGraphQLSubscriptions::database())
            self::database( false );
            $result = $this->stream( expGraphQLEvents::fromSettings(), $prepared, $start, $deliver, $write,
                                     array( 'aborted' => $aborted, 'beat' => function () use ( $subscriptions ) { $subscriptions->beat(); },
                                            'database' => function ( $open ) { if ( !$open ) expGraphQLSubscriptions::database( false ); } ) );
            return array( 'status' => 200, 'refusal' => null, 'result' => $result );
        }
        finally
        {
            $this->release();
        }
    }

    /**
     * The data of one event for one subscriber: null when the subscriber may not read it, else the answer of the
     * subscription's selection with the event as its root (JSON). Reads the user again first, so the rights are those
     * of this moment.
     *
     * @param \GraphQL\Type\Schema $schema
     * @param array $prepared
     * @param array $event
     * @param int $userId
     * @param string $mode
     * @param string $baseUrl
     * @param expGraphQLClassCatalogue $catalogue
     * @param expGraphQLContext $template the context of the request (its key, its scopes)
     * @return string|null
     */
    public static function deliverEvent( $schema, array $prepared, array $event, $userId, $mode, $baseUrl, $catalogue, expGraphQLContext $template )
    {
        if ( class_exists( 'eZContentObject' ) )
            eZContentObject::clearCache();
        $user = $userId > 0 ? eZUser::fetch( (int)$userId ) : null;
        if ( !$user instanceof eZUser )
            return null;
        eZUser::setCurrentlyLoggedInUser( $user, (int)$userId, eZUser::NO_SESSION_REGENERATE );
        $context = new expGraphQLContext( $user, $mode, new expGraphQLRepository( $catalogue ), $baseUrl );
        $context->apiKeyId = $template->apiKeyId;
        $context->apiKeyScopes = $template->apiKeyScopes;
        $context->operationType = 'subscription';
        if ( in_array( $event['event'], array( expGraphQLEvents::TRASH, expGraphQLEvents::DELETE ), true ) )
        {
            if ( !self::snapshotReadable( $user->hasAccessTo( 'content', 'read' ), $event, (int)$userId ) )
                return null;
        }
        else if ( empty( $event['objectId'] ) || $context->repository->contentById( (int)$event['objectId'] ) === null )
            return null;
        $result = \GraphQL\GraphQL::executeQuery( $schema, $prepared['document'], $event, $context, $prepared['variables'], $prepared['operationName'], null, array() );
        $result->setErrorFormatter( function ( \GraphQL\Error\Error $error ) use ( $context ) {
            return expGraphQLServer::formatError( $error, $context, expGraphQL::setting( 'Debug', 'disabled' ) === 'enabled' );
        } );
        $answer = $result->toArray();
        return expGraphQLRestResponse::encode( $answer );
    }

    /**
     * Opens nothing, closes the database connection of this process: a stream waits without one (the next use of
     * eZDB::instance() opens a new one).
     *
     * @param bool $open false: close
     */
    public static function database( $open )
    {
        if ( $open || !class_exists( 'eZDB' ) || !eZDB::hasInstance() )
            return;
        try
        {
            eZDB::instance()->close();
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'Closing the database connection: ' . $e->getMessage(), __METHOD__ );
        }
        eZDB::setInstance( null );
    }
}

?>
