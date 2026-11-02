<?php
/**
 * The GitHub integration of static publishing (expStaticGitHub), the DNS check (expStaticDnsCheck) and the limits
 * checked before a push, without a network: GitHub answers through a fake http callable, DNS through a fake resolver.
 *
 *  GH-01 GitHub addresses: owner and repository from https, ssh and scp forms; the Pages address and base path of a
 *        project site and of <owner>.github.io; other hosts are not GitHub
 *  GH-02 the requests: the token goes in the Authorization header only, never in an address; an error answer names
 *        GitHub's message with the token redacted
 *  GH-03 setup: a missing repository is created (public by default, for the account or for an organization), Pages
 *        is switched on from the branch and folder or from Actions, the custom domain is set and HTTPS enforced; a
 *        plan changes nothing; a missing repository without --create-repo fails; HTTPS waits for the certificate
 *  GH-04 status and the Pages build status (branch builds and Actions runs), and the warnings where GitHub's Pages
 *        settings differ from the target's
 *  GH-05 forTarget(): a deploy key or none cannot call the API; a token is read through the credential
 *  GH-06 the limits of GitHub Pages: a site or a file over the limit is refused, 90 % and 50 MB warn; the repository
 *        refuses the push of a github-pages target over the limits, before any git call
 *  GH-07 the Actions workflow file; written by the build with Delivery=actions
 *  GH-08 the GitHub App: a JWT signed RS256 and verifiable with the public key; the installation token is fetched
 *        with it and never appears in a problem; Credential=app: uses the provider
 *  GH-09 the OAuth device flow: code, pending, slow down, token
 *  GH-10 the DNS check: an apex with GitHub's four A records, missing and foreign records with their fixes, a
 *        subdomain's CNAME to <owner>.github.io, a CNAME to the repository's address
 *  GH-12 Delivery=actions from a branch that is not the repository's default one: status and setup warn that the
 *        environment github-pages lets only the default branch deploy
 *  GH-11 the opt-in integration test against a real GitHub repository: only with EXP_STATIC_GITHUB_TEST_TOKEN and
 *        EXP_STATIC_GITHUB_TEST_REPO (owner/repo) set (or EXP_STATIC_GITHUB_TEST_SECRET, the name of a secret of the
 *        secret store, instead of the token); skipped otherwise
 *  GH-13 the opt-in check of the real test repository's public Pages site: downloaded below /repo/, no broken link;
 *        only with EXP_STATIC_GITHUB_TEST_REPO set
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expStaticGitHubTest extends PHPUnit\Framework\TestCase
{
    const TOKEN = 'github_pat_T3stT0kenV4lue_0123456789';

    /** @var array the requests seen: method, url, headers, body */
    private $requests = array();

    /** @var array "METHOD path" => list of answers (status, body array), taken in turn; the last one repeats */
    private $answers = array();

    /** @var string */
    private $dir = '';

    protected function tearDown(): void
    {
        expStaticCredentials::provider( 'app', null );
        expStaticGitHub::resetAppTokens();
        if ( $this->dir !== '' && is_dir( $this->dir ) )
            expStaticBuild::removeDirectory( $this->dir );
    }

    /** @return callable the fake http of expStaticGitHub */
    private function http()
    {
        $test = $this;
        return function ( $method, $url, array $headers, $body ) use ( $test )
        {
            return $test->answer( $method, $url, $headers, $body );
        };
    }

    /** Public for the closure. */
    public function answer( $method, $url, array $headers, $body )
    {
        $this->requests[] = array( 'method' => $method, 'url' => $url, 'headers' => $headers, 'body' => $body === null ? null : json_decode( $body, true ) );
        $path = preg_replace( '#^https://api\.github\.com#', '', $url );
        $key = "$method $path";
        if ( !isset( $this->answers[$key] ) )
            return array( 'status' => 404, 'body' => '{"message":"Not Found"}' );
        $answer = count( $this->answers[$key] ) > 1 ? array_shift( $this->answers[$key] ) : $this->answers[$key][0];
        return array( 'status' => $answer[0], 'body' => $answer[1] === null ? '' : json_encode( $answer[1] ) );
    }

    private function client()
    {
        return new expStaticGitHub( self::TOKEN, array( 'http' => $this->http(), 'api' => 'https://api.github.com' ) );
    }

    private static function target( array $settings = array() )
    {
        return new expStaticTarget( 'gh', $settings + array( 'SiteAccess' => 'site', 'Kind' => 'github-pages', 'Remote' => 'https://github.com/octo-org/demo-site.git',
                                                            'Branch' => 'gh-pages', 'Credential' => 'token:pages_token' ) );
    }

    private function requestsTo( $method, $pathPart )
    {
        return array_values( array_filter( $this->requests, function ( $r ) use ( $method, $pathPart ) {
            return $r['method'] === $method && strpos( $r['url'], $pathPart ) !== false;
        } ) );
    }

    /** GH-01 */
    public function testGitHubAddresses()
    {
        $expected = array( 'owner' => 'octo-org', 'repo' => 'demo-site' );
        foreach ( array( 'https://github.com/octo-org/demo-site.git', 'https://github.com/octo-org/demo-site', 'git@github.com:octo-org/demo-site.git',
                         'ssh://git@github.com/octo-org/demo-site.git', 'https://github.com/octo-org/demo-site/' ) as $remote )
            $this->assertSame( $expected, expStaticGitHub::parseRemote( $remote ), $remote );
        $this->assertSame( array( 'owner' => 'o', 'repo' => 'my.site' ), expStaticGitHub::parseRemote( 'https://github.com/o/my.site.git' ) );
        foreach ( array( 'https://gitlab.com/o/r.git', 'git@example.com:o/r.git', '/srv/git/r.git', 'https://github.com/o', '' ) as $remote )
            $this->assertNull( expStaticGitHub::parseRemote( $remote ), $remote );
        $this->assertSame( 'https://octo-org.github.io/demo-site/', expStaticGitHub::pagesUrl( 'octo-org', 'demo-site' ) );
        $this->assertSame( 'https://octo-org.github.io/', expStaticGitHub::pagesUrl( 'Octo-Org', 'octo-org.github.io' ) );
        $this->assertSame( '/demo-site/', expStaticGitHub::pagesBasePath( 'octo-org', 'demo-site' ) );
        $this->assertSame( '', expStaticGitHub::pagesBasePath( 'octo-org', 'octo-org.github.io' ) );
    }

    /** GH-02 */
    public function testTheTokenGoesInTheHeaderOnly()
    {
        $this->answers['GET /repos/octo-org/demo-site'] = array( array( 401, array( 'message' => 'Bad credentials for ' . self::TOKEN ) ) );
        $r = $this->client()->repository( 'octo-org', 'demo-site' );
        $this->assertSame( 401, $r['status'] );
        $this->assertSame( 'GitHub answered 401: Bad credentials for ***', $r['error'] );
        $request = $this->requests[0];
        $this->assertSame( 'Bearer ' . self::TOKEN, $request['headers']['Authorization'] );
        $this->assertSame( expStaticGitHub::API_VERSION, $request['headers']['X-GitHub-Api-Version'] );
        $this->assertStringNotContainsString( self::TOKEN, $request['url'] );
        $this->assertStringStartsWith( 'https://api.github.com/', $request['url'] );
    }

    /** GH-03 */
    public function testSetupCreatesTheRepositoryAndSwitchesPagesOn()
    {
        $this->answers['GET /repos/octo-org/demo-site'] = array( array( 404, array( 'message' => 'Not Found' ) ) );
        $this->answers['GET /user'] = array( array( 200, array( 'login' => 'someone' ) ) );
        $this->answers['POST /orgs/octo-org/repos'] = array( array( 201, array( 'name' => 'demo-site', 'private' => false ) ) );
        $report = $this->client()->setup( self::target(), array( 'create' => true ) );
        $this->assertSame( array(), $report['errors'] );
        $this->assertSame( array( 'repository:done', 'pages:planned', 'domain:skipped' ), array_map( function ( $s ) { return $s['step'] . ':' . $s['state']; }, $report['steps'] ) );
        $create = $this->requestsTo( 'POST', '/orgs/octo-org/repos' );
        $this->assertCount( 1, $create );
        $this->assertSame( array( 'name' => 'demo-site', 'private' => false, 'auto_init' => false ), array_intersect_key( $create[0]['body'], array( 'name' => 1, 'private' => 1, 'auto_init' => 1 ) ) );

        // the account's own repository, private
        $this->requests = array();
        $this->answers['GET /user'] = array( array( 200, array( 'login' => 'Octo-Org' ) ) );
        $this->answers['POST /user/repos'] = array( array( 201, array( 'name' => 'demo-site', 'private' => true ) ) );
        $report = $this->client()->setup( self::target(), array( 'create' => true, 'private' => true ) );
        $this->assertSame( true, $this->requestsTo( 'POST', '/user/repos' )[0]['body']['private'] );
        $this->assertStringContainsString( 'paid plan', implode( ' ', $report['warnings'] ) );

        // without --create-repo a missing repository fails
        $report = $this->client()->setup( self::target() );
        $this->assertStringContainsString( 'does not exist', $report['errors'][0] );
    }

    /** GH-03 */
    public function testSetupOfAnExistingRepository()
    {
        $this->answers['GET /repos/octo-org/demo-site'] = array( array( 200, array( 'private' => false ) ) );
        $this->answers['GET /repos/octo-org/demo-site/pages'] = array( array( 404, array( 'message' => 'Not Found' ) ) );
        $this->answers['POST /repos/octo-org/demo-site/pages'] = array( array( 201, array() ) );
        $this->answers['PUT /repos/octo-org/demo-site/pages'] = array( array( 204, null ), array( 400, array( 'message' => 'The certificate does not exist yet' ) ) );
        $target = self::target( array( 'Branch' => 'main', 'Folder' => 'docs', 'CustomDomain' => 'www.example.com' ) );

        $plan = $this->client()->setup( $target, array( 'dry_run' => true ) );
        $this->assertSame( array(), $this->requestsTo( 'POST', '/pages' ), 'a plan changes nothing' );
        $this->assertSame( array( 'repository:unchanged', 'pages:planned', 'domain:planned' ), array_map( function ( $s ) { return $s['step'] . ':' . $s['state']; }, $plan['steps'] ) );

        $report = $this->client()->setup( $target );
        $this->assertSame( array(), $report['errors'] );
        $this->assertSame( array( 'build_type' => 'legacy', 'source' => array( 'branch' => 'main', 'path' => '/docs' ) ), $this->requestsTo( 'POST', '/pages' )[0]['body'] );
        $puts = $this->requestsTo( 'PUT', '/pages' );
        $this->assertSame( array( 'cname' => 'www.example.com' ), $puts[0]['body'] );
        $this->assertSame( array( 'https_enforced' => true ), $puts[1]['body'] );
        $last = end( $report['steps'] );
        $this->assertSame( array( 'https', 'planned' ), array( $last['step'], $last['state'] ), 'HTTPS waits for the certificate' );

        $this->assertSame( array( 'build_type' => 'workflow' ), expStaticGitHub::pagesSource( 'actions', 'main', '' ) );
        $this->assertSame( array( 'build_type' => 'legacy', 'source' => array( 'branch' => 'gh-pages', 'path' => '/' ) ), expStaticGitHub::pagesSource( 'branch', 'gh-pages', '' ) );
    }

    /** GH-04 */
    public function testStatusAndBuildStatus()
    {
        $this->answers['GET /repos/octo-org/demo-site'] = array( array( 200, array( 'private' => false, 'visibility' => 'public', 'html_url' => 'https://github.com/octo-org/demo-site', 'default_branch' => 'main' ) ) );
        $this->answers['GET /repos/octo-org/demo-site/pages'] = array( array( 200, array( 'build_type' => 'legacy', 'source' => array( 'branch' => 'main', 'path' => '/' ),
                                                                                         'cname' => 'www.example.com', 'https_enforced' => true, 'html_url' => 'https://www.example.com/',
                                                                                         'https_certificate' => array( 'state' => 'approved' ) ) ) );
        $this->answers['GET /repos/octo-org/demo-site/pages/builds/latest'] = array( array( 200, array( 'status' => 'built', 'commit' => 'abc', 'updated_at' => '2026-11-01T12:00:00Z' ) ) );
        $status = $this->client()->status( self::target() );
        $this->assertTrue( $status['repo']['exists'] );
        $this->assertSame( 'public', $status['repo']['visibility'] );
        $this->assertSame( 'built', $status['build']['status'] );
        $this->assertSame( 'approved', $status['pages']['certificate'] );
        $warnings = implode( "\n", $status['warnings'] );
        $this->assertStringContainsString( 'GitHub Pages serves main, the target publishes to gh-pages', $warnings );
        $this->assertStringContainsString( 'custom domain "www.example.com"', $warnings );

        $this->answers['GET /repos/octo-org/demo-site/actions/workflows/static-publish-pages.yml/runs?per_page=1'] = array(
            array( 200, array( 'workflow_runs' => array( array( 'status' => 'in_progress', 'conclusion' => null, 'html_url' => 'https://github.com/run/1', 'head_sha' => 'def', 'updated_at' => 'x' ) ) ) ),
            array( 200, array( 'workflow_runs' => array( array( 'status' => 'completed', 'conclusion' => 'failure', 'html_url' => 'https://github.com/run/1', 'head_sha' => 'def', 'updated_at' => 'x' ) ) ) ) );
        $this->assertSame( 'building', $this->client()->buildStatus( 'octo-org', 'demo-site', 'actions' )['status'] );
        $this->assertSame( 'errored', $this->client()->buildStatus( 'octo-org', 'demo-site', 'actions' )['status'] );
        $this->assertSame( 'none', $this->client()->buildStatus( 'octo-org', 'other', 'branch' )['status'] );
        $this->assertSame( 'queued', expStaticGitHub::runState( 'queued', '' ) );
        $this->assertSame( 'built', expStaticGitHub::runState( 'completed', 'success' ) );
    }

    /** GH-12 */
    public function testActionsFromAnotherBranchThanTheDefaultWarns()
    {
        $this->answers['GET /repos/octo-org/demo-site'] = array( array( 200, array( 'private' => false, 'html_url' => 'https://github.com/octo-org/demo-site', 'default_branch' => 'main' ) ) );
        $this->answers['GET /repos/octo-org/demo-site/pages'] = array( array( 200, array( 'build_type' => 'workflow', 'cname' => '' ) ) );
        $this->answers['GET /repos/octo-org/demo-site/actions/workflows/static-publish-pages.yml/runs?per_page=1'] = array( array( 200, array( 'workflow_runs' => array() ) ) );
        $this->answers['PUT /repos/octo-org/demo-site/pages'] = array( array( 204, null ) );

        $actions = self::target( array( 'Delivery' => 'actions', 'Branch' => 'gh-pages' ) );
        $warnings = implode( "\n", $this->client()->status( $actions )['warnings'] );
        $this->assertStringContainsString( 'Delivery=actions deploys from the branch gh-pages', $warnings );
        $this->assertStringContainsString( 'only the default branch main', $warnings );
        $this->assertStringContainsString( 'Settings > Environments > github-pages', $warnings );
        $this->assertStringContainsString( 'deploys from the branch gh-pages', implode( "\n", $this->client()->setup( $actions )['warnings'] ), 'the wizard\'s "Set up on GitHub" says it too' );

        $onDefault = self::target( array( 'Delivery' => 'actions', 'Branch' => 'main' ) );
        $this->assertStringNotContainsString( 'github-pages of', implode( "\n", $this->client()->status( $onDefault )['warnings'] ), 'the default branch may deploy' );
        $this->assertSame( '', expStaticGitHub::environmentBranchWarning( self::target( array( 'Delivery' => 'branch', 'Branch' => 'gh-pages' ) ), 'main' ), 'a branch delivery has no environment' );
        $this->assertSame( '', expStaticGitHub::environmentBranchWarning( $actions, '' ), 'no default branch known, no warning' );
    }

    /** GH-05 */
    public function testForTargetNeedsATokenOrAnApp()
    {
        $problem = '';
        $this->assertNull( expStaticGitHub::forTarget( self::target( array( 'Credential' => 'sshkey:k', 'Remote' => 'git@github.com:octo-org/demo-site.git' ) ), $problem, array( 'http' => $this->http() ) ) );
        $this->assertStringContainsString( 'needs a token or a GitHub App', $problem );
        $this->assertNull( expStaticGitHub::forTarget( self::target( array( 'Remote' => 'https://gitlab.com/o/r.git' ) ), $problem, array( 'http' => $this->http() ) ) );
        $this->assertStringContainsString( 'not a GitHub repository', $problem );
        $credentials = new expStaticCredentials( 'token:pages_token', array( 'lookup' => function ( $name ) { return $name === 'pages_token' ? self::TOKEN : null; } ) );
        $client = expStaticGitHub::forTarget( self::target(), $problem, array( 'http' => $this->http(), 'credentials' => $credentials ) );
        $this->assertInstanceOf( 'expStaticGitHub', $client );
        $client->repository( 'octo-org', 'demo-site' );
        $this->assertSame( 'Bearer ' . self::TOKEN, $this->requests[0]['headers']['Authorization'] );
        $this->assertSame( self::TOKEN, $credentials->apiToken() );
        $this->assertSame( '', ( new expStaticCredentials( 'none' ) )->apiToken() );
    }

    private static function manifest( array $sizes )
    {
        $m = new expStaticManifest();
        foreach ( $sizes as $path => $size )
            $m->add( $path, 'file', str_repeat( 'a', 64 ), $size );
        return $m;
    }

    /** GH-06 */
    public function testLimits()
    {
        $ok = expStaticGitHub::limits( self::manifest( array( 'index.html' => 1000 ) ) );
        $this->assertSame( array( array(), array() ), array( $ok['errors'], $ok['warnings'] ) );
        $big = expStaticGitHub::limits( self::manifest( array( 'a.zip' => 120000000, 'b.mp4' => 60000000, 'index.html' => 5 ) ), array( 'max_site_bytes' => 150000000 ) );
        $this->assertCount( 2, $big['errors'], json_encode( $big ) );
        $this->assertSame( array( 'a.zip' ), $big['too_large'] );
        $this->assertStringContainsString( '50 MB', implode( ' ', $big['warnings'] ) );
        $near = expStaticGitHub::limits( self::manifest( array( 'a' => 95 ) ), array( 'max_site_bytes' => 100, 'private' => true ) );
        $this->assertSame( array(), $near['errors'] );
        $this->assertCount( 2, $near['warnings'] );
    }

    /** GH-06: the repository refuses the push before git is called */
    public function testPushOverTheLimitsIsRefused()
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/static-github-test-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
        mkdir( $this->dir, 0775, true );
        self::manifest( array( 'index.html' => 2000 ) )->save( $this->dir );
        $credentials = new expStaticCredentials( 'none' );
        $repo = new expStaticRepository( self::target( array( 'Credential' => 'none' ) ), $this->dir, $credentials, array( 'max_site_bytes' => 1000 ) );
        $result = array();
        $this->assertSame( expStaticRepository::PENDING, $repo->push( $result ) );
        $this->assertStringContainsString( 'not pushed: the build is', $result['problems'][0] );
        $this->assertSame( 2000, $result['limits']['bytes'] );
        $other = new expStaticRepository( self::target( array( 'Credential' => 'none', 'Kind' => 'git' ) ), $this->dir, $credentials, array( 'max_site_bytes' => 1000 ) );
        $this->assertNull( $other->checkLimits(), 'another git host has no GitHub limits' );
    }

    /** GH-07 */
    public function testWorkflow()
    {
        $yaml = expStaticGitHub::workflow( 'main' );
        foreach ( array( 'branches: ["main"]', 'pages: write', 'id-token: write', 'actions/upload-pages-artifact@v3', 'path: .', 'actions/deploy-pages@v4',
                         '${{ steps.deployment.outputs.page_url }}' ) as $line )
            $this->assertStringContainsString( $line, $yaml );
        $this->assertStringContainsString( 'path: docs', expStaticGitHub::workflow( 'main', '/docs/' ) );
        $injected = expStaticGitHub::workflow( "evil\"]\n  x" );
        $this->assertStringContainsString( 'branches: ["evilx"]', $injected, 'quotes and line breaks never reach the YAML' );
        $this->assertSame( '.github/workflows/static-publish-pages.yml', expStaticGitHub::WORKFLOW_FILE );
    }

    /** GH-08 */
    public function testGitHubAppToken()
    {
        if ( !function_exists( 'openssl_pkey_new' ) )
            $this->markTestSkipped( 'openssl is missing' );
        $key = openssl_pkey_new( array( 'private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA ) );
        $pem = '';
        openssl_pkey_export( $key, $pem );
        $public = openssl_pkey_get_details( $key )['key'];
        $jwt = expStaticGitHub::appJwt( '4242', $pem, 1700000000 );
        list( $h, $p, $s ) = explode( '.', $jwt );
        $payload = json_decode( base64_decode( strtr( $p, '-_', '+/' ) ), true );
        $this->assertSame( array( 'iat' => 1699999940, 'exp' => 1700000540, 'iss' => '4242' ), $payload );
        $this->assertSame( 1, openssl_verify( "$h.$p", base64_decode( strtr( $s, '-_', '+/' ) . str_repeat( '=', ( 4 - strlen( $s ) % 4 ) % 4 ) ), $public, OPENSSL_ALGO_SHA256 ) );
        $this->assertNull( expStaticGitHub::appJwt( '1', 'not a key' ) );

        $this->answers['POST /app/installations/77/access_tokens'] = array( array( 201, array( 'token' => 'ghs_installation', 'expires_at' => '2030-01-01T00:00:00Z' ) ) );
        $problem = '';
        $this->assertSame( 'ghs_installation', expStaticGitHub::appToken( '77', array( 'app_id' => '4242', 'private_key' => $pem, 'http' => $this->http() ), $problem ) );
        $this->assertStringStartsWith( 'Bearer ey', $this->requests[0]['headers']['Authorization'] );
        $this->assertNull( expStaticGitHub::appToken( '78', array( 'app_id' => '4242', 'private_key' => $pem, 'http' => $this->http() ), $problem ) );
        $this->assertStringContainsString( 'gave no token for installation 78', $problem );
        $this->assertStringNotContainsString( 'ey', str_replace( 'key', '', $problem ), 'the JWT is never in a problem' );
        $this->assertNull( expStaticGitHub::appToken( 'abc', array(), $problem ) );
        $this->assertStringContainsString( 'not an installation id', $problem );
        expStaticGitHub::resetAppTokens();
        $this->assertNull( expStaticGitHub::appToken( '77', array( 'app_id' => '', 'private_key' => '' ), $problem ) );
        $this->assertStringContainsString( 'GitHubAppId', $problem );

        // Credential=app: uses the provider, and the token reaches git like a token, through the environment only
        expStaticCredentials::provider( 'app', function ( $installation ) { return $installation === '77' ? 'ghs_installation' : null; } );
        $c = new expStaticCredentials( 'app:77' );
        $this->assertSame( '', $c->problem() );
        $env = $c->gitEnvironment();
        $this->assertSame( 'ghs_installation', $env['env'][expStaticCredentials::ENV_TOKEN] );
        $this->assertStringNotContainsString( 'ghs_installation', implode( ' ', $env['options'] ) );
        $this->assertSame( 'ghs_installation', $c->apiToken() );
    }

    /** GH-09 */
    public function testDeviceFlow()
    {
        $this->answers['POST https://github.com/login/device/code'] = array( array( 200, array( 'device_code' => 'dc', 'user_code' => 'ABCD-1234',
                                                                                                'verification_uri' => 'https://github.com/login/device', 'interval' => 5, 'expires_in' => 900 ) ) );
        $this->answers['POST https://github.com/login/oauth/access_token'] = array( array( 200, array( 'error' => 'authorization_pending' ) ),
                                                                                     array( 200, array( 'error' => 'slow_down' ) ),
                                                                                     array( 200, array( 'access_token' => 'gho_user', 'token_type' => 'bearer' ) ) );
        $code = expStaticGitHub::deviceCode( 'Iv1.client', array( 'http' => $this->http() ) );
        $this->assertSame( array( 'dc', 'ABCD-1234', '' ), array( $code['device_code'], $code['user_code'], $code['error'] ) );
        $this->assertSame( 'repo workflow', $this->requests[0]['body']['scope'] );
        $this->assertArrayNotHasKey( 'Authorization', $this->requests[0]['headers'] );
        $this->assertSame( 'pending', expStaticGitHub::deviceToken( 'Iv1.client', 'dc', array( 'http' => $this->http() ) )['state'] );
        $this->assertSame( 'slow_down', expStaticGitHub::deviceToken( 'Iv1.client', 'dc', array( 'http' => $this->http() ) )['state'] );
        $this->assertSame( array( 'token' => 'gho_user', 'state' => 'done', 'error' => '' ), expStaticGitHub::deviceToken( 'Iv1.client', 'dc', array( 'http' => $this->http() ) ) );
    }

    private static function dns( array $records )
    {
        return new expStaticDnsCheck( array( 'resolver' => function ( $name, $type ) use ( $records ) {
            $out = array();
            foreach ( isset( $records[$name][$type] ) ? $records[$name][$type] : array() as $value )
                $out[] = $type === 'A' ? array( 'ip' => $value ) : ( $type === 'AAAA' ? array( 'ipv6' => $value ) : array( 'target' => $value ) );
            return $out;
        } ) );
    }

    /** GH-10 */
    public function testDnsCheck()
    {
        $good = self::dns( array( 'example.com' => array( 'A' => expStaticGitHub::PAGES_A, 'AAAA' => array( '2606:50c0:8000:0::153' ) ) ) )->check( 'example.com', 'octo-org' );
        $this->assertTrue( $good['ok'], json_encode( $good ) );
        $this->assertSame( 'apex', $good['kind'] );
        $this->assertSame( array(), $good['fixes'] );

        $bad = self::dns( array( 'example.com' => array( 'A' => array( '185.199.108.153', '203.0.113.9' ) ) ) )->check( 'Example.com.', 'octo-org' );
        $this->assertFalse( $bad['ok'] );
        $this->assertStringContainsString( '203.0.113.9', $bad['problems'][0] );
        $this->assertContains( 'example.com.  A  185.199.111.153', $bad['fixes'] );
        $this->assertContains( 'remove: example.com.  A  203.0.113.9', $bad['fixes'] );

        $none = self::dns( array() )->check( 'example.co.uk' );
        $this->assertSame( 'apex', $none['kind'] );
        $this->assertSame( 'example.co.uk has no A record', $none['problems'][0] );

        $sub = self::dns( array( 'www.example.com' => array( 'CNAME' => array( 'octo-org.github.io.' ) ) ) )->check( 'www.example.com', 'Octo-Org' );
        $this->assertTrue( $sub['ok'] );
        $this->assertSame( 'subdomain', $sub['kind'] );
        $wrong = self::dns( array( 'www.example.com' => array( 'CNAME' => array( 'octo-org.github.io.demo-site' ) ) ) )->check( 'www.example.com', 'octo-org' );
        $this->assertFalse( $wrong['ok'] );
        $this->assertSame( array( 'www.example.com.  CNAME  octo-org.github.io.' ), $wrong['fixes'] );
        $other = self::dns( array( 'docs.example.com' => array( 'CNAME' => array( 'someone.github.io' ) ) ) )->check( 'docs.example.com', 'octo-org' );
        $this->assertStringContainsString( 'never the repository', $other['problems'][0] );
        $missing = self::dns( array( 'docs.example.com' => array( 'A' => array( '203.0.113.9' ) ) ) )->check( 'docs.example.com' );
        $this->assertCount( 2, $missing['problems'] );
        $this->assertSame( array( 'docs.example.com.  CNAME  <owner>.github.io.' ), $missing['fixes'] );
        $this->assertSame( 'not a domain is not a host name (www.example.com)', self::dns( array() )->check( 'not a domain' )['problems'][0] );
    }

    /**
     * GH-11: the opt-in integration test against a real GitHub repository (the owner approved one under se7enxweb).
     * It reads the repository and its Pages site; it changes nothing. Set EXP_STATIC_GITHUB_TEST_REPO (owner/repo)
     * and the token: EXP_STATIC_GITHUB_TEST_TOKEN (a token that can read the repository), or, so the value never has
     * to be put into the environment, EXP_STATIC_GITHUB_TEST_SECRET with the name of a secret of the secret store
     * (run on an installation whose database holds it). The token is never printed.
     */
    public function testRealGitHubRepository()
    {
        $token = self::integrationToken();
        $repo = (string)getenv( 'EXP_STATIC_GITHUB_TEST_REPO' );
        if ( $token === '' || !preg_match( '#^([A-Za-z0-9-]+)/([A-Za-z0-9._-]+)$#', $repo, $m ) )
            $this->markTestSkipped( 'the opt-in GitHub integration test needs EXP_STATIC_GITHUB_TEST_REPO (owner/repo) and EXP_STATIC_GITHUB_TEST_TOKEN or EXP_STATIC_GITHUB_TEST_SECRET' );
        if ( !expStaticGitHub::available() )
            $this->markTestSkipped( expStaticGitHub::unavailable() );
        $client = new expStaticGitHub( $token, array( 'api' => expStaticGitHub::API ) );
        $r = $client->repository( $m[1], $m[2] );
        $this->assertSame( 200, $r['status'], $client->redact( $r['error'] ) );
        $status = $client->status( self::target( array( 'Remote' => "https://github.com/{$m[1]}/{$m[2]}.git" ) ) );
        $this->assertTrue( $status['repo']['exists'] );
        $this->assertStringNotContainsString( $token, json_encode( $status ) );
        if ( $status['pages']['enabled'] )
            $this->assertContains( $status['build']['status'], array( 'built', 'building', 'queued', 'errored', 'none' ) );
    }

    /**
     * The token of the opt-in tests: EXP_STATIC_GITHUB_TEST_TOKEN, else the secret named by
     * EXP_STATIC_GITHUB_TEST_SECRET, read from the secret store of the installation the tests run on.
     *
     * @return string '' when there is none
     */
    private static function integrationToken()
    {
        $token = trim( (string)getenv( 'EXP_STATIC_GITHUB_TEST_TOKEN' ) );
        $secret = trim( (string)getenv( 'EXP_STATIC_GITHUB_TEST_SECRET' ) );
        if ( $token !== '' || $secret === '' || !class_exists( 'expSecrets' ) )
            return $token;
        try
        {
            return trim( (string)expSecrets::get( $secret ) );
        }
        catch ( Throwable $e )
        {
            return '';
        }
    }

    /**
     * GH-13: the opt-in check of the published site of the real test repository: its GitHub Pages address is
     * downloaded the way a visitor would read it (the start page and every page, style sheet and file it links to
     * below the repository's base path, at most 200 files) and the link checker finds no broken link under /repo/.
     * Needs EXP_STATIC_GITHUB_TEST_REPO only: the site is public, no token is used.
     */
    public function testRealGitHubPagesSiteHasNoBrokenLinks()
    {
        $repo = (string)getenv( 'EXP_STATIC_GITHUB_TEST_REPO' );
        if ( !preg_match( '#^([A-Za-z0-9-]+)/([A-Za-z0-9._-]+)$#', $repo, $m ) )
            $this->markTestSkipped( 'the opt-in GitHub Pages check needs EXP_STATIC_GITHUB_TEST_REPO (owner/repo)' );
        if ( !function_exists( 'curl_init' ) )
            $this->markTestSkipped( 'the PHP extension curl is missing' );
        $base = rtrim( expStaticGitHub::pagesUrl( $m[1], $m[2] ), '/' ) . '/';
        $mount = expStaticGitHub::pagesBasePath( $m[1], $m[2] );
        $mount = $mount === '' ? '/' : $mount;
        $origin = substr( $base, 0, strlen( $base ) - strlen( $mount ) );
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/static-github-site-test-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
        mkdir( $this->dir, 0775, true );
        $queue = array( $mount );
        $seen = array( $mount => true );
        $fetched = 0;
        while ( $queue && $fetched < 200 )
        {
            $path = array_shift( $queue );
            list( $status, $body ) = self::fetchPublic( $origin . $path );
            $this->assertSame( 200, $status, "the public address $origin$path" );
            $fetched++;
            $relative = rawurldecode( ltrim( substr( $path, strlen( rtrim( $mount, '/' ) ) ), '/' ) );
            if ( $relative === '' || substr( $relative, -1 ) === '/' )
                $relative .= 'index.html';
            $file = $this->dir . '/' . $relative;
            if ( !is_dir( dirname( $file ) ) )
                mkdir( dirname( $file ), 0775, true );
            file_put_contents( $file, $body );
            $extension = strtolower( (string)pathinfo( $relative, PATHINFO_EXTENSION ) );
            if ( $extension !== 'html' && $extension !== 'css' )
                continue;
            $folder = substr( $path, 0, strrpos( $path, '/' ) + 1 );
            $refs = $extension === 'css' ? expStaticLinkChecker::cssReferences( $body ) : expStaticLinkChecker::htmlReferences( $body );
            foreach ( $refs as $ref )
            {
                $resolved = expStaticLinkChecker::resolve( $ref, $folder );
                if ( $resolved === null || isset( $seen[$resolved] ) || strpos( $resolved, $mount ) !== 0 )
                    continue;
                if ( substr( $resolved, -1 ) !== '/' && pathinfo( $resolved, PATHINFO_EXTENSION ) === '' )
                    $resolved .= '/'; // a folder without its slash: GitHub Pages redirects to it
                if ( isset( $seen[$resolved] ) )
                    continue;
                $seen[$resolved] = true;
                $queue[] = $resolved;
            }
        }
        list( $status ) = self::fetchPublic( $origin . $mount . '404.html' );
        if ( $status === 200 )
        {
            list( , $body ) = self::fetchPublic( $origin . $mount . '404.html' );
            file_put_contents( $this->dir . '/404.html', $body );
        }
        $result = expStaticLinkChecker::check( $this->dir, $mount );
        $this->assertGreaterThan( 0, $result['files'] );
        $this->assertSame( 0, $result['broken_count'], json_encode( $result['broken'] ) );
    }

    /** @return array status and body of a public https address (no token, no redirects followed) */
    private static function fetchPublic( $url )
    {
        $ch = curl_init( $url );
        curl_setopt_array( $ch, array( CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30, CURLOPT_CONNECTTIMEOUT => 10,
                                       CURLOPT_PROTOCOLS => CURLPROTO_HTTPS, CURLOPT_USERAGENT => 'Exponential static publishing test' ) );
        $body = curl_exec( $ch );
        $status = (int)curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        if ( PHP_VERSION_ID < 80000 ) curl_close( $ch ); // no effect since PHP 8.0, deprecated in 8.5
        return array( $status, is_string( $body ) ? $body : '' );
    }
}
