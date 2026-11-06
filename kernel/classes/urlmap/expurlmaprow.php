<?php
/**
 * File containing the expUrlMapRow class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One row of a URL map file: an old address and where it goes (old_url, new_url, kind, siteaccess, language, note).
 * Pure and DB-less; the specification is doc/eng-us/specifications/6.0/url-map-file-formats.md.
 *
 * The old and the new address are normalized when the row is made, so that every format gives the same row for the
 * same address:
 * - a scheme and a host are split off (scheme and host in lower case, a default port and a trailing dot dropped);
 *   "//host/path" is a host without a scheme, "www.example.com/path" a host because of its "www."
 * - the path starts with "/", repeated slashes are one, "." and ".." segments are resolved, and a trailing slash is
 *   dropped (the root "/" stays): Exponential serves /about and /about/ as the same page
 * - percent-encoding is decoded where it encodes UTF-8 text; "%2F", "%3F", "%23", "%25", spaces and control
 *   characters stay (or become) encoded, with upper-case hex digits
 * - the fragment (#...) is dropped, the query (?...) is kept as given
 * A new address "node:123" or "eznode://123" becomes "node:123".
 *
 * A row knows the problems that keep it from being imported (problems()): what validation found, and what a reader
 * added (a rewrite rule that a row cannot express is read as a row that is not importable, with the reason).
 */
class expUrlMapRow
{
    const KIND_AUTO = '';
    const KIND_REDIRECT = 'redirect';
    const KIND_ALIAS = 'alias';
    const KIND_WILDCARD_FORWARD = 'wildcard-forward';
    const KIND_WILDCARD_DIRECT = 'wildcard-direct';

    /** @var string[] the canonical columns, in file order */
    const COLUMNS = array( 'old_url', 'new_url', 'kind', 'siteaccess', 'language', 'note' );

    /** @var string */
    private $oldUrl;
    /** @var string */
    private $newUrl;
    /** @var string */
    private $kind;
    /** @var string */
    private $siteaccess;
    /** @var string */
    private $language;
    /** @var string */
    private $note;
    /** @var int */
    private $line;
    /** @var string */
    private $oldScheme = '';
    /** @var string */
    private $oldHost = '';
    /** @var string */
    private $oldPath = '';
    /** @var string */
    private $oldQuery = '';
    /** @var string[] */
    private $problems = array();

    /**
     * @param string $oldUrl
     * @param string $newUrl
     * @param string $kind empty (auto) or one of the KIND_* values; a spelling with "_" or a space and any case is
     *                     accepted, an unknown one is kept and reported as a problem
     * @param string $siteaccess
     * @param string $language
     * @param string $note
     * @param int $line where the row was in its file (a line, a spreadsheet row, a JSON entry); 0 when not from a file
     */
    function __construct( $oldUrl, $newUrl, $kind = '', $siteaccess = '', $language = '', $note = '', $line = 0 )
    {
        $this->line = (int)$line;
        $this->siteaccess = trim( (string)$siteaccess );
        $this->language = trim( (string)$language );
        $this->note = trim( (string)$note );
        $canonicalKind = self::normalizeKind( $kind );
        $this->kind = $canonicalKind === null ? trim( (string)$kind ) : $canonicalKind;

        $oldUrl = trim( (string)$oldUrl );
        $newUrl = trim( (string)$newUrl );
        if ( $oldUrl === '' )
        {
            $this->oldUrl = '';
            $this->problems[] = 'old_url is empty';
        }
        else
        {
            $parts = self::parseUrl( $oldUrl );
            $this->oldScheme = $parts['scheme'];
            $this->oldHost = $parts['host'];
            $this->oldPath = $parts['path'];
            $this->oldQuery = $parts['query'];
            $this->oldUrl = self::buildUrl( $parts );
            foreach ( $parts['problems'] as $problem )
                $this->problems[] = 'old_url: ' . $problem;
        }
        if ( $newUrl === '' )
        {
            $this->newUrl = '';
            $this->problems[] = 'new_url is empty';
        }
        else
        {
            $this->newUrl = self::normalizeNewUrl( $newUrl, $newProblems );
            foreach ( $newProblems as $problem )
                $this->problems[] = 'new_url: ' . $problem;
        }
        $this->validate( $canonicalKind === null );
    }

