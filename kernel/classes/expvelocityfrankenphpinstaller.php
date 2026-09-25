<?php
/**
 * File containing the expVelocityFrankenPHPInstaller class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Puts the FrankenPHP binary in place for the frankenphp engine: `exp:velocity
 * install`, and start's AutoInstall.
 *
 * The binary is one file of 150-180 MB per platform -- too large for the
 * repository or a Composer package -- so it is fetched from the project's
 * GitHub releases for the version velocity.ini pins, and refused unless its
 * SHA-256 matches the value pinned beside it. The file is named after the
 * version (var/vc/frankenphp/bin/frankenphp-1.12.7-linux-x86_64-gnu), so moving to a new
 * release downloads next to the old binary, and going back is a setting.
 *
 * A machine without access to GitHub installs from a file copied there by
 * hand (--from), which is checked the same way; an own build (another PHP
 * version, extra Caddy modules) is named by BinaryPath and never downloaded.
 *
 * @package kernel
 */
class expVelocityFrankenPHPInstaller
{
    /** @var expVelocityFrankenPHP */
    protected $engine;

    /** @var callable|null receives progress lines; set by the console */
    public $progress = null;

    public function __construct( expVelocityFrankenPHP $engine )
    {
        $this->engine = $engine;
    }

    /**
     * The pinned release, without a leading v.
     *
     * @return string
     */
    public function version()
    {
        return ltrim( trim( (string)$this->engine->frankenSetting( 'Version', '' ) ), 'vV' );
    }

    /**
     * @return string gnu, musl or mimalloc
     */
    public function variant()
    {
        $variant = strtolower( trim( (string)$this->engine->frankenSetting( 'Variant', 'gnu' ) ) );
        return $variant !== '' ? $variant : 'gnu';
    }

    /**
     * The release asset for a platform, as the project names them:
     * frankenphp-linux-x86_64 (musl, fully static), -gnu (glibc: faster
     * allocator, loads .so extensions), -mimalloc (musl with mimalloc),
     * frankenphp-linux-aarch64[-gnu], frankenphp-mac-arm64, -mac-x86_64.
     *
     * @param string|null $os PHP_OS_FAMILY
     * @param string|null $machine php_uname('m')
     * @param string|null $variant
     * @return array( asset name or null, error or null )
     */
    public function assetName( $os = null, $machine = null, $variant = null )
    {
        $os = $os !== null ? $os : PHP_OS_FAMILY;
        $machine = strtolower( $machine !== null ? $machine : php_uname( 'm' ) );
        $variant = $variant !== null ? $variant : $this->variant();

        $arch = null;
        if ( in_array( $machine, array( 'x86_64', 'amd64' ), true ) )
            $arch = 'x86_64';
        elseif ( in_array( $machine, array( 'aarch64', 'arm64' ), true ) )
            $arch = 'aarch64';
        if ( $arch === null )
            return array( null, "no FrankenPHP release for the '$machine' architecture; set [FrankenPHPSettings] BinaryPath" );

        if ( $os === 'Darwin' )
            return array( 'frankenphp-mac-' . ( $arch === 'aarch64' ? 'arm64' : 'x86_64' ), null );

        if ( $os !== 'Linux' )
            return array( null, "no FrankenPHP binary download for $os; set [FrankenPHPSettings] BinaryPath" );

        switch ( $variant )
        {
            case 'gnu':
                return array( "frankenphp-linux-$arch-gnu", null );
            case 'musl':
                return array( "frankenphp-linux-$arch", null );
            case 'mimalloc':
                if ( $arch !== 'x86_64' )
                    return array( null, 'Variant=mimalloc is only released for x86_64; use gnu or musl' );
                return array( 'frankenphp-linux-x86_64-mimalloc', null );
        }
        return array( null, "unknown [FrankenPHPSettings] Variant '$variant': use gnu, musl or mimalloc" );
    }

    /**
     * Where the pinned version's binary goes.
     *
     * @return string
     */
    public function targetPath()
    {
        list( $asset ) = $this->assetName();
        // frankenphp-1.12.7-linux-x86_64-gnu: the version, then the asset without its own prefix.
        return $this->directory() . '/frankenphp-' . $this->version() . '-'
             . ( $asset !== null ? preg_replace( '/^frankenphp-/', '', $asset ) : 'unsupported' );
    }

