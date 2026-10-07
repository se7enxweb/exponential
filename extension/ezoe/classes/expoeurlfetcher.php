<?php
/**
 * Fetches a file from a URL for the upload tab of the online editor ("From a URL"). The fetch is a
 * server-side request made for a user, so it is deliberately narrow:
 *
 *  - http and https only, no credentials in the URL;
 *  - the host is resolved once, every address must be a public one, and the connection is made to the
 *    checked address (CURLOPT_RESOLVE), so a changed DNS answer cannot redirect the request;
 *  - redirects are followed by hand, at most 3, each target checked like the first URL;
 *  - connect timeout 5 s, total timeout from the setting (default 300 s), the size limit is enforced while the body
 *    arrives and the body is streamed to the temporary file, never held in memory;
 *  - the file name is sanitized, its extension must pass the upload rules of the editor and the content
 *    (finfo) must agree with the extension; executables are refused.
 *
 * Settings: ezoe.ini [EditorSettings] UploadFromUrl (enabled|disabled), UploadFromUrlMaxSize (default 145M, not capped by upload_max_filesize:
 * a fetch is not a browser upload), UploadFromUrlTimeout (seconds, default 300).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package ezoe
 */

class expOEUrlFetcher
{
    const MAX_REDIRECTS = 3;
    const CONNECT_TIMEOUT = 5;
    const DEFAULT_TIMEOUT = 300;
    const DEFAULT_MAX_SIZE = '145M';

    /** @var callable|null test hook: function ( $host ) returning the list of IP addresses of a host name */
    public static $resolver = null;

    /** blocked address ranges: loopback, private, link-local, multicast, reserved, documentation, carrier-grade NAT, ... */
    protected static $blockedRanges = array(
        '0.0.0.0/8', '10.0.0.0/8', '100.64.0.0/10', '127.0.0.0/8', '169.254.0.0/16', '172.16.0.0/12',
        '192.0.0.0/24', '192.0.2.0/24', '192.88.99.0/24', '192.168.0.0/16', '198.18.0.0/15', '198.51.100.0/24',
        '203.0.113.0/24', '224.0.0.0/4', '240.0.0.0/4',
        '::/128', '::1/128', '::/96', '64:ff9b::/96', '100::/64', '2001::/32', '2001:db8::/32', '2002::/16',
        'fc00::/7', 'fe80::/10', 'fec0::/10', 'ff00::/8',
    );

    /** extension => allowed finfo types (patterns, * at the end allowed) */
    protected static $extensionTypes = array(
        'jpg' => array( 'image/jpeg' ), 'jpeg' => array( 'image/jpeg' ), 'png' => array( 'image/png' ),
        'gif' => array( 'image/gif' ), 'webp' => array( 'image/webp' ), 'bmp' => array( 'image/bmp', 'image/x-ms-bmp' ),
        'svg' => array( 'image/svg+xml', 'text/xml', 'text/plain', 'application/xml' ),
        'tif' => array( 'image/tiff' ), 'tiff' => array( 'image/tiff' ),
        'pdf' => array( 'application/pdf' ),
        'doc' => array( 'application/msword', 'application/vnd.ms-office', 'application/x-ole-storage', 'application/CDFV2*', 'application/cdfv2*' ),
        'xls' => array( 'application/vnd.ms-excel', 'application/vnd.ms-office', 'application/x-ole-storage', 'application/CDFV2*', 'application/cdfv2*' ),
        'ppt' => array( 'application/vnd.ms-powerpoint', 'application/vnd.ms-office', 'application/x-ole-storage', 'application/CDFV2*', 'application/cdfv2*' ),
        'docx' => array( 'application/vnd.openxmlformats-officedocument.*', 'application/zip', 'application/octet-stream' ),
        'xlsx' => array( 'application/vnd.openxmlformats-officedocument.*', 'application/zip', 'application/octet-stream' ),
        'pptx' => array( 'application/vnd.openxmlformats-officedocument.*', 'application/zip', 'application/octet-stream' ),
        'odt' => array( 'application/vnd.oasis.opendocument.*', 'application/zip' ),
        'ods' => array( 'application/vnd.oasis.opendocument.*', 'application/zip' ),
        'odp' => array( 'application/vnd.oasis.opendocument.*', 'application/zip' ),
        'rtf' => array( 'text/rtf', 'application/rtf', 'text/plain' ),
        'txt' => array( 'text/plain', 'inode/x-empty' ), 'csv' => array( 'text/plain', 'text/csv', 'application/csv', 'inode/x-empty' ),
        'zip' => array( 'application/zip', 'application/x-zip*' ),
        'mp3' => array( 'audio/mpeg', 'audio/mp3', 'application/octet-stream' ),
        'wav' => array( 'audio/*', 'application/octet-stream' ),
        'ogg' => array( 'audio/ogg', 'video/ogg', 'application/ogg', 'audio/*' ),
        'm4a' => array( 'audio/*', 'video/mp4', 'application/octet-stream' ),
        'mp4' => array( 'video/mp4', 'video/*', 'audio/mp4', 'application/octet-stream' ),
        'webm' => array( 'video/webm', 'audio/webm', 'video/*' ),
    );

