<?php
/**
 * File containing the expViewCountSite class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One siteaccess whose views are counted, with what the view counter needs to know of it: its access log, how its
 * paths reach nodes and which lines of a shared log are its own. expViewCountConfiguration makes them from the
 * siteaccess's logfile.ini and site.ini; tests make them by hand.
 */
class expViewCountSite
{
    /** @var string the siteaccess name (the key of its resume state) */
    public $name = '';

    /** @var string the access log: logfile.ini [AccessLogFileSettings] StorageDir/LogFileName */
    public $logFile = '';

    /** @var string[] [AccessLogFileSettings] SitePrefix[]: a first path element removed (when the log is not matched) */
    public $sitePrefixes = array();

    /** @var string[] [AccessLogFileSettings] PathPrefix[], else site.ini [SiteAccessSettings] PathPrefix */
    public $pathPrefixes = array();

    /** @var string[] site.ini [SiteAccessSettings] PathPrefixExclude[] */
    public $pathPrefixExclude = array();

    /** @var string site.ini [SiteSettings] IndexPage */
    public $indexPage = '';

    /** @var int|null a fixed node for "/" (tests, or a resolved IndexPage); null: from $indexPage */
    public $indexNodeID = null;

    /** @var string[] site.ini [RegionalSettings] SiteLanguageList[]: the languages of its URL aliases */
    public $languages = array();

    /** @var string [AccessLogFileSettings] Host: the host of the log's requests when a line names none */
    public $host = '';

    /** @var int|null [AccessLogFileSettings] Port: the port of the log's requests when a line names none */
    public $port = null;

    /** @var string[] [AccessLogFileSettings] Hosts[]: only lines of these hosts (lower case); empty: every host */
    public $hosts = array();

    /**
     * @param array $properties property => value
     */
    public function __construct( array $properties = array() )
    {
        foreach ( $properties as $name => $value )
        {
            if ( !property_exists( $this, $name ) )
                throw new InvalidArgumentException( "expViewCountSite has no property $name" );
            $this->$name = $value;
        }
        $this->hosts = array_values( array_filter( array_map( 'strtolower', array_map( 'trim', array_map( 'strval', $this->hosts ) ) ), 'strlen' ) );
    }

    /**
     * Whether a line of this host belongs to the site: Hosts[] is empty, or names it (a line that names no host takes
     * the log's Host).
     *
     * @param string $host lower case, '' when unknown
     * @return bool
     */
    public function acceptsHost( $host )
    {
        if ( !$this->hosts )
            return true;
        $host = $host !== '' ? $host : strtolower( $this->host );
        return $host !== '' && in_array( $host, $this->hosts, true );
    }
}
