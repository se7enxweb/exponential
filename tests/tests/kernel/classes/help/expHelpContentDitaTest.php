<?php
/**
 * The DITA 1.3 export of help maintained as content (doc/eng-us/features/6.0/help-content.md, "DITA export"), without a
 * database: a fixed set of topics, chapters, a manual, a figure and glossary entries goes through the export, and the
 * result is checked as the export's own validator and a DITA processor would see it.
 *
 *  HCD-01  every file is well-formed, carries the DITA 1.3 DOCTYPE of its root, and every reference resolves
 *  HCD-02  the topic types: concept (conbody, sections), task (context, prereq, steps with cmd and info, result,
 *          postreq), reference (refbody, every block in a section, simpletable), troubleshooting (condition,
 *          troubleSolution, cause, remedy)
 *  HCD-03  the topic's id, file name and resourceid are its help_id; audience and platform are filter attributes
 *  HCD-04  only Released topics go into an export; a link to a topic outside it goes to its page in the help
 *  HCD-05  the manual is a bookmap with chapters and topicrefs in the manual's order, and a DITAVAL of its audiences
 *  HCD-06  a task whose steps are not one numbered list becomes steps-informal
 *  HCD-07  figures: the image goes into images/, fig with title and alt, in the field the figure names
 *  HCD-08  the glossary: glossentry with the avoided words as prohibited synonyms; the pool map lists everything
 *  HCD-09  one manual: only its topics; the validator finds broken references, wrong DOCTYPEs, duplicate ids, bad XML
 *  HCD-10  write(): a second run changes nothing; files of an earlier export that are gone are removed, others kept
 *  HCD-11  the help's markup: bold is a UI label, code, links, callouts as notes, headings in flat content
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpContentDitaTest extends PHPUnit\Framework\TestCase
{
    /** @var string a directory below var/tmp for the files of a test */
    private $dir;

    /** @var string a one pixel PNG */
    private $png;

    protected function setUp(): void
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/phpunit-exphelpdita-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) );
        mkdir( $this->dir . '/storage', 0755, true );
        $this->png = $this->dir . '/storage/edit.png';
        file_put_contents( $this->png, base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==' ) );
    }

    protected function tearDown(): void
    {
        $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->dir, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
        foreach ( $files as $file )
            $file->isDir() ? rmdir( $file->getPathname() ) : unlink( $file->getPathname() );
        rmdir( $this->dir );
    }

    private static function topic( $id, $class, $helpId, array $fields, array $over = array() )
    {
        $types = expHelpContentSchema::TOPIC_TYPES;
        return array_replace( array(
            'object_id' => $id, 'node_id' => $id + 100, 'class' => $class, 'type' => $types[$class], 'remote_id' => 'help-topic-' . $helpId,
            'name' => $helpId, 'help_id' => $helpId, 'title' => ucfirst( str_replace( array( '.', '-' ), ' ', $helpId ) ),
            'shortdesc' => 'One sentence about ' . $helpId . '.', 'audience' => array( 'Editors' ), 'platform' => array( 'Admin' ),
            'version_from' => '6.0.15', 'version_to' => '', 'review_date' => 1800000000, 'owner' => 0, 'related' => array(),
            'figures' => array(), 'fields' => $fields, 'section' => 'help_internal', 'section_id' => 7, 'state' => 'released',
            'modified' => 1792281600, 'published' => 1792195200, 'language' => 'eng-US',
        ), $over );
    }

    private function set()
    {
        $topics = array(
            self::topic( 11, 'help_task', 'content.publish-article', array(
                'context' => 'Publishing makes an article visible to readers.',
                'prereq' => 'You need the **Editor** role.',
                'steps' => "1. Click **Create here**.\n2. Choose **Article**.\n\n   The edit form opens.\n3. Click **Publish**.",
                'result' => 'The article is visible. See [versions](help:content.versions).',
                'postreq' => 'Check the article on the site.',
            ), array( 'related' => array( 12, 15 ), 'figures' => array( 31 ), 'audience' => array( 'Editors', 'Visitors' ), 'platform' => array( 'Admin', 'Web' ) ) ),
            self::topic( 12, 'help_concept', 'content.versions', array(
                'body' => "When you edit an object, a draft is made.\n\n## Drafts\n\nA draft is yours until you publish it.\n\n> [!NOTE]\n> Old versions are kept.\n\n## Archive\n\nUse `content/history` to see them.",
            ) ),
            self::topic( 13, 'help_reference', 'content.states', array(
                'body' => "The states of help:\n\n| State | Delivered |\n|---|---|\n| Draft | no |\n| Released | yes |",
            ) ),
            self::topic( 14, 'help_troubleshooting', 'content.publish-fails', array(
                'condition' => 'The **Publish** button does nothing.',
                'cause' => 'A required field is empty.',
                'remedy' => "1. Fill in the field marked red.\n2. Click **Publish** again.",
            ) ),
            self::topic( 15, 'help_concept', 'content.draft-only', array( 'body' => 'Not released.' ), array( 'state' => 'review' ) ),
            self::topic( 16, 'help_task', 'content.loose', array( 'steps' => "Do this, then that.\n\n- one\n- two" ) ),
        );
        $figures = array( array( 'object_id' => 31, 'node_id' => 131, 'class' => 'help_screenshot', 'remote_id' => 'fig-31', 'name' => 'Edit form',
            'section' => 'help_internal', 'state' => 'released', 'modified' => 1792281600, 'language' => 'eng-US',
            'alt_text' => 'The edit form of an article with the Publish button', 'caption' => 'The edit form', 'platform' => array(),
            'version' => '', 'figure_language' => '', 'topic_field' => 'result', 'figure_id' => 'content.publish-article.edit-form',
            'has_image' => true, 'image_url' => '/var/site/storage/images/edit.png' ) );
        $maps = array(
            array( 'object_id' => 21, 'node_id' => 121, 'class' => 'help_manual', 'remote_id' => 'help-manual-editors', 'name' => 'Manual for editors',
                   'section' => 'help_internal', 'state' => 'released', 'modified' => 1792281600, 'published' => 1792195200, 'language' => 'eng-US',
                   'title' => 'Manual for editors', 'subtitle' => 'Writing and publishing', 'shortdesc' => 'Everything an editor needs.',
                   'audience' => array( 'Editors' ), 'edition' => '6.0.15', 'date' => 1792195200, 'license' => 'GNU Free Documentation License 1.3',
                   'authors' => array( 'The help team' ), 'cover' => 0, 'topicrefs' => array( 22, 14, 15 ) ),
            array( 'object_id' => 22, 'node_id' => 122, 'class' => 'help_section', 'remote_id' => 'help-chapter-publishing', 'name' => 'Publishing',
                   'section' => 'help_internal', 'state' => 'released', 'modified' => 1792281600, 'published' => 1792195200, 'language' => 'eng-US',
                   'title' => 'Publishing', 'subtitle' => '', 'shortdesc' => 'From draft to page.', 'audience' => array(), 'edition' => '', 'date' => 0,
                   'license' => '', 'authors' => array(), 'cover' => 0, 'topicrefs' => array( 11, 12 ) ),
        );
        $glossary = array(
            array( 'object_id' => 41, 'node_id' => 141, 'class' => 'help_glossary_entry', 'remote_id' => 'g-41', 'name' => 'Object',
                   'section' => 'help_internal', 'state' => 'released', 'modified' => 1792281600, 'language' => 'eng-US', 'term' => 'Object',
                   'definition' => 'A piece of **content** with fields.', 'avoid' => array( 'item', 'node' ), 'audience' => array( 'Editors' ), 'note' => 'Use in the admin.' ),
            array( 'object_id' => 42, 'node_id' => 142, 'class' => 'help_glossary_entry', 'remote_id' => 'g-42', 'name' => 'Draft only',
                   'section' => 'help_internal', 'state' => 'draft', 'modified' => 1792281600, 'language' => 'eng-US', 'term' => 'Unreleased',
                   'definition' => 'x', 'avoid' => array(), 'audience' => array(), 'note' => '' ),
        );
        return array( 'topics' => $topics, 'figures' => $figures, 'maps' => $maps, 'glossary' => $glossary, 'objects' => array() );
    }

    private function exporter( $manual = null )
    {
        $repository = new expHelpContentDitaTestRepository( $this->set() );
        $provider = new expHelpContentDitaTestProvider( $repository );
        return new expHelpContentDitaTestExport( $provider, $manual, function ( $helpId ) { return 'https://help.example.org/exphelp/topic/' . $helpId; }, $this->png );
    }

    private static function dom( $xml )
    {
        $doc = new DOMDocument();
        self::assertTrue( $doc->loadXML( $xml, LIBXML_NONET ), $xml );
        return $doc;
    }

    private static function xpath( $xml, $query )
    {
        $xpath = new DOMXPath( self::dom( $xml ) );
        $values = array();
        foreach ( $xpath->query( $query ) as $node )
            $values[] = $node instanceof DOMAttr ? $node->value : trim( $node->textContent );
        return $values;
    }

    /** HCD-01 */
    public function testWholeExportIsValid()
    {
        $export = $this->exporter()->build();
        $this->assertSame( array(), expHelpContentDita::validate( $export ) );
        $this->assertSame( array( 'help-manual-editors.ditamap', 'help-manual-editors.ditaval', 'help-pool.ditamap' ),
                           array_values( array_filter( array_keys( $export['files'] ), function ( $p ) { return strpos( $p, '/' ) === false; } ) ) );
        foreach ( $export['files'] as $path => $xml )
        {
            if ( substr( $path, -8 ) === '.ditaval' )
                continue;
            $doc = self::dom( $xml );
            $root = $doc->documentElement->nodeName;
            $this->assertSame( expHelpContentDita::DOCTYPES[$path === 'topics/content.loose.dita' ? 'generalTask' : $root][0], $doc->doctype->publicId, $path );
            $this->assertStringContainsString( 'DITA 1.3', $doc->doctype->publicId );
            $this->assertSame( 'en-US', $doc->documentElement->getAttribute( 'xml:lang' ), $path );
        }
        $this->assertSame( array( 'manuals' => 1, 'chapters' => 1, 'topics' => 5, 'figures' => 1, 'glossary' => 1 ), $export['counts'] );
    }

    /** HCD-02 */
    public function testTopicTypes()
    {
        $files = $this->exporter()->build()['files'];
        $task = $files['topics/content.publish-article.dita'];
        $this->assertSame( '-//OASIS//DTD DITA 1.3 Task//EN', self::dom( $task )->doctype->publicId );
        $this->assertSame( array( 'prereq', 'context', 'steps', 'result', 'postreq' ),
                           self::childNames( $task, '/task/taskbody' ) );
        $this->assertSame( array( 'Click Create here.', 'Choose Article.', 'Click Publish.' ), self::xpath( $task, '//steps/step/cmd' ) );
        $this->assertSame( array( 'Create here', 'Article', 'Publish' ), self::xpath( $task, '//steps/step/cmd/uicontrol' ) );
        $this->assertSame( array( 'The edit form opens.' ), self::xpath( $task, '//steps/step/info/p' ) );

        $concept = $files['topics/content.versions.dita'];
        $this->assertSame( array( 'When you edit an object, a draft is made.' ), self::xpath( $concept, '/concept/conbody/p' ) );
        $this->assertSame( array( 'Drafts', 'Archive' ), self::xpath( $concept, '/concept/conbody/section/title' ) );
        $this->assertSame( array( 'content/history' ), self::xpath( $concept, '//section/p/codeph' ) );

        $reference = $files['topics/content.states.dita'];
        $this->assertSame( array(), self::xpath( $reference, '/reference/refbody/p' ), 'refbody holds no bare paragraph' );
        $this->assertSame( array( 'State', 'Delivered' ), self::xpath( $reference, '//simpletable/sthead/stentry' ) );
        $this->assertSame( array( 'Draft', 'no', 'Released', 'yes' ), self::xpath( $reference, '//simpletable/strow/stentry' ) );

        $trouble = $files['topics/content.publish-fails.dita'];
        $this->assertSame( array( 'condition', 'troubleSolution' ), self::childNames( $trouble, '/troubleshooting/troublebody' ) );
        $this->assertSame( array( 'cause', 'remedy' ), self::childNames( $trouble, '//troubleSolution' ) );
        $this->assertSame( array( 'Fill in the field marked red.', 'Click Publish again.' ), self::xpath( $trouble, '//remedy/steps/step/cmd' ) );
    }

    private static function childNames( $xml, $query )
    {
        $xpath = new DOMXPath( self::dom( $xml ) );
        $names = array();
        foreach ( $xpath->query( $query . '/*' ) as $node )
            $names[] = $node->nodeName;
        return $names;
    }

    /** HCD-03 */
    public function testIdsAndFilterAttributes()
    {
        $task = $this->exporter()->build()['files']['topics/content.publish-article.dita'];
        $doc = self::dom( $task );
        $root = $doc->documentElement;
        $this->assertSame( 'content.publish-article', $root->getAttribute( 'id' ) );
        $this->assertSame( 'editors visitors', $root->getAttribute( 'audience' ) );
        $this->assertSame( 'admin web', $root->getAttribute( 'platform' ) );
        $this->assertSame( '6.0.15', $root->getAttribute( 'rev' ) );
        $this->assertSame( array( 'content.publish-article' ), self::xpath( $task, '/task/prolog/resourceid/@appid' ) );
        $this->assertSame( array( '2027-01-15' ), self::xpath( $task, '//othermeta[@name="review_date"]/@content' ) );
        $this->assertSame( array( 'One sentence about content.publish-article.' ), self::xpath( $task, '/task/shortdesc' ) );
        $this->assertSame( 'editors-and-admins', expHelpContentDita::filterValue( array( 'Editors and admins' ) ) );
        $this->assertSame( 'x-123', expHelpContentDita::fileName( '123' ) );
    }

    /** HCD-04 */
    public function testReleasedOnlyAndOutsideLinks()
    {
        $export = $this->exporter()->build();
        $this->assertArrayNotHasKey( 'topics/content.draft-only.dita', $export['files'] );
        $task = $export['files']['topics/content.publish-article.dita'];
        // the related topic in Review is left out, the Released one is inside the export
        $this->assertSame( array( 'content.versions.dita' ), self::xpath( $task, '//related-links/link/@href' ) );
        $this->assertSame( array( 'content.versions.dita' ), self::xpath( $task, '//result//xref/@href' ) );
        $this->assertStringNotContainsString( 'draft-only', implode( '', $export['files'] ) );

        // one manual: a link to a topic that is not in it goes to the help
        $one = $this->exporter( 'help-manual-editors' )->build();
        $concept = $one['files']['topics/content.versions.dita'];
        $this->assertNotEmpty( $concept );
        $this->assertArrayNotHasKey( 'topics/content.states.dita', $one['files'] );
    }

    /** HCD-05 */
    public function testBookmapAndDitaval()
    {
        $files = $this->exporter()->build()['files'];
        $map = $files['help-manual-editors.ditamap'];
        $this->assertSame( array( 'Manual for editors' ), self::xpath( $map, '/bookmap/booktitle/mainbooktitle' ) );
        $this->assertSame( array( 'Writing and publishing' ), self::xpath( $map, '/bookmap/booktitle/booktitlealt' ) );
        $this->assertSame( array( 'chapters/help-chapter-publishing.dita', 'topics/content.publish-fails.dita' ), self::xpath( $map, '/bookmap/chapter/@href' ) );
        $this->assertSame( array( 'topics/content.publish-article.dita', 'topics/content.versions.dita' ), self::xpath( $map, '/bookmap/chapter[1]/topicref/@href' ) );
        $this->assertSame( array( '6.0.15' ), self::xpath( $map, '//bookid/edition' ) );
        $this->assertSame( array( 'The help team' ), self::xpath( $map, '//bookmeta/author' ) );
        $this->assertSame( array( 'Publishing' ), self::xpath( $files['chapters/help-chapter-publishing.dita'], '/concept/title' ) );

        $val = self::dom( $files['help-manual-editors.ditaval'] );
        $actions = array();
        foreach ( $val->getElementsByTagName( 'prop' ) as $prop )
            $actions[$prop->getAttribute( 'val' )] = $prop->getAttribute( 'action' );
        $this->assertSame( 'include', $actions['editors'] );
        $this->assertSame( 'exclude', $actions['visitors'] );
        $this->assertStringContainsString( 'action="include"', expHelpContentDita::ditaval( array(), array( 'A', 'B' ) ) );
        $this->assertStringNotContainsString( 'exclude', expHelpContentDita::ditaval( array(), array( 'A', 'B' ) ) );
    }

    /** HCD-06 */
    public function testInformalSteps()
    {
        $task = $this->exporter()->build()['files']['topics/content.loose.dita'];
        $this->assertSame( '-//OASIS//DTD DITA 1.3 General Task//EN', self::dom( $task )->doctype->publicId );
        $this->assertSame( array(), self::xpath( $task, '//steps' ) );
        $this->assertSame( array( 'Do this, then that.' ), self::xpath( $task, '//steps-informal/p' ) );
        $this->assertSame( array( 'one', 'two' ), self::xpath( $task, '//steps-informal/ul/li' ) );
    }

    /** HCD-07 */
    public function testFigures()
    {
        $export = $this->exporter()->build();
        $this->assertSame( array( 'images/content.publish-article.edit-form.png' => $this->png ), $export['copies'] );
        $task = $export['files']['topics/content.publish-article.dita'];
        $this->assertSame( array( 'The edit form' ), self::xpath( $task, '//result/fig/title' ) );
        $this->assertSame( array( '../images/content.publish-article.edit-form.png' ), self::xpath( $task, '//result/fig/image/@href' ) );
        $this->assertSame( array( 'The edit form of an article with the Publish button' ), self::xpath( $task, '//result/fig/image/alt' ) );
    }

    /** HCD-08 */
    public function testGlossaryAndPoolMap()
    {
        $files = $this->exporter()->build()['files'];
        $entry = $files['glossary/object.dita'];
        $this->assertSame( array( 'Object' ), self::xpath( $entry, '/glossentry/glossterm' ) );
        $this->assertSame( array( 'A piece of content with fields.' ), self::xpath( $entry, '/glossentry/glossdef' ) );
        $this->assertSame( array( 'item', 'node' ), self::xpath( $entry, '//glossAlt/glossSynonym' ) );
        $this->assertSame( array( 'prohibited', 'prohibited' ), self::xpath( $entry, '//glossAlt/glossStatus/@value' ) );
        $this->assertArrayNotHasKey( 'glossary/unreleased.dita', $files );
        $pool = $files['help-pool.ditamap'];
        $this->assertSame( array( 'Concepts', 'Tasks', 'Reference', 'Troubleshooting', 'Glossary' ), self::xpath( $pool, '/map/topichead/topicmeta/navtitle' ) );
        $this->assertCount( 6, self::xpath( $pool, '//topicref/@href' ) );
    }

    /** HCD-09 */
    public function testOneManualAndValidator()
    {
        $one = $this->exporter( 'help-manual-editors' )->build();
        $this->assertSame( array(), expHelpContentDita::validate( $one ) );
        $this->assertArrayNotHasKey( 'help-pool.ditamap', $one['files'] );
        $this->assertSame( 3, $one['counts']['topics'] );

        $broken = $one;
        unset( $broken['files']['topics/content.versions.dita'] );
        $broken['files']['topics/bad.dita'] = '<concept id="content.publish-fails"><title>x</title>';
        $broken['files']['topics/nodoctype.dita'] = '<?xml version="1.0"?><concept id="content.publish-fails"><title>x</title></concept>';
        $messages = array();
        foreach ( expHelpContentDita::validate( $broken ) as $problem )
            $messages[] = $problem['file'] . ': ' . $problem['message'];
        $text = implode( "\n", $messages );
        $this->assertStringContainsString( 'topics/bad.dita: not well-formed XML', $text );
        $this->assertStringContainsString( 'topics/nodoctype.dita: the DOCTYPE is not the DITA 1.3 one of concept', $text );
        $this->assertStringContainsString( 'topics/nodoctype.dita: the topic id content.publish-fails is also the id of topics/content.publish-fails.dita', $text );
        $this->assertStringContainsString( 'help-manual-editors.ditamap: <topicref> href="topics/content.versions.dita" does not resolve', $text );
        $this->assertStringContainsString( 'topics/content.publish-article.dita: <link> href="content.versions.dita" does not resolve', $text );

        $this->expectException( RuntimeException::class );
        $this->exporter( 'help-manual-missing' )->build();
    }

    /** HCD-10 */
    public function testWriteIsIdempotent()
    {
        $out = $this->dir . '/export';
        mkdir( $out . '/.git', 0755, true );
        file_put_contents( $out . '/README.md', 'kept' );
        mkdir( $out . '/topics' );
        file_put_contents( $out . '/topics/gone.dita', 'old' );
        $export = $this->exporter()->build();
        $first = expHelpContentDita::write( $export, $out );
        $this->assertCount( count( $export['files'] ) + count( $export['copies'] ), $first['written'] );
        $this->assertSame( array( 'topics/gone.dita' ), $first['removed'] );
        $this->assertSame( array(), expHelpContentDita::validate( $out ) );
        $second = expHelpContentDita::write( $this->exporter()->build(), $out );
        $this->assertSame( array(), $second['written'] );
        $this->assertSame( array(), $second['removed'] );
        $this->assertFileExists( $out . '/README.md' );
        $this->assertDirectoryExists( $out . '/.git' );
        $dry = expHelpContentDita::write( $this->exporter( 'help-manual-editors' )->build(), $out, true );
        $this->assertContains( 'help-pool.ditamap', $dry['removed'] );
        $this->assertFileExists( $out . '/help-pool.ditamap', 'a dry run removes nothing' );
    }

    /** HCD-11 */
    public function testMarkup()
    {
        $doc = new DOMDocument( '1.0', 'UTF-8' );
        $markup = new expHelpDitaMarkup( $doc, function ( $helpId ) { return $helpId === 'a.b' ? array( 'href' => 'a.b.dita', 'scope' => 'local' ) : null; } );
        $body = $markup->el( 'conbody' );
        $doc->appendChild( $body );
        $markup->appendBlocks( $body, '<div class="a4-help-body"><p>Click <strong>Save</strong> and see <a href="/__dita_topic__/a.b">this</a>, '
            . '<a href="/__dita_topic__/x.y">that</a>, <a href="https://example.org/">site</a>, <a href="#here">here</a>.</p>'
            . '<div class="a4-help-callout a4-help-callout--warning" role="note"><p class="a4-help-callout-title">Warning</p><p>Careful.</p></div>'
            . '<div class="a4-help-code"><pre class="a4-help-pre"><code>a &lt; b</code></pre><button type="button">Copy</button></div>'
            . '<h3 id="x">Heading <a class="a4-help-anchor" href="#x">#</a></h3><p>After.</p></div>', 'sections' );
        $xml = $doc->saveXML( $body );
        $this->assertStringContainsString( '<uicontrol>Save</uicontrol>', $xml );
        $this->assertStringContainsString( '<xref href="a.b.dita" format="dita">this</xref>', $xml );
        $this->assertStringContainsString( ' that, ', $xml, 'a topic the export cannot reach keeps its text' );
        $this->assertStringContainsString( '<xref href="https://example.org/" scope="external" format="html">site</xref>', $xml );
        $this->assertStringContainsString( ' here.', $xml );
        $this->assertStringContainsString( '<note type="warning"><p>Careful.</p></note>', $xml );
        $this->assertStringContainsString( '<codeblock>a &lt; b</codeblock>', $xml );
        $this->assertStringNotContainsString( 'Copy', $xml );
        $this->assertStringContainsString( '<section><title>Heading</title><p>After.</p></section>', $xml );

        $flat = $markup->el( 'context' );
        $markup->appendBlocks( $flat, '<h2>Title</h2>loose <em>text</em>', 'flat' );
        $this->assertSame( '<context><p><b>Title</b></p><p>loose <i>text</i></p></context>', $doc->saveXML( $flat ) );
    }
}

