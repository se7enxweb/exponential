<?php
/**
 * The mutations of the GraphQL API without a database (doc/eng-us/features/6.0/graphql.md, "Mutations"): switched
 * off by default (no type Mutation, DISABLED); who may run one (POST only, signed in, the scope graphql:write of a
 * key, the form token of a session); the audit of each mutation and of each refusal (never a field value); the
 * response cache expired by a mutation that changed something, never by a refused or cancelled one; the error
 * codes FORBIDDEN and NOT_FOUND of the writer; multipart requests and the scalar Upload; the checks of FieldInput;
 * the checks of uploaded files; preview tokens; uploadFromUrl's host list.
 *
 * The kernel's write path (expGraphQLWriter) is replaced by a recording writer: what the kernel's operations do
 * with a database is tested by their own tests.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expGraphQLMutationsTestContent extends expGraphQLContent
{
    /** @var int */
    public $testId;

    /** @var string */
    public $testName;

    public function __construct( $id, $name )
    {
        parent::__construct( null, null, false );
        $this->testId = (int)$id;
        $this->testName = (string)$name;
    }

    public function id() { return $this->testId; }
    public function remoteId() { return 'remote' . $this->testId; }
    public function classIdentifier() { return 'article'; }
    public function className() { return 'Article'; }
    public function language() { return 'eng-GB'; }
    public function name() { return $this->testName; }
    public function dataMap() { return array(); }
    public function location() { return null; }
    public function mainLocation() { return null; }
    public function locations() { return array(); }
}

class expGraphQLMutationsTestWriter extends expGraphQLWriter
{
    /** @var array[] the calls: method and arguments */
    public $calls = array();

    /** @var expGraphQLError|null thrown by the next call */
    public $throw = null;

    /** @var string the status the next change answers */
    public $status = self::PUBLISHED;

    protected function answer( $method, array $arguments, $status = null )
    {
        $this->calls[] = array( $method, $arguments );
        if ( $this->throw !== null )
            throw $this->throw;
        return array( 'status' => $status !== null ? $status : $this->status, 'contentId' => 101, 'nodeId' => 11, 'version' => 2,
                      'content' => new expGraphQLMutationsTestContent( 101, 'Written' ) );
    }

    public function create( array $args ) { return $this->answer( 'create', array( $args ) ); }
    public function update( array $args, $translate = false ) { return $this->answer( $translate ? 'translate' : 'update', array( $args ) ); }
    public function publishDraft( $id, $versionNumber ) { return $this->answer( 'publishDraft', array( $id, $versionNumber ) ); }
    public function remove( array $args ) { return $this->answer( 'remove', array( $args ), self::TRASHED ); }
    public function move( $nodeId, $newParentId ) { return $this->answer( 'move', array( $nodeId, $newParentId ), self::DONE ); }
    public function visibility( $nodeId, $hide ) { return $this->answer( 'visibility', array( $nodeId, $hide ), self::DONE ); }
    public function addLocation( $id, $parentId ) { return $this->answer( 'addLocation', array( $id, $parentId ), self::DONE ); }

    public function previewTarget( $id, $versionNumber, $language )
    {
        $this->calls[] = array( 'previewTarget', array( $id, $versionNumber, $language ) );
        if ( $this->throw !== null )
            throw $this->throw;
        return array( 'objectId' => (int)$id, 'version' => $versionNumber === null ? 3 : (int)$versionNumber, 'language' => 'eng-GB' );
    }

    public function preview( array $token )
    {
        $this->calls[] = array( 'preview', array( $token ) );
        return new expGraphQLMutationsTestContent( $token['objectId'], 'Draft ' . $token['version'] );
    }
}

class expGraphQLMutationsTestCheck implements expGraphQLUploadCheck
{
    public function check( expGraphQLUpload $upload )
    {
        return strpos( $upload->name, 'virus' ) !== false ? 'The file contains a virus.' : null;
    }
}

class expGraphQLMutationsTest extends PHPUnit\Framework\TestCase
{
    /** @var array settings changed by a test: name => old value */
    protected $changed = array();

    /** @var array[] the audit records of a test: action, name, data */
    protected $audit = array();

    /** @var int how often the response cache was expired */
    protected $expired = 0;

