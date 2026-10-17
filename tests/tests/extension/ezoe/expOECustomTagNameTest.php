<?php
/**
 * The name of the custom tag in the address of the tag dialog, ezoe/tags/<id>/<version>/custom/<name>.
 *
 *  CN-01 - only no name (a new tag) or a name of content.ini [CustomTagSettings] AvailableCustomTags[] is accepted
 *  CN-02 - the view checks the name against AvailableCustomTags[] before the template is fetched, and the messages
 *          that repeat a value from the address escape it
 *  CN-03 - tag_custom.tpl escapes the name in the script string and in the class attribute anyway: a name with a
 *          quote does not end the string, and does not end the attribute
 *
 * The template lines are rendered as written by eZTemplate::factory(). No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezoe/expOECustomTagNameTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 4 ) . '/extension/ezoe/classes/runnable/views/ezoe/tags.php';

use Exponential\View\Extension\Ezoe\Ezoe\Tags;

class expOECustomTagNameTest extends PHPUnit\Framework\TestCase
{
    const VIEW = 'extension/ezoe/classes/runnable/views/ezoe/tags.php';
    const TEMPLATE = 'extension/ezoe/design/standard/templates/ezoe/tag_custom.tpl';
    const ATTACK = "x';alert(document.cookie);//\"><script>alert(1)</script>";

    /** @var string */
    private $dir;

    private $savedGlobals = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/phpunit-ezoe-customtag-name-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir, 0700, true );
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
        $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
    }

    protected function tearDown(): void
    {
        foreach ( $this->savedGlobals as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
        foreach ( (array)glob( $this->dir . '/*' ) as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    /** CN-01 */
    public function testOnlyNoNameOrAnAvailableCustomTagIsAccepted()
    {
        $available = array( 'factbox', 'quote', 'underline' );
        $this->assertTrue( Tags::customTagNameAllowed( '', $available ), 'a new tag' );
        $this->assertTrue( Tags::customTagNameAllowed( 'factbox', $available ) );
        $this->assertTrue( Tags::customTagNameAllowed( 'underline', $available ) );
        foreach ( array( self::ATTACK, 'Factbox', 'factbox ', 'sub', '0', null, array( 'factbox' ), 1 ) as $name )
            $this->assertFalse( Tags::customTagNameAllowed( $name, $available ), var_export( $name, true ) );
        $this->assertFalse( Tags::customTagNameAllowed( 'factbox', array() ), 'no custom tags, no name' );
    }

    /** CN-02 */
    public function testTheViewChecksTheNameBeforeTheTemplateAndEscapesItsMessages()
    {
        $source = file_get_contents( self::VIEW );
        $check = strpos( $source, 'self::customTagNameAllowed( $customTagName, $availableCustomTags )' );
        $fetch = strpos( $source, "\$tpl->fetch( 'design:ezoe/' . \$templateName )" );
        $this->assertNotFalse( $check );
        $this->assertNotFalse( $fetch );
        $this->assertLessThan( $fetch, $check );
        $this->assertStringContainsString( "'%value' => htmlspecialchars( \$customTagName, ENT_QUOTES, 'UTF-8' )", $source );
        $this->assertStringContainsString( "'%value' => htmlspecialchars( \$tagName, ENT_QUOTES, 'UTF-8' )", $source );
        $this->assertStringNotContainsString( "'%value' => \$tagName )", $source );
    }

    /** The lines of tag_custom.tpl that print the name, rendered with $name */
    private function render( $name )
    {
        $lines = array();
        foreach ( file( self::TEMPLATE ) as $line )
        {
            if ( strpos( $line, 'customTagName = ' ) !== false || strpos( $line, 'custom-tag-type-' ) !== false )
                $lines[] = $line;
        }
        $this->assertCount( 2, $lines, 'the script line and the class attribute are in tag_custom.tpl' );
        $file = $this->dir . '/name.tpl';
        // the image of the script line needs a design; it is not what is tested here
        file_put_contents( $file, preg_replace( '/\{"[^"]+"\|ezimage\}/', '""', implode( '', $lines ) ) );
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'tag_name', 'custom' );
        $tpl->setVariable( 'custom_tag_name', $name );
        try
        {
            return $tpl->fetch( $file );
        }
        finally
        {
            foreach ( array( 'tag_name', 'custom_tag_name' ) as $variable )
                $tpl->unsetVariable( $variable );
        }
    }

    /** CN-03 */
    public function testTheTemplateEscapesTheNameAnyway()
    {
        $html = $this->render( self::ATTACK );
        $this->assertStringNotContainsString( "customTagName = 'x';", $html, 'the quote does not end the script string' );
        $this->assertStringNotContainsString( '<script>alert(1)', $html );
        $this->assertSame( 1, preg_match( "/customTagName = '((?:[^'\\\\]|\\\\.)*)'/", $html, $m ), $html );
        $this->assertSame( 1, preg_match( '/<div class="([^"]*)">/', $html, $c ), $html );
        $this->assertStringContainsString( 'custom-tag-type-x&#039;;alert', $c[1] );

        $plain = $this->render( 'factbox' );
        $this->assertStringContainsString( "customTagName = 'factbox'", $plain );
        $this->assertStringContainsString( '<div class="tag-view tag-type-custom custom-tag-type-factbox">', $plain );
    }
}
