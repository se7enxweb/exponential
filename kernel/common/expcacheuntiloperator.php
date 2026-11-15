<?php
/**
 * File containing the expCacheUntilOperator class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The template operators cache_until and seconds_until (expCacheUntil):
 *
 *   {cache_until( 'tomorrow' )}                                  midnight in the site's time zone
 *   {cache_until( 'next hour' )}  {cache_until( '+15 minutes' )}
 *   {cache_until( $event.data_map.start.content.timestamp )}     a Unix time
 *   {cache_until( 0 )}                                           do not keep this page in any cache
 *   {$event_start|cache_until}                                   the same, piped
 *   {def $seconds = 'tomorrow'|seconds_until}                    seconds from now, an integer
 *
 * cache_until prints nothing. It reports to the request (expCacheUntil::request()), so it works in the content view,
 * an include, a layout block of the pagelayout and the body of a cache-block alike, and every call can only bring
 * the expiry earlier. Both operators are evaluated when the template runs, never when it is compiled: they have no
 * template hints, so a constant argument is not folded into the compiled template.
 *
 * Reference: doc/eng-us/help/designers/templates/cache-until.md.
 *
 * @since 6.0.15
 * @package kernel
 */
class expCacheUntilOperator
{
    /** @var string[] */
    public $Operators;

    public function __construct()
    {
        $this->Operators = array( 'cache_until', 'seconds_until' );
    }

    /**
     * @return string[]
     */
    function operatorList()
    {
        return $this->Operators;
    }

    /**
     * @return bool
     */
    function namedParameterPerOperator()
    {
        return true;
    }

    /**
     * @return array
     */
    function namedParameterList()
    {
        $until = array( 'until' => array( 'type' => 'mixed', 'required' => false, 'default' => null ) );
        return array( 'cache_until' => $until, 'seconds_until' => $until );
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement = false )
    {
        // {cache_until( $value )} or {$value|cache_until}
        $value = $operatorParameters ? $namedParameters['until'] : $operatorValue;
        if ( $operatorName === 'seconds_until' )
        {
            $seconds = expCacheUntil::secondsUntil( $value );
            if ( $seconds === null && eZDebug::isDebugEnabled() )
                eZDebug::writeWarning( 'seconds_until(): ' . ( is_scalar( $value ) ? "'" . $value . "'" : gettype( $value ) ) .
                                       ' is not a time, 0 is returned' . self::where( $placement ), __METHOD__ );
            $operatorValue = $seconds === null ? 0 : $seconds;
            return;
        }
        expCacheUntil::request( $value, self::source( $placement ) );
        $operatorValue = '';
    }

    /**
     * The template that called the operator: the placement of an uncompiled template, else the innermost template of
     * the debug bar's template tree; '' when neither is known (without debug output a compiled template passes no
     * placement, and nothing needs the name then).
     *
     * @param mixed $placement
     * @return string
     */
    protected static function source( $placement )
    {
        if ( is_array( $placement ) && isset( $placement[2] ) && is_string( $placement[2] ) && $placement[2] !== '' )
            return $placement[2];
        if ( ( !empty( $GLOBALS['eZDebugEnabled'] ) || !empty( $GLOBALS['eZDebugCollect'] ) ) && class_exists( 'expRenderStats' )
             && method_exists( 'expRenderStats', 'currentTemplate' ) )
            return (string)expRenderStats::currentTemplate();
        return '';
    }

    /**
     * @param mixed $placement
     * @return string
     */
    protected static function where( $placement )
    {
        $source = self::source( $placement );
        return $source !== '' ? ' (' . $source . ')' : '';
    }
}
?>
