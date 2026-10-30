<?php
/**
 * eZINI::getSiteAccessIni() keeps the file it parsed (APCu, else the process) and hands out a copy for as long as
 * the files it was read from and the siteaccess directory are unchanged: the same settings as a fresh read, a copy
 * that can be changed without changing the next one, and a changed, added or removed file read again.
 *
 * The siteaccess is a directory below settings/siteaccess of the installation root (run from that root), made
 * available through injected settings and removed afterwards.
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/ezutils/eZINISiteAccessIniMemoTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZINISiteAccessIniMemoTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $siteAccess;

    /** @var string */
    private $dir;

    protected function setUp(): void
    {
        $this->siteAccess = 'inimemo' . getmypid() . mt_rand( 1000, 9999 );
        $this->dir = 'settings/siteaccess/' . $this->siteAccess;
        mkdir( $this->dir, 0775, true );
        eZINI::injectSettings( array( 'site.ini' => array( 'SiteAccessSettings' => array( 'AvailableSiteAccessList' => array( $this->siteAccess ) ) ) ) );
        eZINI::resetInstance( 'site.ini' );
    }

    protected function tearDown(): void
    {
        foreach ( glob( $this->dir . '/*' ) as $file )
            unlink( $file );
        rmdir( $this->dir );
        eZINI::injectSettings( array() );
        eZINI::resetInstance( 'site.ini' );
    }

    private function write( $file, $value )
    {
        file_put_contents( $this->dir . '/' . $file, "<?php /*\n[Memo]\nValue=$value\nList[]=$value\n*/ ?>\n" );
        // a new modification time even within the same second: the memo compares time, size and inode
        clearstatcache();
    }

    public function testCopyHasTheSettingsOfAFreshRead()
    {
        $this->write( 'memo.ini.append.php', 'first' );
        $fresh = eZINI::fetchFromFile( $this->dir . '/memo.ini.append.php' );
        $one = eZINI::getSiteAccessIni( $this->siteAccess, 'memo.ini.append.php' );
        $two = eZINI::getSiteAccessIni( $this->siteAccess, 'memo.ini.append.php' );
        $this->assertSame( $fresh->BlockValues, $one->BlockValues );
        $this->assertSame( $fresh->BlockValues, $two->BlockValues );
        $this->assertSame( 'first', $two->variable( 'Memo', 'Value' ) );
        $this->assertNotSame( $one, $two );
    }

    public function testChangingACopyDoesNotChangeTheNext()
    {
        $this->write( 'memo.ini.append.php', 'first' );
        $one = eZINI::getSiteAccessIni( $this->siteAccess, 'memo.ini.append.php' );
        $one->setVariable( 'Memo', 'Value', 'changed' );
        $this->assertSame( 'first', eZINI::getSiteAccessIni( $this->siteAccess, 'memo.ini.append.php' )->variable( 'Memo', 'Value' ) );
    }

    public function testChangedFileIsReadAgain()
    {
        $this->write( 'memo.ini.append.php', 'first' );
        $this->assertSame( 'first', eZINI::getSiteAccessIni( $this->siteAccess, 'memo.ini.append.php' )->variable( 'Memo', 'Value' ) );
        // replaced by rename, as a deploy does: another inode, same size
        file_put_contents( $this->dir . '/new.tmp', "<?php /*\n[Memo]\nValue=other\nList[]=other\n*/ ?>\n" );
        rename( $this->dir . '/new.tmp', $this->dir . '/memo.ini.append.php' );
        clearstatcache();
        $this->assertSame( 'other', eZINI::getSiteAccessIni( $this->siteAccess, 'memo.ini.append.php' )->variable( 'Memo', 'Value' ) );
        // written in place with another size
        $this->write( 'memo.ini.append.php', 'longer value' );
        $this->assertSame( 'longer value', eZINI::getSiteAccessIni( $this->siteAccess, 'memo.ini.append.php' )->variable( 'Memo', 'Value' ) );
    }

    public function testAddedAndRemovedFileAreSeen()
    {
        $this->assertFalse( eZINI::getSiteAccessIni( $this->siteAccess, 'late.ini.append.php' )->hasVariable( 'Memo', 'Value' ) );
        // the directory's time changes with the file added (at least a second later, to be sure of it)
        sleep( 1 );
        $this->write( 'late.ini.append.php', 'late' );
        $this->assertSame( 'late', eZINI::getSiteAccessIni( $this->siteAccess, 'late.ini.append.php' )->variable( 'Memo', 'Value' ) );
        unlink( $this->dir . '/late.ini.append.php' );
        clearstatcache();
        $this->assertFalse( eZINI::getSiteAccessIni( $this->siteAccess, 'late.ini.append.php' )->hasVariable( 'Memo', 'Value' ) );
    }
}
