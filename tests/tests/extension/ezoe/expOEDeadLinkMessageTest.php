<?php
/**
 * A link whose target is gone gets a message that says what the target was and how to repair the link, and the link
 * messages of the online editor are escaped for the validation log, without the database:
 * eZOEInputParser::deadLinkMessage(), linkLabel(), messageText() and eZOEInputParser::process().
 *
 *  DL-01 - A target that is not in the trash either: the message says so and names the link
 *  DL-02 - A target in the trash: name, class, old place and date; without a date in the trash row no 1970
 *  DL-03 - A target in the trash that the user may not read: no name, class or place
 *  DL-04 - Only a link by location id says that restoring the target does not repair it
 *  DL-05 - < and > of the link text, the name, the place and the address are escaped once, nothing else is
 *  DL-06 - The link text is shortened to 70 characters; a link without text is named by its address
 *  DL-07 - A script address in a link is escaped in its message
 *  DL-08 - Every message of deadLinkMessage() has a German translation
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expOEDeadLinkMessageTest extends PHPUnit\Framework\TestCase
{
    const TRASHED = array( 'name' => 'Annual report', 'class' => 'Article', 'trashed' => 1760000000,
                           'path' => '/news/annual_report' );

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        if ( !class_exists( 'eZOEInputParser' ) )
            $this->markTestSkipped( 'ezoe is not loaded' );
    }

    public function testATargetThatIsGoneForGoodIsNamedAsSuch()
    {
        $message = eZOEInputParser::deadLinkMessage( 'Report', 'eznode://4310', null, true );
        $this->assertStringContainsString( '"Report"', $message );
        $this->assertStringContainsString( 'eznode://4310', $message );
        $this->assertStringContainsString( 'not even in the trash', $message );
        $this->assertStringNotContainsString( 'Restoring', $message, 'nothing to restore' );
    }

    public function testATrashedTargetIsDescribed()
    {
        $message = eZOEInputParser::deadLinkMessage( 'Report', 'eznode://4310', self::TRASHED, true );
        foreach ( array( 'Annual report', 'Article', '/news/annual_report', 'eznode://4310',
                         eZLocale::instance()->formatShortDateTime( 1760000000 ) ) as $part )
        {
            $this->assertStringContainsString( $part, $message );
        }

        $withoutDate = eZOEInputParser::deadLinkMessage( 'Report', 'eznode://4310', array( 'trashed' => 0 ) + self::TRASHED, true );
        $this->assertStringContainsString( 'was moved to the trash.', $withoutDate );
        $this->assertStringNotContainsString( '1970', $withoutDate );
        $this->assertStringNotContainsString( eZLocale::instance()->formatShortDateTime( 0 ), $withoutDate );
    }

    public function testATrashedTargetTheUserMayNotReadIsNotDescribed()
    {
        $message = eZOEInputParser::deadLinkMessage( 'Report', 'eznode://4310', array(), true );
        $this->assertStringContainsString( 'is in the trash', $message );
        $this->assertStringNotContainsString( 'located at', $message );
    }

    public function testOnlyALinkByLocationIdSaysThatRestoringDoesNotRepairIt()
    {
        $this->assertStringContainsString( 'Restoring the target',
            eZOEInputParser::deadLinkMessage( 'Report', 'eznode://4310', self::TRASHED, true ) );
        $this->assertStringNotContainsString( 'Restoring the target',
            eZOEInputParser::deadLinkMessage( 'Report', 'eznode://news/annual_report', self::TRASHED, false ) );
    }

    public function testValuesFromEditorsAreEscapedOnce()
    {
        $evil = '<img src=x onerror=alert(1)> & "q"';
        $message = eZOEInputParser::deadLinkMessage( $evil, 'eznode://<b>', array( 'name' => $evil, 'class' => '<i>',
                                                     'trashed' => 0, 'path' => '/<s>' ), true );
        $this->assertStringNotContainsString( '<', $message );
        $this->assertStringNotContainsString( '>', $message );
        $this->assertStringContainsString( '&lt;img src=x onerror=alert(1)&gt; & "q"', $message );
        $this->assertStringNotContainsString( '&amp;', $message, 'design/admin4 washes the message: & stays as it is' );
        $this->assertStringContainsString( 'eznode://&lt;b&gt;', $message );
        $this->assertStringContainsString( '/&lt;s&gt;', $message );
    }

    public function testTheLinkTextNamesTheLink()
    {
        $doc = new DOMDocument();
        $link = $doc->createElement( 'link' );
        $this->assertSame( 'eznode://42', eZOEInputParser::linkLabel( $link, 'eznode://42' ) );

        $link->appendChild( $doc->createTextNode( '  ' . str_repeat( 'ä', 80 ) . ' ' ) );
        $label = eZOEInputParser::linkLabel( $link, 'eznode://42' );
        $this->assertSame( 70, mb_strlen( $label, 'UTF-8' ) );
        $this->assertStringEndsWith( '...', $label );
    }

    /**
     * @return string[] the messages the parser gives for $html
     */
    private function messages( $html )
    {
        $parser = new eZOEInputParser();
        $parser->process( '<p>' . $html . '</p>' );
        return $parser->getMessages();
    }

    public function testAScriptAddressIsEscapedInItsMessage()
    {
        $script = implode( "\n", $this->messages( '<a href="javascript:x(&lt;img src=x onerror=alert(1)&gt;)">s</a>' ) );
        $this->assertStringContainsString( 'Using scripts in links is not allowed', $script );
        $this->assertStringContainsString( '&lt;img src=x onerror=alert(1)&gt;', $script );
        $this->assertStringNotContainsString( '<img', $script );
    }

    public function testEveryMessageHasAGermanTranslation()
    {
        preg_match_all( "/'(The link \"%1\"[^']*|Restoring the target[^']*)'/",
                        file_get_contents( 'extension/ezoe/ezxmltext/handlers/input/ezoeinputparser.php' ), $sources );
        $this->assertCount( 5, $sources[1] );
        $ts = new DOMDocument();
        $ts->load( 'extension/ezoe/translations/ger-DE/translation.ts' );
        $translations = array();
        foreach ( ( new DOMXPath( $ts ) )->query( "//context[name='design/standard/ezoe/handler']/message" ) as $message )
        {
            $translations[$message->getElementsByTagName( 'source' )->item( 0 )->textContent] =
                $message->getElementsByTagName( 'translation' )->item( 0 )->textContent;
        }
        foreach ( $sources[1] as $source )
        {
            $this->assertNotEmpty( $translations[$source] ?? '', "no German translation: $source" );
        }
    }
}