    /** @var string[] files made by a test */
    protected $files = array();

    public static function setUpBeforeClass(): void
    {
        if ( !expGraphQL::loadLibrary() )
        {
            if ( getenv( 'CI' ) )
                self::fail( 'webonyx/graphql-php is not installed (composer.json requires it)' );
            self::markTestSkipped( 'webonyx/graphql-php is not installed; set GRAPHQL_LIBRARY_PATH' );
        }
    }

    protected function tearDown(): void
    {
        foreach ( $this->changed as $name => $value )
            expGraphQL::ini()->setVariable( 'GraphQLSettings', $name, $value );
        $this->changed = array();
        foreach ( $this->files as $file )
            @unlink( $file );
        $this->files = array();
    }

    protected function setting( $name, $value )
    {
        if ( !array_key_exists( $name, $this->changed ) )
            $this->changed[$name] = expGraphQL::setting( $name );
        expGraphQL::ini()->setVariable( 'GraphQLSettings', $name, $value );
    }

    /** @return array the fixture classes */
    protected static function classes()
    {
        $attribute = function ( $identifier, $datatype, $required = false ) {
            return array( 'id' => crc32( $identifier ), 'identifier' => $identifier, 'datatype' => $datatype, 'name' => ucfirst( $identifier ),
                          'description' => '', 'required' => $required, 'options' => array() );
        };
        return array(
            array( 'id' => 2, 'identifier' => 'article', 'name' => 'Article', 'description' => '',
                   'attributes' => array( $attribute( 'title', 'ezstring', true ), $attribute( 'intro', 'ezxmltext' ), $attribute( 'image', 'ezimage' ),
                                          $attribute( 'rating', 'ezinteger' ), $attribute( 'secret', 'ezstring' ), $attribute( 'account', 'ezuser' ) ) ),
            array( 'id' => 3, 'identifier' => 'image', 'name' => 'Image', 'description' => '',
                   'attributes' => array( $attribute( 'name', 'ezstring', true ), $attribute( 'image', 'ezimage', true ) ) ),
        );
    }

    protected static function catalogue()
    {
        return expGraphQLClassCatalogue::filtered( self::classes(), array( 'ClassList' => array(), 'ExcludedClassList' => array( 'user' ),
                                                                           'HiddenFieldList' => array( 'article/secret' ),
                                                                           'HiddenDatatypeList' => array( 'ezuser', 'ezpassword' ) ) );
    }

    protected static function builder()
    {
        return new expGraphQLSchemaBuilder( self::catalogue(), new expGraphQLNaming( 'identifier' ) );
    }

    /**
     * @param string $mode
     * @return expGraphQLContext with the recording writer, audit and cache expiry
     */
    protected function context( $mode = expGraphQLContext::AUTH_TOKEN, $method = 'POST' )
    {
        $catalogue = self::catalogue();
        $repository = new expGraphQLRepository( $catalogue, array( 'ClassList' => array(), 'ExcludedClassList' => array() ) );
        $context = new expGraphQLContext( null, $mode, $repository );
        $context->writer = new expGraphQLMutationsTestWriter( $repository, $catalogue, null );
        $context->httpMethod = $method;
        $test = $this;
        $context->auditor = function ( $action, $name, array $data, $id ) use ( $test ) {
            $test->recordAudit( $action, $name, $data );
            return $action === 'begin' ? 'audit-' . count( $test->auditRecords() ) : null;
        };
        $context->cacheExpirer = function () use ( $test ) { $test->recordExpiry(); };
        $context->previewTokens = new expGraphQLPreviewToken( 'test secret' );
        return $context;
    }

    public function recordAudit( $action, $name, array $data )
    {
        $this->audit[] = array( $action, $name, $data );
    }

    public function auditRecords()
    {
        return $this->audit;
    }

    public function recordExpiry()
    {
        $this->expired++;
    }

    protected function runQuery( $query, expGraphQLContext $context, ?array $variables = null )
    {
        return ( new expGraphQLServer( self::builder() ) )->execute( $query, $variables, null, $context );
    }

    const CREATE = 'mutation { createContent(parentId: 2, classIdentifier: "article", fields: [{identifier: "title", value: "Hello"}, {identifier: "intro", value: "Two\n\nparagraphs", format: TEXT}]) { status contentId nodeId version content { id name } } }';

