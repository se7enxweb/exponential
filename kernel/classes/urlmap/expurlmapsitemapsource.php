<?php
/**
 * File containing the expUrlMapSitemapSource class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The old addresses of a site from its sitemap, as URL map rows to fill in (old_url set, new_url empty):
 *
 *   $source = new expUrlMapSitemapSource( array( 'limit' => 5000 ) );
 *   $rows = $source->rows( 'https://www.old-site.com/sitemap.xml' );   // or a file
 *   expUrlMapFormats::writeFile( 'var/tmp/old-site.csv', $rows );
 *
 * Reads a sitemap (<urlset>), a sitemap index (<sitemapindex>: each sitemap it names is read too, up to
 * max_sitemaps, max_depth levels deep), either gzip-compressed, or a crawl list (one address per line; empty lines
 * and lines starting with # are skipped). XML is read without the network and without entities; a sitemap that is
 * not well-formed is read for its <loc> elements, with a warning.
 *
 * Options: limit (the most rows, 0 all), hosts (keep only addresses of these hosts; default the host of the source
 * address, or of the first address of a file), any_host (keep every host), dedupe (default true: an address once;
 * the trailing slash, the fragment and the case of the host do not count), full_urls (default false: old_url is the
 * path and query, as an import of the old site's addresses on this site needs them; true keeps scheme and host),
 * max_sitemaps (default 200), max_depth (3), max_bytes (the largest sitemap, unpacked, default 50 MB).
 *
 * The network is an expUrlMapHttp (a fake one in tests); files are read from disk.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUrlMapSitemapSource
{
    /** @var expUrlMapHttp */
    protected $http;

    /** @var array */
    protected $options;

    /** @var string[] */
    protected $warnings = array();

    /** @var array sitemaps read, addresses found, kept, duplicates, other hosts, skipped (not http) */
    protected $counts = array();

    /** @var array normalized address => true */
    protected $seen = array();

    /** @var string[] lower-case hosts to keep; empty before the first address when no host is known */
    protected $hosts = array();

    /**
     * @param array $options see the class
     * @param expUrlMapHttp|null $http
     */
    public function __construct( array $options = array(), $http = null )
    {
        $this->options = $options + array( 'limit' => 0, 'hosts' => array(), 'any_host' => false, 'dedupe' => true, 'full_urls' => false,
                                           'max_sitemaps' => 200, 'max_depth' => 3, 'max_bytes' => 52428800 );
        $this->http = $http instanceof expUrlMapHttp ? $http : new expUrlMapHttp( array( 'max_bytes' => (int)$this->options['max_bytes'] ) );
    }

    /** @return string[] what was not read, or read only in part */
    public function warnings()
    {
        return $this->warnings;
    }

    /** @return array sitemaps, found, rows, duplicates, other_hosts, skipped, limited */
    public function counts()
    {
        return $this->counts;
    }

    /** @return string[] the hosts kept */
    public function hosts()
    {
        return $this->hosts;
    }

    /**
     * The rows of a sitemap, a sitemap index or a crawl list, read from an address or a file.
     *
     * @param string $source an http(s) address or a file
     * @return expUrlMapRow[]
     * @throws expUrlMapFormatException when the source itself cannot be read
     */
    public function rows( $source )
    {
        $this->warnings = array();
        $this->seen = array();
        $this->counts = array( 'sitemaps' => 0, 'found' => 0, 'rows' => 0, 'duplicates' => 0, 'other_hosts' => 0, 'skipped' => 0, 'limited' => false );
        $this->hosts = array_values( array_filter( array_map( function ( $h ) { return strtolower( trim( (string)$h ) ); }, (array)$this->options['hosts'] ), 'strlen' ) );
        $source = trim( (string)$source );
        $isUrl = (bool)preg_match( '#^https?://#i', $source );
        if ( $isUrl && !$this->hosts && empty( $this->options['any_host'] ) )
            $this->hosts[] = strtolower( (string)parse_url( $source, PHP_URL_HOST ) );
        $data = $isUrl ? $this->fetch( $source ) : $this->readFile( $source );
        $rows = array();
        $this->collect( $data, $source, 0, $rows, $isUrl );
        $this->counts['rows'] = count( $rows );
        return $rows;
    }

    /**
     * The addresses and child sitemaps of one document. Pure.
     *
     * @param string $data the document (gzip-compressed or not)
     * @param int $maxBytes the largest unpacked size
     * @return array type (urlset, sitemapindex, list), locs (string[]), warnings (string[])
     */
    public static function parse( $data, $maxBytes = 52428800 )
    {
        $warnings = array();
        $data = (string)$data;
        if ( strncmp( $data, "\x1f\x8b", 2 ) === 0 )
        {
            $unpacked = function_exists( 'gzdecode' ) ? @gzdecode( $data, max( 1, (int)$maxBytes ) ) : false;
            if ( $unpacked === false )
                return array( 'type' => 'list', 'locs' => array(), 'warnings' => array( 'the gzip data cannot be unpacked (damaged, or larger than ' . (int)$maxBytes . ' bytes unpacked)' ) );
            $data = $unpacked;
        }
        if ( strncmp( $data, "\xEF\xBB\xBF", 3 ) === 0 )
            $data = substr( $data, 3 );
        $head = ltrim( substr( $data, 0, 2048 ) );
        if ( $head === '' || $head[0] !== '<' )
            return array( 'type' => 'list', 'locs' => self::listLines( $data ), 'warnings' => $warnings );

        $type = preg_match( '/<(?:[a-z0-9_\-]+:)?sitemapindex[\s>]/i', $head ) ? 'sitemapindex' : 'urlset';
        $locs = null;
        if ( class_exists( 'DOMDocument' ) )
        {
            $previous = libxml_use_internal_errors( true );
            $doc = new DOMDocument();
            // no network, no entity substitution, no DTD loading
            $loaded = strpos( $data, '<!DOCTYPE' ) === false && $doc->loadXML( $data, LIBXML_NONET | LIBXML_COMPACT | ( defined( 'LIBXML_BIGLINES' ) ? LIBXML_BIGLINES : 0 ) );
            libxml_clear_errors();
            libxml_use_internal_errors( $previous );
            if ( $loaded && $doc->documentElement )
            {
                $root = strtolower( $doc->documentElement->localName );
                $type = $root === 'sitemapindex' ? 'sitemapindex' : 'urlset';
                $container = $type === 'sitemapindex' ? 'sitemap' : 'url';
                $locs = array();
                foreach ( $doc->documentElement->childNodes as $child )
                {
                    if ( !$child instanceof DOMElement || strtolower( $child->localName ) !== $container )
                        continue;
                    foreach ( $child->childNodes as $grand )
                    {
                        if ( $grand instanceof DOMElement && strtolower( $grand->localName ) === 'loc' )
                        {
                            $locs[] = trim( $grand->textContent );
                            break;
                        }
                    }
                }
            }
        }
        if ( $locs === null )
        {
            $warnings[] = 'the sitemap is not well-formed XML: its <loc> elements were read as text';
            preg_match_all( '#<(?:[a-z0-9_\-]+:)?loc>\s*(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?\s*</(?:[a-z0-9_\-]+:)?loc>#is', $data, $m );
            $locs = array_map( function ( $loc ) { return html_entity_decode( trim( $loc ), ENT_QUOTES | ENT_XML1, 'UTF-8' ); }, $m[1] );
        }
        return array( 'type' => $type, 'locs' => array_values( array_filter( $locs, 'strlen' ) ), 'warnings' => $warnings );
    }

    /**
     * The addresses of a crawl list: one per line, empty lines and # comments skipped, a tab or space ends the
     * address (crawler exports with columns). Pure.
     *
     * @param string $data
     * @return string[]
     */
    public static function listLines( $data )
    {
        $out = array();
        foreach ( preg_split( '/\r\n|\r|\n/', (string)$data ) as $line )
        {
            $line = trim( $line );
            if ( $line === '' || $line[0] === '#' )
                continue;
            $line = preg_split( '/[\s,;]+/', $line )[0];
            $line = trim( $line, "\"'" );
            if ( $line !== '' )
                $out[] = $line;
        }
        return $out;
    }

    /**
     * The key two addresses are the same by: scheme and host in lower case, no fragment, no trailing slash. Pure.
     *
     * @param string $url
     * @return string
     */
    public static function dedupeKey( $url )
    {
        $parts = expUrlMapRow::parseUrl( $url );
        return $parts['host'] . expUrlMapRow::buildUrl( array( 'scheme' => '', 'host' => '', 'path' => $parts['path'], 'query' => $parts['query'] ) );
    }

    /**
     * @param string $data
     * @param string $name where it came from (for warnings and relative addresses)
     * @param int $depth
     * @param expUrlMapRow[] $rows
     * @param bool $remote whether child sitemaps are fetched (a file's index names addresses too)
     */
    protected function collect( $data, $name, $depth, array &$rows, $remote )
    {
        $this->counts['sitemaps']++;
        $parsed = self::parse( $data, (int)$this->options['max_bytes'] );
        foreach ( $parsed['warnings'] as $w )
            $this->warnings[] = $name . ': ' . $w;
        if ( $parsed['type'] === 'sitemapindex' )
        {
            foreach ( $parsed['locs'] as $child )
            {
                if ( $this->full() )
                    return;
                if ( $depth + 1 > (int)$this->options['max_depth'] )
                {
                    $this->warnings[] = $child . ': not read, more than ' . (int)$this->options['max_depth'] . ' sitemap index levels';
                    continue;
                }
                if ( $this->counts['sitemaps'] >= (int)$this->options['max_sitemaps'] )
                {
                    $this->warnings[] = 'more than ' . (int)$this->options['max_sitemaps'] . ' sitemaps: the rest is not read';
                    return;
                }
                $child = expUrlMapHttp::absolute( $remote ? $name : '', $child );
                if ( !preg_match( '#^https?://#i', $child ) )
                {
                    $this->warnings[] = $child . ': a sitemap of an index is read only from an http or https address';
                    continue;
                }
                try
                {
                    $this->collect( $this->fetch( $child ), $child, $depth + 1, $rows, true );
                }
                catch ( expUrlMapFormatException $e )
                {
                    $this->warnings[] = $e->getMessage();
                }
            }
            return;
        }
        foreach ( $parsed['locs'] as $loc )
        {
            if ( $this->full() )
                return;
            $this->add( $loc, $remote ? $name : '', $rows );
        }
    }

    /** @return bool whether the limit is reached */
    protected function full()
    {
        $limit = (int)$this->options['limit'];
        if ( $limit > 0 && $this->counts['rows'] >= $limit )
        {
            $this->counts['limited'] = true;
            return true;
        }
        return false;
    }

    /**
     * @param string $loc
     * @param string $base
     * @param expUrlMapRow[] $rows
     */
    protected function add( $loc, $base, array &$rows )
    {
        $this->counts['found']++;
        $url = $base !== '' || preg_match( '#^https?://#i', $loc ) ? expUrlMapHttp::absolute( $base, $loc ) : $loc;
        if ( !preg_match( '#^(https?:)?//|^/#i', $url ) )
        {
            $this->counts['skipped']++;
            return;
        }
        $parts = expUrlMapRow::parseUrl( $url );
        if ( $parts['problems'] )
        {
            $this->counts['skipped']++;
            return;
        }
        if ( empty( $this->options['any_host'] ) && $parts['host'] !== '' )
        {
            if ( !$this->hosts )
                $this->hosts[] = $parts['host'];
            if ( !in_array( $parts['host'], $this->hosts, true ) )
            {
                $this->counts['other_hosts']++;
                return;
            }
        }
        if ( !empty( $this->options['dedupe'] ) )
        {
            $key = empty( $this->options['full_urls'] ) ? self::dedupeKey( expUrlMapRow::buildUrl( array( 'scheme' => '', 'host' => '', 'path' => $parts['path'], 'query' => $parts['query'] ) ) )
                                                        : self::dedupeKey( $url );
            if ( isset( $this->seen[$key] ) )
            {
                $this->counts['duplicates']++;
                return;
            }
            $this->seen[$key] = true;
        }
        $old = empty( $this->options['full_urls'] ) ? expUrlMapRow::buildUrl( array( 'scheme' => '', 'host' => '', 'path' => $parts['path'], 'query' => $parts['query'] ) )
                                                    : $url;
        $rows[] = new expUrlMapRow( $old, '' );
        $this->counts['rows']++;
    }

    /**
     * @param string $url
     * @return string
     * @throws expUrlMapFormatException
     */
    protected function fetch( $url )
    {
        $answer = $this->http->get( $url );
        if ( $answer['error'] !== null && $answer['status'] === 0 )
            throw new expUrlMapFormatException( $url . ': ' . $answer['error'] );
        if ( $answer['status'] >= 300 && $answer['status'] < 400 && $answer['location'] )
        {
            // a sitemap that moved: follow up to five redirects
            for ( $hops = 0; $hops < 5 && $answer['status'] >= 300 && $answer['status'] < 400 && $answer['location']; $hops++ )
                $answer = $this->http->get( $answer['location'] );
        }
        if ( $answer['status'] < 200 || $answer['status'] >= 300 )
            throw new expUrlMapFormatException( $url . ': HTTP ' . $answer['status'] . ( $answer['error'] !== null ? ' (' . $answer['error'] . ')' : '' ) );
        if ( $answer['truncated'] )
            $this->warnings[] = $url . ': larger than ' . (int)$this->options['max_bytes'] . ' bytes, read in part';
        return $answer['body'];
    }

    /**
     * @param string $path
     * @return string
     * @throws expUrlMapFormatException
     */
    protected function readFile( $path )
    {
        if ( !is_file( $path ) || !is_readable( $path ) )
            throw new expUrlMapFormatException( "The file $path cannot be read" );
        $size = (int)filesize( $path );
        if ( $size > (int)$this->options['max_bytes'] )
            throw new expUrlMapFormatException( "The file $path is larger than " . (int)$this->options['max_bytes'] . ' bytes' );
        return (string)file_get_contents( $path );
    }
}
