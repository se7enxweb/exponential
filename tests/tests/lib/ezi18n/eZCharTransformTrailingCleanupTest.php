<?php
/**
 * The URL alias cleanup at the end of an alias: site.ini [URLTranslator] TrailingCleanup
 * (eZCharTransform::trailingCleanup()).
 *
 *  TC-01 - The plain cleanup (urlalias): "!", dots and separators at the end are stripped in both modes, the
 *          cases of LoveStack's test give the same aliases as before
 *  TC-02 - Off (the shipped value): the IRI cleanup (urlalias_iri) keeps the double quote, today's aliases
 *  TC-03 - On: the IRI cleanup treats the double quote as reserved, so LoveStack's cases give "täst" and "test"
 *  TC-04 - The whole urlalias_iri transformation group follows the setting; urlalias and urlalias_compat do not
 *          change
 *  TC-05 - The setting missing from site.ini counts as off
 *
 * No kernel and no cache: transformations run with useCache=false, the word separator is set to "-" for the test.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 7517c0e925), Copyright (C) Mugo Web, GPL v2.
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/ezi18n/eZCharTransformTrailingCleanupTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group ezi18n
 */

class eZCharTransformTrailingCleanupTest extends PHPUnit\Framework\TestCase
{
    /** @var array|null */
    private $separator;

    /** @var array|null the setting before the test, array( value ) or null when it was missing */
    private $setting;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->separator = array_key_exists( 'eZCharTransform_wordSeparator', $GLOBALS ) ? array( $GLOBALS['eZCharTransform_wordSeparator'] ) : null;
        $GLOBALS['eZCharTransform_wordSeparator'] = '-';
        $ini = eZINI::instance();
        $this->setting = $ini->hasVariable( 'URLTranslator', 'TrailingCleanup' ) ? array( $ini->variable( 'URLTranslator', 'TrailingCleanup' ) ) : null;
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance();
        if ( $this->setting === null )
            $ini->removeSetting( 'URLTranslator', 'TrailingCleanup' );
        else
            $ini->setVariable( 'URLTranslator', 'TrailingCleanup', $this->setting[0] );
        if ( $this->separator === null )
            unset( $GLOBALS['eZCharTransform_wordSeparator'] );
        else
            $GLOBALS['eZCharTransform_wordSeparator'] = $this->separator[0];
    }

    private static function set( $value )
    {
        eZINI::instance()->setVariable( 'URLTranslator', 'TrailingCleanup', $value );
    }

    private static function transform( $text, $group )
    {
        return eZCharTransform::instance()->transformByGroup( $text, $group, 'utf-8', false );
    }

    /** TC-01 */
    public function testPlainCleanupSameInBothModes()
    {
        foreach ( array( 'disabled', 'enabled' ) as $mode )
        {
            self::set( $mode );
            $this->assertSame( 'test', eZCharTransform::commandUrlCleanup( 'test."', 'utf-8' ), $mode );
            $this->assertSame( 'te.st', eZCharTransform::commandUrlCleanup( 'te.st."', 'utf-8' ), $mode );
            $this->assertSame( 'test', eZCharTransform::commandUrlCleanup( '.test!"', 'utf-8' ), $mode );
            $this->assertSame( 'Hello-World', eZCharTransform::commandUrlCleanup( 'Hello World!!', 'utf-8' ), $mode );
        }
    }

    /** TC-02 */
    public function testOffKeepsTodaysIriAliases()
    {
        self::set( 'disabled' );
        $this->assertFalse( eZCharTransform::trailingCleanup() );
        $this->assertSame( 'täst."', eZCharTransform::commandUrlCleanupIRI( '.täst."', 'utf-8' ) );
        $this->assertSame( 'test!"', eZCharTransform::commandUrlCleanupIRI( '.test!"', 'utf-8' ) );
        $this->assertSame( 'Say-"Hello"-now', eZCharTransform::commandUrlCleanupIRI( 'Say "Hello" now', 'utf-8' ) );
        $this->assertSame( 'Grüße-aus-Köln', eZCharTransform::commandUrlCleanupIRI( 'Grüße aus Köln!', 'utf-8' ) );
    }

    /** TC-03 */
    public function testOnTreatsDoubleQuoteAsReserved()
    {
        self::set( 'enabled' );
        $this->assertTrue( eZCharTransform::trailingCleanup() );
        $this->assertSame( 'täst', eZCharTransform::commandUrlCleanupIRI( '.täst."', 'utf-8' ) );
        $this->assertSame( 'test', eZCharTransform::commandUrlCleanupIRI( '.test!"', 'utf-8' ) );
        $this->assertSame( 'Say-Hello-now', eZCharTransform::commandUrlCleanupIRI( 'Say "Hello" now', 'utf-8' ) );
        $this->assertSame( 'Grüße-aus-Köln', eZCharTransform::commandUrlCleanupIRI( 'Grüße aus Köln!', 'utf-8' ) );
    }

    /** TC-04 */
    public function testTransformationGroups()
    {
        $name = 'The "Best" Recipe.';
        self::set( 'disabled' );
        $iriOff = self::transform( $name, 'urlalias_iri' );
        $plainOff = self::transform( $name, 'urlalias' );
        $compatOff = self::transform( $name, 'urlalias_compat' );
        self::set( 'enabled' );
        $iriOn = self::transform( $name, 'urlalias_iri' );
        $this->assertSame( 'The-"Best"-Recipe', $iriOff );
        $this->assertSame( 'The-Best-Recipe', $iriOn );
        $this->assertSame( $plainOff, self::transform( $name, 'urlalias' ) );
        $this->assertSame( $compatOff, self::transform( $name, 'urlalias_compat' ) );
    }

    /** TC-05 */
    public function testMissingSettingIsOff()
    {
        eZINI::instance()->removeSetting( 'URLTranslator', 'TrailingCleanup' );
        $this->assertFalse( eZCharTransform::trailingCleanup() );
        $this->assertSame( 'täst."', eZCharTransform::commandUrlCleanupIRI( '.täst."', 'utf-8' ) );
    }
}
