<?php
/**
 * File containing the expUISwitchLanguageProvider class
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The display language of the administration interface: what site.ini [RegionalSettings] Locale of the siteaccess
 * sets (the translations of the interface, date and number formats), for the signed-in user who chose it. Content
 * languages (SiteLanguageList[], ContentObjectLocale) are never touched.
 *
 * The allow-list is built from uiswitch.ini [UISwitch_language]: the locales of the groups (GroupList[],
 * GroupValues_<group>[]), AllowedValues[] and, with AllLanguages=enabled, every locale of share/locale (variants such
 * as eng-GB@euro only when named). A locale is offered only when it is a locale code (eng-GB, ger-DE, nor-NO@intl)
 * and share/locale/<code>.ini exists; with ListUntranslated=disabled (the default when the setting is missing) also
 * only when it is English or the interface has a translation in share/translations/<code>/translation.ts. The
 * siteaccess's own Locale is always offered. A test language ([LanguageSettings] TestLanguage=true, as Lorem Ipsum,
 * qlo-ZZ) is left out of the groups and of AllLanguages; AllowedValues[] can still name it, and it is then listed A-Z.
 *
 * With groups or AllLanguages the toolbar shows the choice grouped (groups()): the configured groups in their order,
 * then every offered locale A-Z by its name, each with how complete its translation is (expUISwitchLanguageCatalogue).
 * Guides: doc/guides/ui-switch-settings.md, doc/features/6.0/ui-switch-languages.md
 */
class expUISwitchLanguageProvider extends expUISwitchProvider
{
    const LOCALE_PATTERN = '/^[a-z]{3}-[A-Z]{2}(@[a-z0-9]{1,16})?$/';

    /** what a group identifier may look like */
    const GROUP_PATTERN = '/^[a-z][a-z0-9_]{0,31}$/';

    /** the translation context of the group names and notes */
    const I18N_CONTEXT = 'kernel/uiswitch/language';

    /** @var string|null the siteaccess's Locale, read before any apply() */
    protected $siteDefault = null;

    /** @var string the installation root the share/ paths are relative to; '' is the working directory */
    protected $root = '';

    /** @var expUISwitchLanguageCatalogue|null */
    protected $catalogue = null;

    /** @var array reader language => value => text of the grouped list */
    protected $displayLabels = array();

    /**
     * @param string $identifier
     * @param array $settings
     * @param string|null $siteDefault the siteaccess's Locale; read from site.ini when null
     * @param string $root
     * @param expUISwitchLanguageCatalogue|null $catalogue names and translation coverage; built when needed
     */
    public function __construct( $identifier, array $settings = array(), $siteDefault = null, $root = '', ?expUISwitchLanguageCatalogue $catalogue = null )
    {
        parent::__construct( $identifier, $settings );
        $this->siteDefault = $siteDefault;
        $this->root = (string)$root;
        $this->catalogue = $catalogue;
    }

    /**
     * @return expUISwitchLanguageCatalogue
     */
    public function catalogue()
    {
        if ( $this->catalogue === null )
            $this->catalogue = new expUISwitchLanguageCatalogue( $this->root );
        return $this->catalogue;
    }

    protected function isEnabledSetting( $name, $default )
    {
        $value = $this->setting( $name, $default );
        return $value === 'enabled' || $value === true || $value === 'true';
    }

    /**
     * Whether locales without a translation of the interface are offered (their texts stay English, their formats
     * are their own). ListUntranslated=enabled; off when the setting is missing.
     *
     * @return bool
     */
    public function listsUntranslated()
    {
        return $this->isEnabledSetting( 'ListUntranslated', 'disabled' );
    }

    /**
     * Whether every locale of the installation is offered and listed A-Z. AllLanguages=enabled; off when missing.
     *
     * @return bool
     */
    public function listsAllLanguages()
    {
        return $this->isEnabledSetting( 'AllLanguages', 'disabled' );
    }

