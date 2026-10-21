<?php
/**
 * The translated documentation (doc/eng-us/bc/6.0/translated-documentation.md): the language directories below doc/,
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
 *  TD-10  path maps (.paths.json, .dirnames.json) merge as a union: sorted, the same file whatever the order, a
 *         conflict kept from the first map and reported, a git conflict read as its two sides; directory names
 *         follow .dirnames.json
 *  TD-11  how much of the help each language has, the "Reading in" choices of the switch, and /(lang)/ for the original
 *  TD-12  a translated page rendered from its file reaches the images and pages of the original's directory
 *  TD-14  no tracked text file names the language directories of 6.0.14 (doc/en/us, doc/de; doc/eng-us and
 *         doc/ger-de since 6.0.15), apart from the history and the code that still answers their old addresses
 *  TD-13  an anchor written for another language's version of a page maps to the shown page's heading at the same
 *         position, when rendered and in the link check, in both directions
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpTranslatedDocumentationTest extends PHPUnit\Framework\TestCase
{
    /** The entries that moved from doc/ to doc/eng-us/ (2026-10-20) */
    const MOVED = array( 'INSTALL.md', 'LICENSE-GFDL-1.3.txt', 'README-LICENSE.md', 'bc', 'branch-overview.md', 'doxygen',
                         'examples', 'features', 'glossary.md', 'guides', 'help', 'history', 'install', 'languages.md',
                         'release-overview', 'shop', 'specifications', 'standards' );

    /** What may be tracked directly below doc/ besides the language directories */
    const DOC_ROOT_ALLOWED = array( 'doc/README.md' );

    /** Files whose text may keep the old paths: they record history (path prefixes), the German translation of the
     *  history pages (doc/ger-de/geschichte/) like the English ones, since it quotes the same commit subjects */
    const HISTORY_PREFIXES = array( 'doc/changelogs/', 'doc/eng-us/history/', 'doc/ger-de/geschichte/', 'share/filelist.md5' );

    /** ... except the changelog of the release being prepared */
    const HISTORY_EXCEPTIONS = array( 'doc/changelogs/6.0/6.0.15.md' );

    /** Files that describe the move itself and name the old paths on purpose */
    const DESCRIBE_THE_MOVE = array( 'doc/eng-us/bc/6.0/translated-documentation.md',
                                     'doc/ger-de/bc/6.0/uebersetzte-dokumentation.md',
                                     'tests/tests/kernel/classes/help/expHelpTranslatedDocumentationTest.php' );

    /** Files that name doc/en/us or doc/de on purpose: the old directories the help still answers for */
    const OLD_DIRECTORIES_ON_PURPOSE = array( 'kernel/classes/help/exphelplanguage.php', 'settings/exphelp.ini',
                                              'kernel/private/classes/views/exphelp/viewer.php' );

    /** @var string the fixture installation */
    private static $root = '';

    public static function setUpBeforeClass(): void
    {
        self::$root = sys_get_temp_dir() . '/exphelp-lang-' . bin2hex( random_bytes( 6 ) );
        $files = array(
            'doc/eng-us/help/start.md' => "# Start here\n\nWelcome. See [the other page](other.md) and [the changes](../../changelogs/6.0.md).\n",
            'doc/eng-us/help/other.md' => "# The other page\n\nOnly in English: quagga.\n",
            'doc/eng-us/help/partners/program.md' => "# Partner program\n\nText.\n",
            'doc/eng-us/help/img/shot.png' => base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==' ),
            'doc/ger-de/hilfe/start.md' => "# Hier beginnen\n\nWillkommen, Zebrafink. Siehe [die andere Seite](../hilfe/andere.md) und [das Bild](../../eng-us/help/img/shot.png).\n",
            'doc/ger-de/hilfe/partner/programm.md' => "# Partnerprogramm\n\nText.\n",
            'doc/ger-de/.paths.json' => json_encode( array( 'hilfe/start.md' => 'help/start.md', 'hilfe/andere.md' => 'help/other.md',
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
        return new expHelpLanguage( $config + array( 'default' => 'eng-us', 'directories' => array( 'eng-us', 'ger-de' ),
                                                     'map' => array( 'eng-US' => 'eng-us', 'eng-GB' => 'eng-us', 'ger-DE' => 'ger-de', 'ger' => 'ger-de' ),
                                                     'names' => array( 'eng-us' => 'English', 'ger-de' => 'German' ) ), self::$root );
    }

    private static function index( $language )
    {
        $tracked = array();
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::$root, FilesystemIterator::SKIP_DOTS ) );
        foreach ( $iterator as $file )
            $tracked[] = substr( $file->getPathname(), strlen( self::$root ) + 1 );
        sort( $tracked );
        $allowlist = new expHelpAllowlist( array( 'root' => self::$root, 'roots' => array( 'doc' ), 'tracked' => $tracked ) );
        $audience = new expHelpAudience( array( 'audienceRules' => array( 'doc/eng-us/help/partners/*;partner' ),
                                                'categoryRules' => array( 'doc/eng-us/help/partners/*;partnership' ) ) );
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
        $this->assertContains( 'doc/eng-us/features/6.0/help-system.md', $tracked );
        $this->assertContains( 'doc/changelogs/6.0/6.0.15.md', $tracked );
        foreach ( self::MOVED as $entry )
            $this->assertNotContains( 'doc/' . $entry, $tracked, 'doc/' . $entry . ' moved to doc/eng-us/' );
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
        $this->assertSame( array(), array_slice( $stale, 0, 40 ), count( $stale ) . ' references name the documentation before it moved to doc/eng-us/' );
    }

    /** TD-14 */
    public function testNoStaleReferencesToTheOldLanguageDirectories()
    {
        $root = self::installRoot();
        if ( self::trackedFiles( 'doc/changelogs' ) === null )
            $this->markTestSkipped( 'not a git checkout' );
        $out = array();
        $rc = 1;
        @exec( 'git -C ' . escapeshellarg( $root ) . ' grep -nIE ' . escapeshellarg( 'doc/(en/us|de)([/"\')`\s]|$)' ) . ' 2>/dev/null', $out, $rc );
        if ( $rc > 1 )
            $this->markTestSkipped( 'git grep failed' );
        $stale = array();
        foreach ( $out as $line )
        {
            if ( !preg_match( '#^([^:]+):(\d+):(.*)$#s', $line, $m ) )
                continue;
            $file = $m[1];
            $skip = in_array( $file, self::DESCRIBE_THE_MOVE, true ) || in_array( $file, self::OLD_DIRECTORIES_ON_PURPOSE, true );
            foreach ( self::HISTORY_PREFIXES as $prefix )
                if ( strpos( $file, $prefix ) === 0 && !in_array( $file, self::HISTORY_EXCEPTIONS, true ) )
                    $skip = true;
            if ( $skip )
                continue;
            // another repository's or extension's doc/de (extension/foo/doc/de/...), unless it is this repository's URL
            if ( !preg_match_all( '#(?<![A-Za-z0-9_.\-])doc/(en/us|de)(?![A-Za-z0-9_\-])#', $m[3], $hits, PREG_OFFSET_CAPTURE ) )
                continue;
            foreach ( $hits[0] as $hit )
            {
                $before = substr( $m[3], 0, $hit[1] );
                if ( preg_match( '#[A-Za-z0-9_.\-]/$#', $before ) && !preg_match( '#github\.com/se7enxweb/exponential/(blob|tree|raw|edit)/[^/\s]+/$#', $before ) )
                    continue;
                $stale[] = $file . ':' . $m[2] . ': ' . trim( substr( $m[3], max( 0, $hit[1] - 40 ), 120 ) );
            }
        }
        $this->assertSame( array(), array_slice( $stale, 0, 40 ), count( $stale ) . ' references name doc/en/us or doc/de, which are doc/eng-us and doc/ger-de since 6.0.15' );
    }

    /** TD-03 */
    public function testLocaleToLanguage()
    {
        $languages = self::languages();
        $this->assertSame( 'eng-us', $languages->defaultDirectory() );
        $this->assertSame( array( 'eng-us', 'ger-de' ), $languages->directories() );
        $this->assertSame( 'eng-us', $languages->forLocale( 'eng-US' ) );
        $this->assertSame( 'eng-us', $languages->forLocale( 'eng-GB' ) );
        $this->assertSame( 'ger-de', $languages->forLocale( 'ger-DE' ) );
        $this->assertSame( 'ger-de', $languages->forLocale( 'ger-AT' ), 'a variant without an entry uses its language' );
        $this->assertSame( 'ger-de', $languages->forLocale( 'ger-DE@euro' ) );
        $this->assertSame( 'eng-us', $languages->forLocale( 'fre-FR' ), 'a language without a directory reads the original' );
        $this->assertSame( 'eng-us', $languages->forLocale( '' ) );
        // a map entry that names a directory which is not configured is ignored
        $other = self::languages( array( 'map' => array( 'fre-FR' => 'fr' ) ) );
        $this->assertSame( 'eng-us', $other->forLocale( 'fre-FR' ) );
        $this->assertFalse( $other->isDirectory( 'fr' ) );
        // the default is always a directory, and comes first
        $swapped = self::languages( array( 'directories' => array( 'ger-de' ) ) );
        $this->assertSame( array( 'eng-us', 'ger-de' ), $swapped->directories() );
        $this->assertNull( expHelpLanguage::cleanDirectory( '../x' ) );
        $this->assertNull( expHelpLanguage::cleanDirectory( 'eng-us/x' ) );
        $this->assertSame( 'eng-us', expHelpLanguage::cleanDirectory( '/ENG-US/' ) );
        $this->assertNull( expHelpLanguage::cleanDirectory( 'en/us' ), 'the old directories are no directories' );
        $this->assertSame( 'eng-US', expHelpLanguage::localeOf( 'eng-us' ) );
        $this->assertSame( 'ger-de', expHelpLanguage::directoryOfLocale( 'ger-DE@euro' ) );
        $this->assertSame( 'en-US', expHelpLanguage::htmlLang( 'eng-us' ) );
        $this->assertSame( 'de-DE', expHelpLanguage::htmlLang( 'ger-de' ) );
        $this->assertSame( 'nb-NO', expHelpLanguage::htmlLang( 'nor-no' ), 'the locale file\'s ContentLanguage' );
        $this->assertSame( 'de', expHelpLanguage::htmlLang( 'de' ), 'an old directory keeps its old code' );
        $this->assertSame( 'rtl', expHelpLanguage::htmlDir( 'ara-sa' ) );
        $this->assertSame( 'rtl', expHelpLanguage::htmlDir( 'ar' ) );
        // the directory is the locale in lower case, without an entry in Map[]; a locale with no directory of its own
        // reads the first directory of its language, Map[] aliases come first
        $direct = self::languages( array( 'map' => array() ) );
        $this->assertSame( 'ger-de', $direct->forLocale( 'ger-DE' ) );
        $this->assertSame( 'ger-de', $direct->forLocale( 'ger-CH' ) );
        $this->assertSame( 'eng-us', $direct->forLocale( 'eng-GB' ) );
        $three = self::languages( array( 'directories' => array( 'ger-de', 'ger-at' ), 'map' => array( 'ger-CH' => 'ger-at' ) ) );
        $this->assertSame( 'ger-at', $three->forLocale( 'ger-AT' ) );
        $this->assertSame( 'ger-at', $three->forLocale( 'ger-CH' ), 'an alias' );
        $this->assertSame( 'ger-de', $three->forLocale( 'ger-LU' ), 'the first directory of the language' );
        $this->assertSame( array( 'eng-US', 'ger-DE', 'ger-AT', 'ger-CH' ), $three->localeOrder() );
        // the old directories and ?lang= keys name today's
        $this->assertSame( 'eng-us', $languages->resolveDirectory( 'en-us' ) );
        $this->assertSame( 'eng-us', $languages->resolveDirectory( 'en/us' ) );
        $this->assertSame( 'ger-de', $languages->resolveDirectory( 'de' ) );
        $this->assertSame( 'ger-de', $languages->resolveDirectory( 'GER-DE' ) );
        $this->assertNull( $languages->resolveDirectory( 'fr' ) );
        $this->assertNull( $languages->resolveDirectory( '../en-us' ) );
        $this->assertSame( 'ltr', expHelpLanguage::htmlDir( 'eng-us' ) );
        $this->assertSame( 'German', $languages->name( 'ger-de' ) );
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
        $this->assertSame( 'eng-us', $group['Default'] );
        $this->assertArrayNotHasKey( 'eng-US', $group['Map'], 'a locale reads its own directory without an entry' );
        $this->assertSame( 'eng-us', $group['Map']['eng-GB'] );
        $this->assertSame( array( 'eng-us', 'ger-de' ), $group['Directories'] );
        $this->assertSame( 'English', $group['Names']['eng-us'] );
        $this->assertSame( 'German', $group['Names']['ger-de'] );
        $this->assertSame( array( 'en/us' => 'eng-us', 'de' => 'ger-de' ), $group['Legacy'] );
        $languages = new expHelpLanguage( array( 'default' => $group['Default'], 'directories' => $group['Directories'],
                                                 'map' => $group['Map'], 'names' => $group['Names'], 'legacy' => $group['Legacy'] ), self::installRoot() );
        $this->assertSame( 'ger-de', $languages->forLocale( 'ger-DE' ) );
        $this->assertSame( 'ger-de', $languages->forLocale( 'ger-CH' ) );
        $this->assertSame( 'eng-us', $languages->forLocale( 'eng-GB' ) );
        $this->assertSame( 'eng-us', $languages->forLocale( 'eng-US' ) );
        // the built-in defaults are the shipped settings
        $this->assertSame( expHelpLanguage::DEFAULTS['map'], $group['Map'] );
        $this->assertSame( expHelpLanguage::DEFAULTS['directories'], $group['Directories'] );
        $this->assertSame( expHelpLanguage::DEFAULTS['legacy'], $group['Legacy'] );
        $this->assertSame( expHelpLanguage::DEFAULTS['names'], $group['Names'] );
        $this->assertContains( 'doc/README.md', self::shippedGroup( 'IndexSettings' )['ExcludePatterns'] );
        $this->assertContains( 'doc/README.md', expHelpAllowlist::DEFAULTS['excludePatterns'] );
    }

    /** TD-05 */
    public function testPathMap()
    {
        $languages = self::languages();
        $map = $languages->pathMap( 'ger-de' );
        $this->assertSame( 'help/start.md', $map['hilfe/start.md'] );
        $this->assertArrayNotHasKey( '../escape.md', $map, 'a path that leaves the directory is left out' );
        $this->assertArrayNotHasKey( 'hilfe/bad.md', $map, 'a value that is not a path is left out' );
        $this->assertSame( array(), $languages->pathMap( 'eng-us' ), 'the original has no map' );
        $this->assertSame( 'help/start.md', $languages->canonical( 'ger-de', 'hilfe/start.md' ) );
        $this->assertSame( 'help/same-name.md', $languages->canonical( 'ger-de', 'help/same-name.md' ), 'an untranslated name is the id' );
        $this->assertSame( 'hilfe/start.md', $languages->translated( 'ger-de', 'help/start.md' ) );
        $this->assertSame( 'help/same-name.md', $languages->translated( 'ger-de', 'help/same-name.md' ) );
        $this->assertSame( array( 'ger-de', 'hilfe/start.md' ), $languages->split( 'doc/ger-de/hilfe/start.md' ) );
        $this->assertSame( array( 'eng-us', 'help/start.md' ), $languages->split( 'doc/eng-us/help/start.md' ) );
        $this->assertNull( $languages->split( 'doc/changelogs/6.0.md' ) );
        $this->assertNull( $languages->split( 'doc/fr/help/start.md' ), 'a directory that is not configured is no language' );
        $this->assertSame( 'help/start.md', $languages->idOfValue( 'doc/eng-us/help/start.md' ) );
        $this->assertSame( 'help/start.md', $languages->idOfValue( 'eng-us/help/start.md' ) );
        $this->assertSame( 'help/start.md', $languages->idOfValue( 'doc/ger-de/hilfe/start.md' ) );
        $this->assertSame( 'help/start.md', $languages->idOfValue( 'help/start.md' ) );
        $this->assertSame( 'changelogs/6.0.md', $languages->idOfValue( 'doc/changelogs/6.0.md' ) );
        // the old directories of 6.0.14 (exphelp/viewer/en/us/<id>, doc/en/us/<id>, de/<translated path>) name the page
        $this->assertSame( 'help/start.md', $languages->idOfValue( 'doc/en/us/help/start.md' ) );
        $this->assertSame( 'help/start.md', $languages->idOfValue( 'en/us/help/start.md' ) );
        $this->assertSame( 'help/start.md', $languages->idOfValue( 'de/hilfe/start.md' ) );
        $this->assertSame( 'help/start.md', $languages->idOfValue( 'doc/de/hilfe/start.md' ) );
        $this->assertSame( 'help/other.md', $languages->idOfValue( 'de/help/other.md' ), 'an untranslated name' );
        $this->assertSame( array( 'ger-de', 'hilfe/start.md' ), $languages->splitLegacy( 'doc/de/hilfe/start.md' ) );
        $this->assertNull( $languages->split( 'doc/en/us/help/start.md' ), 'no file is read from an old directory' );
        $this->assertTrue( $languages->isUnconfiguredLanguagePath( 'doc/en/us/help/start.md' ), 'an old copy left on disk is no page' );
        $this->assertTrue( $languages->isUnconfiguredLanguagePath( 'doc/de/hilfe/start.md' ) );
        $this->assertTrue( $languages->isUnconfiguredLanguagePath( 'doc/fre-fr/help/start.md' ) );
        $this->assertFalse( $languages->isUnconfiguredLanguagePath( 'doc/changelogs/6.0.md' ) );
        $languages->setPathMap( 'ger-de', array( 'x/y.md' => 'help/start.md' ) );
        $this->assertSame( 'x/y.md', $languages->translated( 'ger-de', 'help/start.md' ) );
        $languages->setPathMap( 'ger-de', null );
        $this->assertSame( 'hilfe/start.md', $languages->translated( 'ger-de', 'help/start.md' ), 'read from the file again' );
    }

    /** TD-06 */
    public function testIndexPerLanguageWithFallback()
    {
        $german = self::index( 'ger-de' );
        $this->assertSame( 'ger-de', $german->language() );
        $this->assertFalse( $german->isDefaultLanguage() );
        $start = $german->document( 'help/start.md' );
        $this->assertSame( 'doc/ger-de/hilfe/start.md', $start['path'] );
        $this->assertSame( 'Hier beginnen', $start['title'] );
        $this->assertSame( 'ger-de', $start['language'] );
        $this->assertFalse( $start['fallback'] );
        $other = $german->document( 'help/other.md' );
        $this->assertSame( 'doc/eng-us/help/other.md', $other['path'], 'not translated: the original' );
        $this->assertTrue( $other['fallback'] );
        $this->assertTrue( $german->document( 'changelogs/6.0.md' )['fallback'], 'the changelogs are not translated' );
        $this->assertNull( $german->document( 'hilfe/start.md' ), 'a translated file name is not an id' );
        $this->assertNull( $german->document( 'ger-de/hilfe/start.md' ) );
        $this->assertStringEndsWith( '/cache/lang-ger-de', (string)$german->cacheDir() );

        $english = self::index( 'eng-us' );
        $this->assertTrue( $english->isDefaultLanguage() );
        $this->assertSame( 'doc/eng-us/help/start.md', $english->document( 'help/start.md' )['path'] );
        $this->assertSame( self::$root . '/cache', $english->cacheDir() );
        foreach ( $english->documents() as $id => $document )
        {
            $this->assertFalse( $document['fallback'], $id );
            $this->assertStringStartsNotWith( 'doc/ger-de/', $document['path'], 'the original never reads a translation' );
            $this->assertStringStartsNotWith( 'doc/fr/', $document['path'], 'nor an unconfigured directory' );
        }
        $this->assertSame( array_keys( $english->documents() ), array_keys( $german->documents() ), 'the same pages in both' );
        $this->assertNotSame( $english->signature(), $german->signature() );
        // an unknown language is the default
        $this->assertSame( 'eng-us', self::index( 'xx' )->language() );
    }

    /** TD-07 */
    public function testSameIdEveryLanguageAndLinks()
    {
        $german = self::index( 'ger-de' );
        $english = self::index( 'eng-us' );
        $this->assertSame( 'doc/ger-de/hilfe/start.md', $german->pathOf( 'help/start.md' ) );
        $this->assertSame( 'doc/eng-us/help/start.md', $english->pathOf( 'help/start.md' ) );
        $this->assertSame( 'doc/eng-us/help/other.md', $german->pathOf( 'help/other.md' ) );
        $this->assertSame( 'doc/changelogs/6.0.md', $german->pathOf( 'changelogs/6.0.md' ) );
        $this->assertSame( 'help/start.md', $german->idOf( 'doc/ger-de/hilfe/start.md' ) );
        $this->assertSame( 'help/start.md', $german->idOf( 'doc/eng-us/help/start.md' ) );
        $this->assertSame( 'changelogs/6.0.md', $german->idOf( 'doc/changelogs/6.0.md' ) );
        $this->assertTrue( $german->isAllowed( 'help/img/shot.png' ), 'an image of the original serves the translation' );
        $this->assertNotNull( $german->asset( 'help/img/shot.png' ) );
        // links resolve against the file the page was read from
        $this->assertSame( 'help/other.md', expHelpLinks::resolveId( '../hilfe/andere.md', 'help/start.md', $german ) );
        $this->assertSame( 'help/img/shot.png', expHelpLinks::resolveId( '../../eng-us/help/img/shot.png', 'help/start.md', $german ) );
        $this->assertSame( 'help/other.md', expHelpLinks::resolveId( 'other.md', 'help/start.md', $english ) );
        $this->assertSame( 'changelogs/6.0.md', expHelpLinks::resolveId( '../../changelogs/6.0.md', 'help/start.md', $english ) );
        $this->assertSame( 'help/other.md', expHelpLinks::resolveId( '/doc/eng-us/help/other.md', 'help/start.md', $english ) );
        $this->assertNull( expHelpLinks::resolveId( '../../../../../../etc/passwd', 'help/start.md', $english ) );
        $this->assertSame( 'help/start.md', expHelpLinks::resolvePath( '/doc/eng-us/help/start.md', 'x.md' ) );
    }

    /** TD-08 */
    public function testSearchPerLanguage()
    {
        $german = new expHelpSearch( self::index( 'ger-de' ), array( 'cacheDir' => null ) );
        $ids = array_map( function ( $hit ) { return $hit['id']; }, $german->search( 'Zebrafink' )['results'] );
        $this->assertSame( array( 'help/start.md' ), $ids, 'the translated text is found' );
        $ids = array_map( function ( $hit ) { return $hit['id']; }, $german->search( 'quagga' )['results'] );
        $this->assertSame( array( 'help/other.md' ), $ids, 'an untranslated page is found in the original' );
        $this->assertSame( array(), $german->search( 'Welcome' )['results'], 'not the original of a translated page' );
        $english = new expHelpSearch( self::index( 'eng-us' ), array( 'cacheDir' => null ) );
        $this->assertSame( array(), $english->search( 'Zebrafink' )['results'], 'the original never finds a translation' );
    }

    /** TD-09 */
    public function testTranslationClassifiedLikeItsOriginal()
    {
        $german = self::index( 'ger-de' );
        $page = $german->document( 'help/partners/program.md' );
        $this->assertSame( 'doc/ger-de/hilfe/partner/programm.md', $page['path'] );
        $this->assertSame( array( 'partner' ), $page['audience'] );
        $this->assertSame( 'partnership', $page['category'] );
        $this->assertSame( 'doc/eng-us/help/partners/program.md', $german->rulePath( 'doc/ger-de/hilfe/partner/programm.md' ) );
        $this->assertSame( 'doc/changelogs/6.0.md', $german->rulePath( 'doc/changelogs/6.0.md' ) );
    }

    /** TD-10 */
    public function testPathMapsMergeAsAUnion()
    {
        $ours = array( 'hilfe/start.md' => 'help/start.md', 'hilfe/suchen.md' => 'help/searching.md' );
        $theirs = array( 'hilfe/suchen.md' => 'help/searching.md', 'anleitungen/caches.md' => 'guides/caches.md' );
        $merge = expHelpLanguage::mergePathMaps( array( $ours, $theirs ) );
        $this->assertSame( array( 'anleitungen/caches.md' => 'guides/caches.md', 'hilfe/start.md' => 'help/start.md',
                                  'hilfe/suchen.md' => 'help/searching.md' ), $merge['map'], 'every entry of both, sorted' );
        $this->assertSame( array(), $merge['conflicts'] );
        $this->assertSame( $merge['map'], expHelpLanguage::mergePathMaps( array( $theirs, $ours ) )['map'], 'the order of the sides does not matter' );
        $this->assertSame( expHelpLanguage::encodePathMap( $merge['map'] ),
                           expHelpLanguage::encodePathMap( expHelpLanguage::mergePathMaps( array( $theirs, $ours ) )['map'] ), 'byte for byte' );

        // a key with two values and a page under two names: the first map wins, both are reported
        $merge = expHelpLanguage::mergePathMaps( array( array( 'hilfe/a.md' => 'help/a.md' ),
                                                        array( 'hilfe/a.md' => 'help/b.md', 'hilfe/a2.md' => 'help/a.md' ) ) );
        $this->assertSame( array( 'hilfe/a.md' => 'help/a.md' ), $merge['map'] );
        $this->assertCount( 2, $merge['conflicts'] );
        // malformed entries are left out and reported
        $merge = expHelpLanguage::mergePathMaps( array( array( '../x.md' => 'help/x.md', 'hilfe/y.md' => 7, 'hilfe/z.md' => 'help//z.md', 'ok.md' => 'help/ok.md' ), 'nonsense' ) );
        $this->assertSame( array( 'ok.md' => 'help/ok.md' ), $merge['map'] );
        $this->assertCount( 4, $merge['invalid'] );

        // the file is sorted, one entry per line, slashes unescaped, final newline
        $this->assertSame( "{\n    \"a/b.md\": \"x/y.md\",\n    \"b.md\": \"z.md\"\n}\n", expHelpLanguage::encodePathMap( array( 'b.md' => 'z.md', 'a/b.md' => 'x/y.md' ) ) );
        $this->assertSame( "{}\n", expHelpLanguage::encodePathMap( array() ) );

        // a file git left with conflict markers is read as its two sides, with or without a diff3 base
        $conflicted = "{\n    \"a.md\": \"help/a.md\",\n<<<<<<< HEAD\n    \"b.md\": \"help/b.md\",\n=======\n    \"c.md\": \"help/c.md\",\n>>>>>>> origin/main\n    \"d.md\": \"help/d.md\"\n}\n";
        $sides = expHelpLanguage::decodeMapSides( $conflicted );
        $this->assertSame( array( 'a.md' => 'help/a.md', 'b.md' => 'help/b.md', 'd.md' => 'help/d.md' ), $sides[0] );
        $this->assertSame( array( 'a.md' => 'help/a.md', 'c.md' => 'help/c.md', 'd.md' => 'help/d.md' ), $sides[1] );
        $this->assertSame( array( 'a.md', 'b.md', 'c.md', 'd.md' ), array_keys( expHelpLanguage::mergePathMaps( $sides )['map'] ) );
        $diff3 = "{\n<<<<<<< ours\n    \"b.md\": \"help/b.md\",\n    \"z.md\": \"help/z.md\"\n||||||| base\n    \"z.md\": \"help/z.md\"\n=======\n    \"c.md\": \"help/c.md\",\n    \"z.md\": \"help/z.md\"\n>>>>>>> theirs\n}\n";
        $this->assertSame( array( 'b.md', 'c.md', 'z.md' ), array_keys( expHelpLanguage::mergePathMaps( expHelpLanguage::decodeMapSides( $diff3 ) )['map'] ) );
        // the last entry of a side that lost its comma, or kept one too many, is read all the same
        $commas = "{\n<<<<<<< HEAD\n    \"b.md\": \"help/b.md\"\n=======\n    \"c.md\": \"help/c.md\",\n>>>>>>> theirs\n}\n";
        $sides = expHelpLanguage::decodeMapSides( $commas );
        $this->assertSame( array( 'b.md' => 'help/b.md' ), $sides[0] );
        $this->assertSame( array( 'c.md' => 'help/c.md' ), $sides[1] );
        $this->assertSame( array( '{"a":"b"}' ), expHelpLanguage::conflictSides( '{"a":"b"}' ), 'no markers: the file itself' );

        // every directory is named as .dirnames.json says, or keeps its name
        $dirnames = array( 'help' => 'hilfe', 'guides' => 'anleitungen' );
        $this->assertSame( array(), expHelpLanguage::pathsAgainstDirnames(
            array( 'hilfe/start.md' => 'help/start.md', 'anleitungen/caches.md' => 'guides/caches.md', 'readme.md' => 'README.md',
                   'hilfe/install/x.md' => 'help/install/x.md' ), $dirnames ) );
        $this->assertCount( 2, expHelpLanguage::pathsAgainstDirnames(
            array( 'help/start.md' => 'help/start.md', 'hilfe/anleitung/x.md' => 'help/guides/x.md' ), $dirnames ) );
    }

    /** TD-12 */
    public function testTranslatedPageReachesTheOriginalsImagesWhenRendered()
    {
        $resolver = function ( $src, $id ) { return '/asset/' . $src; };
        $markdown = "![Shot](../../eng-us/help/img/shot.png) and [the original](../../eng-us/help/other.md)\n";
        $options = array( 'image_resolver' => $resolver, 'link_resolver' => $resolver );
        // from the page id (help/start.md) the path climbs above the documentation root: refused
        $byId = ( new expHelpMarkdownRenderer( $options ) )->render( $markdown, 'help/start.md' );
        $this->assertStringNotContainsString( '<img', $byId['html'] );
        // from the file the page was read from (doc/ger-de/hilfe/start.md) it stays inside the installation: resolved
        $byFile = ( new expHelpMarkdownRenderer( $options + array( 'source_path' => 'doc/ger-de/hilfe/start.md' ) ) )->render( $markdown, 'help/start.md' );
        $this->assertStringContainsString( '<img class="a4-help-img" src="/asset/../../eng-us/help/img/shot.png"', $byFile['html'] );
        $this->assertStringContainsString( 'href="/asset/../../eng-us/help/other.md"', $byFile['html'] );
        // and a path that leaves the installation is refused all the same
        $out = ( new expHelpMarkdownRenderer( $options + array( 'source_path' => 'doc/ger-de/hilfe/start.md' ) ) )->render( "![x](../../../../etc/passwd)\n", 'help/start.md' );
        $this->assertStringNotContainsString( '<img', $out['html'] );
        // the viewer's resolvers turn the file into the original's asset id
        $german = self::index( 'ger-de' );
        expHelpDocumentIndex::setInstance( $german );
        try
        {
            $this->assertSame( 'help/img/shot.png', expHelpLinks::resolveId( '../../eng-us/help/img/shot.png', 'help/start.md' ) );
        }
        finally
        {
            expHelpDocumentIndex::setInstance( null );
        }
    }

    /** TD-13 */
    public function testAnchorsMapToTheShownLanguagesHeadings()
    {
        $root = self::$root . '/td13';
        $files = array(
            'doc/eng-us/help/a.md' => "# Anchors\n\n## First part\n\nText.\n\n## Second part\n\nText.\n",
            'doc/eng-us/help/b.md' => "# Linking\n\nSee [the second part](a.md#second-part) and [nothing](a.md#no-such-part).\n",
            'doc/ger-de/hilfe/a.md' => "# Anker\n\n## Erster Teil\n\nText.\n\n## Zweiter Teil\n\nText.\n",
            'doc/ger-de/hilfe/c.md' => "# Verweise\n\nSiehe [den zweiten Teil](a.md#zweiter-teil).\n",
            'doc/eng-us/help/c.md' => "# Links\n\nSee [the second part](a.md#second-part).\n",
            'doc/eng-us/help/d.md' => "# Uneven\n\n## Only part\n",
            'doc/ger-de/hilfe/d.md' => "# Ungleich\n\n## Einziger Teil\n\n## Noch ein Teil\n",
            'doc/ger-de/.paths.json' => json_encode( array( 'hilfe/a.md' => 'help/a.md', 'hilfe/c.md' => 'help/c.md', 'hilfe/d.md' => 'help/d.md' ) ),
        );
        foreach ( $files as $path => $content )
        {
            @mkdir( dirname( $root . '/' . $path ), 0777, true );
            file_put_contents( $root . '/' . $path, $content );
        }
        $tracked = array_keys( $files );
        sort( $tracked );
        $index = function ( $language ) use ( $root, $tracked ) {
            $languages = new expHelpLanguage( array( 'default' => 'eng-us', 'directories' => array( 'eng-us', 'ger-de' ) ), $root );
            return new expHelpDocumentIndex( new expHelpAllowlist( array( 'root' => $root, 'roots' => array( 'doc' ), 'tracked' => $tracked ) ),
                                             array( 'cache_dir' => false, 'git_history' => false, 'languages' => $languages, 'language' => $language ) );
        };
        $german = $index( 'ger-de' );
        $english = $index( 'eng-us' );
        // an English page (b, not translated) links an English anchor of a translated page: the German heading
        $this->assertSame( 'zweiter-teil', $german->anchorOf( 'help/a.md', 'second-part' ) );
        $this->assertSame( 'zweiter-teil', $german->anchorOf( 'help/a.md', 'zweiter-teil' ), 'an anchor the page has stays' );
        $this->assertSame( 'no-such-part', $german->anchorOf( 'help/a.md', 'no-such-part' ), 'an unknown anchor stays' );
        // the other way: a German anchor on the English page (the original, or a fallback)
        $this->assertSame( 'second-part', $english->anchorOf( 'help/a.md', 'zweiter-teil' ) );
        // versions with a different number of headings are not mapped
        $this->assertSame( 'only-part', $german->anchorOf( 'help/d.md', 'only-part' ) );
        $this->assertSame( 'zweiter-teil', $german->anchorOf( 'help/x.md', 'zweiter-teil' ), 'an unknown page keeps it' );

        // at render time and in the link check
        expHelpDocumentIndex::setInstance( $german );
        try
        {
            $this->assertStringEndsWith( '/exphelp/viewer/help/a.md#zweiter-teil', (string)expHelpLinks::link( 'a.md#second-part', 'help/b.md' ) );
            $check = new expHelpLinkCheck( $german );
            $problems = $check->check( 'help/b.md' );
            $anchors = array_values( array_filter( $problems, function ( $p ) { return $p['problem'] === 'anchor'; } ) );
            $this->assertCount( 1, $anchors, 'only the unknown anchor is reported: ' . json_encode( $problems ) );
            $this->assertStringEndsWith( '#no-such-part', $anchors[0]['resolved'] );
        }
        finally
        {
            expHelpDocumentIndex::setInstance( null );
        }
        expHelpDocumentIndex::setInstance( $english );
        try
        {
            $this->assertStringEndsWith( '/exphelp/viewer/help/a.md#second-part', (string)expHelpLinks::link( 'a.md#zweiter-teil', 'help/b.md' ) );
        }
        finally
        {
            expHelpDocumentIndex::setInstance( null );
        }
    }

    /** TD-11 */
    public function testReadingLanguageCoverageAndChoices()
    {
        $languages = self::languages();
        $coverage = self::index( 'ger-de' )->languageCoverage();
        // English has every page; German two of them (hilfe/start.md and hilfe/partner/programm.md)
        $this->assertSame( array( 'pages' => 3, 'total' => 3 ), $coverage['eng-us'] );
        $this->assertSame( array( 'pages' => 2, 'total' => 3 ), $coverage['ger-de'] );
        $this->assertSame( $coverage, self::index( 'eng-us' )->languageCoverage(), 'the same from every language' );

        $labels = array( 'eng-GB' => 'English (United Kingdom)', 'eng-US' => 'English (United States)', 'ger-DE' => 'Deutsch (Deutschland)', 'fre-FR' => 'Français (France)' );
        $reading = expHelp::readingChoices( $languages, 'ger-de', $coverage, array_keys( $labels ), $labels, 'ger-DE', 'eng-GB' );
        $this->assertTrue( $reading['can_switch'] );
        $this->assertSame( 'language', $reading['provider'] );
        $this->assertSame( array( 'English', 'Deutsch' ), array_column( $reading['choices'], 'label' ), 'the languages\' own names, without the country' );
        $this->assertSame( array( 'eng-GB', 'ger-DE' ), array_column( $reading['choices'], 'locale' ), 'English: the siteaccess\'s own English locale' );
        $this->assertSame( array( false, true ), array_column( $reading['choices'], 'current' ) );
        $this->assertSame( array( 'en-US', 'de-DE' ), array_column( $reading['choices'], 'lang' ) );
        $this->assertSame( array( array( 'directory' => 'ger-de', 'name' => 'German', 'pages' => 2, 'total' => 3 ) ), $reading['coverage'] );

        // the current locale wins for its own language: a reader on eng-US stays on eng-US
        $reading = expHelp::readingChoices( $languages, 'eng-us', $coverage, array_keys( $labels ), $labels, 'eng-US', 'eng-GB' );
        $this->assertSame( array( 'eng-US', 'ger-DE' ), array_column( $reading['choices'], 'locale' ) );
        // no translated page yet: German is not offered, and one language is no switch
        $none = array( 'eng-us' => array( 'pages' => 3, 'total' => 3 ), 'ger-de' => array( 'pages' => 0, 'total' => 3 ) );
        $reading = expHelp::readingChoices( $languages, 'eng-us', $none, array_keys( $labels ), $labels, 'eng-GB', 'eng-GB' );
        $this->assertSame( array( 'eng-us' ), array_column( $reading['choices'], 'directory' ) );
        $this->assertFalse( $reading['can_switch'] );
        $this->assertSame( array(), $reading['coverage'] );
        // a user without the switch sees the language only
        $reading = expHelp::readingChoices( $languages, 'ger-de', $coverage, array(), array(), 'ger-DE', 'eng-GB' );
        $this->assertFalse( $reading['can_switch'] );
        $this->assertSame( array( 'ger-de' ), array_column( $reading['choices'], 'directory' ), 'the reader\'s language is always listed' );
        $this->assertSame( 'German', $reading['choices'][0]['label'] );
        // a language no allowed locale maps to is not offered
        $reading = expHelp::readingChoices( $languages, 'eng-us', $coverage, array( 'eng-GB', 'fre-FR' ), $labels, 'eng-GB', 'eng-GB' );
        $this->assertSame( array( 'eng-us' ), array_column( $reading['choices'], 'directory' ) );
        $this->assertFalse( $reading['can_switch'] );

        // ?lang=eng-us asks for the original; the reader's own language, an unknown one or a malformed value is no request
        $this->assertSame( 'eng-us', expHelp::requestedLanguage( array( 'lang' => 'eng-us' ), $languages, 'ger-de' ) );
        $this->assertNull( expHelp::requestedLanguage( array( 'lang' => 'ger-de' ), $languages, 'ger-de' ) );
        $this->assertNull( expHelp::requestedLanguage( array( 'lang' => 'fr' ), $languages, 'ger-de' ) );
        $this->assertNull( expHelp::requestedLanguage( array( 'lang' => array( 'eng-us' ) ), $languages, 'ger-de' ) );
        $this->assertNull( expHelp::requestedLanguage( array( 'lang' => '../eng-us' ), $languages, 'ger-de' ) );
        $this->assertNull( expHelp::requestedLanguage( array(), $languages, 'ger-de' ) );
    }
}
