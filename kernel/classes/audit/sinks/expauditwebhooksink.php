<?php
/**
 * The webhook sink (doc/bc/6.0/audit.md, "Webhook"): POSTs batches of records as JSON,
 *
 *   {"v":1,"installation":"6f1c…","site":"example","batch":"b-…","events":[<record>,…]}
 *
 * with the headers X-Exponential-Timestamp (epoch seconds), X-Exponential-Batch and
 * X-Exponential-Signature: sha256=<hex HMAC-SHA-256(SigningSecret, timestamp + "." + body)>. A receiver checks the
 * signature and rejects timestamps more than 300 seconds away (verify() below is that check). A 2xx answer
 * delivers the batch. Records always go through the spool (a request never waits for the network); the audit
 * cronjob part delivers it with retries (expAuditSinkRegistry::deliverSpool()). The batch id is derived from
 * the first event id and the count, so a retried batch keeps its id; delivery is at least once and receivers
 * de-duplicate by event id.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditWebhookSink extends expAuditSinkBase
{
    /** @var int|null HTTP status of the last POST */
    public $lastStatus = null;

    /** @var string|null */
    public $lastError = null;

    /** @var int|null Fixed timestamp (tests) */
    public $timestamp = null;

    protected function defaults()
    {
        return array( 'URL' => '', 'SigningSecret' => '', 'BatchSize' => '100', 'BatchSeconds' => '10', 'Timeout' => '5',
                      'Retries' => '5', 'RetryBackoff' => '30', 'Events' => array(), 'MinSeverity' => 'notice' );
    }

    public function problem()
    {
        $url = trim( (string)$this->setting( 'URL' ) );
        if ( $url === '' )
            return 'no URL is set ([AuditSink_webhook] URL)';
        if ( !preg_match( '#^https?://#i', $url ) )
            return 'the URL must start with http:// or https://';
        if ( !function_exists( 'curl_init' ) )
            return 'the PHP extension curl is missing';
        if ( trim( (string)$this->setting( 'SigningSecret' ) ) === '' )
            return 'no SigningSecret is set: batches would be unsigned';
        return '';
    }

    public function batchSize()
    {
        return max( 1, (int)$this->setting( 'BatchSize' ) );
    }

    /**
     * POSTs one batch.
     *
     * @param array[] $records
     * @return int the count delivered: all of them on a 2xx answer, else 0
     */
    public function deliver( array $records )
    {
        $this->lastError = null;
        $this->lastStatus = null;
        if ( !$records )
            return 0;
        $url = trim( (string)$this->setting( 'URL' ) );
        if ( $url === '' || !function_exists( 'curl_init' ) )
        {
            $this->lastError = $this->problem();
            return 0;
        }
        list( $body, $headers ) = $this->request( $records );
        $ch = curl_init( $url );
        $timeout = max( 1, (int)$this->setting( 'Timeout' ) );
        curl_setopt_array( $ch, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => min( $timeout, 5 ),
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        ) );
        curl_exec( $ch );
        $this->lastStatus = (int)curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
        if ( curl_errno( $ch ) )
            $this->lastError = curl_error( $ch );
        elseif ( $this->lastStatus < 200 || $this->lastStatus > 299 )
            $this->lastError = 'HTTP ' . $this->lastStatus;
        unset( $ch ); // the handle is freed with the object (curl_close() has had no effect since PHP 8.0)
        return $this->lastError === null ? count( $records ) : 0;
    }

    /**
     * The body and headers of a batch.
     *
     * @param array[] $records
     * @return array( body, headers[] )
     */
    public function request( array $records )
    {
        $events = array();
        foreach ( $records as $r )
        {
            unset( $r['file'] );
            $events[] = $r;
        }
        $batch = 'b-' . ( isset( $events[0]['id'] ) ? $events[0]['id'] : expAudit::ulid() ) . '-' . count( $events );
        $installation = '';
        try
        {
            $keys = new expAuditKeys( expAuditConfig::get() );
            $installation = $keys->installationId();
        }
        catch ( Throwable $e )
        {
        }
        $body = expAuditJson::encode( array( 'v' => 1, 'installation' => $installation, 'site' => self::siteName(),
                                             'batch' => $batch, 'events' => $events ) );
        $ts = $this->timestamp !== null ? (int)$this->timestamp : time();
        $headers = array( 'Content-Type: application/json', 'User-Agent: Exponential-Audit/1',
                          'X-Exponential-Timestamp: ' . $ts, 'X-Exponential-Batch: ' . $batch );
        $secret = (string)$this->setting( 'SigningSecret' );
        if ( $secret !== '' )
            $headers[] = 'X-Exponential-Signature: ' . self::signature( $secret, $ts, $body );
        return array( $body, $headers );
    }

    /** @return string sha256=<hex HMAC-SHA-256( secret, timestamp + "." + body )> */
    public static function signature( $secret, $timestamp, $body )
    {
        return 'sha256=' . hash_hmac( 'sha256', $timestamp . '.' . $body, $secret );
    }

    /**
     * The receiver's check: the signature matches and the timestamp is within $tolerance seconds.
     *
     * @param string $secret
     * @param string $timestamp the X-Exponential-Timestamp header
     * @param string $body
     * @param string $signature the X-Exponential-Signature header
     * @param int $tolerance
     * @param int|null $now
     * @return string '' when valid, else why not (bad_signature, stale_timestamp)
     */
    public static function verify( $secret, $timestamp, $body, $signature, $tolerance = 300, $now = null )
    {
        $now = $now === null ? time() : $now;
        if ( !ctype_digit( (string)$timestamp ) || abs( $now - (int)$timestamp ) > $tolerance )
            return 'stale_timestamp';
        return hash_equals( self::signature( $secret, $timestamp, $body ), (string)$signature ) ? '' : 'bad_signature';
    }

    /** @return string The site's name (site.ini [SiteSettings] SiteName), '' when unknown */
    public static function siteName()
    {
        try
        {
            if ( class_exists( 'eZINI' ) && !expAuditConfig::isOverridden() )
                return (string)eZINI::instance()->variable( 'SiteSettings', 'SiteName' );
        }
        catch ( Throwable $e )
        {
        }
        return '';
    }
}
