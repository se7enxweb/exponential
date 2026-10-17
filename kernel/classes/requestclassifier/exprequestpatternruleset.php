<?php
/**
 * File containing the expRequestPatternRuleSet class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A rule set made of regular expressions: one per rule, all of them compiled into one combined expression, so a
 * request costs one preg_match() per subject (the strings the set looks at: the path, the query, the user agent).
 *
 * The built-in rules come from builtInPatterns(); settings/requestclassifier.ini [RuleSet-<id>] adds to them:
 *
 *   Patterns[<rule>]=<regex>   a rule of its own, or a built-in rule replaced (same name)
 *   Disabled[]=<rule>          a built-in rule switched off
 *   Excludes[<name>]=<regex>   a request whose exclude subject (see excludeSubject()) matches is never flagged by
 *                              this set; Excludes[<built-in name>]= (empty) removes a built-in exclude
 *   Confidence[<rule>]=low|medium|high, DefaultConfidence=...
 *
 * A pattern is PCRE without delimiters, matched case-insensitively. It must not use back references (\1, \g{1}, (?1)):
 * the combined expression renumbers the groups (named groups are refused for the same reason). A pattern that does not compile is left out with a warning.
 *
 * An extension's own set can extend this class and implement category(), builtInPatterns() and subjects().
 *
 * @package kernel
 */
abstract class expRequestPatternRuleSet implements expRequestRuleSetInterface
{
    /** @var string */
    protected $identifier;
    /** @var array The INI group [RuleSet-<id>] */
    protected $settings;
    /** @var expRequestClassifier|null */
    protected $classifier;
    /** @var array|null What compile() gave: regex, rules, exclude, invalid */
    protected $compiled = null;

    /**
     * @param string $identifier
     * @param array $settings
     * @param expRequestClassifier|null $classifier
     */
    public function __construct( $identifier, array $settings = array(), $classifier = null )
    {
        $this->identifier = (string)$identifier;
        $this->settings = $settings;
        $this->classifier = $classifier;
    }

    /**
     * The category of the verdicts: probe, traversal, injection, scanner, or one of the extension's ([a-z0-9_-]).
     *
     * @return string
     */
    abstract public function category();

    /**
     * The built-in rules: rule name => regular expression.
     *
     * @return array
     */
    abstract protected function builtInPatterns();

    /**
     * The strings the rules are matched against, in order; an empty one is skipped.
     *
     * @param expRequestContext $context
     * @return string[]
     */
    abstract protected function subjects( expRequestContext $context );

    /**
     * The built-in excludes: name => regular expression, matched against excludeSubject().
     *
     * @return array
     */
    protected function builtInExcludes()
    {
        return array();
    }

    /**
     * The confidence of a built-in rule: rule name => low | medium | high; a rule not named has defaultConfidence().
     *
     * @return array
     */
    protected function builtInConfidence()
    {
        return array();
    }

    /**
     * @return string
     */
    protected function defaultConfidence()
    {
        return isset( $this->settings['DefaultConfidence'] ) && $this->settings['DefaultConfidence'] !== ''
            ? expRequestVerdict::cleanConfidence( $this->settings['DefaultConfidence'] ) : expRequestVerdict::HIGH;
    }

    /**
     * The string the excludes are matched against (the decoded namespace path by default).
     *
     * @param expRequestContext $context
     * @return string
     */
    protected function excludeSubject( expRequestContext $context )
    {
        return $context->decodedNamespacePath();
    }

    /**
     * @return string
     */
    public function identifier()
    {
        return $this->identifier;
    }

    /**
     * Everything compile() depends on, after the INI settings are applied: the classifier hashes it to find the
     * compiled expressions in its cache.
     *
     * @return array patterns (rule => regex), excludes (name => regex), confidence (rule => level)
     */
    public function definition()
    {
        $patterns = $this->builtInPatterns();
        foreach ( self::settingArray( $this->settings, 'Patterns' ) as $rule => $regex )
        {
            if ( !is_string( $rule ) )
                $rule = 'pattern-' . $rule;
            if ( trim( (string)$regex ) !== '' )
                $patterns[$rule] = (string)$regex;
        }
        foreach ( self::settingArray( $this->settings, 'Disabled' ) as $rule )
            unset( $patterns[(string)$rule] );

        $excludes = $this->builtInExcludes();
        foreach ( self::settingArray( $this->settings, 'Excludes' ) as $name => $regex )
        {
            if ( !is_string( $name ) )
                $name = 'exclude-' . $name;
            if ( trim( (string)$regex ) === '' )
                unset( $excludes[$name] );
            else
                $excludes[$name] = (string)$regex;
        }

        $default = $this->defaultConfidence();
        $levels = $this->builtInConfidence();
        foreach ( self::settingArray( $this->settings, 'Confidence' ) as $rule => $level )
            $levels[(string)$rule] = $level;
        $confidence = array();
        foreach ( array_keys( $patterns ) as $rule )
            $confidence[$rule] = isset( $levels[$rule] ) ? expRequestVerdict::cleanConfidence( $levels[$rule] ) : $default;

        return array( 'category' => $this->category(), 'patterns' => $patterns, 'excludes' => $excludes, 'confidence' => $confidence );
    }

