<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/**
 * cjw_newsletter 4.2.1: the newsletter classes use eng-US.
 *
 * The class packages the extension ships must not name eng-GB (the kernel's package handler creates every language a
 * package names, so eng-GB in a package adds eng-GB to every site that installs the newsletter classes), the
 * installed classes of this site must carry eng-US and no eng-GB, and CjwNewsletterClassLanguages, the 4.2.1
 * upgrade step, must move a list and a mask correctly. Read only: nothing is written to the database.
 */
class cjwNewsletterClassLanguagesTest extends cjwNewsletterTestCase
{
    /** @return array file name => xml of every class definition in the shipped packages */
    protected function shippedClassXml()
    {
        $result = array();
        foreach ( CjwNewsletterClassInstaller::packageFiles() as $file )
        {
            $this->assertFileExists( $file );
            $tar = gzdecode( file_get_contents( $file ) );
            $this->assertNotFalse( $tar, basename( $file ) . ' is a gzip archive' );
            $this->assertStringNotContainsString( 'eng-GB', $tar, basename( $file ) . ' names no eng-GB' );
            foreach ( CjwNewsletterClassInstaller::readClassXml( $file ) as $name => $xml )
                $result[basename( $file ) . '/' . $name] = $xml;
        }
        return $result;
    }

    public function testShippedPackagesNameTheClassesInEngUs()
    {
        $classes = $this->shippedClassXml();
        $this->assertCount( 6, $classes, 'six class definitions' );
        $lists = 0;
        foreach ( $classes as $name => $xml )
        {
            $dom = new DOMDocument();
            $this->assertTrue( $dom->loadXML( $xml ), "$name is XML" );
            foreach ( array( 'serialized-name-list', 'serialized-description-list' ) as $tag )
            {
                foreach ( $dom->getElementsByTagName( $tag ) as $node )
                {
                    $lists++;
                    $list = unserialize( $node->textContent, array( 'allowed_classes' => false ) );
                    $this->assertIsArray( $list, "$name $tag unserializes" );
                    $this->assertArrayNotHasKey( 'eng-GB', $list, "$name $tag" );
                    if ( $tag == 'serialized-name-list' )
                        $this->assertArrayHasKey( 'eng-US', $list, "$name $tag has an eng-US name" );
                    if ( isset( $list['always-available'] ) )
                        $this->assertSame( 'eng-US', $list['always-available'], "$name $tag always available in eng-US" );
                }
            }
        }
        $this->assertGreaterThan( 20, $lists );
    }

    public function testInstalledNewsletterClassesUseEngUs()
    {
        $us = eZContentLanguage::fetchByLocale( 'eng-US' );
        $this->assertNotFalse( $us, 'eng-US is a content language' );
        $usID = (int)$us->attribute( 'id' );
        $gb = eZContentLanguage::fetchByLocale( 'eng-GB' );
        $gbID = $gb ? (int)$gb->attribute( 'id' ) : 0;
        $found = 0;
        foreach ( CjwNewsletterClassInstaller::classIdentifiers() as $identifier )
        {
            $class = eZContentClass::fetchByIdentifier( $identifier );
            if ( !$class )
                continue;
            $found++;
            $this->assertSame( $usID, (int)$class->attribute( 'initial_language_id' ), "$identifier initial language" );
            $this->assertNotSame( 0, (int)$class->attribute( 'language_mask' ) & $usID, "$identifier has eng-US" );
            if ( $gbID )
                $this->assertSame( 0, (int)$class->attribute( 'language_mask' ) & $gbID, "$identifier has no eng-GB" );
            $this->assertStringNotContainsString( 'eng-GB', $class->attribute( 'serialized_name_list' ), $identifier );
            $this->assertStringNotContainsString( 'eng-GB', (string)$class->attribute( 'serialized_description_list' ), $identifier );
            $this->assertNotSame( '', (string)$class->name( 'eng-US' ), "$identifier has an eng-US name" );
            foreach ( $class->fetchAttributes() as $attribute )
            {
                $this->assertStringNotContainsString( 'eng-GB', $attribute->attribute( 'serialized_name_list' ), "$identifier/" . $attribute->attribute( 'identifier' ) );
                $this->assertStringNotContainsString( 'eng-GB', (string)$attribute->attribute( 'serialized_description_list' ), "$identifier/" . $attribute->attribute( 'identifier' ) );
            }
        }
        if ( !$found )
            $this->markTestSkipped( 'The newsletter classes are not installed' );
        $this->assertSame( array(), CjwNewsletterClassLanguages::apply( true )['rows'], 'the upgrade step finds nothing left to change' );
    }

    public function testRemapListMovesTheTextAndTheAlwaysAvailableLanguage()
    {
        $list = serialize( array( 'ger-DE' => 'Liste', 'always-available' => 'eng-GB', 'eng-GB' => 'List' ) );
        $this->assertSame( array( 'ger-DE' => 'Liste', 'always-available' => 'eng-US', 'eng-US' => 'List' ),
                           unserialize( CjwNewsletterClassLanguages::remapList( $list ) ) );

        // eng-US has a text already: it is kept, the eng-GB text goes
        $list = serialize( array( 'eng-US' => 'Color', 'eng-GB' => 'Colour', 'always-available' => 'eng-GB' ) );
        $this->assertSame( array( 'eng-US' => 'Color', 'always-available' => 'eng-US' ),
                           unserialize( CjwNewsletterClassLanguages::remapList( $list ) ) );

        // a ger-DE always-available language moves to eng-US when there is an eng-US text
        $list = serialize( array( 'ger-DE' => 'Konfiguration', 'always-available' => 'ger-DE', 'eng-GB' => 'Configuration' ) );
        $this->assertSame( array( 'ger-DE' => 'Konfiguration', 'always-available' => 'eng-US', 'eng-US' => 'Configuration' ),
                           unserialize( CjwNewsletterClassLanguages::remapList( $list ) ) );

        // nothing to change, or not a list
        $this->assertFalse( CjwNewsletterClassLanguages::remapList( serialize( array( 'eng-US' => 'List', 'always-available' => 'eng-US' ) ) ) );
        $this->assertFalse( CjwNewsletterClassLanguages::remapList( 'not serialized' ) );
        $this->assertFalse( CjwNewsletterClassLanguages::remapList( '' ) );
    }

    public function testRemapMaskMovesTheLanguageBit()
    {
        $this->assertSame( 7, CjwNewsletterClassLanguages::remapMask( 13, 8, 2 ), 'always available + ger-DE + eng-GB -> + eng-US' );
        $this->assertSame( 7, CjwNewsletterClassLanguages::remapMask( 7, 8, 2 ), 'nothing to move' );
        $this->assertSame( 6, CjwNewsletterClassLanguages::remapMask( 14, 8, 2 ), 'eng-US already there' );
        $this->assertSame( 5, CjwNewsletterClassLanguages::remapMask( 5, 0, 2 ), 'no eng-GB on the site' );
    }
}
