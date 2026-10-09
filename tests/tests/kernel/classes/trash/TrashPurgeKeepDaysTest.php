<?php
/**
 * content.ini [TrashSettings] KeepItemsForDays: how long the cronjob trashpurge keeps items in the trash.
 *
 *  - Missing, empty or 0: the cronjob purges the whole trash, as before.
 *  - A whole number of days: only what has been in the trash that long is purged.
 *  - Anything else ("90 days", -1, 1.5, more than 36500 days): nothing is purged and an error is reported, so a
 *    typo cannot empty the trash.
 *  - eZScriptTrashPurge (also bin/php/trashpurge.php --trashed-days) refuses an age whose date overflows into the
 *    future, which matched every item.
 *
 * No database: the purge itself is replaced by a recording stand-in.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** The cronjob part with the purge recorded instead of run */
class expTestTrashpurgeKeepDays extends \Exponential\Cronjob\Kernel\Trashpurge
{
    /** @var array the $keepDays of each purge */
    public static $purges = array();

    /** @var string[] the errors reported */
    public static $errors = array();

    protected function purge( $keepDays )
    {
        self::$purges[] = $keepDays;
        return true;
    }

    protected function reportError( $message )
    {
        self::$errors[] = $message;
    }
}

class TrashPurgeKeepDaysTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        expTestTrashpurgeKeepDays::$purges = array();
        expTestTrashpurgeKeepDays::$errors = array();
    }

    protected function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
    }

    /**
     * Runs the cronjob part with KeepItemsForDays set to $value and returns the $keepDays its purge got.
     *
     * @param mixed $value
     * @return array
     */
    private function runWith( $value )
    {
        ezpINIHelper::setINISetting( 'content.ini', 'TrashSettings', 'KeepItemsForDays', $value );
        $part = new expTestTrashpurgeKeepDays();
        $part->run( array() );
        return expTestTrashpurgeKeepDays::$purges;
    }

    public function testKeepDaysReadsTheSetting()
    {
        $ini = eZINI::instance( 'content.ini' );
        foreach ( array( '90' => 90, ' 30 ' => 30, '1' => 1, '0036500' => 36500, '' => null, '0' => null, '000' => null,
                         '90 days' => false, '-1' => false, '1.5' => false, 'abc' => false, '36501' => false,
                         '10000000000000' => false, '99999999999999999999' => false ) as $value => $expected )
        {
            ezpINIHelper::setINISetting( 'content.ini', 'TrashSettings', 'KeepItemsForDays', (string)$value );
            $this->assertSame( $expected, expTestTrashpurgeKeepDays::keepDays( $ini ), var_export( (string)$value, true ) );
        }
        ezpINIHelper::setINISetting( 'content.ini', 'TrashSettings', 'KeepItemsForDays', array( '90' ) );
        $this->assertFalse( expTestTrashpurgeKeepDays::keepDays( $ini ), 'an array' );
    }

    public function testTheShippedSettingKeepsTheOldBehaviour()
    {
        // settings/content.ini alone, whatever the overrides of this installation say
        $shipped = new eZINI( 'content.ini', 'settings', null, false, false );
        $this->assertNull( expTestTrashpurgeKeepDays::keepDays( $shipped ) );
    }

    public function testTheCronjobPurgesWhatIsOldEnough()
    {
        $this->assertSame( array( 90 ), $this->runWith( '90' ) );
    }

    public function testTheCronjobPurgesEverythingWithoutAnAge()
    {
        $this->assertSame( array( null ), $this->runWith( '' ) );
    }

    public function testAMistypedAgePurgesNothing()
    {
        $this->assertSame( array(), $this->runWith( '90 days' ) );
        $this->assertCount( 1, expTestTrashpurgeKeepDays::$errors );
        $this->assertStringContainsString( 'KeepItemsForDays', expTestTrashpurgeKeepDays::$errors[0] );
    }

    public function testAnAgeThatOverflowsIsRefusedBeforeAnyPurge()
    {
        // returns before it fetches a user or reads the trash, so no database is needed
        $purge = new eZScriptTrashPurge( eZCLI::instance(), true );
        $this->assertFalse( $purge->run( 100, 0, 10000000000000 ) );
    }
}