    /**
     * Compiles a definition() into the combined expressions. Every pattern is checked on its own first; one that
     * does not compile (or uses a back reference) is left out and listed in invalid.
     *
     * @param array $definition
     * @return array regex (string|null, the combined expression), rules (mark => rule), each (rule => expression),
     *         confidence, exclude (string|null), invalid (rule => reason)
     */
    public static function compileDefinition( array $definition )
    {
        $invalid = array();
        $rules = array();
        $parts = array();
        $each = array();
        foreach ( $definition['patterns'] as $rule => $regex )
        {
            $body = self::body( $regex, $reason );
            if ( $body === null )
            {
                $invalid[$rule] = $reason;
                continue;
            }
            $mark = 'r' . count( $rules );
            $rules[$mark] = (string)$rule;
            $parts[] = '(?:' . $body . ')(*MARK:' . $mark . ')';
            $each[(string)$rule] = '~' . $body . '~i';
        }
        $excludeParts = array();
        foreach ( $definition['excludes'] as $name => $regex )
        {
            $body = self::body( $regex, $reason );
            if ( $body === null )
                $invalid['exclude:' . $name] = $reason;
            else
                $excludeParts[] = '(?:' . $body . ')';
        }
        // patterns that compile alone always compile together; checked anyway, so a broken cache can never be written
        if ( $parts && @preg_match( '~' . implode( '|', $parts ) . '~i', '' ) === false )
        {
            $invalid['*'] = 'the combined expression does not compile';
            $parts = array();
            $each = array();
        }
        return array(
            'regex' => $parts ? '~' . implode( '|', $parts ) . '~i' : null,
            'rules' => $rules,
            'each' => $each,
            'confidence' => isset( $definition['confidence'] ) ? $definition['confidence'] : array(),
            'exclude' => $excludeParts ? '~' . implode( '|', $excludeParts ) . '~i' : null,
            'invalid' => $invalid,
        );
    }

    /**
     * A pattern ready to go into the combined expression (the delimiter ~ escaped), or null when it does not
     * compile on its own or uses a back reference or names a group.
     *
     * @param string $regex
     * @param string $reason set when null is returned
     * @return string|null
     */
    protected static function body( $regex, &$reason = null )
    {
        $regex = (string)$regex;
        if ( trim( $regex ) === '' )
        {
            $reason = 'empty';
            return null;
        }
        // an unescaped ~ would end the expression
        $body = preg_replace( '/(?<!\\\\)((?:\\\\\\\\)*)~/', '$1\\~', $regex );
        if ( preg_match( '/\\\\(?:[1-9]|g\s*\{?-?\d|k\s*[<{\']|g\s*[<\']?[a-z_])|\(\?(?:[-+]?\d|R|P=|P>|&|P?<[a-z_]|\')/i', $body ) )
        {
            $reason = 'back reference or recursion';
            return null;
        }
        if ( @preg_match( '~(?:' . $body . ')~i', '' ) === false )
        {
            $reason = 'does not compile';
            return null;
        }
        return $body;
    }

    /**
     * Gives the set its compiled expressions (from the classifier's cache).
     *
     * @param array $compiled what compileDefinition() returned
     * @return void
     */
    public function setCompiled( array $compiled )
    {
        $this->compiled = $compiled;
    }

    /**
     * @return array what compileDefinition() returned for this set
     */
    public function compiled()
    {
        if ( $this->compiled === null )
            $this->compiled = self::compileDefinition( $this->definition() );
        return $this->compiled;
    }

    /**
     * @param expRequestContext $context
     * @return expRequestVerdict|null
     */
    public function evaluate( expRequestContext $context )
    {
        $compiled = $this->compiled();
        if ( $compiled['regex'] === null )
            return null;
        if ( $compiled['exclude'] !== null )
        {
            $subject = $this->excludeSubject( $context );
            if ( $subject !== '' && preg_match( $compiled['exclude'], $subject ) )
                return null;
        }
        $combined = self::combinedIsFaster() || empty( $compiled['each'] );
        foreach ( $this->subjects( $context ) as $subject )
        {
            if ( $subject === '' || $subject === null )
                continue;
            $rule = null;
            $text = '';
            if ( $combined )
            {
                if ( preg_match( $compiled['regex'], $subject, $m ) && isset( $m['MARK'], $compiled['rules'][$m['MARK']] ) )
                {
                    $rule = $compiled['rules'][$m['MARK']];
                    $text = $m[0];
                }
            }
            else
            {
                // The same result as the combined expression (the leftmost match; at the same place the first rule),
                // one expression at a time: without PCRE's JIT each one is much faster alone, as PCRE can skip a
                // subject that lacks a character the expression needs
                $at = PHP_INT_MAX;
                foreach ( $compiled['each'] as $name => $regex )
                {
                    if ( preg_match( $regex, $subject, $m, PREG_OFFSET_CAPTURE ) && $m[0][1] < $at )
                    {
                        $at = $m[0][1];
                        $rule = $name;
                        $text = $m[0][0];
                    }
                }
            }
            if ( $rule !== null )
            {
                $confidence = isset( $compiled['confidence'][$rule] ) ? $compiled['confidence'][$rule] : $this->defaultConfidence();
                return new expRequestVerdict( $this->category(), $rule, $confidence, (string)$text );
            }
        }
        return null;
    }

    /**
     * Whether the combined expression is the faster way: with PCRE's JIT (pcre.jit=1, the default) it is, one pass
     * over the subject; without it, the expressions one by one are (measured: 15 against 100 microseconds a request).
     * Both give the same verdict. $set overrides it (tests).
     *
     * @param bool|null $set
     * @return bool
     */
    public static function combinedIsFaster( $set = null )
    {
        static $override = null;
        if ( func_num_args() > 0 )
            $override = $set;
        if ( $override !== null )
            return $override;
        return (bool)ini_get( 'pcre.jit' );
    }

    /**
     * An INI setting as an array (a missing one, an empty string: none).
     *
     * @param array $settings
     * @param string $name
     * @return array
     */
    protected static function settingArray( array $settings, $name )
    {
        if ( !isset( $settings[$name] ) )
            return array();
        $value = $settings[$name];
        if ( is_array( $value ) )
            return array_filter( $value, function ( $v ) { return $v !== null; } );
        return $value === '' ? array() : array( $value );
    }
}
