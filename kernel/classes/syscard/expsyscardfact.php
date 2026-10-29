<?php
/**
 * File containing the expSysCardFact class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One line of the system card (exp:expfetch, exp:info, exp:about): a label, a value, a severity and the section it
 * belongs to. A fact with a hint adds a line to the "Hints" block at the end of the card.
 *
 *   new expSysCardFact( 'php.opcache', 'OPcache', 'off', expSysCardFact::WARN, 'php', 'Turn OPcache on: ...' );
 *   expSysCardFact::text( 'about.what', 'Exponential is ...', 'about' );   // a paragraph, wrapped to the width
 *
 * A value is a string. Facts are data only: the renderer decides how they look.
 */
class expSysCardFact
{
    const OK = 'ok';
    const INFO = 'info';
    const WARN = 'warn';
    const ERROR = 'error';

    /** A row: label and value */
    const KIND_ROW = 'row';
    /** A paragraph: the value only, wrapped */
    const KIND_TEXT = 'text';

    /** @var string a stable id, "<provider>.<name>" (the JSON key) */
    public $key;
    /** @var string */
    public $label;
    /** @var string */
    public $value;
    /** @var string ok, info, warn or error */
    public $severity;
    /** @var string the section id (system, php, exponential, engine, database, site, ...) */
    public $section;
    /** @var string a short advice for the Hints block; '' for none */
    public $hint;
    /** @var string row or text */
    public $kind = self::KIND_ROW;
    /** @var string the id of the provider that made the fact (set by the registry) */
    public $provider = '';

    /**
     * @param string $key
     * @param string $label
     * @param mixed $value a scalar; an array is joined with ", "
     * @param string $severity
     * @param string $section
     * @param string $hint
     */
    public function __construct( $key, $label, $value, $severity = self::INFO, $section = 'system', $hint = '' )
    {
        $this->key = (string)$key;
        $this->label = (string)$label;
        $this->value = self::scalar( $value );
        $this->severity = self::severity( $severity );
        $this->section = (string)$section;
        $this->hint = (string)$hint;
    }

    /**
     * A paragraph of text (exp:about).
     *
     * @param string $key
     * @param string $text
     * @param string $section
     * @param string $label an optional heading shown above the paragraph
     * @return expSysCardFact
     */
    public static function text( $key, $text, $section, $label = '' )
    {
        $fact = new self( $key, $label, $text, self::INFO, $section );
        $fact->kind = self::KIND_TEXT;
        return $fact;
    }

    /**
     * The fact that stands for a provider that ran out of time or failed: its value is "n/a".
     *
     * @param string $provider
     * @param string $section
     * @param string $why timeout or error
     * @return expSysCardFact
     */
    public static function unavailable( $provider, $section, $why = 'timeout' )
    {
        $fact = new self( $provider . '.unavailable', ucfirst( $provider ), 'n/a (' . $why . ')', self::INFO, $section );
        $fact->provider = $provider;
        return $fact;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public static function scalar( $value )
    {
        if ( is_bool( $value ) )
            return $value ? 'yes' : 'no';
        if ( is_array( $value ) )
            return implode( ', ', array_map( array( __CLASS__, 'scalar' ), $value ) );
        if ( $value === null )
            return 'n/a';
        if ( is_object( $value ) )
            return method_exists( $value, '__toString' ) ? (string)$value : get_class( $value );
        return trim( (string)$value );
    }

    /**
     * @param string $severity
     * @return string one of the four severities; anything else is info
     */
    public static function severity( $severity )
    {
        $severity = strtolower( (string)$severity );
        return in_array( $severity, array( self::OK, self::INFO, self::WARN, self::ERROR ), true ) ? $severity : self::INFO;
    }

    /**
     * The rank of a severity, for sorting the hints (error first).
     *
     * @param string $severity
     * @return int
     */
    public static function rank( $severity )
    {
        $ranks = array( self::ERROR => 0, self::WARN => 1, self::INFO => 2, self::OK => 3 );
        return isset( $ranks[$severity] ) ? $ranks[$severity] : 2;
    }

    /**
     * @return array key, label, value, severity, section, hint, kind, provider
     */
    public function toArray()
    {
        return array( 'key' => $this->key, 'label' => $this->label, 'value' => $this->value, 'severity' => $this->severity,
                      'section' => $this->section, 'hint' => $this->hint, 'kind' => $this->kind, 'provider' => $this->provider );
    }

    /**
     * The fact again from toArray() (what an isolated provider sends back to the parent process).
     *
     * @param array $row
     * @return expSysCardFact
     */
    public static function fromArray( array $row )
    {
        $get = function ( $name, $default = '' ) use ( $row ) { return isset( $row[$name] ) ? $row[$name] : $default; };
        $fact = new self( $get( 'key' ), $get( 'label' ), $get( 'value' ), $get( 'severity', self::INFO ), $get( 'section', 'system' ), $get( 'hint' ) );
        $fact->kind = $get( 'kind', self::KIND_ROW ) === self::KIND_TEXT ? self::KIND_TEXT : self::KIND_ROW;
        $fact->provider = (string)$get( 'provider' );
        return $fact;
    }
}
