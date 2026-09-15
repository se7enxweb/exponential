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
        '|/(stats|calendar|groupeventcalendar)(/|$)#';

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

    /** target url => array( linking page url => true ), capped. */
    private $referrers = array();

    /** target url => how many further linking pages were seen past the cap. */
    private $referrersOver = array();

    /** Everything that did not come back as a page, in the order found. */
    private $problems = array();

    private $counts = array(
        'fetched' => 0, 'skipped' => 0, 'broken' => 0, 'denied' => 0, 'bytes' => 0 );

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
            $siteaccess = trim( (string)$this->options['siteaccess'] );
            $dir = 'settings/siteaccess/' . $siteaccess;
            if ( file_exists( $dir . '/site.ini.append.php' ) )
            {
                $saIni = eZINI::instance( 'site.ini.append.php', $dir, null, false, null, true );
                if ( $saIni->hasVariable( 'SiteSettings', 'SiteURL' ) )
                    $url = (string)$saIni->variable( 'SiteSettings', 'SiteURL' );
            }
        }

        if ( $url === '' )
        {
            $ini = eZINI::instance( 'site.ini' );
            if ( !$ini->hasVariable( 'SiteSettings', 'SiteURL' ) )
                return false;
            $url = (string)$ini->variable( 'SiteSettings', 'SiteURL' );
        }

        $url = rtrim( trim( $url ), '/' );
        if ( $url === '' )
            return false;
        if ( strpos( $url, '://' ) === false )
            $url = 'https://' . $url;
        return $url;
    }

    /**
     * The url prefix that selects this siteaccess on its host, '' when it is
     * matched by host alone.
     *
     * Without this every siteaccess sharing a host was warmed at the host root,
     * which is the default site: choosing Bold Agency warmed Fit & Healthy and
     * reported success. MatchOrder decides which it is, and a siteaccess named
     * in the host map is reached at the root of that host.
     */
    public function basePath()
    {
        if ( $this->options['base_path'] !== null )
            return rtrim( (string)$this->options['base_path'], '/' );

        $siteaccess = trim( (string)$this->options['siteaccess'] );
        if ( $siteaccess === '' )
            return '';

        $ini = eZINI::instance( 'site.ini' );
        foreach ( (array)$ini->variableArray( 'SiteAccessSettings', 'MatchOrder' ) as $matchOrder )
        {
            if ( $matchOrder === 'host' && $ini->hasVariable( 'SiteAccessSettings', 'HostMatchMapItems' ) )
            {
                foreach ( (array)$ini->variable( 'SiteAccessSettings', 'HostMatchMapItems' ) as $item )
                {
                    $parts = explode( ';', $item );
                    if ( isset( $parts[1] ) && $parts[1] === $siteaccess )
                        return '';
                }
            }
            else if ( $matchOrder === 'host_uri' && $ini->hasVariable( 'SiteAccessSettings', 'HostUriMatchMapItems' ) )
            {
                foreach ( (array)$ini->variable( 'SiteAccessSettings', 'HostUriMatchMapItems' ) as $item )
                {
                    $parts = explode( ';', $item );
                    if ( isset( $parts[2] ) && $parts[2] === $siteaccess )
                        return $parts[1] !== '' ? '/' . trim( $parts[1], '/' ) : '';
                }
            }
        }

        // Matched on the first url segment, which is the siteaccess name.
        return '/' . $siteaccess;
    }

    /**
     * The pages to start from: the site root plus any URLTranslationKeyword
     * sections, matching what the command line script warms first.
     */
    public function startUrls( $base )
    {
        $base = $base . $this->basePath();
        $urls = array( $base . '/' );

        $ini = eZINI::instance( 'site.ini' );
        $siteaccess = trim( (string)$this->options['siteaccess'] );
        if ( $siteaccess !== '' && file_exists( 'settings/siteaccess/' . $siteaccess . '/site.ini.append.php' ) )
            $ini = eZINI::instance( 'site.ini.append.php', 'settings/siteaccess/' . $siteaccess, null, false, null, true );

        if ( $ini->hasVariable( 'SiteSettings', 'URLTranslationKeyword' ) )
        {
            $keywords = (string)$ini->variable( 'SiteSettings', 'URLTranslationKeyword' );
            foreach ( explode( ';', $keywords ) as $keyword )
            {
                $keyword = trim( $keyword, "/ \t\n\r" );
                if ( $keyword !== '' )
                    $urls[] = $base . '/' . $keyword . '/';
            }
        }

        foreach ( $urls as $key => $url )
            $urls[$key] = $this->normalise( $url );

        return array_values( array_unique( $urls ) );
    }

    /** One request. Returns status, timing, size and body. */
    private function fetch( $url )
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

    /** Same-host page links worth queueing, absolute and de-fragmented. */
    private function linksFrom( $html, $pageUrl, $base )
    {
        $links = array();
        if ( $html === '' || !preg_match_all( '#<a\b[^>]*href="([^"]+)"#i', $html, $m ) )
            return $links;

        $skip = array_flip( explode( ',', self::SKIP_EXTENSIONS ) );
        $host = parse_url( $base, PHP_URL_HOST );

        foreach ( $m[1] as $href )
        {
            $href = html_entity_decode( $href, ENT_QUOTES, 'UTF-8' );

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
                $url = $base . $href;
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
        $this->say( 'info', 'Base url: ' . $base );
        $this->say( 'info', sprintf( 'Limits: %d pages, depth %d, %ds per request.',
                                     $this->options['max_pages'], $this->options['max_depth'],
                                     $this->options['timeout'] ) );

        $start = $this->startUrls( $base );
        $this->say( 'phase', sprintf( 'Phase 1 of 2 - section pages (%d)', count( $start ) ) );

        foreach ( $start as $url )
            $this->visit( $url, $base, 0, true );

        $this->say( 'phase', 'Phase 2 of 2 - crawling the rest of the site' );

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

        $this->report();

        $elapsed = round( microtime( true ) - $started, 1 );
        $this->say( 'done', sprintf(
            '%d warmed, %d skipped, %d broken, %d denied, %s in %ss.',
            $this->counts['fetched'], $this->counts['skipped'], $this->counts['broken'],
            $this->counts['denied'], $this->formatBytes( $this->counts['bytes'] ), $elapsed ),
            array( 'counts' => $this->counts, 'seconds' => $elapsed ) );

        return $this->counts['broken'] === 0;
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
        $url = preg_replace( '#/index\\.php(?=/|$)#', '', $url, 1 );

        $parts = parse_url( $url );
        $path = isset( $parts['path'] ) ? $parts['path'] : '';

        if ( $path === '' )
            return rtrim( $url, '/' ) . '/';

        // /bold and /bold/ are one page, and were being fetched and stored as
        // two. The host root keeps its slash because there is nothing else of
        // it to keep.
        if ( $path !== '/' && substr( $path, -1 ) === '/' )
            $url = substr( $url, 0, strrpos( $url, '/' ) );

        return $url;
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

?>
