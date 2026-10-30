<?php
/**
 * Captions and the verification after an upgrade, without a database.
 *
 *  UK-01 prune: the newest CaptionKeep stay, younger than CaptionKeepDays stay, the pre and post captions of the last
 *        CaptionKeepUpgrades upgrades stay, git_manager's own captions are never pruned here
 *  UK-02 the checksums of a caption: every file, checksums.json itself left out
 *  UK-03 a caption id is a name inside the caption folders, never a path
 *  UK-04 the error log: fatal lines from a moment on, by the timestamps of eZLog
 *  UK-05 the smoke addresses given win
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expUpgradeCaptionTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    private $dir;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
        foreach ( array( 'expupgradestep', 'expupgraderegistry', 'expupgrade', 'expupgradebackup', 'expupgradecaption', 'expupgradeverifyafter' ) as $f )
            require_once self::$root . "/kernel/classes/upgrade/$f.php";
    }

    protected function setUp(): void
    {
        $this->dir = self::$root . '/var/tmp/expUpgradeCaptionTest-' . getmypid();
        @mkdir( $this->dir . '/upgrade', 0700, true );
    }

    protected function tearDown(): void
    {
        foreach ( array( 'upgrade/a.txt', 'b.txt', 'upgrade/checksums.json', 'error.log' ) as $f )
            @unlink( $this->dir . '/' . $f );
        @rmdir( $this->dir . '/upgrade' );
        @rmdir( $this->dir );
    }

    private function caption( $id, $kind, $daysAgo, $from = '6.0.14', $to = '6.0.15', $ours = true )
    {
        $created = 1800000000 - $daysAgo * 86400;
        return array( 'id' => $id, 'kind' => $kind, 'created' => $created, 'upgrade_part' => $ours,
                      'upgrade' => array( 'from' => $from, 'to' => $to ) );
    }

    /** UK-01 */
    public function testPrunable()
    {
        $now = 1800000000;
        $captions = array(
            $this->caption( 'c1', 'post', 1, '6.0.15', '6.0.16' ),
            $this->caption( 'c2', 'pre', 1, '6.0.15', '6.0.16' ),
            $this->caption( 'c3', 'safety', 100 ),
            $this->caption( 'g1', 'git_manager', 200, '', '', false ),
            $this->caption( 'c4', 'pre', 120, '6.0.13', '6.0.14' ),
            $this->caption( 'c5', 'manual', 130 ),
            $this->caption( 'c6', 'pre', 150, '6.0.12', '6.0.13' ),
            $this->caption( 'c7', 'manual', 10 ),
        );
        $ids = function ( $list ) { return array_column( $list, 'id' ); };
        $this->assertSame( array( 'c3', 'c5', 'c6' ), $ids( expUpgradeCaption::prunable( $captions, 1, 90, 2, $now ) ),
                           'c1 kept (newest), c2 and c4 kept (the last 2 upgrades), c7 young, g1 is git_manager\'s' );
        $this->assertSame( array( 'c3', 'c5' ), $ids( expUpgradeCaption::prunable( $captions, 1, 90, 3, $now ) ) );
        $this->assertSame( array(), $ids( expUpgradeCaption::prunable( $captions, 10, 90, 3, $now ) ) );
        $this->assertSame( array( 'c3', 'c4', 'c5', 'c6' ), $ids( expUpgradeCaption::prunable( $captions, 0, 90, 1, $now ) ) );
    }

    /** UK-02 */
    public function testChecksums()
    {
        file_put_contents( $this->dir . '/b.txt', 'b' );
        file_put_contents( $this->dir . '/upgrade/a.txt', 'a' );
        file_put_contents( $this->dir . '/upgrade/checksums.json', '{}' );
        $this->assertSame( array( 'b.txt' => hash( 'sha256', 'b' ), 'upgrade/a.txt' => hash( 'sha256', 'a' ) ), expUpgradeCaption::checksums( $this->dir ) );
    }

    /** UK-03 */
    public function testFindRefusesPaths()
    {
        foreach ( array( '../x', '/etc', 'a/b', '', '.hidden', str_repeat( 'a', 100 ) ) as $id )
            $this->assertNull( expUpgradeCaption::find( $id, $this->dir ), $id );
        @mkdir( $this->dir . '/2026-10-29_10-15-00' );
        $this->assertSame( $this->dir . '/2026-10-29_10-15-00', expUpgradeCaption::find( '2026-10-29_10-15-00', $this->dir ) );
        @rmdir( $this->dir . '/2026-10-29_10-15-00' );
        $this->assertSame( array( 'pre', 'post', 'safety', 'manual' ), expUpgradeCaption::KINDS );
    }

    /** UK-04 */
    public function testFatalLinesSince()
    {
        file_put_contents( $this->dir . '/error.log', implode( "\n", array(
            '[ Oct 29 2026 10:00:00 ] [web] Unexpected error, the message was : old',
            '[ Oct 29 2026 11:00:00 ] [web] a notice',
            '[ Oct 29 2026 11:00:01 ] [web] PHP Fatal error:  Uncaught Error: new',
            'continued line of the fatal one',
            '[ Oct 29 2026 11:00:02 ] [cli] CRITICAL something',
        ) ) . "\n" );
        $lines = expUpgradeVerifyAfter::fatalLines( $this->dir . '/error.log', strtotime( 'Oct 29 2026 10:30:00' ) );
        $this->assertCount( 2, $lines );
        $this->assertStringContainsString( 'Uncaught Error: new', $lines[0] );
        $this->assertStringContainsString( 'CRITICAL', $lines[1] );
        $this->assertSame( array(), expUpgradeVerifyAfter::fatalLines( $this->dir . '/error.log', strtotime( 'Oct 29 2026 12:00:00' ) ) );
    }

    /** UK-05 */
    public function testGivenSmokeUrlsWin()
    {
        $this->assertSame( array( 'https://x/' ), expUpgradeVerifyAfter::smokeUrls( array( 'https://x/' ) ) );
    }
}
