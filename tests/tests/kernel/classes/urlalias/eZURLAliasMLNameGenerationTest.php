<?php
/**
 * Tests of the parts of eZURLAliasML that need no database: the URL alias name generation of convertToAlias(),
 * convertToAliasCompat() and convertPathToAlias() under each transformation group and word separator, the
 * conversion between actions and internal URLs, URL cleaning, path prefixes and the decision whether a URI is
 * translated at all.
 *
 * No database and no siteaccess. INI values are set in memory and put back in tearDown().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZURLAliasMLNameGenerationTest extends PHPUnit\Framework\TestCase
{
    private $charset;
    private $modulePathList;
    private $exceptionHandler;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->exceptionHandler = self::currentExceptionHandler();
        $this->charset = $GLOBALS['eZTextCodecInternalCharsetReal'] ?? null;
        $GLOBALS['eZTextCodecInternalCharsetReal'] = 'utf-8';
        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'WordSeparator', 'dash' );
        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'TransformationGroup', 'urlalias' );
        unset( $GLOBALS['eZCharTransform_wordSeparator'] );
        $this->modulePathList = $GLOBALS['eZModuleGlobalPathList'] ?? null;
        $GLOBALS['eZModuleGlobalPathList'] = array( 'kernel' );
    }

    private static function currentExceptionHandler()
    {
        $handler = set_exception_handler( null );
        restore_exception_handler();
        return $handler;
    }

    protected function tearDown(): void
    {
        // loading a module definition starts the kernel's shutdown handling, which installs an exception handler
        for ( $i = 0; $i < 5 && self::currentExceptionHandler() !== $this->exceptionHandler; ++$i )
            restore_exception_handler();
        ezpINIHelper::restoreINISettings();
        unset( $GLOBALS['eZCharTransform_wordSeparator'] );
        if ( $this->modulePathList === null )
            unset( $GLOBALS['eZModuleGlobalPathList'] );
        else
            $GLOBALS['eZModuleGlobalPathList'] = $this->modulePathList;
        if ( $this->charset === null )
            unset( $GLOBALS['eZTextCodecInternalCharsetReal'] );
        else
            $GLOBALS['eZTextCodecInternalCharsetReal'] = $this->charset;
    }

    private function useGroup( $group, $separator = 'dash' )
    {
        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'TransformationGroup', $group );
        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'WordSeparator', $separator );
        unset( $GLOBALS['eZCharTransform_wordSeparator'] );
    }

    public static function urlaliasProvider()
    {
        return array(
            'words' => array( 'My car', 'my-car' ),
            'question mark' => array( 'What is this?', 'what-is-this' ),
            'ampersand' => array( 'This & that', 'this-that' ),
            'dot kept inside' => array( 'myfile.tpl', 'myfile.tpl' ),
            'leading and trailing dots' => array( '..a...........b..', 'a-b' ),
            'nordic letters' => array( 'øæå', 'oeaeaa' ),
            'german letters' => array( 'Größe über', 'groesse-ueber' ),
            'digits and underscore' => array( 'abc_123', 'abc_123' ),
            'separators collapse' => array( 'a  -  b', 'a-b' ),
            'leading and trailing separators' => array( '  - a b -  ', 'a-b' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('urlaliasProvider')]
    public function testConvertToAliasWithTheUrlaliasGroup( $in, $expected )
    {
        $this->assertSame( $expected, eZURLAliasML::convertToAlias( $in ) );
    }

    public function testOnlyUnsafeCharactersGiveTheDefault()
    {
        $this->assertSame( '_1', eZURLAliasML::convertToAlias( '' ) );
        $this->assertSame( '_1', eZURLAliasML::convertToAlias( ' &;/:=?[]()+#' ) );
        $this->assertSame( 'fallback', eZURLAliasML::convertToAlias( '???', 'fallback' ) );
        $this->assertSame( 'fall-back', eZURLAliasML::convertToAlias( '???', 'Fall back!' ) );
    }

    public function testUnderscoreAndSpaceSeparators()
    {
        $this->useGroup( 'urlalias', 'underscore' );
        $this->assertSame( 'my_car_is_red', eZURLAliasML::convertToAlias( 'My car is red' ) );
        $this->useGroup( 'urlalias', 'space' );
        $this->assertSame( 'my car', eZURLAliasML::convertToAlias( 'My car?' ) );
    }

    public function testIriGroupKeepsUnicodeAndSafePunctuation()
    {
        $this->useGroup( 'urlalias_iri' );
        $this->assertSame( "$-_.!*',{}^§±@", eZURLAliasML::convertToAlias( "$-_.!*',{}^§±@" ) );
        $this->assertSame( 'ウーラ-Größe', eZURLAliasML::convertToAlias( 'ウーラ Größe' ) );
        $this->assertSame( '_1', eZURLAliasML::convertToAlias( " &;/:=?%[]()+#\t" ) );
    }

    public function testCompatGroupLowercasesAndUsesItsOwnSeparator()
    {
        $this->useGroup( 'urlalias_compat', 'underscore' );
        $this->assertSame( 'abcdefghijklmnopqrstuvwxyz', eZURLAliasML::convertToAlias( 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' ) );
        $this->assertSame( 'a_b', eZURLAliasML::convertToAlias( '..a...........b..' ) );
    }

    public function testConvertToAliasCompatIgnoresTheConfiguredGroup()
    {
        $this->useGroup( 'urlalias_iri' );
        $this->assertSame( 'my_car', eZURLAliasML::convertToAliasCompat( 'My car' ) );
        $this->assertSame( 'myfile_tpl', eZURLAliasML::convertToAliasCompat( 'myfile.tpl' ) );
        $this->assertSame( '_1', eZURLAliasML::convertToAliasCompat( 'ウーラ' ) );
        $this->assertSame( 'x_y', eZURLAliasML::convertToAliasCompat( 'ウーラ', 'X Y' ) );
    }

    public function testConvertPathToAliasConvertsEveryElement()
    {
        $this->assertSame( 'my-car/what-is-this/_1', eZURLAliasML::convertPathToAlias( 'My car/What is this?/???' ) );
        $this->assertSame( '_1', eZURLAliasML::convertPathToAlias( '' ) );
        $this->assertSame( '_1/a', eZURLAliasML::convertPathToAlias( '/a' ) );
    }

    public function testStrtolowerIsMultibyteAware()
    {
        $this->assertSame( 'âll ウーラ größe', eZURLAliasML::strtolower( 'ÂLL ウーラ GRÖßE' ) );
    }

    public function testCreateComputesTheMd5OfTheLowercasedText()
    {
        $alias = eZURLAliasML::create( 'Hello World', 'eznode:42', 7, 3 );
        $this->assertSame( 'Hello World', $alias->attribute( 'text' ) );
        $this->assertSame( md5( 'hello world' ), $alias->attribute( 'text_md5' ) );
        $this->assertSame( 7, $alias->attribute( 'parent' ) );
        $this->assertSame( 3, $alias->attribute( 'lang_mask' ) );
        $this->assertSame( 'eznode:42', $alias->attribute( 'action' ) );
        $this->assertSame( 'content/view/full/42', $alias->actionURL() );
    }

    public function testSetAttributeTextUpdatesTheMd5()
    {
        $alias = eZURLAliasML::create( 'One', 'nop:', 0, 1 );
        $alias->setAttribute( 'text', 'ÜBER' );
        $this->assertSame( md5( 'über' ), $alias->attribute( 'text_md5' ) );
    }

    public static function actionProvider()
    {
        return array(
            'node' => array( 'eznode:42', 'content/view/full/42' ),
            'module' => array( 'module:user/login', 'user/login' ),
            'module with args' => array( 'module:content/view/full/2/(offset)/10', 'content/view/full/2/(offset)/10' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('actionProvider')]
    public function testActionToUrl( $action, $url )
    {
        $this->assertSame( $url, eZURLAliasML::actionToUrl( $action ) );
    }

    public function testNopActionUsesLoadOnPartialAliasPath()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'LoadOnPartialAliasPath', '/partial' );
        $this->assertSame( '/partial', eZURLAliasML::actionToUrl( 'nop:' ) );
    }

    public static function invalidActionProvider()
    {
        return array(
            'no colon' => array( 'eznode' ),
            'wrong separator' => array( 'eznode;2' ),
            'space' => array( ' ' ),
            'unknown type' => array( 'ezblaa:2' ),
            'node not numeric' => array( 'eznode:abc' ),
            'node empty' => array( 'eznode:' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidActionProvider')]
    public function testInvalidActionsGiveFalse( $action )
    {
        $this->assertFalse( eZURLAliasML::actionToUrl( $action ) );
    }

    public function testUrlToAction()
    {
        $this->assertSame( 'eznode:2', eZURLAliasML::urlToAction( 'content/view/full/2' ) );
        $this->assertSame( 'module:user/login', eZURLAliasML::urlToAction( 'user/login' ) );
        $this->assertSame( 'module:content/view/full/2/x', eZURLAliasML::urlToAction( 'content/view/full/2/x' ) );
        $this->assertFalse( eZURLAliasML::urlToAction( 'nosuchmodulek1/view' ) );
        $this->assertFalse( eZURLAliasML::urlToAction( 'user' ) );
        $this->assertFalse( eZURLAliasML::urlToAction( '' ) );
    }

    public function testNodeIDFromAction()
    {
        $this->assertSame( '2', eZURLAliasML::nodeIDFromAction( 'eznode:2' ) );
        $this->assertFalse( eZURLAliasML::nodeIDFromAction( 'eznod:2' ) );
        $this->assertFalse( eZURLAliasML::nodeIDFromAction( ' eznode:2' ) );
        $this->assertFalse( eZURLAliasML::nodeIDFromAction( 'module:eznode:2' ) );
        $this->assertSame( '', eZURLAliasML::nodeIDFromAction( 'eznode:' ) );
    }

    public function testCleanAndSanitizeURL()
    {
        $this->assertSame( 'content/view//full/2', eZURLAliasML::cleanURL( ' ///content/view//full/2/// ' ) );
        $this->assertSame( 'content/view/full/2', eZURLAliasML::sanitizeURL( '///content///view////full//2///' ) );
        $this->assertSame( '', eZURLAliasML::sanitizeURL( '' ) );
        $this->assertSame( '', eZURLAliasML::sanitizeURL( '////' ) );
        $this->assertSame( 'ウ/ー/ラ', eZURLAliasML::sanitizeURL( '//ウ//ー//ラ//' ) );
    }

    public function testUrlTranslationEnabledByUri()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'Translation', 'enabled' );
        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'TranslatableSystemUrls', 'enabled' );
        $this->assertFalse( eZURLAliasML::urlTranslationEnabledByUri( new eZURI( '' ) ) );
        $this->assertTrue( eZURLAliasML::urlTranslationEnabledByUri( new eZURI( 'content/view/full/2' ) ) );
        $this->assertTrue( eZURLAliasML::urlTranslationEnabledByUri( new eZURI( 'Some/Page' ) ) );

        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'TranslatableSystemUrls', 'disabled' );
        $this->assertFalse( eZURLAliasML::urlTranslationEnabledByUri( new eZURI( 'content/view/full/2' ) ) );
        $this->assertTrue( eZURLAliasML::urlTranslationEnabledByUri( new eZURI( 'Some/Page' ) ) );

        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'Translation', 'disabled' );
        $this->assertFalse( eZURLAliasML::urlTranslationEnabledByUri( new eZURI( 'Some/Page' ) ) );
    }
}
