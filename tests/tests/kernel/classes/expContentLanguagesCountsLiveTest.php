<?php
/**
 * File containing the expContentLanguagesCountsLiveTest class.
 *
 * The figures the content languages page (content/translations) reads from the database, on the installation the
 * tests run on: the counts per language agree with eZContentLanguage's own, the objects and classes listed for a
 * language really have it, and every siteaccess list is read. Read-only: no language, object or class is added,
 * changed or removed. Where there is no installation (CI) the tests are skipped.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Content\Translations;

class expContentLanguagesCountsLiveTest extends PHPUnit\Framework\TestCase
{
    private static $installation;

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 4 );
        ezpLiveInstallation::requireOrSkip();
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
    }

    private function languages()
    {
        $languages = eZContentLanguage::fetchList( true );
        if ( !$languages )
            $this->markTestSkipped( 'The installation has no content languages.' );
        return $languages;
    }

    public function testCountsAgreeWithTheKernel()
    {
        foreach ( $this->languages() as $language )
        {
            $counts = Translations::languageCounts( $language->attribute( 'id' ) );
            $this->assertSame( (int) $language->objectCount(), $counts['objects'], $language->attribute( 'locale' ) );
            $this->assertSame( (int) $language->classCount(), $counts['classes'], $language->attribute( 'locale' ) );
            $this->assertSame( (int) $language->objectInitialCount(), $counts['objects_main'], $language->attribute( 'locale' ) );
            $this->assertLessThanOrEqual( $counts['objects'], $counts['objects_only'] );
            $this->assertLessThanOrEqual( $counts['objects'], $counts['objects_main'] );
        }
        $this->assertSame( array( 'objects' => 0, 'objects_main' => 0, 'objects_only' => 0, 'classes' => 0 ),
                           Translations::languageCounts( 1 << 60 ), 'a language that does not exist counts nothing' );
    }

    public function testListedObjectsAndClassesHaveTheLanguage()
    {
        foreach ( $this->languages() as $language )
        {
            $id = (int) $language->attribute( 'id' );
            $objects = Translations::objectsInLanguage( $id, 5 );
            $this->assertLessThanOrEqual( 5, count( $objects ) );
            $this->assertSame( min( 5, (int) $language->objectCount() ), count( $objects ), $language->attribute( 'locale' ) );
            foreach ( $objects as $object )
            {
                $this->assertContains( $language->attribute( 'locale' ), $object['languages'] );
                $this->assertSame( $object['main_locale'] === $language->attribute( 'locale' ), $object['is_main'] );
            }
            // every version of a class counts, unsaved class edits too, as in the kernel's count
            $classes = Translations::classesInLanguage( $id );
            $this->assertSame( (int) $language->classCount(), count( $classes ), $language->attribute( 'locale' ) );
            foreach ( $classes as $class )
            {
                $version = $class['is_draft'] ? eZContentClass::VERSION_STATUS_TEMPORARY : eZContentClass::VERSION_STATUS_DEFINED;
                $stored = eZContentClass::fetch( $class['id'], true, $version ) ?: eZContentClass::fetch( $class['id'], true, eZContentClass::VERSION_STATUS_MODIFIED );
                $this->assertNotSame( 0, ( (int) $stored->attribute( 'language_mask' ) ) & $id );
            }
        }
    }

    public function testEverySiteAccessListIsRead()
    {
        $lists = Translations::siteLanguageLists();
        $available = array_unique( array_filter( (array) eZINI::instance()->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) ) );
        $this->assertSame( array_values( $available ), array_keys( $lists ) );
        foreach ( $lists as $list )
            $this->assertIsArray( $list );
    }
}
