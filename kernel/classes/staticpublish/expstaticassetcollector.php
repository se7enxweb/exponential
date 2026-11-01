<?php
/**
 * File containing the expStaticAssetCollector class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Puts the files a static copy needs next to its pages: every file the pages and style sheets refer to (style sheets
 * and scripts of the designs and the packer in var/<site>/cache/public, images and their aliases, fonts, downloads)
 * and, with Assets=referenced-and-storage, every image of var/<site>/storage that the anonymous user may see.
 *
 * A file is taken from the installation when it lies below a folder the web server serves as files (the folders of
 * LOCAL_ROOTS; var/ through eZClusterFileHandler, so a cluster's files are found too). Anything else is requested
 * from the site as the anonymous user, like a visitor: a download (content/download/...), an image alias not made
 * yet (the request makes it), a packer file not written yet. A file the site does not hand out (401, 403, 404, an
 * HTML page where a file was asked for) is not copied; the build sends its links to the live site and reports it.
 *
 * Style sheets are rewritten (expStaticLinkRewriter::rewriteCss()) after the files they name are collected, so
 * their fonts and background images are copied too (up to MAX_DEPTH style sheets deep).
 */
class expStaticAssetCollector
{
    /** The folders the web server serves as files, relative to the installation */
    const LOCAL_ROOTS = array(
        '#^design/[^/]+/(stylesheets|images|javascript|javascripts|fonts|lib|icons)/#',
        '#^extension/[^/]+/design/[^/]+/(stylesheets|images|javascript|javascripts|fonts|lib|flash|icons)/#',
        '#^share/icons/#',
        '#^var/([^/]+/)?storage/images/#',
        '#^var/([^/]+/)?cache/(public|texttoimage)/#',
        '#^(favicon\.ico|apple-touch-icon[^/]*)$#',
    );

    /** Never copied, from anywhere: code, settings, templates */
    const FORBIDDEN = '#\.(php[0-9]?|phtml|phar|ini|inc|tpl|sql|sh|env|lock|htaccess|htpasswd|bak|orig|swp)(\.|$)|(^|/)\.(git|svn|env)(/|$)#i';

    /** How many style sheets deep an @import or url() of a style sheet is followed */
    const MAX_DEPTH = 5;

    private $root;
    private $output;
    private $fetch;
    private $origin;
    private $rewriter;
    private $emit;
    private $maxFileBytes;

    /** output path => result: ok, kind, how (local, http), source, bytes, sha256, error */
    private $results = array();

    /** output path => array( source, content ) of the style sheets, rewritten at the end */
    private $styleSheets = array();

    /**
     * @param array $options root (the installation, default the current directory), output (the build folder),
     *        fetch (callable( $url ) => array( status, type, body ): a request as the anonymous user), origin
     *        (scheme://host[:port] the site was fetched from), rewriter (the build's expStaticLinkRewriter), emit
     *        (callable( $type, $message )), max_file_bytes (larger files are reported)
     */
    public function __construct( array $options )
    {
        $this->root = rtrim( str_replace( '\\', '/', isset( $options['root'] ) ? (string)$options['root'] : (string)getcwd() ), '/' );
        $this->output = rtrim( str_replace( '\\', '/', (string)$options['output'] ), '/' );
        $this->fetch = isset( $options['fetch'] ) ? $options['fetch'] : null;
        $this->origin = isset( $options['origin'] ) ? rtrim( (string)$options['origin'], '/' ) : '';
        $this->rewriter = isset( $options['rewriter'] ) ? $options['rewriter'] : null;
        $this->emit = isset( $options['emit'] ) ? $options['emit'] : null;
        $this->maxFileBytes = isset( $options['max_file_bytes'] ) ? (int)$options['max_file_bytes'] : 100000000;
    }

    private function say( $type, $message )
    {
        if ( $this->emit !== null )
            call_user_func( $this->emit, $type, $message );
    }

    /**
     * Whether a path of the installation lies below a folder the web server serves as files, and is no code or
     * setting.
     *
     * @param string $path relative to the installation, / separated
     * @return bool
     */
    public static function servedAsFile( $path )
    {
        $path = (string)$path;
        if ( expStaticManifest::normalisePath( $path ) === null || preg_match( self::FORBIDDEN, $path ) )
            return false;
        foreach ( self::LOCAL_ROOTS as $pattern )
            if ( preg_match( $pattern, $path ) )
                return true;
        return false;
    }

    /** @return bool whether a file is in the export (collected without an error) */
    public function has( $output )
    {
        return isset( $this->results[$output] ) && $this->results[$output]['ok'];
    }

