<?php
/**
 * Reporting an error (exphelp/report, doc/features/6.0/help-system.md "Reporting an error"): where to report it
 * (the trackers of [IssueTrackers], the repository of an extension, the community channels), prefilled GitHub
 * new-issue URLs, and a safe summary of the installation to paste into a report.
 *
 * The summary is built from a fixed list of facts (ENVIRONMENT_KEYS): versions, PHP, the engine that serves the
 * request, the database type, design, siteaccess, language and the active extensions. Nothing else is read, and
 * every value goes through scrub(), which removes paths outside the install root, e-mail addresses, IP addresses,
 * credentials in URLs and key=value secrets. No database host, user, password or name, no mail settings, no
 * session, no user is ever part of it. What the visitor types into the report form goes through scrub() as well.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpIssueReport
{
    /** The only facts the environment summary can contain, in this order */
    const ENVIRONMENT_KEYS = array( 'exponential', 'php', 'sapi', 'engine', 'os', 'database', 'siteaccess', 'design',
                                    'language', 'debug', 'view_cache', 'extensions', 'generated' );

    /** The fields of the report form, with their longest accepted length */
    const DRAFT_FIELDS = array( 'title' => 200, 'what' => 2000, 'steps' => 2000, 'expected' => 1000 );

    /** GitHub answers 414 above about 8 KB of address; the prefilled URL stays below this */
    const MAX_URL_LENGTH = 7500;

    /** The pattern of a GitHub repository address, owner and name captured */
    const GITHUB_REPOSITORY = '#^https://github\.com/([A-Za-z0-9](?:[A-Za-z0-9-]{0,38}))/([A-Za-z0-9_.-]{1,100})/?$#';

    /** @var array|null facts injected instead of read (tests), key => string */
    protected $values;

    /** @var array|null settings injected instead of exphelp.ini (tests), "Group/Name" => value */
    protected $settings;

    /** @var array|null per-instance cache of extensionTrackers() */
    protected $extensionTrackers;

    /** @var array|null per-instance cache of the facts read by collect() and of the extensions' info */
    protected $facts;
    protected $infos;

    /**
     * @param array|null $values facts to use instead of reading them (key => string, keys of ENVIRONMENT_KEYS)
     * @param array|null $settings settings to use instead of exphelp.ini ("Group/Name" => value); with $values,
     *                            "extension_infos" => array( name => info ) stands for the active extensions
     */
    public function __construct( ?array $values = null, ?array $settings = null )
    {
        $this->values = $values;
        $this->settings = $settings;
    }

    /**
     * The GitHub trackers of [IssueTrackers] TrackerList[], in that order. A tracker whose URL is not a GitHub
     * repository's issue list is left out.
     *
     * @return array key => array( key, label, description, url, new_issue_url, repository, slug (owner/name) )
     */
    public function trackers()
    {
        $trackers = array();
        foreach ( (array)$this->setting( 'IssueTrackers', 'TrackerList', array() ) as $key )
        {
            $key = (string)$key;
            if ( $key === '' || !preg_match( '/^[a-z0-9_-]{1,40}$/', $key ) )
                continue;
            $group = 'Tracker_' . $key;
            $url = (string)$this->setting( $group, 'URL', '' );
            $repository = self::repositoryFromIssuesUrl( $url );
            if ( $repository === false )
                continue;
            $new = (string)$this->setting( $group, 'NewIssueURL', '' );
            if ( $new === '' || strpos( $new, $repository . '/issues/new' ) !== 0 )
                $new = $repository . '/issues/new';
            $trackers[$key] = array(
                'key' => $key,
                'label' => (string)$this->setting( $group, 'Name', $key ),
                'description' => (string)$this->setting( $group, 'Description', '' ),
                'url' => $repository . '/issues',
                'new_issue_url' => $new,
                'repository' => $repository,
                'slug' => substr( $repository, strlen( 'https://github.com/' ) ),
            );
        }
        return $trackers;
    }

    /**
     * The repositories of the active extensions that have their own tracker on GitHub: the extension's website
     * (extension.xml, ezinfo.php, composer.json, git origin), when it is a GitHub repository of an owner in
     * [IssueTrackers] ExtensionOwners[]. Extensions shipped with the kernel ([IssueTrackers] BundledExtensions[])
     * are reported in the software tracker instead.
     *
     * @return array key ("ext-<name>") => array( key, extension, label, description, url, new_issue_url, repository, slug, version )
     */
    public function extensionTrackers()
    {
        if ( $this->extensionTrackers !== null )
            return $this->extensionTrackers;

        $owners = array_map( 'strtolower', array_map( 'strval', (array)$this->setting( 'IssueTrackers', 'ExtensionOwners', array( 'se7enxweb' ) ) ) );
        $bundled = array_map( 'strval', (array)$this->setting( 'IssueTrackers', 'BundledExtensions', array() ) );
        $trackers = array();
        foreach ( $this->extensionInfos() as $name => $info )
        {
            if ( in_array( $name, $bundled, true ) || !preg_match( '/^[A-Za-z0-9_.-]{1,80}$/', $name ) )
                continue;
            $url = isset( $info['info_url'] ) ? rtrim( trim( (string)$info['info_url'] ), '/' ) : '';
            $url = preg_replace( '#\.git$#', '', $url );
            if ( !preg_match( self::GITHUB_REPOSITORY, $url, $m ) || !in_array( strtolower( $m[1] ), $owners, true ) )
                continue;
            $repository = 'https://github.com/' . $m[1] . '/' . $m[2];
            $key = 'ext-' . $name;
            $trackers[$key] = array(
                'key' => $key,
                'extension' => $name,
                'label' => isset( $info['name'] ) && is_string( $info['name'] ) && trim( $info['name'] ) !== '' ? trim( strip_tags( $info['name'] ) ) : $name,
                'description' => '',
                'url' => $repository . '/issues',
                'new_issue_url' => $repository . '/issues/new',
                'repository' => $repository,
                'slug' => $m[1] . '/' . $m[2],
                'version' => isset( $info['version'] ) && is_string( $info['version'] ) ? self::scrub( $info['version'] ) : '',
            );
        }
        uasort( $trackers, function ( $a, $b ) { return strcasecmp( $a['extension'], $b['extension'] ); } );
        return $this->extensionTrackers = $trackers;
    }

    /**
     * Places to ask a question or talk an error through before it is a report: [IssueTrackers] CommunityLinks[]
     * as "<label>|<https URL>". Only https addresses are kept.
     *
     * @return array of array( label, url )
     */
    public function communityLinks()
    {
        $links = array();
        foreach ( (array)$this->setting( 'IssueTrackers', 'CommunityLinks', array() ) as $entry )
        {
            $parts = explode( '|', (string)$entry, 2 );
            if ( count( $parts ) !== 2 )
                continue;
            $url = trim( $parts[1] );
            if ( !preg_match( '#^https://[A-Za-z0-9.-]+(?:/[^\s"<>]*)?$#', $url ) )
                continue;
            $links[] = array( 'label' => trim( $parts[0] ), 'url' => $url );
        }
        return $links;
    }

    /**
     * The environment summary for the page, with translated labels.
     *
     * @return array label => value, safe to publish
     */
    public function environment()
    {
        $labels = self::environmentLabels( true );
        $environment = array();
        foreach ( $this->environmentValues() as $key => $value )
            $environment[$labels[$key]] = $value;
        return $environment;
    }

    /**
     * The environment summary keyed by fact (ENVIRONMENT_KEYS), every value scrubbed; facts that are not known
     * are left out.
     *
     * @return array key => string
     */
    public function environmentValues()
    {
        if ( $this->facts === null )
        {
            if ( $this->values !== null )
                $this->facts = $this->values;
            else
            {
                // reading the facts can connect the database, which registers the kernel's exception handler: the
                // handler in place before is put back, so a caller (a test, a command) keeps its own
                $before = set_exception_handler( null );
                set_exception_handler( $before );
                $this->facts = $this->collect();
                $after = set_exception_handler( null );
                set_exception_handler( $after );
                if ( $after !== $before )
                    restore_exception_handler();
            }
        }
        $facts = $this->facts;
        $values = array();
        foreach ( self::ENVIRONMENT_KEYS as $key )
        {
            if ( !isset( $facts[$key] ) || !is_scalar( $facts[$key] ) )
                continue;
            // version numbers of four parts (0.0.4.10) look like IPv4 addresses: the extension list keeps them
            $value = self::scrub( (string)$facts[$key], $key === 'extensions' ? 2000 : 200, $key !== 'extensions' );
            if ( $value !== '' )
                $values[$key] = $value;
        }
        return $values;
    }

    /**
     * The environment as a Markdown list in English (an issue is read by the maintainers), for an issue body or
     * the copy button.
     *
     * @return string
     */
    public function environmentText()
    {
        $labels = self::environmentLabels( false );
        $lines = array();
        foreach ( $this->environmentValues() as $key => $value )
            $lines[] = '- ' . $labels[$key] . ': ' . $value;
        return implode( "\n", $lines );
    }

    /**
     * The report form's fields from the request, trimmed, cut to their length and scrubbed.
     *
     * @param array $input usually $_GET
     * @return array title, what, steps, expected => string
     */
    public function draft( array $input )
    {
        $draft = array();
        foreach ( self::DRAFT_FIELDS as $field => $max )
        {
            $value = isset( $input[$field] ) && is_string( $input[$field] ) ? $input[$field] : '';
            // control characters (but newlines and tabs) and invalid UTF-8 are dropped
            $value = (string)preg_replace( '/[^\P{C}\n\t]/u', '', (string)@iconv( 'UTF-8', 'UTF-8//IGNORE', str_replace( "\r\n", "\n", $value ) ) );
            if ( $field === 'title' )
                $value = (string)preg_replace( '/\s+/u', ' ', $value );
            $draft[$field] = self::scrub( trim( $value ), $max );
        }
        return $draft;
    }

    /**
     * The title of a new issue: the visitor's title, else an empty string (GitHub asks for one).
     *
     * @param array $draft from draft()
     * @return string
     */
    public function issueTitle( array $draft )
    {
        return isset( $draft['title'] ) ? (string)$draft['title'] : '';
    }

    /**
     * The body of a new issue: the visitor's text in the sections a maintainer needs, placeholders for the
     * sections left empty, and the environment summary.
     *
     * @param array $draft from draft(); empty: the bare template
     * @param string $extension the extension the error is about, '' for none
     * @return string Markdown
     */
    public function issueBody( array $draft = array(), $extension = '' )
    {
        $section = function ( $heading, $text, $placeholder )
        {
            return '### ' . $heading . "\n\n" . ( $text !== '' ? $text : '<!-- ' . $placeholder . ' -->' ) . "\n";
        };
        $get = function ( $key ) use ( $draft ) { return isset( $draft[$key] ) ? (string)$draft[$key] : ''; };

        $body = array();
        $body[] = $section( 'What happened', $get( 'what' ), 'What did you do, and what went wrong? Quote the error message as it was shown.' );
        $body[] = $section( 'Steps to reproduce', $get( 'steps' ), '1. Go to ...  2. Click ...  3. See the error' );
        $body[] = $section( 'What you expected', $get( 'expected' ), 'What should have happened instead?' );
        $body[] = $section( 'Log messages', '', 'Paste the matching lines from Setup > Logs > Error digest. Remove names, e-mail addresses and anything private first.' );
        $environment = $this->environmentText();
        if ( $extension !== '' )
            $environment = '- Extension: ' . self::scrub( $extension, 120 ) . "\n" . $environment;
        $body[] = "### Environment\n\n" . $environment . "\n";
        return implode( "\n", $body );
    }

    /**
     * The new-issue URL of a tracker (a key of trackers() or extensionTrackers()) with the title and the body.
     * An empty body gives the bare template of issueBody(). The URL stays below MAX_URL_LENGTH: the body is cut
     * (with a note) when it would not fit.
     *
     * @param string $tracker
     * @param string $title
     * @param string $body
     * @return string the new-issue URL with title and body, '' for an unknown tracker
     */
    public function issueUrl( $tracker, $title = '', $body = '' )
    {
        $tracker = (string)$tracker;
        $trackers = $this->trackers();
        if ( !isset( $trackers[$tracker] ) && strpos( $tracker, 'ext-' ) === 0 )
            $trackers = $this->extensionTrackers();
        if ( !isset( $trackers[$tracker] ) )
            return '';

        if ( (string)$body === '' )
            $body = $this->issueBody( array(), isset( $trackers[$tracker]['extension'] ) ? $trackers[$tracker]['extension'] : '' );
        $base = $trackers[$tracker]['new_issue_url'];
        $title = self::cut( (string)$title, 200 );
        $url = $base . '?' . http_build_query( array( 'title' => $title, 'body' => (string)$body ), '', '&', PHP_QUERY_RFC3986 );
        if ( strlen( $url ) <= self::MAX_URL_LENGTH )
            return $url;

        // too long: cut the body until it fits, at a character boundary
        $note = "\n\n<!-- cut to fit the address; paste the rest here -->";
        $prefix = $base . '?' . http_build_query( array( 'title' => $title ), '', '&', PHP_QUERY_RFC3986 ) . '&body=';
        $budget = self::MAX_URL_LENGTH - strlen( $prefix ) - strlen( rawurlencode( $note ) );
        $low = 0;
        $high = mb_strlen( (string)$body, 'UTF-8' );
        while ( $low < $high )
        {
            $mid = (int)ceil( ( $low + $high ) / 2 );
            if ( strlen( rawurlencode( mb_substr( (string)$body, 0, $mid, 'UTF-8' ) ) ) <= $budget )
                $low = $mid;
            else
                $high = $mid - 1;
        }
        return $prefix . rawurlencode( mb_substr( (string)$body, 0, $low, 'UTF-8' ) . $note );
    }

    /**
     * The new-issue URL for the report form's draft: when the whole draft does not fit in the address, the longest
     * of the visitor's fields is shortened (never the environment summary) until it does.
     *
     * @param string $tracker a key of trackers() or extensionTrackers()
     * @param array $draft from draft()
     * @return string '' for an unknown tracker
     */
    public function draftIssueUrl( $tracker, array $draft )
    {
        $all = $this->trackers() + $this->extensionTrackers();
        if ( !isset( $all[$tracker] ) )
            return '';
        $extension = isset( $all[$tracker]['extension'] ) ? $all[$tracker]['extension'] : '';
        for ( $round = 0; $round < 40; $round++ )
        {
            $url = $this->issueUrl( $tracker, $this->issueTitle( $draft ), $this->issueBody( $draft, $extension ) );
            if ( strlen( $url ) < self::MAX_URL_LENGTH )
                return $url;
            $longest = '';
            $length = 0;
            foreach ( array( 'what', 'steps', 'expected' ) as $field )
            {
                $l = isset( $draft[$field] ) ? mb_strlen( $draft[$field], 'UTF-8' ) : 0;
                if ( $l > $length )
                {
                    $longest = $field;
                    $length = $l;
                }
            }
            if ( $length < 20 )
                break;
            $draft[$longest] = self::cut( $draft[$longest], (int)floor( $length * 0.75 ) );
        }
        return $this->issueUrl( $tracker, $this->issueTitle( $draft ), $this->issueBody( $draft, $extension ) );
    }

    /**
     * The private disclosure path for security issues (SECURITY.md): an e-mail address, the policy, and GitHub's
     * private vulnerability reporting form of the software's repository. Never a public tracker.
     *
     * @return array array( 'email' => string, 'policy_url' => string, 'advisory_url' => string )
     */
    public function securityContact()
    {
        $email = trim( (string)$this->setting( 'SecuritySettings', 'Email', '' ) );
        if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) )
            $email = '';
        $policy = trim( (string)$this->setting( 'SecuritySettings', 'PolicyURL', '' ) );
        if ( !preg_match( '#^https://#', $policy ) )
            $policy = '';
        $advisory = trim( (string)$this->setting( 'SecuritySettings', 'AdvisoryURL', '' ) );
        if ( !preg_match( '#^https://github\.com/[A-Za-z0-9-]+/[A-Za-z0-9_.-]+/security/advisories/new$#', $advisory ) )
            $advisory = '';
        return array( 'email' => $email, 'policy_url' => $policy, 'advisory_url' => $advisory );
    }

    /**
     * Removes what must never leave the installation: absolute paths (the install root becomes "."), e-mail
     * addresses, IP addresses, user:password@ in URLs, key=value secrets and long tokens; then cuts to $max.
     *
     * @param string $value
     * @param int $max the longest result, in characters
     * @param bool $addresses false: IPv4-like numbers (a four-part version) are kept
     * @return string
     */
    public static function scrub( $value, $max = 200, $addresses = true )
    {
        $value = (string)$value;
        if ( $value === '' )
            return '';
        $root = class_exists( 'expHelp' ) ? expHelp::installRoot() : '';
        if ( $root !== '' && $root !== '/' )
            $value = str_replace( $root, '.', $value );

        $patterns = array(
            // credentials in an address: scheme://user:password@host
            '#\b([a-z][a-z0-9+.-]*://)[^/\s:@]+(?::[^/\s@]*)?@(?:\[[^\]\s]*\]|[^/\s:?\#]+)(?::\d+)?#i' => '$1[credentials]@[host]',
            // the host of any address that is not a web page (a database, a cache, a mail server)
            '#\b((?!https?://)[a-z][a-z0-9+.-]*://)(?!\[credentials\])(?:\[[^\]\s]*\]|[^/\s:?\#\[]+)(?::\d+)?#i' => '$1[host]',
            // key=value or key: value secrets
            '/\b((?:pass(?:word|wd)?|pwd|secret|token|api[_-]?key|apikey|auth|authorization|bearer|private[_-]?key|client[_-]?secret|session(?:id)?|cookie|dsn)\s*[=:]\s*)("[^"]*"|\'[^\']*\'|\S+)/i' => '$1[redacted]',
            // e-mail addresses
            '/[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}/' => '[email]',
            // IPv4 and IPv6 addresses
            '/\b(?:\d{1,3}\.){3}\d{1,3}\b/' => '[address]',
            '/(?<![\w:])(?:(?:[0-9a-f]{1,4}:){7}[0-9a-f]{1,4}|(?:[0-9a-f]{1,4}:){1,7}:|(?:[0-9a-f]{1,4}:){1,6}:[0-9a-f]{1,4}|(?:[0-9a-f]{1,4}:){1,5}(?::[0-9a-f]{1,4}){1,2}|(?:[0-9a-f]{1,4}:){1,4}(?::[0-9a-f]{1,4}){1,3}|(?:[0-9a-f]{1,4}:){1,3}(?::[0-9a-f]{1,4}){1,4}|(?:[0-9a-f]{1,4}:){1,2}(?::[0-9a-f]{1,4}){1,5}|[0-9a-f]{1,4}:(?::[0-9a-f]{1,4}){1,6}|::(?:[0-9a-f]{1,4}:){0,6}[0-9a-f]{1,4})(?![\w:])/i' => '[address]',
            // absolute paths outside the install root (Unix, home shortcut and Windows)
            '#(?<![\w.:/~])(?:~/|/(?:var|home|root|etc|usr|opt|srv|tmp|web|mnt|data|run|private|Users|Library|Volumes)/)[^\s"\'<>|,;)]*#' => '[path]',
            '#\b[A-Za-z]:\\\\(?:[^\\\\\s]+\\\\?)+#' => '[path]',
            // long opaque tokens (keys, session ids, hashes)
            '/\b[A-Za-z0-9_\-]{32,}\b/' => '[token]',
        );
        if ( !$addresses )
            unset( $patterns['/\b(?:\d{1,3}\.){3}\d{1,3}\b/'] );
        foreach ( $patterns as $pattern => $replacement )
        {
            $result = preg_replace( $pattern, $replacement, $value );
            if ( is_string( $result ) )
                $value = $result;
        }
        return self::cut( $value, $max );
    }

    /**
     * The labels of the facts, translated for the page or in English for an issue.
     *
     * @param bool $translated
     * @return array key => label
     */
    public static function environmentLabels( $translated = true )
    {
        $labels = array(
            'exponential' => 'Exponential',
            'php' => 'PHP',
            'sapi' => 'PHP interface',
            'engine' => 'Web server',
            'os' => 'Operating system',
            'database' => 'Database',
            'siteaccess' => 'Siteaccess',
            'design' => 'Design',
            'language' => 'Language',
            'debug' => 'Debug output',
            'view_cache' => 'View cache',
            'extensions' => 'Active extensions',
            'generated' => 'Generated',
        );
        if ( $translated && class_exists( 'ezpI18n' ) )
        {
            foreach ( $labels as $key => $label )
                $labels[$key] = ezpI18n::tr( 'kernel/exphelp', $label );
        }
        return $labels;
    }

    /**
     * The repository address of a GitHub issue list ("https://github.com/<owner>/<repo>/issues").
     *
     * @param string $url
     * @return string|false
     */
    public static function repositoryFromIssuesUrl( $url )
    {
        if ( !preg_match( '#^(https://github\.com/[A-Za-z0-9](?:[A-Za-z0-9-]{0,38})/[A-Za-z0-9_.-]{1,100})/issues/?$#', trim( (string)$url ), $m ) )
            return false;
        return $m[1];
    }

    /**
     * Reads the facts of ENVIRONMENT_KEYS from the running installation. Each one is read on its own; one that
     * fails is left out.
     *
     * @return array key => string
     */
    protected function collect()
    {
        $facts = array();

        if ( class_exists( 'eZPublishSDK' ) )
            $facts['exponential'] = eZPublishSDK::version();
        elseif ( class_exists( 'ExponentialSDK' ) )
            $facts['exponential'] = ExponentialSDK::version();

        $facts['php'] = PHP_VERSION;
        $facts['sapi'] = PHP_SAPI;
        $facts['engine'] = self::engine();
        $facts['os'] = PHP_OS_FAMILY;

        // the type and server version only: never the host, the user, the password or the database's name
        try
        {
            if ( class_exists( 'eZDB' ) && eZDB::hasInstance() )
            {
                $db = eZDB::instance();
                $type = (string)$db->databaseName();
                $version = $db->databaseServerVersion();
                if ( is_array( $version ) && isset( $version['string'] ) )
                    $version = $version['string'];
                $facts['database'] = trim( $type . ( is_string( $version ) && $version !== '' ? ' ' . $version : '' ) );
            }
        }
        catch ( Throwable $e )
        {
        }

        if ( class_exists( 'eZSiteAccess' ) )
        {
            $access = eZSiteAccess::current();
            if ( is_array( $access ) && isset( $access['name'] ) )
                $facts['siteaccess'] = (string)$access['name'];
        }

        if ( class_exists( 'eZINI' ) )
        {
            $site = eZINI::instance();
            $designs = array();
            if ( $site->hasVariable( 'DesignSettings', 'SiteDesign' ) )
                $designs[] = (string)$site->variable( 'DesignSettings', 'SiteDesign' );
            if ( $site->hasVariable( 'DesignSettings', 'AdditionalSiteDesignList' ) )
                $designs = array_merge( $designs, array_map( 'strval', (array)$site->variable( 'DesignSettings', 'AdditionalSiteDesignList' ) ) );
            $facts['design'] = implode( ', ', array_unique( array_filter( $designs, 'strlen' ) ) );
            if ( $site->hasVariable( 'RegionalSettings', 'ContentObjectLocale' ) )
                $facts['language'] = (string)$site->variable( 'RegionalSettings', 'ContentObjectLocale' );
            if ( $site->hasVariable( 'DebugSettings', 'DebugOutput' ) )
                $facts['debug'] = (string)$site->variable( 'DebugSettings', 'DebugOutput' );
            if ( $site->hasVariable( 'ContentSettings', 'ViewCaching' ) )
                $facts['view_cache'] = (string)$site->variable( 'ContentSettings', 'ViewCaching' );
        }

        $facts['extensions'] = $this->extensionSummary();
        $facts['generated'] = gmdate( 'Y-m-d H:i' ) . ' UTC';
        return $facts;
    }

    /**
     * "name version, name version, ... and N more" for the active extensions, at most
     * [IssueTrackers] ExtensionListLimit names.
     *
     * @return string
     */
    protected function extensionSummary()
    {
        $limit = max( 0, (int)$this->setting( 'IssueTrackers', 'ExtensionListLimit', 60 ) );
        $items = array();
        foreach ( $this->extensionInfos() as $name => $info )
        {
            $version = isset( $info['version'] ) && is_string( $info['version'] ) ? trim( $info['version'] ) : '';
            $items[] = $name . ( $version !== '' ? ' ' . $version : '' );
        }
        $total = count( $items );
        if ( $total === 0 )
            return '';
        $shown = array_slice( $items, 0, $limit );
        $text = $total . ': ' . implode( ', ', $shown );
        if ( $total > count( $shown ) )
            $text .= ' ' . sprintf( '(and %d more)', $total - count( $shown ) );
        return $text;
    }

    /**
     * The info of every active extension (eZExtension::extensionInfo()), read once per instance.
     *
     * @return array name => info array
     */
    protected function extensionInfos()
    {
        if ( $this->infos !== null )
            return $this->infos;
        if ( $this->values !== null )
            return isset( $this->settings['extension_infos'] ) && is_array( $this->settings['extension_infos'] ) ? $this->settings['extension_infos'] : array();
        $infos = array();
        if ( class_exists( 'eZExtension' ) )
        {
            foreach ( eZExtension::activeExtensions() as $name )
            {
                $info = null;
                try
                {
                    $info = eZExtension::extensionInfo( $name );
                }
                catch ( Throwable $e )
                {
                }
                $infos[(string)$name] = is_array( $info ) ? array_change_key_case( $info, CASE_LOWER ) : array();
            }
        }
        // kept by this instance only: a persistent worker reads the list again on the next request
        return $this->infos = $infos;
    }

    /**
     * The engine that serves this request, in words: Exponential Velocity (with its version and engine),
     * FrankenPHP, PHP-FPM, Apache with mod_php, PHP's built-in server or the command line.
     *
     * @return string
     */
    protected static function engine()
    {
        if ( defined( 'QBIX_SERVER_VERSION' ) )
        {
            $version = function_exists( 'qbix_version_label' ) ? (string)qbix_version_label()
                     : ( defined( 'QBIX_SHIP_VERSION' ) ? (string)QBIX_SHIP_VERSION : '' );
            if ( strncmp( $version, 'v0.', 3 ) !== 0 && class_exists( '\Composer\InstalledVersions' ) )
            {
                foreach ( array( 'se7enxweb/exponential-velocity', 'se7enxweb/qbix-webserver' ) as $package )
                {
                    if ( \Composer\InstalledVersions::isInstalled( $package ) )
                    {
                        $version = (string)\Composer\InstalledVersions::getPrettyVersion( $package );
                        break;
                    }
                }
            }
            return trim( 'Exponential Velocity ' . $version );
        }
        $velocityEngine = getenv( 'EXP_VELOCITY_ENGINE' );
        $byVelocity = is_string( $velocityEngine ) && $velocityEngine !== '' ? ' (exp:velocity ' . $velocityEngine . ')' : '';
        switch ( PHP_SAPI )
        {
            case 'frankenphp':
                return 'FrankenPHP' . $byVelocity;
            case 'fpm-fcgi':
                return 'PHP-FPM' . ( isset( $_SERVER['SERVER_SOFTWARE'] ) && stripos( (string)$_SERVER['SERVER_SOFTWARE'], 'nginx' ) !== false ? ' (nginx)'
                                   : ( isset( $_SERVER['SERVER_SOFTWARE'] ) && stripos( (string)$_SERVER['SERVER_SOFTWARE'], 'apache' ) !== false ? ' (Apache)' : '' ) );
            case 'apache2handler':
                return 'Apache (mod_php)';
            case 'cli-server':
                return 'PHP built-in web server' . $byVelocity;
            case 'cli':
                return 'Command line';
        }
        return PHP_SAPI;
    }

    /**
     * A setting from the injected settings, else exphelp.ini through expHelp::setting().
     *
     * @param string $group
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    protected function setting( $group, $name, $default = null )
    {
        if ( $this->settings !== null )
            return array_key_exists( $group . '/' . $name, $this->settings ) ? $this->settings[$group . '/' . $name] : $default;
        return expHelp::setting( $group, $name, $default );
    }

    /**
     * Cuts a string to $max characters, with an ellipsis when it was longer.
     *
     * @param string $value
     * @param int $max
     * @return string
     */
    protected static function cut( $value, $max )
    {
        $value = (string)$value;
        if ( $max > 0 && mb_strlen( $value, 'UTF-8' ) > $max )
            return rtrim( mb_substr( $value, 0, max( 1, $max - 1 ), 'UTF-8' ) ) . "\u{2026}";
        return $value;
    }
}

?>
