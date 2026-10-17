<?php
/**
 * ezoe/embed_view, the preview of an embed the editors ask for: Exponential\View\Extension\Ezoe\Ezoe\EmbedView.
 *
 *  EV-01 - the view reads the EmbedID with Relations::parseEmbedId(), not with explode(): an EmbedID without "_"
 *          is the unknown embed, not an undefined array key
 *  EV-02 - a node that no longer exists is checked before ->object() is called, and an embed without an object is
 *          shown by the unknown branch (no _node template, no node)
 *  EV-03 - class, size, align and view are strings: an array from the request (class[]=x) gives the default
 *  EV-04 - the view is one of content.ini AvailableViewModes[] of [embed], [embed-inline] and the sections of the
 *          embedded object's class; anything else is "embed"
 *
 * No database: the rules are tested on the class, the order in the view on its source.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezoe/expOEEmbedViewTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 4 ) . '/extension/ezoe/classes/runnable/views/ezoe/relations.php';
require_once dirname( __DIR__, 4 ) . '/extension/ezoe/classes/runnable/views/ezoe/embed_view.php';

use Exponential\View\Extension\Ezoe\Ezoe\EmbedView;

/** content.ini as far as the view modes go */
class expOEEmbedViewTestIni
{
    private $values;

    public function __construct( array $values ) { $this->values = $values; }
    public function hasVariable( $section, $name ) { return isset( $this->values[$section][$name] ); }
    public function variable( $section, $name ) { return $this->values[$section][$name]; }
}

class expOEEmbedViewTest extends PHPUnit\Framework\TestCase
{
    const VIEW = 'extension/ezoe/classes/runnable/views/ezoe/embed_view.php';

    private function source()
    {
        return file_get_contents( dirname( __DIR__, 4 ) . '/' . self::VIEW );
    }

    /** EV-01 */
    public function testTheEmbedIdIsReadWithParseEmbedId()
    {
        $source = $this->source();
        $this->assertStringContainsString( "Relations::parseEmbedId( isset( \$Params['EmbedID'] ) ? \$Params['EmbedID'] : null )", $source );
        $this->assertStringNotContainsString( "explode('_', \$Params['EmbedID'])", $source );
        $this->assertStringNotContainsString( 'explode(', $source );
    }

    /** EV-02 */
    public function testAMissingNodeIsCheckedBeforeItsObjectIsRead()
    {
        $source = $this->source();
        $fetch = strpos( $source, '$embedNode = \eZContentObjectTreeNode::fetch( $embedId );' );
        $check = strpos( $source, 'if ( $embedNode instanceof \eZContentObjectTreeNode )' );
        $object = strpos( $source, '$embedObject = $embedNode->object();' );
        $this->assertNotFalse( $fetch );
        $this->assertNotFalse( $check );
        $this->assertNotFalse( $object );
        $this->assertLessThan( $check, $fetch );
        $this->assertLessThan( $object, $check );

        // without an object: no _node template, no node for the template, then the unknown branch
        $unknown = strpos( $source, "if ( !\$embedObject instanceof \\eZContentObject )\n        {\n            \$embedObject = false;\n            \$tplSuffix   = '';\n            unset( \$embedNode );" );
        $this->assertNotFalse( $unknown );
        $this->assertLessThan( strpos( $source, "\$objectName      = 'Unknown';" ), $unknown );
    }

    public static function notStrings()
    {
        return array( 'array' => array( array( 'x' ) ), 'nested array' => array( array( array( 'x' ) ) ), 'null' => array( null ),
                      'bool' => array( true ), 'object' => array( new stdClass() ) );
    }

    /** EV-03 */
    #[PHPUnit\Framework\Attributes\DataProvider( 'notStrings' )]
    public function testAValueThatIsNotAStringGivesTheDefault( $value )
    {
        $this->assertSame( 'medium', EmbedView::stringParameter( $value, 'medium' ) );
    }

    /** EV-03 */
    public function testStringsAndNumbersAreKept()
    {
        $this->assertSame( 'large', EmbedView::stringParameter( 'large', 'medium' ) );
        $this->assertSame( '', EmbedView::stringParameter( '', 'medium' ) );
        $this->assertSame( '3', EmbedView::stringParameter( 3, 'medium' ) );
        $source = $this->source();
        foreach ( array( "\$_GET['class']", "\$_GET['size']", "\$_GET['view']", "\$_GET['align']",
                         "\$http->postVariable('class')", "\$http->postVariable('size')", "\$http->postVariable('view')", "\$http->postVariable('align')" ) as $read )
            $this->assertStringContainsString( 'self::stringParameter( ' . $read . ',', $source, $read );
    }

