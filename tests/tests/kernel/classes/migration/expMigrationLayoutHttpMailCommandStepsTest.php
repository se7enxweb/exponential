<?php
/**
 * The step types layout, layout_block, layout_rule, http, mail and command without a database: their schemas, the
 * checks of their steps, their plans, which undo themselves, the caches they declare, and the http, mail and command
 * steps run against a transport, a mail folder and a process runner of the test (nothing leaves the machine).
 *
 *  ML-01 the kernel registers the six types from migration.ini; their schemas are valid, their examples valid steps;
 *        the layout types say which of their steps undo themselves, http, mail and command never do
 *  ML-02 layout: identifier, something to update, zones as {} or { link: "<layout>/<zone>" } or { link: none }
 *  ML-03 layout_block: layout, zone and name, a definition to create, position, parameter names
 *  ML-04 layout_rule: targets of the resolver's types (a node by any identifier), which: all, something to update
 *  ML-05 http: http(s) URLs without a user, body or json, no body on GET, headers on one line, secrets by name, expect
 *  ML-06 mail: addresses, one line of subject, a transport name, a known mail category
 *  ML-07 command: only exp:* of the allowlist, never exp:migration:* or exp:upgrade*, groups with *, arguments
 *  ML-08 plans name what would happen and that a dry run sends and runs nothing
 *  ML-09 http sends the headers, the bearer and the signature from secrets, a JSON body, and keeps the answer; an
 *        answer it does not expect fails the step, and the URL it shows has no query
 *  ML-10 command hands the command and its arguments to the process as a list, adds nothing a shell would read, and
 *        fails on an exit code it does not expect; a command off the list never runs
 *  ML-11 mail goes through the file transport into a folder of the test, with its mail category (admin by default)
 *  ML-12 the caches: layout steps content and template-block; http, mail and command none
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationLayoutHttpMailCommandStepsTest extends PHPUnit\Framework\TestCase
{
    const TYPES = array( 'layout' => 'expMigrationStepLayout', 'layout_block' => 'expMigrationStepLayoutBlock', 'layout_rule' => 'expMigrationStepLayoutRule',
                         'http' => 'expMigrationStepHttp', 'mail' => 'expMigrationStepMail', 'command' => 'expMigrationStepCommand' );

    /** @var string|null */
    private $dir;

    /** @var array|null the mail settings before the test */
    private $mailSettings;

    /**
     * @param string $yaml
     * @return string the errors of the steps, one per line
     */
    private static function errors( $yaml )
    {
        $errors = array();
        expMigrationYaml::fromText( $yaml, expMigrationStepRegistry::fromSettings(), $errors );
        return implode( "\n", $errors );
    }

    /**
     * @param string $name
     * @param array $step
     * @return array
     */
    private static function step( $name, array $step )
    {
        $type = expMigrationStepRegistry::fromSettings()->get( $name );
        return expMigrationStepSchema::withDefaults( $type->schema(), $step );
    }

    /**
     * @param string $name
     * @param array $step
     * @return string[]
     */
    private static function plan( $name, array $step )
    {
        return expMigrationStepRegistry::fromSettings()->get( $name )->plan( self::step( $name, $step ), null );
    }

    /** @return expMigrationContext */
    private static function context()
    {
        return new expMigrationContext( new stdClass(), true, array( 'no-mirror' => true, 'user' => null, 'run-id' => '20261106-100000-abcdef' ) );
    }

    /** @var callable|null the exception handler before the test */
    private $handler;

    protected function setUp(): void
    {
        if ( !expMigrationYamlParser::available() )
            $this->markTestSkipped( (string)expMigrationYamlParser::problem() );
        expMigrationStepRegistry::forget();
        $this->handler = set_exception_handler( null );
        restore_exception_handler();
    }

    protected function tearDown(): void
    {
        // the audit of a step may look up the current user, which installs the kernel's exception handler
        for ( $i = 0; $i < 5; $i++ )
        {
            $now = set_exception_handler( null );
            restore_exception_handler();
            if ( $now === $this->handler )
                break;
            restore_exception_handler();
        }
        expMigrationStepHttp::setTransport( null );
        expMigrationProcess::setRunner( null );
        putenv( 'WP10E_TOKEN' );
        putenv( 'WP10E_SIGN' );
        if ( $this->mailSettings !== null )
        {
            $ini = eZINI::instance();
            foreach ( $this->mailSettings as $name => $value )
                $ini->setVariable( 'MailSettings', $name, $value );
            $this->mailSettings = null;
        }
        if ( $this->dir !== null && is_dir( $this->dir ) )
        {
            foreach ( glob( $this->dir . '/*' ) as $file )
                unlink( $file );
            rmdir( $this->dir );
        }
        $this->dir = null;
    }

    /** ML-01 */
    public function testRegistered()
    {
        $registry = expMigrationStepRegistry::fromSettings();
        $this->assertSame( array(), $registry->errors() );
        foreach ( self::TYPES as $name => $class )
        {
            $type = $registry->get( $name );
            $this->assertInstanceOf( $class, $type, $name );
            $schema = $type->schema();
            $this->assertSame( array(), expMigrationStepSchema::problems( $schema ), $name );
            $this->assertNotEmpty( $schema['values'], "$name gives values to references:" );
            $this->assertSame( '', self::errors( $schema['example'] ), "the example of $name is a valid step" );
        }
        foreach ( array( 'layout', 'layout_block', 'layout_rule' ) as $name )
        {
            $type = $registry->get( $name );
            $this->assertInstanceOf( 'expMigrationPartlyRevertibleStepType', $type, $name );
            $this->assertTrue( $type->revertible( self::step( $name, array( 'mode' => 'create' ) ) ), "a create of $name undoes itself" );
            $this->assertFalse( $type->revertible( self::step( $name, array( 'mode' => 'update' ) ) ), "an update of $name does not" );
        }
        foreach ( array( 'http', 'mail', 'command' ) as $name )
            $this->assertNotInstanceOf( 'expMigrationRevertibleStepType', $registry->get( $name ), "$name does not undo itself" );
    }

    /** ML-02 */
    public function testLayoutChecks()
    {
        $this->assertSame( '', self::errors( "- type: layout\n  mode: create\n  identifier: header_footer\n  shared: true\n  zones: { header: {}, footer: {} }\n"
                                             . "- type: layout\n  mode: ensure\n  identifier: landing\n  layout_type: layout_2\n"
                                             . "  zones: { header: { link: 'header_footer/header' }, footer: { link: none }, main: ~ }\n  publish: false\n"
                                             . "- type: layout\n  mode: update\n  identifier: landing\n  name: Landing\n"
                                             . "- type: layout\n  mode: delete\n  identifier: landing\n  missing: skip\n" ) );
        $this->assertStringContainsString( 'identifier: letters, digits, _ and -', self::errors( "- type: layout\n  mode: create\n  identifier: 'a b'\n" ) );
        $this->assertStringContainsString( 'nothing to update', self::errors( "- type: layout\n  mode: update\n  identifier: a\n" ) );
        $errors = self::errors( "- type: layout\n  mode: create\n  identifier: a\n  zones: { 'x y': {}, main: { link: 'nope' }, side: { blocks: 1 } }\n" );
        foreach ( array( 'zones.x y: the identifier of a zone', 'zones.main.link: "<shared layout>/<zone>" or none', 'zones.side: {} or { link:' ) as $expected )
            $this->assertStringContainsString( $expected, $errors );
        $this->assertStringContainsString( 'steps[0] (layout).zones: only in the mode create, update, ensure', self::errors( "- type: layout\n  mode: delete\n  identifier: a\n  zones: { main: {} }\n" ) );
    }

    /** ML-03 */
    public function testLayoutBlockChecks()
    {
        $this->assertSame( '', self::errors( "- type: layout_block\n  mode: create\n  layout: landing\n  zone: main\n  name: Welcome\n  definition: title\n"
                                             . "  parameters: { title: Hello, tag: h1 }\n  position: 0\n"
                                             . "- type: layout_block\n  mode: update\n  layout: landing\n  zone: main\n  name: Welcome\n  view_type: title\n"
                                             . "- type: layout_block\n  mode: delete\n  layout: landing\n  zone: main\n  name: Welcome\n  publish: false\n" ) );
        $this->assertStringContainsString( 'definition is missing (mode create)', self::errors( "- type: layout_block\n  mode: create\n  layout: a\n  zone: main\n  name: X\n" ) );
        $this->assertStringContainsString( 'nothing to update', self::errors( "- type: layout_block\n  mode: update\n  layout: a\n  zone: main\n  name: X\n" ) );
        $errors = self::errors( "- type: layout_block\n  mode: ensure\n  layout: 'a/b'\n  zone: main\n  name: X\n  definition: title\n  position: -1\n  parameters: { 'bad name': 1 }\n" );
        foreach ( array( 'layout: letters, digits, _ and -', 'position: 0 or more', 'parameters.bad name: the name of a parameter' ) as $expected )
            $this->assertStringContainsString( $expected, $errors );
        $this->assertStringContainsString( 'name', self::errors( "- type: layout_block\n  mode: delete\n  layout: a\n  zone: main\n" ) );
    }

    /** ML-04 */
    public function testLayoutRuleChecks()
    {
        $this->assertSame( '', self::errors( "- type: layout_rule\n  mode: create\n  layout: landing\n  targets: [ { type: node, value: 'remote:landing' }, { type: path_prefix, value: /campaign } ]\n"
                                             . "  conditions: [ { type: siteaccess, value: site } ]\n  priority: 50\n"
                                             . "- type: layout_rule\n  mode: update\n  layout: landing\n  targets: [ { type: path, value: /a } ]\n  enabled: false\n"
                                             . "- type: layout_rule\n  mode: delete\n  layout: landing\n  which: all\n" ) );
        $this->assertStringContainsString( 'targets is missing: where the rule applies', self::errors( "- type: layout_rule\n  mode: create\n  layout: a\n" ) );
        $this->assertStringContainsString( 'name the rule by targets: or delete every rule of the layout with which: all', self::errors( "- type: layout_rule\n  mode: delete\n  layout: a\n" ) );
        $this->assertStringContainsString( 'targets: does not go with it', self::errors( "- type: layout_rule\n  mode: delete\n  layout: a\n  which: all\n  targets: [ { type: path, value: /a } ]\n" ) );
        $errors = self::errors( "- type: layout_rule\n  mode: create\n  layout: a\n  targets: [ { type: planet, value: mars }, { type: node, value: 'tag:x' }, x ]\n" );
        foreach ( array( 'targets[0]: planet is no target type', 'targets[1].value:', 'targets[2]: { type: <type>, value: <value> }' ) as $expected )
            $this->assertStringContainsString( $expected, $errors );
        $this->assertStringContainsString( 'nothing to update', self::errors( "- type: layout_rule\n  mode: update\n  layout: a\n  targets: [ { type: path, value: /a } ]\n" ) );
    }

    /** ML-05 */
    public function testHttpChecks()
    {
        $this->assertSame( '', self::errors( "- type: http\n  url: https://example.com/a?b=1\n"
                                             . "- type: http\n  method: POST\n  url: http://example.com:8080/hook\n  json: { a: 1 }\n  sign: migration.webhook\n"
                                             . "  bearer: 'env:TOKEN'\n  secret_headers: { X-Api-Key: api.key }\n  headers: { Accept: application/json }\n  expect: [ 200, 202 ]\n" ) );
        $errors = self::errors( "- type: http\n  method: GET\n  url: ftp://example.com\n  body: x\n  json: {}\n  headers: { 'Bad Header': x, X-A: \"a\\nb\" }\n"
                                . "  secret_headers: { X-Key: 'not a name' }\n  bearer: 'a b'\n  expect: [ 99, x ]\n  timeout: 0\n" );
        foreach ( array( 'url: the URL must be http:// or https:// with a host', 'body: and json: exclude each other', 'a GET request has no body',
                         'headers.Bad Header: the name of a header', 'headers.X-A: one line of text', 'secret_headers.X-Key: the name of a secret',
                         'bearer: the name of a secret', 'expect[0]: a status code', 'expect[1]: a status code', 'timeout: 1 to 600 seconds' ) as $expected )
            $this->assertStringContainsString( $expected, $errors );
        $this->assertStringContainsString( 'must not carry a user or a password', self::errors( "- type: http\n  url: https://u:p@example.com/\n" ) );
        $this->assertStringContainsString( 'method', self::errors( "- type: http\n  method: TRACE\n  url: https://example.com/\n" ) );
    }

    /** ML-06 */
    public function testMailChecks()
    {
        $this->assertSame( '', self::errors( "- type: mail\n  to: [ a@example.com, '\${ref.editor}' ]\n  cc: [ b@example.com ]\n  subject: Hello\n  body: Text\n"
                                             . "  from: c@example.com\n  reply_to: d@example.com\n  transport: file\n  html: true\n" ) );
        $errors = self::errors( "- type: mail\n  to: [ nobody ]\n  bcc: [ 'x@' ]\n  subject: \"a\\nb\"\n  body: x\n  from: me\n  transport: 'my transport'\n" );
        foreach ( array( 'to[0]: an e-mail address', 'bcc[0]: an e-mail address', 'subject: one line', 'from: an e-mail address', 'transport: the name of a transport' ) as $expected )
            $this->assertStringContainsString( $expected, $errors );
        $this->assertStringContainsString( 'to: at least one address', self::errors( "- type: mail\n  to: []\n  subject: a\n  body: b\n" ) );
        $this->assertStringContainsString( 'category: a mail category of mailpreferences.ini',
                                           self::errors( "- type: mail\n  to: [ a@example.com ]\n  subject: a\n  body: b\n  category: no_such_category\n" ) );
        $this->assertSame( '', self::errors( "- type: mail\n  to: [ a@example.com ]\n  subject: a\n  body: b\n  category: system\n" ) );
    }

    /** ML-07 */
    public function testCommandChecks()
    {
        $allow = array( 'exp:services:compile', 'exp:static:*', 'exp:migration:status', 'bin/php/ezcache.php' );
        $this->assertSame( '', expMigrationStepCommand::refusal( 'exp:services:compile', $allow ) );
        $this->assertSame( '', expMigrationStepCommand::refusal( 'exp:static:build', $allow ) );
        $this->assertSame( 'exp:services:list is not in migration.ini [StepSettings] CommandAllowList[]', expMigrationStepCommand::refusal( 'exp:services:list', $allow ) );
        $this->assertSame( 'exp:migration:status is never run from a migration (it takes the lock the run holds)', expMigrationStepCommand::refusal( 'exp:migration:status', $allow ) );
        foreach ( array( 'exp:upgrade', 'exp:upgrade:finish' ) as $command )
            $this->assertStringContainsString( 'is never run from a migration', expMigrationStepCommand::refusal( $command, array( 'exp:*' ) ) );
        foreach ( array( 'bin/php/ezcache.php', 'ls', 'exp:a;rm', 'EXP:x', '' ) as $command )
            $this->assertSame( 'only an exp:* command of ./console runs', expMigrationStepCommand::refusal( $command, $allow ), $command );
        $this->assertTrue( expMigrationStepCommand::matches( 'exp:static:*', 'exp:static:publish' ) );
        $this->assertFalse( expMigrationStepCommand::matches( 'exp:static:*', 'exp:staticx' ) );
        $this->assertContains( 'exp:services:compile', expMigrationStepCommand::allowList() );
        $this->assertSame( '', self::errors( "- type: command\n  command: exp:services:compile\n  arguments: [ --apply, 'a;b' ]\n" ) );
        $errors = self::errors( "- type: command\n  command: exp:migration:migrate\n  arguments: [ \"a\\nb\" ]\n  timeout: 0\n" );
        foreach ( array( 'command: exp:migration:migrate is never run from a migration', 'arguments[0]: one line of text', 'timeout: 1 to 86400 seconds' ) as $expected )
            $this->assertStringContainsString( $expected, $errors );
        $this->assertStringContainsString( 'is not in migration.ini [StepSettings] CommandAllowList[]', self::errors( "- type: command\n  command: exp:services:list\n" ) );
    }

    /** ML-08 */
    public function testPlans()
    {
        $this->assertSame( array( 'create the layout landing of the type layout_2, zones main, header (from header_footer/header), left as a draft' . self::layoutNote() ),
                           self::plan( 'layout', array( 'mode' => 'create', 'identifier' => 'landing', 'layout_type' => 'layout_2', 'publish' => false,
                                                        'zones' => array( 'main' => array(), 'header' => array( 'link' => 'header_footer/header' ) ) ) ) );
        $this->assertSame( array( 'delete the layout landing and its draft' . self::layoutNote() ), self::plan( 'layout', array( 'mode' => 'delete', 'identifier' => 'landing' ) ) );
        $this->assertSame( array( 'create the block "Welcome" (title) in the zone main of the layout landing, parameters title' . self::layoutNote() ),
                           self::plan( 'layout_block', array( 'mode' => 'create', 'layout' => 'landing', 'zone' => 'main', 'name' => 'Welcome', 'definition' => 'title',
                                                              'parameters' => array( 'title' => 'Hi' ) ) ) );
        $this->assertSame( array( 'delete every rule of the layout landing' . self::layoutNote() ), self::plan( 'layout_rule', array( 'mode' => 'delete', 'layout' => 'landing', 'which' => 'all' ) ) );
        $this->assertSame( array( 'send POST https://example.com/hook?... (a bearer token, signed, a JSON body) (not sent in a dry run)' ),
                           self::plan( 'http', array( 'method' => 'post', 'url' => 'https://example.com/hook?token=x', 'bearer' => 'b', 'sign' => 's', 'json' => array() ) ) );
        $this->assertSame( array( 'send the mail "Hi" to 2 addresses (transport file) (not sent in a dry run)' ),
                           self::plan( 'mail', array( 'to' => array( 'a@example.com' ), 'bcc' => array( 'b@example.com' ), 'subject' => 'Hi', 'body' => 'x', 'transport' => 'file' ) ) );
        $this->assertSame( array( 'run ./console exp:services:compile --apply (not run in a dry run)' ),
                           self::plan( 'command', array( 'command' => 'exp:services:compile', 'arguments' => array( '--apply' ) ) ) );
    }

    /** @return string the note of a layout plan where Exponential Layouts is missing */
    private static function layoutNote()
    {
        return expMigrationStepLayoutBase::installed() ? '' : ' (Exponential Layouts is not installed here)';
    }

    /** ML-09 */
    public function testHttpSends()
    {
        putenv( 'WP10E_TOKEN=t0ken' );
        putenv( 'WP10E_SIGN=s1gn' );
        $sent = array();
        expMigrationStepHttp::setTransport( function ( $method, $url, array $headers, $body, $timeout ) use ( &$sent ) {
            $sent[] = compact( 'method', 'url', 'headers', 'body', 'timeout' );
            return array( 202, '{"id":7,"ok":true}', null );
        } );
        $step = self::step( 'http', array( 'method' => 'POST', 'url' => 'https://example.com/hook?x=1', 'headers' => array( 'Accept' => 'application/json' ),
                                           'secret_headers' => array( 'X-Api-Key' => 'env:WP10E_TOKEN' ), 'bearer' => 'env:WP10E_TOKEN',
                                           'sign' => 'env:WP10E_SIGN', 'json' => array( 'paths' => array( '/news/*' ) ), 'expect' => array( 202 ), 'timeout' => 5 ) );
        $result = expMigrationStepRegistry::fromSettings()->get( 'http' )->apply( $step, self::context() );
        $this->assertSame( array( 'POST https://example.com/hook?... answered 202' ), $result['lines'] );
        $this->assertSame( 202, $result['values']['status'] );
        $this->assertSame( array( 'id' => 7, 'ok' => true ), $result['values']['json'] );
        $this->assertCount( 1, $sent );
        $this->assertSame( 'POST', $sent[0]['method'] );
        $this->assertSame( 'https://example.com/hook?x=1', $sent[0]['url'] );
        $this->assertSame( '{"paths":["/news/*"]}', $sent[0]['body'] );
        $this->assertSame( 5, $sent[0]['timeout'] );
        $headers = array();
        foreach ( $sent[0]['headers'] as $line )
        {
            list( $name, $value ) = explode( ': ', $line, 2 );
            $headers[$name] = $value;
        }
        $this->assertSame( 'application/json', $headers['Accept'] );
        $this->assertSame( 't0ken', $headers['X-Api-Key'] );
        $this->assertSame( 'Bearer t0ken', $headers['Authorization'] );
        $this->assertSame( 'application/json', $headers['Content-Type'] );
        $this->assertSame( '', expGraphQLWebhooks::verify( 's1gn', $headers['X-Exponential-Timestamp'], $sent[0]['body'], $headers['X-Exponential-Signature'] ),
                           'a receiver of Exponential webhooks accepts the signature' );
        foreach ( $result['lines'] as $line )
            $this->assertStringNotContainsString( 't0ken', $line );

        expMigrationStepHttp::setTransport( function () { return array( 500, 'oops', null ); } );
        try
        {
            expMigrationStepRegistry::fromSettings()->get( 'http' )->apply( self::step( 'http', array( 'url' => 'https://example.com/a' ) ), self::context() );
            $this->fail( 'a 500 fails the step' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertSame( 'GET https://example.com/a answered 500', $e->getMessage() );
        }
        expMigrationStepHttp::setTransport( function () { return array( 0, '', 'Could not resolve host' ); } );
        try
        {
            expMigrationStepRegistry::fromSettings()->get( 'http' )->apply( self::step( 'http', array( 'url' => 'https://example.com/a' ) ), self::context() );
            $this->fail( 'a transport error fails the step' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertSame( 'GET https://example.com/a: Could not resolve host', $e->getMessage() );
        }
        try
        {
            expMigrationStepRegistry::fromSettings()->get( 'http' )->apply( self::step( 'http', array( 'url' => 'https://example.com/a', 'bearer' => 'env:WP10E_NOT_SET' ) ), self::context() );
            $this->fail( 'a missing secret fails the step before anything is sent' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( "bearer: the secret 'env:WP10E_NOT_SET' is not set", $e->getMessage() );
        }
    }

    /** ML-10 */
    public function testCommandRuns()
    {
        $calls = array();
        expMigrationProcess::setRunner( function ( array $command, $cwd, $timeout ) use ( &$calls ) {
            $calls[] = array( array_slice( $command, 1 ), $timeout );
            return array( 0, "compiled\n" );
        } );
        $result = expMigrationStepRegistry::fromSettings()->get( 'command' )->apply(
            self::step( 'command', array( 'command' => 'exp:services:compile', 'arguments' => array( '--apply', 'a;rm -rf x', '$(id)' ), 'timeout' => 30 ) ), self::context() );
        $this->assertCount( 1, $calls );
        $expected = array( 'bin/php/console', 'exp:services:compile', '--apply', 'a;rm -rf x', '$(id)' );
        if ( function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 )
            $expected[] = '--allow-root-user';
        $this->assertSame( $expected, $calls[0][0], 'each argument stays one argument' );
        $this->assertSame( 30, $calls[0][1] );
        $this->assertSame( array( './console exp:services:compile exited with 0' ), $result['lines'] );
        $this->assertSame( array( 'exit' => 0, 'output' => "compiled\n" ), $result['values'] );

        expMigrationProcess::setRunner( function () { return array( 3, "line 1\nfailed here" ); } );
        try
        {
            expMigrationStepRegistry::fromSettings()->get( 'command' )->apply( self::step( 'command', array( 'command' => 'exp:services:compile' ) ), self::context() );
            $this->fail( 'exit 3 fails the step' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertSame( './console exp:services:compile exited with 3 (expected 0): line 1 | failed here', $e->getMessage() );
        }
        $calls = array();
        expMigrationProcess::setRunner( function ( array $command ) use ( &$calls ) {
            $calls[] = $command;
            return array( 0, '' );
        } );
        try
        {
            expMigrationStepRegistry::fromSettings()->get( 'command' )->apply( self::step( 'command', array( 'command' => 'exp:services:list' ) ), self::context() );
            $this->fail( 'a command off the list fails' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'is not in migration.ini', $e->getMessage() );
        }
        $this->assertSame( array(), $calls, 'a command off the list never runs' );
    }

    /** ML-11 */
    public function testMailToTheFileTransport()
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/migration-mail-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
        mkdir( $this->dir, 0775, true );
        $ini = eZINI::instance();
        $this->mailSettings = array( 'FileTransportDirectory' => $ini->hasVariable( 'MailSettings', 'FileTransportDirectory' ) ? $ini->variable( 'MailSettings', 'FileTransportDirectory' ) : '' );
        $ini->setVariable( 'MailSettings', 'FileTransportDirectory', $this->dir );
        $result = expMigrationStepRegistry::fromSettings()->get( 'mail' )->apply(
            self::step( 'mail', array( 'to' => array( 'editors@example.com' ), 'cc' => array( 'chief@example.com' ), 'subject' => 'WP10e news',
                                       'body' => 'The news section has a new class.', 'from' => 'noreply@example.com', 'transport' => 'file' ) ), self::context() );
        $this->assertSame( array( 'sent the mail "WP10e news" to 2 addresses' ), $result['lines'] );
        $this->assertSame( array( 'recipients' => 2 ), $result['values'] );
        $files = glob( $this->dir . '/*.mail' );
        $this->assertCount( 1, $files );
        $text = file_get_contents( $files[0] );
        $this->assertStringContainsString( 'WP10e news', $text );
        $this->assertStringContainsString( 'The news section has a new class.', $text );
        $this->assertStringContainsString( 'editors@example.com', $text );
        $this->assertMatchesRegularExpression( '/^X-Exp-Mail-Category: admin\r?$/mi', $text, 'filed by the mail gate as a notice to administrators' );
    }

    /** ML-12 */
    public function testCaches()
    {
        $registry = expMigrationStepRegistry::fromSettings();
        foreach ( array( 'layout', 'layout_block', 'layout_rule' ) as $name )
            $this->assertSame( array( 'content', 'template-block' ), $registry->get( $name )->caches( array( 'mode' => 'create' ) ), $name );
        foreach ( array( 'http', 'mail', 'command' ) as $name )
            $this->assertSame( array(), $registry->get( $name )->caches( array() ), $name );
    }
}
