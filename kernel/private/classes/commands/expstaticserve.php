<?php
/**
 * @description Preview the static build of a target locally: PHP's built-in web server serves its folder below the target's base path, as GitHub Pages serves a project site
 * @alias exp:static:serve
 *
 * File containing the exp:static:serve command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expstaticserve extends ExpstaticBase
{
    protected function name()
    {
        return 'exp:static:serve';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Preview a static build\n" .
                             "Serves the target's folder with PHP's built-in web server, below the target's base path (/my-site/)\n" .
                             "the way GitHub Pages serves a project site: a folder answers with its index.html, a missing page\n" .
                             "with 404.html. Listens on this computer only unless --host says otherwise; stop it with Ctrl+C.\n" .
                             "\n" .
                             "./console exp:static:serve github-public                    http://127.0.0.1:8090/my-site/\n" .
                             "./console exp:static:serve github-public --base-path=/      at the root, as with a custom domain\n" .
                             "./console exp:static:serve github-public --port=9000",
        ) + self::SCRIPT );
        return (array)$this->startup( '[port:][host:][base-path:][format:]', '[target]', array(
            'port' => 'The port of the preview server (default 8090)',
            'host' => 'The address the preview server listens on (default 127.0.0.1, this computer only)',
            'base-path' => 'Serve the folder below this path (default the target\'s BasePath; / for the root)',
            'format' => 'text (default) or json (the machine output exp-static/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $port = isset( $options['port'] ) && $options['port'] !== false ? (string)$options['port'] : '8090';
        if ( !ctype_digit( $port ) || (int)$port < 1 || (int)$port > 65535 )
            $this->usage( "--port must be a port number, not $port" );
        $host = isset( $options['host'] ) && $options['host'] !== false ? trim( (string)$options['host'] ) : '127.0.0.1';
        if ( !preg_match( '#^[A-Za-z0-9.:\[\]-]+$#', $host ) )
            $this->usage( "--host must be an address or host name, not $host" );
        $basePath = isset( $options['base-path'] ) && $options['base-path'] !== false ? (string)$options['base-path'] : null;
        if ( $basePath !== null && ( $basePath === '' || $basePath[0] !== '/' ) )
            $this->usage( "--base-path must start with /, not $basePath" );
        $target = $this->target( $options, $format );
        $mount = \expStaticLinkRewriter::normaliseBasePath( $basePath !== null ? $basePath : $target->basePath() );
        $ini = \eZINI::instance( 'staticcache.ini' );
        $workDir = $ini->hasVariable( 'StaticPublishSettings', 'WorkDir' ) ? (string)$ini->variable( 'StaticPublishSettings', 'WorkDir' ) : 'static-publish';
        $directory = $target->outputDirectory( \eZSys::varDirectory(), $workDir );
        $root = realpath( $directory );
        if ( $root === false || !is_file( $root . '/index.html' ) )
            $this->finish( array( 'mode' => 'serve', 'target' => $target->id(), 'warnings' => array(),
                                  'errors' => array( "$directory holds no build: run exp:static:build {$target->id()} first" ) ), $format );

        $url = 'http://' . ( strpos( $host, ':' ) !== false && $host[0] !== '[' ? "[$host]" : $host ) . ':' . $port . $mount;
        $command = \expStaticServe::command( PHP_BINARY, $host, (int)$port, (string)$root );
        $start = array( 'schema' => \expStaticBuild::REPORT, 'command' => $this->name(), 'mode' => 'serve', 'target' => $target->id(),
                        'directory' => $root, 'mount' => $mount, 'url' => $url, 'argv' => $command );
        if ( $format === 'json' )
            $this->cli()->output( json_encode( $start, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        else
        {
            $this->output( "Serving $root" );
            $this->output( "at $url  (Ctrl+C stops it)" );
        }
        $env = (array)getenv();
        $env['EXP_STATIC_ROOT'] = (string)$root;
        $env['EXP_STATIC_MOUNT'] = $mount;
        $pipes = array();
        $process = proc_open( $command, array( 0 => STDIN, 1 => STDOUT, 2 => STDERR ), $pipes, null, $env );
        if ( !is_resource( $process ) )
        {
            $this->error( 'FAIL: the preview server did not start' );
            $this->shutdown( \expUpgrade::EXIT_FAILED );
            return;
        }
        $code = proc_close( $process );
        $this->shutdown( $code === 0 ? \expUpgrade::EXIT_OK : \expUpgrade::EXIT_FAILED );
    }
}

}
