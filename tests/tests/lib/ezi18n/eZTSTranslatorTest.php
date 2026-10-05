<?php
/**
 * eZTSTranslator (lib/ezi18n), the reader of the Qt Linguist translation files, with its cache off:
 *   - a translation file of its own in a private repository: contexts, messages, comments (and the empty
 *     <comment/> the schema allows), <byte> elements, untranslated (empty) messages falling back to the source,
 *     several contexts, a context without messages, a file that does not validate, a missing file
 *   - translate(), findMessage() with and without comment, findKey()/keyTranslate(), insert(), remove() and
 *     removeKey() with their return values, the "default" context
 *   - initialize() registering the translator with eZTranslatorManager, fetchList()
 *   - every translation shipped in share/translations validates against schemas/translation/ts.rng and loads
 *
 * The repository is switched with eZINI::setVariable() for the test and restored in tearDown(); files go to a
 * private directory under var/tmp. No translation cache is read or written.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezi18n
 */

class eZTSTranslatorTest extends PHPUnit\Framework\TestCase
{
    const LOCALE = 'tst-TS';

    private $dir;
    private $savedSettings;
    private $savedGlobals;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/phpunit-tstranslator-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir . '/' . self::LOCALE, 0777, true );

        $ini = eZINI::instance();
        $this->savedSettings = array(
            'TranslationRepository' => $ini->variable( 'RegionalSettings', 'TranslationRepository' ),
            'TranslationExtensions' => $ini->variable( 'RegionalSettings', 'TranslationExtensions' ),
        );
        $ini->setVariable( 'RegionalSettings', 'TranslationRepository', $this->dir . '/' );
        $ini->setVariable( 'RegionalSettings', 'TranslationExtensions', array() );

        $this->savedGlobals = array();
        foreach ( array( 'eZTSTranslationTables', 'eZTranslatorManagerInstance', 'eZTranslationCacheTable' ) as $key )
            $this->savedGlobals[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        unset( $GLOBALS['eZTSTranslationTables'], $GLOBALS['eZTranslatorManagerInstance'] );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance();
        foreach ( $this->savedSettings as $name => $value )
            $ini->setVariable( 'RegionalSettings', $name, $value );
        foreach ( $this->savedGlobals as $key => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $saved[0];
        }
        foreach ( glob( $this->dir . '/' . self::LOCALE . '/*' ) as $file )
            unlink( $file );
        rmdir( $this->dir . '/' . self::LOCALE );
        rmdir( $this->dir );
    }

    private function writeTs( $contexts, $filename = 'translation.ts' )
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n<!DOCTYPE TS>\n<TS version=\"2.0\" language=\"de_DE\">\n" . $contexts . "</TS>\n";
        file_put_contents( $this->dir . '/' . self::LOCALE . '/' . $filename, $xml );
    }

    private function sample()
    {
        $this->writeTs(
            "<context><name>kernel/test</name>\n" .
            "<message><source>Hello</source><translation>Hallo</translation></message>\n" .
            "<message><source>Save</source><comment>button</comment><translation>Speichern</translation></message>\n" .
            "<message><source>Save</source><translation>Sichern</translation></message>\n" .
            "<message><source>Empty comment</source><comment/><translation>Leerer Kommentar</translation></message>\n" .
            "<message><source>Not yet</source><translation type=\"unfinished\"></translation></message>\n" .
            "<message><source>Tab<byte value=\"x9\"/>here</source><translation>Tab<byte value=\"x9\"/>hier</translation></message>\n" .
            "</context>\n" .
            "<context><name>design/test</name>\n" .
            "<message><source>Hello</source><translation>Servus</translation></message>\n" .
            "</context>\n"
        );
        $translator = new eZTSTranslator( self::LOCALE, 'translation.ts', false );
        $this->assertTrue( $translator->load( 'kernel/test' ) );
        return $translator;
    }

    public function testTranslations()
    {
        $t = $this->sample();
        $this->assertTrue( $t->isKeyBased() );
        $this->assertSame( 'Hallo', $t->translate( 'kernel/test', 'Hello' ) );
        $this->assertSame( 'Servus', $t->translate( 'design/test', 'Hello' ), 'each context has its own messages' );
        $this->assertNull( $t->translate( 'kernel/test', 'Missing' ) );
        $this->assertNull( $t->translate( 'other/context', 'Hello' ) );
    }

    public function testCommentsSelectTheMessage()
    {
        $t = $this->sample();
        $this->assertSame( 'Speichern', $t->translate( 'kernel/test', 'Save', 'button' ) );
        $this->assertSame( 'Sichern', $t->translate( 'kernel/test', 'Save' ) );
        $this->assertSame( 'Sichern', $t->translate( 'kernel/test', 'Save', 'other comment' ), 'falls back to the message without comment' );
        $msg = $t->findMessage( 'kernel/test', 'Save', 'button' );
        $this->assertSame( 'button', $msg['comment'] );
        $this->assertSame( eZTranslatorManager::createKey( 'kernel/test', 'Save', 'button' ), $msg['key'] );
    }

    /**
     * An empty <comment/> is valid in the schema; reading it failed on the missing text node and stopped the load.
     */
    public function testEmptyCommentIsNoComment()
    {
        $t = $this->sample();
        $this->assertSame( 'Leerer Kommentar', $t->translate( 'kernel/test', 'Empty comment' ) );
    }

    public function testUnfinishedMessageFallsBackToTheSource()
    {
        $this->assertSame( 'Not yet', $this->sample()->translate( 'kernel/test', 'Not yet' ) );
    }

    /**
     * Qt writes bytes as <byte value="x9"/>. The value was read with intval( '0x9' ), which is 0 since PHP 7, so
     * every such byte became a NUL character and the message could not be found.
     */
    public function testByteElements()
    {
        $this->assertSame( "Tab\thier", $this->sample()->translate( 'kernel/test', "Tab\there" ) );
        $this->assertSame( 'A', eZTSTranslator::byteValue( 'x41' ) );
        $this->assertSame( 'A', eZTSTranslator::byteValue( 'X41' ) );
        $this->assertSame( 'A', eZTSTranslator::byteValue( '65' ) );
        $this->assertSame( "\0", eZTSTranslator::byteValue( '' ) );
    }

    public function testKeysInsertAndRemove()
    {
        $t = $this->sample();
        $key = eZTranslatorManager::createKey( 'kernel/test', 'Hello' );
        $this->assertSame( 'Hallo', $t->keyTranslate( $key ) );
        $this->assertSame( 'Hello', $t->findKey( $key )['source'] );
        $this->assertNull( $t->keyTranslate( md5( 'nothing' ) ) );

        $newKey = $t->insert( '', 'Added', 'Hinzugefügt' );
        $this->assertSame( eZTranslatorManager::createKey( 'default', 'Added' ), $newKey );
        $this->assertSame( 'Hinzugefügt', $t->translate( 'default', 'Added' ) );

        $this->assertTrue( $t->remove( '', 'Added' ) );
        $this->assertNull( $t->translate( 'default', 'Added' ) );
        $this->assertFalse( $t->remove( '', 'Added' ) );
        $this->assertTrue( $t->removeKey( $key ) );
        $this->assertFalse( $t->removeKey( $key ) );
        $this->assertNull( $t->translate( 'kernel/test', 'Hello' ) );
    }

    public function testFileThatDoesNotValidateIsSkipped()
    {
        $this->writeTs( "<context><name>x</name><message><translation>no source</translation></message></context>\n" );
        $translator = new eZTSTranslator( self::LOCALE, 'translation.ts', false );
        $this->assertFalse( @$translator->load( 'x' ) );
        $this->assertNull( $translator->translate( 'x', 'anything' ) );
    }

    public function testMissingFile()
    {
        $translator = new eZTSTranslator( self::LOCALE, 'no-such-file.ts', false );
        $this->assertFalse( $translator->load( 'x' ) );
    }

    public function testInitializeRegistersOneTranslatorPerFile()
    {
        $this->sample();
        $a = eZTSTranslator::initialize( 'kernel/test', self::LOCALE, 'translation.ts', false );
        $b = eZTSTranslator::initialize( 'design/test', self::LOCALE, 'translation.ts', false );
        $this->assertSame( $a, $b );
        $this->assertSame( 'Hallo', eZTranslatorManager::instance()->translate( 'kernel/test', 'Hello' ) );
        eZTSTranslator::resetGlobals();
        $this->assertArrayNotHasKey( 'eZTSTranslationTables', $GLOBALS );
    }

    public function testFetchList()
    {
        $this->sample();
        $this->writeTs( "<context><name>c</name><message><source>a</source><translation>b</translation></message></context>\n", 'extra.ts' );
        file_put_contents( $this->dir . '/' . self::LOCALE . '/notes.txt', 'not a translation' );
        $list = eZTSTranslator::fetchList( array( self::LOCALE ) );
        $files = array_map( function ( $t ) { return $t->File; }, $list );
        sort( $files );
        $this->assertSame( array( 'extra.ts', 'translation.ts' ), $files );
        $this->assertSame( self::LOCALE, $list[0]->Locale );
    }

    public static function shippedProvider()
    {
        $root = dirname( __DIR__, 4 ) . '/share/translations';
        $rows = array();
        foreach ( glob( $root . '/*/*.ts' ) as $file )
            $rows[substr( $file, strlen( $root ) + 1 )] = array( $file );
        return $rows;
    }

    /**
     * A broken translation file is skipped with a warning, and the site shows English: every shipped one must
     * validate and load.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('shippedProvider')]
    public function testShippedTranslationValidates( $file )
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );
        $this->assertTrue( @$doc->load( $file ), "$file is XML" );
        $this->assertTrue( @eZTSTranslator::validateDOMTree( $doc ), "$file validates against ts.rng" );
    }
}
