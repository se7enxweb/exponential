<?php
/**
 * File containing the ezpRequestRule class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * One request rule: when every condition matches, the action decides.
 *
 * In requestrules.ini a rule is the group [Rule-<name>]:
 *
 *   [Rule-system_url_full_view_to_alias]
 *   Description=...
 *   Conditions[requested_via]=system
 *   Conditions[module_view]=content/view
 *   Conditions[view_mode]=full
 *   Conditions[policy]=!content/view_system_url
 *   Action=redirect_to_alias
 *   ActionArgs[status]=301
 *   FallbackAction=notfound
 *
 * A condition key is a condition name, optionally with an argument after a
 * colon (param:NodeID, header:X-Requested-With, fact:my_fact). The value is
 * one or more values separated by commas, any of which may match; a leading
 * "!" turns the whole condition around (none of them may match).
 *
 * In PHP the same rule is built with new ezpRequestRule( $name, $conditions,
 * $action, $actionArgs, $fallbackAction, $fallbackArgs, $description ).
 */
class ezpRequestRule
{
    /** @var string */
    public $name;
    /** @var array condition key => value text, e.g. 'view_mode' => 'full', 'policy' => '!content/view_system_url' */
    public $conditions;
    /** @var string action name */
    public $action;
    /** @var array action argument name => value */
    public $actionArgs;
    /** @var string|null action used when the action cannot act (returns null) */
    public $fallbackAction;
    /** @var array */
    public $fallbackArgs;
    /** @var string */
    public $description;

    /**
     * @param string $name
     * @param array $conditions condition key => value text
     * @param string $action
     * @param array $actionArgs
     * @param string|null $fallbackAction
     * @param array $fallbackArgs
     * @param string $description
     */
    public function __construct( $name, array $conditions, $action, array $actionArgs = array(),
                                 $fallbackAction = null, array $fallbackArgs = array(), $description = '' )
    {
        $this->name = (string)$name;
        $this->conditions = $conditions;
        $this->action = (string)$action;
        $this->actionArgs = $actionArgs;
        $this->fallbackAction = ( $fallbackAction === null || $fallbackAction === '' ) ? null : (string)$fallbackAction;
        $this->fallbackArgs = $fallbackArgs;
        $this->description = (string)$description;
    }

    /**
     * Builds a rule from the settings of its [Rule-<name>] group.
     *
     * @param string $name
     * @param array $group setting name => value, as eZINI::group() returns it
     * @return ezpRequestRule
     */
    public static function fromSettings( $name, array $group )
    {
        $array = function ( $key ) use ( $group )
        {
            return isset( $group[$key] ) && is_array( $group[$key] ) ? $group[$key] : array();
        };
        return new self(
            $name,
            $array( 'Conditions' ),
            isset( $group['Action'] ) ? $group['Action'] : '',
            $array( 'ActionArgs' ),
            isset( $group['FallbackAction'] ) ? $group['FallbackAction'] : null,
            $array( 'FallbackArgs' ),
            isset( $group['Description'] ) ? $group['Description'] : ''
        );
    }

    /**
     * Splits a condition key into the condition name and its argument.
     *
     * @param string $key 'param:NodeID'
     * @return array( 'param', 'NodeID' ) or array( 'view_mode', null )
     */
    public static function splitKey( $key )
    {
        $key = trim( (string)$key );
        $pos = strpos( $key, ':' );
        if ( $pos === false )
            return array( strtolower( $key ), null );
        return array( strtolower( substr( $key, 0, $pos ) ), substr( $key, $pos + 1 ) );
    }

    /**
     * Splits a condition value into its values and whether it is negated.
     *
     * @param string $text '!content/view_system_url' or 'alias, wildcard'
     * @return array( string[] $values, bool $negated )
     */
    public static function splitValue( $text )
    {
        $text = trim( (string)$text );
        $negated = false;
        if ( $text !== '' && $text[0] === '!' )
        {
            $negated = true;
            $text = ltrim( substr( $text, 1 ) );
        }
        $values = array();
        foreach ( explode( ',', $text ) as $value )
        {
            $value = trim( $value );
            if ( $value !== '' )
                $values[] = $value;
        }
        return array( $values, $negated );
    }
}

?>
