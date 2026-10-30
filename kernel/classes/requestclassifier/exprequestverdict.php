<?php
/**
 * File containing the expRequestVerdict class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What the request classifier found: the category (probe, traversal, injection, scanner, threshold, or one an
 * extension's rule set adds), the rule, how sure it is, and an optional detail for listeners (never logged as it is).
 *
 * @package kernel
 */
class expRequestVerdict
{
    const LOW = 'low';
    const MEDIUM = 'medium';
    const HIGH = 'high';

    /** @var string [a-z0-9_-]+ */
    public $category;
    /** @var string [a-z0-9_.-]+ */
    public $rule;
    /** @var string low | medium | high */
    public $confidence;
    /** @var string */
    public $detail;
    /** @var string The identifier of the rule set that gave it (set by the classifier) */
    public $ruleSet = '';

    /**
     * @param string $category
     * @param string $rule
     * @param string $confidence low | medium | high (anything else is medium)
     * @param string $detail
     */
    public function __construct( $category, $rule, $confidence = self::MEDIUM, $detail = '' )
    {
        $this->category = self::cleanName( $category, 'other' );
        $this->rule = self::cleanName( $rule, 'unknown', '.' );
        $this->confidence = self::cleanConfidence( $confidence );
        $this->detail = (string)$detail;
    }

    /**
     * The first token of a security.log line: <category>/<rule>.
     *
     * @return string
     */
    public function key()
    {
        return $this->category . '/' . $this->rule;
    }

    /**
     * @param mixed $confidence
     * @return string
     */
    public static function cleanConfidence( $confidence )
    {
        $confidence = strtolower( trim( (string)$confidence ) );
        return in_array( $confidence, array( self::LOW, self::MEDIUM, self::HIGH ), true ) ? $confidence : self::MEDIUM;
    }

    /**
     * A name for the log line: lower case, [a-z0-9_-] (and $extra), at most 64 characters.
     *
     * @param mixed $name
     * @param string $fallback
     * @param string $extra
     * @return string
     */
    public static function cleanName( $name, $fallback, $extra = '' )
    {
        $name = preg_replace( '/[^a-z0-9_\-' . preg_quote( $extra, '/' ) . ']+/', '-', strtolower( trim( (string)$name ) ) );
        $name = substr( trim( (string)$name, '-' ), 0, 64 );
        return $name === '' ? $fallback : $name;
    }
}
