<?php
/**
 * File containing the expContentSecurityPolicyReport class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The reports of the Content Security Policy (doc/features/6.0/content-security-policy.md, "Reports"): what the
 * view csp/report receives from the browser, read and written to var/log/csp.log, one line per violation.
 *
 * - Both formats: application/csp-report (report-uri: {"csp-report": {...}}) and application/reports+json (the
 *   Reporting API, Reporting-Endpoints and report-to: a list of reports, of which those of type csp-violation are
 *   read). application/json is read as either.
 * - Nothing is answered back but a status: 204 for a report taken (also one without a violation in it), 405 for
 *   another method than POST, 415 for another content type, 413 for a body over MaxReportBytes, 400 for a body that
 *   is not a report, 429 over the rate limits. No body, so nothing of the report or the installation leaks.
 * - Rate limited per client (ReportsPerMinute, counted by the salted address hash of the request classifier) and
 *   in total (ReportsPerMinuteTotal), with expRequestThresholdCounter in the classifier's cache directory. The first
 *   refusal of a client in a minute is written to security.log by the request classifier, as abuse/csp-report-flood.
 * - Every value is bounded and masked before it is written: URIs through expRequestClassifier::uriForLog() (the
 *   query's secrets, e-mail and IP addresses masked, capped), the address masked as the request logs mask it, the
 *   sample cut to 40 characters without quotes or control characters, at most MaxViolationsPerReport lines per
 *   report.
 *
 * A line, after the eZLog prefix "[ Oct 17 2026 12:00:00 ] [203.0.x.x] ":
 *
 *   csp/<directive> blocked=<blocked> disposition=<enforce|report> document=<uri> siteaccess=<sa> line=<n>
 *   column=<n> source=<uri> status=<n> sample="<sample>" ua=<hash> "<user agent>"
 *
 * <blocked> is a keyword (inline, eval, wasm-eval, data, blob, self, trusted-types-policy, ...), or the blocked URI.
 * The log viewer reads the line (expLogParser: source csp/<directive>, request the document), its digest groups
 * the lines by directive and blocked origin (expLogDigest grouping directive-blocked).
 *
 * Settings: site.ini [ContentSecurityPolicy] ReportsPerMinute, ReportsPerMinuteTotal, MaxReportBytes,
 * MaxViolationsPerReport, LogFile. The policy points browsers here with ReportURI or ReportEndpoint.
 */
class expContentSecurityPolicyReport
{
    /** The content types taken (the media type, without parameters) */
    const CONTENT_TYPES = array( 'application/csp-report', 'application/reports+json', 'application/json' );

    /** The defaults of the settings */
    const DEFAULTS = array(
        'ReportsPerMinute' => 60,
        'ReportsPerMinuteTotal' => 600,
        'MaxReportBytes' => 65536,
        'MaxViolationsPerReport' => 20,
        'LogFile' => 'csp.log',
    );

    /** The longest sample kept (browsers send 40 characters) */
    const MAX_SAMPLE = 40;

    /** The longest URI kept (document, blocked, source) */
    const MAX_URI = 300;

    /** @var array the settings: the keys of DEFAULTS, plus logDir, cacheDir and maskIP */
    protected $settings;

    /**
     * @param array|null $settings null: from site.ini [ContentSecurityPolicy] (see settingsFromINI()); an array
     *        (tests) holds keys of DEFAULTS and logDir (the directory of the log), cacheDir (of the counters), maskIP
     */
    public function __construct( ?array $settings = null )
    {
        $this->settings = self::cleanSettings( $settings === null ? self::settingsFromINI() : $settings );
    }

    /**
     * The settings of site.ini [ContentSecurityPolicy] for the report.
     *
     * @return array
     */
    public static function settingsFromINI()
    {
        $settings = array();
        try
        {
            $ini = eZINI::instance();
            foreach ( array_keys( self::DEFAULTS ) as $name )
            {
                if ( $ini->hasVariable( 'ContentSecurityPolicy', $name ) )
                    $settings[$name] = $ini->variable( 'ContentSecurityPolicy', $name );
            }
            if ( $ini->hasVariable( 'RequestLogSettings', 'MaskIP' ) )
                $settings['maskIP'] = $ini->variable( 'RequestLogSettings', 'MaskIP' ) !== 'disabled';
        }
        catch ( Throwable $ignored )
        {
        }
        return $settings;
    }

