<?php
/**
 * [Include <pattern>] inside settings files: the pattern is relative to the including file and may hold wildcards,
 * the files are read in name order where the line stands, includes nest, a loop is read once with a warning, and a
 * pattern that matches nothing is a notice. The included files (and the directories their patterns look in) are
 * recorded in the INI cache: with the file time check a changed or added file rebuilds the cache, and with the
 * check off (as config.php sets it) clearing the INI cache, as --clear-tag=ini does, picks the change up. The INI
 * editor's direct-access instance does not read the included files and keeps the line on save.
 *
 * The tests work on a settings tree and an INI cache directory of their own below var/tmp of the installation root;
 * the installation's settings and var/cache/ini are not touched.
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/ezutils/eZINIIncludeTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit f7c382bc2b), Copyright (C) Mugo Web, GPL v2
 */

class eZINIIncludeTest extends PHPUnit\Framework\TestCase
{
    const DEBUG_KEYS = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly', 'eZDebugAlwaysLog', 'eZDebugLogFileEnabled' );

    /** The test tree, relative to the installation root */
    private $base;
    /** Settings directory of the tree */
    private $rootDir;
    private $savedCacheDir;
    private $savedCheckMtime;
    private $savedDebug = array();
    private $debug;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->base = 'var/tmp/ezini_include_' . uniqid( '', true );
        $this->rootDir = $this->base . '/settings';
        mkdir( $this->rootDir, 0700, true );
        mkdir( $this->base . '/cache', 0700, true );
        mkdir( $this->base . '/log', 0700, true );

        $this->savedCacheDir = array_key_exists( 'eZINI_CONFIG_CACHE_DIR', $GLOBALS ) ? array( $GLOBALS['eZINI_CONFIG_CACHE_DIR'] ) : null;
        $GLOBALS['eZINI_CONFIG_CACHE_DIR'] = $this->root() . $this->base . '/cache/';
        $property = new ReflectionProperty( 'eZINI', 'checkFileMtime' );
        $this->savedCheckMtime = $property->getValue();

        foreach ( self::DEBUG_KEYS as $key )
            $this->savedDebug[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        unset( $GLOBALS['eZDebugAlwaysLog'], $GLOBALS['eZDebugLogFileEnabled'] );
        $this->debug = new eZDebug();
        foreach ( $this->debug->LogFiles as $level => $file )
            $this->debug->LogFiles[$level] = array( $this->base . '/log/', $file[1] );
        $GLOBALS['eZDebugGlobalInstance'] = $this->debug;
        $GLOBALS['eZDebugEnabled'] = true;
        $GLOBALS['eZDebugLogOnly'] = false;
    }

    protected function tearDown(): void
    {
        ( new ReflectionProperty( 'eZINI', 'checkFileMtime' ) )->setValue( null, $this->savedCheckMtime );
        if ( $this->savedCacheDir === null )
            unset( $GLOBALS['eZINI_CONFIG_CACHE_DIR'] );
        else
            $GLOBALS['eZINI_CONFIG_CACHE_DIR'] = $this->savedCacheDir[0];
        foreach ( $this->savedDebug as $key => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $saved[0];
        }
        $this->removeDir( $this->root() . $this->base );
    }

    public function testFilesOfARelativePatternAreReadInNameOrderWhereTheLineStands()
    {
        $this->writeFile( 'test.ini', "[Main]\nA=base\nKeep=base\n[Include test.d/*.ini]\n[After]\nC=3\n" );
        $this->writeFile( 'test.d/20-second.ini', "[Main]\nA=second\n" );
        $this->writeFile( 'test.d/10-first.ini', "[Main]\nA=first\nB=first\n[Other]\nX=y\n" );
        $this->writeFile( 'test.d/ignored.txt', "[Main]\nA=txt\n" );

        $ini = $this->ini();
        $this->assertSame( 'second', $ini->variable( 'Main', 'A' ), 'the later file wins, in name order' );
        $this->assertSame( 'first', $ini->variable( 'Main', 'B' ) );
        $this->assertSame( 'base', $ini->variable( 'Main', 'Keep' ) );
        $this->assertSame( 'y', $ini->variable( 'Other', 'X' ) );
        $this->assertSame( '3', $ini->variable( 'After', 'C' ) );
        foreach ( array_keys( $ini->groups() ) as $group )
            $this->assertStringStartsNotWith( 'Include', $group, 'the line is no group' );
        $dir = $this->root() . $this->rootDir;
        $this->assertSame( array( $dir . '/test.d', $dir . '/test.d/10-first.ini', $dir . '/test.d/20-second.ini' ),
                           $ini->includedFiles() );
    }

