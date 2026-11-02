<?php
/**
 * File containing the expStaticSearchIndex class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The search of a static copy (target setting Search=enabled): a JSON index per language, built from the pages, and a
 * small search page with a script of a few kilobytes and no library, which works without a server and offline.
 * Pure: pages in, files out; expStaticBuild writes them.
 *
 *   search.html                    the page; the site's search forms point here (expStaticDegrader); it reads
 *                                  ?SearchText= (the kernel's field) or ?q=
 *   static-search/search.js        the script: loads the index of the page's language, ranks by the words found in
 *                                  the title, the terms and the teaser
 *   static-search/index-<lang>.json  one per language (<html lang>, or "und"): schema exp-static-search/1,
 *                                  pages: [ { t: title, u: address relative to the build's root, d: teaser,
 *                                  k: terms, s: the siteaccess in a copy of several } ]; the page then offers
 *                                  to search one site or all of them (?site=<siteaccess>)
 *
 * Each index is kept below a size (default 2 MB, [StaticPublishSettings] SearchMaxBytes): over it, the terms of every
 * page are shortened first, then the teasers, then the last pages are left out (reported).
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticSearchIndex
{
    const SCHEMA = 'exp-static-search/1';
    const PAGE = 'search.html';
    const DIRECTORY = 'static-search';
    const SCRIPT = 'static-search/search.js';
    const MAX_BYTES = 2000000;

    /** How many terms a page keeps, at most */
    const TERMS = 40;

    /** How long a teaser is, at most */
    const TEASER = 200;

    /** @var array lang => array of entries */
    private $entries = array();

    /** @var int */
    private $maxBytes;

    /**
     * @param int $maxBytes the most bytes of one index
     */
    public function __construct( $maxBytes = self::MAX_BYTES )
    {
        $this->maxBytes = max( 1000, (int)$maxBytes );
    }

    /**
     * Adds a page.
     *
     * @param string $html the page as written
     * @param string $url its address relative to the build's root ('' for the front page, about-us/)
     * @param string $site the siteaccess of the page in a copy of several ('' for a copy of one)
     * @return bool false for a page that has nothing to find (no title and no text, or noindex)
     */
    public function add( $html, $url, $site = '' )
    {
        $page = self::extract( $html );
        if ( $page === null )
            return false;
        $entry = array( 't' => $page['title'], 'u' => (string)$url, 'd' => $page['teaser'], 'k' => $page['terms'] );
        if ( (string)$site !== '' )
            $entry['s'] = (string)$site;
        $this->entries[$page['lang']][] = $entry;
        return true;
    }

    /**
     * What a page offers the search. Pure.
     *
     * @param string $html
     * @return array|null lang, title, teaser, terms (string, space-separated); null for a page with robots noindex or
     *         nothing to find
     */
    public static function extract( $html )
    {
        $html = (string)$html;
        if ( preg_match( '#<meta\b(?=[^>]*\bname=(["\'])robots\1)(?=[^>]*\bcontent=(["\'])[^"\']*noindex)[^>]*>#i', $html ) )
            return null;
        $lang = preg_match( '#<html\b[^>]*\blang=(["\'])([A-Za-z]{2,3}(?:-[A-Za-z0-9]{2,8})*)\1#i', $html, $m ) ? strtolower( $m[2] ) : 'und';
        $title = preg_match( '#<title\b[^>]*>(.*?)</title\s*>#is', $html, $m ) ? self::text( $m[1] ) : '';
        $teaser = '';
        if ( preg_match( '#<meta\b(?=[^>]*\bname=(["\'])description\1)[^>]*\bcontent=(["\'])(.*?)\2#is', $html, $m ) )
            $teaser = self::text( $m[3] );
        // the main content when the page marks it, else the body
        $body = preg_match( '#<main\b[^>]*>(.*?)</main\s*>#is', $html, $m ) ? $m[1] : ( preg_match( '#<body\b[^>]*>(.*)</body\s*>#is', $html, $m ) ? $m[1] : $html );
        $body = expStaticPostProcessor::checked( preg_replace( '#<(script|style|noscript|template|svg|nav|form)\b[^>]*>.*?</\1\s*>#is', ' ', $body ), 'search index' );
        $text = self::text( expStaticPostProcessor::checked( preg_replace( '#<[^>]+>#', ' ', (string)$body ), 'search index' ) );
        if ( $title === '' && $text === '' )
            return null;
        if ( $teaser === '' )
            $teaser = self::cut( $text, self::TEASER );
        return array( 'lang' => $lang, 'title' => self::cut( $title, 200 ), 'teaser' => self::cut( $teaser, self::TEASER ), 'terms' => implode( ' ', self::terms( $text, self::TERMS ) ) );
    }

    /** @return string text without tags, entities decoded, white space collapsed */
    public static function text( $html )
    {
        $text = html_entity_decode( strip_tags( (string)$html ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        return trim( expStaticPostProcessor::checked( preg_replace( '/\s+/u', ' ', $text ), 'search index' ) );
    }

    /** @return string at most $length characters, cut at a word */
    public static function cut( $text, $length )
    {
        $text = (string)$text;
        if ( mb_strlen( $text, 'UTF-8' ) <= $length )
            return $text;
        $cut = mb_substr( $text, 0, $length, 'UTF-8' );
        $space = mb_strrpos( $cut, ' ', 0, 'UTF-8' );
        return rtrim( $space !== false && $space > $length / 2 ? mb_substr( $cut, 0, $space, 'UTF-8' ) : $cut, ' ,.;:' ) . '…';
    }

    /**
     * The words of a text that tell most about it: lower case, three letters or more, by how often they appear.
     *
     * @param string $text
     * @param int $max
     * @return string[]
     */
    public static function terms( $text, $max )
    {
        if ( !preg_match_all( '/[\p{L}\p{N}][\p{L}\p{N}\'-]{2,}/u', mb_strtolower( (string)$text, 'UTF-8' ), $m ) )
            return array();
        $counts = array();
        foreach ( $m[0] as $word )
        {
            $word = trim( $word, "'-" );
            if ( mb_strlen( $word, 'UTF-8' ) < 3 || mb_strlen( $word, 'UTF-8' ) > 40 || ctype_digit( $word ) )
                continue;
            $counts[$word] = ( isset( $counts[$word] ) ? $counts[$word] : 0 ) + 1;
        }
        // most frequent first; the first seen first among equals
        $order = array_flip( array_keys( $counts ) );
        uksort( $counts, function ( $a, $b ) use ( $counts, $order ) {
            return $counts[$b] <=> $counts[$a] ?: $order[$a] <=> $order[$b];
        } );
        return array_slice( array_map( 'strval', array_keys( $counts ) ), 0, (int)$max );
    }

    /**
     * Keeps the entries of an index written before whose pages this one does not have (a partial build).
     *
     * @param string $json an index file
     * @return int entries kept
     */
    public function keep( $json )
    {
        $data = json_decode( (string)$json, true );
        if ( !is_array( $data ) || !isset( $data['schema'], $data['lang'], $data['pages'] ) || $data['schema'] !== self::SCHEMA || !is_array( $data['pages'] ) )
            return 0;
        $lang = (string)$data['lang'];
        $have = array();
        foreach ( isset( $this->entries[$lang] ) ? $this->entries[$lang] : array() as $entry )
            $have[$entry['u']] = true;
        $kept = 0;
        foreach ( $data['pages'] as $entry )
            if ( is_array( $entry ) && isset( $entry['u'] ) && !isset( $have[(string)$entry['u']] ) )
            {
                $keptEntry = array( 't' => (string)( isset( $entry['t'] ) ? $entry['t'] : '' ), 'u' => (string)$entry['u'],
                                     'd' => (string)( isset( $entry['d'] ) ? $entry['d'] : '' ), 'k' => (string)( isset( $entry['k'] ) ? $entry['k'] : '' ) );
                if ( isset( $entry['s'] ) && (string)$entry['s'] !== '' )
                    $keptEntry['s'] = (string)$entry['s'];
                $this->entries[$lang][] = $keptEntry;
                $kept++;
            }
        return $kept;
    }

    /** @return string[] the languages with an index */
    public function languages()
    {
        $langs = array_keys( $this->entries );
        sort( $langs );
        return array_map( 'strval', $langs );
    }

    /** @return int the pages in all indexes */
    public function count()
    {
        return array_sum( array_map( 'count', $this->entries ) );
    }

    /**
     * The index files, each below the size limit.
     *
     * @return array file => array( content, pages, dropped )
     */
    public function files()
    {
        $out = array();
        foreach ( $this->languages() as $lang )
        {
            $entries = $this->entries[$lang];
            $dropped = 0;
            $json = self::encode( $lang, $entries );
            // over the limit: fewer terms, then no teasers, then fewer pages
            foreach ( array( 10, 0 ) as $terms )
            {
                if ( strlen( $json ) <= $this->maxBytes )
                    break;
                foreach ( $entries as $i => $entry )
                    $entries[$i]['k'] = implode( ' ', array_slice( explode( ' ', $entry['k'] ), 0, $terms ) );
                $json = self::encode( $lang, $entries );
            }
            if ( strlen( $json ) > $this->maxBytes )
            {
                foreach ( $entries as $i => $entry )
                    $entries[$i]['d'] = '';
                $json = self::encode( $lang, $entries );
            }
            while ( strlen( $json ) > $this->maxBytes && $entries )
            {
                $cut = max( 1, (int)ceil( count( $entries ) * ( 1 - $this->maxBytes / strlen( $json ) ) ) );
                $entries = array_slice( $entries, 0, count( $entries ) - $cut );
                $dropped += $cut;
                $json = self::encode( $lang, $entries );
            }
            $out[self::indexFile( $lang )] = array( 'content' => $json, 'pages' => count( $entries ), 'dropped' => $dropped );
        }
        return $out;
    }

    /** @return string the file of a language's index */
    public static function indexFile( $lang )
    {
        return self::DIRECTORY . '/index-' . preg_replace( '/[^a-z0-9-]/', '', strtolower( (string)$lang ) ) . '.json';
    }

    /** @return string one index as JSON */
    private static function encode( $lang, array $entries )
    {
        return (string)json_encode( array( 'schema' => self::SCHEMA, 'lang' => (string)$lang, 'pages' => array_values( $entries ) ),
                                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
    }

    /**
     * The search page. Its links are relative to the root of the build, where it lives.
     *
     * @param array $languages the languages with an index (the first is the default)
     * @param array $texts title, label, button, nothing (no result), results (%count), noscript, lang (of the page), where
     *                     (the label of the choice of site), all (searching every site)
     * @param array $sites a copy of several siteaccesses: siteaccess => its folder (/ or /ger/); the page offers to
     *                     search one of them or all (?site=<siteaccess>), and searches every language's index
     * @return string
     */
    public static function page( array $languages, array $texts = array(), array $sites = array() )
    {
        $texts += array( 'title' => 'Search', 'label' => 'Search this site', 'button' => 'Search', 'nothing' => 'Nothing found.',
                         'results' => '%count results', 'noscript' => 'The search needs JavaScript.', 'home' => 'Front page', 'lang' => '',
                         'where' => 'Where', 'all' => 'All sites' );
        $h = function ( $s ) { return htmlspecialchars( (string)$s, ENT_QUOTES, 'UTF-8' ); };
        $lang = $texts['lang'] !== '' ? $texts['lang'] : ( $languages ? (string)$languages[0] : 'en' );
        if ( $lang === 'und' )
            $lang = 'en';
        $indexes = array();
        foreach ( $languages as $l )
            $indexes[(string)$l] = self::indexFile( $l );
        $choice = '';
        if ( $sites )
        {
            $choice = " <label for=\"exp-static-site\">" . $h( $texts['where'] ) . "</label> <select id=\"exp-static-site\" name=\"site\"><option value=\"\">"
                    . $h( $texts['all'] ) . "</option>";
            foreach ( $sites as $site => $folder )
                $choice .= "<option value=\"" . $h( $site ) . "\">" . $h( $site . ' (' . $folder . ')' ) . "</option>";
            $choice .= "</select>";
        }
        return "<!DOCTYPE html>\n<html lang=\"" . $h( $lang ) . "\"><head><meta charset=\"utf-8\"><title>" . $h( $texts['title'] ) . "</title>\n"
             . "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\"><meta name=\"robots\" content=\"noindex\">\n"
             . "<link rel=\"stylesheet\" href=\"static-search/search.css\"></head>\n"
             . "<body class=\"exp-static-search\"><main>\n<h1>" . $h( $texts['title'] ) . "</h1>\n"
             . "<form role=\"search\" method=\"get\" action=\"search.html\"><label for=\"exp-static-q\">" . $h( $texts['label'] ) . "</label>\n"
             . "<input type=\"search\" id=\"exp-static-q\" name=\"SearchText\" autocomplete=\"off\">" . $choice . " <button type=\"submit\">" . $h( $texts['button'] ) . "</button></form>\n"
             . "<p id=\"exp-static-count\" role=\"status\" aria-live=\"polite\"></p><ol id=\"exp-static-results\"></ol>\n"
             . "<noscript><p>" . $h( $texts['noscript'] ) . "</p></noscript>\n"
             . "<p><a href=\"./\">" . $h( $texts['home'] ) . "</a></p></main>\n"
             . "<script id=\"exp-static-search-config\" type=\"application/json\">"
             . str_replace( '</', '<\/', (string)json_encode( array( 'indexes' => (object)$indexes, 'nothing' => $texts['nothing'], 'results' => $texts['results'] )
                                                              + ( $sites ? array( 'sites' => array_keys( $sites ) ) : array() ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) )
             . "</script>\n<script src=\"" . self::SCRIPT . "\" defer></script>\n</body></html>\n";
    }

    /** @return string the search page's style sheet: the system's colors, light and dark */
    public static function styleSheet()
    {
        return ":root{color-scheme:light dark}body.exp-static-search{font:16px/1.5 system-ui,sans-serif;margin:0;padding:1rem}"
             . ".exp-static-search main{max-width:42rem;margin:0 auto}.exp-static-search label{display:block;font-weight:600}"
             . ".exp-static-search input{font:inherit;padding:.4rem;width:min(100%,28rem)}.exp-static-search button{font:inherit;padding:.4rem .8rem}"
             . ".exp-static-search ol{padding-left:1.2rem}.exp-static-search li{margin:.8rem 0}.exp-static-search li p{margin:.2rem 0 0}\n";
    }

    /** @return string the search script: vanilla JavaScript, no library */
    public static function script()
    {
        return <<<'JS'
/* Exponential static publishing: the search of a static copy (exp-static-search/1). No library, no server. */
(function () {
  'use strict';
  var config = JSON.parse(document.getElementById('exp-static-search-config').textContent || '{}');
  var params = new URLSearchParams(window.location.search);
  var query = (params.get('SearchText') || params.get('q') || '').trim();
  var input = document.getElementById('exp-static-q');
  var list = document.getElementById('exp-static-results');
  var count = document.getElementById('exp-static-count');
  input.value = query;
  if (!query) { return; }
  var lang = (document.documentElement.lang || '').toLowerCase();
  var indexes = config.indexes || {};
  // a copy of several sites: every language's index, the pages of the site chosen (?site=) or of all
  var site = (params.get('site') || '').trim();
  var select = document.getElementById('exp-static-site');
  if (select) { select.value = site; }
  var files = config.sites ? Object.keys(indexes).map(function (k) { return indexes[k]; })
    : [indexes[lang] || indexes[lang.split('-')[0]] || indexes[Object.keys(indexes)[0]]].filter(Boolean);
  if (!files.length) { count.textContent = config.nothing; return; }
  function words(s) { return s.toLocaleLowerCase().split(/[^\p{L}\p{N}]+/u).filter(function (w) { return w.length > 1; }); }
  Promise.all(files.map(function (f) { return fetch(f).then(function (r) { return r.json(); }); })).then(function (list) {
    var wanted = words(query);
    var hits = [];
    var pages = [];
    list.forEach(function (index) { pages = pages.concat(index.pages || []); });
    pages.forEach(function (p) {
      if (site && p.s !== site) { return; }
      var title = (p.t || '').toLocaleLowerCase(), terms = ' ' + (p.k || '') + ' ', teaser = (p.d || '').toLocaleLowerCase();
      var score = 0, all = true;
      wanted.forEach(function (w) {
        var s = 0;
        if (title.indexOf(w) !== -1) { s += 10; }
        if (terms.indexOf(' ' + w) !== -1) { s += 5; }
        if (teaser.indexOf(w) !== -1) { s += 2; }
        if (!s) { all = false; }
        score += s;
      });
      if (score && all) { hits.push({ p: p, s: score }); }
    });
    hits.sort(function (a, b) { return b.s - a.s; });
    count.textContent = hits.length ? (config.results || '%count').replace('%count', hits.length) : config.nothing;
    hits.slice(0, 50).forEach(function (h) {
      var li = document.createElement('li'), a = document.createElement('a'), d = document.createElement('p');
      a.href = h.p.u === '' ? './' : h.p.u;
      a.textContent = h.p.t || h.p.u;
      d.textContent = h.p.d || '';
      li.appendChild(a); li.appendChild(d); list.appendChild(li);
    });
  }).catch(function () { count.textContent = config.nothing; });
}());

JS;
    }
}
