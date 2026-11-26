<?php
/**
 * Staging of images from a source installation (expImageAliasStager), without a database:
 *
 *  STG-01 - the source URL of a storage path: BaseURL plus the path, the longest PathMap[] prefix (a path below
 *           BaseURL or a whole URL), each segment encoded; paths outside the storage and base URLs with a user name,
 *           a password or a query are refused
 *  STG-02 - credentials: "user:password" becomes basic authentication, "Bearer <token>" a bearer header, anything
 *           else is refused; the value never reaches the log, the state file, the alert or a reason
 *  STG-03 - what is stored: only an HTTP 200 whose type and content are the image type of the file's extension, at
 *           most MaxFileSize bytes; HTML error pages (typed text/html or typed as an image), corrupt bytes, another
 *           image type, 404, 401 and 500 are refused, and nothing is written for them
 *  STG-04 - the circuit breaker: FailuresBeforePause failures in a row pause the source for PauseSeconds; while
 *           paused nothing is asked of the source; a file that comes through ends the count; the alert goes out once
 *           per pause, at most once an hour, and the warning is kept for the administration
 *  STG-05 - on request: the alias from the source, else its original (missing at the source counts against it),
 *           pending while the source is down; production (Environment=pro) and a source without a base URL never
 *           fetch
 *  STG-06 - the resync of an image's files: missing only by default, overwrite replaces only what differs (size,
 *           then a hash), a dry run writes nothing, the files of the stored XML, the alias filter, the job payload
 *  STG-07 - the real HTTP transport against a loopback source (PHP's built-in server): a file, basic and bearer
 *           authentication, wrong credentials, a timeout within the limit, a source that is down, an HTML page, a
 *           corrupt image, a body over the size limit
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/imagealias/expImageAliasStagerTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expImageAliasStagerTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $base;

    /** @var string a storage directory of the tests */
    private $dir;

    /** @var string|false */
    private $cwd = false;

    /** @var string */
    private $jpeg;

    /** @var string */
    private $png;

    /** @var int */
    private $now = 1800000000;

    /** @var array the transport's calls */
    private $calls = array();

    /** @var array the alerts sent */
    private $mails = array();

    /** @var int stagers made (each keeps its state in a directory of its own) */
    private $stagers = 0;

    /** @var resource|null the loopback source */
    private $server = null;

    public static function setUpBeforeClass(): void
    {
        eZExecution::registerShutdownHandler();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 5 ) );
        $this->base = 'var/tmp/storage/images/exp_stg_' . getmypid() . '_' . mt_rand();
        $this->dir = $this->base . '/7-1-eng-GB';
        mkdir( $this->dir, 0700, true );
        $this->jpeg = (string)file_get_contents( dirname( __DIR__, 4 ) . '/tests/lib/ezimage/data/andernach.jpg' );
        $image = imagecreatetruecolor( 4, 3 );
        ob_start();
        imagepng( $image );
        $this->png = (string)ob_get_clean();
        expImageAliasLog::setDirectory( $this->base . '/log' );
        putenv( 'EXP_STG_TEST_CREDENTIAL=stageuser:s3cr3t-Pa55' );
        putenv( 'EXP_STG_TEST_TOKEN=Bearer tok-0123456789abcdef' );
        putenv( 'EXP_STG_TEST_BROKEN=no-colon-here' );
    }

    protected function tearDown(): void
    {
        if ( $this->server !== null )
        {
            ezpLoopbackServer::stop( $this->server );
            $this->server = null;
        }
        expImageAliasLog::setDirectory( null );
        putenv( 'EXP_STG_TEST_CREDENTIAL' );
        putenv( 'EXP_STG_TEST_TOKEN' );
        putenv( 'EXP_STG_TEST_BROKEN' );
        $this->removeTree( $this->base );
        if ( $this->cwd !== false )
            chdir( $this->cwd );
        parent::tearDown();
    }

    private function removeTree( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        foreach ( (array)scandir( $dir ) as $entry )
        {
            if ( $entry === '.' || $entry === '..' )
                continue;
            $path = $dir . '/' . $entry;
            if ( is_dir( $path ) && !is_link( $path ) )
                $this->removeTree( $path );
            else
                @unlink( $path );
        }
        @rmdir( $dir );
    }

    /**
     * A stager with test settings, a fake transport answering from $answers (url suffix => response) and a fake
     * mailer.
     */
    private function stager( array $answers = array(), array $staging = array(), array $source = array() )
    {
        $stager = new expImageAliasStager( $staging + array(
            'environment' => 'dev', 'sync_from' => 'pro', 'path_map' => array(), 'timeout' => 2, 'failures_before_pause' => 3,
            'pause_seconds' => 300, 'alert_email' => true, 'alert_recipients' => array( 'ops@example.org' ), 'max_file_size' => 1048576 ),
            $source + array( 'base_url' => 'https://pro.example.org', 'credential' => '' ), $this->base . '/state' . ( ++$this->stagers ) );
        $stager->setClock( function () {
            return $this->now;
        } );
        $stager->setMailer( function ( $subject, $body, array $recipients ) {
            $this->mails[] = array( 'subject' => $subject, 'body' => $body, 'recipients' => $recipients );
            return true;
        } );
        $stager->setTransport( function ( $method, $url, array $headers, $timeout, $maxBytes ) use ( $answers ) {
            $this->calls[] = array( 'method' => $method, 'url' => $url, 'headers' => $headers, 'timeout' => $timeout );
            foreach ( $answers as $suffix => $answer )
            {
                if ( substr( $url, -strlen( $suffix ) ) === $suffix )
                    return $answer;
            }
            return array( 'status' => 404, 'type' => 'text/html', 'body' => '<!DOCTYPE html><html></html>' );
        } );
        return $stager;
    }

    private function jpegAnswer()
    {
        return array( 'status' => 200, 'type' => 'image/jpeg', 'body' => $this->jpeg );
    }

    private function logText()
    {
        $file = $this->base . '/log/' . expImageAliasLog::FILE;
        return is_file( $file ) ? (string)file_get_contents( $file ) : '';
    }

    /**
     * STG-01
     */
    public function testSourceUrlAndPathMap()
    {
        $stager = $this->stager();
        $this->assertSame( 'https://pro.example.org/var/site/storage/images/a/1-1-eng-GB/my%20image_small.jpg',
                           $stager->sourceUrl( 'var/site/storage/images/a/1-1-eng-GB/my image_small.jpg' ) );
        $mapped = $this->stager( array(), array( 'path_map' => array(
            'var/site/storage;var/production/storage',
            'var/site/storage/images/media;https://cdn.example.org/media',
            'broken line without separator' ) ) );
        $this->assertSame( 'https://pro.example.org/var/production/storage/images/x/2-1-eng-GB/a.png',
                           $mapped->sourceUrl( 'var/site/storage/images/x/2-1-eng-GB/a.png' ) );
        $this->assertSame( 'https://cdn.example.org/media/b/3-1-eng-GB/a.png', $mapped->sourceUrl( 'var/site/storage/images/media/b/3-1-eng-GB/a.png' ),
                           'the longest prefix wins; a whole URL replaces BaseURL' );
        $this->assertSame( 'https://pro.example.org/var/other/storage/images/a.png', $mapped->sourceUrl( 'var/other/storage/images/a.png' ) );
        // A prefix only matches whole segments
        $this->assertSame( 'https://pro.example.org/var/site/storage/images/media/x.png',
                           $this->stager( array(), array( 'path_map' => array( 'var/site/storage/images/med;elsewhere' ) ) )
                                ->sourceUrl( 'var/site/storage/images/media/x.png' ) );
        foreach ( array( '/etc/passwd', 'var/site/storage/../../settings/site.ini', 'settings/site.ini', '' ) as $bad )
            $this->assertNull( $stager->sourceUrl( $bad ), $bad );
        foreach ( array( 'https://user:pw@pro.example.org', 'https://pro.example.org/?a=1', 'ftp://pro.example.org', 'pro.example.org', '' ) as $badBase )
            $this->assertNull( $this->stager( array(), array(), array( 'base_url' => $badBase ) )->sourceUrl( 'var/site/storage/images/a.jpg' ), $badBase );
        $this->assertTrue( expImageAliasStager::validBaseUrl( 'http://127.0.0.1:18766/ok' ) );
    }

    /**
     * STG-02
     */
    public function testCredentialHeadersNeverLeak()
    {
        $this->assertSame( 'Basic ' . base64_encode( 'stageuser:s3cr3t-Pa55' ), expImageAliasStager::authorizationFor( 'stageuser:s3cr3t-Pa55' ) );
        $this->assertSame( 'Bearer tok-1', expImageAliasStager::authorizationFor( 'Bearer tok-1' ) );
        $this->assertSame( 'Bearer tok-1', expImageAliasStager::authorizationFor( ' bearer   tok-1 ' ) );
        $this->assertFalse( expImageAliasStager::authorizationFor( 'no-colon-here' ) );
        $this->assertFalse( expImageAliasStager::authorizationFor( null ) );
        $this->assertFalse( expImageAliasStager::authorizationFor( ':nouser' ) );

        $path = $this->dir . '/photo.jpg';
        // Basic: the header is sent; a 401 answer pauses after three, and nothing of the secret is written anywhere
        $stager = $this->stager( array( 'photo.jpg' => array( 'status' => 401, 'type' => 'text/html', 'body' => 'denied stageuser:s3cr3t-Pa55' ) ),
                                 array(), array( 'credential' => 'env:EXP_STG_TEST_CREDENTIAL' ) );
        for ( $i = 0; $i < 3; $i++ )
        {
            $result = $stager->fetch( $path );
            $this->assertSame( 'unauthorized', $result['result'] );
        }
        $this->assertSame( 'Basic ' . base64_encode( 'stageuser:s3cr3t-Pa55' ), $this->calls[0]['headers']['Authorization'] );
        $this->assertTrue( $stager->isPaused() );
        $everything = $this->logText() . json_encode( $stager->status() ) . json_encode( $this->mails ) . json_encode( $result )
                    . implode( '', array_map( 'file_get_contents', (array)glob( $this->base . '/state*/pro.json' ) ) );
        $this->assertStringContainsString( '"failures":3', $everything );
        foreach ( array( 's3cr3t-Pa55', base64_encode( 'stageuser:s3cr3t-Pa55' ) ) as $secret )
            $this->assertStringNotContainsString( $secret, $everything );
        $this->assertCount( 1, $this->mails );

        // Bearer
        $this->calls = array();
        $bearer = $this->stager( array( 'photo.jpg' => $this->jpegAnswer() ), array(), array( 'credential' => 'env:EXP_STG_TEST_TOKEN' ) );
        $bearer->setTransport( function ( $method, $url, array $headers ) {
            $this->calls[] = $headers;
            return array( 'status' => 500, 'type' => 'text/plain', 'body' => 'boom tok-0123456789abcdef' );
        } );
        $result = $bearer->fetch( $this->dir . '/other.jpg' );
        $this->assertSame( 'http', $result['result'] );
        $this->assertSame( 'Bearer tok-0123456789abcdef', $this->calls[0]['Authorization'] );
        $this->assertStringNotContainsString( 'tok-0123456789abcdef', $this->logText() . json_encode( $result ) );

        // A credential that is named but missing or unusable: no request at all
        $this->calls = array();
        foreach ( array( 'env:EXP_STG_TEST_MISSING', 'env:EXP_STG_TEST_BROKEN' ) as $name )
        {
            $result = $this->stager( array( 'photo.jpg' => $this->jpegAnswer() ), array(), array( 'credential' => $name ) )->fetch( $this->dir . '/x.jpg' );
            $this->assertSame( 'no-credential', $result['result'] );
        }
        $this->assertSame( array(), $this->calls );
        $this->assertStringNotContainsString( 'no-colon-here', $this->logText() );
    }

    /**
     * STG-03
     */
    public function testOnlyRealImagesAreStored()
    {
        $html = "<!DOCTYPE html>\n<html><body>Error</body></html>";
        $cases = array(
            'html page' => array( array( 'status' => 200, 'type' => 'text/html; charset=utf-8', 'body' => $html ), 'invalid' ),
            'html as image' => array( array( 'status' => 200, 'type' => 'image/jpeg', 'body' => $html ), 'invalid' ),
            'corrupt' => array( array( 'status' => 200, 'type' => 'image/jpeg', 'body' => str_repeat( "\x00garbage", 100 ) ), 'invalid' ),
            'png bytes' => array( array( 'status' => 200, 'type' => 'image/jpeg', 'body' => $this->png ), 'invalid' ),
            'wrong type' => array( array( 'status' => 200, 'type' => 'text/plain', 'body' => $this->jpeg ), 'invalid' ),
            'no type' => array( array( 'status' => 200, 'type' => '', 'body' => $this->jpeg ), 'invalid' ),
            'empty' => array( array( 'status' => 200, 'type' => 'image/jpeg', 'body' => '' ), 'invalid' ),
            'missing' => array( array( 'status' => 404, 'type' => 'text/html', 'body' => $html ), 'missing' ),
            'gone' => array( array( 'status' => 410, 'type' => 'text/html', 'body' => $html ), 'missing' ),
            'forbidden' => array( array( 'status' => 403, 'type' => 'text/html', 'body' => $html ), 'unauthorized' ),
            'server error' => array( array( 'status' => 500, 'type' => 'text/html', 'body' => $html ), 'http' ),
            'redirect' => array( array( 'status' => 302, 'type' => 'text/html', 'body' => '' ), 'http' ),
            'down' => array( array( 'status' => 0, 'error' => 'down' ), 'down' ),
            'timeout' => array( array( 'status' => 0, 'error' => 'timeout' ), 'timeout' ),
            'too large' => array( array( 'status' => 0, 'error' => 'too-large' ), 'too-large' ),
        );
        $i = 0;
        foreach ( $cases as $label => $case )
        {
            $path = $this->dir . '/case' . ( $i++ ) . '.jpg';
            $result = $this->stager( array( basename( $path ) => $case[0] ), array( 'failures_before_pause' => 100 ) )->fetch( $path );
            $this->assertSame( $case[1], $result['result'], $label );
            $this->assertFalse( $result['stored'], $label );
            $this->assertFileDoesNotExist( $path, $label );
        }
        // A body over the limit is refused even when the transport handed it on
        $path = $this->dir . '/big.jpg';
        $result = $this->stager( array( 'big.jpg' => $this->jpegAnswer() ), array( 'max_file_size' => 100 ) )->fetch( $path );
        $this->assertSame( 'too-large', $result['result'] );
        $this->assertFileDoesNotExist( $path );

        // The real thing is stored, byte for byte; "image/jpg" is a name of JPEG
        $path = $this->dir . '/good.jpg';
        $result = $this->stager( array( 'good.jpg' => array( 'status' => 200, 'type' => 'image/jpg', 'body' => $this->jpeg ) ) )->fetch( $path );
        $this->assertSame( 'ok', $result['result'] );
        $this->assertTrue( $result['stored'] );
        $this->assertSame( $this->jpeg, file_get_contents( $path ) );
        $path = $this->dir . '/good.png';
        $this->assertSame( 'ok', $this->stager( array( 'good.png' => array( 'status' => 200, 'type' => 'image/png', 'body' => $this->png ) ) )->fetch( $path )['result'] );
        // SVG: an svg document only
        $svg = '<?xml version="1.0"?><svg xmlns="http://www.w3.org/2000/svg" width="4" height="4"/>';
        $this->assertNull( expImageAliasStager::validate( 'var/site/storage/images/a.svg', array( 'status' => 200, 'type' => 'image/svg+xml', 'body' => $svg ), 0 ) );
        $this->assertSame( 'invalid', expImageAliasStager::validate( 'var/site/storage/images/a.svg',
                                                                    array( 'status' => 200, 'type' => 'image/svg+xml', 'body' => '<html><body/></html>' ), 0 )[0] );
        // Not an image path at all
        $this->assertSame( 'invalid', expImageAliasStager::validate( 'var/site/storage/original/a.pdf', array( 'status' => 200, 'type' => 'application/pdf', 'body' => '%PDF' ), 0 )[0] );
        // An AVIF variant PHP reads no size of (before PHP 8.2): its signature decides; without one it stays refused
        $avif = "\0\0\0\x1cftypavif\0\0\0\0avifmif1miaf" . str_repeat( "\0", 64 );
        $this->assertNull( expImageAliasStager::validate( 'var/site/storage/images/a_small.jpg.avif', array( 'status' => 200, 'type' => 'image/avif', 'body' => $avif ), 0 ) );
        $this->assertSame( 'invalid', expImageAliasStager::validate( 'var/site/storage/images/a_small.jpg.avif',
                                                                    array( 'status' => 200, 'type' => 'image/avif', 'body' => str_repeat( 'x', 80 ) ), 0 )[0] );
    }

    /**
     * STG-04
     */
    public function testCircuitBreakerPausesAndAlertsOncePerHour()
    {
        $stager = $this->stager( array( 'down.jpg' => array( 'status' => 0, 'error' => 'down' ), 'up.jpg' => $this->jpegAnswer() ) );
        $this->assertFalse( $stager->isPaused() );
        $stager->fetch( $this->dir . '/down.jpg' );
        $stager->fetch( $this->dir . '/down.jpg' );
        $this->assertFalse( $stager->isPaused(), 'two failures: not yet' );
        // A file that comes through ends the count
        $this->assertSame( 'ok', $stager->fetch( $this->dir . '/up.jpg' )['result'] );
        $this->assertSame( 0, $stager->status()['failures'] );
        for ( $i = 0; $i < 3; $i++ )
            $stager->fetch( $this->dir . '/down.jpg' );
        $status = $stager->status();
        $this->assertTrue( $status['paused'] );
        $this->assertSame( $this->now + 300, $status['paused_until'] );
        $this->assertSame( 3, $status['warning']['failures'] );
        $this->assertStringStartsWith( 'down', $status['last_error'] );
        $this->assertCount( 1, $this->mails );
        $this->assertSame( array( 'ops@example.org' ), $this->mails[0]['recipients'] );
        $this->assertStringContainsString( 'pro.example.org', $this->mails[0]['body'] );

        // Paused: nothing is asked of the source, at once
        $calls = count( $this->calls );
        $started = microtime( true );
        for ( $i = 0; $i < 20; $i++ )
            $this->assertSame( 'paused', $stager->fetch( $this->dir . '/up.jpg' )['result'] );
        $this->assertSame( $calls, count( $this->calls ) );
        $this->assertLessThan( 0.5, microtime( true ) - $started );
        $this->assertSame( 'pending', $stager->stageAlias( $this->dir . '/up_small.jpg', $this->dir . '/up.jpg', 2.0 ) );

        // After the pause the source is asked again; failing again pauses again, but no second mail within the hour
        $this->now += 301;
        $this->assertFalse( $stager->isPaused() );
        for ( $i = 0; $i < 3; $i++ )
            $stager->fetch( $this->dir . '/down.jpg' );
        $this->assertTrue( $stager->isPaused() );
        $this->assertCount( 1, $this->mails, 'at most one alert an hour per source' );
        // An hour later: the next pause mails again
        $this->now += 3600;
        for ( $i = 0; $i < 3; $i++ )
            $stager->fetch( $this->dir . '/down.jpg' );
        $this->assertCount( 2, $this->mails );
        $this->assertStringContainsString( 'event=fail result=paused', $this->logText() );
        $this->assertStringContainsString( 'result=alert-sent', $this->logText() );

        // The administration's reset
        $stager->resetBreaker();
        $this->assertFalse( $stager->isPaused() );
        $this->assertNull( $stager->status()['warning'] );

        // Alerts switched off: no mail
        $this->mails = array();
        $quiet = $this->stager( array(), array( 'alert_email' => false, 'failures_before_pause' => 1 ), array( 'base_url' => 'https://quiet.example.org' ) );
        $quiet->fetch( $this->dir . '/nothing.jpg' );
        $quiet->recordFailure( 'test' );
        $this->assertTrue( $quiet->isPaused() );
        $this->assertSame( array(), $this->mails );
    }

    /**
     * STG-05
     */
    public function testOnRequestAliasThenOriginal()
    {
        $alias = $this->dir . '/photo_small.jpg';
        $original = $this->dir . '/photo.jpg';
        // The source has the alias
        $this->assertSame( 'alias', $this->stager( array( 'photo_small.jpg' => $this->jpegAnswer() ) )->stageAlias( $alias, $original, 2.0 ) );
        $this->assertFileExists( $alias );
        unlink( $alias );
        // Only the original: fetched, the alias is made here
        $this->calls = array();
        $this->assertSame( 'original', $this->stager( array( 'photo.jpg' => $this->jpegAnswer() ) )->stageAlias( $alias, $original, 2.0 ) );
        $this->assertFileExists( $original );
        $this->assertFileDoesNotExist( $alias );
        $this->assertCount( 2, $this->calls );
        // The original is here already
        $this->assertSame( 'local', $this->stager()->stageAlias( $alias, $original, 2.0 ) );
        unlink( $original );
        // Neither: missing, and that counts against the source
        $stager = $this->stager();
        $this->assertSame( 'missing', $stager->stageAlias( $alias, $original, 2.0 ) );
        $this->assertSame( 1, $stager->status()['failures'] );
        // Down: pending (the browser asks again)
        $this->assertSame( 'pending', $this->stager( array( 'photo_small.jpg' => array( 'status' => 0, 'error' => 'down' ) ) )->stageAlias( $alias, $original, 2.0 ) );
        // An HTML page instead of the alias: not stored, the image counts as missing
        $this->assertSame( 'missing', $this->stager( array( 'photo_small.jpg' => array( 'status' => 200, 'type' => 'text/html', 'body' => '<html></html>' ) ) )
            ->stageAlias( $alias, $original, 2.0 ) );
        $this->assertFileDoesNotExist( $alias );
        // The time left bounds the request
        $this->calls = array();
        $this->stager( array( 'photo_small.jpg' => $this->jpegAnswer() ) )->stageAlias( $this->dir . '/b_small.jpg', '', 0.7 );
        $this->assertEqualsWithDelta( 0.7, $this->calls[0]['timeout'], 0.001 );
        $this->assertSame( 'timeout', $this->stager()->fetch( $alias, 0.0 )['result'] );

        // Production and a source without a base URL never fetch
        $this->calls = array();
        foreach ( array( $this->stager( array(), array( 'environment' => 'pro' ) ), $this->stager( array(), array(), array( 'base_url' => '' ) ),
                         $this->stager( array(), array( 'sync_from' => '' ) ) ) as $inactive )
        {
            $this->assertFalse( $inactive->isActive() );
            $this->assertSame( 'inactive', $inactive->fetch( $alias )['result'] );
        }
        $this->assertSame( array(), $this->calls );
        // Adoption: only while adopting(), only for an existing file
        file_put_contents( $alias, $this->jpeg );
        $this->assertNull( expImageAliasStager::adoptedFile( 'small' ) );
        $this->assertSame( $alias, expImageAliasStager::adopting( 'small', $alias, function () {
            return expImageAliasStager::adoptedFile( 'small' );
        } ) );
        $this->assertNull( expImageAliasStager::adoptedFile( 'small' ) );
        $this->assertNull( expImageAliasStager::adopting( 'small', $this->dir . '/none.jpg', function () {
            return expImageAliasStager::adoptedFile( 'small' );
        } ) );
    }

    /**
     * STG-06
     */
    public function testResyncFilesModes()
    {
        $files = array( 'original' => $this->dir . '/r.jpg', 'small' => $this->dir . '/r_small.jpg', 'large' => $this->dir . '/r_large.jpg' );
        $other = $this->png;
        $answers = array( 'r.jpg' => $this->jpegAnswer(), 'r_small.jpg' => $this->jpegAnswer(), 'r_large.jpg' => array( 'status' => 404, 'type' => 'text/html', 'body' => 'x' ) );
        // Dry run, missing only: nothing asked, nothing written
        $counts = expImageAliasStager::emptyCounts();
        $this->assertSame( 'done', $this->stager( $answers )->resyncFiles( $files, array( 'dry_run' => true ), 'run1', $counts ) );
        $this->assertSame( 3, $counts['would-fetch'] );
        $this->assertSame( array(), $this->calls );
        foreach ( $files as $path )
            $this->assertFileDoesNotExist( $path );
        // Missing only
        $counts = expImageAliasStager::emptyCounts();
        $this->stager( $answers )->resyncFiles( $files, array(), 'run2', $counts );
        $this->assertSame( 2, $counts['fetched'] );
        $this->assertSame( 1, $counts['missing'] );
        // Again: present, nothing fetched
        $this->calls = array();
        $counts = expImageAliasStager::emptyCounts();
        $this->stager( $answers )->resyncFiles( $files, array(), 'run3', $counts );
        $this->assertSame( 2, $counts['present'] );
        $this->assertCount( 1, $this->calls, 'only the file still missing is asked for' );
        // Overwrite: the same file stays, a different one (same size, other bytes; other size) is replaced
        file_put_contents( $files['small'], strrev( $this->jpeg ) );
        file_put_contents( $files['original'], $other );
        $counts = expImageAliasStager::emptyCounts();
        $this->stager( $answers )->resyncFiles( array( 'original' => $files['original'], 'small' => $files['small'] ), array( 'overwrite' => true, 'dry_run' => true ), 'run4', $counts );
        $this->assertSame( 2, $counts['would-replace'] );
        $this->assertSame( $other, file_get_contents( $files['original'] ), 'a dry run replaces nothing' );
        $counts = expImageAliasStager::emptyCounts();
        $this->stager( $answers )->resyncFiles( array( 'original' => $files['original'], 'small' => $files['small'] ), array( 'overwrite' => true ), 'run5', $counts );
        $this->assertSame( 2, $counts['replaced'] );
        $this->assertSame( $this->jpeg, file_get_contents( $files['original'] ) );
        $this->assertSame( $this->jpeg, file_get_contents( $files['small'] ) );
        $counts = expImageAliasStager::emptyCounts();
        $this->stager( $answers )->resyncFiles( array( 'original' => $files['original'] ), array( 'overwrite' => true ), 'run6', $counts );
        $this->assertSame( 1, $counts['same'] );
        $this->assertStringContainsString( 'run=run5', $this->logText() );
        // A paused source stops the run
        $paused = $this->stager( array( 'r_large.jpg' => array( 'status' => 0, 'error' => 'down' ) ), array( 'failures_before_pause' => 1 ) );
        $counts = expImageAliasStager::emptyCounts();
        $this->assertSame( 'paused', $paused->resyncFiles( array( 'large' => $files['large'], 'other' => $this->dir . '/o.jpg' ), array(), 'run7', $counts ) );
        $this->assertSame( 1, $counts['failed'] );

        // The files of a stored XML; the alias filter
        $xml = '<?xml version="1.0" encoding="utf-8"?><ezimage url="var/site/storage/images/a/9-2-eng-GB/a.jpg" filename="a.jpg">'
             . '<original attribute_id="9" attribute_version="2" attribute_language="eng-GB"/>'
             . '<alias name="small" url="var/site/storage/images/a/9-2-eng-GB/a_small.jpg"/>'
             . '<alias name="bad" url="../../etc/passwd"/></ezimage>';
        $listed = expImageAliasStager::filesOfXml( $xml );
        $this->assertSame( array( 'original' => 'var/site/storage/images/a/9-2-eng-GB/a.jpg', 'small' => 'var/site/storage/images/a/9-2-eng-GB/a_small.jpg' ), $listed );
        $this->assertSame( array( 'small' => $listed['small'] ), expImageAliasStager::filterFiles( $listed, array( 'small' ) ) );
        $this->assertSame( $listed, expImageAliasStager::filterFiles( $listed, array() ) );
        $this->assertSame( array(), expImageAliasStager::filesOfXml( 'not xml' ) );
        // The job payload keeps the known options only
        $this->assertSame( array( 'subtree' => 2, 'since' => 1700000000, 'alias' => array( 'small', 'original' ), 'overwrite' => true, 'dry_run' => true, 'run' => 'rs-1' ),
                           expImageAliasJob::resyncPayload( array( 'subtree' => '2', 'since' => 1700000000, 'alias' => array( 'small', 'original', 'bad name' ),
                                                                   'overwrite' => 1, 'dry_run' => 'yes', 'run' => 'rs-1', 'password' => 'x', 'node' => 0 ) ) );
        $this->assertSame( 'subtree=2 since=1700000000 alias=small overwrite dry-run', expImageAliasStager::describeScope(
            array( 'subtree' => 2, 'since' => 1700000000, 'alias' => array( 'small' ), 'overwrite' => true, 'dry_run' => true ) ) );
        $this->assertSame( 'installation', expImageAliasStager::describeScope( array() ) );
    }

    /**
     * STG-07
     */
    public function testRealTransportAgainstLoopbackSource()
    {
        if ( !function_exists( 'proc_open' ) || !function_exists( 'curl_init' ) )
            $this->markTestSkipped( 'Needs proc_open() and curl' );
        $root = $this->base . '/source';
        mkdir( $root . '/var/site/storage/images/a/1-1-eng-GB', 0700, true );
        file_put_contents( $root . '/var/site/storage/images/a/1-1-eng-GB/p.jpg', $this->jpeg );
        // Where the path map below sends the test's own storage directory
        $mapped = $root . '/var/site/storage/images/' . substr( $this->dir, strlen( 'var/tmp/storage/images/' ) );
        mkdir( $mapped, 0700, true );
        file_put_contents( $mapped . '/p.jpg', $this->jpeg );
        $port = 0;
        for ( $try = 0; $try < 5 && $this->server === null; $try++ )
        {
            $port = 20000 + mt_rand( 0, 9999 );
            $env = array( 'STAGING_SOURCE_ROOT' => realpath( $root ), 'STAGING_SOURCE_AUTH' => 'stageuser:s3cr3t-Pa55',
                          'STAGING_SOURCE_TOKEN' => 'tok-0123456789abcdef', 'STAGING_SOURCE_SLOW' => '3', 'PHP_CLI_SERVER_WORKERS' => '4',
                          'PATH' => (string)getenv( 'PATH' ) );
            $server = ezpLoopbackServer::start( array( PHP_BINARY, '-S', "127.0.0.1:$port", __DIR__ . '/fixtures/expimagestagingsource.php' ), $env );
            for ( $wait = 0; $wait < 40; $wait++ )
            {
                usleep( 50000 );
                $socket = @fsockopen( '127.0.0.1', $port, $errno, $errstr, 0.2 );
                if ( $socket )
                {
                    fclose( $socket );
                    $this->server = $server;
                    break;
                }
            }
            if ( $this->server === null )
            {
                proc_terminate( $server );
                proc_close( $server );
            }
        }
        if ( $this->server === null )
            $this->markTestSkipped( 'The loopback source did not start' );
        $local = 'var/site/storage/images/a/1-1-eng-GB/p.jpg';
        $make = function ( $mode, $credential = '', array $staging = array() ) use ( $port ) {
            $stager = new expImageAliasStager( $staging + array( 'environment' => 'dev', 'sync_from' => 'pro', 'path_map' => array( 'var/tmp/storage;var/site/storage' ),
                                                       'timeout' => 1, 'failures_before_pause' => 100, 'pause_seconds' => 60, 'alert_email' => false,
                                                       'alert_recipients' => array(), 'max_file_size' => 1048576 ),
                                               array( 'base_url' => "http://127.0.0.1:$port/$mode", 'credential' => $credential ), $this->base . '/state-' . $mode );
            return $stager;
        };
        // The test's own storage directory maps to the source's var/site/storage
        $target = $this->dir . '/p.jpg';
        $this->assertSame( "http://127.0.0.1:$port/ok/var/site/storage/images/" . substr( $this->dir, strlen( 'var/tmp/storage/images/' ) ) . '/p.jpg',
                           $make( 'ok' )->sourceUrl( $target ) );
        $request = function ( $mode, $credential = '' ) use ( $port, $local ) {
            $authorization = $credential !== '' ? expImageAliasStager::authorizationFor( $credential ) : null;
            $headers = $authorization ? array( 'Authorization' => $authorization ) : array();
            $started = microtime( true );
            $response = expImageAliasStager::request( 'GET', "http://127.0.0.1:$port/$mode/$local", $headers, 1.0, 1048576 );
            $response['seconds'] = microtime( true ) - $started;
            return $response;
        };
        $ok = $request( 'ok' );
        $this->assertSame( 200, $ok['status'] );
        $this->assertSame( $this->jpeg, $ok['body'] );
        $this->assertNull( expImageAliasStager::validate( $local, $ok, 1048576 ) );
        $this->assertSame( 200, $request( 'auth', 'stageuser:s3cr3t-Pa55' )['status'] );
        $this->assertSame( 401, $request( 'auth', 'stageuser:wrong' )['status'] );
        $this->assertSame( 401, $request( 'auth' )['status'] );
        $this->assertSame( 200, $request( 'bearer', 'Bearer tok-0123456789abcdef' )['status'] );
        $this->assertSame( 401, $request( 'bearer', 'Bearer other' )['status'] );
        $slow = $request( 'slow' );
        $this->assertSame( 'timeout', $slow['error'] );
        $this->assertLessThan( 2.5, $slow['seconds'], 'the timeout bounds the request' );
        $this->assertSame( 'invalid', expImageAliasStager::validate( $local, $request( 'html' ), 1048576 )[0] );
        $this->assertSame( 'invalid', expImageAliasStager::validate( $local, $request( 'lying' ), 1048576 )[0] );
        $this->assertSame( 'invalid', expImageAliasStager::validate( $local, $request( 'corrupt' ), 1048576 )[0] );
        $this->assertSame( 'missing', expImageAliasStager::validate( $local, $request( 'missing' ), 1048576 )[0] );
        $big = $request( 'big' );
        $this->assertSame( 'too-large', $big['error'] );
        // Down: a port nobody listens on answers at once
        $started = microtime( true );
        $down = expImageAliasStager::request( 'GET', 'http://127.0.0.1:1/' . $local, array(), 1.0, 1048576 );
        $this->assertSame( 'down', $down['error'] );
        $this->assertLessThan( 1.5, microtime( true ) - $started );

        // Through fetch(): stored with the right credential, nothing with the wrong one
        putenv( 'EXP_STG_TEST_WRONG=stageuser:wrong' );
        $result = $make( 'auth', 'env:EXP_STG_TEST_CREDENTIAL' )->fetch( $target );
        $this->assertSame( 'ok', $result['result'] );
        $this->assertSame( $this->jpeg, file_get_contents( $target ) );
        unlink( $target );
        $this->assertSame( 'unauthorized', $make( 'auth', 'env:EXP_STG_TEST_WRONG' )->fetch( $target )['result'] );
        $this->assertFileDoesNotExist( $target );
        putenv( 'EXP_STG_TEST_WRONG' );
        $this->assertStringNotContainsString( 's3cr3t-Pa55', $this->logText() );
    }
}
