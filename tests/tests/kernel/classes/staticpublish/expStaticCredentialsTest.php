<?php
/**
 * The credentials of static publishing (expStaticCredentials) and the pure parts of its repository
 * (expStaticRepository): no database, no network. git runs where a test needs it, in a folder under var/tmp that is
 * removed afterwards.
 *
 *  SR-01 Credential settings parsed: token:, token:env:, sshkey:, app:, none, and what is not one
 *  SR-02 a token reaches git only through the environment: GIT_ASKPASS names the helper, the token is in
 *        EXP_STATIC_GIT_TOKEN, never in the -c options; credential helpers are switched off
 *  SR-03 the askpass helper through git itself (git credential fill): git asks it and gets the user and the token
 *  SR-04 a token never leaks: a push with it to an unreachable https remote, every git command line, the environment
 *        besides EXP_STATIC_GIT_TOKEN, the report and the remote address in the checkout are scanned for it; an
 *        error text that quotes it (also URL-encoded) is redacted
 *  SR-05 a missing secret and an empty env: variable are named, never a value; app: without the GitHub integration is
 *        refused, with a provider it is used like a token
 *  SR-06 the SSH deploy key: the private key in a file only the process can read for the time of the call, named by
 *        GIT_SSH_COMMAND (BatchMode, the target's known_hosts), removed by cleanup(); the fingerprint; a key made with
 *        ssh-keygen leaves no file behind
 *  SR-07 tag names: static/<target>/<n>, numbers counted numerically (10 after 9), names of other targets ignored
 *  SR-08 the push guard refuses --force, -f, --force-with-lease, --mirror, --delete and + or : refspecs
 *  SR-09 the per-target lock: a second holder is refused with the first one named, released on release()
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expStaticCredentialsTest extends PHPUnit\Framework\TestCase
{
    const TOKEN = 'ghp_Sup3rS3cretT0ken+/=value';

    /** @var string */
    private $dir;

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    protected function setUp(): void
    {
        $this->dir = self::root() . '/var/tmp/static-credentials-test-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
        mkdir( $this->dir, 0775, true );
    }

    protected function tearDown(): void
    {
        putenv( 'EXP_STATIC_TEST_TOKEN' );
        expStaticCredentials::provider( 'app', null );
        if ( is_dir( $this->dir ) )
            expStaticBuild::removeDirectory( $this->dir );
    }

    private function credentials( $credential, array $secrets = array( 'pages_token' => self::TOKEN ) )
    {
        return new expStaticCredentials( $credential, array( 'work_dir' => $this->dir, 'root' => self::root(),
                                                             'lookup' => function ( $name ) use ( $secrets ) {
                                                                 return isset( $secrets[$name] ) ? $secrets[$name] : null;
                                                             } ) );
    }

    /** git without a shell; ok, output */
    private static function git( $cwd, array $args, array $env = array(), $input = '' )
    {
        $pipes = array();
        $base = array( 'PATH' => getenv( 'PATH' ) ?: '/usr/bin:/bin', 'HOME' => getenv( 'HOME' ) ?: '/tmp', 'LC_ALL' => 'C', 'GIT_TERMINAL_PROMPT' => '0' );
        $p = proc_open( array_merge( array( 'git' ), $args ), array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, $cwd, $env + $base );
        fwrite( $pipes[0], $input );
        fclose( $pipes[0] );
        $out = stream_get_contents( $pipes[1] );
        $err = stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $exit = proc_close( $p );
        return array( 'ok' => $exit === 0, 'output' => (string)$out, 'error' => (string)$err );
    }

    private function needGit()
    {
        $probe = self::git( $this->dir, array( '--version' ) );
        if ( !$probe['ok'] )
            $this->markTestSkipped( 'git is not installed' );
    }

    /** SR-01 */
    public function testParse()
    {
        $this->assertSame( array( 'token', 'pages_token' ), expStaticCredentials::parse( 'token:pages_token' ) );
        $this->assertSame( array( 'token', 'env:GH_TOKEN' ), expStaticCredentials::parse( 'token:env:GH_TOKEN' ) );
        $this->assertSame( array( 'sshkey', 'staticpublish.site.deploy_key' ), expStaticCredentials::parse( 'sshkey:staticpublish.site.deploy_key' ) );
        $this->assertSame( array( 'app', '12345' ), expStaticCredentials::parse( 'app:12345' ) );
        $this->assertSame( array( 'none', '' ), expStaticCredentials::parse( 'none' ) );
        $this->assertSame( array( 'none', '' ), expStaticCredentials::parse( '' ) );
        $this->assertSame( '', expStaticCredentials::parse( 'password:x' )[0] );
        $this->assertSame( '', expStaticCredentials::parse( 'token:' )[0] );
        $this->assertSame( 'token:pages_token', $this->credentials( 'token:pages_token' )->describe() );
        $this->assertStringContainsString( 'must be', $this->credentials( 'basic:x' )->problem() );
        $this->assertSame( 'staticpublish.github-public.deploy_key', expStaticCredentials::keySecretName( 'github-public' ) );
    }

    /** SR-02 */
    public function testTokenOnlyInTheEnvironment()
    {
        $c = $this->credentials( 'token:pages_token' );
        $this->assertSame( '', $c->problem() );
        $g = $c->gitEnvironment();
        $this->assertSame( self::root() . '/bin/shell/expstaticaskpass.sh', $g['env']['GIT_ASKPASS'] );
        $this->assertTrue( is_executable( $g['env']['GIT_ASKPASS'] ), 'the askpass helper is executable' );
        $this->assertSame( "#!/usr/bin/env sh\n", substr( file_get_contents( $g['env']['GIT_ASKPASS'] ), 0, 18 ) );
        $this->assertSame( self::TOKEN, $g['env'][expStaticCredentials::ENV_TOKEN] );
        $this->assertSame( 'x-access-token', $g['env'][expStaticCredentials::ENV_USER] );
        $this->assertSame( '0', $g['env']['GIT_TERMINAL_PROMPT'] );
        $this->assertSame( array( '-c', 'credential.helper=' ), $g['options'] );
        foreach ( $g['env'] as $name => $value )
            if ( $name !== expStaticCredentials::ENV_TOKEN )
                $this->assertStringNotContainsString( self::TOKEN, $value, $name );

        putenv( 'EXP_STATIC_TEST_TOKEN=' . self::TOKEN );
        $env = $this->credentials( 'token:env:EXP_STATIC_TEST_TOKEN', array() );
        $this->assertSame( '', $env->problem() );
        $this->assertSame( self::TOKEN, $env->gitEnvironment()['env'][expStaticCredentials::ENV_TOKEN] );

        $none = $this->credentials( 'none' );
        $this->assertSame( '', $none->problem() );
        $g = $none->gitEnvironment();
        $this->assertArrayNotHasKey( 'GIT_ASKPASS', $g['env'] );
        $this->assertArrayNotHasKey( expStaticCredentials::ENV_TOKEN, $g['env'] );
        $this->assertSame( array(), $g['options'] );
    }

    /** SR-03 */
    public function testAskpassThroughGit()
    {
        $this->needGit();
        $c = $this->credentials( 'token:pages_token' );
        $c->problem();
        $g = $c->gitEnvironment();
        $r = self::git( $this->dir, array_merge( $g['options'], array( 'credential', 'fill' ) ), $g['env'], "protocol=https\nhost=github.com\npath=owner/repo.git\n\n" );
        $this->assertTrue( $r['ok'], $r['error'] );
        $this->assertStringContainsString( "username=x-access-token\n", $r['output'] );
        $this->assertStringContainsString( 'password=' . self::TOKEN . "\n", $r['output'] );
    }

    /** SR-04 */
    public function testTokenNeverLeaks()
    {
        $this->needGit();
        $checkout = $this->dir . '/checkout';
        mkdir( $checkout . '/.static-publish', 0775, true );
        file_put_contents( $checkout . '/index.html', '<h1>Home</h1>' );
        $manifest = new expStaticManifest( array( 'run_id' => 'r1' ) );
        $manifest->add( 'index.html', 'page', hash_file( 'sha256', $checkout . '/index.html' ), filesize( $checkout . '/index.html' ) );
        $manifest->save( $checkout );
        // port 9 (discard) on this computer: the connection is refused at once, before git would ask for the token
        $target = new expStaticTarget( 'leak', array( 'SiteAccess' => 'site', 'Remote' => 'https://127.0.0.1:9/owner/repo.git', 'Credential' => 'token:pages_token',
                                                      'Branch' => 'gh-pages' ) );
        $calls = array();
        $repo = new expStaticRecordingRepository( $target, $checkout, $this->credentials( 'token:pages_token' ),
                                                 array( 'commit_name' => 'Test', 'commit_email' => 'test@example.com', 'git_manager' => false ) );
        $repo->calls =& $calls;
        $result = array();
        $this->assertTrue( $repo->prepare( $result ), implode( '; ', $result['problems'] ) );
        $this->assertTrue( $repo->commit( 'Publish', $result ), implode( '; ', $result['problems'] ) );
        $state = $repo->push( $result );
        $this->assertSame( expStaticRepository::PENDING, $state );
        $this->assertNotEmpty( $calls );
        $network = 0;
        foreach ( $calls as $call )
        {
            $this->assertStringNotContainsString( self::TOKEN, implode( ' ', $call['argv'] ), 'no token on a command line' );
            $this->assertStringNotContainsString( rawurlencode( self::TOKEN ), implode( ' ', $call['argv'] ) );
            $this->assertNotContains( '--force', $call['argv'] );
            foreach ( $call['env'] as $name => $value )
                if ( $name !== expStaticCredentials::ENV_TOKEN )
                    $this->assertStringNotContainsString( self::TOKEN, (string)$value, "no token in $name" );
            if ( isset( $call['env'][expStaticCredentials::ENV_TOKEN] ) )
            {
                $network++;
                $this->assertContains( 'push', $call['argv'], 'only the commands that talk to the remote get the token' );
            }
        }
        $this->assertSame( 1, $network );
        $this->assertStringNotContainsString( self::TOKEN, json_encode( $result ) );
        $this->assertStringNotContainsString( self::TOKEN, (string)file_get_contents( $checkout . '/.git/config' ), 'not in the remote address' );
        $this->assertStringContainsString( 'https://127.0.0.1:9/owner/repo.git', (string)file_get_contents( $checkout . '/.git/config' ) );

        $c = $this->credentials( 'token:pages_token' );
        $c->problem();
        $this->assertSame( 'fatal: unable to access https://x-access-token:[secret token:pages_token]@github.com/ and [secret token:pages_token]',
                           $c->redact( 'fatal: unable to access https://x-access-token:' . rawurlencode( self::TOKEN ) . '@github.com/ and ' . self::TOKEN ) );
    }

    /** SR-05 */
    public function testMissingAndAppCredentials()
    {
        $missing = $this->credentials( 'token:other_token' )->problem();
        $this->assertSame( 'the secret other_token is not set (Setup > Secrets, or env:VARIABLE)', $missing );
        $this->assertStringContainsString( 'env:EXP_STATIC_TEST_UNSET', $this->credentials( 'token:env:EXP_STATIC_TEST_UNSET', array() )->problem() );
        $this->assertMatchesRegularExpression( '/GitHub integration|GitHub App is not set up/', $this->credentials( 'app:4711' )->problem() );
        $this->assertStringContainsString( 'no private key', $this->credentials( 'sshkey:pages_token' )->problem() );
        $this->assertStringContainsString( 'more than one line', $this->credentials( 'token:two', array( 'two' => "a\nb" ) )->problem() );

        $seen = array();
        expStaticCredentials::provider( 'app', function ( $installation, $target ) use ( &$seen ) {
            $seen[] = $installation;
            return 'ghs_installation_token';
        } );
        $app = $this->credentials( 'app:4711' );
        $this->assertSame( '', $app->problem() );
        $this->assertSame( array( '4711' ), $seen );
        $this->assertSame( 'ghs_installation_token', $app->gitEnvironment()['env'][expStaticCredentials::ENV_TOKEN] );
        $this->assertSame( 'push to [secret app:4711]', $app->redact( 'push to ghs_installation_token' ) );
    }

    /** SR-06 */
    public function testDeployKey()
    {
        $key = "-----BEGIN OPENSSH PRIVATE KEY-----\nb3BlbnNzaC1rZXktdjEAAAAA\n-----END OPENSSH PRIVATE KEY-----\n";
        $c = $this->credentials( 'sshkey:site_key', array( 'site_key' => $key ) );
        $this->assertSame( '', $c->problem() );
        $g = $c->gitEnvironment();
        $this->assertArrayNotHasKey( 'GIT_ASKPASS', $g['env'] );
        $this->assertSame( 1, preg_match( "#^ssh -i '([^']+)' -o IdentitiesOnly=yes -o BatchMode=yes -o StrictHostKeyChecking=accept-new -o UserKnownHostsFile='([^']+)'$#",
                                          $g['env']['GIT_SSH_COMMAND'], $m ), $g['env']['GIT_SSH_COMMAND'] );
        $this->assertStringStartsWith( $this->dir . '/.keys/key-', $m[1] );
        $this->assertSame( $this->dir . '/.known_hosts', $m[2] );
        $this->assertSame( $key, file_get_contents( $m[1] ) );
        if ( function_exists( 'posix_geteuid' ) )
        {
            $this->assertSame( '0600', substr( sprintf( '%o', fileperms( $m[1] ) ), -4 ) );
            $this->assertSame( '0700', substr( sprintf( '%o', fileperms( dirname( $m[1] ) ) ), -4 ) );
        }
        $this->assertStringNotContainsString( 'PRIVATE', implode( ' ', $g['env'] ) );
        $this->assertStringNotContainsString( 'b3BlbnNzaC1rZXktdjEAAAAA', $c->redact( "x\n$key" ) );
        $c->cleanup();
        $this->assertFileDoesNotExist( $m[1] );
        $this->assertSame( "'/a b/it'\\''s'", expStaticCredentials::shellQuote( '/a b/it\'s' ) );
        $this->assertSame( "'C:/sites/x'", expStaticCredentials::shellQuote( 'C:\\sites\\x' ) );
        $this->assertStringContainsString( 'not an environment variable', $this->credentials( 'sshkey:env:X' )->problem() );

        $this->assertSame( 'SHA256:' . rtrim( base64_encode( hash( 'sha256', 'blob', true ) ), '=' ),
                           expStaticCredentials::fingerprint( 'ssh-ed25519 ' . base64_encode( 'blob' ) . ' c' ) );
        $this->assertSame( '', expStaticCredentials::fingerprint( 'nonsense' ) );

        $made = expStaticCredentials::generateKey( $this->dir, 'test@example' );
        if ( $made['error'] !== '' )
            $this->markTestSkipped( 'ssh-keygen is not installed' );
        $this->assertStringContainsString( 'OPENSSH PRIVATE KEY', $made['private'] );
        $this->assertStringStartsWith( 'ssh-ed25519 ', $made['public'] );
        $this->assertStringEndsWith( ' test@example', $made['public'] );
        $this->assertStringStartsWith( 'SHA256:', $made['fingerprint'] );
        $this->assertSame( array( '.', '..' ), scandir( $this->dir . '/.keys' ), 'no key file stays behind' );
    }

    /** SR-07 */
    public function testTagNames()
    {
        $this->assertSame( 'static/github-public/7', expStaticRepository::tagName( 'github-public', 7 ) );
        $this->assertSame( 7, expStaticRepository::tagNumber( 'static/github-public/7', 'github-public' ) );
        $this->assertSame( 7, expStaticRepository::tagNumber( '7', 'github-public' ) );
        $this->assertSame( 0, expStaticRepository::tagNumber( 'static/other/7', 'github-public' ) );
        $this->assertSame( 0, expStaticRepository::tagNumber( 'static/github-public/7a', 'github-public' ) );
        $this->assertSame( 0, expStaticRepository::tagNumber( 'v7', 'github-public' ) );
        $this->assertSame( 1, expStaticRepository::nextNumber( array(), 'x' ) );
        $this->assertSame( 11, expStaticRepository::nextNumber( array( 'static/x/9', 'static/x/10', 'static/x/2', 'static/y/40' ), 'x' ) );
    }

    /** SR-08 */
    public function testPushGuard()
    {
        expStaticRepository::guard( array( '--literal-pathspecs', 'push', '--quiet', 'origin', 'refs/heads/gh-pages:refs/heads/gh-pages',
                                           'refs/tags/static/x/*:refs/tags/static/x/*' ) );
        expStaticRepository::guard( array( 'commit', '-m', '+1 page' ) );
        foreach ( array( '--force', '-f', '--force-with-lease', '--force-with-lease=main', '--mirror', '--delete', '+refs/heads/main:refs/heads/main', ':refs/tags/static/x/1' ) as $bad )
        {
            try
            {
                expStaticRepository::guard( array( 'push', 'origin', $bad ) );
                $this->fail( "push $bad was let through" );
            }
            catch ( LogicException $e )
            {
                $this->assertStringContainsString( 'never forces', $e->getMessage() );
            }
        }
    }

    /** SR-09 */
    public function testLock()
    {
        $problem = '';
        $first = expStaticLock::acquire( $this->dir, 'site', 'publish', $problem );
        $this->assertNotNull( $first, $problem );
        $this->assertNull( expStaticLock::acquire( $this->dir, 'site', 'build', $problem ) );
        $this->assertStringContainsString( 'another process works on the target site (publish by process ' . getmypid(), $problem );
        $this->assertNotNull( expStaticLock::acquire( $this->dir, 'other', 'build', $problem ), 'each target has its own lock' );
        $first->release();
        $again = expStaticLock::acquire( $this->dir, 'site', 'build', $problem );
        $this->assertNotNull( $again, $problem );
        $again->release();
    }
}

/** A repository that records every git command line and environment. */
class expStaticRecordingRepository extends expStaticRepository
{
    /** @var array[] argv, env */
    public $calls = array();

    protected function run( $cwd, array $arguments, array $env, $seconds )
    {
        $this->calls[] = array( 'argv' => $arguments, 'env' => $env );
        return parent::run( $cwd, $arguments, $env, $seconds );
    }
}
