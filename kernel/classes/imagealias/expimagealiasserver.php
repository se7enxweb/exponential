<?php
/**
 * File containing the expImageAliasServer class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Answers one request for an image alias: the view content/image (handleParams()) and index_image.php, the entry
 * script the web server hands a missing storage file to (handleStoragePath()).
 *
 * 1. Verify: a signed URL (expImageAliasUrl) is the permission to read and to make the alias; an unsigned or wrongly
 *    signed one only serves an alias that exists.
 * 2. Serve, or make: an alias that exists is answered at once (no database for a signed URL on a single server).
 *    A missing one is made within [AliasSettings] MaxWait seconds: the attribute is loaded and its render-mode
 *    eZImageAliasHandler::imageAlias() makes that one alias, so the file and the database writes (the alias list of
 *    the attribute, ezimagefile) are exactly those of render mode.
 * 3. Answer: the file (a redirect to the storage URL, or the file itself; [AliasSettings] Delivery) with long-lived
 *    cache headers, or the placeholder (expImagePlaceholder) with no-store: 200 while a signed image is still being
 *    made, 404 for a refused or unknown one.
 * 4. Formats: an alias whose block lists Formats[]=webp / avif is answered as that variant (expImageFormatConverter,
 *    "<alias file>.webp" next to the alias) to a browser whose Accept header names the type, while [AliasSettings]
 *    Negotiate is enabled; every answer for such an alias carries Vary: Accept. A storage path of a variant is made as
 *    the alias is (index_image.php).
 *
 * The decisions return a response array (status, headers, body or file) and emit() sends it, so they can be tested
 * without a web server.
 *
 * @since 6.0.15
 * @package kernel
 */
class expImageAliasServer
{
    /** @var float when the request began (for MaxWait) */
    protected $start;

    /** @var bool the request came for a storage path (index_image.php): never answered with a redirect to itself */
    protected $storageRequest = false;

    /** @var string the request's Accept header (format negotiation) */
    protected $accept = '';

    /** @var bool the answer depends on the Accept header (the alias has formats to negotiate): Vary: Accept */
    protected $vary = false;

    /** @var bool a queue job or script completes the work (no MaxWait, nothing queued): forJob() */
    protected $job = false;

    /** @var array|null attribute, version, language of the image this request is about (for queueing a miss) */
    protected $miss = null;

    /** @var int[] ids of the jobs this request queued (tests, the log) */
    protected $queued = array();

    /** @var bool|null this request uses the backlog (usesBacklog()) */
    protected $backlog = null;

    public function __construct()
    {
        $this->start = microtime( true );
        $this->accept = isset( $_SERVER['HTTP_ACCEPT'] ) ? (string)$_SERVER['HTTP_ACCEPT'] : '';
    }

    /**
     * A server for background work (expImageAliasJob): it waits for locks and conversion slots as long as
     * [ImageConverterSettings] LockTimeout, not MaxWait, and queues nothing.
     *
     * @return expImageAliasServer
     */
    public static function forJob()
    {
        $server = new self();
        $server->job = true;
        return $server;
    }

    /**
     * Seconds this request may still spend making or fetching (what is left of MaxWait; a job: LockTimeout).
     *
     * @return float
     */
    protected function left()
    {
        if ( $this->job )
            return expImageAliasSettings::lockTimeout();
        return max( 0.0, expImageAliasSettings::maxWait() - ( microtime( true ) - $this->start ) );
    }

    /**
     * @return int[] ids of the jobs this server queued
     */
    public function queuedJobs()
    {
        return $this->queued;
    }

    /**
     * Sets the Accept header the answer is negotiated for (tests; a worker serving another request).
     *
     * @param string $accept
     */
    public function setAccept( $accept )
    {
        $this->accept = (string)$accept;
    }

    // ------------------------------------------------------------------------------------------------ entry points

    /**
     * The view content/image: its module view parameters, in order.
     *
     * @param array $params
     * @return array the response
     */
    public function handleParams( array $params )
    {
        $parsed = expImageAliasUrl::parse( $params );
        if ( $parsed === null )
            return $this->placeholder( null, null, 404, 'unknown' );
        switch ( $parsed['form'] )
        {
            case 'token':
            case 'ids-token':
                return $this->handleSigned( $parsed );
            case 'ids':
                return $this->handleIds( $parsed );
            case 'node':
                return $this->handleNode( $parsed );
        }
        return $this->placeholder( null, null, 404, 'unknown' );
    }

    /**
     * A storage path the web server did not find (index_image.php): served when it is there now, made when it names
     * a configured alias of the published version of a stored image, else the placeholder with 404.
     *
     * @param string $path the request path (with or without the leading slash and the site's www directory)
     * @return array the response
     */
    public function handleStoragePath( $path )
    {
        $this->storageRequest = true;
        $path = self::normalizeRequestPath( $path );
        // Only the image storage: index_image.php must not hand out what the web server keeps behind content/download
        if ( !expImageAliasUrl::validImagePath( $path ) )
            return $this->placeholder( null, null, 404, 'not-storage' );
        $variant = expImageFormatConverter::splitVariant( $path );
        if ( $variant !== null )
            return $this->handleStorageVariant( $path, $variant[0], $variant[1] );
        if ( self::exists( $path ) )
            return $this->deliver( $path, null );

        $aliasNames = array_keys( eZImageManager::factory()->aliasList() );
        $parsed = expImageAliasUrl::parseStoragePath( $path, $aliasNames );
        // Staging: the original of a published image is fetched from the source installation
        if ( $parsed !== null && !$parsed['versioned'] )
        {
            $stager = expImageAliasStager::forRequest();
            $staged = $stager !== null ? $this->stageOriginal( $stager, $parsed, $path ) : null;
            if ( $staged !== null )
                return $staged;
        }
        if ( $parsed === null || !$parsed['candidates'] || $parsed['versioned'] )
            return $this->placeholder( null, null, 404, $parsed !== null && $parsed['versioned'] ? 'versioned' : 'unknown' );

        $attribute = $this->publishedImageAttribute( $parsed['attribute'], $parsed['version'], $parsed['language'] );
        if ( $attribute === null )
            return $this->placeholder( null, null, 404, 'unpublished' );
        $handler = $attribute->attribute( 'content' );
        $aliasList = $handler instanceof eZImageAliasHandler ? $handler->aliasList() : array();
        $original = isset( $aliasList['original'] ) ? $aliasList['original'] : null;
        if ( !$original || (string)$original['url'] === '' || dirname( (string)$original['url'] ) !== $parsed['directory'] )
            return $this->placeholder( null, null, 404, 'other-image' );
        $this->miss = array( 'attribute' => (int)$attribute->attribute( 'id' ), 'version' => (int)$attribute->attribute( 'version' ),
                             'language' => (string)$attribute->attribute( 'language_code' ) );
        foreach ( $parsed['candidates'] as $candidate )
        {
            if ( $candidate['basename'] !== (string)$original['basename'] )
                continue;
            $made = $this->generate( $attribute, $candidate['alias'], $path );
            if ( self::exists( $path ) )
                return $this->deliver( $path, null );
            if ( $made === 'timeout' )
            {
                $this->queueCompletion( $candidate['alias'], array(), $path );
                return $this->placeholder( null, null, 200, 'pending', $candidate['alias'], $path );
            }
        }
        return $this->placeholder( null, null, 404, 'not-made', null, $path );
    }

