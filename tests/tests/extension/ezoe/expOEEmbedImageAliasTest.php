<?php
/**
 * The embed preview of an image in the online editor reads the image alias through
 * eZOEXMLInput::embedImageAliasPreview(), without the database:
 *
 *  EA-01 - A complete alias gives its url, width and height
 *  EA-02 - An alias without width or height (created in part) takes the defaults, no "Undefined array key"
 *  EA-03 - An alias without url (its original is missing from the storage) gives null: the attachment icon stands in
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expOEEmbedImageAliasTest extends PHPUnit\Framework\TestCase
{
    public function testCompleteAliasGivesItsUrlAndSize()
    {
        $this->assertSame( array( 'url' => 'var/storage/images/a/b_medium.jpg', 'width' => 200, 'height' => 150 ),
                           eZOEXMLInput::embedImageAliasPreview( array( 'url' => 'var/storage/images/a/b_medium.jpg', 'width' => 200, 'height' => 150 ) ) );
    }

    public function testMissingWidthOrHeightTakesTheDefaults()
    {
        $this->assertSame( array( 'url' => 'x.jpg', 'width' => 200, 'height' => 32 ),
                           eZOEXMLInput::embedImageAliasPreview( array( 'url' => 'x.jpg', 'width' => 200 ) ) );
        $this->assertSame( array( 'url' => 'x.jpg', 'width' => 64, 'height' => 48 ),
                           eZOEXMLInput::embedImageAliasPreview( array( 'url' => 'x.jpg' ), 64, 48 ) );
    }

    public function testAliasWithoutUrlGivesNull()
    {
        $this->assertNull( eZOEXMLInput::embedImageAliasPreview( array( 'width' => 200, 'height' => 150 ) ) );
        $this->assertNull( eZOEXMLInput::embedImageAliasPreview( array( 'url' => '' ) ) );
        $this->assertNull( eZOEXMLInput::embedImageAliasPreview( false ) );
        $this->assertNull( eZOEXMLInput::embedImageAliasPreview( null ) );
    }
}
