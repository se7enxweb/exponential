<?php
/**
 * File containing the expI18nFallbackConfiguredRule class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 * @subpackage i18n
 */

/**
 * The configured fallbacks, i18n.ini [TranslationSettings] FallbackLanguages[]: always the first rule the resolver
 * asks, so what is configured wins over every other rule. A value of eng-GB means the source texts, as it always did
 * ('untranslated'). An empty value says nothing (the next rule is asked).
 *
 * @package lib
 * @subpackage i18n
 */
class expI18nFallbackConfiguredRule implements expI18nFallbackRuleInterface
{
    public function fallbackFor( $locale, expI18nFallbackResolver $resolver )
    {
        $configured = $resolver->configured();
        if ( !isset( $configured[$locale] ) || !is_string( $configured[$locale] ) || trim( $configured[$locale] ) === '' )
            return null;
        $target = trim( $configured[$locale] );
        return $target === 'eng-GB' ? expI18nFallbackResolver::SOURCE : $target;
    }
}
