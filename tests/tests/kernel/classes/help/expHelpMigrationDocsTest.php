<?php
/**
 * The help center's migrations section (doc/eng-us/help/migrations/, its German counterpart and the category
 * "migrations"). No database.
 *
 *  MD-01  the category migrations is a built-in default (name, icon with its CSS class) and settings/exphelp.ini
 *         lists it with the same name and icon, the path rules of the section, and an introduction in the search
 *  MD-02  the category's name and introduction are translated in kernel/exphelp of every complete catalogue
 *  MD-03  every page of the section has front matter: title = first heading, audience, category migrations, order,
 *         summary, keywords; and a German counterpart in doc/ger-de/.paths.json that exists
 *  MD-04  the hub links every recipe and the recipes index; every recipe links the hub, the recipes index and the
 *         page that tells migrations from migrating a site
 *  MD-05  the hub is featured and opens the module migration's context help
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/help/expHelpMigrationDocsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

// a worktree whose vendor/ is a link to another checkout autoloads from that checkout's class map
foreach ( array( 'expHelpFrontMatter' => 'exphelpfrontmatter.php', 'expHelpAudience' => 'exphelpaudience.php' ) as $helpClass => $helpFile )
    if ( !class_exists( $helpClass ) )
        require_once dirname( __DIR__, 5 ) . '/kernel/classes/help/' . $helpFile;
unset( $helpClass, $helpFile );

class expHelpMigrationDocsTest extends PHPUnit\Framework\TestCase
{
    const FOLDER = 'doc/eng-us/help/migrations';
    const NAME = 'Migrations';
    const INTRO = 'Changes to the installation kept as files: their state, the runs, run and roll back.';
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

    /** The pages of the section, as paths from the installation root */
    private static function pages()
    {
        $pages = array();
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::root() . '/' . self::FOLDER, FilesystemIterator::SKIP_DOTS ) );
        foreach ( $it as $file )
            if ( substr( $file->getFilename(), -3 ) === '.md' )
                $pages[] = substr( str_replace( '\\', '/', $file->getPathname() ), strlen( self::root() ) + 1 );
        sort( $pages );
        return $pages;
    }

    /** The pages a page links to, as paths from the installation root */
    private static function linksOf( $path )
    {
        preg_match_all( '/\]\(([^)\s#]+\.md)(#[^)]*)?\)/', self::read( $path ), $m );
        $out = array();
        foreach ( $m[1] as $link )
        {
            $parts = array();
            foreach ( explode( '/', dirname( $path ) . '/' . $link ) as $part )
            {
                if ( $part === '..' )
                    array_pop( $parts );
                elseif ( $part !== '.' && $part !== '' )
                    $parts[] = $part;
            }
            $out[] = implode( '/', $parts );
        }
        return array_values( array_unique( $out ) );
    }

    /** MD-01 */
    public function testCategoryDefaultsAndSettings()
    {
        $categories = ( new expHelpAudience() )->categories();
        $this->assertArrayHasKey( 'migrations', $categories );
        $this->assertSame( self::NAME, $categories['migrations']['name'] );
        foreach ( array( 'design/admin4/stylesheets/components.css', 'design/admin/stylesheets/components.css' ) as $css )
            $this->assertStringContainsString( '.a4-bi-' . $categories['migrations']['icon'] . ' {', self::read( $css ), $css );

        $section = self::iniSection( 'settings/exphelp.ini', 'CategorySettings' );
        $this->assertMatchesRegularExpression( '/^Categories\[\]=migrations\s*$/m', $section );
        $this->assertMatchesRegularExpression( '/^CategoryNames\[migrations\]=' . self::NAME . '\s*$/m', $section );
        $this->assertMatchesRegularExpression( '/^CategoryIcons\[migrations\]=' . preg_quote( $categories['migrations']['icon'], '/' ) . '\s*$/m', $section );
        foreach ( array( 'doc/eng-us/help/migrations/*', 'doc/eng-us/guides/migrations*.md' ) as $rule )
        {
            $this->assertMatchesRegularExpression( '#^PathRules\[\]=' . preg_quote( $rule, '#' ) . ';migrations\s*$#m', $section );
            $this->assertContains( $rule . ';migrations', expHelpAudience::CATEGORY_RULES );
        }
        $this->assertStringContainsString( "case 'migrations': return \\ezpI18n::tr( \$ctx, '" . self::INTRO . "' );",
                                           self::read( 'kernel/private/classes/views/exphelp/search.php' ) );
    }

    /** MD-02 */
    public function testCategoryStringsTranslated()
    {
        foreach ( self::CATALOGUES as $lang )
        {
            $ts = self::read( 'share/translations/' . $lang . '/translation.ts' );
            $start = strpos( $ts, '<name>kernel/exphelp</name>' );
            $this->assertNotFalse( $start, $lang );
            $context = substr( $ts, $start, strpos( $ts, '</context>', $start ) - $start );
            foreach ( array( self::NAME, self::INTRO ) as $source )
                $this->assertMatchesRegularExpression( '#<source>' . preg_quote( $source, '#' ) . '</source>\s*<translation'
                                                       . ( $lang === 'untranslated' ? '[^>]*>' : '>[^<]+' ) . '</translation>#',
                                                       $context, $lang . ': ' . $source );
        }
    }

    /** MD-03 */
    public function testFrontMatterAndGermanCounterparts()
    {
        $map = json_decode( self::read( 'doc/ger-de/.paths.json' ), true );
        $this->assertIsArray( $map );
        $german = array_flip( $map );
        $pages = self::pages();
        $this->assertGreaterThanOrEqual( 30, count( $pages ) );
        foreach ( $pages as $page )
        {
            list( $meta, $body ) = expHelpFrontMatter::split( self::read( $page ) );
            foreach ( array( 'title', 'audience', 'order', 'summary', 'keywords' ) as $key )
                $this->assertNotEmpty( isset( $meta[$key] ) ? $meta[$key] : '', "$page: $key" );
            $this->assertSame( 'migrations', isset( $meta['category'] ) ? $meta['category'] : '', "$page: category" );
            $this->assertMatchesRegularExpression( '/^# ' . preg_quote( $meta['title'], '/' ) . '\s*$/m', $body, "$page: the first heading is the title" );
            $id = substr( $page, strlen( 'doc/eng-us/' ) );
            $this->assertArrayHasKey( $id, $german, "$page has no German counterpart in .paths.json" );
            $this->assertFileExists( self::root() . '/doc/ger-de/' . $german[$id] );
        }
    }

    /** MD-04 */
    public function testHubAndRecipesLinkEachOther()
    {
        $hub = self::FOLDER . '/README.md';
        $index = self::FOLDER . '/recipes/README.md';
        $versus = self::FOLDER . '/migrations-versus-migrating-a-site.md';
        $fromHub = self::linksOf( $hub );
        $this->assertContains( $index, $fromHub );
        $this->assertContains( $versus, $fromHub );
        foreach ( glob( self::root() . '/' . self::FOLDER . '/recipes/*.md' ) as $file )
        {
            $recipe = substr( $file, strlen( self::root() ) + 1 );
            if ( $recipe === $index )
                continue;
            $this->assertContains( $recipe, $fromHub, "the hub does not link $recipe" );
            $this->assertContains( $recipe, self::linksOf( $index ), "the recipes index does not link $recipe" );
            $links = self::linksOf( $recipe );
            foreach ( array( $hub, $index, $versus ) as $target )
                $this->assertContains( $target, $links, "$recipe does not link $target" );
        }
    }

    /** MD-05 */
    public function testHubIsFeaturedAndOpensTheContextHelp()
    {
        list( $meta ) = expHelpFrontMatter::split( self::read( self::FOLDER . '/README.md' ) );
        $this->assertTrue( in_array( isset( $meta['featured'] ) ? $meta['featured'] : '', array( true, 'true' ), true ) );
        $this->assertMatchesRegularExpression( '#^PageList\[\]=help/migrations/README.md\s*$#m', self::iniSection( 'settings/exphelp.ini', 'FeaturedSettings' ) );
        $this->assertMatchesRegularExpression( '#^Map\[migration\]=help/migrations/README.md\s*$#m', self::iniSection( 'settings/exphelp.ini', 'ContextHelp' ) );
    }
}
