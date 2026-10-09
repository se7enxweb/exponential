<?php
/**
 * The limits for the modes of new files and directories (EZP_FILE_MODE_MAX, EZP_DIR_MODE_MAX in config.php;
 * doc/bc/6.0/file-modes.md), in the whole installation. No database is needed.
 *
 *  FM-01 - The helpers: a mode is only ever narrowed; without the constants nothing changes; the umask that replaces
 *          umask( 0 ); how a constant is read (0750, "0750", "00750", "750", 750 written without the 0, nonsense)
 *  FM-05 - With one constant only, the other limit follows from it (0640 for files gives 0750 for directories, 0750
 *          for directories gives 0640 for files)
 *  FM-06 - A constant that is no mode, or a limit that takes rights from the owner (0, 0640 for directories), is
 *          ignored (no limit, as without it) and reported once; never 0000
 *  FM-02 - With the limits, everything the installation creates stays inside them: directories (eZDir::mkdir(), its
 *          parents, StorageDirPermissions=0777), files (eZFile::create(), file_put_contents() under the umask set at
 *          start-up), logs (eZLog with LogFilePermissions=0666), the INI cache, PHP cache files (eZPHPCreator)
 *  FM-03 - Without the limits the modes asked for are kept, as before
 *  FM-04 - No code of the installation gives a file or directory its mode past the helpers: every native chmod(),
 *          mkdir() and umask() in kernel, lib, bin, cronjobs, update, the extensions of the repository and the entry
 *          scripts uses eZFile::fileMode(), eZDir::dirMode(), eZFile::executableMode() or eZFile::creationUmask()
 *          (BYPASSES is empty; a new call that bypasses them fails)
 *  FM-07 - Executables keep their execute bits within the directory limit; expAuditWriter::ownLikeParent() limits a
 *          directory as a directory and a file as a file
 *  FM-08 - The code that runs before the autoloader (the HTTP cache early exit, the repair queue) loads the helpers
 *          itself
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

require_once __DIR__ . '/fixtures/expfilemodecalls.php';

class expFileModeLimitsTest extends PHPUnit\Framework\TestCase
{
    /**
     * Calls that still bypass the limits, per file (path => number of calls). Empty: every native chmod(), mkdir() and
     * umask() goes through the helpers. An entry needs a reason that no helper fits.
     */
    const BYPASSES = array();

    /** @var string Absolute path of this test's directory under var/tmp */
    private $dir;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->dir = getcwd() . '/var/tmp/expfilemode_' . getmypid() . '_' . bin2hex( random_bytes( 4 ) );
        mkdir( $this->dir, 0755, true );
    }

    protected function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        eZDir::recursiveDelete( $this->dir );
    }

    private function mode( $path )
    {
        clearstatcache();
        return fileperms( $path ) & 0777;
    }

    /** FM-01 */
    public function testReadingAMode()
    {
        $this->assertSame( 0750, eZFile::modeFromSetting( 0750 ) );
        $this->assertSame( 0750, eZFile::modeFromSetting( '0750' ) );
        $this->assertSame( 0750, eZFile::modeFromSetting( '00750' ) );
        $this->assertSame( 0640, eZFile::modeFromSetting( ' 640 ' ) );
        $this->assertSame( 0, eZFile::modeFromSetting( 0 ) );
        $this->assertSame( 0750, eZFile::modeFromSetting( 750 ), 'an integer above 0777 written without the 0' );
        $this->assertSame( 0670, eZFile::modeFromSetting( 440 ), 'below 0777 an integer is taken as it is (write the 0)' );
        foreach ( array( 'rwxr-x---', 07777, -1, '0778', '', 1000 ) as $nonsense )
        {
            $this->assertNull( eZFile::modeFromSetting( $nonsense ), var_export( $nonsense, true ) );
        }
    }

    /** FM-01, FM-03 */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState( false )]
    public function testWithoutLimitsNothingChanges()
    {
        if ( defined( 'EZP_FILE_MODE_MAX' ) || defined( 'EZP_DIR_MODE_MAX' ) )
        {
            $this->markTestSkipped( 'config.php of this installation sets the limits' );
        }
        $this->assertNull( eZFile::fileModeLimit() );
        $this->assertNull( eZDir::dirModeLimit() );
        $this->assertSame( 0666, eZFile::fileMode( 0666 ) );
        $this->assertSame( 0777, eZDir::dirMode( 0777 ) );
        $this->assertSame( 0, eZFile::creationUmask(), 'umask( 0 ) as before' );

        $umask = umask();
        eZFile::applyCreationUmask();
        $this->assertSame( $umask, umask(), 'the umask of the server is left alone' );

        $this->assertTrue( eZDir::mkdir( $this->dir . '/a/b', 0777, true ) );
        $this->assertSame( 0777, $this->mode( $this->dir . '/a/b' ) );
        $this->assertSame( 0777, $this->mode( $this->dir . '/a' ) );
    }

    /** FM-01, FM-02 */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState( false )]
    public function testWithLimitsEverythingStaysInside()
    {
        define( 'EZP_DIR_MODE_MAX', 0750 );
        define( 'EZP_FILE_MODE_MAX', '0640' );
        $this->assertSame( 0640, eZFile::fileMode( 0666 ) );
        $this->assertSame( 0600, eZFile::fileMode( 0600 ), 'a narrower mode stays' );
        $this->assertSame( 0750, eZDir::dirMode( 0777 ) );
        $this->assertSame( 0700, eZDir::dirMode( 0700 ) );
        $this->assertSame( 0027, eZFile::creationUmask() );
        $this->assertSame( 0077, eZFile::creationUmask( 0077 ), 'a narrower umask asked for stays' );
        $this->assertSame( 0750, eZFile::executableMode( 0755 ), 'FM-07: still executable' );
        umask( 0002 );
        eZFile::applyCreationUmask();
        $this->assertSame( 0027, umask(), 'the umask at start-up: the server umask with what the limits forbid' );

        // directories, also the parents of a recursive one and the default from StorageDirPermissions
        $this->assertTrue( eZDir::mkdir( $this->dir . '/a/b', 0777, true ) );
        $this->assertSame( 0750, $this->mode( $this->dir . '/a/b' ) );
        $this->assertSame( 0750, $this->mode( $this->dir . '/a' ) );
        ezpINIHelper::setINISetting( 'site.ini', 'FileSettings', 'StorageDirPermissions', '0777' );
        $this->assertSame( 0750, eZDir::directoryPermission() );
        $this->assertTrue( eZDir::mkdir( $this->dir . '/c' ) );
        $this->assertSame( 0750, $this->mode( $this->dir . '/c' ) );

        // files: eZFile::create() and a plain file_put_contents() under the umask of the process
        $this->assertTrue( eZFile::create( 'x.txt', $this->dir . '/d', 'x' ) );
        $this->assertSame( 0750, $this->mode( $this->dir . '/d' ) );
        $this->assertSame( 0640, $this->mode( $this->dir . '/d/x.txt' ) );
        file_put_contents( $this->dir . '/plain.txt', 'x' );
        $this->assertSame( 0640, $this->mode( $this->dir . '/plain.txt' ) );

        // a log with LogFilePermissions=0666 in a new directory
        ezpINIHelper::setINISetting( 'site.ini', 'FileSettings', 'LogFilePermissions', '0666' );
        eZLog::write( 'x1 file mode test', 'x1.log', $this->dir . '/log' );
        $this->assertSame( 0750, $this->mode( $this->dir . '/log' ) );
        $this->assertSame( 0640, $this->mode( $this->dir . '/log/x1.log' ) );

        // the INI cache directory (0777 asked for) and its file
        $cacheDirBefore = isset( $GLOBALS['eZINI_CONFIG_CACHE_DIR'] ) ? $GLOBALS['eZINI_CONFIG_CACHE_DIR'] : null;
        $GLOBALS['eZINI_CONFIG_CACHE_DIR'] = $this->dir . '/cache/ini/';
        $wasEnabled = eZINI::isCacheEnabled();
        eZINI::setIsCacheEnabled( true );
        try
        {
            new eZINI( 'site.ini', 'settings', null, true );
        }
        finally
        {
            eZINI::setIsCacheEnabled( $wasEnabled );
            $GLOBALS['eZINI_CONFIG_CACHE_DIR'] = $cacheDirBefore;
        }
        $this->assertSame( 0750, $this->mode( $this->dir . '/cache/ini' ) );
        $this->assertSame( 0750, $this->mode( $this->dir . '/cache' ) );
        $cacheFiles = glob( $this->dir . '/cache/ini/*.php' );
        $this->assertNotEmpty( $cacheFiles );
        $this->assertSame( 0640, $this->mode( $cacheFiles[0] ), 'EZP_INI_FILE_PERMISSION 0644, limited to 0640' );

        // a PHP cache file with StorageFilePermissions=0666
        ezpINIHelper::setINISetting( 'site.ini', 'FileSettings', 'StorageFilePermissions', '0666' );
        $creator = new eZPHPCreator( $this->dir . '/php', 'x1.php' );
        $creator->addVariable( 'x', 1 );
        $this->assertTrue( (bool)$creator->store() );
        $this->assertSame( 0750, $this->mode( $this->dir . '/php' ) );
        $this->assertSame( 0640, $this->mode( $this->dir . '/php/x1.php' ) );

        // FM-07: the audit trail's helper for a directory and for a file
        mkdir( $this->dir . '/audit', 0700 );
        expAuditWriter::ownLikeParent( $this->dir . '/audit', 0770 );
        $this->assertSame( 0750, $this->mode( $this->dir . '/audit' ), 'a directory keeps its search bits' );
        file_put_contents( $this->dir . '/audit/x.log', 'x' );
        expAuditWriter::ownLikeParent( $this->dir . '/audit/x.log', 0660 );
        $this->assertSame( 0640, $this->mode( $this->dir . '/audit/x.log' ) );

        // nothing under the test directory is wider than the limits
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->dir, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::SELF_FIRST );
        foreach ( $iterator as $item )
        {
            $limit = $item->isDir() ? 0750 : 0640;
            $this->assertSame( 0, $this->mode( $item->getPathname() ) & ~$limit, $item->getPathname() . ' is inside the limit' );
        }
    }

    /** FM-05 */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState( false )]
    public function testOneConstantGivesTheOtherLimit()
    {
        if ( defined( 'EZP_DIR_MODE_MAX' ) )
        {
            $this->markTestSkipped( 'config.php of this installation sets the limits' );
        }
        define( 'EZP_FILE_MODE_MAX', 0640 );
        $this->assertSame( 0640, eZFile::fileModeLimit() );
        $this->assertSame( 0750, eZDir::dirModeLimit() );
        $this->assertSame( 0027, eZFile::creationUmask() );
        umask( 0 );
        eZFile::applyCreationUmask();
        $this->assertSame( 0027, umask() );
        file_put_contents( $this->dir . '/plain.txt', 'x' );
        mkdir( $this->dir . '/plain' );
        $this->assertSame( 0640, $this->mode( $this->dir . '/plain.txt' ) );
        $this->assertSame( 0750, $this->mode( $this->dir . '/plain' ) );
    }

    /** FM-02: with a pair that does not match, what is written without a mode of its own keeps to both limits */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState( false )]
    public function testAnUnmatchedPairKeepsPlainFilesInsideBothLimits()
    {
        if ( defined( 'EZP_FILE_MODE_MAX' ) || defined( 'EZP_DIR_MODE_MAX' ) )
        {
            $this->markTestSkipped( 'config.php of this installation sets the limits' );
        }
        define( 'EZP_DIR_MODE_MAX', 0750 );
        define( 'EZP_FILE_MODE_MAX', 0600 );
        $this->assertSame( 0067, eZFile::creationUmask() );
        umask( 0 );
        eZFile::applyCreationUmask();
        file_put_contents( $this->dir . '/plain.txt', 'x' );
        mkdir( $this->dir . '/plain' );
        $this->assertSame( 0600, $this->mode( $this->dir . '/plain.txt' ), 'not 0640: the file limit is 0600' );
        $this->assertSame( 0710, $this->mode( $this->dir . '/plain' ) );
        $this->assertTrue( eZFile::create( 'x.txt', $this->dir . '/d', 'x' ) );
        $this->assertSame( 0600, $this->mode( $this->dir . '/d/x.txt' ) );
    }

    /** FM-02: a file limit wider than the directory limit cannot widen a directory made without a mode of its own */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState( false )]
    public function testAWiderFileLimitDoesNotWidenPlainDirectories()
    {
        if ( defined( 'EZP_FILE_MODE_MAX' ) || defined( 'EZP_DIR_MODE_MAX' ) )
        {
            $this->markTestSkipped( 'config.php of this installation sets the limits' );
        }
        define( 'EZP_DIR_MODE_MAX', 0700 );
        define( 'EZP_FILE_MODE_MAX', 0666 );
        umask( 0 );
        eZFile::applyCreationUmask();
        mkdir( $this->dir . '/plain' );
        $this->assertSame( 0700, $this->mode( $this->dir . '/plain' ), 'not 0766' );
    }

    /** FM-05 */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState( false )]
    public function testTheDirectoryLimitAloneGivesTheFileLimit()
    {
        if ( defined( 'EZP_FILE_MODE_MAX' ) )
        {
            $this->markTestSkipped( 'config.php of this installation sets the limits' );
        }
        define( 'EZP_DIR_MODE_MAX', 0750 );
        $this->assertSame( 0640, eZFile::fileModeLimit() );
        $this->assertSame( 0750, eZDir::dirModeLimit() );
        $this->assertSame( 0640, eZFile::fileMode( 0666 ) );
    }

    /** FM-06 */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState( false )]
    public function testAMistypedConstantIsIgnoredAndReported()
    {
        if ( defined( 'EZP_FILE_MODE_MAX' ) || defined( 'EZP_DIR_MODE_MAX' ) )
        {
            $this->markTestSkipped( 'config.php of this installation sets the limits' );
        }
        define( 'EZP_FILE_MODE_MAX', 'rw-r-----' );
        define( 'EZP_DIR_MODE_MAX', 0x1ff0 );
        $log = $this->dir . '/php-error.log';
        $previousLog = ini_set( 'error_log', $log );
        try
        {
            $this->assertNull( eZFile::fileModeLimit(), 'no limit, as without the constant' );
            $this->assertNull( eZDir::dirModeLimit() );
            $this->assertSame( 0666, eZFile::fileMode( 0666 ) );
            $this->assertSame( 0777, eZDir::dirMode( 0777 ) );
            $this->assertSame( 0, eZFile::creationUmask() );
            umask( 0022 );
            eZFile::applyCreationUmask();
            $this->assertSame( 0022, umask(), 'the umask of the server is left alone' );
            eZFile::fileModeLimit();
            eZDir::dirModeLimit();
        }
        finally
        {
            ini_set( 'error_log', $previousLog );
        }
        $lines = file( $log, FILE_IGNORE_NEW_LINES );
        $this->assertCount( 2, $lines, 'each mistyped constant is reported once' );
        $this->assertStringContainsString( 'EZP_FILE_MODE_MAX in config.php is ignored, it is no file mode', $lines[0] );
        $this->assertStringContainsString( 'EZP_DIR_MODE_MAX in config.php is ignored', $lines[1] );
    }

    /** FM-06 */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState( false )]
    public function testALimitThatTakesRightsFromTheOwnerIsIgnored()
    {
        if ( defined( 'EZP_FILE_MODE_MAX' ) || defined( 'EZP_DIR_MODE_MAX' ) )
        {
            $this->markTestSkipped( 'config.php of this installation sets the limits' );
        }
        // 0 would make every new file 0000, '0640' a directory its owner cannot enter: never applied
        define( 'EZP_FILE_MODE_MAX', 0 );
        define( 'EZP_DIR_MODE_MAX', '0640' );
        $log = $this->dir . '/php-error.log';
        $previousLog = ini_set( 'error_log', $log );
        try
        {
            $this->assertNull( eZFile::fileModeLimit() );
            $this->assertNull( eZDir::dirModeLimit() );
            $this->assertSame( 0, eZFile::creationUmask() );
            $this->assertTrue( eZFile::create( 'x.txt', $this->dir . '/d', 'x' ) );
            $this->assertSame( 0600, $this->mode( $this->dir . '/d/x.txt' ) & 0600, 'never 0000' );
        }
        finally
        {
            ini_set( 'error_log', $previousLog );
        }
        $log = file_get_contents( $log );
        $this->assertStringContainsString( 'EZP_FILE_MODE_MAX in config.php is ignored, 0000 takes rights from the owner', $log );
        $this->assertStringContainsString( 'EZP_DIR_MODE_MAX in config.php is ignored, 0640 takes rights from the owner', $log );
    }

    /** FM-06 */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState( false )]
    public function testAnIgnoredConstantLeavesTheOtherInForce()
    {
        if ( defined( 'EZP_FILE_MODE_MAX' ) || defined( 'EZP_DIR_MODE_MAX' ) )
        {
            $this->markTestSkipped( 'config.php of this installation sets the limits' );
        }
        define( 'EZP_FILE_MODE_MAX', 0040 );
        define( 'EZP_DIR_MODE_MAX', 0750 );
        $previousLog = ini_set( 'error_log', $this->dir . '/php-error.log' );
        try
        {
            $this->assertSame( 0640, eZFile::fileModeLimit(), 'from the directory limit, as if only that were set' );
            $this->assertSame( 0750, eZDir::dirModeLimit() );
        }
        finally
        {
            ini_set( 'error_log', $previousLog );
        }
    }

    /** FM-01 */
    public function testAValueOfAnotherTypeIsNoModeAndRaisesNothing()
    {
        error_clear_last();
        foreach ( array( array( 0750 ), 488.0, true, null, new stdClass() ) as $value )
        {
            $this->assertNull( eZFile::modeFromSetting( $value ), var_export( $value, true ) );
        }
        $this->assertNull( error_get_last() );
    }

    /** FM-03: without limits the autoload arrays are written with the modes of before */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState( false )]
    public function testWithoutLimitsTheAutoloadArraysKeepTheirModes()
    {
        if ( defined( 'EZP_FILE_MODE_MAX' ) || defined( 'EZP_DIR_MODE_MAX' ) || defined( 'EZP_INI_FILE_PERMISSION' ) )
        {
            $this->markTestSkipped( 'config.php of this installation sets the limits or EZP_INI_FILE_PERMISSION' );
        }
        umask( 0022 );
        $file = expFileModeAutoloadWriter::write( $this->dir . '/autoload' );
        $this->assertSame( 0755, $this->mode( $this->dir . '/autoload' ), '0777 under the umask of the server' );
        $this->assertSame( 0777, $this->mode( $file ), 'chmod 0777, as before' );
    }

    /** FM-03 */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState( false )]
    public function testWithoutLimitsTheAutoloadArraysKeepEzpIniFilePermission()
    {
        if ( defined( 'EZP_FILE_MODE_MAX' ) || defined( 'EZP_DIR_MODE_MAX' ) || defined( 'EZP_INI_FILE_PERMISSION' ) )
        {
            $this->markTestSkipped( 'config.php of this installation sets the limits or EZP_INI_FILE_PERMISSION' );
        }
        define( 'EZP_INI_FILE_PERMISSION', 0751 );
        umask( 0 );
        $file = expFileModeAutoloadWriter::write( $this->dir . '/autoload' );
        $this->assertSame( 0751, $this->mode( $this->dir . '/autoload' ), 'the directory with EZP_INI_FILE_PERMISSION, as before' );
        $this->assertSame( 0751, $this->mode( $file ) );
    }

    /** FM-02 */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState( false )]
    public function testWithLimitsTheAutoloadArraysStayInside()
    {
        if ( defined( 'EZP_FILE_MODE_MAX' ) || defined( 'EZP_DIR_MODE_MAX' ) || defined( 'EZP_INI_FILE_PERMISSION' ) )
        {
            $this->markTestSkipped( 'config.php of this installation sets the limits or EZP_INI_FILE_PERMISSION' );
        }
        define( 'EZP_INI_FILE_PERMISSION', 0644 );
        define( 'EZP_DIR_MODE_MAX', 0750 );
        umask( 0 );
        eZFile::applyCreationUmask();
        $file = expFileModeAutoloadWriter::write( $this->dir . '/autoload' );
        $this->assertSame( 0750, $this->mode( $this->dir . '/autoload' ), 'a directory mode, not the file mode 0644' );
        $this->assertSame( 0640, $this->mode( $file ) );
    }

    /** FM-03: without limits an image variation gets all of ImagePermissions, as before */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState( false )]
    public function testWithoutLimitsAnImageGetsImagePermissionsAsBefore()
    {
        if ( defined( 'EZP_FILE_MODE_MAX' ) || defined( 'EZP_DIR_MODE_MAX' ) )
        {
            $this->markTestSkipped( 'config.php of this installation sets the limits' );
        }
        $image = $this->dir . '/x.png';
        file_put_contents( $image, 'x' );
        chmod( $image, 0600 );
        ezpINIHelper::setINISetting( 'image.ini', 'FileSettings', 'ImagePermissions', '1664' );
        $this->assertTrue( eZImageHandler::changeFilePermissions( $image ) );
        clearstatcache();
        $this->assertSame( 01664, fileperms( $image ) & 07777 );
    }

    /** FM-02 */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState( false )]
    public function testWithLimitsAnImageStaysInside()
    {
        if ( defined( 'EZP_FILE_MODE_MAX' ) || defined( 'EZP_DIR_MODE_MAX' ) )
        {
            $this->markTestSkipped( 'config.php of this installation sets the limits' );
        }
        define( 'EZP_FILE_MODE_MAX', 0640 );
        $image = $this->dir . '/x.png';
        file_put_contents( $image, 'x' );
        chmod( $image, 0600 );
        ezpINIHelper::setINISetting( 'image.ini', 'FileSettings', 'ImagePermissions', '0666' );
        $this->assertTrue( eZImageHandler::changeFilePermissions( $image ) );
        $this->assertSame( 0640, $this->mode( $image ) );
        $this->assertTrue( eZImageHandler::changeFilePermissions( $image ), 'nothing to change: no chmod' );
    }

    /** FM-08 */
    public function testCodeBeforeTheAutoloaderHasTheHelpers()
    {
        $code = 'require "lib/ezutils/classes/ezprepairqueue.php"; require "kernel/private/classes/httpcache/ezphttpcachecontract.php"; ' .
                'echo class_exists( "ezpAutoloader", false ) ? "autoloader" : "plain", " ", eZFile::fileMode( 0660 ), " ", eZDir::dirMode( 0770 );';
        $output = array();
        exec( escapeshellarg( PHP_BINARY ) . ' -n -r ' . escapeshellarg( $code ) . ' 2>&1', $output, $status );
        $this->assertSame( 0, $status, implode( "\n", $output ) );
        $this->assertSame( 'plain ' . 0660 . ' ' . 0770, implode( "\n", $output ) );
    }

    /** FM-04 */
    public function testNoCodeBypassesTheLimits()
    {
        $found = array();
        foreach ( expFileModeCalls::bypasses( getcwd() ) as $path => $lines )
        {
            $found[$path] = count( $lines );
        }
        $new = array();
        foreach ( $found as $path => $count )
        {
            $listed = isset( self::BYPASSES[$path] ) ? self::BYPASSES[$path] : 0;
            if ( $count > $listed )
            {
                $new[] = "$path: $count calls, $listed listed";
            }
        }
        $this->assertSame( array(), $new, 'chmod(), mkdir() or umask() with a mode that does not go through eZFile::fileMode(), ' .
                                          'eZDir::dirMode() or eZFile::creationUmask()' );
        $stale = array();
        foreach ( self::BYPASSES as $path => $listed )
        {
            $count = isset( $found[$path] ) ? $found[$path] : 0;
            if ( $count < $listed )
            {
                $stale[] = "$path: $listed listed, $count left";
            }
        }
        $this->assertSame( array(), $stale, 'moved to the helpers: lower or remove the entry in BYPASSES' );
    }

    /** FM-04: command line scripts without the .php ending are scanned too (bin/php/console made scripts 0755) */
    public function testTheScannerReadsCommandLineScriptsWithoutAnEnding()
    {
        $files = expFileModeCalls::files( getcwd() );
        $this->assertContains( 'bin/php/console', $files );
        $this->assertContains( 'kernel/classes/ezpackage.php', $files );
        $this->assertNotContains( 'bin/modfix.sh', $files );
        $this->assertFalse( expFileModeCalls::isPhpScript( getcwd() . '/bin/linux/doxygen' ), 'a binary' );
        $script = $this->dir . '/runme';
        foreach ( array( "#!/usr/bin/env php\n<?php" => true, "#!/usr/bin/php8.5 -q\n" => true,
                         "#!/bin/sh\nphp x" => false, "<?php\n" => false ) as $head => $isPhp )
        {
            file_put_contents( $script, $head );
            $this->assertSame( $isPhp, expFileModeCalls::isPhpScript( $script ), $head );
        }
    }

    /** FM-04 */
    public function testTheScannerFindsWhatBypasses()
    {
        $source = '<?php
            chmod( $a, 0666 );
            chmod( $a, eZFile::fileMode( 0666 ) );
            mkdir( $d, 0777, true );
            mkdir( $d );
            mkdir( $d, eZDir::dirMode( 0777 ), true );
            $old = umask( 0 );
            umask( $old );
            umask( eZFile::creationUmask() );
            umask();
            eZDir::mkdir( $d, 0777, true );
            $handler->mkdir( $d, 0777 );
            function mkdir2( $x ) {}
            chmod( $a, octdec( $perm ) );
            \\chmod( $a, 0666 );
            \\chmod( $a, \\eZFile::fileMode( 0666 ) );
            chmod( $a, eZFile::fileMode( 0 ) | 0777 );
            umask( $m );
            $oldUmask = umask( eZFile::creationUmask() );
            umask( $oldUmask );
            chmod( $bin, eZFile::executableMode( 0755 ) );';
        $this->assertSame( array( 2, 4, 5, 7, 14, 15, 17, 18 ), expFileModeCalls::bypassesIn( $source ) );
    }
}

/**
 * Writes one autoload array into a directory of the test (eZAutoloadGenerator::writeAutoloadFiles() is protected).
 */
class expFileModeAutoloadWriter extends eZAutoloadGenerator
{
    /**
     * @param string $dir The output directory, created by the generator
     * @return string The path of the file written
     */
    public static function write( $dir )
    {
        $generator = new self( new ezpAutoloadGeneratorOptions( array( 'outputDir' => $dir ) ) );
        $generator->autoloadArrays = array( self::MODE_EXTENSION => "'expFileModeX' => 'x.php',\n" );
        $generator->writeAutoloadFiles();
        return $dir . '/ezp_extension.php';
    }
}
