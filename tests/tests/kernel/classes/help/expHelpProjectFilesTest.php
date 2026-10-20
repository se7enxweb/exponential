<?php
/**
 * The project files of the help (doc/en/us/features/6.0/help-system.md, "Project files"): README.md, SECURITY.md,
 * CONTRIBUTING.md ... outside doc/, as pages with the id project/<path>. No database; the fixtures are real git
 * repositories made in a temporary directory (skipped without git).
 *
 *  PF-01  the patterns: "*" stays in a directory, "**" crosses directories, "**\/" matches no directory too
 *  PF-02  by name: the shipped Include[] takes the root *.md and the README.md of bin/, design/, kernel/; never
 *         doc/, extension/, a tests/ directory, var/, vendor/, settings/override/, a hidden file or directory
 *         (.github/ included), nor anything but Markdown, even when Include[] asks for everything
 *  PF-03  an untracked root .md (qbix.md, AGENTS.md, 6015.md ...) is never listed, indexed, searched or linked, from
 *         the git index, from "git ls-files" and from the release manifest alike, even when Include[] names it
 *  PF-04  a tracked file in a place .gitignore or .git/info/exclude ignores is refused (forced in with git add -f),
 *         with the negation and directory rules of git
 *  PF-05  ids: project/<path> and back; the repository path as an address leads to the id; doc ids do not change
 *  PF-06  classified by [ProjectFiles]: category project-files, type project, audiences by AudienceRules[]; the title
 *         from the first heading; front matter wins
 *  PF-07  links both ways: ../../../README.md from doc/en/us/... opens the project file in the help, a link of
 *         README.md into doc/en/us/... opens the help page; the link check finds no "not served" link
 *  PF-08  English only: a German reader gets them with the fallback note, like the changelogs
 *  PF-09  navigation: the project files are pages at the top of the tree, next to the folders; there is no folder
 *         "project" (nor any folder of theirs), a title that is only "README" gets its directory in front, and the
 *         breadcrumb of a project file is Help > the page
 *  PF-10  this installation: every tracked root *.md is a project file, nothing untracked is, and the shipped
 *         [ProjectFiles] settings are the built-in defaults
 *  PF-11  license: a project file has the license of the software, not the documentation license
 *  PF-12  Enabled=disabled: no project files at all
 *  PF-13  the new strings are in the translation catalogues (context kernel/exphelp)
 *  PF-14  .github/, tests/ and extension/ are never listed, even when an Include[] names them (tracked files in the
 *         fixture); this installation lists none of them either
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpProjectFilesTest extends PHPUnit\Framework\TestCase
{
    /** Untracked notes in the fixture's root, as alpha's root holds them */
    const PRIVATE_NOTES = array( 'qbix.md', 'AGENTS.md', '6015.md', '64lang.md', 'credentials-rotation.md' );

    /** The words only the private notes contain */
    const SECRET_WORD = 'xylophonesecret';

    /** @var string the fixture installation (a git work tree) */
    private static $root = '';

    /** @var bool whether git could make the fixture */
    private static $git = false;

    public static function setUpBeforeClass(): void
    {
        $base = self::installRoot() . '/var/tmp';
        if ( !is_dir( $base ) || !is_writable( $base ) )
            $base = sys_get_temp_dir();
        self::$root = $base . '/exphelp-project-' . bin2hex( random_bytes( 6 ) );
        $files = array(
            'README.md' => "# Exponential\n\nThe project. Read the [caches guide](doc/en/us/guides/caches.md#clearing) and the "
                         . "[security policy](SECURITY.md). Unique word: marmalade.\n\n## Install\n\nText.\n",
            'SECURITY.md' => "# Security Policy\n\nReport privately.\n",
            'CONTRIBUTING.md' => "---\ntitle: How to contribute\naudience: designer\n---\n# Contributors Guidelines\n\nThanks.\n",
            'LICENSE.md' => "# GNU GENERAL PUBLIC LICENSE\n\nVersion 2.\n",
            'bin/php/README.md' => "# Command line scripts\n\nText.\n",
            'tests/README.md' => "# Tests\n\nText.\n",
            'tests/fixtures/README.md' => "# A fixture\n",
            'kernel/classes/README.md' => "# Kernel classes\n",
            'kernel/private/README.md' => "# README\n\nOnly the name.\n",
            'kernel/classes/tests/README.md' => "# Tests of the kernel classes\n",
            'extension/someext/README.md' => "# Some extension\n",
            '.github/PULL_REQUEST_TEMPLATE.md' => "# Pull request\n",
            'design/admin4/README.md' => "# The admin4 design\n",
            'extension/someext/tests/browser/README.md' => "# Browser tests\n",
            'extension/someext/design/standard/javascript/lib/README.md' => "# A JavaScript library\n",
            'extension/someext/doc/guide.md' => "# The extension's guide\n",
            'lib/README.txt' => "Plain text, not Markdown.\n",
            'kernel/classes/notes.php' => "<?php\n",
            'settings/override/README.md' => "# Never\n",
            'var/README.md' => "# Never\n",
            'vendor/foo/README.md' => "# Never\n",
            '.hidden/README.md' => "# Never\n",
            '.github/SUPPORT.md' => "# Support\n\nAsk.\n",
            'notes/ignored.md' => "# Ignored notes\n\n" . self::SECRET_WORD . "\n",
            'secret-plan.md' => "# Ignored by a pattern\n\n" . self::SECRET_WORD . "\n",
            'keep-secret.md' => "# Re-included by a negation\n",
            'local-only.md' => "# Ignored by info/exclude\n\n" . self::SECRET_WORD . "\n",
            '.gitignore' => "/notes/\nsecret*.md\nkeep*.md\n!keep-secret.md\n/qbix.md\n",
            'doc/en/us/guides/caches.md' => "# Caches\n\nSee [the project](../../../../README.md#install), "
                                          . "[security](../../../../SECURITY.md), [a note](../../../../qbix.md) and "
                                          . "[the agents](../../../../AGENTS.md).\n\n## Clearing\n\nText.\n",
            'doc/changelogs/6.0.md' => "# Changes in 6.0\n",
        );
        foreach ( self::PRIVATE_NOTES as $note )
            $files[$note] = "# Private note\n\n" . self::SECRET_WORD . " see doc/en/us/guides/caches.md\n";
        foreach ( $files as $path => $content )
        {
            @mkdir( dirname( self::$root . '/' . $path ), 0777, true );
            file_put_contents( self::$root . '/' . $path, $content );
        }
        $untracked = array_merge( self::PRIVATE_NOTES, array( 'kernel/classes/notes.php' ) );
        $forced = array( 'notes/ignored.md', 'secret-plan.md', 'local-only.md', 'qbix.md' );
        $tracked = array_diff( array_keys( $files ), $untracked, $forced );
        self::$git = self::git( array( 'init', '-q' ) );
        if ( !self::$git )
            return;
        file_put_contents( self::$root . '/.git/info/exclude', "/local-only.md\n" );
        self::$git = self::git( array_merge( array( 'add', '--' ), array_values( $tracked ) ) )
                  && self::git( array( 'add', '-f', '--', 'notes/ignored.md', 'secret-plan.md', 'local-only.md' ) );
    }

    public static function tearDownAfterClass(): void
    {
        if ( self::$root !== '' && is_dir( self::$root ) )
            self::remove( self::$root );
        expHelpDocumentIndex::setInstance( null );
    }

    protected function setUp(): void
    {
        if ( !self::$git )
            $this->markTestSkipped( 'git cannot make the fixture repository' );
    }

    private static function git( array $args )
    {
        $command = array_merge( array( 'git', '-c', 'safe.directory=*', '-c', 'core.autocrlf=false', '-C', self::$root ), $args );
        $process = @proc_open( $command, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
        if ( !is_resource( $process ) )
            return false;
        stream_get_contents( $pipes[1] );
        stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        return proc_close( $process ) === 0;
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

    private static function allowlist( array $options = array(), $root = null )
    {
        return new expHelpAllowlist( $options + array( 'root' => $root !== null ? $root : self::$root, 'roots' => array( 'doc' ),
                                                       'project_files' => array() ) );
    }

    private static function languages( $root = null )
    {
        return new expHelpLanguage( array( 'default' => 'en/us', 'directories' => array( 'en/us', 'de' ),
                                           'map' => array( 'eng-US' => 'en/us', 'ger-DE' => 'de' ),
                                           'names' => array( 'en/us' => 'English', 'de' => 'German' ) ), $root !== null ? $root : self::$root );
    }

    private static function index( $language = 'en/us', ?expHelpAllowlist $allowlist = null )
    {
        return new expHelpDocumentIndex( $allowlist !== null ? $allowlist : self::allowlist(),
                                         array( 'cache_dir' => false, 'git_history' => false, 'audience' => new expHelpAudience(),
                                                'languages' => self::languages(), 'language' => $language ) );
    }

    /** @return string[] the project files an allowlist lists */
    private static function projectPaths( expHelpAllowlist $allowlist )
    {
        $paths = array();
        foreach ( $allowlist->entries() as $path => $entry )
            if ( $entry['root'] === '' )
                $paths[] = $path;
        return $paths;
    }

    /** PF-01 */
    public function testPatterns()
    {
        $this->assertTrue( expHelpProjectFiles::globMatch( '*.md', 'README.md' ) );
        $this->assertFalse( expHelpProjectFiles::globMatch( '*.md', 'bin/README.md' ), '"*" stays in one directory' );
        $this->assertTrue( expHelpProjectFiles::globMatch( 'bin/**/README.md', 'bin/php/README.md' ) );
        $this->assertTrue( expHelpProjectFiles::globMatch( 'bin/**/README.md', 'bin/README.md' ), '"**/" matches no directory too' );
        $this->assertTrue( expHelpProjectFiles::globMatch( 'bin/**/README.md', 'bin/a/b/c/README.md' ) );
        $this->assertTrue( expHelpProjectFiles::globMatch( '**/fixtures/**', 'tests/fixtures/x/README.md' ) );
        $this->assertTrue( expHelpProjectFiles::globMatch( 'CHANGELOG?.md', 'CHANGELOG2.md' ) );
        $this->assertFalse( expHelpProjectFiles::globMatch( 'a?b', 'a/b' ) );
        $this->assertTrue( expHelpProjectFiles::globMatch( '[A-Z]*.md', 'README.md' ) );
        $this->assertFalse( expHelpProjectFiles::globMatch( '[!A-Z]*.md', 'README.md' ) );
        $this->assertFalse( expHelpProjectFiles::globMatch( 'README.md', 'README.mdx' ), 'anchored at both ends' );
        $this->assertFalse( expHelpProjectFiles::globMatch( 'a.md', 'xa.md' ) );
    }

    /** PF-02 */
    public function testCandidatesByName()
    {
        $files = new expHelpProjectFiles();
        foreach ( array( 'README.md', 'SECURITY.md', 'CONTRIBUTING.md', 'LICENSE.md', 'COPYRIGHT.md', 'CHANGELOG.md',
                         'bin/php/README.md', 'design/admin4/README.md', 'kernel/classes/README.md' ) as $path )
            $this->assertTrue( $files->isCandidate( $path ), $path );
        foreach ( array( '.github/SUPPORT.md', '.github/PULL_REQUEST_TEMPLATE.md', 'tests/README.md', 'tests/browser/README.md',
                         'kernel/classes/tests/README.md', 'extension/ezoe/tests/tinymce8/README.md', 'extension/ezoe/README.md',
                         'doc/README.md', 'doc/en/us/guides/caches.md', 'bin/php/notes.md', 'tests/fixtures/README.md',
                         'extension/ezoe/design/standard/javascript/tinymce8/README.md', 'extension/x/doc/README.md',
                         'settings/override/README.md', 'var/README.md', 'vendor/a/README.md', '.hidden/README.md',
                         'README.txt', 'README', 'x.php', '../README.md', 'a/../README.md', '/README.md/..', '', '.md' ) as $path )
            $this->assertFalse( $files->isCandidate( $path ), $path );
        // whatever the settings ask for, these are never project files
        $greedy = new expHelpProjectFiles( array( 'include' => array( '**', '*', '**/*' ), 'exclude' => array() ) );
        foreach ( array( 'doc/en/us/x.md', 'var/site/x.md', 'vendor/x/README.md', 'settings/override/x.md',
                         'settings/siteaccess/admin/x.md', 'a/node_modules/x/README.md', '.git/x.md', '.claude/x.md',
                         '.github/../x.md', 'extension/x/doc/README.md', 'x.php', 'x.txt', 'settings/site.ini',
                         '.github/SUPPORT.md', '.github/ISSUE_TEMPLATE/bug.md', '.hidden.md', 'bin/.notes.md',
                         'tests/README.md', 'kernel/classes/tests/README.md', 'extension/x/README.md',
                         'extension/x/tests/y/README.md' ) as $path )
            $this->assertFalse( $greedy->isCandidate( $path ), $path );
        $this->assertTrue( $greedy->isCandidate( 'some/where/notes.md' ), 'Include[] does widen the names' );
        $this->assertFalse( ( new expHelpProjectFiles( array( 'enabled' => false ) ) )->isCandidate( 'README.md' ) );
    }

    /** PF-03 the git index, git ls-files, the manifest */
    public function testUntrackedRootNotesAreNeverIndexed()
    {
        // a setting that names the private notes, and one that takes every Markdown file
        $asking = array( 'include' => array_merge( array( '*.md', '**/*.md' ), self::PRIVATE_NOTES ), 'exclude' => array() );
        foreach ( array( array( 'gitindex' ), array( 'gitcommand' ) ) as $sources )
        {
            $allowlist = self::allowlist( array( 'sources' => $sources, 'project_files' => $asking ) );
            $paths = self::projectPaths( $allowlist );
            $this->assertContains( 'README.md', $paths, implode( ',', $sources ) );
            foreach ( self::PRIVATE_NOTES as $note )
            {
                $this->assertTrue( is_file( self::$root . '/' . $note ), 'the note is on disk' );
                $this->assertNotContains( $note, $paths, $note . ' from ' . implode( ',', $sources ) );
                $this->assertFalse( $allowlist->contains( $note ), $note );
                $this->assertNull( $allowlist->safePath( $note ), $note );
                $this->assertFalse( $allowlist->allows( $note ), $note );
            }
            $index = self::index( 'en/us', $allowlist );
            foreach ( self::PRIVATE_NOTES as $note )
            {
                $this->assertNull( $index->document( 'project/' . $note ), $note );
                $this->assertNull( $index->source( 'project/' . $note ), $note );
                $this->assertFalse( $index->isAllowed( 'project/' . $note ), $note );
                $this->assertNull( $index->document( $note ), $note );
                // the viewer's address by file name leads to no page either
                $canonical = Exponential\View\Kernel\Exphelp\Viewer::canonicalId( $index, $note );
                $this->assertTrue( $canonical === null || $index->document( $canonical ) === null, $note );
            }
            foreach ( $index->documents() as $id => $document )
                $this->assertStringNotContainsString( self::SECRET_WORD, (string)$index->source( $id ), $id );
            // the search finds no private note
            $search = new expHelpSearch( $index, array( 'cacheDir' => null ) );
            $this->assertSame( array(), $search->search( self::SECRET_WORD )['results'] );
            $this->assertSame( array( 'project/README.md' ), array_map( function ( $hit ) { return $hit['id']; }, $search->search( 'marmalade' )['results'] ) );
            // a link to a private note is dropped: it names no page of the help
            expHelpDocumentIndex::setInstance( $index );
            foreach ( array( 'qbix.md', 'AGENTS.md' ) as $note )
            {
                $id = expHelpLinks::resolveId( '../../../../' . $note, 'guides/caches.md', $index );
                $this->assertFalse( $id !== null && $index->isAllowed( $id ), $note );
                $this->assertNull( expHelpLinks::link( '../../../../' . $note, 'guides/caches.md' ), $note );
            }
            expHelpDocumentIndex::setInstance( null );
            $problems = array();
            foreach ( ( new expHelpLinkCheck( $index ) )->check( 'guides/' ) as $problem )
                $problems[$problem['target']] = $problem['problem'];
            $this->assertSame( 'not-served', $problems['../../../../qbix.md'] ?? null, 'the link check sees it as not served' );
            $this->assertSame( 'not-served', $problems['../../../../AGENTS.md'] ?? null );
        }

        // a release without .git: the manifest lists what was shipped, never the notes
        $release = self::$root . '-release';
        @mkdir( $release . '/doc/en/us/guides', 0777, true );
        @mkdir( $release . '/share', 0777, true );
        foreach ( array_merge( array( 'README.md', 'doc/en/us/guides/caches.md' ), self::PRIVATE_NOTES ) as $path )
            copy( self::$root . '/' . $path, $release . '/' . $path );
        file_put_contents( $release . '/share/filelist.md5', md5_file( $release . '/README.md' ) . "  README.md\n"
                                                         . md5_file( $release . '/doc/en/us/guides/caches.md' ) . "  doc/en/us/guides/caches.md\n" );
        try
        {
            $allowlist = self::allowlist( array( 'sources' => array( 'manifest' ), 'project_files' => $asking ), $release );
            $this->assertSame( array( 'README.md' ), self::projectPaths( $allowlist ) );
            $this->assertSame( 'manifest', $allowlist->usedSources()['project files'] );
        }
        finally
        {
            self::remove( $release );
        }

        // no source at all: nothing, never the directory
        $none = self::allowlist( array( 'sources' => array(), 'project_files' => $asking ) );
        $this->assertSame( array(), self::projectPaths( $none ) );
    }

    /** PF-04 */
    public function testIgnoredLocationsAreRefused()
    {
        $allowlist = self::allowlist( array( 'project_files' => array( 'include' => array( '*.md', '**/*.md' ) ) ) );
        $paths = self::projectPaths( $allowlist );
        foreach ( array( 'notes/ignored.md', 'secret-plan.md', 'local-only.md' ) as $path )
        {
            $this->assertNotContains( $path, $paths, $path . ' is tracked but ignored' );
            $this->assertFalse( $allowlist->allows( $path ), $path );
        }
        $this->assertContains( 'keep-secret.md', $paths, '"!" re-includes' );
        $this->assertContains( 'README.md', $paths );

        $files = new expHelpProjectFiles();
        $this->assertTrue( $files->isIgnored( self::$root, 'notes/ignored.md', self::$root . '/.git' ) );
        $this->assertTrue( $files->isIgnored( self::$root, 'notes/deeper/x.md' ), 'below an ignored directory' );
        $this->assertFalse( $files->isIgnored( self::$root, 'sub/notes.md' ) );
        $this->assertTrue( $files->isIgnored( self::$root, 'sub/secret-x.md' ), 'a rule without a slash matches at any depth' );
        $this->assertFalse( $files->isIgnored( self::$root, 'sub/qbix.md' ), 'an anchored rule only at its directory' );
        $this->assertTrue( $files->isIgnored( self::$root, 'qbix.md' ) );
        $this->assertTrue( $files->isIgnored( self::$root, 'local-only.md', self::$root . '/.git' ), '.git/info/exclude' );
        $this->assertFalse( $files->isIgnored( self::$root, 'local-only.md' ) );
        $this->assertTrue( $files->isIgnored( self::$root, '../x.md' ), 'not a clean path' );

        $rules = expHelpProjectFiles::parseIgnore( "# comment\n\n/var/*/cache/\n\\#hash.md\n**/build\ndist/   \n" );
        $this->assertCount( 4, $rules );
        $this->assertTrue( $rules[0]['dir'] && $rules[0]['anchored'] );
        $this->assertSame( 1, preg_match( $rules[1]['regex'], '#hash.md' ) );
        $this->assertTrue( $rules[2]['anchored'] );
        $this->assertTrue( $rules[3]['dir'] && !$rules[3]['anchored'] );
    }

    /** PF-05 */
    public function testIds()
    {
        $index = self::index();
        $this->assertSame( 'project/README.md', $index->idOf( 'README.md' ) );
        $this->assertSame( 'project/bin/php/README.md', $index->idOf( 'bin/php/README.md' ) );
        $this->assertSame( 'README.md', $index->pathOf( 'project/README.md' ) );
        $this->assertSame( 'guides/caches.md', $index->idOf( 'doc/en/us/guides/caches.md' ), 'doc ids do not change' );
        $this->assertSame( 'changelogs/6.0.md', $index->idOf( 'doc/changelogs/6.0.md' ) );
        $this->assertNull( $index->idOf( 'kernel/classes/notes.php' ) );
        $this->assertNotNull( $index->document( 'project/README.md' ) );
        foreach ( array( 'project/.github/SUPPORT.md', 'project/.github/PULL_REQUEST_TEMPLATE.md', 'project/tests/README.md',
                         'project/extension/someext/tests/browser/README.md', 'project/extension/someext/README.md',
                         'project/kernel/classes/tests/README.md', 'project/tests/fixtures/README.md', 'project/extension/someext/design/standard/javascript/lib/README.md',
                         'project/settings/override/README.md', 'project/var/README.md', 'project/vendor/foo/README.md',
                         'project/.hidden/README.md', 'project/extension/someext/doc/guide.md', 'project/../README.md' ) as $id )
            $this->assertNull( $index->document( $id ), $id );
        $this->assertSame( 'README.md', $index->document( 'project/README.md' )['path'] );
        // the path in the repository as an address: the viewer redirects to the id
        $this->assertSame( 'project/README.md', Exponential\View\Kernel\Exphelp\Viewer::canonicalId( $index, 'README.md' ) );
        $this->assertSame( 'project/bin/php/README.md', Exponential\View\Kernel\Exphelp\Viewer::canonicalId( $index, 'bin/php/README.md' ) );
        $this->assertNull( Exponential\View\Kernel\Exphelp\Viewer::canonicalId( $index, 'project/README.md' ) );
        $this->assertSame( 'README.md', expHelpProjectFiles::pathOfId( 'project/README.md' ) );
        $this->assertNull( expHelpProjectFiles::pathOfId( 'projects/README.md' ) );
        $this->assertNull( expHelpProjectFiles::pathOfId( 'project/../x.md' ) );
    }

    /** PF-06 */
    public function testClassification()
    {
        $index = self::index();
        $readme = $index->document( 'project/README.md' );
        $this->assertSame( 'Exponential', $readme['title'], 'the first heading' );
        $this->assertSame( 'project-files', $readme['category'] );
        $this->assertSame( 'project', $readme['type'] );
        $this->assertContains( 'developer', $readme['audience'] );
        $this->assertContains( 'admin', $readme['audience'] );
        $this->assertContains( 'editor', $readme['audience'] );
        $this->assertSame( array( 'admin', 'developer', 'hosting' ), $index->document( 'project/SECURITY.md' )['audience'] );
        $this->assertSame( array( 'admin', 'developer' ), $index->document( 'project/bin/php/README.md' )['audience'], 'Audiences[]' );
        $this->assertSame( 'Command line scripts', $index->document( 'project/bin/php/README.md' )['title'] );
        $contributing = $index->document( 'project/CONTRIBUTING.md' );
        $this->assertSame( 'How to contribute', $contributing['title'], 'front matter wins' );
        $this->assertSame( array( 'designer' ), $contributing['audience'] );
        $this->assertSame( 'project-files', $contributing['category'] );
        $categories = $index->categories();
        $this->assertSame( 'Project files', $categories['project-files']['name'] );
        $this->assertGreaterThanOrEqual( 8, $categories['project-files']['count'] );
        $this->assertSame( 'Project file', $index->types()['project']['name'] );
        $this->assertArrayHasKey( 'project/README.md', $index->inCategory( 'project-files', 'admin' ) );
        // the search can be narrowed to them
        $search = new expHelpSearch( $index, array( 'cacheDir' => null ) );
        $ids = array_map( function ( $hit ) { return $hit['id']; }, $search->search( 'Security', array( 'category' => array( 'project-files' ) ) )['results'] );
        $this->assertContains( 'project/SECURITY.md', $ids );
        foreach ( $ids as $id )
            $this->assertStringStartsWith( 'project/', $id );
    }

    /** PF-07 */
    public function testLinksBothWays()
    {
        $index = self::index();
        $this->assertSame( 'project/README.md', expHelpLinks::resolveId( '../../../../README.md', 'guides/caches.md', $index ) );
        $this->assertTrue( $index->isAllowed( 'project/README.md' ) );
        $this->assertSame( 'project/SECURITY.md', expHelpLinks::resolveId( '../../../../SECURITY.md', 'guides/caches.md', $index ) );
        $this->assertSame( 'guides/caches.md', expHelpLinks::resolveId( 'doc/en/us/guides/caches.md', 'project/README.md', $index ) );
        $this->assertSame( 'project/SECURITY.md', expHelpLinks::resolveId( 'SECURITY.md', 'project/README.md', $index ) );
        $this->assertSame( 'project/README.md', expHelpLinks::resolveId( '../../README.md', 'project/bin/php/README.md', $index ) );
        $this->assertNull( expHelpLinks::resolveId( '../README.md', 'project/README.md', $index ), 'never above the root' );
        $problems = array_merge( ( new expHelpLinkCheck( $index ) )->check( 'project/README.md' ),
                                 ( new expHelpLinkCheck( $index ) )->check( 'guides/' ) );
        $byTarget = array();
        foreach ( $problems as $problem )
            $byTarget[$problem['target']] = $problem['problem'];
        foreach ( array( 'doc/en/us/guides/caches.md#clearing', 'SECURITY.md', '../../../../README.md#install', '../../../../SECURITY.md' ) as $target )
            $this->assertArrayNotHasKey( $target, $byTarget, $target . ' resolves to a help page' );
    }

    /** PF-08 */
    public function testEnglishOnlyWithTheFallbackNote()
    {
        $german = self::index( 'de' );
        $readme = $german->document( 'project/README.md' );
        $this->assertSame( 'README.md', $readme['path'] );
        $this->assertSame( '', $readme['language'] );
        $this->assertTrue( $readme['fallback'], 'shown with the note, like the changelogs' );
        $this->assertTrue( $german->document( 'changelogs/6.0.md' )['fallback'] );
        $this->assertSame( 'en-US', Exponential\View\Kernel\Exphelp\Viewer::languageVariables( $german, $readme )['help_page_lang'] );
        $this->assertFalse( self::index( 'en/us' )->document( 'project/README.md' )['fallback'] );
    }

    /** PF-09 */
    public function testNavigation()
    {
        $index = self::index();
        $tree = $index->tree();
        // no folder of the project files at all: not "project", not one below it
        $folders = array();
        $walk = function ( array $node ) use ( &$walk, &$folders ) {
            foreach ( $node['children'] as $child )
            {
                $folders[] = (string)$child['path'];
                $walk( $child );
            }
        };
        $walk( $tree );
        foreach ( $folders as $folder )
            $this->assertStringStartsNotWith( 'project', $folder, 'the project files have no folder: ' . $folder );
        // every project file is a page at the top, next to the folders
        $top = array();
        foreach ( $tree['documents'] as $document )
            $top[$document['id']] = $document['title'];
        foreach ( array( 'project/README.md' => 'Exponential', 'project/SECURITY.md' => 'Security Policy',
                         'project/CONTRIBUTING.md' => 'How to contribute', 'project/LICENSE.md' => 'GNU GENERAL PUBLIC LICENSE',
                         'project/bin/php/README.md' => 'Command line scripts', 'project/kernel/classes/README.md' => 'Kernel classes',
                         'project/design/admin4/README.md' => 'The admin4 design',
                         'project/kernel/private/README.md' => 'kernel/private README' ) as $id => $title )
        {
            $this->assertArrayHasKey( $id, $top, $id . ' is at the top of the tree' );
            $this->assertSame( $title, $top[$id], $id );
        }
        foreach ( array_keys( $top ) as $id )
            $this->assertTrue( expHelpProjectFiles::isProjectId( $id ), $id );
        $this->assertSame( 'kernel/private README', expHelpProjectFiles::treeTitle( 'kernel/private/README.md', 'README' ) );
        $this->assertSame( 'README', expHelpProjectFiles::treeTitle( 'README.md', 'README' ), 'the root one has no directory' );
        $this->assertSame( 'Kernel classes', expHelpProjectFiles::treeTitle( 'kernel/classes/README.md', 'Kernel classes' ) );
        // the side tree: no folder of theirs, each page a link at the top level
        $html = ( new expHelpNavigation( $index ) )->build( $tree );
        $this->assertStringNotContainsString( 'data-nav-path="project', $html );
        $this->assertStringContainsString( '/project/bin/php/README.md"', $html );
        // the breadcrumb: Help > the page
        $crumbs = Exponential\View\Kernel\Exphelp\Viewer::breadcrumb( $index, 'project/bin/php/README.md', 'Command line scripts' );
        $this->assertCount( 2, $crumbs );
        $this->assertSame( 'exphelp/dashboard', $crumbs[0]['url'] );
        $this->assertSame( array( 'url' => false, 'text' => 'Command line scripts' ), $crumbs[1] );
    }

    /** PF-10 */
    public function testThisInstallation()
    {
        $root = self::installRoot();
        if ( expHelpAllowlist::gitDir( $root ) === null )
            $this->markTestSkipped( 'not a git checkout' );
        $tracked = expHelpAllowlist::runGitLsFiles( $root );
        if ( $tracked === null )
            $this->markTestSkipped( 'git cannot be run' );
        $allowlist = new expHelpAllowlist( array( 'root' => $root, 'roots' => array( 'doc' ), 'project_files' => array() ) );
        $paths = self::projectPaths( $allowlist );
        foreach ( array( 'README.md', 'SECURITY.md', 'CONTRIBUTING.md', 'COPYRIGHT.md', 'LICENSE.md' ) as $file )
            $this->assertContains( $file, $paths, $file );
        // every tracked root *.md is a project file
        foreach ( $tracked as $path )
            if ( strpos( $path, '/' ) === false && preg_match( '/\.md$/', $path ) )
                $this->assertContains( $path, $paths, $path );
        // and only tracked files are
        $this->assertSame( array(), array_values( array_diff( $paths, $tracked ) ), 'every project file is tracked' );
        foreach ( glob( $root . '/*.md' ) as $file )
            if ( !in_array( basename( $file ), $tracked, true ) )
                $this->assertNotContains( basename( $file ), $paths, basename( $file ) . ' is not tracked' );
        foreach ( self::PRIVATE_NOTES as $note )
            $this->assertNotContains( $note, $paths, $note );
        $this->assertNotContains( 'extension/ezoe/design/standard/javascript/tinymce8/README.md', $paths, 'a copy of another project' );
        // PF-14: no .github/, no tests, nothing of extension/
        $this->assertNotContains( '.github/PULL_REQUEST_TEMPLATE.md', $paths );
        $this->assertNotContains( 'extension/ezoe/tests/tinymce8/README.md', $paths );
        foreach ( $paths as $path )
        {
            $this->assertDoesNotMatchRegularExpression( '#(^|/)\.#', $path, 'never hidden' );
            $this->assertDoesNotMatchRegularExpression( '#(^|/)tests/#', $path, 'never tests' );
            $this->assertStringStartsNotWith( 'extension/', $path );
        }

        // the links of the project files, and of the help page about them, all land on help pages
        $index = new expHelpDocumentIndex( $allowlist, array( 'cache_dir' => false, 'git_history' => false, 'audience' => new expHelpAudience(),
                                                               'languages' => new expHelpLanguage( array(), $root ) ) );
        $check = new expHelpLinkCheck( $index );
        $errors = array();
        foreach ( array( 'project/', 'help/project-files.md' ) as $below )
            foreach ( $check->check( $below ) as $problem )
                // CODE_OF_CONDUCT.md is not in the repository yet (doc/en/us/help/advocates/advocate-conduct-and-ethics.md says so)
                if ( ( $problem['severity'] === 'error' && $problem['resolved'] !== 'CODE_OF_CONDUCT.md' )
                     || ( $below !== 'project/' && $problem['problem'] === 'not-served' ) )
                    $errors[] = $problem['id'] . ': ' . $problem['target'] . ' (' . $problem['problem'] . ')';
        $this->assertSame( array(), $errors );
        $this->assertNotNull( $index->document( 'project/README.md' ) );
        $this->assertSame( 'project/README.md', expHelpLinks::resolveId( '../../../../README.md', 'help/project-files.md', $index ) );

        // the shipped settings are the built-in defaults
        $group = self::shippedGroup( 'ProjectFiles' );
        $this->assertSame( 'enabled', $group['Enabled'] );
        $this->assertSame( expHelpProjectFiles::DEFAULTS['include'], $group['Include'] );
        $this->assertSame( expHelpProjectFiles::DEFAULTS['exclude'], $group['Exclude'] );
        $this->assertSame( expHelpProjectFiles::DEFAULTS['category'], $group['Category'] );
        $this->assertSame( expHelpProjectFiles::DEFAULTS['type'], $group['Type'] );
        $this->assertSame( expHelpProjectFiles::DEFAULTS['audience'], $group['Audiences'] );
        $this->assertSame( expHelpProjectFiles::DEFAULTS['audienceRules'], $group['AudienceRules'] );
        $this->assertContains( 'project-files', self::shippedGroup( 'CategorySettings' )['Categories'] );
        $this->assertSame( 'Project files', self::shippedGroup( 'CategorySettings' )['CategoryNames']['project-files'] );
        $this->assertSame( 'Project file', self::shippedGroup( 'TypeSettings' )['TypeNames']['project'] );
    }

    /** PF-11 */
    public function testLicense()
    {
        $license = expHelpLicense::forPage( 'project/README.md' );
        $this->assertTrue( $license['project'] );
        $this->assertFalse( $license['free'], 'not under the documentation license' );
        $this->assertStringContainsString( 'GNU General Public License', $license['project_license_name'] );
        $this->assertSame( 'project/LICENSE.md', $license['project_license_page'] );
        $this->assertStringStartsWith( 'This file is part of the source code of Exponential', expHelpLicense::noticeText( 'project/README.md' ) );
        $this->assertFalse( expHelpLicense::forPage( 'guides/caches.md' )['project'] );
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $tpl = (string)file_get_contents( self::installRoot() . '/design/' . $design . '/templates/exphelp/parts/license_notice.tpl' );
            $this->assertStringContainsString( '$lic.project_license_name', $tpl, $design );
        }
    }

    /** PF-12 */
    public function testDisabled()
    {
        $allowlist = self::allowlist( array( 'project_files' => array( 'enabled' => false ) ) );
        $this->assertSame( array(), self::projectPaths( $allowlist ) );
        $this->assertNull( $allowlist->projectFiles() );
        $index = self::index( 'en/us', $allowlist );
        $this->assertNull( $index->document( 'project/README.md' ) );
        $this->assertNull( $index->idOf( 'README.md' ) );
        // without the option (a caller of its own) there are none either
        $this->assertNull( ( new expHelpAllowlist( array( 'root' => self::$root ) ) )->projectFiles() );
        $this->assertNotNull( $index->document( 'guides/caches.md' ), 'the documentation stays' );
    }

    /** PF-13 */
    public function testStringsTranslated()
    {
        $strings = array( 'Project files', 'Project file',
                          'This file is part of the source code of Exponential and is distributed with it under the %license.' );
        foreach ( glob( self::installRoot() . '/share/translations/*/translation.ts' ) as $file )
        {
            $ts = (string)file_get_contents( $file );
            if ( strpos( $ts, '<name>kernel/exphelp</name>' ) === false )
                continue;
            foreach ( $strings as $source )
                $this->assertTrue( strpos( $ts, '<source>' . htmlspecialchars( $source, ENT_QUOTES | ENT_XML1 ) . '</source>' ) !== false,
                                   basename( dirname( $file ) ) . ': ' . $source );
        }
    }

    /** PF-14 */
    public function testHiddenTestsAndExtensionsAreNeverListed()
    {
        $never = array( '.github/SUPPORT.md', '.github/PULL_REQUEST_TEMPLATE.md', 'tests/README.md', 'kernel/classes/tests/README.md',
                        'extension/someext/README.md', 'extension/someext/tests/browser/README.md' );
        $shipped = self::projectPaths( self::allowlist() );
        foreach ( $never as $path )
            $this->assertNotContains( $path, $shipped, $path );
        // an Include[] that names them, and one that takes everything, change nothing
        foreach ( array( array( '.github/*.md', '.github/**/*.md', 'tests/**/README.md', 'tests/README.md', 'kernel/**/README.md',
                                'extension/**/README.md', 'extension/*/tests/**/README.md' ),
                         array( '**', '*', '**/*' ) ) as $include )
        {
            $allowlist = self::allowlist( array( 'project_files' => array( 'include' => $include, 'exclude' => array() ) ) );
            $paths = self::projectPaths( $allowlist );
            foreach ( $never as $path )
                $this->assertNotContains( $path, $paths, $path . ' (' . implode( ' ', $include ) . ')' );
            $index = self::index( 'en/us', $allowlist );
            foreach ( $never as $path )
                $this->assertNull( $index->document( 'project/' . $path ), $path );
        }
        $this->assertContains( 'kernel/classes/README.md', $shipped );
        $this->assertContains( 'kernel/private/README.md', $shipped );
    }

    /**
     * One group of the shipped settings/exphelp.ini, read as text: name => value, list or map.
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
}