    /**
     * The settings with their defaults and bounds.
     *
     * @param array $settings
     * @return array
     */
    public static function cleanSettings( array $settings )
    {
        $bounds = array( 'ReportsPerMinute' => array( 1, 100000 ), 'ReportsPerMinuteTotal' => array( 1, 1000000 ),
                         'MaxReportBytes' => array( 1024, 1048576 ), 'MaxViolationsPerReport' => array( 1, 500 ) );
        $clean = array();
        foreach ( $bounds as $name => $range )
        {
            $value = isset( $settings[$name] ) && is_numeric( $settings[$name] ) ? (int)$settings[$name] : self::DEFAULTS[$name];
            $clean[$name] = max( $range[0], min( $range[1], $value ) );
        }
        // a file name in the log directory, never a path
        $file = isset( $settings['LogFile'] ) && is_scalar( $settings['LogFile'] ) ? basename( trim( (string)$settings['LogFile'] ) ) : '';
        $clean['LogFile'] = preg_match( '/^[A-Za-z0-9_.\-]+\.log$/D', $file ) ? $file : self::DEFAULTS['LogFile'];
        $clean['logDir'] = isset( $settings['logDir'] ) ? (string)$settings['logDir'] : null;
        $clean['cacheDir'] = isset( $settings['cacheDir'] ) ? (string)$settings['cacheDir'] : null;
        $clean['maskIP'] = isset( $settings['maskIP'] ) ? (bool)$settings['maskIP'] : true;
        return $clean;
    }

    /**
     * @return array the settings in use
     */
    public function settings()
    {
        return $this->settings;
    }

    /**
     * The media type of a Content-Type header, in lower case, without parameters.
     *
     * @param mixed $header
     * @return string
     */
    public static function mediaType( $header )
    {
        $header = is_scalar( $header ) ? (string)$header : '';
        $semicolon = strpos( $header, ';' );
        if ( $semicolon !== false )
            $header = substr( $header, 0, $semicolon );
        return strtolower( trim( $header ) );
    }

    /**
     * Whether a request with this Content-Type header is a report the view takes.
     *
     * @param mixed $contentType
     * @return bool
     */
    public static function isReportContentType( $contentType )
    {
        return in_array( self::mediaType( $contentType ), self::CONTENT_TYPES, true );
    }

    /**
     * The violations of a report body, normalized: a list of arrays with the keys directive, disposition, blocked,
     * document, line, column, source, status, sample (strings and integers, not yet bounded for the log). Null when
     * the body is not a report of either format (not JSON, or neither shape). Reports of the Reporting API of
     * another type (deprecation, intervention ...) are skipped.
     *
     * @param string $body
     * @return array[]|null
     */
    public static function parse( $body )
    {
        $data = json_decode( (string)$body, true, 16 );
        if ( !is_array( $data ) )
            return null;

        // report-uri: {"csp-report": {...}}
        if ( isset( $data['csp-report'] ) )
        {
            if ( !is_array( $data['csp-report'] ) )
                return null;
            $r = $data['csp-report'];
            return array( self::violation(
                self::firstOf( $r, array( 'effective-directive', 'violated-directive' ) ),
                self::firstOf( $r, array( 'disposition' ) ),
                self::firstOf( $r, array( 'blocked-uri' ) ),
                self::firstOf( $r, array( 'document-uri' ) ),
                self::firstOf( $r, array( 'line-number' ) ),
                self::firstOf( $r, array( 'column-number' ) ),
                self::firstOf( $r, array( 'source-file' ) ),
                self::firstOf( $r, array( 'status-code' ) ),
                self::firstOf( $r, array( 'script-sample', 'sample' ) )
            ) );
        }

        // the Reporting API: [ {"type": "csp-violation", "url": ..., "body": {...}}, ... ]
        if ( !array_is_list( $data ) )
            $data = array( $data );
        $violations = array();
        $sawReport = false;
        foreach ( $data as $report )
        {
            if ( !is_array( $report ) || !isset( $report['type'] ) || !is_string( $report['type'] ) )
                continue;
            $sawReport = true;
            if ( $report['type'] !== 'csp-violation' || !isset( $report['body'] ) || !is_array( $report['body'] ) )
                continue;
            $b = $report['body'];
            $violations[] = self::violation(
                self::firstOf( $b, array( 'effectiveDirective', 'violatedDirective' ) ),
                self::firstOf( $b, array( 'disposition' ) ),
                self::firstOf( $b, array( 'blockedURL', 'blockedURI' ) ),
                self::firstOf( $b, array( 'documentURL', 'documentURI' ), isset( $report['url'] ) ? $report['url'] : '' ),
                self::firstOf( $b, array( 'lineNumber' ) ),
                self::firstOf( $b, array( 'columnNumber' ) ),
                self::firstOf( $b, array( 'sourceFile' ) ),
                self::firstOf( $b, array( 'statusCode' ) ),
                self::firstOf( $b, array( 'sample' ) )
            );
        }
        return $sawReport ? $violations : null;
    }