    public function testSwitchedOnByDefault()
    {
        // owner decision 2026-10-30: the mutations come on together with the end point
        $this->assertMatchesRegularExpression( '/^MutationsEnabled=enabled$/m', file_get_contents( dirname( __DIR__, 5 ) . '/settings/graphql.ini' ), 'graphql.ini ships them on' );
        $this->assertMatchesRegularExpression( '/^AnonymousMutations=disabled$/m', file_get_contents( dirname( __DIR__, 5 ) . '/settings/graphql.ini' ), 'anonymous mutations stay off' );
        // the code's fallback agrees with the file: no MutationsEnabled at all is on as well
        $this->setting( 'MutationsEnabled', 'enabled' );
        expGraphQL::ini()->removeSetting( 'GraphQLSettings', 'MutationsEnabled' );
        $this->assertNull( expGraphQL::setting( 'MutationsEnabled' ) );
        $this->assertTrue( expGraphQLMutations::enabled() );
        $this->assertNotNull( self::builder()->schema()->getMutationType() );
    }

    public function testSwitchedOff()
    {
        $this->setting( 'MutationsEnabled', 'disabled' );
        $this->assertNull( self::builder()->schema()->getMutationType() );
        $off = self::builder()->schemaHash();
        $answer = $this->runQuery( self::CREATE, $this->context() );
        $this->assertSame( 'DISABLED', $answer['errors'][0]['extensions']['code'] );
        $this->assertArrayNotHasKey( 'data', $answer );

        $this->setting( 'MutationsEnabled', 'enabled' );
        $this->assertNotSame( $off, self::builder()->schemaHash(), 'the schema hash changes with the mutations' );
        $mutation = self::builder()->schema()->getMutationType();
        $this->assertNotNull( $mutation );
        $this->assertSame( array( 'createContent', 'updateContent', 'translateContent', 'publishDraft', 'deleteContent', 'moveContent', 'hideContent',
                                  'revealContent', 'addLocation', 'uploadImage', 'uploadFromUrl', 'createPreviewToken' ), array_keys( $mutation->getFields() ) );
        $sdl = self::builder()->sdl();
        foreach ( array( 'scalar Upload', 'input FieldInput', 'enum FieldFormat', 'enum MutationStatus', 'type ContentPayload', 'type PreviewToken' ) as $type )
            $this->assertStringContainsString( $type, $sdl );
    }

    public function testCreateRunsAuditsAndExpiresTheCache()
    {
        $this->setting( 'MutationsEnabled', 'enabled' );
        $context = $this->context();
        $answer = $this->runQuery( self::CREATE, $context );
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $this->assertSame( array( 'status' => 'PUBLISHED', 'contentId' => 101, 'nodeId' => 11, 'version' => 2, 'content' => array( 'id' => 101, 'name' => 'Written' ) ),
                           $answer['data']['createContent'] );
        $call = $context->writer->calls[0];
        $this->assertSame( 'create', $call[0] );
        $this->assertSame( 2, $call[1][0]['parentId'] );
        $this->assertTrue( $call[1][0]['publish'], 'publish is true unless the mutation says' );
        $this->assertSame( 'TEXT', $call[1][0]['fields'][1]['format'] );
        $this->assertSame( 1, $this->expired, 'a change expires the response cache' );
        $this->assertSame( 'mutation', $context->operationType );

        $this->assertCount( 2, $this->audit );
        $this->assertSame( array( 'begin', 'content.graphql.mutation' ), array_slice( $this->audit[0], 0, 2 ) );
        $begin = $this->audit[0][2];
        $this->assertSame( array( 'type' => 'node', 'id' => 2 ), $begin['object'] );
        $this->assertSame( 'createContent', $begin['after']['mutation'] );
        $this->assertSame( 'token', $begin['after']['signed_in_by'] );
        $this->assertSame( array( 'title', 'intro' ), $begin['after']['fields'] );
        $this->assertStringNotContainsString( 'Hello', json_encode( $this->audit ), 'the audit never records a field value' );
        $this->assertSame( 'end', $this->audit[1][0] );
        $this->assertSame( array( 'result' => 'success', 'after' => array( 'status' => 'PUBLISHED', 'contentId' => 101, 'nodeId' => 11, 'version' => 2 ) ),
                           $this->audit[1][2] );
    }

