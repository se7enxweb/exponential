<?php
/**
 * File containing the expUISwitchProviderInterface interface
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One switchable preference of the administration interface (the "UI chrome"): the display language, the design,
 * or what an extension adds. The registry (expUISwitch) finds providers in uiswitch.ini [UISwitchSettings]
 * ProviderList[], keeps the user's choice in the session and applies it early in every request, before the locale,
 * the translations, the design and any template are set up.
 *
 * A value is only ever one of allowedValues(): nothing from the request reaches a path, a file name or an INI name
 * before isValid() said yes. Guide: doc/bc/6.0/ui-switch.md
 */
interface expUISwitchProviderInterface
{
    /**
     * The provider's identifier: [a-z][a-z0-9_]{0,31}. It is also the policy function uiswitch/<identifier> and
     * the settings group uiswitch.ini [UISwitch_<identifier>].
     *
     * @return string
     */
    public function identifier();

    /**
     * The values a user may choose on the current siteaccess, in display order (the allow-list). The default value
     * is always one of them.
     *
     * @return string[]
     */
    public function allowedValues();

    /**
     * A label for an allowed value, for the toolbar.
     *
     * @param string $value
     * @return string
     */
    public function label( $value );

    /**
     * The siteaccess's own value, which the switch falls back to; choosing it clears the switch.
     *
     * @return string
     */
    public function defaultValue();

    /**
     * Whether a value is one of allowedValues(), strictly: no trimming, no case folding.
     *
     * @param mixed $value
     * @return bool
     */
    public function isValid( $value );

    /**
     * Applies a valid value other than the default for this request: overrides the in-memory settings and resets
     * the statics and globals it touches, so what was built from the old settings is built again.
     *
     * @param string $value
     * @return bool whether it was applied
     */
    public function apply( $value );

    /**
     * Undoes what apply() left behind in an earlier request of the same process (persistent Velocity workers):
     * statics and globals built from the switched value.
     *
     * @return void
     */
    public function reset();

    /**
     * The value in effect for this request.
     *
     * @return string
     */
    public function currentValue();

    /**
     * The key that separates caches built with a value: '' for the default value (no cache file name changes),
     * otherwise a short string of [A-Za-z0-9_.-].
     *
     * @param string $value
     * @return string
     */
    public function cacheVariantKey( $value );
}