    public function testTheLineEndsTheGroupBeforeIt()
    {
        $this->writeFile( 'test.ini', "[Main]\nA=1\n[Include inc.ini]\nLoose=1\n" );
        $this->writeFile( 'inc.ini', "[Inc]\nI=1\n" );
        $ini = $this->ini();
        $this->assertFalse( $ini->hasVariable( 'Main', 'Loose' ) );
        $this->assertFalse( $ini->hasVariable( 'Inc', 'Loose' ) );
        $this->assertSame( '1', $ini->variable( 'Inc', 'I' ) );
    }

    public function testASettingAfterTheLineOverridesTheIncludedOne()
    {
        $this->writeFile( 'test.ini', "[Include inc.ini]\n[Main]\nA=after\n" );
        $this->writeFile( 'inc.ini', "[Main]\nA=included\nB=included\n" );
        $ini = $this->ini();
        $this->assertSame( 'after', $ini->variable( 'Main', 'A' ) );
        $this->assertSame( 'included', $ini->variable( 'Main', 'B' ) );
    }

    public function testIncludesNestAndArePathsRelativeToTheIncludingFile()
    {
        $this->writeFile( 'test.ini', "[Include a/*.ini]\n" );
        $this->writeFile( 'a/one.ini', "[A]\nV=1\n[Include ../b/two.ini]\n" );
        $this->writeFile( 'b/two.ini', "[B]\nV=2\n[Include c/*.ini]\n" );
        $this->writeFile( 'b/c/three.ini', "[C]\nV=3\n" );
        $ini = $this->ini();
        $this->assertSame( '1', $ini->variable( 'A', 'V' ) );
        $this->assertSame( '2', $ini->variable( 'B', 'V' ) );
        $this->assertSame( '3', $ini->variable( 'C', 'V' ) );
    }

    public function testAnOverrideFileCanIncludeAndStillWins()
    {
        $this->writeFile( 'test.ini', "[Main]\nA=base\n" );
        $this->writeFile( 'override/test.ini.append.php', "<?php /*\n[Include test.ini.d/*.ini]\n*/ ?>\n" );
        $this->writeFile( 'override/test.ini.d/local.ini', "[Main]\nA=override-include\n" );
        $this->assertSame( 'override-include', $this->ini()->variable( 'Main', 'A' ) );
    }

    public function testALoopIsReadOnceWithAWarning()
    {
        $this->writeFile( 'test.ini', "[Main]\nA=1\n[Include loop/a.ini]\n" );
        $this->writeFile( 'loop/a.ini', "[Loop]\nList[]=a\n[Include b.ini]\n" );
        $this->writeFile( 'loop/b.ini', "[Loop]\nList[]=b\n[Include a.ini]\n[Include ../test.ini]\n" );
        $ini = $this->ini();
        $this->assertSame( array( 'a', 'b' ), $ini->variable( 'Loop', 'List' ), 'every file read once' );
        $this->assertTrue( $this->hasMessage( eZDebug::LEVEL_WARNING, 'again' ) );
    }

    public function testAFileIncludingItselfIsNotReadAgain()
    {
        $this->writeFile( 'test.ini', "[Main]\nList[]=x\n[Include *.ini]\n" );
        $this->assertSame( array( 'x' ), $this->ini()->variable( 'Main', 'List' ) );
    }

    public function testAPatternThatMatchesNothingIsANotice()
    {
        $this->writeFile( 'test.ini', "[Main]\nA=1\n[Include nothing/*.ini]\n" );
        $ini = $this->ini();
        $this->assertSame( '1', $ini->variable( 'Main', 'A' ) );
        $this->assertTrue( $this->hasMessage( eZDebug::LEVEL_NOTICE, 'matches no file' ) );
        $this->assertFalse( $this->hasMessage( eZDebug::LEVEL_ERROR, '' ) );
    }

    public function testAFileWithoutIncludesIsReadAsBefore()
    {
        $this->writeFile( 'test.ini', "[Main]\nA=1\n[Included]\nB=2\n[Includes Some]\nC=3\n" );
        $ini = $this->ini();
        $this->assertSame( '2', $ini->variable( 'Included', 'B' ), 'a group whose name only starts with Include' );
        $this->assertSame( '3', $ini->variable( 'Includes Some', 'C' ) );
        $this->assertSame( array(), $ini->includedFiles() );
    }