    public function testEveryMutationReachesTheWriter()
    {
        $this->setting( 'MutationsEnabled', 'enabled' );
        $context = $this->context();
        $answer = $this->runQuery( 'mutation {
            u: updateContent(id: 101, fields: [{identifier: "rating", value: "5"}], publish: false) { status }
            t: translateContent(id: 101, language: "ger-DE", fields: [{identifier: "title", value: "Hallo"}]) { status }
            p: publishDraft(id: 101, version: 3) { status }
            d: deleteContent(nodeId: 11) { status }
            m: moveContent(nodeId: 11, newParentId: 2) { status }
            h: hideContent(nodeId: 11) { status }
            r: revealContent(nodeId: 11) { status }
            a: addLocation(id: 101, parentId: 5) { status }
        }', $context );
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $this->assertSame( array( 'update', 'translate', 'publishDraft', 'remove', 'move', 'visibility', 'visibility', 'addLocation' ),
                           array_column( $context->writer->calls, 0 ) );
        $this->assertFalse( $context->writer->calls[0][1][0]['publish'] );
        $this->assertSame( array( 11, true ), $context->writer->calls[5][1] );
        $this->assertSame( array( 11, false ), $context->writer->calls[6][1] );
        $this->assertTrue( $context->writer->calls[3][1][0]['trash'], 'to the trash unless the mutation says' );
        $this->assertSame( 'TRASHED', $answer['data']['d']['status'] );
        $this->assertSame( 8, $this->expired );
    }

    public function testRefusals()
    {
        $this->setting( 'MutationsEnabled', 'enabled' );
        $cases = array(
            array( $this->context( expGraphQLContext::AUTH_TOKEN, 'GET' ), 'BAD_USER_INPUT', 'get' ),
            array( $this->context( expGraphQLContext::AUTH_ANONYMOUS ), 'FORBIDDEN', 'anonymous' ),
            array( $this->context( expGraphQLContext::AUTH_API_KEY ), 'FORBIDDEN', 'scope' ),
            array( $this->context( expGraphQLContext::AUTH_SESSION ), 'FORBIDDEN', 'form_token' ),
        );
        $cases[2][0]->apiKeyScopes = array( 'graphql:read' );
        foreach ( $cases as $case )
        {
            $this->audit = array();
            $answer = $this->runQuery( self::CREATE, $case[0] );
            $this->assertSame( $case[1], $answer['errors'][0]['extensions']['code'], $case[2] );
            $this->assertSame( array(), $case[0]->writer->calls, $case[2] . ': nothing written' );
            $this->assertSame( 'content.graphql.mutation.failed', $this->audit[0][1], $case[2] );
            $this->assertSame( 'refused', $this->audit[0][2]['result'] );
            $this->assertSame( $case[2], $this->audit[0][2]['reason'] );
        }
        $this->assertSame( 0, $this->expired );

        // allowed: anonymous where AnonymousMutations lets them through, a key with graphql:write, a session with its form token, the console
        $this->setting( 'AnonymousMutations', 'enabled' );
        $key = $this->context( expGraphQLContext::AUTH_API_KEY );
        $key->apiKeyScopes = array( 'graphql:read', 'graphql:write' );
        $session = $this->context( expGraphQLContext::AUTH_SESSION );
        $session->formTokenChecked = true;
        foreach ( array( $this->context( expGraphQLContext::AUTH_ANONYMOUS ), $key, $session, $this->context( expGraphQLContext::AUTH_CONSOLE, null ) ) as $context )
        {
            $answer = $this->runQuery( self::CREATE, $context );
            $this->assertArrayNotHasKey( 'errors', $answer, $context->authMode . ': ' . json_encode( $answer ) );
            $this->assertCount( 1, $context->writer->calls );
        }

        // a query is never refused for being one
        $this->setting( 'MutationsEnabled', 'disabled' );
        $answer = $this->runQuery( '{ __typename }', $this->context( expGraphQLContext::AUTH_ANONYMOUS, 'GET' ) );
        $this->assertArrayNotHasKey( 'errors', $answer );
    }