    /** finfo types that are never accepted, whatever the extension says */
    protected static $executableTypes = array(
        'application/x-dosexec', 'application/x-executable', 'application/x-sharedlib', 'application/x-pie-executable',
        'application/x-mach-binary', 'application/x-msdownload', 'application/x-sh', 'application/x-shellscript',
        'application/x-httpd-php', 'application/x-php', 'text/x-php', 'text/x-shellscript', 'text/x-script.python',
        'application/java-archive', 'application/x-java-applet', 'application/x-msi', 'application/vnd.microsoft.portable-executable',
        'text/html', 'application/xhtml+xml', 'application/javascript', 'text/javascript',
    );

    protected static $blockedExtensions = array( 'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'pht', 'phps', 'pl', 'cgi',
        'asp', 'aspx', 'jsp', 'sh', 'exe', 'htaccess', 'bat', 'cmd', 'com', 'msi', 'js', 'html', 'htm', 'xhtml', 'py', 'dll', 'so' );

    protected static function tr( $text, $params = array() )
    {
        return ezpI18n::tr( 'design/standard/ezoe', $text, null, $params );
    }

    public static function enabled()
    {
        $ini = eZINI::instance( 'ezoe.ini' );
        return !$ini->hasVariable( 'EditorSettings', 'UploadFromUrl' ) || $ini->variable( 'EditorSettings', 'UploadFromUrl' ) !== 'disabled';
    }

    /** @return int bytes, shorthand like 20M accepted; 0 for invalid input */
    public static function parseSize( $value )
    {
        if ( !preg_match( '/^(\d+)\s*([kmg]?)b?$/i', trim( (string) $value ), $m ) )
            return 0;
        $factor = array( '' => 1, 'k' => 1024, 'm' => 1048576, 'g' => 1073741824 );
        return (int) $m[1] * $factor[strtolower( $m[2] )];
    }

    /** @return int the size limit in bytes: UploadFromUrlMaxSize only, PHP's upload_max_filesize does not apply to a fetch */
    public static function maxSize()
    {
        $ini = eZINI::instance( 'ezoe.ini' );
        $setting = self::parseSize( $ini->hasVariable( 'EditorSettings', 'UploadFromUrlMaxSize' ) ? $ini->variable( 'EditorSettings', 'UploadFromUrlMaxSize' ) : self::DEFAULT_MAX_SIZE );
        if ( $setting <= 0 )
            $setting = self::parseSize( self::DEFAULT_MAX_SIZE );
        return $setting;
    }

    /** @return int the total timeout in seconds (ezoe.ini UploadFromUrlTimeout, default 300) */
    public static function timeout()
    {
        $ini = eZINI::instance( 'ezoe.ini' );
        $value = $ini->hasVariable( 'EditorSettings', 'UploadFromUrlTimeout' ) ? (int) $ini->variable( 'EditorSettings', 'UploadFromUrlTimeout' ) : 0;
        return $value > 0 ? $value : self::DEFAULT_TIMEOUT;
    }

