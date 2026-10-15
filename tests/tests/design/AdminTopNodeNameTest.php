<?php
/**
 * The top node of the content tree (node 1, no object) is named "Content": "Inhalte" in German, "Content" in American
 * English. The source text stays "Top Level Nodes" (kernel/content), so its users do not change: the tree of the left
 * menu and the name of node 1 (eZContentObjectTreeNode), the online editor's browse dialog (ezoe's server function,
 * and TinyMCE 8's dialog path), and the sentence of the top node's own page under the sub items. No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminTopNodeNameTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminTopNodeNameTest extends PHPUnit\Framework\TestCase
{
    const TOP_NODE_SENTENCE = 'This is the top node of the content structure. It is not a content object: it has no class, attributes, translations, versions or other locations, and it cannot be edited, moved or removed. The top-level nodes it holds are listed under Sub items.';

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function translation( $file, $context, $source )
    {
        $dom = new DOMDocument();
        self::assertTrue( $dom->load( self::root() . '/' . $file ) );
        $query = sprintf( "//context[name='%s']/message[source=%s]/translation", $context,
                          strpos( $source, "'" ) === false ? "'$source'" : '"' . $source . '"' );
        $nodes = ( new DOMXPath( $dom ) )->query( $query );
        self::assertSame( 1, $nodes->length, "$file has \"$source\" once in $context" );
        return $nodes->item( 0 )->textContent;
    }

    public function testTheTopNodeIsCalledContent()
    {
        foreach ( array( 'share/translations', 'extension/ezoe/translations' ) as $dir )
        {
            $this->assertSame( 'Inhalte', self::translation( "$dir/ger-DE/translation.ts", 'kernel/content', 'Top Level Nodes' ) );
            $this->assertSame( 'Content', self::translation( "$dir/eng-US/translation.ts", 'kernel/content', 'Top Level Nodes' ) );
        }
        // the users of the source text are unchanged
        $this->assertStringContainsString( "ezpI18n::tr( 'kernel/content', 'Top Level Nodes' )",
            (string)file_get_contents( self::root() . '/kernel/classes/ezcontentobjecttreenode.php' ) );
        $this->assertStringContainsString( "ezpI18n::tr( 'kernel/content', 'Top Level Nodes')",
            (string)file_get_contents( self::root() . '/extension/ezoe/classes/ezoeserverfunctions.php' ) );
    }

    public function testTheTinyMCE8DialogPathNamesItContent()
    {
        $js = (string)file_get_contents( self::root() . '/extension/ezoe/design/standard/javascript/tinymce8_ez/ezoe_dialog.js' );
        $this->assertStringContainsString( "path.unshift( { node_id: 1, name: t( 'Content' ) } );", $js );
        $this->assertStringContainsString( "'Content': 'Inhalte',", $js );
        $this->assertStringNotContainsString( 'Top level nodes', $js );
    }

    public function testTheTopNodesPageSaysTheContentIsUnderSubItems()
    {
        foreach ( array( 'design/admin', 'design/admin4' ) as $design )
            $this->assertStringContainsString( "{'" . self::TOP_NODE_SENTENCE . "'|i18n( 'design/admin/node/view/full' )}",
                (string)file_get_contents( self::root() . "/$design/templates/node/top_node_full.tpl" ) );
        $this->assertStringEndsWith( 'Die Inhalte darunter sind unter Unterelemente aufgeführt.',
            self::translation( 'share/translations/ger-DE/translation.ts', 'design/admin/node/view/full', self::TOP_NODE_SENTENCE ) );
        $this->assertStringEndsWith( 'The content below it is listed under Sub items.',
            self::translation( 'share/translations/eng-US/translation.ts', 'design/admin/node/view/full', self::TOP_NODE_SENTENCE ) );
    }
}