    public function testWriterErrorsKeepTheirCodes()
    {
        $this->setting( 'MutationsEnabled', 'enabled' );
        foreach ( array( expGraphQLWriter::forbidden(), expGraphQLWriter::notFound() ) as $error )
        {
            $this->audit = array();
            $context = $this->context();
            $context->writer->throw = $error;
            $answer = $this->runQuery( 'mutation { moveContent(nodeId: 11, newParentId: 99) { status } }', $context );
            $this->assertSame( $error->errorCode(), $answer['errors'][0]['extensions']['code'] );
            $this->assertSame( $error->getMessage(), $answer['errors'][0]['message'] );
            $this->assertSame( array( 'moveContent' ), $answer['errors'][0]['path'] );
            // begin, end (refused), and the failed event
            $this->assertSame( array( 'begin', 'end', 'event' ), array_column( $this->audit, 0 ) );
            $this->assertSame( 'refused', $this->audit[1][2]['result'] );
            $this->assertSame( strtolower( $error->errorCode() ), $this->audit[2][2]['reason'] );
            $this->assertSame( 'content.graphql.mutation.failed', $this->audit[2][1] );
        }
        $this->assertSame( 0, $this->expired, 'nothing changed: the cache stays' );

        $context = $this->context();
        $context->writer->status = expGraphQLWriter::CANCELLED;
        $answer = $this->runQuery( self::CREATE, $context );
        $this->assertSame( 'CANCELLED', $answer['data']['createContent']['status'] );
        $this->assertSame( 0, $this->expired, 'a workflow cancelled it: the cache stays' );
        $context->writer->status = expGraphQLWriter::PENDING;
        $answer = $this->runQuery( self::CREATE, $context );
        $this->assertSame( 'PENDING', $answer['data']['createContent']['status'] );
        $this->assertSame( 1, $this->expired );
    }

    public function testMultipartRequestAndUpload()
    {
        $this->setting( 'MutationsEnabled', 'enabled' );
        $file = tempnam( sys_get_temp_dir(), 'gqlup' );
        $this->files[] = $file;
        file_put_contents( $file, 'GIF89a' );
        $post = array( 'operations' => json_encode( array( 'query' => 'mutation ($f: Upload!) { uploadImage(parentId: 2, file: $f, alt: "A cat") { status } }',
                                                           'variables' => array( 'f' => null ) ) ),
                       'map' => json_encode( array( '0' => array( 'variables.f' ) ) ) );
        $files = array( '0' => array( 'name' => '../../cat picture.gif', 'type' => 'image/gif', 'tmp_name' => $file, 'size' => 6, 'error' => UPLOAD_ERR_OK ) );
        $request = expGraphQLServer::parseHttpRequest( 'POST', 'multipart/form-data; boundary=x', '', array(), $post, $files );
        $this->assertArrayNotHasKey( 'error', $request );
        $this->assertInstanceOf( 'expGraphQLUpload', $request['variables']['f'] );
        $this->assertSame( 'cat-picture.gif', $request['variables']['f']->safeName(), 'no directory, no space' );

        $context = $this->context();
        $answer = ( new expGraphQLServer( self::builder() ) )->run( $request, $context )['answer'];
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $args = $context->writer->calls[0][1][0];
        $this->assertSame( 'image', $args['classIdentifier'] );
        $this->assertSame( 'image', $args['fields'][0]['identifier'] );
        $this->assertSame( $request['variables']['f'], $args['fields'][0]['upload'] );
        $this->assertSame( 'A cat', $args['fields'][0]['value'] );
        $this->assertSame( array( 'identifier' => 'name', 'value' => 'cat-picture' ), $args['fields'][1] );

        // a file in a FieldInput, at a path of the variables
        $post['operations'] = json_encode( array( 'query' => 'mutation ($input: [FieldInput!]!) { updateContent(id: 101, fields: $input) { status } }',
                                                  'variables' => array( 'input' => array( array( 'identifier' => 'image', 'upload' => null ) ) ) ) );
        $post['map'] = json_encode( array( '0' => array( 'variables.input.0.upload' ) ) );
        $request = expGraphQLServer::parseHttpRequest( 'POST', 'multipart/form-data', '', array(), $post, $files );
        $context = $this->context();
        $answer = ( new expGraphQLServer( self::builder() ) )->run( $request, $context )['answer'];
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $this->assertInstanceOf( 'expGraphQLUpload', $context->writer->calls[0][1][0]['fields'][0]['upload'] );

        // what cannot be read
        $bad = array(
            array( 'operations' => '[{"query":"{a}"}]', 'map' => '{}' ),                       // a batch
            array( 'operations' => '{"query":"{a}"}', 'map' => '"x"' ),
            array( 'operations' => '{"query":"{a}"}', 'map' => '{"9":["variables.f"]}' ),       // no such file
            array( 'operations' => '{"query":"{a}"}', 'map' => '{"0":["query"]}' ),              // not below variables
        );
        foreach ( $bad as $post )
            $this->assertArrayHasKey( 'error', expGraphQLServer::parseHttpRequest( 'POST', 'multipart/form-data', '', array(), $post, $files ), $post['map'] );

        // Upload takes a file of the request, never a literal
        $answer = $this->runQuery( 'mutation { uploadImage(parentId: 2, file: "/etc/passwd") { status } }', $this->context() );
        $this->assertNotEmpty( $answer['errors'] );
        $answer = $this->runQuery( 'mutation ($f: Upload!) { uploadImage(parentId: 2, file: $f) { status } }', $this->context(), array( 'f' => '/etc/passwd' ) );
        $this->assertNotEmpty( $answer['errors'] );
    }