    /**
     * The first scalar value of these keys in $values.
     *
     * @param array $values
     * @param string[] $keys
     * @param mixed $default
     * @return string
     */
    protected static function firstOf( array $values, array $keys, $default = '' )
    {
        foreach ( $keys as $key )
        {
            if ( isset( $values[$key] ) && is_scalar( $values[$key] ) && (string)$values[$key] !== '' )
                return (string)$values[$key];
        }
        return is_scalar( $default ) ? (string)$default : '';
    }

    /**
     * One violation, its values as the browser sent them (strings), the numbers as integers.
     *
     * @return array
     */
    protected static function violation( $directive, $disposition, $blocked, $document, $line, $column, $source, $status, $sample )
    {
        // violated-directive of CSP 2 is the directive with its sources ("script-src 'self'"): the name is the first word
        $directive = strtok( trim( (string)$directive ), " \t" );
        return array(
            'directive' => $directive === false ? '' : $directive,
            'disposition' => (string)$disposition,
            'blocked' => (string)$blocked,
            'document' => (string)$document,
            'line' => is_numeric( $line ) ? (int)$line : 0,
            'column' => is_numeric( $column ) ? (int)$column : 0,
            'source' => (string)$source,
            'status' => is_numeric( $status ) ? (int)$status : 0,
            'sample' => (string)$sample,
        );
    }

    /**
     * The log line of one violation, without the eZLog prefix. Every value is bounded and masked; none can split
     * the line or add a field of its own.
     *
     * @param array $violation one of parse()
     * @param string $siteaccess
     * @param string $userAgent
     * @return string
     */
    public static function line( array $violation, $siteaccess, $userAgent )
    {
        $directive = strtolower( (string)$violation['directive'] );
        $directive = preg_match( '/^[a-z][a-z0-9\-]{0,39}$/D', $directive ) ? $directive : 'unknown';
        $disposition = strtolower( (string)$violation['disposition'] ) === 'enforce' ? 'enforce' : 'report';
        $siteaccess = substr( preg_replace( '/[^A-Za-z0-9_.\-]/', '', (string)$siteaccess ), 0, 64 );
        return 'csp/' . $directive
            . ' blocked=' . self::blockedForLog( $violation['blocked'] )
            . ' disposition=' . $disposition
            . ' document=' . self::uriField( $violation['document'] )
            . ' siteaccess=' . ( $siteaccess !== '' ? $siteaccess : '-' )
            . ' line=' . max( 0, (int)$violation['line'] )
            . ' column=' . max( 0, (int)$violation['column'] )
            . ' source=' . self::uriField( $violation['source'] )
            . ' status=' . max( 0, min( 999, (int)$violation['status'] ) )
            . ' sample="' . self::sampleForLog( $violation['sample'] ) . '"'
            . ' ' . expRequestClassifier::userAgentForLog( $userAgent );
    }

