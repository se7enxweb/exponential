<?php
/**
 * File containing the expI18nFallbackRuleInterface interface.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 * @subpackage i18n
 */

/**
 * A translation fallback rule: says which translation a locale falls back to when a translation root (the kernel's
 * share/translations, or an extension's translations directory) has no file for it.
 *
 * The resolver (expI18nFallbackResolver) asks i18n.ini [TranslationSettings] FallbackLanguages[] first, then the
 * classes of FallbackRules[] in their order, then, with AutomaticLanguageFallback=enabled, the same-language rule. The
 * first one that answers decides that step; the resolver then asks again for the locale that was answered, which
 * makes a chain (esl-AR -> esl-MX -> esl-ES), stopping at a cycle or at FallbackMaxDepth.
 *
 * A rule is created once per resolver, without arguments, so it can keep what it computed. It must be cheap: the
 * translator asks once per locale and request. See doc/eng-us/features/6.0/ui-switch-languages.md, "Writing a fallback
 * rule".
 *
 * @package lib
 * @subpackage i18n
 */
interface expI18nFallbackRuleInterface
{
    /**
     * The translation $locale falls back to.
     *
     * @param string $locale a locale code (esl-AR), or a variant (ger-DE@euro)
     * @param expI18nFallbackResolver $resolver for what the rule may need: hasTranslation(), localeExists(),
     *        configured(), the options
     * @return string|null a locale code, 'untranslated' for the source texts (English), or null when the rule has no
     *         say about $locale and the next rule is asked
     */
    public function fallbackFor( $locale, expI18nFallbackResolver $resolver );
}
