<?php
/**
 * The community audiences of the help: partners, advocates, designers, hosting companies, application creators,
 * website creators, supporters, sponsors, trainers and press, media and influencers, with their folders and categories (doc/features/6.0/help-system.md).
 * No database.
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
        'partner'         => array( 'Partners', 'doc/help/partners/partner-program.md', 'partnership' ),
        'advocate'        => array( 'Advocates', 'doc/help/advocates/talking-points.md', 'advocacy' ),
        'designer'        => array( 'Designers', 'doc/help/designers/theme-design/first-theme.md', 'theme-design' ),
        'hosting'         => array( 'Hosting companies', 'doc/help/hosting/server-requirements.md', 'hosting' ),
        'app-creator'     => array( 'Application creators', 'doc/help/creators/app-creators/custom-modules.md', 'app-creation' ),
        'website-creator' => array( 'Website creators', 'doc/help/creators/website-creators/plan-the-site.md', 'website-creation' ),
        'supporter'       => array( 'Supporters', 'doc/help/supporters/ways-to-help.md', 'supporting' ),
        'sponsor'         => array( 'Sponsors', 'doc/help/sponsors/why-sponsor.md', 'sponsorship' ),
        'trainer'         => array( 'Trainers', 'doc/help/trainers/editors/lesson-1.md', 'training' ),
        'press'           => array( 'Press, media and influencers', 'doc/help/press/fact-sheet.md', 'press-media' ),
    );
    const CATEGORIES = array( 'partnership', 'advocacy', 'theme-design', 'templates', 'extension-development', 'theme-extensions',
                              'theme-distribution', 'theme-sales', 'theme-lifecycle', 'hosting', 'app-creation',
                              'website-creation', 'supporting', 'sponsorship', 'training', 'press-media' );
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
        $this->assertSame( array_keys( self::AUDIENCES ), array_slice( $ids, 6, 10 ) );
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
                         'supporters' => 'supporter', 'sponsors' => 'sponsor', 'trainers' => 'trainer', 'press' => 'press',
                         'extension-development' => 'developer,designer' ) as $folder => $ids )
            $this->assertMatchesRegularExpression( '#^PathRules\[\]=doc/help/' . preg_quote( $folder, '#' ) . '/\*;' . $ids . '\s*$#m', $section );
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
        $page = $audience->classify( 'doc/help/designers/templates/operators.md' );
        $this->assertSame( 'templates', $page['category'] );
        $page = $audience->classify( 'doc/help/extension-development/01-first-extension.md' );
        $this->assertSame( array( 'developer', 'designer' ), $page['audience'] );
        $this->assertSame( 'extension-development', $page['category'] );
        $page = $audience->classify( 'doc/help/creators/both.md' );
        $this->assertSame( array( 'app-creator', 'website-creator' ), $page['audience'] );

        // front matter wins, aliases resolve, the configured order is kept
        $page = $audience->classify( 'doc/help/partners/hosting-partners.md',
            array( 'audience' => array( 'hosting-companies', 'partners', 'admins' ), 'category' => 'hosting' ) );
        $this->assertSame( array( 'admin', 'partner', 'hosting' ), $page['audience'] );
        $this->assertSame( 'hosting', $page['category'] );
        $this->assertSame( array( 'designer', 'app-creator', 'sponsor' ),
                           $audience->audienceList( array( 'sponsors', 'application-creators', 'designers' ) ) );
        $this->assertSame( array( 'sponsor' ), $audience->guessAudience( 'doc/guides/benefits-sponsor.md', '' ) );

        // the earlier audiences are untouched
        $this->assertSame( array( 'editor', 'user' ), $audience->classify( 'doc/help/editing-content.md' )['audience'] );
        $this->assertSame( array( 'project-manager' ), $audience->classify( 'doc/help/leadership/x.md' )['audience'] );
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
            $this->assertMatchesRegularExpression( "#'" . $id . "', hash\\( 'icon', '[a-z0-9-]+', 'name', '" . preg_quote( $info[0], '#' ) . "'\\|i18n\\( 'kernel/exphelp' \\), 'text', '[^']+'\\|i18n\\( 'kernel/exphelp' \\)#", $dashboard, $id );
            $this->assertStringContainsString( "'" . $id . "', '" . $info[0] . "'|i18n( 'kernel/exphelp' )", $search, $id );
            $this->assertStringContainsString( "{case match='" . $id . "'}{'" . $info[0] . "'|i18n( 'kernel/exphelp' )}{/case}", $meta, $id );
            foreach ( array( '.a4-help-tile.is-' . $id . ' {', '.a4-help-pill.is-' . $id . ' {', '.a4-help-audience.is-' . $id . '::before {' ) as $selector )
                $this->assertStringContainsString( $selector, $css );
        }
        foreach ( array( $dashboard, $search, $meta ) as $tpl )
            $this->assertDoesNotMatchRegularExpression( '/<style|style\s*=|<script|\son[a-z]+\s*=/i', $tpl );
        preg_match_all( '/^\.a4-help-[a-z-]+\.is-(?:' . implode( '|', array_map( 'preg_quote', array_keys( self::AUDIENCES ) ) ) . ')[^{]*\{([^}]*)\}/m', $css, $m );
        $this->assertCount( 30, $m[1] );
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
            $this->assertMatchesRegularExpression( "#'" . $id . "', hash\\( 'icon', [^\n]*?'text', '([^']+)'\\|i18n#", $dashboard );
            preg_match( "#'" . $id . "', hash\\( 'icon', [^\n]*?'text', '([^']+)'\\|i18n#", $dashboard, $m );
            $sources[] = $m[1];
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
                $pattern = '#<source>' . preg_quote( htmlspecialchars( $source, ENT_NOQUOTES | ENT_XML1 ), '#' ) . '</source>\s*<translation'
                         . ( $lang === 'untranslated' ? '[^>]*>' : '>[^<]+' ) . '</translation>#';
                $this->assertMatchesRegularExpression( $pattern, $context, $lang . ': ' . $source );
            }
        }
    }
}
