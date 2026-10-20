<?php
/**
 * The documentation of the active extensions in the help (doc/en/us/features/6.0/help-system.md, "Documentation of
 * the extensions"), on a fixture installation in the temporary directory: extensions with their own git repository
 * and one tracked by the installation's repository, active and inactive ones. No database.
 *
 *  HE-01  an active extension's tracked doc/ pages and images, and its README, CHANGELOG, LICENSE at the top, are pages
 *  HE-02  never shown: PHP and other source files, settings, a script without a suffix, top files that are not in
 *         ExtensionTopFiles[], untracked files, anything of an inactive extension, traversal out of the extension
 *  HE-03  a doc/ file wins over the top file of the same name; ids and paths both ways
 *  HE-04  type "extension", category "extensions", the folder "Extensions" and the per-extension list (extensions())
 *  HE-05  ActiveExtensionDocs off (no active extensions given): only ExtensionDocDirs[], as before
 *  HE-06  the search filter "extension" keeps the pages of one extension
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpExtensionDocsTest extends PHPUnit\Framework\TestCase
{
    /** @var string the fixture installation */
    private static $root = '';

    public static function setUpBeforeClass(): void
    {
        if ( !self::hasGit() )
            return;
        self::$root = sys_get_temp_dir() . '/exphelp-ext-' . bin2hex( random_bytes( 6 ) ) . '/install';
        $png = base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==' );
        $files = array(
            // the installation's own repository
            '.gitignore' => "/extension/foo/\n/extension/bar/\n/extension/baz/\n/extension/qux/\n",
            'doc/en/us/help/start.md' => "# Start\n\nWelcome.\n",
            'extension/inroot/doc/guide.md' => "# In-root guide\n\nAn extension tracked by the installation.\n",
            'extension/inroot/README.md' => "# In-root extension\n",
            'extension/inroot/classes/x.php' => "<?php // source\n",
            // an active extension with its own repository
            'extension/foo/doc/index.md' => "# Foo documentation\n\nThe pages of foo.\n\n## Vector\n\nfoo vector words\n",
            'extension/foo/doc/usage/editors.md' => "# Using foo\n\nfor editors: banana words\n",
            'extension/foo/doc/img/shot.png' => $png,
            'extension/foo/doc/tool' => "#!/usr/bin/env php\n<?php echo 'never';\n",
            'extension/foo/doc/source.php' => "<?php echo 'never';\n",
            'extension/foo/doc/page.html' => "<script>alert(1)</script>\n",
            'extension/foo/README.md' => "# Foo\n\nThe foo extension.\n",
            'extension/foo/CHANGELOG' => "foo 1.0\n- first release\n",
            'extension/foo/LICENSE' => "GNU General Public License v2.0\n",
            'extension/foo/NOTES.md' => "# private notes\n",
            'extension/foo/ezinfo.php' => "<?php class fooInfo {}\n",
            'extension/foo/classes/foo.php' => "<?php class foo {}\n",
            'extension/foo/settings/foo.ini' => "[Secret]\nKey=1\n",
            // an inactive extension
            'extension/bar/doc/index.md' => "# Bar\n",
            'extension/bar/README.md' => "# Bar readme\n",
            // an active extension with only a README
            'extension/baz/README.md' => "# Baz\n\nOnly a readme.\n",
            // a doc/README.md and a top README.md
            'extension/qux/doc/README.md' => "# Qux from doc\n",
            'extension/qux/README.md' => "# Qux from top\n",
        );
        foreach ( $files as $path => $content )
        {
            @mkdir( dirname( self::$root . '/' . $path ), 0777, true );
            file_put_contents( self::$root . '/' . $path, $content );
        }
        foreach ( array( 'extension/foo', 'extension/bar', 'extension/baz', 'extension/qux', '' ) as $repo )
        {
            $dir = $repo === '' ? self::$root : self::$root . '/' . $repo;
            self::git( $dir, 'init -q' );
            self::git( $dir, 'config user.email t@example.invalid' );
            self::git( $dir, 'config user.name T' );
            self::git( $dir, 'add -A' );
            self::git( $dir, 'commit -q -m fixture' );
        }
        // created after the commit: untracked
        file_put_contents( self::$root . '/extension/foo/doc/untracked.md', "# Untracked\n" );
        file_put_contents( self::$root . '/extension/foo/README-local.md', "# Untracked top file\n" );
    }

    public static function tearDownAfterClass(): void
    {
        if ( self::$root !== '' )
            self::remove( dirname( self::$root ) );
    }

    protected function setUp(): void
    {
        if ( !self::hasGit() )
            $this->markTestSkipped( 'git is not available' );
    }

    private static function hasGit()
    {
        $out = array();
        $rc = 1;
        @exec( 'git --version 2>/dev/null', $out, $rc );
        return $rc === 0;
    }

    private static function git( $dir, $args )
    {
        exec( 'git -C ' . escapeshellarg( $dir ) . ' ' . $args . ' 2>&1', $out, $rc );
        if ( $rc !== 0 )
            throw new RuntimeException( 'git ' . $args . ': ' . implode( "\n", $out ) );
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

    /**
     * @param array|null $active the active extensions (null: none, ActiveExtensionDocs disabled)
     * @param array $docDirs ExtensionDocDirs[]
     */
    private static function index( $active = array( 'foo', 'baz', 'qux', 'inroot' ), array $docDirs = array() )
    {
        $allowlist = new expHelpAllowlist( array( 'root' => self::$root, 'roots' => array( 'doc' ),
                                                  'active_extensions' => $active === null ? array() : $active,
                                                  'extension_doc_dirs' => $docDirs ) );
        return new expHelpDocumentIndex( $allowlist, array( 'cache_dir' => false, 'git_history' => false, 'audience' => new expHelpAudience() ) );
    }

    /** HE-01 */
    public function testActiveExtensionPagesAreShown()
    {
        $index = self::index();
        foreach ( array( 'ext/foo/index.md', 'ext/foo/usage/editors.md', 'ext/foo/README.md', 'ext/foo/CHANGELOG',
                         'ext/foo/LICENSE', 'ext/baz/README.md', 'ext/qux/README.md', 'ext/inroot/guide.md', 'ext/inroot/README.md' ) as $id )
        {
            $this->assertNotNull( $index->document( $id ), $id );
            $this->assertNotNull( $index->source( $id ), $id );
        }
        $this->assertNotNull( $index->asset( 'ext/foo/img/shot.png' ) );
        $this->assertSame( 'extension/foo/README.md', $index->document( 'ext/foo/README.md' )['path'] );
        $this->assertSame( 'extension/foo/doc/index.md', $index->document( 'ext/foo/index.md' )['path'] );
        $this->assertSame( 'text', $index->document( 'ext/foo/LICENSE' )['format'] );
    }

    public static function refused()
    {
        return array(
            array( 'ext/foo/tool' ), array( 'ext/foo/source.php' ), array( 'ext/foo/page.html' ), array( 'ext/foo/NOTES.md' ),
            array( 'ext/foo/ezinfo.php' ), array( 'ext/foo/classes/foo.php' ), array( 'ext/foo/../classes/foo.php' ),
            array( 'ext/foo/../settings/foo.ini' ), array( 'ext/foo/settings/foo.ini' ), array( 'ext/foo/untracked.md' ),
            array( 'ext/foo/README-local.md' ), array( 'ext/bar/index.md' ), array( 'ext/bar/README.md' ),
            array( 'ext/inroot/classes/x.php' ), array( 'ext/inroot/../../doc/en/us/help/start.md' ),
            array( 'extension/foo/README.md' ), array( 'extension/foo/classes/foo.php' ), array( 'ext/foo/.git/config' ),
            array( 'ext/../extension/foo/classes/foo.php' ), array( 'ext/foo/%2e%2e/classes/foo.php' ),
        );
    }

    /** HE-02 */
    #[PHPUnit\Framework\Attributes\DataProvider( 'refused' )]
    public function testRefused( $id )
    {
        $index = self::index();
        $this->assertNull( $index->document( $id ), $id );
        $this->assertNull( $index->source( $id ), $id );
        $this->assertNull( $index->asset( $id ), $id );
        foreach ( array_keys( $index->documents() ) as $listed )
            $this->assertStringNotContainsString( 'classes/', $listed );
    }

    /** HE-02: every listed file is a document or an image of an active extension, below its doc/ or a top file */
    public function testOnlyDocTreesAndTopFiles()
    {
        $allowlist = new expHelpAllowlist( array( 'root' => self::$root, 'roots' => array( 'doc' ), 'active_extensions' => array( 'foo', 'baz', 'qux', 'inroot' ) ) );
        foreach ( $allowlist->entries() as $path => $entry )
        {
            $this->assertMatchesRegularExpression( '#^(doc/|extension/(foo|baz|qux|inroot)/(doc/|(README|CHANGELOG|LICENSE)[^/]*$))#', $path );
            $this->assertDoesNotMatchRegularExpression( '/\.(php|html|ini)$/', $path );
        }
        $this->assertTrue( $allowlist->isExtensionTopFile( 'readme.markdown' ) );
        $this->assertFalse( $allowlist->isExtensionTopFile( 'NOTES.md' ) );
        $this->assertFalse( $allowlist->isExtensionTopFile( '.README' ) );
        $this->assertFalse( $allowlist->isExtensionTopFile( 'doc/README.md' ) );
        $this->assertSame( array( 'foo', 'baz', 'qux', 'inroot' ), $allowlist->extensions() );
        $odd = new expHelpAllowlist( array( 'root' => self::$root, 'active_extensions' => array( '../x', 'a/b', '.hidden', 'ok', 'ok' ) ) );
        $this->assertSame( array( 'ok' ), $odd->extensions() );
    }

    /** HE-03 */
    public function testDocFileWinsOverTopFile()
    {
        $index = self::index();
        $this->assertSame( 'extension/qux/doc/README.md', $index->document( 'ext/qux/README.md' )['path'] );
        $this->assertSame( 'Qux from doc', $index->document( 'ext/qux/README.md' )['title'] );
        $this->assertSame( 'extension/qux/doc/README.md', $index->pathOf( 'ext/qux/README.md' ) );
        $this->assertSame( 'extension/foo/README.md', $index->pathOf( 'ext/foo/README.md' ) );
        $this->assertSame( 'ext/foo/README.md', $index->idOf( 'extension/foo/README.md' ) );
        $this->assertSame( 'ext/foo/usage/editors.md', $index->idOf( 'extension/foo/doc/usage/editors.md' ) );
        $this->assertNull( $index->idOf( 'extension/foo/NOTES.md' ) );
        $this->assertNull( $index->idOf( 'extension/bar/README.md' ) );
    }

    /** HE-04 */
    public function testTypeCategoryFolderAndList()
    {
        $index = self::index();
        $doc = $index->document( 'ext/foo/index.md' );
        $this->assertSame( 'extension', $doc['type'] );
        $this->assertSame( 'extensions', $doc['category'] );
        $this->assertArrayHasKey( 'extensions', $index->categories() );
        $this->assertGreaterThanOrEqual( 7, $index->categories()['extensions']['count'] );

        $tree = $index->tree( 'ext' );
        $this->assertSame( array( 'baz', 'foo', 'inroot', 'qux' ), array_map( function ( $n ) { return $n['name']; }, $tree['children'] ) );
        $this->assertSame( 'Extensions', expHelpNavigation::folderTitle( $index->tree()['children'][array_search( 'ext', array_column( $index->tree()['children'], 'name' ) )], array() ) );

        $list = $index->extensions();
        $this->assertSame( array( 'baz', 'foo', 'inroot', 'qux' ), array_keys( $list ) );
        $this->assertSame( 'ext/foo/index.md', $list['foo']['index_id'] );
        $this->assertSame( 'Foo documentation', $list['foo']['title'] );
        $this->assertSame( 'ext/baz/README.md', $list['baz']['index_id'] );
        $this->assertSame( 1, $list['baz']['count'] );
        $ids = array_column( $list['foo']['pages'], 'id' );
        $this->assertSame( 'ext/foo/index.md', $ids[0], 'the index first' );
        foreach ( array( 'ext/foo/README.md', 'ext/foo/CHANGELOG', 'ext/foo/LICENSE', 'ext/foo/usage/editors.md' ) as $id )
            $this->assertContains( $id, $ids );
        $this->assertSame( 'usage', $list['foo']['pages'][array_search( 'ext/foo/usage/editors.md', $ids )]['folder'] );
    }

    /** HE-04: exphelp/extensions/<name> finds the extension without case, refuses other names, groups by folder */
    public function testExtensionsViewHelpers()
    {
        $list = self::index()->extensions();
        $this->assertSame( 'foo', \Exponential\View\Kernel\Exphelp\Extensions::find( $list, 'FOO' )['name'] );
        foreach ( array( 'bar', '../foo', 'foo/doc', '', 'ext' ) as $name )
            $this->assertNull( \Exponential\View\Kernel\Exphelp\Extensions::find( $list, $name ), $name );
        $folders = \Exponential\View\Kernel\Exphelp\Extensions::folders( $list['foo'] );
        $this->assertSame( array( '', 'usage' ), array_column( $folders, 'name' ) );
        $this->assertSame( 'ext/foo/index.md', $folders[0]['pages'][0]['id'] );
        $this->assertSame( array( 'ext/foo/usage/editors.md' ), array_column( $folders[1]['pages'], 'id' ) );
    }

    /** HE-04: the viewer's breadcrumb goes through Extensions; no "Edit on GitHub" of the installation's repository */
    public function testViewerBreadcrumbAndEditLink()
    {
        $index = self::index();
        $path = \Exponential\View\Kernel\Exphelp\Viewer::breadcrumb( $index, 'ext/foo/usage/editors.md', 'Using foo' );
        $this->assertSame( 'exphelp/extensions', $path[1]['url'] );
        $this->assertStringContainsString( 'ext/foo/index.md', (string)$path[2]['url'], 'the extension\'s overview' );
        $this->assertSame( 'Using foo', end( $path )['text'] );
        $path = \Exponential\View\Kernel\Exphelp\Viewer::breadcrumb( $index, 'ext/baz/README.md', 'Baz' );
        $this->assertSame( 'exphelp/extensions/baz', $path[2]['url'], 'without an overview: the extension\'s index' );
        $base = 'https://github.com/se7enxweb/exponential/blob/main/';
        $this->assertSame( '', \Exponential\View\Kernel\Exphelp\Viewer::editUrl( $base, $index->document( 'ext/foo/index.md' ) ) );
        $this->assertSame( $base . 'doc/en/us/help/start.md', \Exponential\View\Kernel\Exphelp\Viewer::editUrl( $base, array( 'path' => 'doc/en/us/help/start.md' ) ) );
        $this->assertSame( '', \Exponential\View\Kernel\Exphelp\Viewer::editUrl( '', array( 'path' => 'doc/en/us/help/start.md' ) ) );
    }

    /** HE-05 */
    public function testWithoutActiveExtensionsOnlyTheNamedOnes()
    {
        $index = self::index( null, array( 'bar' ) );
        $this->assertNotNull( $index->document( 'ext/bar/index.md' ) );
        $this->assertNotNull( $index->document( 'ext/bar/README.md' ) );
        $this->assertNull( $index->document( 'ext/foo/index.md' ) );
        $none = self::index( null );
        foreach ( array_keys( $none->documents() ) as $id )
            $this->assertStringStartsNotWith( 'ext/', $id );
    }

    /** HE-06 */
    public function testSearchFilterByExtension()
    {
        $index = self::index();
        $search = new expHelpSearch( $index, array( 'cacheDir' => null ) );
        $all = $search->search( 'foo' );
        $this->assertGreaterThan( 0, $all['total'] );
        $only = $search->search( '', array( 'extension' => 'foo' ), 50 );
        $this->assertSame( 5, $only['total'], 'index, usage/editors, README, CHANGELOG, LICENSE' );
        foreach ( $only['results'] as $hit )
            $this->assertStringStartsWith( 'ext/foo/', $hit['id'] );
        $baz = $search->search( '', array( 'extension' => 'baz' ), 50 );
        $this->assertSame( array( 'ext/baz/README.md' ), array_column( $baz['results'], 'id' ) );
        $this->assertSame( array( 'audience' => array(), 'category' => array(), 'type' => array(), 'extension' => array( 'foo' ) ),
                           expHelpSearch::cleanFilters( array( 'extension' => 'foo,../x' ) ) );
    }
}