    public function testFieldInput()
    {
        $article = self::catalogue()->classByIdentifier( 'article' );
        $upload = new expGraphQLUpload( 'a.png', 'image/png', '/nowhere', 10 );
        $prepared = expGraphQLFieldInput::prepare( $article, array( array( 'identifier' => 'title', 'value' => 'T' ),
                                                                    array( 'identifier' => 'image', 'upload' => $upload, 'value' => 'Alt' ) ), true );
        $this->assertSame( array( 'title', 'image' ), expGraphQLFieldInput::auditList( $prepared ) );
        $this->assertSame( '/tmp/x/a.png|Alt', expGraphQLFieldInput::stringOf( $prepared['image'], '/tmp/x/a.png', 1 ) );
        $this->assertSame( 'T', expGraphQLFieldInput::stringOf( $prepared['title'], null, 1 ) );

        $refused = array(
            'unknown field' => array( array( 'identifier' => 'nope', 'value' => 'x' ) ),
            'hidden field' => array( array( 'identifier' => 'secret', 'value' => 'x' ) ),
            'user account' => array( array( 'identifier' => 'account', 'value' => 'admin|a@b.c|1234|md5_password|0' ) ),
            'a path for a file' => array( array( 'identifier' => 'image', 'value' => 'settings/override/site.ini.append.php' ) ),
            'a file for text' => array( array( 'identifier' => 'title', 'upload' => $upload ) ),
            'no value' => array( array( 'identifier' => 'title' ) ),
            'twice' => array( array( 'identifier' => 'title', 'value' => 'a' ), array( 'identifier' => 'title', 'value' => 'b' ) ),
            'HTML into an integer' => array( array( 'identifier' => 'rating', 'value' => '<b>1</b>', 'format' => 'HTML' ) ),
        );
        foreach ( $refused as $why => $fields )
        {
            try
            {
                expGraphQLFieldInput::prepare( $article, $fields, false );
                $this->fail( "$why is refused" );
            }
            catch ( expGraphQLError $e )
            {
                $this->assertSame( 'BAD_USER_INPUT', $e->errorCode(), $why );
            }
        }
        try
        {
            expGraphQLFieldInput::prepare( $article, array( array( 'identifier' => 'rating', 'value' => '1' ) ), true );
            $this->fail( 'a new object needs its required fields' );
        }
        catch ( expGraphQLError $e )
        {
            $this->assertStringContainsString( 'title', $e->getMessage() );
        }
        $this->assertCount( 1, expGraphQLFieldInput::prepare( $article, array( array( 'identifier' => 'rating', 'value' => '1' ) ), false ), 'an update needs no other field' );

        $xml = expGraphQLFieldInput::textToXml( "First <one> & more\nstill first\n\nSecond" );
        $this->assertStringStartsWith( '<?xml version="1.0" encoding="utf-8"?><section ', $xml );
        $this->assertStringContainsString( '<paragraph>First &lt;one&gt; &amp; more still first</paragraph><paragraph>Second</paragraph></section>', $xml );
        $this->assertNotFalse( simplexml_load_string( $xml ), 'well formed' );

        $this->setting( 'FileDatatypeList', array( 'ezstring' ) );
        $this->assertContains( 'ezstring', expGraphQLFieldInput::fileDatatypes() );
        try
        {
            expGraphQLFieldInput::prepare( $article, array( array( 'identifier' => 'title', 'value' => '/etc/passwd' ) ), false );
            $this->fail( 'FileDatatypeList[] makes a datatype a file field' );
        }
        catch ( expGraphQLError $e )
        {
            $this->assertSame( 'BAD_USER_INPUT', $e->errorCode() );
        }
    }