    /**
     * The blocked value for the log: a keyword as it is (inline, eval, data ...; CSP 2 browsers send "data:" and
     * "blob:" as the scheme, which is the keyword here), "-" for none, else the URI as uriField() writes it.
     *
     * @param string $blocked
     * @return string
     */
    public static function blockedForLog( $blocked )
    {
        $blocked = trim( (string)$blocked );
        if ( $blocked === '' )
            return '-';
        if ( preg_match( '/^([a-z][a-z0-9\-]{0,39}):?$/D', $blocked, $m ) )
            return $m[1];
        // a data: or blob: URI is its scheme only: the data itself is never written
        if ( preg_match( '/^(data|blob|filesystem|javascript|about|chrome-extension|moz-extension|safari-extension):/i', $blocked, $m ) )
            return strtolower( $m[1] );
        return self::uriField( $blocked );
    }

    /**
     * A URI for a field of the line: the fragment and any user name or password taken off, then
     * expRequestClassifier::uriForLog() (query masked, control characters out, spaces and quotes encoded, capped);
     * "-" for none.
     *
     * @param string $uri
     * @return string
     */
    public static function uriField( $uri )
    {
        $uri = trim( (string)$uri );
        if ( $uri === '' )
            return '-';
        $hash = strpos( $uri, '#' );
        if ( $hash !== false )
            $uri = substr( $uri, 0, $hash );
        $uri = preg_replace( '#^([a-z][a-z0-9+.\-]*://)[^/@?]*@#i', '$1', $uri );
        return expRequestClassifier::uriForLog( $uri, self::MAX_URI );
    }

    /**
     * The sample for the log: at most MAX_SAMPLE characters, without quotes, backslashes or control characters.
     *
     * @param string $sample
     * @return string
     */
    public static function sampleForLog( $sample )
    {
        $sample = preg_replace( '/[\x00-\x1f\x7f"\\\\]+/', ' ', (string)$sample );
        if ( function_exists( 'mb_substr' ) )
            $sample = mb_substr( $sample, 0, self::MAX_SAMPLE, 'UTF-8' );
        else
            $sample = substr( $sample, 0, self::MAX_SAMPLE );
        // invalid UTF-8 is dropped rather than written
        if ( function_exists( 'mb_convert_encoding' ) )
            $sample = mb_convert_encoding( $sample, 'UTF-8', 'UTF-8' );
        return trim( $sample );
    }

    /**
     * What a request leads to, from its method, content type, body and client: the status to answer, and the lines
     * written. The counters are touched only for a POST of a report content type, so a stray GET costs nothing.
     *
     * @param string $method
     * @param string $contentType the Content-Type header
     * @param string|null $body the body, at most MaxReportBytes + 1 bytes of it; null: too big to read
     * @param expRequestContext $context the request (address, user agent, siteaccess)
     * @param int|null $now
     * @return array array( 'status' => int, 'reason' => string, 'lines' => int )
     */
    public function handle( $method, $contentType, $body, expRequestContext $context, $now = null )
    {
        if ( strtoupper( (string)$method ) !== 'POST' )
            return array( 'status' => 405, 'reason' => 'method', 'lines' => 0 );
        if ( !self::isReportContentType( $contentType ) )
            return array( 'status' => 415, 'reason' => 'content-type', 'lines' => 0 );

        $limit = $this->limit( $context, $now );
        if ( $limit !== '' )
            return array( 'status' => 429, 'reason' => $limit, 'lines' => 0 );

        if ( $body === null || strlen( (string)$body ) > $this->settings['MaxReportBytes'] )
            return array( 'status' => 413, 'reason' => 'too-large', 'lines' => 0 );
        $violations = self::parse( (string)$body );
        if ( $violations === null )
            return array( 'status' => 400, 'reason' => 'not-a-report', 'lines' => 0 );

        $written = 0;
        $prefix = expRequestClassifier::prefix( $context->ip, $this->settings['maskIP'], $now );
        foreach ( array_slice( $violations, 0, $this->settings['MaxViolationsPerReport'] ) as $violation )
        {
            if ( expRequestClassifier::appendLine( $this->logFile(), $prefix . self::line( $violation, $context->siteaccess, $context->userAgent ) ) )
                $written++;
        }
        return array( 'status' => 204, 'reason' => 'ok', 'lines' => $written );
    }

