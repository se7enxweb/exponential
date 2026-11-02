<?php
/**
 * The administration of static publishing (Setup > Static publishing, module staticpublish) without a database: the
 * jobs started in the background (expStaticJob), their progress (expStaticProgress), the logic of the views
 * (expStaticAdmin) and the module with its settings.
 *
 *  SA-01 a job's command line: build (--full), publish (--message), rollback (the tag, --apply), always --format=json
 *        --login --run-id --progress; refused arguments (an unknown kind, an id that is no run id, a target that is no
 *        id, no user, a rollback without a tag or with one that is no tag)
 *  SA-02 jobs read back: running or ended, the report of the output, the newest first, the last build, per target;
 *        start() refuses an unknown kind, a target that is no id, no user, a target whose lock another process holds
 *  SA-03 the progress file: one JSON line per event, read in whole lines from an offset, folded (pages, phase,
 *        warnings, errors, the end and its exit code), the meter's steps
 *  SA-04 the policies: yes, no, limited to targets (a policy without the limitation allows every target); a new
 *        target needs setup without the limitation
 *  SA-05 the wizard: its form into settings (lists from lines or commas, the credential from its kind), the
 *        credential back into its choice, the checks (a free id, the deploy key needs SSH, a token needs https), the
 *        live link example (relative and below a base path), the lines it writes (a block with reset lists, the id in
 *        TargetList[], Enabled) through expIniEditor into a scratch scope
 *  SA-06 the preview: the diff with sizes against the last publish (and before the first one), the limits (ok, warn
 *        above 90 %, bad above the site or a file limit), the signed preview link (its user, its target, an hour)
 *  SA-07 the module: views, policies and the policy of each view, the Target limitation, the menu entry, "Verify it's
 *        you" for publish, rollback, save, the key and the switch and never for seeing or building, the dashboard
 *        block and its fetch function, the context help of every view, the two designs' templates and scripts the
 *        same, the link box on Setup > Cache, the manual
 *  SA-08 the dashboard and the history: the version number from the tag static/<target>/<n> (0 without one), the
 *        short commit id, the commit link on GitHub, the deploy state of the last run (pushed, pending, failed,
 *        unchanged, not pushed) and a run as the history shows it
 *  SA-09 several siteaccesses in the wizard: the ticked ones into SiteAccess (one) or SiteAccessList[] with
 *        DefaultSiteAccess (several), the review form posted back keeps them, the lines written, the list shown
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expStaticAdminTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private static $root;

    /** @var string */
    private $dir;

    const ID = '20261101-120000-a1b2c3';

    const CONTEXT = array( 'siteaccesses' => array( 'site', 'admin' ), 'admin' => array( 'admin' ), 'urls' => array( 'site' => 'www.example.com', 'admin' => 'admin.example.com' ) );

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
    }

    protected function setUp(): void
    {
        $this->dir = self::$root . '/var/tmp/static-admin-test-' . getmypid();
        if ( !is_dir( $this->dir ) )
            mkdir( $this->dir, 0775, true );
    }

    protected function tearDown(): void
    {
        expStaticAdmin::setKeyForTest( null );
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->dir, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
        foreach ( $it as $file )
            $file->isDir() ? rmdir( (string)$file ) : unlink( (string)$file );
        rmdir( $this->dir );
    }

    /** SA-01 */
    public function testArguments()
    {
        $this->assertSame( array( 'bin/php/expstaticbuild.php', 'github-public', '--full', '--format=json', '--login=editor', '--siteaccess=admin',
                                  '--run-id=' . self::ID, '--progress=/w/.jobs/x.progress' ),
                           expStaticJob::arguments( 'build', self::ID, 'github-public', array( 'message' => 'not for a build' ), 'editor', 'admin', '/w/.jobs/x.progress' ) );
        $this->assertSame( array( 'bin/php/expstaticpublish.php', 'github-public', '--format=json', '--allow-root-user', '--login=editor', '--run-id=' . self::ID,
                                  '--progress=p', '--message=New opening hours' ),
                           expStaticJob::arguments( 'publish', self::ID, 'github-public', array( 'message' => " New opening\x01 hours " ), 'editor', '', 'p', true ) );
        $this->assertSame( array( 'bin/php/expstaticrollback.php', 'github-public', 'static/github-public/2', '--apply', '--format=json', '--login=editor',
                                  '--run-id=' . self::ID, '--progress=p' ),
                           expStaticJob::arguments( 'rollback', self::ID, 'github-public', array( 'to' => 'static/github-public/2' ), 'editor', '', 'p' ) );
        $this->assertContains( '2', expStaticJob::arguments( 'rollback', self::ID, 't', array( 'to' => '2' ), 'editor', '', 'p' ) );
        // a publish of some nodes (the website sidebar's "Publish this page"): an incremental build of them first
        $this->assertSame( array( 'bin/php/expstaticpublish.php', 't', '--build', '--nodes=57,2', '--format=json', '--login=editor', '--run-id=' . self::ID, '--progress=p' ),
                           expStaticJob::arguments( 'publish', self::ID, 't', array( expStaticJob::NODES => array( '57', 2, 57 ) ), 'editor', '', 'p' ) );
        $this->assertNotContains( '--nodes=57', expStaticJob::arguments( 'build', self::ID, 't', array( expStaticJob::NODES => array( 57 ) ), 'editor', '', 'p' ) );
        foreach ( array( array( 'deploy', self::ID, 't', array() ), array( 'publish', self::ID, 't', array( 'nodes' => array( '1;rm' ) ) ),
                         array( 'publish', self::ID, 't', array( 'nodes' => array( 0 ) ) ),array( 'build', 'not-an-id', 't', array() ), array( 'build', self::ID, '--all', array() ),
                         array( 'build', self::ID, 'Bad Id', array() ), array( 'rollback', self::ID, 't', array() ),
                         array( 'rollback', self::ID, 't', array( 'to' => '--force' ) ), array( 'rollback', self::ID, 't', array( 'to' => 'v1.0' ) ) ) as $case )
        {
            try
            {
                expStaticJob::arguments( $case[0], $case[1], $case[2], $case[3], 'editor', '', 'p' );
                $this->fail( 'refused: ' . json_encode( $case ) );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertNotSame( '', $e->getMessage() );
            }
        }
        $this->expectException( InvalidArgumentException::class );
        expStaticJob::arguments( 'build', self::ID, 't', array(), '', '', 'p' );
    }

    /** SA-02 */
    public function testJobsReadBack()
    {
        $jobs = expStaticJob::directory( $this->dir );
        $this->assertSame( $this->dir . '/.jobs', $jobs );
        $write = function ( $id, $kind, $target, $out ) use ( $jobs ) {
            file_put_contents( "$jobs/$id.json", json_encode( array( 'id' => $id, 'kind' => $kind, 'target' => $target, 'user' => 'editor', 'pid' => 0,
                                                                     'started' => 1, 'progress' => "$jobs/$id.progress" ) ) );
            file_put_contents( "$jobs/$id.out", $out );
        };
        $write( '20261101-100000-aaaaaa', 'build', 'one', "With great power\n{\n    \"schema\": \"exp-static/1\",\n    \"exit\": 0\n}\n" );
        $write( '20261101-110000-bbbbbb', 'publish', 'one', "{\n    \"schema\": \"exp-static/1\",\n    \"exit\": 1\n}\n" );
        $write( '20261101-120000-cccccc', 'build', 'two', 'Fatal error: no report' );
        file_put_contents( "$jobs/not-a-job.json", '{}' );
        $all = expStaticJob::all( $this->dir );
        $this->assertSame( array( '20261101-120000-cccccc', '20261101-110000-bbbbbb', '20261101-100000-aaaaaa' ), array_column( $all, 'id' ) );
        $this->assertSame( array( '20261101-110000-bbbbbb', '20261101-100000-aaaaaa' ), array_column( expStaticJob::all( $this->dir, 'one' ), 'id' ) );
        $this->assertSame( 1, $all[1]['exit'] );
        $this->assertTrue( $all[1]['ended'] );
        $this->assertFalse( $all[1]['running'] );
        $this->assertNull( $all[0]['report'] );
        $this->assertStringContainsString( 'Fatal error', $all[0]['errors'] );
        $this->assertSame( '20261101-100000-aaaaaa', expStaticJob::lastBuild( $this->dir, 'one' )['id'] );
        $this->assertNull( expStaticJob::running( $this->dir, 'one' ) );
        $this->assertNull( expStaticJob::get( '../x', $this->dir ) );
        $this->assertNull( expStaticJob::get( '20261101-130000-dddddd', $this->dir ) );

        $settings = array( 'work_dir' => $this->dir, 'php' => '/bin/false', 'wait' => 0.1 );
        $this->assertSame( expStaticJob::REFUSED_KIND, expStaticJob::start( 'deploy', 'one', array(), 'editor', $settings )['refused'] );
        $this->assertSame( expStaticJob::REFUSED_TARGET, expStaticJob::start( 'build', '../one', array(), 'editor', $settings )['refused'] );
        $this->assertSame( expStaticJob::REFUSED_USER, expStaticJob::start( 'build', 'one', array(), '', $settings )['refused'] );
        $problem = '';
        $lock = expStaticLock::acquire( $this->dir, 'one', 'console build', $problem );
        $this->assertNotNull( $lock );
        // flock() is per open file description: a second acquire in this process is refused as another process's
        $refused = expStaticJob::start( 'build', 'one', array(), 'editor', $settings );
        $this->assertSame( expStaticJob::REFUSED_LOCKED, $refused['refused'] );
        $this->assertStringContainsString( 'console build', $refused['detail'] );
        $lock->release();
        $this->assertCount( 3, expStaticJob::all( $this->dir ), 'a refused start writes no job' );
    }

    /** SA-03 */
    public function testProgress()
    {
        $this->assertSame( "{\"time\":5,\"event\":\"ok\",\"message\":\"index.html 2 KB\"}\n", expStaticProgress::line( 'ok', 'index.html 2 KB', 5 ) );
        $this->assertSame( '', expStaticProgress::line( 'Bad Event', 'x', 5 ) );
        $file = $this->dir . '/p.progress';
        $emit = expStaticProgress::writer( $file, 7 );
        $emit( 'phase', 'Static publishing: one (site)' );
        $emit( 'ok', 'index.html' );
        $emit( 'ok', 'about-us/index.html', array( 'ignored' => true ) );
        $emit( 'warn', 'a warning' );
        $first = expStaticProgress::read( $file, 0 );
        $this->assertCount( 4, $first['events'] );
        $this->assertSame( filesize( $file ), $first['offset'] );
        file_put_contents( $file, '{"time":8,"event":"err', FILE_APPEND );
        $this->assertSame( array(), expStaticProgress::read( $file, $first['offset'] )['events'], 'half a line waits' );
        file_put_contents( $file, "or\",\"message\":\"broken\"}\n" . expStaticProgress::line( 'end', '1', 9 ), FILE_APPEND );
        $rest = expStaticProgress::read( $file, $first['offset'] );
        $this->assertSame( array( 'error', 'end' ), array_column( $rest['events'], 'event' ) );

        $state = expStaticProgress::fold( $first['events'], null, 4 );
        $this->assertSame( 2, $state['pages'] );
        $this->assertSame( 'Static publishing: one (site)', $state['phase'] );
        $this->assertSame( 1, $state['warnings'] );
        $this->assertSame( 45, $state['percent'] );
        $this->assertFalse( $state['ended'] );
        $state = expStaticProgress::fold( $rest['events'], $state, 4 );
        $this->assertTrue( $state['ended'] );
        $this->assertSame( 1, $state['exit'] );
        $this->assertSame( 1, $state['errors'] );
        $this->assertSame( 100, $state['percent'] );
        $this->assertLessThan( 90, expStaticProgress::fold( array_fill( 0, 500, array( 'event' => 'ok' ) ) )['percent'], 'without an expectation it never claims the end' );
        $this->assertSame( array( 0, 0, 44, 96, 100 ), array_map( array( 'expStaticProgress', 'meterStep' ), array( -5, 3, 47, 99, 140 ) ) );
    }

    /** SA-04 */
    public function testPolicies()
    {
        $this->assertTrue( expStaticAdmin::allows( array( 'accessWord' => 'yes' ), 'one' ) );
        $this->assertFalse( expStaticAdmin::allows( array( 'accessWord' => 'no' ), 'one' ) );
        $this->assertFalse( expStaticAdmin::allows( array(), null ) );
        $limited = array( 'accessWord' => 'limited', 'policies' => array( 'p1' => array( 'Target' => array( 'one', 'three' ) ) ) );
        $this->assertTrue( expStaticAdmin::allows( $limited, 'one' ) );
        $this->assertFalse( expStaticAdmin::allows( $limited, 'two' ) );
        $this->assertTrue( expStaticAdmin::allows( $limited, null ), 'some target' );
        $limited['policies']['p2'] = array();
        $this->assertTrue( expStaticAdmin::allows( $limited, 'two' ), 'a policy without the limitation allows every target' );
    }

    /** SA-08: the version number, the short commit and the deploy state of the dashboard and the history */
    public function testVersionHashAndDeployState()
    {
        $this->assertSame( 3, expStaticAdmin::versionNumber( 'static/demo-site-static/3', 'demo-site-static' ) );
        $this->assertSame( 12, expStaticAdmin::versionNumber( ' static/one/12 ', 'one' ) );
        $this->assertSame( 0, expStaticAdmin::versionNumber( 'static/other/3', 'one' ), 'a tag of another target' );
        $this->assertSame( 0, expStaticAdmin::versionNumber( 'static/one/x', 'one' ) );
        $this->assertSame( 0, expStaticAdmin::versionNumber( '', 'one' ), 'a run without a tag' );
        $this->assertSame( 0, expStaticAdmin::versionNumber( '3', 'one' ), 'a bare number is no tag' );

        $this->assertSame( '0aa2c3f', expStaticAdmin::shortHash( '0AA2C3F9b1d2e3f4a5b6c7d8e9f00112233445566' ) );
        $this->assertSame( '0aa2c3f9b1', expStaticAdmin::shortHash( '0aa2c3f9b1d2e3f4a5b6c7d8e9f00112233445566', 10 ) );
        $this->assertSame( '', expStaticAdmin::shortHash( '' ) );
        $this->assertSame( '', expStaticAdmin::shortHash( 'not-a-commit' ) );

        $this->assertSame( 'https://github.com/owner/site/commit/', expStaticAdmin::commitUrl( 'ssh://git@github.com/owner/site.git' ) );
        $this->assertSame( '', expStaticAdmin::commitUrl( 'https://gitlab.com/owner/site.git' ) );

        $this->assertSame( array( 'state' => 'none', 'time' => 0, 'run_id' => '' ), expStaticAdmin::deployState( null ) );
        $run = array( 'run_id' => 'r1', 'kind' => 'publish', 'state' => 'done', 'push_state' => 'pushed', 'started_at' => 100, 'finished_at' => 160 );
        $this->assertSame( array( 'state' => 'pushed', 'time' => 160, 'run_id' => 'r1' ), expStaticAdmin::deployState( $run ) );
        $this->assertSame( 'pending', expStaticAdmin::deployState( array( 'push_state' => 'pending' ) + $run )['state'] );
        $this->assertSame( 'failed', expStaticAdmin::deployState( array( 'state' => 'failed', 'push_state' => 'pending' ) + $run )['state'], 'a failed run is failed' );
        $this->assertSame( 'unchanged', expStaticAdmin::deployState( array( 'state' => 'unchanged', 'push_state' => 'none' ) + $run )['state'] );
        foreach ( array( 'disabled', 'no-remote', 'none', '' ) as $push )
            $this->assertSame( 'not-pushed', expStaticAdmin::deployState( array( 'push_state' => $push ) + $run )['state'], "push state '$push'" );
        $this->assertSame( 100, expStaticAdmin::deployState( array( 'finished_at' => 0 ) + $run )['time'], 'a run not ended: when it began' );

        $view = expStaticAdmin::runView( array( 'tag_name' => 'static/one/4', 'commit_id' => 'ABCDEF0123456789' ) + $run, 'one' );
        $this->assertSame( 4, $view['version'] );
        $this->assertSame( 'abcdef0', $view['commit_short'] );
        $view = expStaticAdmin::runView( array( 'tag_name' => '', 'commit_id' => '' ) + $run, 'one' );
        $this->assertSame( array( 0, '' ), array( $view['version'], $view['commit_short'] ) );
    }

    /** SA-05 */
    public function testWizard()
    {
        $post = array( 'Target' => array( 'Name' => " Public\n site ", 'SiteAccess' => 'site', 'Kind' => 'github-pages', 'Remote' => 'git@github.com:o/r.git',
                                          'Branch' => 'gh-pages', 'Links' => 'relative', 'BasePath' => '/', 'Extras' => array( 'nojekyll', '', 'nojekyll', '404' ),
                                          'ExcludePaths' => "/members/*\r\n\r\n/intern", 'ExcludeSections' => '3, 7', 'Triggers' => array( 'manual', 'cli' ),
                                          'Credential' => 'app:12' ),
                       'CredentialKind' => 'sshkey' );
        $s = expStaticAdmin::settingsFromPost( $post, 'github-public' );
        $this->assertSame( 'Public site', $s['Name'] );
        $this->assertSame( array( 'nojekyll', '404' ), $s['Extras'] );
        $this->assertSame( array( '/members/*', '/intern' ), $s['ExcludePaths'] );
        $this->assertSame( array( '3', '7' ), $s['ExcludeSections'] );
        $this->assertSame( 'sshkey:staticpublish.github-public.deploy_key', $s['Credential'] );
        $this->assertSame( '', $s['BasePath'], 'relative links need no base path' );
        $this->assertArrayNotHasKey( 'Notify', $s, 'what the form does not send keeps its value' );
        $this->assertSame( 'app:12', expStaticAdmin::settingsFromPost( array( 'Target' => array( 'Credential' => 'app:12' ), 'CredentialKind' => '' ), 'x' )['Credential'] );
        $this->assertSame( 'token:gh_pages', expStaticAdmin::settingsFromPost( array( 'CredentialKind' => 'token', 'CredentialToken' => ' gh_pages ' ), 'x' )['Credential'] );
        $this->assertSame( array( 'kind' => 'token', 'token' => 'env:GH_TOKEN', 'app' => '' ), expStaticAdmin::credentialChoice( 'token:env:GH_TOKEN' ) );
        $this->assertSame( array( 'kind' => 'none', 'token' => '', 'app' => '' ), expStaticAdmin::credentialChoice( 'magic:x' ) );
        $this->assertSame( array( 'kind' => 'app', 'token' => '', 'app' => '4711' ), expStaticAdmin::credentialChoice( 'app:4711' ) );
        $this->assertSame( 'app:4711', expStaticAdmin::settingsFromPost( array( 'CredentialKind' => 'app', 'CredentialApp' => ' 47-11 ' ), 'gh' )['Credential'] );

        $ok = expStaticAdmin::check( 'github-public', $s + expStaticTarget::DEFAULTS, self::CONTEXT, array(), true );
        $this->assertSame( array(), $ok['errors'] );
        $taken = expStaticAdmin::check( 'github-public', $s + expStaticTarget::DEFAULTS, self::CONTEXT, array( 'github-public' ), true );
        $this->assertCount( 1, $taken['errors'] );
        $https = expStaticAdmin::check( 'x', array( 'Remote' => 'https://github.com/o/r.git' ) + $s + expStaticTarget::DEFAULTS, self::CONTEXT, array(), true );
        $this->assertStringContainsString( 'SSH', implode( ' ', $https['errors'] ) );
        $token = expStaticAdmin::check( 'x', array( 'Credential' => 'token:t' ) + $s + expStaticTarget::DEFAULTS, self::CONTEXT, array(), true );
        $this->assertStringContainsString( 'https://', implode( ' ', $token['errors'] ) );
        $admin = expStaticAdmin::check( 'x', array( 'SiteAccess' => 'admin' ) + $s + expStaticTarget::DEFAULTS, self::CONTEXT, array(), true );
        $this->assertNotEmpty( $admin['errors'], 'an administration siteaccess is refused' );
        $this->assertSame( 'site', expStaticAdmin::newSettings( self::CONTEXT )['SiteAccess'] );

        $this->assertSame( '../about-us/team/', expStaticAdmin::linkExample( 'relative', '', '/news/', '/about-us/team' ) );
        $this->assertSame( 'about-us/', expStaticAdmin::linkExample( 'relative', '', '/', '/about-us' ) );
        $this->assertSame( '/my-site/about-us/team/', expStaticAdmin::linkExample( 'base-path', 'my-site', '/news/', '/about-us/team' ) );

        // the lines written, into a scratch settings folder
        mkdir( $this->dir . '/settings/override', 0775, true );
        $scope = new expIniScope( 'global', expIniScope::KIND_GLOBAL, 'settings/override', $this->dir . '/', 'Global override (settings/override)' );
        $editor = expStaticAdmin::editor( 'github-public', $s, true, $scope );
        $content = $editor->content();
        $this->assertStringContainsString( "[Target_github-public]\nName=Public site\n", $content );
        $this->assertStringContainsString( "Extras[]\nExtras[]=nojekyll\nExtras[]=404\n", $content );
        $this->assertStringContainsString( "Credential=sshkey:staticpublish.github-public.deploy_key\n", $content );
        $this->assertStringContainsString( "TargetList[]=github-public\n", $content );
        $this->assertStringContainsString( "Enabled=enabled\n", $content );
        $this->assertStringNotContainsString( 'app:12', $content );
    }

    /** SA-06 */
    public function testPreview()
    {
        $old = new expStaticManifest();
        $old->add( 'index.html', 'page', 'a', 100 );
        $old->add( 'about-us/index.html', 'page', 'b', 200 );
        $old->add( 'robots.txt', 'extra', 'c', 30 );
        $new = new expStaticManifest();
        $new->add( 'index.html', 'page', 'a', 100 );
        $new->add( 'about-us/index.html', 'page', 'b2', 250 );
        $new->add( 'news/index.html', 'page', 'd', 400 );
        $d = expStaticAdmin::diffDetails( $old, $new );
        $this->assertSame( array( 'added' => 1, 'changed' => 1, 'removed' => 1, 'unchanged' => 1 ), $d['counts'] );
        $this->assertSame( array( array( 'path' => 'about-us/index.html', 'kind' => 'page', 'size' => 250, 'old_size' => 200 ) ), $d['changed'] );
        $this->assertSame( array( array( 'path' => 'robots.txt', 'kind' => 'extra', 'size' => 0, 'old_size' => 30 ) ), $d['removed'] );
        $this->assertSame( 750, $d['bytes'] );
        $first = expStaticAdmin::diffDetails( null, $new, 2 );
        $this->assertSame( 3, $first['counts']['added'] );
        $this->assertCount( 2, $first['added'] );
        $this->assertSame( 1, $first['more']['added'] );
        $this->assertSame( 0, expStaticAdmin::diffDetails( $old, null )['counts']['added'] );

        $this->assertSame( 'ok', expStaticAdmin::limits( $new, 1000, 500 )['state'] );
        $warn = expStaticAdmin::limits( $new, 800, 500 );
        $this->assertSame( 'warn', $warn['state'] );
        $this->assertSame( 93, $warn['percent'] );
        $this->assertSame( 92, $warn['meter'] );
        $this->assertSame( 'bad', expStaticAdmin::limits( $new, 700, 500 )['state'] );
        $file = expStaticAdmin::limits( $new, 1000, 300 );
        $this->assertSame( 'bad', $file['state'] );
        $this->assertSame( array( array( 'path' => 'news/index.html', 'size' => 400 ) ), $file['too_large'] );
        $this->assertSame( array( 'path' => 'news/index.html', 'size' => 400 ), $file['largest'] );

        expStaticAdmin::setKeyForTest( str_repeat( 'k', 32 ) );
        $path = expStaticAdmin::previewPath( 'one', 14, 1000 );
        $this->assertMatchesRegularExpression( '#^staticpublish/serve/one/4600/[A-Za-z0-9_-]{43}/$#', $path );
        $signature = explode( '/', $path )[4];
        $this->assertTrue( expStaticAdmin::previewValid( 'one', 14, '4600', $signature, 1000 ) );
        $this->assertFalse( expStaticAdmin::previewValid( 'one', 15, '4600', $signature, 1000 ), 'another user' );
        $this->assertFalse( expStaticAdmin::previewValid( 'two', 14, '4600', $signature, 1000 ), 'another target' );
        $this->assertFalse( expStaticAdmin::previewValid( 'one', 14, '4600', $signature, 4601 ), 'expired' );
        $this->assertFalse( expStaticAdmin::previewValid( 'one', 14, '9600', $signature, 1000 ), 'longer than an hour' );
        $this->assertFalse( expStaticAdmin::previewValid( 'one', 14, '46x0', $signature, 1000 ) );
    }

    /** SA-07 */
    public function testTheModule()
    {
        $Module = $ViewList = $FunctionList = null;
        include self::$root . '/kernel/staticpublish/module.php';
        $this->assertSame( array( 'read', 'build', 'publish', 'setup' ), array_keys( $FunctionList ) );
        foreach ( $FunctionList as $function => $limitations )
            $this->assertSame( array( 'class' => 'expStaticAdmin', 'function' => 'limitationList' ),
                               array( 'class' => $limitations['Target']['class'], 'function' => $limitations['Target']['function'] ), "$function has the Target limitation" );
        $policies = array();
        foreach ( $ViewList as $view => $def )
        {
            $policies[$view] = isset( $def['functions'] ) ? $def['functions'] : array();
            $this->assertFileExists( self::$root . '/kernel/staticpublish/' . $def['script'] );
            if ( $view !== 'form' ) // the form endpoint is no page of the administration
                $this->assertSame( 'ezsetupnavigationpart', $def['default_navigation_part'] );
            $this->assertTrue( class_exists( 'Exponential\\View\\Kernel\\Staticpublish\\' . ucfirst( $view ) ), "the class of $view" );
        }
        $this->assertSame( array( 'dashboard' => array( 'read' ), 'target' => array( 'setup' ), 'build' => array( 'build' ), 'stream' => array( 'read' ),
                                  'preview' => array( 'read' ), 'serve' => array( 'read' ), 'history' => array( 'read' ), 'log' => array( 'read' ),
                                  'keys' => array( 'setup' ), 'form' => array() ), $policies );
        $this->assertMatchesRegularExpression( '/^PolicyOmitList\[\]=staticpublish\/form$/m', file_get_contents( self::$root . '/settings/site.ini' ), 'anyone may post to the form endpoint' );
        $this->assertMatchesRegularExpression( '/^ModuleList\[\]=staticpublish$/m', file_get_contents( self::$root . '/settings/module.ini' ) );
        $menu = self::section( 'settings/menu.ini', 'Leftmenu_setup' );
        foreach ( array( 'Links[static_publishing]=staticpublish/dashboard', 'PolicyList_static_publishing[]=staticpublish/read',
                         'GroupLinks_system[]=static_publishing', 'LinkNames[static_publishing]=Static publishing' ) as $line )
            $this->assertContains( $line, $menu );

        $rules = array();
        foreach ( self::section( 'settings/site.ini', 'ReauthGroup_system' ) as $line )
            if ( strpos( $line, 'Rules[]=' ) === 0 )
                $rules[] = substr( $line, 8 );
        $settings = array( 'enabled' => true, 'groups' => array( 'system' => array( 'enabled' => true, 'rules' => $rules, 'user_groups' => array() ) ) );
        $asks = function ( $view, array $post, $method = 'POST' ) use ( $settings ) {
            return expReauth::matchGroup( $settings, 'staticpublish', $view, $method, $post ) !== null;
        };
        $this->assertTrue( $asks( 'preview', array( 'PublishButton' => '1' ) ), 'a publish' );
        $this->assertTrue( $asks( 'history', array( 'RollbackButton' => '1' ) ), 'a rollback' );
        $this->assertTrue( $asks( 'target', array( 'SaveButton' => '1' ) ), 'saving a target' );
        $this->assertTrue( $asks( 'keys', array( 'GenerateButton' => '1' ) ), 'a deploy key' );
        $this->assertTrue( $asks( 'dashboard', array( 'SwitchButton' => '1' ) ), 'the switch' );
        $this->assertFalse( $asks( 'target', array( 'ReviewButton' => '1' ) ), 'the review' );
        $this->assertFalse( $asks( 'build', array( 'BuildButton' => '1' ) ), 'a build' );
        $this->assertFalse( $asks( 'preview', array( 'DiscardButton' => '1' ) ), 'throwing a build away' );
        foreach ( array_keys( $ViewList ) as $view )
            $this->assertFalse( $asks( $view, array(), 'GET' ), "seeing $view" );

        $this->assertContains( 'DashboardBlocks[]=staticpublish', self::section( 'settings/dashboard.ini', 'DashboardSettings' ) );
        $block = self::section( 'settings/dashboard.ini', 'DashboardBlock_staticpublish' );
        $this->assertContains( 'PolicyList[]=staticpublish/read', $block );
        $FunctionList = null;
        include self::$root . '/kernel/staticpublish/function_definition.php';
        $this->assertSame( array( 'class' => 'expStaticAdmin', 'method' => 'fetchSummary' ), $FunctionList['summary']['call_method'] );
        $this->assertStringContainsString( "ezpreference( 'staticpublish_invite' )", file_get_contents( self::$root . '/design/admin4/templates/dashboard/staticpublish.tpl' ) );

        $help = self::section( 'settings/exphelp.ini', 'ContextHelp' );
        foreach ( array_keys( $ViewList ) as $view )
        {
            $found = preg_grep( '#^Map\[staticpublish/' . $view . '\]=#', $help );
            $this->assertCount( 1, $found, "context help of staticpublish/$view" );
            $page = preg_replace( '/#.*$/', '', substr( reset( $found ), strpos( reset( $found ), '=' ) + 1 ) );
            $this->assertFileExists( self::$root . '/doc/eng-us/' . $page );
        }
        $this->assertContains( 'Manuals[]=static-publishing;help/static-publishing', self::section( 'settings/exphelp.ini', 'ManualSettings' ) );
        $this->assertContains( 'JobHistory=50', self::section( 'settings/staticcache.ini', 'StaticPublishSettings' ) );

        foreach ( glob( self::$root . '/design/admin4/templates/staticpublish/{,parts/}*.tpl', GLOB_BRACE ) as $file )
            $this->assertFileEquals( $file, str_replace( '/design/admin4/', '/design/admin/', $file ), 'design/admin has the same template' );
        $this->assertFileEquals( self::$root . '/design/admin4/templates/dashboard/staticpublish.tpl', self::$root . '/design/admin/templates/dashboard/staticpublish.tpl' );
        foreach ( array( 'staticpublish_job.js', 'staticpublish_wizard.js' ) as $script )
            $this->assertFileEquals( self::$root . '/design/admin4/javascript/page/' . $script, self::$root . '/design/admin/javascript/page/' . $script );
        foreach ( array( 'admin', 'admin4' ) as $design )
            $this->assertStringContainsString( "'staticpublish/dashboard'|ezurl", file_get_contents( self::$root . "/design/$design/templates/setup/cache.tpl" ), "the link on Setup > Cache in $design" );
        $this->assertSame( 'https://github.com/owner/site/commit/', \Exponential\View\Kernel\Staticpublish\History::commitUrl( 'git@github.com:owner/site.git' ) );
        $this->assertSame( 'https://github.com/owner/site/commit/', \Exponential\View\Kernel\Staticpublish\History::commitUrl( 'https://github.com/owner/site' ) );
        $this->assertSame( '', \Exponential\View\Kernel\Staticpublish\History::commitUrl( '/srv/git/site.git' ) );
    }

    /**
     * The lines of a section of a shipped settings file, without comments.
     *
     * @param string $file
     * @param string $section
     * @return string[]
     */
    private static function section( $file, $section )
    {
        $lines = array();
        $in = false;
        foreach ( file( self::$root . '/' . $file, FILE_IGNORE_NEW_LINES ) as $line )
        {
            if ( preg_match( '/^\[(.+)\]\s*$/', $line, $m ) )
                $in = $m[1] === $section;
            else if ( $in && $line !== '' && $line[0] !== '#' )
                $lines[] = rtrim( $line );
        }
        return $lines;
    }

    /** SA-09 */
    public function testSeveralSiteAccessesInTheWizard()
    {
        // one ticked: written as SiteAccess, as a target of one always was
        $one = expStaticAdmin::settingsFromPost( array( 'Target' => array( 'SiteAccessList' => array( '', 'site' ), 'DefaultSiteAccess' => '' ) ), 'x' );
        $this->assertSame( array( 'SiteAccess' => 'site', 'SiteAccessList' => array(), 'DefaultSiteAccess' => '' ),
                           array_intersect_key( $one, array_flip( array( 'SiteAccess', 'SiteAccessList', 'DefaultSiteAccess' ) ) ) );
        // several, with the one at the root
        $post = array( 'Target' => array( 'SiteAccessList' => array( '', 'site', 'bold', 'bold_ger', 'bold' ), 'DefaultSiteAccess' => 'bold' ) );
        $several = expStaticAdmin::settingsFromPost( $post, 'x' );
        $this->assertSame( '', $several['SiteAccess'] );
        $this->assertSame( array( 'site', 'bold', 'bold_ger' ), $several['SiteAccessList'] );
        $this->assertSame( 'bold', $several['DefaultSiteAccess'] );
        $this->assertSame( '', expStaticAdmin::settingsFromPost( array( 'Target' => array( 'SiteAccessList' => array( 'site', 'bold' ), 'DefaultSiteAccess' => 'classic' ) ), 'x' )['DefaultSiteAccess'],
                           'a default that is not ticked is none' );
        // the review form posts every setting back: SiteAccess=site with an empty list stays site
        $back = expStaticAdmin::settingsFromPost( array( 'Target' => array( 'SiteAccess' => 'site', 'SiteAccessList' => array( '' ), 'DefaultSiteAccess' => '' ) ), 'x' );
        $this->assertSame( 'site', $back['SiteAccess'] );
        $this->assertSame( array(), expStaticAdmin::settingsFromPost( array( 'Target' => array( 'SiteAccessList' => array( '' ) ) ), 'x' )['SiteAccessList'] );

        $context = array( 'siteaccesses' => array( 'site', 'bold', 'bold_ger', 'admin' ), 'admin' => array( 'admin' ),
                          'urls' => array( 'site' => 'www.example.com', 'bold' => 'www.example.com/bold', 'bold_ger' => 'www.example.com/bold_ger', 'admin' => 'www.example.com/admin' ),
                          'prefixes' => array( 'site' => 'site', 'bold' => 'bold', 'bold_ger' => 'bold_ger', 'admin' => 'admin' ) );
        $settings = $several + array( 'Remote' => '', 'Credential' => 'none' ) + expStaticTarget::DEFAULTS;
        $this->assertSame( array(), expStaticAdmin::check( 'three', $settings, $context, array(), true )['errors'] );
        $this->assertSame( array( array( 'name' => 'bold', 'folder' => '/', 'default' => true ), array( 'name' => 'site', 'folder' => '/site/', 'default' => false ),
                                  array( 'name' => 'bold_ger', 'folder' => '/bold_ger/', 'default' => false ) ),
                           expStaticAdmin::memberList( new expStaticTarget( 'three', $settings ), $context ) );

        mkdir( $this->dir . '/settings/override', 0775, true );
        $scope = new expIniScope( 'global', expIniScope::KIND_GLOBAL, 'settings/override', $this->dir . '/', 'Global override (settings/override)' );
        $content = expStaticAdmin::editor( 'three', $settings, false, $scope )->content();
        $this->assertStringContainsString( "SiteAccess=\nSiteAccessList[]\nSiteAccessList[]=site\nSiteAccessList[]=bold\nSiteAccessList[]=bold_ger\nDefaultSiteAccess=bold\n", $content );
        $single = expStaticAdmin::editor( 'one', $one + expStaticTarget::DEFAULTS, false, $scope )->content();
        $this->assertStringContainsString( "[Target_one]\nName=\nSiteAccess=site\nKind=", $single, 'a target of one: no list, no default' );
    }
}