    public function testUploadChecks()
    {
        $file = tempnam( sys_get_temp_dir(), 'gqlup' );
        $this->files[] = $file;
        file_put_contents( $file, str_repeat( 'x', 2048 ) );
        $upload = new expGraphQLUpload( 'ok.txt', 'text/plain', $file, 2048 );
        $this->assertNull( $upload->problem( false ) );
        $this->assertNotNull( $upload->problem( true ), 'a file that did not come with this request is refused' );
        $this->setting( 'MaxUploadSize', '1k' );
        $this->assertSame( 1024, expGraphQLUpload::maxBytes() );
        $this->assertStringContainsString( '1024', (string)$upload->problem( false ) );
        $this->setting( 'MaxUploadSize', '32M' );
        $this->assertSame( 33554432, expGraphQLUpload::maxBytes() );
        $this->assertNotNull( ( new expGraphQLUpload( 'ok.txt', 'text/plain', $file, 2048, UPLOAD_ERR_PARTIAL ) )->problem( false ) );

        $this->setting( 'UploadCheckList', array( 'expGraphQLMutationsTestCheck' ) );
        $this->assertNull( $upload->problem( false ) );
        $this->assertSame( 'The file contains a virus.', ( new expGraphQLUpload( 'virus.exe', '', $file, 2048 ) )->problem( false ) );
        $this->setting( 'UploadCheckList', array( 'expGraphQLNoSuchUploadCheck' ) );
        $this->assertNotNull( $upload->problem( false ), 'a check that is missing refuses every file' );

        $this->assertSame( 'upload', ( new expGraphQLUpload( '...', '', $file, 1 ) )->safeName() );
        $this->assertSame( 'passwd', ( new expGraphQLUpload( '/etc/passwd', '', $file, 1 ) )->safeName() );
    }

    public function testUploadFromUrlHosts()
    {
        $this->assertStringContainsString( 'UploadFromUrlHostList', (string)expGraphQLMutations::urlProblem( 'https://images.example.com/a.jpg' ), 'off by default' );
        $this->setting( 'UploadFromUrlHostList', array( 'images.example.com' ) );
        $this->assertNull( expGraphQLMutations::urlProblem( 'https://images.example.com/a.jpg' ) );
        $this->assertNull( expGraphQLMutations::urlProblem( 'https://IMAGES.example.com/a.jpg' ) );
        foreach ( array( 'http://images.example.com/a.jpg', 'file:///etc/passwd', 'https://user:pw@images.example.com/a.jpg',
                         'https://evil.example.com/a.jpg', 'https://images.example.com.evil.net/a.jpg', 'not a url' ) as $url )
            $this->assertNotNull( expGraphQLMutations::urlProblem( $url ), $url );
    }

