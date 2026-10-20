<?php
/**
 * The copies of the external images the help pages show (doc/eng-us/features/6.0/help-system.md, "External images").
 *
 * A page such as README.md embeds its screenshots from another site (github.com/user-attachments). The admin's
 * Content Security Policy allows images from 'self' only, so the help shows a local copy instead: exp:help index
 * fetches every https image a page references (indexReferences(), fetchAll()) and stores it as <help cache>/images/<id>.<suffix>,
 * where <id> is the SHA-256 of the address. The view exphelp/image/(id)/<id> serves it, and the renderer's
 * remote_images option (resolve()) puts that local address in the page. An image that could not be fetched keeps
 * the link fallback of expHelpMarkdownRenderer (image_sources).
 *
 * Fetching is careful: https only, on every redirect too (at most MAX_REDIRECTS); never an address in a private or
 * reserved network (the address is resolved once and the connection pinned to it); at most MAX_BYTES; a timeout;
 * and only PNG, JPEG, GIF and WebP, checked against the bytes as well as the Content-Type. SVG is never stored.
 * Nothing is fetched while a page is shown: the web only reads what the index stored.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpRemoteImages
{
    /** the largest image stored, in bytes */
    const MAX_BYTES = 5242880;

    /** seconds for one request */
    const TIMEOUT = 15;

    /** redirects followed (GitHub sends user-attachments to a signed storage address) */
    const MAX_REDIRECTS = 5;

    /** the image types stored: content type => suffix (no SVG: it can carry script) */
    const TYPES = array( 'image/png' => 'png', 'image/jpeg' => 'jpg', 'image/gif' => 'gif', 'image/webp' => 'webp' );

    /** the content types the view sends, by suffix */
    const SUFFIX_TYPES = array( 'png' => 'image/png', 'jpg' => 'image/jpeg', 'gif' => 'image/gif', 'webp' => 'image/webp' );

    /** @var string|null */
    protected $dir;

    /** @var callable|null one request: function( $url ) => array( 'status' => int, 'headers' => array( lower case name => value ), 'body' => string ) or null */
    protected $fetcher;

    /**
     * @param string|null $dir the directory of the copies; null: <help cache>/images
     * @param callable|null $fetcher one request without following redirects (tests give a fake); null: curl
     */
    public function __construct( $dir = null, $fetcher = null )
    {
        $this->dir = $dir !== null ? rtrim( (string)$dir, '/' ) : self::defaultDir();
        $this->fetcher = $fetcher;
    }

    /**
     * @return string|null <help cache>/images (the cache of the original language, shared by every language)
     */
    public static function defaultDir()
    {
        if ( class_exists( 'expHelp' ) && class_exists( 'eZINI' ) && class_exists( 'eZSys' ) )
            return rtrim( expHelp::cacheDirectory(), '/' ) . '/images';
        return null;
    }

    /**
     * @return string|null
     */
    public function dir()
    {
        return $this->dir;
    }

    /**
     * The id of an image address: its SHA-256, 64 hex characters.
     *
     * @param string $url
     * @return string
     */
    public static function id( $url )
    {
        return hash( 'sha256', (string)$url );
    }

    /**
     * @param mixed $id
     * @return bool whether it has the form of an id (nothing else is ever looked up)
     */
    public static function isId( $id )
    {
        return is_string( $id ) && preg_match( '/^[0-9a-f]{64}$/D', $id ) === 1;
    }

    /**
     * The stored copy of an id.
     *
     * @param string $id
     * @return string|null the file, null for a malformed id or one that is not stored
     */
    public function file( $id )
    {
        if ( !self::isId( $id ) || $this->dir === null )
            return null;
        foreach ( self::SUFFIX_TYPES as $suffix => $type )
        {
            $file = $this->dir . '/' . $id . '.' . $suffix;
            if ( is_file( $file ) && !is_link( $file ) )
                return $file;
        }
        return null;
    }

    /**
     * @param string $file
     * @return string|null the content type of a stored copy
     */
    public static function contentType( $file )
    {
        $suffix = strtolower( pathinfo( (string)$file, PATHINFO_EXTENSION ) );
        return isset( self::SUFFIX_TYPES[$suffix] ) ? self::SUFFIX_TYPES[$suffix] : null;
    }

    /**
     * The module-relative address of a stored image (not yet passed through ezurl).
     *
     * @param string $id
     * @return string /exphelp/image/(id)/<id>
     */
    public static function url( $id )
    {
        return '/exphelp/image/(id)/' . $id;
    }

    /**
     * For the renderer's remote_images option: the local copy of an external image, with its size, or null when
     * none is stored (the renderer then keeps its fallback).
     *
     * @param string $href the image's https address, as the renderer resolved it
     * @return array|null array( 'url', 'width', 'height' )
     */
    public function resolve( $href )
    {
        $id = self::id( $href );
        $file = $this->file( $id );
        if ( $file === null )
            return null;
        $url = self::url( $id );
        if ( class_exists( 'expHelpLinks' ) && class_exists( 'eZURI' ) )
            $url = expHelpLinks::absolute( $url );
        $size = @getimagesize( $file );
        return array( 'url' => $url,
                      'width' => is_array( $size ) && !empty( $size[0] ) ? (int)$size[0] : null,
                      'height' => is_array( $size ) && !empty( $size[1] ) ? (int)$size[1] : null );
    }

    /**
     * Part of the render cache key: changes when a copy is added, so a page rendered with a link shows the image
     * after the next index.
     *
     * @return string
     */
    public function signature()
    {
        if ( $this->dir === null || !is_dir( $this->dir ) )
            return '';
        clearstatcache( true, $this->dir );
        return (string)filemtime( $this->dir );
    }

    /**
     * The external https images a Markdown text references, as the renderer resolves them: ![alt](https://...),
     * <img src="https://..."> and reference definitions used by an image.
     *
     * @param string $markdown
     * @return string[] unique addresses
     */
    public static function references( $markdown )
    {
        $markdown = (string)$markdown;
        $found = array();
        if ( preg_match_all( '/!\[[^\]]*\]\(\s*<?([^\s)>]+)/', $markdown, $m ) )
            $found = array_merge( $found, $m[1] );
        if ( preg_match_all( '/<img\b[^>]*?\ssrc\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'=<>`]+))/i', $markdown, $m, PREG_SET_ORDER ) )
            foreach ( $m as $match )
                $found[] = isset( $match[3] ) && $match[3] !== '' ? $match[3] : ( isset( $match[2] ) && $match[2] !== '' ? $match[2] : $match[1] );
        // ![alt][label] and ![label]: the definition [label]: https://...
        if ( preg_match_all( '/!\[([^\]]*)\](?:\[([^\]]*)\])?/', $markdown, $m, PREG_SET_ORDER ) )
        {
            $labels = array();
            foreach ( $m as $match )
                $labels[strtolower( trim( isset( $match[2] ) && $match[2] !== '' ? $match[2] : $match[1] ) )] = true;
            if ( preg_match_all( '/^ {0,3}\[([^\]]+)\]:\s*<?(\S+?)>?(?:\s|$)/m', $markdown, $d, PREG_SET_ORDER ) )
                foreach ( $d as $definition )
                    if ( isset( $labels[strtolower( trim( $definition[1] ) )] ) )
                        $found[] = $definition[2];
        }
        $urls = array();
        foreach ( $found as $url )
        {
            $url = self::normalize( $url );
            if ( $url !== null )
                $urls[$url] = true;
        }
        return array_keys( $urls );
    }

    /**
     * An image address the way the renderer resolves it (expHelpMarkdownRenderer::resolveUrl()), so the stored id
     * is the one the page looks up; null when it is not an external https image.
     *
     * @param string $url
     * @return string|null
     */
    public static function normalize( $url )
    {
        $renderer = new expHelpMarkdownRenderer();
        $target = $renderer->resolveUrl( $url, true );
        if ( $target === null || empty( $target['external'] ) || stripos( $target['href'], 'https://' ) !== 0 )
            return null;
        return $target['href'];
    }

    /**
     * The external images of every page of an index (exp:help index fetches them).
     *
     * @param expHelpDocumentIndex $index
     * @return array url => true
     */
    public static function indexReferences( $index )
    {
        $urls = array();
        foreach ( array_keys( $index->documents() ) as $id )
        {
            $source = $index->source( $id );
            if ( is_string( $source ) && stripos( $source, 'https:' ) !== false )
                foreach ( self::references( $source ) as $url )
                    $urls[$url] = true;
        }
        return $urls;
    }

    /**
     * @param string[] $urls
     * @return array( 'images' => int, 'stored' => int, 'cached' => int, 'failed' => array( url => reason ) )
     */
    public function fetchAll( array $urls )
    {
        $stats = array( 'images' => count( $urls ), 'stored' => 0, 'cached' => 0, 'failed' => array() );
        foreach ( $urls as $url )
        {
            $result = $this->fetch( $url );
            if ( $result['status'] === 'failed' )
                $stats['failed'][$url] = $result['reason'];
            else
                $stats[$result['status']]++;
        }
        return $stats;
    }

    /**
     * Fetch one image and store it, unless it is stored already.
     *
     * @param string $url
     * @return array( 'status' => stored|cached|failed, 'id' => string, 'reason' => string )
     */
    public function fetch( $url )
    {
        $url = (string)$url;
        $id = self::id( $url );
        $failed = function ( $reason ) use ( $id ) {
            return array( 'status' => 'failed', 'id' => $id, 'reason' => $reason );
        };
        if ( $this->dir === null )
            return $failed( 'no cache directory' );
        if ( $this->file( $id ) !== null )
            return array( 'status' => 'cached', 'id' => $id, 'reason' => '' );
        $current = $url;
        $response = null;
        for ( $hop = 0; $hop <= self::MAX_REDIRECTS; $hop++ )
        {
            if ( !self::isFetchable( $current ) )
                return $failed( $hop === 0 ? 'not an https address' : 'redirected to an address that is not https' );
            $response = $this->request( $current );
            if ( !is_array( $response ) || !isset( $response['status'] ) )
                return $failed( isset( $response['error'] ) ? (string)$response['error'] : 'no response' );
            $status = (int)$response['status'];
            $headers = isset( $response['headers'] ) && is_array( $response['headers'] ) ? array_change_key_case( $response['headers'], CASE_LOWER ) : array();
            if ( $status >= 300 && $status < 400 && isset( $headers['location'] ) )
            {
                $current = self::absoluteLocation( $current, (string)$headers['location'] );
                if ( $current === null )
                    return $failed( 'bad redirect' );
                continue;
            }
            if ( $status !== 200 )
                return $failed( 'HTTP ' . $status );
            $body = isset( $response['body'] ) ? (string)$response['body'] : '';
            if ( strlen( $body ) > self::MAX_BYTES )
                return $failed( 'larger than ' . self::MAX_BYTES . ' bytes' );
            $type = strtolower( trim( explode( ';', isset( $headers['content-type'] ) ? (string)$headers['content-type'] : '' )[0] ) );
            if ( !isset( self::TYPES[$type] ) )
                return $failed( 'content type "' . $type . '" is not an allowed image' );
            if ( self::sniff( $body ) !== $type )
                return $failed( 'the bytes are not a ' . $type . ' image' );
            return $this->store( $id, self::TYPES[$type], $body ) ? array( 'status' => 'stored', 'id' => $id, 'reason' => '' )
                                                                 : $failed( 'could not write the copy' );
        }
        return $failed( 'too many redirects' );
    }

    /**
     * @param string $url
     * @return bool https, a host name or address, no user name or password
     */
    public static function isFetchable( $url )
    {
        $parts = parse_url( (string)$url );
        return is_array( $parts ) && isset( $parts['scheme'], $parts['host'] ) && strtolower( $parts['scheme'] ) === 'https'
            && !isset( $parts['user'] ) && !isset( $parts['pass'] ) && preg_match( '/^[A-Za-z0-9.-]+$/', $parts['host'] ) === 1
            && ( !isset( $parts['port'] ) || (int)$parts['port'] === 443 );
    }

    /**
     * A redirect's Location, made absolute against the address that sent it.
     *
     * @param string $base
     * @param string $location
     * @return string|null
     */
    public static function absoluteLocation( $base, $location )
    {
        $location = trim( $location );
        if ( $location === '' || preg_match( '/[\x00-\x20\x7F]/', $location ) )
            return null;
        if ( preg_match( '/^[A-Za-z][A-Za-z0-9+.-]*:/', $location ) )
            return $location;
        $parts = parse_url( $base );
        if ( !is_array( $parts ) || !isset( $parts['host'] ) )
            return null;
        if ( strncmp( $location, '//', 2 ) === 0 )
            return 'https:' . $location;
        $origin = 'https://' . $parts['host'];
        if ( $location[0] === '/' )
            return $origin . $location;
        $path = isset( $parts['path'] ) ? preg_replace( '#/[^/]*$#', '/', $parts['path'] ) : '/';
        return $origin . $path . $location;
    }

    /**
     * The image type of some bytes, by their signature.
     *
     * @param string $body
     * @return string|null image/png, image/jpeg, image/gif, image/webp
     */
    public static function sniff( $body )
    {
        if ( strncmp( $body, "\x89PNG\r\n\x1A\n", 8 ) === 0 )
            return 'image/png';
        if ( strncmp( $body, "\xFF\xD8\xFF", 3 ) === 0 )
            return 'image/jpeg';
        if ( strncmp( $body, 'GIF87a', 6 ) === 0 || strncmp( $body, 'GIF89a', 6 ) === 0 )
            return 'image/gif';
        if ( strncmp( $body, 'RIFF', 4 ) === 0 && substr( $body, 8, 4 ) === 'WEBP' )
            return 'image/webp';
        return null;
    }

    /**
     * Writes a copy atomically, with the storage permissions of site.ini, so Velocity (root) and PHP-FPM (the site
     * user) can both read and replace it.
     *
     * @param string $id
     * @param string $suffix
     * @param string $body
     * @return bool
     */
    protected function store( $id, $suffix, $body )
    {
        if ( !is_dir( $this->dir ) )
        {
            if ( class_exists( 'eZDir' ) && class_exists( 'eZINI' ) )
                eZDir::mkdir( $this->dir, false, true );
            else
                @mkdir( $this->dir, 0777, true );
        }
        if ( !is_dir( $this->dir ) || !is_writable( $this->dir ) )
            return false;
        $temporary = $this->dir . '/.' . $id . '.' . getmypid() . '.' . mt_rand() . '.tmp';
        if ( @file_put_contents( $temporary, $body, LOCK_EX ) === false )
            return false;
        @chmod( $temporary, self::filePermissions() );
        if ( !@rename( $temporary, $this->dir . '/' . $id . '.' . $suffix ) )
        {
            @unlink( $temporary );
            return false;
        }
        return true;
    }

    /**
     * @return int site.ini [FileSettings] StorageFilePermissions (0666 by default)
     */
    protected static function filePermissions()
    {
        if ( class_exists( 'eZINI' ) && class_exists( 'eZFile' ) )
        {
            $value = eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' );
            if ( preg_match( '/^0?[0-7]{3}$/', (string)$value ) )
                return eZFile::fileMode( octdec( $value ) );
        }
        return 0666;
    }

    /**
     * One request, through the fetcher given to the constructor or curl.
     *
     * @param string $url
     * @return array|null
     */
    protected function request( $url )
    {
        if ( is_callable( $this->fetcher ) )
            return call_user_func( $this->fetcher, $url );
        return self::curlRequest( $url );
    }

    /**
     * One https request with curl: no redirect followed (fetch() checks each), the host's address resolved once,
     * refused when it is private or reserved and pinned for the connection, the body cut off past MAX_BYTES.
     *
     * @param string $url
     * @return array|null array( status, headers, body ) or array( error )
     */
    public static function curlRequest( $url )
    {
        if ( !function_exists( 'curl_init' ) )
            return array( 'error' => 'curl is not available' );
        $host = (string)parse_url( $url, PHP_URL_HOST );
        $address = self::publicAddress( $host );
        if ( $address === null )
            return array( 'error' => 'the host "' . $host . '" has no public address' );
        $headers = array();
        $body = '';
        $tooLarge = false;
        $ch = curl_init( $url );
        $options = array(
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'Exponential help indexer',
            CURLOPT_RESOLVE => array( $host . ':443:' . ( strpos( $address, ':' ) !== false ? '[' . $address . ']' : $address ) ),
            CURLOPT_HTTPHEADER => array( 'Accept: image/png, image/jpeg, image/gif, image/webp' ),
            CURLOPT_HEADERFUNCTION => function ( $ch, $line ) use ( &$headers ) {
                $pair = explode( ':', $line, 2 );
                if ( count( $pair ) === 2 )
                    $headers[strtolower( trim( $pair[0] ) )] = trim( $pair[1] );
                return strlen( $line );
            },
            CURLOPT_WRITEFUNCTION => function ( $ch, $chunk ) use ( &$body, &$tooLarge ) {
                if ( strlen( $body ) + strlen( $chunk ) > self::MAX_BYTES )
                {
                    $tooLarge = true;
                    return 0;
                }
                $body .= $chunk;
                return strlen( $chunk );
            },
        );
        if ( defined( 'CURLOPT_PROTOCOLS_STR' ) )
            $options[CURLOPT_PROTOCOLS_STR] = 'https';
        elseif ( defined( 'CURLOPT_PROTOCOLS' ) )
            $options[CURLOPT_PROTOCOLS] = CURLPROTO_HTTPS;
        curl_setopt_array( $ch, $options );
        $ok = curl_exec( $ch );
        $status = (int)curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
        $error = curl_error( $ch );
        if ( PHP_VERSION_ID < 80000 )
            curl_close( $ch );
        if ( $tooLarge )
            return array( 'error' => 'larger than ' . self::MAX_BYTES . ' bytes' );
        if ( $ok === false )
            return array( 'error' => $error !== '' ? $error : 'request failed' );
        return array( 'status' => $status, 'headers' => $headers, 'body' => $body );
    }

    /**
     * @param string $host
     * @return string|null its first address, when every address it has is public
     */
    public static function publicAddress( $host )
    {
        if ( filter_var( $host, FILTER_VALIDATE_IP ) )
            $addresses = array( $host );
        else
        {
            $addresses = array();
            $records = @dns_get_record( $host, DNS_A | DNS_AAAA );
            foreach ( is_array( $records ) ? $records : array() as $record )
            {
                if ( isset( $record['ip'] ) )
                    $addresses[] = $record['ip'];
                elseif ( isset( $record['ipv6'] ) )
                    $addresses[] = $record['ipv6'];
            }
            if ( !$addresses )
                $addresses = array_filter( (array)@gethostbynamel( $host ) );
        }
        if ( !$addresses )
            return null;
        foreach ( $addresses as $address )
            if ( !self::isPublicAddress( $address ) )
                return null;
        // an IPv4 address first: not every server has an IPv6 route
        usort( $addresses, function ( $a, $b ) { return (int)( strpos( $a, ':' ) !== false ) - (int)( strpos( $b, ':' ) !== false ); } );
        return $addresses[0];
    }

    /**
     * @param string $address
     * @return bool not private, loopback, link-local or otherwise reserved
     */
    public static function isPublicAddress( $address )
    {
        return filter_var( $address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false;
    }
}

?>
