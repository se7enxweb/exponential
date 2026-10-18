<?php
/**
 * The project-manager and sales-marketing audiences of the help and their pages (doc/help/leadership/,
 * doc/help/business/; doc/features/6.0/help-system.md). No database.
 *
 *  LA-01  expHelpAudience knows both audiences and both categories by default, with icons that admin4 and the
 *         classic admin draw (an .a4-bi-<icon> class in components.css)
 *  LA-02  settings/exphelp.ini lists them: Audiences[], names, icons, the categories and the folder path rules
 *  LA-03  the classifier: a page in doc/help/leadership/ is for project managers in the leadership category, one in
 *         doc/help/business/ for sales & marketing in the business category; front matter and aliases win
 *  LA-04  the search counts and filters the new audiences (facets) and the search view accepts them
 *  LA-05  the dashboard of both designs (the same file) shows six audience cards, the two new ones with a start page
 *         that exists; help.css colors them; no inline CSS or script
 *  LA-06  the search page and the viewer's pills name both audiences in both designs
 *  LA-07  every new string is in the eleven complete catalogues under kernel/exphelp
 *  LA-08  the pages: front matter (title = first heading, the folder's audience and category, type help), the start
 *         pages and the reading path are featured, every relative link leads to an existing file and every #anchor
 *         to a heading, external links use https, no internal host names
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/help/expHelpLeadershipAudiencesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

// a worktree whose vendor/ is a link to another checkout autoloads from that checkout's class map
foreach ( array( 'expHelpAudience' => 'exphelpaudience.php', 'expHelpSearch' => 'exphelpsearch.php' ) as $helpClass => $helpFile )
    if ( !class_exists( $helpClass ) )
        require_once dirname( __DIR__, 5 ) . '/kernel/classes/help/' . $helpFile;
unset( $helpClass, $helpFile );

use Exponential\View\Kernel\Exphelp\Search;

class expHelpLeadershipAudiencesTest extends PHPUnit\Framework\TestCase
{
    const AUDIENCES = array( 'project-manager' => 'leadership', 'sales-marketing' => 'business' );
    const START_PAGES = array( 'project-manager' => 'help/leadership/project-managers-start-here.md',
                               'sales-marketing' => 'help/business/sales-and-marketing-start-here.md' );
    const CATALOGUES = array( 'ara-SA', 'eng-US', 'esl-ES', 'esl-MX', 'fre-FR', 'ger-DE', 'heb-IL', 'lat-VA', 'tlh-QO',
                              'ukr-UA', 'untranslated' );

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function read( $path )
    {
        $file = self::root() . '/' . $path;
        return is_file( $file ) ? (string)file_get_contents( $file ) : '';
    }

    /** The lines of one section of an INI file */
    private static function iniSection( $path, $section )
    {
        $ini = self::read( $path );
        $start = strpos( $ini, "\n[" . $section . "]" );
        if ( $start === false )
            return '';
        $end = strpos( $ini, "\n[", $start + 1 );
        return substr( $ini, $start, $end === false ? null : $end - $start );
    }

    /** LA-01 */
    public function testBuiltInDefaults()
    {
        $audience = new expHelpAudience();
        $components = array( self::read( 'design/admin4/stylesheets/components.css' ), self::read( 'design/admin/stylesheets/components.css' ) );
        foreach ( self::AUDIENCES as $id => $category )
        {
            $this->assertArrayHasKey( $id, $audience->audiences() );
            $this->assertArrayHasKey( $category, $audience->categories() );
            foreach ( array( $audience->audiences()[$id]['icon'], $audience->categories()[$category]['icon'] ) as $icon )
                foreach ( $components as $css )
                    $this->assertStringContainsString( '.a4-bi-' . $icon . ' {', $css, $icon );
        }
        $this->assertSame( 'Project managers', $audience->audiences()['project-manager']['name'] );
        $this->assertSame( 'Sales & marketing', $audience->audiences()['sales-marketing']['name'] );
        $this->assertSame( array( 'editor', 'admin', 'developer', 'user', 'project-manager', 'sales-marketing' ),
                           array_slice( array_keys( $audience->audiences() ), 0, 6 ), 'the new audiences come after the first four' );
    }

    /** LA-02 */
    public function testSettings()
    {
        $section = self::iniSection( 'settings/exphelp.ini', 'AudienceSettings' );
        $categories = self::iniSection( 'settings/exphelp.ini', 'CategorySettings' );
        $featured = self::iniSection( 'settings/exphelp.ini', 'FeaturedSettings' );
        foreach ( self::AUDIENCES as $id => $category )
        {
            $this->assertMatchesRegularExpression( '/^Audiences\[\]=' . preg_quote( $id, '/' ) . '\s*$/m', $section );
            $this->assertMatchesRegularExpression( '/^AudienceNames\[' . preg_quote( $id, '/' ) . '\]=\S/m', $section );
            $this->assertMatchesRegularExpression( '/^AudienceIcons\[' . preg_quote( $id, '/' ) . '\]=[a-z0-9-]+\s*$/m', $section );
            $this->assertMatchesRegularExpression( '/^Categories\[\]=' . $category . '\s*$/m', $categories );
            $this->assertMatchesRegularExpression( '/^CategoryNames\[' . $category . '\]=\S/m', $categories );
            $this->assertMatchesRegularExpression( '/^CategoryIcons\[' . $category . '\]=[a-z0-9-]+\s*$/m', $categories );
            $folder = $category === 'leadership' ? 'leadership' : 'business';
            $this->assertMatchesRegularExpression( '#^PathRules\[\]=doc/help/' . $folder . '/\*;' . $id . '\s*$#m', $section );
            $this->assertMatchesRegularExpression( '#^PathRules\[\]=doc/help/' . $folder . '/\*;' . $category . '\s*$#m', $categories );
            $this->assertMatchesRegularExpression( '#^PageList\[\]=' . preg_quote( self::START_PAGES[$id], '#' ) . '\s*$#m', $featured );
        }
        $this->assertMatchesRegularExpression( '#^PageList\[\]=help/leadership/leadership-reading-path\.md\s*$#m', $featured );
    }

    /** LA-03 */
    public function testClassifier()
    {
        $audience = new expHelpAudience();
        $page = $audience->classify( 'doc/help/leadership/risk-management.md', array(), 'A page for project managers.' );
        $this->assertSame( array( 'project-manager' ), $page['audience'] );
        $this->assertSame( 'leadership', $page['category'] );
        $this->assertSame( 'help', $page['type'] );

        $page = $audience->classify( 'doc/help/business/demo-scripts.md' );
        $this->assertSame( array( 'sales-marketing' ), $page['audience'] );
        $this->assertSame( 'business', $page['category'] );

        // front matter wins over the folder, aliases resolve, the configured order is kept
        $page = $audience->classify( 'doc/help/leadership/working-together.md',
            array( 'audience' => array( 'sales', 'pm', 'editors', 'nobody' ), 'category' => 'business' ) );
        $this->assertSame( array( 'editor', 'project-manager', 'sales-marketing' ), $page['audience'] );
        $this->assertSame( 'business', $page['category'] );
        $this->assertSame( array( 'project-manager', 'sales-marketing' ), $audience->audienceList( array( 'project-managers', 'marketing' ) ) );

        // a file name says who it is for
        $this->assertSame( array( 'sales-marketing' ), $audience->guessAudience( 'doc/guides/pricing-sales-marketing.md', '' ) );
        $this->assertSame( array( 'project-manager' ), $audience->guessAudience( 'doc/guides/launch-project-manager.md', '' ) );

        // the four first audiences are untouched
        $this->assertSame( array( 'editor', 'user' ), $audience->classify( 'doc/help/editing-content.md' )['audience'] );
    }

    /** LA-04 */
    public function testSearchFacetsAndViewParameters()
    {
        $search = expHelpSearch::fromDocuments( array(
            'help/leadership/risk-management.md' => array(
                'title' => 'Risk management', 'audience' => array( 'project-manager' ), 'category' => 'leadership', 'type' => 'help',
                'text' => "# Risk management\n\nA risk register for an Exponential project and its launch.\n" ),
            'help/business/demo-scripts.md' => array(
                'title' => 'Demo scripts', 'audience' => array( 'sales-marketing', 'editor' ), 'category' => 'business', 'type' => 'help',
                'text' => "# Demo scripts\n\nShow the project to a customer before the launch.\n" ),
            'help/editing-content.md' => array(
                'title' => 'Editing content', 'audience' => array( 'editor' ), 'category' => 'editing', 'type' => 'help',
                'text' => "# Editing content\n\nEdit and publish a project page before the launch.\n" ),
        ) );
        $all = $search->search( 'launch' );
        $this->assertSame( 3, $all['total'] );
        $this->assertSame( 1, $all['facets']['audience']['project-manager'] );
        $this->assertSame( 1, $all['facets']['audience']['sales-marketing'] );
        $this->assertSame( 2, $all['facets']['audience']['editor'] );
        $this->assertSame( 1, $all['facets']['category']['leadership'] );
        $this->assertSame( 1, $all['facets']['category']['business'] );

        $pm = $search->search( 'launch', array( 'audience' => array( 'project-manager' ) ) );
        $this->assertSame( array( 'help/leadership/risk-management.md' ), array_column( $pm['results'], 'id' ) );
        $sales = $search->search( '', array( 'audience' => 'sales-marketing' ) );
        $this->assertSame( array( 'help/business/demo-scripts.md' ), array_column( $sales['results'], 'id' ) );

        if ( !class_exists( Search::class ) )
            $this->markTestSkipped( 'the exphelp search view is not autoloadable here' );
        $known = array( 'category' => array_keys( ( new expHelpAudience() )->categories() ),
                        'audience' => array_keys( ( new expHelpAudience() )->audiences() ),
                        'type' => array( 'help' ) );
        $state = Search::state( array( 'audience' => 'project-manager,sales-marketing,bogus', 'category' => 'business' ), $known );
        $this->assertSame( array( 'project-manager', 'sales-marketing' ), $state['audience'] );
        $this->assertSame( array( 'business' ), $state['category'] );
        $this->assertSame( '/exphelp/search/(audience)/sales-marketing',
                           expHelp::searchUrl( array( 'audience' => array( 'sales-marketing' ) ) ) );
        $this->assertNotSame( '', Search::categoryIntro( 'leadership' ) );
        $this->assertNotSame( '', Search::categoryIntro( 'business' ) );
    }

    /** LA-05 */
    public function testDashboardCards()
    {
        $admin4 = self::read( 'design/admin4/templates/exphelp/dashboard.tpl' );
        $this->assertNotSame( '', $admin4 );
        $this->assertSame( $admin4, self::read( 'design/admin/templates/exphelp/dashboard.tpl' ), 'the classic admin has the same dashboard' );
        $this->assertStringContainsString( "\$help_audience_ids = array( 'editor', 'admin', 'developer', 'user', 'project-manager', 'sales-marketing'", $admin4 );
        foreach ( self::START_PAGES as $id => $page )
        {
            $this->assertMatchesRegularExpression( "#'" . $id . "', hash\\( 'icon', '[a-z0-9-]+', 'name', '[^']+'\\|i18n\\( 'kernel/exphelp' \\), 'text', '[^']+'\\|i18n\\( 'kernel/exphelp' \\),\\s+'start', '" . preg_quote( $page, '#' ) . "' \\)#", $admin4 );
            $this->assertFileExists( self::root() . '/doc/' . $page );
        }
        $this->assertStringContainsString( 'class="a4-help-audience-start"', $admin4 );
        $this->assertStringContainsString( '<strong>{$help_audience_ids|count}</strong>', $admin4 );
        $this->assertDoesNotMatchRegularExpression( '/<style|style\s*=|<script|\son[a-z]+\s*=/i', $admin4 );

        $css = self::read( 'design/admin4/stylesheets/modules/help.css' );
        $this->assertSame( $css, self::read( 'design/admin/stylesheets/modules/help.css' ), 'help.css is the same in both designs' );
        foreach ( array_keys( self::AUDIENCES ) as $id )
            foreach ( array( '.a4-help-tile.is-' . $id . ' {', '.a4-help-pill.is-' . $id . ' {', '.a4-help-audience.is-' . $id . '::before {' ) as $selector )
                $this->assertStringContainsString( $selector, $css );
        $this->assertStringContainsString( '.a4-help-audience-start {', $css );
        // only tokens: no colors or radii of their own in the new rules
        preg_match_all( '/^\.a4-help-[a-z-]+\.is-(?:project-manager|sales-marketing)[^{]*\{([^}]*)\}/m', $css, $m );
        $this->assertCount( 6, $m[1] );
        foreach ( $m[1] as $rule )
            $this->assertDoesNotMatchRegularExpression( '/#[0-9a-f]{3,8}\b|rgba?\(|\d+px/i', $rule );
    }

    /** LA-06 */
    public function testSearchPageAndViewerPills()
    {
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $search = self::read( 'design/' . $design . '/templates/exphelp/search.tpl' );
            $meta = self::read( 'design/' . $design . '/templates/exphelp/parts/viewer_meta.tpl' );
            foreach ( array( 'project-manager' => 'Project managers', 'sales-marketing' => 'Sales & marketing' ) as $id => $name )
            {
                $this->assertStringContainsString( "'" . $id . "', '" . $name . "'|i18n( 'kernel/exphelp' )", $search, $design );
                $this->assertMatchesRegularExpression( "/'" . $id . "', '[a-z0-9-]+'/", $search, $design . ': icon' );
                $this->assertStringContainsString( "{case match='" . $id . "'}{'" . $name . "'|i18n( 'kernel/exphelp' )}{/case}", $meta, $design );
            }
        }
    }

    /** LA-07 */
    public function testStringsTranslated()
    {
        $sources = array( 'Project managers', 'Sales & marketing', 'Start here', 'Project leadership', 'Sales and marketing',
            'Plan and lead projects: phases and roles, hosting choices, scoping, checklists, quality gates, releases and risks.',
            'Present and promote Exponential: positioning, feature highlights, demos, objections, licensing, brand and community.',
            'Planning, running and growing Exponential projects: phases, roles, checklists, risks and working with the community.',
            'Presenting and promoting Exponential: positioning, demos, objections, licensing, brand and growing the community.' );
        $dashboard = self::read( 'design/admin4/templates/exphelp/dashboard.tpl' );
        $view = self::read( 'kernel/private/classes/views/exphelp/search.php' );
        // every source is shown: a template string, a view string, or a name of exphelp.ini (expHelp::translatedNames())
        $ini = self::read( 'settings/exphelp.ini' );
        foreach ( $sources as $source )
            $this->assertTrue( strpos( $dashboard, "'" . $source . "'|i18n" ) !== false || strpos( $view, "'" . $source . "' )" ) !== false
                               || strpos( self::read( 'design/admin4/templates/exphelp/search.tpl' ), "'" . $source . "'|i18n" ) !== false
                               || preg_match( '/^(?:Audience|Category)Names\[[a-z-]+\]=' . preg_quote( $source, '/' ) . '\s*$/m', $ini ), $source );
        foreach ( self::CATALOGUES as $lang )
        {
            $ts = self::read( 'share/translations/' . $lang . '/translation.ts' );
            $start = strpos( $ts, '<name>kernel/exphelp</name>' );
            $this->assertNotFalse( $start, $lang );
            $context = substr( $ts, $start, strpos( $ts, '</context>', $start ) - $start );
            foreach ( $sources as $source )
            {
                $pattern = '#<source>' . preg_quote( htmlspecialchars( $source, ENT_NOQUOTES | ENT_XML1 ), '#' ) . '</source>\s*<translation'
                         . ( $lang === 'untranslated' ? '[^>]*>' : '>[^<]+' ) . '</translation>#';
                $this->assertMatchesRegularExpression( $pattern, $context, $lang . ': ' . $source );
            }
        }
    }

    /** The anchors of a Markdown file's headings, as the renderer makes them (code fences skipped) */
    private static function anchors( $text )
    {
        list( , $body ) = expHelpFrontMatter::split( $text );
        $anchors = array();
        $used = array();
        $fence = false;
        foreach ( preg_split( '/\r?\n/', $body ) as $line )
        {
            if ( preg_match( '/^\s*(```|~~~)/', $line ) )
            {
                $fence = !$fence;
                continue;
            }
            if ( !$fence && preg_match( '/^#{1,6}\s+(.+?)\s*#*\s*$/', $line, $m ) )
                $anchors[] = expHelpMarkdownRenderer::slug( $m[1], $used );
        }
        return $anchors;
    }

    private static function normalize( $path )
    {
        $out = array();
        foreach ( explode( '/', $path ) as $segment )
        {
            if ( $segment === '..' )
                array_pop( $out );
            elseif ( $segment !== '.' && $segment !== '' )
                $out[] = $segment;
        }
        return implode( '/', $out );
    }

    /** LA-08 */
    public function testPages()
    {
        $checked = 0;
        foreach ( self::AUDIENCES as $id => $category )
        {
            $folder = $category === 'leadership' ? 'doc/help/leadership' : 'doc/help/business';
            $files = glob( self::root() . '/' . $folder . '/*.md' );
            $this->assertGreaterThanOrEqual( 10, count( $files ), $folder );
            foreach ( $files as $file )
            {
                $path = substr( $file, strlen( self::root() ) + 1 );
                $text = (string)file_get_contents( $file );
                list( $meta, $body ) = expHelpFrontMatter::split( $text );
                $audience = is_array( $meta['audience'] ) ? $meta['audience'] : array_map( 'trim', explode( ',', (string)$meta['audience'] ) );
                $this->assertTrue( in_array( 'project-manager', $audience, true ) || in_array( 'sales-marketing', $audience, true ), $path . ': audience' );
                $this->assertSame( $category, $meta['category'], $path );
                $this->assertSame( 'help', $meta['type'], $path );
                $this->assertNotSame( '', trim( (string)$meta['summary'] ), $path . ': summary' );
                $this->assertStringStartsWith( '# ' . $meta['title'] . "\n", ltrim( $body ), $path . ': title is the first heading' );
                $this->assertStringContainsString( '../free-documentation.md', $text, $path . ': license notice' );
                $this->assertDoesNotMatchRegularExpression( '/\b[a-z0-9.-]*alpha\.se7enx\.com\b/i', $text, $path . ': internal host' );
                // no raw HTML that could carry script or style (a tag named inside a `code span` is text)
                $this->assertDoesNotMatchRegularExpression( '/<(?:script|style|iframe)\b/i', preg_replace( '/`[^`\n]*`/', '', $text ), $path );

                preg_match_all( '/\]\(([^)\s]+)\)/', $text, $m );
                foreach ( $m[1] as $href )
                {
                    if ( preg_match( '#^[a-z]+:#i', $href ) )
                    {
                        $this->assertStringStartsWith( 'https://', $href, $path . ': external links use https' );
                        continue;
                    }
                    $parts = explode( '#', $href, 2 );
                    $target = $parts[0] === '' ? $path : self::normalize( dirname( $path ) . '/' . $parts[0] );
                    $this->assertStringStartsWith( 'doc/', $target, $path . ': ' . $href );
                    $this->assertFileExists( self::root() . '/' . $target, $path . ': ' . $href );
                    if ( isset( $parts[1] ) && $parts[1] !== '' && preg_match( '/\.(md|markdown)$/', $target ) )
                        $this->assertContains( $parts[1], self::anchors( self::read( $target ) ), $path . ': ' . $href . ': no such heading' );
                    ++$checked;
                }
            }
        }
        $this->assertGreaterThan( 100, $checked );
        foreach ( array_merge( array_values( self::START_PAGES ), array( 'help/leadership/leadership-reading-path.md' ) ) as $page )
        {
            list( $meta ) = expHelpFrontMatter::split( self::read( 'doc/' . $page ) );
            $this->assertTrue( isset( $meta['featured'] ) && ( $meta['featured'] === true || $meta['featured'] === 'true' ), $page . ' is featured' );
        }
    }
}