/**
 * A repository with a fixed set (no database)
 */
class expHelpContentDitaTestRepository extends expHelpContentRepository
{
    public function __construct( array $set )
    {
        $this->loaded = $set;
    }
}

/**
 * A provider that lets the test user read every Released object and edit the others (previews), without a database
 */
class expHelpContentDitaTestProvider extends expHelpContentProvider
{
    public function visible( array $item )
    {
        $item['preview'] = $item['state'] !== expHelpContentSchema::STATE_RELEASED;
        return $item;
    }

    public function relations( array $topic )
    {
        $related = array();
        foreach ( $topic['related'] as $id )
        {
            $found = $this->repository()->byObjectId( $id );
            if ( $found !== null && $found['kind'] === 'topics' )
                $related[] = $this->visible( $found['item'] );
        }
        $figures = array();
        foreach ( $topic['figures'] as $id )
        {
            $found = $this->repository()->byObjectId( $id );
            if ( $found !== null && $found['kind'] === 'figures' )
                $figures[] = $found['item'];
        }
        return array( 'related' => $related, 'figures' => $figures );
    }
}

/**
 * The export with the figure's image from a test file instead of the storage
 */
class expHelpContentDitaTestExport extends expHelpContentDita
{
    private $image;

    public function __construct( expHelpContentProvider $provider, $manual, $topicUrl, $image )
    {
        parent::__construct( $provider, $manual, $topicUrl );
        $this->image = $image;
    }

    protected function figureFile( array $figure )
    {
        return $this->image;
    }
}

?>