    /**
     * @return string
     */
    public function directory()
    {
        return $this->engine->absolutePath( $this->engine->frankenSetting( 'BinaryDir', 'var/vc/frankenphp/bin' ) );
    }

    /**
     * The SHA-256 pinned for an asset of the pinned version.
     *
     * @param string $asset
     * @return string lower-case hex, '' when none is pinned
     */
    public function pinnedSha256( $asset )
    {
        $pinned = (array)$this->engine->frankenSetting( 'Sha256', array() );
        $value = isset( $pinned[$asset] ) ? strtolower( trim( (string)$pinned[$asset] ) ) : '';
        return preg_match( '/^[0-9a-f]{64}$/', $value ) ? $value : '';
    }

    /**
     * @param string $template DownloadUrl or ApiUrl
     * @param string $asset
     * @return string
     */
    protected function url( $template, $asset )
    {
        return strtr( (string)$template, array( '{version}' => $this->version(), '{asset}' => $asset ) );
    }

    /**
     * Whether a musl C library is what this machine runs on (Alpine and the
     * like), where the glibc build cannot start.
     *
     * @return bool
     */
    protected function muslHost()
    {
        return (bool)glob( '/lib/ld-musl-*' );
    }

    /**
     * Download, verify and put the binary in place.
     *
     * @param array $options force: download even if present; from: install
     *        this file instead of downloading; check: re-hash what is
     *        installed; trustGithubDigest: accept the release's own digest
     *        when velocity.ini pins none; progress: a callable for status lines
     * @return array result
     */
    public function install( array $options = array() )
    {
        if ( isset( $options['progress'] ) && is_callable( $options['progress'] ) )
            $this->progress = $options['progress'];

        $path = trim( (string)$this->engine->frankenSetting( 'BinaryPath', '' ) );
        if ( $path !== '' && empty( $options['from'] ) )
            return $this->checkBinaryPath( $this->engine->absolutePath( $path ) );

        if ( $this->version() === '' )
            return $this->result( false, '[FrankenPHPSettings] Version is not set' );

        list( $asset, $error ) = $this->assetName();
        if ( $asset === null )
            return $this->result( false, $error );
        if ( $this->variant() === 'gnu' && $this->muslHost() )
            return $this->result( false, 'this machine runs on musl, where the gnu build cannot start: set [FrankenPHPSettings] Variant=musl' );

        $target = $this->targetPath();
        $expected = $this->pinnedSha256( $asset );

        if ( !empty( $options['check'] ) )
        {
            if ( !is_file( $target ) )
                return $this->result( false, "not installed: $target" );
            $actual = hash_file( 'sha256', $target );
            $want = $expected !== '' ? $expected : trim( (string)@file_get_contents( $target . '.sha256' ) );
            return $this->result( $want !== '' && hash_equals( $want, $actual ),
                $want === '' ? "no checksum to compare $target with" : ( hash_equals( $want, $actual )
                    ? "$target matches its SHA-256 ($actual)" : "$target does NOT match: expected $want, found $actual" ),
                array( 'binary' => $target, 'sha256' => $actual ) );
        }

        if ( is_file( $target ) && is_executable( $target ) && empty( $options['force'] ) && empty( $options['from'] ) )
            return $this->result( true, "already installed: $target", array( 'binary' => $target ) );

        $url = $this->url( $this->engine->frankenSetting( 'DownloadUrl',
            'https://github.com/php/frankenphp/releases/download/v{version}/{asset}' ), $asset );

        if ( $expected === '' )
        {
            if ( empty( $options['trustGithubDigest'] ) || !empty( $options['from'] ) )
                return $this->result( false, "velocity.ini pins no SHA-256 for $asset of version " . $this->version()
                    . ': add [FrankenPHPSettings] Sha256[' . $asset . ']=<hex>, or run install --trust-github-digest'
                    . ' to take the digest GitHub publishes for the release (the same source as the file itself)' );
            $expected = $this->githubDigest( $asset );
            if ( $expected === '' )
                return $this->result( false, "could not read the release digest for $asset from GitHub" );
            $this->say( "no pinned SHA-256; using GitHub's digest for $asset: $expected" );
        }

        $directory = $this->directory();
        if ( !is_dir( $directory ) && !@mkdir( $directory, 0755, true ) )
            return $this->result( false, "could not create $directory" );

        $part = $directory . '/.' . basename( $target ) . '.part.' . getmypid();
        if ( !empty( $options['from'] ) )
        {
            $from = (string)$options['from'];
            if ( !is_file( $from ) )
                return $this->result( false, "no such file: $from" );
            $this->say( "copying $from" );
            if ( !@copy( $from, $part ) )
                return $this->result( false, "could not copy $from to $directory" );
        }
        else
        {
            $this->say( "downloading $url" );
            $downloaded = $this->download( $url, $part );
            if ( $downloaded !== true )
            {
                @unlink( $part );
                return $this->result( false, 'download failed (' . $downloaded . '). Without network access: fetch '
                    . "$url elsewhere, check it has SHA-256 $expected, and run exp:velocity install --from=<file>;"
                    . ' or name an own binary with [FrankenPHPSettings] BinaryPath' );
            }
        }

        $actual = hash_file( 'sha256', $part );
        if ( !hash_equals( $expected, $actual ) )
        {
            @unlink( $part );
            return $this->result( false, "SHA-256 mismatch for $asset: expected $expected, got $actual -- nothing installed" );
        }

        @chmod( $part, 0755 );
        $output = array();
        $code = 1;
        @exec( escapeshellarg( $part ) . ' version 2>&1', $output, $code );
        if ( $code !== 0 )
        {
            @unlink( $part );
            return $this->result( false, "the binary does not run on this machine: " . trim( implode( ' ', array_slice( $output, -2 ) ) )
                . ( $this->variant() === 'gnu' ? ' (try Variant=musl, which needs nothing from the system)' : '' ) );
        }

        if ( !@rename( $part, $target ) )
        {
            @unlink( $part );
            return $this->result( false, "could not move the binary to $target" );
        }
        @file_put_contents( $target . '.sha256', $actual . "\n" );

        return $this->result( true, 'installed ' . self::cleanVersion( (string)( $output[0] ?? '' ) )
            . " as $target (SHA-256 verified)", array( 'binary' => $target, 'sha256' => $actual, 'asset' => $asset ) );
    }

