<?php
/**
 * The script fallback of eZURLAliasML::convertToAlias(): site.ini [URLTranslator] ScriptFallbackTransformationGroup.
 *
 *  SF-01 - Arabic, Persian, Chinese and Thai titles, which urlalias leaves empty, get the IRI alias
 *  SF-02 - Latin, Cyrillic, Greek and Hebrew titles give exactly the aliases they gave before
 *  SF-03 - A mixed title keeps what the configured group leaves as long as a letter is left; a title whose only
 *          remains are digits falls back
 *  SF-04 - Titles without letters (punctuation, emoji) still give _1 or the default value
 *  SF-05 - Setting empty or missing: today's behaviour, the transformation gives '' and the alias _1 or the default
 *  SF-06 - The fallback follows the configured group: urlalias_compat as URL group falls back too, urlalias_iri as
 *          URL group is left alone; convertToAliasCompat() (path_identification_string) is never affected
 *
 * No database and no siteaccess. INI values are set in memory and put back in tearDown().
 *
 * Run: php vendor/bin/phpunit --no-coverage tests/tests/kernel/classes/urlalias/eZURLAliasMLScriptFallbackTest.php
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZURLAliasMLScriptFallbackTest extends PHPUnit\Framework\TestCase
{
    private $charset;

    private $cwd;

    protected function setUp(): void
    {
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 5 ) );
        $this->charset = $GLOBALS['eZTextCodecInternalCharsetReal'] ?? null;
        $GLOBALS['eZTextCodecInternalCharsetReal'] = 'utf-8';
        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'WordSeparator', 'dash' );
        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'TransformationGroup', 'urlalias' );
        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'ScriptFallbackTransformationGroup', 'urlalias_iri' );
        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'TrailingCleanup', 'disabled' );
        unset( $GLOBALS['eZCharTransform_wordSeparator'] );
    }

    protected function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        unset( $GLOBALS['eZCharTransform_wordSeparator'] );
        if ( $this->charset === null )
            unset( $GLOBALS['eZTextCodecInternalCharsetReal'] );
        else
            $GLOBALS['eZTextCodecInternalCharsetReal'] = $this->charset;
        chdir( $this->cwd );
    }

    private static function fallback( $group )
    {
        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'ScriptFallbackTransformationGroup', $group );
    }

    private static function urlGroup( $group )
    {
        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'TransformationGroup', $group );
    }

    public static function scriptProvider()
    {
        return array(
            'Arabic' => array( 'دليل المستخدم', 'دليل-المستخدم' ),
            'Persian' => array( 'راهنمای کاربر', 'راهنمای-کاربر' ),
            'Chinese' => array( '用户手册', '用户手册' ),
            'Thai' => array( 'คู่มือผู้ใช้', 'คู่มือผู้ใช้' ),
        );
    }

    /** SF-01 */
    #[\PHPUnit\Framework\Attributes\DataProvider('scriptProvider')]
    public function testScriptWithoutTransliterationGetsTheIriAlias( $title, $expected )
    {
        $this->assertSame( '', eZCharTransform::instance()->transformByGroup( $title, 'urlalias', 'utf-8', false ),
                           'precondition: urlalias leaves nothing of the title' );
        $this->assertSame( $expected, eZURLAliasML::convertToAlias( $title ) );
        $this->assertSame( $expected, eZURLAliasML::convertToAlias( $title, 'node_42' ) );
        $this->assertSame( $expected . '/' . $expected, eZURLAliasML::convertPathToAlias( $title . '/' . $title ) );
    }

    public static function transliteratedProvider()
    {
        return array(
            'Latin' => array( 'Benutzerhandbuch für Größen', 'benutzerhandbuch-fuer-groessen' ),
            'Nordic' => array( 'øæå', 'oeaeaa' ),
            'Cyrillic' => array( 'Руководство пользователя', 'rukovodstvo-pol-zovatelya' ),
            'Greek' => array( 'Εγχειρίδιο χρήστη', 'egcheirhidio-chrheste' ),
            'Hebrew' => array( 'מדריך למשתמש', 'mdryk-lmstms' ),
        );
    }

    /** SF-02 */
    #[\PHPUnit\Framework\Attributes\DataProvider('transliteratedProvider')]
    public function testTransliteratedScriptsKeepTodaysAlias( $title, $expected )
    {
        $this->assertSame( $expected, eZURLAliasML::convertToAlias( $title ) );
        self::fallback( '' );
        $this->assertSame( $expected, eZURLAliasML::convertToAlias( $title ), 'same alias without the fallback' );
    }

    /** SF-03 */
    public function testMixedTitleFallsBackOnlyWhenNoLetterIsLeft()
    {
        $this->assertSame( 'exponential', eZURLAliasML::convertToAlias( 'Exponential دليل' ) );
        $this->assertSame( 'manual-2', eZURLAliasML::convertToAlias( '用户手册 Manual 2' ) );
        $this->assertSame( 'دليل-2024', eZURLAliasML::convertToAlias( 'دليل 2024' ) );
        $this->assertSame( '2024', eZURLAliasML::convertToAlias( '2024' ), 'digits alone have no letter to keep' );
    }

    /** SF-04 */
    public function testTitlesWithoutLettersKeepTheDefault()
    {
        $this->assertSame( '_1', eZURLAliasML::convertToAlias( '???' ) );
        $this->assertSame( '_1', eZURLAliasML::convertToAlias( '😀 🎉' ) );
        $this->assertSame( 'node_42', eZURLAliasML::convertToAlias( '😀 🎉', 'node_42' ) );
        $this->assertSame( '_1', eZURLAliasML::convertToAlias( '' ) );
    }

    /** SF-05 */
    public function testSettingOffGivesTodaysBehaviour()
    {
        foreach ( array( '', null ) as $off )
        {
            self::fallback( $off );
            $this->assertSame( '_1', eZURLAliasML::convertToAlias( 'دليل المستخدم' ) );
            $this->assertSame( 'node_42', eZURLAliasML::convertToAlias( '用户手册', 'node_42' ) );
            $this->assertSame( 'noname1', eZURLAliasML::convertToAlias( 'คู่มือผู้ใช้', 'noname1' ) );
            $this->assertSame( 'exponential', eZURLAliasML::convertToAlias( 'Exponential دليل' ) );
        }
    }

    /** SF-06 */
    public function testFallbackFollowsTheConfiguredGroup()
    {
        self::urlGroup( 'urlalias_compat' );
        $this->assertSame( 'rukovodstvo_pol_zovatelya', eZURLAliasML::convertToAlias( 'Руководство пользователя' ) );
        $this->assertSame( 'دليل-المستخدم', eZURLAliasML::convertToAlias( 'دليل المستخدم' ) );

        self::urlGroup( 'urlalias_iri' );
        $this->assertSame( 'Руководство-пользователя', eZURLAliasML::convertToAlias( 'Руководство пользователя' ) );
        $this->assertSame( 'دليل-المستخدم', eZURLAliasML::convertToAlias( 'دليل المستخدم' ) );

        self::urlGroup( 'urlalias' );
        $this->assertSame( '_1', eZURLAliasML::convertToAliasCompat( 'دليل المستخدم' ) );
        $this->assertSame( 'node_42', eZURLAliasML::convertToAliasCompat( '用户手册', 'node_42' ) );
        $this->assertSame( '', eZCharTransform::instance()->transformByGroup( 'دليل المستخدم', 'identifier', 'utf-8', false ) );
    }

    /** SF-07 */
    public function testFallbackDropsControlFormatAndUnsafeCharacters()
    {
        $this->assertSame( '用户-手册', eZURLAliasML::convertToAlias( "用户<>|{}^`手册" ) );
        $this->assertSame( 'دليل-المستخدم', eZURLAliasML::convertToAlias( "دليل\u{202E} \u{200B}المستخدم\u{FEFF}\x00" ) );
        $this->assertSame( "راهنمای-کاربر\u{200C}ها", eZURLAliasML::convertToAlias( "راهنمای کاربر\u{200C}ها" ) );
    }
}
