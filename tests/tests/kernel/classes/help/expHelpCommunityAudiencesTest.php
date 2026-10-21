<?php
/**
 * The community audiences of the help: partners, advocates, designers, hosting companies, application creators,
 * website creators, supporters, sponsors, trainers, press, media and influencers, businesses, governments and customers,
 * and the audiences of #645 (community, ecosystem, PHP developers ... students and learners), with their folders and
 * categories (doc/eng-us/features/6.0/help-system.md). No database.
 *
 *  CA-01  expHelpAudience knows the ten audiences and their categories by default, after the first six, with icons
 *         that admin4 and the classic admin draw (an .a4-bi-<icon> class in components.css)
 *  CA-02  settings/exphelp.ini lists them: Audiences[], names, icons, categories and the folder path rules, and agrees
 *         with the built-in defaults
 *  CA-03  the classifier: each folder gives its audience and category; front matter and aliases win
 *  CA-04  the search counts and filters them and the search view accepts them; every new category has an introduction
 *  CA-05  dashboard, search page and viewer pills of both designs (the same files) name each audience; help.css
 *         colors each one from tokens only; no inline CSS or script
 *  CA-06  every new string is in the complete catalogues (twenty-one) under kernel/exphelp
 *  CA-07  the dashboard puts every audience in exactly one group (forty-one cards, the first group open, the others
 *         folded) and the filters behind "More audiences"; "Who makes Exponential" links the partners', the
 *         community's and the ecosystem's landing pages; every audience's start page is written as
 *         help/<folder>/<folder>-start-here.md and, once that page exists, it says who it is for
 *
 *         The start pages of the audiences of #645 are written separately: the test checks one only when its file
 *         exists, so it passes before and after they arrive.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/help/expHelpCommunityAudiencesTest.php
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

class expHelpCommunityAudiencesTest extends PHPUnit\Framework\TestCase
{
    /** id => name, a sample page in its folder, the category of that page */
    const AUDIENCES = array(
        'partner'         => array( 'Partners', 'doc/eng-us/help/partners/partner-program.md', 'partnership' ),
        'advocate'        => array( 'Advocates', 'doc/eng-us/help/advocates/talking-points.md', 'advocacy' ),
        'designer'        => array( 'Designers', 'doc/eng-us/help/designers/theme-design/first-theme.md', 'theme-design' ),
        'hosting'         => array( 'Hosting companies', 'doc/eng-us/help/hosting/server-requirements.md', 'hosting' ),
        'app-creator'     => array( 'Application creators', 'doc/eng-us/help/creators/app-creators/custom-modules.md', 'app-creation' ),
        'website-creator' => array( 'Website creators', 'doc/eng-us/help/creators/website-creators/plan-the-site.md', 'website-creation' ),
        'supporter'       => array( 'Supporters', 'doc/eng-us/help/supporters/ways-to-help.md', 'supporting' ),
        'sponsor'         => array( 'Sponsors', 'doc/eng-us/help/sponsors/why-sponsor.md', 'sponsorship' ),
        'trainer'         => array( 'Trainers', 'doc/eng-us/help/trainers/editors/lesson-1.md', 'training' ),
        'press'           => array( 'Press, media and influencers', 'doc/eng-us/help/press/fact-sheet.md', 'press-media' ),
        'businesses'      => array( 'Businesses', 'doc/eng-us/help/businesses/why-exponential.md', 'business-solutions' ),
        'government'      => array( 'Governments', 'doc/eng-us/help/government/accessibility.md', 'public-sector' ),
        'customer'        => array( 'Customers', 'doc/eng-us/help/customers/day-to-day/content-calendar.md', 'customer-success' ),
        'community'       => array( 'Community', 'doc/eng-us/help/community/community-start-here.md', 'community' ),
        'ecosystem'       => array( 'Ecosystem', 'doc/eng-us/help/ecosystem/ecosystem-start-here.md', 'ecosystem' ),
        'php-developer'   => array( 'PHP Developers', 'doc/eng-us/help/php-developers/php-developers-start-here.md', 'php-development' ),
        'theme-designer'  => array( 'Theme designers', 'doc/eng-us/help/theme-designers/theme-designers-start-here.md', 'theme-design' ),
        'reseller'        => array( 'Resellers', 'doc/eng-us/help/resellers/resellers-start-here.md', 'reselling' ),
        'bundler'         => array( 'Bundlers', 'doc/eng-us/help/bundlers/bundlers-start-here.md', 'bundling' ),
        'ai-agents'       => array( 'AI, agents and agentic', 'doc/eng-us/help/ai-agents/ai-agents-start-here.md', 'ai-agents' ),
        'software-engineer' => array( 'Software Engineers', 'doc/eng-us/help/software-engineers/software-engineers-start-here.md', 'software-engineering' ),
        'adopter'         => array( 'Adopters', 'doc/eng-us/help/adopters/adopters-start-here.md', 'adoption' ),
        'mobile-app-developer' => array( 'Mobile App Developers', 'doc/eng-us/help/mobile-app-developers/mobile-app-developers-start-here.md', 'mobile-apps' ),
        'web-framework-developer' => array( 'Web Framework Developers', 'doc/eng-us/help/web-framework-developers/web-framework-developers-start-here.md', 'web-frameworks' ),
        'cms-developer'   => array( 'CMS Developers', 'doc/eng-us/help/cms-developers/cms-developers-start-here.md', 'cms-development' ),
        'dxp-developer'   => array( 'DXP Developers', 'doc/eng-us/help/dxp-developers/dxp-developers-start-here.md', 'dxp-development' ),
        'ecommerce'       => array( 'E-commerce', 'doc/eng-us/help/e-commerce/e-commerce-start-here.md', 'e-commerce' ),
        'integrator'      => array( 'Integrators', 'doc/eng-us/help/integrators/integrators-start-here.md', 'integration' ),
        'dba'             => array( 'Database Administrators', 'doc/eng-us/help/database-administrators/database-administrators-start-here.md', 'database-administration' ),
        'podcaster'       => array( 'Podcasters, radio and streaming', 'doc/eng-us/help/podcasters/podcasters-start-here.md', 'podcasting-streaming' ),
        'non-profit'      => array( 'Non-profits', 'doc/eng-us/help/non-profits/non-profits-start-here.md', 'non-profits' ),
        'local-business'  => array( 'Local businesses', 'doc/eng-us/help/local-businesses/local-businesses-start-here.md', 'local-business' ),
        'individual'      => array( 'Individuals', 'doc/eng-us/help/individuals/individuals-start-here.md', 'personal-sites' ),
        'non-technical'   => array( 'Non-technical people', 'doc/eng-us/help/non-technical-people/non-technical-people-start-here.md', 'plain-language' ),
        'student'         => array( 'Students and learners', 'doc/eng-us/help/students/students-start-here.md', 'learning' ),
    );
    const CATEGORIES = array( 'partnership', 'advocacy', 'theme-design', 'templates', 'extension-development', 'theme-extensions',
                              'theme-distribution', 'theme-sales', 'theme-lifecycle', 'hosting', 'app-creation',
                              'website-creation', 'supporting', 'sponsorship', 'training', 'press-media', 'project', 'build-two-ways', 'business-solutions', 'public-sector', 'customer-success',
                              'community', 'ecosystem', 'php-development', 'reselling', 'bundling', 'ai-agents', 'software-engineering', 'adoption', 'mobile-apps', 'web-frameworks', 'cms-development', 'dxp-development', 'e-commerce', 'integration', 'database-administration', 'podcasting-streaming', 'non-profits', 'local-business', 'personal-sites', 'plain-language', 'learning' );
    const CATALOGUES = array( 'ara-SA', 'cro-HR', 'cze-CZ', 'dan-DK', 'dut-NL', 'eng-US', 'esl-ES', 'esl-MX', 'fre-FR', 'ger-DE',
                              'heb-IL', 'ita-IT', 'lat-VA', 'nor-NO', 'pol-PL', 'por-BR', 'swe-SE', 'tlh-QO', 'tur-TR', 'ukr-UA',
                              'untranslated' );

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function read( $path )
    {
        $file = self::root() . '/' . $path;
        return is_file( $file ) ? (string)file_get_contents( $file ) : '';
    }

    private static function iniSection( $path, $section )
    {
        $ini = self::read( $path );
        $start = strpos( $ini, "\n[" . $section . "]" );
        if ( $start === false )
            return '';
        $end = strpos( $ini, "\n[", $start + 1 );
        return substr( $ini, $start, $end === false ? null : $end - $start );
    }

    /** CA-01 */
    public function testBuiltInDefaults()
    {
        $audience = new expHelpAudience();
        $ids = array_keys( $audience->audiences() );
        $this->assertSame( array( 'editor', 'admin', 'developer', 'user', 'project-manager', 'sales-marketing' ), array_slice( $ids, 0, 6 ) );
        $this->assertSame( array_keys( self::AUDIENCES ), array_slice( $ids, 6, count( self::AUDIENCES ) ) );
        $components = array( self::read( 'design/admin4/stylesheets/components.css' ), self::read( 'design/admin/stylesheets/components.css' ) );
        foreach ( self::AUDIENCES as $id => $info )
        {
            $this->assertSame( $info[0], $audience->audiences()[$id]['name'] );
            foreach ( $components as $css )
                $this->assertStringContainsString( '.a4-bi-' . $audience->audiences()[$id]['icon'] . ' {', $css, $id );
        }
        foreach ( self::CATEGORIES as $category )
        {
            $this->assertArrayHasKey( $category, $audience->categories() );
            foreach ( $components as $css )
                $this->assertStringContainsString( '.a4-bi-' . $audience->categories()[$category]['icon'] . ' {', $css, $category );
        }
    }

    /** CA-02 */
    public function testSettingsAgreeWithDefaults()
    {
        $section = self::iniSection( 'settings/exphelp.ini', 'AudienceSettings' );
        $categories = self::iniSection( 'settings/exphelp.ini', 'CategorySettings' );
        $audience = new expHelpAudience();
        foreach ( self::AUDIENCES as $id => $info )
        {
            $q = preg_quote( $id, '/' );
            $this->assertMatchesRegularExpression( '/^Audiences\[\]=' . $q . '\s*$/m', $section );
            $this->assertMatchesRegularExpression( '/^AudienceNames\[' . $q . '\]=' . preg_quote( $info[0], '/' ) . '\s*$/m', $section );
            $this->assertMatchesRegularExpression( '/^AudienceIcons\[' . $q . '\]=' . preg_quote( $audience->audiences()[$id]['icon'], '/' ) . '\s*$/m', $section );
        }
        foreach ( self::CATEGORIES as $category )
        {
            $q = preg_quote( $category, '/' );
            $this->assertMatchesRegularExpression( '/^Categories\[\]=' . $q . '\s*$/m', $categories );
            $this->assertMatchesRegularExpression( '/^CategoryNames\[' . $q . '\]=' . preg_quote( $audience->categories()[$category]['name'], '/' ) . '\s*$/m', $categories );
            $this->assertMatchesRegularExpression( '/^CategoryIcons\[' . $q . '\]=' . preg_quote( $audience->categories()[$category]['icon'], '/' ) . '\s*$/m', $categories );
        }
        foreach ( array( 'partners' => 'partner', 'advocates' => 'advocate', 'designers' => 'designer', 'hosting' => 'hosting',
                         'creators/app-creators' => 'app-creator', 'creators/website-creators' => 'website-creator',
                         'supporters' => 'supporter', 'sponsors' => 'sponsor', 'trainers' => 'trainer', 'press' => 'press', 'businesses' => 'businesses', 'government' => 'government',
                         'community' => 'community', 'ecosystem' => 'ecosystem', 'php-developers' => 'php-developer', 'theme-designers' => 'theme-designer', 'resellers' => 'reseller', 'bundlers' => 'bundler', 'ai-agents' => 'ai-agents', 'software-engineers' => 'software-engineer', 'adopters' => 'adopter', 'mobile-app-developers' => 'mobile-app-developer', 'web-framework-developers' => 'web-framework-developer', 'cms-developers' => 'cms-developer', 'dxp-developers' => 'dxp-developer', 'e-commerce' => 'ecommerce', 'integrators' => 'integrator', 'database-administrators' => 'dba', 'podcasters' => 'podcaster', 'non-profits' => 'non-profit', 'local-businesses' => 'local-business', 'individuals' => 'individual', 'non-technical-people' => 'non-technical', 'students' => 'student',
                         'extension-development' => 'developer,designer' ) as $folder => $ids )
            $this->assertMatchesRegularExpression( '#^PathRules\[\]=doc/eng-us/help/' . preg_quote( $folder, '#' ) . '/\*;' . $ids . '\s*$#m', $section );
    }

    /** CA-03 */
    public function testClassifier()
    {
        $audience = new expHelpAudience();
        foreach ( self::AUDIENCES as $id => $info )
        {
            $page = $audience->classify( $info[1] );
            $this->assertSame( array( $id ), $page['audience'], $info[1] );
            $this->assertSame( $info[2], $page['category'], $info[1] );
            $this->assertSame( 'help', $page['type'], $info[1] );
        }
        $page = $audience->classify( 'doc/eng-us/help/designers/templates/operators.md' );
        $this->assertSame( 'templates', $page['category'] );
        $page = $audience->classify( 'doc/eng-us/help/extension-development/01-first-extension.md' );
        $this->assertSame( array( 'developer', 'designer' ), $page['audience'] );
        $this->assertSame( 'extension-development', $page['category'] );
        $page = $audience->classify( 'doc/eng-us/help/creators/both.md' );
        $this->assertSame( array( 'app-creator', 'website-creator' ), $page['audience'] );

        // front matter wins, aliases resolve, the configured order is kept
        $page = $audience->classify( 'doc/eng-us/help/partners/hosting-partners.md',
            array( 'audience' => array( 'hosting-companies', 'partners', 'admins' ), 'category' => 'hosting' ) );
        $this->assertSame( array( 'admin', 'partner', 'hosting' ), $page['audience'] );
        $this->assertSame( 'hosting', $page['category'] );
        $this->assertSame( array( 'designer', 'app-creator', 'sponsor' ),
                           $audience->audienceList( array( 'sponsors', 'application-creators', 'designers' ) ) );
        $this->assertSame( array( 'sponsor' ), $audience->guessAudience( 'doc/eng-us/guides/benefits-sponsor.md', '' ) );

        // the earlier audiences are untouched
        $this->assertSame( array( 'editor', 'user' ), $audience->classify( 'doc/eng-us/help/editing-content.md' )['audience'] );
        $this->assertSame( array( 'project-manager' ), $audience->classify( 'doc/eng-us/help/leadership/x.md' )['audience'] );
    }

    /** CA-04 */
    public function testSearchFacetsAndViewParameters()
    {
        $docs = array();
        foreach ( self::AUDIENCES as $id => $info )
            $docs[substr( $info[1], 4 )] = array( 'title' => $info[0] . ' page', 'audience' => array( $id ), 'category' => $info[2],
                'type' => 'help', 'text' => '# ' . $info[0] . " page\n\nHow the community grows Exponential.\n" );
        $search = expHelpSearch::fromDocuments( $docs );
        $all = $search->search( 'community' );
        $this->assertSame( count( self::AUDIENCES ), $all['total'] );
        foreach ( self::AUDIENCES as $id => $info )
        {
            $this->assertSame( 1, $all['facets']['audience'][$id], $id );
            $one = $search->search( '', array( 'audience' => $id ) );
            $this->assertSame( array( substr( $info[1], 4 ) ), array_column( $one['results'], 'id' ), $id );
        }

        if ( !class_exists( Search::class ) )
            $this->markTestSkipped( 'the exphelp search view is not autoloadable here' );
        $known = array( 'category' => array_keys( ( new expHelpAudience() )->categories() ),
                        'audience' => array_keys( ( new expHelpAudience() )->audiences() ), 'type' => array( 'help' ) );
        $state = Search::state( array( 'audience' => 'sponsor,website-creator,bogus', 'category' => 'theme-sales' ), $known );
        $wanted = $state['audience'];
        sort( $wanted );
        $this->assertSame( array( 'sponsor', 'website-creator' ), $wanted, 'known audiences kept, unknown ones dropped' );
        $this->assertSame( array( 'theme-sales' ), $state['category'] );
        $this->assertSame( '/exphelp/search/(audience)/app-creator', expHelp::searchUrl( array( 'audience' => array( 'app-creator' ) ) ) );
        foreach ( self::CATEGORIES as $category )
            $this->assertNotSame( '', Search::categoryIntro( $category ), $category );
    }

    /** CA-05 */
    public function testTemplatesAndCss()
    {
        foreach ( array( 'templates/exphelp/dashboard.tpl', 'templates/exphelp/search.tpl', 'templates/exphelp/parts/viewer_meta.tpl',
                         'stylesheets/modules/help.css' ) as $file )
            $this->assertSame( self::read( 'design/admin4/' . $file ), self::read( 'design/admin/' . $file ), $file . ' is the same in both designs' );
        $dashboard = self::read( 'design/admin4/templates/exphelp/dashboard.tpl' );
        $search = self::read( 'design/admin4/templates/exphelp/search.tpl' );
        $meta = self::read( 'design/admin4/templates/exphelp/parts/viewer_meta.tpl' );
        $css = self::read( 'design/admin4/stylesheets/modules/help.css' );
        foreach ( self::AUDIENCES as $id => $info )
        {
            $this->assertMatchesRegularExpression( "#\\\$help_audience_ids = array\\([^)]*'" . $id . "'#", $dashboard, $id );
            $this->assertMatchesRegularExpression( "#'" . $id . "', hash\\( 'icon', '[a-z0-9-]+', 'name', '" . preg_quote( $info[0], '#' ) . "'\\|i18n\\( 'kernel/exphelp' \\), 'text', '(?:[^'\\\\]|\\\\.)+'\\|i18n\\( 'kernel/exphelp' \\)#", $dashboard, $id );
            $this->assertStringContainsString( "'" . $id . "', '" . $info[0] . "'|i18n( 'kernel/exphelp' )", $search, $id );
            $this->assertStringContainsString( "{case match='" . $id . "'}{'" . $info[0] . "'|i18n( 'kernel/exphelp' )}{/case}", $meta, $id );
            foreach ( array( '.a4-help-tile.is-' . $id . ' {', '.a4-help-pill.is-' . $id . ' {', '.a4-help-audience.is-' . $id . '::before {' ) as $selector )
                $this->assertStringContainsString( $selector, $css );
        }
        foreach ( array( $dashboard, $search, $meta ) as $tpl )
            $this->assertDoesNotMatchRegularExpression( '/<style|style\s*=|<script|\son[a-z]+\s*=/i', $tpl );
        preg_match_all( '/^\.a4-help-[a-z-]+\.is-(?:' . implode( '|', array_map( 'preg_quote', array_keys( self::AUDIENCES ) ) ) . ')[^{]*\{([^}]*)\}/m', $css, $m );
        $this->assertCount( 3 * count( self::AUDIENCES ), $m[1] );
        foreach ( $m[1] as $rule )
            $this->assertDoesNotMatchRegularExpression( '/#[0-9a-f]{3,8}\b|rgba?\(|\d+px/i', $rule );
    }

    /** CA-06 */
    public function testStringsTranslated()
    {
        $sources = array();
        $audience = new expHelpAudience();
        foreach ( self::AUDIENCES as $id => $info )
            $sources[] = $info[0];
        foreach ( self::CATEGORIES as $category )
            $sources[] = $audience->categories()[$category]['name'];
        $dashboard = self::read( 'design/admin4/templates/exphelp/dashboard.tpl' );
        foreach ( array_keys( self::AUDIENCES ) as $id )
        {
            $this->assertMatchesRegularExpression( "#'" . $id . "', hash\\( 'icon', [^\n]*?'text', '((?:[^'\\\\]|\\\\.)+)'\\|i18n#", $dashboard );
            preg_match( "#'" . $id . "', hash\\( 'icon', [^\n]*?'text', '((?:[^'\\\\]|\\\\.)+)'\\|i18n#", $dashboard, $m );
            $sources[] = stripslashes( $m[1] );
        }
        if ( class_exists( Search::class ) && class_exists( 'ezpI18n' ) )
            foreach ( self::CATEGORIES as $category )
                $sources[] = Search::categoryIntro( $category );
        foreach ( self::CATALOGUES as $lang )
        {
            $ts = self::read( 'share/translations/' . $lang . '/translation.ts' );
            $start = strpos( $ts, '<name>kernel/exphelp</name>' );
            $this->assertNotFalse( $start, $lang );
            $context = substr( $ts, $start, strpos( $ts, '</context>', $start ) - $start );
            foreach ( $sources as $source )
            {
                // an apostrophe is written as is or as &apos;
                $pattern = '#<source>' . str_replace( "'", "(?:'|&apos;)", preg_quote( htmlspecialchars( $source, ENT_NOQUOTES | ENT_XML1 ), '#' ) ) . '</source>\s*<translation'
                         . ( $lang === 'untranslated' ? '[^>]*>' : '>[^<]+' ) . '</translation>#';
                $this->assertMatchesRegularExpression( $pattern, $context, $lang . ': ' . $source );
            }
        }
    }

    /** CA-07 */
    public function testDashboardGroupsAboutAndStartPages()
    {
        $dashboard = self::read( 'design/admin4/templates/exphelp/dashboard.tpl' );
        preg_match( "#\\\$help_audience_ids = array\\(([^)]*)\\)#", $dashboard, $m );
        preg_match_all( "/'([a-z-]+)'/", $m[1], $ids );
        $this->assertCount( 41, $ids[1] );
        preg_match_all( "#hash\\( 'id', '([a-z-]+)', 'open', (true|false)\\(\\),[^\n]*\n\\s*'ids', array\\(([^)]*)\\) \\)#", $dashboard, $groups, PREG_SET_ORDER );
        $this->assertCount( 5, $groups );
        $this->assertSame( 'true', $groups[0][2], 'the first group is open' );
        $grouped = array();
        foreach ( $groups as $i => $group )
        {
            if ( $i > 0 )
                $this->assertSame( 'false', $group[2], $group[1] . ' is folded' );
            preg_match_all( "/'([a-z-]+)'/", $group[3], $g );
            $grouped = array_merge( $grouped, $g[1] );
        }
        $sorted = $ids[1];
        sort( $sorted );
        sort( $grouped );
        $this->assertSame( $sorted, $grouped, 'every audience in exactly one group' );
        $this->assertStringContainsString( '<details class="a4-help-audience-group"', $dashboard );
        $this->assertStringContainsString( "{'More audiences'|i18n( \$help_ctx )}", $dashboard );
        $this->assertStringContainsString( "{'%count audiences'|i18n( \$help_ctx,, hash( '%count', \$help_group.ids|count ) )|wash}", $dashboard );
        $css = self::read( 'design/admin4/stylesheets/modules/help.css' );
        foreach ( array( '.a4-help-audience-group {', '.a4-help-audience-group-head {', '.a4-help-filters-more {' ) as $selector )
            $this->assertStringContainsString( $selector, $css );

        // Who makes Exponential: 7x, the Foundation, then the partners, the community and the ecosystem
        $about = self::read( 'design/admin4/templates/exphelp/parts/dashboard_about.tpl' );
        $this->assertSame( $about, self::read( 'design/admin/templates/exphelp/parts/dashboard_about.tpl' ) );
        $this->assertDoesNotMatchRegularExpression( '/<style|\sstyle\s*=|<script|\son[a-z]+\s*=/i', $about );
        $last = 0;
        foreach ( array( "'Who is 7x?'", "'Who is the Exponential Foundation?'", "'/exphelp/audience/partner'", "'/exphelp/audience/community'", "'/exphelp/audience/ecosystem'" ) as $needle )
        {
            $at = strpos( $about, $needle );
            $this->assertNotFalse( $at, $needle );
            $this->assertGreaterThan( $last, $at, $needle . ' in order' );
            $last = $at;
        }

        // the start page of each audience, and the page itself once it is written
        $audience = new expHelpAudience();
        foreach ( array_slice( self::AUDIENCES, 13, null, true ) as $id => $info )
        {
            $folder = basename( dirname( $info[1] ) );
            $landing = self::iniSection( 'settings/exphelp.ini', 'Landing_' . $id );
            $this->assertMatchesRegularExpression( '#^StartPage=help/' . preg_quote( $folder, '#' ) . '/' . preg_quote( $folder, '#' ) . '-start-here\.md\s*$#m', $landing, $id );
            $this->assertSame( array( $id ), $audience->classify( $info[1] )['audience'], $id );
            $file = self::root() . '/' . $info[1];
            if ( !is_file( $file ) )
                continue;
            $text = (string)file_get_contents( $file );
            $this->assertMatchesRegularExpression( '/^audience:[^\n]*\b' . preg_quote( $id, '/' ) . '\b/m', $text, $info[1] );
        }
    }
}
