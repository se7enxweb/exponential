<?php
/**
 * Making a site data row (ezsite_data) from PHP with eZSiteData::create().
 *
 *  SC-01 - create() gives a row with the name and the value that is not stored: no query reaches the database
 *
 * No database (the database instance refuses queries). createNew(), which stores at once, is tested live in
 * eZSiteDataCreateNewLiveTest.
 *
 * LoveStack added createNew() and its test (commit 55029bd473) and then renamed it to create() (commit
 * 4a27ffecb4); Exponential has both, create() without and createNew() with storing.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commits 55029bd473 and 4a27ffecb4), Copyright (C)
 * Mugo Web, GPL v2
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/eZSiteDataCreateTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/datatypes/eZDatatypeTestFixtures.php';

class eZSiteDataCreateTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    /** SC-01 */
    public function testCreateMakesARowThatIsNotStored()
    {
        $had = array_key_exists( 'eZDBGlobalInstance', $GLOBALS );
        $saved = $had ? $GLOBALS['eZDBGlobalInstance'] : null;
        eZDB::setInstance( new eZDatatypeTestNoDatabase() );
        try
        {
            $row = eZSiteData::create( 'foo', 'bar' );
        }
        finally
        {
            if ( $had )
                $GLOBALS['eZDBGlobalInstance'] = $saved;
            else
                unset( $GLOBALS['eZDBGlobalInstance'] );
        }
        $this->assertInstanceOf( 'eZSiteData', $row );
        $this->assertSame( 'foo', $row->attribute( 'name' ) );
        $this->assertSame( 'bar', $row->attribute( 'value' ) );
    }
}
