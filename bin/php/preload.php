#!/usr/bin/env php
<?php
/**
 * File containing the preload.php script to preload your website cache files to speed up page loading of your website by siteaccess name parameter.
 *
 * Warms the main section pages (derived from site.ini [SiteSettings] SiteURL /
 * URLTranslationKeyword) then spiders the entire site via wget (recursive,
 * level 3) to warm all page caches.  Produces rich, colourised terminal output.
 *
 * Usage:
 *   ./bin/php/preload.php [--siteaccess <name>]
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

require_once 'autoload.php';

$cli    = eZCLI::instance();
$script = eZScript::instance(
    array(
        'description'    => "Exponential CMS — site preloader & cache warmer\n\n" .
                            "Warms section pages then spiders the entire site via wget.\n\n" .
                            "Usage: ./bin/php/preload.php [--siteaccess <name>]",
        'use-session'    => false,
        'use-modules'    => false,
        'use-extensions' => true,
    )
);

$script->startup();
$script->getOptions( "", "", array() );
$script->initialize();

// ══════════════════════════════════════════════════════════════════════════════
//  ANSI colour helpers — degrade gracefully when stdout is not a TTY
// ══════════════════════════════════════════════════════════════════════════════

$isTTY = function_exists( 'posix_isatty' ) && posix_isatty( STDOUT );

$esc = function( $code, $text ) use ( $isTTY )
{
    return $isTTY ? "\033[{$code}m{$text}\033[0m" : $text;
};

$bold    = function( $t ) use ( $esc ) { return $esc( '1',     $t ); };
$dim     = function( $t ) use ( $esc ) { return $esc( '2',     $t ); };
$red     = function( $t ) use ( $esc ) { return $esc( '1;31',  $t ); };
$green   = function( $t ) use ( $esc ) { return $esc( '1;32',  $t ); };
$yellow  = function( $t ) use ( $esc ) { return $esc( '1;33',  $t ); };
$magenta = function( $t ) use ( $esc ) { return $esc( '1;35',  $t ); };
$cyan    = function( $t ) use ( $esc ) { return $esc( '1;36',  $t ); };
$white   = function( $t ) use ( $esc ) { return $esc( '1;37',  $t ); };
$gray    = function( $t ) use ( $esc ) { return $esc( '0;90',  $t ); };

// Speed-tier label + colour for a wall-clock value in ms
$speedLabel = function( $ms ) use ( $green, $yellow, $red )
{
    if ( $ms < 200  ) return $green(  $ms . 'ms  blazing'  );
    if ( $ms < 400  ) return $green(  $ms . 'ms  fast'     );
    if ( $ms < 700  ) return $yellow( $ms . 'ms  ok'       );
    if ( $ms < 1200 ) return $yellow( $ms . 'ms  slow'     );
    return                   $red(    $ms . 'ms  SLOW!'    );
};

// HTTP status badge
$statusBadge = function( $http ) use ( $green, $yellow, $red, $cyan )
{
    if ( $http === '200' )  return $green(  '[ 200 OK ]'       );
    if ( $http[0] === '3' ) return $cyan(   "[ {$http} ↷  ]"  );
    if ( $http[0] === '4' ) return $yellow( "[ {$http} ⚠  ]"  );
    if ( $http[0] === '5' ) return $red(    "[ {$http} ✗  ]"  );
    return $yellow( "[ {$http}    ]" );
};

// Bytes → human-readable
$humanBytes = function( $bytes )
{
    if ( $bytes >= 1048576 ) return round( $bytes / 1048576, 1 ) . ' MB';
    if ( $bytes >= 1024    ) return round( $bytes / 1024,    1 ) . ' kB';
    return $bytes . ' B';
};

// Phase banner
$phaseBanner = function( $phase, $total, $title ) use ( $bold, $cyan )
{
    $bar = str_repeat( '━', 66 );
    echo "\n" . $cyan( $bar ) . "\n";
    echo '  ' . $bold( $cyan( "PHASE {$phase} / {$total}" ) ) . '  ' . $bold( $title ) . "\n";
    echo $cyan( $bar ) . "\n\n";
};

// ══════════════════════════════════════════════════════════════════════════════
//  Resolve site base URL from site.ini
// ══════════════════════════════════════════════════════════════════════════════

$siteIni = eZINI::instance( 'site.ini' );

if ( !$siteIni->hasVariable( 'SiteSettings', 'SiteURL' ) )
{
    $cli->error( 'Cannot determine site URL: SiteSettings.SiteURL is not set in site.ini' );
    $script->shutdown( 1 );
}

$rawURL = rtrim( $siteIni->variable( 'SiteSettings', 'SiteURL' ), '/' );
$site   = ( strpos( $rawURL, '://' ) === false ) ? 'https://' . $rawURL : $rawURL;
$host   = parse_url( $site, PHP_URL_HOST );

// Build section URL list from URLTranslationKeyword (semicolon-separated)
$keywords = '';
if ( $siteIni->hasVariable( 'SiteSettings', 'URLTranslationKeyword' ) )
    $keywords = $siteIni->variable( 'SiteSettings', 'URLTranslationKeyword' );

$sections = array_map(
    function ( $kw ) use ( $site ) { return $site . '/' . trim( $kw, '/ ' ) . '/'; },
    array_filter( explode( ';', $keywords ), function ( $kw ) { return trim( $kw ) !== ''; } )
);
if ( empty( $sections ) )
    $sections = array( $site . '/' );

unset( $siteIni, $rawURL, $keywords );

// ══════════════════════════════════════════════════════════════════════════════
//  Curl fetch helper — returns timing, HTTP status, perf metrics
// ══════════════════════════════════════════════════════════════════════════════

$tmpFile = '/tmp/ezpreload_bench.html';

$fetch = function( $url ) use ( $tmpFile )
{
    $t0   = microtime( true );
    $meta = shell_exec(
        "curl -sk" .
        " -o "          . escapeshellarg( $tmpFile ) .
        " -w '%{http_code} %{time_total} %{size_download}'" .
        " --max-time 30 " . escapeshellarg( $url )
    );
    $wall_ms = round( ( microtime( true ) - $t0 ) * 1000 );

    $parts     = array_pad( explode( ' ', trim( (string) $meta ) ), 3, '0' );
    $http      = $parts[0];
    $sizeBytes = is_numeric( $parts[2] ) ? (int) $parts[2] : 0;

    $html = file_exists( $tmpFile ) ? file_get_contents( $tmpFile ) : '';

    $extract = function( $key ) use ( $html )
    {
        if ( preg_match( '/\b' . preg_quote( $key, '/' ) . '\s*:\s*([0-9.]+)/', $html, $m ) )
            return $m[1];
        return null;
    };

    return array(
        'http'        => $http,
        'wall_ms'     => $wall_ms,
        'size_bytes'  => $sizeBytes,
        'kernel_init' => $extract( 'kernel-init' ),
        'kernel_run'  => $extract( 'kernel-run' ),
        'get_content' => $extract( 'get-content' ),
        'total_ms'    => $extract( 'total' ),
        'peak_memory' => $extract( 'peak-memory' ),
        'db_queries'  => $extract( 'db-queries' ),
        'db_time'     => $extract( 'db-time' ),
        'html'        => $html,
    );
};

// ══════════════════════════════════════════════════════════════════════════════
//  PHASE 1 — Warm section pages
// ══════════════════════════════════════════════════════════════════════════════

$phaseBanner( 1, 2, 'Warming Section Pages' );

$sectionTotal = count( $sections );
$sectionIdx   = 0;

foreach ( $sections as $url )
{
    $sectionIdx++;

    echo '  ' . $dim( "[{$sectionIdx}/{$sectionTotal}]" )
       . '  ' . $bold( $cyan( $url ) ) . "\n";
    flush();

    $r = $fetch( $url );

    $badge = $statusBadge( $r['http'] );
    $speed = $speedLabel( $r['wall_ms'] );
    $size  = $humanBytes( $r['size_bytes'] );

    echo '         ' . $badge
       . '  wall: '  . $speed
       . '  size: '  . $dim( $size ) . "\n";

    $hasDb   = $r['db_queries'] !== null || $r['db_time']    !== null;
    $hasKern = $r['kernel_init'] !== null || $r['kernel_run'] !== null
            || $r['get_content'] !== null || $r['total_ms']  !== null;
    $hasMem  = $r['peak_memory'] !== null;

    if ( $hasDb )
        echo '         ' . $gray( 'db       ' )
           . '  queries: ' . $yellow( $r['db_queries'] ?? '–' )
           . '  time: '    . $yellow( ( $r['db_time'] ?? '–' ) . 'ms' ) . "\n";

    if ( $hasKern )
        echo '         ' . $gray( 'kernel   ' )
           . '  init: '        . $dim( ( $r['kernel_init'] ?? '–' ) . 'ms' )
           . '  run: '         . $dim( ( $r['kernel_run']  ?? '–' ) . 'ms' )
           . '  get-content: ' . $dim( ( $r['get_content'] ?? '–' ) . 'ms' )
           . '  total: '       . $dim( ( $r['total_ms']    ?? '–' ) . 'ms' ) . "\n";

    if ( $hasMem )
        echo '         ' . $gray( 'memory   ' )
           . '  peak: ' . $magenta( $r['peak_memory'] . ' MB' ) . "\n";

    if ( $r['http'] === '500' )
    {
        echo '         ' . $red( '── SERVER ERROR (first 5 non-empty lines) ──' ) . "\n";
        $errLines = array_slice( explode( "\n", strip_tags( $r['html'] ) ), 0, 20 );
        $shown    = 0;
        foreach ( $errLines as $errLine )
        {
            if ( trim( $errLine ) === '' ) continue;
            echo '           ' . $red( trim( $errLine ) ) . "\n";
            if ( ++$shown >= 5 ) break;
        }
    }

    echo "\n";
    flush();
}

// ══════════════════════════════════════════════════════════════════════════════
//  PHASE 2 — Spider the full site via wget, parsed line-by-line
// ══════════════════════════════════════════════════════════════════════════════

$phaseBanner( 2, 2, "Spidering {$site}/ — recursive, depth 3" );

$reject = implode( ',', array(
    // styles & scripts
    'js', 'css', 'map',
    // images
    'png', 'gif', 'jpg', 'jpeg', 'webp', 'svg', 'ico', 'bmp', 'tif', 'tiff',
    'avif', 'heic', 'heif', 'jxl', 'psd', 'ai', 'eps', 'raw', 'cr2', 'nef',
    // fonts
    'woff', 'woff2', 'ttf', 'eot', 'otf',
    // video
    'mp4', 'webm', 'ogv', 'ogg', 'avi', 'mov', 'mkv', 'flv', 'wmv', 'mpg',
    'mpeg', 'mp2', 'm4v', 'm2v', 'ts', 'mts', 'm2ts', 'vob', 'rm', 'rmvb',
    '3gp', '3g2', 'asf', 'divx', 'xvid', 'f4v', 'swf',
    // audio
    'mp3', 'wav', 'flac', 'aac', 'm4a', 'wma', 'aiff', 'aif', 'ape', 'opus',
    'ra', 'mid', 'midi', 'amr', 'au', 'mka',
    // documents / archives
    'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp',
    'rtf', 'txt', 'csv', 'epub', 'mobi',
    'zip', 'tar', 'gz', 'tgz', 'bz2', 'xz', 'zst', 'rar', '7z', 'cab',
    'iso', 'dmg', 'img', 'bin', 'deb', 'rpm', 'apk', 'exe', 'msi', 'pkg',
    // data / misc
    'json', 'xml', 'rss', 'atom', 'yaml', 'yml', 'sql', 'db', 'sqlite',
) );

$wgetCmd =
    "wget --no-check-certificate --spider --recursive --level=3" .
    " --no-directories --delete-after -P /tmp -nv" .
    " --reject="       . escapeshellarg( $reject ) .
    " --reject-regex='/(stats|calendar|groupeventcalendar)/'" .
    " --domains="      . escapeshellarg( $host ) .
    " "                . escapeshellarg( $site . "/" ) .
    " 2>&1";

$handle = popen( $wgetCmd, 'r' );
if ( $handle === false )
{
    $cli->error( 'Failed to launch wget — is it installed and on $PATH?' );
    $script->shutdown( 1 );
}

// Running counters
$urlCount    = 0;
$brokenCount = 0;
$authCount   = 0;
$authBuffer  = 0;   // consecutive auth-fail lines — collapsed into one summary line

$pendingUrl  = null; // bare URL waiting to see if next line is "broken link"

// Flush any buffered consecutive auth failures as a single collapsed summary line
$flushAuth = function() use ( &$authBuffer, $yellow, $dim )
{
    if ( $authBuffer === 0 ) return;
    $plural = $authBuffer === 1 ? 'resource' : 'resources';
    echo '  ' . $dim( $yellow( '⊘' ) )
       . '  ' . $dim( "auth-protected — {$authBuffer} {$plural} skipped" )
       . "\n";
    flush();
    $authBuffer = 0;
};

// Print a fetched URL line.
// Type is indicated by a leading marker rather than indentation, since wget
// does not crawl in depth-first tree order so path-depth indentation produces
// a misleading, jumbled hierarchy.
//
//  ✓  normal content page          — cyan path, full brightness
//  ▸  /layout/set/print/* variant  — dim, labelled "print"
//  ⬇  /content/download/* asset   — dim, labelled "download"
//
$printUrl = function( $ts, $url, $bytes, $redirects, $extra = '' )
             use ( &$urlCount, $gray, $green, $yellow, $cyan, $dim, $humanBytes )
{
    $urlCount++;
    $path = parse_url( $url, PHP_URL_PATH ) ?: '/';

    // Classify by path prefix
    $isPrint    = ( strpos( $path, '/layout/set/print' ) === 0 );
    $isDownload = ( strpos( $path, '/content/download' ) === 0 );

    if ( $isPrint )
    {
        // Strip the /layout/set/print prefix so the meaningful path is visible
        $shortPath = substr( $path, strlen( '/layout/set/print' ) ) ?: '/';
        $icon      = $dim( '▸' );
        $label     = $dim( 'print ' );
        $pathStr   = $dim( $shortPath );
    }
    elseif ( $isDownload )
    {
        $icon      = $dim( '⬇' );
        $label     = $dim( 'download ' );
        $pathStr   = $dim( $path );
    }
    else
    {
        // Green tick for a direct fetch; yellow recycle symbol for a redirect chain
        $icon      = ( (int) $redirects > 1 ) ? $yellow( '↻' ) : $green( '✓' );
        $label     = '';
        $pathStr   = $cyan( $path );
    }

    $byteStr = $bytes > 0 ? $dim( $humanBytes( $bytes ) ) : '';

    echo '  ' . $gray( $ts )
       . '  ' . $icon
       . '  ' . $label . $pathStr
       . ( $byteStr ? '  ' . $byteStr : '' )
       . ( $extra   ? '  ' . $extra   : '' )
       . '  ' . $dim( '#' . $urlCount )
       . "\n";
    flush();
};

// ── main wget output loop ─────────────────────────────────────────────────────

while ( !feof( $handle ) )
{
    $rawLine = fgets( $handle );
    if ( $rawLine === false ) break;
    $line = rtrim( $rawLine );

    // skip blank lines and wget's internal housekeeping noise
    if ( $line === '' )                        continue;
    if ( strpos( $line, 'unlink:'   ) === 0 ) continue;
    if ( strpos( $line, 'Removing ' ) === 0 ) continue;

    // ── resolve a pending bare-URL that may be a broken-link lead-in ──────────
    if ( $pendingUrl !== null )
    {
        if ( strpos( $line, 'broken link' ) !== false )
        {
            $flushAuth();
            $brokenCount++;
            echo '  ' . $red( '✗  BROKEN LINK' )
               . '  ' . $bold( $pendingUrl ) . "\n";
            flush();
            $pendingUrl = null;
            continue;
        }
        // Not a broken-link message — emit the held URL quietly and fall through
        $flushAuth();
        echo '  ' . $dim( $pendingUrl ) . "\n";
        $pendingUrl = null;
    }

    // ── standard spider URL line ───────────────────────────────────────────────
    // "2026-06-12 07:33:29 URL:https://host/path [BYTES] -> "FILE" [N]"
    if ( preg_match(
        '/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) URL:(https?:\/\/\S+) \[(\d+)\] -> "[^"]+" \[(\d+)\]$/',
        $line, $m
    ) ) {
        $flushAuth();
        $printUrl( $m[1], $m[2], (int) $m[3], (int) $m[4] );
        continue;
    }

    // ── content/download line: timestamp + URL + HTTP status ──────────────────
    // "2026-06-12 07:34:27 URL: https://host/content/download/N/M 200 OK"
    if ( preg_match(
        '/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) URL:\s+(https?:\/\/\S+)\s+(\d+)\s+(\w+)$/',
        $line, $m
    ) ) {
        $flushAuth();
        $extra = $m[3][0] === '2'
            ? $dim(    $m[3] . ' ' . $m[4] )
            : $yellow( $m[3] . ' ' . $m[4] );
        $printUrl( $m[1], $m[2], 0, 1, $extra );
        continue;
    }

    // ── bare URL ending in ":" — potential broken-link lead-in ────────────────
    if ( preg_match( '/^(https?:\/\/\S+):$/', $line, $m ) )
    {
        $pendingUrl = $m[1];
        continue;
    }

    // ── auth failure ───────────────────────────────────────────────────────────
    if ( strpos( $line, 'Username/Password Authentication Failed' ) !== false )
    {
        $authCount++;
        $authBuffer++;
        continue;
    }

    // ── wget summary lines ─────────────────────────────────────────────────────
    if ( preg_match( '/^FINISHED --(.+)--$/', $line, $m ) )
    {
        $flushAuth();
        echo "\n  " . $bold( 'Finished' ) . '  ' . $gray( $m[1] ) . "\n";
        continue;
    }
    if ( preg_match( '/^Total wall clock time:\s*(.+)$/', $line, $m ) )
    {
        echo '  ' . $bold( 'Total time:' ) . '  ' . $green( $m[1] ) . "\n";
        continue;
    }
    if ( preg_match( '/^Downloaded:\s*(\d+) files,\s*(.+)$/', $line, $m ) )
    {
        echo '  ' . $bold( 'Downloaded:' )
           . '  ' . $green( $m[1] . ' files' )
           . '  ' . $dim(   $m[2] ) . "\n";
        continue;
    }
    if ( preg_match( '/^Found (\d+) broken links?\.$/', $line, $m ) )
    {
        $n = (int) $m[1];
        echo '  ' . ( $n > 0
            ? $red(   "⚠  Found {$n} broken link(s)." )
            : $green( '✓  No broken links found.'     ) ) . "\n";
        continue;
    }

    // ── broken-link URL list printed by wget at the end ───────────────────────
    // plain "https://..." lines with no trailing colon and no timestamp prefix
    if ( preg_match( '/^https?:\/\/[^\s]+$/', $line ) )
    {
        echo '     ' . $red( '↳  ' ) . $dim( $line ) . "\n";
        continue;
    }

    // ── anything else: pass through dimly ─────────────────────────────────────
    $flushAuth();
    echo '  ' . $dim( $line ) . "\n";
    flush();
}

$flushAuth();
pclose( $handle );

// ══════════════════════════════════════════════════════════════════════════════
//  Final summary
// ══════════════════════════════════════════════════════════════════════════════

$bar = str_repeat( '━', 66 );
echo "\n" . $cyan( $bar ) . "\n";
echo '  ' . $bold( $white( 'PRELOAD COMPLETE' ) ) . "\n";
echo '  ' . $dim( 'Pages cached   ' ) . '  ' . $green( $urlCount   ) . "\n";
echo '  ' . $dim( 'Auth skipped   ' ) . '  ' . $yellow( $authCount )
   . '  ' . $dim( '(password-protected resources)' ) . "\n";
echo '  ' . $dim( 'Broken links   ' ) . '  '
   . ( $brokenCount > 0 ? $red( $brokenCount ) : $green( $brokenCount ) ) . "\n";
echo $cyan( $bar ) . "\n\n";

$script->shutdown();

?>