    /**
     * The configured groups, in GroupList[] order: identifier => the well-formed locales of GroupValues_<group>[],
     * each once per group. Not yet checked against the installation.
     *
     * @return array
     */
    public function configuredGroups()
    {
        $groups = array();
        foreach ( (array)$this->setting( 'GroupList', array() ) as $group )
        {
            if ( !is_string( $group ) || !preg_match( self::GROUP_PATTERN, $group ) || isset( $groups[$group] ) )
                continue;
            $values = array();
            foreach ( (array)$this->setting( 'GroupValues_' . $group, array() ) as $value )
            {
                if ( is_string( $value ) && preg_match( self::LOCALE_PATTERN, $value ) && !in_array( $value, $values, true ) )
                    $values[] = $value;
            }
            $groups[$group] = $values;
        }
        return $groups;
    }

    /**
     * The candidates of the allow-list, in order: the groups' locales, AllowedValues[], and with AllLanguages every
     * locale of share/locale without a variant. isAvailable() then keeps those the installation can use.
     *
     * @return string[]
     */
    public function configuredValues()
    {
        // A test language (Lorem Ipsum, qlo-ZZ) is never offered by a group or by AllLanguages, only when
        // AllowedValues[] names it.
        $catalogue = $this->catalogue();
        $list = array();
        foreach ( $this->configuredGroups() as $values )
        {
            foreach ( $values as $value )
            {
                if ( !$catalogue->isTestLocale( $value ) )
                    $list[] = $value;
            }
        }
        foreach ( parent::configuredValues() as $value )
            $list[] = $value;
        if ( $this->listsAllLanguages() )
        {
            foreach ( $catalogue->localeCodes( false ) as $value )
            {
                if ( !$catalogue->isTestLocale( $value ) )
                    $list[] = $value;
            }
        }
        $unique = array();
        foreach ( $list as $value )
        {
            if ( preg_match( self::VALUE_PATTERN, $value ) && !in_array( $value, $unique, true ) )
                $unique[] = $value;
        }
        return $unique;
    }

    public function defaultValue()
    {
        if ( $this->siteDefault === null )
            $this->siteDefault = (string)eZINI::instance()->variable( 'RegionalSettings', 'Locale' );
        return $this->siteDefault;
    }

    protected function isAvailable( $value )
    {
        if ( !preg_match( self::LOCALE_PATTERN, $value ) )
            return false;
        $prefix = $this->root !== '' ? rtrim( $this->root, '/' ) . '/' : '';
        if ( !is_file( $prefix . 'share/locale/' . $value . '.ini' ) )
            return false;
        if ( $this->listsUntranslated() )
            return true;
        return strpos( $value, 'eng-' ) === 0 || is_file( $prefix . 'share/translations/' . $value . '/translation.ts' );
    }

    /**
     * Whether the choice is shown grouped (a list with headings): there are groups or every language is listed.
     *
     * @return bool
     */
    public function isGrouped()
    {
        return $this->listsAllLanguages() || count( array_filter( $this->configuredGroups() ) ) > 0;
    }

    /**
     * How complete the kernel's translation of the interface is for an offered locale: 0-100, or null when it has
     * none (its texts stay English).
     *
     * @param string $value
     * @return int|null
     */
    public function coverage( $value )
    {
        if ( !$this->isValid( $value ) )
            return null;
        $entry = $this->catalogue()->entry( $value );
        return $entry !== null && $entry['coverage'] !== null ? (int)$entry['coverage'] : null;
    }

    /**
     * The HTTP code of the language the reader sees the interface in now (de-DE), for collation and the names of
     * the languages.
     *
     * @return string
     */
    protected function readerLanguage()
    {
        $current = $this->currentValue();
        $entry = $this->catalogue()->entry( $current );
        if ( $entry !== null && $entry['http'] !== '' )
            return $entry['http'];
        return 'en-GB';
    }

    /**
     * The name of a language in the reader's interface language: Intl's display name when the extension knows it,
     * else the English name of the locale file (InternationalLanguageName), else ''.
     *
     * @param string $value
     * @param string $readerLanguage HTTP code
     * @return string
     */
    public function readerName( $value, $readerLanguage )
    {
        $entry = $this->catalogue()->entry( $value );
        if ( $entry === null )
            return '';
        $names = $this->catalogue()->readerNames( $readerLanguage );
        if ( isset( $names[$value] ) && $names[$value] !== '' )
            return $names[$value];
        return (string)$entry['english'];
    }