    /** @return bool whether an IP address is a public one (not loopback, private, link-local, multicast, reserved) */
    public static function isPublicIp( $ip )
    {
        $packed = @inet_pton( (string) $ip );
        if ( $packed === false )
            return false;
        // an IPv4 address in IPv6 clothes (::ffff:a.b.c.d) is judged as the IPv4 address
        if ( strlen( $packed ) === 16 && substr( $packed, 0, 12 ) === "\0\0\0\0\0\0\0\0\0\0\xff\xff" )
            return self::isPublicIp( inet_ntop( substr( $packed, 12 ) ) );
        foreach ( self::$blockedRanges as $range )
        {
            list( $net, $bits ) = explode( '/', $range );
            $netPacked = inet_pton( $net );
            if ( strlen( $netPacked ) !== strlen( $packed ) )
                continue;
            $bits = (int) $bits;
            $bytes = intdiv( $bits, 8 );
            if ( substr( $packed, 0, $bytes ) !== substr( $netPacked, 0, $bytes ) )
                continue;
            $rest = $bits % 8;
            if ( $rest === 0 )
                return false;
            $mask = ( 0xff << ( 8 - $rest ) ) & 0xff;
            if ( ( ord( $packed[$bytes] ) & $mask ) === ( ord( $netPacked[$bytes] ) & $mask ) )
                return false;
        }
        return true;
    }

    /** @return array the addresses of a host name, an IP literal is returned as it is */
    protected static function resolveHost( $host )
    {
        if ( is_callable( self::$resolver ) )
            return (array) call_user_func( self::$resolver, $host );
        if ( @inet_pton( $host ) !== false )
            return array( $host );
        $ips = array();
        $v4 = @gethostbynamel( $host );
        if ( is_array( $v4 ) )
            $ips = $v4;
        $v6 = @dns_get_record( $host, DNS_AAAA );
        if ( is_array( $v6 ) )
        {
            foreach ( $v6 as $record )
            {
                if ( !empty( $record['ipv6'] ) )
                    $ips[] = $record['ipv6'];
            }
        }
        return array_values( array_unique( $ips ) );
    }

    /**
     * Checks a URL and resolves its host once.
     *
     * @param string $url
     * @return array scheme, host, port, ip (the checked address to connect to), ips (all addresses), url
     * @throws expOEUrlException with a message for the user
     */
    public static function validateUrl( $url )
    {
        $url = trim( (string) $url );
        if ( $url === '' || strlen( $url ) > 2048 || preg_match( '/[\x00-\x20\x7f]/', $url ) )
            throw new expOEUrlException( self::tr( 'The address is not a valid http or https URL.' ) );
        $parts = parse_url( $url );
        if ( !is_array( $parts ) || empty( $parts['scheme'] ) || empty( $parts['host'] ) )
            throw new expOEUrlException( self::tr( 'The address is not a valid http or https URL.' ) );
        $scheme = strtolower( $parts['scheme'] );
        if ( $scheme !== 'http' && $scheme !== 'https' )
            throw new expOEUrlException( self::tr( 'Only http and https addresses can be fetched.' ) );
        if ( isset( $parts['user'] ) || isset( $parts['pass'] ) )
            throw new expOEUrlException( self::tr( 'Addresses with a user name or password are not accepted.' ) );
        $host = strtolower( trim( $parts['host'], '[]' ) );
        $port = isset( $parts['port'] ) ? (int) $parts['port'] : ( $scheme === 'https' ? 443 : 80 );
        if ( $port < 1 || $port > 65535 )
            throw new expOEUrlException( self::tr( 'The address is not a valid http or https URL.' ) );
        $notReachable = self::tr( 'The address points to a host that is not reachable from the server for this purpose.' );
        // numeric spellings of IPv4 (2130706433, 0x7f.1, 017700000001) that are not plain dotted quads: refused, never guessed
        if ( preg_match( '/^(0x[0-9a-f]+|\d+)(\.(0x[0-9a-f]+|\d+)){0,3}$/i', $host ) && !filter_var( $host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) )
            throw new expOEUrlException( $notReachable );
        $ips = self::resolveHost( $host );
        if ( !$ips )
            throw new expOEUrlException( self::tr( 'The host name %host could not be resolved.', array( '%host' => $host ) ) );
        foreach ( $ips as $ip )
        {
            if ( !self::isPublicIp( $ip ) )
                throw new expOEUrlException( $notReachable );
        }
        return array( 'scheme' => $scheme, 'host' => $host, 'port' => $port, 'ip' => $ips[0], 'ips' => $ips, 'url' => $url );
    }

