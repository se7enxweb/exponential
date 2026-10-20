<?php
/**
 * What the help may serve (doc/en/us/features/6.0/help-system.md, "Security"): only git-tracked files below doc/, never
 * anything else, on a fixture installation: a throwaway git repository in the temporary directory with tracked pages,
 * an untracked page, a tracked symbolic link that points out of doc/, a link to a directory outside, a root *.md,
 * settings, ai/, doxygen output, a file with a wrong suffix and one over the size limit. No database.
 *
 *  HX-01  tracked pages and images are allowed, with their metadata (title, audience, category, order, headings)
 *  HX-02  traversal in every spelling is refused: .., %2e%2e, absolute, backslashes, NUL, doubled dots, ./ tricks
 *  HX-03  untracked files, root *.md, settings, ai/, doxygen/, wrong suffixes, oversize files are refused
 *  HX-04  a tracked symbolic link (file or directory) that leaves doc/ is refused; source()/asset() never read it
 *  HX-05  the index of the real installation: the untracked quick-location-buttons page (if present on disk) and
 *         every root *.md are refused; every allowed id is tracked and below doc/
 *  HX-06  tree order and neighbours(); front matter is not shown as text
 *  HX-07  the cache follows a change (signature), a stale cache entry for a file that is gone is not served
 *  HX-08  StatFilesInterval: the look at the files is reused within the interval, never after it
 *  HX-09  the allowlist stamp sees a same-size rewrite of a source file within the same second
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpDocumentIndexTest extends PHPUnit\Framework\TestCase
{
    /** @var string the fixture installation */
    private static $root = '';
    /** @var string a directory beside it: what a symbolic link escapes to */
    private static $outside = '';

    public static function setUpBeforeClass(): void
    {
        if ( !self::hasGit() )
            return;
        $base = sys_get_temp_dir() . '/exphelp-' . bin2hex( random_bytes( 6 ) );
        self::$root = $base . '/install';
        self::$outside = $base . '/outside';
        $files = array(
            'doc/en/us/help/start.md' => "---\ntitle: Getting started\naudience: editor\ncategory: getting-started\norder: 1\n---\n# Start here\n\nWelcome.\n\n## First step\n\nSee [next](editing.md).\n",
            'doc/en/us/help/editing.md' => "---\naudience: [editor, user]\norder: 2\n---\n# Editing content\n\n## Store a draft\n\nText.\n",
            'doc/en/us/help/zz-last.md' => "# Zebra\n\norder by title\n",
            'doc/en/us/features/6.0/feature.md' => "# A feature\n\n```php\necho 1;\n```\n",
            'doc/notes.txt' => "Plain text notes\n",
            'doc/README' => "A readme without suffix\n",
            'doc/en/us/help/img/shot.png' => base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==' ),
            'doc/en/us/help/img/evil.svg' => '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"/>',
            'doc/en/us/help/page.php' => "<?php echo 'never';\n",
            'doc/en/us/help/page.html' => "<script>alert(1)</script>\n",
            'doc/en/us/doxygen/html/index.md' => "# generated\n",
            'doc/en/us/doxygen/index.html' => "<html></html>\n",
            'doc/en/us/help/.hidden.md' => "# hidden\n",
            'doc/en/us/help/big.md' => "# Big\n\n" . str_repeat( "line of text\n", 100000 ),
            'AGENTS.md' => "# Read FIRST - private\n",
            'README.md' => "# root readme\n",
            'settings/site.ini' => "[DatabaseSettings]\nPassword=secret\n",
            'ai/doc/cli.md' => "# private\n",
            'config.php' => "<?php // private\n",
        );
        foreach ( $files as $path => $content )
        {
            @mkdir( dirname( self::$root . '/' . $path ), 0777, true );
            file_put_contents( self::$root . '/' . $path, $content );
        }
        @mkdir( self::$outside, 0777, true );
        file_put_contents( self::$outside . '/secret.md', "# Outside secret\n" );
        file_put_contents( self::$outside . '/secret.png', $files['doc/en/us/help/img/shot.png'] );
        // tracked symbolic links leaving doc/
        symlink( '../../settings/site.ini', self::$root . '/doc/en/us/help/link-to-settings.md' );
        symlink( self::$outside . '/secret.md', self::$root . '/doc/en/us/help/link-absolute.md' );
        symlink( self::$outside, self::$root . '/doc/linkdir' );
        symlink( '../../AGENTS.md', self::$root . '/doc/en/us/help/agents.md' );
        symlink( self::$outside . '/secret.png', self::$root . '/doc/en/us/help/img/outside.png' );
        // a symbolic link inside doc/ to a tracked page (allowed or refused, never something else)
        symlink( 'start.md', self::$root . '/doc/en/us/help/alias.md' );
        self::git( 'init -q' );
        self::git( 'config user.email t@example.invalid' );
        self::git( 'config user.name T' );
        self::git( 'add -A' );
        self::git( 'commit -q -m fixture' );
        // created after the commit: untracked
        file_put_contents( self::$root . '/doc/en/us/help/untracked.md', "# Untracked\n" );
        file_put_contents( self::$root . '/doc/en/us/help/img/untracked.png', $files['doc/en/us/help/img/shot.png'] );
    }

    public static function tearDownAfterClass(): void
    {
        if ( self::$root !== '' )
            self::remove( dirname( self::$root ) );
        if ( method_exists( 'expHelpDocumentIndex', 'setInstance' ) )
            expHelpDocumentIndex::setInstance( null );
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

    private static function git( $args )
    {
        exec( 'git -C ' . escapeshellarg( self::$root ) . ' ' . $args . ' 2>&1', $out, $rc );
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

    /** the index of the fixture installation (B's constructor options; the one place to change) */
    private static function index()
    {
        $allowlist = new expHelpAllowlist( array( 'root' => self::$root, 'roots' => array( 'doc' ), 'max_size' => 1024 * 1024 ) );
        return new expHelpDocumentIndex( $allowlist, array( 'root' => self::$root, 'cache_dir' => false ) );
    }

    /** HX-01 */
    public function testTrackedPagesAllowed()
    {
        $index = self::index();
        foreach ( array( 'help/start.md', 'help/editing.md', 'help/zz-last.md', 'features/6.0/feature.md', 'notes.txt' ) as $id )
        {
            $this->assertTrue( $index->isAllowed( $id ), $id );
            $this->assertNotNull( $index->document( $id ), $id );
            $this->assertNotNull( $index->source( $id ), $id );
        }
        $this->assertTrue( $index->isAllowed( 'help/img/shot.png' ) );
        $asset = $index->asset( 'help/img/shot.png' );
        $this->assertNotNull( $asset );
        $this->assertSame( realpath( self::$root . '/doc/en/us/help/img/shot.png' ), realpath( $asset ) );
        $this->assertNull( $index->document( 'help/img/shot.png' ), 'an image is not a page' );

        $doc = $index->document( 'help/start.md' );
        foreach ( array( 'id', 'path', 'title', 'summary', 'type', 'category', 'audience', 'order', 'suffix', 'headings', 'mtime', 'size' ) as $k )
            $this->assertArrayHasKey( $k, $doc, $k );
        $this->assertSame( 'help/start.md', $doc['id'] );
        $this->assertSame( 'doc/en/us/help/start.md', $doc['path'] );
        $this->assertSame( 'Getting started', $doc['title'] );
        $this->assertSame( array( 'editor' ), array_values( (array)$doc['audience'] ) );
        $this->assertSame( 'getting-started', $doc['category'] );
        $this->assertSame( 1, (int)$doc['order'] );
        $this->assertContains( 'First step', array_column( $doc['headings'], 'text' ) );
        $this->assertEqualsCanonicalizing( array( 'editor', 'user' ), array_values( (array)$index->document( 'help/editing.md' )['audience'] ) );
        $this->assertSame( 'Editing content', $index->document( 'help/editing.md' )['title'], 'the first heading is the title' );
        $this->assertStringNotContainsString( 'audience:', (string)$index->document( 'help/start.md' )['summary'], 'front matter is not text' );
        foreach ( $index->documents() as $id => $d )
        {
            $this->assertSame( $id, $d['id'] );
            $this->assertSame( 'doc/' . $id, $d['path'] );
        }
    }

    public static function traversal()
    {
        return array(
            array( '../settings/site.ini' ), array( '../AGENTS.md' ), array( '../config.php' ), array( 'help/../../AGENTS.md' ),
            array( '%2e%2e/AGENTS.md' ), array( '%252e%252e/AGENTS.md' ), array( '..%2fAGENTS.md' ), array( '..\\AGENTS.md' ),
            array( '/etc/passwd' ), array( '/AGENTS.md' ), array( '//AGENTS.md' ), array( '/tmp/x.md' ),
            array( "help/start.md\0.png" ), array( "help/start.md\0" ), array( '....//AGENTS.md' ), array( '.../AGENTS.md' ),
            array( './../AGENTS.md' ), array( 'help/./../../settings/site.ini' ), array( 'doc/../AGENTS.md' ),
            array( 'linkdir/secret.md' ), array( '' ), array( '.' ), array( '..' ), array( 'help/' ), array( 'help' ),
            array( 'file:///etc/passwd' ), array( 'https://example.com/x.md' ),
        );
    }

    /** HX-02 */
    #[PHPUnit\Framework\Attributes\DataProvider( 'traversal' )]
    public function testTraversalRefused( $id )
    {
        $index = self::index();
        $this->assertFalse( $index->isAllowed( $id ), var_export( $id, true ) );
        $this->assertNull( $index->document( $id ) );
        $this->assertNull( $index->source( $id ) );
        $this->assertNull( $index->asset( $id ) );
    }

    public static function refused()
    {
        return array(
            'untracked page'   => array( 'help/untracked.md' ),
            'untracked image'  => array( 'help/img/untracked.png' ),
            'svg'              => array( 'help/img/evil.svg' ),
            'php'              => array( 'help/page.php' ),
            'html'             => array( 'help/page.html' ),
            'doxygen md'       => array( 'doxygen/html/index.md' ),
            'doxygen html'     => array( 'doxygen/index.html' ),
            'hidden'           => array( 'help/.hidden.md' ),
            'oversize'         => array( 'help/big.md' ),
            'root md'          => array( 'AGENTS.md' ),
            'root readme'      => array( 'README.md' ),
            'settings'         => array( 'settings/site.ini' ),
            'ai'               => array( 'ai/doc/cli.md' ),
            'not there'        => array( 'help/nothing.md' ),
            'case'             => array( 'HELP/START.MD' ),
        );
    }

    /** HX-03 */
    #[PHPUnit\Framework\Attributes\DataProvider( 'refused' )]
    public function testRefused( $id )
    {
        $index = self::index();
        $this->assertFalse( $index->isAllowed( $id ), $id );
        $this->assertNull( $index->document( $id ), $id );
        $this->assertNull( $index->source( $id ), $id );
        $this->assertNull( $index->asset( $id ), $id );
        $this->assertArrayNotHasKey( $id, $index->documents() );
    }

    /** HX-04 */
    public function testSymlinkEscapeRefused()
    {
        $index = self::index();
        foreach ( array( 'help/link-to-settings.md', 'help/link-absolute.md', 'linkdir/secret.md', 'help/agents.md', 'help/img/outside.png' ) as $id )
        {
            $this->assertFalse( $index->isAllowed( $id ), $id . ' is tracked but leaves doc/' );
            $this->assertNull( $index->source( $id ), $id );
            $this->assertNull( $index->asset( $id ), $id );
        }
        // whatever the alias inside doc/ gives, it is the tracked page's text or nothing
        $alias = $index->source( 'help/alias.md' );
        $this->assertTrue( $alias === null || $alias === file_get_contents( self::$root . '/doc/en/us/help/start.md' ) );
        foreach ( $index->documents() as $d )
        {
            $real = realpath( self::$root . '/' . $d['path'] );
            $this->assertNotFalse( $real );
            $this->assertStringStartsWith( realpath( self::$root . '/doc' ) . '/', $real, $d['id'] );
            $this->assertStringNotContainsString( 'Outside secret', (string)$index->source( $d['id'] ) );
            $this->assertStringNotContainsString( 'Password=secret', (string)$index->source( $d['id'] ) );
        }
        // the allowlist itself
        $allow = new expHelpAllowlist( array( 'root' => self::$root, 'roots' => array( 'doc' ), 'max_size' => 1024 * 1024 ) );
        foreach ( $allow->files() as $path )
        {
            $this->assertStringStartsWith( 'doc/', $path );
            $this->assertStringNotContainsString( '..', $path );
            $this->assertTrue( $allow->allows( $path ), $path );
        }
        $this->assertFalse( $allow->allows( 'doc/en/us/help/link-absolute.md' ) );
        $this->assertFalse( $allow->allows( 'AGENTS.md' ) );
        $this->assertFalse( $allow->allows( 'doc/../AGENTS.md' ) );
        $this->assertTrue( $allow->allows( 'doc/en/us/help/start.md' ) );
    }

    /** HX-05 */
    public function testRealInstallation()
    {
        $repo = dirname( __DIR__, 5 );
        $out = array();
        $rc = 1;
        exec( 'git -C ' . escapeshellarg( $repo ) . ' ls-files -- doc 2>/dev/null', $out, $rc );
        if ( $rc !== 0 || !$out )
            $this->markTestSkipped( 'not a git checkout' );
        $tracked = array_flip( $out );
        $allow = new expHelpAllowlist( array( 'root' => $repo ) );
        foreach ( $allow->files() as $path )
        {
            $this->assertArrayHasKey( $path, $tracked, $path . ' is served but not tracked' );
            $this->assertStringStartsWith( 'doc/', $path );
            $this->assertStringNotContainsString( 'doc/en/us/doxygen/', $path );
        }
        exec( 'git -C ' . escapeshellarg( $repo ) . ' ls-files --others --exclude-standard -- doc 2>/dev/null', $untracked );
        foreach ( array_merge( $untracked, array( 'doc/en/us/features/6.0/quick-location-buttons.md' ) ) as $path )
        {
            if ( isset( $tracked[$path] ) )
                continue;
            $this->assertFalse( $allow->allows( $path ), $path . ' is not tracked' );
        }
        foreach ( glob( $repo . '/*.md' ) ?: array() as $file )
            $this->assertFalse( $allow->allows( basename( $file ) ), basename( $file ) );
    }

    /** HX-06 */
    public function testOrderAndNeighbours()
    {
        $index = self::index();
        $n = $index->neighbours( 'help/editing.md' );
        $this->assertArrayHasKey( 'prev', $n );
        $this->assertArrayHasKey( 'next', $n );
        $this->assertSame( 'help/start.md', $n['prev']['id'] ?? null, 'order 1 before order 2' );
        $this->assertNull( $index->neighbours( 'help/untracked.md' )['prev'] );
        $this->assertNull( $index->neighbours( '../AGENTS.md' )['next'] );
        $this->assertNotEmpty( $index->tree() );
    }

    /** HX-07 */
    public function testSignatureFollowsChanges()
    {
        $one = self::index()->signature();
        $this->assertNotSame( '', $one );
        $this->assertSame( $one, self::index()->signature(), 'stable without a change' );
        file_put_contents( self::$root . '/doc/en/us/help/zz-last.md', "# Zebra changed\n" );
        touch( self::$root . '/doc/en/us/help/zz-last.md', time() + 5 );
        self::git( 'add -A doc/en/us/help/zz-last.md' );
        self::git( 'commit -q -m change' );
        $this->assertNotSame( $one, self::index()->signature(), 'a commit changes the signature' );
        $this->assertSame( 'Zebra changed', self::index()->document( 'help/zz-last.md' )['title'] );
    }

    /** an index of the fixture with a cache directory and a StatFilesInterval */
    private static function cachedIndex( $interval )
    {
        $allowlist = new expHelpAllowlist( array( 'root' => self::$root, 'roots' => array( 'doc' ), 'max_size' => 1024 * 1024 ) );
        $dir = dirname( self::$root ) . '/cache';
        @mkdir( $dir, 0777, true );
        return new expHelpDocumentIndex( $allowlist, array( 'root' => self::$root, 'cache_dir' => $dir, 'stat_interval' => $interval ) );
    }

    /** HX-08 StatFilesInterval: the look at the files is reused by other instances for that long, never longer */
    public function testSignatureIntervalReusesTheLookAtTheFiles()
    {
        $file = self::$root . '/doc/en/us/help/editing.md';
        $checked = dirname( self::$root ) . '/cache/' . expHelpDocumentIndex::CHECKED_SIGNATURE_FILE;
        $one = self::cachedIndex( 60 )->signature();
        $this->assertSame( $one, self::cachedIndex( 0 )->signature(), 'the same signature as without the interval' );
        $this->assertFileExists( $checked );

        // an edit without a commit: seen at once without the interval, after the interval with it
        file_put_contents( $file, file_get_contents( $file ) . "\nMore text.\n" );
        touch( $file, time() + 5 );
        $fresh = self::cachedIndex( 0 )->signature();
        $this->assertNotSame( $one, $fresh, 'without the interval the edit is seen at once' );
        $this->assertSame( $one, self::cachedIndex( 60 )->signature(), 'within the interval the last look is reused' );

        // the look is too old: the files are looked at again, and that look is reused in turn
        $data = include $checked;
        $data['checked'] = time() - 120;
        file_put_contents( $checked, "<?php\nreturn " . var_export( $data, true ) . ";\n" );
        if ( function_exists( 'opcache_invalidate' ) )
            @opcache_invalidate( $checked, true );
        $this->assertSame( $fresh, self::cachedIndex( 60 )->signature(), 'an old look is not reused' );
        file_put_contents( $file, file_get_contents( $file ) . "Even more.\n" );
        touch( $file, time() + 10 );
        $two = self::cachedIndex( 0 )->signature();
        $this->assertNotSame( $fresh, $two );
        $this->assertSame( $fresh, self::cachedIndex( 60 )->signature(), 'the new look is reused' );

        // a rebuild always looks at the files
        file_put_contents( $file, file_get_contents( $file ) . "And more.\n" );
        touch( $file, time() + 15 );
        $rebuilt = self::cachedIndex( 60 )->rebuild();
        $this->assertNotSame( $two, $rebuilt['signature'], 'a rebuild looks at the files' );
        $this->assertSame( $rebuilt['signature'], self::cachedIndex( 60 )->signature(), 'and leaves its look for the others' );

        // a commit changes the git index: seen at once, whatever the interval
        file_put_contents( $file, file_get_contents( $file ) . "Committed.\n" );
        self::git( 'add -A doc/en/us/help/editing.md' );
        self::git( 'commit -q -m edit' );
        $this->assertNotSame( $rebuilt['signature'], self::cachedIndex( 60 )->signature(), 'a commit is seen at once' );
        $this->assertSame( self::cachedIndex( 0 )->signature(), self::cachedIndex( 60 )->signature() );
    }

    /** HX-09 a source file rewritten with the same size within the same second still changes the allowlist stamp */
    public function testStampSeesASameSizeRewriteInTheSameSecond()
    {
        $manifest = self::$root . '/' . expHelpAllowlist::MANIFEST;
        @mkdir( dirname( $manifest ), 0777, true );
        $allowlist = new expHelpAllowlist( array( 'root' => self::$root, 'roots' => array( 'doc' ), 'max_size' => 1024 * 1024 ) );
        $now = time();
        file_put_contents( $manifest, "aaaa\n" );
        touch( $manifest, $now );
        $one = $allowlist->stamp();
        file_put_contents( $manifest, "bbbb\n" );
        touch( $manifest, $now );
        $this->assertNotSame( $one, $allowlist->stamp(), 'same size, same second, other content' );
        unlink( $manifest );
        $this->assertNotSame( $one, $allowlist->stamp(), 'a removed file changes it too' );
    }
}
