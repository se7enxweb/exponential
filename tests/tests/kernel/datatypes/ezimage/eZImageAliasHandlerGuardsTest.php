<?php
/**
 * Two guards of eZImageAliasHandler, testable without a database.
 *
 * storedXMLSupersedesDOMTree(): addImageAliases() runs during page renders and writes the
 * whole in-memory <ezimage> XML back. When the row was rewritten meanwhile (publishing resets
 * serial_number and moves dirpath out of images-versioned/), that write reverted the published
 * image to stale draft paths. The write is skipped when the stored root differs in either
 * attribute; an empty, unparseable or foreign stored value never blocks it.
 *
 * imageAlias(): an image attribute that never stored a file (url="") still has an "original"
 * entry, and imageAlias() asked the image manager to create the alias on every request, which
 * logged an error per alias. It now returns null without calling the image manager.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/datatypes/ezimage/eZImageAliasHandlerGuardsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/**
 * An alias handler with a fixed alias list and no content object attribute behind it.
 */
class eZImageAliasHandlerGuardsTestHandler extends eZImageAliasHandler
{
    /** @var array the alias list aliasList() returns */
    public $fixedAliasList = array();

    public function __construct( $aliasList )
    {
        $this->ContentObjectAttributeData = array( 'id' => 0, 'contentobject_id' => 0, 'version' => 0 );
        $this->fixedAliasList = $aliasList;
    }

    function aliasList( $checkValidity = true )
    {
        return $this->fixedAliasList;
    }
}

class eZImageAliasHandlerGuardsTest extends PHPUnit\Framework\TestCase
{
    /**
     * Returns an in-memory document as addImageAliases() holds it.
     *
     * @param string $xml
     * @return DOMDocument
     */
    protected static function domTree( $xml )
    {
        $domTree = new DOMDocument( '1.0', 'utf-8' );
        $domTree->loadXML( $xml );
        return $domTree;
    }

    /**
     * @return array stored data_text, in-memory XML, expected result
     */
    public static function supersedesProvider()
    {
        $memory = '<ezimage serial_number="1" dirpath="var/site/storage/images-versioned/57/2-eng-GB" basename="photo"/>';
        return array(
            'empty stored value never supersedes' =>
                array( '', $memory, false ),
            'same row, aliases added in memory' =>
                array( '<ezimage serial_number="1" dirpath="var/site/storage/images-versioned/57/2-eng-GB" basename="photo"><alias name="small"/></ezimage>', $memory, false ),
            'dirpath moved out of images-versioned by publishing' =>
                array( '<ezimage serial_number="1" dirpath="var/site/storage/images/media/photo/57-2-eng-GB" basename="photo"/>', $memory, true ),
            'serial_number reset by publishing' =>
                array( '<ezimage serial_number="2" dirpath="var/site/storage/images-versioned/57/2-eng-GB" basename="photo"/>', $memory, true ),
            'published row with an empty serial_number and a moved dirpath' =>
                array( '<?xml version="1.0" encoding="utf-8"?><ezimage serial_number="" is_valid="1" dirpath="var/site/storage/images/57/2-eng-GB"><original attribute_id="1"/></ezimage>', $memory, true ),
            'stored document with another root element' =>
                array( '<?xml version="1.0"?><a/>', $memory, false ),
            'unparseable stored value never supersedes' =>
                array( '<ezimage serial_number="1"', $memory, false ),
            'foreign stored root never supersedes' =>
                array( '<image serial_number="2" dirpath="elsewhere"/>', $memory, false ),
            'in-memory document without an ezimage root never is superseded' =>
                array( '<ezimage serial_number="2" dirpath="elsewhere"/>', '<image serial_number="1" dirpath="here"/>', false ),
        );
    }

    /**
     * @dataProvider supersedesProvider
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'supersedesProvider' )]
    public function testStoredXMLSupersedesDOMTree( $storedXML, $memoryXML, $expected )
    {
        $this->assertSame( $expected, eZImageAliasHandler::storedXMLSupersedesDOMTree( $storedXML, self::domTree( $memoryXML ) ) );
    }

    /**
     * Anything that is not a DOMDocument never is superseded.
     */
    public function testNoDOMDocumentNeverSuperseded()
    {
        $this->assertFalse( eZImageAliasHandler::storedXMLSupersedesDOMTree( '<ezimage serial_number="2" dirpath="x"/>', false ) );
        $this->assertFalse( eZImageAliasHandler::storedXMLSupersedesDOMTree( '<ezimage serial_number="2" dirpath="x"/>', null ) );
    }

    /**
     * An original that never stored a file gives no alias, and the alias list is left as it was.
     */
    public function testImageAliasForOriginalWithoutFileIsNull()
    {
        $imageManager = eZImageManager::factory();
        if ( !$imageManager->hasAlias( 'small' ) )
        {
            $this->markTestSkipped( 'No "small" image alias in image.ini' );
        }

        $aliasList = array( 'original' => array( 'url' => '', 'basename' => '', 'alternative_text' => '',
                                                 'original_filename' => '', 'is_new' => false ) );
        $handler = new eZImageAliasHandlerGuardsTestHandler( $aliasList );

        $this->assertNull( $handler->imageAlias( 'small' ) );
        $this->assertSame( array( 'original' ), array_keys( $handler->fixedAliasList ) );
    }

    /**
     * An original whose url is missing altogether is handled like an empty one.
     */
    public function testImageAliasForOriginalWithoutUrlKeyIsNull()
    {
        $imageManager = eZImageManager::factory();
        if ( !$imageManager->hasAlias( 'small' ) )
        {
            $this->markTestSkipped( 'No "small" image alias in image.ini' );
        }

        $handler = new eZImageAliasHandlerGuardsTestHandler( array( 'original' => array( 'basename' => '' ) ) );
        $this->assertNull( $handler->imageAlias( 'small' ) );
    }

    /**
     * An attribute without any alias list (XML without an <ezimage> element) gives no alias.
     */
    public function testImageAliasWithoutOriginalIsNull()
    {
        $imageManager = eZImageManager::factory();
        if ( !$imageManager->hasAlias( 'small' ) )
        {
            $this->markTestSkipped( 'No "small" image alias in image.ini' );
        }

        $handler = new eZImageAliasHandlerGuardsTestHandler( array() );
        $this->assertNull( $handler->imageAlias( 'small' ) );
    }

    /**
     * An alias that already exists is returned as it is, also for an original without a file.
     */
    public function testExistingAliasIsReturned()
    {
        $imageManager = eZImageManager::factory();
        if ( !$imageManager->hasAlias( 'small' ) )
        {
            $this->markTestSkipped( 'No "small" image alias in image.ini' );
        }

        $small = array( 'url' => 'var/site/storage/images/photo_small.png', 'name' => 'small' );
        $handler = new eZImageAliasHandlerGuardsTestHandler( array( 'original' => array( 'url' => '' ), 'small' => $small ) );
        $this->assertSame( $small, $handler->imageAlias( 'small' ) );
    }

    /**
     * An alias name image.ini does not define gives null before the alias list is read.
     */
    public function testUnknownAliasIsNull()
    {
        $handler = new eZImageAliasHandlerGuardsTestHandler( array( 'original' => array( 'url' => 'var/x.png' ) ) );
        $this->assertNull( $handler->imageAlias( 'no_such_alias_' . uniqid() ) );
    }
}
