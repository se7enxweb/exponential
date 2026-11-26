<?php
/**
 * The web servers hand image aliases made on demand to index_image.php, without a database:
 *
 *  IAW-01 - index_image.php answers content/image with its view parameters (more than six parts are no form) and
 *           everything else as a storage path
 *  IAW-02 - Velocity (qbix engine): index_image.php is an entry script; a storage image with no file and
 *           content/image go to it, and no other route takes them first
 *  IAW-03 - FrankenPHP/Caddy: the Caddyfile rewrites a storage image with no file and content/image to
 *           index_image.php before the file server, and never on to index.php
 *  IAW-04 - the php engine's router: a storage image with no file and content/image run index_image.php, a file
 *           that exists is sent by the server, a missing file elsewhere answers 404, dot segments never reach it
 *  IAW-05 - the cluster gateway (index_cluster.php): an image the cluster has no metadata for is handed to
 *           index_image.php (unless CLUSTER_IMAGE_ALIAS_ON_DEMAND is false), any other missing file stays a 404
 *  IAW-06 - .htaccess_root and .htaccess_root_static: the rules in the order that works, the same in both
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/imagealias/expImageAliasWebServersTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** A cluster gateway with no database: the metadata it is given, the hand-over recorded instead of run */
class expImageAliasWebServersTestGateway extends ezpClusterGateway
{
    /** @var array|false */
    public $metaData = false;

    /** @var string[] */
    public $calls = array();

    /** @var string|null */
    public $ranScript = null;

    public function connect()
    {
        $this->calls[] = 'connect';
    }

    public function fetchFileMetadata( $filepath )
    {
        $this->calls[] = 'metadata';
        return $this->metaData;
    }

    public function passthrough( $filepath, $filesize, $offset = false, $length = false )
    {
        $this->calls[] = 'passthrough';
    }

    public function close()
    {
        $this->calls[] = 'close';
    }

    protected function runImageEntryScript( $script )
    {
        $this->ranScript = $script;
    }
}

class expImageAliasWebServersTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname( __DIR__, 5 );
    }

    /**
     * IAW-01
     */
    public function testTheEntryScriptRoutesContentImageAndStoragePaths()
    {
        $this->assertSame( array( 'params', array( 'tok.sig', 'a_small.jpg' ) ),
                           expImageAliasServer::entryRoute( array( 'content', 'image', 'tok.sig', 'a_small.jpg' ) ) );
        $this->assertSame( array( 'params', array( '12', '3', 'eng-GB', 'small', 'a_small.jpg' ) ),
                           expImageAliasServer::entryRoute( array( 'content', 'image', '12', '3', 'eng-GB', 'small', 'a_small.jpg', '' ) ) );
        // More parts than any form has: no form (the placeholder with 404), as in the view
        $this->assertSame( array( 'params', array() ),
                           expImageAliasServer::entryRoute( array( 'content', 'image', '1', '2', '3', '4', '5', '6', '7' ) ) );
        $this->assertSame( array( 'storage', null ),
                           expImageAliasServer::entryRoute( array( 'var', 'site', 'storage', 'images', 'a', '1-1-eng-GB', 'a_small.jpg' ) ) );
        $this->assertSame( array( 'storage', null ), expImageAliasServer::entryRoute( array( 'content', 'view', 'full', '2' ) ) );
        $this->assertSame( array( 'storage', null ), expImageAliasServer::entryRoute( array() ) );
    }

    /**
     * IAW-02
     */
    public function testVelocityRoutesMissingImagesAndContentImageToTheEntryScript()
    {
        $this->assertContains( '/index_image.php', expVelocity::ENTRY_SCRIPTS );
        $this->assertFileExists( $this->root . '/index_image.php' );

        $expect = array(
            '/var/site/storage/images/a/1-1-eng-GB/a_small.jpg' => 'index_image.php',
            '/var/storage/images-versioned/12/3-eng-GB/a_small.jpg' => 'index_image.php',
            '/content/image/tok.sig/a_small.jpg' => 'index_image.php',
            '/site/content/image/tok.sig/a_small.jpg' => 'index_image.php',
            '/content/treemenu/2' => 'index_treemenu.php',
            '/api/ezp/v2/' => 'index_rest.php',
            '/content/view/full/2' => 'index.php',
            '/var/site/storage/original/image/a.jpg' => 'index.php',
            '/var/site/cache/public/a.css' => 'index.php',
            '/content/imagex/1' => 'index.php',
        );
        foreach ( $expect as $path => $script )
        {
            // The engine: the first pattern that matches, else index.php (Q_WebServer::frontController())
            $found = 'index.php';
            foreach ( expVelocity::FRONT_CONTROLLERS as $pattern => $candidate )
            {
                if ( preg_match( '~' . $pattern . '~', $path ) )
                {
                    $found = $candidate;
                    break;
                }
            }
            $this->assertSame( $script, $found, $path );
        }
        // A storage image that exists is a static file and never reaches a script
        $this->assertSame( 1, preg_match( '~' . expVelocity::STATIC_PATHS . '~', '/var/site/storage/images/a/1-1-eng-GB/a_small.jpg' ) );
    }

    /**
     * IAW-03
     */
    public function testTheCaddyfileRewritesMissingImagesBeforeTheFileServer()
    {
        $caddy = expVelocity::create( 'velocity.ini', 'frankenphp' )->caddyfileText();
        $image = strpos( $caddy, "\t\t@image {\n\t\t\tpath_regexp image ^/var/([^/]+/)?storage/images(-versioned)?/\n\t\t\tnot file\n\t\t}\n\t\trewrite @image /index_image.php\n" );
        $view = strpos( $caddy, "\t\t@imageview path_regexp imageview ^/([^/]+/)?content/image/\n\t\trewrite @imageview /index_image.php\n" );
        $fileServer = strpos( $caddy, "\t\tfile_server @static\n" );
        $this->assertIsInt( $image );
        $this->assertIsInt( $view );
        $this->assertIsInt( $fileServer );
        $this->assertLessThan( $fileServer, $image );
        $this->assertLessThan( $fileServer, $view );
        $this->assertStringContainsString( "\t\t@front not path /index_rest.php /index_treemenu.php /index_image.php\n", $caddy );
    }

    /**
     * IAW-04
     */
    public function testThePhpEngineRouterRunsTheEntryScriptForMissingImages()
    {
        require_once $this->root . '/kernel/private/classes/commands/velocity-router.php';
        $base = 'var/tmp/storage/images/exp_iaw_' . getmypid() . '_' . mt_rand();
        eZDir::mkdir( $this->root . '/' . $base, false, true );
        file_put_contents( $this->root . '/' . $base . '/there.jpg', 'x' );

        $server = $_SERVER;
        $cwd = getcwd();
        $env = array();
        foreach ( array( 'EXP_VELOCITY_ROOT', 'EXP_VELOCITY_STATIC_PATHS', 'EXP_VELOCITY_NEVER_STATIC', 'EXP_VELOCITY_FOLLOW_SYMLINKS' ) as $name )
            $env[$name] = getenv( $name );
        putenv( 'EXP_VELOCITY_ROOT=' . $this->root );
        putenv( 'EXP_VELOCITY_STATIC_PATHS=' . expVelocity::STATIC_PATHS );
        putenv( 'EXP_VELOCITY_NEVER_STATIC=' . expVelocity::NEVER_STATIC );
        putenv( 'EXP_VELOCITY_FOLLOW_SYMLINKS=0' );
        $route = function ( $uri ) {
            $_SERVER['REQUEST_URI'] = $uri;
            return \Exponential\Command\Kernel\VelocityRouter::route( $this->root );
        };
        try
        {
            $this->assertSame( $this->root . '/index_image.php', $route( '/' . $base . '/missing_small.jpg' ) );
            $this->assertSame( '/index_image.php', $_SERVER['SCRIPT_NAME'] );
            $this->assertSame( $this->root . '/index_image.php', $route( '/var/site/storage/images-versioned/1/2-eng-GB/a_small.jpg' ) );
            $this->assertSame( $this->root . '/index_image.php', $route( '/content/image/tok.sig/a_small.jpg' ) );
            $this->assertSame( $this->root . '/index_image.php', $route( '/site/content/image/tok.sig/a_small.jpg' ) );
            // A file that exists: the server sends it
            $this->assertFalse( $route( '/' . $base . '/there.jpg' ) );
            // A missing file outside the image storage: 404 as before
            $this->assertTrue( $route( '/design/standard/stylesheets/missing.css' ) );
            // Dot segments never reach the image script (NEVER_STATIC sends them to index.php, as before)
            $this->assertSame( $this->root . '/index.php', $route( '/var/site/storage/images/../../../settings/site.ini' ) );
            $this->assertFalse( \Exponential\Command\Kernel\VelocityRouter::isStorageImagePath( '/var/site/storage/images/a/../../x.jpg' ) );
            $this->assertTrue( \Exponential\Command\Kernel\VelocityRouter::isStorageImagePath( '/var/storage/images/a/1-1-eng-GB/a_small.jpg' ) );
            $this->assertSame( $this->root . '/index.php', $route( '/content/view/full/2' ) );
        }
        finally
        {
            $_SERVER = $server;
            chdir( $cwd );
            foreach ( $env as $name => $value )
                putenv( $value === false ? $name : "$name=$value" );
            unlink( $this->root . '/' . $base . '/there.jpg' );
            rmdir( $this->root . '/' . $base );
        }
    }

    /**
     * IAW-05
     */
    public function testTheClusterGatewayHandsMissingImagesToTheEntryScript()
    {
        $this->assertTrue( ezpClusterGateway::handsOverMissingImage( 'var/site/storage/images/a/1-1-eng-GB/a_small.jpg' ) );
        $this->assertTrue( ezpClusterGateway::handsOverMissingImage( 'var/storage/images-versioned/12/3-eng-GB/a_small.jpg' ) );
        $this->assertFalse( ezpClusterGateway::handsOverMissingImage( 'var/site/storage/original/application/a.pdf' ) );
        $this->assertFalse( ezpClusterGateway::handsOverMissingImage( 'var/site/cache/public/a.css' ) );
        $this->assertFalse( ezpClusterGateway::handsOverMissingImage( 'var/site/storage/images/../../../settings/site.ini' ) );
        $this->assertFalse( ezpClusterGateway::handsOverMissingImage( '/var/site/storage/images/a/1-1-eng-GB/a_small.jpg' ) );
        $this->assertFalse( ezpClusterGateway::handsOverMissingImage( 'var/site/storage/images/a/1-1-eng-GB/a_small.jpg', $this->root . '/var/tmp/no-such-index_image.php' ) );

        $gateway = new expImageAliasWebServersTestGateway();
        $gateway->retrieve( 'var/site/storage/images/a/1-1-eng-GB/a_small.jpg' );
        $this->assertSame( array( 'connect', 'metadata', 'close' ), $gateway->calls );
        $this->assertSame( realpath( $this->root . '/index_image.php' ), realpath( (string)$gateway->ranScript ) );

        // CLUSTER_IMAGE_ALIAS_ON_DEMAND=false (config.cluster.php) keeps the 404; checked in a process of its own,
        // as a constant cannot be taken back
        $this->assertStringContainsString( "defined( 'CLUSTER_IMAGE_ALIAS_ON_DEMAND' ) && !CLUSTER_IMAGE_ALIAS_ON_DEMAND",
                                           (string)file_get_contents( $this->root . '/kernel/clustering/gateway.php' ) );
    }

    /**
     * IAW-06
     */
    public function testTheShippedApacheRulesAreInTheOrderThatWorks()
    {
        $block = null;
        foreach ( array( '.htaccess_root', '.htaccess_root_static' ) as $name )
        {
            $text = (string)file_get_contents( $this->root . '/' . $name );
            $lines = array_values( array_filter( array_map( 'trim', explode( "\n", $text ) ), function ( $line ) {
                return $line !== '' && ( $line[0] !== '#' || strpos( $line, '#RewriteRule' ) === 0 );
            } ) );
            $at = function ( $line ) use ( $lines, $name ) {
                $index = array_search( $line, $lines, true );
                $this->assertIsInt( $index, "$name: $line" );
                return $index;
            };
            $cluster = $at( '#RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* index_cluster.php [L]' );
            $clusterCache = $at( '#RewriteRule ^var/([^/]+/)?cache/(texttoimage|public)/.* index_cluster.php [L]' );
            $clusterPass = $at( '#RewriteRule ^index_cluster\.php - [L]' );
            $condition = $at( 'RewriteCond %{REQUEST_FILENAME} !-f' );
            $missing = $at( 'RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* index_image.php [L]' );
            $view = $at( 'RewriteRule ^([^/]+/)?content/image/ index_image.php [L]' );
            $pass = $at( 'RewriteRule ^index_image\.php - [L]' );
            $served = $at( 'RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* - [L]' );
            $treemenu = $at( 'RewriteRule ^([^/]+/)?content/treemenu.* index_treemenu.php [L]' );
            // The condition belongs to the storage rule only; the cluster lines stand before both
            $this->assertSame( $condition + 1, $missing, $name );
            $this->assertLessThan( $condition, $cluster, $name );
            // the cluster block: stored images, the public cache (packed scripts and stylesheets), then the gateway itself
            $this->assertSame( $cluster + 1, $clusterCache, $name );
            $this->assertSame( $cluster + 2, $clusterPass, $name );
            $this->assertLessThan( $pass, $view, $name );
            $this->assertLessThan( $served, $pass, $name );
            $this->assertLessThan( $view, $treemenu, $name );
            // Every rule that sends to index.php comes after
            foreach ( $lines as $index => $line )
            {
                if ( preg_match( '/^RewriteRule .* index\.php( |$)/', $line ) )
                    $this->assertGreaterThan( $pass, $index, "$name: $line" );
            }
            $slice = implode( "\n", array_slice( $lines, $cluster, $served - $cluster + 1 ) );
            if ( $block === null )
                $block = $slice;
            else
                $this->assertSame( $block, $slice, 'the image rules of both files are the same' );
        }
    }
}