    /**
     * `frankenphp version` without the module hash, and without the doubled
     * "vv" the official glibc builds of 1.12.7 print ("FrankenPHP vv1.12.7").
     *
     * @param string $line "FrankenPHP v1.12.7 PHP 8.5.11 Caddy v2.11.4 h1:..."
     * @return string
     */
    public static function cleanVersion( $line )
    {
        return trim( preg_replace( array( '/\s+h1:\S+/', '/\bvv(?=\d)/' ), array( '', 'v' ), (string)$line ) );
    }

    /**
     * An own binary: nothing to download, checked only against BinarySha256
     * when one is set.
     *
     * @param string $binary
     * @return array result
     */
    protected function checkBinaryPath( $binary )
    {
        if ( !is_file( $binary ) || !is_executable( $binary ) )
            return $this->result( false, "BinaryPath names no executable file: $binary" );

        $pinned = strtolower( trim( (string)$this->engine->frankenSetting( 'BinarySha256', '' ) ) );
        if ( $pinned !== '' )
        {
            $actual = hash_file( 'sha256', $binary );
            if ( !hash_equals( $pinned, $actual ) )
                return $this->result( false, "BinaryPath $binary does not match BinarySha256: expected $pinned, found $actual" );
        }
        return $this->result( true, "BinaryPath is set; using $binary" . ( $pinned !== '' ? ' (SHA-256 verified)' : '' ),
                              array( 'binary' => $binary ) );
    }

    /**
     * The digest GitHub publishes for an asset of the pinned release.
     *
     * @param string $asset
     * @return string lower-case hex, '' when unavailable
     */
    protected function githubDigest( $asset )
    {
        $api = $this->url( $this->engine->frankenSetting( 'ApiUrl',
            'https://api.github.com/repos/php/frankenphp/releases/tags/v{version}' ), $asset );
        $json = $this->fetch( $api );
        $release = json_decode( (string)$json, true );
        foreach ( (array)( $release['assets'] ?? array() ) as $item )
            if ( ( $item['name'] ?? '' ) === $asset
                 && preg_match( '/^sha256:([0-9a-f]{64})$/', (string)( $item['digest'] ?? '' ), $m ) )
                return $m[1];
        return '';
    }

