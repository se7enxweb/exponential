<?php
/**
 * The text direction of a locale: eZLocale::textDirection() and isRightToLeft() read [LanguageSettings]
 * TextDirection of the locale file, eZLocale::textDirectionOf() does so by locale code, normalizeTextDirection()
 * turns any setting into 'rtl' or 'ltr', and templates reach both through attribute() (text_direction,
 * is_right_to_left).
 *
 * No kernel bootstrap, no database: the locale INI files are read from share/locale.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezlocale
 */

class expLocaleTextDirectionTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    public static function directionProvider()
    {
        return array(
            'Hebrew' => array( 'heb-IL', 'rtl' ),
            'Arabic (Saudi Arabia)' => array( 'ara-SA', 'rtl' ),
            'Arabic (Egypt)' => array( 'ara-EG', 'rtl' ),
            'Persian' => array( 'fas-IR', 'rtl' ),
            'Urdu' => array( 'urd-PK', 'rtl' ),
            'Pashto' => array( 'pus-AF', 'rtl' ),
            'Uyghur' => array( 'uig-CN', 'rtl' ),
            'Arabic (Morocco)' => array( 'ara-MA', 'rtl' ),
            'Dari' => array( 'fas-AF', 'rtl' ),
            'Yiddish' => array( 'yid-US', 'rtl' ),
            'Central Kurdish' => array( 'ckb-IQ', 'rtl' ),
            'Syriac' => array( 'syr-IQ', 'rtl' ),
            'Kashmiri' => array( 'kas-IN', 'rtl' ),
            'N\'Ko' => array( 'nqo-GN', 'rtl' ),
            'Dhivehi (hand-made)' => array( 'div-MV', 'rtl' ),
            'English (United States)' => array( 'eng-US', 'ltr' ),
            'German' => array( 'ger-DE', 'ltr' ),
            'Latin' => array( 'lat-VA', 'ltr' ),
            'Kurdish (Latin script)' => array( 'kur-TR', 'ltr' ),
            'Uzbek (Latin script)' => array( 'uzb-UZ', 'ltr' ),
            'Variation of a left-to-right locale' => array( 'ger-DE@euro', 'ltr' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('directionProvider')]
    public function testTextDirectionOfTheLocale( $code, $direction )
    {
        $locale = eZLocale::create( $code );
        $this->assertTrue( $locale->isValid(), "$code is valid" );
        $this->assertSame( $direction, $locale->textDirection() );
        $this->assertSame( $direction === 'rtl', $locale->isRightToLeft() );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('directionProvider')]
    public function testTextDirectionOfTheCode( $code, $direction )
    {
        $this->assertSame( $direction, eZLocale::textDirectionOf( $code ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('directionProvider')]
    public function testTemplateAttributes( $code, $direction )
    {
        $locale = eZLocale::create( $code );
        $this->assertTrue( $locale->hasAttribute( 'text_direction' ) );
        $this->assertTrue( $locale->hasAttribute( 'is_right_to_left' ) );
        $this->assertContains( 'text_direction', $locale->attributes() );
        $this->assertContains( 'is_right_to_left', $locale->attributes() );
        $this->assertSame( $direction, $locale->attribute( 'text_direction' ) );
        $this->assertSame( $direction === 'rtl', $locale->attribute( 'is_right_to_left' ) );
    }

    public function testUnknownOrEmptyCodeIsLeftToRight()
    {
        $this->assertSame( 'ltr', eZLocale::textDirectionOf( 'xxx-YY' ) );
        $this->assertSame( 'ltr', eZLocale::textDirectionOf( '' ) );
        $this->assertSame( 'ltr', eZLocale::textDirectionOf( '   ' ) );
        $this->assertSame( 'ltr', eZLocale::textDirectionOf( null ) );
    }

    public function testCodeIsTrimmed()
    {
        $this->assertSame( 'rtl', eZLocale::textDirectionOf( ' heb-IL ' ) );
    }

    public static function normalizeProvider()
    {
        return array(
            array( 'rtl', 'rtl' ),
            array( 'RTL', 'rtl' ),
            array( ' rtl ', 'rtl' ),
            array( 'right-to-left', 'rtl' ),
            array( 'Right To Left', 'rtl' ),
            array( 'right_to_left', 'rtl' ),
            array( 'ltr', 'ltr' ),
            array( 'LTR', 'ltr' ),
            array( 'left-to-right', 'ltr' ),
            array( '', 'ltr' ),
            array( null, 'ltr' ),
            array( 'auto', 'ltr' ),
            array( 'rtlx', 'ltr' ),
            array( 1, 'ltr' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('normalizeProvider')]
    public function testNormalizeTextDirection( $value, $expected )
    {
        $this->assertSame( $expected, eZLocale::normalizeTextDirection( $value ) );
    }

    public function testDefaultIsLeftToRightBeforeAFileIsRead()
    {
        $locale = eZLocale::create( 'eng-GB' );
        $this->assertSame( 'ltr', $locale->textDirection() );
        $this->assertFalse( $locale->isRightToLeft() );
    }
}
