<?php
/**
 * Shared code of the audit sinks: the sink's settings block ([AuditSink_<name>]), which records it wants
 * (Events[] patterns and MinSeverity) and whether it delivers at flush time or from the spool.
 *
 * A sink of an extension may extend this class, or implement expAuditSink alone (it is then fed at flush time
 * with every record routed to it).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

abstract class expAuditSinkBase implements expAuditSink
{
    /** @var string */
    protected $name;

    /** @var array The [AuditSink_<name>] block, variable => value */
    protected $settings;

    /**
     * @param string $name the registry name
     * @param array|null $settings null: read [AuditSink_<name>]
     */
    public function __construct( $name, ?array $settings = null )
    {
        $this->name = $name;
        $this->settings = ( $settings !== null ? $settings : expAuditConfig::block( 'AuditSink_' . $name ) ) + $this->defaults();
    }

    /** @return array The block's defaults */
    protected function defaults()
    {
        return array( 'Events' => array(), 'MinSeverity' => 'info' );
    }

    public function name()
    {
        return $this->name;
    }

    /**
     * @param string $var
     * @return mixed
     */
    public function setting( $var )
    {
        return isset( $this->settings[$var] ) ? $this->settings[$var] : null;
    }

    /**
     * Whether a record is for this sink: Events[] (empty = everything routed here) or MinSeverity.
     * A record matching Events[] is taken whatever its severity, any other one when it reaches MinSeverity.
     *
     * @param array $record
     * @return bool
     */
    public function wants( array $record )
    {
        $events = array_values( array_filter( array_map( 'trim', (array)$this->setting( 'Events' ) ), 'strlen' ) );
        $name = isset( $record['name'] ) ? $record['name'] : '';
        $severity = isset( $record['severity'] ) ? $record['severity'] : 'info';
        $min = trim( (string)$this->setting( 'MinSeverity' ) );
        if ( $events && expAuditTaxonomy::bestMatch( $events, $name ) >= 0 )
            return true;
        return expAuditTaxonomy::rank( $severity ) <= expAuditTaxonomy::rank( $min !== '' ? $min : 'info' );
    }

    /**
     * true: records are appended to the spool at flush time and delivered by the cronjob part; false: delivered
     * at flush time (a local socket write).
     *
     * @return bool
     */
    public function isSpooled()
    {
        return true;
    }

    /**
     * Records sent by the spool runner (the cronjob part): the batch size to hand to deliver().
     *
     * @return int
     */
    public function batchSize()
    {
        return 100;
    }
}
