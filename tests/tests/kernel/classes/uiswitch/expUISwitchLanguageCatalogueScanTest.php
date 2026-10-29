<?php
/**
 * How the interface language catalogue reads the translation files fast enough for Setup > Interface languages to be
 * built within a request (a cold build took 26 s against Velocity's 30 s limit): the string scan of a well-formed
 * Qt Linguist file gives exactly the keys of the XMLReader stream (entities, numerus forms, Qt <byte/>, comments,
 * obsolete and unfinished messages, XML comments, CRLF line ends, several contexts); a broken file, CDATA or another
 * encoding is streamed as before; the masks are kept per file, so a changed translation reads only itself again and
 * a changed source catalogue all of them; a cache file gets the installation's file mode, so root (Velocity) and the
 * site user (PHP-FPM) can both replace it. Without a database: a fixture installation in var/tmp.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/uiswitch/expUISwitchLanguageCatalogueScanTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$expUISwitchRoot = dirname( __DIR__, 5 );
require_once $expUISwitchRoot . '/kernel/classes/uiswitch/expuiswitchlanguagecatalogue.php';
unset( $expUISwitchRoot );

/** the catalogue with its two readers open, and a count of the masks it builds */
class expUISwitchLanguageCatalogueScanProbe extends expUISwitchLanguageCatalogue
{
    /** @var string[] the files translatedMask() read */
    public $read = array();

    public function scanned( $file, $translatedOnly )
    {
        $text = $this->scannableText( $file );
        if ( $text === null )
            return null;
        return $this->scanMessageKeys( $text, $translatedOnly );
    }

    public function streamed( $file, $translatedOnly )
    {
        $keys = array();
        $this->readMessageKeys( $file, $translatedOnly, function ( $key ) use ( &$keys ) { $keys[] = $key; } );
        return $keys;
    }

    public function translatedMask( $file, array $sourceIndex )
    {
        $this->read[] = basename( dirname( $file ) );
        return parent::translatedMask( $file, $sourceIndex );
    }
}

class expUISwitchLanguageCatalogueScanTest extends PHPUnit\Framework\TestCase
{
    /** @var string the fixture installation */
    protected $fixture;

    protected function setUp(): void
    {
        $this->fixture = dirname( __DIR__, 5 ) . '/var/tmp/uiswitch-catalogue-scan-test-' . getmypid() . '-' . mt_rand();
        expUISwitchLanguageCatalogue::resetMemory();
        foreach ( array( 'eng-GB' => 'en-GB', 'ger-DE' => 'de-DE', 'fre-FR' => 'fr-FR', 'ger-AT' => 'de-AT' ) as $code => $http )
            $this->write( "share/locale/$code.ini", "[RegionalSettings]\nLanguageName=$code\nInternationalLanguageName=$code\n[HTTP]\nContentLanguage=$http\n" );
        $this->messages( 'untranslated', array( 'a' => '', 'b' => '', 'c' => '', 'd' => '' ) );
        $this->messages( 'ger-DE', array( 'a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D' ) );
        $this->messages( 'fre-FR', array( 'a' => 'A' ) );
    }

    protected function tearDown(): void
    {
        expUISwitchLanguageCatalogue::resetMemory();
        $this->removeTree( $this->fixture );
    }

    protected function removeTree( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        foreach ( scandir( $dir ) as $name )
        {
            if ( $name === '.' || $name === '..' )
                continue;
            $path = $dir . '/' . $name;
            is_dir( $path ) && !is_link( $path ) ? $this->removeTree( $path ) : unlink( $path );
        }
        rmdir( $dir );
    }

    protected function write( $relative, $text )
    {
        $path = $this->fixture . '/' . $relative;
        if ( !is_dir( dirname( $path ) ) )
            mkdir( dirname( $path ), 0777, true );
        file_put_contents( $path, $text );
        return $path;
    }

    protected function messages( $locale, array $messages )
    {
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<!DOCTYPE TS>\n<TS version=\"2.0\">\n<context>\n    <name>ctx</name>\n";
        foreach ( $messages as $source => $translation )
            $xml .= "    <message>\n        <source>$source</source>\n        <translation" . ( $translation === '' ? ' type="unfinished"' : '' ) . ">$translation</translation>\n    </message>\n";
        return $this->write( "share/translations/$locale/translation.ts", $xml . "</context>\n</TS>\n" );
    }

    protected function probe( $cache = '' )
    {
        return new expUISwitchLanguageCatalogueScanProbe( $this->fixture, $cache, array( 'ger-AT' => 'ger-DE' ) );
    }

