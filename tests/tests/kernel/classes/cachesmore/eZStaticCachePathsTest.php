<?php
/**
 * Where the static cache keeps and fetches pages (eZStaticCache), without fetching or storing any: the storage
 * directory, the files and directories of a siteaccess for each way a siteaccess is matched (uri, host and uri, host),
 * PathPrefix and PathPrefixExclude, which urls are cached at all, the work a publish queues for the end of the
 * request (inspected and thrown away, never run), and writing one cache file into var/tmp.
 *
 * The static cache is switched off on most installations; none of this needs it on. The siteaccesses are made up:
 * their settings are written under var/tmp and handed to the class's own per request settings cache.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once dirname( __DIR__ ) . '/radwizards/expRadWizardTestHelper.php';

class eZStaticCachePathsTest extends PHPUnit\Framework\TestCase
{
    private $injected;
    private $scratch;
    private $savedIniCache;
    private $savedActions;
    private $hadRequestTime;
    private $requestTime;

    public static function setUpBeforeClass(): void
    {
        expRadWizardTestHelper::boot();
    }

    protected function setUp(): void
    {
        expRadWizardTestHelper::boot();
        $this->injected = expRadWizardTestINI::injected();
        $this->scratch = expRadWizardTestHelper::scratch( 'staticcache' );
        $this->savedIniCache = $this->staticProperty( 'siteAccessIniCache' );
        $this->savedActions = $this->staticProperty( 'actionList' );
        $this->setStaticProperty( 'actionList', array() );
        $this->hadRequestTime = array_key_exists( 'REQUEST_TIME_FLOAT', $_SERVER );
        $this->requestTime = $this->hadRequestTime ? $_SERVER['REQUEST_TIME_FLOAT'] : null;
        $_SERVER['REQUEST_TIME_FLOAT'] = 1767225600.25;

        $this->siteAccess( 'k1eroot', array( 'SiteURL' => 'k1e.example.invalid' ) );
        $this->siteAccess( 'k1ebold', array( 'SiteURL' => 'bold.k1e.example.invalid', 'PathPrefix' => 'bold-agency/', 'PathPrefixExclude' => array( 'media', 'Users' ) ) );
        expRadWizardTestHelper::injectIni( 'staticcache.ini', array( 'CacheSettings' => array(
            'HostName' => '', 'StaticStorageDir' => 'static', 'MaxCacheDepth' => '3', 'CachedURLArray' => array( '/', '/news*', '/about' ),
            'CachedSiteAccesses' => array( 'k1eroot', 'k1ebold' ), 'AlwaysUpdateArray' => array( '/' ), 'SourceProtocol' => 'HTTPS',
            'CronjobCacheClear' => 'disabled', 'AppendGeneratedTime' => 'false' ) ) );
        expRadWizardTestHelper::injectIni( 'site.ini', array( 'SiteAccessSettings' => array( 'MatchOrder' => 'uri' ),
                                                              'FileSettings' => array( 'VarDir' => 'var/k1esite' ) ) );
    }

    protected function tearDown(): void
    {
        $this->setStaticProperty( 'actionList', $this->savedActions );
        $this->setStaticProperty( 'siteAccessIniCache', $this->savedIniCache );
        eZINI::injectSettings( $this->injected );
        if ( $this->hadRequestTime )
            $_SERVER['REQUEST_TIME_FLOAT'] = $this->requestTime;
        else
            unset( $_SERVER['REQUEST_TIME_FLOAT'] );
        expRadWizardTestHelper::removeTree( $this->scratch );
    }

    private function staticProperty( $name )
    {
        return ( new ReflectionProperty( 'eZStaticCache', $name ) )->getValue();
    }

    private function setStaticProperty( $name, $value )
    {
        ( new ReflectionProperty( 'eZStaticCache', $name ) )->setValue( null, $value );
    }

    private static function call( $method, array $arguments, $object = null )
    {
        return ( new ReflectionMethod( 'eZStaticCache', $method ) )->invokeArgs( $object, $arguments );
    }

    /**
     * Settings of a made up siteaccess, put where buildCacheDirPart() keeps the ones it has read this request.
     */
    private function siteAccess( $name, array $siteSettings )
    {
        $text = "[SiteSettings]\nSiteURL=" . $siteSettings['SiteURL'] . "\n[SiteAccessSettings]\n";
        if ( isset( $siteSettings['PathPrefix'] ) )
            $text .= "PathPrefix=" . $siteSettings['PathPrefix'] . "\n";
        if ( isset( $siteSettings['PathPrefixExclude'] ) )
        {
            $text .= "PathPrefixExclude[]\n";
            foreach ( $siteSettings['PathPrefixExclude'] as $exclude )
                $text .= "PathPrefixExclude[]=$exclude\n";
        }
        mkdir( $this->scratch . '/' . $name );
        file_put_contents( $this->scratch . '/' . $name . '/site.ini', $text );
        $ini = eZINI::fetchFromFile( substr( $this->scratch, strlen( expRadWizardTestHelper::root() ) + 1 ) . '/' . $name . '/site.ini' );
        $cache = $this->staticProperty( 'siteAccessIniCache' );
        if ( $cache['request'] !== (string)$_SERVER['REQUEST_TIME_FLOAT'] )
            $cache = array( 'request' => (string)$_SERVER['REQUEST_TIME_FLOAT'], 'ini' => array() );
        $cache['ini'][$name] = $ini;
        $this->setStaticProperty( 'siteAccessIniCache', $cache );
    }

    // ---------------------------------------------------------------- storage directory

    public static function storageProvider()
    {
        return array(
            'the shipped word' => array( 'static', 'var/k1esite/static' ),
            'empty' => array( '', 'var/k1esite/static' ),
            'already below var' => array( 'var/k1esite/static/', 'var/k1esite/static' ),
            'below another var' => array( 'var/other/static', 'var/other/static' ),
            'the var directory itself' => array( 'var/k1esite', 'var/k1esite' ),
            'nested' => array( 'pages/static/', 'var/k1esite/pages/static' ),
            'absolute' => array( '/srv/static/', '/srv/static' ),
            'windows' => array( 'C:\\static', 'C:\\static' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('storageProvider')]
    public function testStorageDirectory( $configured, $expected )
    {
        $this->assertSame( $expected, eZStaticCache::resolveStorageDirectory( $configured ) );
    }

    public function testSettingsAreRead()
    {
        $cache = new eZStaticCache();
        $this->assertSame( 'var/k1esite/static', $cache->storageDirectory() );
        $this->assertSame( '3', $cache->maxCacheDepth() );
        $this->assertSame( array( 'k1eroot', 'k1ebold' ), $cache->cachedSiteAccesses() );
        $this->assertSame( array( '/', '/news*', '/about' ), $cache->cachedURLArray() );
        $this->assertSame( array( '/' ), $cache->alwaysUpdateURLArray() );
        $this->assertSame( '', $cache->hostName() );
        $cache->setCachedSiteAccesses( array( 'k1eroot', '', 'k1ebold' ) );
        $this->assertSame( array( 'k1eroot', 'k1ebold' ), $cache->cachedSiteAccesses() );
    }

    // ---------------------------------------------------------------- PathPrefix

    public static function prefixProvider()
    {
        $bold = array( 'path_prefix' => 'bold-agency', 'path_prefix_exclude' => array( 'Media', '/users/' ) );
        return array(
            'no prefix' => array( array( 'path_prefix' => '', 'path_prefix_exclude' => array() ), '/a/b', true, '/a/b' ),
            'front page' => array( $bold, '', true, '' ),
            'the prefix itself' => array( $bold, '/bold-agency', true, '' ),
            'under the prefix' => array( $bold, '/bold-agency/about-us', true, '/about-us' ),
            'case of the prefix' => array( $bold, '/Bold-Agency/about-us', true, '/about-us' ),
            'only starting like the prefix' => array( $bold, '/bold-agency-old/x', false, '/bold-agency-old/x' ),
            'outside the prefix' => array( $bold, '/fit-healthy/x', false, '/fit-healthy/x' ),
            'excluded' => array( $bold, '/media/images/x', true, '/media/images/x' ),
            'excluded with slashes' => array( $bold, '/Users/editors', true, '/Users/editors' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('prefixProvider')]
    public function testPathPrefix( array $dirPart, $url, $served, $stripped )
    {
        $this->assertSame( $served, self::call( 'servesURL', array( $url, $dirPart ) ) );
        $this->assertSame( $stripped, self::call( 'stripPathPrefix', array( $url, $dirPart ) ) );
    }

    // ---------------------------------------------------------------- files and directories of a siteaccess

    public function testFilesOfAUriMatchedSiteaccess()
    {
        $cache = new eZStaticCache();
        $this->assertSame( array( 'var/k1esite/static/k1eroot/news/index.html' ), $cache->cacheFilePathsForURL( 'k1eroot', '/news' ) );
        $this->assertSame( array( 'var/k1esite/static/k1eroot/index.html' ), $cache->cacheFilePathsForURL( 'k1eroot', '' ) );
        $this->assertSame( array( 'var/k1esite/static/k1ebold/about-us/index.html' ), $cache->cacheFilePathsForURL( 'k1ebold', '/bold-agency/about-us' ) );
        $this->assertSame( array( 'var/k1esite/static/k1eroot' ), $cache->cacheDirectoriesForSiteAccess( 'k1eroot' ) );
    }

    public function testFilesOfHostMatchedSiteaccesses()
    {
        expRadWizardTestHelper::injectIni( 'site.ini', array( 'SiteAccessSettings' => array(
            'MatchOrder' => 'host_uri;host',
            'HostUriMatchMapItems' => array( 'k1e.example.invalid;shop;k1eroot', 'k1e.example.invalid;;k1ebold' ),
            'HostMatchMapItems' => array( 'www.k1e.example.invalid;k1eroot', 'other.example.invalid;nobody' ) ) ) );
        $cache = new eZStaticCache();
        $this->assertSame( array( 'var/k1esite/static/k1e.example.invalid/shop/news/index.html', 'var/k1esite/static/www.k1e.example.invalid/news/index.html' ),
                           $cache->cacheFilePathsForURL( 'k1eroot', '/news' ) );
        $this->assertSame( array( 'var/k1esite/static/k1e.example.invalid/shop', 'var/k1esite/static/www.k1e.example.invalid' ), $cache->cacheDirectoriesForSiteAccess( 'k1eroot' ) );
        $this->assertSame( array( 'var/k1esite/static/k1e.example.invalid/about-us/index.html' ), $cache->cacheFilePathsForURL( 'k1ebold', '/bold-agency/about-us' ) );
        $this->assertSame( array(), $cache->cacheFilePathsForURL( 'k1enomatch', '/x' ) );
    }

    public function testUrlsOfASiteaccessWithAPathPrefix()
    {
        $cache = new eZStaticCache();
        $this->assertSame( '/about-us', $cache->siteAccessPath( 'k1ebold', 'bold-agency/about-us' ) );
        $this->assertSame( '/', $cache->siteAccessPath( 'k1ebold', '/bold-agency/' ) );
        $this->assertSame( '/media/x', $cache->siteAccessPath( 'k1ebold', 'media/x' ) );
        $this->assertFalse( $cache->siteAccessPath( 'k1ebold', 'fit-healthy/x' ) );
        $this->assertSame( '/fit-healthy/x', $cache->siteAccessPath( 'k1eroot', 'fit-healthy/x' ) );
    }

    // ---------------------------------------------------------------- what is cached

    public static function cachedUrlProvider()
    {
        return array(
            'front page' => array( '/', true ), 'listed' => array( '/about', true ), 'under a wildcard' => array( '/news/2026/x', false ),
            'wildcard' => array( '/news/today', true ), 'starting like the wildcard' => array( '/newsletter', true ),
            'not listed' => array( '/contact', false ), 'deeper than allowed' => array( '/news/a/b/c', false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('cachedUrlProvider')]
    public function testWhichUrlsAreCached( $url, $cached )
    {
        $cache = new eZStaticCache();
        $this->assertSame( $cached, $cache->cacheURL( $url ) );
        $actions = $this->staticProperty( 'actionList' );
        if ( !$cached )
            $this->assertSame( array(), $actions );
        else
            $this->assertNotEmpty( $actions );
    }

    public function testAPublishQueuesEachPageOnceForEachSiteaccessThatServesIt()
    {
        $cache = new eZStaticCache();
        $this->assertTrue( $cache->cacheURL( '/news', 59 ) );
        $actions = $this->staticProperty( 'actionList' );
        $this->assertSame( array(
            array( 'store', array( 'var/k1esite/static/k1eroot/news/index.html', 'https://k1e.example.invalid/k1eroot/news' ) ),
            array( 'store', array( 'var/k1esite/static/k1eroot/content/view/full/59/index.html', 'https://k1e.example.invalid/k1eroot/news' ) ),
        ), $actions, 'the page under bold-agency is not one of /news' );

        $this->setStaticProperty( 'actionList', array() );
        $this->assertTrue( $cache->cacheURL( '/', false ) );
        $this->assertSame( array( 'var/k1esite/static/k1eroot/index.html', 'var/k1esite/static/k1ebold/index.html' ),
                           array_map( function ( $action ) { return $action[1][0]; }, $this->staticProperty( 'actionList' ) ) );
        $this->assertSame( 'https://bold.k1e.example.invalid/k1ebold/', $this->staticProperty( 'actionList' )[1][1][1] );
    }

    public function testAnExistingFileIsKeptWhenAskedTo()
    {
        expRadWizardTestHelper::injectIni( 'staticcache.ini', array( 'CacheSettings' => array( 'StaticStorageDir' => substr( $this->scratch, strlen( expRadWizardTestHelper::root() ) + 1 ) . '/pages', 'CachedSiteAccesses' => array( 'k1eroot' ) ) ) );
        $cache = new eZStaticCache();
        $file = $cache->cacheFilePathsForURL( 'k1eroot', '/about' )[0];
        eZStaticCache::storeCachedFile( $file, 'kept' );
        $this->assertTrue( $cache->cacheURL( '/about', false, true ) );
        $this->assertSame( array(), $this->staticProperty( 'actionList' ) );
        $this->assertTrue( $cache->cacheURL( '/about', false, false ) );
        $this->assertCount( 1, $this->staticProperty( 'actionList' ) );
    }

    // ---------------------------------------------------------------- one file

    public function testStoringAndRemovingACacheFile()
    {
        $file = $this->scratch . '/deep/dir/index.html';
        eZStaticCache::storeCachedFile( $file, '<html>k1e</html>' );
        $this->assertSame( '<html>k1e</html>', file_get_contents( $file ) );
        $this->assertSame( array( 'index.html' ), array_values( array_diff( scandir( dirname( $file ) ), array( '.', '..' ) ) ), 'no temporary file is left' );
        $this->assertSame( octdec( eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' ) ), fileperms( $file ) & 0777 );

        expRadWizardTestHelper::injectIni( 'staticcache.ini', array( 'CacheSettings' => array( 'AppendGeneratedTime' => 'true' ) ) );
        eZStaticCache::storeCachedFile( $file, 'again' );
        $this->assertStringStartsWith( "again<!-- Generated: ", file_get_contents( $file ) );

        expRadWizardTestHelper::injectIni( 'staticcache.ini', array( 'CacheSettings' => array( 'StaticStorageDir' => substr( $this->scratch, strlen( expRadWizardTestHelper::root() ) + 1 ) ) ) );
        $cache = new eZStaticCache();
        $cache->removeURL( '/deep/dir' );
        $this->assertFileDoesNotExist( $file );
        $this->assertDirectoryDoesNotExist( dirname( $file ) );
    }

    public function testNothingQueuedIsNothingDone()
    {
        $this->assertSame( 0, eZStaticCache::executeActions() );
    }
}