    /**
     * The text of a choice in the grouped list: the language's own name, a dash and its name in the reader's
     * language when that differs ("Deutsch (Deutschland) — German (Germany)"), and how complete the translation is
     * ("(87%)", "(no translation yet)").
     *
     * @param string $value
     * @param string|null $readerLanguage
     * @return string
     */
    public function displayLabel( $value, $readerLanguage = null )
    {
        if ( !$this->isValid( $value ) )
            return (string)$value;
        if ( $readerLanguage === null )
            $readerLanguage = $this->readerLanguage();
        if ( !isset( $this->displayLabels[$readerLanguage] ) )
        {
            // all at once, so that two locales with the same name ("Српски (Србија)" for ser-SR and srp-RS) get
            // their codes added and stay apart
            $names = array();
            foreach ( $this->allowedValues() as $allowed )
                $names[$allowed] = $this->displayName( $allowed, $readerLanguage );
            $counts = array_count_values( array_map( array( __CLASS__, 'lower' ), $names ) );
            $labels = array();
            $untranslated = self::tr( 'no translation yet' );
            foreach ( $names as $allowed => $name )
            {
                if ( $counts[self::lower( $name )] > 1 )
                    $name .= ' [' . $allowed . ']';
                $entry = $this->catalogue()->entry( $allowed );
                $coverage = $entry !== null ? $entry['coverage'] : null;
                $note = $coverage === null ? $untranslated : (int)$coverage . '%';
                $labels[$allowed] = $name . ' (' . $note . ')';
            }
            $this->displayLabels[$readerLanguage] = $labels;
        }
        return isset( $this->displayLabels[$readerLanguage][$value] ) ? $this->displayLabels[$readerLanguage][$value] : (string)$value;
    }

    /**
     * The name part of a choice's text: the language's own name, and a dash and its name in the reader's language
     * when that differs.
     *
     * @param string $value
     * @param string $readerLanguage
     * @return string
     */
    public function displayName( $value, $readerLanguage )
    {
        $native = $this->nativeName( $value );
        $other = $this->readerName( $value, $readerLanguage );
        if ( $native === '' )
            $native = $other !== '' ? $other : (string)$value;
        if ( $other !== '' && self::lower( $other ) !== self::lower( $native ) )
            return $native . ' — ' . $other;
        return $native;
    }

    /**
     * The language's own name: LanguageName of its locale file. Where that file only repeats the English name
     * ("Indonesian", "Croatian (Hrvatski)", "Serbian (Српски)": its first word is the first word of
     * InternationalLanguageName), Intl's name of the language in itself is used when the extension knows it, its
     * first letter upper case ("Українська (Україна)"). '' for a code the catalogue does not know.
     *
     * @param string $value
     * @return string
     */
    public function nativeName( $value )
    {
        $entry = $this->catalogue()->entry( $value );
        if ( $entry === null )
            return '';
        $native = (string)$entry['native'];
        $repeatsEnglish = $native === '' || self::firstWord( $native ) === self::firstWord( (string)$entry['english'] );
        if ( $repeatsEnglish && isset( $entry['intl_native'] ) && $entry['intl_native'] !== '' )
        {
            $name = (string)$entry['intl_native'];
            if ( function_exists( 'mb_substr' ) )
                $name = mb_strtoupper( mb_substr( $name, 0, 1, 'UTF-8' ), 'UTF-8' ) . mb_substr( $name, 1, null, 'UTF-8' );
            return $name;
        }
        return $native;
    }

    protected static function firstWord( $text )
    {
        $words = preg_split( '/[\s(]+/u', self::lower( trim( (string)$text ) ), 2 );
        return (string)$words[0];
    }

    public static function lower( $text )
    {
        return function_exists( 'mb_strtolower' ) ? mb_strtolower( (string)$text, 'UTF-8' ) : strtolower( (string)$text );
    }

    protected static function tr( $text )
    {
        return class_exists( 'ezpI18n' ) ? ezpI18n::tr( self::I18N_CONTEXT, $text ) : $text;
    }

