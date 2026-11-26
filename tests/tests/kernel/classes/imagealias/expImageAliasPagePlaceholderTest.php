<?php
/**
 * The placeholders a page links to itself past PagePlaceholderAfter aliases not made yet ([AliasSettings]
 * PagePlaceholders, expImageAliasPagePlaceholder), without a database:
 *
 *  IAPP-01 - when it applies: the setting and a server that makes the backlog (never the command line)
 *  IAPP-02 - the first PagePlaceholderAfter aliases of a request keep their URL; the next ones get the static
 *            placeholder of their size with the real address and the path depth in the fragment, go to the backlog
 *            (once each, with their variant formats; the first ones too, in page order, once the limit is passed)
 *            and limit the page's cache time; a new request counts again;
 *            an outdated alias keeps its URL
 *  IAPP-03 - one placeholder file per size, the SVG of expImagePlaceholder; a cache directory that cannot be written
 *            leaves the URL as it is
 *  IAPP-04 - the settings documented in image.ini, the handler hook, the swap script reading the fragment
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/imagealias/expImageAliasPagePlaceholderTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expImageAliasPagePlaceholderTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $base;

    /** @var string|false */
    private $cwd = false;

    /** @var mixed */
    private $requestTime = null;

    public static function setUpBeforeClass(): void
    {
        eZExecution::registerShutdownHandler();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 5 ) );
        $this->requestTime = $_SERVER['REQUEST_TIME_FLOAT'] ?? null;
        $this->base = 'var/tmp/exp_iapp_' . getmypid() . '_' . mt_rand();
        mkdir( $this->base . '/backlog', 0700, true );
        mkdir( $this->base . '/cache', 0700, true );
        expImageAliasBacklog::setDirectory( $this->base . '/backlog' );
        expImageAliasPagePlaceholder::setCacheDirectory( $this->base . '/cache' );
        expImageAliasPagePlaceholder::setApplies( true );
        expImageAliasPagePlaceholder::reset();
        expCacheUntil::reset();
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'PagePlaceholderAfter', '2' );
    }

    protected function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        expImageAliasPagePlaceholder::setApplies( null );
        expImageAliasPagePlaceholder::setCacheDirectory( null );
        expImageAliasPagePlaceholder::reset();
        expImageAliasBacklog::setDirectory( null );
        expImageAliasBacklog::setAnswerFirst( null );
        expCacheUntil::reset();
        if ( $this->requestTime === null )
            unset( $_SERVER['REQUEST_TIME_FLOAT'] );
        else
            $_SERVER['REQUEST_TIME_FLOAT'] = $this->requestTime;
        $this->removeTree( $this->base );
        if ( $this->cwd !== false )
            chdir( $this->cwd );
        parent::tearDown();
    }

    private function removeTree( $dir )
    {
        if ( !is_dir( $dir ) )
        {
            @unlink( $dir );
            return;
        }
        foreach ( array_merge( (array)glob( $dir . '/*' ), (array)glob( $dir . '/.*.tmp' ) ) as $file )
            $this->removeTree( $file );
        @rmdir( $dir );
    }

    /** A computed entry as expImageAliasUrl::computedEntry() gives it */
    private function entry( $n, $width = 160, $height = 106 )
    {
        return array( 'name' => 'i160', 'width' => $width, 'height' => $height, 'mime_type' => 'image/jpeg',
                      'url' => '/content/image/tok' . $n . '.sig/image-' . $n . '_i160.jpg',
                      'full_path' => 'var/site/storage/images/media/image-' . $n . '/' . ( 100 + $n ) . '-1-eng-GB/image-' . $n . '_i160.jpg',
                      'on_demand' => true );
    }

    private function attribute( $n )
    {
        return array( 'id' => 100 + $n, 'version' => 1, 'language_code' => 'eng-GB' );
    }

    private function backlogFiles()
    {
        return glob( $this->base . '/backlog/*.json' );
    }

    /** IAPP-01 */
    public function testWhenItApplies()
    {
        expImageAliasPagePlaceholder::setApplies( null );
        $this->assertTrue( expImageAliasSettings::pagePlaceholdersEnabled(), 'enabled by default' );
        $this->assertFalse( expImageAliasPagePlaceholder::applies(), 'not on the command line (no backlog)' );
        $entry = $this->entry( 1 );
        for ( $i = 0; $i < 5; $i++ )
            $this->assertSame( $entry, expImageAliasPagePlaceholder::apply( $entry, $this->attribute( 1 ), 'i160' ) );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'PagePlaceholders', 'disabled' );
        $this->assertFalse( expImageAliasSettings::pagePlaceholdersEnabled() );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'PagePlaceholders', 'enabled' );
        $this->assertSame( 2, expImageAliasSettings::pagePlaceholderAfter() );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'PagePlaceholderAfter', '-4' );
        $this->assertSame( 0, expImageAliasSettings::pagePlaceholderAfter(), 'never below 0' );
        ezpINIHelper::setINISetting( 'image.ini', 'AliasSettings', 'PagePlaceholderTime', '5' );
        $this->assertSame( 10, expImageAliasSettings::pagePlaceholderTime(), 'at least 10 seconds' );
    }

    /** IAPP-02 */
    public function testPastTheLimitTheRestGetThePlaceholder()
    {
        $_SERVER['REQUEST_TIME_FLOAT'] = '1000.5';
        $out = array();
        for ( $n = 1; $n <= 5; $n++ )
            $out[$n] = expImageAliasPagePlaceholder::apply( $this->entry( $n ), $this->attribute( $n ), 'i160' );
        $this->assertSame( $this->entry( 1 ), $out[1], 'the first two keep their URL' );
        $this->assertSame( $this->entry( 2 ), $out[2] );
        $this->assertSame( 5, expImageAliasPagePlaceholder::count() );
        $file = $this->base . '/cache/' . expImageAliasPagePlaceholder::DIRECTORY . '/160x106.svg';
        $depth = count( explode( '/', $file ) );
        foreach ( array( 3, 4, 5 ) as $n )
        {
            $this->assertTrue( $out[$n]['page_placeholder'] );
            $this->assertSame( $file . '#exp-src=' . rawurlencode( $this->entry( $n )['url'] ) . '&exp-depth=' . $depth, $out[$n]['url'] );
            $this->assertSame( $this->entry( $n )['full_path'], $out[$n]['full_path'], 'the rest of the entry as it was' );
        }
        $files = $this->backlogFiles();
        $this->assertCount( 5, $files, 'past the limit every alias of the page is in the backlog, the first two as well' );
        sort( $files );
        $first = json_decode( (string)file_get_contents( $files[0] ), true );
        $this->assertSame( array( 101, 102, 103, 104, 105 ), array_map( function ( $file ) { return json_decode( (string)file_get_contents( $file ), true )['a']; }, $files ), 'in page order' );
        $this->assertSame( 'i160', $first['n'] );
        $this->assertSame( $this->entry( 1 )['full_path'], $first['u'] );
        $expires = expCacheUntil::expires();
        $this->assertNotNull( $expires, 'the page cache time is limited' );
        $this->assertLessThanOrEqual( time() + expImageAliasSettings::pagePlaceholderTime(), $expires );

        // The same alias again in another request: counted again, not queued twice
        $_SERVER['REQUEST_TIME_FLOAT'] = '1001.5';
        $this->assertSame( 0, expImageAliasPagePlaceholder::count(), 'a new request counts from 0' );
        $this->assertSame( $this->entry( 3 ), expImageAliasPagePlaceholder::apply( $this->entry( 3 ), $this->attribute( 3 ), 'i160' ) );
        expImageAliasPagePlaceholder::apply( $this->entry( 4 ), $this->attribute( 4 ), 'i160' );
        expImageAliasPagePlaceholder::apply( $this->entry( 3 ), $this->attribute( 3 ), 'i160' );
        $this->assertCount( 5, $this->backlogFiles(), 'an alias already waiting is not added again' );

        // An outdated alias keeps its URL (its old file shows until the new one is made)
        $entry = $this->entry( 9 );
        $this->assertSame( $entry, expImageAliasPagePlaceholder::apply( $entry, $this->attribute( 9 ), 'i160', array( 'outdated' => true ) ) );
    }

    /** IAPP-03 */
    public function testOneFilePerSize()
    {
        $a = expImageAliasPagePlaceholder::file( 160, 106 );
        $b = expImageAliasPagePlaceholder::file( 160, 106 );
        $c = expImageAliasPagePlaceholder::file( 320, 180 );
        $this->assertSame( $a, $b );
        $this->assertNotSame( $a, $c );
        $this->assertSame( expImagePlaceholder::svg( 160, 106 ), file_get_contents( $a ) );
        $this->assertSame( expImagePlaceholder::svg( 320, 180 ), file_get_contents( $c ) );
        $this->assertSame( array(), (array)glob( dirname( $a ) . '/*.tmp' ), 'no temporary file left' );
        // No size known: the placeholder's default size
        $url = expImageAliasPagePlaceholder::url( $this->entry( 1, false, false ) );
        $this->assertStringContainsString( '/' . expImagePlaceholder::DEFAULT_WIDTH . 'x' . expImagePlaceholder::DEFAULT_HEIGHT . '.svg#exp-src=', $url );

        // A cache directory that cannot be written: the URL stays
        file_put_contents( $this->base . '/blocked', 'x' );
        expImageAliasPagePlaceholder::setCacheDirectory( $this->base . '/blocked' );
        $this->assertNull( expImageAliasPagePlaceholder::file( 160, 106 ) );
        $_SERVER['REQUEST_TIME_FLOAT'] = '2000.5';
        $out = array();
        for ( $n = 1; $n <= 3; $n++ )
            $out[$n] = @expImageAliasPagePlaceholder::apply( $this->entry( $n ), $this->attribute( $n ), 'i160' );
        $this->assertSame( $this->entry( 3 ), $out[3] );
        $this->assertCount( 0, $this->backlogFiles(), 'nothing queued for an alias linked as before' );
    }

    /** IAPP-04 */
    public function testSettingsHookAndSwapScript()
    {
        $root = dirname( __DIR__, 5 );
        $ini = (string)file_get_contents( $root . '/settings/image.ini' );
        foreach ( array( 'PagePlaceholders=enabled', 'PagePlaceholderAfter=24', 'PagePlaceholderTime=60' ) as $line )
            $this->assertStringContainsString( "\n" . $line . "\n", $ini, $line . ' in image.ini' );
        $handler = (string)file_get_contents( $root . '/kernel/classes/datatypes/ezimage/ezimagealiashandler.php' );
        $this->assertStringContainsString( 'expImageAliasPagePlaceholder::apply( $entry, $attribute, $aliasName, $options )', $handler );
        $js = (string)file_get_contents( $root . '/design/standard/javascript/exp_image_swap.js' );
        $this->assertStringContainsString( "'#exp-src='", $js, 'the swap script finds the page placeholders' );
        $this->assertStringContainsString( "'exp-depth'", $js );
        $this->assertStringContainsString( 'shownAs', $js, 'and swaps the image that shows the placeholder' );
    }
}
