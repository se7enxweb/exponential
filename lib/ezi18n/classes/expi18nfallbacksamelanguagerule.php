<?php
/**
 * File containing the expI18nFallbackSameLanguageRule class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 * @subpackage i18n
 */

/**
 * The automatic fallback (i18n.ini [TranslationSettings] AutomaticLanguageFallback): a locale without a translation
 * of its own in the kernel's translation repository, and without a configured fallback, falls back to the most
 * complete translation of the same language (the three letters before the dash): fre-NC to fre-FR, ger-IT to ger-DE.
 * So a new country locale of a translated language needs no FallbackLanguages[] line.
 *
 * "Most complete" is the translation with the most translated messages (not empty, unfinished or obsolete), counted
 * once per file and its size and time; equal counts take the first code. A variant (ger-AT@euro) is left alone: the
 * translator loads the translation of its locale (ger-AT) first anyway. Languages and locales listed in
 * AutomaticFallbackExclude[] (eng by default: English is the language of the source texts, and an eng-IE should not
 * get the American spelling of eng-US) never fall back automatically.
 *
 * @package lib
 * @subpackage i18n
 */
class expI18nFallbackSameLanguageRule implements expI18nFallbackRuleInterface
{
    /** @var array path:size:mtime => translated messages, for the life of the process */
    private static $counts = array();

    /** @var array language => best translation ('' for none), per rule (a resolver's repository) */
    protected $best = array();

    public function fallbackFor( $locale, expI18nFallbackResolver $resolver )
    {
        $locale = (string)$locale;
        if ( strpos( $locale, '@' ) !== false || !preg_match( '/^([a-z]{3})-[A-Z]{2}$/', $locale, $match ) )
            return null;
        $language = $match[1];
        $exclude = (array)$resolver->option( 'automatic_exclude', array() );
        if ( in_array( $language, $exclude, true ) || in_array( $locale, $exclude, true ) )
            return null;
        if ( $resolver->hasTranslation( $locale ) )
            return null;
        if ( !array_key_exists( $language, $this->best ) )
            $this->best[$language] = $this->bestOf( $language, $resolver );
        return $this->best[$language] !== '' ? $this->best[$language] : null;
    }

    /**
     * The most complete translation of a language in the resolver's repository, '' when it has none.
     *
     * @param string $language
     * @param expI18nFallbackResolver $resolver
     * @return string
     */
    protected function bestOf( $language, expI18nFallbackResolver $resolver )
    {
        $best = '';
        $bestCount = -1;
        $candidates = array();
        foreach ( $resolver->translations() as $code )
        {
            if ( substr( $code, 0, 4 ) === $language . '-' && strpos( $code, '@' ) === false
                 && !in_array( $code, (array)$resolver->option( 'automatic_exclude', array() ), true ) )
                $candidates[] = $code;
        }
        sort( $candidates, SORT_STRING );
        foreach ( $candidates as $code )
        {
            $count = self::translatedCount( $resolver->translationFile( $code ) );
            if ( $count > $bestCount )
            {
                $best = $code;
                $bestCount = $count;
            }
        }
        return $best;
    }

    /**
     * How many messages of a Qt Linguist file have a translation: the <translation> elements that are neither empty
     * nor marked unfinished or obsolete. A quick count of the text, not a parse: it only ranks translations.
     *
     * @param string $file
     * @return int
     */
    public static function translatedCount( $file )
    {
        $stat = @stat( $file );
        if ( !$stat )
            return 0;
        $key = $file . ':' . $stat['size'] . ':' . $stat['mtime'];
        if ( isset( self::$counts[$key] ) )
            return self::$counts[$key];
        $text = (string)@file_get_contents( $file );
        $all = preg_match_all( '/<translation[\s>\/]/', $text );
        $empty = preg_match_all( '/<translation(\s+type="[^"]*")?\s*(\/>|>\s*<\/translation>)/', $text );
        $marked = preg_match_all( '/<translation\s+type="(unfinished|obsolete|vanished)"[^>\/]*>\s*[^<\s]/', $text );
        return self::$counts[$key] = max( 0, (int)$all - (int)$empty - (int)$marked );
    }
}