    /** @return string the absolute URL a Location header points to, relative to the URL it was sent for */
    public static function resolveLocation( $base, $location )
    {
        $location = trim( (string) $location );
        if ( preg_match( '#^[a-z][a-z0-9+.-]*:#i', $location ) )
            return $location;
        $b = parse_url( $base );
        if ( !is_array( $b ) || empty( $b['host'] ) )
            return $location;
        $origin = $b['scheme'] . '://' . $b['host'] . ( isset( $b['port'] ) ? ':' . $b['port'] : '' );
        if ( strpos( $location, '//' ) === 0 )
            return $b['scheme'] . ':' . $location;
        if ( strpos( $location, '/' ) === 0 )
            return $origin . $location;
        $path = isset( $b['path'] ) ? $b['path'] : '/';
        return $origin . substr( $path, 0, strrpos( $path, '/' ) + 1 ) . $location;
    }

    /** @return string a safe file name (no path, no control characters, at most 100 characters), '' if nothing usable is left */
    public static function sanitizeFileName( $name )
    {
        $name = basename( str_replace( '\\', '/', rawurldecode( (string) $name ) ) );
        $name = preg_replace( '/[^\p{L}\p{N}._ -]+/u', '_', $name );
        $name = preg_replace( '/\.{2,}/', '.', (string) $name );
        $name = trim( (string) $name, " ._-" );
        if ( strlen( $name ) > 100 )
        {
            $dot = strrpos( $name, '.' );
            $ext = $dot !== false ? substr( $name, $dot ) : '';
            $name = substr( $name, 0, 100 - strlen( $ext ) ) . $ext;
        }
        return $name;
    }

    /** @return string the name the server offers (Content-Disposition), else the last part of the URL path */
    public static function fileNameFrom( $url, $contentDisposition )
    {
        $name = '';
        if ( $contentDisposition !== '' )
        {
            if ( preg_match( "/filename\\*\\s*=\\s*[^']*'[^']*'([^;]+)/i", $contentDisposition, $m ) )
                $name = rawurldecode( trim( $m[1], " \"" ) );
            elseif ( preg_match( '/filename\s*=\s*"([^"]*)"/i', $contentDisposition, $m ) || preg_match( '/filename\s*=\s*([^;]+)/i', $contentDisposition, $m ) )
                $name = trim( $m[1] );
        }
        $name = self::sanitizeFileName( $name );
        if ( $name === '' )
            $name = self::sanitizeFileName( (string) parse_url( $url, PHP_URL_PATH ) );
        return $name;
    }

    protected static function typeMatches( $type, $patterns )
    {
        foreach ( $patterns as $pattern )
        {
            if ( $pattern === $type || ( substr( $pattern, -1 ) === '*' && strncasecmp( $type, $pattern, strlen( $pattern ) - 1 ) === 0 ) )
                return true;
        }
        return false;
    }

