<?php
/**
 * The translated documentation (doc/en/us/bc/6.0/translated-documentation.md): the language directories below doc/,
 * the reader's language, the fallback to the original and translated file names. No database.
 *
 *  TD-01  the move: every tracked file below doc/ is in a language directory, in doc/changelogs/ or doc/README.md
 *  TD-02  no tracked text file names an old documentation path (doc/<entry> of before the move), apart from the
 *         history that records them (released changelogs, history pages) and other repositories' doc/ directories
 *  TD-03  a locale maps to its language directory: the exact entry, then the locale without variant, then its
 *         language; anything else, and a directory that is not configured, is the default
 *  TD-04  the shipped exphelp.ini [DocLanguages] maps eng-US and eng-GB to en/us and ger-DE to de
 *  TD-05  path map: a translated file name has the id of the page it translates, and back; a malformed entry is
 *         left out; a file name given instead of an id becomes the id
 *  TD-06  the index of a translation serves the translated page where there is one and the original elsewhere,
 *         marked as fallback; the original's index never reads a translation; each has its own cache directory
 *  TD-07  a page id names the same page in every language (pathOf), and links of a translated page resolve against
 *         the file it was read from (expHelpLinks::resolveId)
 *  TD-08  the search of a translation finds the translated text and the original's untranslated pages, never the
 *         original of a translated page
 *  TD-09  a translated page is classified by the settings' path rules of the page it translates
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpTranslatedDocumentationTest extends PHPUnit\Framework\TestCase
{
    /** The entries that moved from doc/ to doc/en/us/ (2026-10-20) */
    const MOVED = array( 'INSTALL.md', 'LICENSE-GFDL-1.3.txt', 'README-LICENSE.md', 'bc', 'branch-overview.md', 'doxygen',
                         'examples', 'features', 'glossary.md', 'guides', 'help', 'history', 'install', 'languages.md',
                         'release-overview', 'shop', 'specifications', 'standards' );

    /** What may be tracked directly below doc/ besides the language directories */
    const DOC_ROOT_ALLOWED = array( 'doc/README.md' );

    /** Files whose text may keep the old paths: they record history (path prefixes) */
    const HISTORY_PREFIXES = array( 'doc/changelogs/', 'doc/en/us/history/', 'share/filelist.md5' );

    /** ... except the changelog of the release being prepared */
    const HISTORY_EXCEPTIONS = array( 'doc/changelogs/6.0/6.0.15.md' );

    /** Files that describe the move itself and name the old paths on purpose */
    const DESCRIBE_THE_MOVE = array( 'doc/en/us/bc/6.0/translated-documentation.md',
                                     'tests/tests/kernel/classes/help/expHelpTranslatedDocumentationTest.php' );

    /** @var string the fixture installation */
    private static $root = '';

    public static function setUpBeforeClass(): void
    {
        self::$root = sys_get_temp_dir() . '/exphelp-lang-' . bin2hex( random_bytes( 6 ) );
        $files = array(
            'doc/en/us/help/start.md' => "# Start here\n\nWelcome. See [the other page](other.md) and [the changes](../../../changelogs/6.0.md).\n",
            'doc/en/us/help/other.md' => "# The other page\n\nOnly in English: quagga.\n",
            'doc/en/us/help/partners/program.md' => "# Partner program\n\nText.\n",
            'doc/en/us/help/img/shot.png' => base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==' ),
            'doc/de/hilfe/start.md' => "# Hier beginnen\n\nWillkommen, Zebrafink. Siehe [die andere Seite](../hilfe/andere.md) und [das Bild](../../en/us/help/img/shot.png).\n",
            'doc/de/hilfe/partner/programm.md' => "# Partnerprogramm\n\nText.\n",
            'doc/de/.paths.json' => json_encode( array( 'hilfe/start.md' => 'help/start.md', 'hilfe/andere.md' => 'help/other.md',
                                                        'hilfe/partner/programm.md' => 'help/partners/program.md',
                                                        '../escape.md' => 'help/x.md', 'hilfe/bad.md' => 7 ) ),
            'doc/fr/help/start.md' => "# Commencer ici\n",
            'doc/changelogs/6.0.md' => "# Changes in 6.0\n\nText.\n",
        );
        foreach ( $files as $path => $content )
        {
            @mkdir( dirname( self::$root . '/' . $path ), 0777, true );
            file_put_contents( self::$root . '/' . $path, $content );
        }
    }

    public static function tearDownAfterClass(): void
    {
        if ( self::$root !== '' )
            self::remove( self::$root );
        expHelpDocumentIndex::setInstance( null );
    }

    private static function remove( $path )
    {
        if ( is_link( $path ) || is_file( $path ) )
            return unlink( $path );
        if ( !is_dir( $path ) )
            return;
        foreach ( scandir( $path ) as $name )
            if ( $name !== '.' && $name !== '..' )
                self::remove( $path . '/' . $name );
        rmdir( $path );
    }

    /** the installation this test belongs to */
    private static function installRoot()
    {
        return dirname( __DIR__, 5 );
    }

    /** @return string[]|null the tracked files of the installation, null without git */
    private static function trackedFiles( $pathspec = '' )
    {
        $root = self::installRoot();
        if ( !is_dir( $root . '/.git' ) && !is_file( $root . '/.git' ) )
            return null;
        $out = array();
        $rc = 1;
        @exec( 'git -C ' . escapeshellarg( $root ) . ' ls-files' . ( $pathspec !== '' ? ' -- ' . escapeshellarg( $pathspec ) : '' ) . ' 2>/dev/null', $out, $rc );
        return $rc === 0 && $out ? $out : null;
    }

    private static function languages( array $config = array() )
    {
        return new expHelpLanguage( $config + array( 'default' => 'en/us', 'directories' => array( 'en/us', 'de' ),
                                                     'map' => array( 'eng-US' => 'en/us', 'eng-GB' => 'en/us', 'ger-DE' => 'de', 'ger' => 'de' ),
                                                     'names' => array( 'en/us' => 'English', 'de' => 'German' ) ), self::$root );
    }

    private static function index( $language )
    {
        $tracked = array();
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::$root, FilesystemIterator::SKIP_DOTS ) );
        foreach ( $iterator as $file )
            $tracked[] = substr( $file->getPathname(), strlen( self::$root ) + 1 );
        sort( $tracked );
        $allowlist = new expHelpAllowlist( array( 'root' => self::$root, 'roots' => array( 'doc' ), 'tracked' => $tracked ) );
        $audience = new expHelpAudience( array( 'audienceRules' => array( 'doc/en/us/help/partners/*;partner' ),
                                                'categoryRules' => array( 'doc/en/us/help/partners/*;partnership' ) ) );
        return new expHelpDocumentIndex( $allowlist, array( 'cache_dir' => self::$root . '/cache', 'git_history' => false,
                                                            'audience' => $audience, 'languages' => self::languages(),
                                                            'language' => $language ) );
    }

    /** TD-01 */
    public function testOnlyLanguageDirectoriesBelowDoc()
    {
        $tracked = self::trackedFiles( 'doc' );
        if ( $tracked === null )
            $this->markTestSkipped( 'not a git checkout' );
        $languages = expHelpLanguage::fromIni( self::installRoot() );
        $stray = array();
        foreach ( $tracked as $path )
        {
            if ( strpos( $path, 'doc/changelogs/' ) === 0 || in_array( $path, self::DOC_ROOT_ALLOWED, true ) )
                continue;
            if ( $languages->split( $path ) === null )
                $stray[] = $path;
        }
        $this->assertSame( array(), $stray, 'every tracked file below doc/ is in a language directory, doc/changelogs/ or ' . implode( ', ', self::DOC_ROOT_ALLOWED ) );
        $this->assertContains( 'doc/en/us/features/6.0/help-system.md', $tracked );
        $this->assertContains( 'doc/changelogs/6.0/6.0.15.md', $tracked );
        foreach ( self::MOVED as $entry )
            $this->assertNotContains( 'doc/' . $entry, $tracked, 'doc/' . $entry . ' moved to doc/en/us/' );
    }

    /** TD-02 */
    public function testNoStaleReferencesToTheOldPaths()
    {
        $root = self::installRoot();
        if ( self::trackedFiles( 'doc/changelogs' ) === null )
            $this->markTestSkipped( 'not a git checkout' );
        $alternatives = implode( '|', array_map( function ( $e ) { return str_replace( '.', '\\.', $e ); }, self::MOVED ) );
        $out = array();
        $rc = 1;
        // git grep: the tracked files only, fast; -I skips binary files
        @exec( 'git -C ' . escapeshellarg( $root ) . ' grep -nIE ' . escapeshellarg( 'doc/(' . $alternatives . ')' ) . ' 2>/dev/null', $out, $rc );
        if ( $rc > 1 )
            $this->markTestSkipped( 'git grep failed' );
        $pattern = '#(?<![A-Za-z0-9_.\-])doc/(' . $alternatives . ')(?![A-Za-z0-9_\-]|\.[A-Za-z0-9])#';
        $stale = array();
        foreach ( $out as $line )
        {
            if ( !preg_match( '#^([^:]+):(\d+):(.*)$#s', $line, $m ) )
                continue;
            $file = $m[1];
            $skip = in_array( $file, self::DESCRIBE_THE_MOVE, true );
            foreach ( self::HISTORY_PREFIXES as $prefix )
                if ( strpos( $file, $prefix ) === 0 && !in_array( $file, self::HISTORY_EXCEPTIONS, true ) )
                    $skip = true;
            if ( $skip )
                continue;
            if ( !preg_match_all( $pattern, $m[3], $hits, PREG_OFFSET_CAPTURE ) )
                continue;
            foreach ( $hits[0] as $hit )
            {
                $before = substr( $m[3], 0, $hit[1] );
                // another repository's or extension's doc/ (extension/expai/doc/examples), or a URL of another project
                if ( preg_match( '#[A-Za-z0-9_.\-]/$#', $before ) && !preg_match( '#github\.com/se7enxweb/exponential/(blob|tree|raw|edit)/[^/\s]+/$#', $before ) )
                {
                    if ( !preg_match( '#(^|[\s"\'(<`$])\./$#', $before ) )
                        continue;
                }
                $stale[] = $file . ':' . $m[2] . ': ' . trim( substr( $m[3], max( 0, $hit[1] - 40 ), 120 ) );
            }
        }
        $this->assertSame( array(), array_slice( $stale, 0, 40 ), count( $stale ) . ' references name the documentation before it moved to doc/en/us/' );
    }

    /** TD-03 */
    public function testLocaleToLanguage()
    {
        $languages = self::languages();
        $this->assertSame( 'en/us', $languages->defaultDirectory() );
        $this->assertSame( array( 'en/us', 'de' ), $languages->directories() );
        $this->assertSame( 'en/us', $languages->forLocale( 'eng-US' ) );
        $this->assertSame( 'en/us', $languages->forLocale( 'eng-GB' ) );
        $this->assertSame( 'de', $languages->forLocale( 'ger-DE' ) );
        $this->assertSame( 'de', $languages->forLocale( 'ger-AT' ), 'a variant without an entry uses its language' );
        $this->assertSame( 'de', $languages->forLocale( 'ger-DE@euro' ) );
        $this->assertSame( 'en/us', $languages->forLocale( 'fre-FR' ), 'a language without a directory reads the original' );
        $this->assertSame( 'en/us', $languages->forLocale( '' ) );
        // a map entry that names a directory which is not configured is ignored
        $other = self::languages( array( 'map' => array( 'fre-FR' => 'fr' ) ) );
        $this->assertSame( 'en/us', $other->forLocale( 'fre-FR' ) );
        $this->assertFalse( $other->isDirectory( 'fr' ) );
        // the default is always a directory, and comes first
        $swapped = self::languages( array( 'directories' => array( 'de' ) ) );
        $this->assertSame( array( 'en/us', 'de' ), $swapped->directories() );
        $this->assertNull( expHelpLanguage::cleanDirectory( '../x' ) );
        $this->assertNull( expHelpLanguage::cleanDirectory( 'en/us/x' ) );
        $this->assertSame( 'en/us', expHelpLanguage::cleanDirectory( '/EN/US/' ) );
        $this->assertSame( 'en-US', expHelpLanguage::htmlLang( 'en/us' ) );
        $this->assertSame( 'de', expHelpLanguage::htmlLang( 'de' ) );
        $this->assertSame( 'rtl', expHelpLanguage::htmlDir( 'ar' ) );
        $this->assertSame( 'ltr', expHelpLanguage::htmlDir( 'en/us' ) );
        $this->assertSame( 'German', $languages->name( 'de' ) );
    }

    /**
     * The settings of one group of the shipped settings/exphelp.ini, read as text (the test bootstrap has no INI
     * reader): name => value, name => list for "Name[]=", name => map for "Name[key]=".
     */
    private static function shippedGroup( $group )
    {
        $text = (string)file_get_contents( self::installRoot() . '/settings/exphelp.ini' );
        $settings = array();
        $in = false;
        foreach ( preg_split( '/\R/', $text ) as $line )
        {
            if ( preg_match( '/^\[([^\]]+)\]\s*$/', $line, $m ) )
            {
                $in = $m[1] === $group;
                continue;
            }
            if ( !$in || $line === '' || $line[0] === '#' || !preg_match( '/^([A-Za-z0-9_]+)(\[([^\]]*)\])?=(.*)$/', $line, $m ) )
                continue;
            if ( $m[2] === '' )
                $settings[$m[1]] = $m[4];
            elseif ( $m[3] === '' )
                $settings[$m[1]] = isset( $settings[$m[1]] ) && is_array( $settings[$m[1]] ) && $m[4] !== '' ? array_merge( $settings[$m[1]], array( $m[4] ) ) : ( $m[4] === '' ? array() : array( $m[4] ) );
            else
                $settings[$m[1]][$m[3]] = $m[4];
        }
        return $settings;
    }

    /** TD-04 */
    public function testShippedSettings()
    {
        $group = self::shippedGroup( 'DocLanguages' );
        $this->assertSame( 'en/us', $group['Default'] );
        $this->assertSame( 'en/us', $group['Map']['eng-US'] );
        $this->assertSame( 'en/us', $group['Map']['eng-GB'] );
        $this->assertSame( 'de', $group['Map']['ger-DE'] );
        $this->assertSame( array( 'en/us', 'de' ), $group['Directories'] );
        $this->assertSame( 'English', $group['Names']['en/us'] );
        $this->assertSame( 'German', $group['Names']['de'] );
        $languages = new expHelpLanguage( array( 'default' => $group['Default'], 'directories' => $group['Directories'],
                                                 'map' => $group['Map'], 'names' => $group['Names'] ), self::installRoot() );
        $this->assertSame( 'de', $languages->forLocale( 'ger-DE' ) );
        $this->assertSame( 'de', $languages->forLocale( 'ger-CH' ) );
        $this->assertSame( 'en/us', $languages->forLocale( 'eng-GB' ) );
        // the built-in defaults are the shipped settings
        $this->assertSame( expHelpLanguage::DEFAULTS['map'], $group['Map'] );
        $this->assertSame( expHelpLanguage::DEFAULTS['directories'], $group['Directories'] );
        $this->assertContains( 'doc/README.md', self::shippedGroup( 'IndexSettings' )['ExcludePatterns'] );
        $this->assertContains( 'doc/README.md', expHelpAllowlist::DEFAULTS['excludePatterns'] );
    }

    /** TD-05 */
    public function testPathMap()
    {
        $languages = self::languages();
        $map = $languages->pathMap( 'de' );
        $this->assertSame( 'help/start.md', $map['hilfe/start.md'] );
        $this->assertArrayNotHasKey( '../escape.md', $map, 'a path that leaves the directory is left out' );
        $this->assertArrayNotHasKey( 'hilfe/bad.md', $map, 'a value that is not a path is left out' );
        $this->assertSame( array(), $languages->pathMap( 'en/us' ), 'the original has no map' );
        $this->assertSame( 'help/start.md', $languages->canonical( 'de', 'hilfe/start.md' ) );
        $this->assertSame( 'help/same-name.md', $languages->canonical( 'de', 'help/same-name.md' ), 'an untranslated name is the id' );
        $this->assertSame( 'hilfe/start.md', $languages->translated( 'de', 'help/start.md' ) );
        $this->assertSame( 'help/same-name.md', $languages->translated( 'de', 'help/same-name.md' ) );
        $this->assertSame( array( 'de', 'hilfe/start.md' ), $languages->split( 'doc/de/hilfe/start.md' ) );
        $this->assertSame( array( 'en/us', 'help/start.md' ), $languages->split( 'doc/en/us/help/start.md' ) );
        $this->assertNull( $languages->split( 'doc/changelogs/6.0.md' ) );
        $this->assertNull( $languages->split( 'doc/fr/help/start.md' ), 'a directory that is not configured is no language' );
        $this->assertSame( 'help/start.md', $languages->idOfValue( 'doc/en/us/help/start.md' ) );
        $this->assertSame( 'help/start.md', $languages->idOfValue( 'en/us/help/start.md' ) );
        $this->assertSame( 'help/start.md', $languages->idOfValue( 'doc/de/hilfe/start.md' ) );
        $this->assertSame( 'help/start.md', $languages->idOfValue( 'help/start.md' ) );
        $this->assertSame( 'changelogs/6.0.md', $languages->idOfValue( 'doc/changelogs/6.0.md' ) );
        $languages->setPathMap( 'de', array( 'x/y.md' => 'help/start.md' ) );
        $this->assertSame( 'x/y.md', $languages->translated( 'de', 'help/start.md' ) );
        $languages->setPathMap( 'de', null );
        $this->assertSame( 'hilfe/start.md', $languages->translated( 'de', 'help/start.md' ), 'read from the file again' );
    }

    /** TD-06 */
    public function testIndexPerLanguageWithFallback()
    {
        $german = self::index( 'de' );
        $this->assertSame( 'de', $german->language() );
        $this->assertFalse( $german->isDefaultLanguage() );
        $start = $german->document( 'help/start.md' );
        $this->assertSame( 'doc/de/hilfe/start.md', $start['path'] );
        $this->assertSame( 'Hier beginnen', $start['title'] );
        $this->assertSame( 'de', $start['language'] );
        $this->assertFalse( $start['fallback'] );
        $other = $german->document( 'help/other.md' );
        $this->assertSame( 'doc/en/us/help/other.md', $other['path'], 'not translated: the original' );
        $this->assertTrue( $other['fallback'] );
        $this->assertTrue( $german->document( 'changelogs/6.0.md' )['fallback'], 'the changelogs are not translated' );
        $this->assertNull( $german->document( 'hilfe/start.md' ), 'a translated file name is not an id' );
        $this->assertNull( $german->document( 'de/hilfe/start.md' ) );
        $this->assertStringEndsWith( '/cache/lang-de', (string)$german->cacheDir() );

        $english = self::index( 'en/us' );
        $this->assertTrue( $english->isDefaultLanguage() );
        $this->assertSame( 'doc/en/us/help/start.md', $english->document( 'help/start.md' )['path'] );
        $this->assertSame( self::$root . '/cache', $english->cacheDir() );
        foreach ( $english->documents() as $id => $document )
        {
            $this->assertFalse( $document['fallback'], $id );
            $this->assertStringStartsNotWith( 'doc/de/', $document['path'], 'the original never reads a translation' );
            $this->assertStringStartsNotWith( 'doc/fr/', $document['path'], 'nor an unconfigured directory' );
        }
        $this->assertSame( array_keys( $english->documents() ), array_keys( $german->documents() ), 'the same pages in both' );
        $this->assertNotSame( $english->signature(), $german->signature() );
        // an unknown language is the default
        $this->assertSame( 'en/us', self::index( 'xx' )->language() );
    }

    /** TD-07 */
    public function testSameIdEveryLanguageAndLinks()
    {
        $german = self::index( 'de' );
        $english = self::index( 'en/us' );
        $this->assertSame( 'doc/de/hilfe/start.md', $german->pathOf( 'help/start.md' ) );
        $this->assertSame( 'doc/en/us/help/start.md', $english->pathOf( 'help/start.md' ) );
        $this->assertSame( 'doc/en/us/help/other.md', $german->pathOf( 'help/other.md' ) );
        $this->assertSame( 'doc/changelogs/6.0.md', $german->pathOf( 'changelogs/6.0.md' ) );
        $this->assertSame( 'help/start.md', $german->idOf( 'doc/de/hilfe/start.md' ) );
        $this->assertSame( 'help/start.md', $german->idOf( 'doc/en/us/help/start.md' ) );
        $this->assertSame( 'changelogs/6.0.md', $german->idOf( 'doc/changelogs/6.0.md' ) );
        $this->assertTrue( $german->isAllowed( 'help/img/shot.png' ), 'an image of the original serves the translation' );
        $this->assertNotNull( $german->asset( 'help/img/shot.png' ) );
        // links resolve against the file the page was read from
        $this->assertSame( 'help/other.md', expHelpLinks::resolveId( '../hilfe/andere.md', 'help/start.md', $german ) );
        $this->assertSame( 'help/img/shot.png', expHelpLinks::resolveId( '../../en/us/help/img/shot.png', 'help/start.md', $german ) );
        $this->assertSame( 'help/other.md', expHelpLinks::resolveId( 'other.md', 'help/start.md', $english ) );
        $this->assertSame( 'changelogs/6.0.md', expHelpLinks::resolveId( '../../../changelogs/6.0.md', 'help/start.md', $english ) );
        $this->assertSame( 'help/other.md', expHelpLinks::resolveId( '/doc/en/us/help/other.md', 'help/start.md', $english ) );
        $this->assertNull( expHelpLinks::resolveId( '../../../../../../etc/passwd', 'help/start.md', $english ) );
        $this->assertSame( 'help/start.md', expHelpLinks::resolvePath( '/doc/en/us/help/start.md', 'x.md' ) );
    }

    /** TD-08 */
    public function testSearchPerLanguage()
    {
        $german = new expHelpSearch( self::index( 'de' ), array( 'cacheDir' => null ) );
        $ids = array_map( function ( $hit ) { return $hit['id']; }, $german->search( 'Zebrafink' )['results'] );
        $this->assertSame( array( 'help/start.md' ), $ids, 'the translated text is found' );
        $ids = array_map( function ( $hit ) { return $hit['id']; }, $german->search( 'quagga' )['results'] );
        $this->assertSame( array( 'help/other.md' ), $ids, 'an untranslated page is found in the original' );
        $this->assertSame( array(), $german->search( 'Welcome' )['results'], 'not the original of a translated page' );
        $english = new expHelpSearch( self::index( 'en/us' ), array( 'cacheDir' => null ) );
        $this->assertSame( array(), $english->search( 'Zebrafink' )['results'], 'the original never finds a translation' );
    }

    /** TD-09 */
    public function testTranslationClassifiedLikeItsOriginal()
    {
        $german = self::index( 'de' );
        $page = $german->document( 'help/partners/program.md' );
        $this->assertSame( 'doc/de/hilfe/partner/programm.md', $page['path'] );
        $this->assertSame( array( 'partner' ), $page['audience'] );
        $this->assertSame( 'partnership', $page['category'] );
        $this->assertSame( 'doc/en/us/help/partners/program.md', $german->rulePath( 'doc/de/hilfe/partner/programm.md' ) );
        $this->assertSame( 'doc/changelogs/6.0.md', $german->rulePath( 'doc/changelogs/6.0.md' ) );
    }
}