    /** a file with everything a Qt Linguist file of the installation holds */
    protected function trickyFile( $lineEnd = "\n" )
    {
        $text = <<<'TS'
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE TS>
<TS version="2.1" language="de_DE">
<!-- <context><name>commented</name><message><source>not a message</source><translation>x</translation></message></context> -->
<context>
    <name> kernel/content </name>
    <message>
        <location filename="a.tpl" line="3"/>
        <source>Fish &amp; chips &lt;b&gt;&quot;now&quot;&apos;s&#160;&#x263A;</source>
        <translation>Fisch &amp; Pommes</translation>
    </message>
    <message>
        <source>Same text</source>
        <comment>button</comment>
        <translatorcomment>not the comment</translatorcomment>
        <extracomment>neither</extracomment>
        <translation>Gleicher Text</translation>
    </message>
    <message>
        <source>Same text</source>
        <translation type="unfinished">Gleicher Text?</translation>
    </message><message numerus="yes">
        <source>%n item(s)</source>
        <translation>
            <numerusform>%n Eintrag</numerusform>
            <numerusform>%n Einträge</numerusform>
        </translation>
    </message>
    <message>
        <source>A <byte value="x9"/>tab</source>
        <translation>Ein Tab</translation>
    </message>
    <message>
        <source>Gone</source>
        <translation type='obsolete'>Weg</translation>
    </message>
    <message>
        <source>Empty</source>
        <translation/>
    </message>
    <message>
        <source>Blank</source>
        <translation>   </translation>
    </message>
    <message>
        <oldsource>Old</oldsource>
        <source>Two
lines</source>
        <translation>Zwei
Zeilen</translation>
    </message>
</context>
<context>
    <name>second</name>
    <message>
        <source/>
        <translation>leer</translation>
    </message>
    <message>
        <source>Two
lines</source>
        <translation type="vanished">x</translation>
    </message>
</context>
</TS>
TS;
        return $this->write( 'share/translations/ger-CH/translation.ts', str_replace( "\n", $lineEnd, $text ) );
    }

    public function testTheScanGivesTheKeysOfTheXmlStream()
    {
        $probe = $this->probe();
        foreach ( array( "\n", "\r\n" ) as $lineEnd )
        {
            $file = $this->trickyFile( $lineEnd );
            foreach ( array( true, false ) as $translatedOnly )
            {
                $scanned = $probe->scanned( $file, $translatedOnly );
                $this->assertNotNull( $scanned, 'a well-formed UTF-8 file is scanned' );
                $this->assertSame( $probe->streamed( $file, $translatedOnly ), $scanned, ( $translatedOnly ? 'translated' : 'all' ) . ' with ' . json_encode( $lineEnd ) );
            }
        }
        $keys = $probe->messageKeys( $file, true );
        $this->assertArrayHasKey( "kernel/content\x1FFish & chips <b>\"now\"'s\u{a0}\u{263a}\x1F", $keys, 'entities resolved, context name trimmed' );
        $this->assertArrayHasKey( "kernel/content\x1FSame text\x1Fbutton", $keys, 'the comment, not the translator comment' );
        $this->assertArrayNotHasKey( "kernel/content\x1FSame text\x1F", $keys, 'unfinished' );
        $this->assertArrayHasKey( "kernel/content\x1F%n item(s)\x1F", $keys, 'numerus forms are a translation' );
        $this->assertArrayHasKey( "kernel/content\x1FA tab\x1F", $keys, 'a <byte/> element has no text' );
        $this->assertArrayHasKey( "kernel/content\x1FTwo\nlines\x1F", $keys, 'line ends as the parser gives them' );
        $this->assertArrayHasKey( "second\x1F\x1F", $keys, 'an empty source' );
        $this->assertCount( 7, $keys, 'not: obsolete, empty, blank, unfinished, the commented-out message' );
        $this->assertArrayNotHasKey( "kernel/content\x1FGone\x1F", $probe->messageKeys( $file, false ), 'obsolete is no source message' );
    }

    public function testABrokenFileCdataOrAnotherEncodingIsStreamedAsBefore()
    {
        $probe = $this->probe();
        $head = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<TS>\n<context><name>c</name>\n";
        $broken = $this->write( 'share/translations/one/translation.ts', $head . "<message><source>a</source><translation>A</translation></message><message><source>z</source><translation>Z</translation></message>\n<message><source>b</source><translation>B</translation>\n<<<<<<< HEAD\n" );
        $this->assertNull( $probe->scanned( $broken, true ), 'a broken file is not scanned' );
        $this->assertSame( array( "c\x1Fa\x1F", "c\x1Fz\x1F" ), array_keys( $probe->messageKeys( $broken, true ) ),
                           'it counts what was read before the error, a message right after another too' );

        $cdata = $this->write( 'share/translations/two/translation.ts', $head . "<message><source><![CDATA[x < y]]></source><translation>X</translation></message>\n</context></TS>\n" );
        $this->assertNull( $probe->scanned( $cdata, true ) );
        $this->assertSame( array( "c\x1Fx < y\x1F" ), array_keys( $probe->messageKeys( $cdata, true ) ) );

        $latin = $this->write( 'share/translations/three/translation.ts', "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n<TS><context><name>c</name><message><source>caf\xE9</source><translation>x</translation></message></context></TS>\n" );
        $this->assertNull( $probe->scanned( $latin, true ) );
        $this->assertSame( array( "c\x1Fcafé\x1F" ), array_keys( $probe->messageKeys( $latin, true ) ) );
    }