    /**
     * Checks a downloaded file against its name: the extension must be accepted by the upload rules of the
     * editor, the content must be of a type that fits the extension and never an executable or a web page.
     *
     * @return string the (possibly completed) file name
     * @throws expOEUrlException
     */
    public static function checkFile( $path, $name )
    {
        $type = (string) ( new finfo( FILEINFO_MIME_TYPE ) )->file( $path );
        if ( $name === '' )
            $name = 'file';
        if ( strpos( $name, '.' ) === false )
        {
            $byType = array( 'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp', 'image/svg+xml' => 'svg',
                             'application/pdf' => 'pdf', 'audio/mpeg' => 'mp3', 'video/mp4' => 'mp4', 'video/webm' => 'webm' );
            if ( isset( $byType[$type] ) )
                $name .= '.' . $byType[$type];
        }
        $parts = explode( '.', $name );
        array_shift( $parts );
        $extension = $parts ? strtolower( end( $parts ) ) : '';
        $refused = self::tr( 'This file type is not accepted by the editor: %file', array( '%file' => $name ) );
        foreach ( $parts as $part )
        {
            if ( in_array( strtolower( $part ), self::$blockedExtensions, true ) )
                throw new expOEUrlException( $refused );
        }
        // the editor's own check can be switched off for a TinyMCE 3 user, a fetched file must still be of a listed type
        if ( $extension === '' || !expOEEditor::uploadExtensionAllowed( $name ) || !in_array( $extension, expOEEditor::uploadExtensions(), true ) )
            throw new expOEUrlException( $refused );
        $mismatch = self::tr( 'The content of the file is not of the type its name says: %file', array( '%file' => $name ) );
        if ( in_array( $type, self::$executableTypes, true ) )
            throw new expOEUrlException( $mismatch );
        if ( isset( self::$extensionTypes[$extension] ) && !self::typeMatches( $type, self::$extensionTypes[$extension] ) )
            throw new expOEUrlException( $mismatch );
        return $name;
    }

    /**
     * Downloads a URL into a temporary file under var/tmp.
     *
     * @param string $url
     * @return array path (the temporary file, carries the checked file name), dir (its directory), name, size, type; give it to cleanup()
     * @throws expOEUrlException with a message for the user; nothing is left on disk then
     */
    public static function fetch( $url )
    {
        if ( !self::enabled() )
            throw new expOEUrlException( self::tr( 'Upload from a URL is switched off.' ) );
        if ( !function_exists( 'curl_init' ) )
            throw new expOEUrlException( self::tr( 'The file could not be fetched.' ) );
        $max = self::maxSize();
        // a long download must not hit the script's own time limit
        @set_time_limit( self::timeout() + 30 );
        $baseDir = eZSys::varDirectory() . '/tmp/ezoe_url';
        if ( !is_dir( $baseDir ) )
        {
            @mkdir( $baseDir, 0777, true );
            @chmod( $baseDir, 0777 );
        }
        $dir = $baseDir . '/' . bin2hex( random_bytes( 12 ) );
        if ( !@mkdir( $dir, 0700 ) )
            throw new expOEUrlException( self::tr( 'The file could not be fetched.' ) );
        $tmp = $dir . '/download';
        $result = null;
        try
        {
            $current = trim( (string) $url );
            for ( $hop = 0; $hop <= self::MAX_REDIRECTS; $hop++ )
            {
                $target = self::validateUrl( $current );
                $response = self::request( $target, $tmp, $max );
                if ( $response['status'] >= 300 && $response['status'] < 400 && $response['location'] !== '' )
                {
                    if ( $hop === self::MAX_REDIRECTS )
                        throw new expOEUrlException( self::tr( 'The address redirects too often.' ) );
                    $current = self::resolveLocation( $current, $response['location'] );
                    continue;
                }
                if ( $response['status'] !== 200 )
                    throw new expOEUrlException( self::tr( 'The server answered with the status %status, no file was fetched.', array( '%status' => (string) $response['status'] ) ) );
                $result = $response;
                $result['final_url'] = $current;
                break;
            }
            if ( $result === null )
                throw new expOEUrlException( self::tr( 'The address redirects too often.' ) );
            if ( !is_file( $tmp ) || filesize( $tmp ) === 0 )
                throw new expOEUrlException( self::tr( 'The server sent an empty file.' ) );
            $name = self::checkFile( $tmp, self::fileNameFrom( $result['final_url'], $result['disposition'] ) );
            // the file carries its sanitized name: the upload takes the object name and the type from it
            $named = $dir . '/' . $name;
            if ( !@rename( $tmp, $named ) )
                throw new expOEUrlException( self::tr( 'The file could not be fetched.' ) );
            return array( 'path' => $named, 'dir' => $dir, 'name' => $name, 'size' => filesize( $named ),
                          'type' => (string) ( new finfo( FILEINFO_MIME_TYPE ) )->file( $named ) );
        }
        catch ( Exception $e )
        {
            self::cleanup( array( 'dir' => $dir ) );
            throw $e;
        }
    }