    /**
     * A variant asked for by its storage path (<alias file>.webp, .avif) that the web server did not find: made from
     * its alias (made first when missing, as handleStoragePath() makes it) when an alias the file name can be lists
     * the format in Formats[]; else the placeholder with 404.
     *
     * @param string $path the variant
     * @param string $aliasPath its alias file
     * @param string $format
     * @return array the response
     */
    protected function handleStorageVariant( $path, $aliasPath, $format )
    {
        $aliasNames = array_keys( eZImageManager::factory()->aliasList() );
        $parsed = expImageAliasUrl::parseStoragePath( $aliasPath, $aliasNames );
        if ( $parsed === null || $parsed['versioned'] )
            return $this->placeholder( null, null, 404, 'unknown', null, $path );
        $aliasName = null;
        foreach ( $parsed['candidates'] as $candidate )
        {
            if ( in_array( $format, expImageAliasSettings::aliasFormats( $candidate['alias'] ), true ) )
            {
                $aliasName = $candidate['alias'];
                break;
            }
        }
        $aliasMime = eZMimeType::findByURL( $aliasPath );
        if ( $aliasName === null || !expImageFormatConverter::variantApplies( $aliasMime['name'], $format )
             || !expImageFormatConverter::canWrite( $format ) )
            return $this->placeholder( null, null, 404, 'no-format', $aliasName, $path );
        $this->miss = array( 'attribute' => (int)$parsed['attribute'], 'version' => (int)$parsed['version'], 'language' => (string)$parsed['language'] );
        if ( !self::exists( $aliasPath ) )
        {
            $alias = $this->handleStoragePath( $aliasPath );
            if ( (int)$alias['status'] !== 200 || isset( $alias['headers']['X-Image-Placeholder'] ) || !self::exists( $aliasPath ) )
                return $alias;
        }
        $made = $this->variant( $aliasPath, $format, $aliasName, true );
        if ( $made !== null )
            return $this->deliver( $made, expImageFormatConverter::mimeType( $format ) );
        return $this->placeholder( null, null, 404, 'not-made', $aliasName, $path );
    }

    // ------------------------------------------------------------------------------------------------ forms

    /**
     * @param array $parsed
     * @return array
     */
    protected function handleSigned( array $parsed )
    {
        $check = expImageAliasUrl::verify( $parsed['token'] );
        $payload = $check['payload'];
        if ( $payload === null )
            return $this->placeholder( null, null, 404, 'malformed' );
        $width = isset( $payload['w'] ) ? $payload['w'] : null;
        $height = isset( $payload['h'] ) ? $payload['h'] : null;
        // The file name and, in the ids-token form, the ids of the URL must be those the token signs
        if ( $parsed['file'] !== (string)$payload['u']
             || ( $parsed['form'] === 'ids-token' && ( (int)$parsed['attribute'] !== (int)$payload['a'] || (int)$parsed['version'] !== (int)$payload['v']
                                                      || $parsed['language'] !== (string)$payload['l'] || $parsed['alias'] !== (string)$payload['n'] ) ) )
            return $this->placeholder( $width, $height, 404, 'mismatch' );

        $target = expImageAliasUrl::targetPath( $payload );
        // The payload of a token whose signature fails is anybody's: only a file of the image storage, and its type by
        // its name, never the m it brings
        if ( !expImageAliasUrl::validImagePath( $target ) )
            return $this->placeholder( $width, $height, 404, 'not-storage' );
        if ( $check['valid'] )
            $this->miss = array( 'attribute' => (int)$payload['a'], 'version' => (int)$payload['v'], 'language' => (string)$payload['l'] );
        $mime = $check['valid'] && isset( $payload['m'] ) && is_string( $payload['m'] ) ? $payload['m'] : null;
        // The formats the browser may get instead (Formats[] of the alias, Negotiate): the answer varies by Accept
        $format = $this->negotiateFor( (string)$payload['n'], $mime !== null ? $mime : eZMimeType::findByURL( $target )['name'] );
        // r: the file there was outdated when the page was made (other alias settings); a valid URL makes it again
        // unless it was made since
        $outdatedBefore = isset( $payload['r'] ) ? (int)$payload['r'] : 0;
        if ( self::exists( $target ) && ( !$check['valid'] || $outdatedBefore === 0 || self::modified( $target ) >= $outdatedBefore ) )
            return $this->deliverAlias( $target, $mime, $format, (string)$payload['n'], $check['valid'] );
        if ( !$check['valid'] )
            return $this->placeholder( $width, $height, 404, $check['reason'], (string)$payload['n'], $target );

        // The backlog with every slot taken (a page full of new images): the signed URL names everything the backlog
        // needs, so the answer goes out without a database query (readers wait while the aliases being made are
        // written); the image is checked when the alias is made. Not while staging, which fetches in the request.
        if ( $this->usesBacklog() && expImageAliasStager::forRequest() === null && expImageAliasThrottle::busy() >= expImageAliasThrottle::limit() )
        {
            expImageAliasLog::write( 'defer', array( 'result' => 'timeout', 'ms' => 0, 'alias' => (string)$payload['n'], 'path' => $target ) );
            $this->queueCompletion( (string)$payload['n'], $format !== null ? array( $format ) : array(), $target );
            return $this->placeholder( $width, $height, 200, 'pending', (string)$payload['n'], $target );
        }

        $attribute = eZContentObjectAttribute::fetch( (int)$payload['a'], (int)$payload['v'] );
        if ( !$attribute instanceof eZContentObjectAttribute || $attribute->attribute( 'language_code' ) !== (string)$payload['l']
             || $attribute->attribute( 'data_type_string' ) !== 'ezimage' )
            return $this->placeholder( $width, $height, 404, 'gone', (string)$payload['n'], $target );
        $made = $this->generate( $attribute, (string)$payload['n'], $target );
        if ( self::exists( $target ) && ( $made === 'ok' || $outdatedBefore === 0 || self::modified( $target ) >= $outdatedBefore ) )
            return $this->deliverAlias( $target, $mime, $format, (string)$payload['n'], true );
        // Not done in time (another process makes it, no conversion slot came free): the browser asks again (swap
        // script), and a job finishes it meanwhile
        if ( $made === 'timeout' )
        {
            $this->queueCompletion( (string)$payload['n'], $format !== null ? array( $format ) : array(), $target );
            return $this->placeholder( $width, $height, 200, 'pending', (string)$payload['n'], $target );
        }
        return $this->placeholder( $width, $height, 404, 'not-made', (string)$payload['n'], $target );
    }

