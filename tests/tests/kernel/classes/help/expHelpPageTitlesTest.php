<?php
/**
 * Clearer titles and summaries for pages whose own are unclear (exphelp.ini [TitleSettings],
 * doc/eng-us/features/6.0/help-system.md, "Titles of unclear pages"), without a database.
 *
 *  HT-01  Titles[<path>] replaces the title a page gives itself; Summaries[<path>] its summary
 *  HT-02  TitleRules[] templates fill {title}, {name}, {fileversion} and {dirversion}; first match wins
 *  HT-03  a front matter title or summary always wins over the settings
 *  HT-04  a page without an entry keeps its own title; an empty entry or template result changes nothing
 *  HT-05  the shipped exphelp.ini gives every listed page a title and no title rule is malformed
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpPageTitlesTest extends PHPUnit\Framework\TestCase
{
    private static function audience()
    {
        return new expHelpAudience( array(
            'titles' => array( 'doc/eng-us/standards/header.txt' => 'Source file header template', 'doc/empty.txt' => '  ' ),
            'summaries' => array( 'doc/eng-us/standards/header.txt' => 'The old header.' ),
            'titleRules' => array(
                'doc/changelogs/*/CHANGELOG-desc-*;Release {fileversion} summary: {title}',
                'doc/eng-us/features/*/event.*;{title} ({dirversion})',
                'doc/eng-us/features/*/odd_*;{name}',
                'doc/blank/*;  ',
            ),
        ) );
    }

    private static function index()
    {
        $allowlist = new expHelpAllowlist( array( 'root' => sys_get_temp_dir(), 'roots' => array( 'doc' ) ) );
        return new expHelpDocumentIndex( $allowlist, array( 'cache_dir' => false, 'audience' => self::audience(), 'git_history' => false ) );
    }

    /** HT-01 */
    public function testExactTitleAndSummary()
    {
        $page = self::index()->describe( 'doc/eng-us/standards/header.txt', "//\n// Definition of classname class\n//\n" );
        $this->assertSame( 'Source file header template', $page['title'] );
        $this->assertSame( 'The old header.', $page['summary'] );
    }

    /** HT-02 */
    public function testRules()
    {
        $a = self::audience();
        $this->assertSame( 'Release 2.9-3 summary: Bug fixes', $a->pageTitle( 'doc/changelogs/2.9/CHANGELOG-desc-2.9-3', 'Bug fixes' ) );
        $this->assertSame( 'Release 3.1.0-1 summary: First release', $a->pageTitle( 'doc/changelogs/3.1/CHANGELOG-desc-3.1.0-1', 'First release.' ) );
        $this->assertSame( 'Event system (4.5)', $a->pageTitle( 'doc/eng-us/features/4.5/event.txt', 'Event system' ) );
        $this->assertSame( 'Odd name here', $a->pageTitle( 'doc/eng-us/features/3.9/odd_name_here.txt', 'x' ) );
    }

    /** HT-03 */
    public function testFrontMatterWins()
    {
        $page = self::index()->describe( 'doc/eng-us/standards/header.txt', "---\ntitle: Mine\nsummary: My summary.\n---\n# Heading\n", array( 'format' => 'markdown' ) );
        $this->assertSame( 'Mine', $page['title'] );
        $this->assertSame( 'My summary.', $page['summary'] );
    }

    /** HT-04 */
    public function testNoEntryKeepsOwnTitle()
    {
        $a = self::audience();
        $this->assertSame( 'Own', $a->pageTitle( 'doc/eng-us/guides/x.md', 'Own' ) );
        $this->assertSame( 'Own', $a->pageTitle( 'doc/empty.txt', 'Own' ) );
        $this->assertSame( 'Own', $a->pageTitle( 'doc/blank/a.txt', 'Own' ) );
        $this->assertSame( 'kept', $a->pageSummary( 'doc/eng-us/guides/x.md', 'kept' ) );
        $page = self::index()->describe( 'doc/eng-us/guides/x.md', "# A guide\n\nText.\n", array( 'format' => 'markdown' ) );
        $this->assertSame( 'A guide', $page['title'] );
    }

    /** HT-05 */
    public function testShippedSettings()
    {
        $file = __DIR__ . '/../../../../../settings/exphelp.ini';
        $text = (string)file_get_contents( $file );
        $this->assertStringContainsString( '[TitleSettings]', $text );
        preg_match_all( '/^Titles\[(doc\/[^\]]+)\]=(.*)$/m', $text, $m, PREG_SET_ORDER );
        $this->assertNotEmpty( $m );
        foreach ( $m as $entry )
        {
            $this->assertNotSame( '', trim( $entry[2] ), $entry[1] );
            $this->assertFileExists( __DIR__ . '/../../../../../' . $entry[1], 'a title for a file that does not exist' );
        }
        preg_match_all( '/^TitleRules\[\]=(.+)$/m', $text, $rules );
        foreach ( $rules[1] as $rule )
            $this->assertMatchesRegularExpression( '/^doc\/[^;]+;.*\S/', $rule );
    }
}