    /** One request to a checked target: the connection goes to the checked address, redirects are not followed. */
    protected static function request( array $target, $file, $max )
    {
        $fp = fopen( $file, 'wb' );
        if ( !$fp )
            throw new expOEUrlException( self::tr( 'The file could not be fetched.' ) );
        $state = array( 'status' => 0, 'location' => '', 'disposition' => '', 'written' => 0, 'tooBig' => false );
        $ch = curl_init( $target['url'] );
        $ipForCurl = strpos( $target['ip'], ':' ) !== false ? '[' . $target['ip'] . ']' : $target['ip'];
        curl_setopt_array( $ch, array(
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_RESOLVE => array( $target['host'] . ':' . $target['port'] . ':' . $ipForCurl ),
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT,
            CURLOPT_TIMEOUT => self::timeout(),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'Exponential-OnlineEditor-UrlUpload',
            CURLOPT_HTTPHEADER => array( 'Accept: */*' ),
            CURLOPT_HEADERFUNCTION => function ( $c, $line ) use ( &$state, $max ) {
                $len = strlen( $line );
                if ( preg_match( '#^HTTP/\S+\s+(\d{3})#', $line, $m ) )
                    $state = array_merge( $state, array( 'status' => (int) $m[1], 'location' => '', 'disposition' => '' ) );
                elseif ( stripos( $line, 'location:' ) === 0 )
                    $state['location'] = trim( substr( $line, 9 ) );
                elseif ( stripos( $line, 'content-disposition:' ) === 0 )
                    $state['disposition'] = trim( substr( $line, 20 ) );
                elseif ( stripos( $line, 'content-length:' ) === 0 && $state['status'] === 200 && (int) trim( substr( $line, 15 ) ) > $max )
                    $state['tooBig'] = true;
                return $state['tooBig'] ? 0 : $len;
            },
            CURLOPT_WRITEFUNCTION => function ( $c, $data ) use ( &$state, $fp, $max ) {
                $len = strlen( $data );
                // only the body of a 200 is kept, an error page or a redirect body is dropped
                if ( $state['status'] !== 200 )
                    return $len;
                $state['written'] += $len;
                if ( $state['written'] > $max )
                {
                    $state['tooBig'] = true;
                    return 0;
                }
                return fwrite( $fp, $data ) === $len ? $len : 0;
            },
        ) );
        $ok = curl_exec( $ch );
        $errno = curl_errno( $ch );
        if ( PHP_VERSION_ID < 80000 ) curl_close( $ch ); // no effect since PHP 8.0, deprecated in 8.5
        fclose( $fp );
        if ( $state['tooBig'] )
            throw new expOEUrlException( self::tr( 'The file is larger than the allowed %size.', array( '%size' => self::formatSize( $max ) ) ) );
        if ( !$ok && !( $state['status'] >= 300 && $state['status'] < 400 ) )
        {
            if ( $errno === CURLE_OPERATION_TIMEDOUT )
                throw new expOEUrlException( self::tr( 'The server did not answer in time.' ) );
            throw new expOEUrlException( self::tr( 'The file could not be fetched.' ) );
        }
        return $state;
    }

    public static function formatSize( $bytes )
    {
        return $bytes >= 1048576 ? round( $bytes / 1048576, 1 ) . ' MB' : max( 1, round( $bytes / 1024 ) ) . ' KB';
    }

    /** Deletes the temporary file and its directory. */
    public static function cleanup( $fetched )
    {
        if ( !is_array( $fetched ) || empty( $fetched['dir'] ) || !is_dir( $fetched['dir'] ) )
            return;
        foreach ( (array) glob( $fetched['dir'] . '/*' ) as $left )
        {
            if ( is_file( $left ) )
                @unlink( $left );
        }
        @rmdir( $fetched['dir'] );
    }
}
