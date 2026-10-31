<?php
/**
 * expContentFieldMarker: the markers around a rendered attribute ({attribute_view_gui}) for editing on the page, only
 * when a handler of site.ini [ContentFieldMarkerSettings] Handlers[] says so; nothing otherwise. DB-less.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expContentFieldMarkerTest extends PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        expContentFieldMarker::reset();
    }

    public function testOpeningMarkerCarriesFieldObjectVersionAttributeAndLanguage()
    {
        $this->assertSame( '<!--exp-field data-exp-field="title" data-exp-object="61" data-exp-version="7" data-exp-attribute="402" data-exp-language="eng-US"-->',
                           expContentFieldMarker::open( array( 'field' => 'title', 'object' => 61, 'version' => 7, 'attribute' => 402, 'language' => 'eng-US' ) ) );
        $this->assertSame( '<!--/exp-field-->', expContentFieldMarker::close() );
    }

    public function testNothingCanEndTheCommentEarly()
    {
        $open = expContentFieldMarker::open( array( 'field' => 'a-->b"<x>', 'object' => '5--><script>', 'language' => 'ger"-->DE' ) );
        $this->assertSame( 1, substr_count( $open, '-->' ), 'the only end is its own' );
        $this->assertStringEndsWith( '-->', $open );
        $this->assertStringNotContainsString( '<script', $open );
        $this->assertStringNotContainsString( '<x>', $open );
        $this->assertStringContainsString( 'data-exp-object="5"', $open );
    }

    public function testNothingIsEmittedForSomethingElseThanAnAttribute()
    {
        expContentFieldMarker::reset( true );
        $this->assertSame( array( '', '' ), expContentFieldMarker::pair( null ) );
        $this->assertSame( array( '', '' ), expContentFieldMarker::pair( new stdClass() ) );
    }

    public function testTheAnswerIsKeptPerUserAndSiteaccess()
    {
        expContentFieldMarker::reset( true );
        $this->assertTrue( expContentFieldMarker::active() );
        expContentFieldMarker::reset( false );
        $this->assertFalse( expContentFieldMarker::active() );
    }

    public function testTheObjectForwarderWrapsAttributeViewGuiOnly()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/kernel/common/ezobjectforwarder.php' );
        $this->assertSame( 2, substr_count( $source, "\$functionName === 'attribute_view_gui'" ), 'compiled and interpreted templates' );
        $this->assertStringContainsString( 'expContentFieldMarker::pair( $input_var )', $source );
        $this->assertStringContainsString( 'expContentFieldMarker::pair( \$expFieldMarkerAttribute )', $source );
    }
}
