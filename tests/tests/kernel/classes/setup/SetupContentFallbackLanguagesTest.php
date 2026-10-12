<?php
/**
 * An installation without a language map (the kickstarter, exp:install) keeps the site package's content in its
 * own languages: an object in none of the chosen languages needs its language as a fallback of the site, or every
 * lookup of it misses (the CJW starter content, ger-DE, installed with eng-US alone logged 125 errors). No
 * database: the package files are written into a throwaway directory under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class SetupContentFallbackLanguagesTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $root;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 5 ) . '/var/tmp/setup-content-fallback-test-' . getmypid() . '-' . mt_rand();
        mkdir( $this->root . '/ezcontentobject', 0775, true );
    }

    protected function tearDown(): void
    {
        foreach ( glob( $this->root . '/ezcontentobject/*' ) ?: array() as $file )
            unlink( $file );
        @rmdir( $this->root . '/ezcontentobject' );
        foreach ( glob( $this->root . '/*' ) ?: array() as $file )
            unlink( $file );
        @rmdir( $this->root );
    }

    public function testContentInAChosenLanguageAddsNothing()
    {
        $this->assertSame( array(), eZStepCreateSites::contentFallbackLanguages(
            array( 'eng-US' ), array(), array( array( 'eng-US' ), array( 'ger-DE', 'eng-US' ) ) ) );
        $this->assertSame( array( 'eng-US' ), eZStepCreateSites::contentFallbackLanguages(
            array( 'ger-DE' ), array( 'eng-US' ), array( array( 'eng-US' ), array( 'ger-DE' ) ) ) );
    }

    public function testContentInNoChosenLanguageAddsItsInitialLanguageOnce()
    {
        $this->assertSame( array( 'ger-DE' ), eZStepCreateSites::contentFallbackLanguages(
            array( 'eng-US' ), array(), array( array( 'ger-DE' ), array( 'ger-DE', 'fre-FR' ), array( 'eng-US' ) ) ) );
        // after the clean data's language; an object the fallbacks so far cover adds nothing
        $this->assertSame( array( 'eng-US', 'ger-DE' ), eZStepCreateSites::contentFallbackLanguages(
            array( 'fre-FR' ), array( 'eng-US' ), array( array( 'ger-DE', 'nor-NO' ), array( 'nor-NO', 'eng-US' ), array( 'nor-NO', 'ger-DE' ) ) ) );
        $this->assertSame( array(), eZStepCreateSites::contentFallbackLanguages( array( 'eng-US' ), array(), array( array(), array( '' ) ) ) );
    }

    public function testObjectLanguageListsReadsEveryObjectFile()
    {
        $files = '';
        $count = eZContentObjectPackageHandler::MAX_LISTED_OBJECTS + 2;
        for ( $i = 1; $i <= $count; $i++ )
        {
            $languages = $i % 2 ? array( 'ger-DE' ) : array( 'eng-US', 'ger-DE' );
            $this->writeObject( "object-o-$i.xml", $languages );
            $files .= "    <object-file filename=\"object-o-$i.xml\"/>\n";
        }
        file_put_contents( $this->root . '/ezcontentobject/contentobjects.xml', "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
            "<content-object>\n  <object-files-list>\n$files  </object-files-list>\n</content-object>\n" );

        $handler = new eZContentObjectPackageHandler();
        $lists = $handler->objectLanguageLists( new SetupContentFallbackTestPackage( $this->root ),
                                                array( 'type' => 'ezcontentobject', 'filename' => 'contentobjects', 'sub-directory' => 'ezcontentobject' ) );
        $this->assertCount( $count, $lists, 'every object, not only the first MAX_LISTED_OBJECTS' );
        $this->assertSame( array( 'ger-DE' ), $lists[0] );
        $this->assertSame( array( 'eng-US', 'ger-DE' ), $lists[1] );
        $this->assertSame( array( 'ger-DE' ), eZStepCreateSites::contentFallbackLanguages( array( 'eng-US' ), array(), $lists ) );
        $this->assertSame( array(), $handler->objectLanguageLists( new SetupContentFallbackTestPackage( $this->root ),
                                                                   array( 'type' => 'ezcontentobject', 'filename' => 'missing', 'sub-directory' => '' ) ) );
    }

    private function writeObject( $file, array $languages )
    {
        $translations = '';
        foreach ( $languages as $language )
            $translations .= "      <ezobject:object-translation language=\"$language\" object_name=\"Name $language\"/>\n";
        file_put_contents( $this->root . '/ezcontentobject/' . $file, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
            "<ezremote:object xmlns:ezremote=\"http://ez.no/ezobject\" xmlns:ezobject=\"http://ez.no/object/\" remote_id=\"r-$file\" " .
            "initial_language=\"{$languages[0]}\">\n  <ezremote:version-list>\n    <ezobject:version ezremote:version=\"1\" initial_language=\"{$languages[0]}\">\n" .
            $translations . "    </ezobject:version>\n  </ezremote:version-list>\n</ezremote:object>\n" );
    }
}

/** The two methods of eZPackage the object handler reads object files with. */
class SetupContentFallbackTestPackage
{
    private $path;

    public function __construct( $path )
    {
        $this->path = $path;
    }

    public function path()
    {
        return $this->path;
    }

    public function fetchDOMFromFile( $file )
    {
        if ( !is_file( $file ) )
            return false;
        $dom = new DOMDocument( '1.0', 'utf-8' );
        return $dom->load( $file ) ? $dom : false;
    }
}