    /**
     * A row from an array with the canonical keys (missing keys are empty; other keys are ignored).
     *
     * @param array $values
     * @param int $line
     * @return expUrlMapRow
     */
    static function fromArray( array $values, $line = 0 )
    {
        $get = function( $key ) use ( $values )
        {
            return isset( $values[$key] ) && is_scalar( $values[$key] ) ? (string)$values[$key] : '';
        };
        return new self( $get( 'old_url' ), $get( 'new_url' ), $get( 'kind' ), $get( 'siteaccess' ),
                         $get( 'language' ), $get( 'note' ), $line );
    }

    /**
     * A row a reader could not turn into a mapping: the old and the new text are kept as they were in the file, not
     * normalized, and the reason is the row's first problem.
     *
     * @param string $old the old address or pattern as written
     * @param string $new the target as written
     * @param string $reason why it cannot be imported
     * @param int $line
     * @param string $note e.g. the directive as it was written
     * @return expUrlMapRow
     */
    static function notImportable( $old, $new, $reason, $line = 0, $note = '' )
    {
        $row = new self( '/', '/', '', '', '', $note, $line );
        $row->oldUrl = trim( (string)$old );
        $row->oldPath = $row->oldUrl;
        $row->newUrl = trim( (string)$new );
        $row->problems = array( 'not importable: ' . $reason );
        return $row;
    }

    /**
     * @return array the six canonical columns as strings
     */
    function toArray()
    {
        return array(
            'old_url' => $this->oldUrl,
            'new_url' => $this->newUrl,
            'kind' => $this->kind,
            'siteaccess' => $this->siteaccess,
            'language' => $this->language,
            'note' => $this->note,
        );
    }

    /** @return string the normalized old address (scheme://host/path?query, //host/path or /path) */
    function oldUrl()
    {
        return $this->oldUrl;
    }

    /** @return string the normalized target: a path, node:<id>, a full URL, or a pattern with {1} placeholders */
    function newUrl()
    {
        return $this->newUrl;
    }

    /** @return string the kind as given (canonical spelling when known; empty = auto) */
    function kind()
    {
        return $this->kind;
    }

    /** @return string */
    function siteaccess()
    {
        return $this->siteaccess;
    }

    /** @return string */
    function language()
    {
        return $this->language;
    }

    /** @return string */
    function note()
    {
        return $this->note;
    }

    /** @return int */
    function line()
    {
        return $this->line;
    }

    /** @return string http, https or empty */
    function oldScheme()
    {
        return $this->oldScheme;
    }

    /** @return string the host in lower case, with ":port" when it is not the default one; empty for a bare path */
    function oldHost()
    {
        return $this->oldHost;
    }

    /** @return string the normalized path, starting with "/" */
    function oldPath()
    {
        return $this->oldPath;
    }

    /** @return string the query without "?", empty when there is none */
    function oldQuery()
    {
        return $this->oldQuery;
    }

    /**
     * The kind the row is imported as: the given one, or for an empty kind a wildcard (forward) when old_url has a
     * "*", else a redirect.
     *
     * @return string
     */
    function effectiveKind()
    {
        if ( $this->kind !== self::KIND_AUTO )
            return $this->kind;
        return strpos( $this->oldPath, '*' ) !== false ? self::KIND_WILDCARD_FORWARD : self::KIND_REDIRECT;
    }

    /** @return bool */
    function isWildcard()
    {
        $kind = $this->effectiveKind();
        return $kind === self::KIND_WILDCARD_FORWARD || $kind === self::KIND_WILDCARD_DIRECT;
    }

    /** @return bool true for the kinds that answer with a redirect (redirect, wildcard-forward) */
    function isForward()
    {
        $kind = $this->effectiveKind();
        return $kind === self::KIND_REDIRECT || $kind === self::KIND_WILDCARD_FORWARD;
    }

    /** @return string[] */
    function problems()
    {
        return $this->problems;
    }

    /** @return bool */
    function isImportable()
    {
        return !$this->problems;
    }

    /**
     * @param string $problem
     * @return expUrlMapRow $this
     */
    function addProblem( $problem )
    {
        $this->problems[] = (string)$problem;
        return $this;
    }

    /**
     * @param string $note
     * @return expUrlMapRow $this
     */
    function setNote( $note )
    {
        $this->note = trim( (string)$note );
        return $this;
    }