    /**
     * Counts the report and says whether it is over a limit: '' when not, 'client' over ReportsPerMinute of its
     * address, 'total' over ReportsPerMinuteTotal. The first refusal of a client in a minute is written to
     * security.log (abuse/csp-report-flood) by the request classifier; the following ones are not, so a flood
     * writes one line a minute, not one per request.
     *
     * @param expRequestContext $context
     * @param int|null $now
     * @return string
     */
    public function limit( expRequestContext $context, $now = null )
    {
        $classifier = expRequestClassifier::instance();
        $counter = new expRequestThresholdCounter( $this->cacheDirectory(), 60 );
        $client = $counter->hit( $classifier->ipKey( $context->ip ), $now );
        if ( $client > $this->settings['ReportsPerMinute'] )
        {
            if ( $client === $this->settings['ReportsPerMinute'] + 1 )
                $this->flagged( $context, 'client' );
            return 'client';
        }
        $total = $counter->hit( 'total', $now );
        if ( $total > $this->settings['ReportsPerMinuteTotal'] )
        {
            if ( $total === $this->settings['ReportsPerMinuteTotal'] + 1 )
                $this->flagged( $context, 'total' );
            return 'total';
        }
        return '';
    }

    /**
     * Writes the security.log line of a client over the limit, through the request classifier (its listeners see
     * it too). A classifier that is disabled writes nothing.
     *
     * @param expRequestContext $context
     * @param string $which client | total
     * @return void
     */
    protected function flagged( expRequestContext $context, $which )
    {
        try
        {
            $classifier = expRequestClassifier::instance();
            if ( !$classifier->enabled() )
                return;
            $verdict = new expRequestVerdict( 'abuse', 'csp-report-flood', expRequestVerdict::MEDIUM, 'limit=' . $which );
            $verdict->ruleSet = 'csp-report';
            $context->status = 429;
            $classifier->write( $verdict, $context );
        }
        catch ( Throwable $e )
        {
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeWarning( 'The CSP report flood could not be classified: ' . $e->getMessage(), __METHOD__ );
        }
    }

    /**
     * The path of the log: LogFile in eZDebug's log directory (var/log by default).
     *
     * @return string
     */
    public function logFile()
    {
        if ( $this->settings['logDir'] !== null && $this->settings['logDir'] !== '' )
            $dir = rtrim( $this->settings['logDir'], '/' );
        else
            $dir = class_exists( 'eZDebug' ) ? rtrim( eZDebug::instance()->logDirectory(), '/' ) : 'var/log';
        return $dir . '/' . $this->settings['LogFile'];
    }

    /**
     * The directory of the counters: csp-report in the request classifier's cache directory.
     *
     * @return string
     */
    public function cacheDirectory()
    {
        if ( $this->settings['cacheDir'] !== null && $this->settings['cacheDir'] !== '' )
            return rtrim( $this->settings['cacheDir'], '/' );
        return expRequestClassifier::instance()->cacheDirectory() . '/csp-report';
    }

    /**
     * Reads the body of the current request, at most $max + 1 bytes (php://input; Velocity serves it per request).
     *
     * @param int $max
     * @return string|null null when it holds more than $max bytes (Content-Length says so, or reading does)
     */
    public static function readBody( $max )
    {
        $max = (int)$max;
        $length = isset( $_SERVER['CONTENT_LENGTH'] ) ? $_SERVER['CONTENT_LENGTH']
                : ( isset( $_SERVER['HTTP_CONTENT_LENGTH'] ) ? $_SERVER['HTTP_CONTENT_LENGTH'] : null );
        if ( is_numeric( $length ) && (int)$length > $max )
            return null;
        $fp = @fopen( 'php://input', 'rb' );
        if ( !$fp )
            return '';
        $body = (string)stream_get_contents( $fp, $max + 1 );
        fclose( $fp );
        return strlen( $body ) > $max ? null : $body;
    }

    /**
     * The Content-Type header of the current request.
     *
     * @return string
     */
    public static function requestContentType()
    {
        if ( isset( $_SERVER['CONTENT_TYPE'] ) )
            return (string)$_SERVER['CONTENT_TYPE'];
        return isset( $_SERVER['HTTP_CONTENT_TYPE'] ) ? (string)$_SERVER['HTTP_CONTENT_TYPE'] : '';
    }
}