    public function testTheShippedTranslationsScanAsTheyStream()
    {
        $root = dirname( __DIR__, 5 );
        $probe = new expUISwitchLanguageCatalogueScanProbe( $root, '' );
        foreach ( array( 'untranslated', 'ger-DE', 'fre-FR' ) as $code )
        {
            $file = $root . '/share/translations/' . $code . '/translation.ts';
            if ( !is_file( $file ) )
                continue;
            $scanned = $probe->scanned( $file, $code !== 'untranslated' );
            $this->assertNotNull( $scanned, "$code is scanned" );
            $this->assertSame( $probe->streamed( $file, $code !== 'untranslated' ), $scanned, $code );
        }
    }

    public function testMasksAreKeptPerFileSoOnlyAChangedTranslationIsReadAgain()
    {
        $cache = $this->fixture . '/cache';
        $first = $this->probe( $cache );
        $entries = $first->build();
        $this->assertSame( 100, $entries['ger-DE']['coverage'] );
        $this->assertSame( 100, $entries['ger-AT']['coverage'], 'through its fallback' );
        $this->assertSame( 25, $entries['fre-FR']['coverage'] );
        $this->assertSame( array( 'fre-FR', 'ger-DE' ), $this->sorted( $first->read ), 'each translation read once' );
        $this->assertFileExists( $cache . '/uiswitch/translation-masks.json' );

        // a new process: nothing read again
        $again = $this->probe( $cache );
        $this->assertSame( $entries, $again->build() );
        $this->assertSame( array(), $again->read );

        // a translation landing changes one file: only that one is read again
        $file = $this->messages( 'fre-FR', array( 'a' => 'A', 'b' => 'B', 'c' => 'C' ) );
        touch( $file, time() + 5 );
        $changed = $this->probe( $cache );
        $entries = $changed->build();
        $this->assertSame( 75, $entries['fre-FR']['coverage'] );
        $this->assertSame( 100, $entries['ger-DE']['coverage'] );
        $this->assertSame( array( 'fre-FR' ), $changed->read );

        // a changed source catalogue: every mask is built again
        $source = $this->messages( 'untranslated', array( 'a' => '', 'b' => '', 'c' => '', 'd' => '', 'e' => '' ) );
        touch( $source, time() + 10 );
        $grown = $this->probe( $cache );
        $entries = $grown->build();
        $this->assertSame( 60, $entries['fre-FR']['coverage'] );
        $this->assertSame( 80, $entries['ger-DE']['coverage'] );
        $this->assertSame( array( 'fre-FR', 'ger-DE' ), $this->sorted( $grown->read ) );

        // a broken store is ignored
        file_put_contents( $cache . '/uiswitch/translation-masks.json', '{"format":' . expUISwitchLanguageCatalogue::FORMAT . ',"masks":{"' . addslashes( $file ) . '":{"stamp":"x"}}}' );
        $this->assertSame( $entries, $this->probe( $cache )->build() );
    }

    public function testTheCatalogueIsTheSameWithAndWithoutTheMaskStore()
    {
        $this->trickyFile();
        $this->write( 'share/locale/ger-CH.ini', "[RegionalSettings]\nLanguageName=Schweiz\n[HTTP]\nContentLanguage=de-CH\n" );
        $withStore = $this->probe( $this->fixture . '/cache' )->build();
        $this->assertSame( $withStore, $this->probe( '' )->build() );
        $this->assertSame( $withStore, $this->probe( $this->fixture . '/cache' )->build(), 'from the store' );
    }

    public function testCacheFilesGetTheInstallationsFileMode()
    {
        if ( !class_exists( 'eZFile' ) || !class_exists( 'eZDir' ) || !class_exists( 'eZINI' ) )
            $this->markTestSkipped( 'needs the kernel file classes' );
        $file = $this->fixture . '/cache/uiswitch/mode-test.json';
        $old = umask( 0077 );
        try
        {
            $this->assertTrue( expUISwitchLanguageCatalogue::writeCacheFile( $file, '{}' ) );
        }
        finally
        {
            umask( $old );
        }
        $mode = eZFile::fileMode( octdec( (string)eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' ) ) );
        clearstatcache();
        $this->assertSame( sprintf( '%o', $mode ), sprintf( '%o', fileperms( $file ) & 0777 ), 'StorageFilePermissions, not the umask of the writer' );
        $this->assertSame( '{}', file_get_contents( $file ) );
        $this->assertFalse( expUISwitchLanguageCatalogue::writeCacheFile( $file, false ), 'nothing to write' );
    }

    protected function sorted( array $list )
    {
        sort( $list );
        return $list;
    }
}