    /**
     * The choice as the toolbar groups it: the configured groups (GroupLabel[<group>]) with their offered locales in
     * their configured order, then "All languages (A–Z)" with every offered locale (AllLanguages=enabled) or "More
     * languages (A–Z)" with the offered ones no group names, sorted by their text in the reader's collation. Empty
     * when the choice is not grouped. The siteaccess's Locale is always in the A-Z list.
     *
     * @return array list of hash( identifier, label, values => list of value )
     */
    public function groups()
    {
        if ( !$this->isGrouped() )
            return array();
        $reader = $this->readerLanguage();
        $labels = (array)$this->setting( 'GroupLabel', array() );
        $result = array();
        $grouped = array();
        foreach ( $this->configuredGroups() as $group => $values )
        {
            $offered = array();
            foreach ( $values as $value )
            {
                // a test language is never ranked, even when AllowedValues[] offers it
                if ( $this->isValid( $value ) && !$this->catalogue()->isTestLocale( $value ) )
                    $offered[] = $value;
            }
            if ( !$offered )
                continue;
            $label = isset( $labels[$group] ) && is_string( $labels[$group] ) && $labels[$group] !== '' ? self::tr( $labels[$group] ) : ucfirst( str_replace( '_', ' ', $group ) );
            $result[] = array( 'identifier' => $group, 'label' => $label, 'values' => $offered );
            $grouped = array_merge( $grouped, $offered );
        }
        $all = $this->listsAllLanguages();
        $rest = array();
        foreach ( $this->allowedValues() as $value )
        {
            if ( $all || !in_array( $value, $grouped, true ) || $value === $this->defaultValue() )
                $rest[$value] = $this->displayLabel( $value, $reader );
        }
        if ( $rest )
            $result[] = array( 'identifier' => 'all',
                               'label' => self::tr( $all ? 'All languages (A–Z)' : 'More languages (A–Z)' ),
                               'values' => expUISwitchLanguageCatalogue::sortValuesByLabel( $rest, $reader ) );
        return $result;
    }

    /**
     * The language's own name (share/locale/<locale>.ini LanguageName: "Deutsch (Deutschland)",
     * "English (United Kingdom)"), as language pickers show it.
     */
    public function label( $value )
    {
        if ( !$this->isValid( $value ) )
            return (string)$value;
        $native = $this->nativeName( $value );
        if ( $native !== '' )
            return $native;
        try
        {
            $locale = eZLocale::create( $value );
            $name = $locale->isValid() ? trim( (string)$locale->languageName() ) : '';
            return $name !== '' ? $name : $value;
        }
        catch ( Throwable $e )
        {
            return $value;
        }
    }

    public function title()
    {
        return class_exists( 'ezpI18n' ) ? ezpI18n::tr( 'kernel/uiswitch', 'Interface language' ) : 'Interface language';
    }

    /**
     * Each language's name is written in that language: lang="de-DE" for ger-DE.
     */
    public function labelLanguage( $value )
    {
        if ( !$this->isValid( $value ) )
            return '';
        $entry = $this->catalogue()->entry( $value );
        if ( $entry !== null && $entry['http'] !== '' )
            return $entry['http'];
        try
        {
            $locale = eZLocale::create( $value );
            return $locale->isValid() ? (string)$locale->httpLocaleCode() : '';
        }
        catch ( Throwable $e )
        {
            return '';
        }
    }

    public function apply( $value )
    {
        if ( !$this->isValid( $value ) || $value === $this->defaultValue() )
            return false;
        $ini = eZINI::instance();
        $ini->setVariable( 'RegionalSettings', 'Locale', $value );
        $ini->setVariable( 'RegionalSettings', 'TextTranslation', 'enabled' );
        self::resetTranslationState();
        $this->current = $value;
        return true;
    }

    public function reset()
    {
        self::resetTranslationState();
        parent::reset();
    }

    /**
     * Forgets the translator, the default eZLocale and the translation cache tables, so the next use builds them
     * from the Locale now in effect.
     */
    protected static function resetTranslationState()
    {
        if ( class_exists( 'eZTranslatorManager' ) )
            eZTranslatorManager::resetTranslations();
        if ( class_exists( 'ezpI18n' ) && method_exists( 'ezpI18n', 'reset' ) )
            ezpI18n::reset();
    }
}