    /**
     * The unsigned ids form: an alias of the published version that exists.
     *
     * @param array $parsed
     * @return array
     */
    protected function handleIds( array $parsed )
    {
        $attribute = $this->publishedImageAttribute( $parsed['attribute'], $parsed['version'], $parsed['language'] );
        if ( $attribute === null || (int)$attribute->attribute( 'version' ) !== (int)$parsed['version'] )
            return $this->placeholder( null, null, 404, 'unpublished' );
        return $this->existingAlias( $attribute, $parsed['alias'], $parsed['file'] );
    }

    /**
     * The unsigned node form: an alias of the image attribute of a visible node that exists.
     *
     * @param array $parsed
     * @return array
     */
    protected function handleNode( array $parsed )
    {
        $node = eZContentObjectTreeNode::fetch( (int)$parsed['node'] );
        if ( !$node instanceof eZContentObjectTreeNode || ( $node->attribute( 'is_invisible' ) && !eZContentObjectTreeNode::showInvisibleNodes() ) )
            return $this->placeholder( null, null, 404, 'unknown' );
        $object = $node->attribute( 'object' );
        if ( !$object instanceof eZContentObject || (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED )
            return $this->placeholder( null, null, 404, 'unpublished' );
        $dataMap = $object->dataMap();
        if ( !isset( $dataMap[$parsed['identifier']] ) || $dataMap[$parsed['identifier']]->attribute( 'data_type_string' ) !== 'ezimage' )
            return $this->placeholder( null, null, 404, 'unknown' );
        return $this->existingAlias( $dataMap[$parsed['identifier']], $parsed['alias'], null );
    }

    /**
     * Serves the alias $aliasName of $attribute when its file exists (and is named $file, when given).
     *
     * @param eZContentObjectAttribute $attribute
     * @param string $aliasName
     * @param string|null $file
     * @return array
     */
    protected function existingAlias( $attribute, $aliasName, $file )
    {
        $handler = $attribute->attribute( 'content' );
        if ( !$handler instanceof eZImageAliasHandler )
            return $this->placeholder( null, null, 404, 'unknown' );
        $aliasList = $handler->aliasList();
        if ( !isset( $aliasList[$aliasName] ) || (string)$aliasList[$aliasName]['url'] === ''
             || ( $file !== null && basename( (string)$aliasList[$aliasName]['url'] ) !== $file ) )
            return $this->placeholder( null, null, 404, 'unsigned-missing' );
        $path = (string)$aliasList[$aliasName]['url'];
        $mime = (string)$aliasList[$aliasName]['mime_type'];
        // Unsigned: a variant that exists is served, none is made
        $format = $this->negotiateFor( $aliasName, $mime );
        if ( !self::exists( $path ) )
            return $this->placeholder( $aliasList[$aliasName]['width'] ?? null, $aliasList[$aliasName]['height'] ?? null, 404, 'unsigned-missing' );
        return $this->deliverAlias( $path, $mime, $format, $aliasName, false );
    }

    /**
     * The attribute $attributeID in $language of the published version of its object, when its image lies in the
     * directory of version $version (an image shared between versions keeps the directory of the version it was
     * uploaded in); null otherwise.
     *
     * @param int $attributeID
     * @param int $version
     * @param string $language
     * @return eZContentObjectAttribute|null
     */
    public function publishedImageAttribute( $attributeID, $version, $language )
    {
        $named = eZContentObjectAttribute::fetch( (int)$attributeID, (int)$version );
        if ( !$named instanceof eZContentObjectAttribute || $named->attribute( 'language_code' ) !== $language
             || $named->attribute( 'data_type_string' ) !== 'ezimage' )
            return null;
        $object = eZContentObject::fetch( (int)$named->attribute( 'contentobject_id' ) );
        if ( !$object instanceof eZContentObject || (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED )
            return null;
        $currentVersion = (int)$object->attribute( 'current_version' );
        if ( $currentVersion === (int)$version )
            return $named;
        $published = eZContentObjectAttribute::fetch( (int)$attributeID, $currentVersion );
        return $published instanceof eZContentObjectAttribute && $published->attribute( 'language_code' ) === $language ? $published : null;
    }

    // ------------------------------------------------------------------------------------------------ making

    /**
     * Makes the alias $aliasName of $attribute as render mode does, waiting at most what is left of MaxWait (a job:
     * LockTimeout) for another process making it and for a conversion slot (expImageAliasThrottle).
     *
     * @param eZContentObjectAttribute $attribute
     * @param string $aliasName
     * @param string|null $target the file expected (staging, the log); null: not known (a job)
     * @return string ok|timeout|failed
     */
    protected function generate( $attribute, $aliasName, $target )
    {
        $handler = $attribute->attribute( 'content' );
        if ( !$handler instanceof eZImageAliasHandler || !eZImageManager::factory()->hasAlias( $aliasName ) )
            return 'failed';
        // Staging: the alias from the source installation, else its original (the alias is then made here)
        $adopt = false;
        $stager = $target === null || self::exists( $target ) ? null : expImageAliasStager::forRequest();
        if ( $stager !== null )
        {
            $aliasList = $handler->aliasList();
            $originalPath = isset( $aliasList['original']['url'] ) ? (string)$aliasList['original']['url'] : '';
            $staged = $stager->stageAlias( $target, $originalPath, $this->left() );
            if ( $staged === 'pending' )
                return 'timeout';
            if ( $staged === 'missing' )
                return 'failed';
            $adopt = $staged === 'alias';
        }
        $left = $this->left();
        // The backlog ([AliasSettings] Backlog): a request makes the alias only in a slot that is free now and waits
        // for no other process; otherwise it answers at once and the alias is made after the answer
        $backlog = $this->usesBacklog();
        $before = expImageAliasLock::setWaitLimit( $backlog ? 0.0 : $left );
        // A request starts no conversion after its MaxWait (a reference alias made on the way to this one included);
        // a conversion that has started is finished (it cannot be stopped half way without losing the work)
        $deadlineBefore = expImageAliasThrottle::setDeadline( $this->job ? null : $this->start + expImageAliasSettings::maxWait() );
        expImageAliasThrottle::forgetLastHold();
        $started = microtime( true );
        $timedOut = false;
        $lockTimeouts = expImageAliasLock::timeouts();
        $slot = false;
        try
        {
            if ( $backlog && !( $slot = expImageAliasThrottle::acquire( 0.0 ) ) )
                $alias = null;
            else
                $alias = expImageAliasSettings::withRender( function () use ( $handler, $aliasName, $adopt, $target ) {
                    // An entry whose file is gone is made again (the XML entry is replaced)
                    $handler->forgetMissingAliasFile( $aliasName );
                    // A file fetched from the source is recorded as render mode records an alias it made
                    if ( $adopt )
                        return expImageAliasStager::adopting( $aliasName, $target, function () use ( $handler, $aliasName ) {
                            return $handler->imageAlias( $aliasName );
                        } );
                    return $handler->imageAlias( $aliasName );
                } );
        }
        catch ( Throwable $e )
        {
            $alias = null;
            eZDebug::writeError( "Making the alias '$aliasName' of $target failed: " . $e->getMessage(), __METHOD__ );
        }
        finally
        {
            if ( $slot )
                expImageAliasThrottle::release();
            expImageAliasLock::setWaitLimit( $before );
            expImageAliasThrottle::setDeadline( $deadlineBefore );
        }
        $ms = (int)round( ( microtime( true ) - $started ) * 1000 );
        $ok = is_array( $alias ) && (string)( $alias['url'] ?? '' ) !== '';
        // After the answer this request makes what waits in the backlog, when it gets a free slot then
        if ( $backlog )
            expImageAliasBacklog::scheduleDrain();
        // No slot in time, or the wait for another process's lock ran out
        if ( !$ok && ( ( $backlog && !$slot ) || expImageAliasThrottle::timedOut() || expImageAliasLock::timeouts() > $lockTimeouts
                       || $ms >= (int)( $left * 1000 ) ) )
            $timedOut = true;
        $fields = array( 'result' => $ok ? 'ok' : ( $timedOut ? 'timeout' : 'failed' ), 'ms' => $ms, 'alias' => $aliasName );
        $fields += self::holdFields();
        if ( $this->job )
            $fields['by'] = 'job';
        $fields['path'] = $target !== null ? $target : ( $ok ? (string)$alias['url'] : '-' );
        // A timeout is logged as defer, not fail: the request answers the placeholder and a job makes the alias
        expImageAliasLog::write( expImageAliasLog::eventFor( $fields['result'] ), $fields );
        return $ok ? 'ok' : ( $timedOut ? 'timeout' : 'failed' );
    }

    /**
     * Whether this request leaves what it cannot make at once to the backlog (expImageAliasBacklog::applies()); never
     * for a job.
     *
     * @return bool
     */
    protected function usesBacklog()
    {
        if ( $this->job )
            return false;
        if ( $this->backlog === null )
            $this->backlog = expImageAliasBacklog::applies();
        return $this->backlog;
    }

    /**
     * The log fields of the conversion slot the last piece of work held: slot, held_at (start, seconds with
     * milliseconds), held_ms, slot_wait_ms. None when it took no slot (another process had made the file, render mode).
     *
     * @return array
     */
    protected static function holdFields()
    {
        $hold = expImageAliasThrottle::lastHold();
        if ( $hold === null )
            return array();
        $until = $hold['until'] !== null ? $hold['until'] : microtime( true );
        return array( 'slot' => (int)$hold['slot'], 'held_at' => sprintf( '%.3f', $hold['from'] ),
                      'held_ms' => (int)round( ( $until - $hold['from'] ) * 1000 ), 'slot_wait_ms' => (int)$hold['wait_ms'] );
    }

    // ------------------------------------------------------------------------------------------------ background work

    /**
     * Completes the alias $aliasName of $attribute and its variants in $formats (a queue job, the synchronous
     * fallback, a script): what is there and current is left alone; the rest is made exactly as a request makes it
     * (the same per-alias lock, shared by every web node on a cluster, and the same conversion slots), waiting as
     * long as LockTimeout. Use a server from forJob().
     *
     * @param eZContentObjectAttribute $attribute
     * @param string $aliasName
     * @param string[] $formats variants to make when the alias lists them (Formats[]) and a converter writes them
     * @return array alias => present|ok|timeout|failed|unknown|no-image, variants => format => present|ok|timeout|failed
     */
    public function complete( $attribute, $aliasName, array $formats = array() )
    {
        $out = array( 'alias' => 'failed', 'variants' => array() );
        $handler = $attribute->attribute( 'content' );
        if ( !$handler instanceof eZImageAliasHandler )
            return $out;
        if ( !eZImageManager::factory()->hasAlias( $aliasName ) )
        {
            $out['alias'] = 'unknown';
            return $out;
        }
        $aliasList = $handler->aliasList();
        if ( !isset( $aliasList['original']['url'] ) || (string)$aliasList['original']['url'] === '' )
        {
            $out['alias'] = 'no-image';
            return $out;
        }
        $entry = isset( $aliasList[$aliasName] ) ? $aliasList[$aliasName] : null;
        $current = $entry !== null && (string)( $entry['url'] ?? '' ) !== '' && !empty( $entry['is_valid'] ) && self::exists( (string)$entry['url'] )
                   && ( !isset( $entry['alias_key'] ) || (int)$entry['alias_key'] === (int)eZImageManager::factory()->createImageAliasKey( eZImageManager::factory()->alias( $aliasName ) ) );
        if ( $current )
            $out['alias'] = 'present';
        else
        {
            $out['alias'] = $this->generate( $attribute, $aliasName, null );
            $aliasList = $handler->aliasList();
            $entry = isset( $aliasList[$aliasName] ) ? $aliasList[$aliasName] : null;
        }
        if ( !in_array( $out['alias'], array( 'present', 'ok' ), true ) || $entry === null )
            return $out;
        $aliasPath = (string)$entry['url'];
        $aliasMime = isset( $entry['mime_type'] ) ? (string)$entry['mime_type'] : eZMimeType::findByURL( $aliasPath )['name'];
        foreach ( array_intersect( expImageAliasSettings::aliasFormats( $aliasName ), $formats ) as $format )
        {
            if ( !expImageFormatConverter::variantApplies( $aliasMime, $format ) || !expImageFormatConverter::canWrite( $format ) )
                continue;
            $path = expImageFormatConverter::variantPath( $aliasPath, $format );
            if ( self::exists( $path ) && self::modified( $path ) >= self::modified( $aliasPath ) )
            {
                $out['variants'][$format] = 'present';
                continue;
            }
            $out['variants'][$format] = $this->variant( $aliasPath, $format, $aliasName, true ) !== null ? 'ok' : ( expImageAliasThrottle::timedOut() ? 'timeout' : 'failed' );
        }
        return $out;
    }

    /**
     * A request that answers the placeholder because the work did not finish in time hands it to the job queue at
     * the visitors' priority (expImageAliasJob::queueMiss()). Not for a job, a draft, or an image this request does
     * not know.
     *
     * @param string $aliasName
     * @param string[] $formats variants to make as well
     * @param string|null $target the file expected
     */
    protected function queueCompletion( $aliasName, array $formats, $target )
    {
        if ( $this->job || $this->miss === null || ( $target !== null && expImageAliasUrl::isVersionedPath( $target ) ) )
            return;
        // The backlog: made by this server right after the answer, no job (a database write per image) needed
        if ( $this->usesBacklog() )
        {
            expImageAliasBacklog::add( $this->miss['attribute'], $this->miss['version'], $this->miss['language'], $aliasName, $formats, $target );
            expImageAliasBacklog::scheduleDrain();
            return;
        }
        $job =expImageAliasJob::queueMiss( $this->miss['attribute'], $this->miss['version'], $this->miss['language'], $aliasName, $formats, $target );
        if ( $job !== null )
            $this->queued[] = $job;
    }

    /**
     * Staging: a storage path that the stored image data of a published image names (its original, or an alias that
     * is not configured here any more), fetched as it is from the source installation within what is left of MaxWait.
     * Null for any other path, and for a configured alias, which generate() stages (the alias, else its original).
     *
     * @param expImageAliasStager $stager
     * @param array $parsed expImageAliasUrl::parseStoragePath() of $path
     * @param string $path
     * @return array|null the response
     */
    protected function stageOriginal( expImageAliasStager $stager, array $parsed, $path )
    {
        $attribute = $this->publishedImageAttribute( $parsed['attribute'], $parsed['version'], $parsed['language'] );
        if ( $attribute === null )
            return null;
        $name = array_search( $path, expImageAliasStager::filesOfXml( (string)$attribute->attribute( 'data_text' ) ), true );
        if ( $name === false || ( $name !== 'original' && eZImageManager::factory()->hasAlias( $name ) ) )
            return null;
        $staged = $stager->stageFile( $path, $this->left() );
        if ( $staged === 'ok' && self::exists( $path ) )
            return $this->deliver( $path, null );
        if ( $staged === 'pending' )
            return $this->placeholder( null, null, 200, 'staging', $name, $path );
        return $this->placeholder( null, null, 404, 'not-at-source', $name, $path );
    }

    // ------------------------------------------------------------------------------------------------ formats

    /**
     * The format this request gets the alias $aliasName in instead of its own, or null for its own. Sets $vary when
     * the alias has formats to negotiate at all (then every answer for its URL carries Vary: Accept).
     *
     * @param string $aliasName
     * @param string|null $aliasMime the MIME type of the alias file
     * @return string|null webp|avif
     */
    protected function negotiateFor( $aliasName, $aliasMime )
    {
        $formats = array();
        foreach ( expImageAliasSettings::negotiatedFormats( $aliasName ) as $format )
        {
            if ( expImageFormatConverter::variantApplies( (string)$aliasMime, $format ) )
                $formats[] = $format;
        }
        if ( !$formats )
            return null;
        $this->vary = true;
        return self::negotiate( $formats, $this->accept );
    }

    /**
     * The format of $formats the Accept header asks for most: only a type named explicitly counts (image/webp,
     * image/avif), never image/* or *\/*, which every browser sends, also those that cannot show the format. The
     * highest quality value wins; between equal ones the order of $formats (the alias block's Formats[]).
     *
     * @param string[] $formats
     * @param string $accept
     * @return string|null
     */
    public static function negotiate( array $formats, $accept )
    {
        $weights = array();
        foreach ( explode( ',', strtolower( (string)$accept ) ) as $range )
        {
            $parts = array_map( 'trim', explode( ';', $range ) );
            $type = array_shift( $parts );
            $q = 1.0;
            foreach ( $parts as $parameter )
            {
                if ( strpos( $parameter, 'q=' ) === 0 && is_numeric( substr( $parameter, 2 ) ) )
                    $q = (float)substr( $parameter, 2 );
            }
            if ( strpos( $type, 'image/' ) === 0 )
                $weights[substr( $type, 6 )] = max( $q, $weights[substr( $type, 6 )] ?? 0.0 );
        }
        $best = null;
        $bestWeight = 0.0;
        foreach ( $formats as $format )
        {
            $weight = $weights[$format] ?? 0.0;
            if ( $weight > $bestWeight )
            {
                $best = $format;
                $bestWeight = $weight;
            }
        }
        return $best;
    }

    /**
     * The answer with an existing alias, or with its variant in $format: the variant when it is there and not older
     * than the alias, else made now when $mayMake (a signed URL, a storage path) and some converter writes the format;
     * the alias itself otherwise.
     *
     * @param string $aliasPath
     * @param string|null $mime of the alias
     * @param string|null $format
     * @param string $aliasName
     * @param bool $mayMake
     * @return array
     */
    protected function deliverAlias( $aliasPath, $mime, $format, $aliasName, $mayMake )
    {
        if ( $format !== null )
        {
            $variant = $this->variant( $aliasPath, $format, $aliasName, $mayMake );
            if ( $variant !== null )
                return $this->deliver( $variant, expImageFormatConverter::mimeType( $format ) );
        }
        return $this->deliver( $aliasPath, $mime );
    }

    /**
     * The variant $format of an existing alias file: there and current, or made within what is left of MaxWait
     * (one lock per variant file, as for an alias).
     *
     * @param string $aliasPath
     * @param string $format
     * @param string $aliasName
     * @param bool $mayMake
     * @return string|null its storage path, null when it is not there
     */
    protected function variant( $aliasPath, $format, $aliasName, $mayMake )
    {
        $path = expImageFormatConverter::variantPath( $aliasPath, $format );
        if ( self::exists( $path ) && self::modified( $path ) >= self::modified( $aliasPath ) )
            return $path;
        if ( !$mayMake || !expImageFormatConverter::canWrite( $format ) )
            return null;
        $left = $this->left();
        // The backlog: no wait for a slot or another process's lock (see generate())
        $before = expImageAliasLock::setWaitLimit( $this->usesBacklog() ? 0.0 : $left );
        $deadlineBefore = expImageAliasThrottle::setDeadline( $this->job ? null : $this->start + expImageAliasSettings::maxWait() );
        expImageAliasThrottle::forgetLastHold();
        $started = microtime( true );
        $lock = new expImageAliasLock( $path );
        $local = null;
        $slot = false;
        $result = 'failed';
        try
        {
            if ( !$lock->acquire() )
                $result = 'timeout';
            elseif ( $lock->waited() && self::exists( $path ) && self::modified( $path ) >= self::modified( $aliasPath ) )
                $result = 'ok';
            elseif ( !( $slot = expImageAliasThrottle::acquire() ) )
                // No conversion slot in time (or MaxWait is used up): not made now
                $result = 'timeout';
            else
            {
                // On a database cluster the alias is fetched to a local file first, and the variant stored after
                $source = $aliasPath;
                if ( !is_file( $aliasPath ) )
                {
                    $local = eZClusterFileHandler::instance( $aliasPath )->fetchUnique();
                    $source = is_string( $local ) && $local !== '' ? $local : null;
                }
                if ( $source !== null && expImageFormatConverter::convert( $source, $path, $format,
                                                                         expImageAliasSettings::formatQuality( $aliasName, $format ) ) )
                {
                    eZClusterFileHandler::instance()->fileStore( $path, 'image', true, expImageFormatConverter::mimeType( $format ) );
                    $result = 'ok';
                }
            }
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( "Making the $format variant of $aliasPath failed: " . $e->getMessage(), __METHOD__ );
        }
        finally
        {
            if ( $slot )
                expImageAliasThrottle::release();
            $lock->release();
            expImageAliasLock::setWaitLimit( $before );
            expImageAliasThrottle::setDeadline( $deadlineBefore );
            if ( is_string( $local ) && $local !== '' )
                eZClusterFileHandler::instance( $aliasPath )->fileDeleteLocal( $local );
        }
        $fields = array( 'result' => $result, 'ms' => (int)round( ( microtime( true ) - $started ) * 1000 ), 'alias' => $aliasName . '.' . $format,
                         'converter' => (string)expImageFormatConverter::lastConverter() );
        $fields += $slot ? self::holdFields() : array();
        if ( $this->job )
            $fields['by'] = 'job';
        $fields['path'] = $path;
        expImageAliasLog::write( expImageAliasLog::eventFor( $result ), $fields );
        // The browser gets the alias in its own format now; a job makes the variant for the next visit
        if ( $result === 'timeout' )
            $this->queueCompletion( $aliasName, array( $format ), $aliasPath );
        return $result === 'ok' && self::exists( $path ) ? $path : null;
    }

    // ------------------------------------------------------------------------------------------------ answers

    /**
     * The answer with an existing alias file.
     *
     * @param string $path
     * @param string|null $mime
     * @return array
     */
    public function deliver( $path, $mime )
    {
        if ( $mime === null || $mime === '' )
        {
            $mimeData = eZMimeType::findByFileContents( $path );
            $mime = $mimeData['name'];
        }
        $cacheControl = 'public, max-age=' . expImageAliasSettings::cacheMaxAge();
        if ( !$this->storageRequest && $this->deliveryMethod( $path ) === 'redirect' )
        {
            $headers = array( 'Location' => rtrim( (string)eZSys::wwwDir(), '/' ) . '/' . self::encodePath( $path ),
                              'Cache-Control' => $cacheControl, 'Content-Length' => '0' );
            // The redirect differs by Accept; response caches keep no redirects (Velocity's keeps 200 and 404 only)
            if ( $this->vary )
                $headers['Vary'] = 'Accept';
            return array( 'status' => 302, 'body' => '', 'file' => null, 'headers' => $headers );
        }
        // A negotiated file under one URL: private, since a response cache that keys by URL alone (Velocity's
        // response cache does; it reads no Vary but Accept-Encoding) would hand one browser's format to the next
        if ( $this->vary )
            $cacheControl = 'private, max-age=' . expImageAliasSettings::cacheMaxAge();
        $headers = array( 'Content-Type' => $mime, 'Cache-Control' => $cacheControl . ', immutable',
                          'X-Content-Type-Options' => 'nosniff' );
        if ( $this->vary )
            $headers['Vary'] = 'Accept';
        if ( $mime === 'image/svg+xml' )
            $headers['Content-Security-Policy'] = "sandbox; default-src 'none'; style-src 'unsafe-inline'";
        if ( is_file( $path ) )
        {
            $size = filesize( $path );
            $mtime = filemtime( $path );
            $headers['Content-Length'] = (string)$size;
            $headers['Last-Modified'] = gmdate( 'D, d M Y H:i:s', (int)$mtime ) . ' GMT';
            return array( 'status' => 200, 'headers' => $headers, 'body' => null, 'file' => $path );
        }
        // A database cluster: the file from the cluster storage
        $fileHandler = eZClusterFileHandler::instance( $path );
        $contents = $fileHandler->fetchContents();
        if ( !is_string( $contents ) )
            return $this->placeholder( null, null, 404, 'unreadable', null, $path );
        $headers['Content-Length'] = (string)strlen( $contents );
        $mtime = $fileHandler->mtime();
        if ( $mtime )
            $headers['Last-Modified'] = gmdate( 'D, d M Y H:i:s', (int)$mtime ) . ' GMT';
        return array( 'status' => 200, 'headers' => $headers, 'body' => $contents, 'file' => null );
    }

    /**
     * How an existing alias is answered: redirect or stream ([AliasSettings] Delivery; auto: the redirect where the
     * web server serves the storage, the file itself on a database cluster and for drafts).
     *
     * @param string $path
     * @return string redirect|stream
     */
    public function deliveryMethod( $path )
    {
        $setting = expImageAliasSettings::delivery();
        if ( $setting !== 'auto' )
            return $setting;
        if ( expImageAliasUrl::isVersionedPath( $path ) )
            return 'stream';
        if ( interface_exists( 'ezpDatabaseBasedClusterFileHandler' )
             && eZClusterFileHandler::instance() instanceof ezpDatabaseBasedClusterFileHandler )
            return 'stream';
        return 'redirect';
    }

    /**
     * The placeholder answer (expImagePlaceholder), logged.
     *
     * @param mixed $width
     * @param mixed $height
     * @param int $status 200 while a signed image is being made, 404 otherwise
     * @param string $reason
     * @param string|null $alias
     * @param string|null $path
     * @return array
     */
    public function placeholder( $width, $height, $status, $reason, $alias = null, $path = null )
    {
        expImageAliasLog::write( 'placeholder', array( 'result' => $reason, 'status' => (int)$status, 'alias' => $alias === null ? '-' : $alias,
                                                       'path' => $path === null ? '-' : $path ) );
        if ( !expImageAliasSettings::placeholderEnabled() )
        {
            $headers = array( 'Cache-Control' => 'no-store, max-age=0', 'Content-Length' => '0', 'X-Image-Placeholder' => '1' );
        }
        else
        {
            $body = expImagePlaceholder::svg( $width, $height );
            $headers = expImagePlaceholder::headers( $body, $status === 200 && expImageAliasSettings::swapScriptEnabled() );
        }
        if ( $this->vary )
            $headers['Vary'] = 'Accept';
        return array( 'status' => (int)$status, 'body' => isset( $body ) ? $body : '', 'file' => null, 'headers' => $headers );
    }

    /**
     * Sends a response: status, headers, then the body or the file (nothing for HEAD). A conditional request for an
     * unchanged file is answered 304.
     *
     * @param array $response
     */
    public static function emit( array $response )
    {
        $status = (int)$response['status'];
        $headers = $response['headers'];
        if ( $status === 200 && isset( $headers['Last-Modified'] ) && isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] )
             && strtotime( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) >= strtotime( $headers['Last-Modified'] ) )
        {
            $status = 304;
            unset( $headers['Content-Length'], $headers['Content-Type'] );
            $response['body'] = '';
            $response['file'] = null;
        }
        if ( !headers_sent() )
        {
            while ( ob_get_level() > 0 && @ob_end_clean() );
            http_response_code( $status );
            header_remove( 'Set-Cookie' );
            header_remove( 'Pragma' );
            header_remove( 'Expires' );
            // Server-Timing is added to (other code, like the request shield, sends its own entries)
            foreach ( $headers as $name => $value )
                header( $name . ': ' . $value, $name !== 'Server-Timing' );
        }
        if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'HEAD' )
            return;
        if ( !empty( $response['file'] ) )
            readfile( $response['file'] );
        elseif ( isset( $response['body'] ) && $response['body'] !== '' )
            echo $response['body'];
    }

    // ------------------------------------------------------------------------------------------------ helpers

    /**
     * Whether a storage file exists (the cluster handler: a file on eZFS, the metadata on a database cluster).
     *
     * @param string $path
     * @return bool
     */
    public static function exists( $path )
    {
        if ( !expImageAliasUrl::validStoragePath( $path ) )
            return false;
        if ( is_file( $path ) )
            return true;
        if ( interface_exists( 'ezpDatabaseBasedClusterFileHandler' ) )
        {
            $handler = eZClusterFileHandler::instance( $path );
            if ( $handler instanceof ezpDatabaseBasedClusterFileHandler )
                return (bool)$handler->exists();
        }
        return false;
    }

    /**
     * When a storage file was last written (unix time; 0 when not known).
     *
     * @param string $path
     * @return int
     */
    public static function modified( $path )
    {
        if ( is_file( $path ) )
        {
            clearstatcache( true, $path );
            return (int)filemtime( $path );
        }
        $mtime = eZClusterFileHandler::instance( $path )->mtime();
        return is_numeric( $mtime ) ? (int)$mtime : 0;
    }

    /**
     * A request path as a storage path: no query string, no leading slash, the site's www directory and
     * "index_image.php/" (a server without rewrite rules) taken off, URL decoded.
     *
     * @param string $path
     * @return string
     */
    public static function normalizeRequestPath( $path )
    {
        $path = (string)$path;
        $query = strpos( $path, '?' );
        if ( $query !== false )
            $path = substr( $path, 0, $query );
        $path = rawurldecode( $path );
        $www = class_exists( 'eZSys' ) ? trim( (string)eZSys::wwwDir(), '/' ) : '';
        $path = ltrim( $path, '/' );
        if ( $www !== '' && strpos( $path, $www . '/' ) === 0 )
            $path = substr( $path, strlen( $www ) + 1 );
        if ( strpos( $path, 'index_image.php/' ) === 0 )
            $path = substr( $path, strlen( 'index_image.php/' ) );
        return $path;
    }

    /**
     * @param string $path
     * @return string the path with each segment URL encoded
     */
    protected static function encodePath( $path )
    {
        return implode( '/', array_map( 'rawurlencode', explode( '/', (string)$path ) ) );
    }

    // ------------------------------------------------------------------------------------------------ index_image.php

    /**
     * What index_image.php answers for the path elements left after the siteaccess was matched: content/image with
     * its view parameters (handleParams()), or a storage path (handleStoragePath()).
     *
     * content/image reaches this script where a web server sends it here (Velocity, FrankenPHP, the rules of
     * .htaccess_root): the same answer as the view, without the module, the template engine and the page kernel.
     * More parts than any URL form has are no form, as in the view.
     *
     * @param string[] $elements the path elements after the siteaccess
     * @return array array( 'params', string[] ) or array( 'storage', null )
     */
    public static function entryRoute( array $elements )
    {
        $elements = array_values( array_filter( array_map( 'strval', $elements ), 'strlen' ) );
        if ( count( $elements ) >= 2 && $elements[0] === 'content' && $elements[1] === 'image' )
        {
            $parts = array_slice( $elements, 2 );
            return array( 'params', count( $parts ) > 6 ? array() : $parts );
        }
        return array( 'storage', null );
    }

    /**
     * index_image.php: the light entry script for storage paths the web server did not find, and for content/image
     * where the web server routes it here. No session, no module, no template: the settings, the siteaccess (for its
     * image.ini and database), then handleStoragePath() or handleParams().
     *
     * Also run by a persistent worker (Velocity, FrankenPHP worker mode), between page requests of any siteaccess:
     * the kernel's per-request resets run first, as ezpKernelWeb runs them, and every setting is read for this
     * request's siteaccess. Also included by the cluster gateway (index_cluster.php) for an image the cluster does
     * not have yet (ezpClusterGateway::retrieve()).
     */
    public static function runEntryScript()
    {
        // The request scope: the query cache's memo, a transaction a request before left open, the reset hooks
        // (a persistent worker that served a page before)
        if ( !class_exists( 'expKernelServiceScope' ) )
            require_once __DIR__ . '/../expkernelservicescope.php';
        expKernelServiceScope::resetRequest();

        // eZUpdateDebugSettings(), which the siteaccess change calls; this script leaves eZDebug alone
        require_once __DIR__ . '/../../private/classes/debug_settings_functions.php';
        eZDebugSettingsMode( 'none' );
        eZExecution::addFatalErrorHandler( function () {
            if ( !headers_sent() )
                header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1' ) . ' 500 Internal Server Error' );
        } );
        eZDebug::setHandleType( eZDebug::HANDLE_FROM_PHP );

        // eZSys works with index.php while the request came for index_image.php (or index_cluster.php)
        foreach ( array( 'SCRIPT_FILENAME', 'PHP_SELF', 'SCRIPT_NAME' ) as $name )
        {
            if ( isset( $_SERVER[$name] ) )
                $_SERVER[$name] = str_replace( array( '/index_image.php', '/index_cluster.php' ), '/index.php', $_SERVER[$name] );
        }
        $ini = eZINI::instance();
        $timezone = $ini->variable( 'TimeZoneSettings', 'TimeZone' );
        if ( $timezone )
            putenv( "TZ=$timezone" );
        $requestURI = eZSys::serverVariable( 'REQUEST_URI' );
        $GLOBALS['eZGlobalRequestURI'] = $requestURI;
        eZSys::init( 'index.php', $ini->variable( 'SiteAccessSettings', 'ForceVirtualHost' ) === 'true' );
        unset( $GLOBALS['eZURIRequestInstance'] );
        $uri = eZURI::instance( eZSys::requestURI() );
        $GLOBALS['eZRequestedURI'] = $uri;
        eZExtension::activateExtensions( 'default' );
        // The host without the port, as ezpKernelWeb matches it (HostMatchMapItems name hosts, not ports: on
        // Velocity's :8080 the host carries the port)
        $access = eZSiteAccess::match( $uri, preg_replace( '/:\d+$/', '', (string)eZSys::hostname() ), eZSys::serverPort(), eZSys::indexFile() );
        eZSiteAccess::change( $access );
        eZExtension::activateExtensions( 'access' );
        // The settings of this siteaccess, not those a request before left in a persistent worker
        eZINI::resetAllInstances( false );

        $server = new self();
        list( $route, $parts ) = self::entryRoute( $uri->elements( false ) );
        if ( $route === 'params' )
            self::emit( $server->handleParams( $parts ) );
        else
            self::emit( $server->handleStoragePath( (string)$requestURI ) );

        eZExecution::cleanup();
        eZExecution::setCleanExit();
    }
}
