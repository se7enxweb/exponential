<?php
/**
 * The autolink template operator (eZAutoLinkOperator) without preg_replace()'s /e modifier: plain http, https and
 * ftp addresses and a mail address in running text, each in parentheses as in the original cases, with the link
 * text shortened to max_chars where it is longer.
 *
 *  AL-01 - an http address becomes a link with the address as title and text; the text around it stays
 *  AL-02 - the same for https
 *  AL-03 - a mail address becomes a mailto: link
 *  AL-04 - an ftp address with a path and a file name (template.ini [AutoLinkOperator] Methods[] lists ftp)
 *  AL-05 - a link text longer than max_chars is shortened, the href and title keep the whole address
 *  AL-06 - a scheme that Methods[] does not list is left as text
 *
 * The cases come from LoveStack's test of the change that removed the /e modifier (the same change reached
 * Exponential from the upstream line as 82617a0a97, EZP-27092). The settings the operator reads are set in memory
 * and put back. No database.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit d894b9e885), Copyright (C) Mugo Web, GPL v2
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/common/eZAutoLinkOperatorLinksTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group templateoperators
 */

class eZAutoLinkOperatorLinksTestTpl
{
    public function ini()
    {
        return eZINI::instance( 'template.ini' );
    }

    public function missingParameter( $operatorName, $parameter )
    {
        return false;
    }
}

class eZAutoLinkOperatorLinksTest extends PHPUnit\Framework\TestCase
{
    /** @var array[] array( group, name, had, value ) of template.ini, put back in tearDown() */
    private $saved = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->setting( 'AutoLinkOperator', 'MaxCharacters', '72' );
        $this->setting( 'AutoLinkOperator', 'Methods', array( 'http', 'https', 'ftp' ) );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'template.ini' );
        foreach ( array_reverse( $this->saved ) as $entry )
        {
            list( $group, $name, $had, $value ) = $entry;
            if ( $had )
                $ini->setVariable( $group, $name, $value );
            else
                $ini->removeSetting( $group, $name );
        }
        $this->saved = array();
    }

    private function setting( $group, $name, $value )
    {
        $ini = eZINI::instance( 'template.ini' );
        $had = $ini->hasVariable( $group, $name );
        $this->saved[] = array( $group, $name, $had, $had ? $ini->variable( $group, $name ) : null );
        $ini->setVariable( $group, $name, $value );
    }

    private function autolink( $text, $maxChars )
    {
        $operator = new eZAutoLinkOperator();
        $operator->modify( new eZAutoLinkOperatorLinksTestTpl(), 'autolink', array(), '', '', $text, array( 'max_chars' => $maxChars ), false );
        return $text;
    }

    public static function linkProvider()
    {
        return array(
            'AL-01 http' => array( 32, 'Heath ( http://example.com/p/10979 )',
                'Heath ( <a href="http://example.com/p/10979" title="http://example.com/p/10979">http://example.com/p/10979</a> )' ),
            'AL-02 https' => array( 32, 'Heath ( https://example.com/p/10979 )',
                'Heath ( <a href="https://example.com/p/10979" title="https://example.com/p/10979">https://example.com/p/10979</a> )' ),
            'AL-03 mail' => array( 72, 'Heath ( heath@example.com )',
                "Heath ( <a href='mailto:heath@example.com'>heath@example.com</a> )" ),
            'AL-04 ftp' => array( 72, 'Heath ( ftp://example.com/pub/mockbinaryfile.tar.gz )',
                'Heath ( <a href="ftp://example.com/pub/mockbinaryfile.tar.gz" title="ftp://example.com/pub/mockbinaryfile.tar.gz">ftp://example.com/pub/mockbinaryfile.tar.gz</a> )' ),
        );
    }

    /** AL-01 to AL-04 */
    #[\PHPUnit\Framework\Attributes\DataProvider('linkProvider')]
    public function testAddressesBecomeLinks( $maxChars, $text, $expected )
    {
        $this->assertSame( $expected, $this->autolink( $text, $maxChars ) );
    }

    /** AL-05 */
    public function testLongLinkTextIsShortened()
    {
        $url = 'ftp://example.com/pub/mockbinaryfile.tar.gz';
        $html = $this->autolink( "Heath ( $url )", 20 );
        $this->assertStringContainsString( 'href="' . $url . '"', $html );
        $this->assertStringContainsString( 'title="' . $url . '"', $html );
        $this->assertSame( 1, preg_match( '#>([^<]*)</a>#', $html, $m ) );
        $this->assertNotSame( $url, $m[1] );
        $this->assertLessThanOrEqual( 20, strlen( $m[1] ) );
        $this->assertStringStartsWith( 'Heath ( <a ', $html );
        $this->assertStringEndsWith( '</a> )', $html );
    }

    /** AL-06 */
    public function testUnlistedSchemeStaysText()
    {
        $this->setting( 'AutoLinkOperator', 'Methods', array( 'http', 'https' ) );
        $this->assertSame( 'Heath ( ftp://example.com/pub/x.tar.gz )', $this->autolink( 'Heath ( ftp://example.com/pub/x.tar.gz )', 72 ) );
    }
}
