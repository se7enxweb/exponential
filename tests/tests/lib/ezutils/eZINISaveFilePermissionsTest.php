<?php
/**
 * File containing the eZINISaveFilePermissionsTest class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

/**
 * Mode, owner and group of the settings files eZINI::save() writes
 * (doc/bc/6.0/ini-save-file-permissions.md). Before 6.0.15 every saved file was
 * chmod 0666 (EZP_INI_FILE_PERMISSION's default), settings/override/site.ini.append.php
 * with the database password included. No database is needed.
 */
class eZINISaveFilePermissionsTest extends ezpTestCase
{
    /** @var string Absolute path of this test's directory under var/tmp */
    private $dir;

    /** @var string The same, relative to the installation root (what eZINI takes) */
    private $relDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->relDir = 'var/tmp/ezini_save_perm_' . getmypid() . '_' . bin2hex( random_bytes( 4 ) );
        $this->dir = getcwd() . '/' . $this->relDir;
        mkdir( $this->dir, 0755, true );
        chmod( $this->dir, 0755 );
    }

    protected function tearDown(): void
    {
        $this->removeTree( $this->dir );
        parent::tearDown();
    }

    private function removeTree( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        foreach ( scandir( $dir ) as $entry )
        {
            if ( $entry === '.' || $entry === '..' )
                continue;
            $path = $dir . '/' . $entry;
            if ( is_dir( $path ) && !is_link( $path ) )
                $this->removeTree( $path );
            else
                unlink( $path );
        }
        rmdir( $dir );
    }

    private function mode( $path )
    {
        clearstatcache( true, $path );
        return fileperms( $path ) & 07777;
    }

    /**
     * Writes $fileName in the test directory with one setting, as the settings editor does.
     */
    private function saveSetting( $fileName, $value, $relDir = null )
    {
        $relDir = $relDir === null ? $this->relDir : $relDir;
        $ini = new eZINI( $fileName, $relDir, null, false, false, true, false, true );
        $ini->setVariable( 'DatabaseSettings', 'Password', $value );
        return $ini->save();
    }

    private function writeExisting( $fileName, $mode )
    {
        $path = $this->dir . '/' . $fileName;
        file_put_contents( $path, "<?php /* #?ini charset=\"utf-8\"?\n\n[DatabaseSettings]\nPassword=old\n*/ ?>" );
        chmod( $path, $mode );
        return $path;
    }

    public function testNewFileGetsSaveFilePermission()
    {
        if ( defined( 'EZP_INI_SAVE_FILE_PERMISSION' ) )
            $this->markTestSkipped( 'EZP_INI_SAVE_FILE_PERMISSION is defined in this installation' );

        $this->assertTrue( $this->saveSetting( 'site.ini.append.php', 'secret' ) );
        $path = $this->dir . '/site.ini.append.php';
        $this->assertFileExists( $path );
        $this->assertSame( 0640, $this->mode( $path ) );
        $this->assertStringContainsString( 'Password=secret', file_get_contents( $path ) );
    }

    public function testNewFileGetsOwnerAndGroupOfItsDirectory()
    {
        if ( function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 )
        {
            // a directory root owns is covered by the test below; here it is the site user's
            $owner = $this->unprivilegedUser();
            chown( $this->dir, $owner['uid'] );
            chgrp( $this->dir, $owner['gid'] );
        }
        $this->assertTrue( $this->saveSetting( 'site.ini.append.php', 'secret' ) );
        $path = $this->dir . '/site.ini.append.php';
        clearstatcache();
        $this->assertSame( filegroup( $this->dir ), filegroup( $path ) );
        if ( function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 )
            $this->assertSame( fileowner( $this->dir ), fileowner( $path ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerExistingModes')]
    public function testExistingFileKeepsItsMode( $before, $after )
    {
        $path = $this->writeExisting( 'site.ini.append.php', $before );
        $this->assertTrue( $this->saveSetting( 'site.ini.append.php', 'new' ) );
        $this->assertSame( $after, $this->mode( $path ), sprintf( 'a %04o file saved', $before ) );
        $this->assertStringContainsString( 'Password=new', file_get_contents( $path ) );
    }

    public static function providerExistingModes()
    {
        return array(
            '0600 kept' => array( 0600, 0600 ),
            '0640 kept' => array( 0640, 0640 ),
            '0644 kept' => array( 0644, 0644 ),
            '0664 kept' => array( 0664, 0664 ),
            // a file a 0666 save left behind loses the bit that lets other users write it
            '0666 becomes 0664' => array( 0666, 0664 ),
        );
    }

    public function testExistingFileKeepsOwnerAndGroupWhenRoot()
    {
        if ( !function_exists( 'posix_geteuid' ) || posix_geteuid() !== 0 )
            $this->markTestSkipped( 'Only root can hand a file to another user' );
        $owner = $this->unprivilegedUser();
        $path = $this->writeExisting( 'site.ini.append.php', 0640 );
        chown( $path, $owner['uid'] );
        chgrp( $path, $owner['gid'] );

        $this->assertTrue( $this->saveSetting( 'site.ini.append.php', 'new' ) );
        clearstatcache();
        $this->assertSame( array( $owner['uid'], $owner['gid'], 0640 ), array( fileowner( $path ), filegroup( $path ), $this->mode( $path ) ) );
    }

    public function testNewFileInRootOwnedDirectoryGetsOwnerOfTheDirectoryAbove()
    {
        if ( !function_exists( 'posix_geteuid' ) || posix_geteuid() !== 0 )
            $this->markTestSkipped( 'Only root makes root-owned directories here' );
        $owner = $this->unprivilegedUser();
        chown( $this->dir, $owner['uid'] );
        chgrp( $this->dir, $owner['gid'] );
        // like settings/siteaccess/<name> made by a root process (Velocity) under the site user's settings/
        mkdir( $this->dir . '/byroot', 0755 );
        chown( $this->dir . '/byroot', 0 );
        chgrp( $this->dir . '/byroot', 0 );

        $this->assertTrue( $this->saveSetting( 'site.ini.append.php', 'secret', $this->relDir . '/byroot' ) );
        $path = $this->dir . '/byroot/site.ini.append.php';
        clearstatcache();
        $this->assertSame( array( $owner['uid'], $owner['gid'] ), array( fileowner( $path ), filegroup( $path ) ),
                           'the site user must still be able to read what root wrote' );
    }

    public function testNewDirectoryIsNotWorldWritable()
    {
        $ini = new eZINI( 'site.ini.append.php', $this->relDir, null, false, false, true, false, true );
        $ini->setVariable( 'DatabaseSettings', 'Password', 'secret' );
        $this->assertTrue( $ini->save( 'site.ini.append.php', false, false, false, $this->relDir . '/sub/deeper' ) );

        $this->assertSame( eZINI::SAVE_DIRECTORY_PERMISSION, $this->mode( $this->dir . '/sub' ) );
        $this->assertSame( eZINI::SAVE_DIRECTORY_PERMISSION, $this->mode( $this->dir . '/sub/deeper' ) );
        $this->assertSame( eZINI::newSaveFileMode(), $this->mode( $this->dir . '/sub/deeper/site.ini.append.php' ) );
    }

    public function testNoTemporaryFileIsLeftAndTheBackupKeepsTheOldMode()
    {
        $this->writeExisting( 'site.ini.append.php', 0600 );
        $this->assertTrue( $this->saveSetting( 'site.ini.append.php', 'new' ) );
        $backup = 'site.ini.append.php' . eZSys::backupFilename();
        $left = array_values( array_diff( scandir( $this->dir ), array( '.', '..' ) ) );
        sort( $left );
        $this->assertSame( array( 'site.ini.append.php', $backup ), $left );
        // the previous file, password included, is kept as the backup: as readable as it was, not more
        $this->assertSame( 0600, $this->mode( $this->dir . '/' . $backup ) );
    }

    /**
     * EZP_INI_FILE_PERMISSION governs the INI cache files only; a save ignores it.
     */
    public function testCacheFilePermissionDoesNotApplyToSave()
    {
        new eZINI( 'site.ini', 'settings', null, false, false, true );
        $property = new ReflectionProperty( 'eZINI', 'filePermission' );
        if ( PHP_VERSION_ID < 80100 )
            $property->setAccessible( true );
        $original = $property->getValue();
        $this->assertSame( defined( 'EZP_INI_FILE_PERMISSION' ) ? EZP_INI_FILE_PERMISSION : 0644, $original,
                           'cache files: EZP_INI_FILE_PERMISSION, else 0644 (no longer 0666)' );

        $property->setValue( null, 0666 );
        try
        {
            $this->assertTrue( $this->saveSetting( 'site.ini.append.php', 'secret' ) );
        }
        finally
        {
            $property->setValue( null, $original );
        }
        $this->assertSame( eZINI::newSaveFileMode(), $this->mode( $this->dir . '/site.ini.append.php' ) );
    }

    public function testSaveFileModeHelpers()
    {
        $path = $this->writeExisting( 'a.ini', 04666 );
        $this->assertSame( 0664, eZINI::saveFileMode( $path ), 'no setuid, no write for others' );
        $this->assertSame( eZINI::newSaveFileMode(), eZINI::saveFileMode( $this->dir . '/missing.ini' ) );
        $this->assertSame( eZINI::newSaveFileMode(), eZINI::saveFileMode( false ) );
        $this->assertSame( 0, eZINI::newSaveFileMode() & 0002, 'never writable by others' );
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testSaveFilePermissionConstantIsHonouredButNeverWorldWritable()
    {
        define( 'EZP_INI_SAVE_FILE_PERMISSION', 0666 );
        $this->assertSame( 0664, eZINI::newSaveFileMode() );
        $this->assertTrue( $this->saveSetting( 'site.ini.append.php', 'secret' ) );
        $this->assertSame( 0664, $this->mode( $this->dir . '/site.ini.append.php' ) );
    }

    /**
     * @return array uid and gid of a user that is not root (the owner of the installation, else nobody)
     */
    private function unprivilegedUser()
    {
        $uid = fileowner( getcwd() );
        $gid = filegroup( getcwd() );
        if ( $uid === 0 && function_exists( 'posix_getpwnam' ) && ( $nobody = posix_getpwnam( 'nobody' ) ) )
            return array( 'uid' => $nobody['uid'], 'gid' => $nobody['gid'] );
        if ( $uid === 0 )
            $this->markTestSkipped( 'No user other than root to test with' );
        return array( 'uid' => $uid, 'gid' => $gid );
    }
}