    public function testTheCacheRecordsTheIncludedFiles()
    {
        $this->writeFile( 'test.ini', "[Include test.d/*.ini]\n" );
        $this->writeFile( 'test.d/a.ini', "[Main]\nA=1\n" );
        $this->cachedIni();
        $files = glob( $this->root() . $this->base . '/cache/test-*.php' );
        $this->assertCount( 1, $files );
        $data = false;
        include $files[0];
        $dir = $this->root() . $this->rootDir;
        $this->assertSame( array( $dir . '/test.d', $dir . '/test.d/a.ini' ), $data['includes'] );
        $this->assertNotContains( $dir . '/test.d/a.ini', $data['files'], 'not read again as an input file' );

        $this->assertSame( $data['includes'], $this->cachedIni()->includedFiles(), 'known when loaded from the cache' );
    }

    public function testWithTheFileTimeCheckAChangedIncludedFileRebuildsTheCache()
    {
        $this->setCheckMtime( true );
        $this->writeFile( 'test.ini', "[Include test.d/*.ini]\n" );
        $this->writeFile( 'test.d/a.ini', "[Main]\nA=old\n" );
        $this->assertSame( 'old', $this->cachedIni()->variable( 'Main', 'A' ) );
        $this->ageCache();

        $this->writeFile( 'test.d/a.ini', "[Main]\nA=new\n" );
        $this->assertSame( 'new', $this->cachedIni()->variable( 'Main', 'A' ) );
    }

    public function testWithTheFileTimeCheckAnAddedFileRebuildsTheCache()
    {
        $this->setCheckMtime( true );
        $this->writeFile( 'test.ini', "[Include test.d/*.ini]\n" );
        $this->writeFile( 'test.d/a.ini', "[Main]\nA=a\n" );
        $this->assertSame( 'a', $this->cachedIni()->variable( 'Main', 'A' ) );
        $this->ageCache();

        $this->writeFile( 'test.d/b.ini', "[Main]\nA=b\n" );
        $this->assertSame( 'b', $this->cachedIni()->variable( 'Main', 'A' ) );
    }

    public function testWithoutTheFileTimeCheckClearingTheINICacheTakesTheChange()
    {
        $this->setCheckMtime( false );
        $this->writeFile( 'test.ini', "[Include test.d/*.ini]\n" );
        $this->writeFile( 'test.d/a.ini', "[Main]\nA=old\n" );
        $this->assertSame( 'old', $this->cachedIni()->variable( 'Main', 'A' ) );
        $this->ageCache();

        $this->writeFile( 'test.d/a.ini', "[Main]\nA=new\n" );
        $this->assertSame( 'old', $this->cachedIni()->variable( 'Main', 'A' ), 'the cache is used as configured' );

        // What --clear-tag=ini runs for the global INI cache, here on the test's own cache directory
        eZCache::clearGlobalINICache( array( 'path' => $this->base . '/cache' ) );
        $this->assertSame( 'new', $this->cachedIni()->variable( 'Main', 'A' ) );
    }

    public function testVelocityRefreshSeesAChangedIncludedFile()
    {
        $this->writeFile( 'test.ini', "[Include test.d/*.ini]\n" );
        $this->writeFile( 'test.d/a.ini', "[Main]\nA=old\n" );
        // the files a second older than the cache, so they count as read for it
        $this->touchFile( 'test.d/a.ini', time() - 1 );
        $this->touchFile( 'test.ini', time() - 1 );
        $this->cachedIni();
        $this->assertSame( 0, expVelocity::refreshIni( 'test.ini' ), 'a current cache is kept' );
        $this->writeFile( 'test.d/a.ini', "[Main]\nA=new\n" );
        $this->touchFile( 'test.d/a.ini', time() + 2 );
        $this->assertSame( 1, expVelocity::refreshIni( 'test.ini' ) );
    }