    /** EV-04 */
    public function testTheViewIsAnAvailableViewModeOfTheTag()
    {
        $ini = new expOEEmbedViewTestIni( array(
            'embed' => array( 'AvailableViewModes' => array( 'embed', 'full', 'line' ) ),
            'embed-inline' => array( 'AvailableViewModes' => array( 'embed-inline', 'inline-small' ) ),
            'embed_image' => array( 'AvailableViewModes' => array( 'gallery' ) ),
        ) );
        $modes = EmbedView::availableViewModes( $ini, 'embed', false );
        $this->assertSame( array( 'embed', 'full', 'line' ), $modes );
        $this->assertSame( 'line', EmbedView::allowedView( 'line', $modes ) );
        foreach ( array( '../../../../settings/site.ini', 'gallery', 'Line', '', array( 'line' ), null ) as $view )
            $this->assertSame( 'embed', EmbedView::allowedView( $view, $modes ), var_export( $view, true ) );

        // the sections of the embedded object's class add their modes, for their tag only
        $this->assertSame( 'gallery', EmbedView::allowedView( 'gallery', EmbedView::availableViewModes( $ini, 'embed', 'image' ) ) );
        $this->assertSame( 'embed', EmbedView::allowedView( 'gallery', EmbedView::availableViewModes( $ini, 'embed', 'article' ) ) );
        $this->assertSame( 'embed-inline', EmbedView::allowedView( 'gallery', EmbedView::availableViewModes( $ini, 'embed-inline', 'image' ) ) );
    }

    /** EV-04: content.ini of the distribution, empty AvailableViewModes[] for both tags */
    public function testAnInlineEmbedKeepsItsViewWithEmptyLists()
    {
        $ini = new expOEEmbedViewTestIni( array( 'embed' => array( 'AvailableViewModes' => array() ),
                                                 'embed-inline' => array( 'AvailableViewModes' => array() ) ) );
        $inline = EmbedView::availableViewModes( $ini, 'embed-inline', false );
        $this->assertSame( array( 'embed-inline' ), $inline );
        $this->assertSame( 'embed-inline', EmbedView::allowedView( 'embed-inline', $inline ), 'what the TinyMCE 8 plugin sends' );
        $this->assertSame( 'embed-inline', EmbedView::allowedView( null, $inline ), 'no view: the tag\'s own' );
        $this->assertSame( 'embed', EmbedView::allowedView( 'embed', EmbedView::availableViewModes( $ini, 'embed', false ) ) );
        $this->assertSame( 'embed-inline', EmbedView::allowedView( 'embed', $inline ), 'an inline embed cannot ask for the block view' );
    }

    /** EV-04: a block embed cannot ask for a view of the inline lists */
    public function testABlockEmbedCannotRequestAnInlineOnlyView()
    {
        $ini = new expOEEmbedViewTestIni( array( 'embed' => array( 'AvailableViewModes' => array( 'embed', 'line' ) ),
                                                 'embed-inline' => array( 'AvailableViewModes' => array( 'embed-inline', 'inline-small' ) ),
                                                 'embed-inline_image' => array( 'AvailableViewModes' => array( 'icon' ) ) ) );
        $block = EmbedView::availableViewModes( $ini, 'embed', 'image' );
        $this->assertSame( 'embed', EmbedView::allowedView( 'embed-inline', $block ) );
        $this->assertSame( 'embed', EmbedView::allowedView( 'inline-small', $block ) );
        $this->assertSame( 'embed', EmbedView::allowedView( 'icon', $block ) );
        $this->assertSame( 'icon', EmbedView::allowedView( 'icon', EmbedView::availableViewModes( $ini, 'embed-inline', 'image' ) ) );
    }

    /** EV-04: the view passes the tag, after the inline flag is read and before the template gets the view */
    public function testTheViewPassesTheTagName()
    {
        $source = $this->source();
        $allowed = strpos( $source, "\$view = self::allowedView( \$view, self::availableViewModes( \\eZINI::instance( 'content.ini' ), \$tagName, \$classIdentifier ) );" );
        $this->assertNotFalse( $allowed );
        $this->assertLessThan( $allowed, strpos( $source, "\$tagName = 'embed-inline';" ) );
        $this->assertLessThan( strpos( $source, "\$tpl->setVariable( 'view', \$view );" ), $allowed );
    }
}