    /**
     * A small document over HTTPS, for the release metadata.
     *
     * @param string $url
     * @return string|false
     */
    protected function fetch( $url )
    {
        $context = stream_context_create( array( 'http' => array(
            'header' => "User-Agent: exp-velocity\r\nAccept: application/vnd.github+json\r\n",
            'timeout' => 30, 'follow_location' => 1 ) ) );
        return @file_get_contents( $url, false, $context );
    }

    /**
     * Download a file: with ext/curl, else PHP's own HTTPS streams, else the
     * curl or wget program.
     *
     * @param string $url
     * @param string $file
     * @return true|string true, or what went wrong
     */
    protected function download( $url, $file )
    {
        if ( function_exists( 'curl_init' ) )
        {
            $out = @fopen( $file, 'wb' );
            if ( !$out )
                return "cannot write $file";
            $handle = curl_init( $url );
            $last = 0;
            $say = $this->progress;
            curl_setopt_array( $handle, array(
                CURLOPT_FILE => $out, CURLOPT_FOLLOWLOCATION => true, CURLOPT_FAILONERROR => true,
                CURLOPT_CONNECTTIMEOUT => 15, CURLOPT_LOW_SPEED_LIMIT => 1024, CURLOPT_LOW_SPEED_TIME => 60,
                CURLOPT_USERAGENT => 'exp-velocity',
                CURLOPT_NOPROGRESS => $say === null,
                CURLOPT_PROGRESSFUNCTION => function ( $h, $total, $done ) use ( &$last, $say ) {
                    // Every 16 MB and once at the end; curl calls this far more often.
                    if ( $say !== null && $total > 0 && $last < $total
                         && ( $done - $last > 16 * 1048576 || $done === $total ) )
                    {
                        $last = $done;
                        call_user_func( $say, sprintf( '  %d of %d MB', $done / 1048576, $total / 1048576 ) );
                    }
                    return 0;
                },
            ) );
            $ok = curl_exec( $handle );
            $error = curl_error( $handle );
            curl_close( $handle );
            fclose( $out );
            return $ok ? true : ( $error !== '' ? $error : 'curl failed' );
        }

        if ( in_array( 'https', stream_get_wrappers(), true ) )
        {
            $context = stream_context_create( array( 'http' => array(
                'header' => "User-Agent: exp-velocity\r\n", 'timeout' => 60, 'follow_location' => 1 ) ) );
            $in = @fopen( $url, 'rb', false, $context );
            $out = @fopen( $file, 'wb' );
            if ( !$in || !$out )
                return $in ? "cannot write $file" : 'cannot open ' . $url;
            $copied = stream_copy_to_stream( $in, $out );
            fclose( $in );
            fclose( $out );
            return $copied > 0 ? true : 'nothing received';
        }

        foreach ( array( 'curl' => '-fsSL -o %s %s', 'wget' => '-q -O %s %s' ) as $program => $format )
        {
            $binary = $this->which( $program );
            if ( $binary === false )
                continue;
            @exec( escapeshellarg( $binary ) . ' ' . sprintf( $format, escapeshellarg( $file ), escapeshellarg( $url ) ) . ' 2>&1', $output, $code );
            return $code === 0 ? true : "$program exited with $code";
        }
        return 'no way to download: neither ext/curl, HTTPS streams, curl nor wget is available';
    }

    /**
     * @param string $name
     * @return string|false
     */
    protected function which( $name )
    {
        foreach ( array_filter( explode( PATH_SEPARATOR, (string)getenv( 'PATH' ) . PATH_SEPARATOR . expVelocity::DEFAULT_PATH ) ) as $dir )
            if ( is_executable( rtrim( $dir, '/' ) . '/' . $name ) )
                return rtrim( $dir, '/' ) . '/' . $name;
        return false;
    }

    /**
     * @param string $line
     */
    protected function say( $line )
    {
        if ( $this->progress !== null )
            call_user_func( $this->progress, $line );
    }

    /**
     * @param bool $ok
     * @param string $message
     * @param mixed $data
     * @return array
     */
    protected function result( $ok, $message, $data = null )
    {
        return array( 'ok' => (bool)$ok, 'message' => $message, 'data' => $data );
    }
}