    /** @return array output path => result */
    public function results()
    {
        return $this->results;
    }

    /** @return array output path => result, of the files that could not be collected */
    public function failures()
    {
        return array_filter( $this->results, function ( $r ) { return !$r['ok']; } );
    }

    /**
     * Collects files.
     *
     * @param array $files output path => source path (the path on the host, /var/site/storage/...)
     * @param string $kind the manifest kind of the files (file, storage)
     */
    public function collect( array $files, $kind = 'file' )
    {
        $queue = array();
        foreach ( $files as $output => $source )
            $queue[] = array( (string)$output, (string)$source, 0 );
        while ( $queue )
        {
            list( $output, $source, $depth ) = array_shift( $queue );
            if ( isset( $this->results[$output] ) )
                continue;
            $result = $this->collectOne( $output, $source, $kind );
            $this->results[$output] = $result;
            if ( !$result['ok'] || strtolower( (string)pathinfo( $output, PATHINFO_EXTENSION ) ) !== 'css' || $this->rewriter === null )
                continue;
            // a style sheet: the files it names, found by rewriting it once (the text is rewritten again at the end)
            $css = isset( $this->styleSheets[$output] ) ? $this->styleSheets[$output][1] : '';
            $this->rewriter->reset();
            $this->rewriter->rewriteCss( $css, $source, $output );
            if ( $depth < self::MAX_DEPTH )
                foreach ( $this->rewriter->foundFiles() as $next => $nextSource )
                    if ( !isset( $this->results[$next] ) )
                        $queue[] = array( $next, $nextSource, $depth + 1 );
        }
    }

    /**
     * Rewrites the style sheets collected and writes them: after collect(), when the rewriter knows which files the
     * export holds.
     *
     * @return int how many were written
     */
    public function writeStyleSheets()
    {
        $n = 0;
        foreach ( $this->styleSheets as $output => $sheet )
        {
            $css = $this->rewriter !== null ? $this->rewriter->rewriteCss( $sheet[1], $sheet[0], $output ) : $sheet[1];
            if ( $this->write( $output, $css ) )
            {
                $this->results[$output]['bytes'] = strlen( $css );
                $this->results[$output]['sha256'] = hash( 'sha256', $css );
                $n++;
            }
        }
        $this->styleSheets = array();
        return $n;
    }

    /** One file: from the installation when it is served as a file there, else from the site. */
    private function collectOne( $output, $source, $kind )
    {
        $result = array( 'ok' => false, 'kind' => $kind, 'how' => '', 'source' => $source, 'bytes' => 0, 'sha256' => '', 'error' => '' );
        if ( !expStaticLinkRewriter::safePath( $output ) || preg_match( self::FORBIDDEN, $output ) )
            return array( 'error' => 'not a file a static copy may hold' ) + $result;

        $local = ltrim( rawurldecode( (string)parse_url( $source, PHP_URL_PATH ) ), '/' );
        if ( self::servedAsFile( $local ) )
        {
            $copied = $this->copyLocal( $local, $output );
            if ( $copied !== null )
                return array( 'ok' => true, 'how' => 'local' ) + $copied + $result;
        }
        if ( $kind === 'storage' )
            return array( 'error' => 'not found in the storage' ) + $result;
        if ( $this->fetch === null || $this->origin === '' )
            return array( 'error' => 'not a file of the installation, and nothing to request it from' ) + $result;

        $response = (array)call_user_func( $this->fetch, $this->origin . $source );
        $status = isset( $response['status'] ) ? (int)$response['status'] : 0;
        $body = isset( $response['body'] ) ? (string)$response['body'] : '';
        $type = isset( $response['type'] ) ? strtolower( (string)$response['type'] ) : '';
        if ( $status < 200 || $status >= 300 )
            return array( 'error' => $status ? "the site answered $status" : 'no response' ) + $result;
        if ( strpos( $type, 'text/html' ) === 0 && !preg_match( '#\.html?$#i', $output ) )
            return array( 'error' => 'the site answered with a page, not the file' ) + $result;
        if ( strlen( $body ) > $this->maxFileBytes )
            $this->say( 'warn', sprintf( '%s is %d bytes, more than the %d a file may have on the host', $output, strlen( $body ), $this->maxFileBytes ) );
        if ( strtolower( (string)pathinfo( $output, PATHINFO_EXTENSION ) ) === 'css' )
        {
            $this->styleSheets[$output] = array( $source, $body );
            return array( 'ok' => true, 'how' => 'http', 'bytes' => strlen( $body ), 'sha256' => hash( 'sha256', $body ) ) + $result;
        }
        if ( !$this->write( $output, $body ) )
            return array( 'error' => 'could not be written' ) + $result;
        return array( 'ok' => true, 'how' => 'http', 'bytes' => strlen( $body ), 'sha256' => hash( 'sha256', $body ) ) + $result;
    }

