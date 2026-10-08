<?php
/**
 * The limits for the modes of new files and directories (EZP_FILE_MODE_MAX, EZP_DIR_MODE_MAX in config.php;
 * doc/bc/6.0/file-modes.md), in the whole installation. No database is needed.
 *
 *  FM-01 - The helpers: a mode is only ever narrowed; without the constants nothing changes; the umask that replaces
 *          umask( 0 ); how a constant is read (0750, "0750", "00750", "750", 750 written without the 0, nonsense)
 *  FM-05 - With one constant only, the other limit follows from it (0640 for files gives 0750 for directories, 0750
 *          for directories gives 0640 for files)
 *  FM-06 - A constant that is no mode limits to the owner (0700 / 0600) instead of opening or locking out the site
 *  FM-02 - With the limits, everything the installation creates stays inside them: directories (eZDir::mkdir(), its
 *          parents, StorageDirPermissions=0777), files (eZFile::create(), file_put_contents() under the umask set at
 *          start-up), logs (eZLog with LogFilePermissions=0666), the INI cache, PHP cache files (eZPHPCreator)
 *  FM-03 - Without the limits the modes asked for are kept, as before
 *  FM-04 - No code of the installation gives a file or directory its mode past the helpers: every native chmod(),
 *          mkdir() and umask() in kernel, lib, bin, cronjobs, update, the extensions of the repository and the entry
 *          scripts uses eZFile::fileMode(), eZDir::dirMode() or eZFile::creationUmask() (BYPASSES lists what is still
 *          to be moved, file by file; a new call, or a list that no longer matches, fails)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

require_once __DIR__ . '/fixtures/expfilemodecalls.php';

class expFileModeLimitsTest extends PHPUnit\Framework\TestCase
{
    /**
     * Calls that still bypass the limits, per file, until they are moved to the helpers. Only ever shrinks.
     */
    const BYPASSES = array(
        'bin/php/install.php'                                                       => 3,
        'extension/ezoe/classes/expoeurlfetcher.php'                                => 3,
        'kernel/classes/audit/archive/expauditarchiver.php'                         => 3,
        'kernel/classes/audit/expauditkeys.php'                                     => 1,
        'kernel/classes/audit/expauditwriter.php'                                   => 3,
        'kernel/classes/contentjob/expcontentjob.php'                               => 1,
        'kernel/classes/contentjob/expcontentjobstore.php'                          => 4,
        'kernel/classes/contentjob/expcontentjobworker.php'                         => 1,
        'kernel/classes/datatypes/ezbinaryfile/ezbinaryfiletype.php'                => 1,
        'kernel/classes/datatypes/ezbinaryfile/plugins/ezwordparser.php'            => 1,
        'kernel/classes/debugbar/expdebugbarlog.php'                                => 2,
        'kernel/classes/expcachemanager.php'                                        => 1,
        'kernel/classes/expkickstarterini.php'                                      => 2,
        'kernel/classes/expmaintenance.php'                                         => 2,
        'kernel/classes/expnotificationservice.php'                                 => 2,
        'kernel/classes/expphar.php'                                                => 1,
        'kernel/classes/exppreloadhistory.php'                                      => 2,
        'kernel/classes/exppreloadlock.php'                                         => 1,
        'kernel/classes/expsetuplog.php'                                            => 1,
        'kernel/classes/expvelocity.php'                                            => 2,
        'kernel/classes/expvelocityconfig.php'                                      => 2,
        'kernel/classes/expvelocityconfiglayout.php'                                => 4,
        'kernel/classes/expvelocityfrankenphp.php'                                  => 5,
        'kernel/classes/expvelocityfrankenphpinstaller.php'                         => 2,
        'kernel/classes/ezpackage.php'                                              => 1,
        'kernel/classes/ezsslzone.php'                                              => 1,
        'kernel/classes/ini/actions/expinimover.php'                                => 4,
        'kernel/classes/ini/expinieditor.php'                                       => 8,
        'kernel/classes/mailpreferences/expmailbouncereader.php'                    => 1,
        'kernel/classes/mailpreferences/expmailgate.php'                            => 1,
        'kernel/classes/mailpreferences/expmailsecret.php'                          => 2,
        'kernel/classes/mailpreferences/expmailsenderdetails.php'                   => 2,
        'kernel/private/classes/commands/checkdbfiles.php'                          => 2,
        'kernel/private/classes/commands/ezasynchronouspublisher.php'               => 1,
        'kernel/private/classes/commands/install.php'                               => 3,
        'kernel/private/classes/commands/kickstarter.php'                           => 1,
        'kernel/private/classes/commands/mailconsent.php'                           => 1,
        'kernel/private/classes/commands/mailpreferences.php'                       => 1,
        'kernel/private/classes/commands/solr.php'                                  => 1,
        'kernel/private/classes/ezpformtokenrefusal.php'                            => 1,
        'kernel/private/classes/httpcache/ezphttpcachecontract.php'                 => 2,
        'kernel/private/classes/httpcache/ezphttpcachelistener.php'                 => 3,
        'kernel/private/classes/services/trashrecord.php'                           => 2,
        'kernel/private/classes/views/audit/dashboard.php'                          => 1,
        'kernel/private/classes/views/visual/templatecreate.php'                    => 4,
        'kernel/private/classes/views/visual/templateedit.php'                      => 1,
        'kernel/setup/expclassloadcheck.php'                                        => 1,
        'kernel/setup/expextensionwizard.php'                                       => 3,
        'kernel/setup/expradsurvey.php'                                             => 1,
        'kernel/setup/steps/ezstep_site_admin.php'                                  => 3,
        'kernel/shop/classes/ezshopreceipt.php'                                     => 2,
        'lib/ezdb/classes/ezdbquerycache.php'                                       => 2,
        'lib/ezdb/classes/ezsqlite3db.php'                                          => 1,
        'lib/ezi18n/classes/ezcodepage.php'                                         => 2,
        'lib/ezutils/classes/ezprepairqueue.php'                                    => 3,
    );

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
    public function testAMistypedConstantLimitsToTheOwner()
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
            $this->assertSame( 0600, eZFile::fileModeLimit() );
            $this->assertSame( 0700, eZDir::dirModeLimit() );
            $this->assertSame( 0600, eZFile::fileMode( 0666 ) );
            eZFile::fileModeLimit();
        }
        finally
        {
            ini_set( 'error_log', $previousLog );
        }
        $lines = file( $log, FILE_IGNORE_NEW_LINES );
        $this->assertCount( 2, $lines, 'each mistyped constant is reported once' );
        $this->assertStringContainsString( 'EZP_FILE_MODE_MAX in config.php is no file mode', $lines[0] );
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
            umask( $oldUmask );';
        $this->assertSame( array( 2, 4, 5, 7, 14, 15, 17, 18 ), expFileModeCalls::bypassesIn( $source ) );
    }
}