    /**
     * @param string $kind
     * @return string|null the canonical kind (empty for auto), or null when it is not a kind
     */
    static function normalizeKind( $kind )
    {
        $kind = strtolower( trim( (string)$kind ) );
        $kind = preg_replace( '/[\s_]+/', '-', $kind );
        if ( $kind === '' || $kind === 'auto' )
            return self::KIND_AUTO;
        $known = array( self::KIND_REDIRECT, self::KIND_ALIAS, self::KIND_WILDCARD_FORWARD, self::KIND_WILDCARD_DIRECT );
        return in_array( $kind, $known, true ) ? $kind : null;
    }

    /** @return string[] the kinds a row can have, without auto */
    static function kinds()
    {
        return array( self::KIND_REDIRECT, self::KIND_ALIAS, self::KIND_WILDCARD_FORWARD, self::KIND_WILDCARD_DIRECT );
    }

    /**
     * Splits and normalizes an address.
     *
     * @param string $url
     * @return array scheme, host, path, query, problems
     */
    static function parseUrl( $url )
    {
        $url = trim( (string)$url );
        $result = array( 'scheme' => '', 'host' => '', 'path' => '/', 'query' => '', 'problems' => array() );
        $hash = strpos( $url, '#' );
        if ( $hash !== false )
            $url = substr( $url, 0, $hash );
        $rest = $url;
        if ( preg_match( '#^([a-zA-Z][a-zA-Z0-9+.\-]*):(//.*)?$#s', $url, $m ) && isset( $m[2] ) && $m[2] !== '' )
        {
            $result['scheme'] = strtolower( $m[1] );
            if ( $result['scheme'] !== 'http' && $result['scheme'] !== 'https' )
                $result['problems'][] = 'the scheme "' . $m[1] . '" is not http or https';
            $rest = $m[2];
        }
        elseif ( preg_match( '#^www\.[^/?\s]+\.[a-zA-Z]{2,}(?:[/?]|$)#', $url ) )
        {
            $rest = '//' . $url;
        }
        if ( substr( $rest, 0, 2 ) === '//' )
        {
            $end = strcspn( $rest, '/?', 2 ) + 2;
            $authority = substr( $rest, 2, $end - 2 );
            $rest = (string)substr( $rest, $end );
            if ( strpos( $authority, '@' ) !== false )
            {
                $result['problems'][] = 'a user name or password in the address is not allowed';
                $authority = substr( $authority, strrpos( $authority, '@' ) + 1 );
            }
            $port = '';
            if ( preg_match( '/^(\[[^\]]*\]|[^:]*):(\d*)$/', $authority, $pm ) )
            {
                $authority = $pm[1];
                $port = $pm[2];
            }
            $host = rtrim( function_exists( 'mb_strtolower' ) ? mb_strtolower( $authority, 'UTF-8' ) : strtolower( $authority ), '.' );
            if ( $host === '' )
                $result['problems'][] = 'the host is empty';
            if ( $port !== '' && !( $port === '80' && $result['scheme'] === 'http' ) && !( $port === '443' && $result['scheme'] === 'https' ) )
                $host .= ':' . (int)$port;
            $result['host'] = $host;
        }
        $question = strpos( $rest, '?' );
        if ( $question !== false )
        {
            $result['query'] = (string)substr( $rest, $question + 1 );
            $rest = substr( $rest, 0, $question );
        }
        $result['path'] = self::normalizePath( $rest );
        return $result;
    }

    /**
     * @param array $parts as parseUrl() gives them
     * @return string
     */
    static function buildUrl( array $parts )
    {
        $url = '';
        if ( $parts['host'] !== '' )
            $url = ( $parts['scheme'] !== '' ? $parts['scheme'] . ':' : '' ) . '//' . $parts['host'];
        $url .= $parts['path'];
        if ( $parts['query'] !== '' )
            $url .= '?' . $parts['query'];
        return $url;
    }

    /**
     * Normalizes a path: leading slash, one slash between segments, dot segments resolved, no trailing slash (but
     * "/"), percent-encoding decoded where it is UTF-8 text.
     *
     * @param string $path
     * @return string
     */
    static function normalizePath( $path )
    {
        $path = self::normalizePercentEncoding( (string)$path );
        $out = array();
        foreach ( explode( '/', $path ) as $segment )
        {
            if ( $segment === '' || $segment === '.' )
                continue;
            if ( $segment === '..' )
            {
                array_pop( $out );
                continue;
            }
            $out[] = $segment;
        }
        return '/' . implode( '/', $out );
    }