    /**
     * Copies a file of the installation into the build; var/ through the cluster file handler.
     *
     * @return array|null bytes, sha256; null when there is no such file
     */
    private function copyLocal( $local, $output )
    {
        $isCss = strtolower( (string)pathinfo( $output, PATHINFO_EXTENSION ) ) === 'css';
        $temporary = null;
        $handler = null;
        if ( strpos( $local, 'var/' ) === 0 && $this->clustered() )
        {
            $handler = eZClusterFileHandler::instance( $local );
            if ( !$handler->exists() )
                return null;
            $path = $handler->fetchUnique();
            if ( !is_string( $path ) || !is_file( $path ) )
                return null;
            if ( $path !== $local )
                $temporary = $path;
        }
        else
        {
            $path = $this->root . '/' . $local;
            if ( !is_file( $path ) )
                return null;
        }
        $size = (int)filesize( $path );
        if ( $size > $this->maxFileBytes )
            $this->say( 'warn', sprintf( '%s is %d bytes, more than the %d a file may have on the host', $output, $size, $this->maxFileBytes ) );
        if ( $isCss )
        {
            $css = (string)file_get_contents( $path );
            $this->styleSheets[$output] = array( '/' . $local, $css );
            $out = array( 'bytes' => strlen( $css ), 'sha256' => hash( 'sha256', $css ) );
        }
        else
        {
            $target = $this->output . '/' . $output;
            if ( !$this->makeDir( dirname( $target ) ) || !copy( $path, $target ) )
                $out = null;
            else
                $out = array( 'bytes' => $size, 'sha256' => (string)hash_file( 'sha256', $target ) );
        }
        if ( $temporary !== null && $handler !== null )
            $handler->fileDeleteLocal( $temporary );
        return $out;
    }

    /**
     * Whether var/ is read through the cluster file handler: when the installation is the current directory (its
     * paths are relative to it), as in every request and command of the installation.
     *
     * @return bool
     */
    private function clustered()
    {
        static $answer = array();
        if ( !isset( $answer[$this->root] ) )
        {
            $root = realpath( $this->root );
            $cwd = realpath( (string)getcwd() );
            $answer[$this->root] = class_exists( 'eZClusterFileHandler' ) && $root !== false && $root === $cwd;
        }
        return $answer[$this->root];
    }

    /** Writes a file into the build. */
    private function write( $output, $content )
    {
        $target = $this->output . '/' . $output;
        return $this->makeDir( dirname( $target ) ) && file_put_contents( $target, $content ) !== false;
    }

    private function makeDir( $dir )
    {
        return is_dir( $dir ) || mkdir( $dir, eZDir::dirMode( 0775 ), true ) || is_dir( $dir );
    }

    /**
     * The image files of var/<site>/storage/images that belong to the published version of an object the anonymous
     * user may read: the files of ezimagefile (the originals and every alias made), by object.
     *
     * @param eZDBInterface $db
     * @param callable $canRead callable( $objectId ) => bool, asked once per object
     * @param string $varDir the var directory (var/site): only files below its storage/images
     * @return array path => object id
     */
    public static function storageImages( $db, $canRead, $varDir )
    {
        $prefix = rtrim( str_replace( '\\', '/', (string)$varDir ), '/' ) . '/storage/images/';
        $rows = $db->arrayQuery( 'SELECT DISTINCT f.filepath AS filepath, a.contentobject_id AS object_id
                                  FROM ezimagefile f, ezcontentobject_attribute a, ezcontentobject o
                                  WHERE a.id = f.contentobject_attribute_id AND o.id = a.contentobject_id
                                    AND a.version = o.current_version AND o.status = 1' );
        $allowed = array();
        $out = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            $path = str_replace( '\\', '/', (string)$row['filepath'] );
            $object = (int)$row['object_id'];
            if ( strpos( $path, $prefix ) !== 0 || !self::servedAsFile( $path ) )
                continue;
            if ( !isset( $allowed[$object] ) )
                $allowed[$object] = (bool)call_user_func( $canRead, $object );
            if ( $allowed[$object] )
                $out[$path] = $object;
        }
        ksort( $out, SORT_STRING );
        return $out;
    }
}