    public function testPreviewTokens()
    {
        $tokens = new expGraphQLPreviewToken( 'secret one' );
        $token = $tokens->issue( 58, 4, 'eng-GB', 14, 2000000000 );
        $this->assertStringStartsWith( 'exp1.', $token );
        $this->assertSame( array( 'objectId' => 58, 'version' => 4, 'language' => 'eng-GB', 'userId' => 14, 'expires' => 2000000000 ),
                           $tokens->verify( $token, 1900000000 ) );
        $this->assertNull( $tokens->verify( $token, 2000000001 ), 'expired' );
        $this->assertNull( ( new expGraphQLPreviewToken( 'secret two' ) )->verify( $token, 1900000000 ), 'another key' );
        $parts = explode( '.', $token );
        $forged = $parts[0] . '.' . rtrim( strtr( base64_encode( '{"o":1,"v":1,"l":"eng-GB","u":14,"e":2000000000}' ), '+/', '-_' ), '=' ) . '.' . $parts[2];
        $this->assertNull( $tokens->verify( $forged, 1900000000 ), 'a changed payload' );
        foreach ( array( '', 'exp1', 'exp2.' . $parts[1] . '.' . $parts[2], $token . '.x' ) as $bad )
            $this->assertNull( $tokens->verify( $bad, 1900000000 ), $bad );

        $this->assertSame( 3600, expGraphQLPreviewToken::lifetime() );
        $this->assertSame( 60, expGraphQLPreviewToken::lifetime( 5 ) );
        $this->assertSame( 86400, expGraphQLPreviewToken::lifetime( 10000000 ) );

        // made by the mutation, read by the query, also anonymously
        $this->setting( 'MutationsEnabled', 'enabled' );
        $context = $this->context();
        $answer = $this->runQuery( 'mutation { createPreviewToken(id: 58, version: 4, ttl: 600) { token expires contentId version language } }', $context );
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $made = $answer['data']['createPreviewToken'];
        $this->assertSame( array( 58, 4, 'eng-GB' ), array( $made['contentId'], $made['version'], $made['language'] ) );
        $this->assertSame( 0, $this->expired, 'a token changes no content' );
        $this->assertSame( 'createPreviewToken', $this->audit[0][2]['after']['mutation'], 'recorded: a token gives access' );

        $anonymous = $this->context( expGraphQLContext::AUTH_ANONYMOUS, 'GET' );
        $answer = $this->runQuery( '{ preview(token: "' . $made['token'] . '") { id name } }', $anonymous );
        $this->assertSame( array( 'id' => 58, 'name' => 'Draft 4' ), $answer['data']['preview'] );
        $answer = $this->runQuery( '{ preview(token: "exp1.x.y") { id } }', $anonymous );
        $this->assertSame( 'FORBIDDEN', $answer['errors'][0]['extensions']['code'] );
    }

    public function testMutationAnswersAreNeverCached()
    {
        $answer = array( 'data' => array( 'createContent' => array( 'status' => 'PUBLISHED' ) ) );
        // only query operations are kept (expGraphQLResponseCache::isQueryOperation())
        $this->assertFalse( expGraphQLResponseCache::storable( $answer, 'mutation { x }', false ) );
        $this->assertFalse( expGraphQLResponseCache::storable( $answer, "# a stored one\nmutation M { x }", false ) );
        $this->assertFalse( expGraphQLResponseCache::storable( $answer, "query Q { x }\nmutation M { x }", false, 'M' ) );
        $this->assertTrue( expGraphQLResponseCache::storable( $answer, "query Q { x }\nmutation M { x }", false, 'Q' ) );
        $this->assertTrue( expGraphQLResponseCache::storable( $answer, '{ x }', false ) );
    }

    public function testFormTokenOfASession()
    {
        $this->assertFalse( expGraphQLMutations::formTokenMatches( array(), array() ) );
        if ( !class_exists( 'ezxFormToken' ) )
            return;
        $token = ezxFormToken::getToken();
        $this->assertTrue( expGraphQLMutations::formTokenMatches( array( 'HTTP_X_CSRF_TOKEN' => $token ), array() ) );
        $this->assertTrue( expGraphQLMutations::formTokenMatches( array(), array( 'ezxform_token' => $token ) ) );
        $this->assertFalse( expGraphQLMutations::formTokenMatches( array( 'HTTP_X_CSRF_TOKEN' => $token . 'x' ), array() ) );
    }

    public function testAuditNamesAreInTheCatalogue()
    {
        $catalogue = expAuditTaxonomy::catalogue();
        foreach ( array( expGraphQLMutations::AUDIT, expGraphQLMutations::AUDIT_FAILED ) as $name )
        {
            $this->assertArrayHasKey( $name, $catalogue );
            $this->assertSame( 'on', $catalogue[$name]['default'] );
            $this->assertSame( 'content', $catalogue[$name]['channel'] );
        }
    }
}

?>
