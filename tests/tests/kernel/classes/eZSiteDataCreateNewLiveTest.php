<?php
/**
 * eZSiteData::createNew() stores a site data row (ezsite_data) at once.
 *
 *  SN-01 - createNew() stores the row; fetchByName() finds it with its value
 *  SN-02 - createNew() with a name that is stored replaces the value; there is still one row of that name
 *
 * Live style, in the database group: on the installation the tests run on, inside a transaction that is rolled
 * back, with a name of the test's own (x1-site-data-...); skipped without an installation. create(), which does not
 * store, is tested without a database in eZSiteDataCreateTest.
 *
 * LoveStack added createNew() and its test (commit 55029bd473) and then renamed it to create() (commit
 * 4a27ffecb4); Exponential has both, create() without and createNew() with storing.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commits 55029bd473 and 4a27ffecb4), Copyright (C)
 * Mugo Web, GPL v2
 *
 * Run: php vendor/bin/phpunit --group database tests/tests/kernel/classes/eZSiteDataCreateNewLiveTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

#[\PHPUnit\Framework\Attributes\Group('database')]
class eZSiteDataCreateNewLiveTest extends PHPUnit\Framework\TestCase
{
    /** @var bool a transaction of the test is open */
    private $inTransaction = false;

    public static function setUpBeforeClass(): void
    {
        ezpLiveInstallation::requireOrSkip();
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        eZDB::instance()->begin();
        $this->inTransaction = true;
    }

    protected function tearDown(): void
    {
        if ( $this->inTransaction )
        {
            eZDB::instance()->rollback();
            $this->inTransaction = false;
        }
    }

    private static function newName()
    {
        return 'x1-site-data-' . bin2hex( random_bytes( 4 ) );
    }

    private static function rowsNamed( $name )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT COUNT(*) AS c FROM ezsite_data WHERE name = '" . $db->escapeString( $name ) . "'" );
        return (int)$rows[0]['c'];
    }

    /** SN-01 */
    public function testCreateNewStoresTheRow()
    {
        $name = self::newName();
        $this->assertNull( eZSiteData::fetchByName( $name ) );
        $this->assertInstanceOf( 'eZSiteData', eZSiteData::createNew( $name, 'first' ) );
        $stored = eZSiteData::fetchByName( $name );
        $this->assertInstanceOf( 'eZSiteData', $stored );
        $this->assertSame( 'first', $stored->attribute( 'value' ) );
    }

    /** SN-02 */
    public function testCreateNewReplacesARowOfTheSameName()
    {
        $name = self::newName();
        eZSiteData::createNew( $name, 'first' );
        eZSiteData::createNew( $name, 'second' );
        $this->assertSame( 'second', eZSiteData::fetchByName( $name )->attribute( 'value' ) );
        $this->assertSame( 1, self::rowsNamed( $name ) );
    }
}