    public function testTheEditorDoesNotReadIncludesAndKeepsTheLineOnSave()
    {
        $this->writeFile( 'test.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n\n[Main]\nA=1\n\n# split settings\n[Include test.d/*.ini]\n\n[Last]\nL=1\n*/ ?>\n" );
        $this->writeFile( 'test.d/a.ini', "[Included]\nI=1\n" );

        $ini = new eZINI( 'test.ini.append', $this->rootDir, null, null, null, true, true );
        $this->assertFalse( $ini->hasGroup( 'Included' ), 'included files are not read' );
        $this->assertSame( array(), $ini->includedFiles() );

        $ini->setVariable( 'Main', 'A', '2' );
        $ini->setVariable( 'New', 'N', '1' );
        $this->assertTrue( $ini->save() );
        $saved = file_get_contents( $this->root() . $this->rootDir . '/test.ini.append.php' );
        $this->assertStringContainsString( "# split settings\n[Include test.d/*.ini]\n", $saved );
        $this->assertStringContainsString( "[Main]\nA=2\n", $saved );
        $this->assertStringContainsString( "[Last]\nL=1\n", $saved );
        $this->assertStringContainsString( "[New]\nN=1\n", $saved );
        $this->assertStringNotContainsString( 'Included', $saved, 'no included setting is copied in' );
        $this->assertSame( 1, substr_count( $saved, '[Include' ) );
    }

    public function testExpIniKeepsTheLineAndDoesNotTakeItForABlock()
    {
        $writer = new expIniWriter( "[Main]\nA=1\n[Include test.d/*.ini]\n[Last]\nL=1\n" );
        $this->assertSame( array( 'Main', 'Last' ), $writer->blocks() );
        $this->assertSame( array( 'Main' => array( 'A' => '1' ), 'Last' => array( 'L' => '1' ) ), $writer->values() );
        $writer->insertInBlock( 'Main', array( 'B=2' ) );
        $this->assertSame( "[Main]\nA=1\nB=2\n[Include test.d/*.ini]\n[Last]\nL=1\n", $writer->content(),
                           'a new setting of the group before the line goes in that group, above the line' );
    }

    /** @return eZINI an uncached instance for test.ini of the tree, with an override directory */
    private function ini()
    {
        $ini = new eZINI( 'test.ini', $this->rootDir, null, false, true, false, false, false );
        $this->setDirs( $ini );
        $ini->load();
        return $ini;
    }

    /** @return eZINI a cached instance, the cache in the test's cache directory */
    private function cachedIni()
    {
        $ini = new eZINI( 'test.ini', $this->rootDir, null, true, true, false, false, false );
        $this->setDirs( $ini );
        $ini->load();
        return $ini;
    }

    private function setDirs( eZINI $ini )
    {
        $ini->setOverrideDirs( array( 'sa-extension' => array(),
                                      'siteaccess' => array(),
                                      'extension' => array(),
                                      'override' => array( array( 'override', false ) ) ) );
    }

    /** Dates the cache files a few seconds back, so a file written now is newer than the cache */
    private function ageCache()
    {
        foreach ( glob( $this->root() . $this->base . '/cache/*.php' ) as $file )
        {
            $contents = file_get_contents( $file );
            $contents = preg_replace( "#'created' => '[^']+'#", "'created' => '" . date( 'c', time() - 10 ) . "'", $contents );
            file_put_contents( $file, $contents );
            if ( function_exists( 'opcache_invalidate' ) )
                opcache_invalidate( $file, true );
        }
    }

    private function setCheckMtime( $value )
    {
        ( new ReflectionProperty( 'eZINI', 'checkFileMtime' ) )->setValue( null, $value );
    }

    private function hasMessage( $level, $text )
    {
        foreach ( $this->debug->DebugStrings as $message )
        {
            if ( $message['Level'] == $level && ( $text === '' || strpos( (string)$message['String'], $text ) !== false ) )
                return true;
        }
        return false;
    }

    private function writeFile( $relativePath, $contents )
    {
        $path = $this->root() . $this->rootDir . '/' . $relativePath;
        if ( !is_dir( dirname( $path ) ) )
            mkdir( dirname( $path ), 0700, true );
        file_put_contents( $path, $contents );
        clearstatcache( true, $path );
        clearstatcache( true, dirname( $path ) );
    }

    private function touchFile( $relativePath, $time )
    {
        $path = $this->root() . $this->rootDir . '/' . $relativePath;
        touch( $path, $time );
        clearstatcache( true, $path );
    }

    private function root()
    {
        return rtrim( getcwd(), '/' ) . '/';
    }

    private function removeDir( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        foreach ( scandir( $dir ) as $entry )
        {
            if ( $entry === '.' || $entry === '..' )
                continue;
            $path = $dir . '/' . $entry;
            if ( is_dir( $path ) && !is_link( $path ) )
                $this->removeDir( $path );
            else
                unlink( $path );
        }
        rmdir( $dir );
    }
}
