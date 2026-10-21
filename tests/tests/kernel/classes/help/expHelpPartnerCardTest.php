<?php
/**
 * The "Become an Exponential Partner" block at the end of the help pages is shown as the card of the help dashboard's
 * footer blocks (doc/eng-us/features/6.0/help-system.md, "The partner card"): expHelpMarkdownRenderer recognizes it
 * between its markers and renders section.a4-help-partner with the bold lead as heading, the text, and the links as
 * buttons. No database, no settings needed.
 *
 *  PC-01  English: the marked block is a card; heading, text, two buttons with the links kept, the partner icon
 *  PC-02  German: the same, from the German block
 *  PC-03  other bold paragraphs, the block without the option, without its end marker or with two paragraphs stay
 *         paragraphs as written
 *  PC-04  the buttons follow the link policy (a refused target is text) and the card has no inline CSS
 *  PC-05  real pages: an English and a German help page render exactly one card, the viewer turns it on and the
 *         render cache key moved with it
 *  PC-06  design: the card's CSS is in stylesheets/modules/help.css of admin and admin4 (identical), only tokens
 *  PC-07  the PDF writer prints the card as a box with its title and its links
 *  PC-08  a German page shown to an English reader (/(lang)/ger-de) keeps its buttons: its links resolve in its own
 *         language's index; links that name no page leave the sentence as text
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpPartnerCardTest extends PHPUnit\Framework\TestCase
{
    const EN = "<!-- exp-partner-block:start -->\n"
        . "**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and\n"
        . "develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for\n"
        . "mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with\n"
        . "[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).\n"
        . "<!-- exp-partner-block:end -->\n";

    const DE = "<!-- exp-partner-block:start -->\n"
        . "**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und\n"
        . "entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027\n"
        . "geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit\n"
        . "[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).\n"
        . "<!-- exp-partner-block:end -->\n";

    private static function renderer( $card = true )
    {
        return new expHelpMarkdownRenderer( array(
            'partner_card' => $card,
            'link_resolver' => static function ( $href, $currentId ) {
                if ( preg_match( '#^[a-z][a-z0-9+.-]*:#i', (string)$href ) )
                    return null;
                $id = expHelpLinks::resolvePath( preg_replace( '/#.*$/', '', (string)$href ), $currentId );
                return $id === null ? null : '/admin/exphelp/viewer/' . $id;
            } ) );
    }

    /** @return DOMXPath */
    private static function xpath( $html )
    {
        $dom = new DOMDocument();
        $prev = libxml_use_internal_errors( true );
        $dom->loadHTML( '<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>', LIBXML_NONET );
        libxml_clear_errors();
        libxml_use_internal_errors( $prev );
        return new DOMXPath( $dom );
    }

    private static function cls( $name )
    {
        return "contains(concat(' ', normalize-space(@class), ' '), ' " . $name . " ')";
    }

    private static function text( DOMNode $node )
    {
        return trim( preg_replace( '/\s+/u', ' ', $node->textContent ) );
    }

    private function assertCard( $html, $heading, $textStart, $textEnd, array $buttons )
    {
        $x = self::xpath( $html );
        $cards = $x->query( '//section[' . self::cls( 'a4-help-partner' ) . ']' );
        $this->assertSame( 1, $cards->length, 'one card: ' . $html );
        $card = $cards->item( 0 );
        $this->assertSame( 1, $x->query( './span[' . self::cls( 'a4-help-tile' ) . '][@aria-hidden="true"]/span[' . self::cls( 'a4-icon' ) . ' and ' . self::cls( 'a4-bi-building' ) . ']', $card )->length, 'the partner icon on a tile' );
        $h2 = $x->query( './div[' . self::cls( 'a4-help-partner-main' ) . ']/h2[' . self::cls( 'a4-help-partner-title' ) . ']', $card );
        $this->assertSame( 1, $h2->length );
        $this->assertSame( $heading, self::text( $h2->item( 0 ) ) );
        $this->assertFalse( $h2->item( 0 )->hasAttribute( 'id' ), 'not a section of the page (table of contents, scroll tracking)' );
        $p = $x->query( './div[' . self::cls( 'a4-help-partner-main' ) . ']/p', $card );
        $this->assertSame( 1, $p->length );
        $text = self::text( $p->item( 0 ) );
        $this->assertStringStartsWith( $textStart, $text );
        $this->assertStringEndsWith( $textEnd, $text );
        $this->assertSame( 0, $x->query( './/a', $p->item( 0 ) )->length, 'the links are buttons, not in the text' );
        $links = $x->query( './div[' . self::cls( 'a4-help-partner-actions' ) . ']/a[' . self::cls( 'a4-btn' ) . ']', $card );
        $this->assertSame( count( $buttons ), $links->length );
        $n = 0;
        foreach ( $buttons as $label => $href )
        {
            $a = $links->item( $n );
            $this->assertSame( $label, self::text( $a ) );
            $this->assertSame( $href, $a->getAttribute( 'href' ) );
            $this->assertSame( $n === 0, strpos( ' ' . $a->getAttribute( 'class' ) . ' ', ' a4-btn-primary ' ) !== false, 'the first button is the primary one' );
            $this->assertSame( 1, $x->query( './span[' . self::cls( 'a4-icon' ) . '][@aria-hidden="true"]', $a )->length );
            $n++;
        }
        $this->assertSame( 0, $x->query( '//*[@style]' )->length, 'no inline CSS' );
        $this->assertDoesNotMatchRegularExpression( '/<style|<script|exp-partner-block/i', $html );
    }

    /** PC-01 */
    public function testEnglishBlockIsACard()
    {
        $r = self::renderer()->render( "# Page\n\nText.\n\n## Related\n\n- one\n\n" . self::EN, 'help/ai-agents/ai-agents-start-here.md' );
        $this->assertCard( $r['html'], 'Become an Exponential Partner',
            'Partners share their information, provide funding', 'land new features faster.',
            array( 'Partners: start here' => '/admin/exphelp/viewer/help/partners/partners-start-here.md',
                   'How to apply' => '/admin/exphelp/viewer/help/partners/how-to-apply.md' ) );
        $this->assertSame( array( 'Page', 'Related' ), array_column( $r['toc'], 'text' ), 'the card is not in the table of contents' );
    }

    /** PC-02 */
    public function testGermanBlockIsACard()
    {
        $r = self::renderer()->render( "# Seite\n\nText.\n\n" . self::DE, 'hilfe/ki-agenten/start.md' );
        $this->assertCard( $r['html'], 'Werden Sie Exponential-Partner',
            'Partner teilen ihre Informationen', 'neue Funktionen früher liefern.',
            array( 'Partner: hier beginnen' => '/admin/exphelp/viewer/hilfe/partner/partner-hier-beginnen.md',
                   'Wie Sie sich bewerben' => '/admin/exphelp/viewer/hilfe/partner/wie-sie-sich-bewerben.md' ) );
    }

    /** PC-03 */
    public function testEverythingElseStaysAsWritten()
    {
        $cases = array(
            'other bold paragraph' => array( true, "**Become an Exponential Partner.** Not marked, so a paragraph. Start with [A](a.md) and [B](b.md).\n" ),
            'a bold lead elsewhere' => array( true, "**Note.** Something. See [A](a.md).\n" ),
            'option off' => array( false, self::EN ),
            'no end marker' => array( true, str_replace( "<!-- exp-partner-block:end -->\n", '', self::EN ) . "\nMore text.\n" ),
            'two paragraphs' => array( true, str_replace( "faster. Start", "faster.\n\nStart", self::EN ) ),
            'no bold lead' => array( true, str_replace( '**Become an Exponential Partner.**', 'Become an Exponential Partner.', self::EN ) ),
        );
        foreach ( $cases as $name => $case )
        {
            $html = self::renderer( $case[0] )->render( $case[1], 'help/page.md' )['html'];
            $x = self::xpath( $html );
            $this->assertSame( 0, $x->query( '//section[' . self::cls( 'a4-help-partner' ) . ']' )->length, $name . ': ' . $html );
            $this->assertSame( 0, $x->query( '//*[' . self::cls( 'a4-btn' ) . ']' )->length, $name );
            $this->assertGreaterThan( 0, $x->query( '//p' )->length, $name );
            $this->assertStringNotContainsString( 'exp-partner-block', $html, $name . ': the markers are comments, never shown' );
        }
        // without the option the block is the paragraph it always was, bold lead and inline links
        $html = self::renderer( false )->render( self::EN, 'help/ai-agents/x.md' )['html'];
        $x = self::xpath( $html );
        $this->assertSame( 'Become an Exponential Partner.', self::text( $x->query( '//p/strong' )->item( 0 ) ) );
        $this->assertSame( 2, $x->query( '//p/a' )->length );
    }

    /** PC-04 */
    public function testButtonsFollowTheLinkPolicy()
    {
        $md = str_replace( '(../partners/how-to-apply.md)', '(javascript:alert(1))', self::EN );
        $html = self::renderer()->render( $md, 'help/ai-agents/x.md' )['html'];
        $x = self::xpath( $html );
        $this->assertSame( 1, $x->query( '//section[' . self::cls( 'a4-help-partner' ) . ']' )->length );
        $this->assertStringNotContainsString( 'javascript:', $html );
        // a link that leads nowhere: no buttons, the sentence stays in the text as written (its links as text)
        $this->assertSame( 0, $x->query( '//div[' . self::cls( 'a4-help-partner-actions' ) . ']' )->length );
        $text = self::text( $x->query( '//section//p' )->item( 0 ) );
        $this->assertStringEndsWith( 'Start with Partners: start here and How to apply.', $text );
        $this->assertSame( 1, $x->query( '//section//p/a' )->length, 'the link that resolves is still a link' );
        $this->assertSame( 0, $x->query( '//*[@style]' )->length );

        $md = str_replace( '(../partners/how-to-apply.md)', '(https://example.com/apply)', self::EN );
        $html = self::renderer()->render( $md, 'help/ai-agents/x.md' )['html'];
        $x = self::xpath( $html );
        $ext = $x->query( '//div[' . self::cls( 'a4-help-partner-actions' ) . ']/a[@href="https://example.com/apply"]' );
        $this->assertSame( 1, $ext->length );
        $this->assertStringContainsString( 'noopener', $ext->item( 0 )->getAttribute( 'rel' ) );

        // labels are escaped like every other text
        $md = str_replace( '[How to apply]', '[How <img src=x onerror=alert(1)> to apply]', self::EN );
        $html = self::renderer()->render( $md, 'help/ai-agents/x.md' )['html'];
        $this->assertDoesNotMatchRegularExpression( '/<img|onerror=/i', str_replace( '&lt;img', '', $html ) );
    }

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    /** PC-05 */
    public function testRealPagesAndTheViewer()
    {
        $root = self::root();
        $pages = array(
            'doc/eng-us/help/ai-agents/ai-agents-start-here.md' => 'Become an Exponential Partner',
            'doc/ger-de/installation/migration/wordpress.md' => 'Werden Sie Exponential-Partner',
        );
        foreach ( $pages as $file => $heading )
        {
            if ( !is_file( $root . '/' . $file ) )
                $this->markTestSkipped( $file . ' is not in this checkout' );
            $id = preg_replace( '#^doc/[a-z]{3}-[a-z]{2}/#', '', $file );
            $r = self::renderer()->render( file_get_contents( $root . '/' . $file ), $id );
            $x = self::xpath( $r['html'] );
            $cards = $x->query( '//section[' . self::cls( 'a4-help-partner' ) . ']' );
            $this->assertSame( 1, $cards->length, $file );
            $this->assertSame( $heading, self::text( $x->query( './/h2', $cards->item( 0 ) )->item( 0 ) ), $file );
            $this->assertSame( 2, $x->query( './/a[' . self::cls( 'a4-btn' ) . ']', $cards->item( 0 ) )->length, $file );
        }

        $viewer = file_get_contents( $root . '/kernel/private/classes/views/exphelp/viewer.php' );
        $this->assertStringContainsString( "'partner_card' => true", $viewer, 'the viewer shows the card' );
        $this->assertMatchesRegularExpression( '/const RENDER_VERSION = ([7-9]|[1-9][0-9]);/', $viewer, 'cached pages render again' );
    }

    /** PC-06 */
    public function testDesignRules()
    {
        $root = self::root();
        $css = array();
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $file = $root . '/design/' . $design . '/stylesheets/modules/help.css';
            $this->assertFileExists( $file );
            $css[$design] = file_get_contents( $file );
            $this->assertStringContainsString( '.a4-help-partner {', $css[$design], $design );
            $this->assertStringContainsString( '.a4-help-partner-actions', $css[$design], $design );
        }
        $this->assertSame( $css['admin4'], $css['admin'], 'design/admin and design/admin4 have the same help.css' );
        $start = strpos( $css['admin4'], '.a4-help-partner {' );
        $end = strpos( $css['admin4'], '/* ----', $start );
        $rules = substr( $css['admin4'], $start, $end === false ? 4000 : $end - $start );
        $this->assertDoesNotMatchRegularExpression( '/#[0-9a-fA-F]{3,8}\b|rgba?\(|hsla?\(/', $rules, 'colours only from tokens' );
        $this->assertDoesNotMatchRegularExpression( '/border-radius:\s*[0-9]/', $rules, 'radius only from tokens' );

        // the renderer writes no inline CSS, ever (the admin's Content Security Policy forbids it)
        $source = file_get_contents( $root . '/kernel/classes/help/exphelpmarkdownrenderer.php' );
        $this->assertDoesNotMatchRegularExpression( '/style=\\\\?["\']/', $source );
    }

    /** PC-08 */
    public function testGermanPageShownToAnotherReaderKeepsItsButtons()
    {
        $root = sys_get_temp_dir() . '/exphelp-pcard-' . bin2hex( random_bytes( 6 ) );
        $files = array(
            'doc/eng-us/help/designers/designers-start-here.md' => "# Designers\n\nText.\n",
            'doc/eng-us/help/partners/partners-start-here.md' => "# Partners\n",
            'doc/eng-us/help/partners/how-to-apply.md' => "# Apply\n",
            'doc/ger-de/hilfe/designer/designer-hier-beginnen.md' => "# Designer\n\nText.\n\n" . self::DE,
            'doc/ger-de/hilfe/partner/partner-hier-beginnen.md' => "# Partner\n",
            'doc/ger-de/hilfe/partner/wie-sie-sich-bewerben.md' => "# Bewerben\n",
            'doc/ger-de/.paths.json' => json_encode( array(
                'hilfe/designer/designer-hier-beginnen.md' => 'help/designers/designers-start-here.md',
                'hilfe/partner/partner-hier-beginnen.md' => 'help/partners/partners-start-here.md',
                'hilfe/partner/wie-sie-sich-bewerben.md' => 'help/partners/how-to-apply.md' ) ),
        );
        $tracked = array_keys( $files );
        sort( $tracked );
        foreach ( $files as $path => $content )
        {
            @mkdir( dirname( $root . '/' . $path ), 0777, true );
            file_put_contents( $root . '/' . $path, $content );
        }
        $languages = new expHelpLanguage( array( 'default' => 'eng-us', 'directories' => array( 'eng-us', 'ger-de' ),
                                                 'map' => array( 'eng-US' => 'eng-us', 'ger-DE' => 'ger-de' ),
                                                 'names' => array( 'eng-us' => 'English', 'ger-de' => 'German' ) ), $root );
        $index = function ( $language ) use ( $root, $tracked, $languages ) {
            $allowlist = new expHelpAllowlist( array( 'root' => $root, 'roots' => array( 'doc' ), 'tracked' => $tracked ) );
            return new expHelpDocumentIndex( $allowlist, array( 'cache_dir' => false, 'git_history' => false,
                                                                'languages' => $languages, 'language' => $language ) );
        };
        $english = $index( 'eng-us' );
        $german = $index( 'ger-de' );
        $id = 'help/designers/designers-start-here.md';
        $renderGerman = function () use ( $german, $id ) {
            $document = $german->document( $id );
            $renderer = new expHelpMarkdownRenderer( array( 'partner_card' => true, 'source_path' => $document['path'],
                'link_resolver' => static function ( $href, $current ) {
                    $target = expHelpLinks::resolveId( preg_replace( '/#.*$/', '', $href ), $current );
                    return $target === null || expHelpDocumentIndex::instance()->document( $target ) === null ? null : '/admin/exphelp/viewer/' . $target;
                } ) );
            return $renderer->render( $german->source( $id ), $id )['html'];
        };
        try
        {
            $this->assertSame( 'doc/ger-de/hilfe/designer/designer-hier-beginnen.md', $german->document( $id )['path'] );
            // an English reader's index resolves the German page's links against the English file: they name no page
            expHelpDocumentIndex::setInstance( $english );
            $x = self::xpath( $renderGerman() );
            $this->assertSame( 0, $x->query( '//a[' . self::cls( 'a4-btn' ) . ']' )->length, 'no buttons to nowhere' );
            $this->assertStringEndsWith( 'Beginnen Sie mit Partner: hier beginnen und Wie Sie sich bewerben.', self::text( $x->query( '//section//p' )->item( 0 ) ) );
            // the page's own language, as the viewer now renders it: the German buttons, to the pages they name
            expHelpDocumentIndex::setInstance( $german );
            $x = self::xpath( $renderGerman() );
            $buttons = $x->query( '//div[' . self::cls( 'a4-help-partner-actions' ) . ']/a[' . self::cls( 'a4-btn' ) . ']' );
            $this->assertSame( 2, $buttons->length );
            $this->assertSame( 'Partner: hier beginnen', self::text( $buttons->item( 0 ) ) );
            $this->assertSame( '/admin/exphelp/viewer/help/partners/partners-start-here.md', $buttons->item( 0 )->getAttribute( 'href' ) );
            $this->assertSame( 'Wie Sie sich bewerben', self::text( $buttons->item( 1 ) ) );
            $this->assertSame( '/admin/exphelp/viewer/help/partners/how-to-apply.md', $buttons->item( 1 )->getAttribute( 'href' ) );
            $this->assertSame( $german, expHelpDocumentIndex::fixedInstance() );
        }
        finally
        {
            expHelpDocumentIndex::setInstance( null );
            foreach ( array_reverse( $tracked ) as $path )
                @unlink( $root . '/' . $path );
            foreach ( array( 'doc/eng-us/help/designers', 'doc/eng-us/help/partners', 'doc/eng-us/help', 'doc/eng-us',
                             'doc/ger-de/hilfe/designer', 'doc/ger-de/hilfe/partner', 'doc/ger-de/hilfe', 'doc/ger-de', 'doc', '' ) as $dir )
                @rmdir( rtrim( $root . '/' . $dir, '/' ) );
        }
        $this->assertNull( expHelpDocumentIndex::fixedInstance() );

        // the viewer renders a page in its own language's index (/(lang)/ger-de for an English reader), then restores it
        $viewer = file_get_contents( self::root() . '/kernel/private/classes/views/exphelp/viewer.php' );
        $this->assertStringContainsString( '\expHelpDocumentIndex::setInstance( $index );', $viewer );
        $this->assertStringContainsString( '\expHelpDocumentIndex::setInstance( $fixed );', $viewer );
        $this->assertMatchesRegularExpression( '/const RENDER_VERSION = ([8-9]|[1-9][0-9]);/', $viewer );
    }

    /** PC-07 */
    public function testPdfPrintsTheCard()
    {
        $html = self::renderer()->render( "# Page\n\nText.\n\n" . self::EN, 'help/ai-agents/x.md' )['html'];
        $pdf = ( new expHelpPdfWriter( array( 'fonts' => array(), 'compress' => false ) ) )->render( array( 'title' => 'Page' ), $html );
        $this->assertStringStartsWith( '%PDF-', $pdf );
        foreach ( array( 'Become', 'Partners', 'faster.', 'apply' ) as $word )
            $this->assertStringContainsString( $word, $pdf );
    }
}
