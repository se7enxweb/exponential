<?php
/**
 * The administration views and endpoints take their options as view parameters, /(name)/value, never as GET query
 * parameters (the rule of AdminHelpViewParametersOnlyTest, for every other view that had a query string option).
 * DB-less: reads the views, module.php, the templates and the scripts.
 *
 *  VP-01  the converted views read no $_GET, no getVariable()/hasGetVariable(), and no $http->variable() (GET too)
 *  VP-02  their module.php declares the options as unordered_params
 *  VP-03  their templates of every design build no "?name=" option string and have no GET form
 *  VP-04  their scripts build no "?name=" option string
 *  VP-05  their values travel in one encoding (expViewParameters::encode()): the views write none by hand (no base64url,
 *         no double rawurlencode(), no expLogPermalink::encode()), the templates write a free value with the operator
 *         view_parameter (never urlencode), the scripts with window.a4ViewParameter.encode() (never encodeURIComponent
 *         alone after a "/(name)/")
 *  VP-06  a4_url.js is the same file in admin and admin4, and its window.a4ViewParameter writes exactly what
 *         expViewParameters::encode() writes and decodes it back (in node; skipped without node outside CI)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminNodeBinary.php';

class AdminViewParametersOnlyTest extends PHPUnit\Framework\TestCase
{
    /** The converted views, under kernel/private/classes/views/ (and one module script) */
    const VIEWS = array(
        'content/job.php', 'content/jobs.php', 'content/subitemsexport.php', 'content/bookmark.php', 'content/draft.php',
        'content/urlalias_global.php', 'content/urlalias_wildcard.php',
        'contentcheck/storage.php', 'contentcheck/storageaction.php', 'contentcheck/brokenrelations.php',
        'contentcheck/unreferenced.php', 'contentcheck/fixfiles.php',
        'contentfinder/search.php', 'settings/view.php',
        'role/list.php', 'user/unactivated.php', 'url/list.php', 'search/stats.php', 'pdf/list.php',
        'mailpreferences/admin.php', 'shop/orderreceipt.php',
        'setup/cronjobsstream.php', 'setup/staticcachestream.php', 'setup/logsfeed.php', 'setup/languages.php',
        'setup/sitedata.php', 'setup/cronjobs.php', 'setup/logs.php',
        'visual/templatelist.php',
        'package/list.php', 'package/compare.php', 'package/view.php',
    );

    /** module => view => view parameters that module.php must declare */
    const DECLARED = array(
        'content' => array( 'job' => array( 'json', 'log' ),
                            'jobs' => array( 'state', 'type', 'user', 'sort', 'order', 'offset' ),
                            'subitemsexport' => array( 'columns', 'sort', 'order' ),
                            'bookmark' => array( 'q' ), 'draft' => array( 'q' ),
                            'urltranslator' => array( 'q' ), 'urlwildcards' => array( 'q' ) ),
        'contentfinder' => array( 'search' => array( 'text', 'type' ) ),
        'settings' => array( 'view' => array( 'q', 'scope', 'changed', 'compare' ) ),
        'role' => array( 'list' => array( 'q' ) ),
        'user' => array( 'unactivated' => array( 'q' ) ),
        'url' => array( 'list' => array( 'q' ) ),
        'pdf' => array( 'list' => array( 'search' ) ),
        'shop' => array( 'orderreceipt' => array( 'download' ) ),
        'setup' => array( 'staticcachestream' => array( 'siteaccess', 'maxpages', 'maxdepth' ),
                          'cronjobsstream' => array( 'offset' ), 'languages' => array( 'show' ), 'sitedata' => array( 'filter' ) ),
        'visual' => array( 'templatelist' => array( 'filter' ) ),
        'package' => array( 'list' => array( 'search', 'type', 'state', 'sort' ), 'view' => array( 'type', 'search', 'offset' ) ),
    );

    /** The templates (relative to design/<design>/templates/) of the converted views */
    const TEMPLATES = array(
        'content/job.tpl', 'content/jobs.tpl', 'content/bookmark.tpl', 'content/draft.tpl',
        'content/urlalias_global.tpl', 'content/urlalias_wildcard.tpl',
        'contentcheck/*.tpl', 'contentfinder/*.tpl', 'settings/view.tpl',
        'role/list.tpl', 'user/unactivated.tpl', 'url/list.tpl', 'search/stats.tpl', 'oauthadmin/keys.tpl',
        'pdf/list.tpl', 'mailpreferences/*.tpl', 'shop/orderreceipt.tpl',
        'setup/cronjobs.tpl', 'setup/cache.tpl', 'setup/logs*.tpl', 'setup/languages.tpl', 'setup/sitedata.tpl',
        'visual/templatelist.tpl', 'package/*.tpl', 'package/view/*.tpl',
    );

    /** The scripts (relative to design/<design>/javascript/) */
    const SCRIPTS = array( 'ezajaxsubitems_expdatatable.js', 'page/setup_logs.js', 'page/setup_cronjobs.js',
                           'page/setup_cache_static.js', 'setup_logs.js', 'setup_cronjobs.js', 'setup_cache_static.js' );

    /** An option written as a query string */
    const QUERY_OPTION = '/[?&](?:amp;)?(?:q|json|log|state|type|user|sort|order|offset|columns|test|search|export|download|key|archive|file|rotation|Show|Filter|filterString|SearchText|SearchType|scope|changed|compare|Offset|SiteAccess|MaxPages|MaxDepth|Purge|Ajax|Status|UserID|NodeID|SelectAll|KindChoice|ClassChoice)=/';

    protected static function root()
    {
        return dirname( __DIR__, 3 );
    }

    protected static function relative( $file )
    {
        return substr( $file, strlen( self::root() ) + 1 );
    }

    /** @return string[] */
    protected static function viewFiles()
    {
        $root = self::root();
        $files = array();
        foreach ( self::VIEWS as $view )
            $files[] = $root . '/kernel/private/classes/views/' . $view;
        $files[] = $root . '/kernel/private/modules/oauthadmin/keys.php';
        return $files;
    }

    /** @return array<string,bool>|null the extensions with files in this repository's git index, null without git */
    protected static function trackedExtensions()
    {
        $out = array();
        $rc = 1;
        @exec( 'git -C ' . escapeshellarg( self::root() ) . ' ls-files -- extension 2>/dev/null', $out, $rc );
        if ( $rc !== 0 || !$out )
            return null;
        $names = array();
        foreach ( $out as $path )
        {
            $parts = explode( '/', $path );
            if ( isset( $parts[1] ) )
                $names[$parts[1]] = true;
        }
        return $names;
    }

    /** @return string[] */
    protected static function files( $kind, array $patterns )
    {
        $files = array();
        // the designs of this repository: an extension this repository does not track (a checkout such as
        // exp_adminui on a site, a git repository of its own with its own release) is not part of it
        $tracked = self::trackedExtensions();
        $extensionDesigns = array_filter( glob( self::root() . '/extension/*/design/*', GLOB_ONLYDIR ) ?: array(), function ( $dir ) use ( $tracked ) {
            $extension = dirname( $dir, 2 );
            if ( $tracked !== null )
                return isset( $tracked[basename( $extension )] );
            return !file_exists( $extension . '/.git' );
        } );
        foreach ( array_merge( glob( self::root() . '/design/*', GLOB_ONLYDIR ), $extensionDesigns ) as $design )
            foreach ( $patterns as $pattern )
                foreach ( glob( $design . '/' . $kind . '/' . $pattern ) ?: array() as $file )
                    $files[$file] = $file;
        sort( $files );
        return array_values( $files );
    }

    /** VP-01 */
    public function testViewsReadNoGetParameters()
    {
        $hits = array();
        foreach ( self::viewFiles() as $file )
        {
            $this->assertFileExists( $file );
            foreach ( file( $file ) as $n => $line )
            {
                if ( preg_match( '/\$_GET\b|\$_REQUEST\b|->hasGetVariable\(|->getVariable\(|\$http->(?:hasVariable|variable)\(|http_build_query\(/', $line ) )
                    $hits[] = self::relative( $file ) . ':' . ( $n + 1 ) . ': ' . trim( $line );
            }
        }
        $this->assertSame( array(), $hits, 'read the options as view parameters (expViewParameters::get())' );
    }

    /** VP-02 */
    public function testModulesDeclareTheViewParameters()
    {
        // read as text: including a module.php here would load the kernel
        foreach ( self::DECLARED as $module => $views )
        {
            $source = file_get_contents( self::root() . '/kernel/' . $module . '/module.php' );
            foreach ( $views as $view => $names )
            {
                $this->assertSame( 1, preg_match( '/\$ViewList\[[\'"]' . preg_quote( $view, '/' ) . '[\'"]\]\s*=\s*array\((.*?)\);\s*$/ms', $source, $m ), "$module/$view" );
                $this->assertSame( 1, preg_match( "/['\"]unordered_params['\"]\s*=>\s*array\((.*?)\)/s", $m[1], $u ), "$module/$view: unordered_params" );
                foreach ( $names as $name )
                    $this->assertMatchesRegularExpression( "/['\"]" . preg_quote( $name, '/' ) . "['\"]\s*=>\s*['\"][A-Za-z]+['\"]/", $u[1], "$module/$view: ($name)" );
            }
        }
    }

    /** VP-03 */
    public function testTemplatesBuildNoQueryOptions()
    {
        $files = self::files( 'templates', self::TEMPLATES );
        $this->assertGreaterThan( 30, count( $files ), 'the templates are found' );
        $hits = array();
        foreach ( $files as $file )
        {
            $source = file_get_contents( $file );
            foreach ( explode( "\n", $source ) as $n => $line )
                if ( preg_match( self::QUERY_OPTION, $line ) )
                    $hits[] = self::relative( $file ) . ':' . ( $n + 1 ) . ': ' . trim( $line );
            if ( preg_match_all( '/<form\b[^>]*>/i', $source, $m ) )
                foreach ( $m[0] as $form )
                    if ( preg_match( '/method\s*=\s*"?get/i', $form ) )
                        $hits[] = self::relative( $file ) . ': ' . $form;
        }
        $this->assertSame( array(), $hits, 'build /(name)/value addresses; filter forms are posted and answered with a 303' );
    }

    /** VP-04 */
    public function testScriptsBuildNoQueryOptions()
    {
        $files = self::files( 'javascript', self::SCRIPTS );
        $this->assertNotEmpty( $files );
        $hits = array();
        foreach ( $files as $file )
            foreach ( file( $file ) as $n => $line )
                if ( preg_match( self::QUERY_OPTION, $line ) )
                    $hits[] = self::relative( $file ) . ':' . ( $n + 1 ) . ': ' . trim( $line );
        $this->assertSame( array(), $hits );
    }

    /** VP-05 */
    public function testValuesTravelInOneEncoding()
    {
        $hits = array();
        foreach ( self::viewFiles() as $file )
            foreach ( file( $file ) as $n => $line )
                if ( preg_match( '/rawurlencode\(\s*rawurlencode\(|base64_encode\(|expLogPermalink::encode\(/', $line ) )
                    $hits[] = self::relative( $file ) . ':' . ( $n + 1 ) . ': ' . trim( $line );
        foreach ( self::files( 'templates', self::TEMPLATES ) as $file )
            foreach ( file( $file ) as $n => $line )
                if ( preg_match( '/\/\([a-z_]+\)\/[\'"][^}]*\|(?:raw)?urlencode/', $line ) )
                    $hits[] = self::relative( $file ) . ':' . ( $n + 1 ) . ': ' . trim( $line );
        foreach ( self::files( 'javascript', self::SCRIPTS ) as $file )
            foreach ( file( $file ) as $n => $line )
                if ( preg_match( '/\/\([a-z_]+\)\/[\'"]\s*\+\s*encodeURIComponent\(/', $line ) )
                    $hits[] = self::relative( $file ) . ':' . ( $n + 1 ) . ': ' . trim( $line );
        $this->assertSame( array(), $hits, 'write view parameter values with expViewParameters::encode()/suffix(), view_parameter or a4ViewParameter.encode()' );

        // the free values of the converted views use the shared encoding
        $root = self::root();
        foreach ( array( 'design/admin/templates/settings/view.tpl', 'design/admin4/templates/settings/view.tpl' ) as $tpl )
            $this->assertStringContainsString( "'/(q)/', \$hit.name|view_parameter", file_get_contents( "$root/$tpl" ), $tpl );
        foreach ( array( 'design/admin/javascript/page/setup_logs.js', 'design/admin4/javascript/page/setup_logs.js' ) as $js )
            $this->assertStringContainsString( 'window.a4ViewParameter.encode( value )', file_get_contents( "$root/$js" ), $js );
        $this->assertStringContainsString( '\\expViewParameters::encode(', file_get_contents( "$root/kernel/private/classes/views/contentfinder/search.php" ) );
        $this->assertStringContainsString( 'expViewParameters::encode(', file_get_contents( "$root/kernel/classes/logviewer/explogpermalink.php" ) );
    }

    /** VP-06 */
    public function testTheScriptHelperWritesTheKernelEncoding()
    {
        $root = self::root();
        $this->assertFileEquals( "$root/design/admin/javascript/a4_url.js", "$root/design/admin4/javascript/a4_url.js" );
        $node = AdminNodeBinary::require();
        $values = array( 'editor', 'example.com/a', '/var/site/storage/images/a/b (1).jpg', 'https://www.example.com/News?x=1&y=2#top',
                         '(q)', '~', '~2F', 'with space', 'a+b', '100%', '%2F', 'Müller', '日本語 テスト', "emoji \u{1F600}", '.', '..',
                         '...', 'back\\slash', "tab\there", "line\nbreak", 'quote " and \' too', '<b>&amp;</b>', 'a,b:c@d=e!f*g', 'x;y$z', '' );
        $dir = sys_get_temp_dir() . '/vp06-' . getmypid() . '-' . mt_rand();
        mkdir( $dir );
        $harness = "global.window = {};\nrequire(" . json_encode( "$root/design/admin4/javascript/a4_url.js" ) . ");\n"
                 . "const values = JSON.parse(require('fs').readFileSync(process.argv[2], 'utf8'));\n"
                 . "process.stdout.write(JSON.stringify(values.map((v) => { const e = window.a4ViewParameter.encode(v); return [e, window.a4ViewParameter.decode(decodeURIComponent(e))]; })));\n";
        file_put_contents( "$dir/h.js", $harness );
        file_put_contents( "$dir/values.json", json_encode( $values ) );
        try
        {
            $out = shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( "$dir/h.js" ) . ' ' . escapeshellarg( "$dir/values.json" ) . ' 2>&1' );
        }
        finally
        {
            @unlink( "$dir/h.js" );
            @unlink( "$dir/values.json" );
            @rmdir( $dir );
        }
        $got = json_decode( (string)$out, true );
        $this->assertIsArray( $got, (string)$out );
        foreach ( $values as $i => $value )
        {
            $this->assertSame( expViewParameters::encode( $value ), $got[$i][0], "encode: $value" );
            $this->assertSame( $value, $got[$i][1], "decode: $value" );
        }
    }
}
