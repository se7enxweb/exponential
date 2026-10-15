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
 * The allow-list is uiswitch.ini [UISwitch_language] AllowedValues[]: a locale is offered only when it is a locale
 * code (eng-GB, ger-DE, nor-NO@intl), share/locale/<code>.ini exists and, except for English, the interface has a
 * translation in share/translations/<code>/translation.ts. The siteaccess's own Locale is always offered.
 * Guide: doc/guides/ui-switch-settings.md
 */
class expUISwitchLanguageProvider extends expUISwitchProvider
{
    const LOCALE_PATTERN = '/^[a-z]{3}-[A-Z]{2}(@[a-z0-9]{1,16})?$/';

    /** @var string|null the siteaccess's Locale, read before any apply() */
    protected $siteDefault = null;

    /** @var string the installation root the share/ paths are relative to; '' is the working directory */
    protected $root = '';

    /**
     * @param string $identifier
     * @param array $settings
     * @param string|null $siteDefault the siteaccess's Locale; read from site.ini when null
     * @param string $root
     */
    public function __construct( $identifier, array $settings = array(), $siteDefault = null, $root = '' )
    {
        parent::__construct( $identifier, $settings );
        $this->siteDefault = $siteDefault;
        $this->root = (string)$root;
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
        return strpos( $value, 'eng-' ) === 0 || is_file( $prefix . 'share/translations/' . $value . '/translation.ts' );
    }

    /**
     * The language's own name (share/locale/<locale>.ini LanguageName: "Deutsch (Deutschland)",
     * "English (United Kingdom)"), as language pickers show it.
     */
    public function label( $value )
    {
        if ( !$this->isValid( $value ) )
            return (string)$value;
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
