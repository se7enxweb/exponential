<?php
/**
 * The outgoing webhooks of the GraphQL API (doc/eng-us/features/6.0/graphql.md, "Webhooks"): every event of the
 * journal (expGraphQLEvents) an end point asks for is POSTed to it, signed, from the cronjob part graphqlwebhooks
 * (and ./console exp:graphql:webhooks --deliver), never from the request that changed the content.
 *
 * The end points are settings (graphql.ini, written to settings/override by exp:graphql:webhooks --add and by Setup >
 * GraphQL):
 *
 *   [GraphQLWebhooks]
 *   EndpointList[]=shop
 *
 *   [GraphQLWebhook_shop]
 *   URL=https://shop.example.com/hooks/exponential
 *   Events[]=publish                 (empty: every event)
 *   SiteAccessList[]=eng             (content below the root node of these siteaccesses, in their languages; empty: all)
 *   ClassList[]=article              (empty: every class)
 *   SubtreeList[]=2                  (empty: the whole tree)
 *   Secret=graphql.webhook.shop      (the NAME of the signing secret in the secret store, or env:VARIABLE)
 *   Payload=event                    (event: the event and its ids; query: the answer of a persisted query)
 *
 * The signing secret is never in a settings file: it is kept in the kernel's secret store (expSecrets, Setup > Secrets),
 * encrypted, under the name Secret names; exp:graphql:webhooks --add makes one and shows it once.
 *
 * A request: POST, Content-Type application/json, the body
 *
 *   {"data":{"contentEvent":{"id":"…","event":"publish","time":"2026-10-30T09:15:02.120Z","objectId":57,…}},
 *    "extensions":{"webhook":"shop","delivery":"…","attempt":1}}
 *
 * and the headers X-Exponential-Webhook, X-Exponential-Event, X-Exponential-Event-Id, X-Exponential-Delivery,
 * X-Exponential-Timestamp (epoch seconds) and X-Exponential-Signature: sha256=<hex HMAC-SHA-256(secret,
 * timestamp + "." + body)>. A receiver checks the signature and refuses a timestamp more than 300 seconds away (verify()).
 * A 2xx answer delivers it; anything else is tried again after RetryBackoff seconds, doubled each time up to MaxBackoff,
 * at most MaxAttempts times; then the delivery is kept in failed/. Every attempt is a line of the delivery log. A
 * delivery keeps its id over its retries: receivers de-duplicate by it (delivery is at least once).
 *
 * Files under var/<site>/graphql/webhooks/: cursor.json (where in the journal the next run reads), outbox/ (the
 * deliveries waiting), failed/ (given up), deliveries.jsonl (the log).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLWebhooks
{
    /** The name of an end point. */
    const NAME_PATTERN = '/^[a-z][a-z0-9_]{0,39}$/';

    /** The scope of the signing secrets in the secret store. */
    const SECRET_SCOPE = 'graphql';

    /** The schema of the body's extensions, and of exp:graphql:webhooks --format=json. */
    const SCHEMA = 'exp-graphql-webhook/1';

    /** The seconds a receiver accepts a timestamp (verify()). */
    const TOLERANCE = 300;

    /** @var string var/<site>/graphql/webhooks */
    protected $dir;

    /** @var expGraphQLEvents */
    protected $journal;

    /** @var array[] name => endpoint (endpoints()) */
    protected $endpoints;

    /** @var array delivery settings: Timeout, MaxAttempts, RetryBackoff, MaxBackoff, BatchSize, LogMaxBytes */
    protected $settings;

    /** @var callable|null function( string $url, string $body, string[] $headers, int $timeout ): array( status, error ) */
    public $transport = null;

    /** @var callable|null function( string $secretName ): ?string (null: the secret store) */
    public $secretReader = null;

    /** @var callable|null function( string $siteAccess ): array( rootNodeIds, languages ) (null: the siteaccess's settings) */
    public $siteAccessScope = null;

    /** @var callable|null function( array $endpoint, array $event ): array the answer of the persisted query (Payload=query) */
    public $queryRunner = null;

    /** @var int|null a fixed time (tests) */
    public $now = null;

    /** @var array siteaccess => scope, asked once per run */
    protected $scopes = array();

    /**
     * @param string $dir
     * @param expGraphQLEvents $journal
     * @param array[] $endpoints of endpoints()
     * @param array $settings
     */
    public function __construct( $dir, expGraphQLEvents $journal, array $endpoints, array $settings = array() )
    {
        $this->dir = rtrim( (string)$dir, '/' );
        $this->journal = $journal;
        $this->endpoints = $endpoints;
        $this->settings = $settings + array( 'Timeout' => 10, 'MaxAttempts' => 8, 'RetryBackoff' => 30, 'MaxBackoff' => 3600, 'BatchSize' => 200,
                                             'LogMaxBytes' => 1048576 );
    }

    /**
     * The webhooks of the installation (graphql.ini of the current siteaccess, settings/override included).
     *
     * @return self
     */
    public static function fromSettings()
    {
        $ini = eZINI::instance( 'graphql.ini' );
        $settings = array();
        foreach ( array( 'Timeout', 'MaxAttempts', 'RetryBackoff', 'MaxBackoff', 'BatchSize' ) as $name )
        {
            if ( $ini->hasVariable( 'GraphQLWebhooks', $name ) && (int)$ini->variable( 'GraphQLWebhooks', $name ) > 0 )
                $settings[$name] = (int)$ini->variable( 'GraphQLWebhooks', $name );
        }
        if ( $ini->hasVariable( 'GraphQLWebhooks', 'LogMaxBytes' ) && expGraphQLEvents::bytes( $ini->variable( 'GraphQLWebhooks', 'LogMaxBytes' ) ) > 0 )
            $settings['LogMaxBytes'] = expGraphQLEvents::bytes( $ini->variable( 'GraphQLWebhooks', 'LogMaxBytes' ) );
        return new self( eZSys::varDirectory() . '/graphql/webhooks', expGraphQLEvents::fromSettings(), self::endpoints( $ini ), $settings );
    }

    /**
     * @param eZINI|null $ini null: graphql.ini of the current siteaccess
     * @return string[] the names of EndpointList[]
     */
    public static function configuredNames( $ini = null )
    {
        if ( $ini === null )
            $ini = eZINI::instance( 'graphql.ini' );
        if ( !$ini->hasVariable( 'GraphQLWebhooks', 'EndpointList' ) )
            return array();
        $names = array();
        foreach ( (array)$ini->variable( 'GraphQLWebhooks', 'EndpointList' ) as $name )
        {
            $name = trim( (string)$name );
            if ( preg_match( self::NAME_PATTERN, $name ) )
                $names[] = $name;
        }
        return array_values( array_unique( $names ) );
    }

    /**
     * The end points of a graphql.ini.
     *
     * @param eZINI $ini
     * @return array[] name => name, url, events, siteAccesses, classes, subtrees, secret, payload, query, user, enabled,
     *                 problem ('' when it can be used)
     */
    public static function endpoints( $ini )
    {
        $list = array();
        foreach ( self::configuredNames( $ini ) as $name )
        {
            $block = 'GraphQLWebhook_' . $name;
            $get = function ( $variable, $default ) use ( $ini, $block ) {
                return $ini->hasVariable( $block, $variable ) ? $ini->variable( $block, $variable ) : $default;
            };
            $list[$name] = self::endpoint( $name, array(
                'URL' => $get( 'URL', '' ), 'Events' => $get( 'Events', array() ), 'SiteAccessList' => $get( 'SiteAccessList', array() ),
                'ClassList' => $get( 'ClassList', array() ), 'SubtreeList' => $get( 'SubtreeList', array() ), 'Secret' => $get( 'Secret', '' ),
                'Payload' => $get( 'Payload', 'event' ), 'Query' => $get( 'Query', '' ), 'QueryUser' => $get( 'QueryUser', '' ),
                'Enabled' => $get( 'Enabled', 'enabled' ) ) );
        }
        return $list;
    }

    /**
     * An end point from its settings, checked.
     *
     * @param string $name
     * @param array $settings URL, Events, SiteAccessList, ClassList, SubtreeList, Secret, Payload, Query, QueryUser, Enabled
     * @return array
     */
    public static function endpoint( $name, array $settings )
    {
        $list = function ( $value ) {
            return array_values( array_unique( array_filter( array_map( 'trim', array_map( 'strval', (array)$value ) ), 'strlen' ) ) );
        };
        $endpoint = array(
            'name' => (string)$name,
            'url' => trim( (string)( isset( $settings['URL'] ) ? $settings['URL'] : '' ) ),
            'events' => $list( isset( $settings['Events'] ) ? $settings['Events'] : array() ),
            'siteAccesses' => $list( isset( $settings['SiteAccessList'] ) ? $settings['SiteAccessList'] : array() ),
            'classes' => $list( isset( $settings['ClassList'] ) ? $settings['ClassList'] : array() ),
            'subtrees' => array_map( 'intval', $list( isset( $settings['SubtreeList'] ) ? $settings['SubtreeList'] : array() ) ),
            'secret' => trim( (string)( isset( $settings['Secret'] ) ? $settings['Secret'] : '' ) ),
            'payload' => trim( (string)( isset( $settings['Payload'] ) ? $settings['Payload'] : 'event' ) ) ?: 'event',
            'query' => trim( (string)( isset( $settings['Query'] ) ? $settings['Query'] : '' ) ),
            'user' => trim( (string)( isset( $settings['QueryUser'] ) ? $settings['QueryUser'] : '' ) ),
            'enabled' => ( isset( $settings['Enabled'] ) ? (string)$settings['Enabled'] : 'enabled' ) !== 'disabled',
        );
        if ( $endpoint['secret'] === '' )
            $endpoint['secret'] = self::defaultSecretName( $name );
        $endpoint['problem'] = self::problem( $endpoint );
        return $endpoint;
    }

    /**
     * @param array $endpoint
     * @return string why the end point cannot be used ('' when it can)
     */
    public static function problem( array $endpoint )
    {
        if ( !preg_match( self::NAME_PATTERN, $endpoint['name'] ) )
            return 'the name must be lower case letters, digits and _, starting with a letter';
        $urlProblem = self::urlProblem( $endpoint['url'] );
        if ( $urlProblem !== '' )
            return $urlProblem;
        foreach ( $endpoint['events'] as $event )
        {
            if ( !expGraphQLEvents::isEvent( $event ) )
                return "unknown event $event (" . implode( ', ', expGraphQLEvents::ALL ) . ')';
        }
        if ( $endpoint['payload'] !== 'event' && $endpoint['payload'] !== 'query' )
            return 'Payload must be event or query';
        if ( $endpoint['payload'] === 'query' && $endpoint['query'] === '' )
            return 'Payload=query needs Query, the name or SHA-256 of a persisted query';
        if ( !class_exists( 'expSecrets' ) || !expSecrets::validName( $endpoint['secret'] ) )
            return 'Secret must name a secret of the secret store, or env:VARIABLE';
        return '';
    }

    /**
     * @param string $url
     * @return string why it cannot be an end point ('' when it can): http or https, a host, no user or password in it
     */
    public static function urlProblem( $url )
    {
        $url = (string)$url;
        if ( $url === '' )
            return 'no URL';
        $parts = parse_url( $url );
        if ( !is_array( $parts ) || empty( $parts['host'] ) || !isset( $parts['scheme'] ) || !in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) )
            return 'the URL must be http:// or https:// with a host';
        if ( isset( $parts['user'] ) || isset( $parts['pass'] ) )
            return 'the URL must not carry a user or a password (the signature proves who sends)';
        return '';
    }

    /**
     * @param string $name
     * @return string graphql.webhook.<name>
     */
    public static function defaultSecretName( $name )
    {
        return 'graphql.webhook.' . $name;
    }

    /**
     * @return string a new signing secret: 32 random bytes as 64 hexadecimal digits
     */
    public static function newSecret()
    {
        return bin2hex( random_bytes( 32 ) );
    }

    /**
     * @param string $secret
     * @param int $timestamp
     * @param string $body
     * @return string sha256=<hex HMAC-SHA-256( secret, timestamp + "." + body )>
     */
    public static function signature( $secret, $timestamp, $body )
    {
        return 'sha256=' . hash_hmac( 'sha256', (int)$timestamp . '.' . $body, (string)$secret );
    }

    /**
     * The receiver's check: the signature matches and the timestamp is within $tolerance seconds.
     *
     * @param string $secret
     * @param string $timestamp X-Exponential-Timestamp
     * @param string $body
     * @param string $signature X-Exponential-Signature
     * @param int $tolerance
     * @param int|null $now
     * @return string '' when valid, else why not (stale_timestamp, bad_signature)
     */
    public static function verify( $secret, $timestamp, $body, $signature, $tolerance = self::TOLERANCE, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        if ( !ctype_digit( (string)$timestamp ) || abs( $now - (int)$timestamp ) > $tolerance )
            return 'stale_timestamp';
        return hash_equals( self::signature( $secret, (int)$timestamp, $body ), (string)$signature ) ? '' : 'bad_signature';
    }

    /**
     * @return array[] name => endpoint
     */
    public function all()
    {
        return $this->endpoints;
    }

    /** @return string */
    public function directory()
    {
        return $this->dir;
    }

    /** @return int */
    protected function now()
    {
        return $this->now !== null ? (int)$this->now : time();
    }

    /**
     * Whether an end point wants an event: its events, classes, subtrees and siteaccesses.
     *
     * @param array $endpoint
     * @param array $event
     * @return bool
     */
    public function wants( array $endpoint, array $event )
    {
        if ( !$endpoint['enabled'] || $endpoint['problem'] !== '' )
            return false;
        if ( !expGraphQLEvents::matches( $event, array( 'events' => $endpoint['events'], 'classes' => $endpoint['classes'], 'subtrees' => $endpoint['subtrees'] ) ) )
            return false;
        if ( !$endpoint['siteAccesses'] )
            return true;
        foreach ( $endpoint['siteAccesses'] as $siteAccess )
        {
            $scope = $this->scope( $siteAccess );
            if ( expGraphQLEvents::matches( $event, array( 'rootNodeIds' => $scope['rootNodeIds'], 'languages' => $scope['languages'] ) ) )
                return true;
        }
        return false;
    }

    /**
     * @param string $siteAccess
     * @return array rootNodeIds, languages: the content tree and the languages of a siteaccess
     */
    protected function scope( $siteAccess )
    {
        if ( isset( $this->scopes[$siteAccess] ) )
            return $this->scopes[$siteAccess];
        if ( $this->siteAccessScope !== null )
            $scope = call_user_func( $this->siteAccessScope, $siteAccess );
        else
        {
            $scope = array( 'rootNodeIds' => array(), 'languages' => array() );
            try
            {
                $content = eZSiteAccess::getIni( $siteAccess, 'content.ini' );
                $site = eZSiteAccess::getIni( $siteAccess, 'site.ini' );
                if ( $content instanceof eZINI && (int)$content->variable( 'NodeSettings', 'RootNode' ) > 0 )
                    $scope['rootNodeIds'] = array( (int)$content->variable( 'NodeSettings', 'RootNode' ) );
                if ( $site instanceof eZINI && $site->hasVariable( 'RegionalSettings', 'SiteLanguageList' ) )
                    $scope['languages'] = array_values( array_filter( (array)$site->variable( 'RegionalSettings', 'SiteLanguageList' ), 'strlen' ) );
            }
            catch ( Exception $e )
            {
                eZDebug::writeWarning( "The settings of the siteaccess $siteAccess could not be read: " . $e->getMessage(), __METHOD__ );
            }
        }
        return $this->scopes[$siteAccess] = $scope + array( 'rootNodeIds' => array(), 'languages' => array() );
    }

    /**
     * Reads the journal from the cursor and puts a delivery in the outbox for every end point that wants an event.
     * A first run (no cursor yet) starts at the end of the journal: an end point gets what happens after it was set up.
     *
     * @param int $limit at most this many events
     * @return array events (read), queued (deliveries put in the outbox), lost (events removed from the journal before
     *               they were read), position
     */
    public function dispatch( $limit = 1000 )
    {
        $cursor = $this->cursor();
        if ( $cursor === null )
        {
            $this->writeCursor( $this->journal->end() );
            return array( 'events' => 0, 'queued' => 0, 'lost' => false, 'position' => $this->journal->end() );
        }
        $read = $this->journal->read( $cursor, $limit );
        $queued = 0;
        foreach ( $read['events'] as $event )
        {
            unset( $event['_position'] );
            foreach ( $this->endpoints as $endpoint )
            {
                if ( $this->wants( $endpoint, $event ) && $this->queue( $endpoint['name'], $event ) )
                    $queued++;
            }
        }
        $this->writeCursor( $read['position'] );
        return array( 'events' => count( $read['events'] ), 'queued' => $queued, 'lost' => $read['lost'], 'position' => $read['position'] );
    }

    /**
     * Puts a delivery in the outbox.
     *
     * @param string $endpoint
     * @param array $event a record of the journal
     * @return string|null the delivery id
     */
    public function queue( $endpoint, array $event )
    {
        $dir = $this->dir . '/outbox';
        if ( !expGraphQLEvents::ensureDirectory( $dir ) )
            return null;
        $id = expGraphQLEvents::newId();
        $entry = array( 'id' => $id, 'endpoint' => (string)$endpoint, 'event' => $event, 'attempts' => 0, 'next' => $this->now(),
                        'created' => $this->now(), 'lastStatus' => null, 'lastError' => null );
        $path = $dir . '/' . $id . '-' . $endpoint . '.json';
        if ( !self::writeJson( $path, $entry ) )
            return null;
        return $id;
    }

    /**
     * A test delivery (event ping) for an end point, put in the outbox.
     *
     * @param string $endpoint
     * @return string|null the delivery id
     */
    public function queueTest( $endpoint )
    {
        return $this->queue( $endpoint, self::pingEvent() );
    }

    /**
     * @return array the event of a test delivery
     */
    public static function pingEvent()
    {
        return expGraphQLEvents::record( array( 'event' => expGraphQLEvents::PING ) );
    }

    /**
     * Sends the deliveries of the outbox that are due, oldest first. One run at a time (a lock): a second run at the
     * same time does nothing.
     *
     * @param int|null $limit at most this many (null: BatchSize)
     * @return array sent, failed (will be tried again), gaveUp, waiting (still in the outbox), busy (another run holds the lock)
     */
    public function deliver( $limit = null )
    {
        $report = array( 'sent' => 0, 'failed' => 0, 'gaveUp' => 0, 'waiting' => 0, 'busy' => false );
        if ( !expGraphQLEvents::ensureDirectory( $this->dir ) )
            return $report;
        $lock = @fopen( $this->dir . '/deliver.lock', 'cb' );
        if ( !$lock || !flock( $lock, LOCK_EX | LOCK_NB ) )
        {
            $report['busy'] = true;
            if ( $lock )
                fclose( $lock );
            return $report;
        }
        $limit = $limit === null ? (int)$this->settings['BatchSize'] : (int)$limit;
        $now = $this->now();
        foreach ( $this->outbox() as $path )
        {
            $entry = self::readJson( $path );
            if ( !is_array( $entry ) || !isset( $entry['endpoint'], $entry['event'] ) )
            {
                @unlink( $path );
                continue;
            }
            if ( (int)$entry['next'] > $now || $limit <= 0 )
            {
                $report['waiting']++;
                continue;
            }
            $limit--;
            $result = $this->attempt( $entry );
            if ( $result === 'sent' )
            {
                @unlink( $path );
                $report['sent']++;
            }
            else if ( $result === 'gaveUp' )
            {
                $failed = $this->dir . '/failed';
                if ( expGraphQLEvents::ensureDirectory( $failed ) )
                    @rename( $path, $failed . '/' . basename( $path ) );
                else
                    @unlink( $path );
                $report['gaveUp']++;
            }
            else
            {
                self::writeJson( $path, $result );
                $report['failed']++;
                $report['waiting']++;
            }
        }
        flock( $lock, LOCK_UN );
        fclose( $lock );
        return $report;
    }

    /**
     * Sends one delivery now.
     *
     * @param array $entry
     * @return string|array sent, gaveUp, or the entry with its next try
     */
    protected function attempt( array $entry )
    {
        $entry['attempts'] = (int)$entry['attempts'] + 1;
        $endpoint = isset( $this->endpoints[$entry['endpoint']] ) ? $this->endpoints[$entry['endpoint']] : null;
        $started = microtime( true );
        $status = 0;
        if ( $endpoint === null )
            $error = 'the end point is no longer configured';
        else if ( $endpoint['problem'] !== '' )
            $error = $endpoint['problem'];
        else
        {
            $secret = $this->secret( $endpoint );
            if ( $secret === null || $secret === '' )
                $error = 'the signing secret ' . $endpoint['secret'] . ' is not set (Setup > Secrets)';
            else
            {
                list( $body, $headers ) = $this->request( $endpoint, $entry['event'], $entry['id'], $entry['attempts'], $secret );
                list( $status, $error ) = $this->send( $endpoint['url'], $body, $headers );
            }
        }
        $ms = (int)round( ( microtime( true ) - $started ) * 1000 );
        $sent = $error === null || $error === '';
        $gaveUp = !$sent && ( $entry['attempts'] >= (int)$this->settings['MaxAttempts'] || $endpoint === null );
        $this->log( array( 'time' => $this->now(), 'delivery' => $entry['id'], 'endpoint' => $entry['endpoint'],
                           'event' => isset( $entry['event']['event'] ) ? (string)$entry['event']['event'] : '',
                           'eventId' => isset( $entry['event']['id'] ) ? (string)$entry['event']['id'] : '',
                           'objectId' => isset( $entry['event']['objectId'] ) ? (int)$entry['event']['objectId'] : null,
                           'attempt' => $entry['attempts'], 'status' => (int)$status, 'ms' => $ms,
                           'result' => $sent ? 'sent' : ( $gaveUp ? 'gave-up' : 'retry' ), 'error' => $sent ? null : (string)$error ) );
        if ( $sent )
            return 'sent';
        if ( $gaveUp )
            return 'gaveUp';
        $entry['lastStatus'] = (int)$status;
        $entry['lastError'] = (string)$error;
        $entry['next'] = $this->now() + $this->backoff( $entry['attempts'] );
        return $entry;
    }

    /**
     * @param int $attempts the attempts made so far
     * @return int seconds before the next one: RetryBackoff, doubled after each failed attempt, at most MaxBackoff
     */
    public function backoff( $attempts )
    {
        $base = max( 1, (int)$this->settings['RetryBackoff'] );
        $seconds = $base * pow( 2, max( 0, (int)$attempts - 1 ) );
        return (int)min( (int)$this->settings['MaxBackoff'], $seconds );
    }

    /**
     * The body and the headers of a delivery.
     *
     * @param array $endpoint
     * @param array $event a record of the journal
     * @param string $deliveryId
     * @param int $attempt
     * @param string $secret
     * @param int|null $timestamp
     * @return array( body, headers[] )
     */
    public function request( array $endpoint, array $event, $deliveryId, $attempt, $secret, $timestamp = null )
    {
        $public = expGraphQLEvents::publicFields( $event );
        $extensions = array( 'schema' => self::SCHEMA, 'webhook' => $endpoint['name'], 'delivery' => (string)$deliveryId, 'attempt' => (int)$attempt );
        if ( $endpoint['payload'] === 'query' && $event['event'] !== expGraphQLEvents::PING )
        {
            $answer = $this->runQuery( $endpoint, $event );
            $payload = array();
            if ( array_key_exists( 'data', $answer ) )
                $payload['data'] = $answer['data'];
            if ( !empty( $answer['errors'] ) )
                $payload['errors'] = $answer['errors'];
            $payload['extensions'] = $extensions + array( 'event' => $public );
        }
        else
            $payload = array( 'data' => array( 'contentEvent' => $public ), 'extensions' => $extensions );
        $body = json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
        $timestamp = $timestamp === null ? $this->now() : (int)$timestamp;
        $headers = array( 'Content-Type: application/json; charset=UTF-8',
                          'User-Agent: Exponential-GraphQL-Webhooks/1',
                          'X-Exponential-Webhook: ' . $endpoint['name'],
                          'X-Exponential-Event: ' . $public['event'],
                          'X-Exponential-Event-Id: ' . $public['id'],
                          'X-Exponential-Delivery: ' . $deliveryId,
                          'X-Exponential-Timestamp: ' . $timestamp,
                          'X-Exponential-Signature: ' . self::signature( $secret, $timestamp, (string)$body ) );
        return array( (string)$body, $headers );
    }

    /**
     * The answer of an end point's persisted query for an event (Payload=query), with the variables objectId, nodeId
     * (the main node), version, language and event, run as QueryUser (default: the anonymous user) with that user's
     * policies.
     *
     * @param array $endpoint
     * @param array $event
     * @return array the GraphQL answer
     */
    protected function runQuery( array $endpoint, array $event )
    {
        if ( $this->queryRunner !== null )
            return (array)call_user_func( $this->queryRunner, $endpoint, $event );
        $variables = array( 'objectId' => isset( $event['objectId'] ) ? (int)$event['objectId'] : null,
                            'nodeId' => isset( $event['mainNodeId'] ) ? (int)$event['mainNodeId'] : ( isset( $event['nodeIds'][0] ) ? (int)$event['nodeIds'][0] : null ),
                            'version' => isset( $event['version'] ) ? (int)$event['version'] : null,
                            'language' => isset( $event['language'] ) ? (string)$event['language'] : null,
                            'event' => (string)$event['event'] );
        if ( !expGraphQL::loadLibrary() )
            return array( 'errors' => array( array( 'message' => 'The GraphQL library is not installed.', 'extensions' => array( 'code' => expGraphQLError::INTERNAL ) ) ) );
        $previous = eZUser::currentUser();
        $user = $endpoint['user'] !== '' ? eZUser::fetchByName( $endpoint['user'] ) : null;
        if ( !$user instanceof eZUser )
            $user = eZUser::fetch( (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) );
        try
        {
            if ( $user instanceof eZUser )
                eZUser::setCurrentlyLoggedInUser( $user, (int)$user->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
            $builder = expGraphQLSchemaBuilder::fromSettings();
            $context = new expGraphQLContext( $user, expGraphQLContext::AUTH_CONSOLE, new expGraphQLRepository( $builder->catalogue() ),
                                              expGraphQLContext::requestBaseUrl() );
            $result = ( new expGraphQLServer( $builder ) )->run( array( 'id' => $endpoint['query'], 'variables' => $variables ), $context );
            return $result['answer'];
        }
        catch ( Exception $e )
        {
            eZDebug::writeError( 'The persisted query of the webhook ' . $endpoint['name'] . ': ' . $e->getMessage(), __METHOD__ );
            return array( 'errors' => array( array( 'message' => 'Internal server error', 'extensions' => array( 'code' => expGraphQLError::INTERNAL ) ) ) );
        }
        finally
        {
            if ( $previous instanceof eZUser )
                eZUser::setCurrentlyLoggedInUser( $previous, (int)$previous->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        }
    }

    /**
     * Sends a request now and tells how it went, without the outbox (exp:graphql:webhooks --test --apply).
     *
     * @param string $name
     * @return array status, error (null: delivered), ms, body, headers (the signature masked)
     */
    public function sendTest( $name )
    {
        if ( !isset( $this->endpoints[$name] ) )
            return array( 'status' => 0, 'error' => "no webhook $name", 'ms' => 0, 'body' => '', 'headers' => array() );
        $endpoint = $this->endpoints[$name];
        if ( $endpoint['problem'] !== '' )
            return array( 'status' => 0, 'error' => $endpoint['problem'], 'ms' => 0, 'body' => '', 'headers' => array() );
        $secret = $this->secret( $endpoint );
        if ( $secret === null || $secret === '' )
            return array( 'status' => 0, 'error' => 'the signing secret ' . $endpoint['secret'] . ' is not set (Setup > Secrets)', 'ms' => 0, 'body' => '', 'headers' => array() );
        $id = expGraphQLEvents::newId();
        list( $body, $headers ) = $this->request( $endpoint, self::pingEvent(), $id, 1, $secret );
        $started = microtime( true );
        list( $status, $error ) = $this->send( $endpoint['url'], $body, $headers );
        $ms = (int)round( ( microtime( true ) - $started ) * 1000 );
        $this->log( array( 'time' => $this->now(), 'delivery' => $id, 'endpoint' => $name, 'event' => expGraphQLEvents::PING, 'eventId' => '',
                           'objectId' => null, 'attempt' => 1, 'status' => (int)$status, 'ms' => $ms,
                           'result' => $error === null ? 'sent' : 'failed', 'error' => $error ) );
        return array( 'status' => (int)$status, 'error' => $error, 'ms' => $ms, 'body' => $body, 'headers' => self::maskSignature( $headers ) );
    }

    /**
     * @param string[] $headers
     * @return string[] the headers with the signature shown as sha256=…
     */
    public static function maskSignature( array $headers )
    {
        return array_map( function ( $header ) {
            return stripos( $header, 'X-Exponential-Signature:' ) === 0 ? 'X-Exponential-Signature: sha256=…' : $header;
        }, $headers );
    }

    /**
     * @param array $endpoint
     * @return string|null the signing secret (never logged, never shown after it was made)
     */
    protected function secret( array $endpoint )
    {
        if ( $this->secretReader !== null )
            return call_user_func( $this->secretReader, $endpoint['secret'] );
        return class_exists( 'expSecrets' ) ? expSecrets::get( $endpoint['secret'] ) : null;
    }

    /**
     * POSTs a body: no redirects, http and https only, the timeout of the settings.
     *
     * @param string $url
     * @param string $body
     * @param string[] $headers
     * @return array( status, error ) error null on a 2xx answer
     */
    protected function send( $url, $body, array $headers )
    {
        $timeout = max( 1, (int)$this->settings['Timeout'] );
        if ( $this->transport !== null )
            $result = call_user_func( $this->transport, $url, $body, $headers, $timeout );
        else
        {
            if ( !function_exists( 'curl_init' ) )
                return array( 0, 'the PHP extension curl is missing' );
            $ch = curl_init( $url );
            curl_setopt_array( $ch, array(
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_CONNECTTIMEOUT => min( $timeout, 5 ),
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            ) );
            curl_exec( $ch );
            $status = (int)curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
            $error = curl_errno( $ch ) ? curl_error( $ch ) : null;
            unset( $ch ); // freed with the handle (curl_close() has had no effect since PHP 8.0)
            $result = array( $status, $error );
        }
        list( $status, $error ) = array( (int)$result[0], isset( $result[1] ) ? $result[1] : null );
        if ( $error === null && ( $status < 200 || $status > 299 ) )
            $error = 'HTTP ' . $status;
        return array( $status, $error );
    }

    /**
     * @return string[] the files of the outbox, oldest first
     */
    public function outbox()
    {
        $files = glob( $this->dir . '/outbox/*.json' );
        if ( !is_array( $files ) )
            return array();
        sort( $files );
        return $files;
    }

    /**
     * @return array name => array( waiting, failed, last (the last log line) ); and '' => the totals
     */
    public function stats()
    {
        $stats = array();
        foreach ( array_keys( $this->endpoints ) as $name )
            $stats[$name] = array( 'waiting' => 0, 'failed' => 0, 'last' => null );
        $count = function ( $files, $key ) use ( &$stats ) {
            foreach ( is_array( $files ) ? $files : array() as $file )
            {
                if ( preg_match( '/^\d+-[0-9a-f]+-([a-z][a-z0-9_]*)\.json$/', basename( $file ), $m ) )
                {
                    if ( !isset( $stats[$m[1]] ) )
                        $stats[$m[1]] = array( 'waiting' => 0, 'failed' => 0, 'last' => null );
                    $stats[$m[1]][$key]++;
                }
            }
        };
        $count( glob( $this->dir . '/outbox/*.json' ), 'waiting' );
        $count( glob( $this->dir . '/failed/*.json' ), 'failed' );
        foreach ( array_reverse( $this->recent( 500 ) ) as $line )
        {
            if ( isset( $line['endpoint'], $stats[$line['endpoint']] ) && $stats[$line['endpoint']]['last'] === null )
                $stats[$line['endpoint']]['last'] = $line;
        }
        return $stats;
    }

    /**
     * Appends a line to the delivery log (never a secret, never a body); a log past LogMaxBytes is kept as
     * deliveries.1.jsonl and a new one started.
     *
     * @param array $line
     */
    protected function log( array $line )
    {
        if ( !expGraphQLEvents::ensureDirectory( $this->dir ) )
            return;
        $path = $this->dir . '/deliveries.jsonl';
        clearstatcache( true, $path );
        if ( is_file( $path ) && filesize( $path ) > (int)$this->settings['LogMaxBytes'] )
            @rename( $path, $this->dir . '/deliveries.1.jsonl' );
        $created = !is_file( $path );
        $json = json_encode( $line, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
        if ( $json !== false && @file_put_contents( $path, $json . "\n", FILE_APPEND | LOCK_EX ) !== false && $created )
            expGraphQLEvents::ownLikeParent( $path, 0640 );
    }

    /**
     * @param int $limit
     * @return array[] the last lines of the delivery log, oldest first
     */
    public function recent( $limit = 20 )
    {
        $lines = array();
        foreach ( array( $this->dir . '/deliveries.1.jsonl', $this->dir . '/deliveries.jsonl' ) as $path )
        {
            if ( !is_file( $path ) )
                continue;
            foreach ( (array)@file( $path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) as $text )
            {
                $line = json_decode( (string)$text, true );
                if ( is_array( $line ) )
                    $lines[] = $line;
            }
        }
        return array_slice( $lines, -max( 1, (int)$limit ) );
    }

    /**
     * @return string|null the position in the journal the next dispatch() reads from (null: never ran)
     */
    public function cursor()
    {
        $data = self::readJson( $this->dir . '/cursor.json' );
        return is_array( $data ) && isset( $data['position'] ) && expGraphQLEvents::isPosition( $data['position'] ) ? $data['position'] : null;
    }

    /**
     * Starts the cursor at the end of the journal when there is none yet (an end point was added).
     */
    public function startCursor()
    {
        if ( $this->cursor() === null )
            $this->writeCursor( $this->journal->end() );
    }

    /**
     * @param string $position
     */
    protected function writeCursor( $position )
    {
        if ( expGraphQLEvents::ensureDirectory( $this->dir ) )
            self::writeJson( $this->dir . '/cursor.json', array( 'position' => (string)$position, 'time' => $this->now() ) );
    }

    /**
     * The settings changes that add an end point (exp:graphql:webhooks --add, Setup > GraphQL), in settings/override.
     *
     * @param string $name
     * @param array $settings URL, Events, SiteAccessList, ClassList, SubtreeList, Secret, Payload, Query, QueryUser
     * @return array[] changes for expGraphQLSetup::apply()
     * @throws expGraphQLError BAD_USER_INPUT when the end point would not work
     */
    public static function addChanges( $name, array $settings )
    {
        $endpoint = self::endpoint( $name, $settings );
        if ( $endpoint['problem'] !== '' )
            throw new expGraphQLError( $endpoint['problem'], expGraphQLError::BAD_USER_INPUT );
        $block = 'GraphQLWebhook_' . $name;
        $change = function ( $op, $block, $variable, $value = '' ) {
            return array( 'scope' => 'global', 'file' => 'graphql', 'op' => $op, 'block' => $block, 'variable' => $variable, 'value' => (string)$value );
        };
        $changes = array();
        if ( !in_array( $name, self::configuredNames(), true ) )
            $changes[] = $change( 'add', 'GraphQLWebhooks', 'EndpointList', $name );
        $changes[] = $change( 'set', $block, 'URL', $endpoint['url'] );
        foreach ( array( 'Events' => 'events', 'SiteAccessList' => 'siteAccesses', 'ClassList' => 'classes', 'SubtreeList' => 'subtrees' ) as $variable => $key )
        {
            $changes[] = $change( 'clear', $block, $variable );
            foreach ( $endpoint[$key] as $value )
                $changes[] = $change( 'add', $block, $variable, $value );
        }
        $changes[] = $change( 'set', $block, 'Secret', $endpoint['secret'] );
        $changes[] = $change( 'set', $block, 'Payload', $endpoint['payload'] );
        if ( $endpoint['payload'] === 'query' )
        {
            $changes[] = $change( 'set', $block, 'Query', $endpoint['query'] );
            if ( $endpoint['user'] !== '' )
                $changes[] = $change( 'set', $block, 'QueryUser', $endpoint['user'] );
        }
        return $changes;
    }

    /**
     * The settings changes that remove an end point from settings/override.
     *
     * @param string $name
     * @return array[]
     */
    public static function removeChanges( $name )
    {
        return array( array( 'scope' => 'global', 'file' => 'graphql', 'op' => 'remove', 'block' => 'GraphQLWebhooks', 'variable' => 'EndpointList', 'value' => (string)$name ),
                      array( 'scope' => 'global', 'file' => 'graphql', 'op' => 'removeBlock', 'block' => 'GraphQLWebhook_' . $name, 'variable' => '', 'value' => '' ) );
    }

    /**
     * Stores a new signing secret for an end point in the secret store.
     *
     * @param array $endpoint
     * @param string $secret
     * @return bool
     */
    public static function storeSecret( array $endpoint, $secret )
    {
        if ( strpos( $endpoint['secret'], 'env:' ) === 0 || !class_exists( 'expSecrets' ) )
            return false;
        return expSecrets::set( $endpoint['secret'], (string)$secret, self::SECRET_SCOPE, 'Signing secret of the GraphQL webhook ' . $endpoint['name'] );
    }

    /**
     * @param string $path
     * @return mixed
     */
    protected static function readJson( $path )
    {
        if ( !is_file( $path ) )
            return null;
        $text = @file_get_contents( $path );
        return $text === false ? null : json_decode( $text, true );
    }

    /**
     * Writes a file whole (a temporary file renamed), so a reader never sees half of it.
     *
     * @param string $path
     * @param array $data
     * @return bool
     */
    protected static function writeJson( $path, array $data )
    {
        $json = json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
        if ( $json === false )
            return false;
        $temp = $path . '.' . getmypid() . '.tmp';
        if ( @file_put_contents( $temp, $json ) === false )
            return false;
        expGraphQLEvents::ownLikeParent( $temp, 0640 );
        if ( !@rename( $temp, $path ) )
        {
            @unlink( $temp );
            return false;
        }
        return true;
    }
}

?>
