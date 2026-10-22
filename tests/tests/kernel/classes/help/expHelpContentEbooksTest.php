<?php
/**
 * E-books of help manuals stored as file objects (doc/eng-us/features/6.0/help-content.md, "Stored e-books"), without a
 * database: what goes into a stored edition, which manuals a release rebuilds, and that the same content gives the
 * same EPUB file (so a rebuild publishes a new version only when the book changed).
 *
 *  HCB-01  the edition of a public manual carries only public, Released topics, chapters and figures; an internal
 *          manual's edition every Released one; never a preview, whoever builds it
 *  HCB-02  the manuals a release rebuilds: the manual itself, through chapters, topics and the figures of topics
 *  HCB-03  remote_ids of stored books, and public manuals from their audience
 *  HCB-04  the EPUB entries carry the book's content date, not the clock
 *  HCB-05  the state assignment calls the release hook, guarded for a server that does not have the class yet
 *  HCB-06  a manual has an edition in the default language, and in another only when it is translated into it
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpContentEbooksTest extends PHPUnit\Framework\TestCase
{
    private static function topic( $id, $helpId, $section, array $audience, $state = 'released', array $figures = array() )
    {
        return array( 'object_id' => $id, 'node_id' => $id + 100, 'class' => 'help_concept', 'type' => 'concept', 'remote_id' => 'help-topic-' . $helpId,
                      'name' => $helpId, 'help_id' => $helpId, 'title' => $helpId, 'shortdesc' => 'About ' . $helpId . '.', 'audience' => $audience,
                      'platform' => array(), 'version_from' => '', 'version_to' => '', 'review_date' => 0, 'owner' => 0, 'related' => array(),
                      'figures' => $figures, 'fields' => array( 'body' => 'Text of ' . $helpId . '.' ), 'section' => $section, 'section_id' => 1,
                      'state' => $state, 'modified' => 1792000000 + $id, 'published' => 1792000000, 'language' => 'eng-US' );
    }

    private static function map( $id, $class, $remoteId, $section, array $refs, array $audience = array() )
    {
        return array( 'object_id' => $id, 'node_id' => $id + 100, 'class' => $class, 'remote_id' => $remoteId, 'name' => $remoteId,
                      'section' => $section, 'state' => 'released', 'modified' => 1792000000, 'published' => 1792000000, 'language' => 'eng-US',
                      'title' => $remoteId, 'subtitle' => '', 'shortdesc' => '', 'audience' => $audience, 'edition' => '', 'date' => 0,
                      'license' => '', 'authors' => array(), 'cover' => 0, 'topicrefs' => $refs );
    }

    private static function set()
    {
        return array(
            'topics' => array(
                self::topic( 1, 'account.log-in', 'help_public', array( 'Visitors' ) ),
                self::topic( 2, 'content.internal', 'help_internal', array( 'Editors' ), 'released', array( 31 ) ),
                self::topic( 3, 'account.stale-section', 'help_public', array( 'Editors' ) ),
                self::topic( 4, 'account.draft', 'help_public', array( 'Visitors' ), 'draft' ),
                self::topic( 5, 'account.outside', 'help_public', array( 'Visitors' ) ),
            ),
            'figures' => array( array( 'object_id' => 31, 'class' => 'help_screenshot', 'section' => 'help_internal', 'state' => 'released',
                                       'image_url' => '/var/site/storage/x.png', 'alt_text' => 'x', 'caption' => '', 'figure_id' => '', 'topic_field' => '' ) ),
            'maps' => array(
                self::map( 21, 'help_manual', 'help-manual-visitors', 'help_public', array( 22, 1, 2, 3, 4 ), array( 'Visitors' ) ),
                self::map( 22, 'help_section', 'help-chapter-a', 'help_public', array( 1 ) ),
                self::map( 23, 'help_manual', 'help-manual-editors', 'help_internal', array( 24 ), array( 'Editors' ) ),
                self::map( 24, 'help_section', 'help-chapter-b', 'help_internal', array( 2, 4 ) ),
            ),
            'glossary' => array(),
            'objects' => array(),
        );
    }

    private static function repository()
    {
        return new expHelpContentEbooksTestRepository( self::set() );
    }

    /** HCB-01 */
    public function testEditions()
    {
        $public = new expHelpContentEditionProvider( self::repository(), true );
        $internal = new expHelpContentEditionProvider( self::repository(), false );
        $set = self::set();
        $byId = array();
        foreach ( array_merge( $set['topics'], $set['maps'], $set['figures'] ) as $item )
            $byId[$item['object_id']] = $item;

        $this->assertNotNull( $public->visible( $byId[1] ) );
        $this->assertNull( $public->visible( $byId[2] ), 'an internal topic never goes into a public book' );
        $this->assertNull( $public->visible( $byId[3] ), 'a topic in help_public whose audience is internal stays out' );
        $this->assertNull( $public->visible( $byId[4] ), 'a Draft never goes into a book' );
        $this->assertNotNull( $public->visible( $byId[22] ) );
        $this->assertNull( $public->visible( $byId[24] ) );
        $this->assertNull( $public->visible( $byId[31] ), 'an internal figure stays out of a public book' );
        $this->assertFalse( $public->visible( $byId[1] )['preview'] );

        $this->assertNotNull( $internal->visible( $byId[2] ) );
        $this->assertNotNull( $internal->visible( $byId[3] ) );
        $this->assertNull( $internal->visible( $byId[4] ) );
        $this->assertSame( array( 31 ), array_column( $internal->relations( $byId[2] )['figures'], 'object_id' ) );
        $this->assertSame( array(), $public->relations( $byId[2] )['figures'] );

        $book = new expHelpContentExport( $public, $byId[21] );
        $this->assertSame( array( 'account.log-in' ), array_keys( $book->topics() ) );
        $book = new expHelpContentExport( $internal, $byId[23] );
        $this->assertSame( array( 'content.internal' ), array_keys( $book->topics() ) );
    }

    /** HCB-02 */
    public function testManualsContaining()
    {
        $set = self::set();
        $this->assertSame( array( 'help-manual-visitors' ), expHelpContentEbooks::manualsContaining( $set, 1 ) );
        $this->assertSame( array( 'help-manual-visitors', 'help-manual-editors' ), expHelpContentEbooks::manualsContaining( $set, 2 ) );
        $this->assertSame( array( 'help-manual-visitors', 'help-manual-editors' ), expHelpContentEbooks::manualsContaining( $set, 31 ), 'a figure of a topic of both manuals' );
        $this->assertSame( array( 'help-manual-editors' ), expHelpContentEbooks::manualsContaining( $set, 24 ), 'a chapter' );
        $this->assertSame( array( 'help-manual-editors' ), expHelpContentEbooks::manualsContaining( $set, 23 ), 'the manual itself' );
        $this->assertSame( array(), expHelpContentEbooks::manualsContaining( $set, 5 ), 'a topic no manual lists' );
    }

    /** HCB-03 */
    public function testRemoteIdsAndPublicManuals()
    {
        $this->assertSame( 'help-ebook-help-manual-editors-epub', expHelpContentEbooks::remoteId( 'help-manual-editors', 'epub' ) );
        $this->assertTrue( expHelpContentEbooks::isPublicManual( array( 'audience' => array( 'Members', 'Visitors' ) ) ) );
        $this->assertFalse( expHelpContentEbooks::isPublicManual( array( 'audience' => array( 'Editors' ) ) ) );
        $this->assertFalse( expHelpContentEbooks::isPublicManual( array( 'audience' => array() ) ) );
    }

    /** HCB-06 */
    public function testEditionOnlyInTheManualsLanguages()
    {
        $this->assertTrue( expHelpContentEbooks::hasEdition( array( 'eng-US' ), 'eng-US', 'eng-US' ), 'the default edition always' );
        $this->assertTrue( expHelpContentEbooks::hasEdition( array(), 'eng-US', 'eng-US' ), 'the default edition even without a language list' );
        $this->assertTrue( expHelpContentEbooks::hasEdition( array( 'eng-US', 'ger-DE' ), 'ger-DE', 'eng-US' ) );
        $this->assertFalse( expHelpContentEbooks::hasEdition( array( 'eng-US' ), 'ger-DE', 'eng-US' ),
                            'an untranslated manual has no German book (it would be a copy of the English one)' );
        $this->assertTrue( expHelpContentEbooks::hasEdition( array( 'eng-US', 'ger-DE' ), 'ger-de', 'eng-US' ), 'as --language=ger-de is accepted' );
    }

    /** HCB-04 */
    public function testEpubDatedByContent()
    {
        if ( !class_exists( 'ZipArchive' ) || !method_exists( 'ZipArchive', 'setMtimeName' ) )
            $this->markTestSkipped( 'no zip extension with setMtimeName' );
        $set = self::set();
        $manual = $set['maps'][0];
        $export = new expHelpContentExport( new expHelpContentEditionProvider( self::repository(), true ), $manual );
        $this->assertSame( 1792000001, $export->modified(), 'the newest topic of the book' );
        $dir = dirname( __DIR__, 5 ) . '/var/tmp/phpunit-exphelpebooks-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) );
        mkdir( $dir, 0755, true );
        try
        {
            $result = $export->writeEpub( $dir . '/book.epub' );
            $zip = new ZipArchive();
            $this->assertTrue( $zip->open( $result['file'] ) );
            $this->assertSame( 'mimetype', $zip->getNameIndex( 0 ) );
            for ( $i = 0; $i < $zip->numFiles; $i++ )
            {
                // DOS time has a resolution of two seconds
                $this->assertEqualsWithDelta( 1792000001, $zip->statIndex( $i )['mtime'], 2, $zip->getNameIndex( $i ) );
            }
            $this->assertStringContainsString( '<meta property="dcterms:modified">2026-10-14T17:46:41Z</meta>', (string)$zip->getFromName( 'OEBPS/content.opf' ) );
            $zip->close();
        }
        finally
        {
            foreach ( glob( $dir . '/*' ) as $file )
                unlink( $file );
            rmdir( $dir );
        }
    }

    /** HCB-05 */
    public function testReleaseHook()
    {
        $source = (string)file_get_contents( dirname( __DIR__, 5 ) . '/kernel/content/ezcontentoperationcollection.php' );
        $this->assertMatchesRegularExpression( "/class_exists\\( 'expHelpContentEbooks' \\)\\s*\\)\\s*\\n\\s*expHelpContentEbooks::onStateAssign\\( \\\$objectID, \\\$selectedStateIDList \\);/", $source );
        $this->assertSame( 'enabled', eZINI::instance( 'exphelp.ini' )->variable( 'ContentSettings', 'EbooksOnRelease' ) );
    }
}

/**
 * A repository with a fixed set (no database)
 */
class expHelpContentEbooksTestRepository extends expHelpContentRepository
{
    public function __construct( array $set )
    {
        $this->loaded = $set;
    }
}

?>
