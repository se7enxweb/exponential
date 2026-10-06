<?php
/**
 * File containing the expPreloadRunner class.
 *
 * The preloader as a library: it warms the site's page caches and reports what
 * it did through a callback, so the same code can drive a terminal, an
 * administration view or anything else.
 *
 * bin/php/preload.php did this work inline and shelled out to curl and wget,
 * which is fine for a terminal but unusable from a module view: wget need not
 * be installed, shell_exec is often disabled under php-fpm, and neither gives
 * the caller anything to render progressively. The reference implementation in
 * exponentialbasic went the other way and had the administration page spawn the
 * command line script, which means the web request inherits the script's
 * environment, its exit codes and its assumptions about being a tty.
 *
 * This runs in process on the curl extension, and crawls with its own queue
 * rather than wget, so the administration view needs no shell access and no
 * external binaries.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */


if ( !class_exists( 'expPreloadRunner', false ) ) {
class expPreloadRunner
{
    /**
     * Extensions never worth requesting: fetching them warms nothing, and on a
     * media heavy site they are the bulk of the links on a page.
     */
    const SKIP_EXTENSIONS =
        'jpg,jpeg,png,gif,webp,svg,ico,bmp,tiff,avif,' .
        'css,js,map,woff,woff2,ttf,eot,otf,' .
        'mp3,mp4,m4a,m4v,mov,avi,mkv,webm,ogg,oga,ogv,wav,flac,' .
        'pdf,doc,docx,xls,xlsx,ppt,pptx,odt,ods,odp,rtf,txt,csv,epub,mobi,' .
        'zip,tar,gz,tgz,bz2,xz,zst,rar,7z,cab,iso,dmg,img,bin,deb,rpm,apk,exe,msi,pkg,' .
        'json,xml,rss,atom,yaml,yml,sql,db,sqlite';

    /**
     * Paths that are never public pages and must not be crawled.
     *
     * The administration modules matter more than they look. With debug output
     * enabled every rendered page carries the toolbar's template links, and on
     * a single content page those outnumber the real links four to one - 81
     * /visual/ links against 18 content ones on /healthy-eating. Left in, the
     * crawler spends its whole budget opening the template editor instead of
     * warming the site, and does so as whatever user the request runs as.
     *
     * Content lives under url aliases, so excluding module paths costs nothing.
     * content/search is the exception and stays: it is a real public page.
     */
    const SKIP_PATTERN =
        '#^/(visual|setup|package|class|role|section|settings|workflow|' .
        'infocollector|notification|pdf|trigger|state|collaboration|ezinfo|' .
        'switchlanguage)(/|$)' .
        '|^/content/(edit|draft|history|translations|versionview|removeobject|' .
        'copy|move|browse|action|upload|trash|bookmark|pendinglist|search)(/|$)' .
        '|^/user/(login|logout|preferences|setting)(/|$)' .
        // The debug block at the foot of a page. These are fragment targets,
        // and they reached this list only because fragments were being parsed
        // into paths; that is fixed, and the rule is kept, unanchored now, in
        // case a template emits a real link to either.
        '|/(collapse-[0-9]+|debug-end)(/|$)' .
        '|/(stats|calendar|groupeventcalendar)(/|$)' .
        // Exponential Velocity's own panel (/Q/dashboard, /Q/panel, /Q/metrics ...): the server's, not the site's.
        '|^/Q(/|$)#';

    /**
     * How many linking pages to keep per broken target.
     *
     * A broken link in a template or in the site menu is on every page of the
     * site, and listing four thousand of them helps nobody: the first few name
     * the pattern, and the count says how far it reaches.
     */
    const MAX_REFERRERS = 20;

    private $emit;
    private $options;
    private $seen = array();
    private $queue = array();
    private $siteaccessNames = null;
    private $storeCallback = null;
    private $address = null;

    /** image url => array( page url => true ), the pages that show it, capped like the referrers. */
    private $images = array();

    /** target url => array( linking page url => true ), capped. */
    private $referrers = array();

    /** target url => how many further linking pages were seen past the cap. */
    private $referrersOver = array();

    /** Everything that did not come back as a page, in the order found. */
    private $problems = array();

    private $counts = array(
        'fetched' => 0, 'skipped' => 0, 'broken' => 0, 'denied' => 0, 'bytes' => 0,
        'images' => 0, 'images_broken' => 0 );

    /**
     * @param callable $emit  Called as $emit( $type, $message, $data ). Types are
     *                        'phase', 'ok', 'warn', 'error', 'info', 'report'
     *                        and 'done'; a front end may render them however it
     *                        likes. 'report' closes the run and carries a
     *                        'broken' array of
     *                        ( url, path, status, reason, referrers, more ),
     *                        one entry per broken target, for a front end that
     *                        can do more with it than print it.
     * @param array    $options  base_url, max_pages, max_depth, timeout.
     */
    public function __construct( $emit, array $options = array() )
    {
        $this->emit = $emit;
        $this->options = $options + array(
            'base_url'   => '',
            'base_path'  => null,
            'siteaccess' => '',
            'max_pages'  => 250,
            'max_depth'  => 3,
            'timeout'    => 20,
            // Paths relative to the site ('/about-us') to start from instead
            // of the site root and its sections; with max_depth 0 exactly
            // these pages are fetched.
            'start_paths' => array(),
            // Also request the images the warmed pages show (their <img src> on this host), once each,
            // and report the ones that do not come back. An image alias is made when a page that shows it is
            // rendered; this finds the ones that are still missing and the pages that show them.
            'images'     => false,
            'max_images' => 2000,
        );
    }

    /**
     * Registers a callable handed every page that was fetched successfully, as
     * ( $url, $path, $body ).
     *
     * The crawl already holds the rendered html of every page a visitor can
     * reach. The static cache generator stores exactly that, so it needs no
     * request of its own and no second opinion about which urls exist.
     *
     * @param callable|null $callback
     */
    public function setStoreCallback( $callback )
    {
        $this->storeCallback = $callback;
    }

    private function say( $type, $message, array $data = array() )
    {
        call_user_func( $this->emit, $type, $message, $data );
    }

    /**
     * The site.ini of the chosen siteaccess (its own settings/siteaccess/<name>/site.ini.append.php), or the
     * current one when none was chosen or it has none.
     *
     * @return eZINI
     */
    private function siteaccessIni()
    {
        $siteaccess = trim( (string)$this->options['siteaccess'] );
        if ( $siteaccess !== '' && preg_match( '#^[A-Za-z0-9_-]+$#', $siteaccess )
             && file_exists( 'settings/siteaccess/' . $siteaccess . '/site.ini.append.php' ) )
            return eZINI::instance( 'site.ini.append.php', 'settings/siteaccess/' . $siteaccess, null, false, null, true );
        return eZINI::instance( 'site.ini' );
    }

    /**
     * The site's base url, from site.ini unless the caller supplied one.
     *
     * SiteURL is stored without a scheme, so one is added rather than assumed
     * further down where a missing scheme would silently produce a relative url.
     */
    public function baseUrl()
    {
        $url = trim( (string)$this->options['base_url'] );

        // Which site to warm has to be asked for, not assumed from the current
        // siteaccess. Run from the administration interface the current
        // siteaccess is the admin one, whose SiteURL is the administration host
        // - localhost on a default install - so warming it would warm nothing a
        // visitor ever sees. The command line script takes --siteaccess for the
        // same reason.
        if ( $url === '' && trim( (string)$this->options['siteaccess'] ) !== '' )
        {
            $saIni = $this->siteaccessIni();
            if ( $saIni->hasVariable( 'SiteSettings', 'SiteURL' ) )
                $url = (string)$saIni->variable( 'SiteSettings', 'SiteURL' );
        }

        if ( $url === '' )
        {
            $ini = eZINI::instance( 'site.ini' );
            if ( !$ini->hasVariable( 'SiteSettings', 'SiteURL' ) )
                return false;
            $url = (string)$ini->variable( 'SiteSettings', 'SiteURL' );
        }

        return expPreloadAddress::baseUrl( $url );
    }

    /**
     * How the chosen siteaccess is reached at the base url (expPreloadAddress::prefix()): the prefix, whether
     * site.ini's matching was shown to send that address to the siteaccess, and by which method.
     *
     * Without this every siteaccess sharing a host was warmed at the host root,
     * which is the default site: choosing Bold Agency warmed Fit & Healthy and
     * reported success.
     *
     * @return array hash prefix, reached, how
     */
    public function address()
    {
        if ( $this->address !== null )
            return $this->address;
        if ( $this->options['base_path'] !== null )
            return $this->address = array( 'prefix' => rtrim( (string)$this->options['base_path'], '/' ), 'reached' => true, 'how' => 'given' );
        $base = $this->baseUrl();
        return $this->address = expPreloadAddress::fromINI()->prefix( (string)$this->options['siteaccess'], $base === false ? '' : $base );
    }

    /**
     * The url prefix that selects this siteaccess on its host, '' when it is
     * matched by host alone.
     */
    public function basePath()
    {
        $address = $this->address();
        return $address['prefix'];
    }

    /**
     * The pages to start from: the site root plus any URLTranslationKeyword
     * sections, matching what the command line script warms first.
     */
    public function startUrls( $base )
    {
        $keywords = '';
        if ( !$this->options['start_paths'] )
        {
            $ini = $this->siteaccessIni();
            if ( $ini->hasVariable( 'SiteSettings', 'URLTranslationKeyword' ) )
                $keywords = (string)$ini->variable( 'SiteSettings', 'URLTranslationKeyword' );
        }
        return expPreloadAddress::startUrls( $base, $this->basePath(), $keywords, (array)$this->options['start_paths'] );
    }

    /** One request. Returns status, timing, size and body. */
    private function fetch( $url, $headOnly = false )
    {
        $started = microtime( true );
        $ch = curl_init();
        curl_setopt_array( $ch, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_TIMEOUT        => (int)$this->options['timeout'],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_USERAGENT      => 'Exponential preloader',
            CURLOPT_ENCODING       => '',
            CURLOPT_NOBODY         => (bool)$headOnly,
        ) );
        $body = curl_exec( $ch );
        $status = (int)curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        $type = (string)curl_getinfo( $ch, CURLINFO_CONTENT_TYPE );
        $error = curl_error( $ch );
        curl_close( $ch );

        return array(
            'status'  => $status,
            'ms'      => (int)round( ( microtime( true ) - $started ) * 1000 ),
            'bytes'   => $body === false ? 0 : strlen( $body ),
            'type'    => $type,
            'body'    => $body === false ? '' : $body,
            'error'   => $error,
        );
    }

    /**
     * The siteaccess names this installation serves, for stripping a url prefix.
     */
    private function siteaccessNames()
    {
        if ( $this->siteaccessNames !== null )
            return $this->siteaccessNames;

        $ini = eZINI::instance( 'site.ini' );
        $names = array();
        foreach ( array( 'AvailableSiteAccessList', 'RelatedSiteAccessList' ) as $key )
        {
            if ( !$ini->hasVariable( 'SiteAccessSettings', $key ) )
                continue;
            foreach ( (array)$ini->variable( 'SiteAccessSettings', $key ) as $name )
            {
                $name = trim( (string)$name );
                if ( $name !== '' )
                    $names[$name] = true;
            }
        }

        $this->siteaccessNames = $names;
        return $names;
    }

    /**
     * True when the path addresses a module rather than content.
     *
     * The module segment is not always first. With uri matching in play the
     * same view is reachable as /visual/templateview/... and as
     * /bold_ger/visual/templateview/..., so a pattern anchored at the start of
     * the path misses every siteaccess-prefixed form - which is most of them on
     * a multi-siteaccess installation. One leading segment is removed when it
     * names a siteaccess this installation actually serves; anything else is
     * left alone, so a content path is never truncated.
     */
    private function isModulePath( $path )
    {
        if ( preg_match( self::SKIP_PATTERN, $path ) )
            return true;

        if ( preg_match( '#^/([^/]+)(/.*)$#', $path, $m ) )
        {
            $names = $this->siteaccessNames();
            if ( isset( $names[$m[1]] ) && preg_match( self::SKIP_PATTERN, $m[2] ) )
                return true;
        }

        return false;
    }

    /**
     * Whether a path belongs to the site being warmed.
     *
     * One host can serve several siteaccesses, so same-host is not the same as
     * same-site: without this, warming Bold Agency followed every link into
     * Fit & Healthy and back, and the static cache generator stored the other
     * site's pages under this one's name.
     */
    private function belongsToSite( $path )
    {
        $basePath = $this->basePath();

        if ( $basePath !== '' )
            return $path === $basePath || strpos( $path, $basePath . '/' ) === 0;

        // Matched by host, so this site is everything on it that does not begin
        // with the name of another siteaccess.
        if ( preg_match( '#^/([^/]+)(/|$)#', $path, $m ) )
        {
            $names = $this->siteaccessNames();
            if ( isset( $names[$m[1]] ) )
                return false;
        }

        return true;
    }

    /**
     * The values of one attribute of one element in a page's markup, decoded, and only those that can be an address.
     *
     * The page's own markup only: what is inside <script>, <template>, <style> and comments is removed first. An
     * inline script that builds markup in a string ('<a href="/' + esc(p.path) + '">') holds what looks like an
     * attribute, and was taken for a link: every such page reported "/' + esc(p.path) + '" as a broken link. A value
     * that still carries a quote, a JavaScript or template expression (' +, ${, {{ }}) or whitespace inside it is
     * not an address either and is dropped.
     *
     * @param string $html
     * @param string $element a, img
     * @param string $attribute href, src
     * @return array
     */
    private function attributeValues( $html, $element, $attribute )
    {
        $html = preg_replace( array( '#<script\b.*?</script\s*>#is', '#<template\b.*?</template\s*>#is',
                                     '#<style\b.*?</style\s*>#is', '#<!--.*?-->#s' ), ' ', (string)$html );
        if ( $html === null || !preg_match_all( '#<' . $element . '\b[^>]*?\s' . $attribute
                                                . '\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'>]+))#i', $html, $m, PREG_SET_ORDER ) )
            return array();
        $values = array();
        foreach ( $m as $match )
        {
            $value = isset( $match[3] ) && $match[3] !== '' ? $match[3] : ( isset( $match[2] ) && $match[2] !== '' ? $match[2] : $match[1] );
            $value = trim( html_entity_decode( $value, ENT_QUOTES, 'UTF-8' ) );
            if ( $value === '' || preg_match( '#["\'`<>{}\s]|\$\{|\+\s*\'#', $value ) )
                continue;
            $values[] = $value;
        }
        return $values;
    }

    /** Same-host page links worth queueing, absolute and de-fragmented. */
    private function linksFrom( $html, $pageUrl, $base )
    {
        $links = array();
        $hrefs = $this->attributeValues( $html, 'a', 'href' );
        if ( !$hrefs )
            return $links;

        $skip = array_flip( explode( ',', self::SKIP_EXTENSIONS ) );
        $host = parse_url( $base, PHP_URL_HOST );
        // A root-relative link ('/site/about') starts at the host, not at the base url, which
        // may itself end in the siteaccess prefix.
        $origin = parse_url( $base, PHP_URL_SCHEME ) . '://' . $host
                . ( parse_url( $base, PHP_URL_PORT ) ? ':' . parse_url( $base, PHP_URL_PORT ) : '' );

        foreach ( $hrefs as $href )
        {
            // Cut the fragment off, and keep what is left even when that is
            // nothing. strtok() was used here, and it skips leading delimiters:
            // given '#main' it hands back 'main', not ''. Every in-page anchor
            // on the site therefore became a relative link and was requested -
            // the skip link '#main' as /main, the debug toolbar's '#debug-end'
            // as /debug-end, and from a deeper page as /tags/view/main and the
            // like. None of them exist, so every page on the site contributed
            // a 404 that no editor could act on, because there is no such link
            // to find and nothing to repair.
            $hash = strpos( $href, '#' );
            if ( $hash !== false )
                $href = substr( $href, 0, $hash );

            $href = trim( $href );
            if ( $href === '' )
                continue;
            if ( preg_match( '#^(mailto|tel|javascript|data):#i', $href ) )
                continue;

            if ( strpos( $href, '//' ) === 0 )
                $url = 'https:' . $href;
            elseif ( strpos( $href, '://' ) !== false )
                $url = $href;
            elseif ( $href[0] === '/' )
                $url = $origin . $href;
            else
                $url = rtrim( dirname( $pageUrl . 'x' ), '/' ) . '/' . $href;

            if ( parse_url( $url, PHP_URL_HOST ) !== $host )
                continue;

            $url = $this->normalise( $url );

            $path = (string)parse_url( $url, PHP_URL_PATH );
            if ( $path !== '' && !$this->belongsToSite( $path ) )
                continue;
            $ext = strtolower( (string)pathinfo( $path, PATHINFO_EXTENSION ) );
            if ( $ext !== '' && isset( $skip[$ext] ) )
                continue;
            if ( $this->isModulePath( $path ) )
                continue;

            $links[$url] = true;
        }

        return array_keys( $links );
    }

    /**
     * Warm the site. Phase one is the section pages, phase two crawls outwards
     * from them, both bounded by max_pages so a view cannot run away.
     */
    public function run()
    {
        $base = $this->baseUrl();
        if ( $base === false )
        {
            $this->say( 'error', 'Cannot determine the site url: SiteSettings/SiteURL is not set in site.ini.' );
            $this->say( 'done', 'Nothing was warmed.', array( 'counts' => $this->counts ) );
            return false;
        }

        $started = microtime( true );
        $phases = $this->options['images'] ? 3 : 2;
        $address = $this->address();
        $this->say( 'info', 'Base url: ' . $base . ( $address['prefix'] !== '' ? ', siteaccess prefix ' . $address['prefix'] : '' ) );
        if ( !$address['reached'] )
            $this->say( 'warn', 'The siteaccess matching in site.ini does not send this address to the siteaccess chosen; the pages warmed may be those of another one.' );
        $this->say( 'info', sprintf( 'Limits: %d pages, depth %d, %ds per request.',
                                     $this->options['max_pages'], $this->options['max_depth'],
                                     $this->options['timeout'] ) );

        $start = $this->startUrls( $base );
        $this->say( 'phase', sprintf( 'Phase 1 of %d - section pages (%d)', $phases, count( $start ) ) );

        foreach ( $start as $url )
            $this->visit( $url, $base, 0, true );

        $this->say( 'phase', sprintf( 'Phase 2 of %d - crawling the rest of the site', $phases ) );

        while ( $this->queue )
        {
            if ( $this->counts['fetched'] >= $this->options['max_pages'] )
            {
                $this->say( 'warn', sprintf( 'Reached the limit of %d pages; %d queued urls were left.',
                                             $this->options['max_pages'], count( $this->queue ) ) );
                break;
            }
            $next = array_shift( $this->queue );
            $this->visit( $next['url'], $base, $next['depth'], false );
        }

        if ( $this->options['images'] )
        {
            $this->say( 'phase', sprintf( 'Phase 3 of 3 - images shown on the warmed pages (%d)',
                                          min( count( $this->images ), (int)$this->options['max_images'] ) ) );
            $this->checkImages();
        }

        $this->report();

        $elapsed = round( microtime( true ) - $started, 1 );
        $this->say( 'done', sprintf(
            '%d warmed, %d skipped, %d broken, %d denied, %s in %ss.',
            $this->counts['fetched'], $this->counts['skipped'], $this->counts['broken'],
            $this->counts['denied'], $this->formatBytes( $this->counts['bytes'] ), $elapsed )
            . ( $this->options['images'] ? sprintf( ' %d images checked, %d missing.', $this->counts['images'], $this->counts['images_broken'] ) : '' ),
            array( 'counts' => $this->counts, 'seconds' => $elapsed ) );

        return $this->counts['broken'] === 0;
    }

    /**
     * The images a page shows: same host <img src> addresses, absolute, once each.
     *
     * @return array
     */
    private function imagesFrom( $html, $pageUrl, $base )
    {
        $found = array();
        $sources = $this->attributeValues( $html, 'img', 'src' );
        if ( !$sources )
            return $found;
        $host = parse_url( $base, PHP_URL_HOST );
        $origin = parse_url( $base, PHP_URL_SCHEME ) . '://' . $host
                . ( parse_url( $base, PHP_URL_PORT ) ? ':' . parse_url( $base, PHP_URL_PORT ) : '' );
        foreach ( $sources as $src )
        {
            if ( $src === '' || preg_match( '#^(data|javascript):#i', $src ) )
                continue;
            if ( strpos( $src, '//' ) === 0 )
                $url = 'https:' . $src;
            elseif ( strpos( $src, '://' ) !== false )
                $url = $src;
            elseif ( $src[0] === '/' )
                $url = $origin . $src;
            else
                $url = rtrim( dirname( $pageUrl . 'x' ), '/' ) . '/' . $src;
            if ( parse_url( $url, PHP_URL_HOST ) !== $host )
                continue;
            // nothing of Velocity's own panel, not even its pictures
            if ( preg_match( '#^(/[^/]+)?/Q(/|$)#', (string)parse_url( $url, PHP_URL_PATH ), $q )
                 && ( $q[1] === '' || isset( $this->siteaccessNames()[substr( $q[1], 1 )] ) ) )
                continue;
            $hash = strpos( $url, '#' );
            if ( $hash !== false )
                $url = substr( $url, 0, $hash );
            $found[$url] = true;
        }
        return array_keys( $found );
    }

    /**
     * Requests each image once (HEAD), and keeps the ones that do not come back with the pages that show them.
     */
    private function checkImages()
    {
        $left = (int)$this->options['max_images'];
        foreach ( $this->images as $url => $pages )
        {
            if ( $left-- <= 0 )
            {
                $this->say( 'warn', sprintf( 'Reached the limit of %d images; %d were not checked.',
                                             (int)$this->options['max_images'], count( $this->images ) - (int)$this->options['max_images'] ) );
                break;
            }
            $result = $this->fetch( $url, true );
            $path = (string)parse_url( $url, PHP_URL_PATH );
            if ( $result['status'] >= 200 && $result['status'] < 400 )
            {
                ++$this->counts['images'];
                $this->say( 'image', sprintf( '%-52s %3d  %5dms', ( strlen( $path ) > 52 ? "\xe2\x80\xa6" . substr( $path, -51 ) : $path ), $result['status'], $result['ms'] ),
                            array( 'url' => $url, 'status' => $result['status'], 'ms' => $result['ms'] ) );
                continue;
            }
            ++$this->counts['images_broken'];
            $this->problems[] = array(
                'url'       => $url,
                'path'      => $path,
                'status'    => (int)$result['status'],
                'reason'    => $result['status'] === 0 ? 'Images without a response' : sprintf( 'Missing images (%d)', $result['status'] ),
                'referrers' => array_keys( $pages ),
                'more'      => 0,
            );
            $this->say( 'error', sprintf( '%s  %s  shown on %s', $path,
                                          $result['status'] ? (string)$result['status'] : ( $result['error'] !== '' ? $result['error'] : 'no response' ),
                                          $this->shorten( (string)parse_url( (string)key( $pages ), PHP_URL_PATH ), 40 ) ),
                        array( 'url' => $url, 'status' => (int)$result['status'] ) );
        }
    }

    private function visit( $url, $base, $depth, $isSection )
    {
        if ( isset( $this->seen[$url] ) )
            return;
        $this->seen[$url] = true;

        $result = $this->fetch( $url );
        $path = (string)parse_url( $url, PHP_URL_PATH );
        if ( $path === '' ) $path = '/';

        if ( $result['status'] === 0 )
        {
            ++$this->counts['broken'];
            $this->noteProblem( $url, 0, 'No response' );
            $this->say( 'error', sprintf( '%s  %s%s', $path,
                                          $result['error'] !== '' ? $result['error'] : 'no response',
                                          $this->referrerNote( $url ) ) );
            return;
        }

        // 401 and 403 are expected on a site with protected areas; they are not
        // failures of the preloader and are counted apart from broken links.
        if ( $result['status'] === 401 || $result['status'] === 403 )
        {
            ++$this->counts['denied'];
            $this->say( 'warn', sprintf( '%s  %d access denied%s', $path, $result['status'],
                                         $this->referrerNote( $url ) ) );
            return;
        }

        if ( $result['status'] >= 400 )
        {
            ++$this->counts['broken'];
            $this->noteProblem( $url, $result['status'],
                                $result['status'] === 404 ? 'Missing pages (404)'
                                                          : sprintf( 'Server errors (%d)', $result['status'] ) );
            $this->say( 'error', sprintf( '%s  %d%s', $path, $result['status'],
                                          $this->referrerNote( $url ) ) );
            return;
        }

        if ( stripos( $result['type'], 'text/html' ) === false )
        {
            ++$this->counts['skipped'];
            return;
        }

        ++$this->counts['fetched'];
        $this->counts['bytes'] += $result['bytes'];

        $this->say( $isSection ? 'phase-item' : 'ok', sprintf( '%-52s %3d  %5dms  %s',
            $this->shorten( $path, 52 ), $result['status'], $result['ms'],
            $this->formatBytes( $result['bytes'] ) ),
            array( 'url' => $url, 'status' => $result['status'], 'ms' => $result['ms'] ) );

        if ( $this->storeCallback )
            call_user_func( $this->storeCallback, $url, $path, $result['body'] );

        if ( $this->options['images'] )
        {
            foreach ( $this->imagesFrom( $result['body'], $url, $base ) as $image )
            {
                if ( !isset( $this->images[$image] ) )
                    $this->images[$image] = array();
                if ( count( $this->images[$image] ) < self::MAX_REFERRERS )
                    $this->images[$image][$url] = true;
            }
        }

        if ( $depth >= $this->options['max_depth'] )
            return;

        foreach ( $this->linksFrom( $result['body'], $url, $base ) as $link )
        {
            // Noted before the seen test, not after: a link that is already
            // queued or already fetched is still a link from this page, and if
            // it turns out to be broken this page is one of the ones that has
            // to be edited.
            $this->noteReferrer( $link, $url );

            if ( isset( $this->seen[$link] ) )
                continue;
            $this->queue[] = array( 'url' => $link, 'depth' => $depth + 1 );
        }
    }

    /** Remember that $from links to $target. */
    private function noteReferrer( $target, $from )
    {
        if ( $target === $from )
            return;

        if ( !isset( $this->referrers[$target] ) )
            $this->referrers[$target] = array();

        if ( isset( $this->referrers[$target][$from] ) )
            return;

        if ( count( $this->referrers[$target] ) >= self::MAX_REFERRERS )
        {
            $this->referrersOver[$target] = isset( $this->referrersOver[$target] )
                                          ? $this->referrersOver[$target] + 1 : 1;
            return;
        }

        $this->referrers[$target][$from] = true;
    }

    /** The pages that link to $url, in the order they were crawled. */
    public function referrersOf( $url )
    {
        return isset( $this->referrers[$url] ) ? array_keys( $this->referrers[$url] ) : array();
    }

    /**
     * Records a url that did not come back as a page, with the pages that link
     * to it, so the report can say where the editing has to happen.
     */
    private function noteProblem( $url, $status, $reason )
    {
        $from = $this->referrersOf( $url );

        $this->problems[] = array(
            'url'       => $url,
            'path'      => (string)parse_url( $url, PHP_URL_PATH ),
            'status'    => (int)$status,
            'reason'    => $reason,
            'referrers' => $from,
            'more'      => isset( $this->referrersOver[$url] ) ? (int)$this->referrersOver[$url] : 0,
        );
    }

    /** ' - from /fitness' and the like, to put on the line as it is printed. */
    private function referrerNote( $url )
    {
        $from = $this->referrersOf( $url );
        if ( !$from )
            return '  (a starting page)';

        $first = (string)parse_url( $from[0], PHP_URL_PATH );
        if ( $first === '' ) $first = '/';

        $others = count( $from ) - 1 + ( isset( $this->referrersOver[$url] ) ? $this->referrersOver[$url] : 0 );

        return '  linked from ' . $this->shorten( $first, 40 )
             . ( $others > 0 ? sprintf( ' and %d other page%s', $others, $others === 1 ? '' : 's' ) : '' );
    }

    /**
     * The closing report: every url that did not come back as a page, and for
     * each of them the full address of every page that links to it.
     *
     * The running output is a log, and on a site of any size the failures
     * scroll past between hundreds of successes. What an editor needs is the
     * other way round - the broken target once, and then the pages to open and
     * fix - and they need the whole address, because that is what goes in the
     * address bar. The same list is handed to the front end as data, so a view
     * can make the addresses clickable.
     */
    private function report()
    {
        if ( !$this->problems )
        {
            $this->say( 'report', 'No broken links were found.', array( 'broken' => array() ) );
            return;
        }

        // One entry per target: the same missing page reached from two places
        // is one thing to fix, not two.
        $pages = array();
        foreach ( $this->problems as $problem )
            $pages[$problem['url']] = $problem;

        $groups = array();
        foreach ( $pages as $problem )
            $groups[$problem['reason']][] = $problem;
        ksort( $groups );

        $linking = array();
        foreach ( $pages as $problem )
            foreach ( $problem['referrers'] as $from )
                $linking[$from] = true;

        $lines = array( '' );
        $lines[] = sprintf( '%d broken link%s on %d page%s of this site.',
                            count( $pages ), count( $pages ) === 1 ? '' : 's',
                            count( $linking ), count( $linking ) === 1 ? '' : 's' );

        foreach ( $groups as $reason => $entries )
        {
            $lines[] = '';
            $lines[] = sprintf( '%s (%d)', $reason, count( $entries ) );

            foreach ( $entries as $problem )
            {
                $lines[] = '';
                $lines[] = '  ' . $problem['url'];

                if ( !$problem['referrers'] )
                {
                    $lines[] = '      a starting page; nothing on the site links to it';
                    continue;
                }

                $lines[] = sprintf( '      linked from %d page%s:',
                                    count( $problem['referrers'] ) + $problem['more'],
                                    ( count( $problem['referrers'] ) + $problem['more'] ) === 1 ? '' : 's' );

                foreach ( $problem['referrers'] as $from )
                    $lines[] = '        ' . $from;

                if ( $problem['more'] > 0 )
                    $lines[] = sprintf( '        ... and %d more', $problem['more'] );
            }
        }

        $lines[] = '';
        $lines[] = 'Open each page listed under a broken link, correct the link, then run this again.';

        $this->say( 'report', implode( "\n", $lines ), array( 'broken' => array_values( $pages ) ) );
    }

    /**
     * Collapse the forms of a url that address the same page.
     *
     * A site that still emits /index.php/foo alongside /foo would otherwise be
     * crawled twice over, and on a bounded run the duplicates crowd out pages
     * that have not been warmed at all. The trailing slash is normalised for
     * the same reason.
     */
    private function normalise( $url )
    {
        // /bold and /bold/ are one page, and /index.php/foo is /foo: expPreloadAddress::normalise(), which the
        // starting pages use too
        return expPreloadAddress::normalise( $url );
    }

    private function shorten( $text, $width )
    {
        if ( strlen( $text ) <= $width )
            return $text;
        return substr( $text, 0, $width - 1 ) . "\xe2\x80\xa6";
    }

    private function formatBytes( $bytes )
    {
        if ( $bytes < 1024 )
            return $bytes . 'B';
        if ( $bytes < 1048576 )
            return round( $bytes / 1024, 1 ) . 'K';
        return round( $bytes / 1048576, 1 ) . 'M';
    }

    public function counts()
    {
        return $this->counts;
    }

    /**
     * Every url that did not come back as a page, with the pages linking to it.
     *
     * @return array of ( url, path, status, reason, referrers, more )
     */
    public function problems()
    {
        $pages = array();
        foreach ( $this->problems as $problem )
            $pages[$problem['url']] = $problem;

        return array_values( $pages );
    }
}
}


?>
