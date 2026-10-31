<?php
/**
 * The mutations of the GraphQL API (doc/eng-us/features/6.0/graphql.md, "Mutations"): on together with the end point
 * (graphql.ini [GraphQLSettings] MutationsEnabled, enabled by default; disabled per siteaccess for a read-only API):
 *
 *   createContent(parentId, classIdentifier, fields, language, remoteId, publish)
 *   updateContent(id, fields, language, publish)          a new version, published or left a draft
 *   translateContent(id, language, fields, publish)       a new translation, copied from the main language
 *   publishDraft(id, version)
 *   deleteContent(id | nodeId, trash)                     to the trash by default
 *   moveContent(nodeId, newParentId)
 *   hideContent(nodeId), revealContent(nodeId)
 *   addLocation(id, parentId)
 *   uploadImage(parentId, file, name, alt, ...)           a file of a multipart request (Upload)
 *   uploadFromUrl(parentId, url, name, alt, ...)          a file fetched from a host of UploadFromUrlHostList[]
 *   createPreviewToken(id, version, language, ttl)        a short-lived token for preview(token:)
 *
 * Who may run one (refusal(), before the query runs): a POST (never a GET), signed in (AnonymousMutations), with a
 * personal API key only when it has the scope graphql:write, with a session only when the form token came along.
 * Then each mutation checks the user's policies on the content it changes (expGraphQLWriter), runs the kernel's
 * operations (workflows included), is recorded in the audit (content.graphql.mutation; a refusal or an error as
 * content.graphql.mutation.failed, with its reason), and expires the response cache when it changed something.
 * The answer (ContentPayload) has the status (PUBLISHED, DRAFT, PENDING, CANCELLED, DONE, TRASHED, DELETED) and
 * the content as the user may now read it.
 *
 * Loaded only after webonyx/graphql-php.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

use GraphQL\Type\Definition\CustomScalarType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class expGraphQLMutations
{
    /** The audit names. */
    const AUDIT = 'content.graphql.mutation';
    const AUDIT_FAILED = 'content.graphql.mutation.failed';

    /** The scope a personal API key needs for mutations (rest.ini [ApiKeySettings] Scopes[graphql:write]). */
    const WRITE_SCOPE = 'graphql:write';

    /**
     * @return bool mutations are switched on in the current siteaccess (MutationsEnabled=enabled)
     */
    public static function enabled()
    {
        return expGraphQL::setting( 'MutationsEnabled', 'enabled' ) === 'enabled';
    }

    /**
     * Why the request may not run a mutation, or null. Recorded in the audit when it refuses.
     *
     * @param expGraphQLContext $ctx
     * @return array|null array( message, code )
     */
    public static function refusal( expGraphQLContext $ctx )
    {
        $refusal = null;
        $reason = '';
        if ( !self::enabled() )
        {
            $refusal = array( expGraphQLMessage::tr( 'Mutations are switched off in this siteaccess (graphql.ini [GraphQLSettings] MutationsEnabled).' ), expGraphQLError::DISABLED );
            $reason = 'disabled';
        }
        else if ( $ctx->httpMethod === 'GET' )
        {
            $refusal = array( expGraphQLMessage::tr( 'Send a mutation with POST.' ), expGraphQLError::BAD_USER_INPUT );
            $reason = 'get';
        }
        else if ( !$ctx->isSignedIn() && expGraphQL::setting( 'AnonymousMutations', 'disabled' ) !== 'enabled' )
        {
            $refusal = array( expGraphQLMessage::tr( 'Sign in to change content.' ), expGraphQLError::FORBIDDEN );
            $reason = 'anonymous';
        }
        else if ( $ctx->authMode === expGraphQLContext::AUTH_API_KEY && !in_array( self::WRITE_SCOPE, (array)$ctx->apiKeyScopes, true ) )
        {
            $refusal = array( expGraphQLMessage::tr( 'The API key has no scope %scope.', array( '%scope' => self::WRITE_SCOPE ) ), expGraphQLError::FORBIDDEN );
            $reason = 'scope';
        }
        else if ( $ctx->authMode === expGraphQLContext::AUTH_SESSION && !$ctx->formTokenChecked )
        {
            $refusal = array( expGraphQLMessage::tr( 'A mutation with a session needs the form token of the session (header X-CSRF-Token).' ), expGraphQLError::FORBIDDEN );
            $reason = 'form_token';
        }
        if ( $refusal !== null && $reason !== 'disabled' )
            $ctx->audit( 'event', self::AUDIT_FAILED, array( 'object' => array( 'type' => 'graphql', 'id' => 'mutation' ), 'result' => 'refused',
                                                             'reason' => $reason, 'after' => self::auditExtra( $ctx, null ) ) );
        return $refusal;
    }

    /**
     * Whether a session request sent the form token of its session (ezformtoken; X-CSRF-Token or the form field).
     *
     * @param array $server $_SERVER
     * @param array $post $_POST
     * @return bool
     */
    public static function formTokenMatches( array $server, array $post )
    {
        if ( !class_exists( 'ezxFormToken' ) )
            return false;
        $token = isset( $server['HTTP_X_CSRF_TOKEN'] ) ? $server['HTTP_X_CSRF_TOKEN'] : ( isset( $post['ezxform_token'] ) ? $post['ezxform_token'] : null );
        $expected = (string)ezxFormToken::getToken();
        return is_string( $token ) && $token !== '' && $expected !== '' && hash_equals( $expected, $token );
    }

    // -------------------------------------------------------------------------------------------------- the schema

    /**
     * @param expGraphQLTypes $types
     * @return ObjectType the root type Mutation
     */
    public static function type( expGraphQLTypes $types )
    {
        return new ObjectType( array(
            'name' => 'Mutation',
            'description' => 'Changes to content, with the rights of the user who sends them; recorded in the audit.',
            'fields' => function () use ( $types ) { return expGraphQLMutations::fields( $types ); },
        ) );
    }

    /**
     * @param expGraphQLTypes $types
     * @return array the fields of Mutation
     */
    public static function fields( expGraphQLTypes $types )
    {
        // nullable: a mutation that fails answers null and an error, and the mutations before it in the request keep their answers
        $payload = self::payloadType( $types );
        $fields = Type::listOf( Type::nonNull( self::fieldInputType( $types ) ) );
        $language = array( 'type' => Type::string(), 'description' => 'The language (eng-GB); default: the main language of the content, or the first of the site.' );
        $publish = array( 'type' => Type::boolean(), 'defaultValue' => true, 'description' => 'false: leave the new version a draft (publish it with publishDraft).' );
        $imageArgs = array(
            'parentId' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'The node id of the parent location.' ),
            'name' => array( 'type' => Type::string(), 'description' => 'The name (default: the file name without its extension).' ),
            'alt' => array( 'type' => Type::string(), 'description' => 'The alternative text of the image.' ),
            'classIdentifier' => array( 'type' => Type::string(), 'defaultValue' => 'image', 'description' => 'The class of the new content.' ),
            'fieldIdentifier' => array( 'type' => Type::string(), 'defaultValue' => 'image', 'description' => 'The field that takes the file.' ),
            'nameIdentifier' => array( 'type' => Type::string(), 'defaultValue' => 'name', 'description' => 'The field that takes the name.' ),
            'language' => $language,
            'publish' => $publish,
        );
        return array(
            'createContent' => array(
                'type' => $payload,
                'description' => 'Creates content below a location (content/create there), and publishes it unless publish is false.',
                'args' => array( 'parentId' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'The node id of the parent location.' ),
                                 'classIdentifier' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'The identifier of the content class.' ),
                                 'fields' => array( 'type' => Type::nonNull( $fields ), 'description' => 'The fields, by attribute identifier.' ),
                                 'language' => $language,
                                 'remoteId' => array( 'type' => Type::string(), 'description' => 'The remote id of the new object (default: a new one).' ),
                                 'publish' => $publish ),
                'resolve' => function ( $root, $args, expGraphQLContext $ctx ) {
                    return expGraphQLMutations::run( 'createContent', $ctx, $args, array( 'type' => 'node', 'id' => (int)$args['parentId'] ),
                        function ( expGraphQLWriter $writer ) use ( $args ) { return $writer->create( $args ); } );
                },
            ),
            'updateContent' => array(
                'type' => $payload,
                'description' => 'Writes fields into a new version of content (content/edit), and publishes it unless publish is false.',
                'args' => array( 'id' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'The content object id.' ),
                                 'fields' => array( 'type' => Type::nonNull( $fields ), 'description' => 'The fields to change; the others keep their value.' ),
                                 'language' => $language, 'publish' => $publish ),
                'resolve' => function ( $root, $args, expGraphQLContext $ctx ) {
                    return expGraphQLMutations::run( 'updateContent', $ctx, $args, array( 'type' => 'object', 'id' => (int)$args['id'] ),
                        function ( expGraphQLWriter $writer ) use ( $args ) { return $writer->update( $args ); } );
                },
            ),
            'translateContent' => array(
                'type' => $payload,
                'description' => 'Adds a translation: a new version in the language, copied from the main language, with these fields.',
                'args' => array( 'id' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'The content object id.' ),
                                 'language' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'The new language (ger-DE).' ),
                                 'fields' => array( 'type' => $fields, 'description' => 'The translated fields; the others keep the main language\'s value.' ),
                                 'publish' => $publish ),
                'resolve' => function ( $root, $args, expGraphQLContext $ctx ) {
                    return expGraphQLMutations::run( 'translateContent', $ctx, $args, array( 'type' => 'object', 'id' => (int)$args['id'] ),
                        function ( expGraphQLWriter $writer ) use ( $args ) { return $writer->update( $args, true ); } );
                },
            ),
            'publishDraft' => array(
                'type' => $payload,
                'description' => 'Publishes a draft (one of the user\'s, or one the user may read and edit).',
                'args' => array( 'id' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'The content object id.' ),
                                 'version' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'The version number of the draft.' ) ),
                'resolve' => function ( $root, $args, expGraphQLContext $ctx ) {
                    return expGraphQLMutations::run( 'publishDraft', $ctx, $args, array( 'type' => 'object', 'id' => (int)$args['id'] ),
                        function ( expGraphQLWriter $writer ) use ( $args ) { return $writer->publishDraft( $args['id'], $args['version'] ); } );
                },
            ),
            'deleteContent' => array(
                'type' => $payload,
                'description' => 'Removes content (content/remove): one location (nodeId) or every location of an object (id); to the trash unless trash is false.',
                'args' => array( 'id' => array( 'type' => Type::int(), 'description' => 'The content object id: every location of it.' ),
                                 'nodeId' => array( 'type' => Type::int(), 'description' => 'One location.' ),
                                 'trash' => array( 'type' => Type::boolean(), 'defaultValue' => true, 'description' => 'false: remove for good.' ) ),
                'resolve' => function ( $root, $args, expGraphQLContext $ctx ) {
                    $object = isset( $args['nodeId'] ) ? array( 'type' => 'node', 'id' => (int)$args['nodeId'] ) : array( 'type' => 'object', 'id' => isset( $args['id'] ) ? (int)$args['id'] : 0 );
                    return expGraphQLMutations::run( 'deleteContent', $ctx, $args, $object,
                        function ( expGraphQLWriter $writer ) use ( $args ) { return $writer->remove( $args ); } );
                },
            ),
            'moveContent' => array(
                'type' => $payload,
                'description' => 'Moves a location below another (content/edit and content/remove here, content/create there).',
                'args' => array( 'nodeId' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'The location to move.' ),
                                 'newParentId' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'The node id of the new parent.' ) ),
                'resolve' => function ( $root, $args, expGraphQLContext $ctx ) {
                    return expGraphQLMutations::run( 'moveContent', $ctx, $args, array( 'type' => 'node', 'id' => (int)$args['nodeId'] ),
                        function ( expGraphQLWriter $writer ) use ( $args ) { return $writer->move( $args['nodeId'], $args['newParentId'] ); } );
                },
            ),
            'hideContent' => array(
                'type' => $payload,
                'description' => 'Hides a location and the content below it (content/hide).',
                'args' => array( 'nodeId' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'The location.' ) ),
                'resolve' => function ( $root, $args, expGraphQLContext $ctx ) {
                    return expGraphQLMutations::run( 'hideContent', $ctx, $args, array( 'type' => 'node', 'id' => (int)$args['nodeId'] ),
                        function ( expGraphQLWriter $writer ) use ( $args ) { return $writer->visibility( $args['nodeId'], true ); } );
                },
            ),
            'revealContent' => array(
                'type' => $payload,
                'description' => 'Reveals a hidden location (content/hide).',
                'args' => array( 'nodeId' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'The location.' ) ),
                'resolve' => function ( $root, $args, expGraphQLContext $ctx ) {
                    return expGraphQLMutations::run( 'revealContent', $ctx, $args, array( 'type' => 'node', 'id' => (int)$args['nodeId'] ),
                        function ( expGraphQLWriter $writer ) use ( $args ) { return $writer->visibility( $args['nodeId'], false ); } );
                },
            ),
            'addLocation' => array(
                'type' => $payload,
                'description' => 'Places content at one more location (content/edit of it, content/create below the parent).',
                'args' => array( 'id' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'The content object id.' ),
                                 'parentId' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'The node id of the new parent.' ) ),
                'resolve' => function ( $root, $args, expGraphQLContext $ctx ) {
                    return expGraphQLMutations::run( 'addLocation', $ctx, $args, array( 'type' => 'object', 'id' => (int)$args['id'] ),
                        function ( expGraphQLWriter $writer ) use ( $args ) { return $writer->addLocation( $args['id'], $args['parentId'] ); } );
                },
            ),
            'uploadImage' => array(
                'type' => $payload,
                'description' => 'Creates an image from a file sent with the request (a multipart request, the variable of type Upload).',
                'args' => array( 'file' => array( 'type' => Type::nonNull( self::uploadType( $types ) ), 'description' => 'The file.' ) ) + $imageArgs,
                'resolve' => function ( $root, $args, expGraphQLContext $ctx ) {
                    return expGraphQLMutations::run( 'uploadImage', $ctx, $args, array( 'type' => 'node', 'id' => (int)$args['parentId'] ),
                        function ( expGraphQLWriter $writer ) use ( $args ) { return $writer->create( expGraphQLMutations::imageArgs( $args, $args['file'] ) ); } );
                },
            ),
            'uploadFromUrl' => array(
                'type' => $payload,
                'description' => 'Creates an image (or a file) from an https address on a host of graphql.ini UploadFromUrlHostList[] (imports).',
                'args' => array( 'url' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'The https address of the file.' ) ) + $imageArgs,
                'resolve' => function ( $root, $args, expGraphQLContext $ctx ) {
                    return expGraphQLMutations::run( 'uploadFromUrl', $ctx, $args, array( 'type' => 'node', 'id' => (int)$args['parentId'] ),
                        function ( expGraphQLWriter $writer ) use ( $args ) {
                            $upload = expGraphQLMutations::fetchUrl( (string)$args['url'] );
                            try
                            {
                                return $writer->create( expGraphQLMutations::imageArgs( $args, $upload ) );
                            }
                            finally
                            {
                                @unlink( $upload->tmpName );
                            }
                        } );
                },
            ),
            'createPreviewToken' => array(
                'type' => self::previewTokenType( $types ),
                'description' => 'A short-lived token to read a version (a draft) with the query preview(token:), also without signing in.',
                'args' => array( 'id' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'The content object id.' ),
                                 'version' => array( 'type' => Type::int(), 'description' => 'The version (default: the newest draft of the user, else the published one).' ),
                                 'language' => array( 'type' => Type::string(), 'description' => 'The language to read.' ),
                                 'ttl' => array( 'type' => Type::int(), 'description' => 'Seconds the token is valid (graphql.ini PreviewTokenTtl, at most MaxPreviewTokenTtl).' ) ),
                'resolve' => function ( $root, $args, expGraphQLContext $ctx ) {
                    return expGraphQLMutations::run( 'createPreviewToken', $ctx, $args, array( 'type' => 'object', 'id' => (int)$args['id'] ),
                        function ( expGraphQLWriter $writer ) use ( $args, $ctx ) { return expGraphQLMutations::previewToken( $writer, $ctx, $args ); }, false );
                },
            ),
        );
    }

    /**
     * Runs one mutation: the change through the writer, the audit around it, the response cache expired after it.
     *
     * @param string $name the mutation
     * @param expGraphQLContext $ctx
     * @param array $args
     * @param array $object what it changes, for the audit (type, id)
     * @param callable $change function( expGraphQLWriter ): array the answer
     * @param bool $changes it changes content (the response cache is expired after it)
     * @return array
     */
    public static function run( $name, expGraphQLContext $ctx, array $args, array $object, callable $change, $changes = true )
    {
        $fields = array();
        foreach ( isset( $args['fields'] ) && is_array( $args['fields'] ) ? $args['fields'] : array() as $field )
        {
            if ( isset( $field['identifier'] ) )
                $fields[] = (string)$field['identifier'];
        }
        $id = $ctx->audit( 'begin', self::AUDIT, array( 'object' => $object, 'after' => self::auditExtra( $ctx, $name ) + ( $fields ? array( 'fields' => $fields ) : array() ) ) );
        try
        {
            $answer = class_exists( 'expAudit' ) && $ctx->auditor === null ? expAudit::withParent( $id, function () use ( $ctx, $change ) { return $change( $ctx->writer() ); } )
                                                                            : $change( $ctx->writer() );
        }
        catch ( Exception $e )
        {
            $code = $e instanceof expGraphQLError ? $e->errorCode() : expGraphQLError::INTERNAL;
            $failure = array( 'result' => $code === expGraphQLError::FORBIDDEN || $code === expGraphQLError::NOT_FOUND ? 'refused' : 'failed', 'reason' => strtolower( $code ) );
            if ( $id !== null )
                $ctx->audit( 'end', self::AUDIT, $failure, $id );
            $ctx->audit( 'event', self::AUDIT_FAILED, array( 'object' => $object, 'after' => self::auditExtra( $ctx, $name ) ) + $failure );
            throw $e;
        }
        if ( $changes && ( !isset( $answer['status'] ) || $answer['status'] !== expGraphQLWriter::CANCELLED ) )
            $ctx->expireCache();
        $after = array();
        foreach ( array( 'status', 'contentId', 'nodeId', 'version' ) as $key )
        {
            if ( isset( $answer[$key] ) )
                $after[$key] = $answer[$key];
        }
        if ( $id !== null )
            $ctx->audit( 'end', self::AUDIT, array( 'result' => 'success', 'after' => $after ), $id );
        // the GraphQL event journal (webhooks and subscriptions with the event mutation)
        if ( $changes && $name !== null && class_exists( 'expGraphQLEventListener' ) && ( !isset( $answer['status'] ) || $answer['status'] !== expGraphQLWriter::CANCELLED ) )
            expGraphQLEventListener::mutation( $name, $after );
        return $answer;
    }

    /**
     * @param expGraphQLContext $ctx
     * @param string|null $name
     * @return array what the audit records of the request: the mutation, how it was signed in, the request id, the key
     */
    protected static function auditExtra( expGraphQLContext $ctx, $name )
    {
        $extra = array( 'signed_in_by' => $ctx->authMode, 'request_id' => $ctx->requestId );
        if ( $name !== null )
            $extra = array( 'mutation' => $name ) + $extra;
        if ( $ctx->apiKeyId )
            $extra['api_key'] = (int)$ctx->apiKeyId;
        return $extra;
    }

    /**
     * The arguments of createContent for uploadImage and uploadFromUrl.
     *
     * @param array $args
     * @param expGraphQLUpload $upload
     * @return array
     */
    public static function imageArgs( array $args, expGraphQLUpload $upload )
    {
        $name = isset( $args['name'] ) && trim( (string)$args['name'] ) !== '' ? (string)$args['name'] : preg_replace( '/\.[^.]*$/', '', $upload->safeName() );
        $fields = array( array( 'identifier' => isset( $args['fieldIdentifier'] ) ? (string)$args['fieldIdentifier'] : 'image', 'upload' => $upload,
                                'value' => isset( $args['alt'] ) ? (string)$args['alt'] : null ) );
        $nameField = isset( $args['nameIdentifier'] ) ? (string)$args['nameIdentifier'] : 'name';
        if ( $nameField !== '' )
            $fields[] = array( 'identifier' => $nameField, 'value' => $name );
        return array( 'parentId' => $args['parentId'], 'classIdentifier' => isset( $args['classIdentifier'] ) ? (string)$args['classIdentifier'] : 'image',
                      'fields' => $fields, 'language' => isset( $args['language'] ) ? $args['language'] : null,
                      'publish' => !isset( $args['publish'] ) || $args['publish'] );
    }

    /**
     * Checks an address for uploadFromUrl: https, a host of UploadFromUrlHostList[] (empty: none at all).
     *
     * @param string $url
     * @return string|null why it may not be fetched, or null
     */
    public static function urlProblem( $url )
    {
        $hosts = array_map( 'strtolower', expGraphQL::listSetting( 'UploadFromUrlHostList' ) );
        if ( !$hosts )
            return expGraphQLMessage::tr( 'uploadFromUrl is switched off: graphql.ini [GraphQLSettings] UploadFromUrlHostList[] names no host.' );
        $parts = parse_url( (string)$url );
        if ( !is_array( $parts ) || !isset( $parts['scheme'], $parts['host'] ) || strtolower( $parts['scheme'] ) !== 'https' || isset( $parts['user'] ) || isset( $parts['pass'] ) )
            return expGraphQLMessage::tr( 'The address must be an https address.' );
        if ( !in_array( strtolower( $parts['host'] ), $hosts, true ) )
            return expGraphQLMessage::tr( 'The host %host is not in graphql.ini [GraphQLSettings] UploadFromUrlHostList[].', array( '%host' => strtolower( $parts['host'] ) ) );
        return null;
    }

    /**
     * Fetches a file for uploadFromUrl into the cache directory (no redirects, at most MaxUploadSize).
     *
     * @param string $url
     * @return expGraphQLUpload
     * @throws expGraphQLError BAD_USER_INPUT
     */
    public static function fetchUrl( $url )
    {
        $problem = self::urlProblem( $url );
        if ( $problem !== null )
            throw new expGraphQLError( $problem, expGraphQLError::BAD_USER_INPUT );
        if ( !function_exists( 'curl_init' ) )
            throw new expGraphQLError( expGraphQLMessage::tr( 'The file could not be fetched.' ), expGraphQLError::BAD_USER_INPUT );
        $dir = eZSys::cacheDirectory() . '/graphql/uploads';
        if ( !expGraphQLResponseCache::makeDirectory( $dir ) )
            throw new expGraphQLError( expGraphQLMessage::tr( 'Internal server error' ), expGraphQLError::INTERNAL );
        $file = $dir . '/fetch-' . bin2hex( random_bytes( 8 ) );
        $handle = @fopen( $file, 'wb' );
        if ( $handle === false )
            throw new expGraphQLError( expGraphQLMessage::tr( 'Internal server error' ), expGraphQLError::INTERNAL );
        $max = expGraphQLUpload::maxBytes();
        $curl = curl_init( (string)$url );
        curl_setopt_array( $curl, array( CURLOPT_FILE => $handle, CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => 30, CURLOPT_CONNECTTIMEOUT => 10,
                                         CURLOPT_PROTOCOLS => CURLPROTO_HTTPS, CURLOPT_FAILONERROR => true, CURLOPT_MAXFILESIZE => $max,
                                         CURLOPT_NOPROGRESS => false,
                                         CURLOPT_PROGRESSFUNCTION => function ( $c, $total, $done ) use ( $max ) { return $done > $max ? 1 : 0; } ) );
        $ok = curl_exec( $curl );
        $type = (string)curl_getinfo( $curl, CURLINFO_CONTENT_TYPE );
        fclose( $handle );
        if ( $ok !== true )
        {
            @unlink( $file );
            throw new expGraphQLError( expGraphQLMessage::tr( 'The file could not be fetched.' ), expGraphQLError::BAD_USER_INPUT );
        }
        @chmod( $file, eZFile::fileMode( 0666 ) );
        $path = (string)parse_url( (string)$url, PHP_URL_PATH );
        $upload = new expGraphQLUpload( basename( $path ) !== '' ? basename( $path ) : 'download', $type, $file, (int)filesize( $file ) );
        $upload->fetched = true;
        return $upload;
    }

    /**
     * The answer of createPreviewToken.
     *
     * @param expGraphQLWriter $writer
     * @param expGraphQLContext $ctx
     * @param array $args
     * @return array token, expires, contentId, version, language
     */
    public static function previewToken( expGraphQLWriter $writer, expGraphQLContext $ctx, array $args )
    {
        $target = $writer->previewTarget( $args['id'], isset( $args['version'] ) ? (int)$args['version'] : null,
                                          isset( $args['language'] ) ? (string)$args['language'] : null );
        $tokens = $ctx->previewTokens();
        if ( $tokens === null )
            throw new expGraphQLError( expGraphQLMessage::tr( 'Internal server error' ), expGraphQLError::INTERNAL );
        $expires = time() + expGraphQLPreviewToken::lifetime( isset( $args['ttl'] ) ? (int)$args['ttl'] : null );
        $userId = $ctx->user instanceof eZUser ? (int)$ctx->user->attribute( 'contentobject_id' ) : 0;
        return array( 'token' => $tokens->issue( $target['objectId'], $target['version'], $target['language'], $userId, $expires ),
                      'expires' => expGraphQLTypes::isoDate( $expires ), 'contentId' => $target['objectId'], 'version' => $target['version'],
                      'language' => $target['language'], 'status' => expGraphQLWriter::DONE );
    }

    // --------------------------------------------------------------------------------------------------- the types

    /**
     * @param expGraphQLTypes $types
     * @return CustomScalarType the scalar Upload: a file of a multipart request
     */
    public static function uploadType( expGraphQLTypes $types )
    {
        return $types->register( 'Upload', function () {
            return new CustomScalarType( array(
                'name' => 'Upload',
                'description' => 'A file sent with the request by the GraphQL multipart request specification (only as a variable).',
                'serialize' => function ( $value ) {
                    throw new expGraphQLError( expGraphQLMessage::tr( 'Upload is an input type only.' ), expGraphQLError::BAD_USER_INPUT );
                },
                'parseValue' => function ( $value ) {
                    if ( !$value instanceof expGraphQLUpload )
                        throw new expGraphQLError( expGraphQLMessage::tr( 'A variable of type Upload takes a file of a multipart request.' ), expGraphQLError::BAD_USER_INPUT );
                    return $value;
                },
                'parseLiteral' => function ( $node, ?array $variables = null ) {
                    throw new expGraphQLError( expGraphQLMessage::tr( 'A variable of type Upload takes a file of a multipart request.' ), expGraphQLError::BAD_USER_INPUT );
                },
            ) );
        } );
    }

    /**
     * @param expGraphQLTypes $types
     * @return InputObjectType
     */
    public static function fieldInputType( expGraphQLTypes $types )
    {
        return $types->register( 'FieldInput', function () use ( $types ) {
            return new InputObjectType( array(
                'name' => 'FieldInput',
                'description' => 'A field to write: its attribute identifier and its value in the string format of its datatype, or a file (upload).',
                'fields' => array(
                    'identifier' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'The attribute identifier (title, image).' ),
                    'value' => array( 'type' => Type::string(), 'description' => 'The value, as the datatype reads it from a string (the format of the CSV import); for a file field the alternative text or file name.' ),
                    'format' => array( 'type' => expGraphQLMutations::fieldFormatType( $types ), 'defaultValue' => expGraphQLFieldInput::FORMAT_RAW,
                                       'description' => 'Rich text only: TEXT (plain text, a paragraph per block) or HTML (simple HTML); RAW: the value as it is.' ),
                    'upload' => array( 'type' => expGraphQLMutations::uploadType( $types ), 'description' => 'A file of the request, for a file field (image, file, media).' ),
                ),
            ) );
        } );
    }

    /**
     * @param expGraphQLTypes $types
     * @return EnumType
     */
    public static function fieldFormatType( expGraphQLTypes $types )
    {
        return $types->register( 'FieldFormat', function () {
            return new EnumType( array( 'name' => 'FieldFormat', 'description' => 'How the value of a FieldInput is written.',
                                        'values' => array( 'RAW' => array( 'value' => expGraphQLFieldInput::FORMAT_RAW, 'description' => 'As the datatype reads it.' ),
                                                           'TEXT' => array( 'value' => expGraphQLFieldInput::FORMAT_TEXT, 'description' => 'Plain text into rich text.' ),
                                                           'HTML' => array( 'value' => expGraphQLFieldInput::FORMAT_HTML, 'description' => 'Simple HTML into rich text.' ) ) ) );
        } );
    }

    /**
     * @param expGraphQLTypes $types
     * @return EnumType
     */
    public static function statusType( expGraphQLTypes $types )
    {
        return $types->register( 'MutationStatus', function () {
            return new EnumType( array( 'name' => 'MutationStatus', 'description' => 'What a mutation did.',
                'values' => array(
                    'PUBLISHED' => array( 'value' => expGraphQLWriter::PUBLISHED, 'description' => 'The version is published.' ),
                    'DRAFT' => array( 'value' => expGraphQLWriter::DRAFT, 'description' => 'The version is a draft (publish: false).' ),
                    'PENDING' => array( 'value' => expGraphQLWriter::PENDING, 'description' => 'A workflow holds it (an approval); it goes on when the workflow does.' ),
                    'CANCELLED' => array( 'value' => expGraphQLWriter::CANCELLED, 'description' => 'A workflow cancelled it; nothing changed.' ),
                    'DONE' => array( 'value' => expGraphQLWriter::DONE, 'description' => 'Done (moved, hidden, revealed, a location added, a token made).' ),
                    'TRASHED' => array( 'value' => expGraphQLWriter::TRASHED, 'description' => 'Moved to the trash.' ),
                    'DELETED' => array( 'value' => expGraphQLWriter::DELETED, 'description' => 'Removed for good.' ),
                ) ) );
        } );
    }

    /**
     * @param expGraphQLTypes $types
     * @return ObjectType
     */
    public static function payloadType( expGraphQLTypes $types )
    {
        return $types->register( 'ContentPayload', function () use ( $types ) {
            return new ObjectType( array(
                'name' => 'ContentPayload',
                'description' => 'The answer of a mutation: what it did and the content as the user may now read it.',
                'fields' => function () use ( $types ) {
                    return array(
                        'status' => array( 'type' => Type::nonNull( expGraphQLMutations::statusType( $types ) ),
                                           'resolve' => function ( array $a ) { return $a['status']; } ),
                        'contentId' => array( 'type' => Type::int(), 'description' => 'The content object id.',
                                              'resolve' => function ( array $a ) { return isset( $a['contentId'] ) ? $a['contentId'] : null; } ),
                        'nodeId' => array( 'type' => Type::int(), 'description' => 'The location changed or made (the main location of new content).',
                                           'resolve' => function ( array $a ) { return isset( $a['nodeId'] ) ? $a['nodeId'] : null; } ),
                        'version' => array( 'type' => Type::int(), 'description' => 'The version written.',
                                            'resolve' => function ( array $a ) { return isset( $a['version'] ) ? $a['version'] : null; } ),
                        'content' => array( 'type' => $types->content(), 'description' => 'The content now (a draft: that version); null when it is gone or held by a workflow.',
                                            'resolve' => function ( array $a ) { return isset( $a['content'] ) ? $a['content'] : null; } ),
                    );
                },
            ) );
        } );
    }

    /**
     * @param expGraphQLTypes $types
     * @return ObjectType
     */
    public static function previewTokenType( expGraphQLTypes $types )
    {
        return $types->register( 'PreviewToken', function () {
            return new ObjectType( array(
                'name' => 'PreviewToken',
                'description' => 'A token to read one version with preview(token:) until it expires.',
                'fields' => array(
                    'token' => array( 'type' => Type::nonNull( Type::string() ), 'resolve' => function ( array $a ) { return $a['token']; } ),
                    'expires' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'When it expires, ISO 8601.',
                                        'resolve' => function ( array $a ) { return $a['expires']; } ),
                    'contentId' => array( 'type' => Type::nonNull( Type::int() ), 'resolve' => function ( array $a ) { return $a['contentId']; } ),
                    'version' => array( 'type' => Type::nonNull( Type::int() ), 'resolve' => function ( array $a ) { return $a['version']; } ),
                    'language' => array( 'type' => Type::nonNull( Type::string() ), 'resolve' => function ( array $a ) { return $a['language']; } ),
                ),
            ) );
        } );
    }
}

?>
