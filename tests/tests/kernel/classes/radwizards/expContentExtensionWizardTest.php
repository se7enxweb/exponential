<?php
/**
 * The content extension wizard of Setup > RAD (expContentExtensionWizard): the datatypes it offers, how the
 * attribute, custom tag and translation lists of its form are read, identifiers, datatype strings and locales,
 * what it finds wrong, and the class script, custom tag templates and translation file it writes.
 *
 * No database: an empty one stands in (no content class or class group exists).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expRadWizardTestHelper.php';

class expContentExtensionWizardTest extends PHPUnit\Framework\TestCase
{
    private $warnings = array();

    public static function setUpBeforeClass(): void
    {
        expRadWizardTestHelper::boot();
    }

    protected function setUp(): void
    {
        expRadWizardTestHelper::boot();
        expRadWizardTestHelper::useEmptyDatabase();
    }

    protected function tearDown(): void
    {
        expRadWizardTestHelper::restoreDatabase();
    }

    private function catchWarnings( $callable )
    {
        $this->warnings = array();
        set_error_handler( function ( $no, $message, $file, $line ) {
            $this->warnings[] = "$message ($file:$line)";
            return true;
        }, E_WARNING | E_NOTICE | E_DEPRECATED | E_USER_WARNING );
        try
        {
            return $callable();
        }
        finally
        {
            restore_error_handler();
        }
    }

    public function testDatatypesAreReadWithoutAnyRegisteredYet()
    {
        $hadTypes = array_key_exists( 'eZDataTypes', $GLOBALS );
        $types = $hadTypes ? $GLOBALS['eZDataTypes'] : null;
        $hadObjects = array_key_exists( 'eZDataTypeObjects', $GLOBALS );
        $objects = $hadObjects ? $GLOBALS['eZDataTypeObjects'] : null;
        unset( $GLOBALS['eZDataTypes'], $GLOBALS['eZDataTypeObjects'] );
        try
        {
            $known = $this->catchWarnings( function () { return expContentExtensionWizard::datatypes(); } );
        }
        finally
        {
            if ( $hadTypes )
                $GLOBALS['eZDataTypes'] = $types;
            if ( $hadObjects )
                $GLOBALS['eZDataTypeObjects'] = $objects;
        }
        $this->assertSame( array(), $this->warnings );
        $this->assertArrayHasKey( 'ezstring', $known );
        $this->assertSame( array( 'type' => 'ezstring', 'name' => 'ezstring' ), $known['ezstring'] );
        $keys = array_keys( $known );
        $sorted = $keys;
        sort( $sorted );
        $this->assertSame( $sorted, $keys );
    }

    public function testAttributeList()
    {
        $attributes = expContentExtensionWizard::attributeList(
            "Title, ezstring, The title, required, NOSEARCH\n\n  body , ezxmltext\n title, eztext\nrating, ez-integer!, , collect, notranslate\n#bad, ezstring\nodd, nosuchtype" );
        $this->assertSame( array( 'title', 'body', 'rating', 'bad', 'odd' ), array_column( $attributes, 'identifier' ) );
        $this->assertSame( array( 'identifier' => 'title', 'type' => 'ezstring', 'known' => true, 'name' => 'The title', 'required' => true,
                                  'searchable' => false, 'collector' => false, 'translatable' => true ), $attributes[0] );
        $this->assertSame( 'Body', $attributes[1]['name'] );
        $this->assertSame( 'ezxmltext', $attributes[1]['type'] );
        $this->assertSame( 'ezinteger', $attributes[2]['type'] );
        $this->assertSame( 'Rating', $attributes[2]['name'] );
        $this->assertTrue( $attributes[2]['collector'] );
        $this->assertFalse( $attributes[2]['translatable'] );
        $this->assertFalse( $attributes[4]['known'] );
        $this->assertSame( array(), expContentExtensionWizard::attributeList( array( 'x' ) ) );
        $this->assertCount( 50, expContentExtensionWizard::attributeList( implode( "\n", array_map( function ( $i ) { return "a$i"; }, range( 1, 60 ) ) ) ) );
        // no datatype given: a text line
        $this->assertSame( 'ezstring', expContentExtensionWizard::attributeList( 'plain' )[0]['type'] );
    }

    public function testTagList()
    {
        $tags = expContentExtensionWizard::tagList( "note: tone, size, tone, inline\nquote\n: nothing\nBad Name!: Some Attr" );
        $this->assertSame( array(
            array( 'name' => 'note', 'inline' => true, 'attributes' => array( 'tone', 'size' ) ),
            array( 'name' => 'quote', 'inline' => false, 'attributes' => array() ),
            array( 'name' => 'bad_name', 'inline' => false, 'attributes' => array( 'some_attr' ) ),
        ), $tags );
        $this->assertSame( array(), expContentExtensionWizard::tagList( null ) );
        $this->assertCount( 30, expContentExtensionWizard::tagList( implode( "\n", array_map( function ( $i ) { return "t$i"; }, range( 1, 40 ) ) ) ) );
    }

    public function testStringList()
    {
        $strings = expContentExtensionWizard::stringList( "design/k1e|Hello | world\nno bar here\n|no context\ncontext only|\n  k1e/x | Bye */  " );
        $this->assertSame( array(
            array( 'context' => 'design/k1e', 'source' => 'Hello | world' ),
            array( 'context' => 'k1e/x', 'source' => 'Bye *' ),
        ), $strings );
        $this->assertSame( array(), expContentExtensionWizard::stringList( array() ) );
    }

    public static function identifierProvider()
    {
        return array( array( 'Title', 'title' ), array( 'my title', 'my_title' ), array( '_x_', 'x' ), array( '9lives', '' ),
                      array( '', '' ), array( array(), '' ), array( 'a' . str_repeat( 'b', 60 ), 'a' . str_repeat( 'b', 60 ) ),
                      array( 'a' . str_repeat( 'b', 61 ), '' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('identifierProvider')]
    public function testSafeIdentifier( $value, $expected )
    {
        $this->assertSame( $expected, expContentExtensionWizard::safeIdentifier( $value ) );
    }

    public static function typeAndLocaleProvider()
    {
        return array(
            array( 'safeType', 'ezString', 'ezstring' ), array( 'safeType', 'ez-string', 'ezstring' ), array( 'safeType', 'x', '' ),
            array( 'safeType', '1ez', '' ), array( 'safeType', null, '' ),
            array( 'safeLocale', 'eng-GB', 'eng-GB' ), array( 'safeLocale', 'ENG-gb', 'eng-GB' ), array( 'safeLocale', 'nor_NO', '' ),
            array( 'safeLocale', ' ger-DE ', 'ger-DE' ), array( 'safeLocale', 'en-GB', '' ), array( 'safeLocale', array(), '' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('typeAndLocaleProvider')]
    public function testTypeAndLocale( $method, $value, $expected )
    {
        $this->assertSame( $expected, expContentExtensionWizard::$method( $value ) );
    }

    private function settings( array $input = array() )
    {
        return expContentExtensionWizard::settings( $input + array(
            'name' => 'k1e_content_ext', 'licence' => 'MIT', 'class' => 'k1e_article', 'locale' => 'eng-GB',
            'attributes' => "title, ezstring, Title, required\nbody, eztext",
            'tags' => 'k1e_note: tone', 'strings' => 'k1e/content|Hello', 'parts' => array_keys( expContentExtensionWizard::parts() ) ) );
    }

    public function testSettingsFillTheGaps()
    {
        $settings = expContentExtensionWizard::settings( array( 'name' => 'k1e_content_ext', 'class' => 'k1e_article', 'attributes' => 'headline' ) );
        $this->assertSame( 'K1e Content Ext', $settings['title'] );
        $this->assertSame( 'K1e Article', $settings['class_name'] );
        $this->assertSame( '<headline>', $settings['pattern'] );
        $this->assertSame( 1, $settings['class_group'] );
        $this->assertSame( 'Content for Exponential.', $settings['summary'] );
        $this->assertSame( 7, expContentExtensionWizard::settings( array( 'class_group' => '7x' ) )['class_group'] );
    }

    public function testProblems()
    {
        $this->assertSame( array(), array_values( expContentExtensionWizard::problems( $this->settings() ) ) );

        $problems = expContentExtensionWizard::problems( $this->settings( array( 'class' => '', 'attributes' => '', 'tags' => '', 'locale' => '', 'strings' => '' ) ) );
        $text = implode( ' | ', $problems );
        $this->assertStringContainsString( 'Choose at least one thing', $text );
        $this->assertStringContainsString( 'needs an identifier', $text );
        $this->assertStringContainsString( 'no attributes', $text );
        $this->assertStringContainsString( 'Custom tags were chosen and none were named', $text );
        $this->assertStringContainsString( 'needs a locale', $text );
        $this->assertStringContainsString( 'no strings were given', $text );

        $problems = expContentExtensionWizard::problems( $this->settings( array( 'attributes' => 'odd, nosuchtype' ) ) );
        $this->assertStringContainsString( 'nosuchtype', implode( ' ', $problems ) );
    }

    public function testTagsTheInstallationHasAreTaken()
    {
        $existing = (array)eZINI::instance( 'content.ini' )->variable( 'CustomTagSettings', 'AvailableCustomTags' );
        if ( !$existing )
            $this->markTestSkipped( 'no custom tags configured' );
        $settings = $this->settings( array( 'tags' => $existing[0] . "\nk1e_fresh" ) );
        $this->assertSame( array( $existing[0] ), expContentExtensionWizard::takenTags( $settings ) );
        $this->assertArrayHasKey( 'exists_tag_' . $existing[0], expContentExtensionWizard::problems( $settings ) );
    }

    public function testChosenTopicsNeedSomethingToSay()
    {
        $this->assertSame( array( 'class', 'customtag', 'translation' ), expContentExtensionWizard::chosenTopics( $this->settings() ) );
        $this->assertSame( array( 'class' ), expContentExtensionWizard::chosenTopics( $this->settings( array( 'tags' => '', 'strings' => '' ) ) ) );
        $this->assertSame( array(), expContentExtensionWizard::chosenTopics( $this->settings( array( 'parts' => array( 'readme' ) ) ) ) );
    }

    public function testFilesCarryTheClassTagsAndTranslation()
    {
        $files = expContentExtensionWizard::files( $this->settings() );
        expRadWizardTestHelper::assertFilesAreSafe( $this, $files );
        $all = implode( "\n", array_keys( $files ) );
        $this->assertStringContainsString( 'k1e_note', $all );
        $this->assertStringContainsString( 'translations/eng-GB/translation.ts', $all );
        $ts = $files['translations/eng-GB/translation.ts'];
        $dom = new DOMDocument();
        $this->assertTrue( $dom->loadXML( $ts ) );
        $this->assertSame( 'Hello', $dom->getElementsByTagName( 'source' )->item( 0 )->textContent );
        $this->assertSame( 'k1e/content', $dom->getElementsByTagName( 'name' )->item( 0 )->textContent );
        $script = implode( "\n", array_filter( $files, function ( $contents, $path ) { return strpos( $path, 'bin/' ) === 0 || strpos( $path, '.php' ) !== false; }, ARRAY_FILTER_USE_BOTH ) );
        $this->assertStringContainsString( 'k1e_article', $script );
        $this->assertStringContainsString( "ActiveExtensions[]=k1e_content_ext", expContentExtensionWizard::activation( $this->settings() ) );
        $this->assertNotEmpty( expContentExtensionWizard::topics() );
        $this->assertIsArray( expContentExtensionWizard::groups() );
    }
}