    /**
     * Decodes %XX runs that encode UTF-8 text; keeps / ? # % encoded, encodes spaces and control characters, and
     * writes the hex digits that stay in upper case.
     *
     * @param string $text
     * @return string
     */
    static function normalizePercentEncoding( $text )
    {
        $text = preg_replace_callback( '/(?:%[0-9A-Fa-f]{2})+/', function( $m )
        {
            $decoded = rawurldecode( $m[0] );
            if ( !preg_match( '//u', $decoded ) )
                return strtoupper( $m[0] );
            return preg_replace_callback( '/[\x00-\x20\x7F%\/?#]/', function( $c )
            {
                return sprintf( '%%%02X', ord( $c[0] ) );
            }, $decoded );
        }, $text );
        return preg_replace_callback( '/[\x00-\x20\x7F]/', function( $c )
        {
            return sprintf( '%%%02X', ord( $c[0] ) );
        }, $text );
    }

    /**
     * @param string $url
     * @param string[] $problems filled with what is wrong
     * @return string
     */
    static function normalizeNewUrl( $url, &$problems = null )
    {
        $problems = array();
        $url = trim( (string)$url );
        if ( preg_match( '#^(?:node:|eznode://)\s*(\d+)$#i', $url, $m ) )
            return 'node:' . (int)$m[1];
        if ( preg_match( '#^(?:node:|eznode:)#i', $url ) )
        {
            $problems[] = 'a node reference is node:<number> or eznode://<number>';
            return $url;
        }
        $parts = self::parseUrl( $url );
        $problems = $parts['problems'];
        if ( $parts['host'] !== '' && $parts['scheme'] === '' )
            $parts['scheme'] = 'https';
        return self::buildUrl( $parts );
    }

    /**
     * Validates the combination of the fields and records the problems.
     *
     * @param bool $unknownKind
     */
    private function validate( $unknownKind )
    {
        if ( $unknownKind )
            $this->problems[] = 'unknown kind "' . $this->kind . '"; use redirect, alias, wildcard-forward or wildcard-direct (or leave it empty)';
        if ( $this->siteaccess !== '' && !preg_match( '/^[A-Za-z0-9_\-]+$/', $this->siteaccess ) )
            $this->problems[] = 'siteaccess "' . $this->siteaccess . '" is not a siteaccess name';
        if ( $this->language !== '' && !preg_match( '/^[a-z]{2,3}-[A-Za-z]{2,4}$/', $this->language ) )
            $this->problems[] = 'language "' . $this->language . '" is not a locale such as eng-GB';
        if ( $this->oldUrl === '' || $this->newUrl === '' || $unknownKind )
            return;

        $stars = substr_count( $this->oldPath, '*' );
        if ( strpos( $this->oldHost, '*' ) !== false )
            $this->problems[] = 'old_url: a * is only allowed in the path, not in the host';
        if ( $this->oldQuery !== '' && $stars )
            $this->problems[] = 'old_url: a wildcard cannot have a query (?...)';
        $kind = $this->effectiveKind();
        $wildcard = $kind === self::KIND_WILDCARD_FORWARD || $kind === self::KIND_WILDCARD_DIRECT;
        if ( $stars && !$wildcard )
            $this->problems[] = 'old_url has a * but the kind ' . $kind . ' is not a wildcard';
        if ( $wildcard && !$stars )
            $this->problems[] = 'the kind ' . $kind . ' needs a * in old_url';
        preg_match_all( '/\{(\d+)\}/', $this->newUrl, $placeholders );
        if ( $placeholders[1] )
        {
            if ( !$wildcard )
                $this->problems[] = 'new_url has a placeholder ' . $placeholders[0][0] . ', which only a wildcard can fill';
            else
            {
                foreach ( $placeholders[1] as $number )
                {
                    if ( (int)$number < 1 || (int)$number > $stars )
                    {
                        $this->problems[] = 'new_url: the placeholder {' . $number . '} has no * in old_url (it has ' . $stars . ')';
                        break;
                    }
                }
            }
        }
        if ( !$wildcard && $this->oldHost === '' && $this->oldQuery === '' && $this->newUrl === $this->oldPath )
            $this->problems[] = 'old_url and new_url are the same address';
    }
}
